<?php
require_once('includes/CommonIncludes.php');
$error = $msg = '';
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
$error = $msg = '';
if(isset($_POST['forget_password_submit']) && $_POST['forget_password_submit'] == 'Submit')
{
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
    $_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
    $condition  	= " EmailAddress = '{$_POST['email']}'";
    $login_result 	= $adminLoginObj->checkAdminLogin($condition);
    if($login_result){		
		$mailContentArray['name'] 		= $login_result[0]->UserName;
		$mailContentArray['toemail'] 	= $login_result[0]->EmailAddress;
		$mailContentArray['password'] 	= $login_result[0]->Password;
		$mailContentArray['subject'] 	= 'Forget Password Mail';
		$mailContentArray['userType']	= 'Admin';
		$mailContentArray['from'] 		= $login_result[0]->EmailAddress;
		$mailContentArray['fileName']	= 'adminForgotPasswordMail.html';
		sendMail($mailContentArray,'3');
		$msg = "Login information has been sent to your mail"; 
	}
	else{
		$error = "Invalid Email Address ";
	}
}
commonHead();?>
<body onload="fieldfocus('email');"  class="login_bg">
<div id="login_form">
	<form action="" class="l_form" name="forget_password_form" id="forget_password_form"  method="post">
		<div class="login">
			<h1>MGC - TiLT</h1>
			<h2>Forgot Password</h2>
			<div class="msg_hgt" style="height:40px;"> 
				<?php if($error !='') { ?><div class="error_msg" align="center"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $error;?></div><?php  } ?>
				<?php if($msg !='') { ?><div class="success_msg" style="margin-top:0px;" align="center"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $msg;?></div><?php  } ?>
			</div>
			<div class="f-filed"> 
				<label>Email</label>
				<input type="text" class="" name="email" id="email" value="" />
			</div>
			<div class="f-buttons"> 
				<input type="submit" class="submit_button" title="Submit" alt="Submit" name="forget_password_submit" id="forget_password_submit" value="Submit" />
				<a href="Login" class="submit_button" name="Back" id="Back" value="Back">Back </a>
			</div>
		</div>
	</form>
</div>
</body>
<?php commonFooter(); ?>
</html>


