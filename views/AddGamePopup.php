<?php 
require_once('includes/CommonIncludes.php');
require_once("includes/phmagick.php");
developer_login_check();
commonHead();
require_once('controllers/GameController.php');
$gameObj   =   new GameController();

$field_focus	=	'gamename';
$class			=	$ExistCondition		=	$photoUpdateString		= $GameStatus 	= $submitFlag	= $temp_app_image = '';
$gamename 		= 	$itunesurl = 	$tiltkey = $androidurl 	= $gamedescription 	= $package = $bundle = '';
$exists			=	$show = 0;
$insertios_pnid =   $insertandroid_pnid =  $gcmkey = $ios_password = ''; 
$eliminationArr = 	$highscoreruleArr	= 	$gameRulesResult 	= 	$image_array  			= $gamefilesResult 	= $image_patharray =  array();
$certStatus		=	'Not Uploaded';
$certClass 		=	'cert_inactive';
$gameId 		= 	$certificateName 	= 	$password = '';
$today			=	date('Y-m-d H:i:s');
$iosswitch = 1;
$androidswitch = 0;

// Add/edit Game Detail
if(isset($_POST['saveDetail'])	&&	$_POST['saveDetail'] != "")	{
	if(isset($_POST['gamename'])	&&	$_POST['gamename']!='')
		$gamename 			=	trim(($_POST['gamename']));
	if(isset($_POST['iosswitch'])	&&	$_POST['iosswitch']!='')
		$iosswitch 		=	1;
	else
		$iosswitch 		=	0;
	$temp_app_image = isset($_POST['game_photo_upload']) && !empty($_POST['game_photo_upload']) ? $_POST['game_photo_upload'] : '';
	
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
			
	$field	= 	" * ";
	$Cond 	= " Name = '".$_POST['gamename']."' and Status in (1,2,4) ";
	if($_POST['saveDetail'] == 'Save'){
		$Cond .= "and id != '".$_POST['game_id']."' ";
		$edit_id = $_POST['game_id'];
	}
	
	// To check the already exist condition for the user email address and there fb id
	$alreadyExist    =	$gameObj->selectGameDetails($field,$Cond);
	if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0)	{
		if(($alreadyExist[0]->Name != '') && ($_POST['gamename'] != '')){
			if(strcasecmp($alreadyExist[0]->Name,trim(stripslashes($_POST['gamename'])))  == 0){
				$exists 	=	1;
				$error_msg   = "Game already exists";
				$field_focus = 'gamename';
				$display = "block";
				$class   = "error_msg";
			}
		}
	}else if(!isset($_POST['iosswitch'])){
		$exists 	=	2;
		$error_msg   = "iOS must be in On status";
		$field_focus = 'iosswitch';
		$display = "block";
		$class   = "error_msg";
	}

	if($exists	==	0){
		$insertios_pnid = isset($_POST['insertios_pnid']) && $_POST['insertios_pnid'] != '' ? $_POST['insertios_pnid'] : '';
		$insertandroid_pnid = isset($_POST['insertandroid_pnid']) && $_POST['insertandroid_pnid'] != '' ? $_POST['insertandroid_pnid'] : '';
		if($_POST['saveDetail'] == 'Save'){
			$insert_id = $_POST['game_id'];
			$condition 			= "id = ".$insert_id;
			$fields = " Name            	=	'".trim($_POST['gamename'])."',
						IosStatus       	=	'".$iosswitch."',
						AndroidStatus      	=	'".$androidswitch."',
						DateModified		=	'".date('Y-m-d H:i:s')."'";
			$gameObj->updateGameDetails($fields,$condition);
			if($iosswitch == 0 && $insertios_pnid != ''){
				$field = 	"Status 				 =	2,
							DateModified			 =	'".date('Y-m-d H:i:s')."'";
				$cond  = "id = ".$insertios_pnid;
				$gameObj->updateGamePushNotification($field,$cond);
				
				$fields = " ITunesUrl       =	'',
							Bundle       	=	'',
							DateModified	=	'".date('Y-m-d H:i:s')."'";
				$gameObj->updateGameDetails($fields,$condition);
			}
			$error_msg 	= "Game updated successfully";
			$class 		= "success_msg form-group";
		}else{
			$_POST['developerId']=	$_SESSION['tilt_developer_id'];
			$insert_id   		=	$gameObj->insertGameDetails($_POST);
			$wordResult 	    =	$gameObj->selectWordDetails();	
			$word               =	$wordResult[0]->words;
			$numeric            =	'1234567890';
			$numbers            =	substr(str_shuffle($numeric), 0, 3);
			$actualPassword     =	trim($word.$numbers).$insert_id;
			$password			=	sha1($actualPassword.ENCRYPTSALT);
			$updateString 		=	" Password = '" . $password . "' ";
			$condition 			=	"id = ".$insert_id;
			
			// To upload the user table with the generated password
			$gameObj->updateGameDetails($updateString,$condition);
			//To set default game rules
			if(isset($_POST["elimination_rule"])	&&	count($_POST["elimination_rule"]) > 0){
				$insertRules = "";
				for($index = 0;$index < count($_POST["elimination_rule"]);$index++) {
					$elimination	= $highscorerule = '';
					if(isset($_POST["elimination_rule"][$index]))
						$elimination	=	$_POST["elimination_rule"][$index];
					if(isset($_POST["highscore_rule"][$index]))
						$highscorerule	=	$_POST["highscore_rule"][$index];
					if($elimination	!= '' || $highscorerule != '')
						$insertRules	.=	"('".$insert_id."','".$elimination."','".$highscorerule."','1','".date('Y-m-d H:i:s')."'),";
				}
				if($insertRules != ''){
					$insertRules =	trim($insertRules,",");
					$gameObj->insertGameRules($insertRules);
				}
			}
			$submitFlag	= 1; 
			
			$error_msg 	= "Game added successfully";
			$class 		= "success_msg form-group";
		}
		$edit_id = $insert_id;
		//Start upload and save App icon 
		$photoUpdateString = '';
		if (isset($_POST['game_photo_upload']) && !empty($_POST['game_photo_upload'])) {
			$imageName 				= $insert_id . '_' .time() . '.png';
			$temp_image_path 		= TEMP_IMAGE_PATH_REL . $_POST['game_photo_upload'];
			$image_path 			= UPLOAD_GAMES_PATH_REL . $imageName;
			$imageThumbPath     	= UPLOAD_GAMES_THUMB_PATH_REL.$imageName;
			$oldUserName			= $_POST['name_game_photo'];
			if ( !file_exists(UPLOAD_GAMES_PATH_REL) ){
				mkdir (UPLOAD_GAMES_PATH_REL, 0777);
			}
			if ( !file_exists(UPLOAD_GAMES_THUMB_PATH_REL) ){
				mkdir (UPLOAD_GAMES_THUMB_PATH_REL, 0777);
			}
			copy($temp_image_path,$image_path);
			
			$phMagick = new phMagick($image_path);
			$phMagick->setDestination($imageThumbPath)->resize(100,100);
			
			if ($_SERVER['HTTP_HOST'] != '172.21.4.104'){
				if($oldUserName!='') {
					if(image_exists(6,$oldUserName)) {
						deleteImages(6,$oldUserName);
					}
					if(image_exists(7,$oldUserName)) {
						deleteImages(7,$oldUserName);
					}
				}
				
				uploadImageToS3($imageThumbPath,7,$imageName);
				uploadImageToS3($image_path,6,$imageName);
				unlink($image_path);
				unlink($imageThumbPath);
			}
			else if($oldUserName !=''){
				if ( file_exists(UPLOAD_GAMES_PATH_REL.$oldUserName) ){
					unlink(UPLOAD_GAMES_PATH_REL.$oldUserName);
				}
				if ( file_exists(UPLOAD_GAMES_THUMB_PATH_REL.$oldUserName) ){
					unlink(UPLOAD_GAMES_THUMB_PATH_REL.$oldUserName);
				}
			}
			$photoUpdateString	.= " Photo = '" . $imageName . "'";
			unlink(TEMP_IMAGE_PATH_REL . $_POST['game_photo_upload']);
		}
		if($photoUpdateString!=''){
			$condition 			= "id = ".$insert_id;
			$gameObj->updateGameDetails($photoUpdateString,$condition);
		}
		//End upload and save App icon 
	}
}

if(isset($_POST['appSave'])	&&	$_POST['appSave'] != "" && isset($_POST['game_id']) && $_POST['game_id'] > 0){
	$insert_id = $_POST['game_id'];
	$insertios_pnid = isset($_POST['insertios_pnid']) && $_POST['insertios_pnid'] != '' ? $_POST['insertios_pnid'] : '';
	$insertandroid_pnid = isset($_POST['insertandroid_pnid']) && $_POST['insertandroid_pnid'] != '' ? $_POST['insertandroid_pnid'] : '';
		
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	
	$condition 			= "id = ".$insert_id;
	$fields = " Bundle         =	'".trim($_POST['bundle'])."',
				ITunesUrl			=	'".trim($_POST['itunesurl'])."',
				Package				=	'".trim($_POST['package'])."',
				AndroidUrl			=	'".trim($_POST['androidurl'])."',
				DateModified		=	'".date('Y-m-d H:i:s')."'";
	$gameObj->updateGameDetails($fields,$condition);
	
	//pushnotification
	if($insertios_pnid != ''){
		$pn_fields = '';
		if(isset($_POST['game_key']) && $_POST['game_key'] != '')
			 $pn_fields = " fkGamesKey =	'".$_POST['game_key']."',";
		if(isset($_POST['ios_password'])	&&	$_POST['ios_password']!='')
			$pn_fields .= "CertificatePassword =  '".md5(trim($_POST['ios_password']))."',";
		$pn_fields .= 	"Status 				 =	1,
					DateModified			 =	'".date('Y-m-d H:i:s')."'";
		$pn_condition 			= "id = ".$insertios_pnid;
		$gameObj->updateGamePushNotification($pn_fields,$pn_condition);
	}else{
		if((isset($_FILES['game_certificate']['name']) && $_FILES['game_certificate']['name'] != '')){
			$pn_array['platform'] = 1;
			$pn_array['game_id'] = $insert_id;
			$pn_array['tiltkey'] = $_POST['game_key'];	
			if(isset($_POST['ios_password'])	&&	$_POST['ios_password']!= '')
				$pn_array['password'] = md5(trim($_POST['ios_password']));
			$insertios_pnid  =	$gameObj->insertGamePushNotification($pn_array);
		}
	}
	//end pushnotification
	
	//Start game certificate
	if(isset($_POST['flag_game_certificate']) && $_POST['flag_game_certificate'] == 1 && isset($_FILES['game_certificate']) && count($_FILES['game_certificate']) > 0 ){
		if($_FILES['game_certificate']['name'] != '')
		{
			$certificateName	=	$insert_id.'/'.str_replace(' ', '_', $_FILES['game_certificate']['name']);
			$certificatePath        = UPLOAD_GAME_CERTIFICATE_PATH_REL.$certificateName;
			if ( !file_exists(UPLOAD_GAME_CERTIFICATE_PATH_REL.$insert_id) ) {
				mkdir (UPLOAD_GAME_CERTIFICATE_PATH_REL.$insert_id, 0777);
			 }
			copy($_FILES['game_certificate']['tmp_name'],$certificatePath);
			if ($_SERVER['HTTP_HOST'] != '172.21.4.104'){
				 if($_POST['old_game_certificate'] != '') {
					 if(image_exists(19,$_POST['old_game_certificate'])) {
						deleteImages(19,$_POST['old_game_certificate']);
					 }
				}
				uploadImageToS3($certificatePath,19,$certificateName);
				unlink($certificatePath);
			 }
			 else if($_POST['old_game_certificate'] != '') {
				if(file_exists(UPLOAD_GAME_CERTIFICATE_PATH_REL.$insert_id.'/'.$_POST['old_game_certificate']))
					unlink(UPLOAD_GAME_CERTIFICATE_PATH_REL.$insert_id.'/'.$_POST['old_game_certificate']);
			 }
			 
			 $photoUpdateString	=	" Certificate = '".str_replace(' ', '_', $_FILES['game_certificate']['name'])."'";
			 unlink($_FILES['game_certificate']['tmp_name']);
		}
	}
	if($photoUpdateString!='' &&  $insertios_pnid != '')
	{
		$condition 			= "id = ".$insertios_pnid;
		$gameObj->updateGamePushNotification($photoUpdateString,$condition);
	}
	//End game certificate
	
	$sucess_msg_2 	= "Game updated successfully";
	$show 			= 1;
	$edit_id 		= $insert_id;
}

if(isset($_POST['labSave'])	&&	$_POST['labSave'] != "" && isset($_POST['game_id']) && $_POST['game_id'] > 0){
	
	$insert_id = $_POST['game_id'];
	
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	
	$condition 			= "id = ".$insert_id;
	$fields = " Description         =	'".$_POST['gamedescription']."',
				PlayTime			=	'".$_POST['play_time']."',
				DateModified		=	'".date('Y-m-d H:i:s')."'";
	$gameObj->updateGameDetails($fields,$condition);
	//insert GameRule
	$insertRules = '';
	$gameObj->updateGameRules('Status = 2',$insert_id);  
	if(isset($_POST["elimination_rule"])	&&	count($_POST["elimination_rule"]) > 0){
		for($index = 0;$index < count($_POST["elimination_rule"]);$index++) {
			$elimination	= $highscorerule = '';
			if(isset($_POST["elimination_rule"][$index]))
				$elimination	=	$_POST["elimination_rule"][$index];
			if(isset($_POST["highscore_rule"][$index]))
				$highscorerule	=	$_POST["highscore_rule"][$index];
			if($elimination	!= '' || $highscorerule != '')
				$insertRules	.=	"('".$insert_id."','".$elimination."','".$highscorerule."','1','".date('Y-m-d H:i:s')."'),";
		}
		if($insertRules != ''){
			$insertRules =	trim($insertRules,",");
			$gameObj->insertGameRules($insertRules);
		}
	}
	
	//insert multiple file upload
	if(isset($_POST['game_temp_file'])	&& is_array($_POST['game_temp_file'])){
		$tempFileArray	=	$_POST['game_temp_file'];
		$imageidArray   =   $_POST['image_id'];
	}
	
	if(isset($_POST['oldgame_temp_file'])	&& is_array($_POST['oldgame_temp_file'])){
		$oldtempFileArray	=	$_POST['oldgame_temp_file'];
	}

	//update status for deleted old images
	if(isset($imageid_array) &&  $imageid_array != ''){
		foreach($imageid_array as $imgkey=>$imgvalue){
			if(!(in_array($imgvalue,$imageidArray)))
			{    
			   $updatestatus  = " Status = 2 "; 
			   $updatecondition = " id = '".$imgvalue."' ";
			   $gameObj->updateGameFiles($updatestatus,$updatecondition);
			   if(isset($image_array[$imgkey]) && $image_array[$imgkey] != ''){
				 $oldImage = $image_array[$imgkey];
				  if (SERVER){
						if($oldImage!='') {
							if(image_exists(6,$insert_id.'/'.$oldImage)) 
								deleteImages(6,$insert_id.'/'.$oldImage);
						}
					}
					else if ( $oldImage !='' && file_exists(UPLOAD_GAMES_PATH_REL.$insert_id.'/'.$oldImage) ){
						unlink(UPLOAD_GAMES_PATH_REL.$insert_id.'/'.$oldImage);
					}
				}
			}
		}
	}
	
	if(isset($tempFileArray)	&& is_array($tempFileArray) &&  isset($oldtempFileArray) &&  is_array($oldtempFileArray) ){
	   foreach($tempFileArray as $key=>$value){
			if(isset($tempFileArray[$key]) && 	$tempFileArray[$key] != ''){
				//update status for deleted old images
				if(isset($oldtempFileArray[$key]) && ($oldtempFileArray[$key]!= '')){
					if(isset($imageidArray[$key]) && $imageidArray[$key] != ''){
						$oldImage	=	$oldtempFileArray[$key];
						$updatestatus  = " Status = 2 "; 
						$updatecondition = " id = '".$imageidArray[$key]."' ";
						$gameObj->updateGameFiles($updatestatus,$updatecondition);
						if (SERVER){
							if($oldImage!='') {
								if(image_exists(6,$insert_id.'/'.$oldImage)) {
									deleteImages(6,$insert_id.'/'.$oldImage);
								}
							}
						}
						else if ( $oldImage !='' && file_exists(UPLOAD_GAMES_PATH_REL.$insert_id.'/'.$oldImage) ){
							unlink(UPLOAD_GAMES_PATH_REL.$insert_id.'/'.$oldImage);
						}
					}
				 }
				
				//insert images
				$queryString	=	"(".$insert_id.",'','1','".$today."')";
				$queryString	=	$queryString;
				$entryId	    =   $gameObj->insertGameFiles($queryString);
				$temp_image_path  = TEMP_IMAGE_PATH_REL.$value;
				$fileName = '';
				if(isset($value) && $value !=''){	
					$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
					$uploadPath	= UPLOAD_GAMES_PATH_REL;
					if ( !file_exists($uploadPath.$insert_id) ){
						mkdir ($uploadPath.$insert_id, 0777);
					}
					$fileName	=	'game_'.$entryId.'_'.time().'.'.$ext;
					$imageName 	=	 $insert_id.'/'.$fileName;
					$temp_image_path 	=	TEMP_IMAGE_PATH_REL.$value;
					$image_path 		=	$uploadPath.$imageName;
					copy($temp_image_path,$image_path);
					if (SERVER){
						uploadImageToS3($image_path,6,$imageName); // image_path
						unlink($image_path);
					}
				}
				
				if($fileName !=''){
					$updateString	= " Image= '".$fileName."' ";	
					$updatecondition = " id = ".$entryId;
					$gameObj->updateGameFiles($updateString,$updatecondition);
				}
			}
		}
	}
	//end multiple file upload
	$sucess_msg_3 	= "Game updated successfully";
	$show 			= 2;
	$edit_id 		= $insert_id;
}

//Get game detail
if(isset($edit_id) && $edit_id != '' ){
	$condition       = " id = ".$edit_id;
	$field			 = " * ";
	$gameId	=	$edit_id;
	$gameDetailsResult  = $gameObj->selectGameDetails($field,$condition);
	if(isset($gameDetailsResult) && is_array($gameDetailsResult) && count($gameDetailsResult) > 0){
		$gameId	 			=	$gameDetailsResult[0]->id;
		$GameStatus 		=   $gameDetailsResult[0]->Status;
		$gamename 			=	$gameDetailsResult[0]->Name;
		$tiltkey   			=	$gameDetailsResult[0]->TiltKey;
		$itunesurl 			=	$gameDetailsResult[0]->ITunesUrl;
		$androidurl 		=	$gameDetailsResult[0]->AndroidUrl;
		$gamedescription 	= 	$gameDetailsResult[0]->Description;
		$bundle     		= 	$gameDetailsResult[0]->Bundle;
		$package    		= 	$gameDetailsResult[0]->Package;
		$playTime   		= 	$gameDetailsResult[0]->PlayTime;
		$iosswitch 			=	$gameDetailsResult[0]->IosStatus;
		if(isset($gameDetailsResult[0]->Photo) && $gameDetailsResult[0]->Photo != ''){
			$user_image_name = $gameDetailsResult[0]->Photo;
			if(image_exists(6,$user_image_name))
				$original_image_path = GAMES_IMAGE_PATH.$user_image_name;
			else
				$original_image_path = '';	
			if(image_exists(7,$user_image_name)){
				$user_image_path = GAMES_THUMB_IMAGE_PATH.$user_image_name;
			}else{
				$user_image_path = '';
			}
		}
	}
	
	//select GameRule
	$rulecondition       = " fkGamesId = ".$edit_id." and Status = 1 ";
	$gameRulesResult  = $gameObj->selectGameRules($rulecondition);
	if(is_array($gameRulesResult) && isset($gameRulesResult) && count($gameRulesResult)>0){
		foreach($gameRulesResult as $val)
		{
			$eliminationArr[]	=	$val->EliminationRules;
			$highscoreruleArr[]	=	$val->HighscoreRules;
		}
	}
	
	//select pushnotification
	$rulecondition       = " fkGamesId = ".$edit_id." and Status = 1";
	$gamepushnotificaion  = $gameObj->selectGamePushNotification(" * " ,$rulecondition);
	if(is_array($gamepushnotificaion) && isset($gamepushnotificaion) && count($gamepushnotificaion)>0){	
		foreach($gamepushnotificaion as $val){
			if($val->Platform == 1){
				$insertios_pnid  = $val->id;
				$platform_ios    = $val->Platform;
				$certificateName = $val->Certificate;
				if($certificateName !=''){
					$certStatus	= 'Uploaded';
					$certClass 	= 'cert_active';
				}
				if(isset($val->Certificatepassword) && $val->Certificatepassword != '')
					$ios_password	=	1;
			}
		}
	}
	//select GameImages
	$filecondition       = " fkGamesId = ".$edit_id." and Status = 1 and Image != ''";
	$gamefilesResult  = $gameObj->selectGameFiles($filecondition);
	if(is_array($gamefilesResult) && isset($gamefilesResult) && count($gamefilesResult)>0){
		foreach($gamefilesResult as $imageval)
		{
			 if(SERVER){
				if(image_exists(6,$edit_id.'/'.$imageval->Image)) {
					$image_array[]	=	$imageval->Image;
					$image_patharray[]	=	GAMES_IMAGE_PATH.$edit_id.'/'.$imageval->Image;
					$imageid_array[]	=	$imageval->id;
				}
			}else{
				if(file_exists(GAMES_IMAGE_PATH_REL.$edit_id.'/'.$imageval->Image)){	
					$image_array[]	=	$imageval->Image;
					$image_patharray[]	=	GAMES_IMAGE_PATH.$edit_id.'/'.$imageval->Image;
					$imageid_array[]	=	$imageval->id;
				}
			}
		}
	}
}
?>
<body onload="return fieldfocus('<?php echo $field_focus; ?>');" class="skin-black"  <?php if(isset($_GET['popup'])) {?> style="height:auto;min-height:0;" <?}?> >
	<section class="content-header">
		<div class="box-body col-md-7 box-center">
			<h2 align="left">Add Game</h2>
		</div>
	</section>
	
   	<section class="content">
			
		<div class="game-form-style-popup">
				<div class="box-body col-md-7 box-center">
					<div id="rootwizard" class="tabbable tabs popup_tab">
						<ul>
							<li><a href="#tab1" data-toggle="tab" title="Details"><i class="fa fa-list"></i> Details</a></li>
							<li><a title="App Details" <?php if(isset($edit_id) && $edit_id != '' ) { ?> href="#tab2" data-toggle="tab" <?php } ?>><i class="fa fa-mobile"></i> App Details</a></li>
							<li><a title="TiLT Labs" <?php if(isset($edit_id) && $edit_id != '' ) { ?> href="#tab3" data-toggle="tab" <?php } ?>><i class="fa fa-flask"></i> TiLT Labs</a></li>
						</ul>
						<div class="tab-content">
							
							<div class="tab-pane" id="tab1">
								<form name="add_game_detail" id="add_game_detail" action="" method="post" enctype="multipart/form-data" data-webforms2-force-js-validation="true">
									<input type="Hidden" name="game_id" id="game_id_1" value="<?php if(isset($edit_id) && $edit_id != '' ) echo $edit_id;?>">
									<input type="hidden" name="insertios_pnid" value="<?php if($insertios_pnid != '') echo $insertios_pnid; ?>">
									<input type="hidden" name="insertandroid_pnid" value="<?php if($insertandroid_pnid != '') echo $insertandroid_pnid; ?>">
									<div class="clear padding10" align="right"><small>*&nbsp;necessary fields</small></div>
									  <?php if(isset($error_msg) && $error_msg != '') { ?>
										<div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $error_msg;  ?></span></div>
									  <?php } ?>
									  <div class="form-group">
										<label class="col-xs-5 text-right"><span class="required_field">*</span>&nbsp;Game Name </label>
										<div class="col-xs-5">
											<input type="text" class="form-control" name="gamename" id="game_name_field" required="required" maxlength="50" value="<?php if(isset($gamename) && $gamename != '') echo $gamename;  ?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-5 col-xs-12 text-right">&nbsp;App Icon</label>
										<div class="col-xs-7 col-xs-12 pull-right">
											<div class="col-xs-7 col-xs-12 detail_con no-padding">
												<div class="btn btn-file btn-gray">
													Select File <input type="file"  name="game_photo" id="game_photo" onchange="return ajaxAdminFileUploadProcess('game_photo');"  />
												</div>
												<div id="loading" style="display:none"></div>
												<p class="help-block clear text-right">(Minimum dimension 100x100)</p>
											</div>
											<div class="fakefile_photo col-xs-3">
												<div id="game_photo_img">
													<?php if($exists != 0 && $temp_app_image != '' ){ ?>
														<img src="<?php echo TEMP_IMAGE_PATH.$temp_app_image; ?>" width="75" height="75">
														<input type="hidden" name="game_photo_upload" id="game_photo_upload" value="<?php echo $temp_app_image;?>">
													<?php }else if(isset($user_image_path) && $user_image_path != ''){  ?>
														<img class="img_border" src="<?php  echo $user_image_path;  ?>" width="75" height="75" alt="Image"/>
													<?php  }else{  ?>
														<img src="<?php echo GAME_IMAGE_PATH; ?>add_game.jpg" width="75" height="75">
													<? } ?>
												</div>
											</div>
											<?php  if(isset($_POST['game_photo_upload']) && $_POST['game_photo_upload'] != '' && $exists != 0){  ?><input type="Hidden" name="game_photo_upload" id="game_photo_upload" value="<?php  echo $_POST['game_photo_upload'];  ?>"><?php  }  ?>
											<input type="Hidden" name="empty_game_photo" id="empty_game_photo" required="required" value="<?php  if(isset($user_image_name) && $user_image_name != '') { echo $user_image_name; }  ?>" />
											<input type="Hidden" name="name_game_photo" id="name_game_photo" value="<?php  if(isset($user_image_name) && $user_image_name != '') { echo $user_image_name; }  ?>" />
										</div>							
									</div>
									<div class="form-group full_width">
										<label class="col-xs-5 col-xs-12 text-right">iOS</label>
										<div class="col-xs-7">
											<div class="onoffswitch">
												<input type="checkbox" name="iosswitch" class="onoffswitch-checkbox" id="myonoffswitch" <?php if($iosswitch == 1) echo "checked";?>>
												<label class="onoffswitch-label" for="myonoffswitch">
												<div class="onoffswitch-type">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</div>
												</label>
											</div>
										</div>
									</div>
									<div class="form-group full_width">
										<label class="col-xs-5 col-xs-12 text-right">Android</label>
										<div class="col-xs-7">
											<div class="onoffswitch">
												<input type="checkbox" name="androidswitch" class="onoffswitch-checkbox" id="">
												<label class="onoffswitch-label" for="myonoffswitch_and">
												<div class="onoffswitch-type">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</div>
												</label>
											</div>
										</div>
									</div>
									<?php if(!isset($_GET['editId'])) { ?>
									<input type="hidden" id="elimination_rule_1" name="elimination_rule[]" value="10 turns per player" >
									<input type="hidden" id="highscore_rule_1" name="highscore_rule[]" value="3 turns per player">
									<input type="hidden" id="elimination_rule_2" name="elimination_rule[]" value="3 hours to play" >
									<input type="hidden" id="highscore_rule_2" name="highscore_rule[]" value="1 hour to play" >
									<input type="hidden" id="elimination_rule_3" name="elimination_rule[]" value="Game starts when even players join" >
									<input type="hidden" id="highscore_rule_2" name="highscore_rule[]" value="Game starts when all players join" >
									<?php } ?>
									<div class="form-group">
										<div class="col-sm-12 col-md-12 sub_btn text-center"><input type="submit" class="btn btn-green" name="saveDetail" <?php if(isset($edit_id) && $edit_id != '' ) { ?> value="Save" title="Save" alt="Save" <? }else{ ?> value="Save and generate Game Key" title="Save and generate Game Key" alt="Save and generate Game Key" <?php }?> onclick="return validateGameDetailForm();"></div>	<!--  -->
									</div>
									<?php  if(isset($edit_id) && $edit_id != '' ) { ?>
									<div class="form-group">
										<label class="col-xs-5 col-xs-12 text-right">Game Key</label>
										<div class="col-xs-7">
											<label style="color:#000 !important"><?php  if(isset($tiltkey) && $tiltkey != '' ) echo $tiltkey;   ?></label>
										</div>
									</div>
									<?php } ?>	
								</form>
							</div>
							
							<div class="tab-pane" id="tab2">
							  <form name="add_game_app" id="add_game_app" action="" method="post" onsubmit="" enctype="multipart/form-data" data-webforms2-force-js-validation="true">
							  <input type="Hidden" name="game_id" id="game_id_2" value="<?php if(isset($edit_id) && $edit_id != '' ) echo $edit_id;?>">
							  <input type="Hidden" name="game_key" id="" value="<?php if(isset($tiltkey) && $tiltkey != '' ) echo $tiltkey; ?>">
							  <div class="clear padding10" align="right"><small>*&nbsp;necessary fields</small></div>
								<?php if(isset($sucess_msg_2) && $sucess_msg_2 != '') { ?>
									<div class="success_msg no-padding w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $sucess_msg_2;?></span></div>
								<? } ?>
								<div style="<?php if($iosswitch != 1) echo "display:none;" ?>">
									<input type="hidden" name="insertios_pnid" value="<?php if($insertios_pnid != '') echo $insertios_pnid; ?>">
									<div class="form-group"><h4 class="col-xs-12"><i class="fa fa-apple"></i> iOS</h4></div>
									<div class="form-group">
										<label class="col-xs-5 text-right">Bundle ID</label>
										<div class="col-xs-5">
											<input type="text" class="form-control" name="bundle" id="bundle" maxlength="90" value="<?php  if(isset($bundle)) echo $bundle; ?>" autocomplete="off">
										</div>
									</div> 
									<div class="form-group">
										<label class="col-xs-5 text-right">iTunes URL</label>
										<div class="col-xs-5">
											<input type="url" class="form-control" pattern="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?" id="itunesurl" name="itunesurl" value="<?php if(isset($itunesurl) && $itunesurl != '') echo $itunesurl;  ?>" maxlength="250" autocomplete="off">
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-5 col-xs-12 text-right"><?php if(isset($edit_id) && $edit_id != '' ){ ?>&nbsp;<span class="required_field">* </span><?php }?>Apple Push Certificate (.p12)</label>
										<div class="col-xs-7 col-xs-12">
											<div class="col-xs-7 col-xs-12 no-padding">
												<div class="btn btn-file btn-gray ">
													Select File <input type="file"  name="game_certificate" id="game_certificate" title="" onchange="ajaxCertificateUploadProcess(this.value,'game_certificate');"  />
												</div>
											</div>
											 <div id="loading" style="display:none"></div>
											<p align="left" style="padding-top:5px;" class="name_game_certificate certificate_name" id="name_game_certificate"><?php if(isset($certificateName) && !empty($certificateName)) echo $certificateName.'&nbsp;&nbsp;&nbsp;<a target="_blank" href="Download?gameId='.$gameId.'&fileName='.urlencode($certificateName).'" ><i class="fa fa-download fa-2x"></i></a>';else echo 'No File selected' ?></p>
											<input type="Hidden" name="empty_game_certificate" id="empty_game_certificate" value="<?php if(isset($certificateName) && !empty($certificateName)) echo $certificateName;?>" />
											<input type="Hidden" name="old_game_certificate" id="old_game_certificate" value="<?php if(isset($certificateName) && !empty($certificateName)) echo $certificateName;?>" />
											<input type="Hidden" name="flag_game_certificate" id="flag_game_certificate" value="" />
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-5 text-right">Active Certificate</label>
										<div class="col-xs-5">
											<span id="status_game_certificate" class="<?php echo $certClass; ?>"><?php if(isset($certStatus) && $certStatus !='') echo $certStatus; ?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-5 text-right">Password</label>
										<div class="col-xs-5">
										<input type="password" class="form-control" name="ios_password" id="ios_password" placeholder="<?if(isset($ios_password) && $ios_password == 1 ) echo 'Overwrite old password';else {?>Your Certificate Password (leave empty for no password)<?php }?>" value="" maxlength="100"  autocomplete="off" ondrop="return false;" onpaste="return false;">
										</div>
									</div>	
								</div>
							
							<div style="<?php echo "display:none;" ?>">
								<input type="hidden" name="insertandroid_pnid" value="<?php if($insertandroid_pnid != '') echo $insertandroid_pnid; ?>">
								<div class="form-group"><h4 style="width:100px"><i class="fa fa-android"></i> Android</h4></div>
								<div class="form-group">
									<label class="col-xs-5 text-right">Package ID</label>
									<div class="col-xs-5">
										<input type="text" class="form-control" name="package" id="package" maxlength="90" value="<?php  if(isset($package)) echo $package;   ?>" autocomplete="off">
									</div>
								</div> 
								<div class="form-group">
									<label class="col-xs-5 text-right">Google Play URL</label>
									<div class="col-xs-5">
										<input type="url" class="form-control" pattern="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?" id="androidurl" name="androidurl" value="<?php if(isset($androidurl) && $androidurl != '') echo $androidurl;  ?>" maxlength="200" autocomplete="off">
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-5 text-right">GCM key</label>
									<div class="col-xs-5">
									<input type="text" class="form-control" name="gcmkey" id="gcmkey"  maxlength="250" value="<?php if(isset($gcm_key) && $gcm_key != '') echo $gcm_key; ?>"  autocomplete="off">
									</div>
								</div>
							</div>
							<div align="center"><input type="submit" class="btn btn-green" name="appSave" value="Save" title="Save" alt="Save" onclick="validateGameAppForm();"></div>
							</form>
							</div>
							<div class="tab-pane" id="tab3">
								<form name="add_game_lab" id="add_game_lab" action="" method="post" onsubmit="return assign_fileupload();" enctype="multipart/form-data" data-webforms2-force-js-validation="true">
								<input type="Hidden" name="game_id" id="game_id_3" value="<?php if(isset($edit_id) && $edit_id != '' ) echo $edit_id;?>">
								<div class="clear padding10" align="right"><small>*&nbsp;necessary fields</small></div>
								<?php if(isset($sucess_msg_3) && $sucess_msg_3 != '') { ?>
									<div class="success_msg no-padding w50 form-group"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $sucess_msg_3;  ?></span></div>
								<?php } ?>
								<div class="form-group">
									<label class="col-xs-4 text-right"><span class="required_field">*</span>&nbsp;Play Time</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="play_time" id="play_time" placeholder="hh:mm:ss" maxlength="8" value="<?php  if(isset($playTime) && !empty($playTime) && $playTime !='00:00:00') echo $playTime;   ?>" required  autocomplete="off" onkeypress="return timeField(event);">
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 text-right">Description</label>
									<div class="col-xs-8">
										<textarea  name="gamedescription" id="gamedescription" class="form-control" rows="4"  maxlength="2000"><?php if(isset($gamedescription) && $gamedescription !='') echo $gamedescription;?></textarea>	
									</div>
								</div>
								<div class="form-group no-margin clear divrule clear">
									<div class="col-xs-12">							
										<div class="label-one col-xs-5 no-padding"><span class="required_field">*</span> Elimination Rule</div>					
										<div class="col-xs-5 no-padding label-two" style="">High Score Rule</div>
										<div class="col-xs-2 no-padding">&nbsp;</div>
									</div> 
									<?php if(isset($edit_id) && $edit_id != '' && is_array($gameRulesResult) && count($gameRulesResult) > 0){
											for($index = 0;$index < count($gameRulesResult);$index++){
												$hideadd = "style='display:none;'";
												if($index == count($gameRulesResult)-1)
													$hideadd = '';
											?>
									<div class="col-sm-12 row form-group clear col-xs-12 " clone="<?php echo $index;?>" id="gamerule" style="margin-left:1px"	>	
										<div class="col-xs-5">
											<input type="text" class="form-control" id="elimination_rule_<?php echo $index;?>" name="elimination_rule[]" value="<?php if(is_array($eliminationArr) && isset($eliminationArr[$index])) echo $eliminationArr[$index];?>" autocomplete="off" maxlength="200">												
										</div>							
										<div class="col-xs-5">
											<input type="text" class="form-control" id="highscore_rule_<?php echo $index;?>" name="highscore_rule[]" value="<?php if(is_array($highscoreruleArr) && isset($highscoreruleArr[$index]))  echo $highscoreruleArr[$index];?>" autocomplete="off" maxlength="200">					
										</div>
										<div class="pull-right"><a href="javascript:void(0);" onclick="delRule(this);"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="addRule(this);" class="addrule" <?php echo $hideadd; ?>><i class="fa text-green fa-lg fa-plus-circle"></i></a></div>
									</div>
								  <?php   }
									 } else {?>	
										<div class="col-xs-12 row form-group col-xs-12" clone="1" id="gamerule" style="margin-left: 1px;">	
											<div class="col-xs-5">
												<input type="text" class="form-control" id="elimination_rule_1" name="elimination_rule[]" value="10 turns per player" autocomplete="off" maxlength="200">											
											</div>							
											<div class="col-xs-5">
												<input type="text" class="form-control" id="highscore_rule_1" name="highscore_rule[]" value="3 turns per player" autocomplete="off" maxlength="200">								
											</div>
											<div class="pull-right"><a href="javascript:void(0);" onclick="delRule(this);"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="addRule(this);" class="addrule" style="display:none"><i class="fa text-green fa-lg fa-plus-circle"></i></a></div>
										</div>		
										<div class="col-xs-12 row form-group col-xs-12" clone="2" id="gamerule" style="margin-left: 1px;">	
											<div class="col-xs-5">
												<input type="text" class="form-control" id="elimination_rule_2" name="elimination_rule[]" value="3 hours to play" autocomplete="off" maxlength="200">											
											</div>							
											<div class="col-xs-5">
												<input type="text" class="form-control" id="highscore_rule_2" name="highscore_rule[]" value="1 hour to play" autocomplete="off" maxlength="200">								
											</div>
											<div class="pull-right"><a href="javascript:void(0);" onclick="delRule(this);"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="addRule(this);" class="addrule" style="display:none"><i class="fa text-green fa-lg fa-plus-circle"></i></a></div>
										</div>
										<div class="col-xs-12 row form-group col-xs-12" clone="3" id="gamerule" style="margin-left: 1px;">	
											<div class="col-xs-5">
												<input type="text" class="form-control" id="elimination_rule_3" name="elimination_rule[]" value="Game starts when even players join" autocomplete="off" maxlength="200">											
											</div>							
											<div class="col-xs-5">
												<input type="text" class="form-control" id="highscore_rule_3" name="highscore_rule[]" value="Game starts when all players join" autocomplete="off" maxlength="200">								
											</div>
											<div class="pull-right"><a href="javascript:void(0);" onclick="delRule(this);"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="addRule(this);" class="addrule"><i class="fa text-green fa-lg fa-plus-circle"></i></a></div>
										</div>
								  <?php }?>
								</div>
								<div class="form-group no-margin clear" id="imagefiles">
									<label class="col-xs-4 text-right">Image for User Portal</label>
									<?php if(is_array($image_array) && isset($image_array) && count($image_array)>0){ 
												for($index = 0;$index < count($image_array);$index++){
												 if(isset($image_array[$index]) && $image_array[$index] != ''){
													$hideadd = "style='display:none;'";
													if($index == count($image_array)-1)
														$hideadd = '';
																?>
									<div class="col-xs-8 row  no-padding pull-right " clone="<?php echo $index;?>" id ="imagerow" style="margin: 0px;">	
										<div class="col-xs-12 no-padding" style="text-align: left">
											<div class="col-xs-6">
												<div class="btn btn-file btn-gray ">
													Select File <input type="file"  name="game_image<?php echo $index ;?>" id="game_image<?php echo $index ;?>" title="" value="<?php echo $image_array[$index];?>" onchange="return ajaxAdminFileUploadProcess(this.id);"  />
												</div>
												<p class="help-block">(Minimum dimension 100x100)</p>	
											</div>
											<div id="game_image<?php echo $index ;?>_img" class="col-xs-3">
												<img class="img_border" src="<?php  echo $image_patharray[$index];  ?>" width="75" height="75" alt="Image"/>
											</div>
											
											<div class="no-padding pull-right">
												<a href="javascript:void(0);" onclick="delImage(this);"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="addImagePopup(this);" class="addimg"  <?php echo $hideadd; ?> ><i class="fa text-green fa-lg fa-plus-circle"></i></a>
											</div>	
											<input type="hidden" value="" name="game_temp_file[]"  id="game_temp_file<?php echo $index ;?>">
											<input type="hidden" value="<?php echo $image_array[$index];?>" name="oldgame_temp_file[]"  id="oldgame_temp_file<?php echo $index ;?>"> 
											<input type="Hidden" value="<?php echo $imageid_array[$index];?>" id="image_id" name = "image_id[]" >								
										</div>														
										<div class="col-sm-3" id="action<?php echo $index ;?>"></div>
									</div>
								  <?php  } }
									 } else {?>	
										<div class="col-xs-8 row  no-padding pull-right " clone="0"  id ="imagerow" style="margin: 0px;">	
											<div class="col-xs-12 no-padding" style="text-align: left">
												
												<div class="col-xs-6">
													<div class="btn btn-file btn-gray ">
														Select File <input type="file"  name="game_image0" id="game_image0" title="" value="" onchange="return ajaxAdminFileUploadProcess(this.id);"  />
														</div>
														<p class="help-block text-right">(Minimum dimension 100x100)</p>		
												</div>
												<div id="game_image0_img" class="col-xs-3"></div>
												<div  class="pull-right">
														<a href="javascript:void(0);" onclick="delImage(this);" ><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="addImagePopup(this);" class="addimg"><i class="fa text-green fa-lg fa-plus-circle"></i></a>
												</div>
													<input type="hidden" value="" name="game_temp_file[]"  id="game_temp_file0">
													<input type="hidden" value="" name="oldgame_temp_file[]"  id="oldgame_temp_file0"> 
													<input type="Hidden" value="" id="image_id" name = "image_id[]">
											</div>
											<div class="col-sm-3" id="action0"></div>
										</div>							
								  <?php }?>
								</div>
								<div align="center"><input type="submit" class="btn btn-green" name="labSave" value="Save" title="Save" alt="Save" onclick="validateGameLabForm()"></div>
								</form>
							</div>
						</div>	
					</div>
				</div>
		</div>
	</section>
		<div class="clear" align="center"></div>
						  	
<?php commonFooter(); ?>
<script type="text/javascript">
$(document).ready(function() {
  	$('#rootwizard').bootstrapWizard({'tabClass': 'nav nav-tabs'});
	$('#rootwizard').bootstrapWizard('show',<?php echo $show; ?>);
	setTimeout(function(){$(".success_msg").fadeOut(1000); }, 6000);
});

function assign_fileupload(){

		$("div #imagerow").each(function() {			
			var n = $(this).attr('clone');
			var file	=	$("#game_image"+n+"_upload").val();
			if(file!=''){
				$("#game_temp_file"+n).val(file);			
			}		
		});
		return true
}

function validateGameDetailForm(){
	if($.trim($("#game_name_field").val()) !=''){
		document.getElementById("game_name_field").setCustomValidity(""); 
	} else {
		document.getElementById("game_name_field").setCustomValidity("Please fill Game Name");
	}
} 

function validateGameAppForm(){
	<?php if($iosswitch == 1) {?>
	if($("#empty_game_certificate").val() == '' && $("#game_id_2").val() != '' )	
		document.getElementById("game_certificate").setCustomValidity("Apple Push Certificate is required"); 
	else
		document.getElementById("game_certificate").setCustomValidity("");
	<?php } ?>
}

function validateGameLabForm(){
	var timeFormat      =   /^([0-9][0-9]):([0-5][0-9]):([0-5][0-9])$/;
	var playTime = $("#play_time").val();
	if( playTime != ''){
		if(playTime != '00:00:00'){
			if ( ( timeFormat.test($("#play_time").val()) ) !== true) {
				document.getElementById('play_time').setCustomValidity("Please provide the correct time format"); 
			} else {
				document.getElementById('play_time').setCustomValidity(""); 
			}
		}else{
			document.getElementById('play_time').setCustomValidity("Please provide the valid Play Time"); 
		}
	}
	 $(".divrule").find("div[clone]").each(function() {		
		var text1 	=	$(this).find("input").eq(0).val();	
		var text2 	=	$(this).find("input").eq(1).val();		
		if($.trim(text1) == ""){
			document.getElementById($(this).find("input").eq(0).attr('id')).setCustomValidity("Please fill Elimination Rule");						
		}else {
			document.getElementById($(this).find("input").eq(0).attr('id')).setCustomValidity('');
		}		

		if($.trim(text2) == ""){
			document.getElementById($(this).find("input").eq(1).attr('id')).setCustomValidity("Please fill High Score Rule");						
		}else {
			document.getElementById($(this).find("input").eq(1).attr('id')).setCustomValidity('');
		}				
	});
}
</script>
<style>
.addimg,.addrule {margin-right:5px}
.label-one,.label-two {display:block !important}
.col-xs-6 {width:51%}
#imagerow .col-xs-12 .col-xs-2 {/*padding:0 6px;text-align:right*/}
#gamerule {padding:0}
@media only screen and (max-width : 640px) {
	/*.form-group .col-xs-4,.form-group .col-xs-8,.col-xs-5,.col-xs-6,.col-xs-12,.col-xs-7{width:95%;float:left;clear:both}*/
	#gamerule div:first-child:before{content: "* Elimination Rule" !important}
	#gamerule div:nth-child(2):before{content: "High Score Rule" !important}
	.label-one,.label-two {display:none !important}
	#imagerow .col-xs-6{width:63%}
	.divrule #gamerule .col-xs-5, .divlevel #gamelevel .col-xs-5 {margin-bottom:10px;width:55% !important}
	#imagerow .col-md-3 {float:left;width:auto}
	#gamerule {padding:0 15px}
	.nav-tabs>li>a .fa-mobile,.nav-tabs>li>a .fa-flask{width:14px;text-align:center}
	.form-group > .col-xs-5,.form-group > .col-xs-7,.col-xs-4,.col-xs-8 {width:100% !important}
	.full_width .col-xs-7,.full_width .col-xs-5{width:50% !important;}
	.detail_con {width:65% !important} 
}
@media only screen and (max-width : 480px){
		/*#imagerow .col-xs-12 .col-xs-2 {padding:0;}
		#gamerule .col-xs-1 {width:16.6667% !important}*/
}
@media only screen and (max-width : 479px) { 
	.tabs > .nav-tabs > li > a,.tabs > .nav-tabs > li{width:100%;clear:both;float:none}
	.sub_btn {padding:0 3%}
	.divrule #gamerule .col-xs-5, .divlevel #gamelevel .col-xs-5 {width:100% !important}
}
@media only screen and (max-width : 360px) { .detail_con {width:100%} }


</style>

</html>


