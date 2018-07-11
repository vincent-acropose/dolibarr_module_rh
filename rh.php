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
dol_include_once('/rh/class/rh.class.php');

if(empty($user->rights->rh->read)) accessforbidden();

$langs->load('rh@rh');

$action = GETPOST('action');
$idUser = GETPOST('id', 'int');

$object = new User($db);
$object->fetch($idUser);

$rhManager = new Rh($db);

$medVisits = $rhManager->getMed($idUser);
$habilitations = $rhManager->getHabilitations($idUser);

// Action
switch ($action) {
	case 'edit':
		$salary = GETPOST('salary');
		if($rhManager->set('salary', $salary, $object->id)) {
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

	case 'del_med':
		$med = GETPOST('med');
		$rhManager->delMed($med);

		header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);

	case 'new_hab':
		$date = GETPOST('date_hab');
		$intitule = GETPOST('intitule');

		$rhManager->setHab($date, $intitule, $idUser);

	case 'del_hab':
		$hab = GETPOST('hab');
		$rhManager->delHab($hab);


	header('Location: '.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id);
}

// Vue
llxHeader('',$langs->trans("RHCard"));
$head = user_prepare_head($object);
$picto = 'user';

dol_fiche_head($head, 'rh', $langs->trans("User"), -1, $picto);

if ($action == "modify") {
	print '<form method=POST action="' . $_SERVER["PHP_SELF"] . '?id='.$object->id.'">';
	print '<input type=hidden name="action" value="edit">';
	print '<table class="border" width="100%">';

	// Salary
	print '<tr><td class="titlefieldcreate">' . $langs->trans('Salary') . '</td><td><input type=text name="salary" value=' . $rhManager->get("salary", $object->id)->salary . '></td></tr>';

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
	print '<div class="underbanner clearboth"></div>';
	print '<table class="border tableforfield" width="100%">';
	print '<tbody>';
	print '<tr>';

	print '<td class="titlefield">'.$langs->trans('Salary').'</td>';
	print '<td>'.$rhManager->get("salary", $object->id)->salary.'</td>';

	print '</tr>';
	print '</tbody>';
	print '</table>';

	print '<div class="tabsAction">';
	print '<div class="inline-block divButAction">';
	print '<a class="butAction" href="'.dol_buildpath('/rh/rh.php', 1).'?id='.$object->id.'&action=modify">'.$langs->trans('Modify').'</a>';
	print '</div>';
	print '</div>';

	print '<table>';
	print '<tbody><tr><td class="nobordernopadding" valign="middle"><div class="titre">Visites médicales</div></td></tr></tbody>';
	print '</table>';

	print '<form action="' . $_SERVER["PHP_SELF"] . '?id='.$idUser.'" method=POST>';
	print '<input name="action" value="new_med" type="hidden">';
	print '<table class="noborder" width="100%">';
	print '<tbody>';
	print '<tr class="liste_titre">';
	print '<th class="liste_titre" width="25%">Ajouter une visite médicale</th>';
	print '<th align="right"><input type=date name=date_visit></th>';
	print '<th align="right"><input type=text name=commentaire placeholder=Commentaire : ></th>';
	print '<th align="right"><input type=submit class=button value=Ajouter></th>';
	print '</tr>';

	if ($medVisits->num_rows) {

		foreach ($medVisits as $oneMed) {
			print '<tr class="oddeven">';
			print '<td>';
			print date("d/m/Y", strtotime($oneMed['date_visit']));
			print '</td>';
			print '<td colspan=2>';
			print $oneMed['commentaire'];
			print '</td>';
			print '<td align="right">';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=del_med&med='.$oneMed['rowid'].'"><img src="/dolibarr/htdocs/theme/eldy/img/delete.png" alt="" title="Supprimer la visite" class="pictodelete"></a>';
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
	print '<tbody><tr><td class="nobordernopadding" valign="middle"><div class="titre">Habilitations</div></td></tr></tbody>';
	print '</table>';

	print '<form action="' . $_SERVER["PHP_SELF"] . '?id='.$idUser.'" method=POST>';
	print '<input name="action" value="new_hab" type="hidden"><table class="noborder" width="100%">';
	print '<tbody>';
	print '<tr class="liste_titre">';
	print '<th class="liste_titre" width="25%">Ajouter une habilitation</th>';
	print '<th align="right"><input type=text name=intitule placeholder=Intitulé : ></th>';
	print '<th align="right"><input type=date name=date_hab></th>';
	print '<th align="right"><input type=submit class=button value=Ajouter></th>';
	print '</tr>';
	print '<tr class="oddeven">';

	if ($habilitations->num_rows) {

		foreach ($habilitations as $habilitation) {
			print '<tr class="oddeven">';
			print '<td>';
			print date("d/m/Y", strtotime($habilitation['date_hab']));
			print '</td>';
			print '<td colspan=2>';
			print $habilitation['label'];
			print '</td>';
			print '<td align="right">';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=del_hab&hab='.$habilitation['rowid'].'"><img src="/dolibarr/htdocs/theme/eldy/img/delete.png" alt="" title="Supprimer l\'habilitation" class="pictodelete"></a>';
			print '</td>';
			print '</tr>';
		}
	}

	else {
		print '<tr class="oddeven">';
		print '<td colspan=4>-- Aucune habilitation enregistrée --</td>';
		print '</tr>';
	}

	print '</td>';
	print '</tr>';
	print '</tbody>';
	print '</table>';
	print '</form>';

	print '</div>';
	print '</div>';
}

llxfooter();

?>