<?php
$version = "1.2.1";
$qm       = strpos($_SERVER['REQUEST_URI'], '?');
$base_url = ($qm === FALSE) ? $_SERVER['REQUEST_URI'] : dirname($_SERVER['REQUEST_URI']) . '/';
if ($base_url == "//") {
	$base_url = "/";
}
require_once ('includes/config.php');
require_once ('includes/class_db.php');
require_once ('includes/class_member.php');
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">';
echo '<html>';
echo '<head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >';
	echo '<title>Mail Admin Tool</title>';
	echo '<meta name="title" content="Mail Admin tool" >';
	echo '<meta name="description" content="Administration of domain names, email accounts and aliases" >';
	echo '<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" >';
	echo '<link rel="icon" type="image/x-icon" href="images/favicon.png" >';
	echo '<link type="text/css" href="stylesheets/global.css" rel="stylesheet" >';
echo '</head>';
echo '<body>';
	echo '<div id="wrapper">';
		if ($member = new member) {
			if ($member->is_loggedin()) {
				echo '<div id="menu_wrapper">';
					echo '<div class="menu_button"><a href="' . $base_url .'">&#8226; Home</a></div>';
					echo '<div class="menu_button"><a href="?page=domains">&#8226; Domains</a></div>';
					echo '<div class="menu_button"><a href="?page=emails">&#8226; Emails</a></div>';
					echo '<div class="menu_button"><a href="?page=aliases">&#8226; Aliases</a></div>';
					echo '<div class="menu_button"><a href="?page=logout">&#8226; Logout</a></div>';
				echo '</div>';
			}
		}
		else {
			echo '<div class="error">';
				echo 'An error happened.. could not load the "member" object';
			echo '</div>';
		}
		echo '<div id="toplogo"></div>';
		echo '<h1>Mail Admin Tool</h1>';
		echo '<div class="clear_float"></div>';
		echo '<div id="content">';
			if ($member->is_loggedin()) {
				if (!empty($_GET['page'])) {
					if ($_GET['page'] == "domains") {
						require_once('includes/class_domains.php');
						if ($domains = new domains) {
							echo '<h2>Domains</h2>';
							echo '<p>If you delete a domain all Email accounts and aliases on it will be deleted too (exept if you converted the databse to MyISAM, then you have to delete Email accounts and Aliases manually, if you dont know what MyISAM is then you propaply didnt.)</p>';
							echo '<p>You can see a list of domains, create a new one or delete the old ones below</p>';
							echo '<div class="create_form">';
								echo '<form id="create_domain" name="create_domain" action="?page=domains&amp;create=true" method="POST">';
									echo '<input type="text" name="domain_name" id="domain_name" placeholder="example.com">';
									echo ' <a onClick="document.create_domain.submit();">Create</a>';
								echo '</form>';
							echo '</div>';
							
							if (!empty($_GET['create'])) {
								$new_domain = $domains->db->clean($_POST['domain_name']);
								if($domains->create_domain($new_domain)) {
									echo '<div class="success">';
										echo 'The domain has been created, and should be in the list below.';
									echo '</div>';
								}
								else {
									echo '<div class="error">';
										echo 'Could not create domain, make sure you typed it correctly.. do not include the @';
									echo '</div>';
								}
							}
							
							if (!empty($_GET['delete'])) {
								if (is_numeric($_GET['delete'])) {
									$delete = $domains->db->clean($_GET['delete']);
									if ($domains->delete_domain($delete)) {
										echo '<div class="success">';
											echo 'The domain and all its accounts has been deleted.';
										echo '</div>';
									}
									else {
										echo '<div class="error">';
											echo 'Could not delete the domain, something went wrong!';
										echo '</div>';
									}
								}
							}
							
							echo '<h3>In database</h3>';
							echo '<p>(Click on the domain name to view emails associatet with that domain or "Aliases: " to view aliases associatet with that domain)</p>';
							if ($domains->get_all_domains()) {
								if ($domains->num_domains() > 0){
									if (!$domains->echo_domain_list()) {
										echo '<div class="error">';
											echo 'An error happened.. Could not show the domains!';
										echo '</div>';
									}
								}
								else {
									echo 'No domains found';
								}
							}
							else {
								echo '<div class="error">';
									echo 'An error happened.. could not get domains from database';
								echo '</div>';
							}
						}
						else {
							echo '<div class="error">';
								echo 'An error happened.. could not load the "Domains" object';
							echo '</div>';
						}
					}
					elseif ($_GET['page'] == "emails") {
						require_once('includes/class_domains.php');
						require_once('includes/class_emails.php');
						if ($emails = new emails) {
							
							echo '<h2>Email accounts</h2>';
							echo '<p>Here you can delete or add email accounts. You can even reset the password if your users forget them.</p>';
							echo '<p>Please thoose a domain in the dropdown list you want to modify/view</p>';
							if (!empty($_GET['create'])) {
								$new_email = $emails->db->clean($_POST['new_email']);
								$new_password = $emails->db->clean($_POST['new_password']);
								$new_domainid = $emails->db->clean($_POST['new_domainid']);
								if($emails->create_email($new_email, $new_password, $new_domainid)) {
									echo '<div class="success">';
										echo 'The email has been created, and should be in the list below.';
									echo '</div>';
								}
								else {
									echo '<div class="error">';
										echo 'Could not create email, make sure you typed it correctly.. you have to include the "@" and ".", example: "user@example.com" (without " ")';
									echo '</div>';
								}
							}
							
							if (!empty($_GET['reset_password']) && !empty($_POST['new_password'])) {
								$email_id = $emails->db->clean($_GET['reset_password']);
								$new_password = $emails->db->clean($_POST['new_password']);
								if (is_numeric($email_id)) {
									if ($emails->reset_password($email_id, $new_password)) {
										echo '<div class="success">';
											echo 'The password has been reset!';
										echo '</div>';
									}
									else {
										echo '<div class="error">';
											echo 'Something went wrong. the password has not been reset!';
										echo '</div>';
									}
								}
							}
							
							if (!empty($_GET['delete'])) {
								if (is_numeric($_GET['delete'])) {
									$delete = $emails->db->clean($_GET['delete']);
									if ($emails->delete_email($delete)) {
										echo '<div class="success">';
											echo 'The email and all its aliases has been deleted.';
										echo '</div>';
									}
									else {
										echo '<div class="error">';
											echo 'Could not delete the email, something went wrong!';
										echo '</div>';
									}
								}
							}
							
							$domainid = 0;
							if ((!empty($_GET['domainid'])) || (!empty($_POST['domainid']))) {
								if (!empty($_POST['domainid'])) { $_GET['domainid'] = $_POST['domainid']; }
								$domainid = $emails->db->clean($_GET['domainid']);
								if (!is_numeric($domainid)) {
									$domainid = 0;
								}
							}
							if ($domains = new domains) {
								if ($domains->get_all_domains()) {
									if ($domains->num_domains() > 0) {
										echo '<form id="domainid_selector" name="domainid_selector" action="?page=emails" method="POST">';
											echo 'Show: <select name="domainid" onChange="document.domainid_selector.submit();">';
													echo '<option value="0">-------------</option>';
												foreach ($domains->all_domains as $domain) {
													echo '<option value="' . $domain['id'] . '" ';
													if ($domainid == $domain['id']) { echo 'SELECTED="SELECTED" '; }
													echo '>' . $domain['name'] . '</option>';
												}
												echo '</select>';
										echo '</form>';
										
										if (!empty($domainid)) {
											echo '<div class="create_form">';
												echo '<h3>Create new</h3>';
												echo '<form id="create_email" name="create_email" action="?page=emails&amp;domainid=' . $domainid . '&amp;create=true" method="POST">';
													echo 'Email: <input type="text" name="new_email" placeholder="user@example.com" autocomplete="off"><br>';
													echo 'Pass: <input type="password" name="new_password" autocomplete="off"><br>';
													echo '<input type="hidden" name="new_domainid" value="' . $domainid . '">';
													echo ' <a onClick="document.create_email.submit();">Create</a>';
												echo '</form>';
											echo '</div>';
											
											echo '<h3>In database</h3>';
											echo '<p>You can click on a email address to see what aliases has the email as "Destination"<br>To reset a password, click the key icon on the left site of the email you want to change it for and input a new password in the field on the right</p>';
											if ($emails->num_emails_for_domain($domainid) > 0) {
												if ($emails->get_emails_for_domain($domainid)){
													if (!$emails->echo_email_list($domainid)) {
														echo '<div class="error">';
															echo 'An error happened.. Could not show the emails!';
														echo '</div>';
													}
												}
												else {
													echo '<div class="error">';
														echo 'An error happened.. could not get emails from database';
													echo '</div>';
												}
											}
											else {
												echo 'No emails found for that domain';
											}
										}
									}
									else {
										echo 'You have not created any domains yet. Please create one from the <a href="?page=domains">Domains</a> page';
									}
								}
								else {
									echo '<div class="error">';
										echo 'Could not get domains from database';
									echo '</div>';
								}
							}
							else {
								echo '<div class="error">';
									echo 'An error happened.. could not load the "domains" object';
								echo '</div>';
							}
						}
						else {
							echo '<div class="error">';
								echo 'An error happened.. could not load the "emails" object';
							echo '</div>';
						}
					}
					elseif ($_GET['page'] == "aliases") {
						require_once('includes/class_domains.php');
						require_once('includes/class_aliases.php');
						if ($aliases = new aliases) {
							echo '<h2>Aliases</h2>';
							echo '<p>On this page you can view the aliases for a domain, create new ones and delete old ones.</p>';
							echo '<p>Deleting an alias will not delete any users or domains.<br>Please choose the domain you want to show the aliases for below</p>';
							if (!empty($_GET['create'])) {
								$new_source = $aliases->db->clean($_POST['new_source']);
								$new_destination = $aliases->db->clean($_POST['new_destination']);
								$new_domainid = $aliases->db->clean($_POST['new_domainid']);
								if($aliases->create_alias($new_source, $new_destination, $new_domainid)) {
									echo '<div class="success">';
										echo 'The alias has been created, and should be in the list below.';
									echo '</div>';
								}
								else {
									echo '<div class="error">';
										echo 'Could not create alias, make sure you typed it correctly.. you have to include the "@" and ".", example: "user@example.com" or "@domain.com" for a catch-all alias (without " ")';
									echo '</div>';
								}
							}
							
							if (!empty($_GET['delete'])) {
								if (is_numeric($_GET['delete'])) {
									$delete = $aliases->db->clean($_GET['delete']);
									if ($aliases->delete_alias($delete)) {
										echo '<div class="success">';
											echo 'The alias and all its aliases has been deleted.';
										echo '</div>';
									}
									else {
										echo '<div class="error">';
											echo 'Could not delete the alias, something went wrong!';
										echo '</div>';
									}
								}
							}
							
							
							$domainid = 0;
							if ((!empty($_GET['domainid'])) || (!empty($_POST['domainid']))) {
								if (!empty($_POST['domainid'])) { $_GET['domainid'] = $_POST['domainid']; }
								$domainid = $aliases->db->clean($_GET['domainid']);
								if (!is_numeric($domainid)) {
									$domainid = 0;
								}
							}
							
							$emailid = 0;
							if (!empty($_GET['emailid'])) {
								$emailid = $aliases->db->clean($_GET['emailid']);
								if (!is_numeric($emailid)) {
									$emailid = 0;
								}
							}
							
							
							if ($domains = new domains) {
								if ($domains->get_all_domains()) {
									if ($domains->num_domains() > 0) {
										echo '<form id="domainid_selector" name="domainid_selector" action="?page=aliases" method="POST">';
											echo 'Show: <select name="domainid" onChange="document.domainid_selector.submit();">';
													echo '<option value="0">-------------</option>';
												foreach ($domains->all_domains as $domain) {
													echo '<option value="' . $domain['id'] . '" ';
													if ($domainid == $domain['id']) { echo 'SELECTED="SELECTED" '; }
													echo '>' . $domain['name'] . '</option>';
												}
												echo '</select>';
										echo '</form>';
										
										if (!empty($emailid)) {
											if ($domainid = $aliases->get_domainid_for_emailid($emailid)) {
												echo '<div class="create_form">';
													echo '<h3>Create new</h3>';
													echo '<p>To make a "catch all" alias that redirects everything send to an entire domain to one user, type it like this:<br>Source: @domain-to-catch.com<br>Destination: user@to-get-it-all.com</p>';
													echo '<form id="create_alias" name="create_alias" action="?page=aliases&amp;domainid=' . $domainid . '&amp;create=true" method="POST">';
														echo 'Alias source: <input type="text" name="new_source" placeholder="user@example.com" autocomplete="off"><br>';
														echo 'Alias Destination: <input type="text" name="new_destination" placeholder="user2@example.com" autocomplete="off"><br>';
														echo '<input type="hidden" name="new_domainid" value="' . $domainid . '">';
														echo ' <a onClick="document.create_alias.submit();">Create</a>';
													echo '</form>';
												echo '</div>';
												
												echo '<h3>In database</h3>';
												if ($email_address = $aliases->get_emailaddress_for_emailid($emailid)) {
													if ($aliases->num_aliases_for_destinationemail($email_address) > 0) { 
														if ($aliases->get_aliases_for_emaildestination($email_address)) {
															if (!$aliases->echo_alias_list_for_emaildestination($email_address, $emailid)) {
																echo '<div class="error">';
																	echo 'An error happened.. Could not show the aliases!';
																echo '</div>';
															}
														}
														else {
															echo '<div class="error">';
																echo 'An error happened.. could not get aliases from database';
															echo '</div>';
														}
													}
													else {
														echo 'No aliases for that email address.';
													}
												}
												else {
													echo '<div class="error">';
														echo 'An error happened.. could not get the email address based on the email ID';
													echo '</div>';
												}
											}
											else {
												echo '<div class="error">';
													echo 'An error happened.. could not get the domain ID for the emails';
												echo '</div>';
											}
										}
										elseif (!empty($domainid)) {
											echo '<div class="create_form">';
												echo '<h3>Create new</h3>';
												echo '<form id="create_alias" name="create_alias" action="?page=aliases&amp;domainid=' . $domainid . '&amp;create=true" method="POST">';
													echo 'Alias source: <input type="text" name="new_source" placeholder="user@example.com" autocomplete="off"><br>';
													echo 'Alias Destination: <input type="text" name="new_destination" placeholder="user2@example.com" autocomplete="off"><br>';
													echo '<input type="hidden" name="new_domainid" value="' . $domainid . '">';
													echo ' <a onClick="document.create_alias.submit();">Create</a>';
												echo '</form>';
											echo '</div>';
											
											echo '<h3>In database</h3>';
											if ($aliases->num_aliases_for_domain($domainid) > 0) {
												if ($aliases->get_aliases_for_domain($domainid)){
													if (!$aliases->echo_alias_list($domainid)) {
														echo '<div class="error">';
															echo 'An error happened.. Could not show the aliases!';
														echo '</div>';
													}
												}
												else {
													echo '<div class="error">';
														echo 'An error happened.. could not get aliases from database';
													echo '</div>';
												}
											}
											else {
												echo 'No aliases found for that domain';
											}
										}
									}
									else {
										echo 'You have not created any domains yet. Please create one from the <a href="?page=domains">Domains</a> page';
									}
								}
								else {
									echo '<div class="error">';
										echo 'Could not get domains from database';
									echo '</div>';
								}
							}
							else {
								echo '<div class="error">';
									echo 'An error happened.. could not load the "domains" object';
								echo '</div>';
							}
						}
						else {
							echo '<div class="error">';
								echo 'An error happened.. could not load the "aliases" object';
							echo '</div>';
						}
					}
					elseif ($_GET['page'] == "logout") {
						if ($member->logout()) {
							echo '<div class="success">';
								echo 'You are now logged out!';
								?>
								<script type="text/javascript">
									window.setTimeout('window.location="<?php echo $base_url; ?>"',5000);
								</script>
								<?php
							echo '</div>';
						}
						else {
							echo '<div class="error">';
								echo "The logout failed, you are still logged in!";
							echo '</div>';
						}
					}
				}
				else {
					?>
					<noscript>Javascript is required for this to work correctly. Please enable javascript or some functions wont work!</noscript>
					<?php
					echo '<p>Welcome!<br>This site is for editing your mailservers virtual domains, email accounts and aliases</p>';
					echo '<p>It was made with the <a href="http://workaround.org/ispmail">ISPmail</a> guide in mind and works on the database created when following the guide.</p>';
					echo '<p>If you find any errors, or have some improvement, fixes, suggestions then please email me at <a href="mailto:mat@ssdata.dk">mat@ssdata.dk</a></p>';
					echo '<p>Use the menu top-right to start editing. If you need a search function then use the browsers CTRL+F</p>';
					echo '<p>Make sure to visit <a href="http://mat.ssdata.dk">http://mat.ssdata.dk</a> once in a while for updates, you are running version <u><b>' . $version . '</b></u></p>';
				}
			}
			else {
				if (!empty($_POST['uname']) && !empty($_POST['pword'])) {
					$uname = $member->db->clean($_POST['uname']);
					$pword = $member->db->clean($_POST['pword']);
					
					if (!$member->login($uname, $pword)) {
						echo '<div class="error">Wrong username or password.</div>';
						if (!$member->echo_login_form()) {
							echo '<div class="error">';
								echo 'An error happened, the loggin form could not be displayed.';
							echo '</div>';
						}
					}
					else {
						?>
						<script type="text/javascript">
							window.location="<?php echo $base_url; ?>";
						</script>
						<?php
						echo 'Success! you should automaticly be redirected to the homepage, if not please click <a href="./">here</a>';
					}
				}
				else {
					if (!$member->echo_login_form()) {
						echo '<div class="error">';
							echo 'An error happened, the loggin form could not be displayed.';
						echo '</div>';
					}
				}
			}
		echo '</div>';
	echo '</div>';
	echo '<div id="footer">';
		echo 'This tool was made by Steffan Slot (<a href="http://mat.ssdata.dk">http://mat.ssdata.dk</a>)';
	echo '</div>';
	echo '</body>';
echo '</html>';


?>
