<?php 

ini_set('max_execution_time', 3600);
ini_set('memory_limit', '2048M');
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

//connect db 
$con = mysqli_connect($mysqli_server,$mysqli_user,$mysqli_pass);
if (mysqli_connect_errno()) {
   	printf("Connect failed: %s\n", mysqli_connect_error());
   	exit();
}
$db_con = mysqli_select_db($con,$mysqli_name) or die('Error in selecting databse MySQL');//

echo "<br><br>=======>BEGIN : Elimination players played entry to Activities table <=======";
echo "<br><br>/** Insert data - Start **/";
$sql 		= "select *  from tournamentsplayed where 1 and fkUsersId=0 AND Elimination = 1 ";
echo "<br><br>==>".$sql;

$sql_result = mysqli_query($con,$sql);
echo '<br><br>=== Number of Elimination Tournaments ==>'.$sql_result->num_rows;
while($Played = mysqli_fetch_assoc($sql_result)){
	$PlayedArray[] = $Played;
}
$eliminationEntry = 0;
if($PlayedArray){
	$string			=	"fkUsersId, fkActionId, fkPlayedId, ActionType,CountryCode, ActivityDate";
	$i	=	$j	=	0;
	$insertString	=	"";
	foreach($PlayedArray as $key=>$val){
		$sql 			= 	"SELECT count(id) as totalcount FROM `eliminationplayer` WHERE fkTournamentsPlayedId =".$val['id'];
		$sql_result 	= 	mysqli_query($con,$sql);
		while($row = mysqli_fetch_assoc($sql_result)){
			$totalCount = $row['totalcount'];
		}
		$eliminationEntry += $totalCount;
		if($totalCount > 0){
			$tids = '';
			$sql 			= 	"SELECT fkUsersId,fkTournamentsPlayedId,id as fkPlayedId,CountryCode,DatePlayed FROM `eliminationplayer` WHERE fkTournamentsPlayedId =".$val['id']." " ;
			$sql_res 	= 	mysqli_query($con,$sql);
			while($records = mysqli_fetch_assoc($sql_res)){
				$sql 		= 	"select *  from activities where fkUsersId='".$records['fkUsersId']."' and fkActionId='".$val['fkTournamentsId']."' and fkPlayedId='".$records['fkPlayedId']."'";
				$sql_result1 = 	mysqli_query($con,$sql);
				if(isset($sql_result1->num_rows) && $sql_result1->num_rows == 0) { 
					if($records['CountryCode'] == '(null)')
						$insertString	.=	"(".$records['fkUsersId'].", ".$val['fkTournamentsId'].", ".$records['fkPlayedId'].", 2,'', '".$records['DatePlayed']."'),";
					else $insertString	.=	"(".$records['fkUsersId'].", ".$val['fkTournamentsId'].", ".$records['fkPlayedId'].", 2,'".$records['CountryCode']."', '".$records['DatePlayed']."'),";
					$i++;
				} else {
					$j++;
				}
			}
		}
	}
	echo "<br>===>Total entry in Elimination table :  ".$eliminationEntry;
	echo "<br>===>Total New entry to Activities table : ".$i;
	echo "<br>===>Total Old entry already in Activities table : ".$j;
	if(!empty($insertString)) {
		$sql 			= 	'insert into activities ('.$string.') values '.rtrim($insertString,',');
		echo '<br><br>==Insertion query : ==> '.$sql;
		// mysqli_query($con,$sql);
	}
}
echo "<br><br>/** Insert data - End **/";
echo "<br><br>=======>END : Elimination players played entry to Activities table <=======";
?>

