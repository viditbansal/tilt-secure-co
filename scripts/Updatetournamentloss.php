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
$lossArray 	= $playerArray = $winArray = $lossIds = $updateLossIds = array();
$totalCount = 0;
$tids		= '';
$insertQuery = 'Insert into activities (fkUsersId,fkActionId,fkPlayedId,ActionType,CountryCode,ActivityDate) VALUES ';
$sql 			= 	"SELECT count(id) as totalcount FROM `tournaments` WHERE TournamentStatus = 3 ";
$sql_result 	= 	mysqli_query($con,$sql);
while($row = mysqli_fetch_assoc($sql_result)){
	$totalCount = $row['totalcount'];
}
if($totalCount > 0){
	echo "Total Count ---- ".$totalCount; //die;
	for($i=0;$i*50<=$totalCount;$i++){
		// echo "<br>".$i." ---- ".$i*50 ." , 50";
		$tids = '';
		$sql 			= 	"SELECT id as tIds FROM `tournaments` WHERE TournamentStatus = 3 LIMIT ".($i*50).",50";
		$sql_result 	= 	mysqli_query($con,$sql);
		while($row = mysqli_fetch_assoc($sql_result)){
			if($row['tIds'] != '' && $row['tIds'] > 0)
				$tids .= $row['tIds'].',';
		}
		echo "<BR>".__LINE__."----SQL----".$tids; //die;
		if($tids != ''){
			$lossArray 	= $playerArray = $winArray = $lossIds = $updateLossIds = $dateArray = array();
			
			//Loss Ids from activities Table
			$sql 			= 	"SELECT fkActionId, GROUP_CONCAT( DISTINCT ( fkUsersId ) ) as lossIds FROM `activities` WHERE ActionType =4 AND fkActionId in (".rtrim($tids,',').")  GROUP BY fkActionId";
			$sql_result 	= 	mysqli_query($con,$sql);
			while($row = mysqli_fetch_assoc($sql_result)){
				$lossArray[$row['fkActionId']] = $row['lossIds'];
			}
			echo "<pre style='color:green'>Line No : ".__LINE__." -- lossArray <br>"; print_r($lossArray); echo "</pre>";
			
			//Win and player Ids
			$sql 			= 	"SELECT tp.fkTournamentsId, t.GameType, GROUP_CONCAT(Distinct(tp.fkUsersId)) as hPlayer, GROUP_CONCAT(Distinct(ep.fkUsersId)) as ePlayer, GROUP_CONCAT(Distinct(ts.fkUsersId)) as winIds, ts.DateCreated FROM `tournamentsplayed` as tp LEFT JOIN tournaments as t ON (t.id=tp.fkTournamentsId) LEFT JOIN `eliminationplayer`as ep on (tp.id=ep.fkTournamentsPlayedId) LEFT JOIN tournamentsstats as ts on (t.id=ts.fkTournamentsId) WHERE tp.fkTournamentsId in (".rtrim($tids,',').") GROUP BY tp.fkTournamentsId";
			$sql_result 	= 	mysqli_query($con,$sql);
			while($row = mysqli_fetch_assoc($sql_result)){
				$winArray[$row['fkTournamentsId']] = $row['winIds'];
				if($row['GameType'] == 2){
					$playerArray[$row['fkTournamentsId']] = $row['ePlayer'];
				}else
					$playerArray[$row['fkTournamentsId']] = $row['hPlayer'];
					
				$dateArray[$row['fkTournamentsId']] = $row['DateCreated'];
			} 
			echo "<pre style='color:green'>Line No : ".__LINE__." -- winArray <br>"; print_r($winArray); echo "</pre>";
			echo "<pre style='color:green'>Line No : ".__LINE__." -- playerArray <br>"; print_r($playerArray); echo "</pre>"; 
			// echo "<pre style='color:green'>Line No : ".__LINE__." -- dateArray <br>"; print_r($dateArray); echo "</pre>"; 
			
			foreach($winArray as $key=>$value){
				if(array_key_exists($key,$lossArray)){
					if($value != '' && $value != 0 && $lossArray[$key] != '' && $lossArray[$key] != 0)
						$lossIds[$key]=$value.','.$lossArray[$key];
					else if($value == '' || $value == 0)
						$lossIds[$key]=$lossArray[$key];
					else if($lossArray[$key] == '' || $lossArray[$key] == 0)
						$lossIds[$key]=$value;
				}else
					$lossIds[$key]=$value;
			}
			foreach($lossArray as $key=>$value){
				if(!array_key_exists($key,$winArray)){
					$lossIds[$key]=$value;
				}
			}
			echo "<pre style='color:green'>Line No : ".__LINE__." -- losswinids <br>"; print_r($lossIds); echo "</pre>";
			
			//Loss id to insert
			foreach($playerArray as $key => $value){
				if(array_key_exists($key,$lossIds)){
					$temp1 = explode(',',$value);
					$temp2 = explode(',',$lossIds[$key]);
					$updateLossIds[$key] = array_diff($temp1, $temp2);
				}
			}
			echo "<pre style='color:green'>Line No : ".__LINE__." -- updateLossIds <br>"; print_r($updateLossIds); echo "</pre>"; //die;
			
			//Insert into activities Table
			$time 	 =  date('Y-m-d  H:i:s');
			if(is_array($updateLossIds) && count($updateLossIds) > 0){
				foreach($updateLossIds as $key => $value){
					// echo "<pre style='color:green'>Line No : ".__LINE__."<br>"; print_r($value); echo "</pre>"; //die;
					if(is_array($value) && count($value) > 0){
						$query = '';
						$dateCreated = ($dateArray[$key] != '' && $dateArray[$key] != '0000-00-00 00:00:00') ? $dateArray[$key] :$time;
						foreach($value as $subkey => $subvalue){
							if($subvalue != '' && $subvalue > 0)
								$query .= "('".$subvalue."','".$key."',0,4,'','".$dateCreated."'),";
						}
						if($query != ''){
							$sql 			= 	$insertQuery.rtrim($query, ',');
							// $sql_result 	= 	mysqli_query($con,$sql);
							echo "<br>".__LINE__."----SQL----".$sql; //die;
						}
					}
				}
			}
		}//End Tournament ids
		echo "<br> END LOOP".($i+1)."<br><br>";
	}//End Tournament Loop
	
}
echo "<pre style='color:green'>  $$$----> Script executed successfully <----$$$ ";echo "</pre>"; die;
?>