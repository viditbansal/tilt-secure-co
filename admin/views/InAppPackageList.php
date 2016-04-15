<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/GiftCardController.php');
$inAppObj	=	new GiftCardController();
admin_login_check();
commonHead();
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_product_id']);
	unset($_SESSION['mgc_sess_product_name']);
	unset($_SESSION['mgc_sess_product_status']);
	unset($_SESSION['mgc_sess_product_price']);
}
if(isset($_POST['do_action']) && $_POST['do_action'] != '')	{
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
		$Ids	=	implode(',',$_POST['checkedrecords']);
		if($_POST['bulk_action']==1){
			$productIds	=	$Ids;
			$updateStatus	=	1;
		}
		else if($_POST['bulk_action']==2){
			$productIds	=	$Ids;
			$updateStatus	=	2;
		}
		else
			$delete_id = $Ids;
	}
}
if(isset($_GET['delId']) && $_GET['delId']!='')
	$delete_id      = $_GET['delId'];
if(isset($_GET['editId']) && $_GET['editId']!=''	&& isset($_GET['status'])	&&	$_GET['status']!=''){
	$updateStatus	=	$_GET['status'];
	$productIds      = $_GET['editId'];
}
if(isset($delete_id) && $delete_id != ''){	
	$inAppObj->changeProductStatus($delete_id,'3');
	$_SESSION['notification_msg_code']	=	3;
	header("location:InAppPackageList");
	die();
}
else if(isset($productIds) && $productIds != ''){	
	$inAppObj->changeProductStatus($productIds,$updateStatus);
	$_SESSION['notification_msg_code']	=	4;
	header("location:InAppPackageList");
	die();
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$post_values	=	$_POST;
	$post_values    =   unEscapeSpecialCharacters($post_values);
	$post_values    =   escapeSpecialCharacters($post_values);
	if(isset($post_values['product_id']))
		$_SESSION['mgc_sess_product_id'] 	=	trim($post_values['product_id']);
	if(isset($post_values['product_name']))
		$_SESSION['mgc_sess_product_name']	    	=	trim($post_values['product_name']);
	if(isset($post_values['product_price']))
		$_SESSION['mgc_sess_product_price']	    	=	trim($post_values['product_price']);
	if(isset($post_values['product_status']))
		$_SESSION['mgc_sess_product_status']	=	$post_values['product_status'];
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields = " * ";
$condition = " Status != 3 ";
$productResult = $inAppObj->getInAppList($fields,$condition);
$tot_rec 		 = $inAppObj->getTotalRecordCount();
?>
<body > 
<?php top_header(); ?>
	<div class="box-header">
		<h2><i class="fa fa-list"></i>InApp Product List</h2>
		<span class="fright"><a href="InAppPackages"  title="Add Product"><i class="fa fa-plus-circle"></i>&nbsp;Add Product</a></span>
	</div>
		<div class="clear">
			<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
			
				<tr><td style="padding: 0 5px">
					<form name="search_category" action="InAppPackageList" method="post">
					<table align="center" cellpadding="6" cellspacing="0" border="0" width="100%" class="filter_form">									       
						<tr><td></td></tr>
						<tr>													
							<td width="5%" style="padding-left:20px;"><label>Product Id</label></td>
							<td width="2%" align="center">:</td>
							<td align="left"  width="20%">
								<input type="text" class="input" name="product_id" id="product_id"  value="<?php  if(isset($_SESSION['mgc_sess_product_id']) && $_SESSION['mgc_sess_product_id'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_product_id']);  ?>" >
							</td>
							<td  width="5%"><label>Product Name</label></td>
							<td width="2%" align="center">:</td>
							<td align="left"  width="20%" >
								<input type="text" class="input" id="product_name" name="product_name"  value="<?php  if(isset($_SESSION['mgc_sess_product_name']) && $_SESSION['mgc_sess_product_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_product_name']);  ?>" >
							</td>
							<td  width="5%"><label>Price</label></td>
							<td width="2%" align="center">:</td>
							<td align="left"  width="17%" >
								<input type="text" class="input" id="product_price" name="product_price"  onkeypress="return isNumberKey(event);" value="<?php  if(isset($_SESSION['mgc_sess_product_price']) && $_SESSION['mgc_sess_product_price'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_product_price']);  ?>" maxlength="8" >
							</td>
							<td  width="5%"><label>Status</label></td>
							<td width="2%" align="center">:</td>
							<td width="15%">
								<select name="product_status" id="product_status" tabindex="2" title="Select Status" class="w50">
									<option value="">Select</option>
								<?php foreach($inAppStatusArray as $key => $statusValue) { if($key !=3){ ?>
									<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_product_status']) && $_SESSION['mgc_sess_product_status'] != '' && $_SESSION['mgc_sess_product_status'] == $key) echo 'Selected';  ?>><?php echo $statusValue; ?></option>
								<?php }
								}?>
								</select>
							</td>
						</tr>
						<tr height="50">
							<td align="center" valign="top" colspan="12"><input type="submit" class="submit_button" name="Search" title="Search" id="Search" value="Search"></td>
						</tr>
					 </table>
					 </form>
				</td></tr>
				<tr><td height="20"></td></tr>
				<tr><td>
					<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
						<tr>
							<?php if(isset($productResult) && is_array($productResult) && count($productResult) > 0){ ?>
							<td align="left" width="20%">No. of Product(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
							<?php } ?>
							<td align="center">
									<?php if(isset($productResult) && is_array($productResult) && count($productResult) > 0 ) {
										pagingControlLatest($tot_rec,'InAppPackageList'); ?>
									<?php }?>
							</td>
						</tr>
					</table>
				</td></tr>
				<tr><td height="10"></td></tr>
				<tr><td colspan= '2' align="center">
					<?php displayNotification('Product'); ?>
				</td></tr>
				<tr><td height="10"></td></tr>
				<tr><td>
					<div class="tbl_scroll">
					  <form action="InAppPackageList" class="l_form" name="InAppPackageListForm" id="InAppPackageListForm"  method="post"> 
						<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
							<tr align="left">
								<th align="center" class="text-center" width="2%"><input onclick="checkAllRecords('InAppPackageListForm');" type="Checkbox" name="checkAll"/></th>
								<th align="center" width="2%" class="text-center">#</th>												
								<th width="26%"><?php echo SortColumn('Name','Product Name'); ?></th>
								<th width="10%"><?php echo SortColumn('ProductId','Product Id'); ?></th>
								<th width="45%">Description</th>
								<th width="10%"><?php echo SortColumn('Price','Price'); ?></th>
								<th width="5%"><?php echo SortColumn('Status','Status'); ?></th>
							</tr>
							
							<?php if(isset($productResult) && is_array($productResult) && count($productResult) > 0 ) { 
								$style = ' style="float:left;width:126px;" ';
									foreach($productResult as $key=>$value){ ?>									
							<tr id="test_id_<?php echo $value->id;?>"	>
							 	<td valign="top" align="center"><input id="checkedrecords" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" /></td>
								<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
								<td valign="top" align="left" >
									<div class="user_profile" style="width: 295px;float: left;word-wrap: break-word;line-height: 18px">
										<p align="left" style="padding-bottom:6px"><strong>	<?php if(isset($value->Name) && !empty($value->Name)) echo ucFirst($value->Name); else echo ' - ';?></strong></p>
										<div class="userAction" style="display:block" id="userAction">
										<?php if(isset($value->Status)	&&	$value->Status == 1) { ?>			
												<a class="userIcon" alt=" Active" title="Active" onclick="javascript:return confirm('Are you sure want to change the status?')" href="InAppPackageList?editId=<?php echo $value->id;?>&status=2"><i class="fa fa-user fa-lg"></i></a>
										<?php } else if(isset($value->Status)	&&	$value->Status == 2){ ?>
												<a class="userIcon" style="color:gray"  title="Inactive" alt="Inactive" onclick="javascript:return confirm('Are you sure want to change the status?')" href="InAppPackageList?editId=<?php echo $value->id;?>&status=1"><i class="fa fa-user fa-lg"></i></a>
										<?php }	?>
										<a href="InAppPackages?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="editUser pop_up"><i class="fa fa-edit fa-lg"></i></a>
										<a onclick="javascript:return confirm('Are you sure to delete?')" href="InAppPackageList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="deleteUser"><i class="fa fa-trash-o fa-lg"></i></a>
										</div>
									</div>
								</td>
								<td valign="top" align="left" ><?php if(isset($value->ProductId) && !empty($value->ProductId)) echo $value->ProductId; else echo ' - ';?></td>
								<td valign="top" align="left" >
									<div style="float: left; word-wrap: break-word; white-space: normal; line-height: 18px;width: 590px;">
										<?php if(isset($value->Description) && !empty($value->Description)) echo ucFirst($value->Description); else echo ' - ';?>
									</div>
								</td>
								<td valign="top" align="left" ><?php if(isset($value->Price) && $value->Price != '0') echo '$'.number_format($value->Price); else echo ' - ';?></td>
								<td valign="top" align="left" ><?php if(isset($value->Status) && isset($inAppStatusArray[$value->Status])) echo $inAppStatusArray[$value->Status]; else echo ' - ';?></td>
							</tr>
							<?php } ?> 																		
						</table>
						<?php if(isset($inAppStatusArray) && is_array($inAppStatusArray) && count($inAppStatusArray) > 0){ 
								bulk_action($inAppStatusArray);
							}
						?>
						</form>
						<?php } else { ?>	
							<tr>
								<td colspan="16" align="center" style="color:red;">No Product(s) Found</td>
							</tr>
						<?php } ?>
					</div>
					
					</td>
				</tr>
			</table>
		</div>
	</div>	
						  	
<?php commonFooter(); ?>
<script type="text/javascript">
jQuery(function() {
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[id^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");
	   
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
$(document).ready(function() {
	$(".pop_up").colorbox(
		{
			iframe:true,
			width:"75%", 
			height:"50%",
			title:true,
	});
});
</script>
</html>
