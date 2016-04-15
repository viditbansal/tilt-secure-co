<?php
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/UserController.php');
$userObj   =   new UserController();
require_once('controllers/MessageController.php');
$messageObj   =   new MessageController();
require_once('controllers/GiftCardController.php');
$giftObj   =   new GiftCardController();

$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_merchant_name']);
	unset($_SESSION['mgc_sess_card_name']);
	unset($_SESSION['mgc_sess_amount']);
	unset($_SESSION['mgc_sess_merchant_registerdate']);
	unset($_SESSION['mgc_sess_available']);
	unset($_SESSION['mgc_sess_user_status']);

	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
if(isset($_GET['user_back']) && $_GET['user_back']=='1') {
	unset($_SESSION['ordertype']);
	unset($_SESSION['sortBy']);
	unset($_SESSION['orderby']);

}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['ses_merchantname']))
		$_SESSION['mgc_sess_merchant_name']	=	trim($_POST['ses_merchantname']);
	if(isset($_POST['ses_cardname']))
		$_SESSION['mgc_sess_card_name'] 	=	trim($_POST['ses_cardname']);
	if(isset($_POST['ses_amount']))
		$_SESSION['mgc_sess_amount']	   	=	trim($_POST['ses_amount']);
	if(isset($_POST['ses_status']))
		$_SESSION['mgc_sess_user_status']	=	$_POST['ses_status'];
	if(isset($_POST['ses_status']))
		$_SESSION['mgc_sess_available']		=	$_POST['ses_available'];

	if(isset($_POST['giftCard_date']) && $_POST['giftCard_date'] != ''){
		$validate_date = dateValidation($_POST['giftCard_date']);
		if($validate_date == 1){
			$date = date('Y-m-d',strtotime($_POST['giftCard_date']));
			if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
				$_SESSION['mgc_sess_giftCard_date']	= $date;
			else
				$_SESSION['mgc_sess_giftCard_date']	= '';
		}
		else
			$_SESSION['mgc_sess_giftCard_date']	= '';
	}
	else
		$_SESSION['mgc_sess_giftCard_date']	= '';
}

if(isset($_POST['do_action']) && $_POST['do_action'] != '')	{
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
		$Ids	=	implode(',',$_POST['checkedrecords']);
		if($_POST['bulk_action']==1){
			$GiftIds	=	$Ids;
			$updateStatus	=	1;
		}
		else if($_POST['bulk_action']==2){
			$GiftIds	=	$Ids;
			$updateStatus	=	2;
		}
		else
			$delete_id = $Ids;
	}
}

if(isset($_GET['delId']) && $_GET['delId']!='')
	$delete_id      = $_GET['delId'];


if(isset($delete_id) && $delete_id != ''){
	$giftObj->deleteGiftCardEntries($delete_id);
	$_SESSION['notification_msg_code']	=	3;
	header("location:GiftCardList");
	die();
}

else if(isset($GiftIds) && $GiftIds != ''){
	$giftObj->changeGiftCardStatus($GiftIds,$updateStatus);
	$_SESSION['notification_msg_code']	=	4;
	header("location:GiftCardList");
	die();
}


if(isset($_GET['editId']) && $_GET['editId']!=''	&& isset($_GET['status'])	&&	$_GET['status']!=''){
	$condition = " id = ".$_GET['editId'];
	$update_string = " Visibility = ".$_GET['status'];
	$giftCardListResult  = $giftObj->updateGiftCardDetails($update_string,$condition);
	$_SESSION['notification_msg_code']	=	4;
	header("location:GiftCardList");
	die();
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);

$fields    = " gc.*";
$condition = " ";
$giftCardListResult  = $giftObj->getGiftCardList($fields,$condition);
$tot_rec 		 = $giftObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($giftCardListResult)) {
	$_SESSION['curpage'] = 1;
	$giftCardListResult  = $giftObj->getGiftCardList($fields,$condition);
}
?>
<body>
<?php top_header(); ?>

	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i>Gift Card List</h2>
		<span class="fright"><a href="GiftCardManageAdd" title="Add Gift Card"><i class="fa fa-plus-circle"></i> Add Gift Card</a></span>
	</div>
           <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">

			<tr><td class="filter_form" >
				<form name="search_category" action="GiftCardList" method="post">
            	<table align="center" cellpadding="6" cellspacing="0" border="0" width="98%">
					<tr><td colspan="9"></td></tr>
					<tr>
						<td width="5%" ><label> Merchant Name </label></td>
						<td width="1%" align="center">:</td>
						<td align="left"  width="10%">
							<input type="text" tabindex="1" class="input" name="ses_merchantname" id="ses_merchantname"  value="<?php  if(isset($_SESSION['mgc_sess_merchant_name']) && $_SESSION['mgc_sess_merchant_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_merchant_name']);  ?>" >
						</td>
						<td  width="5%"><label>Amount&nbsp;(USD)</label></td>
						<td width="1%" align="center">:</td>
						<td align="left"  width="10%" >
							<input type="text" tabindex="2" class="input" id="ses_amount" name="ses_amount"  onkeypress="return isNumberKey(event);" value="<?php  if(isset($_SESSION['mgc_sess_amount']) && $_SESSION['mgc_sess_amount'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_amount']);  ?>" >
						</td>
						<td  width="5%"><label>Status</label></td>
						<td width="1%" align="center">:</td>
						<td width="10%">
							<select name="ses_status" id="ses_status" tabindex="3" title="Select Status" class="input " >
								<option value="">Select</option>
							<?php $i=1;
									foreach($cardStatus as $key => $card_status) {
										if($i<=2) {?>
								<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_user_status']) && $_SESSION['mgc_sess_user_status'] != '' && $_SESSION['mgc_sess_user_status'] == $key) echo 'Selected';  ?>><?php echo $card_status; ?></option>
							<?php 		} $i++;
									}?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label>Date</label></td>
						<td align="center">:</td>
						<td >
							<input  type="text" autocomplete="off"  maxlength="10" class="input w50" name="giftCard_date" id="giftCard_date" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_giftCard_date']) && $_SESSION['mgc_sess_giftCard_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_giftCard_date'])); else echo '';?>"  onkeypress="return dateField(event);" > (mm/dd/yyyy)
						</td>
						<td><label>Availability</label></td>
						<td align="center">:</td>
						<td>
							<select name="ses_available" id="ses_available" tabindex="3" title="Select Status" class="input " >
								<option value="">Select</option>
							<?php $i=1;
									foreach($cardAvailability as $key => $card_status) {
										if($i<=2) {?>
								<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_available']) && $_SESSION['mgc_sess_available'] != '' && $_SESSION['mgc_sess_available'] == $key) echo 'Selected';  ?>><?php echo $card_status; ?></option>
							<?php 		} $i++;
									}?>
							</select>
						</td>
					</tr>
					<tr><td colspan="9"></td></tr>
					<tr>
						<td align="center" colspan="9" ><input type="submit" tabindex="4" class="submit_button" name="Search" id="Search" value="Search" title="Search"></td>
					</tr>
					<tr><td colspan="9"></td></tr>
				 </table>
				 </form>
			</td></tr>
			<tr><td height="20"></td></tr>
			<tr><td>
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<?php if(isset($giftCardListResult) && is_array($giftCardListResult) && count($giftCardListResult) > 0){ ?>
						<td align="left" width="20%">No. of Gift Card(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
								<?php if(isset($giftCardListResult) && is_array($giftCardListResult) && count($giftCardListResult) > 0 ) {
								 	pagingControlLatest($tot_rec,'GiftCardList'); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td></tr>
			<tr><td height="10"></td></tr>
			<tr><td colspan= '2' align="center">
				<?php displayNotification('Gift Card'); ?>
			</td></tr>
			<tr><td height="10"></td></tr>
			<tr><td>
			<div class="tbl_scroll">

				  <form action="GiftCardList" class="l_form" name="GiftCardListForm" id="GiftCardListForm"  method="post">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
						<tr align="left">
							<th align="center" class="text-center" width="1%"><input onclick="checkAllRecords('GiftCardListForm');" type="Checkbox" name="checkAll"/></th>
							<th align="center" width="1%" class="text-center">#</th>
							<th width="20%"><?php echo SortColumn('CardId','Gift Card'); ?></th>
							<th width="6%">Status</th>
							<th width="6%">Availability</th>
							<th width="3%"><?php echo SortColumn('Amount','Amount (USD)');?></th>
							<th width="6%"><?php echo SortColumn('DateCreated','Date'); ?></th>
						</tr>

						<?php if(isset($giftCardListResult) && is_array($giftCardListResult) && count($giftCardListResult) > 0 ) { ?>

						<?php foreach($giftCardListResult as $key=>$value){
						?>
						<tr id="test_id_<?php echo $value->id;?>"	>
							<td valign="top" align="center"><input id="checkedrecords" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" hashCount="<?php if(isset($value->hash_count) && $value->hash_count > 0 ) echo $value->hash_count; ?>"/></td>
							<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td valign="top">
								<div class="user_profile">
									<p align="left" style="padding-left:5px">
										<?php if(isset($value->GiftCardName) && $value->GiftCardName != ''){ ?>
												<strong>Name : </strong>
										<?php 	echo $value->GiftCardName;
											  }
										?>
									</p>
									<p align="left" style="padding-left:5px">
										<strong>Merchant : </strong><?php if(isset($value->MerchantName) && $value->MerchantName != ''){ echo $value->MerchantName; }else echo '-';?>
									</p>
									<p align="left" style="padding-left:5px;padding-bottom:5px">
									<strong>Card Id : </strong><?php if(isset($value->id) && $value->id != '' ){ echo $value->id;}else echo "-.-";?>
									</p>

									<div class="userAction" style="display:block;min-height:20;" id="GiftCardAction">
										<?php if($value->Visibility == 1)	{	?>
											<a class="userIcon" alt="Hide Card" title="Hide Card" onclick="javascript:return confirm('Are you sure want to change the status?')" href="GiftCardList?editId=<?php echo $value->id;?>&status=2">
											<i class="fa fa-eye fa-lg"></i>&nbsp;</a>
										<?php } else if ($value->Visibility == 2)	{	?>
											<a class="" title="Show Card" alt="Show Card" onclick="javascript:return confirm('Are you sure want to change the status?')" href="GiftCardList?editId=<?php echo $value->id;?>&status=1">&nbsp;
											<i class="fa fa-eye-slash fa-lg"></i>&nbsp;</a>
										<?php } ?>
										<a href="GiftCardManageAdd?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="editUser pop_up"><i class="fa fa-edit fa-lg"></i></a>
										<a onclick="javascript:return confirm('Are you sure to delete?')" href="GiftCardList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="deleteUser">&nbsp;<i class="fa fa-trash-o fa-lg"></i></a>
									</div>
								</div>
							</td>
							<td valign="top"><?php if(isset($value->Visibility) && $value->Visibility != 0	&&	isset($cardVisibility[$value->Visibility])){ echo $cardVisibility[$value->Visibility]; }else echo '-';?></td>
							<td valign="top">
								<?php if(isset($value->Status) && $value->Status != 0	&&	isset($cardAvailability[$value->Status])){ echo $cardAvailability[$value->Status]; }else echo '-';?>
								<?php if($value->CoverImage && $value->CoverImage!==''){ ?>
									<img src="<?php echo($value->CoverImage); ?>" width="100" height"100">
								<?php } ?>
							</td>
							<td valign="top" align="right" style="padding-right:15px;"><?php if(isset($value->Amount) && $value->Amount != ''){ echo '$'.number_format($value->Amount); }else echo '-';?></td>
							<td valign="top" ><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
						</tr>
						<?php } ?>

					<?php if(isset($giftCardListResult) && is_array($giftCardListResult) && count($giftCardListResult) > 0){
							bulk_action($cardStatusArray);
							}
						} else { ?>
						<tr>
							<td valign="middle" colspan="7" align="center" style="color:red;">No Gift Card(s) Found</td>
						</tr>
					<?php } ?>
					</table>
					</form>
				</div>

			</td></tr>
           </table>

<?php commonFooter(); ?>
<script type="text/javascript">
$(".fancybox").colorbox({title:true});
$("#giftCard_date").datepicker({
	showButtonPanel	:	true,
    buttonText		:	'',
    buttonImageOnly	:	true,
    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	maxDate			:	"0",
	closeText		:   "Close"
   });

  $(document).ready(function() {
		$(".pop_up").colorbox(
			{
				iframe:true,
				width:"50%",
				height:"80%",
				title:true,
		});
	});



jQuery(function() {
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[id^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");

    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
</script>
</html>
