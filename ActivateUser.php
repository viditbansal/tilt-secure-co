<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
require_once('admin/includes/AdminTemplates.php');
require_once('admin/config/config.php');
require_once('admin/includes/CommonFunctions.php');
$success  = 0;
$responseErrorMessage = $responseSuccessMessage= $url = '';
$type = '';
$data 	= array();
if(isset($_GET['UID']) && $_GET['UID'] !='' && isset($_GET['Type']) && $_GET['Type'] !=''){
	$type  			= 	$_GET['Type'];
	$url			=	ACTIVATION_LINK_PATH.'/v4/users/checkActivateUser/'.decode($_GET['UID']).'?Type='.$_GET['Type'];
	$method			=   'GET';
	$curlResponse	=	curlRequest($url,$method,$data);
}
else {
	header('location:404.php');
	die();
}
if(isset($curlResponse) && is_array($curlResponse)  && $curlResponse['meta']['code'] == 201) {
	$requestMessage = '';
}else if(isset($curlResponse['meta']['errorMessage']) && $curlResponse['meta']['errorMessage'] != '') {
	$requestMessage	=	$curlResponse['meta']['errorMessage'];
	$type  			= 	$_GET['Type'];
	header('location:404.php?Active='.$_GET['UID'].'&Type='.$type);
	die();
} else {
	$requestMessage 	= 	"Bad Request";
}
if(isset($requestMessage) && $requestMessage == ''){

	$success = 1;
} 
$success  = 1;
commonHeadResponsive();?>
<body onload="fieldfocus('Password');" class="responsive">
	<div class="adm_head error-header">
		<div class="login_logo"><h1></h1></div>
	</div>
	<div id="login_form">
		<table align="center" cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" >
			<tr>
				<td valign="middle" align="center" height="100%">
											
					<?php if(isset($success) && $success == 1) { ?>
					<form action="" id="admin_login_form">
						<table align="center" cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" >
							<tr>
								<td>
								<div class="acenter sample_active">
									<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr><td colspan="3" class="top_space"></td></tr>
										<tr><td colspan="3" height="50"></td></tr>
										<tr>
											<td>
												<section class="">                 
													<div class="error-page success">
														<h1 style="color:#1c4478;text-align:center;padding-left:10px;" class="headline text-info"><i class="fa fa-check" style="text-shadow:3px 2px  0 rgba(0, 0, 0, 0.15)"></i></h1>
														<div class="error-content">
															<h3 style="font-size:26px;padding-bottom:0">Boom!</h3>
															<?php if($type == 1) { ?>
																<h3 style="font-size:18px;padding-top:7px">Your TiLT account has been activated successfully.</h3>
																<p>You may now access your TiLT account.</p>  
															<?php } else if($type == 2){ ?>
																<h3 style="font-size:18px;padding-top:7px">Your TiLT Brand account has been activated successfully.</h3>
															<?php } else if($type == 3){ ?>
																<h3 style="font-size:18px;padding-top:7px">Your TiLT Developer & Brand account has been verified successfully.</h3>
															<?php }else {
																	header('location:404.php');
																	die();
																}
															?>
														</div>
													</div>
												</section>
												
											</td>
										</tr>
										<tr><td height="30"></td></tr>
									</table>
								</div>
							</td>
						</tr>
					</table>
					</form>
					<?php }?>
				</td>
			</tr>
		</table>
	</div>
</body>
<?php commonFooter(); ?>
</html>

