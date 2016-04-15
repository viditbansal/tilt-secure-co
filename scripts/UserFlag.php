<?php 

ini_set('max_execution_time', 600);
/*$mysqli_server 	= 'localhost';
$mysqli_user 	= 'root';
$mysqli_pass 	= '';
$mysqli_name 	= 'tilt';*/

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
if(!isset($_GET['Type'])){
	$_GET['Type'] = '1';
}

// $sql = "update `users` set Location = 'India',City = 'Puducherry', State = 'Puducherry',Country = 'India'   where 1 and id in (307,739)";
// mysqli_query($con,$sql);
$sql 			= 	"SELECT fkUsersId FROM activities WHERE ActionType in (1,2) group by fkUsersId";
$sql_result 	= 	mysqli_query($con,$sql);
$gameCount 		= 	$countryArray = $userArrayFlag = $userArray = $countryArrayFlag = Array();
$userIds 		= 	'';
while($count = mysqli_fetch_assoc($sql_result)){
	$userArray[] = $count['fkUsersId'];
}
$user = $userCountry = array();
if(is_array($userArray)){
	$userArray 	=	array_unique($userArray);
	$userIds 	= 	implode($userArray,',');
	$sql 		= 	"SELECT id,Country FROM users WHERE Country != '' and Country != '(null)' and id in (".$userIds.")";
	echo "<br>========================".$sql;
	
	$sql_result = 	mysqli_query($con,$sql);
	$i = 0;
	while($count = mysqli_fetch_assoc($sql_result)){
		$userCountry[]					=	$count['id'];	
		$countryArray[] 				= 	$count['Country'];
		$userArrayFlag[$i]['UserId'] 	= 	$count['id'];
		$userArrayFlag[$i]['Country'] 	= 	$count['Country'];
		$user[$count['id']] 			= 	$count['Country'];
		$i++;
	}
}
if(is_array($countryArray) && count($countryArray) > 0){
	$countryArray 	= 	array_unique($countryArray);
	$countryName  	= 	implode($countryArray,"','");
	$sql 			= 	"SELECT Country,CountryCode FROM countries WHERE Country in ('".$countryName."')";
	$sql_result 	= 	mysqli_query($con,$sql);
	$i = 0;
	while($count = mysqli_fetch_assoc($sql_result)){
		$countryArrayFlag[$count['Country']] = $count['CountryCode'];
		$i++;
	}
}
if($_GET['Type'] == '1'){
	echo "<br><br>/** Country **/";		
	if(is_array($userArrayFlag)){
		foreach($userArrayFlag as $value){
			if(array_key_exists($value['Country'],$countryArrayFlag)){
				$countrykey 	= 	$value['Country'];
				$countryCode 	=  	$countryArrayFlag[$countrykey];
				if($countryCode != ''){
					$sql 		= 	"update activities set CountryCode = '".$countryCode."'  where fkUsersId = ".$value['UserId']." and CountryCode = '' and ActionType = 1 " ;
					$sql_result = 	mysqli_query($con,$sql);
					echo '<br>--->'.$sql.'<---';
				}
			}
		}
	}
}
if($_GET['Type'] == '2'){
	$sql 		= "SELECT id,fkUsersId,fkTournamentsId,CountryCode FROM tournamentsplayed WHERE CountryCode != '' and CountryCode != '(null)' ";
	echo '<br>--sql->'.$sql.'<---';
	$sql_result = mysqli_query($con,$sql);
	$i = 0;
	while($count = mysqli_fetch_assoc($sql_result)){	
		if($count['CountryCode'] != ''){
			$sql = "update activities set CountryCode = '".$count['CountryCode']."' where ActionType = 2 and fkPlayedId = ".$count['id']." and fkActionId = '".$count['fkTournamentsId']."'";
			$result = mysqli_query($con,$sql);
			echo '<br>--->'.$sql.'<---';	
		}
		
	}
}

if($_GET['Type'] == '3'){
	$sql 		= "SELECT ep.id,ep.fkUsersId,tp.fkTournamentsId,ep.CountryCode FROM eliminationplayer as ep
					left join tournamentsplayed as tp on (tp.id = ep.fkTournamentsPlayedId)
					WHERE ep.CountryCode != '' and ep.CountryCode != '(null)' ";
	echo '<br>--sql->'.$sql.'<---';
	$sql_result = mysqli_query($con,$sql);
	$i = 0;
	while($count = mysqli_fetch_assoc($sql_result)){	
		if($count['CountryCode'] != ''){
			$sql = "update activities set CountryCode = '".$count['CountryCode']."' where ActionType = 2 and fkPlayedId = ".$count['id']." and fkActionId = '".$count['fkTournamentsId']."'";
			$result = mysqli_query($con,$sql);
			echo '<br>--->'.$sql.'<---';	
		}
		
	}
}

if($_GET['Type'] == '4'){
	$sql 			= 	"SELECT id,fkUsersId,fkTournamentsId,CountryCode FROM tournamentsplayed  WHERE (CountryCode = '' or CountryCode = '(null)') and fkUsersId in (".implode($userCountry,',').")";
	echo '<br>--sql->'.$sql.'<---';
	$sql_result 	= 	mysqli_query($con,$sql);
	while($count = mysqli_fetch_assoc($sql_result)){
		echo "<br><br><b>Tournament Played Id = ".$count['id']."</b>";		
		if(isset($user[$count['fkUsersId']]) && isset($countryArrayFlag[$user[$count['fkUsersId']]]) && !empty($countryArrayFlag[$user[$count['fkUsersId']]])){
			$sql 	= 	"update activities set CountryCode = '".$countryArrayFlag[$user[$count['fkUsersId']]]."' where ActionType = 2 and fkPlayedId = ".$count['id']." and fkActionId = '".$count['fkTournamentsId']."'";
			$result = 	mysqli_query($con,$sql);
			echo '<br>--->'.$sql.'<---';
			$sql 	= 	"update tournamentsplayed set CountryCode = '".$countryArrayFlag[$user[$count['fkUsersId']]]."' where id = ".$count['id'];
			echo '<br>--->'.$sql.'<---';
			$result = 	mysqli_query($con,$sql);
		}
	}
}

if($_GET['Type'] == '5'){
	$sql 		= 	"SELECT ep.id,ep.fkUsersId,tp.fkTournamentsId,ep.CountryCode FROM eliminationplayer as ep
					left join tournamentsplayed as tp on (tp.id = ep.fkTournamentsPlayedId)
					WHERE (ep.CountryCode = '' or ep.CountryCode = '(null)') and ep.fkUsersId in (".implode($userCountry,',').")";
	echo '<br>--sql->'.$sql.'<---';
	$sql_result = 	mysqli_query($con,$sql);
	while($count = mysqli_fetch_assoc($sql_result)){	
		echo "<br><br><b>Tournament Played Id = ".$count['id']."</b>";		
		if($count['CountryCode'] == '' && isset($user[$count['fkUsersId']]) && isset($countryArrayFlag[$user[$count['fkUsersId']]]) && !empty($countryArrayFlag[$user[$count['fkUsersId']]])){
			$sql 	= 	"update activities set CountryCode = '".$countryArrayFlag[$user[$count['fkUsersId']]]."' where ActionType = 2 and fkPlayedId = ".$count['id']." and fkActionId = '".$count['fkTournamentsId']."'";
			$result = 	mysqli_query($con,$sql);
			echo '<br>--->'.$sql.'<---';
			$sql 	= 	"update eliminationplayer set CountryCode = '".$countryArrayFlag[$user[$count['fkUsersId']]]."' where id = ".$count['id'];
			echo '<br>--->'.$sql.'<---';
			$result = 	mysqli_query($con,$sql);
		}
	}
}

if($_GET['Type'] == '6' && isset($_GET['UserId']) && !empty($_GET['UserId'])){
	$sql 	= 	"update users set Location = 'India',City = 'Puducherry', State = 'Puducherry',Country = 'India'  where 1 and id in (".$_GET['UserId'].")";
	echo '<br>--->'.$sql.'<---';
	mysqli_query($con,$sql);
}
?>

