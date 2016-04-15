<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$iapObj   =   new LogController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
global $link_type_array;
$where		=	' ';
$userName	=	' ';

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_iaptrack_date']);
	unset($_SESSION['mgc_sess_iaptrack_userName']);
	unset($_SESSION['mgc_sess_iaptrack_receiptId']);
	unset($_SESSION['mgc_sess_iaptrack_price']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['userName'])){
		$_SESSION['mgc_sess_iaptrack_userName']	=	trim($_POST['userName']);
	}
	if(isset($_POST['recieptId'])){
		$where .= " and c.TransactionReceiptId LIKE '%".$_POST['recieptId']."%' ";
		$_SESSION['mgc_sess_iaptrack_receiptId']	=	trim($_POST['recieptId']);
	}
	
	if(isset($_POST['packagePrice'])){
		$_SESSION['mgc_sess_iaptrack_price'] = trim($_POST['packagePrice']);
	}
	if(isset($_POST['createdDate']) && $_POST['createdDate'] != ''){
		$validate_date = dateValidation($_POST['createdDate']);
		if($validate_date == 1){
			$date = date('Y-m-d',strtotime($_POST['createdDate']));
			if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
				$_SESSION['mgc_sess_iaptrack_date']	= $_POST['createdDate'];
			else 
				$_SESSION['mgc_sess_iaptrack_date']	= '';
		}
		else 
			$_SESSION['mgc_sess_iaptrack_date']	= '';
	}
	else{ 
		$_SESSION['mgc_sess_iaptrack_date']	= '';
	}

}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$iapTrackResult	=	$iapObj->iapTrackDetails($where);
$tot_rec 		 = $iapObj->getTotalRecordCount();
if($tot_rec != 0 && !is_array($iapTrackResult)) {
	$_SESSION['curpage'] = 1;
	$iapTrackResult	=	$iapObj->iapTrackDetails($where);
}
?>
<body>
	<?php top_header(); ?>
						 <div class="box-header"><h2><i class="fa fa-list"></i>IAP Tracking</h2></div>
				            <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
								
								<tr>
									<td colspan="2">
										<form name="search_category" action="IAPTracking" method="post" id="search_form">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr height="100">													
													<td width="7%" style="padding-left:20px;"><label>User Name</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40" >
														<input  type="text"   class="input" autocomplete="off"  title="User Name" id="userName" name="userName" value="<?php 
														if(isset($_SESSION['mgc_sess_iaptrack_userName']) && !empty($_SESSION['mgc_sess_iaptrack_userName'])) echo unEscapeSpecialCharacters($_SESSION['mgc_sess_iaptrack_userName']);?>">
													</td>
													<td width="7%" style="padding-left:20px;"><label>Reciept Id</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text"  class="input" autocomplete="off"   title="Reciept Id" id="recieptId" name="recieptId" value="<?php
														if(isset($_SESSION['mgc_sess_iaptrack_receiptId']) && !empty($_SESSION['mgc_sess_iaptrack_receiptId'])) echo unEscapeSpecialCharacters($_SESSION['mgc_sess_iaptrack_receiptId']);?>">
													</td>
													<td width="7%" style="padding-left:20px;"><label>Package Price</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text"  class="input" autocomplete="off"   title="Purchase Price" id="packagePrice" name="packagePrice" value="<?php 
														if(isset($_SESSION['mgc_sess_iaptrack_price']) && $_SESSION['mgc_sess_iaptrack_price'] !='') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_iaptrack_price']);?>">
													</td>
													<td width="7%" style="padding-left:20px;"><label>Created Date</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40" width="10%">
														<input type="text"   class="input" autocomplete="off"   title="Created Date" id="createdDate" name="createdDate" value="<?php 
														if(isset($_SESSION['mgc_sess_iaptrack_date']) && $_SESSION['mgc_sess_iaptrack_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_iaptrack_date']));?>" onkeypress="return dateField(event);"> 
													</td>
													<td width="10"></td>
													</tr>
													<tr><td height="10"></td></tr>
													<tr>
														<td align="center" colspan="12" ><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td>
													</tr>
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
												<?php if(isset($iapTrackResult) && is_array($iapTrackResult) && count($iapTrackResult) > 0){ ?>
												<td align="left" width="20%">No. of Purchase(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(isset($iapTrackResult)	&&	is_array($iapTrackResult) && count($iapTrackResult) > 0 ) {
														 	pagingControlLatest($tot_rec,'IAPTracking'); ?>
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
											  <form action="CronTracking" class="l_form" name="CronTrackingForm" id="CronTrackingForm"  method="post"> 
											<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions" id="fixed">
												<col width="1%" />
   												<col width="10%" />
    											<col width="10%" />
												<col width="20%" />
												<col width="10%" />
												<col width="5%" />
												<tr>
													<th width="1%" align="center" style="text-align: center">#</th>
													<th width="10%">User Name</th>
													<th width="10%">Reciept Id</th>
													<th width="30%">Transaction Reciept</th>
													<th width="10%">Package Price</th>
													<th width="5%">Created Date</th>
												</tr>
												<?php if(isset($iapTrackResult) && is_array($iapTrackResult) && count($iapTrackResult) > 0 ) { ?>
												
												<?php foreach($iapTrackResult as $key=>$value){ ?>									
												<tr>
													<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
													<td align="left" ><?php  if(isset($value->FirstName) && $value->FirstName != '') $userName .= ucfirst($value->FirstName);
																			 if(isset($value->LastName) && $value->LastName != '') 	$userName .= " ".ucfirst($value->LastName);
																			 
																			 if(isset($value->UniqueUserId) && $value->UniqueUserId != ''){
																				echo "Guest".$value->userId;
																				$userName='';
																			 }else if(isset($userName) && $userName!='' && isset($value->userId) && $value->userId !=''){	
																				if($value->Status == 3)
																					echo $userName;
																				else
																					echo '<a href="UserDetail?viewId='.$value->userId.'&back=IAPTracking">'.$userName.'</a>';	
																				$userName='';
																			 }
																			 else echo '-';?></td>
													<td valign="top"><?php if(isset($value->TransactionReceiptId) && $value->TransactionReceiptId != ''){ 
													echo  $value->TransactionReceiptId;
													}else echo '-';?></td>
													<td valign="top" class="brk_wrd"><?php if(isset($value->TransactionReceipt) && $value->TransactionReceipt != ''){ 
													echo  displayText($value->TransactionReceipt,200);
													}else echo '-';?></td>
													<td align="left" ><?php 
															if(isset($value->PackagePrice)	&&	$value->PackagePrice !='') 
															echo $value->PackagePrice; 
															else echo '-';
														?>
													</td>
													<td valign="top"><?php if(isset($value->CreatedDate) && $value->CreatedDate != '0000-00-00 00:00:00'){ 
													echo date('m/d/Y',strtotime($value->CreatedDate)); 
													}else echo '-';?></td>
														
												</tr>
												<?php } ?>
												
												<?php } else { ?>	
													<tr>
														<td colspan="16" align="center" style="color:red;">No Purchase(s) Found</td>
													</tr>
													
												<?php    } ?>
												
												</table>
												</form>
											</div>
											
										</td>
									</tr>
								
				            </table>
<?php commonFooter(); ?>
<script type="text/javascript">
$("#createdDate").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
    onClose			: 	function () { $(this).focus(); },
    buttonImage		:	'../webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	closeText		:   "Close"
 });
 </script>
</html>
