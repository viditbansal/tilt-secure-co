<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/BrandController.php');
require_once('controllers/AdminController.php');
require_once("includes/phmagick.php");
$brandObj  		=   new BrandController();
$adminLoginObj	=	new AdminController();	

$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_brand_name']);
	unset($_SESSION['mgc_sess_brand_user_name']);
	unset($_SESSION['mgc_sess_brand_location']);
	unset($_SESSION['mgc_sess_brand_status']);
	unset($_SESSION['mgc_sess_brand_registerdate']);
	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
if(!isset($_GET['statistics'])) {
	unset($_SESSION['mgc_sess_from_date']);
	unset($_SESSION['mgc_sess_to_date']);
}
$statistics	=	'';
if(isset($_GET['statistics'])	&&	$_GET['statistics']	==	1){
	$statistics	=	'?statistics=1';
}

if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['brand']))
		$_SESSION['mgc_sess_brand_name'] 				=	trim($_POST['brand']);
	if(isset($_POST['brand_user']))
		$_SESSION['mgc_sess_brand_user_name'] 			=	trim($_POST['brand_user']);
	if(isset($_POST['brand_location']))
		$_SESSION['mgc_sess_brand_location']	    	=	trim($_POST['brand_location']);
	if(isset($_POST['brand_status']))
		$_SESSION['mgc_sess_brand_status']				=	$_POST['brand_status'];
	if(isset($_POST['ses_date']))
		$_SESSION['mgc_sess_brand_registerdate']		=	$_POST['ses_date'];
	if(isset($_POST['do_action']) && $_POST['do_action'] != '')	{
		if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
			$Ids	=	implode(',',$_POST['checkedrecords']);
			if($_POST['bulk_action']==3)
				$delete_id = $Ids;
			}
	}
	
}
if(!isset($_GET['statistics'])){ //blocked for statistics page
if(isset($_POST['do_action']) && $_POST['do_action'] != '')	{
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
		$Ids	=	implode(',',$_POST['checkedrecords']);
		if($_POST['bulk_action']==3)
			$delete_id = $Ids;
		else if($_POST['bulk_action']==1){
			$approveIds	=	$Ids;
		}
		else if($_POST['bulk_action']==4){
			$rejectIds	=	$Ids;
		}
	}
}
if(isset($_GET['delId']) && $_GET['delId']!='')
	$delete_id      = $_GET['delId'];
if(isset($delete_id) && $delete_id != ''){	
	$brandObj->updateBrandDetail('Status = 3','id IN ('.$delete_id.')' );
	$_SESSION['notification_msg_code']	=	3;
	header("location:BrandList");
	die();
}
if(isset($approveIds) && $approveIds != ''){	
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0){
		$brandDetailsArray				=	array();
		$fields							=	' * ';
		$condition 						=	' 1 ';
		$login_result 					=	$adminLoginObj->getAdminDetails($fields,$condition);
		
		$where      			= 	" id in (".$approveIds.") and Status = 2 ";
		$brandDetailsResult  	= 	$brandObj->SingleBrandDetails($where);
		if(isset($brandDetailsResult) && is_array($brandDetailsResult) && count($brandDetailsResult) > 0){
			foreach($brandDetailsResult as $key => $value){
				$brandDetailsArray[$value->id] = $value;
			}
		}
		foreach($_POST['checkedrecords'] as $ap_key=>$ap_val){
			if(isset($brandDetailsArray[$ap_val])){
				$mailContentArray['password'] 		=	$brandDetailsArray[$ap_val]->ActualPassword;
				$mailContentArray['name']			=	$brandDetailsArray[$ap_val]->UserName;
				$mailContentArray['brand']			=	$brandDetailsArray[$ap_val]->BrandName;
				$mailContentArray['toemail'] 		=	$brandDetailsArray[$ap_val]->Email;
				$mailContentArray['brandSite']		=	BRAND_SITE_PATH;
				$mailContentArray['subject'] 		=	'Brand Approved';
				$mailContentArray['from'] 			=	$login_result[0]->EmailAddress;
				$mailContentArray['fileName']		=	'BrandApprove.html';
				sendMail($mailContentArray,'4');
				$mailsent	=	1;
			}
		}
	}
	$brandObj->updateBrandDetail('Status = 1','id IN ( '.$approveIds.' ) and (Status = 2 OR Status = 4)');
	$_SESSION['notification_msg_code']	=	6;
	header("location:BrandList");
	die();
}
if(isset($rejectIds)	&&	$rejectIds	!= ''){
	$brandObj->updateBrandDetail('Status = 4','id IN ('.$rejectIds.')' );
	$_SESSION['notification_msg_code']	=	7;
	header("location:BrandList");
	die();
}
if(isset($_GET['editId']) && $_GET['editId']!=''	&& isset($_GET['status'])	&&	$_GET['status']!=''){
	if($_GET['status'] == 1){
			$fields							=	' * ';
			$condition 						=	' 1 ';
			$login_result 					=	$adminLoginObj->getAdminDetails($fields,$condition);
			$where      			= 	"   id = ".$_GET['editId']." and Status IN (2) LIMIT 1 ";
			$brandDetailsResult  	= 	$brandObj->SingleBrandDetails($where);
			if(isset($brandDetailsResult) && is_array($brandDetailsResult) && count($brandDetailsResult) > 0){
				$mailContentArray['password'] 		=	$brandDetailsResult[0]->ActualPassword;
				$mailContentArray['name']			=	$brandDetailsResult[0]->UserName;
				$mailContentArray['brand']			=	$brandDetailsResult[0]->BrandName;
				$mailContentArray['toemail'] 		=	$brandDetailsResult[0]->Email;
				$mailContentArray['brandSite']		=	BRAND_SITE_PATH;
				$mailContentArray['subject'] 		=	'Brand Approved';
				$mailContentArray['from'] 			=	$login_result[0]->EmailAddress;
				$mailContentArray['fileName']		=	'BrandApprove.html';
				sendMail($mailContentArray,'4');
				$mailsent	=	1;
			}
	}
	$condition 		= " id = ".$_GET['editId'];
	$update_string 	= " Status = ".$_GET['status'];
	$brandObj->updateBrandDetail($update_string,$condition);
	$_SESSION['notification_msg_code']	=	4;
	header("location:BrandList");
	die();
}
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " b.id,b.*,count(t.id) as tournament_id";
$condition = " and b.Status IN (1,2,4) ";
if(isset($_GET['statistics']) && $_GET['statistics'] == '1'){
	$condition = ' and b.Status IN (1,2,4) ';
	if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != ''){
		$condition .= " AND ((date(b.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."' and date(b.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."') ) ";
	}
	else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '')
		$condition .= " AND date(b.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."'";
	else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '')
		$condition .= " AND date(b.DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."'";
}
$getBrandDetails	=	$brandObj->getBrandDetails($fields,$condition);
$tot_rec			=	$brandObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($getBrandDetails)) {
	$_SESSION['curpage'] = 1;
	$getBrandDetails  = $brandObj->getBrandDetails($fields,$condition);
}
$brandStatusArray	= array('0'=>'Not Verified','1'=>'Approved','2'=>'Pending','4'=>'Rejected');
?>
<body>
<?php if(!isset($_GET['statistics']))	top_header(); ?>
	<div class="box-header">	<h2><i class="fa fa-list"></i>Brand List</h2> </div>
	<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="clear">
		
		<tr>
			<td valign="top" align="center" colspan="2" style="padding: 0 5px">
				<form name="search_category" action="BrandList<?php echo $statistics;?>" method="post">
				   <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
						<tr><td height="15"></td></tr>
						<tr>													
							<td width="2%" style="padding-left:20px;"><label>Brand Name</label></td>
							<td width="2%" align="center">:</td>
							<td align="left" width="20%" height="40">
								<input type="text" class="input" name="brand" id="brand"  value="<?php  if(isset($_SESSION['mgc_sess_brand_name']) && $_SESSION['mgc_sess_brand_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_brand_name']);  ?>" >
							</td>
							<td width="2%" style="padding-left:20px;"><label>User Name</label></td>
							<td width="2%" align="center">:</td>
							<td align="left" width="20%" height="40">
								<input type="text" class="input" name="brand_user" id="brand_user"  value="<?php  if(isset($_SESSION['mgc_sess_brand_user_name']) && $_SESSION['mgc_sess_brand_user_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_brand_user_name']);  ?>" >
							</td>
							<td width="10%" style="padding-left:20px;"><label>Location</label></td>
							<td width="2%" align="center">:</td>
							<td align="left"  height="40" width="">
								<input type="text" class="input" name="brand_location" id="brand_location"  value="<?php  if(isset($_SESSION['mgc_sess_brand_location']) && $_SESSION['mgc_sess_brand_location'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_brand_location']);  ?>" >
							</td>
						</tr>
						<tr><td height="10"></td></tr>
						<tr>
							
							<td width="" style="padding-left:20px;"><label>Status</label></td>
							<td width="" align="center">:</td>
							<td align="left" width="" height="40">
								<select name="brand_status" id="brand_status" tabindex="2" title="Select Status" >
									<option value="">Select</option>
								<?php foreach($brandStatusArray as $key => $brand_status) { ?>
										<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_brand_status']) && $_SESSION['mgc_sess_brand_status'] != '' && $_SESSION['mgc_sess_brand_status'] == $key) echo 'Selected';  ?>><?php echo $brand_status; ?></option>
								<?php } ?>
								</select>
							</td>
							<td width="10%" style="padding-left:20px;"><label>Registered Date</label></td>
							<td width="2%" align="center">:</td>
							<td align="left"  height="40">
								<input  type="text" autocomplete="off"  maxlength="10" class="input w50" name="ses_date" id="ses_date" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_brand_registerdate']) && $_SESSION['mgc_sess_brand_registerdate'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_brand_registerdate'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
							</td>
							<td colspan="3"></td>
						</tr>
						<tr><td height="10"></td></tr>
						<tr><td colspan="9" align="center" width=""><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
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
						<?php if(isset($getBrandDetails) && is_array($getBrandDetails) && count($getBrandDetails) > 0){ ?>
						<td align="left" width="20%">No. of Brand(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
								<?php if(isset($getBrandDetails) && is_array($getBrandDetails) && count($getBrandDetails) > 0 ) {
									pagingControlLatest($tot_rec,'BrandList'.$statistics); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="10"></td></tr>
		<tr><td colspan= '2' align="center">
			<?php displayNotification('Brand'); ?>
			</td></tr>
		<tr><td height="10"></td></tr>
		<tr>
			<td colspan="2">
			<div class="tbl_scroll">
			  <form action="BrandList" class="l_form" name="BrandListForm" id="BrandListForm"  method="post"> 
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr align="left">
					<?php if(!isset($_GET['statistics'])) { ?>
						<th align="center" style="text-align:center" width="3%">
							<input onclick="checkAllRecords('BrandListForm');" type="Checkbox" name="checkAll"/>
						</th>
					<?php } ?>
						<th align="center" width="3%" class="text-center">#</th>
						<th  align="left" width="32%"><?php echo SortColumn('BrandName','Brand Name'); ?></th>
						<th width="20%" align="left" class=""><?php echo SortColumn('UserName','User Name'); ?></th>
						<th width="20%" align="left" class=""><?php echo SortColumn('Name','Name'); ?></th>
						<th  align="left" width="25%" class=""><?php echo SortColumn('Location','Location'); ?></th>
						<th width="10%" align="left" class=""><?php echo SortColumn('Amount','Balance TiLT$'); ?></th>
						<th width="10%" align="left" class=""><?php echo SortColumn('VirtualCoins','Balance Virtual Coins'); ?></th>
						<th width="10%" align="left" class=""><?php echo 'Status'; ?></th>
					<?php if(!isset($_GET['statistics'])) { ?>
						<th>Tournaments</th>
					<?php } ?>
						<th  align="left" width="15%" class=""><?php echo SortColumn('DateCreated','Registered Date'); ?></th>
					</tr>
					<?php if(isset($getBrandDetails) && is_array($getBrandDetails) && count($getBrandDetails) > 0 ) { 
							foreach($getBrandDetails as $key=>$value)	{	
								$image_path = ADMIN_IMAGE_PATH.'no_brand.jpeg';
								$original_path = ADMIN_IMAGE_PATH.'no_brand.jpeg';
								$photo = $value->Logo;
								if(isset($photo) && $photo != ''){
									$brand_image = $photo;		
									$image_path_rel = BRANDS_IMAGE_PATH_REL.$value->id.'/'.$brand_image;
									$original_path_rel = BRANDS_IMAGE_PATH_REL.$value->id.'/original_'.$brand_image;
									if(SERVER){
										if(image_exists(5,$brand_image)){
											$image_path = BRANDS_IMAGE_PATH.$value->id.'/'.$brand_image;
											$original_path = BRANDS_IMAGE_PATH.$value->id.'/original_'.$brand_image;
										}
									}
									else if(file_exists($image_path_rel)){
											$image_path = BRANDS_IMAGE_PATH.$value->id.'/'.$brand_image;
											$original_path = BRANDS_IMAGE_PATH.$value->id.'/original_'.$brand_image;
									}
								}?>
					<tr id="test_id_<?php echo $value->id;?>"	>
						<?php if(!isset($_GET['statistics'])) { ?>
						<td valign="top" align="center"><input id="checkedrecords" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" /></td>
						<?php } ?>
						<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td valign="top" align="center" ><?php if(isset($value->BrandName)	&&	$value->BrandName!=''){?>
							<div  style="<?php if(isset($cover_path) && $cover_path != '' ) { ?>
								background: url('<?php echo $cover_path;?>') no-repeat;
								<?php } else { ?>
								background: none no-repeat; 
								<?php 	} ?>;background-size:cover;float:left">
								<a <?php if(isset($original_path) && $original_path != ADMIN_IMAGE_PATH.'no_brand.jpeg' ) { ?> href="<?php echo $original_path; ?>" class="brand_image_pop_up"  <?php } ?> title="View Photo"  ><img class="user_img" width="36" height="36" src="<?php echo $image_path;?>" ></a>
							</div>
							<div class="user_profile">
							<p align="left" style="padding-left:50px">
							<?php  if(isset($value->BrandName) && $value->BrandName != '')	{ ?>
							<a href="#" class="recordView <?php if(isset($_GET['statistics']) && $_GET['statistics'] == '1'){ echo 'tournament_list_pop_up'; } ?>" onclick="location.href='BrandDetail?viewId=<?php echo $value->id; if(isset($_GET['statistics']) && $_GET['statistics'] == '1'){ echo '&statistics=1'; } ?>';"><?php echo trim($value->BrandName); } else echo ' - '; ?></a></p>
							<?php if(!isset($_GET['statistics'])) { ?>	
							<div class="userAction"  id="userAction" >
									<p align="left" style="padding-left: 10px;" >
									<a href="BrandManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="editUser"><i class="fa fa-edit fa-lg"></i></a>
									
									<?php if(isset($value->VerificationStatus)	&&	$value->VerificationStatus == 1) { ?>
										<?php  if(isset($value->Status)	&&	$value->Status == 1) { ?>		
										<a class="editUser" alt="Approved" title="Approved Brand" onclick="javascript:return confirm('Are you sure to disapprove the brand?')" href="BrandList?editId=<?php echo $value->id;?>&status=2"> <i class="fa fa-thumbs-up fa-lg"></i></a>
										<?php } else if(isset($value->Status)	&&	$value->Status == 2){ ?>
										<a class="editUser"  title="Not yet approved" alt="Not Yet Approved Brand" onclick="javascript:return confirm('Are you sure to approve the brand?')" href="BrandList?editId=<?php echo $value->id;?>&status=1" style="color:grey;"><i class="fa fa-thumbs-o-up fa-lg"></i><i class="fa fa-times" style="position:absolute;display:block;margin:-6px 0px 0px 5px;color:#F53;"></i></a>
										<a class="userIcon rejectIcon"  title="Reject Brand" alt="Reject Brand" onclick="javascript:return confirm('Are you sure to Reject the brand?')" href="BrandList?editId=<?php echo $value->id;?>&status=4" ><i class="fa fa-thumbs-o-down fa-lg"></i></i></a>
										<?php } if(isset($value->Status)	&&	$value->Status == 4) { ?>
										<a class="userIcon rejectIcon"  title="Rejected Brand" alt="Rejected Brand" style="color:grey;cursor:auto;"><i class="fa fa-thumbs-o-down fa-lg"></i></i></a>
										<?php } ?>
									<?php } else { ?>
										<a class="editUser" alt="Approved" title="Not verified" style="color:grey;"><i class="fa fa-fw fa-check fa-lg"></i></a>
									<?php } ?>
										
									<a href="BrandDetail?viewId=<?php echo $value->id; ?>" title="View" alt="View" class="viewUser">&nbsp;<i class="fa fa-search-plus fa-lg"></i></a>
									<a onclick="javascript:return confirm('Are you sure to delete?')" href="BrandList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="deleteUser">&nbsp;<i class="fa fa-trash-o fa-lg"></i></a>
									</p>
							</div>
							<?php } ?>
							</div>
						<?php }  else { echo " - "; } ?>
						</td>
						<td align="left"><?php if(isset($value->UserName)	&&	$value->UserName!=''){	echo $value->UserName; } else { echo " - "; } ?></td>
						<td align="left"><?php if(isset($value->Name)	&&	$value->Name!=''){	echo $value->Name; } else { echo " - "; } ?></a>&nbsp;</td>
						<td  align="left"><?php if(isset($value->Location)	&&	$value->Location!=''){	echo $value->Location; } else { echo " - "; } ?></td>
						<td  align="right" style="padding-right:15px;"><?php if(isset($value->Amount)	&&	$value->Amount!=''){	echo number_format($value->Amount); } else { echo " 0 "; } ?></td>
						<td  align="right" style="padding-right:15px;"><?php if(isset($value->VirtualCoins)	&&	$value->VirtualCoins!=''){	echo number_format($value->VirtualCoins); } else { echo " 0 "; } ?></td>
						<td  align="right" style="padding-right:15px;"><?php if(isset($value->VerificationStatus) && $value->VerificationStatus == 0){ echo 'Not Verified'; } else if(isset($value->Status)){	echo $value->Status == 1 ? 'Approved' :($value->Status == 2 ? 'Pending' : ($value->Status == 4 ? 'Rejected' : '-')) ; } else { echo " - "; } ?></td>
					<?php if(!isset($_GET['statistics'])) { ?>
					<td align="center">
						<?php if(isset($value->tournament_id)	&&	$value->tournament_id > 0){	?>
							<a href="TournamentList?brand_id=<?php echo $value->id;?>&from=1&cs=1&hide_players=1" class="tournament_list_pop_up" name="Tournaments" id="Tournaments" title="Tournaments" alt="Tournaments"><i class="fa fa-trophy fa-lg"></i> <?php echo $value->tournament_id; ?></a>
							<?php } else { echo " - "; } ?>
					</td>
					<?php }?>
					<td valign="top"><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
					</tr>
					<?php } ?>
				</table>
				<?php 	if(!isset($_GET['statistics'])) {
							if(isset($getBrandDetails) && is_array($getBrandDetails) && count($getBrandDetails) > 0){ 
								bulk_action($brandActionArray); ?>
				<?php 		}
						}
				?>
				</form>
				</div>
				</td>
		</tr>
				
				<?php } else { ?>	
					<tr><td colspan="16" align="center" style="color:red;">No Brand(s) Found</td></tr>
				<?php } ?>
		<div style="height: 20px;"></div>
	</table>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".brand_image_pop_up").colorbox({
	title:true,
	maxWidth:"50%", 
	maxHeight:"50%"
});
jQuery(function() {
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[id^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");
	   
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
	$(document).ready(function() {		
		$(".tournament_list_pop_up").colorbox(
			{
				iframe:true,
				width:"80%", 
				height:"45%",
				title:true
		});
});

$(function(){

   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '580';
   var maxWidth  = '900';
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
$("#ses_date").datepicker({
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
</script>
</html>
