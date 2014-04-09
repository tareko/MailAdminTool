<?php

class emails {
	public $db;
	private $emails_for_domain;
	private $num_emails_for_domain;
	
	
	public function __construct() {
		if ($this->db = new database(constant('DB_HOST'), constant('DB_USER'), constant('DB_PASSWORD'), constant('DB_DATABASE'))) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function get_emails_for_domain($domainid) {
		if (!empty($domainid)) {
			if (is_numeric($domainid)) {
				if ($this->db->query("SELECT id,email FROM virtual_users WHERE domain_id='$domainid'")) {
					if ($this->emails_for_domain = $this->db->rows()) {
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
	
	public function num_emails_for_domain($domainid) {
		if (!empty($domainid)) {
			if (is_numeric($domainid)) {
				if (empty($this->emails_for_domain)) {
					$this->get_emails_for_domain($domainid);
				}
				return count ($this->emails_for_domain);
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	public function echo_email_list($domainid) {
		if ($this->num_emails_for_domain($domainid) > 0) {
			echo '<div>';
				foreach ($this->emails_for_domain as $email) {
					echo '<div class="returned_row">';
						echo '<a href="?page=emails&domainid=' . $domainid . '&reset_password=' . $email['id'] . '" onClick="';
						echo 'getElementById(\'change_password_for_' . $email['id'] . '\').style.display = (getElementById(\'change_password_for_' . $email['id'] . '\').style.display != \'none\' ? \'none\' : \'block\'); return false;';
						echo '"><div class="reset_password_button"></div></a>';
						echo '<a href="?page=emails&domainid=' . $domainid . '&delete=' . $email['id'] . '" onClick="if(confirm(\'Really delete the email and all aliases on it???\')) { window.location=\'?page=emails&domainid=' . $domainid . '&delete=' . $email['id'] . '\' } else { return false; }"><div class="delete_button"></div></a>';
						echo '<div class="row_content"> - <a href="?page=aliases&emailid=' . $email['id'] . '">' . $email['email'] . '</a></div>';
						echo ' <div id="change_password_for_' . $email['id'] . '" class="change_password_wrapper" style="display: none">';
							echo '<form id="new_password_for_' . $email['id'] . '" name="new_password_for_' . $email['id'] . '" action="?page=emails&domainid=' . $domainid . '&reset_password=' . $email['id'] . '" method="POST">';
								echo '<input name="new_password" type="password" placeholder="New password"> ';
								echo '<a class="set_password_submit" onClick="document.new_password_for_' . $email['id'] . '.submit()">Set</a>';
							echo '</form>';
						echo '</div>';
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
	
	private function does_email_exist($email) {
		if (!empty($email)) {
			if ($this->db->query("SELECT email FROM virtual_users WHERE email='$email'")) {
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
	
	public function create_email($new_email, $password, $domainid) {
		if (!empty($new_email) && !empty($domainid) && !empty($password)) {
			if (is_numeric($domainid)) {
				if ((strpos($new_email,'.') !== false) && (strpos($new_email,'@') !== false)) {
					if (!$this->does_email_exist($new_email)) {
						$password = md5($password);
						if ($this->db->query("INSERT INTO virtual_users SET email='$new_email', domain_id='$domainid', password='$password'")) {
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
	
	public function delete_email($emailid_to_delete) {
		if (!empty($emailid_to_delete)) {
			if ($this->db->query("DELETE FROM virtual_users WHERE id='$emailid_to_delete'")) {
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
	
	public function reset_password($emailid, $new_password) {
		if (!empty($emailid) && !empty($new_password)) {
			$new_password = md5($new_password);
			if ($this->db->query("UPDATE virtual_users SET password='$new_password' WHERE id='$emailid'")) {
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
}
?>
