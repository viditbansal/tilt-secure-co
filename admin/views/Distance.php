<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$commissionObj   =   new AdminController();
$class =  $msg  = '';
$display = 'none';
$error = $msg = '';
$fields		  =	" * ";
$where		  =	" 1 and Status = 1 ";
$commission_details = $commissionObj->getDistance($fields,$where);
if(isset($commission_details) && is_array($commission_details) && count($commission_details)>0){	
	$distance	=	$commission_details[0]->Distance;	
	$count = 1;
}
else
	$count = 0;
if(isset($_POST['distance_settings_submit']) && $_POST['distance_settings_submit'] != '' )
{
	$updateString   =   " Distance = '".$_POST['Distance']."' ";
	$condition      =   " Status = 1 ";
	$commissionObj->updateDistanceDetails($updateString,$condition);
	header('location:Distance?msg=2');
}
if(isset($_POST['ins_distance_settings_submit']) && $_POST['ins_distance_settings_submit'] != '' )
{	
	$commissionObj->insertDistance($_POST['Distance']);
	header('location:Distance?msg=1');
}

if(isset($_GET['msg']) && $_GET['msg'] != '' && $_GET['msg'] == 1)
	$msg = "Distance settings inserted successfully";
else if(isset($_GET['msg']) && $_GET['msg'] != '' && $_GET['msg'] == 2)
	$msg = "Distance settings updated successfully";

commonHead(); ?>
<body>
	<?php top_header(); ?>
	<div class="box-header"><h2><i class="fa fa-arrows-h"></i>Distance Settings</h2></div>
	<form name="general_settings_form" id="general_settings_form" action="" method="post">
	<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">	
		<tr><td align="center">
			<table cellpadding="0" cellspacing="0" align="center" border="0" width="35%">							 
				<tr><td  height="40" align="center" colspan="3">
				<?php if($msg !='') { ?><div class="success_msg" align="center"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $msg;?></span></div><?php  } ?>
				</td></tr>
				<tr><td height="10"></td></tr>
				<tr>
				<td align="left" width="30%" valign="top"><label>Distance</label></td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" >
				<input type="text" class="input" name="Distance" onkeypress="return isNumberKey(event);" maxlength="6" style="width:90px" id="Distance" value="<?php  if(isset($distance)) echo $distance;  ?>" /> Kms
				
				</td>
				</tr>				
				<tr>
				<td colspan="2"></td>
				<td align="left">
				<input type="submit" class="submit_button" name="<?php if($count != 0) echo "distance_settings_submit";  else  echo "ins_distance_settings_submit";?>" id="<?php if($count != 0) echo "distance_settings_submit";  else  echo "ins_distance_settings_submit";?>" value="Submit" title="Submit" alt="Submit" />
				</td>
				</tr>
			</table>
		</td></tr>
		<tr><td height="10"></td></tr>
	</table>
	</form>	
<?php commonFooter(); ?>
</html>