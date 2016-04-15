<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/UserController.php');
require_once('controllers/AdminController.php');
require_once("includes/phmagick.php");
admin_login_check();
commonHead();

$adminLoginObj  =   new AdminController();
$userObj   		=   new UserController();

require_once('controllers/GameController.php');
$gameObj   =   new GameController();

$field_focus	=	'username';
$class			=	$ExistCondition		=	$location		=	$photoUpdateString	=	$description  =  $minplayers = $bundle = $istatus = $astatus = '';
$email_exists 	= 	$facebookid_exist	=	$linkedid_exist	=	$twitter_exist		=	$googleplus_exist	=	$userName_exists	=	0;
$notificationFlag	 = 0;
$eliminationArr = $highscoreruleArr	= $gameRulesResult = $image_array  = $gamefilesResult = $image_patharray = array();
$certStatus	=	'Not Uploaded';
$certClass 	=	'cert_inactive';
if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
	$gameId			 =	$_GET['viewId'];
	$condition       = " id = ".$_GET['viewId']." ";
	$field			 = " * ";
	$gameDetailsResult  = $gameObj->getSingleGameDetails($field,$condition);
	if(isset($gameDetailsResult) && is_array($gameDetailsResult) && count($gameDetailsResult) > 0){
		$gamename 	 =	$gameDetailsResult[0]->Name;
		$email       =	$gameDetailsResult[0]->Email;
		$tiltkey   	 =	$gameDetailsResult[0]->TiltKey;
		$itunesurl   =	$gameDetailsResult[0]->ITunesUrl;
		$androidurl  =	$gameDetailsResult[0]->AndroidUrl;
		$description =	$gameDetailsResult[0]->Description;
		$bundle      = $gameDetailsResult[0]->Bundle;
		$package     = $gameDetailsResult[0]->Package;
		$playTime    = $gameDetailsResult[0]->PlayTime;
		$istatus 	 =	$gameDetailsResult[0]->IosStatus;
		$astatus 	 =	$gameDetailsResult[0]->AndroidStatus;
		$image_path = ADMIN_IMAGE_PATH.'add_game.jpg';
		$original_image_path = ADMIN_IMAGE_PATH.'add_game.jpg';
		if(isset($gameDetailsResult[0]->Photo) && $gameDetailsResult[0]->Photo != ''){
			$user_image_name = $gameDetailsResult[0]->Photo;
			$image_path_rel 	= GAMES_THUMB_IMAGE_PATH_REL.$user_image_name;
			if(SERVER){
				if(image_exists(1,$user_image_name)){
					$image_path 	= GAMES_THUMB_IMAGE_PATH.$user_image_name;
					$original_image_path 	= GAMES_IMAGE_PATH.$user_image_name;
				}
				
			}
			else if(file_exists($image_path_rel)){
					$image_path 	= GAMES_THUMB_IMAGE_PATH.$user_image_name;
					$original_image_path 	= GAMES_IMAGE_PATH.$user_image_name;
			}
		}
	}
	
	$rulecondition       = " fkGamesId = ".$_GET['viewId']." and Status  = 1 ";
	$gameRulesResult  = $gameObj->selectGameRules($rulecondition);
	if(is_array($gameRulesResult) && isset($gameRulesResult) && count($gameRulesResult)>0){
		foreach($gameRulesResult as $val)
		{
			$eliminationArr[]	=	$val->EliminationRules;
			$highscoreruleArr[]	=	$val->HighscoreRules;
		}
	}
	
	//pushnotification
	$rulecondition       = " fkGamesId = ".$_GET['viewId']." and Status = 1";
	$gamepushnotificaion = $gameObj->selectGamePushNotification(" * " ,$rulecondition);
	
	if(is_array($gamepushnotificaion) && isset($gamepushnotificaion) && count($gamepushnotificaion)>0){	
		foreach($gamepushnotificaion as $val){
			if($val->Platform == 1){
				$insertios_pnid  = $val->id;
				$platform_ios    = $val->Platform;
				$iosarn_key      = $val->ARNKey;
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
				$platform_android   = $val->Platform;
				$arn_key            = $val->ARNKey;
				$gcm_key            = $val->GCMkey;
				if(isset($val->Certificatepassword) && $val->Certificatepassword != ''){
					$android_password	=	1;
				}
			}
		}
	}
	
	$filecondition       = " fkGamesId = ".$_GET['viewId']." and Status = 1 and Image != ''";
	$gamefilesResult  = $gameObj->selectGameFiles($filecondition);
	if(is_array($gamefilesResult) && isset($gamefilesResult) && count($gamefilesResult)>0){
		foreach($gamefilesResult as $imageval)
		{
			 if(SERVER){
				if(image_exists(6,$_GET['viewId'].'/'.$imageval->Image)) {
					$image_array[]	=	$imageval->Image;
					$image_patharray[]	=	GAMES_IMAGE_PATH.$_GET['viewId'].'/'.$imageval->Image;
					$imageid_array[]	=	$imageval->id;
				}
			}else{
				if(file_exists(GAMES_IMAGE_PATH_REL.$_GET['viewId'].'/'.$imageval->Image)){	
					$image_array[]	=	$imageval->Image;
					$image_patharray[]	=	GAMES_IMAGE_PATH.$_GET['viewId'].'/'.$imageval->Image;
					$imageid_array[]	=	$imageval->id;
				}
			}
		}
	}
	
}
?>
<body onload="return fieldfocus('<?php echo $field_focus; ?>');">
	<?php if(!isset($_GET['statistics']))	top_header(); ?>
			<div class="box-header">
				<h2><i class="fa fa-search"></i>View Game</h2>
			</div>
			<div class="clear">
							<form name="add_game_form" id="add_game_form" action="" method="post">
							<input type="Hidden" name="game_id" id="game_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
						  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
									<tr><td align="center">
									<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">    
										<tr><td align="center" valign="top" class="msg_height" colspan="6">
											<div class="<?php echo $class;  ?> w50">
												<span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span>
											</div>
										</td></tr>
										<tr>
											<td  height="50" width="15%" align="left"  valign="top"><label>Game Name&nbsp;</label></td>
											<td align="center"  valign="top" width="3%">:</td>
											<td align="left"  height="40"  valign="top" width="32%">
												<?php if(isset($gamename) && $gamename != '') echo $gamename; else echo ' - ';  ?>
											</td>
											<td height="50" align="left"  valign="top" width="15%"><label>Game Key</label></td>
											<td align="center"  valign="top" width="3%">:</td>
											<td align="left"  height="40"  valign="top" width="32%">
												<?php  if(isset($tiltkey) && $tiltkey != '' ) echo $tiltkey; else echo ' - ';   ?>
											</td>
										</tr>
										<tr>
											<td height="60"  align="left"  valign="top"  valign="top" width="10%"><label>App Icon</label></td>
											<td  align="center" valign="top">:</td>
											<td align="left"  height="60" valign="top" colspan="">
												<div class="upload fleft">
													<div style="clear: both;float: left">
													<span class="error" for="empty_user_photo" generated="true" style="display: none">Game Image is required</span>
													<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
														<div id="game_photo_img" style="padding-bottom:25px;">
															<?php  if(isset($image_path) && $image_path != ''){  ?>
																<a href="<?php if(isset($original_image_path) && $original_image_path != '') { echo $original_image_path; ?>" class="user_photo_pop_up"<?php } else { ?>Javascript:void(0);<?php } ?>" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $image_path;  ?>" width="75" height="75" alt="Image"/></a>
															<?php  }  ?>
														</div>
													</div>
												</div>												
											<?php  if(isset($_POST['game_photo_upload']) && $_POST['game_photo_upload'] != ''){  ?><input type="Hidden" name="game_photo_upload" id="game_photo_upload" value="<?php  echo $_POST['game_photo_upload'];  ?>"><?php  }  ?>
											<input type="Hidden" name="empty_user_photo" id="empty_user_photo" value="<?php  if(isset($user_image_name) && $user_image_name != '') { echo $user_image_name; }  ?>" />
											<input type="Hidden" name="name_user_photo" id="name_user_photo" value="<?php  if(isset($user_image_name) && $user_image_name != '') { echo $user_image_name; }  ?>" />
											</div>
											</td>
											<td height="50" align="left"  valign="top"><label>Play Time</label></td>
											<td align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
											<?php  if(isset($playTime) && !empty($playTime)) echo $playTime; else echo ' - ';   ?>
											</td>
										</tr>
										<tr>
											<td height="50" align="left" valign="top"><label>Description&nbsp;</label></td>
											<td align="center"  valign="top" >:</td>
											<td align="left"  height="40"  valign="top" colspan="4">
												<div style="width: 700px;word-wrap: break-word">
													<?php if(isset($description) && $description != '' ) echo $description; else echo ' - ';  ?>
												</div>
													
											</td>	
										</tr>
										<tr><td height="20"></td></tr>
										<tr>											
											<td height="50" align="left"  valign="top"><label>Rule</label></td>
											<td align="center"  valign="top">:</td>
											<td align="left"   valign="top" colspan="4">												
											<table width="70%" border="0" cellpadding="0" class="rule-table" cellspacing="0">
												<tr  height="40" > 
													<td style='color:#494949;' align="center"><b>Elimination Rule</b></td>
										
													<td style='color:#494949;' align="center"><b>HighScore Rule</b></td>													
												</tr>
											<?php if(is_array($gameRulesResult) && count($gameRulesResult) > 0){
													for($index = 0;$index < count($gameRulesResult);$index++){?>
												<tr  height="30">													
													<td width="50%" valign="middle">
														<div style="width: 300px;float:left;word-wrap:break-word;">
															<?php if(is_array($eliminationArr) && isset($eliminationArr[$index]) && $eliminationArr[$index] != '') echo $eliminationArr[$index]; else echo "-";?>&nbsp;
														</div>
													</td>
													<td width="50%" valign="middle">
														<div style="width: 300px;float:left;word-wrap:break-word;">
															<?php if(is_array($highscoreruleArr) && isset($highscoreruleArr[$index]) && $highscoreruleArr[$index] != '')  echo $highscoreruleArr[$index]; else echo "-"; ?>&nbsp;
														</div>
													</td>																					
												</tr>
													<?php }
														} else {?> 
												<tr  height="30" class="clone" clone="1">													
													<td width="28%" valign="middle" style="padding-left:10%"> - </td>
													<td width="28%" valign="middle" style="padding-left:10%"> - </td>																					
												 </tr>
												<?php }?>
												
											</table>
											</td>
										</tr>
										<tr><td height="20"></td></tr>
										<tr>	
										<td height="50" align="left"  valign="top" width="15%"><label>Image for User Portal </label></td>	
										<td  align="center" valign="top" width="3%">:</td>								
										<td colspan="4" valign="top">
										
										<?php if(is_array($image_array) && isset($image_array) && count($image_array)>0){ ?>
										<div style="max-width: 963px; width: 100%;">
										<div class="scroll_content">
										<div id="game_list" class="game_list">
										 <div class="game-list"> <ul  class="clone" clone="" id="fileupload">	
									   <?php	$i = 0 ;
											for($index = 0;$index < count($image_array);$index++){
												
												 if(isset($image_array[$index]) && $image_array[$index] != ''){
													$hideadd = "style='display:none;'";
													if($index == count($image_array)-1)
														$hideadd = '';
													?>		
													<li>										
														<div id="game_image<?php echo $index ;?>_img" >															
															<a href="<?php if(isset($image_patharray[$index]) && $image_patharray[$index] != '') { echo $image_patharray[$index]; ?>" class="game_pop_up"<?php } else { ?>Javascript:void(0);<?php } ?>" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $image_patharray[$index];  ?>" width="75" height="75" alt="Image"/></a>
														</div>																									
												 </li>	 
											<?php   } }?>
											</ul>	
												</div> 
												</div>
											</div>
											</div>
											<?php  }  else echo  "-" ;?>
										</td>
									</tr>
									<tr><td height="20" colspan="6"></td></tr>
									<?php if($astatus == 1 || $istatus == 1) { ?>
									<tr>	
										<td height="50" align="left" colspan="3" valign="top"><h2>Push Notification Settings</h2></td>			
										<td colspan="3">&nbsp;</td>
									 </tr>
									 <?php if($istatus == 1) { ?>
									 <tr>	
										<td colspan="3" height="50" align="left"  valign="top"><h3>iOS</h3></td>	
										<td colspan="3">&nbsp;</td>
									 </tr>
									<tr>
										<td  height="50" align="left"  valign="top"><label>Bundle ID&nbsp;</label></td>
											<td align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<?php if(isset($bundle) && $bundle != '') echo $bundle; else echo ' - ';  ?>
											</td>
											<td height="50" align="left"  valign="top"><label>iTunes URL</label></td>
											<td align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<div style="width: 240px;float: left;word-wrap: break-word;padding-right:10px;">
												<?php if(isset($itunesurl) && $itunesurl != '') echo $itunesurl; else echo ' - ';  ?>
												</div>
											</td>
									</tr>
									<tr>																					
										<td height="50" align="left"  valign="top"><label>Apple push certificate&nbsp;(.p12)</label></td>
										<td align="center"  valign="top">:</td>
										<td align="left"  height="40"  valign="top">											
											<p class="certificate_name"><?php if(isset($certificateName) && !empty($certificateName)) echo '<span class="certificate">'.$certificateName.'</span>&nbsp;&nbsp;&nbsp;<a target="_blank" href="Download?gameId='.$_GET['viewId'].'&fileName='.urlencode($certificateName).'" ><i class="fa fa-download fa-2x" style="padding-top:6px;"></i></a>'; ?></p>
										</td>
										
										<td height="50" align="left"  valign="top"><label>Active Certificate</label></td>
										<td align="center"  valign="top">:</td>
										<td align="left"  height="40"  valign="top">
											<span id="status_game_certificate" class="<?php if(isset($certClass) && $certClass !='') echo $certClass; ?>"><em class="fa fa-check"></em><?php if(isset($certStatus) && $certStatus !='') echo $certStatus; else echo '-'; ?></span>
										</td>
									</tr>
									<?php } 
									if($astatus == 1) { ?>
									  <tr>	
										<td colspan="3" height="50" align="left"  valign="top"><h3>Android</h3></td>	
										<td colspan="3">&nbsp;</td>
									 </tr>
									 <tr>
										<td  height="50" align="left"  valign="top"><label>Package ID&nbsp;</label></td>
											<td align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<?php if(isset($package) && $package != '') echo $package; else echo ' - ';  ?>
											</td>
											<td height="50" align="left"  valign="top"><label>Google Play URL</label></td>
											<td align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<div style="width: 240px;float: left;word-wrap: break-word;padding-right:10px;"><?php if(isset($androidurl) && $androidurl != '') echo $androidurl; else echo ' - ';  ?></div>
											</td>
									 </tr>
									 <tr>
										<td height="60" align="left"  valign="top"><label>GCM Key</label></td>
										<td align="center"  valign="top" >:</td>
										<td align="left"  height="40"  valign="top">
											<div style="width: 290px;float: left;word-wrap: break-word">
												<?php if(isset($gcm_key) && $gcm_key != '') echo $gcm_key; else echo '-'; ?>
											</div>
										</td>
									</tr>
									<?php } ?>
									<?php } ?>						
									</table>									
									</td></tr>
									<tr><td height="10"></td></tr>
									<tr>
										<td colspan="6" align="center">
										<?php if(isset($_GET['viewId']) && $_GET['viewId'] != '' && !isset($_GET['statistics'])){ ?>
											<a href="GameManage?<?php echo 'editId='.$_GET['viewId'];?>"  class="submit_button" name="Edit" id="Edit"  value="Edit" title="Edit" alt="Edit">Edit </a>
										<?php } ?>
										<?php if(!isset($_GET['statistics'])){ ?>
											<a href="GameList"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
										<?php } else { ?>
											<a href="GameList?statistics=1"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
										<?php } ?>
									</td>
								</tr>	
									<tr><td height="35"></td></tr>	
												  
								</table>
							</form>
					</div>
						  	
<?php commonFooter(); ?>
<script type="text/javascript">

$(".user_photo_pop_up").colorbox({title:true, maxWidth:"70%", maxHeight:"60%"});
$(".game_pop_up").colorbox({
	title:true,
	maxWidth:"70%",
	maxHeight:"60%"
});
$(function(){

   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '580';
   var maxWidth  = '1100';
   if(bodyHeight<maxHeight) {   	
	setHeight = bodyHeight;
   } else {
		setHeight = maxHeight;
   }
   if(bodyWidth>maxWidth) {
		setWidth = bodyWidth;
   } else {
		setWidth = maxWidth;
   }
   parent.$.colorbox.resize({
		innerWidth :setWidth,
		innerHeight:setHeight
	});
});
</script>
</html>