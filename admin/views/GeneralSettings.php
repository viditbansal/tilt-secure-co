<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
$code	=	$class =  $msg  = '';
$display = 'none';
$error = $msg = $gamerulesContent	=	$generalTerms	=	$tournamentRules	=	$cutoff_time = $friendinvitemail = $tour_endtime = $gameDevVirtualCoins = $defaultTilt = $defaultVirtualCoins = '';
$freecoins = $freecoinstimeperiod = $gamedeveloper_support = '';
$passwordFlag	=	1;
$fields		  =	" * ";
$where		  =	" 1 ";
$user_details = $adminLoginObj->getAdminDetails($fields,$where);
if(isset($user_details) && is_array($user_details) && count($user_details)>0){
	foreach($user_details as $key => $value){
		$user_name 	= 	$value->UserName;
		$email		=	$value->EmailAddress;
	}
}
$fields	=	' * ';
$where	= 	' id = 1 ';
$setting_details	=	$adminLoginObj->getDistance($fields,$where);
if(isset($setting_details) && is_array($setting_details) && count($setting_details)>0){
	foreach($setting_details as $key => $value){
		$distance 				= 	$value->Distance;
		$PlayableDistance 		= 	$value->PlayableDistance;
		$comission				=	$value->Commission;
		$conversion 			=	$value->ConversionValue;
		$gamerulesContent		=	$value->GameRules;
		$generalTerms			=	$value->TermsAndConditions;
		$tilt_fee				=	$value->TiltFee;
		$tournamentRules		=	$value->TournamentRules;
		$cutoff_time  			=   $value->CutoffTime;
		$tour_endtime       	=   $value->TournamentEndTime;
		$friendinvitemail   	=   $value->FriendInviteMail;
		$gameDevVirtualCoins	=	$value->VirtualCoinsDeveloper;
		$defaultTilt			=	$value->DefaultTilt;
		$defaultVirtualCoins	=	$value->DefaultVirtualCoins;
		$iTunesUrl				=   $value->ITunesUrl;
		$androidUrl				=   $value->AndroidUrl;
		$freecoins 				= 	$value->FreeCoins;
		$freecoinstimeperiod 	= 	$value->FreeCoinsTimePeriod;
		$gamedeveloper_support 	= 	$value->GameDeveloperSupport;
		$canStart 				= 	$value->DelayTime;
	}
}
if(isset($_POST['general_settings_submit']) && $_POST['general_settings_submit'] != '' )
{	
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	$condition      =   " id = 1 ";
	$updateString   =   " UserName  = '".$_POST['user_name']."',EmailAddress = '".$_POST['email']."'";
	$adminLoginObj->updateAdminDetails($updateString,$condition);

	$distance	=	$commission = $conversion = 0;
	$terms	=	$rules	=	'';
	if(isset($_POST['Distance'])	&&	$_POST['Distance'] != '')
		$distance	=	$_POST['Distance'];
	if(isset($_POST['PlayableDistance'])	&&	$_POST['PlayableDistance'] != '')
		$PlayableDistance	=	$_POST['PlayableDistance'];
	if(isset($_POST['Commission'])	&&	$_POST['Commission'] != '')	
		$comission	=	$_POST['Commission'];
	if(isset($_POST['ConversionValue'])	&&	$_POST['ConversionValue'] != '')	
		$conversion	=	$_POST['ConversionValue'];
	if(isset($_POST['tilt_fee'])	&&	$_POST['tilt_fee'] != '')	
		$tiltFee	=	$_POST['tilt_fee'];
	if(isset($_POST['terms_condition'])	&&	$_POST['terms_condition'] != '')	
		$terms	=	$_POST['terms_condition'];
	if(isset($_POST['game_rules'])	&&	$_POST['game_rules'] != '')	
		$rules	=	$_POST['game_rules'];
	if(isset($_POST['tournament_rules'])	&&	$_POST['tournament_rules'] != '')	
		$tournamentRules	=	$_POST['tournament_rules'];
	if(isset($_POST['cutoff_time'])	&&	$_POST['cutoff_time'] != '')	
		$cutoff_time	  =	$_POST['cutoff_time'];
	if(isset($_POST['invite_mail'])	&&	$_POST['invite_mail'] != '')	
		$friendinvitemail =	$_POST['invite_mail'];
	if(isset($_POST['tour_endtime'])	&&	$_POST['tour_endtime'] != '')
		$tour_endtime	  =	$_POST['tour_endtime'];
	
	if(isset($_POST['game_dev_virtual_coins'])	&&	$_POST['game_dev_virtual_coins'] != '')
		$gameDevVirtualCoins	  =	$_POST['game_dev_virtual_coins'];
	else
		$gameDevVirtualCoins	  =	0;
	if(isset($_POST['default_tilt'])	&&	$_POST['default_tilt'] != '')
		$defaultTilt	  =	$_POST['default_tilt'];
	else
		$defaultTilt	  =	0;
	if(isset($_POST['default_virtual_coins'])	&&	$_POST['default_virtual_coins'] != '')
		$defaultVirtualCoins	  =	$_POST['default_virtual_coins'];
	else
		$defaultVirtualCoins	  =	0;
	if(isset($_POST['itunesurl']))
		$iTunesUrl =	$_POST['itunesurl'];
	if(isset($_POST['androidurl']))
		$androidUrl =	$_POST['androidurl'];
	if(isset($_POST['freecoins']) && $_POST['freecoins'] != '')
		$freecoins  =	$_POST['freecoins'];
	if(isset($_POST['timeperiod']) && $_POST['timeperiod'] != '')
		$freecoinstimeperiod =	$_POST['timeperiod'];
	if(isset($_POST['canstart']) && $_POST['canstart'] != '')
		$canStart 	=	$_POST['canstart'];
	if(isset($_POST['developer_support']) && $_POST['developer_support'] != '')
		$gamedeveloper_support  =	$_POST['developer_support'];
	
	$updateString   =   ' Distance = "'.$distance.'",PlayableDistance = "'.$PlayableDistance.'",Commission = "'.$comission.'", ConversionValue = "'.$conversion.'",TiltFee="'.$tiltFee.'",TermsAndConditions = "'.$terms.'",GameRules = "'.$rules.'",TournamentRules="'.$tournamentRules.'",DateModified="'.date('Y-m-d H:i:s').'",CutoffTime="'.$cutoff_time.'",TournamentEndTime="'.$tour_endtime.'",VirtualCoinsDeveloper="'.$gameDevVirtualCoins.'",FriendInviteMail="'.$friendinvitemail.'",DefaultTilt="'.$defaultTilt.'",DefaultVirtualCoins="'.$defaultVirtualCoins.'",ITunesUrl = "'.$iTunesUrl.'",AndroidUrl = "'.$androidUrl.'"';
	$updateString  .=   ' ,FreeCoins = "'.$freecoins.'",FreeCoinsTimePeriod = "'.$freecoinstimeperiod.'", DelayTime = "'.$canStart.'", GameDeveloperSupport = "'.$gamedeveloper_support.'"';
	$condition      =   " id = 1 ";
	$adminLoginObj->updateDistanceDetails($updateString,$condition);
	if($passwordFlag)
		header('location:GeneralSettings?msg=1');
	else 
		header('location:GeneralSettings?msg=2');
	die();
}
if(isset($_GET['msg']) && $_GET['msg'] != '')
	if($_GET['msg']==1){
		$class          = "success_msg";
		$display        = "block";
		$msg 			= "General Settings updated successfully";
	}
	else if($_GET['msg']==2){
		$class          = "error_msg";
		$display        = "block";
		$msg 			= "Invalid Old Password";
	}
commonHead(); ?>
<body>
	<?php top_header(); ?>
	<div class="box-header"><h2><i class="fa fa-cog" ></i>General Settings</h2></div>
	<div class="clear">
	<form name="general_settings_form" id="general_settings_form" action="" method="post" onSubmit="return saveEditor();">
	<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">
	
		<tr><td align="center">
			<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">							 
				<tr><td align="center" colspan="3" valign="top" class="msg_height">
				<?php if($msg !='') { ?><div class="<?php echo $class; ?>" align="center"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $msg;?></span></div><?php  } ?>
				</td></tr>
				<tr>
				<td align="left" width="15%" valign="top">
					<label>User Name
					<span class="required_field">*</span></label>
					</label>
				</td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" >
				<input type="text" readonly="readonly" class="input" style="width:260px" name="user_name" id="user_name" value="<?php  if(isset($user_name) && $user_name) echo $user_name  ?>" />
				</td>
				</tr>
				<tr>
					<td align="left" valign="top">
						<label>Email
						<span class="required_field">*</span></label>
					</td>
					<td class="" valign="top" align="center">:</td>
					<td align="left"  height="60" valign="top">
					<input type="text" class="input" style="width:260px" name="email" id="email" value="<?php  if(isset($email) && $email) echo $email  ?>" />
					</td>
				</tr>
				<tr>
				<td align="left" valign="top">
					<label>Distance (meters)
					<span class="required_field">*</span></label>
				</td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" >
				<input type="text" class="input limitWidth" style="width:260px" name="Distance" onkeypress="return isNumberKey(event);" maxlength="6" id="Distance" value="<?php  if(isset($distance)) echo $distance;  ?>" />
				</td>
				</tr>
				<tr>
				<td align="left" valign="top">
					<label>Playable Distance (meters)
					<span class="required_field">*</span></label>
				</td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" >
				<input type="text" class="input limitWidth" style="width:260px" name="PlayableDistance" onkeypress="return isNumberKey(event);" maxlength="6" id="PlayableDistance" value="<?php  if(isset($PlayableDistance)) echo $PlayableDistance;  ?>" />
				</td>
				</tr>
				<tr>
				<td align="left" valign="top">
					<label>Commission (%)
					<span class="required_field">*</span></label>
				</td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" >
					<input type="text" class="input limitWidth" style="width:260px" name="Commission" onkeypress="return isNumberKey(event);"  maxlength="3" id="Commission" value="<?php  if(isset($comission)) echo $comission;  ?>" />
				</td>
				</tr>
				<tr>
				<td align="left" valign="top">
					<label>Conversion Value (coins)
					<span class="required_field">*</span></label>
				</td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" >
					<div class="con_value">
						<input type="text" class="input limitWidth" style="width:260px" name="ConversionValue" onkeypress="return settingValidation(event,this.id);"  maxlength="3" id="ConversionValue" value="<?php  if(isset($conversion)) echo $conversion;  ?>" /> 
						<span class="pos_value">( 1$ = X Coins)</span>
					</div>
				</td>
				</tr>
				<tr>
					<td align="left" valign="top"><label>TiLT Fee (TiLT$)</label></td>
					<td align="center" class="" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left" ><input type="text" class="input limitWidth" style="width:260px" name="tilt_fee" onkeypress="return isNumberKey(event);" maxlength="6" id="tilt_fee" value="<?php  if(isset($tilt_fee)) echo $tilt_fee;  ?>" /></td>
				</tr>
				<tr>
					<td align="left" valign="top">
						<label>Default End Time
						<span class="required_field">*</span></label>
					</td>
					<td align="center" class="" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left" >
						<div class="end_time">
							<input type="text" class="input limitWidth" style="width:50px" name="tour_endtime"  maxlength="2" id="tour_endtime" value="<?php  if(isset($tour_endtime) && $tour_endtime != '0') echo $tour_endtime; ?>" onkeypress="return isNumberKey(event);" />  
							<span class="pos_value">Hour(s)</span>
						</div>
					</td>
				</tr>
				<tr>
				<td align="left" valign="top"><label>Game Developer Virtual Coins (per month)</label></td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" ><input type="text" class="input limitWidth" style="width:260px" name="game_dev_virtual_coins"  maxlength="8" id="game_dev_virtual_coins" onkeypress="return isNumberKey(event);" value="<?php  if(isset($gameDevVirtualCoins) && $gameDevVirtualCoins != '0') echo $gameDevVirtualCoins;  ?>" /></td>
				</tr>
				<tr>
				<td align="left" valign="top">
					<label>Default TiLT$
					<span class="required_field">*</span></label>
				</td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" ><input type="text" class="input limitWidth" style="width:260px" name="default_tilt"  maxlength="8" id="default_tilt" onkeypress="return isNumberKey(event);" value="<?php  if(isset($defaultTilt) && $defaultTilt != '0') echo $defaultTilt;  ?>" /></td>
				</tr>
				<tr>
				<td align="left" valign="top">
					<label>Default Virtual Coins
					<span class="required_field">*</span></label>
				</td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" ><input type="text" class="input limitWidth" style="width:260px" name="default_virtual_coins"  maxlength="8" id="default_virtual_coins" onkeypress="return isNumberKey(event);" value="<?php  if(isset($defaultVirtualCoins) && $defaultVirtualCoins != '0') echo $defaultVirtualCoins;  ?>" /></td>
				</tr>
				<tr>
					<td align="left" valign="top"><label>iTunes URL</label></td>
					<td align="center" class="" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left">
						<input type="url" class="input" id="itunesurl" name="itunesurl" style="width:260px" pattern="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?" value="<?php if(isset($iTunesUrl) && $iTunesUrl != '')  echo $iTunesUrl; ?>">
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Google Play URL</label></td>
					<td align="center" class="" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left">
						<input type="url" class="input" id="androidurl"  style="width:260px" name="androidurl" pattern="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?" value="<?php if(isset($androidUrl) && $androidUrl != '')  echo $androidUrl; ?>">
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>				
				<tr>
					<td align="left" valign="top"><label>User Free Coins</label></td>
					<td align="center" class="" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left" ><input type="text" class="input" style="width:260px" name="freecoins"  id="freecoins" value="<?php if(isset($freecoins) && $freecoins != 0) echo $freecoins;?>" onkeypress="return isNumberKey(event);" maxlength="6" /></td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Time Period To Receive User Free Coins</label></td>
					<td align="center" class="" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left">
						<input type="text" class="input" id="timeperiod" style="width:260px" name="timeperiod"  value="<?php if(isset($freecoinstimeperiod) && $freecoinstimeperiod != '00:00:00') echo $freecoinstimeperiod;?>">
					</td>
				</tr>
				<tr>
					<td align="left" valign="top">
						<label>Can Start
						<span class="required_field">*</span></label>
					</td>
					<td align="center" class="" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left">
						<input type="text" class="input" id="canstart" style="width:260px" name="canstart" onkeypress="return timeField(event);" value="<?php if(isset($canStart) && $canStart != '00:00:00') echo $canStart;?>">
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
				<td align="left" valign="top"><label>Terms and Conditions</label></td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" >
					<textarea class="textarea-full editor_tournamentRules" id="terms_condition" name="terms_condition"><?php echo $generalTerms; ?></textarea>
				</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
				<td align="left" valign="top"><label>Game Rules</label></td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" >
					<textarea class="textarea-full editor_tournamentRules" id="game_rules"  name="game_rules"><?php echo $gamerulesContent; ?></textarea>
				</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Tournament Rules</label></td>
					<td align="center" class="" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left" >
						<textarea class="textarea-full editor_tournamentRules" id="tournament_rules" name="tournament_rules"><?php echo $tournamentRules; ?></textarea>
					</td>
				</tr>		
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Friend Invite Mail </label></td>
					<td align="center" class="" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left" >
						<textarea class="textarea-full editor" id="invite_mail" name="invite_mail"><?php echo $friendinvitemail; ?></textarea>
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Game Developer Support Content </label></td>
					<td align="center" class="" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left" >
						<textarea class="textarea-full editor" id="developer_support" name="developer_support"><?php echo $gamedeveloper_support; ?></textarea>
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
				<td colspan="2"></td>
				<td align="left">
				<input type="submit" class="submit_button" name="general_settings_submit" id="general_settings_submit" value="Submit" title="Submit" alt="Submit" />
				<a href="UserList?cs=1" class="submit_button" name="Cancel" id="Cancel" value="Cancel" title="Cancel" alt="Cancel" tabindex="NaN">Cancel</a>
				</td>
				</tr>
			</table>
		</td></tr>
		<tr><td height="35"></td></tr>
	</table>
	</form>	
	</div>
<?php commonFooter(); ?>
<script>
	tinymce.init({
	height 	: "250",
	width	: "400",
	mode : "specific_textareas",
	selector: "textarea.editor_tournamentRules",statusbar: false, menubar:false,
	plugins	: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
				],
	toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent"
	});
	function saveEditor(){
		tinyMCE.triggerSave();
	}
	
	tinymce.init({
	height 	: "250",
	width	: "400",
	mode : "specific_textareas",
	selector: "textarea.editor", statusbar: false, menubar:false,
	plugins	: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
				],
	toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
	});
	function saveEditor(){
		tinyMCE.triggerSave();
	}

$(function(){
	$(document).ready(function(){		
		$('#cutoff_time,#timeperiod').timepicker({showSecond: true,timeFormat: 'HH:mm:ss'});
	});
});
</script>
</html>