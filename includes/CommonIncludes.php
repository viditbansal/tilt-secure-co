<?php
if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
ob_start('ob_gzhandler');
else
ob_start();

// ADDED THE CURRENT TIME ZONE TO NEW YORK AS PER CLIENT REQ ON 19/05/2014
date_default_timezone_set('America/New_York');

session_start();
require_once('controllers/Controller.php');

require_once('models/Database.php');
require_once('config/db_config.php');

global $globalDbManager;
global $con;
$globalDbManager = new Database();
$con = $globalDbManager->dbConnect = $globalDbManager->connect($dbConfig['hostName'], $dbConfig['userName'], $dbConfig['passWord'], $dbConfig['dataBase']);
mysqli_set_charset($globalDbManager->dbConnect,'utf8');
require_once('includes/stripe/lib/Stripe.php');
require_once('models/Model.php');
require_once('config/config.php');
require_once('includes/AdminTemplates.php');
require_once('includes/CommonFunctions.php');
?>