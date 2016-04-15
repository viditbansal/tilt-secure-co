<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/ReportController.php');
$reportObj  		=   new ReportController();
developer_login_check();
$devId		=	$_SESSION['tilt_developer_id'];
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	if(isset($_SESSION['tilt_sess_purchase_amount']))
		unset($_SESSION['tilt_sess_purchase_amount']);
	if(isset($_SESSION['tilt_sess_purchase_date']))
		unset($_SESSION['tilt_sess_purchase_date']);
}
if(isset($_POST['Search'])	&&	$_POST['Search']=='Search')	{
	if(isset($_POST['amount']))	
		$_SESSION['tilt_sess_purchase_amount']	=	trim($_POST['amount']);
	if(isset($_POST['purchase_date']))	
		$_SESSION['tilt_sess_purchase_date']	=	trim($_POST['purchase_date']);
}
$fields		=	' id,Coins,Amount,CustomerToken,CustomerEmail,CreatedDate';
$condition	=	' fkDeveloperId = '.$devId;
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$purchaseListResult		=	$reportObj->getPurchaseList($fields,$condition);
$tot_rec				=	$reportObj->getTotalRecordCount();
commonHead();
?>
<body class="skin-black"  >
<?php top_header(); ?>
	<section class="content-header">
		<h2 align="center">Purchase History</h2>
	</section>
   	<section class="content">
		<div class="search_group">
			<form method="post" action="PurchaseStats">
				<div class="box-body col-xs-12 col-sm-6 col-md-6 col-lg-4 box-center">
					<div class="form-group col-xs-6 col-sm-6 col-sm-6">
						<input  type="text" class="form-control" placeholder="Amount($)" title="Amount($)" name="amount" id="amount" maxlength="10" value="<?php if(isset($_SESSION['tilt_sess_purchase_amount']) && $_SESSION['tilt_sess_purchase_amount'] != '') echo $_SESSION['tilt_sess_purchase_amount'];?>" onkeypress="return isFloatNumber(event,this)" >
					</div>
					<div class="form-group col-xs-6 col-sm-6 col-sm-6">
						<input  type="text" class="form-control" placeholder="Purchased Date" onkeypress="return dateField(event);" autocomplete="off"  title="Purchased Date" id="purchase_date" name="purchase_date" value="<?php if(isset($_SESSION['tilt_sess_purchase_date']) && $_SESSION['tilt_sess_purchase_date'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_purchase_date']));?>">
					</div>
				</div>
				<div class="box-footer clear" align="center">
					<input type="submit"  class="btn btn-green" name="Search" id="Search" title="Search" value="Search">
				</div>
			</form>
		</div>
		<div class="clear" style="height: 20px"></div>
		<div class="col-lg-3 col-sm-12">
				<?php if(isset($purchaseListResult) && is_array($purchaseListResult) && count($purchaseListResult) > 0){ ?>
				Total Purchase(s) &nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div class="clear" style="height: 20px"></div>
		<div class="col-xs-12">
		<div class="table-responsive">
			
			<table cellpadding="0" cellspacing="0" align="center" border="0" class="table table-striped table-responsive" width="100%">
				<tr>
					<th align="center" width="2%">#</th>
					<th align="left" width="15%">Token</th>
					<th align="left" width="10%">Amount($)</th>
					<th align="left" width="6%">TiLT$</th>
					<th align="left" width="9%">Email</th>
					<th align="center" width="3%">Purchased Date</th>
				</tr>
				<?php if(!empty($purchaseListResult)) {
					foreach($purchaseListResult as $key=>$value)	{	?>
				<tr>
					<td width="" align="center" valign="">
						<?php if(isset($_SESSION['curpage'])	&&	$_SESSION['curpage'] !=''	&&	isset($_SESSION['perpage'])	&&	$_SESSION['perpage'] !='')echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?>
					</td>
					<td align="left"><?php if(isset($value->CustomerToken)	&&	$value->CustomerToken!=''){	echo $value->CustomerToken; } else { echo " - "; } ?></td>
					<td align="left" style="padding-right:20px;"><?php if(isset($value->Amount)	&&	$value->Amount!=''){	echo '$'.number_format($value->Amount,'2'); } else { echo " - "; } ?></td>
					<td align="left"><?php if(isset($value->Coins)	&&	$value->Coins!=''){	echo number_format($value->Coins); } else { echo " - "; } ?></td>
					<td align="left"><?php if(isset($value->CustomerEmail)	&&	$value->CustomerEmail!=''){	echo $value->CustomerEmail; } else { echo " - "; } ?></td>
					<td  align="left">	<?php 	if(isset($value->CreatedDate) && $value->CreatedDate != '0000-00-00 00:00:00')	{ echo date('m/d/Y',strtotime($value->CreatedDate)); 	} else echo '-';?></td>
				</tr>
			<?php } 
			}
			else { ?>
				<tr><td align="center" colspan="13" class="error">No Purchase Detail(s) Found</td></tr>
			<?php } ?>
		</table>
		
		
	</div>
	</div>
	<div class="col-xs-12 clear">
		<?php if(isset($purchaseListResult) && is_array($purchaseListResult) && count($purchaseListResult) > 0 ) {
			pagingControlLatest($tot_rec,'PurchaseStats'); ?>
		<?php }?>
	</div>
</section>

	

<?php footerLinks(); commonFooter(); ?>
<script>
$('#purchase_date').datetimepicker({
		format:'m/d/Y',
		maxDate:'today',
		timepicker:false,
		scrollInput:false
	});
</script>
</html>