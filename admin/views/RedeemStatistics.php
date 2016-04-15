<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$redeemStatObj   =   new LogController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
top_header();

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_redeemed_giftcard']);
	unset($_SESSION['mgc_sess_redeemed_user']);
	unset($_SESSION['mgc_sess_redeemed_date']);

	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['giftcard']))
		$_SESSION['mgc_sess_redeemed_giftcard'] 	=	trim($_POST['giftcard']);
	if(isset($_POST['redeemed_user']))
		$_SESSION['mgc_sess_redeemed_user'] 	=	trim($_POST['redeemed_user']);
	if(isset($_POST['redeemed_date']) && $_POST['redeemed_date'] != ''){
		$validate_date = dateValidation($_POST['redeemed_date']);
		if($validate_date == 1)
			$_SESSION['mgc_sess_redeemed_date']	= $_POST['redeemed_date'];
		else 
			$_SESSION['mgc_sess_redeemed_date']	= '';
	}
	else 
		$_SESSION['mgc_sess_redeemed_date']	= '';
}
$tot_rec	=	"";
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " r.id,r.CoinsUsed,r.fkUsersId,r.ConversionValue,gc.CardId,gc.GiftCardName,gc.MerchantName,gc.Amount,r.DateCreated,u.FirstName,u.LastName,u.Status,u.UniqueUserId ";
$condition = " 1 ";
$redeemListResult  	= $redeemStatObj->redeemList($fields,$condition);
$tot_rec 		 	= $redeemStatObj->getTotalRecordCount();
$fields    = " sum(CoinsUsed) as coins ";
$condition = " 1 ";
$totalCoinsResult  	= $redeemStatObj->getTotalCoins($fields,$condition);
if(isset($totalCoinsResult)	&&	is_array($totalCoinsResult)	&&	count($totalCoinsResult) >0 )
	$totalCoins	=	$totalCoinsResult[0]->coins;
?>
<body >
	<div class="box-header"><h2><i class="fa fa-list"></i>Redeem Statistics</h2>
		<h2 style="float:right">Total TiLT$&nbsp;:&nbsp;<span class="total_value"><?php if(isset($totalCoins)	&&	$totalCoins !='') echo  number_format($totalCoins); else echo '0';?></span></h2>
	</div>
		<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" >
			
			
			<tr>
				<td valign="top" align="center" colspan="2">
					
					<form name="search_category" action="RedeemStatistics" method="post">
					   <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
							<tr>
								<td height="15" align="right"><label></label>
								</td>
							</tr>
							<tr>													
								<td width="10%" style="padding-left:20px;"><label>Gift Card</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<input type="text" class="input" name="giftcard" id="giftcard"  value="<?php  if(isset($_SESSION['mgc_sess_redeemed_giftcard']) && $_SESSION['mgc_sess_redeemed_giftcard'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_redeemed_giftcard']);  ?>" >
								</td>
								<td width="10%" style="padding-left:20px;" ><label>User</label></td>
								<td width="2%" align="center">:</td>
								<td height="40" align="left" >
									<input type="text" class="input" name="redeemed_user" id="redeemed_user"  value="<?php  if(isset($_SESSION['mgc_sess_redeemed_user']) && $_SESSION['mgc_sess_redeemed_user'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_redeemed_user']);  ?>" >
								</td>
								<td  style="padding-left:20px;" width="10%"><label>Redeemed On</label></td>
								<td width="2%" align="center">:</td>
								<td >
									<input  type="text" autocomplete="off"  maxlength="10" style="width: 80px" class="input" name="redeemed_date" id="redeemed_date" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_redeemed_date']) && $_SESSION['mgc_sess_redeemed_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_redeemed_date'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
								</td>
							</tr>
							<tr><td align="center" colspan="9" style="padding-top:20px"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
							<tr><td height="15"></td></tr>
						 </table>
					  </form>	
				</td>
			</tr>
			<tr><td height="20"></td></tr>
		
			<tr>
				<td colspan="2">
					<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
						<tr>
							<?php if(isset($redeemListResult) && is_array($redeemListResult) && count($redeemListResult) > 0){ ?>
							<td align="left" width="20%">No. of Redeem(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
							<?php } ?>
							<td align="center">
									<?php if(isset($redeemListResult) && is_array($redeemListResult) && count($redeemListResult) > 0 ) {
											pagingControlLatest($tot_rec,'RedeemStatistics'); ?>
									<?php }?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td colspan="2">
				
			<div class="tbl_scroll">
				<form action="TournamentStatistics" class="l_form" name="TournamentStatisticsForm" id="TournamentStatisticsForm"  method="post"> 
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">

						<tr align="left">
							<th align="center" width="1%" style="text-align:center">#</th>
							<th width="30%"><?php echo SortColumn('GiftCardName','Gift Card'); ?></th>
							<th width="12%"><?php echo SortColumn('Amount','Card Value (USD)'); ?></th>
							<th width="12%"><?php echo SortColumn('ConversionValue','Conversion Value'); ?></th>
							<th width="20%"><?php echo SortColumn('FirstName','User'); ?></th>
							<th width="12%"><?php echo SortColumn('r.DateCreated','Redeemed On'); ?></th>
							<th width="6%"><?php echo SortColumn('r.CoinsUsed','TiLT$'); ?></th>
						</tr>
						<?php if(isset($redeemListResult) && is_array($redeemListResult) && count($redeemListResult) > 0 ) { 
								$subCoinsTotal	=	0;
								foreach($redeemListResult as $key=>$value){ 
									if(isset($value->CoinsUsed) && $value->CoinsUsed != '')
										$subCoinsTotal	+=	$value->CoinsUsed;
									$userName	=	'-';
									if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
										$userName = 'Guest'.$value->id;
									else if(isset($value->FirstName)	&&	isset($value->LastName)) 	
										$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
									else if(isset($value->FirstName))	
										$userName	=	 ucfirst($value->FirstName);
									else if(isset($value->LastName))	
										$userName	=	ucfirst($value->LastName);
								 ?>
						<tr>
							<td valign="top" align="center" ><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td valign="top" >
								<?php if(isset($value->GiftCardName) && $value->GiftCardName != ''){?>
								<p><strong>Name&nbsp;:&nbsp;</strong><?php echo $value->GiftCardName; ?></p>
								<?php } ?>
								<p><strong>Merchant&nbsp;:&nbsp;</strong><?php if(isset($value->MerchantName) && $value->MerchantName != ''){ echo $value->MerchantName; }else echo '-';?></p>
								<p><strong>Card Id&nbsp;:&nbsp;</strong><?php if(isset($value->CardId) && $value->CardId != ''){ echo $value->CardId; }else echo '-';?></p>
							</td>
							<td valign="top" align="right" style="padding-right:15px;"><?php if(isset($value->Amount) && $value->Amount != ''){ echo '$'.number_format($value->Amount); }else echo '0';?></td>
							<td valign="top" align="right" style="padding-right:15px;"><?php if(isset($value->ConversionValue) && $value->ConversionValue != ''){ echo ($value->ConversionValue); } ?></td>
							<td valign="top" >
							<?php 	if(isset($userName) && $userName != ''){ 
										 if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
											echo $userName;
										 else if(isset($value->Status) && $value->Status != 3)
											echo '<a href="UserDetail?viewId='.$value->fkUsersId.'&back=RedeemStatistics" >'.$userName.'</a>';
										 else
											echo $userName; 
									}else echo '-';?></td>
							<td valign="top" ><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
							<td valign="top" align="right" style="padding-right:15px;"><?php if(isset($value->CoinsUsed) && $value->CoinsUsed != ''){ echo number_format($value->CoinsUsed); }else echo '0';?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="6" align="right"><strong>SubTotal TiLT$</strong></td>
							<td align="right" style="padding-right:15px;"><strong><?php echo number_format($subCoinsTotal);?></strong></td>
						</tr>	
					</table>
				</form>
					<?php } else { ?>	
						<tr>
							<td colspan="16" align="center" style="color:red;">No Redeem(s) Found</td>
						</tr>
					</table>
					<?php } ?>
					</div>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
<?php commonFooter(); ?>
<script type="text/javascript">
//redeemed_date
$("#redeemed_date").datepicker({
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
