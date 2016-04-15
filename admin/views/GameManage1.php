<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/UserController.php');
require_once('controllers/AdminController.php');
require_once('controllers/GameController.php');
require_once("includes/phmagick.php");
admin_login_check();
commonHead();
$adminLoginObj  =   new AdminController();
$userObj   		=   new UserController();
$gameObj   =   new GameController();
$password	=	0;
$field_focus	=	'username';
$class			=	$ExistCondition		=	$location		=	$photoUpdateString	= '';
$gamename = $email = $sdkcode = $itunesurl = $fbkey = $tiltkey =  $androidurl = $gamedescription =  $minplayers = $bundle = '';
$email_exists 	= 	$facebookid_exist	=	$linkedid_exist	=	$twitter_exist		=	$googleplus_exist	=	$userName_exists	=	$game_exists	=	0;
$insertios_pnid = $insertandroid_pnid = $iosarn_key = $arn_key = $gcmkey = $ios_password = $android_password = ''; 
$eliminationArr = $highscoreruleArr	= $gameRulesResult = $image_array  = $gamefilesResult = $image_patharray = $gameLevelResult = array();
$gameId = $certificatePath	=	'';
$iosswitch = 1;
$androidswitch = 0;
$certStatus	=	'Not Uploaded';
$certClass 	=	'cert_inactive';
$today		=	date('Y-m-d H:i:s');
if(isset($_GET['editId']) && $_GET['editId'] != ''){
	$filecondition       = " fkGamesId = ".$_GET['editId']." and Status = 1 and Image != ''";
	$gamefilesResult  = $gameObj->selectGameFiles($filecondition);
	if(is_array($gamefilesResult) && isset($gamefilesResult) && count($gamefilesResult)>0){
		foreach($gamefilesResult as $imageval)
		{
			 if(SERVER){
				if(image_exists(6,$_GET['editId'].'/'.$imageval->Image)) {
					$image_array[]	=	$imageval->Image;
					$image_patharray[]	=	GAMES_IMAGE_PATH.$_GET['editId'].'/'.$imageval->Image;
					$imageid_array[]	=	$imageval->id;
				}
			}else{
				if(file_exists(GAMES_IMAGE_PATH_REL.$_GET['editId'].'/'.$imageval->Image)){	
					$image_array[]	=	$imageval->Image;
					$image_patharray[]	=	GAMES_IMAGE_PATH.$_GET['editId'].'/'.$imageval->Image;
					$imageid_array[]	=	$imageval->id;
				}
			}
		}
	}
}
if(isset($_POST['submit'])	&&	$_POST['submit']!="")	{
	
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	
	if(isset($_POST['gamename'])	&&	$_POST['gamename']!=''){
		$ExistCondition  = " Name = '".trim($_POST['gamename'])."' "; 
		if($_POST['submit'] == 'Save')
			$id_exists = " and id != '".$_POST['game_id']."' and Status in (1,2,4,5) ";
		else
			$id_exists = "  and Status in (1,2,4,5) ";
		
		if(isset($_POST['gamename'])	&&	$_POST['gamename']!='')
			$gamename 			=	($_POST['gamename']); 
		if(isset($_POST['itunesurl']) 	&&	$_POST['itunesurl']!='')
			$itunesurl   		=	($_POST['itunesurl']);
		if(isset($_POST['androidurl']) 	&&	$_POST['androidurl']!='')
			$androidurl   		=	($_POST['androidurl']);
		if(isset($_POST['gamedescription']) 	&&	$_POST['gamedescription']!='')	
			$gamedescription   	=	($_POST['gamedescription']);
		if(isset($_POST['bundle']) 	&&	isset($_POST['bundle']) && $_POST['bundle']!='')
			$bundle			=	($_POST['bundle']);
		if(isset($_POST['gcmkey']) && $_POST['gcmkey']!='')
			$gcm_key    = $_POST['gcmkey'];
		if(isset($_POST['play_time'])	&&	$_POST['play_time']!='')
			$playTime      		=	trim($_POST['play_time']);	
		$field 				= 	" id, Name ";	
		$ExistCondition 	.=	$id_exists;
		
		// To check the already exist condition for the user email address and there fb id
		$alreadyExist    	=	$gameObj->selectGameDetails($field,$ExistCondition);
		
		if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0)	{
			if(($alreadyExist[0]->Name == $_POST['gamename']) && ($_POST['gamename'] != ''))
				$game_exists 			=	2;
		}
		if($game_exists != '2' )	{
			if($_POST['submit'] == 'Save')	{
				if(isset($_POST['game_id']) && $_POST['game_id'] != '')	{
					$condition	= " id = ".$_POST['game_id'];	
					$fields 	= " Name            	=	'".trim($_POST['gamename'])."',
								PlayTime         	=	'".trim($_POST['play_time'])."',";
					if(isset($_POST['iOnOff'])){
						$fields .= " IosStatus            	=	'".trim($_POST['iOnOff'])."',";
						if($_POST['iOnOff'] == 1){
							if(isset($_POST['bundle']))
								$fields .= " Bundle        =	'".trim($_POST['bundle'])."',";
							if(isset($_POST['itunesurl']))
								$fields .= " ITunesUrl 	=	'".trim($_POST['itunesurl'])."',";
						}
						else{
							$fields .= " Bundle        =	'',";
							$fields .= " ITunesUrl 	=	'',";
						}
					}
					if(isset($_POST['aOnOff'])){
						$fields .= " AndroidStatus            	=	'".trim($_POST['aOnOff'])."',";
						if($_POST['aOnOff'] == 1){
							if(isset($_POST['androidurl']))
								$fields .= " AndroidUrl        =	'".trim($_POST['androidurl'])."',";
							if(isset($_POST['package']))
								$fields .= " Package        =	'".trim($_POST['package'])."',";
						}
						else{
							$fields .= " AndroidUrl        =	'',";
							$fields .= " Package        =	'',";
						}
					}
					if(isset($_POST['gamedescription']))
						$fields .="Description				=	'".trim($_POST['gamedescription'])."',";
					
					$fields .= 	" DateModified			 =	'".date('Y-m-d H:i:s')."'";
					$gameObj->updateGameDetails($fields,$condition);
					$insert_id = $_POST['game_id'];
					$gameObj->updateGameRules('Status = 2',$_POST['game_id']);
					
					$_SESSION['notification_msg_code']	=	2;
				}
			}
			if($_POST['submit']		==	'Add')	{
				$insert_id   		=	$gameObj->insertGameDetails($_POST);
				$wordResult 	    =	$userObj->selectWordDetails();
				$word               =	$wordResult[0]->words;
				$numeric            =	'1234567890';
				$numbers            =	substr(str_shuffle($numeric), 0, 3);
				$actualPassword     =	trim($word.$numbers).$insert_id;
				$password			=	sha1($actualPassword.ENCRYPTSALT);
				$updateString 		=	" Password = '" . $password . "' ";
				$condition 			=	"id = ".$insert_id;
				// To upload the user table with the generated password
				$gameObj->updateGameDetails($updateString,$condition);
				$_SESSION['notification_msg_code']	=	1;
			}
			
			if(isset($insert_id)	&&	$insert_id!='')	{
			
				$insert_tilt_key = '';
				if(isset($_POST['tiltkey']) && $_POST['tiltkey'] != ''){
					$insert_tilt_key =  $_POST['tiltkey'];
				}else
				{
					$cond = ' id = '.$insert_id;
					$gettiltkey    	=	$gameObj->selectGameDetails('TiltKey',$cond);
					if(isset($gettiltkey) && is_array($gettiltkey) && count($gettiltkey) > 0){
						$insert_tilt_key = $gettiltkey[0]->TiltKey;
					}
				}
				
				if(isset($_POST['ios_pnid']) && $_POST['ios_pnid'] != '')
					$insertios_pnid = $_POST['ios_pnid'];
				if(isset($_POST['android_pnid']) && $_POST['android_pnid'] != '')
					$insertandroid_pnid = $_POST['android_pnid'];
				if(isset($_POST['iOnOff']) && $_POST['iOnOff'] == 1){ //iOS details given
					if($insertios_pnid != ''){ //Update old
						$pn_fields = '';
						if(isset($insert_tilt_key) && $insert_tilt_key != '')
							 $pn_fields = " fkGamesKey =	'".$insert_tilt_key."',";
						if(isset($_POST['ios_password'])	&&	$_POST['ios_password']!='')
							$pn_fields .= "CertificatePassword =  '".md5($_POST['ios_password'])."',";
						$pn_fields .= 	"Status 				 =	1,
									DateModified			 =	'".date('Y-m-d H:i:s')."'";
						$pn_condition 			= "id = ".$insertios_pnid;
						$gameObj->updateGamePushNotification($pn_fields,$pn_condition);
					}else{ //New entry
						if((isset($_FILES['game_certificate']['name']) && $_FILES['game_certificate']['name'] != '')){
							$pn_array['platform'] = 1;
							$pn_array['game_id'] =   $insert_id;
							$pn_array['tiltkey'] = $insert_tilt_key;
							if(isset($_POST['ios_password'])	&&	$_POST['ios_password']!= '')
								$pn_array['password'] = md5($_POST['ios_password']);
							$insertios_pnid  =	$gameObj->insertGamePushNotification($pn_array);
						}
					}
				}else{ // iOS in Off
					if($insertios_pnid != ''){//Unset previous entry
						$pn_fields 		= "Status = 2";
						$pn_condition 	= "id = ".$insertios_pnid;
						$gameObj->updateGamePushNotification($pn_fields,$pn_condition);
					}
					if ($_SERVER['HTTP_HOST'] != '172.21.4.104'){
						 if($_POST['old_game_certificate'] != '') {
							 if(image_exists(19,$_POST['old_game_certificate'])) {
								deleteImages(19,$_POST['old_game_certificate']);
							 }
						}
					} else if($_POST['old_game_certificate'] != '') {
						if(file_exists(UPLOAD_GAME_CERTIFICATE_PATH_REL.$insert_id.'/'.$_POST['old_game_certificate']))
							unlink(UPLOAD_GAME_CERTIFICATE_PATH_REL.$insert_id.'/'.$_POST['old_game_certificate']);
					}
				}
				
				if(isset($_POST['aOnOff']) && $_POST['aOnOff'] == 1){ //Only push Android in on status
					if($insertandroid_pnid != ''){ //Update old
						$pn_fields = '';
						if(isset($insert_tilt_key) && $insert_tilt_key != '')
							 $pn_fields = " fkGamesKey =	'".$insert_tilt_key."',";
						if(isset($_POST['gcmkey']))
							$pn_fields .= "GCMkey =  '".$_POST['gcmkey']."',";
						$pn_fields .= 	"Status  =	1,
									DateModified =	'".date('Y-m-d H:i:s')."'";
						$pn_condition 			= "id = ".$insertandroid_pnid;	
						$gameObj->updateGamePushNotification($pn_fields,$pn_condition);
					}else{ //Update new entry
						if(isset($_POST['gcmkey'])	&&	$_POST['gcmkey']!= ''){
							$pn_array = array();
							$pn_array['platform'] = 2;
							$pn_array['game_id'] =   $insert_id;
							$pn_array['tiltkey'] = $insert_tilt_key;
							if(isset($_POST['gcmkey'])	&&	$_POST['gcmkey']!= '')
								$pn_array['gcmkey'] = $_POST['gcmkey'];
							$insertandroid_pnid  =	$gameObj->insertGamePushNotification($pn_array);
						}
					}
				}else{ //Android Off
					if($insertandroid_pnid != ''){ //Unset previous entry
						$pn_fields 		= " Status = 2 ";
						$pn_condition 	= " id = ".$insertandroid_pnid;	
						$gameObj->updateGamePushNotification($pn_fields,$pn_condition);
					}
				}
				$photoUpdateString	=	'';
				//Game Image upload
				if (isset($_POST['game_photo_upload']) && !empty($_POST['game_photo_upload'])) {
					$imageName 				= $insert_id . '_' .time() . '.png';
					$temp_image_path 		= TEMP_USER_IMAGE_PATH_REL . $_POST['game_photo_upload'];
					$image_path 			= UPLOAD_GAME_PATH_REL . $imageName;
					$imageThumbPath     	= UPLOAD_GAME_THUMB_PATH_REL.$imageName;
					$oldGameImage			= $_POST['name_game_photo'];
					if ( !file_exists(UPLOAD_GAME_PATH_REL) ){
						mkdir (UPLOAD_GAME_PATH_REL, 0777);
					}
					if ( !file_exists(UPLOAD_GAME_THUMB_PATH_REL) ){
						mkdir (UPLOAD_GAME_THUMB_PATH_REL, 0777);
					}
					copy($temp_image_path,$image_path);
					
					$phMagick = new phMagick($image_path);
					$phMagick->setDestination($imageThumbPath)->resize(100,100);
					
					if ($_SERVER['HTTP_HOST'] != '172.21.4.104'){
						if($oldGameImage!='') {
							if(image_exists(6,$oldGameImage)) {
								deleteImages(6,$oldGameImage);
							}
							if(image_exists(7,$oldGameImage)) {
								deleteImages(7,$oldGameImage);
							}
						}
						
						uploadImageToS3($imageThumbPath,7,$imageName);
						uploadImageToS3($image_path,6,$imageName);
						unlink($image_path);
						unlink($imageThumbPath);
					}
					else if($oldGameImage!='') {
						if ( file_exists(UPLOAD_GAME_PATH_REL.$oldGameImage) )
							unlink(UPLOAD_GAME_PATH_REL.$oldGameImage);
						if ( file_exists(UPLOAD_GAME_THUMB_PATH_REL.$oldGameImage) )
							unlink(UPLOAD_GAME_THUMB_PATH_REL.$oldGameImage);
					}
					$photoUpdateString	.= " Photo = '" . $imageName . "'";
					unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['game_photo_upload']);
				}
				
				if($photoUpdateString!='')
				{
					$condition 			= "id = ".$insert_id;
					$gameObj->updateGameDetails($photoUpdateString,$condition);
				}
				
				
				//game_certificate
				if(isset($_POST['iOnOff']) && $_POST['iOnOff'] == 1){ //iOS
					$photoUpdateString = '';
					if(isset($_POST['flag_game_certificate']) && $_POST['flag_game_certificate'] == 1 && isset($_FILES['game_certificate']) && count($_FILES['game_certificate']) > 0  ){
						if($_FILES['game_certificate']['name'] != '')
						{
							//tmp_name
							// $certificateName	=	$insert_id.'/'.$_FILES['game_certificate']['name'];
							$LimitFileName		=	substr(substr(str_replace(' ', '_', $_FILES['game_certificate']['name']),0,-4),0,100).".p12";
							$certificateName	=	$insert_id.'/'.$LimitFileName;

							//UPLOAD_GAME_CERTIFICATE_PATH_REL
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
							 if($photoUpdateString!='')
								$photoUpdateString	.=	",Certificate = '".$LimitFileName."'";		//$photoUpdateString	.=	",Certificate = '".str_replace(' ', '_', $_FILES['game_certificate']['name'])."'";
							else
								$photoUpdateString	=	" Certificate = '".$LimitFileName."'";
								// $photoUpdateString	=	" Certificate = '".str_replace(' ', '_', $_FILES['game_certificate']['name'])."'";
							 unlink($_FILES['game_certificate']['tmp_name']);
						}
					}
					//end game certificate
					if($photoUpdateString!='' &&  $insertios_pnid != '')
					{
						$condition 			= "id = ".$insertios_pnid;
						$gameObj->updateGamePushNotification($photoUpdateString,$condition);
					}
				}
				//insert GameRule
				$insertRules = '';
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
				
				//del old images
				if(isset($imageid_array) &&  $imageid_array != ''){
					foreach($imageid_array as $imgkey=>$imgvalue)
					{
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
								else if ( $oldImage !='' && file_exists(GAMES_IMAGE_PATH_REL.$insert_id.'/'.$oldImage) ){
									unlink(GAMES_IMAGE_PATH_REL.$insert_id.'/'.$oldImage);
								}
							}
						}
					}
				}
				
				if(isset($tempFileArray)	&& is_array($tempFileArray) &&  isset($oldtempFileArray) &&  is_array($oldtempFileArray) ){
				   foreach($tempFileArray as $key=>$value){
							
							if(isset($tempFileArray[$key]) && 	$tempFileArray[$key] != ''){
									
									//del old images
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
											else if ( $oldImage !='' && file_exists(GAMES_IMAGE_PATH_REL.$insert_id.'/'.$oldImage) ){
												unlink(GAMES_IMAGE_PATH_REL.$insert_id.'/'.$oldImage);
											}
										}
									}
									
									//insert images
									$queryString	=	"(".$insert_id.",'','1','".$today."')";
									$queryString	=	$queryString;
									$entryId	    =   $gameObj->insertGameFiles($queryString);
									$temp_image_path  = TEMP_USER_IMAGE_PATH_REL.$value;
									$fileName = '';
									if(isset($value) && $value !=''){	
										$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
										$uploadPath	= UPLOAD_GAME_PATH_REL;
										if ( !file_exists($uploadPath.$insert_id) ){
											mkdir ($uploadPath.$insert_id, 0777);
										}
										$fileName	=	'game_'.$entryId.'_'.time().'.'.$ext;
										$imageName 	=	 $insert_id.'/'.$fileName;
										$temp_image_path 	=	TEMP_USER_IMAGE_PATH_REL.$value;
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
			}
			header("location:GameList?cs=1");
			die();
		}	//	End of Already Exist condition
		else{
			if($game_exists	==	2	)	{
				$error_msg   = "Game already exists";
				$field_focus = 'gamename';
			}
			$display = "block";
			$class   = "error_msg";
		}	
	}
}
$gameFlag = 1;
if(isset($_GET['editId']) && $_GET['editId'] != '' ){
	$condition       = " id = ".$_GET['editId']." and Status in (1,2,4)";
	$field			 = " * ";
	$gameId	=	$_GET['editId'];
	$gameDetailsResult  = $gameObj->getSingleGameDetails($field,$condition);
	if(isset($gameDetailsResult) && is_array($gameDetailsResult) && count($gameDetailsResult) > 0){
		
		$gameId	 			=	$gameDetailsResult[0]->id;
		$gamename 			=	$gameDetailsResult[0]->Name;
		$tiltkey   			=	$gameDetailsResult[0]->TiltKey;
		$itunesurl 			=	$gameDetailsResult[0]->ITunesUrl;
		$androidurl 		=	$gameDetailsResult[0]->AndroidUrl;
		$bundle     		= 	$gameDetailsResult[0]->Bundle;
		$package    		= 	$gameDetailsResult[0]->Package;
		$playTime   		= 	$gameDetailsResult[0]->PlayTime;
		$gamedescription 	= 	$gameDetailsResult[0]->Description;
		$iosswitch 		 	=	$gameDetailsResult[0]->IosStatus;
		$androidswitch 	 	=	$gameDetailsResult[0]->AndroidStatus;

		if(isset($gameDetailsResult[0]->Photo) && $gameDetailsResult[0]->Photo != ''){
			$user_image_name = $gameDetailsResult[0]->Photo;
			 if(SERVER){
				if(image_exists(3,$user_image_name))
					$original_image_path = GAMES_IMAGE_PATH.$user_image_name;
				else
					$original_image_path = '';	
				if(image_exists(1,$user_image_name)){
					$user_image_path = GAMES_THUMB_IMAGE_PATH.$user_image_name;
				}
			}else{
				if(file_exists(GAMES_IMAGE_PATH_REL.$user_image_name))
					$original_image_path = GAMES_IMAGE_PATH.$user_image_name;
				else
					$original_image_path = '';	
				if(file_exists(GAMES_THUMB_IMAGE_PATH_REL.$user_image_name))
					$user_image_path = GAMES_THUMB_IMAGE_PATH.$user_image_name;
			}
		}
		//select GameRule
		$rulecondition       = " fkGamesId = ".$_GET['editId']." and Status = 1 ";
		$gameRulesResult  = $gameObj->selectGameRules($rulecondition);
		if(is_array($gameRulesResult) && isset($gameRulesResult) && count($gameRulesResult)>0){
			foreach($gameRulesResult as $val)
			{
				$eliminationArr[]	=	$val->EliminationRules;
				$highscoreruleArr[]	=	$val->HighscoreRules;
			}
		}
		
		//pushnotification
		$rulecondition       = " fkGamesId = ".$_GET['editId']." and Status = 1";
		$gamepushnotificaion  = $gameObj->selectGamePushNotification(" * " ,$rulecondition);
		if(is_array($gamepushnotificaion) && isset($gamepushnotificaion) && count($gamepushnotificaion)>0){	
			foreach($gamepushnotificaion as $val){
				if($val->Platform == 1){
					$insertios_pnid  = $val->id;
					$certificateName = $val->Certificate;
					if(SERVER){
						if(!image_exists(19,$gameId.'/'.$certificateName)) 
							$certificateName = '';
					}else{
						if(!file_exists(GAME_CERTIFICATE_PATH_REL.$gameId.'/'.$certificateName))
							$certificateName = '';
					}
					if($certificateName !=''){
						$certStatus	= 'Uploaded';
						$certClass 	= 'cert_active';
					}
					if(isset($val->Certificatepassword) && $val->Certificatepassword != '')
						$ios_password	=	1;
				}else{
					$insertandroid_pnid = $val->id;
					$gcm_key            = $val->GCMkey;
				}
			}
		}
	}else{
		$error_msg 	= "Invalid game detail";
		$class		= "error_msg";
		$gameFlag 	= 0;
		$image_array = $image_patharray = $imageid_array = array();
	}
}

?>
<body onload="return fieldfocus('<?php echo $field_focus; ?>');">
	<?php top_header(); ?>
			<div class="box-header">
				<h2><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo '<i class="fa fa-edit"></i>Edit '; else echo '<i class="fa fa-plus-circle"></i>Add ';?>Game</h2></div>
							<div class="clear">
							<form name="add_game_form" id="add_game_form" action="" method="post" onsubmit="return assign_fileupload()" enctype='multipart/form-data'>
							<input type="text" name="fakeusername" id="fakeusername" class="hidden" autocomplete="off" style="display: none;">
							<input type="password" name="fakepassword" id="fakepassword" class="hidden" autocomplete="off" style="display: none;">
							<input type="Hidden" name="game_id" id="game_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
						  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="98%">
									<tr><td align="center">
									<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%"> 
										<tr><td colspan="6" valign="top" class="msg_height" align="center">
											<div class="<?php echo $class;  ?> w50">
												<span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span>
											</div>
										</td></tr>
										<tr>
											<td  height="50" width="16%" align="left"  valign="top"><label>Game Name&nbsp;<span class="required_field">*</span></label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top" width="32%">
												<input type="text" class="input" name="gamename" id="gamename" maxlength="50" value="<?php if(isset($gamename) && $gamename != '') echo $gamename;  ?>" >
											</td>
										
											<td height="50" align="left"  valign="top" width="16%"><label>Play Time&nbsp;<span class="required_field">*</span></label></td>
											<td align="center"  valign="top" width="3%">:</td>
											<td align="left"  height="40"  valign="top" width="32%">
												<input type="text" class="input" name="play_time" id="play_time" value="<?php  if(isset($playTime) && !empty($playTime) && $playTime !='00:00:00') echo $playTime;   ?>" placeholder="hh:mm:ss" maxlength="8" onkeypress="return timeField(event);">
											</td>										
										</tr>
										<tr>
											<td  height="50" width="15%" align="left"  valign="top"><label>iOS&nbsp;</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top" width="32%">
												<input type="radio" class="" name="iOnOff" id="iOff" onchange="changeNotification(1)" value="0" <?php if($iosswitch != 1) echo 'Checked'; ?>> 
												<label for="iOff">Off</label>
												<input type="radio" class="" name="iOnOff" id="iOn" onchange="changeNotification(1)" value="1" <?php if($iosswitch) echo 'Checked'; ?>> 
												<label for="iOn">On</label>
											</td>
										
											<td height="50" align="left"  valign="top"><label>Android&nbsp;</label></td>
											<td align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="radio" class="" name="aOnOff" id="aOff" onchange="changeNotification(2)" value="0" <?php  if($androidswitch != 1)  echo 'Checked'; ?>> 
												<label for="aOff">Off</label>
												<input type="radio" class="" name="aOnOff" id="aOn" onchange="changeNotification(2)" value="1" <?php  if($androidswitch == 1) echo 'Checked';  ?> disabled> 
												<label for="aOn">On</label>
											</td>										
										</tr>
										<tr>	
											<td height="60"  align="left"  valign="top">
												<label>App Icon&nbsp;</label>
											</td>
											<td  align="center" valign="top">:</td>
											<td align="left"  height="60" valign="top">
												<div class="upload fleft">
												<div style="clear: both;float: left"> 
												<input type="file"  name="game_photo" id="game_photo" title="Game Photo" onclick="" onchange="return ajaxAdminFileUploadProcess('game_photo');"  /> 
												</div>
												<div style="width:230px;">(Minimum dimension 100x100)</div>
												<span class="error" for="empty_user_photo" generated="true" style="display: none">Game Image is required</span>

												<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
													<div id="game_photo_img">
														<?php  if(isset($user_image_path) && $user_image_path != ''){  ?>
															<a href="<?php if(isset($original_image_path) && $original_image_path != '') { echo $original_image_path; ?>" class="user_photo_pop_up"<?php } else { ?>Javascript:void(0);<?php } ?>" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $user_image_path;  ?>" width="75" height="75" alt="Image"/></a>
														<?php  } else { ?>
															<img src="<?php echo ADMIN_IMAGE_PATH; ?>add_game.jpg" width="75" height="75">
														<?php } ?>
													</div>
												</div>
												</div>
												
											<?php  if(isset($_POST['game_photo_upload']) && $_POST['game_photo_upload'] != ''){  ?><input type="Hidden" name="game_photo_upload" id="game_photo_upload" value="<?php  echo $_POST['game_photo_upload'];  ?>"><?php  }  ?>
											<input type="Hidden" name="empty_game_photo" id="empty_game_photo" value="<?php  if(isset($user_image_name) && $user_image_name != '') { echo $user_image_name; }  ?>" />
											<input type="Hidden" name="name_game_photo" id="name_game_photo" value="<?php  if(isset($user_image_name) && $user_image_name != '') { echo $user_image_name; }  ?>" />
											</td>
											<?php  if(isset($_GET['editId']) && $_GET['editId'] != '' ) { ?>
											<td height="50" align="left" valign="top"><label>Game Key</label></td>
											<td align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input readonly type="text" class="input" id="tiltkey" name="tiltkey" maxlength="30" value="<?php  if(isset($tiltkey) && $tiltkey != '' ) echo $tiltkey;   ?>" >
											</td>
											<?php } ?>
										</tr>										
										<tr><td height="20"></td></tr>
										<tr>
											<td height="50" align="left"  valign="top"><label>Description</label></td>
											<td align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<textarea  name="gamedescription" id="gamedescription"  rows="4" cols="36" ><?php  if(isset($gamedescription) && $gamedescription != '' ) echo $gamedescription;   ?></textarea>	
											</td>
										</tr>
										<tr><td colspan="6" align="center" width="0%"></td></tr>
										<tr><td height="20"></td></tr>
										<tr>	
											<td height="50" align="left"  valign="top"><label>Rule&nbsp;<span class="required_field">*</span></label></td>	
											<td  align="center" valign="top">:</td>								
											<td colspan="4" style="padding-left:0px;" height="75" valign="top">
												<table class='game_rules'>											
													<tr>
														<td style='color:#494949;' valign="top"><b>Elimination Rule</b></td>
														<td style='color:#494949;' valign="top"><b>High Score Rule</b></td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
													</tr>
												<?php if(isset($_GET['editId']) && $_GET['editId'] != '' && is_array($gameRulesResult) && count($gameRulesResult) > 0){
														for($index = 0;$index < count($gameRulesResult);$index++){
															$hideadd = "style='display:none;'";
															if($index == count($gameRulesResult)-1)
																$hideadd = '';
													?>
													<tr  height="" class="clone rule" clone="<?php echo $index;?>">													
														<td width="28%" valign="top"><input type="text" class="input" id="elimination_rule" name="elimination_rule[]" value="<?php if(is_array($eliminationArr) && isset($eliminationArr[$index])) echo $eliminationArr[$index];?>" ></td>
														<td width="28%" valign="top"><input type="text" class="input" id="highscore_rule" name="highscore_rule[]" value="<?php if(is_array($highscoreruleArr) && isset($highscoreruleArr[$index]))  echo $highscoreruleArr[$index];?>" ></td>	
														<td width="3%" valign="top"><a href="javascript:void(0)" onclick="delRule(this)"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>
														<td width="3%" valign="top"><a href="javascript:void(0)" onclick="addRule(this)" class="addrule" <?php echo $hideadd; ?>><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td> 							
													</tr>
														<?php }
															} else {?> 
													<tr  height="" class="clone rule" clone="1">
														<td width="28%" valign="top"><input type="text" class="input" id="elimination_rule" name="elimination_rule[]" value="10 turns per player" ></td>
														<td width="28%" valign="top"><input type="text" class="input" id="highscore_rule" name="highscore_rule[]" value="3 turns per player" ></td>	
														<td width="3%" valign="top"><a href="javascript:void(0)" onclick="delRule(this)" ><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>
														<td width="3%" valign="top"><a href="javascript:void(0)" onclick="addRule(this)" class="addrule" style="display:none;"><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td> 							
													 </tr>
													 <tr  height="" class="clone rule" clone="2">
														<td width="28%" valign="top"><input type="text" class="input" id="elimination_rule" name="elimination_rule[]" value="3 hours to play" ></td>
														<td width="28%" valign="top"><input type="text" class="input" id="highscore_rule" name="highscore_rule[]" value="1 hour to play" ></td>	
														<td width="3%" valign="top"><a href="javascript:void(0)" onclick="delRule(this)" ><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>
														<td width="3%" valign="top"><a href="javascript:void(0)" onclick="addRule(this)" class="addrule" style="display:none;"><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td> 							
													 </tr>
													 <tr  height="" class="clone rule" clone="3">
														<td width="28%" valign="top"><input type="text" class="input" id="elimination_rule" name="elimination_rule[]" value="Game starts when even players join" ></td>
														<td width="28%" valign="top"><input type="text" class="input" id="highscore_rule" name="highscore_rule[]" value="Game starts when all players join" ></td>	
														<td width="3%" valign="top"><a href="javascript:void(0)" onclick="delRule(this)" ><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>
														<td width="3%" valign="top"><a href="javascript:void(0)" onclick="addRule(this)" class="addrule"><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td> 							
													 </tr>
													<?php }?>
													
												</table>
												<span style="padding-left: 8px; display: inline-block;">
													<input type="hidden" id="hidden_gamerules" name = "hidden_gamerules">
												</span>
											</td>
										</tr>
										<tr><td height="20"></td></tr>
										<tr>	
											<td height="50" align="left"  valign="top"><label>Image for User Portal</label></td>	
											<td  align="center" valign="top">:</td>								
											<td  style="padding-left:0px;">
												<table>
												<?php if(is_array($image_array) && isset($image_array) && count($image_array)>0){ 
														for($index = 0;$index < count($image_array);$index++){
														 if(isset($image_array[$index]) && $image_array[$index] != ''){
															$hideadd = "style='display:none;'";
															if($index == count($image_array)-1)
																$hideadd = '';
														?>
													<tr  height="50" class="clone" clone="<?php echo $index ;?>" id="fileupload">	
														<td align="left" width="80%"  height="60" valign="top">												
															<div id="game_image<?php echo $index ;?>_img">
																<a <?php if(isset($image_patharray[$index]) && $image_patharray[$index] != '') { ?> href="<?php echo $image_patharray[$index]; ?>" class="game_pop_up"<?php } else { ?>href="Javascript:void(0);"<?php } ?> title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $image_patharray[$index];  ?>" width="75" height="75" alt="Image"/></a>
															</div>
															<input type="file"  name="game_image<?php echo $index ;?>" id="game_image<?php echo $index ;?>" title="Game Image" onclick="" value="<?php echo $image_array[$index];?>" onchange="return ajaxAdminFileUploadProcess(this.id);"  />
															<input type="hidden" value="" name="game_temp_file[]"  id="game_temp_file<?php echo $index ;?>">
															<input type="hidden" value="<?php echo $image_array[$index];?>" name="oldgame_temp_file[]"  id="oldgame_temp_file<?php echo $index ;?>"> 
															<input type="Hidden" value="<?php echo $imageid_array[$index];?>" id="image_id" name = "image_id[]" >
															<div style="width:230px;">(Minimum dimension 100x100)</div>															
															</div>																																			
														</td>	
														<td width="10%" valign="top"><a href="javascript:void(0)" onclick="delImage(this)"  ><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>	
														<td width="10%" valign="top"><a href="javascript:void(0)" onclick="addImage(this)"  class="addimg" <?php echo $hideadd; ?>><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td> 																	
													 </tr>		
												<?php   } }
												} else {?>		
														<tr  height="50" class="clone" clone="1" id="fileupload">		
														<td align="left" width="80%"  height="60" valign="top">												
															<div id="game_image1_img"></div>
															<input type="file"  name="game_image1" id="game_image1" title="Game Image" onclick="" onchange="return ajaxAdminFileUploadProcess(this.id);"  />
															<input type="hidden" value="" name="game_temp_file[]"  id="game_temp_file1">
															<input type="hidden" value="" name="oldgame_temp_file[]"  id="oldgame_temp_file1">
															<input type="Hidden" value="" id="image_id" name = "image_id[]" >
															<div style="width:230px;">(Minimum dimension 100x100)</div>															
															</div>																																			
														</td>
														<td width="10%" valign="top"><a href="javascript:void(0)" onclick="delImage(this)"  ><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>											
														<td width="10%" valign="top"><a href="javascript:void(0)" onclick="addImage(this)" class="addimg"><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td> 																																	
													 </tr>		
												<?php }?>								
												</table>
											</td>
										</tr>
										<tr>	
											<td colspan="3" height="50" align="left"  valign="top"><h2 style="padding-left:0px;">Push Notification Settings</h2></td>	
											<td colspan="3">&nbsp;</td>
										 </tr>
										 <tr>
											<td colspan="6">
												<table width="100%" id="iosNotif" style="<?php if($iosswitch != 1) echo "display:none" ; ?>">
													<tr>	
														<td colspan="3" height="50" align="left"  valign="top"><h3>iOS</h3></td>	
														<td colspan="3">&nbsp;</td>
													 </tr>
													 <tr>
														<td height="50" align="left"  valign="top" width="16%" ><label>Bundle ID</label></td>
														<td align="center"  valign="top" width="3%" >:</td>
														<td align="left"  height="40"  valign="top" width="32%" >
															<input type="text" class="input" name="bundle" id="bundle" maxlength="90" value="<?php  if(isset($bundle) && $bundle != '' ) echo $bundle;   ?>">
														</td>
														<td height="50" align="left"  valign="top" width="16%" ><label>iTunes URL</label></td>
														<td align="center"  valign="top" width="3%" >:</td>
														<td align="left"  height="40"  valign="top" width="32%" >
															<input type="url" class="input" id="itunesurl" name="itunesurl" pattern="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?"  value="<?php if(isset($itunesurl) && $itunesurl != '') echo $itunesurl;  ?>" >
														</td>
													 </tr>
													 <tr>																					
														<td height="50" align="left"  valign="top"><label>Apple Push Certificate&nbsp;(.p12)&nbsp;<span class="required_field">*</span></label></td>
														<td align="center"  valign="top">:</td>
														<td align="left"  height="40"  valign="top">
															<div class="upload fleft">
																<div style="clear: both;float: left;width:100%;"> 
																<input type="file"  name="game_certificate" id="game_certificate" style="width:90%;word-wrap:break-all;white-space:pre-wrap;" title="Game certificate" onchange="ajaxCertificateUploadProcess(this.value,'game_certificate');"  />
																</div>
																<span class="error" for="empty_game_certificate" generated="true" style="display: none">Game certificate is required</span>
															</div>
															<br>
															<p class="certificate_name fleft marginb20" style="margin-top:10px;"><?php if(isset($certificateName) && !empty($certificateName)) echo $certificateName.'&nbsp;&nbsp;&nbsp;<a target="_blank" href="Download?gameId='.$gameId.'&fileName='.urlencode($certificateName).'" ><i class="fa fa-download fa-2x"></i></a>'; ?></p>
															<input type="Hidden" name="empty_game_certificate" id="empty_game_certificate" value="<?php if(isset($certificateName) && !empty($certificateName)) echo $certificateName;?>" />
															<input type="Hidden" name="old_game_certificate" id="old_game_certificate" value="<?php if(isset($certificateName) && !empty($certificateName)) echo $certificateName;?>" />
															<input type="Hidden" name="flag_game_certificate" id="flag_game_certificate" value="" />
														</td>
														
														<td height="50" align="left"  valign="top"><label>Active Certificate</label></td>
														<td align="center"  valign="top">:</td>
														<td align="left"  height="40"  valign="top">
															<span id="status_game_certificate" class="<?php echo $certClass; ?>"><em class="fa fa-check"></em><?php if(isset($certStatus) && $certStatus !='') echo $certStatus; ?></span>
														</td>
													</tr>
													<tr>
														<td height="60"  align="left"  valign="top"  valign="top"><label>Password&nbsp;</label></td>
														<td  align="center" valign="top">:</td>
														<td align="left"  height="60" valign="top">
															<input type="password" name="ios_password" id="ios_password" class="input" maxlength="255" placeholder="<?if(isset($ios_password) && $ios_password == 1 ) echo 'Overwrite old password'; else {?>Your Certificate Password (leave empty for no password)<?php }?>" value="" autocomplete="off" ondrop="return false;" onpaste="return false;">
														</td>
													</tr>
												</table>
											</td>
										 </tr>
										 <tr>
											<td colspan="6">
												<table width="100%" align="center" id="andNotif" style="<?php /* if($androidswitch != 1) */ echo "display:none" ; ?>">
													<tr>	
														<td colspan="3" height="50" align="left"  valign="top"><h3>Android</h3></td>	
														<td colspan="3">&nbsp;</td>
													 </tr>
													 <tr>
														<td height="50" align="left"  valign="top" width="16%"><label>Package ID</label></td>
														<td align="center"  valign="top" width="3%">:</td>
														<td align="left"  height="40" width="32%"  valign="top">
															<input type="text" class="input" name="package" id="package" maxlength="90" value="<?php  if(isset($package) && $package != '' ) echo $package;   ?>">
														</td>
														<td height="50" align="left"  valign="top" width="16%"><label>Google Play URL</label></td>
														<td align="center"  valign="top" width="3%">:</td>
														<td align="left"  height="40"  valign="top" width="32%" >
															<input  type="url" class="input" id="androidurl" name="androidurl"  pattern="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?" value="<?php if(isset($androidurl) && $androidurl != '') echo $androidurl;  ?>" >
														</td>
													 </tr>
													 <tr>
														<td height="60" align="left"  valign="top"><label>GCM Key</label></td>
														<td align="center"  valign="top" >:</td>
														<td align="left"  height="40"  valign="top"><input type="text" class="input" id="gcmkey" name="gcmkey"  maxlength="250" value="<?php if(isset($gcm_key) && $gcm_key != '') echo $gcm_key; ?>" ></td>
													</tr>
												</table>
											</td>
										 </tr>
										  
										<tr><td height="20"></td></tr>
									</table>
									</td></tr>									
									<tr>
										<td colspan="6" align="center">
										<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
											<input type="Hidden" name="ios_pnid" id="ios_pnid" value="<?php if(isset($insertios_pnid) && !empty($insertios_pnid)) echo $insertios_pnid;?>" />
											<input type="Hidden" name="android_pnid" id="android_pnid" value="<?php if(isset($insertandroid_pnid) && !empty($insertandroid_pnid)) echo $insertandroid_pnid;?>" />
											<input type="submit" class="submit_button" name="submit" id="submit" value="Save" title="Save" alt="Save">
										<?php } else { ?>
										<input type="submit" class="submit_button" name="submit" id="submit" value="Add" title="Add" alt="Add">
										<?php } ?>
										<a href="GameList"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
									</td>
								</tr>	
								<tr><td height="35"></td></tr>				  
								</table>
							</form>
							</div>	
						  	
<?php commonFooter(); ?>
<script type="text/javascript">

$(".user_photo_pop_up").colorbox({title:true, maxWidth:"70%", maxHeight:"60%"});
$(".game_pop_up").colorbox({title:true , maxWidth:"70%", maxHeight:"60%"});

function assign_fileupload(){	
		$("tr #fileupload").each(function() {	
			var n = $(this).attr('clone');
			var file	=	$("#game_image"+n+"_upload").val();
			if(file!=''){
				$("#game_temp_file"+n).val(file);			
			}		
		});	
		return true;	
}
function changeNotification(type){
	if($("#iOff").is(":Checked") && $("#aOff").is(":Checked")){
		alert('iOS must be in On status');
		if(type == 1)
			$("#iOn").attr("Checked",true);
		else
			$("#aOn").attr("Checked",true);
	}else{
		if(type == 1){
			if($("#iOff").is(":Checked")){
				$("#iosNotif").hide();
				$("#bundle").val('');
				$("#itunesurl").val('');
				$("#ios_password").val('');
			}
			else
				$("#iosNotif").show();
		}else{
			if($("#aOff").is(":Checked")){
				$("#andNotif").hide();
				$("#package").val('');
				$("#androidurl").val('');
				$("#gcmkey").val('');
			}else
				$("#andNotif").show();
		}
			
	}
}
</script>
</html>