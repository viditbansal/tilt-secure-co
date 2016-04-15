<?php 
ini_set('display_errors', '1');
error_reporting(E_ALL ^ (E_NOTICE));
ini_set('max_execution_time', 7200);
ini_set('memory_limit', '1024M');

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
$uLimit 	= 0;
$tLimit 	= 0;
$tStartId 	= 1;
$latitude 		= '11.949535';
$longitude 		= '79.814258';
$Country 		= 'India';
$CountryCode	= 'IN';
$State 			= '';
$zone			= 'America/New_York';
$format			=	'Y-m-d H:i:s';
$deviceCreated	=	getCurrentTime($zone,$format);
$format			=	'Y-m-d';
$today			=	getCurrentTime($zone,$format);

if(isset($_GET['uLimit']) && $_GET['uLimit'] > 0 && isset($_GET['tLimit']) && $_GET['tLimit'] > 0 && isset($_GET['tid']) && $_GET['tid'] > 0){
	$uLimit 	= $_GET['uLimit'];
	$tLimit 	= $_GET['tLimit'];
	$tStartId 	= $_GET['tid'];
}else{
	echo "------->Please provide tid(Tournament Id), tLimit(Tournament Limit), uLimit(User Limit)  parameter in url(e.g : \"TournamentPlay.php?tid=112&tLimit=10&uLimit=100\") "; die;
}
$page = 1;
$paging = 0;
if(isset($_GET['page']) && $_GET['page'] > 0){
	$page = $_GET['page'];
	$paging = (1000*($page-1));
}
echo $paging." -- ".$page; //die;
if(isset($page) && $page > 0){
	$tourIdsArray = $tourNameArray = array();
	$sql = "Select t.id, t.TournamentName from tournaments as t where t.id >=".$tStartId." AND t.Status = 1 AND DATE_FORMAT(t.StartDate,'%Y-%m-%d %H:%i:%s') <= '".$deviceCreated."' AND t.TournamentName LIKE '%LT Tilt Fish%' AND t.TournamentStatus != 3 ORDER BY t.id LIMIT ".(($page-1)*10).", 10 ";
	$sql_result 	= 	mysqli_query($con,$sql);
	// echo $sql;
	
	while($row = mysqli_fetch_assoc($sql_result)){
		$tourIdsArray[]		= $row['id'];
		$tourNameArray[] 	= $row['TournamentName'];
	}
	// echo "<pre style='color:green'>Line No : ".__LINE__."<br>FILE : ".__FILE__."<br>"; print_r($tourNameArray); echo "</pre>"; //die;
	for($j = 0; $j<count($tourIdsArray); $j++){
	// for($j = 0; $j<10; $j++){
		$userNameArray = $userIdsArray = array();
		$fkTournamentsId = $tourIdsArray[$j];		// Tournament Id
		echo "<br><br>Line : ".__LINE__."<br>------Tournament------->".$tourNameArray[$j]."<br>";
		
		$format			=	'Y-m-d H:i:s';
		$dateCreated	=	getCurrentTime($zone,$format);
		$format			=	'H:i:s';
		$now			=	getCurrentTime($zone,$format);
		$datePlayed		=	$today;
		$dateTime   	=	$now;
		
		// $sql = "Select id, FirstName from users where Status = 1 AND LastName = 'alttabmobile' ORDER BY id LIMIT ".($paging + 100*$j).", 100";
		// $sql = "Select id, FirstName from users where Status = 1 AND LastName = 'alttabmobile' LIMIT 0, 100";
		$sql = "Select id, FirstName from users where Status = 1 AND id in (870,476)";
		// echo $sql."<br>";  
		$sql_result 	= 	mysqli_query($con,$sql);
		while($row = mysqli_fetch_assoc($sql_result)){
			$userIdsArray[]		= $row['id'];
			$userNameArray[] 	= $row['FirstName'];
		}
		if(count($userIdsArray) > 0){
			foreach($userIdsArray as $ukey => $uvalue){
				echo "<br>------ User id---".$uvalue."-- User Name -->".$userNameArray[$ukey]. " alttabmobile";
				$fkUsersId	= $uvalue;
				
				$maximumcount = $reserverFlag = 0;
				$fields	=	'';
				$playedId	 = $existPlayedId = '';
				$existingTournament = array();
				//Check user detail start
				$sql = "select  * from users where id = ".$fkUsersId." and Status = 1";
				$sql_result 	= 	mysqli_query($con,$sql);
				while($row = mysqli_fetch_assoc($sql_result)){
					$userDetails = $row;
				}
				if (!(isset($userDetails) && is_array($userDetails) && count($userDetails) > 0)) {
					echo "<pre style='color:red'>Line No : ".__LINE__."<br>---------> Your status is not in active state <----------"; echo "</pre>"; continue;
				}
				//Check user detail end
				
				if($latitude != ''	&&	$longitude != '')	{
					$fields	=	' ,SQRT( POW( 69.1 * (Latitude - '.$latitude.' ) , 2 ) 
														+ POW( 69.1 * ('.$longitude.' - Longitude ) * COS( Latitude / 57.3 ) , 2 ) )*1609.34 as Distance';
				}
				$sql = " Select t.CreatedBy,t.EntryFee,t.Type,t.TotalTurns,t.fkGamesId,t.fkUsersId,t.TournamentStatus,
							t.EndDate,t.StartDate,t.MaxPlayers,t.PIN,t.GameType,t.LocationRestrict,t.LocationBased,t.DelayTime,g.PlayTime,t.NextTime
							,t.fkUsersId as TournamentUserId,t.fkDevelopersId as TournamentGameDeveloperId,t.fkBrandsId as TournamentBrandId ".$fields." 
							from tournaments as t 
							left join games as g on (g.id = t.fkGamesId) where t.id = '".$fkTournamentsId."' and t.Status = 1 limit 0,1";
				
				$sql_result 	= 	mysqli_query($con,$sql);
				while($row = mysqli_fetch_assoc($sql_result)){
					$existingTournament[0] = $row;
				}
				
				if($existingTournament[0]['GameType'] == '1' && $existingTournament[0]['TotalTurns'] > 0){
					for($tt=1;$tt<=$existingTournament[0]['TotalTurns'];$tt++){
						// echo "<br>------->Turns".$tt;
						$tournaments = $existingPlay = array();
						$sql = " Select id,Status from tournamentsplayed where date(DatePlayed) = '".$datePlayed."' and fkUsersId = '".$fkUsersId."' and fkTournamentsId = '".$fkTournamentsId."' ORDER by id desc "; 
						$sql_result 	= 	mysqli_query($con,$sql);
						while($row = mysqli_fetch_assoc($sql_result)){
							$existingPlay[] = $row;
						}			
						if(isset($existingPlay)	&&	count($existingPlay) == $existingTournament[0]['TotalTurns'] && $existingTournament[0]['PIN'] != 1 ){
								// Your turn over for today 
								echo "<pre style='color:red'>Line No : ".__LINE__."<br>---------> Sorry! your turn is over for the day <----------"; echo "</pre>"; break;
						}
						if (!empty($existingPlay)	&&	$existingPlay[0]['Status'] == '0') {
							$tournaments['PlayedId'] 	= $existingPlay[0]['id'];
						}else{						
							$sql = " select count(id) as tot_count from  tournamentsplayed where fkTournamentsId = ".$fkTournamentsId." AND fkUsersId <> ".$fkUsersId." group by fkUsersId ";
							while($row = mysqli_fetch_assoc($sql_result)){
								$maxPlayer[] = $row;
							}
							if(isset($maxPlayer) && is_array($maxPlayer) && count($maxPlayer) > 0){
								$maximumcount = count($maxPlayer);
							}
							$sql = " Select t.MaxPlayers from tournaments as t where t.id = '".$fkTournamentsId."' and t.Status = 1 limit 0,1";
							$sql_result 	= 	mysqli_query($con,$sql);
							while($row = mysqli_fetch_assoc($sql_result)){
								$existingTournament[0]['MaxPlayers'] = $row['MaxPlayers'];
							}
							if($existingTournament[0]['MaxPlayers'] <= $maximumcount){
								echo "<pre style='color:red'>Line No : ".__LINE__."<br>---------> Sorry! This tournament has reached maximum players count. Please try some other tournament <----------"; echo "</pre>"; break;
							}
							$tournaments['PlayedId'] 	= 0;						
						}
						
						$tournaments['Reserve'] 	= $reserverFlag;
						if(isset($existingTournament[0]['Distance']))
							$tournaments['Distance'] 			= $existingTournament[0]['Distance'];	
						$tournaments['LocationRestrict'] 		= $existingTournament[0]['LocationRestrict'];
						$tournaments['LocationBased'] 			= $existingTournament[0]['LocationBased'];
						$tournaments['PIN'] 					= $existingTournament[0]['PIN'];
						$tournaments['GamesId'] 				= $existingTournament[0]['fkGamesId'];
						$tournaments['Turns'] 					= $existingTournament[0]['TotalTurns'];
						$tournaments['MaxPlayers'] 				= $existingTournament[0]['MaxPlayers'];
						$tournaments['EntryFee'] 				= $existingTournament[0]['EntryFee'];
						$tournaments['Type'] 					= $existingTournament[0]['Type'];
						$tournaments['GameType'] 				= $existingTournament[0]['GameType'];
						$tournaments['CreatedBy'] 				= $existingTournament[0]['CreatedBy'];
						$tournaments['TournamentUserId'] 		= $existingTournament[0]['TournamentUserId'];
						$tournaments['TournamentGameDeveloperId'] 	= $existingTournament[0]['TournamentGameDeveloperId'];
						$tournaments['TournamentBrandId'] 		= $existingTournament[0]['TournamentBrandId'];
						
						//Check and add player join
						if( isset($tournaments['PlayedId']) && $tournaments['PlayedId'] != '' && $tournaments['PlayedId'] != 0){
							$existPlayedId 		  = $tournaments['PlayedId'];
						}
						else{
								unset($exists);
								$sql = " select id from tournamentsplayed where fkTournamentsId = ".$fkTournamentsId." and fkUsersId = ".$fkUsersId; 
								$sql_result 	= 	mysqli_query($con,$sql);
								while($row = mysqli_fetch_assoc($sql_result)){
									$exists[] = $row;
								}
								if(!(isset($exists) && is_array($exists) && count($exists) > 0) ){
									$sql = " update tournaments set PlayersJoined = PlayersJoined+1 where id = ".$fkTournamentsId." and PlayersJoined < ".$tournaments['MaxPlayers'];
									$sql_result 	= 	mysqli_query($con,$sql);
								}
						}
						//Check and add player join
						
						//Add played entire
						$City = '';
						$playedId = 0;
						$sql = "INSERT INTO tournamentsplayed SET 	Latitude = '".$latitude."',
																	Longitude = '".$longitude."',
																	City = '".$City."',
																	State = '".$State."',
																	Country = '".$Country."',
																	CountryCode = '".$CountryCode."',
																	fkUsersId = '".$fkUsersId."',
																	DatePlayed = '".$datePlayed."',
																	DateCreated = '".$dateCreated."',
																	StartTime = '".$dateTime."',
																	EndTime = '".$dateTime."',
																	fkTournamentsId = '".$fkTournamentsId."',
																	fkGamesId = '".$tournaments['GamesId']."'";
						// echo $sql;
						$sql_result	= 	mysqli_query($con,$sql);
						$playedId	= mysqli_insert_id($con);
						//Add played entire
						
						if($playedId > 0){
							//Add activity
							$countryCode = $CountryCode;
							if($CountryCode == '' && $Country != ''){
								$sql = "select CountryCode from countries where Country = '".$Country."' and Status = 1";
								$sql_result 	= 	mysqli_query($con,$sql);
								while($row = mysqli_fetch_assoc($sql_result)){
									$countryCodeArray = $row;
								}
								if($countryCodeArray)
									$countryCode = $countryCodeArray['CountryCode'];
							}
							$activeCountryCode = '';
							if($countryCode != '')
								$activeCountryCode = " CountryCode = '".$countryCode."', ";
								
							$sql = "INSERT INTO activities SET 	fkUsersId = '".$fkUsersId."',
																fkActionId = '".$fkTournamentsId."',
																fkPlayedId = '".$playedId."',
																ActionType = '2',".$activeCountryCode."
																ActivityDate = '".$dateCreated."'";
							$sql_result 	= 	mysqli_query($con,$sql);
							//Add activity
							
							// Score update for tournament 
							$playedScore = rand(1,50);
							$sql = "update tournamentsplayed SET finalScore = '".$playedScore."', PlayerCurrentHighScore = '".$playedScore."', TournamentHighScore = '".$playedScore."', StartTime = '".$dateTime."', EndTime = '".$dateTime."' WHERE id = ".$playedId;
							$sql_result 	= 	mysqli_query($con,$sql);
							
							$sql 		 	  = " select max(TournamentHighScore) as highScore, max(finalScore) as finalScore from tournamentsplayed where fkTournamentsId = ".$fkTournamentsId." and fkUsersId = ".$fkUsersId;
							$sql_result 	= 	mysqli_query($con,$sql);
							while($row = mysqli_fetch_assoc($sql_result)){
								$playDetails[0] = $row;
							}
							if(isset($playDetails) && is_array($playDetails) && count($playDetails) > 0){
								$sql = " update tournamentsplayed set TournamentHighScore = '".$playDetails[0]['highScore']."' where fkTournamentsId = ".$fkTournamentsId." and fkUsersId = ".$fkUsersId;
								$sql_result 	= 	mysqli_query($con,$sql);
								
								$sql = " update tournamentsplayed set finalScore = '".$playDetails[0]['finalScore']."' where id = ".$playedId;
								$sql_result 	= 	mysqli_query($con,$sql);
							}
							// Score update for tournament 
							
							//Update tournament high score
							$sql 		 	  = " select CurrentHighestScore from tournaments where id = ".$fkTournamentsId;
							$sql_result 	= 	mysqli_query($con,$sql);
							$sql = '';
							while($row = mysqli_fetch_assoc($sql_result)){
								if($row['CurrentHighestScore'] < $playedScore){
									$sql = "update tournaments set CurrentHighestScore = '".$playedScore."' where id = ".$fkTournamentsId;
								}
							}
							if($sql != '')
								$sql_result 	= 	mysqli_query($con,$sql);
							//Update tournament high score
						}
					}
				}
			}
		}
	}
}
?>
<br><br><br><br>
<html>
<head>
<title>TiLT</title>		
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"> 
</head>
<body>
	<div style="text-align:center">
	<?php
		if(isset($tLimit) && $tLimit > 0){
			$page = floor($tLimit/10);
			for($a=1;$a<=$page;$a++){
			?>
				<a href="TournamentPlay.php?tid=<?php echo $tStartId; ?>&tLimit=<?php echo $tLimit; ?>&uLimit=<?php echo $uLimit; ?>&page=<?php echo $a; ?>"><?php echo $a; ?></a>
			<?php }
		}
	?>
	</div>
</body>
</html>