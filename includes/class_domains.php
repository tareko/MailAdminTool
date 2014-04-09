<?php

class domains {
	public $db;
	public $all_domains;

	public function __construct() {
		if ($this->db = new database(constant('DB_HOST'), constant('DB_USER'), constant('DB_PASSWORD'), constant('DB_DATABASE'))) {
			return true;
		}
		else {
			return false;
		}
	}

	public function get_all_domains() {
		if ($this->db->query("SELECT * FROM virtual_domains")) {
			if ($this->all_domains = $this->db->rows()) {
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
	
	public function num_domains() {
		if (empty($this->all_domains)) {
			$this->get_all_domains();
		}
		return count ($this->all_domains);
	}

	public function echo_domain_list() {
		if ($this->num_domains() > 0) {
			echo '<div>';
				foreach ($this->all_domains as $domain) {
					echo '<div class="returned_row">';
						echo '<a href="?page=domains&delete=' . $domain['id'] . '" onClick="if(confirm(\'Really delete the domain and all account on it???\')) { window.location=\'?page=domains&delete=' . $domain['id'] . '\' } else { return false; }"><div class="delete_button"></div></a> - <a href="?page=emails&domainid=' . $domain['id'] . '">' . $domain['name'] . '</a>';
						echo ' (<a href="?page=emails&domainid=' . $domain['id'] . '">Emails: ';
						if ($emails = $this->get_num_emails($domain['id'])) {
							echo $emails;
						}
						else {
							echo '0';
						}
						echo '</a>';
						echo ' - ';
						echo '<a href="?page=aliases&domainid=' . $domain['id'] . '">Aliases: ';
						if ($aliases = $this->get_num_aliases($domain['id'])) {
							echo $aliases;
						}
						else {
							echo '0';
						}
						echo '</a>)';
					echo '</div>';
					echo '<div class="clear_float"></div>';
				}
			echo '</div>';
			return true;
		}
		else {
			return false;
		}
	}
	
	private function does_domain_exists($domainname) {
		if (!empty($domainname)) {
			if ($this->db->query("SELECT name FROM virtual_domains WHERE name='$domainname'")) {
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
	
	public function create_domain($new_domain) {
		if (!empty($new_domain)) {
			if ((strpos($new_domain,'.') !== false) && (strpos($new_domain,'@') === false)) {
				if (!$this->does_domain_exists($new_domain)) {
					if ($this->db->query("INSERT INTO virtual_domains SET name='$new_domain'")) {
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
	
	public function delete_domain($domain_to_delete) {
		if (!empty($domain_to_delete)) {
			if ($this->db->query("DELETE FROM virtual_domains WHERE id='$domain_to_delete'")) {
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
	
	private function get_num_emails($domainid) {
		if (!empty($domainid)) {
			if (is_numeric($domainid)) {
				if ($this->db->query("SELECT count(*) as count FROM virtual_users WHERE domain_id='$domainid'")) {
					if ($row = $this->db->get_single_row()) {
						return $row['count'];
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
	
	private function get_num_aliases($domainid) {
		if (!empty($domainid)) {
			if (is_numeric($domainid)) {
				if ($this->db->query("SELECT count(*) as count FROM virtual_aliases WHERE domain_id='$domainid'")) {
					if ($row = $this->db->get_single_row()) {
						return $row['count'];
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
}


?>
