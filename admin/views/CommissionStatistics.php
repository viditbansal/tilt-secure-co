<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$coinStatObj   =   new LogController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
top_header();

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_commission_brand']);
	unset($_SESSION['mgc_sess_commission_user']);
	unset($_SESSION['mgc_sess_commission_date']);

	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['brand']))
		$_SESSION['mgc_sess_commission_brand'] 	=	trim($_POST['brand']);
	if(isset($_POST['purchased_user']))
		$_SESSION['mgc_sess_commission_user'] 	=	trim($_POST['purchased_user']);
	if(isset($_POST['purchased_date']) && $_POST['purchased_date'] != ''){
		$validate_date = dateValidation($_POST['purchased_date']);
		if($validate_date == 1)
			$_SESSION['mgc_sess_commission_date']	= $_POST['purchased_date'];	//$date;
		else 
			$_SESSION['mgc_sess_commission_date']	= '';
	}
	else 
		$_SESSION['mgc_sess_commission_date']	= '';
}
$tot_rec	=	"";
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " gp.id,gp.fkDeveloperId,gp.Amount,gp.CreatedDate,gp.Coins,gp.Commission,gd.Company,gd.Email,gd.UserName,gd.Status ";
$condition = " 1 ";

$coinListResult  	= $coinStatObj->getCommissionList($fields,$condition);
$tot_rec 		 	= $coinStatObj->getTotalRecordCount();
$totalAmount		=	0;
$fields    			= " sum(gp.Commission) as Amount ";
$condition 			= " 1 ";
$purchaseAmountResult  	= $coinStatObj->getTotalCommissionAmount($fields,$condition);
if(isset($purchaseAmountResult)	&&	is_array($purchaseAmountResult)	&&	count($purchaseAmountResult) >0 )
	$totalAmount	=	$purchaseAmountResult[0]->Amount;
?>
<body >
	<div class="box-header"><h2><i class="fa fa-list"></i>Commission Statistics</h2>
		<h2 style="float:right" >Total Amount&nbsp;(USD) :&nbsp;<span class="total_value">
								<?php if(isset($totalAmount)	&&	$totalAmount !='') echo  '$'.number_format ($totalAmount,2); else echo '0';?>
								</span>
		</h2>
	</div>
	<div class="clear">
		<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" >
			
			
			<tr>
				<td valign="top" align="center" colspan="2">
					
					<form name="search_category" action="CommissionStatistics" method="post">
					   <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
							<tr>
								<td height="15" align="right">
								</td>
							</tr>
							<tr>													
								<td width="10%" style="padding-left:20px;"><label>Developer & Brand</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<input type="text" class="input" name="brand" id="brand"  value="<?php  if(isset($_SESSION['mgc_sess_commission_brand']) && $_SESSION['mgc_sess_commission_brand'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_commission_brand']);  ?>" >
								</td>
								<td  style="padding-left:20px;" width="10%"><label>Purchased On</label></td>
								<td width="2%" align="center">:</td>
								<td >
									<input  type="text" autocomplete="off"  maxlength="10" class="input w50" name="purchased_date" id="purchased_date" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_commission_date']) && $_SESSION['mgc_sess_commission_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_commission_date'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
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
							<?php if(isset($coinListResult) && is_array($coinListResult) && count($coinListResult) > 0){ ?>
							<td align="left" width="20%">No. of Commission(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
							<?php } ?>
							<td align="center">
									<?php if(isset($coinListResult) && is_array($coinListResult) && count($coinListResult) > 0 ) {
											pagingControlLatest($tot_rec,'CommissionStatistics'); ?>
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
				<form action="PurchaseCoinStatistics" class="l_form" name="CoinStatisticsForm" id="CoinStatisticsForm"  method="post"> 
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">

						<tr align="left">
							<th align="center" width="1%" class="text-center">#</th>
							<th width="27%"><?php echo SortColumn('Company','Developer & Brand'); ?></th>
							<th width="12%"><?php echo SortColumn('gp.CreatedDate','Purchased On'); ?></th>
							<th width="8%"><?php echo SortColumn('Amount','Purchased (USD)'); ?></th>
							<th width="5%"><?php echo SortColumn('Coins','TiLT$'); ?></th>
							<th width="12%" ><?php echo SortColumn('Commission','Commission (USD)'); ?></th>
						</tr>
						<?php if(isset($coinListResult) && is_array($coinListResult) && count($coinListResult) > 0 ) { 
								$subTotal	=	0;
								foreach($coinListResult as $key=>$value){ 
									if(isset($value->Commission) && $value->Commission != '')
										$subTotal	+=	$value->Commission;
								 ?>
						<tr>
							<td valign="top" align="center" ><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td valign="top" >
								<?php 	if(isset($value->Company) && $value->Company != ''){
										if(isset($value->Status) && $value->Status != 3)
											echo '<a href="GameDeveloperDetail?viewId='.$value->fkDeveloperId.'&back=CommissionStatistics" >'.$value->Company.'</a>';
										else
											echo $value->Company;
									}else echo '-';?>
							</td>
							<td valign="top" ><?php if(isset($value->CreatedDate) && $value->CreatedDate != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->CreatedDate)); }else echo '-';?></td>
							<td valign="top" align="right" style="padding-right:15px;"><?php if(isset($value->Amount) && $value->Amount != ''){ echo  '$'.number_format ($value->Amount,2); }else echo '0';?></td>
							<td valign="top" align="right" style="padding-right:15px;"><?php if(isset($value->Coins) && $value->Coins != ''){ echo  number_format ($value->Coins); }else echo '0';?></td>
							<td valign="top" align="right" style="padding-right:15px;"><?php if(isset($value->Commission) && $value->Commission != ''){ echo  '$'.number_format ($value->Commission,2); }else echo '0';?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="5" align="right"><strong>Sub Total&nbsp;(USD)&nbsp;</strong></td>
							<td align="right" class="show_amount" style="padding-right:15px;"><strong><?php echo  '$'.number_format ($subTotal,2);?></strong></td>
						</tr>	
					</table>
				</form>
					<?php } else { ?>	
						<tr>
							<td colspan="16" align="center" style="color:red;">No Commission(s) Found</td>
						</tr>
					</table>
					<?php } ?>
					</div>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
	</div>
<?php commonFooter(); ?>
<script type="text/javascript">
$("#purchased_date").datepicker({
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
