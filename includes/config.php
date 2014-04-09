<?php

	define('DB_HOST', '127.0.0.1'); //the mysql host IP
	define('DB_USER', 'dbuser'); //user for mysql must have: SELECT, INSERT, UPDATE, DELETE rights to the mailserver database!
	define('DB_PASSWORD', 'password'); //password for mysql
	define('DB_DATABASE', 'mailserver'); //database name is mysql containing the domains, accounts and aliases
	
	//only one username+password is possible, since there propably only will be one administrator of the email server
	define('username', 'AdminUser'); //username to access the site
	define('password', 'AdminPassword'); //password to access the site
	

?>
