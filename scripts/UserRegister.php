<?php
ini_set('max_execution_time', 7200);
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

/* require_once('../admin/includes/CommonFunctions.php');
require_once('../admin/config/config.php'); */
//connect db 
$con = mysqli_connect($mysqli_server,$mysqli_user,$mysqli_pass);
if (mysqli_connect_errno()) {
   	printf("Connect failed: %s\n", mysqli_connect_error());
   	exit();
}
$db_con = mysqli_select_db($con,$mysqli_name) or die('Error in selecting databse MySQL');//
$count = 0;
date_default_timezone_set("America/New_York");
if(isset($_GET['count']) && $_GET['count'] > 0)
	$count = $_GET['count']; //User create limit
else{
	echo "------->Please provide 'count' parameter in url(e.g : \"UserRegister.php?count=10\") "; die;
}
// echo $count;
// die;
/* Ip Address */
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip_address=$_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
	$ip_address=$_SERVER['REMOTE_ADDR'];
}
$ActualPassword  = 'testing' ;
$Password 		 = sha1($ActualPassword.'saltisgood');
$DefaultTilt			=	20;
$DefaultVirtualCoins 	=	100;
$sql = "select DefaultTilt, DefaultVirtualCoins from settings where id=1";
$sql_result 	= 	mysqli_query($con,$sql);
while($row = mysqli_fetch_assoc($sql_result)){
	$DefaultTilt			=	$row['DefaultTilt'];
	$DefaultVirtualCoins 	=	$row['DefaultVirtualCoins'];
}
$insertedId = array();
$insertcount = 0;
//for($i=1;$i<=$count;$i++){
$i = 1;
do{
	$email = (10000+ $i)."@alttabmobile.com";
	
	$sql	 =	"select id, count(id) as count from users where ( Email = '".$email."') and Status in (1,2,4)";
	$sql_result 	= 	mysqli_query($con,$sql);
	$emailCheck = 0;
	while($row = mysqli_fetch_assoc($sql_result)){
		if($row['count'] > 0){
			$emailCheck   = 1;
			$insertedId[] =  $row['id'];
		}
	}
	if($emailCheck == 1){
		// echo "<br>".$email;
	}else{
		$sql	 =	"insert into users set ";		
		$sql	.=  "FirstName			=	'".(10000 + $i)."',";	
		$sql	.=	"LastName			=	'alttabmobile',";			
		$sql 	.=	"Email 				= 	'".$email."',";		
		$sql 	.=	"IpAddress 			= 	'".$ip_address."',";			
		$sql	.=  "Coins				=	'".$DefaultTilt."',";
		$sql	.=  "VirtualCoins		=	'".$DefaultVirtualCoins."',";		
		$sql	.=  "Password			=	'".$Password."',";		
		$sql	.=  "ActualPassword		=	'".$ActualPassword."',";		
		$sql 	.=	" Status			= 	1,
					TournamentStart 	= 	1,
					TournamentEnd 		= 	1,
					BrandNewTournament 	= 	1,
					CouponWon 			= 	1,
					ContactWinner 		= 	1,
					NumberOfCoins 		= 	1,
					InviteFriend 		= 	1,
					FBFriendsJoin 		= 	1,
					BeatHighScore 		= 	1,
					ChatNotification 	= 	1,
					GroupNotification 	= 	1,
					EmailNotification 	= 	1,
					VerificationStatus 	= 	1,
					DateCreated 		= 	'".date('Y-m-d H:i:s')."',
					DateModified		= 	'".date('Y-m-d H:i:s')."'";
		echo "<br>".__LINE__."<br>----SQL----".$sql; //die;
		/* $sql_result 	= 	mysqli_query($con,$sql);
		$insertId = $insertedId[] =  mysqli_insert_id($con); */
		$insertcount++;
		/* if($insertId > 0){
			//$insertcount++;
			$sql	 =	"insert into tiltcoins set fkUsersId = '".$insertId."', TiltCoins = '".$DefaultTilt."', Status = 1, DateCreated = 	'".date('Y-m-d H:i:s')."'";
			$sql_result 	= 	mysqli_query($con,$sql);
			$sql =	"insert into paymenthistory set BrandDeveloperId = '0', PurchasedBy = '0', fkUsersId = '".$insertId."', Type = '6', Coins = '".$DefaultTilt."', CoinType = '1', DateCreated = '".date('Y-m-d H:i:s')."'";
			$sql_result 	= 	mysqli_query($con,$sql);
			$sql =	"INSERT INTO virtualcoins set fkUsersId = '".$insertId."', VirtCoins = '".$DefaultVirtualCoins."', Status = '1', DateCreated = '".date('Y-m-d H:i:s')."'";
			$sql_result 	= 	mysqli_query($con,$sql);
			$sql =	"insert into paymenthistory set BrandDeveloperId = '0', PurchasedBy = '0', fkUsersId = '".$insertId."', Type = '6', Coins = '".$DefaultVirtualCoins."', CoinType = 	'2', DateCreated = '".date('Y-m-d H:i:s')."'";
			$sql_result 	= 	mysqli_query($con,$sql);
		} */
	}
	$i++;
}while($insertcount != $count);
echo "<pre style='color:green'>Line No : ".__LINE__."<br>"; print_r($insertedId); echo "</pre>"; die;
?>
