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


$gameLimit = $start = 0;
$ActivityEndLimit = 20;
$GameEndLimit = 10;
if(isset($_GET['EndLimit']) && $_GET['EndLimit'] != '')
	$ActivityEndLimit = $_GET['EndLimit'];
	
if(isset($_GET['Start']) && $_GET['Start'] != '')
	$start = $_GET['Start'];
	
if(isset($_GET['GameId']) && $_GET['GameId'] != '')
	$condition = " and id in (".$_GET['GameId'].")";

echo "<br><br>/** Game count of tournamentsplayed Table - Start **/";
$sql 		= "SELECT fkGamesId, count( DISTINCT tp.fkUsersId ) AS playerCount FROM tournamentsplayed AS tp WHERE 1 and fkUsersId != 0 group by fkGamesId limit $gameLimit,$GameEndLimit";
echo "<br><br>==>".$sql;
$sql_result = mysqli_query($con,$sql);
$gameCount = Array();
while($count = mysqli_fetch_assoc($sql_result)){	
	$gameCount[$count['fkGamesId']]['game'] = 	$count['fkGamesId'];
	$gameCount[$count['fkGamesId']]['playerCount'] = 	$count['playerCount'];
}
echo "<pre>";print_r($gameCount);echo "</pre>";
echo "<br><br>/** Game count of tournamentsplayed Table - End **/";
$total	 = 0;

echo "<br><br>/** Insert data - Start **/";
$sql 		= "select SQL_CALC_FOUND_ROWS fkUsersId,fkTournamentsId,id,DatePlayed from tournamentsplayed where 1 and fkUsersId!=0 limit $start,$ActivityEndLimit";
echo "<br><br>==>".$sql;
$sql_result = mysqli_query($con,$sql);
$sql_count_query 	= 'SELECT FOUND_ROWS() as totalCount';
$resource 			= mysqli_query($con,$sql_count_query);
echo '<pre>===>';print_r($resource);echo '<===</pre>';
while($count = mysqli_fetch_assoc($resource)){	
	$total		 	= $count['totalCount'];
}

echo '-total_activity-->'.$total.'<---';

while($Played = mysqli_fetch_assoc($sql_result)){
	$PlayedArray[] = $Played;
}
if($PlayedArray){
	$string			=	"fkUsersId, fkActionId, fkPlayedId, ActionType, ActivityDate";
	$insertString	=	"";
	$i	=	$j	=	0;
	foreach($PlayedArray as $key=>$val){
		$sql 		= 	"select *  from activities where 1 and fkUsersId='".$val['fkUsersId']."' and fkActionId='".$val['fkTournamentsId']."' and fkPlayedId='".$val['id']."'";
		$sql_result = 	mysqli_query($con,$sql);
		if(isset($sql_result->num_rows) && $sql_result->num_rows == 0) { 
			$i++;
			$insertString	.=	"(".$val['fkUsersId'].", ".$val['fkTournamentsId'].", ".$val['id'].", 2, '".$val['DatePlayed']."'),";	
		} else {
			$j++;
		}
	}
	echo "<br>===>Count not = ".$i;
	echo "<br>===>Count = ".$j;
	if(!empty($insertString)) {
		echo "<br>===>Count not = ".$i;
		echo "<br>===>Count = ".$j;
		$sql 			= 	'insert into activities ('.$string.') values '.rtrim($insertString,',');
		echo "<br>".__LINE__."----SQL----<br>".$sql; die;
		// mysqli_query($con,$sql);
	}
}
echo "<br><br>/** Insert data - End **/";
die();

echo "<br><br>/** Game count of activities Table - Start **/";
$sql 	= 	"SELECT t.fkGamesId, count( DISTINCT a.fkUsersId ) AS playerCount FROM activities AS a 
				LEFT JOIN tournaments t ON ( a.fkActionId = t.id )
				WHERE 1 AND a.fkUsersId !=0 and a.ActionType = 2 GROUP BY t.fkGamesId";
echo "<br><br>==>".$sql;
$sql_result = mysqli_query($con,$sql);
$gameCount = Array();
while($count = mysqli_fetch_assoc($sql_result)){	
	$gameCount[$count['fkGamesId']]['game'] = 	$count['fkGamesId'];
	$gameCount[$count['fkGamesId']]['playerCount'] = 	$count['playerCount'];
}
echo "<pre>";print_r($gameCount);echo "</pre>";
echo "<br><br>/** Game count of activities Table - End **/";
?>

