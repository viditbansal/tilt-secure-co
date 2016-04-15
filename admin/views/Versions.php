<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/ServiceController.php');
$appversionObj  =   new ServiceController();
$class 			=  	$msg  = '';
$display 		= 	'none';
$error 			= 	$msg = '';
$fields		  	=	" * ";
$where		  	=	" 1 ";
global $platform_array,$app_type_array;
$device_name_array = array_slice($platform_array, 1, 2,true);
$success_msg	= ''; // Hides success msg on password change;

if(isset($_GET['delete']) && $_GET['delete'] != '' ) {
	$delete 	= $appversionObj->deleteAppversion($_GET['delete']);
	header("location:Versions?msg=3");
	die();
}

// update App Status
if(isset($_POST['status_update'],$_POST['edit_id']) && $_POST['status_update'] != '') {
	$status_insertid 	= $appversionObj->updateAppversion(escapeSpecialCharacters($_POST),$_POST['edit_id']);
	header("location:Versions?msg=2");
	die();
}
if(isset($_POST['status_save'])){
	
	$post = escapeSpecialCharacters($_POST);
	$app_exists = $appversionObj->checkAppVersionExists($post['device_type'],$post['status'],$post['game_key']);
	if(is_array($app_exists) && count($app_exists) > 0 ){
		$status_insertid = $appversionObj->updateAppversion($post,$app_exists[0]->id);
		header("location:Versions?msg=2");
	}
	else{
		$status_insertid = $appversionObj->addAppversion($post);
		header("location:Versions?msg=1");
	}
	die();
}

$app_array = $appversionObj->getAppversionList();

if(isset($_GET['msg']) && $_GET['msg'] == 1){
	$msg 		= 	"App Detail added successfully";
	$display	=	"block";
	$class 		= 	"success_msg w50";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 2){
	$msg 		= 	"App Detail updated successfully";
	$display	=	"block";
	$class 		= 	"success_msg w50";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 3){
	$msg 		= 	"App Detail deleted successfully";
	$display	=	"block";
	$class 		= 	"error_msg w50";
}
	
$JS = array('functions.js'); 
commonHead(); 
?>
<body>
<?php top_header(); ?>						 
	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i>App Version Settings</h2>
	 	<span class="fright"><a href="#ins" onclick="clear_app('add_appstatus_form'); Show('add_newapp');" title="Add App Version"><i class="fa fa-plus-circle"></i> Add App Version</a></span>
	</div>
	<div class="clear">
           <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
			<tr>
				<td valign="top" class="msg_height50" align="center"><?php if(isset($msg) && $msg != '') { ?>
					<div class="row" id="alert_center">
						<div align="center" class="alert <?php  echo $class;  ?> alert-dismissable   col-xs-12 col-sm-5  col-lg-4"><span><i class="fa fa-lg"></i> <?php echo $msg; ?></span></div>
					</div>	
				<?php } ?>
				</td>
			</tr>
			<tr><td>
					<form action="" method="post" id="appstatus_form" name="appstatus_form" class="l_form">
						<table class="user_table user_actions" width="100%">
						<tr>
											<th width="5%" style="text-align:center">#</th>
											<th width="8%">Device Name</th>
											<th width="10%">App Type</th>
											<th width="10%" align="center">Version</th>
											<th width="10%" class="center">Build</th>									
											<th width="10%" class="center">Game Key</th>									
											<th width="10%" class="center">Action</th>
										</tr>
										<?php if(isset($app_array) && is_array($app_array) && count($app_array) > 0 ) { 
										$i = 0;
										foreach($app_array as $key=>$value){	
											$i++;
											$device_name	= (isset($device_name_array[$value->device_type]))?$device_name_array[$value->device_type]:'';
											$device_status	= (isset($app_type_array[$value->app_type]))?$app_type_array[$value->app_type]:'';
											$version		= (trim($value->version)!='')?trim($value->version):'-';
											$build			= (trim($value->build)!='')?trim($value->build):'-';
											$game_key		= (trim($value->app_game_key)!='')?trim($value->app_game_key):'0';
											$class = ($value->app_type == 1)?"Live":"Beta";
											if(isset($_GET['edit']) && $_GET['edit'] == $value->id) { ?>
										<tr>
											<td style="text-align:center"><?php echo $i;?></td>
											<td>
												<?php echo $device_name;?>
												<input type="hidden" name="edit_id" id="edit_id" value="<?php echo  $_GET['edit'] ;?>" >
											</td>
											<td>
												<select name="status" id="status_edit" title="Select Status" class="input">
													<option value="">--Select--</option>
														<?php if(is_array($app_type_array) && count($app_type_array) > 0) {
															foreach($app_type_array as $s_key => $s_value) {
														?>
													<option value="<?php echo $s_key;?>" <?php if($s_key == $value->app_type) echo "selected";?>><?php echo $s_value;?></option>
													 <?php } } ?>
												</select>
												<div id="status_edit_msg_container" style="display:none;"><div id="status_edit_msg" class="error"></div></div>
											</td>
											<td><input type="text" title="Enter Version" onpaste="return false;" class="input"  maxlength="10" onkeypress="return isNumberKey_Enter(event);" style="width:80px;" name="device_version" id="device_version_edit" value="<?php echo trim(stripslashes($value->version));?>">
												<div id="device_version_edit_msg_container" style="display:none;"><div id="device_version_edit_msg" class="error"></div></div>
											</td>
											<td><input type="text" title="Enter Build" class="input" onpaste="return false;"  maxlength="10" onkeypress="return isNumberKey_Enter(event);" style="width:80px;" name="device_build" id="device_build_edit" value="<?php echo trim(stripslashes($value->build));?>">
												<div id="device_build_edit_msg_container" style="display:none;"><div id="device_build_edit_msg" class="error"></div></div>
											</td>	
											<td><input type="text" title="Enter Game Key" class="input" style="width:80px;" name="game_key"  maxlength="200" id="game_key_edit" value="<?php echo trim(stripslashes($value->app_game_key));?>">
												<div id="game_key_edit_msg_container" style="display:none;"><div id="game_key_edit_msg" class="error"></div></div>
											</td>
											<td>
												<span class="butbor"><a id="ins" name="ins"></a><input class="submit_button" type="button"  onclick="location.href='Versions'" value="Cancel" title="Cancel" /></span>
												<span class="butbor" style="margin-left: 5px;"><input onclick="return validateAppStatus('_edit')" type="submit" id="status_update" name="status_update" value="Save" title="Save" class="submit_button" /></span>
											</td>
										</tr>
										<?php } else { ?> 
										<tr>
											<td style="text-align:center"><?php echo $i;?></td>
											<td><?php echo $device_name; ?></td>
											<td><span class="<?php echo $class; ?>"><?php echo $device_status; ?></span>&nbsp;</td>
											<td><?php echo $version; ?></td>
											<td><?php echo $build; ?></td>
											<td><?php echo $game_key; ?></td>
											<td>
												<a href="Versions?edit=<?php echo $value->id; ?>#e<?php echo $value->id; ?>" title="Edit" ><i class="fa fa-edit fa-lg"></i></a>&nbsp;&nbsp;&nbsp;
												<a href="Versions?delete=<?php echo $value->id; ?>#d<?php echo $value->id; ?>" title="Delete" onclick="return confirm('Are you sure to delete');"class="deleteUser">&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-trash-o fa-lg"></i></a>
											</td>
										</tr>
										<?php } } } else { ?>	
											<tr>
												<td colspan="16" align="center" style="color:red;">No App detail(s) Found</td>
											</tr>
										<?php } ?>
										<input type="hidden" id="errorFlag" name="errorFlag" value="0">
									</table>
								</form>
									</td>
								</tr>
								<tr><td height="20"></td></tr>
								<tr>
								<td>
								<form action="Versions"  method="POST" id="add_appstatus_form" name="add_appstatus_form" onsubmit="return validateAppStatus('');">
								<table id="add_newapp" style="display:none;overflow:hidden;"  cellpadding="0" cellspacing="0" border="0" width="100%" align="center" class="headertable user_table user_actions table">
									<tr>	
										<td width="5%"></td>
										<td width="8%" >
											<select name="device_type" id="device_type" title="Select Device" class="form-control">
												<option value="">--Select--</option>
													<?php if(is_array($device_name_array) && count($device_name_array) > 0) {
														foreach($device_name_array as $d_key => $d_value) {
													?>
												<option value="<?php echo $d_key;?>"><?php echo $d_value;?></option>
												 <?php } }?>
											</select>
											<div id="device_type_msg_container" style="display:none;"><div id="device_type_msg" class="error" ></div></div>
										</td>
										<td width="10%" >
											<select name="status" id="status" title="Select Status" class="form-control">
												<option value="">--Select--</option>
													<?php if(is_array($app_type_array) && count($app_type_array) > 0) {
														foreach($app_type_array as $s_key => $s_value) {
													?>
												<option value="<?php echo $s_key;?>"><?php echo $s_value;?></option>
													<?php } } ?>
											</select>
											<div id="status_msg_container" style="display:none;"><div id="status_msg" class="error"></div></div>
										</td>
										<td width="10%" >
											<input type="text" class="input" onpaste="return false;" title="Enter Version" maxlength="10" onkeypress="return isNumberKey_Enter(event);" id="device_version" name="device_version" value="" /><br />	 
											<div id="device_version_msg_container" style="display:none;"><div id="device_version_msg" class="error"></div></div>
										</td>
										<td width="10%" >
											<input type="text" class="input" onpaste="return false;" title="Enter Build"  maxlength="10" onkeypress="return isNumberKey_Enter(event);" id="device_build" name="device_build" value=""  /><br />	 
											<div id="device_build_msg_container" style="display:none;"><div id="device_build_msg" class="error"></div></div>
										</td>
										<td width="10%" >
											<input type="text" class="input" title="Enter Game key"  id="game_key" name="game_key" value=""  maxlength="200" /><br />	 
											<div id="game_key_msg_container" style="display:none;"><div id="game_key_msg" class="error"></div></div>
										</td>
										<td width="10%">
											<span class="borbut"><a id="ins" name="ins"></a><input class="submit_button" type="button" onclick="Cancel('add_newapp')" value="Cancel" title="Cancel" /></span>
											<span class="borbut" style="margin-left: 5px;"><input type="submit" id="status_save" name="status_save" value="Save" title="Save" class="submit_button" /></span>
										</td>
									</tr>
									<input type="hidden" id="errorFlag" name="errorFlag" value="0">
								</table>
							</form>
							</td>
						</tr>
			</td></tr>
           </table>
       </div>
<?php commonFooter(); ?>
</html>
