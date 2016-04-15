<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/DeveloperController.php');
require_once("includes/phmagick.php");
require_once('controllers/AdminController.php');
$adminLoginObj	=	new AdminController();	
$gameDevObj		=   new DeveloperController();
commonHead();
$error_msg	=	$virtualcoins = '';
$photoUpdateString	=	'';
if(isset($_POST['submit'])	&&	$_POST['submit']!="")
{
	$fields	= ' VirtualCoinsDeveloper ';
	$where	= ' id = 1 ';
	$setting_details	=	$adminLoginObj->getSettingDetails($fields,$where);
	if(isset($setting_details) && is_array($setting_details) && count($setting_details) > 0){
		$virtualcoins = $setting_details[0]->VirtualCoinsDeveloper;
	}
	
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	if(isset($_POST['email'])	&&	$_POST['email']!='') {
		$ExistCondition 	= "  ( Email = '".trim($_POST['email'])."' ) AND Status !=3 ";
		$company 			=	($_POST['company']);
		$contactName		=	($_POST['contact_name']);
		$email      		=	($_POST['email']);
		// To check the already exist condition for the user email address and there fb id
		$alreadyExist    	=	$gameDevObj->checkGameDeveloperLogin($ExistCondition);	
		$email_exists		=	$user_exists 			=	0;
		if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0)	{
			if(strcasecmp($alreadyExist[0]->Email, $_POST['email']) == 0)
				$email_exists 			=	1;
		}

		if($email_exists != '1'){
			$_POST['actual_password'] 	=	$_POST['password'];
			$_POST['password']			=	sha1(trim($_POST['password']));
			$insert_id   				=	$gameDevObj->regDeveloperDetails($_POST);
			
			if(isset($insert_id)	&&	$insert_id!='')	{
				//Send developer registration mail
				$fields 						=	'*';
				$condition 						=	' 1';
				$login_result 					=	$adminLoginObj->getAdminDetails($fields,$condition);
				$mailContentArray['password'] 	=	$_POST['actual_password'];
				$mailContentArray['toemail'] 	=	$_POST['email'];
				$mailContentArray['subject'] 	=	'Registration';
				$mailContentArray['from'] 		=	$login_result[0]->EmailAddress;
				$mailContentArray['verifylink']	=	SITE_PATH.'/ActivateUser.php?UID='.encode($insert_id).'&Type=3';
				$mailContentArray['fileName']	=	'registration.html';
				sendMail($mailContentArray,'5');
				$mailContentArray['fileName']	= 	'approveDeveloperRegistration.html';
				$mailContentArray['userEmail']	=	$_POST['email'];
				$mailContentArray['subject'] 	=	'New Developer & Brand Registration';
				$mailContentArray['toemail'] 	=	ADMIN_APPROVE_MAIL.', '.ADMIN_ASK_MAIL;
				sendMail($mailContentArray,'7');

				//update developer virtual coins
				if($virtualcoins != ''){
					$update_string = "VirtualCoins = ".$virtualcoins;
					$condition     = "id = ".$insert_id;
					$gameDevObj->updateGameDevDetails($update_string,$condition);
				}
				
				$_SESSION['tilt_developer_id'] 		=	$insert_id;		
				$_SESSION['tilt_developer_name'] 	=	trim($_POST['contact_name']);
				$_SESSION['tilt_developer_company']	=	trim($_POST['company']);
				$_SESSION['tilt_developer_email'] 	=	trim($_POST['email']);
				$_SESSION['tilt_developer_amount'] 	=	0;
				$_SESSION['tilt_developer_logo'] 	=	'developer_logo.png';
				$_SESSION['tilt_developer_coins'] 	=	$virtualcoins;
				header('location:GameList?cs=1');
			}
			header("location:Login");
			die();
		}	//	End of Already Exist condition
		else{
			if($email_exists == 1){
				$error_msg   = "Email address already exists";
				$field_focus = 'email';
			}
			$display = "block";
			$class   = "error_msg";
		}	
	}
}
?>
<body class="login_bg skin-black">
<?php top_header(); ?>
	<div class="form-box no-top-margin" id="login-box">
      <form name="profile_form" id="profile_for" action="" method="post" data-webforms2-force-js-validation="true">
            <div class="body">
				<h1>Developer & Brand - Sign Up</h1>
				<?php if($error_msg !='') { ?><div class="error_msg"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $error_msg;?></div><?php  } ?>
                <div class="form-group">
                    <label>Company Name </label>
					<input type="text" class="form-control" name="company" id="company" maxlength="100" value="<?php if(isset($company) && $company != '') echo $company;  ?>" >
                </div>
                <div class="form-group">
                    <label>Contact Name</label>
					<input type="text" class="form-control" name="contact_name" id="contact_name" maxlength="50" value="<?php if(isset($contactName) && $contactName != '') echo $contactName;  ?>" >
                </div>
                <div class="form-group">
                    <label><span class="required_field">*</span>&nbsp;E-mail </label>
					<input type="email" class="form-control" name="email" required id="email" maxlength="75" value="<?php if(isset($email) && $email != '') echo $email;  ?>" >
                </div>
                <div class="form-group">
                    <label><span class="required_field">*</span>&nbsp;Password </label>
					<input type="password" class="form-control" name="password" required id="password" maxlength="20" value="<?php if(isset($password) && $password != '') echo $password;  ?>" oninput="passwordMinLength('password');" autocomplete="off" ondrop="return false;" onpaste="return false;">
                </div>
                <div class="form-group">
                    <label><span class="required_field">*</span>&nbsp;Confirm Password </label>
					<input type="password" class="form-control" name="confirm_password" required id="confirm_password"  oninput="check(this);" maxlength="20" value="<?php if(isset($confPassword) && $confPassword != '') echo $confPassword;  ?>" autocomplete="off" ondrop="return false;" onpaste="return false;">
                </div>         
            </div>
            <div class="footer">          
				<input type="submit" class="btn btn-default btn-block" name="submit" id="submit" value="Sign Up" title="Sign Up" alt="Sign Up" onclick="return validateRegForm();">
            </div>
        </form>
    </div> 
<?php footerLinks(); commonFooter(); ?>
<script language='javascript' type='text/javascript'>
function check(input) {
	if (input.value != document.getElementById('password').value) {
		input.setCustomValidity('Password Must be Matching.');
	} else {
		input.setCustomValidity('');
	}
}
function validateRegForm(){
	if($("#password").val() != '') {
		if($.trim($("#password").val()) == '') {
			$("#password").val('');
			document.getElementById("password").setCustomValidity("White space is not allowed");
		}
	}
	if($.trim($("#password").val()) != $.trim($("#confirm_password").val())){
		document.getElementById("confirm_password").setCustomValidity("Password Must be Matching.");
	}
}
</script>
</html>