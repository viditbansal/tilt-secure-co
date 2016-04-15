<?php 
ini_set('display_errors', '1');
error_reporting(E_ALL ^ (E_NOTICE));
ini_set('max_execution_time', 7200);
ini_set('memory_limit', '4120M'); // 1024 - 3 tourn,4120 - 8, 6168 - 
/*$mysqli_server 	= 'localhost';
$mysqli_user 	= 'root';
$mysqli_pass 	= '';
$mysqli_name 	= 'tilt';*/
$mysqli_server 	= 'aa1vo4uao1prtm6.cslj5bbd5oqi.us-west-2.rds.amazonaws.com';
$mysqli_user 	= 'tiltbetadbuser';
$mysqli_pass 	= 'tb2db0us1er4';
$mysqli_name 	= 'ebdb';

//$webservice = "http://172.21.4.104/MGC";
$webservice = "http://tiltbeta.elasticbeanstalk.com";
//$webservice = "https://api.tilt.co";



//connect db 
$con = mysqli_connect($mysqli_server,$mysqli_user,$mysqli_pass);
if (mysqli_connect_errno()) {
   	printf("Connect failed: %s\n", mysqli_connect_error($con));
   	exit();
}
$db_con = mysqli_select_db($con,$mysqli_name) or die('Error in selecting databse MySQL');//

echo "Execution Time ----> ".ini_get('max_execution_time')."<br><br>"; //die;

require_once('../admin/includes/CommonFunctions.php');

global $con, $fkTournamentsId, $latitude, $longitude, $fkUsersId, $Country, $CountryCode, $State, $distance, $playDistance;
$UserIdArray = array();

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

$page 	= 1;
$paging = 0;
if(isset($_GET['page']) && $_GET['page'] > 0){
	$page = $_GET['page'];
	$paging = 100 * ($page-1);
	echo $paging;
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
	<table width="35%" border="0" align="center"> 
		<tr>
			
				<?php
		
					if(isset($tLimit) && $tLimit > 0){
						$totalpage = floor($tLimit/10);
						for($a=1;$a<=$totalpage;$a++){
							echo '<td>';
							if($page != $a){
						?>
							<a href="TournamentPlay_API.php?tid=<?php echo $tStartId; ?>&tLimit=<?php echo $tLimit; ?>&uLimit=<?php echo $uLimit; ?>&page=<?php echo $a; ?>"><?php echo $a; ?></a>
						<?php 
							}
							else{
								echo $a;
							}
							echo '</td>';
						}
					}
				?>
			
		</tr>
	</table>
	
</body>
</html>
<br><br><br><br>
<?php
if(isset($page) && $page > 0){

	/*START : Basic User Login details */
	$data['ClientId'] 		= 'aad82a6f9e878e0187c5616cd6e0eb3515ff3938';
	$data['ClientSecret'] 	= 'e3256f12849d4e633e91772a24fac43d736f2dd8';
	/*END : Basic User Login details */


	/* START : Getting User Login details */
	$sql = "Select id,Email,ActualPassword,FirstName from users where Status = 1 AND LastName = 'alttabmobile' ORDER BY RAND() LIMIT 0, ".$uLimit;
	echo $sql."<br>"; 
	$sql_result 	= 	mysqli_query($con,$sql);
	while($row = mysqli_fetch_assoc($sql_result)){
		$userIdsArray[]		= $row['id'];
		$userNameArray[$row['id']] 	= $row;
	}
	if(count($userIdsArray) > 0){
		foreach($userIdsArray as $ukey => $uvalue){
			/*Start : Basic User Login details */
			$data['Email'] 		= $userNameArray[$uvalue]['Email'];
			$data['Password'] 	= $userNameArray[$uvalue]['ActualPassword'];
			/*End : Basic User Login details */
			$userNameArray[$uvalue]['AccessToken'] = $loginResponse['login']['AccessToken'];
			
			$url				=	$webservice.'/oauth2/password/token';
			$loginResponse 		= 	curlRequest($url, 'POST', $data);
			
			if(isset($loginResponse) && is_array($loginResponse) && $loginResponse['meta']['code'] == 201 && isset($loginResponse['login']) && is_array($loginResponse['login'])) {
				$userNameArray[$uvalue]['AccessToken'] = $loginResponse['login']['AccessToken'];
			}						
		}
	}
	echo '<pre>===>';print_r($userNameArray);echo '<===</pre>';
	/* END : Getting User Login details */


	/*START : Basic details for tournament play process*/
	$playData['Latitude'] 		= '11.949535';
	$playData['Longitude'] 		= '79.814258';
	$playData['Country'] 		= 'India';
	$playData['CountryCode'] 	= 'IN';
	/*END : Basic User Login details */
	for($i=0;$i<($tLimit/10);$i++){
		$tourIdsArray = $tourNameArray = array();
		
		// Randomly selecting tournament for playing
		$sql = "Select t.id, t.TournamentName,TotalTurns 
				from tournaments as t			
				where t.GameType = 1 and t.id >=".$tStartId." AND t.Status = 1 AND DATE_FORMAT(t.StartDate,'%Y-%m-%d %H:%i:%s') <= '".$deviceCreated."'  AND DATE_FORMAT(t.EndDate,'%Y-%m-%d %H:%i:%s') >= '".$deviceCreated."' AND t.TournamentName LIKE '%LT Tilt Fish%' LIMIT ".(($page-1)*10).", 10 ";
		echo '--->'.$sql.'<---';
		$sql_result 	= 	mysqli_query($con,$sql);
		while($row = mysqli_fetch_assoc($sql_result)){
			$tourIdsArray[]				= $row['id'];
			$tourNameArray[] 			= $row['TournamentName'];
			$turnsArray[$row['id']]		= $row['TotalTurns'];
		}
		for($j = 0; $j<count($tourIdsArray); $j++){
			$fkTournamentsId = $tourIdsArray[$j];		// Tournament Id	
			echo 'Tournament Id : '.$fkTournamentsId.'<br><br>';
			$totalTurns = 1;
			if(isset($turnsArray[$fkTournamentsId]))
				$totalTurns = $turnsArray[$fkTournamentsId];
			
			// START : Start tournament play endpoint //		
			$url						=	$webservice.'/v3/tournaments/'.$fkTournamentsId;
			if(isset($userNameArray) && is_array($userNameArray) && count($userNameArray) > 0){
				foreach($userNameArray as $key=>$value){				
					for($k=0;$k<$totalTurns;$k++){
						//Start tournament play endpoint
						$playResponse 		= 	curlRequest($url, 'POST', $playData,$value['AccessToken']);
						if(isset($playResponse) && is_array($playResponse) ) {
							if( isset($playResponse['TournamentsPlayDetails']['PlayId']) && $playResponse['TournamentsPlayDetails']['PlayId'] != '' ){
								//START : Score updating process 
								$playedScore = rand(1,50);
								$scoreupdate['Score'] 			= $playedScore;
								$scoreupdate['Status'] 			= 2;
								$scoreupdate['TournamentId'] 	= $fkTournamentsId;
							
								//Calling score update endpoint
								$updateurl						=	$webservice.'/v3/tournaments/'.$playResponse['TournamentsPlayDetails']['PlayId'];
								$scoreupdateResponse 			= 	curlRequest($updateurl, 'PUT', json_encode($scoreupdate),$value['AccessToken']);
								//END : Score updating process 						
							}					
						}			
					}	
				}
			}
			// END : Start tournament play endpoint //				
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
	<table width="35%" border="0" align="center"> 
		<tr>
			
				<?php
		
					if(isset($tLimit) && $tLimit > 0){
						$totalpage = floor($tLimit/10);
						for($a=1;$a<=$totalpage;$a++){
							echo '<td>';
							if($page != $a){
						?>
							<a href="TournamentPlay_API.php?tid=<?php echo $tStartId; ?>&tLimit=<?php echo $tLimit; ?>&uLimit=<?php echo $uLimit; ?>&page=<?php echo $a; ?>"><?php echo $a; ?></a>
						<?php 
							}
							else{
								echo $a;
							}
							echo '</td>';
						}
					}
				?>
			
		</tr>
	</table>
	
</body>
</html>
<br><br><br><br>