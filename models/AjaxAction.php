<?php 
ob_start();
require_once('../includes/AdminCommonIncludes.php');
if(isset($_POST['action'])	&&	$_POST['action'] == 'PAYMENT_MODULE' ) {
	if(isset($_POST['stripeToken']) && trim($_POST['stripeToken']) != '' && isset($_POST['amount']) && trim($_POST['amount']) != '' && isset($_POST['developerId']) && trim($_POST['developerId']) != '' && isset($_POST['email']) && trim($_POST['email']) != '') {
		require_once('../controllers/DeveloperController.php');
		require_once('../controllers/AdminController.php');
		$developerObj	=	new DeveloperController();
		$adminObj  		=   new AdminController();
		$token  = $_POST['stripeToken'];
		$customer = Stripe_Customer::create(array(
		  'email' => $_POST['email'],
		  'card'  => $token
		));
		$fields				=	' * ';
		$condition			=	' id = 1 ';
		$comissionInfo		=	$adminObj->getSettingDetails($fields,$condition);
		$comissionValue		=	$comissionInfo[0]->Commission;
		$conversionValue	=	$comissionInfo[0]->ConversionValue;
		if($conversionValue < 1 )
			$conversionValue = 1;
		
		
		$actualAmount		=	$_POST['amount']/$conversionValue;
		$actualComission	=	ceil($actualAmount * ($comissionValue/100));
		$finalAmount		= 	ceil(($_POST['amount'] * 100)/$conversionValue);
		$comission			= 	ceil($finalAmount * ($comissionValue /100));
		
		$charge = Stripe_Charge::create(array(
		  'customer' => $customer->id,
		  'amount'   => $finalAmount+$comission,
		  'currency' => 'usd',
		  'description' => 'Coins purchased for '.(isset($_POST['developerName']) ? $_POST['developerName'] : '')
		));
		/** TO INSERT THE PAYMENT DETAILS **/
		$values	= "('".$_POST['developerId']."','".$customer->id."','".$token."','".$_POST['email']."','".$_POST['amount']."','".$actualAmount."','".$actualComission."','".addslashes($customer)."','".addslashes($charge)."','".date('Y-m-d H:i:s')."')";
		$developerObj->insertGamePaymentDetails($values);
		/** TO UDPATE THE BRAND PORTAL PAID AMOUNT **/
		$updateString = " Amount = Amount+".$_POST['amount'];
		$con = ' id = '.$_POST['developerId'];
		$developerObj->updateGameDevDetails($updateString,$con);
		$existsAmount		= $developerObj->selectSingleDeveloper('Amount',$con);
		$payment_condition 	= " PurchasedBy = 2,BrandDeveloperId = ".$_POST['developerId'].",Type = 4,Coins=".$_POST['amount'].",CoinType = 1,DateCreated = '".date('Y-m-d H:i:s')."'" ;
		$developerObj->insertPaymentHistoryDetails($payment_condition);
		if(isset($existsAmount[0]) && !empty($existsAmount[0]->Amount)){
			echo number_format($existsAmount[0]->Amount);
			$_SESSION['tilt_developer_amount'] = $existsAmount[0]->Amount;
		}
	}
	else
		echo 'Error';
}
if(isset($_POST['action'])	&&	$_POST['action'] == 'GAME_DETAILS' ) {
	require_once('../controllers/GameController.php');
	$gameManageObj   =   new GameController();
	$description = 'NO RESULT';
	$fields	=	" Description ";
	if(isset($_POST['gameId']) && $_POST['gameId'] !=''){
		$condition	=	" id = ".$_POST['gameId']." ";
		$gameDesc	=	$gameManageObj->selectGameDetails($fields,$condition);
		if(isset($gameDesc) && is_array($gameDesc) && count($gameDesc)>0){
			$description = $gameDesc[0]->Description;
		}
	}
	echo $description;
} 
if(isset($_POST['action'])	&&	$_POST['action'] == 'CHECK_TOURNAMENT' ) {
	require_once('../controllers/TournamentController.php');
	$tourManageObj   	=   new TournamentController();
	$alreadyExist		=	0;
	$condition			=	'';
	$_POST        = unEscapeSpecialCharacters($_POST);
	$_POST        = escapeSpecialCharacters($_POST);
	if(isset($_POST['edit_id'])	&&	$_POST['edit_id'] > 0 )	{
		$condition	.=	' id <> '.$_POST['edit_id'].'  AND ';
	}
	$fields				=	' id,TournamentName ';
	$condition			.=	" TournamentName ='".trim($_POST['tour_name'])."' AND  Status !=3 AND TournamentStatus !=3 ";
	$checkTournamentExist	=	$tourManageObj->selectTournament($fields,$condition);
	if(isset($checkTournamentExist)	&&	is_array($checkTournamentExist)	&&	count($checkTournamentExist) > 0){
			$alreadyExist	=	1;
	}
	echo $alreadyExist;
}
if(isset($_POST['action'])	&&	$_POST['action'] == 'CREATE_TOURNAMENT'){
	require_once('../controllers/TournamentController.php');
	require_once('../controllers/DeveloperController.php');
	require_once('../controllers/AdminController.php');
	$gameDevObj   	 =   new DeveloperController();
	$tourManageObj   =   new TournamentController();
	$adminManageObj  =   new AdminController();
	
	$tourStatus	= 'NOT_CREATED';
	$msg		= array();
	$postValues = unEscapeSpecialCharacters($_POST);
	$postValues      = escapeSpecialCharacters($postValues);
	$alreadyExist	= 0;
	//Check tournament exist
	$fields				 =	' id,TournamentName ';
	$condition			 =	" TournamentName ='".trim($postValues['tournament_name'])."' AND  Status !=3 AND TournamentStatus !=3 ";
	$checkTournamentExist	=	$tourManageObj->selectTournament($fields,$condition);
	if(isset($checkTournamentExist)	&&	is_array($checkTournamentExist)	&&	count($checkTournamentExist) > 0){
		
		if(strcasecmp($checkTournamentExist[0]->TournamentName,trim($postValues['tournament_name'])) == 0)
			$alreadyExist	=	1;
	}
	if($alreadyExist	==	0){
		$fields	=	' TermsAndConditions,TournamentRules,GameRules '; $where	= 	' id = 1 ';
		$templateArray	= $rules =	array();
		$rules['gen_terms_condition'] 		= "";
		$rules['gen_game_rules'] 			= "";
		$rules['gen_tournmentrules'] 		= "";
		$templatesRes	=	$adminManageObj->getSettingDetails($fields,$where);
		if(isset($templatesRes) && is_array($templatesRes) && count($templatesRes)>0){
			$gen_termsCond		=	$templatesRes[0]->TermsAndConditions;
			$gen_gamerules  	= 	$templatesRes[0]->GameRules;
			$gen_tournmentrules	=	$templatesRes[0]->TournamentRules;
			$rules['gen_terms_condition'] 		= $gen_termsCond;
			$rules['gen_game_rules'] 			= $gen_gamerules;
			$rules['gen_tournmentrules'] 		= $gen_tournmentrules;
			$rules        = unEscapeSpecialCharacters($rules);
			$rules        = escapeSpecialCharacters($rules);
		}
		$postValues['gen_terms_condition'] 	= $rules['gen_terms_condition'];
		$postValues['gen_game_rules'] 		= $rules['gen_game_rules'];
		$postValues['gen_tournmentrules']	= $rules['gen_tournmentrules'];
		$tournamentId	=	$tourManageObj->insertTournamentDetails($postValues);	// Insert tournament detail
		$_SESSION['tour_notification_msg_code'] = 1;
		// BEGIN: Developer Tilt$ update
		if( $tournamentId && isset($postValues['tilt_prize'])	&&	trim($postValues['tilt_prize'] >= 2)){ //tilt
			$condition	=	' id = '.$_SESSION['tilt_developer_id'];
			$amount	=	$coin	=	'';
			$update_string		=	'';
			$tiltFee = 0;
			if(isset($postValues['tilt_default_fee']) && $postValues['tilt_default_fee'] !='')
				$tiltFee	=	$postValues['tilt_default_fee'];
			if(($_SESSION['tilt_developer_amount'] - ($tiltFee.'+'.$postValues['tilt_prize'])) >= 0){
				$update_string		=	' Amount = Amount - ('.$postValues['tilt_prize'].'+'.$tiltFee.')';
				$condition	.=	'  AND Amount >=('.$postValues['tilt_prize'].'+'.$tiltFee.') ';
				$_SESSION['tilt_developer_amount']	=	($_SESSION['tilt_developer_amount']) - ($postValues['tilt_prize']+$tiltFee);
			}
			if(!empty($update_string)){
				$gameDevObj->updateGameDevDetails($update_string,$condition);
			}
		}
		if($tournamentId){
			$devId			=	$_SESSION['tilt_developer_id'];
			generatePDF($devId,$tournamentId);
			//START : PUSH NOTIFICATION FOR NEW TOURNAMENT
			$followfields 		= ' fkUsersId ';
			$followcondition 	= ' f.fkDevelopersId = '.$devId.' and f.Status = 1 and u.Status = 1 and u.BrandNewTournament = 1 ' ;
			$devDetails		= $gameDevObj->getBrandFollowList($followfields, $followcondition);				
			$followUser			= $tokenexists = '';
			$tokenArray  = $followUserArray = $sdkTokenArray = array();
			if($devDetails){
				foreach($devDetails as $bkey=>$bvalue){
					$followUser .= $bvalue->fkUsersId.',';
				}
				if($followUser != ''){
					$followUser = trim($followUser,',');
					$pnfields	 				= ' d.*,d.fkUsersId as UserId ';
					$pncondition 				= " and d.fkUsersId in (".$followUser.") and d.AppGameId = '0'";
					$notificationUserDetails 	= $gameDevObj->getUserDetailsForPN($pnfields, $pncondition);
					$message					= $_SESSION['tilt_developer_company'].' has been created a new tournament "'.$postValues['tournament_name'].'" ';
					$log_content = '';
					if($notificationUserDetails){
						foreach($notificationUserDetails as $nkey=>$value){
							$tokenexists .= $value->UserId.',';
							$gameDevObj->updateBadgeToken($value->Token);
							$success = sendNotificationAWS($message,$value->EndPointARN,$value->Platform,$value->Badge,'5',$tournamentId,$devId,0,'');
							if($success == '1')
								$log_content .= "\r\n To user(".$value->UserId.") : ".$message." - Success ";
							else
								$log_content .= "\r\n To user(".$value->UserId.") :  ".$message." - Failure ";
						}							
					}
					$tokenexists 		= trim($tokenexists,',');
					$tokenArray 		= explode(',',$tokenexists);
					$tokenArray			= array_unique($tokenArray);
					$followUserArray 	= explode(',',$followUser);
					$followUserArray	= array_unique($followUserArray);
					$sdkTokenArray		= array_diff($followUserArray,$tokenArray);
					if(is_array($sdkTokenArray)){
						foreach($sdkTokenArray as $tkey=>$tval){
							$pncondition 		= " and d.fkUsersId = ".$tval." order by LoginedDate limit 0,1 ";
							$sdkUserDetails	 	= $gameDevObj->getUserDetailsForPN(' d.AppGameId ', $pncondition);
							if($sdkUserDetails){
								$pnfields	 			= ' d.*,d.fkUsersId as UserId ';
								$pncondition 			= " and d.fkUsersId in (".$tval.") and d.AppGameId = '".$sdkUserDetails[0]->AppGameId."' ";
								$sdkUserTokenDetails	= $gameDevObj->getUserDetailsForPN($pnfields, $pncondition);
								if($sdkUserTokenDetails){
									foreach($sdkUserTokenDetails as $skey=>$value){
										$gameDevObj->updateBadgeToken($value->Token);
										$success = sendNotificationAWS($message,$value->EndPointARN,$value->Platform,$value->Badge,'5',$tournamentId,$devId,0,'');
										if($success == '1')
											$log_content .= "\r\n To user(".$value->UserId.") : ".$message." - Success ";
										else
											$log_content .= "\r\n To user(".$value->UserId.") :  ".$message." - Failure ";
									}
								}										
							}
						}
					}
				}
			}
			//END : PUSH NOTIFICATION FOR NEW TOURNAMENT
			$tourStatus	= $tournamentId;
			$msg		= "SUCCESS";
		}
		// END: Developer Tilt$ update
	} else { // If Tounament Name already exist
		$tourStatus	= 'ALREADY_EXIST';
		$msg		= "Tournament Already Exist";
	}
	$result = array("status"=>$tourStatus,"msg"=>$msg);
	echo json_encode($result);
}
/* END : Check whether tournament already exist */

/* START : Get Latitude and Longitude for search address(Used in auto complete location search) */
if(isset($_GET['countryname']) && !empty($_GET['countryname']) && isset($_GET['action']) && $_GET['action'] == 'COUNTRY') {
	if(isset($_GET['searchType']) && $_GET['searchType'] == 1)
		$jsonArray	=	getCountryLocation($_GET['countryname']); //Google Map API(text search) to get Lat, Lon
	else
		$jsonArray	=	getAddresstoLatLng($_GET['countryname']); //Normal google API
	$echoStr	=	'';
	if(isset($jsonArray['latitude'])) 
		$echoStr	=	$jsonArray['latitude'];
	
	if(isset($jsonArray['longitude'])) 
		$echoStr	.=	'###'.$jsonArray['longitude'];
	
	echo $echoStr;	
	die();
}
/* END : Get Latitude and Longitude for search address(Used in auto complete location search) */

/* START : Foursquare search module */
if(isset($_GET['action'])	&&	$_GET['action']	&&	$_GET['action'] == 'SEARCH_LOCATION' )	{
	require_once('../includes/FoursquareAPI.class.php');
	$client_key = "aad82a6f9e878e0187c5616cd6e0eb3515ff3938";
	$client_secret = "e3256f12849d4e633e91772a24fac43d736f2dd8";
	$foursquare = new FoursquareAPI($client_key,$client_secret);
	
	$searchkey = $latitude	= $longitude	= '';
	$response_json = 0;
	if(isset($_GET['curlat'])	&&	$_GET['curlat'] != '')
		$latitude	=	$_GET['curlat'];
	if(isset($_GET['curlong'])	&&	$_GET['curlong'] != '')
		$longitude	=	$_GET['curlong'];
	if((trim($latitude) == '' || $longitude == '') && (isset($_GET['location']) && $_GET['location'] != ''))
		list($latitude,$longitude) = $foursquare->GeoLocate($_GET['location']);
	
	if(isset($_GET['term'])	&&	$_GET['term'] != '')	
		$searchkey	=	$_GET['term'];
	
	$quryString = "&query=".urlencode($_GET['term']);
	$limit	= 	"&limit=5";
	$radiusString = "&radius=10000000";
	$locationResponse = getFourSquareInfo($latitude,$longitude,$quryString,$limit,$radiusString);
	if(!empty($locationResponse)) {
		foreach($locationResponse as $key=>$val) {
			$jsonResponseArray[$key]['id'] 		= $val['location']['lat'].','.$val['location']['lng'];
			$label_name	=	$val['name'];
			if(isset($val['location']))	{
				$label_value = '';
				if(isset($val['location']['address']))	{
					$label_value	.= $val['location']['address'].', ';
				}
				if(isset($val['location']['city']))	{
					$label_value	.= $val['location']['city'].', ';
				}
				if(isset($val['location']['state']))	{
					$label_value	.= $val['location']['state'].', ';
				}
				if(isset($val['location']['country']))	{
					$label_value	.= $val['location']['country'].', ';
				}
				
				$jsonResponseArray[$key]['value'] 	= $label_name.", ".rtrim($label_value,', ');
				$jsonResponseArray[$key]['label'] 	= $label_name.", ".rtrim($label_value,', ');
			}
		}
		$response_json =	json_encode($jsonResponseArray);
	}
	echo $response_json;
}
/* END : Foursquare search module */

/* START : Tournament PIN Generation */
if(isset($_POST['action'])	&&	$_POST['action'] == 'ADD_PINCODE' )	{
require_once('../controllers/TournamentController.php');
$tournamentObj  		=   new TournamentController();
$i=0;
$retainId	=	0;
$retainCodes	=	'';
$sno			=	1;
$noItem	=	10;
$pinArray	=	array();
	if(isset($_POST['tournamentId'])	&&	$_POST['tournamentId']	!=''	){
		if(isset($_POST['number'])	&&	$_POST['number']	!=''	){
		$sno	=	$retainId	=	$_POST['number'];
		}
		$tournamentsId	=	$_POST['tournamentId'];
		
		$fields			=	" id, PinCode, fkTournamentsId ";
		$condition		=	" fkTournamentsId	=	".$tournamentsId." AND Status	=	0 ";
			
		if(isset($_POST['codesList'])	&&	$_POST['codesList']	!='0'	){
			$codeList	=	explode(',',$_POST['codesList']);
			
			if(isset($codeList)	&&	is_array($codeList) &&	count($codeList)>0){
				$notInPins	=	'';
				foreach($codeList as $code){
					$notInPins	.=	"'".$code."',";
				}
				$notInPins	=	rtrim($notInPins,',');
				if($notInPins	!='')
					$condition	.=	" AND PinCode NOT IN (".$notInPins.") ";
			}
		}
		$unUsedPincode	=	$tournamentObj->selectPinCode($fields,$condition);
		$PincodeArray	=	array();
		if(isset($unUsedPincode)	&&	is_array($unUsedPincode) &&	count($unUsedPincode)>0){
			foreach($unUsedPincode as $key1 => $value){
				$PincodeArray[$value->id]['pincode']		=	$value->PinCode;
				$PincodeArray[$value->id]['TournamentsId']	=	$value->fkTournamentsId;
			}
		}
		$unUsedCount	=	count($PincodeArray);
		$remain	=	$rem	=	$noItem-$unUsedCount;
		if($remain>0){
			$replaceArray	=	array();
			while($remain>0){
				
				$j	=	1;
				$tempCodes	=	"";
				while($j<=$remain){
					$passphrase = getPassphrase(PIN_LENGTH);
					while(in_array($passphrase, $replaceArray))
						$passphrase = getPassphrase(PIN_LENGTH);
					$tempArray[] = $passphrase;
					$tempCodes	.=	"'".$passphrase."',";
					$j++;
				}
				if($tempCodes	!=''){
					$fields			=	" PinCode ";
					$condition		=	" PinCode IN (".rtrim($tempCodes,',').") ";
					$pincodeResult	=	$tournamentObj->selectPinCode($fields,$condition);
				}
				if(isset($pincodeResult)	&&	is_array($pincodeResult) &&	count($pincodeResult)>0){
					foreach($pincodeResult as $pin){
						if(in_array($pin, $tempArray))	;
						else $replaceArray[]	=	$pin;
					}
				}
				else {
				
					foreach($tempArray as $pin){
						 $replaceArray[]	=	$pin;
					}
				}
				$tot	=	count($replaceArray);
					$remain	=	$rem - $tot;
			}
			$today			=	date('Y-m-d');
			$values			=	" VALUES ";
			
			foreach($replaceArray as $key2 => $pincodes){
				
				$values		=	" VALUES ('".$pincodes."','".$today."',0,".$tournamentsId.",0)";
				$insertedId =	$tournamentObj->insertPinCode($values);
				$PincodeArray[$insertedId]['pincode']		=	$pincodes;
				$PincodeArray[$insertedId]['TournamentsId']	=	$tournamentsId;
			}
			
			
		}	
		foreach($PincodeArray as $keycode=>$codes){
			$retainCodes	.=	$codes['pincode'].",";
		}
		$retainCodes	=	rtrim($retainCodes,',');
	}
	
	
?>

<?php if(isset($PincodeArray)	&&	is_array($PincodeArray)	){ 
		foreach($PincodeArray as $key=>$pin) { ?>
			<tr>
				<td align="center" width="5%"><?php echo $sno; ?></td>
				<td align="left" width="20%"><?php echo $pin['pincode']; ?></td>
				<td width="5%"><a href='forcedownload?pin=<?php echo $pin['pincode']; ?>&tourid=<?php echo $pin['TournamentsId'];?>' target="_blank" title="PDF"><i class="fa fa-file-pdf-o"></i></a></td>
				<td align="center" width="10%"><input type="checkbox" name="pins[]" class="pins" id="pin_<?php echo $sno; ?>" value="<?php echo $key; ?>" /></td>
			</tr>
<?php 
	$sno++;	}
	  } ?>
	  <input type="hidden" id="showId_<?php echo $retainId; ?>" name="showId_<?php echo $retainId; ?>" value="<?php echo $retainCodes; ?>">
	  <input type="hidden" id="showCount_<?php echo $retainId; ?>" name="showCount_<?php echo $retainId; ?>" value="<?php echo $sno; ?>">
<?php 
} 
/* END : Tournament PIN Generation */

/* START : Show used PINs */
if(isset($_POST['action'])	&&	$_POST['action'] == 'SHOW_PINCODE' )	{
require_once('../controllers/TournamentController.php');
$tournamentObj  		=   new TournamentController();
$i=1;
$retainId	=	0;
$retainCodes	=	'';
$sno			=	1;
$noItem	=	10;
$pinArray	=	array();
	if(isset($_POST['tournamentId'])	&&	$_POST['tournamentId']	!=''	){
		if(isset($_POST['number'])	&&	$_POST['number']	!=''	){
		$sno	=	$retainId	=	$_POST['number'];
		}
		$tournamentsId	=	$_POST['tournamentId'];
		
		$fields			=	" PinCode ";
		$condition		=	" fkTournamentsId	=	".$tournamentsId." AND Status	=	1 ";
			
		if(isset($_POST['codesList'])	&&	$_POST['codesList']	!='0'	){
			$codeList	=	explode(',',$_POST['codesList']);
			
			if(isset($codeList)	&&	is_array($codeList) &&	count($codeList)>0){
				$notInPins	=	'';
				foreach($codeList as $code){
					$notInPins	.=	"'".$code."',";
				}
				$notInPins	=	rtrim($notInPins,',');
				if($notInPins	!='')
					$condition	.=	" AND PinCode NOT IN (".$notInPins.") ";
			}
		}
		$unUsedPincode	=	$tournamentObj->selectPinCode($fields,$condition);
		$PincodeArray	=	array();
		if(isset($unUsedPincode)	&&	is_array($unUsedPincode) &&	count($unUsedPincode)>0){
			foreach($unUsedPincode as $key1 => $value){
				$PincodeArray[]	=	$value->PinCode;	
			}
		}
		else { 	return 1;}
		foreach($PincodeArray as $codes){
			$retainCodes	.=	$codes.",";
		}
		$retainCodes	=	rtrim($retainCodes,',');
	}
	if(isset($PincodeArray)	&&	is_array($PincodeArray)	){ 
		foreach($PincodeArray as $key=>$pin) { ?>
			<tr>
				<td align="center" width="2%"><?php echo $sno; ?></td>
				<td align="left"><?php echo $pin; ?>
				</td>
			</tr>
<?php $i++;
	$sno++;	}?>
	<input type="hidden" id="showId_<?php echo $retainId; ?>" name="showId_<?php echo $retainId; ?>" value="<?php echo $retainCodes; ?>">
	<input type="hidden" id="showCount_<?php echo $retainId; ?>" name="showCount_<?php echo $retainId; ?>" value="<?php echo $sno; ?>">
<?php if(isset($i)	&&	$i<10){ echo 'ADD_MORE_STATUS=1'; } 
	} else echo '1';
}
/* END : Show used PINs */

/* START : Set/Rest pin listing */
if(isset($_POST['action'])	&&	$_POST['action'] == 'SET_PINCODE_LIST' )	{ ?>
	<table cellpadding="0" cellspacing="0" border="0" class="pop_up_list table table-striped" id="pincodeContainer" width="100%">
		<tr style="background:none repeat scroll 0 0 #1C4478;">
			<th align="center" width="5%" style="text-align:center;"># <i class="fa fa-sorted-icon"></i></th>
			<th align="left" colspan="3">PinCode <i class="fa fa-sorted-icon"></i></th>
		</tr>
	</table>
<?php 
}
/* END : Set/Rest pin listing */

?>