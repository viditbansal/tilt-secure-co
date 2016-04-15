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
$class  =  $msg    = $cover_path = $userName =  $userId = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_merchant_name']);
	unset($_SESSION['mgc_sess_amount']);	
	unset($_SESSION['mgc_sess_merchant_registerdate']);
	unset($_SESSION['mgc_sess_tournament_purchase']);
	
	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
if(isset($_GET['user_back']) && $_GET['user_back']=='1') {
	unset($_SESSION['ordertype']);
	unset($_SESSION['sortBy']);
	unset($_SESSION['orderby']);

}
if(isset($_GET['userName'])	&&	$_GET['userName']!= '')
	$userName	=	$_GET['userName'];
if(isset($_GET['userId'])	&&	$_GET['userId'] != '')
	$userId		=	$_GET['userId'];
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['ses_merchantname']))
		$_SESSION['mgc_sess_merchant_name'] 	=	trim($_POST['ses_merchantname']);
	if(isset($_POST['ses_amount']))
		$_SESSION['mgc_sess_amount']	    	=	trim($_POST['ses_amount']);
	if(isset($_POST['purchasedate']))
		$_SESSION['mgc_sess_tournament_purchase']	=	$_POST['purchasedate'];
	
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);

$fields    = " gc.*,r.CoinsUsed,r.DateCreated ";
$condition = " and gc.Status != 0 AND r.Status = 1 AND r.fkUsersId = ".$userId;
$giftCardListResult  = $giftObj->getUserGiftCardList($fields,$condition);
$tot_rec 		 = $giftObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($giftCardListResult)) {
	$_SESSION['curpage'] = 1;
	$giftCardListResult  = $giftObj->getUserGiftCardList($fields,$condition);
}
$totalCoins	=	$totalAmount	=	0;
if(isset($giftCardListResult) && is_array($giftCardListResult) && count($giftCardListResult) > 0 ) { 
	foreach($giftCardListResult as $key=>$value){
		if(isset($value->CoinsUsed) && $value->CoinsUsed != ''){
			$totalCoins	+=	$value->CoinsUsed;
		}
		if(isset($value->Amount) && $value->Amount != ''){
			$totalAmount	+=	$value->Amount;
		}
	}
}
$action	=	'UserPurchaseList?userId='.$userId.'&userName='.$userName;
if(isset($_GET['totalCoins']) && $_GET['totalCoins'] != ''){
	$action	.=	'&totalCoins='.$_GET['totalCoins'];
	$totalCoins		=	$_GET['totalCoins'];
}
?>
<body>
						 
	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i><?php echo $userName; ?> - Redeemed List</h2>
		<h2 style="float:right" >Total TiLT$ Used&nbsp;:&nbsp;<span class="total_value"><?php if(isset($totalCoins)	&&	$totalCoins !='') echo  number_format($totalCoins); else echo '0';?></span></h2>
	</div>
	<div class="clear">
           <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
			
			<tr><td class="filter_form" >
				<form name="search_category" action="<?php echo $action; ?>" method="post">
            	<table align="center" cellpadding="6" cellspacing="0" border="0"width="98%">									       
					<tr><td colspan="12"></td></tr>
					<tr>													
						<td width="5%" align='right'><label> Merchant Name </label></td>
						<td width="1%" align="center">:</td>
						<td align="left"  width="20%">
							<input type="text" tabindex="1" class="input" name="ses_merchantname" id="ses_merchantname"  value="<?php  if(isset($_SESSION['mgc_sess_merchant_name']) && $_SESSION['mgc_sess_merchant_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_merchant_name']);  ?>" >
						</td>
						<td  width="5%" align='right'><label>Amount&nbsp;(USD)</label></td>
						<td width="1%" align="center">:</td>
						<td align="left"  width="15%" >
							<input type="text" tabindex="2" class="input" id="ses_amount" name="ses_amount"  onkeypress="return isNumberKey(event);" value="<?php  if(isset($_SESSION['mgc_sess_amount']) && $_SESSION['mgc_sess_amount'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_amount']);  ?>" >
						</td>

						<td  width="3%" align='right'><label>Redeemed Date</label></td>
						<td width="1%" align="center">:</td>
						<td width="25%" align='left'>
							<input style="width:90px;" type="text" autocomplete="off" maxlength="10" class="input datepicker" name="purchasedate" id="purchasedate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_tournament_purchase']) && $_SESSION['mgc_sess_tournament_purchase'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_tournament_purchase'])); else echo '';?>" > (mm/dd/yyyy)
						</td>						
					</tr>					
					<tr>
						<td align="center" colspan="12" ><input type="submit" tabindex="4" class="submit_button" name="Search" id="Search" value="Search" title="Search"></td>
					</tr>								       
					<tr><td colspan="12"></td></tr>
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
								 	pagingControlLatest($tot_rec,$action); 
								}
								?>
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
							<th align="center" width="3%" class="text-center">#</th>												
							<th width="30%"><?php echo SortColumn('CardId','Gift Card'); ?></th>
							<th width="5%"><?php echo SortColumn('Amount','Amount (USD)');?></th>
							<th width='5%'><?php echo SortColumn('CoinsUsed','Used TiLT$');?></th>
							<th width='5%'>Redeemed Date</th>
						</tr>
						<?php if(isset($giftCardListResult) && is_array($giftCardListResult) && count($giftCardListResult) > 0 ) { ?>
						<?php foreach($giftCardListResult as $key=>$value){
						 ?>									
						<tr id="test_id_<?php echo $value->id;?>"	>
							<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td valign="top">
								<div class="user_profile">
									<p align="left" style="padding-left:5px">
										<?php if(isset($value->GiftCardName) && $value->GiftCardName != ''){ ?>
												<strong>Name : </strong>
										<?php 	echo $value->GiftCardName; 
											  } ?>
									</p>
									<p align="left" style="padding-left:5px">
										<strong>Merchant : </strong><?php if(isset($value->MerchantName) && $value->MerchantName != ''){ echo $value->MerchantName; }else echo '-';?>
									</p>
									<p align="left" style="padding-left:5px">
										<strong>Card Id : </strong><?php if(isset($value->CardId) && $value->CardId != '' ){ echo $value->CardId;}else echo "-";?>
									</p>							
								</div>
							</td>
							<td valign="top" align="left" style="padding-right:15px;"><?php if(isset($value->Amount) && $value->Amount != ''){ echo '$'.number_format($value->Amount); }else echo '-';?></td>		
							<td valign="top" align="left" ><?php if(isset($value->CoinsUsed) && $value->CoinsUsed != ''){ echo number_format($value->CoinsUsed); }else echo '-';?></td>
							<td valign="top"><?php if(isset($value->DateCreated) && $value->DateCreated != ''){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>							
							</tr>
						<?php } ?> 
						<?php } else { ?>
							<tr><td colspan="5" align="center" style="color:red;">No Card(s) Found</td></tr>
						<?php } ?>
					</table>
					</form>
						
						
				</div>
			</td></tr>
			<tr><td height="20"></td></tr>
           </table>
       </div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".fancybox").colorbox({title:true});	
$("#purchasedate").datepicker({
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
		$(".purchase_pop_up").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"80%",
				title:true,
		});
	});	
	
$(function(){

   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '550';
   var maxWidth  = '1100';
   if(bodyHeight<maxHeight) {   	
   	setHeight = bodyHeight;
   } else {
   		setHeight = maxHeight;
   }
   if(bodyWidth>maxWidth) {
   		setWidth = bodyWidth;
   } else {
   		setWidth = maxWidth;
   }
   parent.$.colorbox.resize({
        innerWidth :setWidth,
        innerHeight:setHeight
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
