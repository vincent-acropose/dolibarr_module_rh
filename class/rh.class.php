<?php

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

class Rh extends CommonObject
{

	public $table_element='rh';
	public $table_medicale="rh_med";
	public $table_habiliations='rh_hab';

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
			return -1;
		}
	}

	public function getRowid() {
		$sql = "SELECT MAX(rowid) as maxrowid FROM ".MAIN_DB_PREFIX.$this->table_element;
		$result = $this->request($sql);

		return $result->maxrowid + 1;
	}

	public function set($key, $value, $userId) {

		$sql = "SELECT * FROM ".MAIN_DB_PREFIX.$this->table_element." WHERE fk_user=".$userId;
		$result1 = $this->request($sql);

		if ($result1) {
			$sql = 'UPDATE '.MAIN_DB_PREFIX.$this->table_element.' SET '.$key.'='.$value.' WHERE fk_user='.$userId;
		}
		else {
			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.$this->table_element.' (rowid, fk_user, '.$key.') VALUES ('.$this->getRowid().', '.$userId.', '.$value.')';
		}

		$result = $this->request($sql, 1);
		return $result;

	}

	public function getMed($userId) {
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX.$this->table_medicale." WHERE fk_user=".$userId;

		$result = $this->request($sql, 0, "*");
		return $result;
	}

	public function getHabilitations($userId) {
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX.$this->table_habiliations." WHERE fk_user=".$userId;

		$result = $this->request($sql, 0, "*");
		return $result;
	}

	public function setMed($date, $commentaire, $userId) {
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_medicale.' (fk_user, date_visit, commentaire) VALUES ('.$userId.', "'.$date.'", "'.$commentaire.'")';

		$result = $this->request($sql, 1);
		return $result;
	}

	public function setHab($date, $intitule, $userId) {
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_habiliations.' (fk_user, date_hab, label) VALUES ('.$userId.', "'.$date.'", "'.$intitule.'")';
		
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

	public function getNextId() {
		$id = $this->request("SELECT MAX(rowid) AS rowid FROM ".MAIN_DB_PREFIX.$this->table_element);

		is_null($id) ? $id = 1 : $id = $id+1;

		return $id;
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