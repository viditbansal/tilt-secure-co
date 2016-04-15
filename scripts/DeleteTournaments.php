<?php 
ini_set('display_errors', '1');
error_reporting(E_ALL ^ (E_NOTICE));
ini_set('max_execution_time', 7200);
ini_set('memory_limit', '50120M');

/*$mysqli_server 	= 'localhost';
$mysqli_user 	= 'root';
$mysqli_pass 	= '';
$mysqli_name 	= 'tilt'; 

 $mysqli_server 	= 'aa1vo4uao1prtm6.cslj5bbd5oqi.us-west-2.rds.amazonaws.com';
$mysqli_user 	= 'tiltbetadbuser';
$mysqli_pass 	= 'tb2db0us1er4';
$mysqli_name 	= 'ebdb'; */

$mysqli_server 	= 'mgc-tilt.cslj5bbd5oqi.us-west-2.rds.amazonaws.com';
$mysqli_user 	= 'TiltUser';
$mysqli_pass 	= 'TiltUser123';
$mysqli_name 	= 'ebdb';

require_once('../admin/includes/CommonFunctions.php');

global $con, $fkTournamentsId, $latitude, $longitude, $fkUsersId, $Country, $CountryCode, $State, $distance, $playDistance;
$UserIdArray = array();
//connect db 
$con = mysqli_connect($mysqli_server,$mysqli_user,$mysqli_pass);
if (mysqli_connect_errno()) {
   	printf("Connect failed: %s\n", mysqli_connect_error());
   	exit();
}
$db_con = mysqli_select_db($con,$mysqli_name) or die('Error in selecting databse MySQL');//
date_default_timezone_set("America/New_York");



$zone			= 'America/New_York';
$format			=	'Y-m-d H:i:s';
$deviceCreated	=	getCurrentTime($zone,$format);
$format			=	'Y-m-d';
$today			=	getCurrentTime($zone,$format);
$tournamentsIds = '';
$tourIdsArray = $tourNameArray = array();
if(isset($_GET['tid']) && $_GET['tid'] != ''){
	$tournamentsIds = $_GET['tid'];
}
if($tournamentsIds != ''){

	$sql = "delete from tournaments where id in (".$tournamentsIds.") ";
	echo '<br>--->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);

	$sql = "delete from tournamentcustomprize where fkTournamentsId in (".$tournamentsIds.") ";
	echo '<br>--->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);

	$sql = "delete from tournamentscouponadlink where fkTournamentsId in (".$tournamentsIds.") ";
	echo '<br>--->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);

	$sql = "delete from tournamentsplayed where fkTournamentsId in (".$tournamentsIds.") ";
	echo '<br>--->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);

	$sql = "delete from tournamentsrules where fkTournamentsId in (".$tournamentsIds.") ";
	echo '<br>--->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);

	$sql = "delete from tournamentsstats where fkTournamentsId in (".$tournamentsIds.") ";
	echo '<br>--->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);

	$sql = "delete from userdeletedtournaments where fkTournamentsId in (".$tournamentsIds.") ";
	echo '<br>--->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);

	$sql = "delete from challenge where fkTournamentsId in (".$tournamentsIds.") ";
	echo '<br>--->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);

	$sql = "delete from activities where fkActionId in (".$tournamentsIds.") ";
	echo '<br>--->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);

	$sql = "delete from reserve where fktournamentsId in (".$tournamentsIds.") ";
	echo '<br>--->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);
}
?>
