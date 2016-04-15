<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();

require_once('controllers/GameController.php');
require_once('controllers/AdminController.php');
require_once('controllers/TournamentController.php');
$gameObj   =   new GameController();
$adminLoginObj	=	new AdminController();	
$tourObj   =   new TournamentController();
$display   =   'none';
$class  =  $msg    = $cover_path = $statistics = $virtualcoins = '';
$updateStatus	=	1;
$devgamecount = array();

//-- Get virtual coins from general settings --
$fields	= ' VirtualCoinsDeveloper ';
$where	= ' id = 1 ';
$setting_details	=	$adminLoginObj->getDistance($fields,$where);
if(isset($setting_details) && is_array($setting_details) && count($setting_details) > 0){
	$virtualcoins = $setting_details[0]->VirtualCoinsDeveloper;
}

if(!isset($_GET['statistics'])) {
	unset($_SESSION['mgc_sess_from_date']);
	unset($_SESSION['mgc_sess_to_date']);
	unset($_SESSION['mgc_sess_statistic_from_date']);
	unset($_SESSION['mgc_sess_statistic_to_date']);
}
$popup = 1;
if(isset($_GET['statistics'])	&&	$_GET['statistics']	==	1){
	$statistics	=	'?statistics=1';
	$popup = 0;
}
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_search_company_name']);
	unset($_SESSION['mgc_sess_search_dev_name']);
	unset($_SESSION['mgc_sess_search_status']);
	unset($_SESSION['mgc_sess_search_registerdate']);
}
if($popup){
	if(isset($_GET['approveId']) && $_GET['approveId']!='' || ( isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0 && $_POST['bulk_action']!='' )){
		$approveId  = (isset($_GET['approveId'])?$_GET['approveId']:implode(',',$_POST['checkedrecords']));
		$developerDetailArray = array();
		if(isset($_POST['bulk_action']) && $_POST['bulk_action'] == 3){
			$updateStatus = 3;
			$_SESSION['notification_msg_code']	=	3;
		}
		else if(isset($_POST['bulk_action']) && $_POST['bulk_action'] == 4){
			$updateStatus = 4;
			$_SESSION['notification_msg_code']	=	7;
		}
		else{
			$updateStatus = 1;
			$mailSendIds = array();
			
			//update developer virtual coins
			if($virtualcoins != ''){
				$update_string = "VirtualCoins = ".$virtualcoins;
				$condition     = "id in (".$approveId.")";
				$gameObj->updateGameDevDetails($update_string,$condition);
			}
			
			if(isset($_GET['approveId']) && $_GET['approveId'] !=''){
				$mailSendIds[]	=	$_GET['approveId'];
			}
			else {
				$mailSendIds	=	$_POST['checkedrecords'];
			}
			$where      			= 	"   id in (".$approveId.") and Status = 2";
			$developerDetails  	= 	$gameObj->selectGameDeveloper($where);
			if(isset($developerDetails) && is_array($developerDetails) && count($developerDetails) > 0){
				foreach($developerDetails as $key => $value){
					$developerDetailArray[$value->id] = $value;
				}
			}
			
			if(isset($mailSendIds) && is_array($mailSendIds) && count($mailSendIds) > 0){
				$fields 							=	'*';
				$condition 							=	' 1';
				$login_result 						=	$adminLoginObj->getAdminDetails($fields,$condition);
				foreach($mailSendIds as $ap_key=>$ap_val){
					if(isset($developerDetailArray[$ap_val])){
						$mailContentArray['password'] 		=	$developerDetailArray[$ap_val]->ActualPassword;
						$mailContentArray['name']			=	$developerDetailArray[$ap_val]->UserName;
						$mailContentArray['toemail'] 		=	$developerDetailArray[$ap_val]->Email;
						$mailContentArray['gameSite']		=	GAME_SITE_PATH;
						$mailContentArray['subject'] 		=	'Developer & Brand Approved';
						$mailContentArray['from'] 			=	$login_result[0]->EmailAddress;
						$mailContentArray['fileName']		=	'GameDeveloperApprove.html';
						sendMail($mailContentArray,'10');
						$mailsent	=	1;
					}
				}
			}
			$_SESSION['notification_msg_code']	=	6;
		}
		$gameObj->approveDeveloper($approveId,$updateStatus);
		header("location:GameDeveloperList");
		die();
	}
	$devId	=	'';
	if(isset($_GET['disApproveId']) && $_GET['disApproveId']!=''){
		$devId	=	$_GET['disApproveId'];
		$updateStatus = 4;
		$_SESSION['notification_msg_code']	=	4;
		//update developer virtual coins
		$gameObj->updateGameDevDetails("VirtualCoins = 0","id in (".$devId.")");
	}
	else if(isset($_GET['rejectId']) && $_GET['rejectId']!=''){
		$devId	=	$_GET['rejectId'];
		$updateStatus = 4;
		$_SESSION['notification_msg_code']	=	7;
	}
	else if(isset($_GET['delId']) && $_GET['delId']!=''){
		$devId	= $_GET['delId'];
		$updateStatus = 3;
		$_SESSION['notification_msg_code']	=	3;
	}
	if($devId !=''){
		$gameObj->approveDeveloper($devId,$updateStatus);
		header("location:GameDeveloperList");
		die();
	}
}

if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['developerName']))
		$_SESSION['mgc_sess_search_dev_name']	    =	trim($_POST['developerName']);
	if(isset($_POST['companyName']))
		$_SESSION['mgc_sess_search_company_name']	=	trim($_POST['companyName']);
	if(isset($_POST['ses_status']))
		$_SESSION['mgc_sess_search_status']			=	trim($_POST['ses_status']);
	if(isset($_POST['ses_date']))
		$_SESSION['mgc_sess_search_registerdate']	=	trim($_POST['ses_date']);
}


setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = "gd.id,gd.*,gd.id as gdId,gd.Photo as developerPhoto,gd.Name as devName,count(g.id) as gamecount ";
$condition = " AND  gd.Status !=3";
// ****** For statistics page ****
	if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != ''){
		$condition .= " AND ((date(gd.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."' and date(gd.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."') ) ";
	}
	else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '')
		$condition .= " AND date(gd.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."'";
	else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '')
		$condition .= " AND date(gd.DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."'";
// ****** End ***********

$gameDeveloper  	= $gameObj->getDeveloperList($fields,$condition);
$tot_rec 		 	= $gameObj->getTotalRecordCount();
$devIds = '';
$tourCountArray = array();
if(isset($gameDeveloper) && is_array($gameDeveloper) && count($gameDeveloper) > 0){
	foreach($gameDeveloper as $key0=>$value0)
		if(isset($value0->gdId) && !empty($value0->gdId)) $devIds .= $value0->gdId.',';
	$devIds = rtrim($devIds,',');
	if(!empty($devIds)){
		$fields 	= "count(id) as tournamentCount,fkDevelopersId as id ";
		$condition	= " AND fkDevelopersId IN (".$devIds.") AND CreatedBy = 3 and Status = 1 GROUP BY fkDevelopersId ";
		$gameDevTourCount  	= $tourObj->tournamentList($fields,$condition);
		if(isset($gameDevTourCount) && is_array($gameDevTourCount) && count($gameDevTourCount) > 0){
			foreach($gameDevTourCount as $key1=>$value1){
				if(isset($value1->tournamentCount) && !empty($value1->tournamentCount) && $value1->tournamentCount >0)
					$tourCountArray[$value1->id] = $value1->tournamentCount;
			}
		}
	}
}
if(isset($_GET['msg']) && $_GET['msg'] == 4){
	$msg 		= 	"Developer & Brand approved successfully";
	$display	=	"block";
	$class 		= 	"alert-success";
	$class_icon =   "fa-check";
}
?>
<body>
<?php if($popup) top_header(); ?>						 
	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i>Developer & Brand List</h2>
	</div>
	<div class="clear">
           <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
			
			<tr><td class="filter_form" >
				<form name="search_category" action="GameDeveloperList<?php echo $statistics;?>" method="post">
            	<table align="center" cellpadding="6" cellspacing="0" border="0"width="98%">									       
					<tr><td></td></tr>					
					<tr>													
						<td width="10%" ><label>Name</label></td>
						<td width="2%" align="center">:</td>
						<td align="left" width="20%">
							<input type="text" class="input" name="developerName" id="developerName"  value="<?php  if(isset($_SESSION['mgc_sess_search_dev_name']) && $_SESSION['mgc_sess_search_dev_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_search_dev_name']);  ?>" >
						</td>
						<td width="10%" ><label>Company</label></td>
						<td width="2%" align="center">:</td>
						<td align="left" width="20%">
							<input type="text" class="input" id="companyName" name="companyName"  value="<?php  if(isset($_SESSION['mgc_sess_search_company_name']) && $_SESSION['mgc_sess_search_company_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_search_company_name']);  ?>" >
						</td>	
						<td width="10%" style="padding-left:20px;"><label>Status</label></td>
						<td width="2%" align="center">:</td>
						<td align="left"  height="40">
							<select name="ses_status" id="ses_status" tabindex="2" title="Select Status" style="width:40%;">
								<option value="">Select</option>
							<?php $i=1; 
									foreach($gameStatusArray as $key => $user_status) { 
										?>
								<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_search_status']) && $_SESSION['mgc_sess_search_status'] != '' && $_SESSION['mgc_sess_search_status'] == $key) echo 'Selected';  ?>><?php echo $user_status; ?></option>
							<?php 	 
									}?>
							</select>
						</td>
					</tr>
					<tr>
						<td width="10%"><label>Registered Date</label></td>
						<td width="2%" align="center">:</td>
						<td align="left"  height="40">
							<input  type="text" autocomplete="off"  maxlength="10" class="input w50" name="ses_date" id="ses_date" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_search_registerdate']) && $_SESSION['mgc_sess_search_registerdate'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_search_registerdate'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
						</td>
					</tr>
						<tr><td align="center" colspan="9"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
					<tr><td height="10"></td></tr>
				 </table>
				 </form>
			</td></tr> 
			<tr><td height="20"></td></tr>
			<tr><td>
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<?php if(isset($gameDeveloper) && is_array($gameDeveloper) && count($gameDeveloper) > 0){ ?>
						<td align="left" width="20%">No. of Developer & Brand(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
								<?php if(isset($gameDeveloper) && is_array($gameDeveloper) && count($gameDeveloper) > 0 ) {
								 	pagingControlLatest($tot_rec,'GameDeveloperList'.$statistics); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td></tr>
			<tr><td height="10"></td></tr>
			<tr><td colspan= '2' align="center">
				<?php displayNotification('Developer & Brand'); ?>
			</td></tr>
			<tr><td height="10"></td></tr>
			<tr><td>
			<div class="tbl_scroll">
				  <form action="GameDeveloperList<?php echo $statistics;?>" class="l_form" name="GameDeveloperList" id="GameDeveloperList"  method="post"> 
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
						<tr align="left">
							<?php if($popup){ ?>
							<th align="center" style="text-align:center" width="3%"><input onclick="checkAllRecords('GameDeveloperList');" type="Checkbox" name="checkAll"/></th>
							<?php } ?>
							<th align="center" style="text-align:center"  width="3%">#</th>												
							<th width="20%">Email</th>
							<th width="15%">Name</th>
							<th width="9%">Company</th>
							<th width="9%">TiLT$</th>
							<th width="9%">Virtual Coins</th> 
							<th width="9%">Status</th>							
							<th width="9%">No. of Games</th>
							<th width="9%">Registered Date</th>
						</tr>
						<?php if(isset($gameDeveloper) && is_array($gameDeveloper) && count($gameDeveloper) > 0 ) { ?>
						<?php foreach($gameDeveloper as $key=>$value){
								$userName	=	$Company = '';
								$userName	=	ucfirst($value->Name);
								$Company	=	ucfirst($value->Company);
								$email		=	' - ';
								if(isset($value->Email) && $value->Email != '') $email = $value->Email; 
								$dev_image_path = ADMIN_IMAGE_PATH.'developer_logo.png';
								$dev_original_path = ADMIN_IMAGE_PATH.'developer_logo.png';
								$photo = $value->Photo;
								if(isset($photo) && $photo != ''){
									$user_image = $photo;
									$image_path_rel = DEVELOPER_THUMB_IMAGE_PATH_REL.$user_image;
									$original_path_rel = DEVELOPER_IMAGE_PATH_REL.$user_image;
									if(SERVER){
										if(image_exists(1,$user_image)){
											$dev_image_path = DEVELOPER_THUMB_IMAGE_PATH.$user_image;
											$dev_original_path = DEVELOPER_IMAGE_PATH.$user_image;
										}
									}
									else if(file_exists($image_path_rel)){
											$dev_image_path = DEVELOPER_THUMB_IMAGE_PATH.$user_image;
											$dev_original_path = DEVELOPER_IMAGE_PATH.$user_image;
									}
								}
						 ?>									
						<tr id="test_id_<?php echo $value->gdId;?>">
							<?php if($popup){ ?>
							<td valign="top" align="center"><input id="checkedrecords" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" hashCount="<?php if(isset($value->hash_count) && $value->hash_count > 0 ) echo $value->hash_count; ?>" <?php echo $deleteStatus; ?>/></td>
							<?php } ?>
							<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td valign="top">
								<?php if($popup){ ?>
								<div style="background: none no-repeat;background-size:cover;float:left">
								<a <?php if(isset($dev_original_path) && $dev_original_path != ADMIN_IMAGE_PATH.'developer_logo.png' ) { ?> href="<?php echo $dev_original_path; ?>" class="fancybox"  <?php } ?> title="View Photo"  ><img class="user_img" style="margin-right:5px;" width="36" height="36" src="<?php echo $dev_image_path;?>" ></a>
								</div>
								<div class="col-xs-11 col-md-5 col-lg-3 no-padding">
									<a href="javascript:void(0);" class="recordView">&nbsp;<?php echo $email ?></a>
								</div>		
								<div class="userAction" style="display:block; padding-top:8px; min-height:20px;" id="userAction">	
									<?php if(isset($value->Status) && $value->Status == 1 ) { ?>
										<a data-toggle="tooltip" title="Active Developer" onclick="javascript:return confirm('Are you sure to inactive this developer?')" href="GameDeveloperList?disApproveId=<?php if(isset($value->gdId) && $value->gdId != '') echo $value->gdId; ?>"><i class="fa fa-thumbs-up fa-lg"></i></a>
									<?php }else if(isset($value->Status)	&&	$value->Status == 4) { ?>
										<a data-toggle="tooltip" title="Inactive Developer" title="Inactive Developer" style="color:grey;cursor:auto;" ><i class="fa fa-thumbs-o-down fa-lg"></i></a>
									<?php } ?>
									<a href="GameDeveloperManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="editUser">&nbsp;<i class="fa fa-edit fa-lg"></i></a>
									<a href="GameDeveloperDetail?viewId=<?php echo $value->id; ?>" title="View" alt="View" class="viewUser">&nbsp;<i class="fa fa-search-plus fa-lg"></i></a>
									<?php if(isset($value->gdId) && $value->gdId != '' && array_key_exists($value->gdId,$tourCountArray)) { ?>
									<a href="javascript:void(0);" onclick="javascript:return alert('Sorry ! You can&#146;t delete this Developer & Brand  because it associated with some tournaments')"  title="Delete" alt="Delete" class="deleteUser" style="color:grey;">&nbsp;<i class="fa fa-trash-o fa-lg"></i></a>
									<?php }else{ ?>
									<a onclick="javascript:return confirm('Are you sure to delete?')" href="GameDeveloperList?delId=<?php if(isset($value->gdId) && $value->gdId != '') echo $value->gdId;?>" title="Delete" alt="Delete" class="deleteUser">&nbsp;<i class="fa fa-trash-o fa-lg"></i></a>
									<?php } ?>
									<?php if(isset($value->VerificationStatus)	&&	$value->VerificationStatus != 1) { ?>
										<a class="viewUser" alt="Approved" title="Not verified"  style="color:grey;">&nbsp;<i class="fa fa-fw fa-check fa-lg"></i></a>
									<?php } ?>
									
								</div>
							<?php }else{ ?>
								<div style="background: none no-repeat;background-size:cover;float:left">
								<img class="user_img" style="margin-right:5px;" width="36" height="36" src="<?php echo $dev_image_path;?>" >
								</div>
								<div class="col-xs-11 col-md-5 col-lg-3 no-padding">
									<?php  echo $email; ?>
								</div>
								
							<?php } ?>
							</td>
							<td valign="top"><?php  echo (!empty($userName)?$userName:'-'); ?></td>
							<td valign="top"><?php if(isset($value->Company) && $value->Company != ''){ echo $value->Company; }else echo '-';?></td>	
							<td valign="top"><?php if(isset($value->Amount) &&  $value->Amount !='') echo number_format($value->Amount); else echo '-'; ?></td>
							<td><?php if(isset($value->VirtualCoins) &&  $value->VirtualCoins !='') echo number_format($value->VirtualCoins); else echo '-'; ?></td> 
							<td>
								<?php
									if((isset($value->VerificationStatus) && $value->VerificationStatus == 0) && (isset($value->Status) && $value->Status == 1)) {
										echo 'Not Verified';
									} else if(isset($value->Status) && $value->Status !='' && isset($gameStatusArray[$value->Status])) {
										echo $gameStatusArray[$value->Status];
									} else echo '-';
								?>
							</td> 
							<td><?php  if(isset($value->gamecount) && $value->gamecount > 0){ if($popup) { ?><a href="GameDetails?cs=1&devId=<?php echo $value->id;?>" class="game_list_pop_up" name="gamelist" id="gamelist" title="gamelist" alt="gamelist"><?php echo $value->gamecount; ?> </a> <?php } else echo $value->gamecount; } else echo '0' ;?>&nbsp;</td>
							<td valign="top"><?php if(isset($value->DateCreated) && $value->DateCreated != ''){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
						</tr>
						<?php } ?> 																		
					</table>
					 <tr><td height="20"></td></tr>
					 <tr>
					 <td>
						<?php if($popup) if(isset($gameDeveloper) && is_array($gameDeveloper) && count($gameDeveloper) > 0){ 
								bulk_action($brandActionArray); ?>
						<?php 	}?>
					 </td>
					</tr>
					</table>
					<tr><td height="20"></td></tr>
					</form>
					<?php } else { ?>	
						<tr>
							<td colspan="16" align="center" style="color:red;">No Developer & Brand(s) Found</td>
						</tr>
					<?php } ?>
				</div>
				
			</td></tr>
           </table>
       </div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".fancybox").colorbox({
	title:true,
	maxWidth:"50%", 
	maxHeight:"50%"
});	
$(".gamelogo").colorbox({title:true});	
$(document).ready(function() {		
		$(".game_list_pop_up").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"80%",
				title:true
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
