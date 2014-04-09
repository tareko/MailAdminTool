<?php

class member {
	public $db;

	public function __construct() {
		if ($this->db = new database(constant('DB_HOST'), constant('DB_USER'), constant('DB_PASSWORD'), constant('DB_DATABASE'))) {
			session_start();
			return true;
		}
		else {
			return false;
		}
	}

	public function is_loggedin() {
		if (!empty($_SESSION['username']) && !empty($_SESSION['login_id'])) {
			if (($_SESSION['login_id'] == "42") && ($_SESSION['username'] == constant('username'))) {
				session_regenerate_id();
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

	public function login($username, $password) {
		if (!empty($username) && !empty($password)) {
			if (($username == constant('username')) && ($password == constant('password'))) {
				$_SESSION['username'] = "$username";
				$_SESSION['login_id'] = "42";
				session_regenerate_id();
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
	
	public function echo_login_form() {
		echo '<div id="login_form">';
			echo '<form id="login" action="./" method="POST">';
				echo '<div class="inputholder">';
					echo '<input name="uname" id="uname" type="text" placeholder="Username"';
						if (!empty($_POST['uname'])) { echo ' value="' . $_POST['uname'] . '" '; }
					echo '>';
				echo '</div>';
				echo '<div class="inputholder">';
					echo '<input name="pword" id="pword" type="password" placeholder="Password"';
						if (!empty($_POST['pword'])) { echo ' value="' . $_POST['pword'] . '" '; }
					echo '>';
				echo '</div>';
				echo '<div class="inputholder">';
					echo '<input type="submit" value="Login">';
				echo '</div>';
			echo '</form>';
		echo '</div>';
		return true;
	}
	
	public function logout() {
		if (!empty($_SESSION['login_id'])) {
			session_unset();
			session_destroy();
			if (session_id()) {
				return false;
			}
			else {
				return true;
			}
		}
		else {
			return true;
		}
	}
}

?>
