<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$cronObj   =   new LogController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
global $link_type_array;
$display	=	"none";
$today		= getCurrentTime('America/New_York','Y-m-d');
$where		=	' ';

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_cron_fromDate']);
	unset($_SESSION['mgc_sess_cron_toDate']);
	unset($_SESSION['mgc_sess_cron_status']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['from_date']) ){
		$validate_date = dateValidation($_POST['from_date']);
		if($validate_date == 1){
			$date = date('Y-m-d',strtotime($_POST['from_date']));
			if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
				$_SESSION['mgc_sess_cron_fromDate']	= $_POST['from_date'];
			else 
				$_SESSION['mgc_sess_cron_fromDate']	= '';//$today;
		}
		else 
			$_SESSION['mgc_sess_cron_fromDate']	= '';//$today;
	}
	else{ 
		$_SESSION['mgc_sess_cron_fromDate']	= '';//$today;
	}
	if(isset($_POST['to_date'])){
		$validate_date = dateValidation($_POST['to_date']);
		if($validate_date == 1){
			$date = date('Y-m-d',strtotime($_POST['to_date']));
			if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
				$_SESSION['mgc_sess_cron_toDate']	= $_POST['to_date'];
			else 
				$_SESSION['mgc_sess_cron_toDate']	= '';//$today;
		}
		else 
			$_SESSION['mgc_sess_cron_toDate']	= '';//$today;
	}
	else{ 
		$_SESSION['mgc_sess_cron_toDate']	= '';//$today;
	}
	$_SESSION['mgc_sess_cron_status']      	= trim($_POST['cron_status']);
	//action_type
}
if(!isset($_SESSION['mgc_sess_cron_toDate'])) 
	$_SESSION['mgc_sess_cron_toDate']	=	$today;//date('Y-m-d');	//=	$today;//
if(!isset($_SESSION['mgc_sess_cron_fromDate'])) 
	$_SESSION['mgc_sess_cron_fromDate']	=	$today;//date('Y-m-d');
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$cronTrackResult	=	$cronObj->cronTrackDetails($where);
$tot_rec 		 = $cronObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($cronTrackResult)) {
	$_SESSION['curpage'] = 1;
$cronTrackResult	=	$cronObj->cronTrackDetails($where);
}
?>
<body>
	<?php top_header(); ?>
						 <div class="box-header"><h2><i class="fa fa-list"></i>Cron Tracking</h2></div>
				            <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
								
								<tr>
									<td colspan="2">
										<form name="search_category" action="CronTracking" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>													
													<td width="10%" style="padding-left:20px;"><label>Start Date</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40" >
														<input  type="text"  style="width:290px;" class="input medium datepicker w50" autocomplete="off"  title="Select Date" id="startdate" name="from_date" value="<?php if(isset($_SESSION['mgc_sess_cron_fromDate']) && $_SESSION['mgc_sess_cron_fromDate'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_cron_fromDate']));?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
													</td>
													<td width="10%" style="padding-left:20px;"><label>End Date</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" style="width:290px;" class="input medium datepicker w50" autocomplete="off"   title="Select Date" id="enddate" name="to_date" value="<?php if(isset($_SESSION['mgc_sess_cron_toDate']) && $_SESSION['mgc_sess_cron_toDate'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_cron_toDate']));?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
													</td>
													<td  width="10%" style="padding-left:20px;"><label>Status</label></td>
													<td width="2%" align="center">:</td>
													<td  >
														<select name="cron_status" id="ses_status"   tabindex="2" title="Select Status" class="w150">
															<option value="">Select</option>
														<?php	foreach($cronStatus as $key => $cron_status) { ?>
															<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_cron_status']) && $_SESSION['mgc_sess_cron_status'] != '' && $_SESSION['mgc_sess_cron_status'] == $key) echo 'Selected';  ?>><?php echo $cron_status; ?></option>
														<?php }?>
														</select>
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
												<?php if(isset($cronTrackResult) && is_array($cronTrackResult) && count($cronTrackResult) > 0){ ?>
												<td align="left" width="20%">No. of Cron(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(isset($cronTrackResult)	&&	is_array($cronTrackResult) && count($cronTrackResult) > 0 ) {
														 	pagingControlLatest($tot_rec,'CronTracking'); ?>
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
											<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
												<tr>
													<th width="1%" align="center" class="text-center">#</th>
													<th width="15%">Cron</th>
													<th width="10%">Start Date</th>
													<th width="10%">End Date</th>
													<th width="5%">Status</th>
												</tr>
												<?php if(isset($cronTrackResult) && is_array($cronTrackResult) && count($cronTrackResult) > 0 ) { ?>
												
												<?php foreach($cronTrackResult as $key=>$value){ ?>									
												<tr>
													<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
													<td align="left" ><?php if(isset($value->Name)	&&	$value->Name !='') echo $value->Name; else echo '-';?></td>
													<td valign="top"><?php if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00'){ 
													echo date('m/d/Y H:i:s',strtotime($value->StartDate)); 
													}else echo '-';?></td>
													<td valign="top"><?php if(isset($value->EndDate) && $value->EndDate != '0000-00-00 00:00:00'){ 
													echo date('m/d/Y H:i:s',strtotime($value->EndDate)); 
													}else echo '-';?></td>
													<td align="left" ><?php 
															if(isset($value->Status)	&&	$value->Status !=''	&&	isset($cronStatus[$value->Status])) 
															echo $cronStatus[$value->Status]; 
															else echo '-';
														?>
													</td>
														
												</tr>
												<?php } ?>
												
												<?php } else { ?>	
													<tr>
														<td colspan="16" align="center" style="color:red;">No Cron(s) Found</td>
													</tr>
													
												<?php    } ?>
												
												</table>
												</form>
											</div>
											
										</td>
									</tr>
							<tr><td height="30"></td></tr>	
				            </table>
<?php commonFooter(); ?>
<script type="text/javascript">
$("#startdate").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
	onSelect		: function (dateText, inst) {
						$('#enddate').datepicker("option", 'minDate', new Date(dateText));
						},
    onClose			: function () { $(this).focus(); },

    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	closeText		:   "Close"
 });
 $("#enddate").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
	onSelect		: function () { },
    onClose			: function () { $(this).focus(); },
    buttonImage		:	path+'webresources/images/calender.png',
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
