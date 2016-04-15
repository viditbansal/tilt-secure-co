<?php
ob_start();
require_once('includes/CommonIncludes.php');
require_once('controllers/DeveloperController.php');
$gameDevObj   =   new DeveloperController();
if(isset($_SESSION['tilt_developer_id'])){ 
	header('location:GameList?cs=1');
	die();
}
$error = '';
if(isset($_POST['login_submit']) && $_POST['login_submit'] == 'Login'){
	$_POST          =   unEscapeSpecialCharacters($_POST);
   	$_POST          =   escapeSpecialCharacters($_POST);
    $md5Pass        =   sha1(trim($_POST['password']));		
    $email        	=   trim($_POST['email']);		
	$condition  	=   " Email = '{$email}' AND Password = '{$md5Pass}' AND Status = 1";
    $result 		=   $gameDevObj->checkGameDeveloperLogin($condition);
	if(isset($result) && count($result)>0 && is_array($result))
    {
		$_SESSION['tilt_developer_id'] 		=	$result[0]->id;		// mgc_developer_id
		$_SESSION['tilt_developer_name'] 	=	$result[0]->Name;
		$_SESSION['tilt_developer_company']	=	$result[0]->Company;
		$_SESSION['tilt_developer_email'] 	=	$result[0]->Email;	// mgc_admin_user_email
		$_SESSION['tilt_developer_amount'] 	=	$result[0]->Amount;
		$_SESSION['tilt_developer_coins'] 	=	$result[0]->VirtualCoins;
		$_SESSION['tilt_developer_timeZone']=	getTimezoneFromLocation(USLAT,USLONG); //Used to get newyork time in datetimepicker
		if($result[0]->Photo != '') $_SESSION['tilt_developer_logo']	=	$result[0]->Photo;
		else $_SESSION['tilt_developer_logo']	=	'developer_logo.png';
		header('location:GameList?cs=1');
		die();
	} else{
		$condition  	=   " Status != 1 AND Email = '{$email}' AND Password = '{$md5Pass}'";
		$result 		=   $gameDevObj->checkGameDeveloperLogin($condition);
		if(isset($result) && count($result)>0 && is_array($result)) {
			if(isset($result[0]->Status) && $result[0]->Status == 3)
				$error = "Your account is deleted.";
			else if(isset($result[0]->Status) && $result[0]->Status	==	4)
				$error = "Your account is rejected.";
		}
		else
			$error = "Invalid Email or Password.";	// Invalid User Name or Password.
	}
}
commonHead();
?>
<body onload="fieldfocus('email');" class="login_bg skin-black">
<?php top_header(); ?>
<div class="form-box" id="login_form">
	<form action="" name="admin_login_form" id="admin_login_form"  method="post" data-webforms2-force-js-validation="true">
		<div class="body login">
			<h1>Developer & Brand - Login</h1>
			<div class="msg_hgt"> 
			<?php displayNotification(' '); ?>
				<?php if($error !='') { ?><div class="error_msg"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $error;?></div><?php  } ?>	
			</div>
			<div class="form-group"> 
				<label>Email</label>
				<input type="email" class="form-control" name="email" maxlength="100" required id="email" value="" />
			</div>
			<div class="form-group"> 
				<label>Password</label>
				<input type="password" class="form-control" name="password" maxlength="100" required id="password" value="" >
			</div>
		</div>
			 <div class="footer">  
				<input type="submit" value="Login" class="btn btn-default btn-block" title="Login" name="login_submit" id="login_submit"/>
				<a href="ForgotPassword" class="text-center link" title="Forget Password?">Forgot Password?</a>
			</div>
	</form>
</div>
<?php footerLinks(); commonFooter(); ?>
</html>