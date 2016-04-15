<?php
/**
 * MySQL server connection information
 * 
 * This file has configuration information to establish connection to the MySQL server
 *	- hostName = mysql server to connect
 *  - userName = database username to login
 *  - passWord = database password to login
 *  - dataBase = database name
 */
if ($_SERVER['HTTP_HOST'] == '::1') { //172.21.4.104') { // Local
	define('HOST_NAME','localhost');
	define('USER_NAME','root');
	define('PASSWORD','');
	define('DATABASE_NAME','tilt');
}
else {  // Main 
	define('HOST_NAME','mgc-tilt.cslj5bbd5oqi.us-west-2.rds.amazonaws.com');
	define('USER_NAME','TiltUser');
	define('PASSWORD','TiltUser123');
	define('DATABASE_NAME','ebdb');
}
$dbConfig['hostName'] = HOST_NAME;
$dbConfig['userName'] = USER_NAME;
$dbConfig['passWord'] = PASSWORD;
$dbConfig['dataBase'] = DATABASE_NAME;

?>