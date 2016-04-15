<?php
require_once('includes/CommonIncludes.php');
$error = $msg = '';
require_once('controllers/DeveloperController.php');
$gameDevObj   =   new DeveloperController();
require_once('controllers/AdminController.php');
$adminLoginObj	=	new AdminController();	
$error = $msg = '';
if(isset($_POST['forget_password_submit']) && $_POST['forget_password_submit'] == 'Submit')
{
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	$condition  	= " Email = '{$_POST['email']}' and Status IN (1,2) ";
    $result 		=   $gameDevObj->checkGameDeveloperLogin($condition);
	if(isset($result) && count($result)>0 && is_array($result))
    {
		if( isset($result[0]->VerificationStatus)	&& $result[0]->VerificationStatus	!=	1 ){
				$error	=	"You need to verify your Email address.";
			}
			else 
			if(isset($result[0]->Status)	&&	$result[0]->Status	==	1 && isset($result[0]->VerificationStatus)	&& $result[0]->VerificationStatus	==	1)	{
				$fields							=	' * ';
				$condition 						=	' 1 ';
				$admin_result 					=	$adminLoginObj->getAdminDetails($fields,$condition);
				$mailContentArray['password'] 	=	$result[0]->ActualPassword;
				$mailContentArray['email'] 		=	$result[0]->Email;
				$mailContentArray['name']		=	$result[0]->UserName;
				$mailContentArray['toemail'] 	=	$result[0]->Email;
				$mailContentArray['subject'] 	=	'Forgot Password';
				$mailContentArray['from'] 		=	$admin_result[0]->EmailAddress;//'MGC';
				$mailContentArray['fileName']	=	'developerForgotPasswordMail.html';
				sendMail($mailContentArray,'6');
				$msg = "Login information has been sent to your Email";
			}
			else if(isset($result[0]->Status)	&&	$result[0]->Status	==	2)	$error = "You are not yet approved by admin.";
		
	}else{
		$condition  	= " Email = '{$_POST['email']}' and Status NOT IN (1,2) ";
		$result 		=   $gameDevObj->checkGameDeveloperLogin($condition);
		if(isset($result) && count($result)>0 && is_array($result))
		{
			if(isset($result[0]->Status)	&&	$result[0]->Status	==	3)		$error = "Your account is deleted.";
			else if(isset($result[0]->Status)	&&	$result[0]->Status	==	4)	$error = "Your account is rejected.";
		}
		else	$error = "Email is not associated with us.";
	}
}
commonHead();?>
<body onload="fieldfocus('email');"  class="login_bg skin-black">
<?php top_header(); ?>
<div class="form-box" id="login_form">
	<form action="" class="l_form" name="forget_password_form" id="forget_password_form"  method="post" data-webforms2-force-js-validation="true">
		<div class="body login">
			<h1 style="font-size:18px;">Developer & Brand - Forgot Password</h1>
			<div class="msg_hgt"> 
				<?php if($error !='') { ?><div class="error_msg" align="center"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $error;?></div><?php  } ?>
				<?php if($msg !='') { ?><div class="success_msg" style="margin-top:0px;" align="center"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $msg;?></div><?php  } ?>
			</div>
			<div class="form-group"> 
				<label>Email</label>
				<input type="email" class="form-control" name="email" id="email" required value="" />
			</div>
		</div>
			 <div class="footer">  
				<input type="submit" class="btn btn-default btn-block" title="Submit" name="forget_password_submit" id="forget_password_submit" value="Submit" />
				<a href="Login" class="text-center link" title="Back to Login">Back to Login</a>
			</div>
	</form>
</div>
<?php footerLinks(); commonFooter(); ?>
</html>


