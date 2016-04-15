<?php
require_once('includes/CommonIncludes.php');
require_once("includes/phmagick.php");
require_once('controllers/GameController.php');
require_once('controllers/TournamentController.php');
require_once('controllers/DeveloperController.php');
$gameDevObj   	 =   new DeveloperController();
require_once('controllers/AdminController.php');
$gameManageObj   =   new GameController();
$tourManageObj   =   new TournamentController();
$adminManageObj  =   new AdminController();
developer_login_check();
$logoPath	=	'';
if(isset($_SESSION['tilt_developer_logo']) && trim($_SESSION['tilt_developer_logo']) != ''){
	if($_SESSION['tilt_developer_logo']=='developer_logo.png')
		$logoPath = GAME_IMAGE_PATH.$_SESSION['tilt_developer_logo'];
	else $logoPath = DEVELOPER_IMAGE_PATH.$_SESSION['tilt_developer_logo'];
}
$devId		=	$_SESSION['tilt_developer_id'];
$class		=	$photoUpdateString	=	$tournamentId = $tilt_prize = '';
$tiltCoin	=	$virtualCoin	=	$prizeCoin = $couponLimit = $defautPosition	=	$locRest	=	$show = $pinStatus = 0;
$gameId		=	$tournamentName=$maxPlayer=$gameType=$startTime=$endTime=$pinCode=$youtubeLink=$couponCode	=$oldbannerImage =$minPlayer = '';
$tourStateId = $tourCountryId = '';
$gen_termsCond = $gen_gamerules = $gen_tournmentrules = $gen_privacyPolicy = $tour_termsCond = $tour_gamerules = $tour_rules = $tour_privacyPolicy = '';
$requiresdownloadpartnersapp = $partnerappiospackageid = $partnerappandroidpackageid = $partnerappiosurl = $partnerappandroidurl = '';
$tourRulesResult = array();
$usId = USID;
$today		=	date('Y-m-d H:i:s');
$bannertextDisp	= $bannerimageDisp   =	$youtubeurlDisp	= $youtubecodeDisp = $youtubeImageDisp 	= 'style="display:none"';
$banner_video_array = array(
	"1" => "mp4"
);
$FeeType = $editStatus = 1;
//$marquee = '';
//sucess message notification
if(isset($_SESSION['tour_notification_msg_code'])){
	if($_SESSION['tour_notification_msg_code'] == 1){
		$sucess_msg_1 	= "Tournament added successfully";
	}
	else if($_SESSION['tour_notification_msg_code'] == 2){
		$sucess_msg_1 	= "Tournament updated successfully";
	}
	else if($_SESSION['tour_notification_msg_code'] == 3){
		$sucess_msg_2 	= "Tournament updated successfully";
		$show 			= 1;
	}
	else if($_SESSION['tour_notification_msg_code'] == 4){
		$sucess_msg_3 	= "Tournament updated successfully";
		$show 			= 2;
	}
	unset($_SESSION['tour_notification_msg_code']);
}

$adminResult 		= $adminManageObj->getSettingDetails(' TiltFee ', ' id=1 ');
$tiltdefaultfee  	=  isset($adminResult[0]->TiltFee) ? $adminResult[0]->TiltFee : '';

//country details
$fields			=	' id,Country';
$conditions		=	' Status = 1 ';
$countryList	=	$tourManageObj->getCountryList($fields,$conditions);
if(!empty($countryList))	{
	foreach($countryList as $key=>$value)	{
		$countryArray[$value->id]	=	$value->Country;
	}
	asort ($countryArray);
}
$fields			=	' id,State';
$conditions		=	' Status = 1 AND fkCountriesId = '.$usId.' ';
$stateList		=	$tourManageObj->getStateList($fields,$conditions);
if(!empty($stateList))	{
	foreach($stateList as $key=>$value)	{
		$usStateArray[$value->id]	=	$value->State;
	}
}

//Get rules template
$fields	=	' TermsAndConditions,TournamentRules,GameRules '; $where	= 	' id = 1 ';
$templateArray	=	array();
$templatesRes	=	$adminManageObj->getDistance($fields,$where);
if(isset($templatesRes) && is_array($templatesRes) && count($templatesRes)>0){
		$gen_termsCond		=	$templatesRes[0]->TermsAndConditions;
		$gen_gamerules  	= 	$templatesRes[0]->GameRules;
		$gen_tournmentrules	=	$templatesRes[0]->TournamentRules;
}

if(isset($_POST['tournamentLab']) && $_POST['tournamentLab'] != '' && isset($_POST['edit_id'])	&&	$_POST['edit_id'] > 0){
	$sql 			= '';
	$insert_id		=	$tournamentId	=	$_POST['edit_id'];
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);

	if(isset($_POST['prize_type'])){
		$sql 	.=	"Type 				= 	'".$_POST['prize_type']."',";
		if($_POST['prize_type'] == 4){
				$sql 	.=	"Prize 				= 	0,";
		}
		else if(isset($_POST['prize_coin']))
			$sql 	.=	"Prize 				= 	'".$_POST['prize_coin']."',";
	}

	if(isset($_POST['elimination'])	&&	trim($_POST['elimination']) == 1){
		$sql 	.=	"GameType 			= 	'2',";
		if(isset($_POST['can_we_start'])	&&	$_POST['can_we_start'] != '')
			$sql	.=	"DelayTime 	= 	'".$_POST['can_we_start']."',";
	}else{
		$sql 	.=	"GameType 			= 	'1',";
		$sql	.=	"DelayTime 			= 	'00:00:00',";
	}

	if(isset($_POST['entry_type']) && $_POST['entry_type'] == 1){
		$sql	.=	"FeeType 	= 	'2',";
		if(isset($_POST['entry_fee']) && $_POST['entry_fee'] != '')
			$sql	.=	"EntryFee 	= 	'".$_POST['entry_fee']."',";
	}else {
		$sql	.=	"FeeType 	= 	'1', ";
		$sql	.=	"EntryFee 	= 	'0',";
	}

	//START : Location restriction
	if(isset($_POST['pin_chk_option'])	&&	trim($_POST['pin_chk_option'] != ''))
		$sql	.=	"PIN			=	1,";
	else
		$sql	.=	"PIN			=	0,";
	if(isset($_POST['location'])	&&	trim($_POST['location'] != ''))
		$sql	.=	"LocationRestrict			=	1,";
	else
		$sql	.=	"LocationRestrict			=	0,";
	if(isset($_POST['loc_chk_option']) && $_POST['loc_chk_option'] != '' ){//Location based tournament
		$sql	.=	"LocationBased			=	1,";
		if(isset($_POST['country_loc_tour'])	&&	trim($_POST['country_loc_tour']!=""))
			$sql 	.=	"fkCountriesId 			= 	'".$_POST['country_loc_tour']."',";
		if(isset($_POST['state_loc_tour'])	&&	trim($_POST['state_loc_tour']!=""))
			$sql 	.=	"fkStatesId 			= 	'".$_POST['state_loc_tour']."',";
		else
			$sql 	.=	"fkStatesId 			= 	'0',";
		if(isset($_POST['locationsearch_tour'])	&&	trim($_POST['locationsearch_tour']!=""))
			$sql 	.=	"TournamentLocation 	= 	'".$_POST['locationsearch_tour']."',";
		if(isset($_POST['latitude_tour'])	&&	trim($_POST['latitude_tour']!=""))
			$sql 	.=	"Latitude 			= 	'".$_POST['latitude_tour']."',";
		if(isset($_POST['longitude_tour'])	&&	trim($_POST['longitude_tour']!=""))
			$sql 	.=	"Longitude 			= 	'".$_POST['longitude_tour']."',";
	}
	else{
		$sql	.=	"LocationBased		=	0, ";
		$sql 	.=	"fkCountriesId 		= 	0, ";
		$sql 	.=	"fkStatesId 		= 	0, ";
		$sql 	.=	"TournamentLocation = 	'', ";
		$sql 	.=	"Latitude 			= 	0, ";
		$sql 	.=	"Longitude 			= 	0, ";
	}
	$sql 	.=	" DateModified	= 	'".date('Y-m-d H:i:s')."'";
	$condition 	= "id = ".$insert_id;
	$tourManageObj->updateTournamentDetail($sql,$condition);

	//***********Start:  PIN Generation **********************
	if(isset($_POST['pin_chk_option'])	&&	trim($_POST['pin_chk_option'] != '')){
		$tourManageObj->generateTournamentPins($insert_id);
	}
	//***********End:  PIN Generation **********************

	if(isset($_POST['location'])	&&	trim($_POST['location'] == 1)){ //Location restricted tournament
		if(isset($_POST['locationedit']) && !empty($_POST['locationedit']) && count($_POST['locationedit']) > 0) {
			$resfields	=	" Status = 2 ";
			$upids		=	implode(',',$_POST['locationedit']);
			$resLocCon	=	" id not in (".$upids.") and fkTournamentsId='".$tournamentId."' and fkDevelopersId='".$devId."' ";
			$tourManageObj->updateRestrictedLocation($resfields,$resLocCon); //Change status for old entries

			for($loci=0;$loci<count($_POST['locationedit']);$loci++) {
				$query = "fkTournamentsId = '".$tournamentId."', fkDevelopersId = '".$devId."' ";
				if(isset($_POST['countryLocation'][$loci]))
					$query	.=	", fkCountriesId='".$_POST['countryLocation'][$loci]."'";
				if(isset($_POST['stateLocation'][$loci]))
					$query	.=	", fkStatesId='".$_POST['stateLocation'][$loci]."'";
				if(isset($_POST['locationsearch'][$loci]))
					$query	.=	", LocationValue='".$_POST['locationsearch'][$loci]."'";
				if(isset($_POST['latitude'][$loci]))
					$query	.=	", Latitude='".$_POST['latitude'][$loci]."'";
				if(isset($_POST['longitude'][$loci]))
					$query	.=	", Longitude='".$_POST['longitude'][$loci]."'";
				$con	=	" id=".$_POST['locationedit'][$loci];
				$tourManageObj->updateRestrictedLocation($query,$con);
			}
			$loccount	=	count($_POST['locationedit']);
			for($loci=$loccount;$loci<count($_POST['countryLocation']);$loci++) {
				$query = "fkTournamentsId = '".$tournamentId."', fkDevelopersId = '".$devId."' ";
				if(isset($_POST['countryLocation'][$loci]))
					$query	.=	", fkCountriesId='".$_POST['countryLocation'][$loci]."'";
				if(isset($_POST['stateLocation'][$loci]))
					$query	.=	", fkStatesId='".$_POST['stateLocation'][$loci]."'";
				if(isset($_POST['locationsearch'][$loci]))
					$query	.=	", LocationValue='".$_POST['locationsearch'][$loci]."'";
				if(isset($_POST['latitude'][$loci]))
					$query	.=	", Latitude='".$_POST['latitude'][$loci]."'";
				if(isset($_POST['longitude'][$loci]))
					$query	.=	", Longitude='".$_POST['longitude'][$loci]."'";
				$tourManageObj->insertRestrictedLocation($query);
			}

		} else {
			$resfields	=	" Status = 2 ";
			$resLocCon	=	" fkTournamentsId='".$tournamentId."' and fkDevelopersId='".$devId."' ";
			$tourManageObj->updateRestrictedLocation($resfields,$resLocCon);

			if(isset($_POST['countryLocation']) && isset($_POST['stateLocation']) && isset($_POST['locationsearch']) ) {
				$loccount	=	count($_POST['countryLocation']);
				if($loccount >  0 && count($_POST['stateLocation']) == $loccount  && count($_POST['locationsearch']) == $loccount ) {
					for($loci=0;$loci<$loccount;$loci++) {
						$query = "fkTournamentsId = '".$tournamentId."', fkDevelopersId = '".$devId."' ";
						if(isset($_POST['countryLocation'][$loci]))
							$query	.=	", fkCountriesId='".$_POST['countryLocation'][$loci]."'";
						if(isset($_POST['stateLocation'][$loci]))
							$query	.=	", fkStatesId='".$_POST['stateLocation'][$loci]."'";
						if(isset($_POST['locationsearch'][$loci]))
							$query	.=	", LocationValue='".$_POST['locationsearch'][$loci]."'";
						if(isset($_POST['latitude'][$loci]))
							$query	.=	", Latitude='".$_POST['latitude'][$loci]."'";
						if(isset($_POST['longitude'][$loci]))
							$query	.=	", Longitude='".$_POST['longitude'][$loci]."'";
						$tourManageObj->insertRestrictedLocation($query);
					}
				}
			}
		}
	}else {
		$resfields	=	" Status = 2 ";
		$resLocCon	=	" fkTournamentsId='".$tournamentId."' and fkDevelopersId='".$devId."' ";
		$tourManageObj->updateRestrictedLocation($resfields,$resLocCon);
	}

	//***********Start:  Custom Prize **********************
	if(isset($_POST['prize_type'])	&&	trim($_POST['prize_type'] == 4)){
		if(isset($_POST['prize_title'])	&& is_array($_POST['prize_title']) && isset($_POST['prize_title_id'])	&& is_array($_POST['prize_title_id']) ){
			$tempFileArray	=	$oldFileArray	=	$prizeDescArray	=	$titleArray	=	$entryIdArray = array();
			$titleArray		=	$_POST['prize_title'];
			$entryIdArray		=	$_POST['prize_title_id'];
			if(isset($_POST['prize_tempFile'])	&& is_array($_POST['prize_tempFile']))
				$tempFileArray	=	$_POST['prize_tempFile'];
			if(isset($_POST['old_prize_file'])	&& is_array($_POST['old_prize_file']))
				$oldFileArray	=	$_POST['old_prize_file'];
			if(isset($_POST['custom_prizeDes'])	&& is_array($_POST['custom_prizeDes']))
				$prizeDescArray	=	$_POST['custom_prizeDes'];
			//Unset old entries
			$updateString	= " Status = 2, DateModified='".$today."' ";
			$condition	=	" fkTournamentsId=".$tournamentId."  ";
			$tourManageObj->updateCustomPrizeDetails($updateString,$condition);
			$queryString	=	"";

			foreach($entryIdArray as $oldKey =>$oldId){
				if($oldId !=''){ //Update entries
					$updateString	=	$imageName =  $prizeDesc	=	$title = "";
					$fileName = '';
					if(isset($tempFileArray[$oldKey]) && $tempFileArray[$oldKey] !=''){
						$ext = pathinfo($tempFileArray[$oldKey], PATHINFO_EXTENSION);
						$uploadPath	=	UPLOAD_CUSTOM_PRIZE_PATH_REL;
						if ( !file_exists($uploadPath.$tournamentId) ){
							mkdir ($uploadPath.$tournamentId, 0777);
						}
						if ( !file_exists($uploadPath.$tournamentId.'/thumbnail') ){
							mkdir ($uploadPath.$tournamentId.'/thumbnail', 0777);
						}
						$fileName	=	'prize_'.$oldId.'_'.time().'.png';
						$imageName 	=	 $tournamentId.'/'.$fileName;
						$thumbName 	=	 $tournamentId.'/thumbnail/'.$fileName;
						$temp_image_path 			=	TEMP_IMAGE_PATH_REL . $tempFileArray[$oldKey];
						$image_path 				=	$uploadPath.$imageName;
						$imageThumbPath     		=	$uploadPath.$thumbName;
						$oldImage	= '';
						if(isset($oldFileArray[$oldKey])){
							$oldImage				=	$oldFileArray[$oldKey];
						}
						copy($temp_image_path,$image_path);
						$phMagick = new phMagick($image_path);
						$phMagick->setDestination($imageThumbPath)->resize(60,60);
						if (SERVER){
							if($oldImage!='') {
								if(image_exists(17,$tournamentId.'/'.$oldImage)) {
									deleteImages(17,$tournamentId.'/'.$oldImage);
								}
								if(image_exists(17,$tournamentId.'/thumbnail/'.$oldImage)) {
									deleteImages(17,$tournamentId.'/thumbnail/'.$oldImage);
								}
							}
							uploadImageToS3($image_path,17,$imageName); // image_path
							uploadImageToS3($imageThumbPath,17,$thumbName); // image_path
							unlink($imageThumbPath);
							unlink($image_path);
						}
						else if ( $oldImage !='' ){
							if( file_exists(CUSTOM_PRIZE_IMAGE_PATH_REL.$tournamentId.'/'.$oldImage) )
								unlink(CUSTOM_PRIZE_IMAGE_PATH_REL.$tournamentId.'/'.$oldImage);
							if( file_exists(CUSTOM_PRIZE_IMAGE_PATH_REL.$tournamentId.'/thumbnail/'.$oldImage) )
								unlink(CUSTOM_PRIZE_IMAGE_PATH_REL.$tournamentId.'/thumbnail/'.$oldImage);
						}
					}
					if(isset($titleArray[$oldKey]) && $titleArray[$oldKey] !=''){
						$title		=	$titleArray[$oldKey];
					}
					if(isset($prizeDescArray[$oldKey]) && $prizeDescArray[$oldKey] !=''){
						$prizeDesc		=	$prizeDescArray[$oldKey];
					}
					if($fileName !=''){
						$updateString	= " PrizeTitle='".$title."',PrizeDescription ='".$prizeDesc."',PrizeImage='".$fileName."',PrizeOrder='".($oldKey+1)."',Status= 1,DateModified='".$today."'";
					}
					else
						$updateString	= " PrizeTitle='".$title."',PrizeDescription ='".$prizeDesc."',PrizeOrder='".($oldKey+1)."',Status= 1,DateModified='".$today."' ";
					$condition	=	" id=".$oldId." ";

					//update Query
						$tourManageObj->updateCustomPrizeDetails($updateString,$condition);
				}
				else{ // new entry
					$updateString	=	$imageName = $fileName = $prizeDesc	=	$title =  "";

					$status	=	1;
					if(isset($titleArray[$oldKey]) && $titleArray[$oldKey] !='')			$title		=	$titleArray[$oldKey];
					if(isset($prizeDescArray[$oldKey]) && $prizeDescArray[$oldKey] !='')	$prizeDesc		=	$prizeDescArray[$oldKey];
					$queryString	=	"('0',".$devId.",".$tournamentId.",'".$title."','','".$prizeDesc."','".($oldKey+1)."',1,'".$today."','".$today."')";
					$queryString	=	' VALUES '.$queryString;
					$entryId		=	$tourManageObj->insertCustomPrize($queryString);
					if(isset($tempFileArray[$oldKey]) && $tempFileArray[$oldKey] !=''){
						$ext = pathinfo($tempFileArray[$oldKey], PATHINFO_EXTENSION);
						$uploadPath	=	UPLOAD_CUSTOM_PRIZE_PATH_REL;
						if ( !file_exists($uploadPath.$tournamentId) ){
							mkdir ($uploadPath.$tournamentId, 0777);
						}
						if ( !file_exists($uploadPath.$tournamentId.'/thumbnail') ){
							mkdir ($uploadPath.$tournamentId.'/thumbnail', 0777);
						}
						$fileName	=	'prize_' .$entryId.'_'.time().'.png';
						$imageName 	=	 $tournamentId.'/'.$fileName;
						$thumbName 	=	 $tournamentId.'/thumbnail/'.$fileName;
						$temp_image_path 			=	TEMP_IMAGE_PATH_REL . $tempFileArray[$oldKey];
						$image_path 				=	$uploadPath.$imageName;
						$imageThumbPath     		=	$uploadPath.$thumbName;
						copy($temp_image_path,$image_path);

						$phMagick = new phMagick($image_path);
						$phMagick->setDestination($imageThumbPath)->resize(60,60);
						if (SERVER){
							uploadImageToS3($image_path,17,$imageName); // image_path
							uploadImageToS3($imageThumbPath,17,$thumbName); // image_path

							unlink($image_path);
							unlink($imageThumbPath);
						}
					}
					if($fileName !=''){
						$updateString	= " PrizeImage='".$fileName."' ";
						$condition	=	" id=".$entryId." ";
						//update image details
						$tourManageObj->updateCustomPrizeDetails($updateString,$condition);
					}
				}
			}
		}
	}
	//***********End:  Custom Prize **********************

	// mgc AD list
	// if(isset($_POST['mgc_ad_tempFile'])	&& is_array($_POST['mgc_ad_tempFile'])){
	// 	$tempFileArray	=	$_POST['mgc_ad_tempFile'];
	// }

	if(isset($_POST['mgc_ad_title']) && is_array($_POST['mgc_ad_title'])){
		$sql = "DELETE FROM tournamentcustomad WHERE fkTournamentsId=".$tournamentId;
	  $tourManageObj->updateCustomAdDetails($sql);
		// always clear old rows and insert new ones.
		$ii = 0;
		foreach($_POST['mgc_ad_title'] as $mgcadK => $mgcadV ){
			// upload files to s3
			if(strlen($mgcadV)>0){
				$adImageOrVideoFile = $_POST["custom_mgc_adImage".$ii."_upload"];
				// echo 'adImgOrVid::'.$adImageOrVideoFile.'<br>';

				$ext = pathinfo($adImageOrVideoFile, PATHINFO_EXTENSION);
				// echo 'ext::'.$ext.'<br>';
				$fileName 	=	 'mgcAd_'.$ii.'_'.time().'.'.$ext;
				$imageName 	=	 $tournamentId.'/'.$fileName;
				// echo 'imageName::'.$imageName.'<br>';
				$image_path =	UPLOAD_CUSTOM_PRIZE_PATH_REL.$imageName;
				// echo 'image_path::'.$image_path.'<br>';
				$temp_image_path = TEMP_IMAGE_PATH_REL . $_POST["custom_mgc_adImage".$ii."_upload"];
				uploadImageToS3($temp_image_path,17,$imageName);

				// $sql = "INSERT INTO tournamentcustomad (fkTournamentsId, AdCaption, AdImageOrVideoFile)
				// 	VALUES (-777, 'Caught exception:', '".$e->getMessage()."')";
				// $tourManageObj->updateCustomAdDetails($sql);

				$sql = "INSERT INTO tournamentcustomad (fkTournamentsId, AdCaption, AdImageOrVideoFile)
				  VALUES ($tournamentId, '$mgcadV', '".$fileName."')";
				$tourManageObj->updateCustomAdDetails($sql);
			}

			$ii++;
		}
	}
	// START MGC background images
	if(isset($_POST['mgc_backgroundimage_delay']) && is_array($_POST['mgc_backgroundimage_delay'])){
		$sql = "DELETE FROM tournamentbackgroundimage WHERE fkTournamentsId=".$tournamentId;
		$tourManageObj->updateCustomAdDetails($sql);
		// $tourManageObj->updateCustomAdDetails("INSERT INTO tournamentbackgroundimage (fkTournamentsId, delaytonextimage, backgroundimage) VALUES (9990011, '9', 'ver 9-before loop')");
		// $tourManageObj->updateCustomAdDetails("INSERT INTO tournamentbackgroundimage (fkTournamentsId, delaytonextimage, backgroundimage) VALUES (9990011, '9', 'ver 8-array::".json_encode($_POST['mgc_backgroundimage_delay'], true)."')");
		// always clear old rows and insert new ones.
		$ii = 0;
		foreach($_POST['mgc_backgroundimage_delay'] as $mgcadV ){
			// // upload files to s3
			if(strlen($mgcadV)>0){
				// $tourManageObj->updateCustomAdDetails("INSERT INTO tournamentbackgroundimage (fkTournamentsId, delaytonextimage, backgroundimage) VALUES (9990011, '9', 'mgcadV::".$mgcadV."')");
				$adImageOrVideoFile = $_POST["custom_mgc_backgroundImage".$ii."_upload"];
				// echo 'adImgOrVid::'.$adImageOrVideoFile.'<br>';
				$ext = pathinfo($adImageOrVideoFile, PATHINFO_EXTENSION);
				// echo 'ext::'.$ext.'<br>';
				$fileName 	=	 'mgcBackgroundImage_'.$ii.'_'.time().'.'.$ext;
				$imageName 	=	 $tournamentId.'/'.$fileName;
				// echo 'imageName::'.$imageName.'<br>';
				$image_path =	UPLOAD_CUSTOM_PRIZE_PATH_REL.$imageName;
				// echo 'image_path::'.$image_path.'<br>';
				$temp_image_path = TEMP_IMAGE_PATH_REL . $_POST["custom_mgc_backgroundImage".$ii."_upload"];
				// $tourManageObj->updateCustomAdDetails("INSERT INTO tournamentbackgroundimage (fkTournamentsId, delaytonextimage, backgroundimage) VALUES (9990011, '2', 'mgcadV::$mgcadV')");
				// $tourManageObj->updateCustomAdDetails("INSERT INTO tournamentbackgroundimage (fkTournamentsId, delaytonextimage, backgroundimage) VALUES (9990011, '10', 'temp_image_path::$temp_image_path')");
				// $tourManageObj->updateCustomAdDetails("INSERT INTO tournamentbackgroundimage (fkTournamentsId, delaytonextimage, backgroundimage) VALUES (9990011, '20', 'imageName::$imageName')");
				uploadImageToS3($temp_image_path,17,$imageName);
			 	// $sql = "INSERT INTO tournamentbackgroundimage (fkTournamentsId, delaytonextimage, backgroundimage)
			 	// 	VALUES (-777, 'Caught exception:', '".$e->getMessage()."')";
			 	// $tourManageObj->updateCustomAdDetails($sql);
			 	$sql = "INSERT INTO tournamentbackgroundimage (fkTournamentsId, delaytonextimage, backgroundimage) VALUES ($tournamentId, '$mgcadV', '".$fileName."')";
			 	$tourManageObj->updateCustomAdDetails($sql);
				$ii++;
			}
		}
	}
	// END MGC background images


	// BEGIN: Developer Tilt$/Virtual Coin update
	if(isset($_POST['prize_type'])	&&	trim($_POST['prize_type'] == 2)){ //tilt
		$condition	=	' id = '.$_SESSION['tilt_developer_id'];
		$amount	=	$coin	=	'';
		$update_string		=	'';
		$tiltFee = 0;
			if(isset($_POST['tilt_default_fee']) && $_POST['tilt_default_fee'] !='')
				$tiltFee	=	$_POST['tilt_default_fee'];
		if(isset($_POST['titl_coin_flag']) && $_POST['titl_coin_flag'] == 2){// Last Type Tilt
			if((($_SESSION['tilt_developer_amount']+$_POST['old_prize_coin'])-$_POST['prize_coin']) >=0){
				$update_string		=	' Amount = (Amount + '.$_POST['old_prize_coin'].') - '.$_POST['prize_coin'];
				$_SESSION['tilt_developer_amount']	=	($_SESSION['tilt_developer_amount']+$_POST['old_prize_coin']) - $_POST['prize_coin'];
				$condition	.=	" AND (Amount + ".$_POST['old_prize_coin'].") >= ".$_POST['prize_coin']." ";
			}
		}
		else if(isset($_POST['titl_coin_flag']) && $_POST['titl_coin_flag'] == 3){ // Last Type Virtual
			if(($_SESSION['tilt_developer_amount'] - ($tiltFee.'+'.$_POST['prize_coin'])) >=0){
				$update_string		=	' Amount = (Amount  - ('.$_POST['prize_coin'].'+'.$tiltFee.')),VirtualCoins = (VirtualCoins+'.$_POST['old_prize_vcoin'].') ';
				$condition	.=	" AND (Amount + ".$_POST['old_prize_coin']."+".$tiltFee.") >= ".$_POST['prize_coin']." ";
				$_SESSION['tilt_developer_amount']	=	($_SESSION['tilt_developer_amount']) - ($_POST['prize_coin']+$tiltFee);
				$_SESSION['tilt_developer_coins']	=	($_SESSION['tilt_developer_coins']) + $_POST['old_prize_vcoin'];
			}
		}
		else{ // Last Type Custom or New tournament
			if(($_SESSION['tilt_developer_amount'] - ($tiltFee.'+'.$_POST['prize_coin'])) >=0){
				$update_string		=	' Amount = Amount - ('.$_POST['prize_coin'].'+'.$tiltFee.')';
				$condition	=	' id = '.$_SESSION['tilt_developer_id'].' AND Amount >=('.$_POST['prize_coin'].'+'.$tiltFee.') ';
				$_SESSION['tilt_developer_amount']	=	($_SESSION['tilt_developer_amount']) - ($_POST['prize_coin']+$tiltFee);
			}
			if(isset($_POST['titl_coin_flag']) && $_POST['titl_coin_flag'] == 4){
				$updateString1	= " Status = 2, DateModified='".$today."' ";
				$condition1	=	" fkTournamentsId=".$tournamentId."  ";
				$tourManageObj->updateCustomPrizeDetails($updateString1,$condition1);
			}
		}
		if(!empty($update_string)){
			$gameDevObj->updateGameDevDetails($update_string,$condition);
		}
	}
	else if(isset($_POST['prize_type'])	&&	trim($_POST['prize_type'] == 3)){ //virtual
		$condition			=	' id = '.$_SESSION['tilt_developer_id'];

		if(isset($_POST['titl_coin_flag']) && $_POST['titl_coin_flag'] == 3){
			if((($_SESSION['tilt_developer_coins']+$_POST['old_prize_vcoin']) - $_POST['prize_coin']) >= 0){
				$update_string		=	' VirtualCoins = (VirtualCoins + '.$_POST['old_prize_vcoin'].') - '.$_POST['prize_coin'];
				$_SESSION['tilt_developer_coins']	=	($_SESSION['tilt_developer_coins']+$_POST['old_prize_vcoin']) - $_POST['prize_coin'];
				$condition	.=	" AND (VirtualCoins + ".$_POST['old_prize_vcoin'].") >= ".$_POST['prize_coin']." ";
			}
		}
		else if(isset($_POST['titl_coin_flag']) && $_POST['titl_coin_flag'] == 2){
			if((($_SESSION['tilt_developer_coins']) - $_POST['prize_coin']) >=0){
				$update_string		=	' VirtualCoins = (VirtualCoins - '.$_POST['prize_coin'].'),Amount = (Amount +'.$_POST['old_prize_coin'].')';
				$_SESSION['tilt_developer_coins']	=	($_SESSION['tilt_developer_coins']) - $_POST['prize_coin'];
				$_SESSION['tilt_developer_amount']	=	($_SESSION['tilt_developer_amount']) + $_POST['old_prize_coin'];
				$condition	.=	" AND (VirtualCoins) >= ".$_POST['prize_coin']." ";
			}
		}
		else {
			if((($_SESSION['tilt_developer_coins']) - $_POST['prize_coin'])>=0){
				$update_string		=	' VirtualCoins = VirtualCoins - '.$_POST['prize_coin'];
				$condition	=	' id = '.$_SESSION['tilt_developer_id'].' AND VirtualCoins >='.$_POST['prize_coin'].' ';
				$_SESSION['tilt_developer_coins']	=	($_SESSION['tilt_developer_coins']) - $_POST['prize_coin'];
			}
			if(isset($_POST['titl_coin_flag']) && $_POST['titl_coin_flag'] == 4){
				$updateString1	= " Status = 2, DateModified='".$today."' ";
				$condition1	=	" fkTournamentsId=".$tournamentId."  ";
				$tourManageObj->updateCustomPrizeDetails($updateString1,$condition1);
			}
		}
		$gameDevObj->updateGameDevDetails($update_string,$condition);
	}
	else if(isset($_POST['prize_type'])	&&	trim($_POST['prize_type'] == 4)){ //Custom prize
		// Re-assign tilt$/coin to developer
		$condition			=	' id = '.$_SESSION['tilt_developer_id'];
		$amount	=	$coin	=	$update_string		=	'';
		if($_POST['titl_coin_flag'] == 2){
			if(isset($_POST['old_prize_coin']) && $_POST['old_prize_coin'] !='') {
				$update_string		=	' Amount = (Amount + '.$_POST['old_prize_coin'].')';
				$_SESSION['tilt_developer_amount']	=	$_SESSION['tilt_developer_amount']+$_POST['old_prize_coin'];
			}
		}
		elseif($_POST['titl_coin_flag'] == 3){
			if(isset($_POST['old_prize_vcoin']) && $_POST['old_prize_vcoin'] !='') {
				$update_string		=	' VirtualCoins = (VirtualCoins + '.$_POST['old_prize_vcoin'].')';
				$_SESSION['tilt_developer_coins']	=	($_SESSION['tilt_developer_coins']+$_POST['old_prize_vcoin']);
			}
		}
		if(!empty($update_string))
			$gameDevObj->updateGameDevDetails($update_string,$condition);
	}
	// END: Developer Tilt$/Virtual Coin update

	//update OR insert coupon,banner and youtube
	if(isset($insert_id) &&	$insert_id!=''){
		//Start: Insert/update Coupon
		if(( isset($_POST['coupon_limit'])	&& trim($_POST['coupon_limit'])!= "" ) || (isset($_POST['coupon_code'])	&& trim($_POST['coupon_code'])!= "")){
			$couponLimit	=	$_POST['coupon_limit'];
			$title      	=  	$_POST['coupon_code'];
			$coupon_startdate = $coupon_enddate = $couponDesc =  '';
			if(isset($_POST['coupon_startdate'])	&&	trim($_POST['coupon_startdate'])!=""){
				$coupon_startdate = date('Y-m-d H:i',strtotime($_POST['coupon_startdate'])) ;
			}
			if(isset($_POST['coupon_enddate'])	&&	trim($_POST['coupon_enddate'])!=""){
				$coupon_enddate = date('Y-m-d H:i',strtotime($_POST['coupon_enddate'])) ;
			}
			if(isset($_POST['coupon_text'])	&& trim($_POST['coupon_text'])!= "")
				$couponDesc =  $_POST['coupon_text'];
			if(isset($_POST['coupon_code_id'])	&&	trim($_POST['coupon_code_id']!="")){// Old Entry
				$fileName = '';
				$oldId      = $_POST['coupon_code_id'];
				$oldCoupon	= $_POST['name_coupon_image'];
				if(isset($_POST['coupon_image_upload']) && $_POST['coupon_image_upload'] != ''){ //update

					$temp_image_path  = TEMP_IMAGE_PATH_REL.$_POST['coupon_image_upload'];
					$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
					$uploadPath	=	UPLOAD_COUPON_PATH_REL;
					$fileName	=	'coupon_' .$oldId.'_'.time().'.'.$ext;
					$imageName 	=	 $insert_id.'/'.$fileName;
					$image_path =	$uploadPath.$imageName;
					if ( !file_exists($uploadPath.$insert_id) ){
						mkdir ($uploadPath.$insert_id, 0777);
					}
					copy($temp_image_path,$image_path);

					if (SERVER){
						if($oldCoupon!='') {
							if(image_exists(11,$insert_id.'/'.$oldCoupon)) {
								deleteImages(11,$insert_id.'/'.$oldCoupon);
							}
						}
						uploadImageToS3($image_path,11,$imageName); // image_path
						unlink($image_path);
					}
					else if ( $oldCoupon !='' && file_exists(COUPON_IMAGE_PATH_REL.$insert_id.'/'.$oldCoupon) ){
						unlink(COUPON_IMAGE_PATH_REL.$insert_id.'/'.$oldCoupon);
					}
				}

				$updateString	= " CouponAdLink ='".$couponDesc."',CouponTitle='".$title."',CouponStartDate='".$coupon_startdate."',CouponEndDate='".$coupon_enddate."',DateModified='".$today."',CouponLimit = ".$couponLimit." ";

				if($fileName !='')
					$updateString	.= ", File='".$fileName."' ";
				$condition	=	" id=".$oldId." ";
				$tourManageObj->updateCouponBannerLink($updateString,$condition);
			}else { //insert
				$queryString  =	$fileName  =	"";
				$queryString	=	"(".$tournamentId.",0,'".$couponDesc."','','','".$title."','".$coupon_startdate."','".$coupon_enddate."',1,1,1,'".$today."','".$today."',".$couponLimit.")";
				$queryString	=	' VALUES '.$queryString;
				$entryId	    =   $tourManageObj->insertCouponBannerLink($queryString);
				$temp_image_path  = TEMP_IMAGE_PATH_REL . $_POST['coupon_image_upload'];

				if(isset($_POST['coupon_image_upload']) && $_POST['coupon_image_upload'] !=''){
					$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
					$uploadPath	=	UPLOAD_COUPON_PATH_REL;
					if ( !file_exists($uploadPath.$insert_id) ){
						mkdir ($uploadPath.$insert_id, 0777);
					}
					$fileName	=	'coupon_' .$entryId.'_'.time().'.'.$ext;
					$imageName 	=	 $insert_id.'/'.$fileName;
					$temp_image_path 	=	TEMP_IMAGE_PATH_REL . $_POST['coupon_image_upload'];
					$image_path 		=	$uploadPath.$imageName;
					copy($temp_image_path,$image_path);
					if (SERVER){
						uploadImageToS3($image_path,11,$imageName); // image_path
						unlink($image_path);
					}
				}

				if($fileName !=''){
					$updateString	= " File='".$fileName."' ";
					$condition	=	" id=".$entryId." ";
					$tourManageObj->updateCouponBannerLink($updateString,$condition);
				}
			}
		}
		//End: Insert/update Coupon

		//Start: Insert/update Youtube
		if((isset($_POST['youtube_type']) && $_POST['youtube_type'] != '') && (isset($_POST['youtube_link'])&& trim($_POST['youtube_link'])!="" || (isset($_POST['youtube_image_upload']) && trim($_POST['youtube_image_upload']) !=""))){
			if(isset($_POST['youtube_link_id'])	&&	trim($_POST['youtube_link_id']!="")){
				$fileName = $oldId = $url = $code = $inputType = '';
				$oldId = $_POST['youtube_link_id'];

				if(isset($_POST['youtube_image_upload']) && $_POST['youtube_image_upload'] != ''){
					$oldLink = $_POST['name_youtube_image'];
					$temp_image_path  = TEMP_IMAGE_PATH_REL.$_POST['youtube_image_upload'];
					$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
					$uploadPath	=	UPLOAD_YOUTUBE_LINK_PATH_REL;
					if ( !file_exists($uploadPath.$insert_id) ){
						mkdir ($uploadPath.$insert_id, 0777);
					}
					$fileName	=	'link_'.$oldId.'_'.time().'.'.$ext;
					$imageName 	=	 $insert_id.'/'.$fileName;
					$temp_image_path 	=	TEMP_IMAGE_PATH_REL . $_POST['youtube_image_upload'];
					$image_path 		=	$uploadPath.$imageName;

					copy($temp_image_path,$image_path);
					if (SERVER){
						if($oldLink!='') {
							if(image_exists(14,$insert_id.'/'.$oldLink)) {
								deleteImages(14,$insert_id.'/'.$oldLink);
							}
						}
						uploadImageToS3($image_path,14,$imageName); // image_path
						unlink($image_path);
					}
					else if ( $oldLink !='' && file_exists(YOUTUBE_LINK_IMAGE_PATH_REL.$insert_id.'/'.$oldLink) ){
						unlink(YOUTUBE_LINK_IMAGE_PATH_REL.$insert_id.'/'.$oldLink);
					}
				}

				 $inputType = (isset($_POST['youtube_type']))? $_POST['youtube_type']:'';
				 if($inputType == 1)
					$url       = (isset($_POST['youtube_link']))?$_POST['youtube_link']:'';
				 else
					 $code      = (isset($_POST['youtube_code']))? $_POST['youtube_code']:'';

				if($fileName == '')
					$updateString	= " URL = '".$url."',CouponAdLink='".$code."',InputType=".$inputType.",DateModified='".$today."' ";
				else
					$updateString	= " URL = '".$url."',CouponAdLink='".$code."',File='".$fileName."',InputType=".$inputType.",DateModified='".$today."' ";

				$condition	=	" id=".$oldId." ";
				$tourManageObj->updateCouponBannerLink($updateString,$condition);

			} else {
				$queryString  =	$fileName  = $youtube_url = $youtube_code =  "";
				if($_POST['youtube_type'] == 1){
					$youtube_url  = (isset($_POST['youtube_link']))?$_POST['youtube_link']:'';
				}else
					$youtube_code = (isset($_POST['youtube_code']))?$_POST['youtube_code']:'';

				$queryString	=	"(".$tournamentId.",0,'".$youtube_code."','".$youtube_url."','','','','',3,'".$_POST['youtube_type']."',1,'".$today."','".$today."',0)";
				$queryString	=	' VALUES '.$queryString;
				$entryId	    =   $tourManageObj->insertCouponBannerLink($queryString);
				$temp_image_path  = TEMP_IMAGE_PATH_REL.$_POST['youtube_image_upload'];
				$fileName = '';
				if(isset($_POST['youtube_image_upload']) && $_POST['youtube_image_upload'] !=''){

					$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
					$uploadPath	= UPLOAD_YOUTUBE_LINK_PATH_REL;
					if ( !file_exists($uploadPath.$insert_id) ){
						mkdir ($uploadPath.$insert_id, 0777);
					}
					$fileName	=	'link_'.$entryId.'_'.time().'.'.$ext;
					$imageName 	=	 $insert_id.'/'.$fileName;
					$temp_image_path 	=	TEMP_IMAGE_PATH_REL.$_POST['youtube_image_upload'];
					$image_path 		=	$uploadPath.$imageName;
					copy($temp_image_path,$image_path);
					if (SERVER){
						uploadImageToS3($image_path,14,$imageName); // image_path
						unlink($image_path);
					}
				}
				if($fileName !=''){
					$updateString	= " File='".$fileName."' ";
					$condition	=	" id=".$entryId." ";
					$tourManageObj->updateCouponBannerLink($updateString,$condition);
				}
			}
		}else if(isset($_POST['youtube_link_id'])	&&	trim($_POST['youtube_link_id']!="")){
				$oldId = $_POST['youtube_link_id'];
				$updateString	= " URL = '',CouponAdLink='',File='',InputType='0',DateModified='".$today."' ";
				$condition	=	" id=".$oldId." ";
				$tourManageObj->updateCouponBannerLink($updateString,$condition);
		}
		//End: Insert/update Youtube

		//Start: Insert/update Banner
		if((isset($_POST['banner_type']) && $_POST['banner_type'] != '') && (isset($_POST['banner_text'])&& trim($_POST['banner_text'])!=""	|| (isset($_POST['banner_image_upload']) && trim($_POST['banner_image_upload']) !="") || (isset($_POST['banner_link']) && trim($_POST['banner_link']) !=""))){
			if(isset($_POST['banner_image_id'])	&&	trim($_POST['banner_image_id']!="")){
				$updateString	=	$imageName = $fileName = $prizeDesc	=	$bannerText	=	$bannerLink	=	"";
				$inputType  = $_POST['banner_type'];
				$oldId      = $_POST['banner_image_id'] ;
				if($inputType  == 1){
					$bannerText = (isset($_POST['banner_text']))? $_POST['banner_text'] : '';
				}else
					$bannerLink = (isset($_POST['banner_link']))? $_POST['banner_link'] : '';

				if($inputType == 2){

					if(isset($_POST['banner_image_upload']) && $_POST['banner_image_upload'] != ''){

						$oldBanner = $_POST['name_banner_image'];
						$temp_image_path  = TEMP_IMAGE_PATH_REL.$_POST['banner_image_upload'];
						$ext              = pathinfo($temp_image_path,PATHINFO_EXTENSION);
						$uploadPath	=	UPLOAD_BANNER_PATH_REL;
							if ( !file_exists($uploadPath.$insert_id) ){
								mkdir ($uploadPath.$insert_id, 0777);
							}
						$fileName	=	'banner_'.$oldId.'_'.time().'.'.$ext;
						$imageName 	=	 $insert_id.'/'.$fileName;
						$image_path =	$uploadPath.$imageName;

						copy($temp_image_path,$image_path);

							if(SERVER){
								if($oldBanner!='') {
									if(image_exists(12,$insert_id.'/'.$oldBanner)){
										deleteImages(12,$insert_id.'/'.$oldBanner);
									}
								}
								uploadImageToS3($image_path,12,$imageName); // image_path
								unlink($image_path);
							}
							else if ( $oldBanner!='' && file_exists(BANNER_IMAGE_PATH_REL.$insert_id.'/'.$oldBanner) ){
								unlink(BANNER_IMAGE_PATH_REL.$insert_id.'/'.$oldBanner);
							}
					}
				}

				if($inputType == 2 && $fileName !=''){
					$updateString	= " CouponAdLink ='',URL='".$bannerLink."',File='".$fileName."',InputType=".$inputType.",DateModified='".$today."' ";
				}
				else
					$updateString	= " CouponAdLink ='".$bannerText."',URL='".$bannerLink."',InputType=".$inputType.",DateModified='".$today."' ";
				$condition	=	" id=".$oldId." ";
				//update Query
				$tourManageObj->updateCouponBannerLink($updateString,$condition);
			} else {
				$bannerText = $bannerLink = '';

				if($_POST['banner_type'] == 1){
					$bannerText = (isset($_POST['banner_text']))?$_POST['banner_text']:'';
				}else{
					$bannerLink = (isset($_POST['banner_link']))?$_POST['banner_link']:'';
				}

				$queryString	=	" VALUES(".$insert_id.",0,'".$bannerText."','".$bannerLink."','','','','',2,'".$_POST['banner_type']."',1,'".$today."','".$today."',0)";
				$entryId		=	$tourManageObj->insertCouponBannerLink($queryString);

				if (isset($_POST['banner_image_upload']) && !empty($_POST['banner_image_upload'])) {
					$temp_image_path 	= TEMP_IMAGE_PATH_REL.$_POST['banner_image_upload'];
					$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
					$uploadPath	=	UPLOAD_BANNER_PATH_REL;
					if ( !file_exists($uploadPath.$insert_id) ){
						mkdir ($uploadPath.$insert_id, 0777);
					}
					$fileName	=	'banner_' .$entryId.'_'.time().'.'.$ext;
					$imageName 	=	 $insert_id.'/'.$fileName;
					$image_path 		=	$uploadPath.$imageName;
					copy($temp_image_path,$image_path);
					if (SERVER){
						uploadImageToS3($image_path,12,$imageName); // image_path
						unlink($image_path);
					 }

					 if($fileName !=''){
						$updateString	= " File='".$fileName."' ";
						$condition	=	" id=".$entryId." ";
						//update image details
						$tourManageObj->updateCouponBannerLink($updateString,$condition);
					}
				}
			}
		}else if(isset($_POST['banner_image_id'])	&&	trim($_POST['banner_image_id']!="")){
				$oldId = $_POST['banner_image_id'];
				$updateString	= " URL = '',CouponAdLink='',File='',InputType='0',DateModified='".$today."' ";
				$condition	=	" id=".$oldId." ";
				$tourManageObj->updateCouponBannerLink($updateString,$condition);
		}
		//End: Insert/update Banner
	}
	generatePDF($devId,$tournamentId);	//Generate PDF
	$_SESSION['tour_notification_msg_code'] = 4;
	header("location:TournamentManage?editId=".$insert_id);
	die;
}

if(isset($_POST['tournamentApp']) && $_POST['tournamentApp'] != '' && isset($_POST['edit_id'])	&&	$_POST['edit_id'] > 0){
	$sql = '';
	$tournamentId	=	$_POST['edit_id'];
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);

	if(isset($_POST['turns']) && $_POST['turns'] > 0)
		$sql	.=	"TotalTurns			=	'".$_POST['turns']."',";
	if(isset($_POST['max_player'])	&&	trim($_POST['max_player']!=""))
		$sql	.=	"MaxPlayers			=	'".$_POST['max_player']."',";
	if(isset($_POST['gen_terms_condition']))
		$sql	.=	" TermsAndCondition =	'".$_POST['gen_terms_condition']."', ";
	if(isset($_POST['gen_game_rules']))
		$sql	.=	" TournamentRule =	'".$_POST['gen_game_rules']."', ";
	if(isset($_POST['gen_tournmentrules']))
		$sql	.=	" GftRules =	'".$_POST['gen_tournmentrules']."', ";
	if(isset($_POST['gen_privacy_policy_tab']))
		$sql	.=	" PrivacyPolicy =	'".$_POST['gen_privacy_policy_tab']."', ";

	$sql 	    .=	"DateModified	= 	'".date('Y-m-d H:i:s')."'";
	$condition 	 = "id = ".$tournamentId;
	$tourManageObj->updateTournamentDetail($sql,$condition);

	//BEGIN: Rules integration
	$unsetStates =	$unsetCountries	=	'';
	if(isset($_POST['rule_country'])	&&	is_array($_POST['rule_country'])	&& count($_POST['rule_country']) > 0 ){
			$countryIds	=	$_POST['rule_country'];
			//change all entry status to in active
			$updateString	=	" Status=2 ,DateModified='".$today."' ";
			$condition	=	" fkTournamentsId=".$tournamentId." ";
			$tourManageObj->updateTournamentRules($updateString,$condition);

			$stateFlag	=	0;
			$countryEntry	=	$usEntryPair	=	$usStateIds	=	$otherCountryIds	=	'';
			$countryEntries	= $usEntries = $existStateArrays = $existArrays = $unsetCountryArray = $unsetStateArray = $tempArray = array();

			if(isset($_POST['rule_state'])	&&	is_array($_POST['rule_state'])	&& count($_POST['rule_state']) > 0){
				$stateFlag	=	1;
				$stateIds	=	$_POST['rule_state'];
			}
			if(isset($_POST['tournamentConditions']) &&	is_array($_POST['tournamentConditions']) && count($_POST['tournamentConditions']) > 0)
				$termsConditions	=	$_POST['tournamentConditions'];
			if(isset($_POST['tournamentRules']) && is_array($_POST['tournamentRules']) && count($_POST['tournamentRules']) > 0)
				$rulesList	=	$_POST['tournamentRules'];
			if(isset($_POST['thirdtournamentRules']) &&	is_array($_POST['thirdtournamentRules']) && count($_POST['thirdtournamentRules']) > 0)
				$thirdtournament	=	$_POST['thirdtournamentRules'];
			if(isset($_POST['privacy_policy_arr']) && is_array($_POST['privacy_policy_arr']) && count($_POST['privacy_policy_arr']) > 0)
				$privacyPolicyArr	=	$_POST['privacy_policy_arr'];

			$countryState	=	array();
			$tempStateArr	=	$tempCountryArr	=	array();
			foreach($countryIds as $key =>$id){
				if($id !=''){
					$tempStateId	=	'';
					if($id==$usId){
						if($stateFlag && isset($stateIds[$key])	&&	$stateIds[$key] !=''){
							if(!in_array($stateIds[$key],$tempStateArr)){
								$tempStateArr[]	=	$stateIds[$key];
								$tour_rule	=	$tour_condition	=	$thirdtour_rule = $privacyPolicySingle = '';
								$tempStateId	=	$stateIds[$key];
								if(isset($rulesList[$key]))
									$tour_rule	=	$rulesList[$key];
								if(isset($termsConditions[$key]))
									$tour_condition	=	$termsConditions[$key];
								if(isset($thirdtournament[$key]))
									$thirdtour_rule	 =	$thirdtournament[$key];
								if(isset($privacyPolicyArr[$key]))
									$privacyPolicySingle	 =	$privacyPolicyArr[$key];
								$usEntries[$stateIds[$key]]	=	array('stateId'=>$stateIds[$key],'rule'=>$tour_rule,'condition'=>$tour_condition,'thirdtournametrule'=>$thirdtour_rule,'privacyPolicySingle'=>$privacyPolicySingle);
								$usEntryPair		.=	$stateIds[$key].',';
								$countryState[]	=	array($id,$tempStateId);
							}
						}
					}
					else {
						if(!in_array($id,$tempCountryArr)){
							$tempCountryArr[]	=	$id;
							$countryEntry	.=	$id.',';
							$tour_rule		=	$tour_condition			=	'';
							$thirdtour_rule	=	$privacyPolicySingle	=	'';
							if(isset($rulesList[$key]))
								$tour_rule	     =	$rulesList[$key];
							if(isset($termsConditions[$key]))
								$tour_condition	 =	$termsConditions[$key];
							if(isset($thirdtournament[$key]))
								$thirdtour_rule	 =	$thirdtournament[$key];
							if(isset($privacyPolicyArr[$key]))
									$privacyPolicySingle	 =	$privacyPolicyArr[$key];
							$countryEntries[$id] =	array('rule'=>$tour_rule,'condition'=>$tour_condition,'thirdtournametrule'=>$thirdtour_rule,'privacyPolicySingle'=>$privacyPolicySingle);
							$countryState[]	=	array($id,$tempStateId);
						}
					}
				}
			}

			$countryEntry	=	rtrim($countryEntry,',');
			if($countryEntry){
				$fields			=	'*';
				 $condition		=	'fkCountriesId IN ('.$countryEntry.') AND fkTournamentsId ='.$tournamentId.' ';	// added 20140827
				$countryEntryResult		=	$tourManageObj->checkRulesEntry($fields,$condition);
				if(isset($countryEntryResult) && is_array($countryEntryResult) && count($countryEntryResult) > 0){
					foreach($countryEntryResult as $existKey => $existCountry){
						$existArrays[]	=	$existCountry->fkCountriesId;
					}
				}
			}
			$usEntryPair	=	rtrim($usEntryPair,',');
			if($usEntryPair !=''){
				$fields			=	'*';
				 $condition		=	'fkStatesId IN ('.$usEntryPair.') AND fkTournamentsId ='.$tournamentId.' AND fkCountriesId = '.$usId.' '; // added 20140827
				$usEntryResult		=	$tourManageObj->checkRulesEntry($fields,$condition);
				if(isset($usEntryResult) && is_array($usEntryResult) && count($usEntryResult) > 0){
					foreach($usEntryResult as $existStateKey => $existState){
						$existStateArrays[]	=	$existState->fkStatesId;
					}
				}
			}
			$stateUpdateArray	=	array();
			if(isset($countryState)	&&	is_array($countryState)	&& count($countryState) >0 ){
				foreach($countryState as $key =>$entry){
					$stateValues		= 	$countryValues	=	'';
					if($entry[0] !=''){
						if($entry[0]==$usId){
							if(in_array($entry[1],$existStateArrays)	&&	isset($usEntries[$entry[1]])){	//update state rules
								$updateString	=	" TournamentRules='".$usEntries[$entry[1]]['rule']."',TermsAndConditions='".$usEntries[$entry[1]]['condition']."',GftRules='".$usEntries[$entry[1]]['thirdtournametrule']."',PrivacyPolicy='".$usEntries[$entry[1]]['privacyPolicySingle']."',DateModified='".$today."',Status=1 ";
								$condition	    =	" fkTournamentsId=".$tournamentId."  AND fkCountriesId=".$entry[0]." AND fkStatesId=".$usEntries[$entry[1]]['stateId']." ";
								$tourManageObj->updateTournamentRules($updateString,$condition);
							}
							else{	// new entry to state
								if(isset($usEntries[$entry[1]])){
									$stateValues		.=	"(".$tournamentId.",0,".$entry[0].",".$usEntries[$entry[1]]['stateId'].",'".$usEntries[$entry[1]]['rule']."','".$usEntries[$entry[1]]['condition']."','".$usEntries[$entry[1]]['thirdtournametrule']."','".$usEntries[$entry[1]]['privacyPolicySingle']."','".$today."','".$today."',1),";
									$stateValues	=	' VALUES '.rtrim($stateValues,',');
									$tourManageObj->insertRules($stateValues);
								}
							}
						}
						else {
						$id	=	$entry[0];
							if(in_array($id,$existArrays)	&&	isset($countryEntries[$id])){	//update country rules
								$updateString	=	" TournamentRules='".$countryEntries[$id]['rule']."',TermsAndConditions='".$countryEntries[$id]['condition']."',GftRules='".$countryEntries[$id]['thirdtournametrule']."',PrivacyPolicy='".$countryEntries[$id]['privacyPolicySingle']."',DateModified='".$today."',Status=1 ";
								$condition	    =	" fkTournamentsId=".$tournamentId."  AND fkCountriesId=".$id." ";
								$tourManageObj->updateTournamentRules($updateString,$condition);
							}
							else { // new entry to country
								if(isset($countryEntries[$id])){
									$countryValues		.=	"(".$tournamentId.",0,".$id.",0,'".$countryEntries[$id]['rule']."','".$countryEntries[$id]['condition']."','".$countryEntries[$id]['thirdtournametrule']."','".$countryEntries[$id]['privacyPolicySingle']."','".$today."','".$today."',1),";
									$countryValues	=	' VALUES '.rtrim($countryValues,',');
									$tourManageObj->insertRules($countryValues);
								}
							}
						}
					}
				}
			}
		}
	//END: Rules Integration
	$_SESSION['tour_notification_msg_code'] = 3;
	header("location:TournamentManage?editId=".$tournamentId);
	die;
}

if(isset($_POST['tournamentDetail']) && $_POST['tournamentDetail'] != ''){
	$alreadyExist	= 0 ;
	$_POST['gen_terms_condition'] 		= $gen_termsCond;
	$_POST['gen_game_rules'] 			= $gen_gamerules;
	$_POST['gen_tournmentrules'] 		= $gen_tournmentrules;
	$_POST['gen_privacy_policy_tab'] 	= "";
	$_POST['fkDevelopersId']			= $devId;
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	$condition		=	'';

	if(isset($_POST['edit_id'])	&&	$_POST['edit_id'] > 0 )	{
		$condition	.=	' id <> '.$_POST['edit_id'].'  AND ';
		$tournamentId	=	$_POST['edit_id'];
	}
	//Check tournament exist
	$fields				 =	' id,TournamentName ';
	$condition			 .=	" TournamentName ='".trim($_POST['tournament_name'])."' AND  Status !=3 AND TournamentStatus !=3 ";
	$checkTournamentExist	=	$tourManageObj->selectTournament($fields,$condition);
	if(isset($checkTournamentExist)	&&	is_array($checkTournamentExist)	&&	count($checkTournamentExist) > 0){
		if(strcasecmp($checkTournamentExist[0]->TournamentName,trim($_POST['tournament_name'])) == 0)
			$alreadyExist	=	1;
	}

	if($alreadyExist	==	0){
		if(isset($tournamentId) && $tournamentId !=''){			//Update tournament detail
			$sql	=	'';
			if(isset($_POST['tournament_name']))
				$sql	.=	"TournamentName 	= 	'".$_POST['tournament_name']."',";
			if(isset($_POST['tournamentType']))
				$sql	.=	"TournamentType 	= 	'".$_POST['tournamentType']."',";
			if(isset($_POST['tournament_name']))
				$sql	.=	"fkGamesId 	= 	'".$_POST['game_id']."',";
			if(isset($_POST['tilt_prize']) && $_POST['tilt_prize'] >= 2){
				$sql 	.=	"Type 				= 	'2',";
				$sql 	.=	"Prize 				= 	'".$_POST['tilt_prize']."',";
			}
			if(isset($_POST['game_type'])	&&	trim($_POST['game_type']!="")){
				$sql 	.=	"GameType 			= 	'".$_POST['game_type']."',";
			}
			if(isset($_POST['start_time'])	&&	trim($_POST['start_time'])!=""){
				$sql	.=	"StartDate	=	'".date('Y-m-d H:i',strtotime($_POST['start_time']))."',";
				$sql	.=	"StartTime	=	'".date('Y-m-d H:i',strtotime($_POST['start_time']))."',";
			}
			else {
				$sql	.=	"StartDate	=	'2038-01-01 03:14:07',"; // till 19th day
				$sql	.=	"StartTime	=	'2038-01-01 03:14:07',";
			}
			if(isset($_POST['end_time'])	&&	trim($_POST['end_time'])!=""){
				$sql	.=	"EndDate	=	'".date('Y-m-d H:i',strtotime($_POST['end_time']))."',";
				$sql	.=	"EndTime	=	'".date('Y-m-d H:i',strtotime($_POST['end_time']))."',";
			}
			else {
				$sql	.=	"EndDate	=	'2038-01-02 03:14:07',";
				$sql	.=	"EndTime	=	'2038-01-02 03:14:07',";
			}
			if(isset($_POST['requiresdownloadpartnersapp'])	&&	trim($_POST['requiresdownloadpartnersapp']!="")){
				$sql 	.=	"requiresdownloadpartnersapp 			= 	'".$_POST['requiresdownloadpartnersapp']."',";
			}
			if(isset($_POST['partnerappiospackageid'])	&&	trim($_POST['partnerappiospackageid']!="")){
				$sql 	.=	"partnerappiospackageid 			= 	'".$_POST['partnerappiospackageid']."',";
			}
			if(isset($_POST['partnerappandroidpackageid'])	&&	trim($_POST['partnerappandroidpackageid']!="")){
				$sql 	.=	"partnerappandroidpackageid 			= 	'".$_POST['partnerappandroidpackageid']."',";
			}
			if(isset($_POST['partnerappiosurl'])	&&	trim($_POST['partnerappiosurl']!="")){
				$sql 	.=	"partnerappiosurl 			= 	'".$_POST['partnerappiosurl']."',";
			}
			if(isset($_POST['partnerappandroidurl'])	&&	trim($_POST['partnerappandroidurl']!="")){
				$sql 	.=	"partnerappandroidurl 			= 	'".$_POST['partnerappandroidurl']."',";
			}
			// if(isset($_POST['marquee'])	&&	trim($_POST['marquee']!="")){
			// 	$sql 	.=	"marquee 			= 	'".$_POST['marquee']."',";
			// }

			$sql 	    .=	"DateModified	= 	'".date('Y-m-d H:i:s')."'";
			$condition 			= "id = ".$tournamentId;
			$tourManageObj->updateTournamentDetail($sql,$condition);
			$_SESSION['tour_notification_msg_code'] = 2;
		}else{
			$tournamentId	=	$tourManageObj->insertTournamentDetails($_POST);	// Insert tournament detail
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
					$message					= $_SESSION['tilt_developer_company'].' has been created a new tournament : "'.$_POST['tournament_name'].'" ';
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
			$_SESSION['facebook_share'] = isset($_POST['facebook']) && $_POST['facebook'] == 1 ? 1 : 0 ;
			$_SESSION['twitter_share'] = isset($_POST['twitter']) && $_POST['twitter'] == 1 ? 1 : 0 ;
			$_SESSION['tour_notification_msg_code'] = 1;
		}
		generatePDF($devId,$tournamentId);
		// BEGIN: Developer Tilt$ update
		if(isset($_POST['tilt_prize'])	&&	trim($_POST['tilt_prize'] >= 2)){ //tilt
			$condition	=	' id = '.$_SESSION['tilt_developer_id'];
			$amount	=	$coin	=	'';
			$update_string		=	'';
			$tiltFee = 0;
			if(isset($_POST['tilt_default_fee']) && $_POST['tilt_default_fee'] !='')
				$tiltFee	=	$_POST['tilt_default_fee'];
			if(isset($_POST['tilt_prize_type']) && $_POST['tilt_prize_type'] == 2){// Last Type Tilt
				if((($_SESSION['tilt_developer_amount']+$_POST['old_prize_coin'])-$_POST['tilt_prize']) >=0){
					$update_string		=	' Amount = (Amount + '.$_POST['old_prize_coin'].') - '.$_POST['tilt_prize'];
					$_SESSION['tilt_developer_amount']	=	($_SESSION['tilt_developer_amount']+$_POST['old_prize_coin']) - $_POST['tilt_prize'];
					$condition	.=	" AND (Amount + ".$_POST['old_prize_coin'].") >= ".$_POST['tilt_prize']." ";
				}
			}
			else if(isset($_POST['tilt_prize_type']) && $_POST['tilt_prize_type'] == 3){ // Last Type Virtual
				if(($_SESSION['tilt_developer_amount'] - ($tiltFee.'+'.$_POST['tilt_prize'])) >=0){
					$update_string		=	' Amount = (Amount  - ('.$_POST['tilt_prize'].'+'.$tiltFee.')),VirtualCoins = (VirtualCoins+'.$_POST['old_prize_vcoin'].') ';
					$condition	.=	" AND Amount  >= (".$_POST['tilt_prize']." +".$tiltFee.")";
					$_SESSION['tilt_developer_amount']	=	($_SESSION['tilt_developer_amount']) - ($_POST['tilt_prize']+$tiltFee);
					$_SESSION['tilt_developer_coins']	=	($_SESSION['tilt_developer_coins']) + $_POST['old_prize_vcoin'];
				}
			}
			else{ // Last Type Custom or New tournament
				if(($_SESSION['tilt_developer_amount'] - ($tiltFee.'+'.$_POST['tilt_prize'])) >= 0){
					$update_string		=	' Amount = Amount - ('.$_POST['tilt_prize'].'+'.$tiltFee.')';
					$condition	=	' id = '.$_SESSION['tilt_developer_id'].' AND Amount >=('.$_POST['tilt_prize'].'+'.$tiltFee.') ';
					$_SESSION['tilt_developer_amount']	=	($_SESSION['tilt_developer_amount']) - ($_POST['tilt_prize']+$tiltFee);
				}
				if(isset($_POST['tilt_prize_type']) && $_POST['tilt_prize_type'] == 4){
					$updateString1	= " Status = 2, DateModified='".$today."' ";
					$condition1	=	" fkTournamentsId=".$tournamentId."  ";
					$tourManageObj->updateCustomPrizeDetails($updateString1,$condition1);
				}
			}

			// if(isset($_POST['requiresdownloadpartnersapp']) && trim($_POST['requiresdownloadpartnersapp']) !='')
			// 	$requiresdownloadpartnersapp	=	$_POST['requiresdownloadpartnersapp'];
			// if(isset($_POST['partnerappiospackageid']) && trim($_POST['partnerappiospackageid']) !='')
			// 	$partnerappiospackageid	=	$_POST['partnerappiospackageid'];
			// if(isset($_POST['partnerappandroidpackageid']) && trim($_POST['partnerappandroidpackageid']) !='')
			// 	$partnerappandroidpackageid	=	$_POST['partnerappandroidpackageid'];
			// if(isset($_POST['partnerappiosurl']) && trim($_POST['partnerappiosurl']) !='')
			// 	$partnerappiosurl	=	$_POST['partnerappiosurl'];
			// if(isset($_POST['partnerappandroidurl']) && trim($_POST['partnerappandroidurl']) !='')
			// 	$partnerappandroidurl	=	$_POST['partnerappandroidurl'];

			if(!empty($update_string)){
				$gameDevObj->updateGameDevDetails($update_string,$condition);
			}
		}
		// END: Developer Tilt$ update
		generatePDF($devId,$tournamentId);	//Generate PDF
		header("location:TournamentManage?editId=".$tournamentId);
		die;
	} else { // If Tounament Name already exist
		if(isset($_POST['game_id']) && trim($_POST['game_id']) !='')
			$gameId	=	$_POST['game_id'];
		if(isset($_POST['tournament_name']) && trim($_POST['tournament_name']) !='')
			$tournamentName	=	$_POST['tournament_name'];
		if(isset($_POST['tournamentType']) && trim($_POST['tournamentType']) !='')
			$tournamentType	=	$_POST['tournamentType'];
		if(isset($_POST['game_type']) && trim($_POST['game_type']) !='')
			$gameType	=	$_POST['game_type'];
		if(isset($_POST['start_time']) && trim($_POST['start_time']) !='')
			$startTime	=	$_POST['start_time'];
		if(isset($_POST['end_time']) && trim($_POST['end_time']) !='')
			$endTime	=	$_POST['end_time'];

		if(isset($_POST['requiresdownloadpartnersapp']) && trim($_POST['requiresdownloadpartnersapp']) !='')
			$requiresdownloadpartnersapp	=	$_POST['requiresdownloadpartnersapp'];
		if(isset($_POST['partnerappiospackageid']) && trim($_POST['partnerappiospackageid']) !='')
			$partnerappiospackageid	=	$_POST['partnerappiospackageid'];
		if(isset($_POST['partnerappandroidpackageid']) && trim($_POST['partnerappandroidpackageid']) !='')
			$partnerappandroidpackageid	=	$_POST['partnerappandroidpackageid'];
		if(isset($_POST['partnerappiosurl']) && trim($_POST['partnerappiosurl']) !='')
			$partnerappiosurl	=	$_POST['partnerappiosurl'];
		if(isset($_POST['partnerappandroidurl']) && trim($_POST['partnerappandroidurl']) !='')
			$partnerappandroidurl	=	$_POST['partnerappandroidurl'];

		if(isset($_POST['tilt_prize_type']) && trim($_POST['tilt_prize_type']) !='')
			$tilt_prize_type	=	$_POST['tilt_prize_type'];
		if(isset($_POST['tilt_prize']) && trim($_POST['tilt_prize']) !='')
			$tilt_prize	=	$_POST['tilt_prize'];
		if($alreadyExist == 1){
			$error_msg   = "Tournament already exists";
			$field_focus = 'tournament_name';
		}
		$display = "block";
		$class   = "error_msg";
	}
}

$fields			=	"*";
$condition		=	" AND Status = 1 ";
$gameDetails	=	$gameManageObj->getGameList($fields,$condition);
$tournamentId	=	'';
$prizeArray		=	$prizeDetails	= $customAdResult = $customMgcBackgroundImageResult = array();
if(isset($_GET['editId']) && $_GET['editId'] !=''){
	$fields  = ' t.id,t.fkGamesId,t.TournamentName,t.TournamentType,t.MaxPlayers,t.MinPlayers,t.TotalTurns,';
	$fields .= 't.EntryFee,t.Prize,t.StartDate,t.EndDate,t.DelayTime,t.PlayTime,t.NextTime,';
	$fields .= 't.LocationBased,t.GameType,t.Type,t.LocationRestrict,t.MaxLevel,t.CurrentLevel,';
	$fields .= 't.LevelDifficulty,t.TermsAndCondition,t.TournamentRule,t.GftRules,t.PrivacyPolicy,';
	$fields .= 't.PIN,t.FeeType,fkCountriesId,fkStatesId,TournamentLocation,Latitude,Longitude,';
	$fields .= 't.requiresdownloadpartnersapp,t.partnerappiospackageid,t.partnerappandroidpackageid,';
	$fields .= 't.partnerappiosurl,t.partnerappandroidurl, t.marquee';

	$tournamentId		=	$_GET['editId'];
	$condition			=	"  t.id = ".$tournamentId." AND  t.Status !=3 AND t.TournamentStatus !=3 AND t.fkDevelopersId = ".$devId;
	$tournamentDetail	=	$tourManageObj->selectTournamentDetails($fields,$condition);
	if(isset($tournamentDetail) && is_array($tournamentDetail) && count($tournamentDetail)>0){
		$gameId			=	$tournamentDetail[0]->fkGamesId;
		$tournamentName	=	$tournamentDetail[0]->TournamentName;
		$tournamentType	=	$tournamentDetail[0]->TournamentType;
		$maxPlayer		=	$tournamentDetail[0]->MaxPlayers;
		$turns			=	$tournamentDetail[0]->TotalTurns;
		$gameType		=	$tournamentDetail[0]->GameType;
		$startTime		=	$tournamentDetail[0]->StartDate;
		$endTime		=	$tournamentDetail[0]->EndDate;
		$marquee		=	$tournamentDetail[0]->marquee;

		$requiresdownloadpartnersapp = $tournamentDetail[0]->requiresdownloadpartnersapp;
		$partnerappiospackageid      = $tournamentDetail[0]->partnerappiospackageid;
		$partnerappandroidpackageid  = $tournamentDetail[0]->partnerappandroidpackageid;
		$partnerappiosurl            = $tournamentDetail[0]->partnerappiosurl;
		$partnerappandroidurl        = $tournamentDetail[0]->partnerappandroidurl;

		$prizeType		=	$tilt_prize_type	=	$tournamentDetail[0]->Type;
		$EntryFee		=	$tournamentDetail[0]->EntryFee;
		$FeeType		=	$tournamentDetail[0]->FeeType;
		$tour_termsCond		=	$tournamentDetail[0]->TermsAndCondition;
		$tour_gamerules		=	$tournamentDetail[0]->TournamentRule;
		$tour_rules			=	$tournamentDetail[0]->GftRules;
		$tour_privacyPolicy	=	$tournamentDetail[0]->PrivacyPolicy;
		$can_we_start		=	$tournamentDetail[0]->DelayTime;
		if($tournamentDetail[0]->PIN==1)	$pinBased = 1;
		if(isset($tournamentDetail[0]->tournamentPin)){
			$tournamentPin = $tournamentDetail[0]->tournamentPin;
		}else{
			$tournamentPin = rand(100000, 999999);
		}
		if($tournamentDetail[0]->LocationBased==1){
			$locationBased	= $tournamentDetail[0]->LocationBased;
			if(isset($tournamentDetail[0]->fkCountriesId) && !empty($tournamentDetail[0]->fkCountriesId))
				$tourCountryId	= $tournamentDetail[0]->fkCountriesId;
			if(isset($tournamentDetail[0]->fkStatesId) && !empty($tournamentDetail[0]->fkStatesId))
				$tourStateId	= $tournamentDetail[0]->fkStatesId;
			if(isset($tournamentDetail[0]->TournamentLocation) && !empty($tournamentDetail[0]->TournamentLocation))
				$tourLocation =	$tournamentDetail[0]->TournamentLocation;
			if(isset($tournamentDetail[0]->Latitude)&&!empty($tournamentDetail[0]->Latitude))
				$tourLocLat		= $tournamentDetail[0]->Latitude;
			if(isset($tournamentDetail[0]->Longitude) && !empty($tournamentDetail[0]->Longitude))
				$tourLocLon		= $tournamentDetail[0]->Longitude;
		}else if($tournamentDetail[0]->LocationRestrict==1){
			$locationRestrict	= $tournamentDetail[0]->LocationRestrict;
			$conditions			= ' and fkTournamentsId ='.$tournamentId.' AND Status = 1 ';
			$resLocRes			= $tourManageObj->getRestrictedLocation($conditions);
		}
		if(date('Y-m-d H:i:s',strtotime($startTime)) > $today){
			$editStatus = 0;
		}
		//restrict date editable of started/any user played
		if($gameType == 2){ //Elimination score Tournament
			$fields		=	" * ";
			$condition	=	" fkTournamentsId =".$tournamentId." ";
			$eliminationTourResult	=	$tourManageObj->getTournamentPlayed($fields,$condition);
			if(isset($eliminationTourResult) && is_array($eliminationTourResult) && count($eliminationTourResult)>0){
				$tourEditStatus		=	1;
			}
		}//High score Tournament
		else if(isset($tournamentDetail[0]->EndDate) && $tournamentDetail[0]->EndDate != '0000-00-00 00:00:00' &&	isset($tournamentDetail[0]->StartDate) && $tournamentDetail[0]->StartDate != '0000-00-00 00:00:00' ){
			if(date('Y-m-d H:i:s',strtotime($tournamentDetail[0]->StartDate)) < $today){ // started
				$tourEditStatus		=	1;
			}//else not started
		}
		if($prizeType == 4)	{
			$fields		=	"*";
			$condition	=	" fkTournamentsId = ".$tournamentId." AND Status = 1 ";
			$prizeDetails	=	$tourManageObj->getCustomPrizeDetails($fields,$condition);
		}
		else {
			$prizeCoin		=	$tournamentDetail[0]->Prize;
			if($prizeType == 2){	$tilt_prize = $tiltCoin	=	$prizeCoin; }
			else if($prizeType == 3)	$virtualCoin	=	$prizeCoin;
		}
		try{
			$condition	=	" fkTournamentsId = ".$tournamentId." ORDER BY AdOrder ASC ";
			$customAdResult  = $tourManageObj->getCustomAdDetails(" * ",$condition);
		} catch (Exception $e) {
	    	echo 'Caught exception[1149]: ',  $e->getMessage(), "\n";
		}
		try{
			$condition	=	" fkTournamentsId = ".$tournamentId." ORDER BY id ";
			$customMgcBackgroundImageResult = $tourManageObj->getMgcBackgroundImageDetails(" * ",$condition);
		} catch (Exception $e) {
	    	echo 'Caught exception[1156-getMgcBackgroundImageDetails]: ',  $e->getMessage(), "\n";
		}


		$locRest		=	$tournamentDetail[0]->LocationRestrict;
		$fields			=	'*';
		$condition		=	' fkTournamentsId ='.$tournamentId.'  AND Status = 1 ';
		$tourRulesResult	=	$tourManageObj->checkRulesEntry($fields,$condition);

		//display  tournamentscouponadlink
		$bannerImage='';
		$fields		=	"*";
		$condition	=	" fkTournamentsId	=	".$tournamentId." AND Status = 1 ";
		$couponAdLink		=	$tourManageObj->checkCouponBannerLink($fields,$condition);
		if(isset($couponAdLink) && is_array($couponAdLink) && count($couponAdLink) > 0 ){
			foreach($couponAdLink as $key =>$values){
				if(isset($values->Type) && $values->Type == 1){ // coupon
					$couponStartdate = $couponEnddate  = '';
					$couponAdlink	 =	$values->CouponAdLink;
					$couponCode		 =	$values->CouponTitle;
					$couponCodeId	 =	$values->id;
					$couponLimit	 =	$values->CouponLimit;
					if(isset($values->CouponStartDate) && $values->CouponStartDate != '0000-00-00 00:00:00')
						$couponStartdate =  date('m/d/Y',strtotime($values->CouponStartDate));
					if(isset($values->CouponEndDate) && $values->CouponEndDate != '0000-00-00 00:00:00')
						$couponEnddate   =  date('m/d/Y',strtotime($values->CouponEndDate));
					$couponImage		 =	$values->File;
					if($couponImage != '')
						$coupon_image_path	 =	COUPON_IMAGE_PATH.$tournamentId.'/'.$couponImage;
				}
				else if(isset($values->Type) && $values->Type == 2){ //banner

					$bannertextDisp = $bannertextSelected = $bannerimageDisp = $bannerimageSelected =  $textDisp = $bannerLink = '';
					if($values->InputType==1){
						$bannertextSelected	=	'selected';
						$bannerimageDisp	=	'style="display:none"';
					}
					else if($values->InputType==2){
						$bannerimageSelected	=	'selected';
						$bannertextDisp		    =	'style="display:none"';
					}else{
						$bannerimageDisp	=	'style="display:none"';
						$bannertextDisp	    =	'style="display:none"';
					}
					$bannerImageId	=	$values->id;
					$banner_text    =	$values->CouponAdLink;
					$banner_link    =	$values->URL;
					$bannerImage	=	$values->File;
					$banner_file_path	 =	"";
					$bannerFile = '';

					if(isset($values->File)	&&	$values->File !=''){
						$bannerFile 	= $values->File;
						if(SERVER){
							if(image_exists(12,$tournamentId.'/'.$bannerFile))
								$banner_file_path = BANNER_IMAGE_PATH.$tournamentId.'/'.$bannerFile;
							else $banner_file_path = '';
						}else{
							if(file_exists(BANNER_IMAGE_PATH_REL.$tournamentId.'/'.$bannerFile))
								$banner_file_path = BANNER_IMAGE_PATH.$tournamentId.'/'.$bannerFile;
							else $banner_file_path = '';
						}
						if(!empty($banner_file_path)){
							$ext = pathinfo($bannerFile, PATHINFO_EXTENSION);
							if (in_array($ext,$banner_video_array)) {
								$bannerVideo = 1;
								$oldbannerImage	=	$bannerFile;
							}
							else
								$oldbannerImage	= '<img  src="'.BANNER_IMAGE_PATH.$tournamentId.'/'.$bannerFile.'" width="75" height="75" >';
						}
					}
				}
				else if(isset($values->Type) && $values->Type == 3){ //youtube
					$youtubeurlSelected = $youtubecodeSelected  = $youtubeurlDisp = $youtubecodeDisp = $youtubeImageDisp = '';

					if($values->InputType==1){
						$youtubeurlSelected	=	'selected';
						$youtubecodeDisp	=	'style="display:none"';
					}
					else if($values->InputType==2){
						$youtubecodeSelected	=	'selected';
						$youtubeurlDisp		=	'style="display:none"';
					}
					else{
						$youtubeImageDisp 	=	$youtubecodeDisp	=	'style="display:none;"';
						$youtubeurlDisp		=	'style="display:none;"';
					}

					$youtubeLinkId	=	$values->id;
					$youtube_text    =	$values->CouponAdLink;
					$youtubeLink    =	$values->URL;
					$youtubeImage	 =	$values->File;
					if($youtubeImage != '')
						$youtube_image_path	 =	YOUTUBE_LINK_IMAGE_PATH.$tournamentId.'/'.$youtubeImage;
				}
			}
		}
	}else{
		header("Location:TournamentList?cs=1");
		die();
	}
}
commonHead();
?>
<body class="skin-black" >
<?php top_header(); ?>
	<div class="fancybox-overlay fancybox-overlay-fixed" id="loader" style="display:block"> <div id="fancybox-loading"><div></div></div></div>
	<?php if(isset($gameDetails) && is_array($gameDetails) && count($gameDetails)>0) { ?>

	<section class="content-header">
		<div class="box-body col-md-9 box-center">
			<h2 align="center"><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo 'Edit '; else echo 'Create ';?> Tournament</h2>
		</div>
	</section>

	<div class="col-sm-12 col-md-10 col-lg-9 box-center">
		<div id="rootwizard" class="tabbable tabs-left">
			<ul class="col-xs-12 col-sm-3">
				<li><a href="#tab1" data-toggle="tab" title="Tournament Details"><i class="fa fa-trophy tour_detail"></i> Tournament Details</a></li>
				<li><a <?php if(isset($tournamentId) && $tournamentId != '' ) { ?> href="#tab2" data-toggle="tab" <?php } ?> title="Advanced"><i class="fa fa-gears"></i> Advanced</a></li>
				<li><a <?php if(isset($tournamentId) && $tournamentId != '' ) { ?> href="#tab3" data-toggle="tab" <?php } ?> title="TiLT Labs"><i class="fa fa-flask"></i> TiLT Labs</a></li>
			</ul>
			<div class="tab-content col-xs-12 col-sm-9">
				<div class="tab-pane tournament_tab1" id="tab1">
					<form class="tournament" id="tournament_manage" name="tournamentDetail" method="post" data-webforms2-force-js-validation="true" >

					<input type="hidden" id="edit_id" name="edit_id" value="<?php if(isset($tournamentId) && $tournamentId != '' ) echo $tournamentId; ?>">
					<div class="clear padding10" align="right"><small>*&nbsp;necessary fields</small></div>
					<?php if(isset($sucess_msg_1) && $sucess_msg_1 != '') { ?>
						<div class="success_msg no-padding w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $sucess_msg_1;?></span></div>
					<?php } ?>
					<?php if(isset($error_msg) && $error_msg != '') { ?>
						<div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span></div>
					<?php } ?>
					<div class="error_msg w50" style="display:none;" id="tour_name_exists"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;Tournament already exists</span></div>
					<div class="box-body col-md-7 box-center">
						<h2 align="center">Select Game</h2>
					</div>
					<div class="form-group">
					<div class="scroll_content" style="height:150px;">
								<ul id="game_list" class="select_game">
								<?php if(isset($gameDetails) && count($gameDetails) > 0){
									foreach ($gameDetails as $g_key=>$g_val){
										$game_logo	=	$g_val->Photo;
										$game_image_path = GAME_IMAGE_PATH.'add_game.jpg';
										if($game_logo != '' ){
											if (!SERVER){
												if(file_exists(UPLOAD_GAMES_PATH_REL.$game_logo)){
													$game_image_path = GAMES_IMAGE_PATH.$game_logo;
												}
											}
											else {
												if(image_exists(10,$game_logo))
												$game_image_path = GAMES_IMAGE_PATH.$game_logo;
											}
										}
										$selectActive	=	'class="inactive"';
										if(isset($gameId) && $gameId == $g_val->id){
											$play_time 		=	(isset($g_val->PlayTime) && !empty($g_val->PlayTime) && $g_val->PlayTime !='00:00:00') ? $g_val->PlayTime : '00:10:00';
											$defautPosition = 	$g_val->id;
											$selectedGame	=	'<span style="margin-bottom:10px;">'.ucFirst(displayText($g_val->Name,20)).'</span><br><img src="'.$game_image_path.'" width="60" height="60" alt="">';
											$Games	=	'<li class=\"active\" ><div><label for=\"\" title=\"'.ucFirst($g_val->Name).'\"><img src=\"'.$game_image_path.'\" width=\"60\" height=\"60\" alt=\"\" id=\"game_img_'.$g_val->id.'\"  onclick=\"selectGame(this,'.$g_val->id.','.$g_val->id.');\" ><span>'.ucFirst(displayText($g_val->Name,9)).'</span></label><input type=\"Radio\" name=\"game_id\"  required id=\"game_id_'.$g_val->id.'\" checked autofocus value=\"'.$g_val->id.'\"><input type=\"hidden\" class=\"game_playTime\" id=\"game_playTime_'.$g_val->id.'\" name=\"game_playTime\" value=\"'.$g_val->PlayTime.'\"></div></li>';
											continue;
										}
								?>
									<li <?php echo $selectActive; ?> >
									<div>
										<label for="" title="<?php echo ucFirst($g_val->Name);?>">
										<img src="<?php echo $game_image_path;?>" width="60" height="60" alt="" id="game_img_<?php echo $g_val->id;?>"  onclick="selectGame(this,<?php echo $g_val->id;?>,<?php echo $g_val->id;?>);" ><span><?php echo ucFirst(displayText($g_val->Name,9));?></span>
										</label>
										<input type="Radio" name="game_id"  required id="game_id_<?php echo $g_val->id;?>" <?php if(isset($gameId) && $gameId == $g_val->id) echo 'checked autofocus';?> value="<?php echo $g_val->id;?>">
										<input type="hidden" class="game_playTime" id="game_playTime_<?php echo $g_val->id;?>" name="game_playTime" value="<?php if(isset($g_val->PlayTime) && !empty($g_val->PlayTime) && $g_val->PlayTime !='00:00:00') echo $g_val->PlayTime; else echo '00:10:00'; ?>">
									</div>
									</li>
								<?php } } ?>
								</ul>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-6 col-sm-5 text-right">*&nbsp;Tournament Type</label>
						<div class="col-xs-6 col-sm-4">
							<select name="tournamentType" class="country form-control" id="tournamentType">
								<option value="1" <?php if(isset($tournamentType) && $tournamentType==1) echo 'Selected';  ?>>Automatic</option>
								<option value="2" <?php if(isset($tournamentType) && $tournamentType==2) echo 'Selected';  ?>>Manual</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-6 col-sm-5 text-right">*&nbsp;Tournament Name</label>
						<div class="col-xs-6 col-sm-4">
							<input type="text" value="<?php if(isset($tournamentName) && $tournamentName !='') echo htmlentities($tournamentName);?>" id="tournament_name" name="tournament_name" class="form-control" required maxlength="150" autocomplete="off">
							<p align="center" id="tournament_msg"></p>
						</div>
					</div>

					<div class="form-group">
						<label class="col-xs-3 col-sm-5 text-right"><?php if(!isset($tournamentId) || $tournamentId == '' || $tilt_prize_type == '2') echo "*"; ?>&nbsp;Prize</label>
						<div class="col-xs-9 col-sm-4 prize_right">
							<input class="form-control inline" type="number" name="tilt_prize" id="tilt_prize" style="width:100px;margin-right:4%" min="2" max="1223317" value="<?php echo $tilt_prize; ?>" onKeyPress="return isNumberKey(event)" <?php if(!isset($tournamentId) || $tournamentId == '' || $tilt_prize_type == '2') echo "required"; ?> <?php if($FeeType == 2) echo "readonly"; ?> > TiLT$
							<input type="Hidden" value="<?php if(!isset($tournamentId) || $tournamentId == '' ) echo "1"; else if($tilt_prize_type > '0') echo $tilt_prize_type; ?>" name="tilt_prize_type" id="tilt_prize_type">
							<input type="Hidden" id="tilt_default_fee" name="tilt_default_fee" value="<?php echo $tiltdefaultfee; ?>" >
							<input type="Hidden" id="old_prize_coin" name="old_prize_coin" value="<?php echo $tiltCoin; ?>" >
							<input type="Hidden" id="old_prize_vcoin" name="old_prize_vcoin" value="<?php echo $virtualCoin; ?>" >
							<input type="Hidden" id="tilt_max" name="tilt_max" value="<?php if(isset($_SESSION['tilt_developer_amount'])) echo ($_SESSION['tilt_developer_amount']+$tiltCoin); ?>" >
							<input type="Hidden" id="vcoin_max" name="vcoin_max" value="<?php if(isset($_SESSION['tilt_developer_coins'])) echo ($_SESSION['tilt_developer_coins']+$virtualCoin); ?>" >
						</div>
					</div>
					<div class="form-group" id="start_date_div">
						<label class="col-xs-6 col-sm-5 text-right">*&nbsp;Start Date</label>
						<div class="col-xs-6 col-sm-4">
							<input name="start_time" value="<?php if(isset($startTime) && $startTime !='' && $startTime != '0000-00-00 00:00:00') echo date('m/d/Y H:i',strtotime($startTime)); ?>" id="start_time" type="text" class="form-control" autocomplete="off" onKeyPress="return isCalender(event);" onKeyDown="return isDelete(event);" required="required">
						</div>
					</div>
					<?php if(isset($gameType) && $gameType != '2' ) { ?>
					<div class="form-group" id="end_date_div">
						<label class="col-xs-6 col-sm-5 text-right">*&nbsp;End Date</label>
						<div class="col-xs-6 col-sm-4">
							<input name="end_time" value="<?php if(isset($endTime) && !empty($endTime) && $endTime !='0000-00-00 00:00:00') echo date('m/d/Y H:i',strtotime($endTime)); ?>" id="end_time" type="text" class="form-control"   autocomplete="off" onKeyPress="return isCalender(event);" onKeyDown="return isDelete(event);" required="required">
						</div>
					</div>
					<?php } ?>
					<?php if(!isset($_GET['editId'])) { ?>
							<div id="social_share" class="form-group">
									<input type="hidden" id="facebook" value="0" name="facebook">
									<input type="hidden" id="twitter" value="0" name="twitter">
									<label for="privacy_policy"  class="col-xs-6 col-sm-5 text-right">Share</label>
									<div class="controls search col-xs-6 col-sm-7">
										<button class="btn btn-large facebook" id="facebook_btn" type="button" title="Share with Facebook" onClick="setShare(this,'facebook')">Facebook</button>
										<button class="btn btn-large twitter" id="twitter_btn" type="button" title="Share with Twitter" onClick="setShare(this,'twitter')">Twitter</button>
									</div>
							</div>
							<div id="requiredownloadpartnersapp" class="form-group">

								<label class="col-xs-7 col-sm-7 col-md-7 text-right">Requires Download Partner's App</label>
								<div class="col-xs-5 col-sm-5 col-md-5 no-padding">
									<div class="onoffswitch">
										<input type="checkbox" name="requiresdownloadpartnersapp"
										 class="onoffswitch-checkbox" id="requiresdownloadpartnersapp"
										 <?php if(isset($requiresdownloadpartnersapp) && $requiresdownloadpartnersapp == 1) echo 'checked'; ?>
									 	 value="1">
										<label class="onoffswitch-label" for="requiresdownloadpartnersapp">
											<div class="onoffswitch-type">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</div>
										</label>
									</div>
								</div>

								<div class="requiresdownloadpartnersappgroup">

									<label class="col-xs-7 col-sm-7 text-right">Download Partner's App's iOS Package Id</label>
									<div class="col-xs-5 col-sm-5">
										<input type="text"
										value="<?php if(isset($partnerappiospackageid) && $partnerappiospackageid !=''){
											echo htmlentities($partnerappiospackageid);
										} ?>"
										id="partnerappiospackageid" name="partnerappiospackageid"
										class="form-control" maxlength="150"
										autocomplete="off">
										<p align="center" id="tournament_msg"></p>
									</div>

									<label class="col-xs-7 col-sm-7 text-right">Download Partner's App's Android Package Id</label>
									<div class="col-xs-5 col-sm-5">
										<input type="text"
										value="<?php if(isset($partnerappandroidpackageid) && $partnerappandroidpackageid !=''){
											echo htmlentities($partnerappandroidpackageid);
										} ?>"
										id="partnerappandroidpackageid" name="partnerappandroidpackageid"
										class="form-control" maxlength="150"
										autocomplete="off">
										<p align="center" id="tournament_msg"></p>
									</div>

									<label class="col-xs-7 col-sm-7 text-right">Download Partner's App's iOS URL</label>
									<div class="col-xs-5 col-sm-5">
										<input type="text"
										value="<?php if(isset($partnerappiosurl) && $partnerappiosurl !=''){
											echo htmlentities($partnerappiosurl);
										} ?>"
										id="partnerappiosurl" name="partnerappiosurl"
										class="form-control" maxlength="150"
										autocomplete="off">
										<p align="center" id="tournament_msg"></p>
									</div>

									<label class="col-xs-7 col-sm-7 text-right">Download Partner's App's Android URL</label>
									<div class="col-xs-5 col-sm-5">
										<input type="text"
										value="<?php if(isset($partnerappandroidurl) && $partnerappandroidurl !=''){
											echo htmlentities($partnerappandroidurl);
										} ?>"
										id="partnerappandroidurl" name="partnerappandroidurl"
										class="form-control" maxlength="150"
										autocomplete="off">
										<p align="center" id="tournament_msg"></p>
									</div>
								</div>

							</div>
					<?php } else { ?>
						<input type="hidden" id="facebook" value="0">
						<input type="hidden" id="twitter" value="0">

						<div id="requiredownloadpartnersapp" class="form-group">

							<label class="col-xs-7 col-sm-7 col-md-7 text-right">Requires Download Partner's App</label>
							<div class="col-xs-5 col-sm-5 col-md-5 no-padding">
								<div class="onoffswitch">
									<input type="checkbox" name="requiresdownloadpartnersapp"
									class="onoffswitch-checkbox" id="requiresdownloadpartnersapp"
									<?php if(isset($requiresdownloadpartnersapp) && $requiresdownloadpartnersapp == 1) echo 'checked'; ?>
										value="1">
									<label class="onoffswitch-label" for="requiresdownloadpartnersapp">
										<div class="onoffswitch-type">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</div>
									</label>
								</div>
							</div>

							<div class="requiresdownloadpartnersappgroup">

								<label class="col-xs-7 col-sm-7 text-right">Download Partner's App's iOS Package Id</label>
								<div class="col-xs-5 col-sm-5">
									<input type="text"
									value="<?php if(isset($partnerappiospackageid) && $partnerappiospackageid !=''){
										echo htmlentities($partnerappiospackageid);
									} ?>"
									id="partnerappiospackageid" name="partnerappiospackageid"
									class="form-control" maxlength="150"
									autocomplete="off">
									<p align="center" id="tournament_msg"></p>
								</div>

								<label class="col-xs-7 col-sm-7 text-right">Download Partner's App's Android Package Id</label>
								<div class="col-xs-5 col-sm-5">
									<input type="text"
									value="<?php if(isset($partnerappandroidpackageid) && $partnerappandroidpackageid !=''){
										echo htmlentities($partnerappandroidpackageid);
									} ?>"
									id="partnerappandroidpackageid" name="partnerappandroidpackageid"
									class="form-control" maxlength="150"
									autocomplete="off">
									<p align="center" id="tournament_msg"></p>
								</div>

								<label class="col-xs-7 col-sm-7 text-right">Download Partner's App's iOS URL</label>
								<div class="col-xs-5 col-sm-5">
									<input type="text"
									value="<?php if(isset($partnerappiosurl) && $partnerappiosurl !=''){
										echo htmlentities($partnerappiosurl);
									} ?>"
									id="partnerappiosurl" name="partnerappiosurl"
									class="form-control" maxlength="150"
									autocomplete="off">
									<p align="center" id="tournament_msg"></p>
								</div>

								<label class="col-xs-7 col-sm-7 text-right">Download Partner's App's Android URL</label>
								<div class="col-xs-5 col-sm-5">
									<input type="text"
									value="<?php if(isset($partnerappandroidurl) && $partnerappandroidurl !=''){
										echo htmlentities($partnerappandroidurl);
									} ?>"
									id="partnerappandroidurl" name="partnerappandroidurl"
									class="form-control" maxlength="150"
									autocomplete="off">
									<p align="center" id="tournament_msg"></p>
								</div>
							</div>

						</div>

					<?php } ?>
					<input type="hidden" id="logo_path" name="logo_path" value="<?php if(isset($logoPath) && $logoPath != '' ) echo $logoPath; ?>">
					<input type="hidden" id="fkDevelopersId" name="fkDevelopersId" value="<?php if(isset($devId) && $devId != '' ) echo $devId; ?>">
					<div align="center"><?php if(isset($_GET['editId']) && $_GET['editId'] != '') { ?><input type="submit" class="btn btn-green" name="tournamentDetail" value="Save" title="Save" alt="Save" onClick="return validateTDetail();"><?php } else { ?>  <input type="submit" class="btn btn-green" name="tournamentDetail" value="Create Tournament" title="Create Tournament" alt="Create Tournament" onClick="return validateTDetail();"><?php } ?> </div>
					</form>
				</div>
				<div class="tab-pane" id="tab2">
					<form class="tournament" id="" name="tournamentApp" method="post" data-webforms2-force-js-validation="true">
					<input type="hidden" id="edit_id" name="edit_id" value="<?php if(isset($tournamentId) && $tournamentId != '' ) echo $tournamentId; ?>">
					<input type="hidden" id="game_type" name="game_type" value="<?php if(isset($gameType) && $gameType != '' ) echo $gameType; ?>">
					<input type="hidden" id="entry_fee_hidden" name="entryFee" value="<?php if(isset($EntryFee) && $EntryFee != '' ) echo $EntryFee; ?>">
					<input type="hidden" id="fee_type_hidden" name="feeType" value="<?php if(isset($FeeType) && $FeeType != '' ) echo $FeeType; ?>">
					<input type="hidden" id="prize_type_hidden" name="prizeType" value="<?php if(isset($prizeType) && $prizeType != '' ) echo $prizeType; ?>">
					<input type="hidden" id="prize_coin_hidden" name="prizeCoin" value="<?php if(isset($prizeCoin) && $prizeCoin != '' ) echo $prizeCoin; ?>">

					<div class="form-group" style="margin-top:10px;margin-bottom:20px;">
						<?php if(isset($selectedGame) && $selectedGame!= '' ) { ?>
						<div class="col-xs-6 col-sm-6 selected_game">
							<?php echo $selectedGame; ?>
						</div>
						<?php } ?>
						<div class="col-xs-6 col-sm-6" align="right"><small>*&nbsp;necessary fields</small></div>
					</div>
					<?php if(isset($sucess_msg_2) && $sucess_msg_2 != '') { ?>
						<div class="success_msg no-padding w50" style="padding-bottom:25px;"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $sucess_msg_2;?></span></div>
					<?php } ?>
					<div class="form-group">
						<label class="col-xs-6 col-sm-4 col-md-5 text-right">*&nbsp;Max. Players</label>
						<div class="col-xs-6 col-sm-4">
							<input type="number" min="2" class="form-control multi-select" required name="max_player" id="max_player" max="99999999" value="<?php if(isset($maxPlayer) && $maxPlayer !=0) echo $maxPlayer; else echo '2';?>" onKeyPress="return isNumberKey(event)" onChange="prizeCalc()" <?php if(isset($FeeType) && $FeeType == 2 ) echo 'readonly'; ?> >
						</div>
					</div>
					<div class="form-group" id="numberofturns">
						<label class="col-xs-6 col-sm-4 col-md-5 text-right">*&nbsp;No. of Turns Per day</label>
						<div class="col-xs-6 col-sm-4">
							<input type="number" min="1" class="form-control multi-select pull-left" required name="turns" id="turns" <?php if(isset($gameType) && $gameType == 2) { ?> value=1 disabled <?php } else { ?> value="<?php if(isset($turns) && $turns !=0) echo $turns; else echo '3';?>" <?php } ?> onkeypress="return isNumberKey(event)" max="100" >
						</div>
					</div>
					<div class="form-group clear">
						<label class="col-xs-12 col-sm-2">Global</label>
						<div class="col-xs-12 col-sm-10">
						 	<ul class="nav nav-tabs tab_style" style="padding-top:0px;">
								<li id="termsandcond_link" onClick="tournament_blockdisp('terms_cond',this);" class="active">Terms and Conditions</li>
								<li id ="gamerules_link" onClick="tournament_blockdisp('game_rules',this);">Game Rules</li>
								<li id ="tourrules_link" onClick="tournament_blockdisp('tour_rules',this);">Tournament Rules</li>
								<li id ="privacypolicy_link" onClick="tournament_blockdisp('tour_privacy',this);">Privacy Policy</li>
							<ul>
							<input type="hidden" style="disabled='true'" value="<?php echo htmlentities($gen_termsCond); ?>" name='terms_cond_template' id='terms_cond_template'>
							<input type="hidden" style="disabled='true'" value="<?php echo htmlentities($gen_gamerules); ?>" name='game_rules_template' id='game_rules_template'>
							<input type="hidden" style="disabled='true'" value="<?php echo htmlentities($gen_tournmentrules); ?>" name='tournament_rules_template' id='tournament_rules_template'>
							<input type="hidden" style="disabled='true'" value="<?php echo htmlentities($gen_privacyPolicy); ?>" name="privacy_rules_template" id='privacy_rules_template'>
						</div>
						<div class="col-xs-12 col-sm-10 pull-right">

							<div class="terms_cond" id="terms_td"><textarea class="textarea-full textEditor" id="terms_condition" rows="3" cols="32" tabindex="11" name="gen_terms_condition"><?php if(isset($_GET['editId']) && $_GET['editId'] != '') echo $tour_termsCond ; else  echo $gen_termsCond; ?></textarea></div>
							<div class="game_rules"   style ="display:none;" id="rules_td"><textarea class="textarea-full textEditor" id="game_rules" rows="2" cols="32" tabindex="11" name="gen_game_rules"><?php if(isset($_GET['editId']) && $_GET['editId'] != '') echo $tour_gamerules ; else echo $gen_gamerules; ?></textarea></div>
							<div class="tour_rules"   style ="display:none;" id="tournamentrules_td" ><textarea class="textarea-full textEditor" id="tournment_rules" rows="2" cols="32" tabindex="11" name="gen_tournmentrules"><?php  if(isset($_GET['editId']) && $_GET['editId'] != '') echo $tour_rules ; else echo $gen_tournmentrules; ?></textarea></div>
							<div class="tour_privacy" style ="display:none;" id="privacypolicy_td" ><textarea class="textarea-full textEditor" id="privacy_policy_tab" rows="2" cols="32" tabindex="11" name="gen_privacy_policy_tab"><?php if(isset($_GET['editId']) && $_GET['editId'] != '') echo $tour_privacyPolicy ; else  echo $gen_privacyPolicy; ?></textarea></div>
						</div>
						<div class="Ruleslist col-xs-12 col-sm-12  no-padding">
						<?php if(isset($tourRulesResult) && is_array($tourRulesResult) && count($tourRulesResult)>0){
							$rulesCount = count($tourRulesResult);
							foreach($tourRulesResult as $ruleKey => $rulesDetails){
								$addRule = 'style="display:none"';
								$stateDisplay = 'display:none';
								if($rulesCount == ($ruleKey+1)) $addRule = "";
								if($rulesDetails->fkCountriesId == $usId)  $stateDisplay = "";
								?>
							<div clone="<?php echo $ruleKey;?>" class="rulerow ">
								<div class="add_remove col-xs-12 col-sm-2" >
									<div class="fleft">
										<a href="javascript:void(0)" onClick="delCountryRule(this)"><i class="fa fa-lg  fa-minus-circle"></i></a>
										<span id="new_<?php echo $ruleKey; ?>"  class="addNewRule" <?php echo $addRule;?>><a href="javascript:void(0)" onClick="addCountryRule(this)"><i class="fa fa-lg fa-plus-circle"></i></a></span>&nbsp;&nbsp;
									</div>
									<div class="fleft col-xs-12" style="padding-left:0px;">
										<select name="rule_country[]" tabindex="10" class="country form-control" id="country_<?php echo $ruleKey; ?>" onChange="countryShow(<?php echo $ruleKey;?>);">
											<option value="">Select</option>
											<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
												foreach($countryArray as $c1key => $c1value) {  ?>
												<option value="<?php echo $c1key; ?>" <?php   if($c1key == $rulesDetails->fkCountriesId) echo 'Selected';  ?>><?php echo $c1value; ?></option>
											<?php 	} ?>
										</select>
										<br>
										<span id='field_name_empty' class="error_empty"></span>
										<span class="slabel" id="state_label_<?php echo $ruleKey; ?>" style="<?php echo $stateDisplay;?>;float:left;" >State</span>
										<br>
										<select name="rule_state[]" tabindex="10" class="state form-control" style="<?php echo $stateDisplay;?>" id="state_<?php echo $ruleKey; ?>">
											<option value="">Select</option>
											<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
												foreach($usStateArray as $s1key => $s1value) {  ?>
												<option value="<?php echo $s1key; ?>" <?php   if($s1key == $rulesDetails->fkStatesId) echo 'Selected';  ?>><?php echo $s1value; ?></option>
											<?php 	} ?>
										</select>
										<span id='sample_data_empty' class="error_empty"></span>
									</div>
								</div>
								<div class="terms_cond terms_td col-xs-12 col-sm-10" id="terms_td" ><textarea class="textarea-full textEditor" id="terms_condition_<?php echo $ruleKey; ?>" rows="3" cols="32" tabindex="11" name="tournamentConditions[]"><?php echo $rulesDetails->TermsAndConditions; ?></textarea></div>
								<div class="game_rules rules_td col-sm-10 "  style ="display:none;" id="rules_td" ><textarea class="textarea-full textEditor " id="game_rules_<?php echo $ruleKey; ?>" rows="2" cols="32" tabindex="11" name="tournamentRules[]"><?php echo $rulesDetails->TournamentRules; ?></textarea></div>
								<div class="tour_rules tournamentrules_td col-sm-10"  style ="display:none;" id="tournamentrules_td" ><textarea class="textarea-full textEditor " id="tournment_rules_<?php echo $ruleKey; ?>" rows="2" cols="32" tabindex="11" name="thirdtournamentRules[]"><?php echo $rulesDetails->GftRules; ?></textarea></div>
								<div class="tour_privacy privacypolicy_td col-sm-10"  style ="display:none;" id="privacypolicy_td" ><textarea class="textarea-full textEditor " id="privacy_policy_<?php echo $ruleKey; ?>" rows="2" cols="32" tabindex="11" name="privacy_policy_arr[]"><?php echo $rulesDetails->PrivacyPolicy; ?></textarea></div>
							</div>
							<?php }
						}else {?>
							<div clone="0" class="rulerow">
								<div class="add_remove col-xs-12 col-sm-2" >
									<div class="fleft">
										<a href="javascript:void(0)" onClick="delCountryRule(this)"><i class="fa fa-lg  fa-minus-circle"></i></a>
										<span id="new_0"  class="addNewRule" ><a href="javascript:void(0)" onClick="addCountryRule(this)"><i class="fa fa-lg fa-plus-circle"></i></a></span>&nbsp;&nbsp;
									</div>
									<div class="fleft col-xs-12" style="padding-left:0px;">
										<select name="rule_country[]" tabindex="10" style="width:100%;" class="form-control country" id="country_0" onChange="countryShow(0);">
											<option value="">Select</option>
											<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
												foreach($countryArray as $ckey => $cvalue) {  ?>
												<option value="<?php echo $ckey; ?>" ><?php echo $cvalue; ?></option>
											<?php 	} ?>
										</select>
										<br>
										<span id='field_name_empty' class="error_empty"></span>
										<span class="slabel" id="state_label_0" style="line-height:25px;display:none;float:left;">State</span>
										<br>
										<select name="rule_state[]" tabindex="10" class="state form-control" style="display:none;" id="state_0">
											<option value="">Select</option>
											<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
												foreach($usStateArray as $skey => $svalue) {  ?>
												<option value="<?php echo $skey; ?>" ><?php echo $svalue; ?></option>
											<?php 	} ?>
										</select>
										<span id='sample_data_empty' class="error_empty"></span>
									</div>
								</div>
								<div class="terms_cond terms_td col-xs-12 col-sm-10" id="terms_td"><textarea class="textarea-full textEditor" id="terms_condition_0" rows="3" cols="32" tabindex="11" name="tournamentConditions[]"><?php echo $gen_termsCond; ?></textarea></div>
								<div class="game_rules rules_td col-xs-12 col-sm-10"  style ="display:none;" id="rules_td"><textarea class="textarea-full textEditor " id="game_rules_0" rows="2" cols="32" tabindex="11" name="tournamentRules[]"><?php echo $gen_gamerules; ?></textarea></div>
								<div class="tour_rules tournamentrules_td col-xs-12 col-sm-10"  style ="display:none;" id="tournamentrules_td" ><textarea class="textarea-full textEditor " id="tournment_rules_0" rows="2" cols="32" tabindex="11" name="thirdtournamentRules[]"><?php echo $gen_tournmentrules; ?></textarea></div>
								<div class="tour_privacy privacypolicy_td col-xs-12 col-sm-10"  style ="display:none;" id="privacypolicy_td" ><textarea class="textarea-full textEditor " id="privacy_policy_0" rows="2" cols="32" tabindex="11" name="privacy_policy_arr[]"><?php echo $gen_privacyPolicy; ?></textarea></div>
							</div>
					<?php } ?>
					</div>

					</div>
					<div align="center"><input type="submit" class="btn btn-green" name="tournamentApp" value="Save" title="Save" alt="Save"></div>
					</form>
				</div>
				<div class="tab-pane" id="tab3">
					<form class="tournament" id="" name="tournamentLab" method="post" data-webforms2-force-js-validation="true">
					<input type="hidden" id="edit_id" name="edit_id" value="<?php echo $tournamentId; ?>">
					<input type='hidden' id='foursquare_selected' name='foursquare_selected' value="">
					<input type='hidden' id='locationsearch_id' name='locationsearch_id' value="">
					<input type='hidden' id='temp_hidden_lat' name='temp_hidden_lat' value="">
					<input type='hidden' id='temp_hidden_long' name='temp_hidden_long' value="">
					<input type='hidden' id='temp_hidden_flag' name='temp_hidden_flag' value="">
					<input type="hidden" name="max_player" id="max_player_hidden" value="<?php if(isset($maxPlayer) && $maxPlayer !=0) echo $maxPlayer; else echo '2';?>" >
					<input name="start_time" value="<?php if(isset($startTime) && $startTime != '' && $startTime !='0000-00-00 00:00:00') echo date('m/d/Y H:i',strtotime($startTime)); ?>" id="start_time_hidden" type="hidden">
					<input name="end_time" value="<?php if(isset($endTime) && !empty($endTime) && $endTime !='0000-00-00 00:00:00') echo date('m/d/Y H:i',strtotime($endTime)); ?>" id="end_time_hidden" type="hidden">


					<div class="box-body col-xs-12 no-padding box-center">
					<div class="form-group" style="margin-top:10px;margin-bottom:10px;">
						<?php if(isset($selectedGame) && $selectedGame!= '' ) { ?>
						<div class="col-xs-6 col-sm-6 selected_game">
							<?php echo $selectedGame; ?>
						</div>
						<?php } ?>
						<div class="col-xs-6 col-sm-6" align="right"><small>*&nbsp;necessary fields</small></div>
					</div>
					<?php if(isset($sucess_msg_3) && $sucess_msg_3 != '') { ?>
						<div class="success_msg no-padding w50" style="padding-bottom:25px;"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $sucess_msg_3;?></span></div>
					<?php } ?>

					<div class="form-group">
							<label class="col-xs-6 col-sm-4 col-md-5 text-right">Elimination</label>
							<div class="col-xs-6 col-sm-8 col-md-6 no-padding">
								<div class="col-sm-12 no-padding hgt68">
									<div class="col-xs-12">
									<div class="pull-left col-xs-12 no-padding">
										<div class="onoffswitch">
												<input type="checkbox" name="elimination" class="onoffswitch-checkbox" id="elimination"  onchange="chkTournamentType()" <?php if(isset($gameType) && $gameType == 2) echo 'checked'; ?> value="1">
												<label class="onoffswitch-label" for="elimination">
													<div class="onoffswitch-type">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
													</div>
												</label>
											</div>
									</div>
									</div>

								</div>
							</div>
					</div>
					<div id="can_we_start_div" class="clear">
						<div class="form-group" >
							<label class="col-xs-6 col-sm-4 col-md-5 text-right">*&nbsp;Can Start</label>
							<div class="col-xs-6 col-md-2 col-sm-6">
								<input type="text" class="form-control" placeholder="hh:mm:ss" name="can_we_start" id="can_we_start" value="<?php if(isset($can_we_start) && $can_we_start != '00:00:00') echo $can_we_start; ?>" maxlength="8" onKeyPress="return timeField(event);" autocomplete="off">
							</div>
						</div>
						<div class="form-group" id="play_time_div">
							<label class="col-xs-6 col-sm-4 col-md-5 text-right">Play Time</label>
							<div class="col-xs-6 col-md-2 col-sm-6">
								<input type="text" class="form-control" placeholder="hh:mm:ss" name="play_time" id="play_time" value="<?php if(isset($play_time) && !empty($play_time) && $play_time !='00:00:00') echo $play_time; ?>" disabled="true">
							</div>
						</div>
					</div>
						<div class="form-group">
							<label class="col-xs-6 col-sm-4 col-md-5 text-right">Entry Fees</label>
							<div class="col-xs-6 col-sm-8 col-md-6 no-padding">
								<div class="col-sm-12 no-padding hgt68">
									<div class="col-xs-12">
									<div class="pull-left col-xs-12 no-padding">
										<div class="onoffswitch">
											<input type="checkbox" name="entry_type" class="onoffswitch-checkbox" id="entry_type" <?php echo ($FeeType == 2)?'checked="checked"':''; ?> onClick="show_pay();" value="1">
											<label class="onoffswitch-label" for="entry_type">
												<div class="onoffswitch-type">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
												</div>
											</label>
										</div>
									</div>
									</div>
									</div>

								</div>
							</div>

								<div id="fee_pay_type_block" class="clear" style="display:none">
								<div class="form-group">
								<label class="col-xs-12 col-sm-4 col-md-5 text-right">Type</label>
								<div class="col-xs-12 col-sm-8 col-md-6">
								<label for="pay_tilt"><input type="radio" name="fee_pay_type" onChange="setFeeType('tilt')" id="pay_tilt" <?php if(isset($prizeType) && $prizeType == 2) echo 'checked'; ?> value="2" >&nbsp;&nbsp;TiLT$&nbsp;&nbsp;|&nbsp;&nbsp;</label>
										<label for="pay_vcoin"><input type="radio" name="fee_pay_type" onChange="setFeeType('vcoin')" id="pay_vcoin" <?php if(isset($prizeType) && $prizeType == 3) echo 'checked'; ?> value="3" >&nbsp;&nbsp;Virtual Coins&nbsp;&nbsp;</label>
								</div>

								<label class="col-xs-12 col-sm-4 col-md-5 text-right clear"></label>
										<div class="col-xs-12 col-sm-8 col-md-6" id="pay_type" style="<?php echo ($FeeType != 2)?'display:none':''; ?>;">
									<input type="text" onKeyPress="return isNumberKey(event);" maxlength="10" class="w80 form-control" name="entry_fee" id="entry_fee" value="<?php echo isset($EntryFee) ? $EntryFee: '';?>" onChange="calcPrize()" onBlur="calcPrize()">&nbsp;&nbsp;<label class="entry_fee_text" id="fee_type">TiLT$</label>
									</div>
							</div>
							</div>
						<input type="Hidden" id="tilt_default_fee" name="tilt_default_fee" value="<?php echo $tiltdefaultfee; ?>" >
						<input type="hidden" id="minTime" name="minTime" value="<?php echo date('H:i'); ?>">
						<div class="form-group">
							<label class="col-xs-12 col-sm-4 col-md-5 text-right">*&nbsp;Prize</label>
							<div class="col-xs-12 col-sm-8 col-md-7 prize_tournament">
								<label ><input type="radio" name="prize_type" onClick="setPrizeType('tilt')" id="tilt" <?php if(isset($prizeType) && $prizeType == 2) echo 'checked'; ?> required value="2" >&nbsp;&nbsp;TiLT$&nbsp;&nbsp;|&nbsp;&nbsp;</label>
								<label ><input type="radio" name="prize_type" onClick="setPrizeType('vcoin')" id="coin" <?php if(isset($prizeType) && $prizeType == 3) echo 'checked'; ?> value="3" >&nbsp;&nbsp;Virtual Coins&nbsp;&nbsp; |&nbsp;&nbsp;</label>
								<label ><input type="radio" name="prize_type" onClick="setPrizeType('custom')" id="custom" <?php if(isset($prizeType) && $prizeType == 4) echo 'checked'; ?> value="4" >&nbsp;&nbsp;Custom&nbsp;&nbsp;</label>
								<a href="javascript:void(0);" class="question_icon" title="Tournament Prize can be TiLT$, Virtual Coins, Custom Prize will be distributed to winners of the tournament w.r.t Price Payout."></a>
							</div>
							<label class="col-xs-12 col-sm-4 col-md-5 text-right clear"> </label>
							<div class="col-xs-12 col-sm-8 col-md-6" id ="prize_tiltCoin">
								<input  class="form-control inline" type="number" name="prize_coin" id="prize_coin" style="width:100px;margin-right:4%" min="2" max="" value="<?php if(isset($prizeCoin) && $prizeCoin !='') echo $prizeCoin; else echo '2';?>"  onkeypress="return isNumberKey(event)">
								<input type="Hidden" id="old_prize_coin" name="old_prize_coin" value="<?php echo $tiltCoin; ?>" >
								<input type="Hidden" id="old_prize_vcoin" name="old_prize_vcoin" value="<?php echo $virtualCoin; ?>" >
								<input type="Hidden" id="tilt_max" name="tilt_max" value="<?php if(isset($_SESSION['tilt_developer_amount'])) echo ($_SESSION['tilt_developer_amount']+$tiltCoin); ?>" >
								<input type="Hidden" id="vcoin_max" name="vcoin_max" value="<?php if(isset($_SESSION['tilt_developer_coins'])) echo ($_SESSION['tilt_developer_coins']+$virtualCoin); ?>" >
								<input type="hidden" id="titl_coin_flag" name="titl_coin_flag" value="<?php if(isset($prizeType) && $prizeType == 2) echo '2';else if(isset($prizeType) && $prizeType == 3) echo '3';else if(isset($prizeType) && $prizeType == 4) echo '4'; else echo '0'; ?>">
							 </div>



							<div class="col-xs-12 col-sm-12 root no-padding" id ="prize_custom" style="display:none;margin-top:15px">
									<?php if(isset($prizeDetails) && is_array($prizeDetails) && count($prizeDetails)>0){
											$prizeCount	=	count($prizeDetails) - 1;
											foreach($prizeDetails as $key=>$details){
												$prizeImage	=	$details->PrizeImage;
												$prizeImagePath	=	'';
												$addBtn	=	'style="display:none"';
												if($prizeCount == $key) $addBtn	=	'';
												if($prizeImage !=''){
													if(SERVER){
														if(image_exists(17,$details->fkTournamentsId.'/'.$prizeImage))
															$prizeImagePath	=	CUSTOM_PRIZE_IMAGE_PATH.$details->fkTournamentsId.'/'.$prizeImage;
													}
													else {
														if(file_exists(CUSTOM_PRIZE_IMAGE_PATH_REL.$details->fkTournamentsId.'/'.$prizeImage))
															$prizeImagePath	=	CUSTOM_PRIZE_IMAGE_PATH.$details->fkTournamentsId.'/'.$prizeImage;
													}
												} ?>
												<div class="col-sm-12 no-padding base" id="custom_prizeRow<?php echo ($key); ?>" clone="<?php echo ($key); ?>">
													<div class="form-group col-xs-6 col-sm-3 col-md-4 col-lg-3">
														<label class="col-xs-12 col-sm-12 col-md-12 text-left no-padding">Prize Name/Title</label>
														<div class="col-xs-12 col-sm-12 col-md-12 no-padding">
														<input type="text" required id="prize_title<?php echo ($key); ?>" name="prize_title[]" class="form-control prize_title" value="<?php if(isset($details->PrizeTitle)) echo $details->PrizeTitle;?>">
														<input type="hidden" value="<?php if(isset($details->id)) echo $details->id;?>" name="prize_title_id[]" class="prize_title_id w90">
														</div>
													</div>
													<div class="form-group col-xs-6 col-sm-3 col-md-3 col-lg-2">
														<label class="col-xs-12 col-sm-12 col-md-12 no-padding">Image</label>
														<div class="col-xs-12 col-sm-3 col-md-12 no-padding">
														<div class="btn btn-file btn-gray upload_img">
															Select File <input type="file" required name="custom_prizeImage<?php echo ($key); ?>" id="custom_prizeImage<?php echo ($key); ?>" onChange="return ajaxAdminFileUploadProcess('custom_prizeImage<?php echo ($key); ?>');"  />
														</div>
														</div>

														<div id="custom_prizeImage<?php echo ($key); ?>_img" class="display_img pull-left" style="margin-bottom:10px;margin-top:6px;clear:left;">
															<?php  if(isset($prizeImagePath) && $prizeImagePath != ''){  ?>
																<a href="<?php echo $prizeImagePath; ?>" class="user_photo_pop_up" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $prizeImagePath;  ?>" width="75" height="75" alt="Image"/></a>
																<input type="hidden" value="oldimage" id="custom_prizeImage<?php echo $key; ?>_upload" name="custom_prizeImage<?php echo $key; ?>_upload">
															<?php  }  ?>
														</div>

														<input type="hidden" value="" id="prize_tempFile<?php echo ($key); ?>" name="prize_tempFile[]" class="prize_tempFile w90">
														<input type="hidden" id="old_prize<?php echo $key; ?>" name="old_prize_file[]" value="<?php if(isset($prizeImage) && !empty($prizeImage)) echo $prizeImage;?>">
													</div>
													<div class="form-group col-xs-6 col-sm-3 col-md-3 col-lg-5">
														<label class="col-xs-12 col-sm-12 col-md-12 no-padding">Description</label>
														<div class="col-xs-12 col-sm-12 col-md-12 no-padding">
															<textarea name="custom_prizeDes[]" id="custom_prizeDes<?php echo ($key); ?>" required cols="100" rows="4" style="" class="form-control custom_prizeDes"><?php if(isset($details->PrizeDescription)) echo $details->PrizeDescription;?></textarea>
														</div>
													</div>
														<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 form-group clone_action add_button">

														<a onClick="deletePrizeRow(this,'custom_prizeRow<?php echo ($key); ?>')" href="javascript:void(0)"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;&nbsp;
														<a id="new_code_0" class="add_new" <?php echo $addBtn; ?> onClick="addPrizeRow(this,'custom_prizeRow<?php echo ($key); ?>')" href="javascript:void(0)"><i class="fa text-green fa-lg fa-plus-circle"></i></a>
													</div>
													</div>

								<?php 		}
										}else{ ?>
										<div class="col-sm-12 no-padding base" id="custom_prizeRow0" clone="0">
											<div class="form-group col-xs-6 col-sm-3 col-md-4 col-lg-3">
												<label class="col-xs-12 col-sm-12 col-md-12 text-left no-padding">Prize Name/Title</label>
												<div class="col-xs-12 col-sm-12 col-md-12 no-padding">
													<input type="text" required id="prize_title0" name="prize_title[]" class="form-control prize_title">
													<input type="hidden" value="" name="prize_title_id[]" class="prize_title_id w90">
												</div>
											</div>
											<div class="form-group col-xs-6 col-sm-3 col-md-3 col-lg-2">
												<label class="col-xs-12 col-sm-12 col-md-12 no-padding">Image</label>
												<div class="">
												<div class="col-xs-12 col-sm-3 col-md-12 no-padding">
													<div class="btn btn-file btn-gray upload_img ">
														Select File <input type="file" required name="custom_prizeImage0" id="custom_prizeImage0" onChange="return ajaxAdminFileUploadProcess('custom_prizeImage0');"  />
													</div>
												</div>
												<div id="custom_prizeImage0_img" class="display_img pull-left" style="margin-bottom:10px;margin-top:6px;clear:left;">
												</div>
												<input type="hidden" value="" id="prize_tempFile0" name="prize_tempFile[]" class="prize_tempFile w90">
												<input type="hidden" id="old_prize0" name="old_prize_file[]" value="">
											</div>
										</div>
										<div class="form-group col-xs-6 col-sm-3 col-md-3 col-lg-5">
												<label class="col-xs-12 col-sm-12 col-md-12 no-padding">Description</label>
												<div class="col-xs-12 col-sm-12 col-md-12 no-padding">
													<textarea name="custom_prizeDes[]" id="custom_prizeDes0" required cols="100" rows="4" style="" class="form-control custom_prizeDes"></textarea>
												</div>
										</div>
												<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 form-group clone_action add_button">
												<a onClick="deletePrizeRow(this,'custom_prizeRow0')" href="javascript:void(0)"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;&nbsp;
												<a id="new_code_0" class="add_new" onClick="addPrizeRow(this,'custom_prizeRow0')" href="javascript:void(0)"><i class="fa text-green fa-lg fa-plus-circle"></i></a>
											</div>
											</div>


								<?php } ?>
								</div>
							</div>



							<?php
							//
							//
							//
							// list of Ads for the mgctv e.g. http://api.tilt.co/v5/mgc/?id=1055
							//
							//
							//
							?>
							<div class="form-group">
								<label class="col-xs-6 col-sm-4 col-md-5 text-right">MGC Bottom Marquee</label>
								<div class="col-xs-6 col-sm-8 col-md-6 no-padding">
									<div class="col-sm-12 no-padding hgt68">
										<div class="col-xs-12">
											<div class="pull-left col-xs-12 no-padding">
												<div class="onoffswitch">
													<input type="text" name="marquee" class="" id="marquee" value="<?php echo $marquee; ?>">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<h3>MGC Ad list</h3>
							<div class="form-group">
								<div class="col-xs-12 col-sm-12 root no-padding" id ="mgc_ad_custom" style="margin-top:15px">
									<!-- I don't want this to be hidden, ever. -->
									<!-- display:none; -->
										<?php if(false && isset($customAdResult) && is_array($customAdResult) && count($customAdResult)>0){
												$mgc_adCount	=	count($customAdResult) - 1;
												foreach($customAdResult as $key=>$details){
													$mgc_adImage	=	$details->AdImageOrVideoFile;
													$mgc_adImagePath	=	'';
													$addBtn	=	'style="display:none"';
													if($mgc_adCount == $key) $addBtn	=	'';
													if($mgc_adImage !=''){
														if(SERVER){
															if(image_exists(17,$details->fkTournamentsId.'/'.$mgc_adImage))
																$mgc_adImagePath	=	CUSTOM_PRIZE_IMAGE_PATH.$details->fkTournamentsId.'/'.$mgc_adImage;
														}	else {
															if(file_exists(CUSTOM_PRIZE_IMAGE_PATH_REL.$details->fkTournamentsId.'/'.$mgc_adImage))
																$mgc_adImagePath	=	CUSTOM_PRIZE_IMAGE_PATH.$details->fkTournamentsId.'/'.$mgc_adImage;
														}
													} ?>
													<div class="col-sm-12 no-padding base" id="custom_mgc_adRow<?php echo ($key); ?>" clone="<?php echo ($key); ?>">
														<div class="form-group col-xs-6 col-sm-3 col-md-4 col-lg-3">
															<label class="col-xs-12 col-sm-12 col-md-12 text-left no-padding">MGC Ad Name/Title</label>
															<div class="col-xs-12 col-sm-12 col-md-12 no-padding">
																<input type="text"  id="mgc_ad_title<?php echo ($key); ?>" name="mgc_ad_title[]" class="form-control mgc_ad_title" value="<?php if(isset($details->AdCaption)) echo $details->AdCaption;?>">
																<input type="hidden" value="<?php if(isset($details->id)) echo $details->id;?>" name="mgc_ad_title_id[]" class="mgc_ad_title_id w90">
															</div>
														</div>
														<div class="form-group col-xs-6 col-sm-3 col-md-3 col-lg-2">
															<label class="col-xs-12 col-sm-12 col-md-12 no-padding">Image</label>
															<div class="col-xs-12 col-sm-3 col-md-12 no-padding">
																<div class="btn btn-file btn-gray upload_img">
																	Select File <input type="file"  name="custom_mgc_adImage<?php echo ($key); ?>" id="custom_mgc_adImage<?php echo ($key); ?>" onChange="return ajaxAdminFileUploadProcess('custom_mgc_adImage<?php echo ($key); ?>');"  />
																</div>
															</div>

															<div id="custom_mgc_adImage<?php echo ($key); ?>_img" class="display_img pull-left" style="margin-bottom:10px;margin-top:6px;clear:left;">
																<?php  if(isset($mgc_adImagePath) && $mgc_adImagePath != ''){  ?>
																	<a href="<?php echo $mgc_adImagePath; ?>" class="user_photo_pop_up" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $mgc_adImagePath;  ?>" width="75" height="75" alt="Image"/></a>
																	<input type="hidden" value="oldimage" id="custom_mgc_adImage<?php echo $key; ?>_upload" name="custom_mgc_adImage<?php echo $key; ?>_upload">
																<?php  }  ?>
															</div>
															<input type="hidden" value="" id="mgc_ad_tempFile<?php echo ($key); ?>" name="mgc_ad_tempFile[]" class="mgc_ad_tempFile w90">
															<input type="hidden" id="old_mgc_ad<?php echo $key; ?>" name="old_mgc_ad_file[]" value="<?php if(isset($mgc_adImage) && !empty($mgc_adImage)) echo $mgc_adImage;?>">
														</div>
														<!-- <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 form-group clone_action add_button">
															<a onclick="deletePrizeRow(this,'custom_mgc_adRow<?php echo ($key); ?>')" href="javascript:void(0)"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;&nbsp;
															<a id="new_code_0" class="add_new" <?php echo $addBtn; ?> onclick="addPrizeRow(this,'custom_mgc_adRow<?php echo ($key); ?>')" href="javascript:void(0)"><i class="fa text-green fa-lg fa-plus-circle"></i></a>
														</div> -->
													</div>
									<?php 		}
											}else{
												for($ii=0; $ii<8; $ii++){
									 ?>
											<div class="col-sm-12 no-padding base" id="custom_mgc_adRow<?php echo($ii); ?>" clone="0">
												<div class="form-group col-xs-6 col-sm-3 col-md-4 col-lg-3">
													<label class="col-xs-12 col-sm-12 col-md-12 text-left no-padding">MGC Ad Name/Title</label>
													<div class="col-xs-12 col-sm-12 col-md-12 no-padding">
														<input type="text"  id="mgc_ad_title<?php echo($ii); ?>"
														 name="mgc_ad_title[]" class="form-control mgc_ad_title"
														 value=<?php if(count($customAdResult)>$ii && isset($customAdResult[$ii]->AdCaption)) echo $customAdResult[$ii]->AdCaption;?> >
														<input type="hidden" value="" name="mgc_ad_title_id[]" class="mgc_ad_title_id w90">
													</div>
												</div>
												<div class="form-group col-xs-6 col-sm-3 col-md-3 col-lg-2">
													<label class="col-xs-12 col-sm-12 col-md-12 no-padding">Image or video</label>
													<div class="">
														<div class="col-xs-12 col-sm-3 col-md-12 no-padding">
															<div class="btn btn-file btn-gray upload_img ">
																Select File <input type="file"  name="custom_mgc_adImage<?php echo($ii); ?>" id="custom_mgc_adImage<?php echo($ii); ?>" onChange="return ajaxAdminFileUploadProcess('custom_mgc_adImage<?php echo($ii); ?>');"  />
															</div>
														</div>
														<div id="custom_mgc_adImage<?php echo($ii); ?>_img" class="display_img pull-left" style="margin-bottom:10px;margin-top:6px;clear:left;">
															<?php if(count($customAdResult)>$ii){ ?>
																<a href="<?php echo CUSTOM_PRIZE_IMAGE_PATH.$customAdResult[$ii]->fkTournamentsId.'/'.$customAdResult[$ii]->AdImageOrVideoFile; ?>"
																	 class="user_photo_pop_up" title="Click here" alt="Click here" >
																	<img class="img_border"
																	 src="<?php echo CUSTOM_PRIZE_IMAGE_PATH.$customAdResult[$ii]->fkTournamentsId.'/'.$customAdResult[$ii]->AdImageOrVideoFile; ?>"
																	 width="75" height="75" alt="Image"/>
																</a>
															<?php } ?>
														</div>
														<input type="hidden" value="" id="mgc_ad_tempFile<?php echo($ii); ?>" name="mgc_ad_tempFile[]" class="mgc_ad_tempFile w90">
														<input type="hidden" id="old_mgc_ad<?php echo($ii); ?>" name="old_mgc_ad_file[]" value="">
													</div>
												</div>
											</div>

										<?php
									    }
										}
										?>
									</div>
									<?php
									//
									//
									//
									// END OF list of Ads for the mgctv e.g. http://api.tilt.co/v5/mgc/?id=1055
									//
									//
									//
									 ?>


							</div>
						</div>
						<div class="no-margin clear">
							<label class="col-xs-12 col-sm-12" style=" border-bottom: 1px solid #676767;display: inline-block;float: left;margin: 15px;padding: 0;width: auto;">Tournament Type</label>
							<div class="control-group col-xs-12 no-padding">
											<div class="col-sm-12 tournament_opt" id="tournament_options">
												<input type="Checkbox" onChange="return setTournamentOptions2();" name="loc_chk_option" id="loc_chk_option"  value="2" <?php if(isset($locationBased) && $locationBased == 1) echo 'checked'; ?> >&nbsp;&nbsp;<label for="loc_chk_option">Location Based</label>
<input type="Checkbox" onChange="return setTournamentOptions1();"
	name="pin_chk_option" id="pin_chk_option" value="1" <?php if(isset($pinBased) && $pinBased == 1) echo 'checked'; ?> >
	&nbsp;&nbsp;<label for="pin_chk_option">PIN Based</label>
<a id="generatepin" style="display:none"
  href="GeneratePin?tournamentId=<?php echo $tournamentId?>"
	title="Generate Pin" class="fancybox-manual-b fancybox">Generate PIN
</a>
<input type="text" name="tournamentpin" id="tournamentpin"  style="display:none"
  value="<?php echo $tournamentPin; ?>">
												<input type="Checkbox" onClick="return search();"  id="location" name="location" value="1" <?php if(isset($locationRestrict) && $locationRestrict == 1) echo 'checked'; ?> >&nbsp;&nbsp;<label for="location">Location Restricted</label>
												<input type='hidden' id='pin_based_tour' name='pin_based_tour' value="<?php if(isset($pinBased) && $pinBased == 1) echo 1; else echo 0; ?>">
											</div>
										</div>
										<div class="control-group" id="tournament_loc_block" <?php if(isset($locationBased) && $locationBased == 1){ echo ''; $locDisplay = '';}else { $locDisplay = 'style="display:none;"'; echo  $locDisplay; }
										?> >
										<?php $locStateDis = 'style="display:none;"';
										if(isset($locationBased) && $locationBased == 1 && $tourCountryId == $usId) $locStateDis = "";?>
											<div class="col-sm-12 control-group no-padding tournament_loc" id="tournament_location">
													<div class="control-group row-fluid col-xs-4 col-sm-3 no-padding">
														<label class="col-sm-12 text_left">Country</label>
														<div class="col-sm-12 search no-margin-left">
														<select name="country_loc_tour"  class="country form-control" id="country_loc_tour" onChange="tourlocationShow('tour',1);" >
															<option value="">Select</option>
																<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 ){
																	foreach($countryArray as $countryId => $country) {?>
															 <option value="<?php echo $countryId; ?>" <?php if($tourCountryId == $countryId) echo 'Selected';  ?> ><?php echo $country; ?></option>
																<?php 	} }?>
														</select>
														<input type='hidden' class="latitude" id='latitude_tour' name='latitude_tour' value="<?php if(isset($tourLocLat) && !empty($tourLocLat)) echo $tourLocLat;?>">
														<input type='hidden' class="longitude" id='longitude_tour' name='longitude_tour' value="<?php if(isset($tourLocLon) && !empty($tourLocLon)) echo $tourLocLon;?>">
														</div>
													</div>
													<div class="control-group row-fluid col-xs-4 col-sm-3 no-padding">
														<label class="col-sm-12 text_left"> <span id="state_location_tour" <?php echo $locStateDis;?>>State</span></label>
														<div class="col-sm-12 search state no-margin-left" id="tournament_loc_state">
														<select name="state_loc_tour" class="state form-control" id="state_loc_tour"  onchange="tourlocationShow('tour',2);" <?php echo $locStateDis;?>>
														<option value="">Select</option>
														<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
															foreach($usStateArray as $stateId => $state) {  ?>
															<option value="<?php echo $stateId; ?>" <?php if($tourStateId == $stateId) echo 'Selected';  ?>><?php echo $state; ?></option>
														<?php 	} ?>
														</select>
														</div>
													</div>
													<div class="control-group row-fluid col-xs-4 col-sm-5 no-padding">
														<label class="col-sm-12 text_left"><span id="location_state_tour" <?php echo $locDisplay;?>>Location</span></label>
														<div class="location col-sm-12 search no-margin-left" >
															<input type='search' class="locationsearch form-control" id='locationsearch_tour' name='locationsearch_tour' value="<?php if(isset($tourLocation) && !empty($tourLocation)) echo $tourLocation;?>" onKeyPress="autoCompleteLocation('tour')" <?php echo $locDisplay;?> autocomplete="off">
														</div>
													</div>

												<div class="clear"></div>
											</div>
											<div class="control-group row-fluid loc_note" >

												<div class="col-sm-12"><p id="loc_based_note" style="padding-top:12px;"><small>If you set the exact address then users will able to play only if they are in the perimeter of <span id="perimeter_value"></span>m from this address.</small></p></div>
											</div>
										</div>


										<div class="control-group row-fluid" >
								<div class="col-sm-12">
									<p class="user_txt1" <?php if(isset($pinBased) && $pinBased == 1) echo ''; else echo 'style="display:none;margin-bottom:10px;"'; ?>  id="tournament_pin_block"><small>User need to enter correct PIN to join and play the tournament. List of PINs will be generated and sent to your email address</small></p>
									<div id='share_search' <?php if(isset($locationRestrict) && $locationRestrict == 1) ; else echo 'style="display:none;"'; ?>>
										<div class="head_clone_block">
											<div class="row-fluid col-xs-12 col-sm-12 no-padding">
												<label class="col-xs-1 col-sm-1 no-padding loc_plus_minus">&nbsp;</label>
												<label class="text_left col-xs-3 col-sm-3">Country</label>
												<label class="text_left col-xs-3 col-sm-3" id="state_location"  style="display:none;">State</label>
												<label class="text_left col-xs-4 col-sm-3" id="location_state" style="display:none;">Location</label>
											</div>
											<div class="clone loc_based" id="location_clone" clone="1" style="display:none;margin-bottom:10px;clear:both;padding-top:10px;">

												<div class="col-xs-1 col-sm-1 text_left no-padding loc_plus_minus" style="clear:both;">
													<a href="javascript:void(0)" class="locminus" onClick="manageLocation(this,'2')" id="minus_clone" style="display:none;"><i class="fa fa-lg text-red fa-minus-circle"></i></a>&nbsp;&nbsp;
													<a href="javascript:void(0)" class="locplus" onClick="manageLocation(this,'1')" id="plus_clone"><i class="fa fa-lg  text-green fa-plus-circle"></i></a>
												</div>
												<div class="col-xs-3 col-sm-3 text_left">
													<select class="col-sm-12 country form-control" name="country_clone" tabindex="10" id="country_loc_clone" onChange="locationShow('1',1);">
														<option value="">Select</option>
														<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 ){
																		foreach($countryArray as $countryId => $country) {
																			?>
																		<option value="<?php echo $countryId; ?>"><?php echo $country; ?></option>
																	<?php 	} }?>
													</select>
												</div>
												<div class="col-xs-3 col-sm-3 text_left">
													<div class="state" id="col_loc_clone_3" style="display:none;">
														<select name="state_clone" tabindex="10" class="state col-sm-12 form-control" id="state_loc_clone" style="display:none;" onChange="locationShow('1',2);">
															<option value="">Select</option>
															<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
																			foreach($usStateArray as $stateId => $state) {  ?>
																			<option value="<?php echo $stateId; ?>"><?php echo $state; ?></option>
																		<?php 	} ?>
														</select>
														<input type='hidden' class="latitude" id='latitude_clone' name='latitude_clone[]' value="">
														<input type='hidden' class="longitude" id='longitude_clone' name='longitude_clone[]' value="">
													</div>
												</div>
												<div class="col-xs-4 col-sm-5 text_left">
													<div class="location col-sm-12 no-padding" id="col_loc_clone_4" style="display:none;">
														&nbsp;&nbsp;<input type='search' class="locationsearch col-sm-12 form-control" id='locationsearch_clone' name='locationsearch_clone' value="" onKeyPress="autoCompleteLocation(1)" autocomplete="off">
													</div>
												</div>
											</div>
										</div>
										<div class="control-group row-fluid" id="RestrictedLocationContent">
										<?php if(isset($locationRestrict) && $locationRestrict == 1 && isset($resLocRes) && is_array($resLocRes) && count($resLocRes)>0){
											$totalCount = count($resLocRes);
											foreach($resLocRes as $lrKey=>$lrValues){
												$counts 	 = ($lrKey+1);
												$addMore = $display = 'style="display:none"';
												if($totalCount == $counts) $addMore = "";
												if($lrValues->fkCountriesId == $usId){ $display = ""; $stateLocationFlag = 1;}
										?>
											<div id="location_<?php echo $counts;?>" class="clone loc_based" clone="<?php echo $counts;?>" style="clear:both;padding-top:10px;">
												<div class="col-xs-1 col-sm-1 text_left no-padding loc_plus_minus" style="clear:both;">
													<a href="javascript:void(0)" class="locminus text-red" onClick="manageLocation(this,'2')" id="minus_<?php echo $counts;?>" style=""><i class="fa fa-lg fa-minus-circle"></i></a>&nbsp;&nbsp;
													<a href="javascript:void(0)" class="locplus text-green" onClick="manageLocation(this,'1')" id="plus_<?php echo $counts;?>" <?php echo $addMore;?>><i class="fa fa-lg fa-plus-circle"></i></a>
												</div>
												<div class="col-xs-3 col-sm-3 text_left">
													<select name="countryLocation[]" tabindex="10" class="country col-sm-12 form-control" id="country_loc_<?php echo $counts;?>" onChange="locationShow('<?php echo $counts;?>',1);">
														<option value="">Select</option>
														<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 ){
																		foreach($countryArray as $countryId => $country) {
																			?>
																		<option value="<?php echo $countryId; ?>" <?php if($lrValues->fkCountriesId == $countryId) echo 'Selected';  ?>><?php echo $country; ?></option>
														<?php 	} }?>
													</select>
												</div>
												<div class="col-xs-3 col-sm-3 text_left">
													<div class="state" id="col_loc_<?php echo $counts;?>_3" <?php echo $display;?> >
														<select name="stateLocation[]" tabindex="10" class="state col-sm-12 form-control" id="state_loc_<?php echo $counts;?>" <?php echo $display;?> onChange="locationShow('<?php echo $counts;?>',2);">
															<option value="">Select</option>
															<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
																			foreach($usStateArray as $stateId => $state) {  ?>
																			<option value="<?php echo $stateId; ?>" <?php if($lrValues->fkStatesId == $stateId) echo 'Selected';  ?>><?php echo $state; ?></option>
																		<?php 	} ?>
														</select>
														<input type='hidden' name='locationedit[]' value="<?php if($lrValues->id !='') echo $lrValues->id; ?>">
														<input type='hidden' class="latitude" id='latitude_<?php echo $counts;?>' name='latitude[]' value="<?php if($lrValues->Latitude !='') echo $lrValues->Latitude; ?>">
														<input type='hidden' class="longitude" id='longitude_<?php echo $counts;?>' name='longitude[]' value="<?php if($lrValues->Longitude !='') echo $lrValues->Longitude; ?>">
													</div>
												</div>
												<div class="col-xs-4 col-sm-5 text_left">
													<div class="location col-sm-12 no-padding" id="col_loc_<?php echo $counts;?>_4" <?php echo $display;?>>
														<input type='search' class="locationsearch col-sm-12 form-control" id='locationsearch_<?php echo $counts;?>' name='locationsearch[]' value="<?php if($lrValues->LocationValue !='') echo $lrValues->LocationValue; ?>" onKeyPress="autoCompleteLocation(<?php echo $counts;?>)" autocomplete="off">
													</div>
												</div>
											</div>
											<?php } ?>
											<input type="hidden" name="totLoc" id="totLoc" value="<?php echo count($resLocRes);?>">
											<?php } else { ?>
											<div id="location_1" class="clone loc_based" clone="1" style="clear:both;padding-top:10px;">
												<div class="col-xs-1 col-sm-1 text_left no-padding loc_plus_minus" style="clear:both;">
													<a href="javascript:void(0)" class="locminus text-red" onClick="manageLocation(this,'2')" id="minus_1" style=""><i class="fa fa-lg fa-minus-circle"></i></a>&nbsp;&nbsp;
													<a href="javascript:void(0)" class="locplus text-green" onClick="manageLocation(this,'1')" id="plus_1"><i class="fa fa-lg fa-plus-circle"></i></a>
												</div>
												<div class="col-xs-3 col-sm-3 text_left">
													<select name="countryLocation[]" tabindex="10" class="country col-sm-12 form-control" id="country_loc_1" onChange="locationShow('1',1);">
														<option value="">Select</option>
														<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 ){
															foreach($countryArray as $countryId => $country) { ?>
														<option value="<?php echo $countryId; ?>"><?php echo $country; ?></option>
														<?php 	} }?>
													</select>
												</div>
												<div class="col-xs-3 col-sm-3 text_left">
													<div class="state" id="col_loc_1_3" style="display:none;">
														<select name="stateLocation[]" tabindex="10" class="state col-sm-12 form-control" id="state_loc_1" style="display:none;" onChange="locationShow('1',2);">
															<option value="">Select</option>
															<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
																			foreach($usStateArray as $stateId => $state) {  ?>
																			<option value="<?php echo $stateId; ?>"><?php echo $state; ?></option>
																		<?php 	} ?>
														</select>
														<input type='hidden' class="latitude" id='latitude_1' name='latitude[]' value="">
														<input type='hidden' class="longitude" id='longitude_1' name='longitude[]' value="">
													</div>
												</div>
												<div class="col-xs-4 col-sm-5 text_left">
													<div class="location col-sm-12 no-padding" id="col_loc_1_4" style="display:none;">
														<input type='search' class="locationsearch col-sm-12 form-control" id='locationsearch_1' name='locationsearch[]' value="" onKeyPress="autoCompleteLocation(1)" autocomplete="off">
													</div>
												</div>
											</div>
											<input type="hidden" name="totLoc" id="totLoc" value="1">
											<?php } ?>
										</div>
									</div>
								</div>
								<div id='location_error'></div>
										</div>
							</div>
						<div class="no-margin clear add_coupon">
							<label class="col-sm-12" style=" border-bottom: 1px solid #676767;display: inline-block;float: left;margin: 15px;padding: 0;width: auto;">Add Participation Coupon To</label>
							<div class="col-sm-12 no-padding">

								<div class="col-xs-12 no-padding  form-group">
								<label class="col-xs-6 col-md-5 col-sm-4 text-right">Coupon Code/Title</label>
								<div class="col-xs-6 col-md-6 col-sm-8">
									<input type="text" class="form-control" name="coupon_code" value="<?php if(isset($couponCode) && $couponCode !='') echo htmlentities($couponCode);?>"  maxlength ="80" id="coupon_title">
								</div>
								</div>
								<div class="col-xs-12 no-padding  form-group">
								<label class="col-xs-6 col-md-5 col-sm-4 text-right">Limit</label>
								<div class="col-xs-6 col-md-3 col-sm-5">
									<input class="form-control coupon_title" type="text" value="<?php if(isset($couponLimit) && $couponLimit !='') echo $couponLimit; ?>"  name="coupon_limit" id="coupon_limit" onpaste="return false"  onkeypress="return isNumberKey(event);"  maxlength ="8" ondrop="event.dataTransfer.dropEffect='none';event.stopPropagation(); event.preventDefault();">
								</div>
								</div>
								<div class="col-xs-12 no-padding form-group">
								<label class="col-xs-6 col-md-5 col-sm-4 text-right">Description</label>
								<div class="col-xs-6 col-md-5 col-sm-8">
									<textarea  name="coupon_text" id="coupon_text" class="form-control" style="" rows="4" cols="100" maxlength ="250" ><?php if(isset($couponAdlink) && $couponAdlink !='') echo $couponAdlink;?></textarea>
								</div>
								</div>

								<div class="col-xs-12 no-padding form-group select_file">
									<label class="col-xs-6 col-md-5 col-sm-4 text-right">Image</label>
									<div class="col-xs-3 col-md-3 col-sm-3 col-lg-2">
										<div class="btn btn-file btn-gray ">
											Select File <input type="file"  name="coupon_image" id="coupon_image" onChange="return ajaxAdminFileUploadProcess('coupon_image');"  />
											<input type="hidden" value="<?php  if(isset($couponImage) && ! empty($couponImage)) { echo $couponImage; }  ?>" id="empty_coupon_image" name="empty_coupon_image">
										</div>
									</div>
									<div class="fakefile_photo pull-left" id="youtube_photo">
										<div id="coupon_image_img" class="image_img">
											<?php  if(isset($coupon_image_path) && $coupon_image_path != ''){  ?>
												<a href="<?php echo $coupon_image_path; ?>" class="user_photo_pop_up" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $coupon_image_path;  ?>" width="75" height="75" alt="Image"/></a>
											<?php  }  ?>
										</div>
									</div>
								</div>



								</div>
								<div class="col-xs-12 no-padding  col-sm-12 form-group">
									<label class="col-xs-6 col-md-5 col-sm-4 text-right">Start Date</label>
									<div class="col-xs-6 col-md-2 col-sm-3">
										<input type="text" value="<?php if(isset($couponStartdate) && $couponStartdate !='0000-00-00 00:00:00') echo $couponStartdate;?>" name="coupon_startdate" class="form-control inline"  id="coupon_startdate"  autocomplete="off" onKeyPress="return dateField(event);">
									</div>
								</div>
								<div class="col-xs-12 no-padding  col-sm-12 form-group">
									<label class="col-xs-6 col-md-5 col-sm-4 text-right">End Date</label>
									<div class="col-xs-6 col-md-2 col-sm-3">
										<input type="text" value="<?php if(isset($couponEnddate) && $couponEnddate !='0000-00-00 00:00:00') echo $couponEnddate;?>" name="coupon_enddate"  class="form-control inline"  id="coupon_enddate" onKeyPress="return dateField(event);"  autocomplete="off">
									</div>
									</div>
									<input type="Hidden" name="name_coupon_image" id="name_coupon_image" value="<?php  if(isset($couponImage) && $couponImage != '') { echo $couponImage; }  ?>" />
									<input type="hidden" name="coupon_code_id" value="<?php if(isset($couponCodeId) && $couponCodeId !='') echo $couponCodeId;?>" >
								</div>
			<hr>
						<div class="banner_tab">
						<div class="col-xs-12">
							<label class="no-padding" style=" border-bottom: 1px solid #676767;display: inline-block;float: left;margin: 15px 0px;padding: 0;width: auto;">Banner Ad</label>
						</div>
						<div class="col-sm-12 no-padding no-padding clear">

							<div class="form-group">
								<label class="col-xs-6 col-sm-4 col-md-5 text-right">Banner Ad (320X66)</label>
								<div class="col-xs-6 col-sm-8 col-md-6">
									<div class="col-xs-12 col-md-4 col-sm-6 no-padding">
										<select id="banner_type" class="form-control" name="banner_type" onChange="bannerHideShow()">
										<option value="">Select</option>
										<option value="1" <?php if(isset($bannertextSelected)) echo $bannertextSelected;?>>Text</option>
										<option value="2" <?php if(isset($bannerimageSelected)) echo $bannerimageSelected;?> >Image/Video</option>
									</select>
									</div>
								</div>
							</div>
							<div class="form-group" id="banner_text_div" <?php if(isset($bannertextDisp)) echo $bannertextDisp; ?>>
								<label class="col-xs-6 col-sm-4 col-md-5 text-right">Text</label>
								<div class="col-xs-6 col-sm-8 col-md-5">
									<textarea  name="banner_text" id="banner_text" class="form-control" rows="4" cols="130"><?php if(isset($banner_text) && $banner_text !='') echo $banner_text;?></textarea>
									<input type="hidden" value="" name="banner_text_id" class="banner_text_id">
								</div>
							</div>
							<div class="form-group" id="banner_image_div" <?php if(isset($bannerimageDisp)) echo $bannerimageDisp; ?> >
								<div  align="left" >
									<div class="col-sm-12 no-padding form-group">
										<label class="col-xs-6 col-sm-4 col-md-5 text-right">Text/Link</label>
										<div class="col-xs-6 col-md-5 col-sm-8">
											<input type="text" class="form-control"  id="banner_link"  name="banner_link" value="<?php if(isset($banner_link) && $banner_link !='') echo htmlentities($banner_link);?>" >

										</div>

									</div>
									<div class="col-sm-12 no-padding ">
										<div class="fakefile_photo col-sm-12 text-left no-padding form-group" >
											<label class="col-xs-6 col-sm-4 col-md-5 text-right">Image / Video</label>
											<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2">
												<div class="btn btn-file btn-gray ">Select File <input type="file"  name="banner_image" id="banner_image" onChange="return ajaxImageVideoUploadProcess(this.value,'banner_image');"  />
												</div>
											</div>
												<div id="banner_image_img" class="pull-left">
													<?php  if(isset($banner_file_path) && $banner_file_path != ''){  ?>
														<a href="<?php echo $banner_file_path; ?>" class="<?php if(isset($bannerVideo) && $bannerVideo == 1) echo "banner_video"; else echo "image_popup"; ?>" style="color:#676767" title="Click here" alt="Click here" ><?php echo $oldbannerImage; ?>
														</a>
													<?php  }else{ echo $oldbannerImage; }  ?>
												</div>
												<span class="error" for="empty_banner_image" generated="true" style="display: none">Banner Image is required</span>
											</div>
										</div>
									</div>
								</div>
								<?php  if(isset($_POST['banner_image_upload']) && $_POST['banner_image_upload'] != ''){  ?><input type="Hidden" name="game_photo_upload" id="game_photo_upload" value="<?php  echo $_POST['banner_image_upload'];  ?>"><?php  }  ?>
								<input type="Hidden" name="empty_banner_image" id="empty_banner_image" required="required" value="<?php  if(isset($bannerImage) && $bannerImage != '') { echo $bannerImage; }  ?>" />
								<input type="Hidden" name="name_banner_image" id="name_banner_image" value="<?php  if(isset($bannerImage) && $bannerImage != '') { echo $bannerImage; }  ?>" />
								<input type="Hidden" name="banner_image_id" id="name_banner_image_id" value="<?php  if(isset($bannerImageId) && $bannerImageId != '') { echo $bannerImageId; }  ?>" />

							</div>
							</div>
<hr>
							<h3>MGC background images</h3>
							<div class="form-group">
								<div class="col-xs-12 col-sm-12 root no-padding" id ="mgc_background_images" style="margin-top:15px">
								<?php 
								if(false && isset($customMgcBackgroundImageResult) && is_array($customMgcBackgroundImageResult) && count($customMgcBackgroundImageResult)>0){
										$mgc_adCount	=	count($customMgcBackgroundImageResult) - 1;
										foreach($customMgcBackgroundImageResult as $key=>$details){
											$mgc_backgroundImage	=	$details->backgroundimage;
											$mgc_backgroundImagePath	=	'';
											if($mgc_backgroundImage !=''){
												if(SERVER){
													if(image_exists(17,$details->fkTournamentsId.'/'.$mgc_backgroundImage))
														$mgc_backgroundImagePath	=	CUSTOM_PRIZE_IMAGE_PATH.$details->fkTournamentsId.'/'.$mgc_backgroundImage;
												}	else {
													if(file_exists(CUSTOM_PRIZE_IMAGE_PATH_REL.$details->fkTournamentsId.'/'.$mgc_backgroundImage))
														$mgc_backgroundImagePath	=	CUSTOM_PRIZE_IMAGE_PATH.$details->fkTournamentsId.'/'.$mgc_backgroundImage;
												}
											} ?>
											<div class="col-sm-12 no-padding base" id="custom_mgc_backgroundImageRow<?php echo ($key); ?>" clone="<?php echo ($key); ?>">
												<div class="form-group col-xs-6 col-sm-3 col-md-4 col-lg-3">
													<label class="col-xs-12 col-sm-12 col-md-12 text-left no-padding">MGC background image delay to next image</label>
													<div class="col-xs-12 col-sm-12 col-md-12 no-padding">
														<input type="text"  id="mgc_backgroundimage_delay<?php echo ($key); ?>" name="mgc_backgroundimage_delay[]" class="form-control mgc_backgroundimage_delay" value="<?php if(isset($details->delaytonextimage)) echo $details->delaytonextimage;?>">
														<input type="hidden" value="<?php if(isset($details->id)) echo $details->id;?>" name="mgc_backgroundimage_id[]" class="mgc_backgroundimage_id w90">
													</div>
												</div>
												<div class="form-group col-xs-6 col-sm-3 col-md-3 col-lg-2">
													<label class="col-xs-12 col-sm-12 col-md-12 no-padding">Image</label>
													<div class="col-xs-12 col-sm-3 col-md-12 no-padding">
														<div class="btn btn-file btn-gray upload_img">
															Select File <input type="file"  name="custom_mgc_backgroundImage<?php echo ($key); ?>" id="custom_mgc_backgroundImage<?php echo ($key); ?>" onChange="return ajaxAdminFileUploadProcess('custom_mgc_backgroundImage<?php echo ($key); ?>');"  />
														</div>
													</div>
													<div id="custom_mgc_backgroundImage<?php echo ($key); ?>_img" class="display_img pull-left" style="margin-bottom:10px;margin-top:6px;clear:left;">
														<?php  if(isset($mgc_backgroundImagePath) && $mgc_backgroundImagePath != ''){  ?>
															<a href="<?php echo $mgc_backgroundImagePath; ?>" class="user_photo_pop_up" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $mgc_backgroundImagePath;  ?>" width="75" height="75" alt="Image"/></a>
															<input type="hidden" value="oldimage" id="custom_mgc_backgroundImage<?php echo $key; ?>_upload" name="custom_mgc_backgroundImage<?php echo $key; ?>_upload">
														<?php  }  ?>
													</div>
													<input type="hidden" value="" id="mgc_ad_tempFile<?php echo ($key); ?>" name="mgc_ad_tempFile[]" class="mgc_ad_tempFile w90">
													<input type="hidden" id="old_mgc_ad<?php echo $key; ?>" name="old_mgc_ad_file[]" value="<?php if(isset($mgc_backgroundImage) && !empty($mgc_backgroundImage)) echo $mgc_backgroundImage;?>">
												</div>
											</div>
								<?php
									}
								}else{
									echo '<!-- customMgcBackgroundImageResult amihdebug--';
									echo var_export($customMgcBackgroundImageResult, true);
									echo '-- customMgcBackgroundImageResult amihdebug-->';
									for($ii=0; $ii<8; $ii++){
								?>
									<div class="col-sm-12 no-padding base" id="custom_mgc_backgroundImageRow<?php echo($ii); ?>" clone="0">
										<div class="form-group col-xs-6 col-sm-3 col-md-4 col-lg-3">
											<label class="col-xs-12 col-sm-12 col-md-12 text-left no-padding">MGC background image delay to next image</label>
											<div class="col-xs-12 col-sm-12 col-md-12 no-padding">
												<input type="text"  id="mgc_backgroundimage_delay<?php echo($ii); ?>"
												 name="mgc_backgroundimage_delay[]" class="form-control mgc_backgroundimage_delay"
												 value=<?php if(count($customMgcBackgroundImageResult)>$ii && isset($customMgcBackgroundImageResult[$ii]->delaytonextimage)) echo $customMgcBackgroundImageResult[$ii]->delaytonextimage;?> >
												<input type="hidden" value="" name="mgc_backgroundimage_id[]" class="mgc_backgroundimage_id w90">
											</div>
										</div>
										<div class="form-group col-xs-6 col-sm-3 col-md-3 col-lg-2">
											<label class="col-xs-12 col-sm-12 col-md-12 no-padding">Image</label>
											<div class="">
												<div class="col-xs-12 col-sm-3 col-md-12 no-padding">
													<div class="btn btn-file btn-gray upload_img ">
														Select File <input type="file"  name="custom_mgc_backgroundImage<?php echo($ii); ?>" id="custom_mgc_backgroundImage<?php echo($ii); ?>" onChange="return ajaxAdminFileUploadProcess('custom_mgc_backgroundImage<?php echo($ii); ?>');"  />
													</div>
												</div>
												<div id="custom_mgc_backgroundImage<?php echo($ii); ?>_img" class="display_img pull-left" style="margin-bottom:10px;margin-top:6px;clear:left;">
													<?php if(count($customMgcBackgroundImageResult)>$ii){ ?>
														<a href="<?php echo CUSTOM_PRIZE_IMAGE_PATH.$customMgcBackgroundImageResult[$ii]->fkTournamentsId.'/'.$customMgcBackgroundImageResult[$ii]->backgroundimage; ?>"
															 class="user_photo_pop_up" title="Click here" alt="Click here" >
															<img class="img_border"
															 src="<?php echo CUSTOM_PRIZE_IMAGE_PATH.$customMgcBackgroundImageResult[$ii]->fkTournamentsId.'/'.$customMgcBackgroundImageResult[$ii]->backgroundimage; ?>"
															 width="75" height="75" alt="Image"/>
														</a>
													<?php } ?>
												</div>
												<input type="hidden" value="" id="mgc_ad_tempFile<?php echo($ii); ?>" name="mgc_ad_tempFile[]" class="mgc_ad_tempFile w90">
												<input type="hidden" id="old_mgc_ad<?php echo($ii); ?>" name="old_mgc_ad_file[]" value="">
											</div>
										</div>
									</div>

								<?php
							    	}
								}
								?>
							</div>
							<?php
							//
							// END OF mgc background images for the mgctv e.g. http://api.tilt.co/v5/mgc/?id=1055
							//
							?>
							</div>
						</div>
<hr>

							<div class="youtube_tab">
							<div class="col-xs-12">
								<label class="no-padding col-xs-12" style=" border-bottom: 1px solid #676767;display: 	inline-block;float: left;margin: 15px 0px;padding: 0;width: auto;">Youtube Link</label>
							</div>
							<div class="col-sm-12 no-padding">

								<div class="form-group clear no-padding col-sm-12">
									<label class="col-xs-6 col-sm-4 col-md-5 text-right">Youtube Link</label>
									<div class="col-xs-6 col-sm-8 col-md-6">
										<div class="col-md-4 col-sm-6 no-padding">
											<select id="youtube_type" class="form-control" name="youtube_type" onChange="youtubeHideShow()">
												<option value="">Select</option>
												<option value="1"  <?php if(isset($youtubeurlSelected)) echo $youtubeurlSelected;?>>URL</option>
												<option value="2" <?php if(isset($youtubecodeSelected)) echo $youtubecodeSelected;?> >Embedded Code</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-sm-12 no-padding">
									<div  id="youtube_text" class="form-group" <?php if(isset($youtubeurlDisp)) echo $youtubeurlDisp;?>>
										<label class="col-xs-6 col-sm-4 col-md-5 text-right">URL</label>
										<div class="col-xs-6 col-sm-8 col-md-5">
											<input type="text" class="form-control" id="youtube_link" name="youtube_link" value="<?php if(isset($youtubeLink) && $youtubeLink !='') echo htmlentities($youtubeLink);?>" >
										</div>
									</div>
									<div id="youtube_code" class="form-group" valign="top" <?php if(isset($youtubecodeDisp)) echo $youtubecodeDisp;?> >
										<label class="col-xs-6 col-sm-4 col-md-5 text-right">Embedded Code</label>
										<div class="col-xs-6 col-sm-8 col-md-5">
											<textarea  name="youtube_code" id="youtube_codes" class="form-control"  rows="4" cols="60"><?php if(isset($youtube_text)) echo $youtube_text;?></textarea>
										</div>
									</div>
								</div>
								<div class="col-sm-12 no-padding youtube_image_img" <?php echo $youtubeImageDisp; ?>>
									<div class="fakefile_photo clear form-group" >
											<label class="col-xs-6 col-sm-4 col-md-5 text-right">Image</label>
											<div class="col-xs-3 col-md-3 col-sm-3 col-lg-2">
												<div class="no-padding">
													<div class="btn btn-file btn-gray ">
														Select File <input type="file"  name="youtube_image" id="youtube_image" onChange="return ajaxAdminFileUploadProcess('youtube_image');"  />
													</div>
												</div>
										</div>

										<div id="youtube_image_img" class="pull-left image_img">
											<?php  if(isset($youtube_image_path) && $youtube_image_path != ''){  ?>
												<a href="<?php echo $youtube_image_path; ?>" class="user_photo_pop_up" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $youtube_image_path;  ?>" width="75" height="75" alt="Image"/></a>

											<?php  }  ?>
										</div>
									</div>
									<input type="Hidden" name="name_youtube_image" id="name_youtube_image" value="<?php  if(isset($youtubeImage) && $youtubeImage != '') { echo $youtubeImage; }  ?>" />
									<input type="Hidden" name="empty_youtube_image" id="empty_youtube_image" value="<?php  if(isset($youtubeImage) && $youtubeImage != '') { echo '1'; }  ?>" />
									<input type="hidden"  name="youtube_link_id" value="<?php if(isset($youtubeLinkId) && $youtubeLinkId !='') echo $youtubeLinkId;?>" >

								</div>
								<div align="center">
									<button class="btn btn-green space" type="submit" id="save_button" name="tournamentLab" value="save" onClick="return validateLab();" title="Save">Save </button>
								</div>
							</div>
							</div>
					</form>
				</div>
			</div>
		</div>
	</div>

		<?php }else { ?>
		<div class="row">
			<div align="center"><br><br><br>No game created in your profile click here to <a href="AddGame"> Add new game</a>  to create tournament</div>
		</div>
		<?php } ?>
<?php footerLinks(); commonFooter(); ?>
<script>
<?php if(isset($_SESSION['tilt_developer_amount']) && $_SESSION['tilt_developer_amount'] <= 0  && isset($_SESSION['tilt_developer_coins']) &&  $_SESSION['tilt_developer_coins'] <= 0 &&  !isset($_GET['editId'])) {  ?>
	alert("You don't have enough TiLT$/ Virtual Coins to create tournament");
<?php } ?>
$('.user_photo_pop_up').fancybox({helpers: { title: null}, maxWidth: "70%", maxHeight: "60%"});

$(function(){
	var dt = new Date();
	var time = dt.getHours() + ":" + dt.getMinutes();
	$("#minTime").val(time);

	var logic = function( currentDateTime ){
		var starting_time	=	$('#start_time').val();
		var ending_time		=	$('#end_time').val();
		start_dArr = starting_time.split(" ");
		start_DateArr = start_dArr[0];
		start_TimeArr = start_dArr[1];
		end_dArr = ending_time.split(" ");
		end_DateArr = end_dArr[0];
		end_TimeArr = end_dArr[1];
		if(start_DateArr == end_DateArr)
			tme_new	=	start_TimeArr;
		else
			tme_new		=	false;

		if(start_DateArr != '' && start_TimeArr != ''){
			this.setOptions({
				minDate:start_DateArr,
				minTime:tme_new
			});
		}else
			this.setOptions({
				Date:start_DateArr
			});
	};
	var logic1 = function( currentDateTime ){
		var starting_time	=	$('#start_time_hidden').val();
		if(starting_time && starting_time !=''){
			start_dArr = starting_time.split(" ");
			start_DateArr = start_dArr[0];
			start_TimeArr = start_dArr[1];
			if(start_DateArr != ''){
				this.setOptions({
					minDate:start_DateArr
				});
			}
		}
		if(!$('#elimination').is(':checked')){
			var ending_time		=	$('#end_time_hidden').val();
			if(ending_time && ending_time !=''){
				end_dArr = ending_time.split(" ");
				end_DateArr = end_dArr[0];
				end_TimeArr = end_dArr[1];
				if(end_DateArr != ''){
					this.setOptions({
						maxDate:end_DateArr
					});
				}else{
					this.setOptions({
						maxDate:false
					});
				}
			}
		}
	};
	var logic2 = function( currentDateTime ){
		var starting_time	=	$('#coupon_startdate').val();
		if(starting_time && starting_time !=''){
			start_dArr = starting_time.split(" ");
			start_DateArr = start_dArr[0];
			start_TimeArr = start_dArr[1];
			if(start_DateArr != ''){
				this.setOptions({
					minDate:start_DateArr
				});
			}
		}
		if(!$('#elimination').is(':checked')){
			var ending_time		=	$('#end_time_hidden').val();
			if(ending_time && ending_time !=''){
				end_dArr = ending_time.split(" ");
				end_DateArr = end_dArr[0];
				end_TimeArr = end_dArr[1];
				this.setOptions({
						maxDate:end_DateArr
				});
			}
		}else{
			this.setOptions({
					maxDate:false
			});
		}

	};

	$('#start_time').datetimepicker({
		dateFormat: 'm/d/Y',
		timeFormat: 'hh:mm z',
  		minDate:0,
		minTime:0,
		onChangeDateTime:function(current_time,$input){
			if(current_time){
				var locDate = new Date();
				var utc = locDate.getTime() + (locDate.getTimezoneOffset() * 60000);
				var nyTstamp = utc - (3600000*(USOFFSET));
				var newyorkDate = new Date(nyTstamp);
				var d = new Date(nyTstamp);
				if((newyorkDate.getFullYear() == current_time.getFullYear()) && (newyorkDate.getMonth() == current_time.getMonth()) && (newyorkDate.getDate() == current_time.getDate())){
					this.setOptions({ minTime:0 });
				}
				else{
					this.setOptions({ minTime:'00:00'});
				}
			}
		}
 	});

	$('#end_time').datetimepicker({
		dateFormat: 'm/d/Y',
		timeFormat: 'hh:mm z',
		onChangeDateTime:logic,
		onShow:logic,
	});

	$('#coupon_startdate').datetimepicker({
  		format:'m/d/Y',
		onChangeDateTime:logic1,
		onShow:logic1,
		timepicker:false,
		scrollInput:false
 	});

	$('#coupon_enddate').datetimepicker({
  		format:'m/d/Y',
		onChangeDateTime:logic2,
		onShow:logic2,
		timepicker:false,
		scrollInput:false
 	});

});

<?php
 if(isset($FeeType) && !empty($FeeType)){ ?> show_pay();<?php }
 if(isset($prizeType) && $prizeType == 2){ ?>
	setPrizeType('tilt');
	<?php if($FeeType == 2) { ?> setFeeType('tilt');  <?php } ?>
	$("#pay_vcoin").attr("checked",false);
	$("#pay_tilt").attr("checked",true);
	$("#coin").attr("checked",false);
	$("#custom").attr("checked",false);
	$("#tilt").attr("checked",true);
	<?php }
else if(isset($prizeType) && $prizeType == 3){ ?>
	setPrizeType('vcoin');
	<?php if($FeeType == 2) { ?> setFeeType('vcoin');  <?php } ?>
	$("#pay_tilt").attr("checked",false);
	$("#pay_vcoin").attr("checked",true);
	$("#custom").attr("checked",false);
	$("#tilt").attr("checked",false);
	$("#coin").attr("checked",true);
	<?php }
else if(isset($prizeType) && $prizeType == 4){ ?>
	setPrizeType('custom');
	$("#pay_vcoin").attr("checked",false);
	$("#pay_tilt").attr("checked",true);
	$("#coin").attr("checked",false);
	$("#tilt").attr("checked",false);
	$("#custom").attr("checked",true);
<?php } ?>

function chkTournamentType(){
	if($('#elimination').is(':checked')){
		$("#can_we_start_div").show();
		$("#can_we_start").attr('required', true);
	} else {
		$("#can_we_start_div").hide();
		$("#can_we_start").attr('required', false);
	}
}
// function chkrequiresdownloadpartnersapp(){
// 	if($('#requiresdownloadpartnersapp').is(':checked')){
// 		$(".requiresdownloadpartnersappgroup").show();
// 	} else {
// 		$(".requiresdownloadpartnersappgroup").hide();
// 	}
// }

function show_pay(){
	$("#tilt").attr("disabled",false);
	$("#coin").attr("disabled",false);
	$("#custom").attr("disabled",false);
	$("#pay_tilt").attr("checked",false);
	$("#pay_vcoin").attr("checked",false);

	if($('#entry_type').is(':checked')){
		$("#pay_tilt").attr('required', true);
		$("#entry_fee").attr('required', true);
		$("#prize_coin").attr("readonly",true);
		$('#pay_type').show();
		$('#fee_pay_type_block').show();
		$("#tilt").attr("checked",false);
		$("#coin").attr("checked",false);
		$("#custom").attr("checked",false);
		$('#prize_custom').hide();
		$('#prize_tiltCoin').show();
		calcPrize();
	}else{
		$("#pay_tilt").attr('required', false);
		$("#entry_fee").attr('required', false);
		$("#prize_coin").removeAttr("readonly");
		$('#pay_type').hide();
		$('#fee_pay_type_block').hide();
		$("#tilt").attr("checked",false);
		$("#coin").attr("checked",false);
		$("#custom").attr("checked",false);
		$('#prize_custom').hide();
		$('#prize_tiltCoin').show();
	}
}

function setFeeType(type){
	$('#pay_type').show();
	if(type == 'tilt'){
		$("#tilt").attr("disabled",false);
		$("#coin").attr("disabled",true);
		$("#custom").attr("disabled",false);
		$("#vcoin").attr("checked",false);
		$("#tilt").attr("checked",true);
		$(".entry_fee_text").html("TiLT$");
		setPrizeType('tilt');
	}
	else if(type == 'vcoin'){
		$("#tilt").attr("disabled",true);
		$("#coin").attr("disabled",false);
		$("#custom").attr("disabled",true);
		$("#coin").attr("checked",true);
		$("#tilt").attr("checked",false);
		$(".entry_fee_text").html("Virtual Coins");
		setPrizeType('vcoin');
	}
}

function setPrizeType(prizeType){
	if(prizeType == 'tilt'){
		$("#prize_tiltCoin").show();
		$("#prize_custom").hide();
		$("#prize_coin").attr("required","required");
		$("#prize_custom div.base").each(function(i) {
			var n = $(this).attr('clone');
			$("#custom_prizeImage"+n).attr("required",false);
			$("#prize_title"+n).attr("required",false);
			$("#custom_prizeDes"+n).attr("required",false);
		});
	}
	else if(prizeType == 'vcoin'){
		$("#prize_tiltCoin").show();
		$("#prize_custom").hide();
		$("#prize_coin").attr("required","required");
		$("#prize_custom div.base").each(function(i) {
			var n = $(this).attr('clone');
			$("#custom_prizeImage"+n).attr("required",false);
			$("#prize_title"+n).attr("required",false);
			$("#custom_prizeDes"+n).attr("required",false);
		});
	}
	else if(prizeType == 'custom'){
		$("#prize_custom").show();
		$("#prize_tiltCoin").hide();
		$("#prize_coin").attr("required",false);
		document.getElementById('prize_coin').setCustomValidity("");
		$("#prize_custom div.base").each(function(i) {
			var n = $(this).attr('clone');
			$("#prize_title"+n).attr("required",true);
			$("#custom_prizeDes"+n).attr("required",true);
		});
	}
}

function initializeEditor(){
 tinymce.init({
	height 	: "200",
	width	: "200",
	mode : "specific_textareas",
	selector: ".textEditor", statusbar: false, menubar:false,
	plugins	: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
				],
	toolbar: "insertfile undo redo styleselect | bold italic  alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link "
	});
}

$(document).ready(function() {
  	$('#rootwizard').bootstrapWizard({'tabClass': 'nav nav-tabs'});
	$('#rootwizard').bootstrapWizard('show',<?php echo $show; ?>);
	chkTournamentType();
	initializeEditor();
	$("#loader").remove();
	<?php if(isset($Games) && $Games != '') { ?>
		$("#game_list").prepend(<?php echo '"'.$Games.'"'; ?>);
	<?php } ?>
	check_generate_pin('#pin_chk_option');
	<?php if(isset($_SESSION['facebook_share']) && $_SESSION['facebook_share'] == 1) { unset($_SESSION['facebook_share']); ?>
		var comment_share	= 'The "'+$("#tournament_name").val()+'" tournament going to start at '+$("#start_time").val()+' and end at '+$("#end_time").val();
		var twitterShare = 0;
		<?php if(isset($_SESSION['twitter_share']) && $_SESSION['twitter_share'] == 1) { unset($_SESSION['twitter_share']); ?>
		twitterShare = 1;
		<?php } ?>
		setTimeout(function(){facebookShare(twitterShare,comment_share)}, 2000);
	<?php } else if(isset($_SESSION['twitter_share']) && $_SESSION['twitter_share'] == 1) { unset($_SESSION['twitter_share']); ?>
		var comment_share	= 'The "'+$("#tournament_name").val()+'" tournament going to start at '+$("#start_time").val()+' and end at '+$("#end_time").val();
		setTimeout(function(){shareTwitterPost(comment_share) }, 2000);
	<?php } else { ?>
		setTimeout(function(){$(".success_msg").fadeOut(1000); }, 6000);
	<?php } ?>
});

function calcPrize(){
	$("#prize_coin").val(parseInt($("#entry_fee").val()) * parseInt($("#max_player_hidden").val()));
}
function validateLab(){
	if($('#elimination').is(':checked')){
		var timeFormat      =   /^([0-9][0-9]):([0-5][0-9]):([0-5][0-9])$/;
		var startTime = $("#can_we_start").val();
		if( startTime != ''){
			if(startTime != '00:00:00'){
				if ( ( timeFormat.test($("#can_we_start").val()) ) !== true) {
					document.getElementById('can_we_start').setCustomValidity("Please provide the valid time format");
				} else {
					document.getElementById('can_we_start').setCustomValidity("");
				}
			}else{
				document.getElementById('can_we_start').setCustomValidity("Please provide the valid Can Start time");
			}
		}
	}else
		document.getElementById('can_we_start').setCustomValidity('');

	if($("#entry_type").is(":checked")){
		if($("#entry_fee").val() < 0){
			document.getElementById('entry_fee').setCustomValidity('Provide Entry Fee');
		}
		var payType1		=	$("input[name=fee_pay_type]:checked").val();
		if(payType1 && payType1 !=''){
			document.getElementById('pay_tilt').setCustomValidity('');
			var maxPlayer 	= parseInt($("#max_player_hidden").val());
			var entryFee 	= parseInt($("#entry_fee").val());
			if(maxPlayer && maxPlayer > 0 && entryFee && entryFee > 0){
				var total		= 0;
				var prizeType  	= $("input[name=prize_type]:checked").val();
				if(prizeType == '2' || prizeType == '3' || prizeType == '4'){
					var msg = "";
					if(prizeType == '3'){
						var cur_amount 	= parseInt($("#vcoin_max").val());
						var oldCoins 	= parseInt($("#old_prize_vcoin").val());
						total			= (maxPlayer * entryFee);
						msg 			= "You don't have enough virtual Coins to create tournament, You have only "+addCommas(cur_amount)+" Virtual Coins";
					}else {
						var cur_amount 	= parseInt($("#tilt_max").val());
						var oldCoins 	= parseInt($("#old_prize_coin").val());
						if(oldCoins == '0'){
							 if(prizeType == '4'){
								 total		= (maxPlayer * entryFee);
							 }else{
								 total		= (maxPlayer * entryFee)+parseInt($("#tilt_default_fee").val());
							 }
						}
						else{
							total		= (maxPlayer * entryFee);
						}
						msg 			= "You don't have enough TiLT$ to create tournament, You have only "+addCommas(cur_amount)+" TiLT$";
					}
					if(total > cur_amount ){
						document.getElementById('entry_fee').setCustomValidity(msg);
					}
					else {
						document.getElementById('entry_fee').setCustomValidity('');
						if(prizeType != '4')
							$("#prize_coin").val(maxPlayer * entryFee);
					}
					document.getElementById('tilt').setCustomValidity('');
				}else if(prizeType == '4'){
					document.getElementById('tilt').setCustomValidity('');
				}
				else {
					 document.getElementById('tilt').setCustomValidity('Provide prize type');
				}
			}else{
				if(!entryFee || entryFee == '0' || entryFee == '' )
					document.getElementById('entry_fee').setCustomValidity('Provide entry fee');
				else document.getElementById('entry_fee').setCustomValidity('');
			}
			document.getElementById('prize_coin').setCustomValidity('');
		}
		else{
			document.getElementById('pay_tilt').setCustomValidity('Provide entry fee pay type');
		}
	}else {
		document.getElementById('pay_tilt').setCustomValidity('');
		var total		= 0;
		var prizeType  	= $("input[name=prize_type]:checked").val();
		if(prizeType == '2' || prizeType == '3'){
			var currentPrize = parseInt($("#prize_coin").val());
			if(currentPrize && currentPrize !='0'){
				var msg = "";
				if(prizeType == '3'){
					var cur_amount 	= parseInt($("#vcoin_max").val());
					var oldCoins 	= parseInt($("#old_prize_vcoin").val());
					msg 			= "You don't have enough virtual coin to create tournament, You have only "+addCommas(cur_amount)+" virtual coin";
				}else {
					var cur_amount 	= parseInt($("#tilt_max").val());
					var oldCoins 	= parseInt($("#old_prize_coin").val());
					if(oldCoins == 0){
						 currentPrize	= currentPrize+parseInt($("#tilt_default_fee").val());
					}
					msg 			= "You don't have enough TiLT$ to create tournament, You have only "+addCommas(cur_amount)+" TiLT$";
				}
				document.getElementById('prize_coin').setCustomValidity('');
				if(currentPrize > cur_amount ){
					document.getElementById('prize_coin').setCustomValidity(msg);
				}
			}else{
				document.getElementById('prize_coin').setCustomValidity('Provide prize');
			}
		}
		else{
			document.getElementById('entry_fee').setCustomValidity('');
		}
	}
	custprize = '';
	var ch	= $("input[name=prize_type]:checked").val();
	if(ch==4){
		$("#prize_coin").attr("required",false);
		if($("#max_player_hidden").val() < $("#prize_custom div.base").length){
			custprize = 1;
		}
		$("#prize_custom div.base").each(function() {
		 var n = $(this).attr('clone');
			var prizeImage	=	$("#custom_prizeImage"+n+"_upload").val();
			if(!prizeImage || prizeImage == 'undefined' || prizeImage ==  ''){
				var imgId	=	'custom_prizeImage'+n;
				$("#"+imgId).attr("required","required");
			}else{
				if(prizeImage != 'oldimage')
						$("#prize_tempFile"+n).val(prizeImage);
				var imgId	=	'custom_prizeImage'+n;
				$("#"+imgId).attr("required",false);
			}
			$("#prize_title"+n).attr("required",'required');
			$("#custom_prizeDes"+n).attr("required",'required');
		});

	}else{
		$("#prize_coin").attr("required",'required');
		$("#prize_custom div.base").each(function() {
			var n = $(this).attr('clone');
			$("#custom_prizeImage"+n).attr("required",false);
			$("#prize_title"+n).attr("required",false);
			$("#custom_prizeDes"+n).attr("required",false);
		});
	}

	if($('#elimination').is(':checked') && ch==4){
		if( $("#prize_custom .base").length != '1'){
			document.getElementById('custom').setCustomValidity('Custom Prize Tournament with elimination one prize only allowed');
		}
		else
		document.getElementById('custom').setCustomValidity('');
	}
	else if(custprize == 1)
		document.getElementById('custom').setCustomValidity('Custom Prize cannot exceed  Max number of players');
	else
		document.getElementById('custom').setCustomValidity('');

	if(($("#coupon_title").val() != '') || $("#coupon_limit").val() != '' || $("#coupon_text").val() != ''  || $("#coupon_startdate").val() != ''  ||  $("#coupon_enddate").val() != ''||  $("#empty_coupon_image").val() != '' ){
		$("#coupon_title").attr("required","required");
		$("#coupon_limit").attr("required","required");
		$("#coupon_text").attr("required","required");
		$("#coupon_startdate").attr("required","required");
		$("#coupon_enddate").attr("required","required");
		if($("#empty_coupon_image").val() ==''){
			$("#coupon_image").attr("required","required");
		}else{
			$("#coupon_image").attr("required",false);
		}

		if($("#coupon_limit").val() !=''){
			if(parseInt($.trim($("#coupon_limit").val())) == 0){
				document.getElementById("coupon_limit").setCustomValidity('Limit must be greater than zero');
			}else if(parseInt($.trim($("#coupon_limit").val())) > parseInt($.trim($("#max_player_hidden").val()))){
				document.getElementById("coupon_limit").setCustomValidity('Limit must be less than or equal to Max no. of Players');
			}else
				document.getElementById("coupon_limit").setCustomValidity('');
		}
		var starttime	=	$('#start_time_hidden').val();
		var endtime		=	$('#end_time_hidden').val();
		var cStarttime	=	$('#coupon_startdate').val();
		var cEndtime	=	$('#coupon_enddate').val();
		if($("#elimination").is(":checked")){
			if(starttime != '' && cEndtime != '' && cStarttime != ''){
				starttime_arr = starttime.split(" ");
				starttime = starttime_arr[0];
				cEndtime_arr = cEndtime.split(" ");
				cEndtime = cEndtime_arr[0];
				cStarttime_arr = cStarttime.split(" ");
				cStarttime = cStarttime_arr[0];
				if(( Date.parse(cStarttime) > Date.parse(starttime) || (Date.parse(cStarttime) == Date.parse(starttime)))){
						if(Date.parse(cStarttime) > Date.parse(cEndtime)){
							document.getElementById('coupon_enddate').setCustomValidity("Coupon end date should be greater than or equal to tournament start date");
						}
						else{
							document.getElementById('coupon_enddate').setCustomValidity("");
						}
					document.getElementById('coupon_startdate').setCustomValidity("");
				}else{
					document.getElementById('coupon_startdate').setCustomValidity("Coupon Start Date should be grater than tournament Start Date");
				}
			}
		}else{
			if(starttime != '' && endtime != '' && cStarttime != ''){
				starttime_arr = starttime.split(" ");
				starttime = starttime_arr[0];
				endtime_arr = endtime.split(" ");
				endtime = endtime_arr[0];
				cStarttime_arr = cStarttime.split(" ");
				cStarttime = cStarttime_arr[0];
				if(( Date.parse(cStarttime) > Date.parse(starttime) || (Date.parse(cStarttime) == Date.parse(starttime))) && ( Date.parse(cStarttime) < Date.parse(endtime) || (Date.parse(cStarttime) == Date.parse(endtime)))){
					if(cStarttime != '' && cEndtime != ''){
						cEndtime_arr = cEndtime.split(" ");
						cEndtime = cEndtime_arr[0];
						if(Date.parse(cStarttime) > Date.parse(cEndtime)){
							document.getElementById('coupon_enddate').setCustomValidity("Coupon end date should be greater than or equal to tournament start date");
						}else if(Date.parse(endtime) < Date.parse(cEndtime) ){
							document.getElementById('coupon_enddate').setCustomValidity("Coupon end date should be less than or equal to tournament end date");
						}else{
							document.getElementById('coupon_enddate').setCustomValidity("");
						}
					}
					document.getElementById('coupon_startdate').setCustomValidity("");
				}else{
					document.getElementById('coupon_startdate').setCustomValidity("Coupon start date should be in between tournament start date and end date");
				}
			}
		}
	}else{
		$("#coupon_title").attr("required",false);
		$("#coupon_limit").attr("required",false);
		$("#coupon_text").attr("required",false);
		$("#coupon_startdate").attr("required",false);
		$("#coupon_enddate").attr("required",false);
		$("#coupon_image").attr("required",false);
		document.getElementById("coupon_limit").setCustomValidity('');
	}

	var selVal	=	$('#banner_type option:selected').val();
	if(selVal !=''	&&	selVal !='undefined'){
		if(selVal == 1){
			$("#banner_text").attr("required","required");
			$("#banner_link").attr("required",false);
			$("#banner_image").attr("required",false);
		}
		else if(selVal == 2){
			$("#banner_text").attr("required",false);
			$("#banner_link").attr("required","required");
			if($("#empty_banner_image").val() ==''){
				$("#banner_image").attr("required","required");
			}else{
				$("#banner_image").attr("required",false);
			}
		}
	}else{
		$("#banner_text").attr("required",false);
		$("#banner_link").attr("required",false);
		$("#banner_image").attr("required",false);
	}

	var ytext =	$("#youtube_codes").val();
	var ylink =	$("#youtube_link").val();
	var yimage = $("#empty_youtube_image").val();
	if( (yimage && yimage !='') || (ylink && ylink !='') || (ytext && ytext !='')){
		document.getElementById("youtube_type").setCustomValidity('Select Youtube link type');
	}else document.getElementById("youtube_type").setCustomValidity('');

	var selVal	=	$('#youtube_type option:selected').val();
	if(selVal !=''	&&	selVal !='undefined'){
		document.getElementById("youtube_type").setCustomValidity('');
		if(selVal == 1){
			$("#youtube_link").attr("required","required");
			$("#youtube_codes").attr("required",false);
		}
		else if(selVal == 2){
			$("#youtube_link").attr("required",false);
			$("#youtube_codes").attr("required","required");
		}
		if($("#empty_youtube_image").val() ==''){
			$("#youtube_image").attr("required","required");
		}else{
			$("#youtube_image").attr("required",false);
		}
	}else{
		$("#youtube_link").attr("required",false);
		$("#youtube_codes").attr("required",false);
		$("#youtube_image").attr("required",false);
	}

	var submitFlag = 1;
	document.getElementById("state_loc_tour").setCustomValidity('');
	document.getElementById("locationsearch_tour").setCustomValidity('');
	document.getElementById("country_loc_tour").setCustomValidity('');
	$("#RestrictedLocationContent div.clone").each(function() {
		var n = $(this).attr("clone");
		document.getElementById('country_loc_'+n).setCustomValidity('');
		document.getElementById('state_loc_'+n).setCustomValidity('');
		document.getElementById('locationsearch_'+n).setCustomValidity('');
	});
	var country = $("#country_loc_tour").val();
	if($("#loc_chk_option").is(":checked")){
		if(country == ''){
			document.getElementById("country_loc_tour").setCustomValidity('Country is required');
		}
		if(country !='' && $("#locationsearch_tour").val() == ''){
			document.getElementById("locationsearch_tour").setCustomValidity('Location is required');
		}
		var stext	=	$('#country_loc_tour option:selected').text();
		if(stext=='United States'){
			if($("#state_loc_tour").val() == ''){
				document.getElementById("state_loc_tour").setCustomValidity('State is required');
			}
		}
	}
	else if($("#location").is(":checked")){
		$("#RestrictedLocationContent div.clone").each(function() {
			var n = $(this).attr("clone");
			var country = $("#country_loc_"+n).val();
			if(country == ''){
				document.getElementById('country_loc_'+n).setCustomValidity('Country is required');
			}
			var stext	=	$('#country_loc_'+n+' option:selected').text();
			if(stext=='United States'){
				if($("#state_loc_"+n).val() == ''){
					document.getElementById('state_loc_'+n).setCustomValidity('State is required');
				}
				if($("#locationsearch_"+n).val() == ''){
					document.getElementById('locationsearch_'+n).setCustomValidity('Location is required');
				}
			}
		});
	}
}

function validateTDetail(){
	nameflag = 0
	if($.trim($('#tournament_name').val()) != ''){
		var edit_id = 0;
		if($('#edit_id').val() != undefined && $('#edit_id').val() != '' && $('#edit_id').val() != 0)
			edit_id = $('#edit_id').val();
		$.ajax({
				url:actionPath+'models/AjaxAction.php?rand='+Math.random(),
				type: 'POST',
				async: false,
				data: {action:'CHECK_TOURNAMENT',tour_name: $('#tournament_name').val(), edit_id: edit_id },
				success: function(result){
					if($.trim(result) != 0){
						nameflag = 1;
					}
				}
		});
	}else
		nameflag = 2;

	if(nameflag == 1)
		document.getElementById('tournament_name').setCustomValidity("Tournament already exists");
	else if(nameflag == 2)
		document.getElementById('tournament_name').setCustomValidity("Please fill out this field");
	else
		document.getElementById('tournament_name').setCustomValidity("");

	msg = "You don't have enough TiLT$ to create tournament, You have only "+addCommas(parseInt($('#tilt_max').val()))+" TiLT$";
	if($("#titl_coin_flag").val() != 2){
		if(parseInt($("#tilt_max").val()) < (parseInt($("#tilt_prize").val()) +  parseInt($("#tilt_default_fee").val())))
			document.getElementById('tilt_prize').setCustomValidity(msg);
		else
			document.getElementById('tilt_prize').setCustomValidity('');

	}else{
		if(parseInt($("#tilt_max").val()) < parseInt($("#tilt_prize").val()))
			document.getElementById('tilt_prize').setCustomValidity(msg);
		else
			document.getElementById('tilt_prize').setCustomValidity('');
	}

	var tourDateFlag = 1;
	var tourDate		= $('#start_time').val();
	if(tourDate){
		var locDate = new Date();
		var utc = locDate.getTime() + (locDate.getTimezoneOffset() * 60000);
		var utcDate = new Date(utc);
		var nyTstamp = utc - (3600000*(USOFFSET));
		var newyorkDate = new Date(nyTstamp);
		if((Date.parse(tourDate)) < (Date.parse(newyorkDate))){tourDateFlag = 0;}
	}
	if(tourDateFlag){
		document.getElementById('start_time').setCustomValidity("");
		var starttime	=	$('#start_time').val();
		var endtime		=	$('#end_time').val();
		if(!$('#elimination').is(':checked')){
			if(starttime != '' && endtime != ''){
				if(Date.parse(starttime) > Date.parse(endtime) || (Date.parse(starttime) == Date.parse(endtime))){
					document.getElementById('end_time').setCustomValidity("End time should be greater than start time");
				}else{
					document.getElementById('end_time').setCustomValidity("");
				}
			}
		}
	}else document.getElementById('start_time').setCustomValidity("Start time should be greater than current EST time");
}

function search()	{
	location_error.innerHTML='';
	var checkedvalue	=	$('#location').is(':checked');
		$('#locationsearch').val('');
		$('#location_text').val('');
		$('#selected_lat').val('');
		$('#selected_lng').val('');
		$('#selected_location').val('');
	if(checkedvalue == true)	{
		$('#share_search').show();
		$('#loc_chk_option').prop('checked', false);
		$("#tournament_loc_block").hide();
	}
	else {
		$('#share_search').hide();
	}
}
function setTournamentOptions1(){
	check_generate_pin('#pin_chk_option');
	var checkedvalue	=	$('#pin_chk_option').is(':checked');
	if(checkedvalue == true)	{
		$('#loc_chk_option').prop('checked', false);
		$("#tournament_loc_block").hide();

		$("#tournament_pin_block").show();
	}
	else{
		$("#tournament_pin_block").hide();
	}
}
function setTournamentOptions2(){
	var checkedvalue	=	$('#loc_chk_option').is(':checked');
	if(checkedvalue == true)	{
		$('#pin_chk_option').prop('checked', false);
		$('#pin_chk').prop('checked', false);
		$('#location').prop('checked', false);
		$('#share_search').hide();
		$("#tournament_loc_block").show();
		$("#tournament_pin_block").hide();
	}
	else{
		$("#tournament_loc_block").hide();
	}
	check_generate_pin('#pin_chk_option');
}
	function check_generate_pin(thisval) {
			if(!$(thisval).is(':checked')) {
				$('#generatepin').hide();
			}
			else{
				if($('#pin_based_tour').val()==1)
					$('#generatepin').show();
			}
	}
	$('.fancybox').fancybox({
			type : 'ajax',
			helpers: {
			title: null
			}
	});

	function locationShow(evt,type){
	var stext	=	$('#country_loc_'+evt+' option:selected').text();
	var isiPad = /ipad/i.test(navigator.userAgent.toLowerCase());
	var isAndroid = /android/i.test(navigator.userAgent.toLowerCase());
	var isiPhone = /iphone/i.test(navigator.userAgent.toLowerCase());
	var isiPod = /ipod/i.test(navigator.userAgent.toLowerCase());
	if(type == 1) {
		if(stext=='United States'){
			$('#state_loc_'+evt).show();
			$('#locationsearch_'+evt).hide();
			$('#locationsearch_'+evt).val('');
			$('#col_loc_'+evt+'_3').show();
		}
		else {
			$('#locationsearch_'+evt).hide();
			$('#locationsearch_'+evt).val('');
			$('#state_loc_'+evt).hide();
			$('#state_loc_'+evt).val('');
			$('#col_loc_'+evt+'_3').show();
		}
		st_loc_show	=	0;
		$("#RestrictedLocationContent div.clone").each(function() {
			var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
			if(cvalue == 'United States')
				st_loc_show	=	1;
		});
		var curr_st_loc_show = 0;
		if($("#country_loc_"+evt).find("option:selected").eq(0).text() == 'United States')
			curr_st_loc_show = 1
		if(curr_st_loc_show == 1){
			if((isiPad || isAndroid || isiPhone || isiPod)){
				$('#col_loc_'+evt+'_3').removeClass('mobview');
				$('#col_loc_'+evt+'_4').removeClass('mobview');
			}
		}else{
			if((isiPad || isAndroid || isiPhone || isiPod)){
				$('#col_loc_'+evt+'_3').addClass('mobview');
				$('#col_loc_'+evt+'_4').addClass('mobview');
			}
		}
		if(st_loc_show == 1){
			$('#state_location').show();
		}else {
			$('#state_location').hide();
			$('#location_state').hide();
		}
	} else {
		var stattext	=	$('#state_loc_'+evt+' option:selected').text();
		var search		=	stattext+','+stext;
		$.ajax({
				type: "GET",
				url: actionPath+"models/AjaxAction.php?countryname="+search+"&action=COUNTRY",
				data: '',
				async: false,
				success: function (json_data){
						if(json_data != '') {
						resultdata	=	json_data.split('###');
							if(resultdata[0] != 0)
								$('#latitude_'+evt).val(resultdata[0].trim());
							if(resultdata[1] != 0)
								$('#longitude_'+evt).val(resultdata[1].trim());
								$("#temp_hidden_flag").val(0);
						}
				}
			});
		$('#locationsearch_'+evt).show();
		$('#locationsearch_'+evt).val('');
		st_loc_show	=	0;
		$("#RestrictedLocationContent div.clone").each(function() {
			var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
			if(cvalue == 'United States')
				st_loc_show	=	1;
		});
		if(st_loc_show == 1) {
			$('#location_state').show();
			$('#col_loc_'+evt+'_4').show();
		}
		else{
			$('#location_state').hide();
			$('#col_loc_'+evt+'_4').hide();
		}
	}
}


	function manageLocation(ref,type) {
	if(type == 1) {

		var count 		= 	0;
		var node 		= 	$('#location_clone').closest("div");
		var empty		=	0;
		curtotLoc	=	parseInt($('#totLoc').val());
		$("#RestrictedLocationContent div.clone").each(function() {

			var text 	=	$(this).find("input.locationsearch").eq(0).val();
			var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
			if(text == "" && cvalue == 'United States')
				empty = 1;

			if($(this).find("select.country")){
				var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
				if(cvalue != '' &&	cvalue=='United States'){
					if($(this).find("select.state")){
						var svalue	=	$(this).find("select.state option:selected").eq(0).text();
						if(svalue!='' && svalue=='Select'){
							empty = 3;
						}else{

						}
					}
				}
				else if(cvalue != '' &&	cvalue=='Select'){
					empty = 2;
				}
			}
		});
		if(empty == 0)
		{
			var length		= node.attr("clone");
			var tabindex	= $("#method").attr("tabindex");
			var clonedRow 	= node.clone(true);
			clonedRow.appendTo('#RestrictedLocationContent');
			$('.locplus').hide();
			if(length >= 0) 	{
				count	=	curtotLoc + 1;
				clonedRow.attr("clone",count);
				clonedRow.attr("style",'clear:both;padding-top:10px;');
				clonedRow.attr("id",'location_'+count);
				clonedRow.find("a.locplus").attr("id",'plus_'+count);
				clonedRow.find("a.locminus").attr("id",'minus_'+count);
				clonedRow.find("a.locminus").attr("style",'');
				clonedRow.find("select").val(0);
				clonedRow.find("select.country").attr("id",'country_loc_'+count);
				clonedRow.find("select.country").attr("name",'countryLocation[]');
				clonedRow.find("select.country").attr("onchange",'locationShow('+count+',\'1\')');
				clonedRow.find("select.state").attr("id",'state_loc_'+count);
				clonedRow.find("select.state").attr("name",'stateLocation[]');
				clonedRow.find("select.state").attr("onchange",'locationShow('+count+',\'2\')');
				clonedRow.find("select.state").attr("style",'display:none;');
				clonedRow.find("div.state").attr("id",'col_loc_'+count+'_3');
				clonedRow.find("div.location").attr("id",'col_loc_'+count+'_4');
				clonedRow.find("input.locationsearch").attr("id",'locationsearch_'+count);
				clonedRow.find("input.locationsearch").attr("name",'locationsearch[]');
				clonedRow.find("input.locationsearch").attr("onkeypress",'autoCompleteLocation('+count+')');
				clonedRow.find("input.locationsearch").val("");
				clonedRow.find("input.latitude").attr("id",'latitude_'+count);
				clonedRow.find("input.latitude").attr("name",'latitude[]');
				clonedRow.find("input.latitude").val('');
				clonedRow.find("input.longitude").attr("id",'longitude_'+count);
				clonedRow.find("input.longitude").attr("name",'longitude[]');
				clonedRow.find("input.longitude").val('');
				$('#col_loc_'+count+'_3').hide();
				$('#col_loc_'+count+'_4').hide();
				$('#plus_'+count).show();
				$('#minus_'+curtotLoc).show();
				$('#totLoc').val(count)
			}

		}
		else if(empty==2)
			alert("Please select country");
		else if(empty==3)
			alert("Please select state");
		else if(empty==1)
			alert("Please enter location");
	} else {
		var rinc	=	0;
		$("#RestrictedLocationContent div.clone").each(function() {
			rinc	=	rinc + 1;
		});
		if(rinc == 1) {
			alert('Atleast one row is required');
			return false;
		}

		var node 	= $(ref).closest("div.clone");
		var n		= node.attr("clone");
		totLoc		=	parseInt($('#totLoc').val());
		$('#location_'+n).remove();
		for(i=totLoc;i>=1;i--) {
			if ($('#location_'+i).length) {
				$('#plus_'+i).show();
				$('#minus_'+i).show();
				break;
			}
		}

		st_loc_show	=	0;
		$("#RestrictedLocationContent div.clone").each(function() {
			var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
			if(cvalue == 'United States')
				st_loc_show	=	1;
		});
		if(st_loc_show == 1) {
			$('#location_state').show();
			$('#state_location').show();
		}
		else{
			$('#location_state').hide();
			$('#state_location').hide();
		}
	}
}


function tourlocationShow(evt,type){

	var stext	=	$('#country_loc_'+evt+' option:selected').text();

	if(type == 1) {
		if(stext=='United States'){
			$('#state_loc_'+evt).show();
			$('#state_location_'+evt).show();
			$('#locationsearch_'+evt).hide();
			$('#locationsearch_'+evt).val('');
		}
		else {
			$('#locationsearch_'+evt).hide();
			$('#state_location_'+evt).hide();
			$('#location_state_'+evt).hide();
			$('#locationsearch_'+evt).val('');
			$('#state_loc_'+evt).hide();
			$('#state_loc_'+evt).val('');
			var stattext	=	$('#country_loc_'+evt+' option:selected').text();
			var search		=	stattext;
			$.ajax({
					type: "GET",
					url: actionPath+"models/AjaxAction.php?countryname="+search+"&action=COUNTRY",
					data: '',
					success: function (json_data){
							if(json_data != '') {
							resultdata	=	json_data.split('###');
								if(resultdata[0] != 0)
									$('#latitude_'+evt).val(resultdata[0].trim());
								if(resultdata[1] != 0)
									$('#longitude_'+evt).val(resultdata[1].trim());
									$("#temp_hidden_flag").val(0);
							}
					}
				});
			$('#location_state_'+evt).show();
			$('#locationsearch_'+evt).show();
			$('#locationsearch_'+evt).val('');
		}

	} else {
		var stattext	=	$('#state_loc_'+evt+' option:selected').text();
		var search		=	stattext+','+stext;
		$.ajax({
				type: "GET",
				url: actionPath+"models/AjaxAction.php?countryname="+search+"&action=COUNTRY",
				data: '',
				success: function (json_data){
						if(json_data != '') {
						resultdata	=	json_data.split('###');
							if(resultdata[0] != 0)
								$('#latitude_'+evt).val(resultdata[0].trim());
							if(resultdata[1] != 0)
								$('#longitude_'+evt).val(resultdata[1].trim());
								$("#temp_hidden_flag").val(0);
						}
				}
			});
		$('#location_state_'+evt).show();
		$('#locationsearch_'+evt).show();
		$('#locationsearch_'+evt).val('');
	}
}

function autoCompleteLocation(txtid) {
	$( "#locationsearch_"+txtid ).autocomplete({
	  minLength: 2,
	  source: function( request, response ) {
		var cache = {};
		var tempFlag	=	$("#temp_hidden_flag").val();
		if(tempFlag && tempFlag == 0){
			var currentlat	=	$('#latitude_'+txtid).val();
			var currentlong	=	$('#longitude_'+txtid).val();
			 $( "#temp_hidden_lat").val(currentlat);
			$( "#temp_hidden_long").val(currentlong);
		}else{
			var currentlat	=	$('#temp_hidden_lat').val();
			var currentlong	=	$('#temp_hidden_long').val();
		}
		var location	=	$('#locationsearch_'+txtid).val();
		if(($.trim(currentlat) == '' || $.trim(currentlong) == '') && $.trim(location) == '')	{
			alert('latitude & longitude not valid');
			return false;
		}
		var term = request.term;
		$( "#locationsearch_id").val(txtid);
		var searchtext	=	$( "#locationsearch_"+txtid ).val();
		var countrytext	=	$('#country_loc_'+txtid+' option:selected').text();
		if(searchtext.length > 2)	{
			$("#temp_hidden_flag").val(1);
			if($('#state_loc_'+txtid).length){
				var stattext	=	$('#state_loc_'+txtid+' option:selected').text();
				if(stattext && stattext !='' && stattext != 'Select')
					searchtext = searchtext+','+stattext;
			}
			searchtext = searchtext+','+countrytext;
			$.ajax({
				type: "GET",
				url: actionPath+"models/AjaxAction.php?countryname="+searchtext+"&action=COUNTRY",
				data: '',
				success: function (json_data){
						if(json_data != '') {
						resultdata	=	json_data.split('###');
							if(resultdata[0] != 0)
								$( "#temp_hidden_lat").val(resultdata[0].trim());
							if(resultdata[1] != 0)
								$( "#temp_hidden_long").val(resultdata[1].trim());
						}
						var currentlat	=	$( "#temp_hidden_lat").val();
						var currentlong	=	$( "#temp_hidden_long").val();
						var location	=	$('#locationsearch_'+txtid).val();
						$.getJSON( actionPath+'models/AjaxAction.php?curlat='+currentlat+'&curlong='+currentlong+'&location='+location+'&action=SEARCH_LOCATION&rand='+Math.random(), request, function( data, status, xhr ) {
							response( data );
						});
				}
			});
		}
	},
	  select: function( event,ui )
		{
			var choosed = ui.item.id;
			var choosed_location = ui.item.label;
			$('#locationsearch_'+txtid).val(choosed_location);
			var position =  choosed.split(",");
			$('#selected_lat').val(position[0]);
			$('#selected_lng').val(position[1]);
			$('#latitude_'+txtid).val(position[0]);
			$('#longitude_'+txtid).val(position[1]);
			$("#locationsearch_id").val('');
		}
	});
}

function addMorePinCode(id,postType)	{

	var totalNumber	=	parseInt($("#codeNumber").val());
	$('#noCodeFound').hide();
	$("#addPinCode").show();
	$('#moreOption_show').hide();
	$("#moreOption_gen").show();
	$("#downloadPdf").show();
	var codeInList	=	0;
	if(totalNumber	==	0)	totalNumber	=	1;
	else if(postType){ totalNumber	=	1;

	}
	else{
		var pincodes	=	$('#showId_'+totalNumber).val();
		var oldIds	=	$('#showIds').val();
		var newIds;
		if(oldIds	!='')
			newIds	=	oldIds+','+pincodes;
		else
			newIds	=	pincodes;
		$('#showIds').val(newIds);
		codeInList	=	$('#showIds').val();
		totalNumber	=	totalNumber+10;
	}
	var numberCount	=	$('#showCount_'+totalNumber).val();
	$("#codeNumber").val(totalNumber);
	var totalNumber1	=	$("#codeNumber").val();
	$.post(actionPath+'models/AjaxAction.php?rand='+Math.random(),{action:'ADD_PINCODE',
			tournamentId:id,
			number:totalNumber,
			codesList:codeInList
	},
	function(result){
		$("#pincodeContainer").append(result);
		$("#pinCodeControl").addClass("popup_scroll");
		$("#pinCodeControl").css("overflow","scroll");
		var widthinner = $('.fancybox-inner').width();
		if(widthinner < 160){
			parent.$('.fancybox-inner').width(widthinner+40);
			var widthfancyboxwrap = $('.fancybox-wrap').width();
			parent.$('.fancybox-wrap').width(widthfancyboxwrap+40);
		}
		var percentageToScroll = 100;
		var height = $(document).innerHeight();
		var scrollAmount = height * percentageToScroll/ 100;
		 var overheight = jQuery(document).height() - jQuery(window).height();
		jQuery("html, body").animate({scrollTop: scrollAmount}, 900);
	});
}

function showUsedPinCode(id,postType)	{

	var totalNumber	=	parseInt($("#codeNumber").val());
	var codeInList	=	0;
	$("#downloadPdf").hide();
	if(totalNumber	==	0)	totalNumber	=	1;
	else if(postType){ totalNumber	=	1; $("#addPinCode").show(); }
	else{
		var pincodes	=	$('#showId_'+totalNumber).val();
		var oldIds	=	$('#showIds').val();
		var newIds;
		if(oldIds	!='')
			newIds	=	oldIds+','+pincodes;
		else
			newIds	=	pincodes;
		$('#showIds').val(newIds);
		codeInList	=	$('#showIds').val();
		if(newIds !=''	&&	oldIds	!=newIds)
			totalNumber	=	totalNumber+10;
	}
	$("#codeNumber").val(totalNumber);
	var totalNumber1	=	$("#codeNumber").val();
	$.post(actionPath+'models/AjaxAction.php?rand='+Math.random(),{action:'SHOW_PINCODE',
			tournamentId:id,
			number:totalNumber,
			codesList:codeInList,
			postType:postType
	},
	function(result){
		if($.trim(result)==''){
			$("#noCodeFound").show();
			$("#addPinCode").hide();
			$('#moreOption_show').hide();
		}
		else {
				$("#addPinCode").hide();
				$("#pincodeContainer").append(result);
			if ($.trim(result).indexOf("ADD_MORE_STATUS") >= 0){
				$('#moreOption_show').hide();
				$("#moreOption_gen").hide();
			}
			else {
				$('#moreOption_show').show();
				$("#moreOption_gen").hide();
			}
		}
	var percentageToScroll = 100;
    var height = $(document).innerHeight();
    var scrollAmount = height * percentageToScroll/ 100;
     var overheight = jQuery(document).height() - jQuery(window).height();
	jQuery("html, body").animate({scrollTop: scrollAmount}, 900);
	});
}
function resetListing(type){
	if(type==1){
		$.post(actionPath+'models/AjaxAction.php?rand='+Math.random(),{action:'SET_PINCODE_LIST',
		},
		function(result){
			$("#codeNumber").val(1);
			$('#showIds').val('');
			$('#noCodeFound').val('');
			$('#pinCodeControl').html(result);
			var tournamentId	=	$("#tournamentId").val();
			showUsedPinCode(tournamentId,1);
		});
	}else{

		$.post(actionPath+'models/AjaxAction.php?rand='+Math.random(),{action:'SET_PINCODE_LIST',
		},
		function(result){
			$("#codeNumber").val(1);
			$('#showIds').val('');
			$('#noCodeFound').val('');
			$('#pinCodeControl').html(result);
			var tournamentId	=	$("#tournamentId").val();
			addMorePinCode(tournamentId,1);
		});
	}
}
$('.image_popup').fancybox({ helpers: { title: null}});
$(".banner_video").fancybox({'width': '350','height': '350','maxWidth': '100%', 'type': 'iframe','autoSize' : false});
$("#tournamentType").change(function() {
	if($(this).val()==2) {
		$("#start_date_div").hide();
		$("#end_date_div").hide();
		$("#start_time").val('');
		$("#end_time").val('');
		$("#start_time").removeAttr('required');
		$("#end_time").removeAttr('required');
	}
	else {
		$("#start_date_div").show();
		$("#end_date_div").show();
		$("#start_time").attr('required', 'required');
		$("#end_time").attr('required', 'required');
	}
});
<?php if(isset($tournamentType) && $tournamentType==2) echo "$('#start_date_div').hide(); $('#end_date_div').hide(); $('#start_time').removeAttr('required');$('#end_time').removeAttr('required');$('#start_time').val('');$('#end_time').val('');";  ?>
</script>
</html>
