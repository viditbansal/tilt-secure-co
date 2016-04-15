<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
$class =  $msg  = '';
$display = 'none';
$error = $msg = '';
$fields		  =	" * ";
$where		  =	" 1 LIMIT 1 ";
$distance_details = $adminLoginObj->getDistance($fields,$where);
if(isset($distance_details) && is_array($distance_details) && count($distance_details)>0)
	$distance	=	$distance_details[0]->Distance;
if(isset($_POST['distance_settings_submit']) && $_POST['distance_settings_submit'] != '' )
{	
	$updateString   =   " Distance = '".$_POST['distance']."' ";
	$condition      =   " id = 1 ";
	$adminLoginObj->updateDistanceDetails($updateString,$condition);
	header('location:DistanceSettings?msg=1');
}
if(isset($_GET['msg']) && $_GET['msg'] != '')
	$msg = "Distance updated successfully";
commonHead(); ?>
<body>
	<?php top_header(); ?>
							  <form name="distance_settings_form" id="distance_settings_form" action="" method="post">
							  	 <table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">
									<tr><td align="right"><span><h2> Distance Settings </h2></span></td></tr>		
								<tr><td align="center"><table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">							 
									<tr><td  height="40" align="center" colspan="3">
									<?php if($msg !='') { ?><div class="success_msg" align="center"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $msg;?></span></div><?php  } ?>
									</td></tr>
									<tr><td height="10"></td></tr>
									<tr>
										<td align="left" valign="top"><label>Distance</label></td>
										<td class="" valign="top" align="center">:</td>
										<td align="left"  height="60" valign="top">
											<input type="text" class="input" name="distance" id="distance" value="<?php  if(isset($distance) && $distance) echo $distance  ?>" />
										</td>
									</tr>
									<tr>
										<td colspan="2"></td>
										<td align="left">
											<input type="submit" class="submit_button" name="distance_settings_submit" id="distance_settings_submit" value="Submit" title="Submit" alt="Submit" />
										</td>
									</tr>
									</table></td></tr>
								</table>
							  </form>	
						  
<?php commonFooter(); ?>
</html>