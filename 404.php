<?php
require_once('admin/includes/AdminTemplates.php');
require_once('admin/config/config.php');
commonHeadResponsive();
$type = '';
if(isset($_GET['Type']) && $_GET['Type'] != '')
	$type = $_GET['Type'];
?>


<body class="responsive">
	<div class="adm_head error-header">
		<div class="login_logo"><h1></h1></div>
	</div>
	<div id="login_form">
		<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" >
			<tr>
				<td valign="middle" align="center" height="100%">
					<table align="center" cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" >
							<tr>
								<td>
								<div class="acenter">
									<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr><td colspan="3" class="top_space"></td></tr>
										
										<tr><td colspan="3" height="50"></td></tr>
										<tr>
											<td>
												<section class="">                 
													<div class="error-page">
														<h1 style="color:#1c4478" class="headline text-info"> <strong style="text-shadow:3px 2px  0 rgba(0, 0, 0, 0.15)">404</strong></h1>
														<div class="error-content">
															
															<?php if(isset($_GET['UID']) && !empty($_GET['UID'])) { ?>
															<h3><i class="fa fa-warning text-yellow"></i> Oops! Link not found.</h3>
															<p>You can't access this link directly. Please proceed Forget Password process with your App.</p>   
															<?php } 
															else if(isset($_GET['Active']) && !empty($_GET['Active'])) { ?>																
																<?php if($type == 1) { ?>
																	<h3><i class="fa fa-warning text-yellow"></i> Oops! Link not found.</h3>
																<p>You can't access this link again. Please access your TiLT account.</p> 
																<?php } else if($type == 2){ ?>
																	<h3><i class="fa fa-warning text-yellow"></i> Oops! Link not found.</h3>
																<p>You can't access this link again. Please access your TiLT Brand account.</p> 
																<?php } else if($type == 3){ ?>
																	<h3><i class="fa fa-warning text-yellow"></i> Oops! Link not found.</h3>
																		<p>You can't access this link again. Please access your TiLT Game developer account.</p> 
																<?php } 
															} else { ?>
															<h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>
															<p>We could not find the page you were looking for.</p>        
															<?php } ?>                   
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
				</td>
			</tr>
		</table>
	</div>
</body>
</html>

