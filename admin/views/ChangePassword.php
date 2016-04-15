<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
$class =  $msg  = '';
$display = 'none';
if(isset($_POST['change_password_submit']) && $_POST['change_password_submit'] == 'Submit')
{
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
    $md5Pass        =  $_POST['old_password'];
	$condition      =   " id  = '1' AND Password = '{$md5Pass}'";
    $result         =   $adminLoginObj->checkAdminLogin($condition);	
    if($result)
    {
        $updateString   =   " password  = '".$_POST['new_password']."'";
        $condition      =   " id = 1 ";
        $adminLoginObj->updateAdminDetails($updateString,$condition);
		$msg            = "Password updated successfully";
		$class          = "success_msg";
		$display        = "block";
	}
	else{
		$class    = "error_msg";
		$display  = "block";
		$msg      = "Invalid Old Password";
	}

}
commonHead(); ?>
<body>
	<?php top_header(); ?>
	<div class="box-header"><h2><i class="fa fa-key" ></i>Change Password</h2></div>
	<div class="clear">
	  <form name="change_password_form" id="change_password_form" action="" method="post">
	  <table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
			<tr><td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" width="41%">	
			
			<tr>
				<td align="center" valign="top" class="msg_height" colspan="3"><div class="<?php  echo $class;  ?>"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($msg) && $msg != '') echo $msg;  ?></span></div></td>
			</tr>
			<tr>
				<td align="left" valign="top" width="12%" >
					<label>Old Password
					<span class="required_field">*</span></label>
				</td>
				<td align="center" valign="top" width="5%">:</td>
				<td align="left" width="" height="60" valign="top">											
					<input type="Password" maxlength="20" class="input" name="old_password" id="old_password"  value="" autocomplete="off" ondrop="return false;" onpaste="return false;">
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" >
					<label>New Password
					<span class="required_field">*</span></label>
				</td>
				<td  align="center" valign="top">:</td>
				<td align="left"  height="60" valign="top">
					<input type="Password" maxlength="20" class="input" name="new_password" id="new_password"  value="" autocomplete="off" ondrop="return false;" onpaste="return false;">	
				</td>
			</tr>
			<tr>
				<td align="left"  valign="top">
					<label>Confirm Password
					<span class="required_field">*</span></label>
				</td>
				<td align="center" valign="top">:</td>
				<td align="left"  height="60" valign="top">
					<input type="Password" class="input" maxlength="20" id="confirm_password" name="confirm_password"  value="" autocomplete="off" ondrop="return false;" onpaste="return false;">
				</td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td align="left"><input type="submit" class="submit_button" name="change_password_submit" id="change_password_submit" value="Submit" title="Submit" alt="Submit">										
				<a href="UserList?cs=1" class="submit_button" name="Cancel" id="Cancel" value="Cancel" title="Cancel" alt="Cancel" tabindex="NaN">Cancel</a>										
				</td>
			</tr>	
			<tr><td height="10"></td></tr>	
		</table>
		</table></td></tr>
	  </form>
	  </div>	
						  	
<?php commonFooter(); ?>
</html>