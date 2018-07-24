<?php

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

class Rh extends CommonObject
{

	public $table_element='rh';
	public $table_medicale="rh_med";
	public $table_habiliations='rh_hab';
	public $table_entretiens='rh_ent';
	public $table_primes='rh_prime';

	public function __construct($db)
	{
		global $langs;
		
		$this->db = $db;
	}

	public function get($key, $userId) {
		$sql = 'SELECT '.$key.' FROM '.MAIN_DB_PREFIX.$this->table_element.' WHERE fk_user='.(int)$userId;
		$result = $this->request($sql);
		if ($result) {
			return $result;
		}
		else {
			return 0;
		}
	}

	public function getRowid() {
		$sql = "SELECT MAX(rowid) as maxrowid FROM ".MAIN_DB_PREFIX.$this->table_element;
		$result = $this->request($sql);

		return $result->maxrowid + 1;
	}

	public function set($values, $userId) {

		$sql = "SELECT * FROM ".MAIN_DB_PREFIX.$this->table_element." WHERE fk_user=".$userId;
		$result1 = $this->request($sql);

		if (!$result1) {
			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.$this->table_element.' (rowid, fk_user) VALUES ('.$this->getRowid().', '.$userId.')';
			$this->request($sql, 1);
		}

		if (is_array($values)) {
			$sql = 'UPDATE '.MAIN_DB_PREFIX.$this->table_element.' SET';

			if ($values["salary"]) {
				$sql .= ' salary="'.$values['salary'].'"';
			}
			else {
				$sql .= ' salary=NULL';
			}
			if ($values["salary_brut"]) {
				$sql .= ', salary_brut="'.$values['salary_brut'].'"';
			}
			else {
				$sql .= ', salary_brut=NULL';
			}
			if ($values["address1"]) {
				$sql .= ', address1="'.$values['address1'].'"';
			}
			else {
				$sql .= ', address1=NULL';
			}
			if ($values["address2"]) {
				$sql .= ', address2="'.$values['address2'].'"';
			}
			else {
				$sql .= ', address2=NULL';
			}
			if ($values["zip"]) {
				$sql .= ', zip="'.$values['zip'].'"';
			}
			else {
				$sql .= ', zip=NULL';
			}
			if ($values["city"]) {
				$sql .= ', city="'.$values['city'].'"';
			}
			else {
				$sql .= ', city=NULL';
			}
			if ($values["telFixe"]) {
				$sql .= ', telFixe="'.$values['telFixe'].'"';
			}
			else {
				$sql .= ', telFixe=NULL';
			}
			if ($values["telPortable"]) {
				$sql .= ', telPortable="'.$values['telPortable'].'"';
			}
			else {
				$sql .= ', telPortable=NULL';
			}
			if ($values["contact"]) {
				$sql .= ', contact="'.$values['contact'].'"';
			}
			else {
				$sql .= ', contact=NULL';
			}
			if ($values["telContact1"]) {
				$sql .= ', telContact1="'.$values['telContact1'].'"';
			}
			else {
				$sql .= ', telContact1=NULL';
			}
			if ($values["telContact2"]) {
				$sql .= ', telContact2="'.$values['telContact2'].'"';
			}
			else {
				$sql .= ', telContact2=NULL';
			}
			if ($values["present"]) {
				$sql .= ', present="'.$values['present'].'"';
			}
			else {
				$sql .= ', present=NULL';
			}
			$sql .= ' WHERE fk_user='.$userId;
		}

		else {
			return -1;
		}

		$result = $this->request($sql, 1);
		return $result;

	}

	public function getMed($userId) {
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX.$this->table_medicale." WHERE fk_user=".$userId." ORDER BY date_visit DESC";

		$result = $this->request($sql, 0, "*");
		return $result;
	}

	public function getLastMed($userId) {
		$sql = "SELECT MAX(date_visit) AS 'date_visit', commentaire FROM ".MAIN_DB_PREFIX.$this->table_medicale." WHERE fk_user=".$userId." GROUP BY commentaire";
		$result = $this->request($sql);
		return $result;
	}

	public function getHabilitations($userId) {
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX.$this->table_habiliations." WHERE fk_user=".$userId." ORDER BY date_hab DESC";

		$result = $this->request($sql, 0, "*");
		return $result;
	}

	public function getEntretiens($userId) {
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX.$this->table_entretiens." WHERE fk_user=".$userId." ORDER BY date_ent DESC";

		$result = $this->request($sql, 0, "*");
		return $result;
	}

	public function getPrimes($userId) {
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX.$this->table_primes." WHERE fk_user=".$userId." ORDER BY date_prime DESC";

		$result = $this->request($sql, 0, "*");
		return $result;
	}

	public function setMed($date, $commentaire, $userId) {
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_medicale.' (fk_user, date_visit, commentaire) VALUES ('.$userId.', "'.date("Y-m-d", strtotime($date)).'", "'.$commentaire.'")';

		$result = $this->request($sql, 1);
		return $result;
	}

	public function setHab($numero, $date, $datefin, $intitule, $userId) {
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_habiliations.' (numero, fk_user, date_hab, date_fin, label) VALUES ("'.$numero.'", '.$userId.', "'.$date.'", "'.$datefin.'", "'.$intitule.'")';
		
		$result = $this->request($sql, 1);
		return $result;
	}

	public function setEnt($date, $commentaire, $idUser) {
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_entretiens.' (date_ent, commentaire, fk_user) VALUES ("'.$date.'", "'.$commentaire.'", '.$idUser.')';
		$result = $this->request($sql, 1);
		return $result;
	}

	public function setPrime($date, $montant, $idUser) {
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_primes.' (date_prime, montant, fk_user) VALUES ("'.$date.'", "'.$montant.'", '.$idUser.')';
		$result = $this->request($sql, 1);

		return $result;
	}

	public function delMed($idMed) {
		$sql = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_medicale.' WHERE rowid='.$idMed;

		$result = $this->request($sql, 1);
		return $result;
	}

	public function delHab($idHab) {
		$sql = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_habiliations.' WHERE rowid='.$idHab;

		$result = $this->request($sql, 1);
		return $result;
	}

	public function delEnt($idEnt) {
		$sql = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_entretiens.' WHERE rowid='.$idEnt;

		$result = $this->request($sql, 1);
		return $result;
	}

	public function delPrime($idPrime) {
		$sql = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_primes.' WHERE rowid='.$idPrime;

		$result = $this->request($sql, 1);
		return $result;
	}

	public function getNextId() {
		$id = $this->request("SELECT MAX(rowid) AS rowid FROM ".MAIN_DB_PREFIX.$this->table_element);

		is_null($id) ? $id = 1 : $id = $id+1;

		return $id;
	}

	public function getLabelHabilitations() {
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX."rh_hab_name";
	    $habilitations = $this->request($sql, 0, "*");

	    $value = "<select name=intitule>";

	    foreach ($habilitations as $habilitation) {
	        $value .= "<option value='".$habilitation['label']."'>".$habilitation['label']."</option>";
	    }

	    $value .= "</select>";
	    return $value;
	}

	public function get_last_hab($label, $userId) {
		$sql = "SELECT MAX(date_hab) AS 'date_hab', numero, date_fin FROM ".MAIN_DB_PREFIX.$this->table_habiliations." WHERE fk_user=".$userId." AND label = '".trim($label)."' GROUP BY numero, date_fin";
		$result = $this->request($sql);

		if ($result) {
			$retour = ";".$result->numero.";".$result->date_hab.";".$result->date_fin;
		}
		else {
			$retour = ";-;-;-";
		}

		return $retour;
	}

	public function makeCsv($type, $user, $userId="0") {
		$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.$this->table_element;


		$usersRh = $this->request($sql, 0, "*");
		$newUser = new User($this->db);
		
		switch ($type) {

			case '1':
				$contains = "Contrat;Nom;Prénom;Sexe;Adresse1;Adresse2;CP;Ville;Date d'embauche;Date d'ancienneté;Ancienneté;Fonction;Niveau;Status;Date de naissance;age\n";
				foreach ($usersRh as $userRh) {
					if ($userRh['present'] == "Oui") {
						$newUser->fetch($userRh['fk_user']);

						if (($newUser->array_options['options_prod_or_not'] == 1 && $user->rights->rh->production) or ($newUser->array_options['options_prod_or_not'] == 2 && $user->rights->rh->notProduction)) {

							$anciennete =  strtotime(date("Y-m-d")) - strtotime($newUser->array_options['options_DDA']);
							$anneeAnciennete = (int)($anciennete/31536000);
							$jourAnciennete = (int)(($anciennete/86400)%365);

							$age = strtotime(date("Y-m-d")) - strtotime(date("d/m/Y", strtotime($newUser->array_options['options_DDN'])));
							$ageYear = (int)($age/31536000);
							$contains .= $newUser->array_options['options_CONTRAT'];
							$contains .= ";".$newUser->lastname;
							$contains .= ";".$newUser->firstname;
							$contains .= ";".$newUser->gender;
							$contains .= ";".$userRh['address1'];
							$contains .= ";".$userRh['address2'];
							$contains .= ";".$userRh['zip'];
							$contains .= ";".$userRh['city'];
							$contains .= ";".date('d/m/Y', $newUser->dateemployment);
							$contains .= ";".date("d/m/Y", strtotime($newUser->array_options['options_DDA']));
							$contains .= ";".$anneeAnciennete." ans ".$jourAnciennete." jours";
							$contains .= ";".$newUser->array_options['options_FONCTION'];
							$contains .= ";".$newUser->array_options['options_NIVEAU'];
							$contains .= ";".$newUser->array_options['options_STATUT'];
							$contains .= ";".date("d/m/Y", strtotime($newUser->array_options['options_DDN']));
							$contains .= ";".$ageYear." ans";
							$contains .= "\n";

						}
					}
				}

				$f = fopen(DOL_DATA_ROOT."/rh/liste_utilisateurs.csv", "w");
				fwrite($f, $contains);
				fclose($f);

				break;

			case '2':
				$contains = "Collaborateur";

				$sql = "SELECT * FROM ".MAIN_DB_PREFIX."rh_hab_name";
			    $habilitations = $this->request($sql, 0, "*");

			    $listHab = [];
			    $value = "<select name=intitule>";

			    foreach ($habilitations as $habilitation) {
			    	$contains .= ";numéro_".trim($habilitation['label']).";Date d'obtention;Date de fin de validité";
			    	array_push($listHab, trim($habilitation['label']));			        
			    }
				$contains .= "\n";

				foreach ($usersRh as $userRh) {
					if ($userRh['present'] == "Oui") {
						$newUser->fetch($userRh['fk_user']);

						if (($newUser->array_options['options_prod_or_not'] == 1 && $user->rights->rh->production) or ($newUser->array_options['options_prod_or_not'] == 2 && $user->rights->rh->notProduction)) {

							$contains .= $newUser->login;

							foreach ($listHab as $habName) {
								$contains .= $this->get_last_hab($habName, $newUser->id);
							}

							$contains .= "\n";

						}
					}
				}

				$f = fopen(DOL_DATA_ROOT."/rh/habilitations.csv", "w");
				fwrite($f, $contains);
				fclose($f);

				break;

			case '3':
				$contains = "Trigramme;Date de la Visite;Commentaire\n";

				foreach ($usersRh as $userRh) {
					if ($userRh['present'] == "Oui") {
						$newUser->fetch($userRh['fk_user']);
						if (($newUser->array_options['options_prod_or_not'] == 1 && $user->rights->rh->production) or ($newUser->array_options['options_prod_or_not'] == 2 && $user->rights->rh->notProduction)) {

							$contains .= $newUser->login;
							$visit = $this->getLastMed($newUser->id);

							$contains .= ";".$visit->date_visit.";".$visit->commentaire."\n";

						}
					}
				}

				$f = fopen(DOL_DATA_ROOT."/rh/visites.csv", "w");
				fwrite($f, $contains);
				fclose($f);

				break;

			case '4':
				$contains = "Date d'attribution de la prime;Montant\n";

				$primes = $this->getPrimes((int)$userId);

				foreach ($primes as $prime) {
					$contains .= $prime['date_prime'].";".$prime['montant']." €\n";
				}

				$f = fopen(DOL_DATA_ROOT."/rh/primes.csv", "w");
				fwrite($f, $contains);
				fclose($f);

				break;
		}
	}

	/* ----------------------------- */
	/* ---------- METHODS ---------- */
	/* ----------------------------- */

	/**
	 * function request 
	 * 		$request => Requête à effectué sur la base de donnée
	 * 		$type => 0:(SELECT), 1:(INSERT, UPDATE, DELETE)
	 */
	public function request($request, $type=0, $line=1) {

		switch ($type) {
			case 0:
				$result = $this->db->query($request);
				if ($result) {
					if ($line == 1) {
						return $this->db->fetch_object($result);
					}
					else {
						return $result;
					}
				}
				else {
					return -1;
				}

				break;

			case 1:
				return $this->db->query($request);
				break;
			
			default:
				return -1;
				break;
		}

	}
}