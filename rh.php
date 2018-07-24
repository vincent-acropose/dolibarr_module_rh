<?php

require 'config.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/usergroups.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
dol_include_once('/rh/class/rh.class.php');
dol_include_once('/formation/class/formation.class.php');

$idUser = GETPOST('id', 'int');

$object = new User($db);
$object->fetch($idUser);

$formation = new Formation($db);
$listFormation = $formation->getTrainings(['user' => $object->id, "beginYear" => 2017, "finishYear" => 3000, "statut" => -1]);

if ($object->array_options['options_prod_or_not'] == 1 && empty($user->rights->rh->production)) {
    accessforbidden();
}

elseif($object->array_options['options_prod_or_not'] == 2 && empty($user->rights->rh->notProduction)) {
	accessforbidden();
}

$langs->load('rh@rh');
$langs->load('formation@formation');

$action = GETPOST('action');

$rhManager = new Rh($db);

$medVisits = $rhManager->getMed($idUser);
$habilitations = $rhManager->getHabilitations($idUser);
$entretiens = $rhManager->getEntretiens($idUser);
$primes = $rhManager->getPrimes($idUser);

// Action
switch ($action) {
	case 'edit':
		$values = [];
		$values['present'] = GETPOST('present');
		$values['salary'] = GETPOST('salary');
		$values['salary_brut'] = GETPOST('salary_brut');
		$values['address1'] = GETPOST('address1');
		$values['address2'] = GETPOST('address2');
		$values['zip'] = GETPOST('zip');
		$values['city'] = GETPOST('city');
		$values['telFixe'] = GETPOST('telFixe');
		$values['telPortable'] = GETPOST('telPortable');
		$values['contact'] = GETPOST('contact');
		$values['telContact1'] = GETPOST('telContact1');
		$values['telContact2'] = GETPOST('telContact2');

		if($rhManager->set($values, $object->id)) {
			header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);
			exit;
		}
		else {
			setEventMessage("Problème rencontré lors de la modification du salaire", "errors");
		}

		break;

	case 'new_med':
		$date = GETPOST('date_visit');
		$commentaire = GETPOST('commentaire');

		$rhManager->setMed($date, $commentaire, $idUser);

		header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);
		break;

	case 'del_med':
		$med = GETPOST('med');
		$rhManager->delMed($med);

		header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);
		break;

	case 'new_hab':
		$numero = GETPOST('numero');
		$date = GETPOST('date_hab');
		$datefin = GETPOST('date_hab_fin');
		$intitule = GETPOST('intitule');

		$rhManager->setHab($numero, $date, $datefin, $intitule, $idUser);

		header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);
		break;

	case 'del_hab':
		$hab = GETPOST('hab');
		$rhManager->delHab($hab);

		header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);
		break;

	case 'new_ent':
		$date = GETPOST('date_ent');
		$commentaire = GETPOST('commentaire');

		$rhManager->setEnt($date, $commentaire, $idUser);

		header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);
		break;

	case 'del_ent':
		$ent = GETPOST('ent');
		$rhManager->delEnt($ent);

		header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);

		break;

	case 'new_prime':
		$date = GETPOST('date_prime');
		$montant = GETPOST('montant');

		$rhManager->setPrime($date, $montant, $idUser);
		header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);
		break;

	case 'del_prime':
		$prime = GETPOST('prime');
		$rhManager->delPrime($prime);

		header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);

		break;

	case 'getCsv_1':
		$rhManager->makeCsv(1, $user);

		header("Location: ".DOL_URL_ROOT."/document.php?modulepart=rh&file=liste_utilisateurs.csv");
		break;

	case 'getCsv_2':
		$rhManager->makeCsv(2, $user);

		header("Location: ".DOL_URL_ROOT."/document.php?modulepart=rh&file=habilitations.csv");
		break;

	case 'getCsv_3':
		$rhManager->makeCsv(3, $user);

		header("Location: ".DOL_URL_ROOT."/document.php?modulepart=rh&file=visites.csv");
		break;

}

// Vue
llxHeader('',$langs->trans("RHCard"));
$head = user_prepare_head($object);
$picto = 'user';
$form=new Form($db);

dol_fiche_head($head, 'rh', $langs->trans("User"), -1, $picto);

if ($action == "modify") {
	print '<form method=POST action="' . $_SERVER["PHP_SELF"] . '?id='.$object->id.'">';
	print '<input type=hidden name="action" value="edit">';
	print '<table class="border" width="100%">';

	// Salary
	print '<tr><td class="titlefieldcreate">' . $langs->trans('Present') . '</td><td><select name=present><option value=Oui>Oui</option><option value=Non>Non</option></select></td>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('Salary') . '</td><td><input type=text name="salary" value=' . $rhManager->get("salary", $object->id)->salary . '></td></tr>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('SalaryMonth') . '</td><td><input type=text name="salary_brut" value=' . $rhManager->get("salary_brut", $object->id)->salary_brut . '></td></tr>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('Address1') . '</td><td><input type=text name="address1" value="' . $rhManager->get("address1", $object->id)->address1 . '"></td></tr>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('Address2') . '</td><td><input type=text name="address2" value="' . $rhManager->get("address2", $object->id)->address2 . '"></td></tr>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('Zip') . '</td><td><input type=text name="zip" value=' . $rhManager->get("zip", $object->id)->zip . '></td></tr>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('City') . '</td><td><input type=text name="city" value="' . $rhManager->get("city", $object->id)->city . '"></td></tr>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('TelFixe') . '</td><td><input type=text name="telFixe" value="' . $rhManager->get("telFixe", $object->id)->telFixe . '"></td></tr>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('TelPortable') . '</td><td><input type=text name="telPortable" value="' . $rhManager->get("telPortable", $object->id)->telPortable . '"></td></tr>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('Contact') . '</td><td><input type=text name="contact" value="' . $rhManager->get("contact", $object->id)->contact . '"></td></tr>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('TelContact1') . '</td><td><input type=text name="telContact1" value="' . $rhManager->get("telContact1", $object->id)->telContact1 . '"></td></tr>';
	print '<tr><td class="titlefieldcreate">' . $langs->trans('TelContact2') . '</td><td><input type=text name="telContact2" value="' . $rhManager->get("telContact2", $object->id)->telContact2 . '"></td></tr>';

	print "</table>";
	print '<div class="center">';
	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	print '<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
	print '</form>';
}

else {
	$linkback = '<a href="'.dol_buildpath('/user/index.php', 1).'">'.$langs->trans("BackToList").'</a>';
	dol_banner_tab($object,'id',$linkback,$user->rights->user->user->lire || $user->admin);

	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print "<div class='underbanner clearboth'></div>";
	print '<table class="border tableforfield" width="90%">';
	print '<tbody>';
	print '<tr>';
	print '<td class="titlefield">Genre</td>';
	print '<td>'.$langs->trans($object->gender).'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">Date d\'embauche</td>';
	print '<td>'.date('d/m/Y', $object->dateemployment).'</td>';
	print '</tr>';

	$anciennete =  strtotime(date("Y-m-d")) - strtotime(date("Y-m-d", $object->dateemployment));
	$jour = (int)($anciennete/86400);
	$année = (int)($jour/31536000);
	$jour = $jour - 365*$annee;

	print '<tr>';
	print '<td class="titlefield">Ancienneté</td>';
	print '<td>'.$année." ".$langs->trans('years')." ".$jour.' '.$langs->trans('days').'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">Date de naissance</td>';
	print '<td>'.date('d/m/Y', strtotime($object->array_options['options_DDN'])).'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">Situation de famille</td>';
	print '<td>'.$object->array_options['options_SIT_FAM'].'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">Nombre d\'enfant à charge</td>';
	print '<td>'.$object->array_options['options_NB_ENF_CHARGE'].'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">Horaire contractuel</td>';
	print '<td>'.$object->array_options['options_HORAIRE'].'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">Statut</td>';
	print '<td>'.$object->array_options['options_STATUT'].'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">Niveau de classification</td>';
	print '<td>'.$object->array_options['options_NIVEAU'].'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">Contrat</td>';
	print '<td>'.$object->array_options['options_CONTRAT'].'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">Fonction</td>';
	print '<td>'.$object->array_options['options_FONCTION'].'</td>';
	print '</tr>';

	print '</tbody>';
	print '</table>';

	print '<div class="tabsAction">';
	print '<div class="inline-block divButAction">';
	print '<a class="butAction" href="'.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id.'&action=modify">'.$langs->trans('Modify').'</a>';
	print '</div>';
	print '</div>';

    print '</div>';

    print '<div class="fichehalfright">';
    print "<div class='underbanner clearboth'></div>";

	print '<table class="border tableforfield" width="90%">';
	print '<tbody>';

	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('Present').'</td>';
	print '<td>'.$rhManager->get("present", $object->id)->present.'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('Salary').'</td>';
	print '<td>'.$rhManager->get("salary", $object->id)->salary.'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('SalaryMonth').'</td>';
	print '<td>'.$rhManager->get("salary_brut", $object->id)->salary_brut.'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('Address1').'</td>';
	print '<td>'.$rhManager->get("address1", $object->id)->address1.'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('Address2').'</td>';
	print '<td>'.$rhManager->get("address2", $object->id)->address2.'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('Zip').'</td>';
	print '<td>'.$rhManager->get("zip", $object->id)->zip.'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('City').'</td>';
	print '<td>'.$rhManager->get("city", $object->id)->city.'</td>';
	print '</tr>';


	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('TelFixe').'</td>';
	print '<td>'.$rhManager->get("telFixe", $object->id)->telFixe.'</td>';
	print '</tr>';


	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('TelPortable').'</td>';
	print '<td>'.$rhManager->get("telPortable", $object->id)->telPortable.'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('Contact').'</td>';
	print '<td>'.$rhManager->get("contact", $object->id)->contact.'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('TelContact1').'</td>';
	print '<td>'.$rhManager->get("telContact1", $object->id)->telContact1.'</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="titlefield">'.$langs->trans('TelContact2').'</td>';
	print '<td>'.$rhManager->get("telContact2", $object->id)->telContact2.'</td>';
	print '</tr>';

	print '</tbody>';
	print '</table>';

	print '</div>';

	print '<table width="100%">';
	print '<tbody><tr><td class="nobordernopadding" valign="middle"><div class="titre">'.$langs->trans('medicalVisites').'</div></td></tr></tbody>';
	print '</table>';

	print '<form action="' . $_SERVER["PHP_SELF"] . '?id='.$idUser.'" method=POST>';
	print '<input name="action" value="new_med" type="hidden">';
	print '<table class="noborder" width="100%">';
	print '<tbody>';
	print '<tr class="liste_titre">';
	print '<th class="liste_titre" width="25%">Ajouter une visite médicale</th>';
	print '<th align="right"><input title="Date de passage de la visite médicale" id="date_visit" name="date_visit" class="maxwidth75" maxlength="11" value="2018-08-01" type="text"></th>';
	print '<th align="right"><input type=text name=commentaire placeholder=Commentaire : ></th>';
	print '<th align="right"><input type=submit class=button value=Ajouter></th>';
	print '</tr>';

	print '<tr class="liste_titre">';
	print '<th class="liste_titre">Date de la visite</th>';
	print '<th class="liste_titre">Commentaire</th>';
	print '<th align="right" colspan=2></th>';
	print '</tr>';
	print '<tr class="oddeven">';

	if ($medVisits->num_rows) {

		foreach ($medVisits as $oneMed) {
			print '<tr class="oddeven">';
			print '<td>';
			print date("d/m/Y", strtotime($oneMed['date_visit']));
			print '</td>';
			print '<td>';
			print $oneMed['commentaire'];
			print '</td>';
			print '<td align="right" colspan=2>';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=del_med&med='.$oneMed['rowid'].'">'.img_delete().'</a>';
			print '</td>';
			print '</tr>';
		}
	}

	else {
		print '<tr class="oddeven">';
		print '<td colspan=4>-- Aucune visite enregistrée --</td>';
		print '</tr>';
	}

	print '</tbody>';
	print '</table>';
	print '</form>';

	print '<table>';
	print '<tbody><tr><td class="nobordernopadding" valign="middle"><div class="titre">'.$langs->trans('habilitations').'</div></td></tr></tbody>';
	print '</table>';

	print '<form action="' . $_SERVER["PHP_SELF"] . '?id='.$idUser.'" method=POST>';
	print '<input name="action" value="new_hab" type="hidden"><table class="noborder" width="100%">';
	print '<tbody>';
	print '<tr class="liste_titre">';
	print '<th class="liste_titre" width="15%">Ajouter une habilitation</th>';
	print '<th align="right"><input type=text name=numero placeholder=Numéro : ></th>';
	print '<th align="right">'.$rhManager->getLabelHabilitations().'</th>';
	print '<th align="right"><input title="Date d\'obtention de l\'habilitation" id="date_hab" name="date_hab" class="maxwidth75" maxlength="11" value="2018-08-01" type="text"></th>';
	print '<th align="right"><input title="Date de fin de l\'habilitation" id="date_hab_fin" name="date_hab_fin" class="maxwidth75" maxlength="11" value="2018-08-01" type="text"></th>';
	print '<th align="right"><input type=submit class=button value=Ajouter></th>';
	print '</tr>';
	print '<tr class="oddeven">';

	print '<tr class="liste_titre">';
	print '<th class="liste_titre">Date de début</th>';
	print '<th class="liste_titre">Numéro</th>';
	print '<th class="liste_titre">Libelle</th>';
	print '<th class="liste_titre">Date de fin</th>';
	print '<th align="right" colspan=3></th>';
	print '</tr>';
	print '<tr class="oddeven">';

	if ($habilitations->num_rows) {

		foreach ($habilitations as $habilitation) {
			print '<tr class="oddeven">';
			print '<td>';
			print date("d/m/Y", strtotime($habilitation['date_hab']));
			print '</td>';
			print '<td>';
			print $habilitation['numero'];
			print '</td>';
			print '<td>';
			print $habilitation['label'];
			print '</td>';
			print '<td>';
			print date("d/m/Y", strtotime($habilitation['date_fin']));
			print '</td>';
			print '<td align="right" colspan=2>';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=del_hab&hab='.$habilitation['rowid'].'">'.img_delete().'</a>';
			print '</td>';
			print '</tr>';
		}
	}

	else {
		print '<tr class="oddeven">';
		print '<td colspan=7>-- Aucune habilitation enregistrée --</td>';
		print '</tr>';
	}

	print '</td>';
	print '</tr>';
	print '</tbody>';
	print '</table>';
	print '</form>';

	print '<table>';
	print '<tbody><tr><td class="nobordernopadding" valign="middle"><div class="titre">'.$langs->trans('entretiens').'</div></td></tr></tbody>';
	print '</table>';

	print '<form action="' . $_SERVER["PHP_SELF"] . '?id='.$idUser.'" method=POST>';
	print '<input name="action" value="new_ent" type="hidden"><table class="noborder" width="100%">';
	print '<tbody>';
	print '<tr class="liste_titre">';
	print '<th class="liste_titre" width="25%">Ajouter un entretien</th>';
	print '<th align="right"><input title="Date de l\'entretien" id="date_ent" name="date_ent" class="maxwidth75" maxlength="11" value="2018-08-01" type="text"></th>';
	print '<th align="right"><input type=text name=commentaire placeholder=Commentaires : ></th>';
	print '<th align="right"><input type=submit class=button value=Ajouter></th>';
	print '</tr>';
	print '<tr class="oddeven">';

	print '<tr class="liste_titre">';
	print '<th class="liste_titre">Date de l\'entretien</th>';
	print '<th class="liste_titre">Commentaire</th>';
	print '<th align="right" colspan=2></th>';
	print '</tr>';
	print '<tr class="oddeven">';

	if ($entretiens->num_rows) {

		foreach ($entretiens as $entretien) {
			print '<tr class="oddeven">';
			print '<td>';
			print date("d/m/Y", strtotime($entretien['date_ent']));
			print '</td>';
			print '<td>';
			print $entretien['commentaire'];
			print '</td>';
			print '<td align="right" colspan=2>';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=del_ent&ent='.$entretien['rowid'].'">'.img_delete().'</a>';
			print '</td>';
			print '</tr>';
		}
	}

	else {
		print '<tr class="oddeven">';
		print '<td colspan=4>-- Aucun entretien enregistré --</td>';
		print '</tr>';
	}

	print '</td>';
	print '</tr>';
	print '</tbody>';
	print '</table>';
	print '</form>';

	print '<table>';
	print '<tbody><tr><td class="nobordernopadding" valign="middle"><div class="titre">'.$langs->trans('Primes').'</div></td></tr></tbody>';
	print '</table>';

	print '<form action="' . $_SERVER["PHP_SELF"] . '?id='.$idUser.'" method=POST>';
	print '<input name="action" value="new_prime" type="hidden"><table class="noborder" width="100%">';
	print '<tbody>';
	print '<tr class="liste_titre">';
	print '<th class="liste_titre" width="25%">Ajouter une prime</th>';
	print '<th align="right"><input title="Date d\'attribution" id="date_prime" name="date_prime" class="maxwidth75" maxlength="11" value="2018-08-01" type="text"></th>';
	print '<th align="right"><input type=text name=montant placeholder=Montant : ></th>';
	print '<th align="right"><input type=submit class=button value=Ajouter></th>';
	print '</tr>';
	print '<tr class="oddeven">';

	print '<tr class="liste_titre">';
	print '<th class="liste_titre">Date d\'attribution de la prime</th>';
	print '<th class="liste_titre">Montant</th>';
	print '<th align="right" colspan=2></th>';
	print '</tr>';
	print '<tr class="oddeven">';

	if ($primes->num_rows) {

		foreach ($primes as $prime) {
			print '<tr class="oddeven">';
			print '<td>';
			print date("d/m/Y", strtotime($prime['date_prime']));
			print '</td>';
			print '<td>';
			print $prime['montant']." €";
			print '</td>';
			print '<td align="right" colspan=2>';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=del_prime&prime='.$prime['rowid'].'">'.img_delete().'</a>';
			print '</td>';
			print '</tr>';
		}
	}

	else {
		print '<tr class="oddeven">';
		print '<td colspan=4>-- Aucune prime enregistrée --</td>';
		print '</tr>';
	}

	print '</td>';
	print '</tr>';
	print '</tbody>';
	print '</table>';
	print '</form>';

	print '<table>';
	print '<tbody><tr><td class="nobordernopadding" valign="middle"><div class="titre">'.$langs->trans('Trainings').'</div></td></tr></tbody>';
	print '</table>';

	print '<table class="noborder" width="100%">';

	print '<tr class="liste_titre">';
	print '<th class="liste_titre">Référence</th>';
	print '<th class="liste_titre">Libelle</th>';
	print '<th class="liste_titre">Fournisseur</th>';
	print '<th class="liste_titre">Date de Début</th>';
	print '<th class="liste_titre">Date de Fin</th>';
	print '</tr>';

	print '<tr class="oddeven">';

	if (!empty($listFormation['year'])) {

		foreach ($listFormation['year'] as $formation) {
			$fournisseur = new Fournisseur($db);
			$fournisseur->fetch($formation->fk_product_fournisseur_price->fourn_id);

			print '<tr class="oddeven">';
			print '<td>';
			print $formation->getNomUrl(1);
			print '</td>';
			print '<td>';
			print $formation->label;
			print '</td>';
			print '<td>';
			print $fournisseur->getNomUrl(1);
			print '</td>';
			print '<td>';
			print date("d/m/y", strtotime($formation->dated));
			print '</td>';
			print '<td>';
			print date("d/m/y", strtotime($formation->datef));
			print '</td>';
			print '</tr>';
		}

	}

	else {
		print '<tr class="oddeven">';
		print '<td colspan=5>-- Aucune formation enregistrée --</td>';
		print '</tr>';
	}

	print '</tr>';
	print '</table>';

	print '</div>';
	print '</div>';
}

llxfooter();

?>