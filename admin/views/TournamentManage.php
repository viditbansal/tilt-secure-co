<?php
//insertRules( need to update in rules management
require_once('includes/CommonIncludes.php');
require_once('controllers/AdminController.php');
require_once("includes/phmagick.php");
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
$adminObj   	 =   new AdminController();
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
}
$usId = USID;
$banner_video_array = array("1" => "mp4");
$todayWithoutMin	= getCurrentTime('America/New_York','Y-m-d');
$today				= getCurrentTime('America/New_York','Y-m-d H:i:s');
$coupon_image_path 	= $couponImage = $banner_file_path = $bannerFile = $link_image_path = $linkImage = '';
$bannerImageBlock 	= $youtubeImageBlock = '';
$field_focus	=	'brand';
$class			=	$ExistCondition		=	$location		=	$photoUpdateString	=	'';
$tournamenttype = $gametype		=	0;
$entryfee_flag	=	$startDateFlag	=	$endDateFlag	=	$dateFlag	=	0;
$condition      = " ";
$field			= " id as brandId,BrandName ";
$brandDetailsResult  = $tournamentObj->selectBrandDetails($field,$condition);
$condition      = " ";
$field			= " id as gameId,Name ";
$gameDetailsResult  = $tournamentObj->selectGameDetails($field,$condition);
$tourEditStatus		=	1; // 2 - Started , 1 - Upcoming (its only for edit)
$eliminationTour	=	0; // 1 - Elimination, 2 - closed
$createdBy		=	'';
$gen_termsCond = $gen_gamerules = $gen_tournmentrules = $gen_privacyPolicy = '';
$tour_termsCond = $tour_gamerules = $tour_rules = $tour_privacyPolicy = '';
$gameDeveloper = '-';
//country details
$fields			=	' id,Country';
$conditions		=	' Status = 1 ';
$countryList	=	$tournamentObj->getCountryList($fields,$conditions);
if(!empty($countryList))	{
	foreach($countryList as $key=>$value)	{
		$countryArray[$value->id]	=	$value->Country;
	}
	asort ($countryArray);
}

$fields			=	' id,State';
$conditions		=	' Status = 1 AND fkCountriesId = '.$usId.' ';
$stateList		=	$tournamentObj->getStateList($fields,$conditions);
if(!empty($stateList))	{
	foreach($stateList as $key=>$value)	{
		$usStateArray[$value->id]	=	$value->State;
	}
}

//Get rules template
$fields	=	' TermsAndConditions,TournamentRules,GameRules '; $where	= 	' id = 1 ';
$templateArray	=	array();
$templatesRes	=	$adminObj->getDistance($fields,$where);
if(isset($templatesRes) && is_array($templatesRes) && count($templatesRes)>0){
		$gen_termsCond		=	$templatesRes[0]->TermsAndConditions;
		$gen_gamerules  	= 	$templatesRes[0]->GameRules;
		$gen_tournmentrules	=	$templatesRes[0]->TournamentRules;
}
$customRestrict = 1;
 if(isset($_GET['editId']) && $_GET['editId'] != '' ){
	$condition      = "  AND t.id = ".$_GET['editId']." and t.Status in (1,2) LIMIT 1 ";
	$tournamentId	=	$_GET['editId'];
	if(isset($_GET['createdBy']) && $_GET['createdBy'] == 'user' ) 	$createdBy	=	1;
	else if(isset($_GET['createdBy']) && $_GET['createdBy'] == 'developer' )$createdBy	=	3;
	$field			=	' g.id as gameId,g.PlayTime as gamePlayTime, t.* ';
	$tournamentDetailsResult  = $tournamentObj->getTournamentDetails($field,$condition);
	if(isset($tournamentDetailsResult) && is_array($tournamentDetailsResult) && count($tournamentDetailsResult) > 0){
		$tournament =	$tournamentDetailsResult[0]->TournamentName;
		$DevId 		=	$tournamentDetailsResult[0]->fkDevelopersId;
		$game		=	$tournamentDetailsResult[0]->gameId;
		$maxplayer  =	$tournamentDetailsResult[0]->MaxPlayers;
		$turns   	=	$tournamentDetailsResult[0]->TotalTurns;
		$entryfee_flag   =	$tournamentDetailsResult[0]->FeeType;
		$entryfee   =	$tournamentDetailsResult[0]->EntryFee;
		$startdate 	=	$tournamentDetailsResult[0]->StartDate;
		$enddate 	=	$tournamentDetailsResult[0]->EndDate;
		$prize 		=	$tournamentDetailsResult[0]->Prize;
		$gametype 	=	$tournamentDetailsResult[0]->GameType;
		$tournamenttype 	=	$tournamentDetailsResult[0]->Type;
		$elimination 		=	$tournamentDetailsResult[0]->Elimination;
		$dateFlag			=	$tournamentDetailsResult[0]->TournamentStatus;
		$tour_termsCond		=	$tournamentDetailsResult[0]->TermsAndCondition;
		$tour_gamerules		=	$tournamentDetailsResult[0]->TournamentRule;
		$tour_rules			=	$tournamentDetailsResult[0]->GftRules;
		$tour_privacyPolicy	=	$tournamentDetailsResult[0]->PrivacyPolicy;
		$locationRestrict	=	$tournamentDetailsResult[0]->LocationRestrict;
		$pin	=	$tournamentDetailsResult[0]->PIN;
		$pinNumber	=	$tournamentDetailsResult[0]->TournamentPin;
		$can_we_start		=	isset($tournamentDetailsResult[0]->DelayTime) ? $tournamentDetailsResult[0]->DelayTime : '';
		if(isset($tournamentDetailsResult[0]->CreatedBy) && $tournamentDetailsResult[0]->CreatedBy ==3){
			$condition1      	= 	" AND id = ".$tournamentDetailsResult[0]->fkDevelopersId."";
			$field1				= 	" id as gameDevId,Company";
			$gameDeveloperDetails  = $tournamentObj->getGameDeveloper($field1, $condition1);
			if(isset($gameDeveloperDetails) && is_array($gameDeveloperDetails) && count($gameDeveloperDetails) > 0 && !empty($gameDeveloperDetails[0]->Company)){
				$gameDeveloper = $gameDeveloperDetails[0]->Company;
			}
		}
		if($tournamentDetailsResult[0]->PIN==1	&&	$tournamentDetailsResult[0]->LocationRestrict==1)
			$string	=	'PIN Based & Location restricted';
		else if($tournamentDetailsResult[0]->PIN==1)
			$string	=	'PIN Based';
		else if($tournamentDetailsResult[0]->LocationRestrict==1)
			$string	=	'Location restricted';
		else if($tournamentDetailsResult[0]->LocationBased==1)
			$string	=	'Location Based';
		else
			$string	=	'';

		if($tournamentDetailsResult[0]->LocationRestrict == 1){
			$locationRestrictResult	=	$tournamentObj->getRestrictedLocation(' AND `fkTournamentsId` ='.$_GET['editId'].' AND Status = 1');


		}
		if($createdBy == 3){ //Get rules for brand created tournament
			$fields			=	'*';
			$condition		=	' fkTournamentsId ='.$tournamentId.'  AND Status = 1 ';
			$tourRulesResult	=	$tournamentObj->checkRulesEntry($fields,$condition);

			$fields			=	' * ';
			$conditions		=	' AND Status = 1 AND fkTournamentsId ='.$tournamentId.' Order by id desc ';
			$couponAdLinkEntries		=	$tournamentObj->getTournamentsCoupon($fields,$conditions);

			$linkType1 	= 	$linkType2 	= 0;	$linkUrl	=	$linkCode	= '';
			$couponDetails	=	$bannerDetails	=	$linkDetails	=	array();
			$bannerCount	=	$couponCount	=  $linkCount	=	$couponLimit	=	0;
			if(isset($couponAdLinkEntries) && is_array($couponAdLinkEntries) && count($couponAdLinkEntries) > 0){
				foreach($couponAdLinkEntries as $key1=>$value1){
					if($value1->Type==1 && $couponCount == 0)	{
						$couponDetails = $value1; $couponCount++;
						if(isset($value1->File) && $value1->File !='') {
							$couponImage = $value1->File;
							if(SERVER){
								if(image_exists(11,$tournamentId.'/'.$couponImage))	$coupon_image_path = COUPON_IMAGE_PATH.$tournamentId.'/'.$couponImage;
								else	$coupon_image_path = '';
							}else{
								if(file_exists(COUPON_IMAGE_PATH_REL.$tournamentId.'/'.$couponImage))	$coupon_image_path = COUPON_IMAGE_PATH.$tournamentId.'/'.$couponImage;
								else	$coupon_image_path = '';
							}
						}
					}
					if($value1->Type==2 && $bannerCount == 0)	{
						$bannerDetails = $value1; $bannerCount++;
						if(isset($value1->File) && $value1->File !='') {
							$bannerFile = $value1->File;
							if(SERVER){
								if(image_exists(12,$tournamentId.'/'.$bannerFile))	$banner_file_path = BANNER_IMAGE_PATH.$tournamentId.'/'.$bannerFile;
								else	$banner_file_path = '';
							}else{
								if(file_exists(BANNER_IMAGE_PATH_REL.$tournamentId.'/'.$bannerFile))	$banner_file_path = BANNER_IMAGE_PATH.$tournamentId.'/'.$bannerFile;
								else	$banner_file_path = '';
							}
							if(!empty($banner_file_path)) {
								$ext = pathinfo($bannerFile, PATHINFO_EXTENSION);
								if (in_array($ext,$banner_video_array)) $bannerImageBlock =  '<a href="'.$banner_file_path.'" class="banner_video">'.$bannerFile.'</a>';
								else {
									$bannerImageBlock =  '<a href="'.$banner_file_path.'" class="image_pop_up"><img  src="'.$banner_file_path.'" width="75" height="75" class="img_border" ></a>';
								}
							}
						}
					}
					if($value1->Type==3 && $linkCount == 0)		{
						$linkDetails 	 = $value1; $linkCount++;
						if(isset($value1->File) && $value1->File !='') {
							$linkImage = $value1->File;
							if(SERVER){
								if(image_exists(14,$tournamentId.'/'.$linkImage))	$link_image_path = YOUTUBE_LINK_IMAGE_PATH.$tournamentId.'/'.$linkImage;
								else	$link_image_path = '';
							}else{
								if(file_exists(YOUTUBE_LINK_IMAGE_PATH_REL.$tournamentId.'/'.$linkImage))	$link_image_path = YOUTUBE_LINK_IMAGE_PATH.$tournamentId.'/'.$linkImage;
								else	$link_image_path = '';
							}
						}
					}
				}
			}
		}
		//restrict date editable of started/any user played
		if($gametype == 2){ //Elimination score Tournament
			$fields		=	" * ";
			$condition	=	" fkTournamentsId =".$tournamentId." ";
			$eliminationTourResult	=	$tournamentObj->getTournamentPlayed($fields,$condition);
			if(isset($eliminationTourResult) && is_array($eliminationTourResult) && count($eliminationTourResult)>0){
				$eliminationTour	=	1;
				$tourEditStatus		=	2;
				$dateFlag	=	3;
				if($eliminationTourResult[0]->EndTime !=''){
					$eliminationTour	=	2; //closed tournament
				}
			}
			if(isset($tournamenttype) && $tournamenttype == 4){
				$customRestrict = 0;
			}
		}//High score Tournament

		else if(isset($tournamentDetailsResult[0]->StartDate) && $tournamentDetailsResult[0]->StartDate != '0000-00-00 00:00:00' ){
			if(date('Y-m-d H:i:s',strtotime($tournamentDetailsResult[0]->StartDate)) < $today){ // started
				$tourEditStatus		=	2;
			}//else Started
		}
		if(isset($tournamenttype) && $tournamenttype == 4){ //Custom prize tournament
			$condition	=	" AND fkTournamentsId = ".$tournamentId." AND Status = 1 ORDER BY PrizeOrder ASC ";//PrizeOrder
			$fields 	=	" * ";
			$customPrizeResult  = $tournamentObj->getCustomPrize($fields,$condition);
		}
	}
}
if(isset($_POST['submit'])	&&	$_POST['submit']!="")
{
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	$alreadyExist	=	0;
	$condition 		=	'';
	if(isset($_POST['tournament_id']) && $_POST['tournament_id'] != '')
		$condition	.=	' id <> '.$_POST['tournament_id'].'  AND ';
	if(isset($_POST['tournament'])	&&	trim($_POST['tournament'])!=""){
		$fields				=	' id,TournamentName,Status ';
		$condition			.=	" TournamentName ='".trim($_POST['tournament'])."' AND  Status !=3 AND TournamentStatus !=3 ";
		$checkTournamentExist	=	$tournamentObj->selectTournament($fields,$condition);
		if(isset($checkTournamentExist)	&&	is_array($checkTournamentExist)	&&	count($checkTournamentExist) > 0)	{
			if(strcasecmp($checkTournamentExist[0]->TournamentName,trim($_POST['tournament']) == 0))
				$alreadyExist	=	1;
		}
	}
	if($alreadyExist == 0){
		if($_POST['submit'] == 'Save')	{
			if(isset($_POST['tournament_id']) && $_POST['tournament_id'] != '')	{
				$tournamentId	=	$_POST['tournament_id'];
				$brandId 		=	isset($_POST['hidden_brandid']) ? $_POST['hidden_brandid'] : 0;
				$devId 			=	isset($_POST['hidden_gamedevid']) ? $_POST['hidden_gamedevid'] : 0;
				$condition	=	" id= ".$_POST['tournament_id'];
				$fields	=	'';
				if(isset($_POST['tournament']) && $_POST['tournament'] !='')
					$fields	.=	" TournamentName =	'".trim($_POST['tournament'])."', ";
				if(isset($_POST['elimination'])	&& $_POST['elimination'] !='')
					$fields	.=	" Elimination =	'".$_POST['elimination']."', ";

				if(isset($_POST['gen_terms_condition'])	&& $_POST['gen_terms_condition'] !='')
					$fields	.=	" TermsAndCondition =	'".$_POST['gen_terms_condition']."', ";
				if(isset($_POST['gen_game_rules'])	&& $_POST['gen_game_rules'] !='')
					$fields	.=	" TournamentRule =	'".$_POST['gen_game_rules']."', ";
				if(isset($_POST['gen_tournmentrules'])	&& $_POST['gen_tournmentrules'] !='')
					$fields	.=	" GftRules =	'".$_POST['gen_tournmentrules']."', ";
				if(isset($_POST['gen_privacy_policy_tab'])	&& $_POST['gen_privacy_policy_tab'] !='')
					$fields	.=	" PrivacyPolicy =	'".$_POST['gen_privacy_policy_tab']."', ";
				// added 20141208 starts elimination
				if(isset($_POST['can_we_start'])	&&	$_POST['can_we_start'] != ''){
					$fields	.=	"DelayTime 	= 	'".$_POST['can_we_start']."',";
				}
				// added 20141208 ends elimination

				if((isset($_POST['location'])	&&	trim($_POST['location'] != '')) || (isset($_POST['location_restriction'])	&&	trim($_POST['location_restriction'] == '1')))
					$fields	.=	"LocationRestrict	=	1,";
				else
					$fields	.=	"LocationRestrict	=	0,";

				if((isset($_POST['pin'])	&&	trim($_POST['pin']) != ''))
					$fields	.=	"PIN	=	'1',";
				else
					$fields	.=	"PIN	=	'',";
				if((isset($_POST['pinNumber'])	&&	trim($_POST['pinNumber']) != ''))
					$fields	.=	"TournamentPin	=	'".trim($_POST['pinNumber'])."',";
				else
					$fields	.=	"TournamentPin	=	'',";

				if(isset($_POST['location'])	&&	trim($_POST['location'] == 1)){
					$fields	.=	" LocationBased		=	0, ";
					$fields .=	" fkCountriesId 	= 	0, ";
					$fields .=	" fkStatesId 		= 	0, ";
					$fields .=	" Latitude 			= 	0, ";
					$fields .=	" Longitude 		= 	0, ";
					$fields .=	" TournamentLocation = 	'', ";
				}
				$fields	.= "DateModified		=	'".date('Y-m-d H:i:s')."'";
				$tournamentObj->updateTournamentDetails($fields,$condition);
			//START Location restriction insert
			if(isset($_POST['location'])	&&	trim($_POST['location'] == 1)){

				if(isset($_POST['locationedit']) && !empty($_POST['locationedit']) && count($_POST['locationedit']) > 0) {
					$resfields	=	" Status = 2 ";
					$upids		=	implode(',',$_POST['locationedit']);
					$resLocCon	=	" id not in (".$upids.") and fkTournamentsId='".$tournamentId."' and fkBrandsId='".$brandId."' ";
					$tournamentObj->updateRestrictedLocation($resfields,$resLocCon);

					for($loci=0;$loci<count($_POST['locationedit']);$loci++) {
						$query = "fkTournamentsId = '".$tournamentId."', fkBrandsId = '".$brandId."' ";
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
						$tournamentObj->updateRestrictedLocation($query,$con);
					}
					$loccount	=	count($_POST['locationedit']);
					for($loci=$loccount;$loci<count($_POST['countryLocation']);$loci++) {
						$query = "fkTournamentsId = '".$tournamentId."', fkBrandsId = '".$brandId."' ";
						if(isset($_POST['countryLocation'][$loci]))
							$query	.=	", fkCountriesId='".$_POST['countryLocation'][$loci]."'";
						if(isset($_POST['stateLocation'][$loci]))
							$query	.=	", fkStatesId='".$_POST['stateLocation'][$loci]."'";
						if(isset($_POST['locationsearch'][$loci]))
							$query	.=	", LocationValue='".$_POST['locationsearch'][$loci]."'";
						if(isset($_POST['uslatitude'][$loci]))
							$query	.=	", Latitude='".$_POST['uslatitude'][$loci]."'";
						if(isset($_POST['uslongitude'][$loci]))
							$query	.=	", Longitude='".$_POST['uslongitude'][$loci]."'";
						$tournamentObj->insertRestrictedLocation($query);
					}
				} else {
					$resfields	=	" Status = 2 ";
					$resLocCon	=	" fkTournamentsId='".$tournamentId."' and fkBrandsId='".$brandId."' ";
					$tournamentObj->updateRestrictedLocation($resfields,$resLocCon);
					if(isset($_POST['countryLocation']) && isset($_POST['stateLocation']) && isset($_POST['locationsearch']) ) {
						$loccount	=	count($_POST['countryLocation']);
						if($loccount >  0 && count($_POST['stateLocation']) == $loccount  && count($_POST['locationsearch']) == $loccount ) {
							for($loci=0;$loci<$loccount;$loci++) {
								$query = "fkTournamentsId = '".$tournamentId."', fkBrandsId = '".$brandId."' ";
								if(isset($_POST['countryLocation'][$loci]))
									$query	.=	", fkCountriesId='".$_POST['countryLocation'][$loci]."'";
								if(isset($_POST['stateLocation'][$loci]))
									$query	.=	", fkStatesId='".$_POST['stateLocation'][$loci]."'";
								if(isset($_POST['locationsearch'][$loci]))
									$query	.=	", LocationValue='".$_POST['locationsearch'][$loci]."'";
								if(isset($_POST['uslatitude'][$loci]))
									$query	.=	", Latitude='".$_POST['uslatitude'][$loci]."'";
								if(isset($_POST['uslongitude'][$loci]))
									$query	.=	", Longitude='".$_POST['uslongitude'][$loci]."'";
								$tournamentObj->insertRestrictedLocation($query);
							}
						}
					}
				}
			}else {
				$resfields	=	" Status = 2 ";
				$resLocCon	=	" fkTournamentsId='".$tournamentId."' ";
				$tournamentObj->updateRestrictedLocation($resfields,$resLocCon);
			}
			//END Location restriction insert

			//BEGIN: Coupon Entry
			$updateString	=	$imageName =  $couponText	=	$title = $couponstartdate =  $couponenddate = $fileName = "";
			$fileFlag	=	0;$status	=	1;
			$couponLimit	= "";
			$couponId	=	0;
			if(isset($_POST['coupon_text_id']) && $_POST['coupon_text_id'] !=''){ // Update entry
				if(isset($_POST['coupon_title']) )			$title			=	$_POST['coupon_title'];
				if(isset($_POST['coupon_limit']) )			$couponLimit 	=	$_POST['coupon_limit'];
				if(isset($_POST['coupon_text']) )			$couponText 	=	$_POST['coupon_text'];
				if(isset($_POST['coupon_startdate']) && $_POST['coupon_startdate'] != '0000-00-00 00:00:00' )		$couponstartdate 	=	date('Y-m-d',strtotime($_POST['coupon_startdate']));
				if(isset($_POST['coupon_enddate']) && $_POST['coupon_enddate'] != '0000-00-00 00:00:00' )			$couponenddate 		=	date('Y-m-d',strtotime($_POST['coupon_enddate']));
				$updateString	= " CouponAdLink ='".$couponText."',CouponTitle='".$title."',CouponStartDate='".$couponstartdate."',CouponEndDate='".$couponenddate."',Status=1,DateModified='".$today."' ,CouponLimit = ".$couponLimit." ";
				$condition	=	" id=".$_POST['coupon_text_id']." ";
				$couponId	=	$_POST['coupon_text_id'];
				//update Query
				$tournamentObj->updateTournamentCouponAdLink($updateString,$condition);
			}
			else { // new entry
				if(isset($_POST['coupon_title']) )			$title			=	$_POST['coupon_title'];
				if(isset($title) && !empty($title)){
					if(isset($_POST['coupon_limit']) )			$couponLimit 	=	$_POST['coupon_limit'];
					if(isset($_POST['coupon_text']) )			$couponText 	=	$_POST['coupon_text'];
					if(isset($_POST['coupon_startdate']) && $_POST['coupon_startdate'] != '0000-00-00 00:00:00' )		$couponstartdate 	=	date('Y-m-d',strtotime($_POST['coupon_startdate']));
					if(isset($_POST['coupon_enddate']) && $_POST['coupon_startdate'] != '0000-00-00 00:00:00' )			$couponenddate 		=	date('Y-m-d',strtotime($_POST['coupon_enddate']));
					$queryString	=	"(".$tournamentId.",0,'".$couponText."','','','".$title."','".$couponstartdate."','".$couponenddate."',1,0,1,'".$today."','".$today."',".$couponLimit.")";
					$queryString	=	' VALUES '.$queryString;
					$couponId		=	$tournamentObj->insertYoutubeLink($queryString);
				}
			}
			if(!empty($couponId) && $couponId !=0){
				$insertId	=	$tournamentId;
				if(isset($_POST['coupon_file_upload']) && $_POST['coupon_file_upload'] !=''){
					$ext = pathinfo($_POST['coupon_file_upload'], PATHINFO_EXTENSION);
					$fileFlag	=	1;
					$uploadPath	=	UPLOAD_COUPON_PATH_REL;
					if ( !file_exists($uploadPath.$insertId) ){
						mkdir ($uploadPath.$insertId, 0777);
					}
					$fileName	=	'coupon_'.$couponId.'_'.time().'.'.$ext;
					$imageName 	=	 $insertId.'/'.$fileName;
					$temp_image_path 			=	TEMP_USER_IMAGE_PATH_REL . $_POST['coupon_file_upload'];
					$image_path 				=	$uploadPath.$imageName;
					$oldCoupon	= '';
					if(isset($_POST['old_coupon_file'])){
						$oldCoupon				=	$_POST['old_coupon_file'];
					}
					copy($temp_image_path,$image_path);
					if (SERVER){
						if($oldCoupon!='') {
							if(image_exists(11,$insertId.'/'.$oldCoupon)) {
								deleteImages(11,$insertId.'/'.$oldCoupon);
							}
						}

						echo uploadImageToS3($image_path,11,$imageName); // image_path

						unlink($image_path);

					}
					else if ( $oldCoupon !='' && file_exists(COUPON_IMAGE_PATH_REL.$insertId.'/'.$oldCoupon) ){
						unlink(COUPON_IMAGE_PATH_REL.$insertId.'/'.$oldCoupon);
					}
				}

				if($fileName !=''){
					$updateString	= " File='".$fileName."' ";
					$condition	=	" id=".$couponId." ";
					//update image details
					$tournamentObj->updateTournamentCouponAdLink($updateString,$condition);

				}
			}
			//END: Coupon Entry
			//BEGIN: Banner Entry
			$updateString	=	$imageName = $fileName = $couponText	=	$bannerText	=	$bannerLink	=	"";
			$bannerId	=	$inputType	=	0;

			if(isset($_POST['banner_text_id']) && $_POST['banner_text_id'] !=''){ // Update entry
				if(isset($_POST['banner_type']) )			$inputType		=	$_POST['banner_type'];
				if(isset($_POST['banner_text']) )			$bannerText 	=	$_POST['banner_text'];
				if(isset($_POST['banner_link']) )			$bannerLink 	=	$_POST['banner_link'];
				$updateString	= " CouponAdLink ='".$bannerText."',URL='".$bannerLink."',InputType=".$inputType.",Status=1,DateModified='".$today."' ";
				$condition	=	" id=".$_POST['banner_text_id']." ";
				$bannerId	=	$_POST['banner_text_id'];
				//update Query
				$tournamentObj->updateTournamentCouponAdLink($updateString,$condition);
			}
			else { // new entry
				if(isset($_POST['banner_type']) )			$inputType		=	$_POST['banner_type'];
				if($inputType !=0){
					if(isset($_POST['banner_text']) )			$bannerText 	=	$_POST['banner_text'];
					if(isset($_POST['banner_link']) )			$bannerLink 	=	$_POST['banner_link'];
					$queryString	=	"(".$tournamentId.",0,'".$bannerText."','".$bannerLink."','','','','',2,".$inputType.",1,'".$today."','".$today."',0)";
					$queryString	=	' VALUES '.$queryString;
					$bannerId		=	$tournamentObj->insertYoutubeLink($queryString);
				}
			}
			$fileName = '';

			if(!empty($bannerId) && $bannerId !=0 && $inputType == 2){
				$insertId	=	$tournamentId;
				if(isset($_POST['banner_file_upload']) && $_POST['banner_file_upload'] !=''){
					$ext = pathinfo($_POST['banner_file_upload'], PATHINFO_EXTENSION);
					$uploadPath	=	UPLOAD_BANNER_PATH_REL;
					if ( !file_exists($uploadPath.$insertId) ){
						mkdir ($uploadPath.$insertId, 0777);
					}
					$fileName		=	'banner_' .$bannerId.'_'.time().'.'.$ext;
					$imageName 		=	 $insertId.'/'.$fileName;
					$temp_image_path 			=	TEMP_USER_IMAGE_PATH_REL . $_POST['banner_file_upload'];
					$image_path 				=	$uploadPath.$imageName;
					$oldBanner	= '';
					if(isset($_POST['old_banner_file'])){
						$oldBanner				=	$_POST['old_banner_file'];
					}
					copy($temp_image_path,$image_path);

					if(SERVER){
						if($oldBanner!='') {
							if(image_exists(12,$insertId.'/'.$oldBanner)) {
								deleteImages(12,$insertId.'/'.$oldBanner);
							}
						}
						uploadImageToS3($image_path,12,$imageName); // image_path
						unlink($image_path);
					}
					else if ( $oldBanner!='' && file_exists(BANNER_IMAGE_PATH_REL.$insertId.'/'.$oldBanner) ){
						unlink(BANNER_IMAGE_PATH_REL.$insertId.'/'.$oldBanner);
					}
				}
				if($fileName !=''){
					$updateString	= " File='".$fileName."' ";
					$condition	=	" id=".$bannerId." ";
					//update image details
					$tournamentObj->updateTournamentCouponAdLink($updateString,$condition);
				}
			}
			//END: Banner Entry
			$url =  $code = '';
			$inputType = 0;
			//BEGIN: Link Entry
			if(isset($_POST['youtube_text_id']) && $_POST['youtube_text_id'] !=''){ // Update entry
				if(isset($_POST['youtube_type']) )			$inputType		=	$_POST['youtube_type'];
				if(isset($_POST['youtube_text']) )			$url 	=	$_POST['youtube_text'];
				if(isset($_POST['youtube_code']) )			$code 	=	$_POST['youtube_code'];
				$condition	=	" id=".$_POST['youtube_text_id']." ";
				$updateString	= " URL = '".$url."',CouponAdLink='".$code."',Status=1,InputType=".$inputType.",DateModified='".$today."' ";
				$condition	=	" id=".$_POST['youtube_text_id']." ";
				$linkId	=	$_POST['youtube_text_id'];
				//update Query
				$tournamentObj->updateTournamentCouponAdLink($updateString,$condition);
			}
			else { // new entry
				if(isset($_POST['youtube_type']) )			$inputType		=	$_POST['youtube_type'];
				if($inputType !=0){
					if(isset($_POST['youtube_text']) )			$url 	=	$_POST['youtube_text'];
					if(isset($_POST['youtube_code']) )			$code 	=	$_POST['youtube_code'];
					$queryString	=	$fileName	=	'';
					$queryString	=	"(".$tournamentId.",0,'".$code."','".$url."','','','','',3,".$inputType.",1,'".$today."','".$today."',0)";
					$queryString	=	' VALUES '.$queryString;
					$linkId		=	$tournamentObj->insertYoutubeLink($queryString);
				}
			}
			if(!empty($linkId) && $linkId !=0){
				$insertId	=	$tournamentId;
				$fileName	=	'';
				if(isset($_POST['link_file_upload']) && $_POST['link_file_upload'] !=''){
					$ext = pathinfo($_POST['link_file_upload'], PATHINFO_EXTENSION);
					$uploadPath	=	UPLOAD_YOUTUBE_LINK_PATH_REL;
					if ( !file_exists($uploadPath.$insertId) ){
						mkdir ($uploadPath.$insertId, 0777);
					}
					$fileName	=	'link_'.$linkId.'_'.time().'.'.$ext;
					$imageName 	=	 $insertId.'/'.$fileName;
					$temp_image_path 			=	TEMP_USER_IMAGE_PATH_REL . $_POST['link_file_upload'];
					$image_path 				=	$uploadPath.$imageName;
					$oldLink	= '';
					if(isset($_POST['old_link_file'])){
						$oldLink				=	$_POST['old_link_file'];
					}
					copy($temp_image_path,$image_path);
					if (SERVER){
						if($oldLink!='') {
							if(image_exists(14,$insertId.'/'.$oldLink)) {
								deleteImages(14,$insertId.'/'.$oldLink);
							}
						}
						uploadImageToS3($image_path,14,$imageName); // image_path
						unlink($image_path);
					}
					else if ( $oldLink !='' && file_exists(YOUTUBE_LINK_IMAGE_PATH_REL.$insertId.'/'.$oldLink) ){
						unlink(YOUTUBE_LINK_IMAGE_PATH_REL.$insertId.'/'.$oldLink);
					}
				}
				if($fileName !=''){
					$updateString	= " File='".$fileName."' ";
					$condition	=	" id=".$linkId." ";
					//update image details
					$tournamentObj->updateTournamentCouponAdLink($updateString,$condition);
				}
			}
			//END: Link Entry
			//BEGIN: Rules integration
			$unsetStates =	$unsetCountries	=	'';

				if(isset($_POST['country'])	&&	is_array($_POST['country'])	&& count($_POST['country']) > 0 ){
					$countryIds	=	$_POST['country'];
					//change all entry status to in active
						$updateString	=	" Status=2 ,DateModified='".$today."' ";
						$condition	=	" fkTournamentsId=".$tournamentId." ";
						$tournamentObj->updateTournamentRules($updateString,$condition);

					$stateFlag	=	0;
					$countryEntry	=	$usEntryPair	=	$usStateIds	=	$otherCountryIds	=	'';
					$countryEntries	= $usEntries = $existStateArrays = $existArrays = $unsetCountryArray = $unsetStateArray = $tempArray = array();

					if(isset($_POST['state'])	&&	is_array($_POST['state'])	&& count($_POST['state']) > 0){
						$stateFlag	=	1;
						$stateIds	=	$_POST['state'];
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
						 $condition		=	'fkCountriesId IN ('.$countryEntry.') AND fkTournamentsId ='.$tournamentId.' ';
						$countryEntryResult		=	$tournamentObj->checkRulesEntry($fields,$condition);
						if(isset($countryEntryResult) && is_array($countryEntryResult) && count($countryEntryResult) > 0){
							foreach($countryEntryResult as $existKey => $existCountry){
								$existArrays[]	=	$existCountry->fkCountriesId;
							}
						}
					}
					$usEntryPair	=	rtrim($usEntryPair,',');
					if($usEntryPair !=''){
						$fields			=	'*';
						 $condition		=	'fkStatesId IN ('.$usEntryPair.') AND fkTournamentsId ='.$tournamentId.' AND fkCountriesId = '.$usId.' ';
						$usEntryResult		=	$tournamentObj->checkRulesEntry($fields,$condition);
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
										$tournamentObj->updateTournamentRules($updateString,$condition);
									}
									else{	// new entry to state
										if(isset($usEntries[$entry[1]])){
											$stateValues		.=	"(".$tournamentId.",0,".$entry[0].",".$usEntries[$entry[1]]['stateId'].",'".$usEntries[$entry[1]]['rule']."','".$usEntries[$entry[1]]['condition']."','".$usEntries[$entry[1]]['thirdtournametrule']."','".$usEntries[$entry[1]]['privacyPolicySingle']."','".$today."','".$today."',1),";
											$stateValues	=	' VALUES '.rtrim($stateValues,',');
											$tournamentObj->insertRules($stateValues);
										}
									}
								}
								else {
								$id	=	$entry[0];
									if(in_array($id,$existArrays)	&&	isset($countryEntries[$id])){	//update country rules
										$updateString	=	" TournamentRules='".$countryEntries[$id]['rule']."',TermsAndConditions='".$countryEntries[$id]['condition']."',GftRules='".$countryEntries[$id]['thirdtournametrule']."',PrivacyPolicy='".$countryEntries[$id]['privacyPolicySingle']."',DateModified='".$today."',Status=1 ";
										$condition	    =	" fkTournamentsId=".$tournamentId."  AND fkCountriesId=".$id." ";
										$tournamentObj->updateTournamentRules($updateString,$condition);
									}
									else { // new entry to country
										if(isset($countryEntries[$id])){
											$countryValues		.=	"(".$tournamentId.",0,".$id.",0,'".$countryEntries[$id]['rule']."','".$countryEntries[$id]['condition']."','".$countryEntries[$id]['thirdtournametrule']."','".$countryEntries[$id]['privacyPolicySingle']."','".$today."','".$today."',1),";
											$countryValues	=	' VALUES '.rtrim($countryValues,',');
											$tournamentObj->insertRules($countryValues);
										}
									}
								}
							}
						}
					}
					$stateValues	=	rtrim($stateValues,',');
					$countryValues	=	rtrim($countryValues,',');
					if($stateValues !=''){
						$stateValues	=	' VALUES '.rtrim($stateValues,',');
					}
					if($countryValues !=''){
						$countryValues	=	' VALUES '.rtrim($countryValues,',');
					}
				}
			//END: Rules Integration
			//***********END:  Custom Prize **********************
			$_SESSION['notification_msg_code']	=	2;
			header("location:TournamentList");
			die();
			}
		}
		else if($_POST['submit']		==	'Add')	{
			header("location:TournamentList?cs=1");
			die();
		}
	}else if($alreadyExist == 1){
		$class		= "error";
		$error_msg 	= "Tournament already exist";
	}
}
$readOnly	=	"";
if($tourEditStatus == 2){
	$readOnly	=	'disabled="true"';
}
?>
<body onload="return fieldfocus('<?php echo $field_focus; ?>');">
	<?php top_header(); ?>
	<div id="loading" style="display:none;"><i class="fa fa-spinner fa-spin fa-4x"></i></div>
	<div class="box-header">
		<h2><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo "<i class='fa fa-edit'></i> Edit "; else echo "<i class='fa fa-plus-circle'></i> Add ";?>Tournament</h2>
	</div>
	<div class="clear">
	<form name="add_tournament_form" id="add_tournament_form" action="" method="post">
		<input type="Hidden" name="tournament_id" id="tournament_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
		<input type='hidden' id='temp_hidden_lat' name='temp_hidden_lat' value="">
		<input type='hidden' id='temp_hidden_long' name='temp_hidden_long' value="">
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
			<tr><td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">

					<tr><td colspan="7" align="center" class="msg_height" valign="top"><div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span></div></td></tr>

					<tr>
						<td width="15%" height="50" align="left"  valign="top"><label>Tournament Name&nbsp;<span class="required_field">*</span></label></td>
						<td width="3%" align="center"  valign="top">:</td>
						<td width="32%" align="left"  height="40"  valign="top">
						<input type="text" class="input" id="tournament" name="tournament" value="<?php if(isset($tournament) && $tournament != '') echo $tournament;  ?>" maxlength="150">
						</td>
						<?php if(isset($createdBy) && $createdBy == 3 ){ ?>
							<td width="12%"  height="50"  align="left"  valign="top"><label>Developer & Brand&nbsp;</label></td>
							<td width="2%" align="center"  valign="top">:</td>
							<td width="30%" align="left"  height="40"  valign="top">
								<?php echo ucFirst($gameDeveloper); ?>
								<input type='hidden' id='hidden_gamedevid' name='hidden_gamedevid' value="<?php  if(isset($DevId) && $DevId != '') echo $DevId;  ?>">
							</td>
						<?php } ?>
					</tr>
					<tr>
						<td height="50" align="left" valign="top"><label>Game&nbsp;<span class="required_field">*</span></label></td>
						<td  align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top">
							<select name="game" id="game" style="width:93%;" class="input" <?php echo $readOnly;?> disabled>
								<option value="">Select</option>
								<?php if(isset($gameDetailsResult) && is_array($gameDetailsResult) && count($gameDetailsResult) > 0){
										foreach($gameDetailsResult as $key => $value) { ?>
												<option value="<?php echo $value->gameId; ?>" <?php  if(isset($game) && $game != '' && $game == $value->gameId) echo 'Selected';  ?>><?php echo $value->Name; ?></option>
									<?php	}
									}?>
							</select>
						</td>

						<td align="left" valign="top"><label>Game Type</label></td>
						<td align="center" valign="top">:</td>
						<td align="left" valign="top">
							<input type="hidden" name="game_type" id="game_type"  value="<?php echo $gametype;?>" >
							<label for="game_score">
							<input type="radio" name="game_type" required id="game_score"  value="1" <?php if(isset($gametype) && $gametype == 1) echo ' checked '?> disabled="true">&nbsp;&nbsp; High Score &nbsp;&nbsp;&nbsp;</label>
							<label for="game_elimination">
							<input type="radio" name="game_type" id="game_elimination" value="2" <?php if(isset($gametype) && $gametype == 2) echo 'checked' ?> disabled="true">&nbsp;&nbsp; Elimination</label>
						<?php //}?>
						</td>
					</tr>
					<tr>
						<?php if(isset($gametype) && $gametype==2){?>

						<?php }?>
						<td height="50" align="left"  valign="top"><label>Maximum Player&nbsp;<span class="required_field">*</span></label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top">
							<input type="text" class="input w50" id="maxplayer" name="maxplayer" onkeypress="return isNumberKey(event);" value="<?php if(isset($maxplayer) && $maxplayer != '') echo $maxplayer;  ?>" <?php echo 'disabled="true"';?>>
						</td>

						<td height="50" align="left"  valign="top"><label>No. of Turns&nbsp;<span class="required_field">*</span></label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top">
							<input type="text" class="input w50" id="turns" name="turns" onkeypress="return isNumberKey(event);" value="<?php if(isset($gametype) && $gametype==2) echo "1"; else if(isset($turns) && $turns != '') echo $turns;  ?>" disabled="true">
							<span >(per day)</span>
						</td>
					</tr>
					<tr height="50">
						<td height="50" align="left"  valign="top"><label>Entry Fee&nbsp;<span class="required_field">*</span></label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top">
						<label><input type="Radio" value="1"  id="entryfee_free" name="entryfee_flag" <?php if(isset($entryfee_flag) && $entryfee_flag == 1	) echo 'checked';?>  disabled="true"> &nbsp;Free</label>&nbsp;&nbsp;
						<label><input type="Radio" value="2" id="entryfee_paid" name="entryfee_flag"  <?php if(isset($entryfee_flag) && $entryfee_flag == 2) echo 'checked';?>  disabled="true"> &nbsp;Pay</label>&nbsp;&nbsp;
						<input type="text" class="input" id="entryfee" name="entryfee" maxlength="12" onkeypress="return isNumberKey(event);" style="width:90px" value="<?php if(isset($entryfee) && $entryfee != 0) echo $entryfee;  ?>" disabled="true">&nbsp;<?php if(isset($entryfee_flag) && $entryfee_flag == 2) echo ($tournamenttype == 2)?'TiLT$': ($tournamenttype == 3 ?  'Virtual Coins' : '') ?>
						</td>

						<td height="50" align="left"  valign="top"><label>Prize</label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top">
						<?php if(isset($tournamenttype) && $tournamenttype == 4) echo "Custom Prize"; else {?>
							<input type="text" class="input w50" name="prize" id="prize" onkeypress="return isNumberKey(event);" maxlength="12" value="<?php  if(isset($prize) && $prize != '' ) echo $prize;   ?>" <?php echo 'disabled="true"'?>>&nbsp;<?php echo (($tournamenttype == 2)? 'TiLT$': (($tournamenttype == 3) ?  'Virtual Coins' : '' ))?>
						<?php } ?>
						<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
							<input type="hidden" id="tournament_type" name="tournament_type" value="<?php echo $tournamenttype;?>">
						<?php }?>
						</td>
					</tr>
					<tr>
						<td height="50" align="left"  valign="top"><label>Start Date&nbsp;&nbsp;<span class="required_field">*</span></label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top" >
							<input  type="text" disabled="disabled" class="input datetimepicker w50" readonly name="startdate" id="start_time" value="<?php if(isset($startdate) && $startdate != '0000-00-00 00:00:00') echo date('m/d/Y H:i',strtotime($startdate)); else echo '';?>">
						</td>
						<?php if($gametype == 2){ ?>
							<td height="50" align="left"  valign="top"><label>Can Start&nbsp;&nbsp;<span class="required_field">*</span></label></td>
							<td align="center"  valign="top">:</td>
							<td align="left"  height="40"  valign="top" >
								<input type="text" class="input w50" placeholder="hh:mm:ss" name="can_we_start" id="can_we_start" value="<?php if(isset($can_we_start) && $can_we_start != '00:00:00') echo $can_we_start; ?>" maxlength="8" onkeypress="return timeField(event);" >
							</td>
						<?php }else{ ?>
							<td height="50" align="left"  valign="top"><label>End Date&nbsp;&nbsp;<span class="required_field">*</span></label></td>
							<td align="center"  valign="top">:</td>
							<td align="left"  height="40"  valign="top" >
								<input type="text"  autocomplete="off" disabled="disabled" class="input datetimepicker w50" readonly name="enddate" id="end_time"  value="<?php if(isset($enddate) && $enddate != '0000-00-00 00:00:00') echo date('m/d/Y H:i',strtotime($enddate)); else echo '';?>" >
							</td>
						<?php } ?>
					</tr>
					<?php if($gametype == 2){ ?>
					<tr>

						<td height="50" align="left"  valign="top"><label>Play Time&nbsp;&nbsp;</label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top" >
							<input type="text" class="input w50" placeholder="hh:mm:ss" name="play_time" id="play_time" value="<?php if(isset($play_time) && $play_time !='00:00:00') echo $play_time; ?>" disabled>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<?php if(isset($string) && $string != '')	{?>
							<td height="50" align="left" valign="top"><label>Tournament Type</label></td>
							<td align="center" valign="top">:</td>
							<td align="left" valign="top">
							<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){
								if(isset($string) && $string != '') echo $string;  else echo '-';?>
							<?php } else { ?>
							<input class="input" type='text' id='pinbased' name='pinbased' value="<?php if(isset($string) && $string != '') echo $string;  else echo '-'; ?>" readonly >
							<?php } ?>
							</td>
						<?php } ?>
						<td>&nbsp;</td>
						<?php if(isset($location)	&&	$location!='')	{	?>
							<td align="left" valign="top"><label>Location</label></td>
							<td align="center" valign="top">:</td>
							<td align="left" valign="top">
							<input class="input" type='text' id='locationbased' name='locationbased' value="<?php if(isset($location) && !empty($location)) echo $location;  else echo '-'; ?>" readonly >
							</td>
					<?php } ?>
					</tr>
<tr>
	<td height="50" align="left"  valign="top"><label for="location">PIN restricted</label></td>
	<td align="center" valign="top">:</td>
	<td colspan="1" valign="top">
		<input type="Checkbox" onclick="return checkpinrestriction();"
		 id="pin" name="pin" value="1"
		<?php if(isset($pin) && $pin == 1) echo 'checked'; ?>>&nbsp;&nbsp;
	</td>
	<td class="pinNumber" width="14%" height="50" align="left"  valign="top"><label>PIN number<span class="required_field">*</span></label></td>
	<td class="pinNumber" width="3%" align="center"  valign="top">:</td>
	<td class="pinNumber" width="10%" align="left"  height="40"  valign="top">
		<input type="text" class="input w50" id="pinNumber" name="pinNumber"
		value="<?php if(isset($pinNumber) && $pinNumber != '') echo $pinNumber;  ?>" maxlength="12">
	</td>
</tr>
					<?php if($createdBy == '3') { ?>
					<tr>
						<td height="50" align="left"  valign="top"><label for="location">Location Restricted</label></td>
						<td align="center" valign="top">:</td>
						<td colspan="5" valign="top">
							<input type="Checkbox" onclick="return checklocationrestriction();"  id="location" name="location" value="1" <?php if(isset($locationRestrict) && $locationRestrict == 1) echo 'checked'; ?>>&nbsp;&nbsp;
							<?php if(isset($locationRestrictResult) && is_array($locationRestrictResult) && count($locationRestrictResult) > 0) {
									$countLocationRestrict = count($locationRestrictResult);
									$inc	=	0; ?>
									<table id="restrictTable" cellpadding="0" cellspacing="0" width="100%">
									<tbody>
											<tr>
												<th align="left" width="5%">&nbsp;</th>
												<th align="left" width="30%"><label style="padding-left: 4px;padding-bottom: 8px;">Country</label></th>
												<th align="left" id="state_location" style="display:none;padding-left: 4px;padding-bottom: 8px;"><label>State</label></th>
												<th align="left" id="location_state" style="display:none;padding-left: 4px;padding-bottom: 8px;"><label>Location</label></th>
											</tr>
											<tr id="location_clone" class="clone" clone="1" style="display:none">
												<td align="left" valign="top" height="50">
													<a href="javascript:void(0)" class="locminus" onclick="manageLocation(this,'2')" id="minus_clone" style="display:none;"><i class="fa fa-lg text-red fa-minus-circle"></i></a>
													<a href="javascript:void(0)" class="locplus" onclick="manageLocation(this,'1')" id="plus_clone"><i class="fa fa-lg  text-green fa-plus-circle"></i></a>
												</td>
												<td align="left" valign="top">
													<select name="country_clone" tabindex="10" class="country" id="country_loc_clone" onchange="locationShow('1',1);">
														<option value="">Select</option>
														<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 ){
															foreach($countryArray as $countryId => $country) {
																?>
															<option value="<?php echo $countryId; ?>" <?php if($countryId == 1) echo 'Selected';  ?>><?php echo $country; ?></option>
														<?php 	} }?>
													</select>
												</td>
												<td align="left" valign="top">
													<div class="state" id="col_loc_clone_3" style="<?php if(!isset($currentLocationid)) echo "display:none;";  ?>">
														<select name="state_clone" tabindex="10" class="state" id="state_loc_clone" style="display:none;" onchange="locationShow('1',2);">
															<option value="">Select</option>
															<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
																foreach($usStateArray as $stateId => $state) {  ?>
																<option value="<?php echo $stateId; ?>" <?php   //echo 'Selected';  ?>><?php echo $state; ?></option>
															<?php 	} ?>
														</select>
														<input type='hidden' class="latitude" id='latitude_clone' name='latitude_clone[]' value="">
														<input type='hidden' class="longitude" id='longitude_clone' name='longitude_clone[]' value="">
													</div>
												</td>
												<td align="left" valign="top">
													<div class="location" id="col_loc_clone_4" style="<?php if(!isset($currentLocationid)) echo "display:none;";  ?>">
														<input type='search' class="locationsearch input" id='locationsearch_clone' name='locationsearch_clone' value="" onkeypress="autoCompleteLocation(1)">
													</div>
												</td>
											</tr>
											</tbody>
									<tbody id="RestrictedLocationContent">
									<?php foreach($locationRestrictResult as $val) { $inc ++; ?>
										<tr id="location_<?php echo $inc; ?>" class="clone" clone="<?php echo $inc; ?>">
											<td width="6%" align="left" valign="top" height="50">
												<a href="javascript:void(0)" class="locminus" onclick="manageLocation(this,'2')" id="minus_<?php echo $inc; ?>" style=""><i class="fa fa-lg text-red  fa-minus-circle"></i></a>
												<a href="javascript:void(0)" class="locplus" onclick="manageLocation(this,'1')" id="plus_<?php echo $inc; ?>" style="<?php if($countLocationRestrict != $inc) echo 'display:none'; ?>"><i class="fa text-green fa-lg fa-plus-circle"></i></a>
											</td>
											<td width="10%" align="left" valign="top">
												<select name="countryLocation[]" tabindex="10" class="country" id="country_loc_<?php echo $inc; ?>" onchange="locationShow('<?php echo $inc; ?>',1);">
													<option value="">Select</option>
													<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
														foreach($countryArray as $countryId => $country) {
															?>
														<option value="<?php echo $countryId; ?>" <?php if(isset($val->fkCountriesId) && $val->fkCountriesId==$countryId) echo 'Selected';  ?>><?php echo $country; ?></option>
													<?php 	} ?>
												</select>
											</td>
											<td width="20%" align="left" valign="top">
												<div class="state" id="col_loc_<?php echo $inc; ?>_3" style="<?php if(!isset($val->fkStatesId) || !isset($usStateArray[$val->fkStatesId])) echo "display:none;";  ?>">
													<select name="stateLocation[]" tabindex="10" class="state" id="state_loc_<?php echo $inc; ?>" style="<?php if(!isset($val->fkStatesId) || !isset($usStateArray[$val->fkStatesId])) echo "display:none;";  ?>" onchange="locationShow('<?php echo $inc; ?>',2);">
														<option value="">Select</option>
														<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
															foreach($usStateArray as $stateId => $state) {  ?>
															<option value="<?php echo $stateId; ?>" <?php   if(isset($val->fkStatesId) && $val->fkStatesId==$stateId) echo 'Selected';  ?>><?php echo $state; ?></option>
														<?php 	} ?>
													</select>
													<input type='hidden' class="latitude" id='latitude_<?php echo $inc; ?>' name='latitude[]' value="<?php if($val->Latitude !='') echo $val->Latitude; ?>">
													<input type='hidden' class="longitude" id='longitude_<?php echo $inc; ?>' name='longitude[]' value="<?php if($val->Longitude !='') echo $val->Longitude; ?>">
												</div>
											</td>
											<td width="20%" align="left" valign="top">
												<div class="location" id="col_loc_<?php echo $inc; ?>_4" style="<?php if(!isset($val->fkStatesId) || !isset($usStateArray[$val->fkStatesId])) echo "display:none;";  ?>">
													<input type='search' class="locationsearch input" id='locationsearch_<?php echo $inc; ?>' name='locationsearch[]' value="<?php   if(isset($val->LocationValue) && $val->LocationValue != '') echo $val->LocationValue;  ?>" onkeypress="autoCompleteLocation(<?php echo $inc; ?>)">
													<input type="hidden" class="locationedit" id="locationedit_<?php echo $inc; ?>" name="locationedit[]" value="<?php echo $val->id; ?> ">
												</div>
											</td>
										</tr>
									<?php  } ?>
									<input type="hidden" name="totLoc" id="totLoc" value="<?php echo $inc; ?>">
									</tbody>
									</table>
							<? } else { ?>
									<table style="display:none" id="restrictTable" width="100%" cellpadding="0" cellspacing="0">
									<tbody>
											<tr>
												<th align="left" width="5%">&nbsp;</th>
												<th align="left" width="10%"><label style="padding-left: 4px;padding-bottom: 8px;">Country</label></th>
												<th align="left" width="20%" id="state_location" style="display:none;"><label style="padding-left: 4px;padding-bottom: 8px;">State</label></th>
												<th align="left" width="20%" id="location_state" style="display:none;"><label style="padding-left: 4px;padding-bottom: 8px;">Location</label></th>
											</tr>
											<tr id="location_clone" class="clone" clone="1" style="display:none">
												<td align="left" height="50" valign="top">
													<a href="javascript:void(0)" class="locminus" onclick="manageLocation(this,'2')" id="minus_clone" style="display:none;"><i class="fa fa-lg text-red fa-minus-circle"></i></a>
													<a href="javascript:void(0)" class="locplus" onclick="manageLocation(this,'1')" id="plus_clone"><i class="fa fa-lg  text-green fa-plus-circle"></i></a>
												</td>
												<td align="left" valign="top">
													<select name="country_clone" tabindex="10" class="country" id="country_loc_clone" onchange="locationShow('1',1);">
														<option value="">Select</option>
														<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 ){
															foreach($countryArray as $countryId => $country) {
																?>
															<option value="<?php echo $countryId; ?>" <?php if($countryId == 1) echo 'Selected';  ?>><?php echo $country; ?></option>
														<?php 	} }?>
													</select>
												</td>
												<td align="left" valign="top">
													<div class="state" id="col_loc_clone_3" style="<?php if(!isset($currentLocationid)) echo "display:none;";  ?>">
														<select name="state_clone" tabindex="10" class="state" id="state_loc_clone" style="display:none;" onchange="locationShow('1',2);">
															<option value="">Select</option>
															<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
																foreach($usStateArray as $stateId => $state) {  ?>
																<option value="<?php echo $stateId; ?>"><?php echo $state; ?></option>
															<?php 	} ?>
														</select>
														<input type='hidden' class="latitude" id='latitude_clone' name='latitude_clone[]' value="">
														<input type='hidden' class="longitude" id='longitude_clone' name='longitude_clone[]' value="">
													</div>
												</td>
												<td align="left" valign="top">
													<div class="location" id="col_loc_clone_4" style="<?php if(!isset($currentLocationid)) echo "display:none;";  ?>">
														<input type='search' class="locationsearch input" id='locationsearch_clone' name='locationsearch_clone' value="" onkeypress="autoCompleteLocation(1)">
													</div>
												</td>
											</tr>
											</tbody>
									<tbody id="RestrictedLocationContent">
										<tr id="location_1" class="clone" clone="1">
											<td width="6%" align="left" height="50" valign="top">
												<a href="javascript:void(0)" class="locminus" onclick="manageLocation(this,'2')" id="minus_1" style=""><i class="fa fa-lg text-red  fa-minus-circle"></i></a>
												<a href="javascript:void(0)" class="locplus" onclick="manageLocation(this,'1')" id="plus_1"><i class="fa text-green fa-lg fa-plus-circle"></i></a>
											</td>
											<td width="10%" align="left" valign="top">
												<select name="countryLocation[]" tabindex="10" class="country" id="country_loc_1" onchange="locationShow('1',1);">
													<option value="">Select</option>
													<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
														foreach($countryArray as $countryId => $country) {
															?>
														<option value="<?php echo $countryId; ?>" <?php if(isset($currentLocationid) && $currentLocationid==$countryId) echo 'Selected';  ?>><?php echo $country; ?></option>
													<?php 	} ?>
												</select>
											</td>
											<td width="20%" align="left" valign="top">
												<div class="state" id="col_loc_1_3" style="<?php if(!isset($currentLocationid)) echo "display:none;";  ?>">
													<select name="stateLocation[]" tabindex="10" class="state" id="state_loc_1" style="display:none;" onchange="locationShow('1',2);">
														<option value="">Select</option>
														<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
															foreach($usStateArray as $stateId => $state) {  ?>
															<option value="<?php echo $stateId; ?>"><?php echo $state; ?></option>
														<?php 	} ?>
													</select>
													<input type='hidden' class="latitude" id='latitude_1' name='latitude[]' value="">
													<input type='hidden' class="longitude" id='longitude_1' name='longitude[]' value="">
												</div>
											</td>
											<td width="20%" align="left" valign="top">
												<div class="location" id="col_loc_1_4" style="<?php if(!isset($currentLocationid)) echo "display:none;";  ?>">
													<input type='search' class="locationsearch input" id='locationsearch_1' name='locationsearch[]' value="" onkeypress="autoCompleteLocation(1)" maxlength="150">
												</div>
											</td>
										</tr>
										<input type="hidden" name="totLoc" id="totLoc" value="1">
									</tbody>
									</table>
							<?php } ?>
					</td></tr>
					<?php } else if($createdBy == '33') { ?>
					<tr>
						<td height="50" align="left"  valign="top"><label for="location">Location Restricted</label></td>
						<td align="center" valign="top">:</td>
						<td colspan="5" valign="top">
							<div class="form-group clear">
								<div class="col-sm-5">
									<label for="location_restriction">
									<input type="hidden" name="locRestStatus" id="locRestStatus"  value="<?php if(isset($locationRestrict) && $locationRestrict == 1) echo '1'; else echo '0'; ?>" >
									<input type="radio" name="location_restriction" id="location_restriction"  value="1"  onclick="enableMap('1');" <?php if(isset($locationRestrict) && $locationRestrict == 1) echo 'checked'; ?> >&nbsp;&nbsp;On&nbsp;|&nbsp;&nbsp;</label>
									<label for="location_restriction1">
									<input type="radio" name="location_restriction" id="location_restriction1" value="0"  onclick="enableMap('2');" <?php if(!isset($locationRestrict) || $locationRestrict != 1) echo 'checked'; ?> >&nbsp;&nbsp;Off&nbsp;</label>
									<a href="#" class="question_icon" title="Set ON to restrict tournament for a particular area. Set OFF will allow the users to play tournament all over the world"></a>
								</div>
							</div>
							<div class="" align="center" id="map"></div>
								<div class="map-description">
									<div class="col-xs-12 game-form-style">
										<div class="clear locationRestrict" >
											<input type="hidden" name="latitude" id="latitude" value="<?php if(isset($latitude) && $latitude !='') echo $latitude;?>" >
											<input type="hidden" name="longitude" id="longitude" value="<?php if(isset($longitude) && $longitude !='') echo $longitude;?>" >
										</div>

										<div class="form-group clear locationRestrict">
											<label>Country</label>
											<div class="location-input">
												<input type="text" class="input" name="lrcountry" id="country" readonly value="<?php if(isset($lrcountry) && $lrcountry !='') echo $lrcountry;?>" >
												&nbsp;&nbsp;<input type="checkbox"  onchange="changeCheckStatus(this,'country');" disabled <?php if(isset($countryCheck) && $countryCheck == 1) echo 'checked'; ?> name="country_check" id="country_check">
												<input type="hidden" name="country_check_hidden" id="country_check_hidden" value="<?php if(isset($countryCheck) && $countryCheck == 1) echo 1; ?>">
											</div>
										</div>

										<div class="form-group locationRestrict">
											<label>State</label>
											<div class="location-input">
												<input type="text" class="input" name="lrstate" id="state" readonly value="<?php if(isset($lrstate) && $lrstate !='') echo $lrstate;?>" >
												&nbsp;&nbsp;<input type="checkbox" onchange="changeCheckStatus(this,'state');" disabled <?php if(isset($stateCheck) && $stateCheck == 1) echo 'checked'; ?> name="state_check" id="state_check">
												<input type="hidden" name="state_check_hidden" id="state_check_hidden" value="<?php if(isset($stateCheck) && $stateCheck == 1) echo 1; ?>">
											</div>
										</div>

										<div class="form-group locationRestrict">
											<label>City</label>
											<div class="location-input">
												<input type="text" class="input" name="lrcity" id="city" <?php if(isset($cityCheck) && $cityCheck == 1) echo 'readonly'; ?> value="<?php if(isset($lrcity) && $lrcity !='') echo $lrcity;?>" >
												&nbsp;&nbsp;<input type="checkbox" onchange="changeCheckStatus(this,'city');" <?php if(isset($cityCheck) && $cityCheck == 1) echo 'checked'; ?> name="city_check" id="city_check">
											</div>
										</div>

										<div class="form-group locationRestrict">
											<label>Zip code</label>
											<div class="location-input">
												<input type="text" class="input" name="lrzip_code" id="zip_code" <?php if(isset($zipcodeCheck) && $zipcodeCheck == 1) echo 'readonly'; ?> value="<?php if(isset($lrzipCode) && $lrzipCode !='') echo $lrzipCode;?>" >
												&nbsp;&nbsp;<input type="checkbox" onchange="changeCheckStatus(this,'zip_code');" <?php if(isset($zipcodeCheck) && $zipcodeCheck == 1) echo 'checked'; ?> name="zipcode_check" id="zipcode_check">
											</div>
										</div>

										<div class="form-group locationRestrict">
											<label>Address</label>
											<div class="location-input">
												<input type="text" class="input" name="lraddress" id="address"  <?php if(isset($addressCheck) && $addressCheck == 1) echo 'readonly'; ?> value="<?php if(isset($lraddress) && $lraddress !='') echo $lraddress;?>" >
												&nbsp;&nbsp;<input type="checkbox" onchange="changeCheckStatus(this,'address');" <?php if(isset($addressCheck) && $addressCheck == 1) echo 'checked'; ?> name="address_check" id="address_check">
											</div>
										</div>
									</div>
								</div>
					</td></tr>
					<?php } ?>
					<?php if(isset($tournamenttype) && $tournamenttype == 4 && isset($customPrizeResult) && is_array($customPrizeResult) && count($customPrizeResult)>0){
						$count	=	count($customPrizeResult);?>
						<tr><th colspan="6" align="left"><h2 style="padding:0px;">Custom Prize</h2></th></tr>
						<tr><td height="20"></td></tr>
						<tr><td colspan="7">
							<table cellpadding="0" cellspacing="0" align="center" border="0" width="100%" id="custom_prizeTable">
								<tr height="40" clone="0">
									<th width="30%" align="left" style="padding-left:5px;">Prize name</th>
									<th width="10%" align="left" style="padding-left:5px;">Image</th>
									<th width="30%" align="left" style="padding-left:5px;">Description</th>
								</tr>
							<?php foreach($customPrizeResult as $key=>$value) {
								$addMoreBtn	=	' style="display:none" ';
								if($customRestrict)
									if(($key+1) == $count) $addMoreBtn	=	''; ?>
								<tr clone="<?php echo ($key+1);?>" height="120">
									<td align="left" valign="top">
										<input type="text" class="input prize_title" style="width:60%;" name="prizeTitle[]" id="prize_title<?php echo ($key+1);?>" value="<?php if(isset($value->PrizeTitle) && $value->PrizeTitle != '') echo $value->PrizeTitle;?>" disabled>
										<input type="hidden" name="prizeId[]" value="<?php if(isset($value->id) && $value->id != '') echo $value->id;?>">
									</td>
									<td align="left" valign="top" width="15%" class="upload_td">
									<?php $prizeImageName = $prize_image_path = "";
									if(isset($value->PrizeImage) && $value->PrizeImage != ''){
										$prizeImage = $tournamentId.'/'.$value->PrizeImage;
										$prizeImageName = $value->PrizeImage;
										if(SERVER){
											if(image_exists(20,$prizeImage)){
												$prize_image_path = CUSTOM_PRIZE_IMAGE_PATH.$prizeImage;
											}
										}
										else if(file_exists(CUSTOM_PRIZE_IMAGE_PATH_REL.$prizeImage)){
												$prize_image_path = CUSTOM_PRIZE_IMAGE_PATH.$prizeImage;
										}
									 } ?>
										<div id="custom_prize<?php echo ($key+1);?>_img">
											<?php if(isset($prize_image_path) && $prize_image_path != '') {?>
												<a href=<?php echo '"'.$prize_image_path.'"'; ?> class="image_pop_up" title="Click here" alt="Click here" >
												<img  src="<?php echo CUSTOM_PRIZE_IMAGE_PATH.$prizeImage; ?>" width="75" height="75" >
												<input type="hidden" value="oldimage" id="custom_prize<?php echo ($key+1);?>_upload" id="custom_prize<?php echo ($key+1);?>_upload">
												</a>
											<?php }?>
										</div>
										<div>
											<input type="hidden" name="oldCustomPrize[]" value="<?php echo $prizeImageName;?>">
											<input type="hidden" value="" name="tempCustomPrize[]" class="prize_file_name" id="name_custom_prize<?php echo ($key+1);?>">
										</div>
									</td>
									<td align="left" valign="top"><textarea name="prizeDesc[]" id="custom_prizeDes<?php echo ($key+1);?>" class="custom_description" cols="30" rows="4" style="width:60%;"><?php if(isset($value->PrizeDescription) && $value->PrizeDescription != '') echo $value->PrizeDescription ;?></textarea></td>
								</tr>
							<?php } ?>
							</table>
						</td></tr>
					<?php }else if(isset($tournamenttype) && $tournamenttype == 4 ){ ?>
						<tr><th colspan="6" align="left"><h2 style="padding:0px;">Custom Prize</h2></th></tr>
						<tr><td height="20"></td></tr>
						<tr><td colspan="7">
							<table cellpadding="0" cellspacing="0" align="center" border="0" width="100%" id="custom_prizeTable">
								<tr height="25" clone="0">
									<th width="30%" valign="top" align="left" style="padding-left:5px;"><label>Prize Name/Title</label></th>
									<th width="10%" valign="top" align="left" style="padding-left:4px;"><label>Image</label></th>
									<th width="30%" valign="top" align="left" style="padding-left:4px;"><label>Description</label></th>
								</tr>
							<?php   $key = 0;
								$addMoreBtn	=	' style="display:none" ';
								if($customRestrict)
									$addMoreBtn	=	'';
							?>
								<tr clone="<?php echo ($key+1);?>" height="120">
									<td align="left" valign="top">
										<input type="text" class="input prize_title" style="width:50%;" name="prizeTitle[]" id="prize_title<?php echo ($key+1);?>" maxlength="50" value="">
										<input type="hidden" name="prizeId[]" value="">
									</td>
									<td align="left" valign="top" width="15%" class="upload_td">
										<div id="custom_prize<?php echo ($key+1);?>_img">
												<input type="hidden" value=""  id="custom_prize<?php echo ($key+1);?>_upload">
										</div>
										<div>
											<input type="file" name="custom_prize<?php echo ($key+1);?>" id="custom_prize<?php echo ($key+1);?>" onchange="return ajaxAdminFileUploadProcess('custom_prize<?php echo ($key+1);?>');">
											<input type="hidden" name="oldCustomPrize[]" value="">
											<input type="hidden" value="" name="tempCustomPrize[]" class="prize_file_name" id="name_custom_prize<?php echo ($key+1);?>">
										</div>
									</td>
									<td align="left" valign="top"><textarea name="prizeDesc[]" id="custom_prizeDes<?php echo ($key+1);?>" class="custom_description" cols="30" rows="4" style="width:60%;"></textarea></td>
								</tr>
							</table>
						</td></tr>
					<?php } ?>


					<?php
					if($createdBy == 3){ ?>
					<tr><td colspan="3" height="30" align="left"  valign="top"><h2 style="padding:0px;">Coupon</h2></td>	<td colspan="3">&nbsp;</td></tr>
					<tr>
						<td colspan="7">
						<table cellpadding="0" cellspacing="7" id="coupon_table" border="0" width="100%" align="center">
							<tr align="center"  class="clone" clone="0" >
								<td align="left" width="17%" valign="top"><label>Title</label></td>
								<td align="left" width="17%" valign="top"><label>Limit</label></td>
								<td align="left" width="20%" valign="top"><label>Description</label></td>
								<td class="upload_td" valign="top" align="left" width="22%"><label>Image</label></td>
								<td valign="top" align="left" width="13%"><label>Start Date</label></td>
								<td valign="top" align="left" width="15%"><label>End Date</label></td>
							</tr>
							<tr align="center" height="110" class="clone" clone="0" >
								<td align="left" width="17%" valign="top"><input class="input" type="text" value="<?php if($couponCount > 0 && isset($couponDetails)) echo $couponDetails->CouponTitle; ?>" style="width:86%;" name="coupon_title" class="coupon_title" id="coupon_title" maxlength="80"></td>
								<td align="left" width="17%" valign="top"><input class="input" type="text" value="<?php if($couponCount > 0 && isset($couponDetails)) echo $couponDetails->CouponLimit; ?>" style="width:86%;" maxlength="8"  name="coupon_limit" class="coupon_title" id="coupon_limit" onkeypress="return isNumberKey(event);" onpaste="return false;" ondrop="event.dataTransfer.dropEffect='none';event.stopPropagation(); event.preventDefault();"></td>
								<td align="left" width="20%" valign="top">
									<textarea  name="coupon_text" id="coupon_text" class="coupon_text w90" maxlength="250" rows="4" cols="30"><?php if($couponCount > 0 && isset($couponDetails)) echo $couponDetails->CouponAdLink; ?></textarea>
									<input type="hidden" value="<?php if($couponCount > 0 && isset($couponDetails)) echo $couponDetails->id; ?>" name="coupon_text_id" class="coupon_text_id">
								</td>
								<td class="upload_td" valign="top" align="left" width="22%">
									<div id="coupon_file_img" class="coupon_image">
									<?php if(!empty($coupon_image_path)) { ?>
										<a href="<?php echo $coupon_image_path;?>" class="image_pop_up"><img  src="<?php echo $coupon_image_path; ?>" width="75" height="75" class="img_border"></a>
									<?php } ?>
									</div>
									<span id="coupon_bloc">
										<input type="file" class="upload w90" id="coupon_file" name="coupon_file" onchange="return ajaxAdminFileUploadProcess('coupon_file');">
									</span>
									<input type="hidden" id="old_coupon_file" name="old_coupon_file" value="<?php echo $couponImage;?>">
									<input type="hidden" value="<?php if(isset($couponImage) && !empty($couponImage)) echo $couponImage;?>" name="empty_coupon_file" class="coupon_file_name" id="empty_coupon_file">
								</td>
								<td align="left" width="13%" valign="top"><input class="input" type="text" value="<?php if($couponCount > 0 && isset($couponDetails) && isset($couponDetails->CouponStartDate)	&&	$couponDetails->CouponStartDate !='0000-00-00 00:00:00') echo date('m/d/Y',strtotime($couponDetails->CouponStartDate)); ?>" readonly name="coupon_startdate" class="coupon_title" id="coupon_startdate"></td>
								<td align="left" width="15%" valign="top"><input class="input" type="text" value="<?php if($couponCount > 0 && isset($couponDetails) && isset($couponDetails->CouponEndDate)	&&	$couponDetails->CouponEndDate !='0000-00-00 00:00:00') echo date('m/d/Y',strtotime($couponDetails->CouponEndDate)); ?>" readonly name="coupon_enddate" class="coupon_title" id="coupon_enddate"></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr><td colspan="3" height="30" align="left"  valign="top"><h2 style="padding:0px;">Banner Ad</h2></td>	<td colspan="4">&nbsp;</td></tr>
					<tr>
						<td colspan="7">
							<table cellpadding="0" cellspacing="7" id="banner_table" border="0" width="100%" align="center">
								<tr align="center"  class="clone" clone="0" >
									<td align="left" width="9%"  valign="top"><label>Type</label></td>
									<td align="left" width="45%" valign="top">
									<label class="banner_link banner_image_td" style="float:left;width:21%;" >Image/Video</label>
									<label class="banner_link banner_text_td" style="float:left;width:29%;" >Text</label>
									<label class="banner_image_td">Text/Link</label>
									</td>
								</tr>
								<tr align="center" height="110" class="clone" clone="0" >
									<td align="left"  valign="top" width="9%">
										<select id="banner_type" class="banner_type w90"  name="banner_type" onchange="bannerHideShow()">
											<option value="">Select</option>
											<option value="1" <?php if($bannerCount > 0 && isset($bannerDetails) && $bannerDetails->InputType == 1) echo 'selected'; ?>>Text</option>
											<option value="2" <?php if($bannerCount > 0 && isset($bannerDetails) && $bannerDetails->InputType == 2) echo 'selected'; ?>>Image/Video</option>
										</select>
									</td>
									<td align="left"  valign="top" width="45%">
										<div style="display:none"  class="banner_text_td" id="banner_text_td">
											<textarea  name="banner_text" id="banner_text" class="banner_text w90" rows="4" cols="50" style="width:50%;"><?php if($bannerCount > 0 && isset($bannerDetails)) echo $bannerDetails->CouponAdLink; ?></textarea>
											<input type="hidden" value="<?php if($bannerCount > 0 && isset($bannerDetails)) echo $bannerDetails->id; ?>" name="banner_text_id" class="banner_text_id">
										</div>
										<div style="display:none" class="upload_td banner_image_td" id="banner_image_td"  align="left">
											<div class="upload_video">
											<div id="banner_file_img" class="banner_image">
											<?php echo $bannerImageBlock; ?>
											</div>
											<span id="banner_block1">
												<input type="file" class="upload" id="banner_file" name="banner_file" onchange="return ajaxImageVideoUploadProcess(this.value,'banner_file');">
												<span class="banner_note">Best in (320 X 66)</span>
											</span>
											</div>
											<input type="hidden" id="old_banner_file" name="old_banner_file" value="<?php if($bannerCount > 0 && isset($bannerDetails)) echo $bannerDetails->File; ?>">
											<input type="hidden" value="<?php if(!empty($banner_file_path)) echo '1';?>" name="empty_banner_file" class="banner_temp_file" id="empty_banner_file">
										</div>

										<div class="banner_image_td" style="float: left; width: 65%; display: block;">
										<input type="text" class="input w90" id="banner_link"  name="banner_link" maxlength="200"value="<?php if($bannerCount > 0 && isset($bannerDetails)) echo $bannerDetails->URL; ?>" >
										</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td colspan="3" height="30" align="left"  valign="top"><h2 style="padding:0px;">Youtube Link</h2></td>	<td colspan="3">&nbsp;</td></tr>
					<tr>
						<td colspan="7">
						<table cellpadding="0" cellspacing="7" id="youtube_table" border="0" width="100%" align="center">
							<tr align="center"  class="clone" clone="0" >
								<td align="left" width="9%"  valign="top"><label>Type</label></td>
								<td width="45%" align="left"><label style="float: left; width: 21%; display: block;" ><span class="youtube_image_img">Image</span></label>
								<label class="youtube_image_img" >URL/Embedded Code</label>
								</td>
							</tr>
							<tr align="center" height="110" class="clone" clone="0" >
								<td align="left"  valign="top" width="9%">
									<select id="youtube_type" class="youtube_type youtube_select w90" name="youtube_type" onchange="youtubeHideShow()">
										<option value="">Select</option>
										<option value="1" <?php if($linkCount > 0 && isset($linkDetails) && $linkDetails->InputType == 1) echo 'selected'; ?>>URL</option>
										<option value="2" <?php if($linkCount > 0 && isset($linkDetails) && $linkDetails->InputType == 2) echo 'selected'; ?>>Embedded Code</option>
									</select>
								</td>

								<td class="upload_td" valign="top" align="left" width="45%">
								<div style="float:left;">
								<div class="upload_video youtube_image_img">
									<div id="link_file_img" class="link_image">
									<?php if(!empty($link_image_path)) { ?>
										<a href="<?php echo $link_image_path;?>" class="image_pop_up"><img  src="<?php echo $link_image_path; ?>" width="75" height="75" class="img_border"></a>
									<?php } ?>
									</div>
									<span id="coupon_bloc">
										<input type="file" class="upload  w90" id="link_file" name="link_file" onchange="return ajaxAdminFileUploadProcess('link_file');">
									</span>
								</div>
									<input type="hidden" class="old_link_file" id="old_link_file" name="old_link_file" value="<?php if($linkCount > 0 && isset($linkDetails)) echo $linkDetails->File; ?>">
									<input type="hidden" value="<?php if(!empty($link_image_path)) echo '1';?>" name="empty_link_file" class="link_temp_file" id="empty_link_file">
									<input type="hidden" value="<?php if($linkCount > 0 && isset($linkDetails)) echo $linkDetails->id; ?>" name="youtube_text_id" class="youtube_text_id w90">
								</div>
									<div class="youtube_text_td"  style="display:none;float: left; width: 65%;" id="youtube_text_td">
										<input type="text" class="input" value="<?php if($linkCount > 0 && isset($linkDetails)) echo $linkDetails->URL; ?>" name="youtube_text" maxlength="200" id="youtube_text" class="youtube_text w90" pattern="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?">
									</div>
									<div style="display:none;float: left; width: 65%;" id="youtube_code_td" class="youtube_code_td" valign="top">
										<textarea  name="youtube_code" id="youtube_code" class="youtube_code  w90"  rows="4" cols="45"><?php if($linkCount > 0 && isset($linkDetails)) echo $linkDetails->CouponAdLink; ?></textarea>
									</div>
								</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
					<td colspan="7">
						<table cellpadding="10" cellspacing="7" id="inputParam" border="0" align="center" width="100%">
							<input type="hidden" style="disabled='true'" value="<?php echo $gen_termsCond; ?>" name='terms_cond_template' id='terms_cond_template'>
							<input type="hidden" style="disabled='true'" value="<?php echo $gen_gamerules; ?>" name='game_rules_template' id='game_rules_template'>
							<input type="hidden" style="disabled='true'" value="<?php echo $gen_tournmentrules; ?>" name='tournament_rules_template' id='tournament_rules_template'>
							<input type="hidden" style="disabled='true'" value="<?php echo $gen_privacyPolicy; ?>" name="privacy_rules_template" id='privacy_rules_template'>
							<tr align="center" height="20">
								<td class="add_remove">&nbsp;<div class="fleft">Global&nbsp;&nbsp;</div></td>
								<td colspan="4" valign="top">
								<div class="">
									<ul class="nav nav-tabs tab_style">
										<li id="termsandcond_link" onclick="tournament_blockdisp('terms_cond',this);" class="active">Terms and Conditions</li>
										<li id ="gamerules_link" onclick="tournament_blockdisp('game_rules',this);">Game Rules</li>
										<li id ="tourrules_link" onclick="tournament_blockdisp('tour_rules',this);">Tournament Rules</li>
										<li id ="privacypolicy_link" onclick="tournament_blockdisp('tour_privacy',this);">Privacy Policy</li>
									<ul>
								</div>
								</td>
							</tr>
							<tr>
								<td class="add_remove" valign="top">&nbsp;</td>
								<td class="terms_cond" id="terms_td" valign="top"><textarea class="textarea-full textEditor" id="terms_condition" rows="3" cols="32" tabindex="11" name="gen_terms_condition"><?php echo $tour_termsCond; ?></textarea></td>
								<td class="game_rules"  style ="display:none;" id="rules_td" valign="top"><textarea class="textarea-full textEditor" id="game_rules" rows="2" cols="32" tabindex="11" name="gen_game_rules"><?php echo $tour_gamerules; ?></textarea></td>
								<td class="tour_rules"  style ="display:none;" id="tournamentrules_td" valign="top"><textarea class="textarea-full textEditor" id="tournment_rules" rows="2" cols="32" tabindex="11" name="gen_tournmentrules"><?php echo $tour_rules; ?></textarea></td>
								<td class="tour_privacy"  style ="display:none;" id="privacypolicy_td" valign="top"><textarea class="textarea-full textEditor" id="privacy_policy_tab" rows="2" cols="32" tabindex="11" name="gen_privacy_policy_tab"><?php echo $tour_privacyPolicy; ?></textarea></td>
							</tr>

			<?php		if(isset($tourRulesResult) && is_array($tourRulesResult) && count($tourRulesResult)>0){
							$rulesCount = count($tourRulesResult);
							foreach($tourRulesResult as $ruleKey => $rulesDetails){
								$addRule = 'style="display:none"';
								$stateDisplay = 'display:none';
								if($rulesCount == ($ruleKey+1)) $addRule = "";
								if($rulesDetails->fkCountriesId == $usId)  $stateDisplay = "";
								?>
							<tr clone="<?php echo $ruleKey;?>">
								<td class="add_remove" >
									<div class="fleft">
										<a href="javascript:void(0)" onclick="delCountryRule(this)"><i class="fa fa-lg  fa-minus-circle"></i></a>
										<span id="new_<?php echo $ruleKey; ?>"  class="addNewRule" <?php echo $addRule;?>><a href="javascript:void(0)" onclick="addCountryRule(this)"><i class="fa fa-lg fa-plus-circle"></i></a></span>&nbsp;&nbsp;
									</div>
									<div class="fleft">
										<select name="country[]" tabindex="10" style="width:150px;" class="country" id="country_<?php echo $ruleKey; ?>" onchange="countryShow(<?php echo $ruleKey;?>);">
											<option value="">Select</option>
											<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
												foreach($countryArray as $countryId => $country) {  ?>
												<option value="<?php echo $countryId; ?>" <?php   if($countryId == $rulesDetails->fkCountriesId) echo 'Selected';  ?>><?php echo $country; ?></option>
											<?php 	} ?>
										</select>
										<br>
										<span id='field_name_empty' class="error_empty"></span>
										<span class="slabel" id="state_label_<?php echo $ruleKey; ?>" style="<?php echo $stateDisplay;?>;padding-top:6px;" >State</span>
										<br>
										<select name="state[]" tabindex="10" class="state" style="width:150px;<?php echo $stateDisplay;?>" id="state_<?php echo $ruleKey; ?>">
											<option value="">Select</option>
											<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
												foreach($usStateArray as $stateId => $state) {  ?>
												<option value="<?php echo $stateId; ?>" <?php   if($stateId == $rulesDetails->fkStatesId) echo 'Selected';  ?>><?php echo $state; ?></option>
											<?php 	} ?>
										</select>
										<span id='sample_data_empty' class="error_empty"></span>
									</div>
								</td>
								<td class="terms_cond terms_td" id="terms_td" valign="top"><textarea class="textarea-full textEditor" id="terms_condition_<?php echo $ruleKey; ?>" rows="3" cols="32" tabindex="11" name="tournamentConditions[]"><?php echo $rulesDetails->TermsAndConditions; ?></textarea></td>
								<td class="game_rules rules_td"  style ="display:none;" id="rules_td" valign="top"><textarea class="textarea-full textEditor " id="game_rules_<?php echo $ruleKey; ?>" rows="2" cols="32" tabindex="11" name="tournamentRules[]"><?php echo $rulesDetails->TournamentRules; ?></textarea></td>
								<td class="tour_rules tournamentrules_td"  style ="display:none;" id="tournamentrules_td" valign="top"><textarea class="textarea-full textEditor " id="tournment_rules_<?php echo $ruleKey; ?>" rows="2" cols="32" tabindex="11" name="thirdtournamentRules[]"><?php echo $rulesDetails->GftRules; ?></textarea></td>
								<td class="tour_privacy privacypolicy_td"  style ="display:none;" id="privacypolicy_td" valign="top"><textarea class="textarea-full textEditor " id="privacy_policy_<?php echo $ruleKey; ?>" rows="2" cols="32" tabindex="11" name="privacy_policy_arr[]"><?php echo $rulesDetails->PrivacyPolicy; ?></textarea></td>
							</tr>
			<?php 			}
						}else {?>
							<tr clone="0">
								<td class="add_remove" >
									<div class="fleft">
										<a href="javascript:void(0)" onclick="delCountryRule(this)"><i class="fa fa-lg  fa-minus-circle"></i></a>
										<span id="new_0"  class="addNewRule" ><a href="javascript:void(0)" onclick="addCountryRule(this)"><i class="fa fa-lg fa-plus-circle"></i></a></span>&nbsp;&nbsp;
									</div>
									<div class="fleft">
										<select name="country[]" tabindex="10" style="width:150px;" class="country" id="country_0" onchange="countryShow(0);">
											<option value="">Select</option>
											<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
												foreach($countryArray as $countryId => $country) {  ?>
												<option value="<?php echo $countryId; ?>" ><?php echo $country; ?></option>
											<?php 	} ?>
										</select>
										<br>
										<span id='field_name_empty' class="error_empty"></span>
										<span class="slabel" id="state_label_0" style="line-height:25px;display:none;padding-top:6px;">State</span>
										<br>
										<select name="state[]" tabindex="10" class="state" style="display:none;width:150px;" id="state_0">
											<option value="">Select</option>
											<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
												foreach($usStateArray as $stateId => $state) {  ?>
												<option value="<?php echo $stateId; ?>" ><?php echo $state; ?></option>
											<?php 	} ?>
										</select>
										<span id='sample_data_empty' class="error_empty"></span>
									</div>
								</td>
								<td class="terms_cond terms_td" id="terms_td" valign="top"><textarea class="textarea-full textEditor" id="terms_condition_0" rows="3" cols="32" tabindex="11" name="tournamentConditions[]"><?php echo $gen_termsCond; ?></textarea></td>
								<td class="game_rules rules_td"  style ="display:none;" id="rules_td" valign="top"><textarea class="textarea-full textEditor " id="game_rules_0" rows="2" cols="32" tabindex="11" name="tournamentRules[]"><?php echo $gen_gamerules; ?></textarea></td>
								<td class="tour_rules tournamentrules_td"  style ="display:none;" id="tournamentrules_td" valign="top"><textarea class="textarea-full textEditor " id="tournment_rules_0" rows="2" cols="32" tabindex="11" name="thirdtournamentRules[]"><?php echo $gen_tournmentrules; ?></textarea></td>
								<td class="tour_privacy privacypolicy_td"  style ="display:none;" id="privacypolicy_td" valign="top"><textarea class="textarea-full textEditor " id="privacy_policy_0" rows="2" cols="32" tabindex="11" name="privacy_policy_arr[]"><?php echo $gen_privacyPolicy; ?></textarea></td>
							</tr>
			<?php		} ?>
						</table>
					</td>
					</tr>
			<?php	}?>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td colspan="6" align="center">
				<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
					<input type="submit" class="submit_button" name="submit" id="submit" value="Save" title="Save" alt="Save">
				<?php } else { ?>
				<input type="submit" class="submit_button" name="submit" id="submit" value="Add" title="Add" alt="Add">
				<?php } ?>
				<a href="<?php if(isset($href_page) && $href_page != '' ) echo $href_page; else echo 'TournamentList';?>"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
			</td>
		</tr>
			<tr><td height="35"></td></tr>
		</table>
	</form>
</div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".image_pop_up").colorbox({
	title:true,
	maxWidth:"65%",
	maxHeight:"50%"
});
$(".banner_video").colorbox({
	iframe:true,
	width:"45%",
	height:"65%",
	title:true
});
$(function(){

	$('#start_time').datetimepicker({
  		format:'m/d/Y H:i',
  		minDate:0,
		onChangeDateTime: function() {
			var starting_time	=	$('#start_time').val();
			 $('#coupon_startdate').datepicker("option", 'minDate', new Date(starting_time));
		},
  		onShow:function( ct ){
   			this.setOptions({
   			})
   		},
 	});
	var logic = function( currentDateTime ){
	var coupon_endTime	=	$('#end_time').val();
	$('#coupon_startdate').datepicker("option", 'maxDate', new Date(coupon_endTime));
	$('#coupon_enddate').datepicker("option", 'maxDate', new Date(coupon_endTime));
		var starting_time	=	$('#start_time').val();
		var ending_time		=	$('#end_time').val();
		start_dArr = starting_time.split(" ");
		start_DateArr = start_dArr[0];
		start_TimeArr = start_dArr[1];
		end_dArr = ending_time.split(" ");
		end_DateArr = end_dArr[0];
		end_TimeArr = end_dArr[1];
		if(start_DateArr == end_DateArr){
			 tme_new		=	start_TimeArr;
		}
		else{
			 tme_new		=	false;
		}
		if(start_DateArr != '' && start_TimeArr != ''){
			this.setOptions({
				minDate:start_DateArr,
				minTime:tme_new
			});
		}else{
			this.setOptions({
				Date:start_DateArr
			});
		}
	};
	$('#end_time').datetimepicker({
		onChangeDateTime:logic,
		onShow:logic
	});

});

$(document).ready(function() {
	checkpinrestriction();
initializeEditor();
 <?php if(isset($_GET['editId'])){ ?>
bannerHideShow();
youtubeHideShow();
var starting_time	=	$('#start_time').val();
if(starting_time && starting_time !=''){
	$('#coupon_startdate').datepicker("option", 'minDate', new Date(starting_time));
	$('#coupon_enddate').datepicker("option", 'minDate', new Date(starting_time));
}
var coupon_endTime	=	$('#end_time').val();
if(coupon_endTime && coupon_endTime !=''){
	$('#coupon_startdate').datepicker("option", 'maxDate', new Date(coupon_endTime));
	$('#coupon_enddate').datepicker("option", 'maxDate', new Date(coupon_endTime));
}
<?php } ?>
});
function initializeEditor(){
 tinymce.init({
	height 	: "200",
	width	: "350",
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
$("#coupon_startdate").datepicker({
			showButtonPanel	:	true,
   			 buttonText		:	'',
    		buttonImageOnly	:	true,
			onSelect		: function (dateText, inst) {
						$('#coupon_enddate').datepicker("option", 'minDate', new Date(dateText));
						},
    		onClose			: function () { $(this).focus(); },
   			buttonImage		:	path+'webresources/images/calender.png',
		    dateFormat		:	'mm/dd/yy',
			changeMonth		:	true,
			changeYear		:	true,
			hideIfNoPrevNext:	true,
			showWeek		:	true,
			yearRange		:	"c-30:c",
			closeText		:   "Close"
	   });

		$("#coupon_enddate").datepicker({
			showButtonPanel	:	true,
   			 buttonText		:	'',
    		buttonImageOnly	:	true,
			onSelect		: function () { },
    		onClose			: function () { $(this).focus(); },
   			buttonImage		:	path+'webresources/images/calender.png',
		    dateFormat		:	'mm/dd/yy',
			changeMonth		:	true,
			changeYear		:	true,
			hideIfNoPrevNext:	true,
			showWeek		:	true,
			yearRange		:	"c-30:c",
			closeText		:   "Close"
	   });
function checklocationrestriction(){
	if($("#location").is(":checked"))
		$("#restrictTable").show();
	else
		$("#restrictTable").hide();
}
function checkpinrestriction(){
	if($('#pinNumber').val()==''){
		$('#pinNumber').val(Math.floor(Math.random() * 1000000) + 100000  );
	}
	if($("#pin").is(":checked"))
		$(".pinNumber").show();
	else
		$(".pinNumber").hide();
}
</script>
</html>
