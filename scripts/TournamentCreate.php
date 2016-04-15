<?php
ini_set('max_execution_time', 3600);
// ini_set('max_execution_time', 30);
/* $mysqli_server 	= 'localhost';
$mysqli_user 	= 'root';
$mysqli_pass 	= '';
$mysqli_name 	= 'tilt'; */

$mysqli_server 	= 'aa1vo4uao1prtm6.cslj5bbd5oqi.us-west-2.rds.amazonaws.com';
$mysqli_user 	= 'tiltbetadbuser';
$mysqli_pass 	= 'tb2db0us1er4';
$mysqli_name 	= 'ebdb';

/*$mysqli_server 	= 'mgc-tilt.cslj5bbd5oqi.us-west-2.rds.amazonaws.com';
$mysqli_user 	= 'TiltUser';
$mysqli_pass 	= 'TiltUser123';
$mysqli_name 	= 'ebdb';*/

require_once('../admin/includes/CommonFunctions.php');
require_once('../admin/config/config.php');
//connect db 
$con = mysqli_connect($mysqli_server,$mysqli_user,$mysqli_pass);
if (mysqli_connect_errno()) {
   	printf("Connect failed: %s\n", mysqli_connect_error());
   	exit();
}
$db_con = mysqli_select_db($con,$mysqli_name) or die('Error in selecting databse MySQL');//
$count = 0;
$brandId = 0;		//	Brand id

date_default_timezone_set("America/New_York");
echo date('Y-m-d H:i:s');
if(isset($_GET['count']) && $_GET['count'] > 0 && isset($_GET['gid']) && $_GET['gid'] > 0 && isset($_GET['bid']) && $_GET['bid'] > 0){
	$count 	 = $_GET['count']; //Tournament create limit
	$gameId  = $_GET['gid']; 
	$brandId = $_GET['bid']; 
}else{
	echo "------->Please provide count(Tournament create limit), gid(Game Id), bid(Brand Id)  parameter in url(e.g : \"TournamentCreate.php?count=10&gid=1&bid=1\") "; die;
} 

if(isset($_GET['endhours']) && $_GET['endhours'] > 0){
	$endhours = $_GET['endhours'];
}
else{
	$endhours = '3';
}

$PrivacyPolicy	= $TermsAndConditions = $GameRules = $TournamentRules = '';

$sql = "select count(id) as count from brands where id=".$brandId." AND VerificationStatus = 1 and Status = 1";
$sql_result 	= 	mysqli_query($con,$sql);
while($row = mysqli_fetch_assoc($sql_result)){
	if($row['count'] != 1){
		echo "------->Brand status is not in active state "; die;
	}
}

$sql = "select count(id) as count from games where id=".$gameId." and Status = 1";
// echo $sql;
$sql_result 	= 	mysqli_query($con,$sql);
while($row = mysqli_fetch_assoc($sql_result)){
	if($row['count'] != 1){
		echo "------->Game status is not in active state "; die;
	}
}
$sql = "select TermsAndConditions, GameRules, TournamentRules, TiltFee from settings where id=1";
$sql_result 	= 	mysqli_query($con,$sql);
while($row = mysqli_fetch_assoc($sql_result)){
	$TermsAndConditions	=	$row['TermsAndConditions'];
	$GameRules 			=	$row['GameRules'];
	$TournamentRules 	=	$row['TournamentRules'];
	$TiltFee 	=	$row['TiltFee'];
}
$sql = "select Content from staticpages where id=2";
$sql_result 	= 	mysqli_query($con,$sql);
while($row = mysqli_fetch_assoc($sql_result)){
	$PrivacyPolicy	=	$row['Content'];
}
/* echo "<BR>------ PrivacyPolicy------->".$PrivacyPolicy;
echo "<BR>------ TermsAndConditions------->".$TermsAndConditions;
echo "<BR>------ GameRules------->".$GameRules;
echo "<BR>------ TournamentRules------->".$TournamentRules; */
if($count == 0)
	die;
	
$i = 1;
$insertcount = 0;
do{
	$TournamentName = "LT Tilt Fish ".$i;
	$Prize = 2;
	//Check Tournament Name
	$sql	=	" SELECT id, count(id) as count FROM tournaments WHERE TournamentName ='".trim($TournamentName)."' AND  Status !=3 ";
	$sql_result 	= 	mysqli_query($con,$sql);
	$alreadyExist = 0;
	while($row = mysqli_fetch_assoc($sql_result)){
		if($row['count'] > 0){
			$alreadyExist = 1;
		}
	}
	//Check Tournament Name
	
	if($alreadyExist != 1){
			$sql	 =	'INSERT INTO tournaments SET ';
			$sql	.=  "fkGamesId			=	'".$gameId."',";
			$sql	.=  "fkBrandsId			=	'".$brandId."',";
			$sql	.=	"TournamentName 	= 	'".$TournamentName."',";
			$sql	.=	"GameType			=	'1',";
			$sql	.=	"MinPlayers			=	0,";
			$sql	.=	"MaxPlayers			=	'50',";
			$sql	.=	"TotalTurns			=	'3',";
			$sql	.=	"DelayTime 			= 	'',";
			$sql 	.=	"Type 				= 	'2',";
			$sql 	.=	"Prize 				= 	'".$Prize."',";
			$sql 	.=	"EntryFee 			= 	0,";
			$sql 	.=	"FeeType 			= 	'1',";
			$sql	.=	"StartDate			=	'".date('Y-m-d H:i:s', strtotime('+5 minutes'))."',";
			$sql	.=	"StartTime			=	'".date('Y-m-d H:i:s', strtotime('+5 minutes'))."',";
			$sql	.=	"EndDate			=	'".date('Y-m-d H:i:s', strtotime('+'.$endhours.' hour'))."' ,";	
			$sql	.=	"EndTime			=	'".date('Y-m-d H:i:s', strtotime('+'.$endhours.' hour'))."',";
			$sql	.=	"Status 			=	1,";
			$sql	.=	"CreatedBy 			=	0,";
			
			$sql	.=	" PrivacyPolicy 	= '".addslashes($PrivacyPolicy)."',";
			$sql	.=	" TermsAndCondition = '".addslashes($TermsAndConditions)."',";
			$sql	.=	" GftRules			= '".addslashes($TournamentRules)."',";
			$sql	.=	" TournamentRule 	= '".addslashes($GameRules)."',";
			
			$sql	.=	"PIN				=	0,";
			$sql	.=	"LocationRestrict	=	0,";
			$sql	.=	"LocationBased		=	0, ";
			$sql 	.=	"fkCountriesId 		= 	0, ";
			$sql 	.=	"fkStatesId 		= 	0, ";
			$sql 	.=	"Latitude 			= 	0, ";
			$sql 	.=	"Longitude 			= 	0, ";
			$sql 	.=	"TournamentLocation = 	'', ";
			$sql	.=  "DateCreated		= 	'".date('Y-m-d H:i:s')."', ";
			$sql	.=  "DateModified		= 	'".date('Y-m-d H:i:s')."' ";
			// echo "<br>".__LINE__."<br>----SQL----".$sql;
			$insertcount++;
			$sql_result 	= 	mysqli_query($con,$sql);
			$insertId =  mysqli_insert_id($con);
			if($insertId > 0){
				$sql	 =	'UPDATE brands SET ';
				$sql	.=	' Amount = Amount - ('.$Prize."+".$TiltFee.")";
				$sql	.=	' WHERE id = '.$brandId.' AND Amount >=('.$Prize.'+'.$TiltFee.')';
				$sql_result = 	mysqli_query($con,$sql);
			}
	}
	$i++;
}while($insertcount != $count);
echo "<pre style='color:green'>Line No : ".__LINE__."<br> Script executed successfully"; echo "</pre>"; die;
die;
?>