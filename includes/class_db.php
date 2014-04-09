<?php

class database {
	protected $link, $result, $num_rows;
	
	public function __construct($server, $username, $password, $db) {
		if ((isset($server)) && (isset($username)) && (isset($password)) && (isset($db))) {
			$this->link = mysql_connect($server, $username, $password);
			if (mysql_select_db($db, $this->link)) {
				mysql_query("SET NAMES utf8");
				mysql_query("SET character_set_results=’utf8′");
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	public function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	
	public function disconnect() {
		if (mysql_close($this->link)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function query($qry) {
		if ($this->result = mysql_query($qry, $this->link)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function num_rows() {
		if ($this->num_rows = mysql_num_rows($this->result)) {
			return $this->num_rows;
		}
		else {
			return false;
		}
	}

	public function rows() {
		if (!empty($this->result)) {
			$rows = array();
			while ($row = mysql_fetch_assoc($this->result)) {
				$rows[] = $row;
			}
			return $rows;
		}
		else {
			return false;
		}
	}
	
	public function get_single_row() {
		if (!empty($this->result)) {
			if ($row = mysql_fetch_assoc($this->result)) {
				return $row;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
}

?>
