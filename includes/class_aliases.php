<?php

class aliases {
	public $db;
	private $aliases_for_domain;
	private $num_aliases_for_domain;
	private $aliases_for_destination;
	
	
	public function __construct() {
		if ($this->db = new database(constant('DB_HOST'), constant('DB_USER'), constant('DB_PASSWORD'), constant('DB_DATABASE'))) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function get_aliases_for_domain($domainid) {
		if (!empty($domainid)) {
			if (is_numeric($domainid)) {
				if ($this->db->query("SELECT id,source,destination FROM virtual_aliases WHERE domain_id='$domainid'")) {
					if ($this->aliases_for_domain = $this->db->rows()) {
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
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	public function num_aliases_for_domain($domainid) {
		if (!empty($domainid)) {
			if (is_numeric($domainid)) {
				if (empty($this->aliases_for_domain)) {
					$this->get_aliases_for_domain($domainid);
				}
				return count ($this->aliases_for_domain);
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	public function echo_alias_list($domainid) {
		if ($this->num_aliases_for_domain($domainid) > 0) {
			echo '<div>';
				foreach ($this->aliases_for_domain as $alias) {
					echo '<div class="returned_row">';
						echo '<a href="?page=aliases&domainid=' . $domainid . '&delete=' . $alias['id'] . '" onClick="if(confirm(\'Really delete the alias???\')) { window.location=\'?page=aliases&domainid=' . $domainid . '&delete=' . $alias['id'] . '\' } else { return false; }"><div class="delete_button"></div></a>';
						echo '<div class="row_content"> - ' . $alias['source'] . ' -> ' . $alias['destination'] . '</div>';
					echo '<div class="clear_float"></div>';
					echo '</div>';
				}
			echo '</div>';
			return true;
		}
		else {
			return false;
		}
	}
	
	private function does_alias_exist($alias_source, $alias_destination) {
		if (!empty($alias_source) && !empty($alias_destination)) {
			if ($this->db->query("SELECT id FROM virtual_aliases WHERE source='$alias_source' AND destination='$alias_destination'")) {
				if ($this->db->num_rows() > 0) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return true;
			}
		}
		else {
			return true;
		}
	}
	
	public function create_alias($new_source, $new_destination, $domainid) {
		if (!empty($new_source) && !empty($new_destination) && !empty($domainid)) {
			if (is_numeric($domainid)) {
				if ((strpos($new_source,'.') !== false) && (strpos($new_source,'@') !== false) && (strpos($new_destination,'.') !== false) && (strpos($new_destination,'@') !== false)) {
					if (!$this->does_alias_exist($new_source, $new_destination)) {
						if ($this->db->query("INSERT INTO virtual_aliases SET source='$new_source', destination='$new_destination', domain_id='$domainid'")) {
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
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	public function delete_alias($aliasid_to_delete) {
		if (!empty($aliasid_to_delete)) {
			if ($this->db->query("DELETE FROM virtual_aliases WHERE id='$aliasid_to_delete'")) {
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
	
	public function get_emailaddress_for_emailid($emailid) {
		if (!empty($emailid)) {
			if (is_numeric($emailid)) {
				if ($this->db->query("SELECT email FROM virtual_users WHERE id='$emailid'")) {
					if ($row = $this->db->get_single_row()) {
						return $row['email'];
					}
					else {
						return false;
					}
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	public function get_domainid_for_emailid($emailid) {
		if (!empty($emailid)) {
			if (is_numeric($emailid)) {
				if ($this->db->query("SELECT domain_id FROM virtual_users WHERE id='$emailid'")) {
					if ($row = $this->db->get_single_row()) {
						return $row['domain_id'];
					}
					else {
						return false;
					}
				}
				else {
						echo "test";
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	public function get_aliases_for_emaildestination($emailaddress) {
		if (!empty($emailaddress)) {
			if ($this->db->query("SELECT id,source,destination FROM virtual_aliases WHERE destination='$emailaddress'")) {
				if ($this->aliases_for_destination = $this->db->rows()) {
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
		else {
			return false;
		}
	}
	
	public function num_aliases_for_destinationemail($emailaddress) {
		if (!empty($emailaddress)) {
			if (empty($this->aliases_for_destination)) {
				$this->get_aliases_for_emaildestination($emailaddress);
			}
			return count ($this->aliases_for_destination);
		}
		else {
			return false;
		}
	}
	
	public function echo_alias_list_for_emaildestination($emailaddress, $emailid) {
		if ($this->num_aliases_for_destinationemail($emailaddress) > 0) {
			echo '<div>';
				foreach ($this->aliases_for_destination as $alias) {
					echo '<div class="returned_row">';
						echo '<a href="?page=aliases&emailid=' . $emailid . '&delete=' . $alias['id'] . '" onClick="if(confirm(\'Really delete the alias???\')) { window.location=\'?page=aliases&emailid=' . $emailid . '&delete=' . $alias['id'] . '\' } else { return false; }"><div class="delete_button"></div></a>';
						echo '<div class="row_content"> - ' . $alias['source'] . ' -> ' . $alias['destination'] . '</div>';
					echo '<div class="clear_float"></div>';
					echo '</div>';
				}
			echo '</div>';
			return true;
		}
		else {
			return false;
		}
	}
}
?>
