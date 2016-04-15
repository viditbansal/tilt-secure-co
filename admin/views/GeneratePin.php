<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
$pagingParam	=	'';
$totalCoins		=	0;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_pinCode_status']);
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " p.* ";
if(isset($_POST['pincode_status'])	&&	$_POST['pincode_status']	!=''	){
	$_SESSION['mgc_pinCode_status']	=	$_POST['pincode_status'];
}
if(isset($_SESSION['mgc_pinCode_status'])	&&	$_SESSION['mgc_pinCode_status']	!=''	){
	$condition = " AND p.Status = ".$_SESSION['mgc_pinCode_status']." ";
	if($_SESSION['mgc_pinCode_status']	== 1)
		$fields	.=	',u.FirstName,u.Lastname,u.Status as userStatus ';
}
else {
	$condition = " AND p.Status = 0 ";
}
if(isset($_GET['tournamentId'])	&&	$_GET['tournamentId']	!=''	){
	$tournamentsId	=	$_GET['tournamentId'];
	$pagingParam	=	"?tournamentId=".$tournamentsId;
	if(isset($_GET['tournamentName'])	&&	$_GET['tournamentName']	!=''	)
		$pagingParam	.= "&tournamentName=".$_GET['tournamentName'];
	$condition .= " AND fkTournamentsId	=".$tournamentsId." ";
	$pinCodeList	=	$tournamentObj->selectPinCode($fields,$condition);
	$tot_rec 		 = $tournamentObj->getTotalRecordCount();
}
?>
<body class="popup_bg" >
	<div class="box-header"><h2><i class="fa fa-list"></i>Pin List<?php if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '')	echo ' - '.$_GET['tournamentName'].''; ?></h2>
	</div>
	<div class="clear">
	<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" class="headertable">
		<tr><td height="20"></td></tr>
		<tr>
			<td valign="top" align="center" colspan="2">
				
				<form name="player_search" action="<?php echo 'GeneratePin'.$pagingParam;?>&cs=1" method="post">
				   <table  cellpadding="0" cellspacing="0" border="0" class="filter_form" width="95%">									       
						<tr><td height="10"></td></tr>
						<tr>
							<td width="20%" align="center" valign="middle"><label>Status</label></td>
							<td width="2%" align="center"  valign="middle">&nbsp;:&nbsp;</td>
							<td align="left" width="20%" valign="middle"  height="40" >
							<select name="pincode_status"  id="pincode_status" class="input" tabindex="2" title="Select Status" >
								<?php foreach($pinStatus as $key => $pin_status) { ?>
									<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_pinCode_status']) && $_SESSION['mgc_pinCode_status'] != '' && $_SESSION['mgc_pinCode_status'] == $key) echo 'Selected';  ?>><?php echo $pin_status; ?></option>
								<?php }?>
								</select>
							</td>
							<td align="left" valign="middle" style="padding-left:80px;"><input type="submit" title="Search" class="submit_button" name="Search" id="Search" value="Search"></td>
						</tr>
						<tr><td height="10"></td></tr>
					 </table>
				  </form>	
			</td>
		</tr>
		<tr><td height="20"></td></tr>
		<tr>
			<td colspan="2">
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<?php if(isset($pinCodeList) && is_array($pinCodeList) && count($pinCodeList) > 0){ ?>
						<td align="left" width="20%">No. of Pin(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
								<?php if(isset($pinCodeList) && is_array($pinCodeList) && count($pinCodeList) > 0 ) {
									pagingControlLatest($tot_rec,'GeneratePin'.$pagingParam); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="20"></td></tr>
		<tr>
			<td colspan="2">
			<form action="GeneratePin" class="l_form" name="GeneratePinList" id="GeneratePinList"  method="post"> 
				<div class="tbl_scroll">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr>
						<th align="center" width="3%" class="text-center">#</th>
						<th width="30%">Pin</th>
						<?php if(isset($_SESSION['mgc_pinCode_status'])	&&	$_SESSION['mgc_pinCode_status']	==1	){?>
						<th width="10%">User Name</th>
						<th width="10%">Used Date</th>
						<?php }?>
					</tr>
					<?php if(isset($pinCodeList) && is_array($pinCodeList) && count($pinCodeList) > 0 ) { 
							 foreach($pinCodeList as $key=>$value){
					 ?>									
					<tr id="test_id_<?php echo $value->id;?>">
						<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td ><?php if(isset($value->PinCode) && $value->PinCode != '') echo $value->PinCode; else echo '-';?></td>
						<?php if(isset($_SESSION['mgc_pinCode_status'])	&&	$_SESSION['mgc_pinCode_status']	==1	){
								$userName	=	'';
							if(isset($value->FirstName)	&&	isset($value->LastName)) 	
								$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
							else if(isset($value->FirstName))	
								$userName	=	 ucfirst($value->FirstName);
							else if(isset($value->LastName))	
								$userName	=	ucfirst($value->LastName);
						?>
							<td >
						<?php 	if(isset($userName) && $userName != ''){
									if(isset($value->userStatus)	&&	$value->userStatus !=3){?>
										<a href="#" onclick="close_this();window.parent.location.href='UserDetail?viewId=<?php echo $value->fkUsersId;?>&referList=1';"><?php echo $userName; ?></a>
							<?php	}else 
										echo $userName; 
								}
								else echo '-';?></td>
							<td valign="top"><?php if(isset($value->UsedDate) && $value->UsedDate != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->UsedDate)); }else echo '-';?></td>
						<?php }?>
					</tr>
					<?php } ?> 																		
				</table>
				<?php } else { ?>	
					<tr>
						<td colspan="16" align="center" style="color:red;">No Pin(s) Found</td>
					</tr>
				</table>
				<?php } ?>
				</div>
				</form>
			</td>
		</tr>
		<tr><td height="10"></td></tr>
	</table>
 </div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(document).ready(function() {		
	$(".pop_up").colorbox(
		{
			iframe:true,
			width:"50%", 
			height:"45%",
			title:true
	});
});
$(function(){

   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '550';
   var maxWidth  = '600';
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
function close_this()
{
self.close();
}
</script>
</html>
