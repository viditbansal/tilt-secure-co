<?php
ob_start();
require_once('includes/CommonIncludes.php');
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
if(isset($_SESSION['mgc_admin_user_name'])){
	header('location:UserList?cs=1');
	die();
}
$error = '';
if(isset($_POST['admin_login_submit']) && $_POST['admin_login_submit'] == 'Submit'){
	$_POST          =   unEscapeSpecialCharacters($_POST);
   	$_POST          =   escapeSpecialCharacters($_POST);
    $md5Pass        =   $_POST['password'];		
    $condition  	=   " UserName = '{$_POST['user_name']}' AND Password = '{$md5Pass}'";
    $result 		=   $adminLoginObj->checkAdminLogin($condition);
	if($result)	{
		$_SESSION['mgc_admin_user_id'] 		=	$result[0]->id;
		$_SESSION['mgc_admin_user_name'] 	=	$result[0]->UserName;
		$_SESSION['mgc_admin_user_email'] 	=	$result[0]->EmailAddress;
		$fields     						=	" LastLoginDate = '".date('Y-m-d H:i:s')."'";
		$condition  						=	" Id = ".$result[0]->id;
		$result     						=   $adminLoginObj->updateAdminDetails($fields,$condition);
		header('location:UserList?cs=1');
		die();
	}
	else{
		$error = "Invalid User Name or Password";
	}
}
commonHead();
?>
<body onload="fieldfocus('user_name');" class="login_bg">
<div id="login_form">
	<form action="" name="admin_login_form" id="admin_login_form"  method="post">
		<div class="login">
			<h1>MGC - TiLT</h1>
			<h2>Administrator Login</h2>
			<div class="msg_hgt"> 
				<?php if($error !='') { ?><div class="error_msg"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $error;?></div><?php  } ?>
			</div>
			<div class="f-filed"> 
				<label>User Name</label>
				<input type="text" class="" name="user_name" id="user_name" value="" />
			</div>
			<div class="f-filed"> 
				<label>Password</label>
				<input type="password" class="" name="password" id="password" value="" maxlength="20">
			</div>
			<div class="f-buttons"> 
				<input type="submit" value="Submit" class="submit" title="Submit" alt="Submit" name="admin_login_submit" id="admin_login_submit"/>
				<a href="ForgotPassword" title="Forgot your password">Forgot your password?</a>
			</div>
		</div>
	</form>
</div>
<?php commonFooter(); ?>
</html>