<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$commissionObj   =   new AdminController();
$class =  $msg  = '';
$display = 'none';
$error = $msg = '';
$fields		  =	" * ";
$where		  =	" 1 and Status = 2 ";
$commission_details = $commissionObj->getCommission($fields,$where);
if(isset($commission_details) && is_array($commission_details) && count($commission_details)>0){	
	$Commission	=	$commission_details[0]->Commission;	
	$count = 1;
}
else
	$count = 0;
if(isset($_POST['Commission_settings_submit']) && $_POST['Commission_settings_submit'] != '' )
{
	$updateString   =   " Commission = '".$_POST['Commission']."' ";
	$condition      =   " Status = 2 ";
	$commissionObj->updateCommissionDetails($updateString,$condition);
	header('location:Commission?msg=2');
}
if(isset($_POST['ins_Commission_settings_submit']) && $_POST['ins_Commission_settings_submit'] != '' )
{	
	$commissionObj->insertCommission($_POST['Commission']);
	header('location:Commission?msg=1');
}

if(isset($_GET['msg']) && $_GET['msg'] != '' && $_GET['msg'] == 1)
	$msg = "Commission settings inserted successfully";
else if(isset($_GET['msg']) && $_GET['msg'] != '' && $_GET['msg'] == 2)
	$msg = "Commission settings updated successfully";

commonHead(); ?>
<body>
	<?php top_header(); ?>
	<div class="box-header"><h2><i class="fa fa-money"></i>Commission Settings</h2></div>
	<div class="clear">
	<form name="general_settings_form" id="general_settings_form" action="" method="post">
	<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">	
		<tr><td align="center">
			<table cellpadding="0" cellspacing="0" align="center" border="0" width="35%">							 
				<tr><td  height="40" align="center" colspan="3">
				<?php if($msg !='') { ?><div class="success_msg" align="center"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $msg;?></div><?php  } ?>
				</td></tr>
				<tr><td height="10"></td></tr>
				<tr>
				<td align="left" width="30%" valign="top"><label>Commission</label></td>
				<td align="center" class="" valign="top" width="3%">:</td>
				<td height="60" valign="top" align="left" >
				<input type="text" class="input w50" name="Commission" onkeypress="return isNumberKey(event);"  maxlength="3" id="Commission" value="<?php  if(isset($Commission)) echo $Commission;  ?>" /> %
				
				</td>
				</tr>				
				<tr>
				<td colspan="2"></td>
				<td align="left">
				<input type="submit" class="submit_button" name="<?php if($count != 0) echo "Commission_settings_submit";  else  echo "ins_Commission_settings_submit";?>" id="<?php if($count != 0) echo "Commission_settings_submit";  else  echo "ins_Commission_settings_submit";?>" value="Submit" title="Submit" alt="Submit" />
				</td>
				</tr>
			</table>
		</td></tr>
		<tr><td height="10"></td></tr>
	</table>
	</form>	
	</div>
<?php commonFooter(); ?>
</html>