<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/UserController.php');
$userObj   =   new UserController();
require_once('controllers/MessageController.php');
$messageObj   =   new MessageController();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();

$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_user_platform']);
	unset($_SESSION['mgc_sess_user_name']);
	unset($_SESSION['mgc_sess_email']);
	unset($_SESSION['mgc_sess_user_status']);
	unset($_SESSION['mgc_sess_location']);
	unset($_SESSION['mgc_sess_user_registerdate']);
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
	
	if(isset($_POST['ses_username']))
		$_SESSION['mgc_sess_user_name'] 	=	trim($_POST['ses_username']);
	if(isset($_POST['ses_email']))
		$_SESSION['mgc_sess_email']	    	=	trim($_POST['ses_email']);
	if(isset($_POST['ses_status']))
		$_SESSION['mgc_sess_user_status']	=	$_POST['ses_status'];
	
	if(isset($_POST['ses_date']) && $_POST['ses_date'] != ''){
		$validate_date = dateValidation($_POST['ses_date']);
		if($validate_date == 1){
			$date = date('Y-m-d',strtotime($_POST['ses_date']));
			if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
				$_SESSION['mgc_sess_user_registerdate']	= $date;
			else 
				$_SESSION['mgc_sess_user_registerdate']	= '';
		}
		else 
			$_SESSION['mgc_sess_user_registerdate']	= '';
	}
	else 
		$_SESSION['mgc_sess_user_registerdate']	= '';
}
if(isset($_POST['do_action']) && $_POST['do_action'] != '')	{
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
		$Ids	=	implode(',',$_POST['checkedrecords']);
		if($_POST['bulk_action']==1){
			$userIds	=	$Ids;
			$updateStatus	=	1;
		}
		else if($_POST['bulk_action']==2){
			$userIds	=	$Ids;
			$updateStatus	=	2;
		}
		else
			$delete_id = $Ids;
	}
}
if(isset($_GET['delId']) && $_GET['delId']!='')
	$delete_id      = $_GET['delId'];

if(isset($delete_id) && $delete_id != ''){	
	$userObj->deleteUserReleatedEntries($delete_id);
	$_SESSION['notification_msg_code']	=	3;
	header("location:UserList");
	die();
}
else if(isset($userIds) && $userIds != ''){	
	$userObj->changeUsersStatus($userIds,$updateStatus);
	$_SESSION['notification_msg_code']	=	4;
	header("location:UserList");
	die();
}
if(isset($_GET['editId']) && $_GET['editId']!=''	&& isset($_GET['status'])	&&	$_GET['status']!=''){
	$condition = " id = ".$_GET['editId'];
	$update_string = " Status = ".$_GET['status'];
	$userListResult  = $userObj->updateUserDetails($update_string,$condition);
	$_SESSION['notification_msg_code']	=	4;
	header("location:UserList");
	die();
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " u.id,u.*,count(t.id) as tcount";
$condition = " and u.Status != '3' ";

$userListResult  = $userObj->getUserListDetail($fields,$condition);
$tot_rec 		 = $userObj->getTotalRecordCount();
/* Chat list for all user*/
$userIds = '';
$tourCountArray	=	array();
if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ){
	foreach($userListResult as $key=>$value){
		$userIds .= $value->id.',';
	}
	if($userIds != ''){
	    $userIds	=	rtrim($userIds,',');
		$chatList = $messageObj->selectMessageDetails($userIds);
		$fromIds = array();
		$toIds   = array();
		if(isset($chatList) && is_array($chatList) && count($chatList) >0){
			foreach($chatList as $key=>$value){
				$fromIds[] = $value->fkUsersId;
				$toIds[]   = $value->tofkUsersId; 
			}
		}
		$field		=	" sum(case when t.Type=4 then 1 else 0 end)as customCount,ts.fkUsersId,sum(case when t.Type=3 then ts.Prize else 0 end) virtCoin,sum(case when t.Type=2 then ts.Prize else 0 end) tiltCoin,t.Type ";//,t.Type
		$condition	=	" ts.fkUsersId IN(".$userIds.")";
		$userCoinsResult  = $userObj->getCoinsCount($field,$condition);
		
		$field				=	' sum(r.CoinsUsed) as totalCoins,fkUsersId';
		$condition			=	' gc.Status != 0 and r.fkUsersId in ('.$userIds.') and r.Status = 1 ';
		$userPurchaseResult	=	$userObj->getPurchaseList($field,$condition);
	}
}

if( (isset($fromIds) && count($fromIds) > 0) || (isset($toIds) && count($toIds) > 0) ){
	$chatIds = array_unique(array_merge($fromIds,$toIds));
}
$coinsCountArray	=	array();
$totVirtCoin	=	$totTiltCoin	=	0;
if( isset($userCoinsResult) && is_array($userCoinsResult) && count($userCoinsResult) > 0 ){
	foreach($userCoinsResult as $key1=>$coinDetails){
		$coinsCountArray[$coinDetails->fkUsersId]	=	array('customCount' =>$coinDetails->customCount,'tiltCoin' =>$coinDetails->tiltCoin,'virtCoin' =>$coinDetails->virtCoin);
	}
}
$redeemResult	=	array();
if( isset($userPurchaseResult) && is_array($userPurchaseResult) && count($userPurchaseResult) > 0 ){
	foreach($userPurchaseResult as $key=>$value)
		$redeemResult[$value->fkUsersId]	=	$value->totalCoins;
}
?>
<body>
<?php top_header(); ?>
	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i>User List</h2>
	 	<span class="fright"><a href="UserManage"  title="Add User"><i class="fa fa-plus-circle"></i> Add User</a></span>
	</div>
    <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
		
		<tr><td>
			<form name="search_category" action="UserList" method="post" >
			<table align="center" cellpadding="6" cellspacing="0" border="0" width="100%" class="filter_form">	
				<tr><td height="2"></td></tr>
				<tr>													
					<td width="2%" style="padding-left:20px;"><label>User</label></td>
					<td width="1%" align="center">:</td>
					<td align="left"  width="15%">
						<input type="text" class="input" name="ses_username" id="ses_username"  value="<?php  if(isset($_SESSION['mgc_sess_user_name']) && $_SESSION['mgc_sess_user_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_user_name']);  ?>" >
					</td>
					<td  width="2%" style="padding-left:20px;"><label>Email</label></td>
					<td width="1%" align="center">:</td>
					<td align="left"  width="15%" >
						<input type="text" class="input" id="ses_email" name="ses_email"  value="<?php  if(isset($_SESSION['mgc_sess_email']) && $_SESSION['mgc_sess_email'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_email']);  ?>" >
					</td>
					<td  width="2%" style="padding-left:20px;"><label>Status</label></td>
					<td width="1%" align="center">:</td>
					<td width="10%">
						<select name="ses_status" id="ses_status" tabindex="2" title="Select Status" class="w50">
							<option value="">Select</option>
							<option value="0" <?php  if(isset($_SESSION['mgc_sess_user_status']) && $_SESSION['mgc_sess_user_status'] != '' && $_SESSION['mgc_sess_user_status'] == '0') echo 'Selected';  ?>>Not Verified</option>
						<?php $i=1; 
								foreach($userStatus as $key => $user_status) { 
									if($i<=2) {?>
							<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_user_status']) && $_SESSION['mgc_sess_user_status'] != '' && $_SESSION['mgc_sess_user_status'] == $key) echo 'Selected';  ?>><?php echo $user_status; ?></option>
						<?php 		} $i++; 
								}?>	
						
						</select>
					</td>
				</tr>

				<tr>
					
					<td  align="left" width="2%" style="padding-left:20px;"><label>Registered Date</label></td>
					<td width="1%" align="center">:</td>
					<td  width="15%">
						<input  type="text" autocomplete="off"  onkeypress="return dateField(event);" maxlength="10" class="input w50" name="ses_date" id="ses_date" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_user_registerdate']) && $_SESSION['mgc_sess_user_registerdate'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_user_registerdate'])); else echo '';?>"> (mm/dd/yyyy)
					</td>
				</tr>
				<tr><td align="center" colspan="9" style="padding-top:20px"><input type="submit" class="submit_button" name="Search" title="Search" id="Search" value="Search"></td></tr>
				<tr>
				</tr>								       
				<tr><td></td></tr>
			 </table>
			 </form>
		</td></tr>
		<tr><td height="20"></td></tr>
		<tr><td>
			<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
				<tr>
					<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0){ ?>
					<td align="left" width="20%">No. of User(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
					<?php } ?>
					<td align="center">
							<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ) {
								pagingControlLatest($tot_rec,'UserList'); ?>
							<?php }?>
					</td>
				</tr>
			</table>
		</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td colspan= '2' align="center">
			<?php displayNotification('User'); ?>
		</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td>
			<div class="tbl_scroll">
			
			  <form action="UserList" class="l_form" name="UserListForm" id="UserListForm"  method="post"> 
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr align="left">
						<th align="center" class="text-center" width="2%"><input onclick="checkAllRecords('UserListForm');" type="Checkbox" name="checkAll"/></th>
						<th align="center" width="3%" class="text-center">#</th>												
						<th width="18%"><?php echo SortColumn('FirstName','User'); ?></th>
						<th width="13%"><?php echo SortColumn('Email','Email'); ?></th>
						<th width="12%"><?php echo SortColumn('FBId','Facebook Id');?></th>
						<th width="9%"><?php echo SortColumn('Location','Location'); ?></th>
						<th width="15%">TiLT$ / Virtual Coins / Custom Prize</th>
						<th width="2%">Platform</th>	
						<th width="3%"><?php echo SortColumn('DateCreated','Registered Date'); ?></th>
					</tr>
					
					<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ) { 
						$style = ' style="float:left;width:126px;" ';
					?>
					
					<?php foreach($userListResult as $key=>$value){
								$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
								$original_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
								$photo = $value->Photo;
								if(isset($photo) && $photo != ''){
									$user_image = $photo;		
									$image_path_rel = USER_THUMB_IMAGE_PATH_REL.$user_image;
									$original_path_rel = USER_IMAGE_PATH_REL.$user_image;
									if(SERVER){
										if(image_exists(1,$user_image)){
											$image_path = USER_THUMB_IMAGE_PATH.$user_image;
											$original_path = USER_IMAGE_PATH.$user_image;
										}
									
									}
									else if(file_exists($image_path_rel)){
											$image_path = USER_THUMB_IMAGE_PATH.$user_image;
											$original_path = USER_IMAGE_PATH.$user_image;
									}
								}
							$userName	=	'';
							if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
								$userName = 'Guest'.$value->id;
							else if(isset($value->FirstName)	&&	isset($value->LastName)) 	
								$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
							else if(isset($value->FirstName))	
								$userName	=	 ucfirst($value->FirstName);
							else if(isset($value->LastName))	
								$userName	=	ucfirst($value->LastName);
					 ?>									
					<tr id="test_id_<?php echo $value->id;?>"	>
						<td valign="top" align="center"><input id="checkedrecords" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" hashCount="<?php if(isset($value->hash_count) && $value->hash_count > 0 ) echo $value->hash_count; ?>"/></td>
						<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td valign="top" align="center" >
							<div  style="<?php if(isset($cover_path) && $cover_path != '' ) { ?>
													background: url('<?php echo $cover_path;?>') no-repeat;<?php 
												} else { 
													?>background: none no-repeat; 
										<?php 	} ?>;background-size:cover;float:left">
										<a <?php if(isset($original_path) && $original_path != ADMIN_IMAGE_PATH.'no_user.jpeg' ) { ?> href="<?php echo $original_path; ?>" class="fancybox"  <?php } ?> title="View Photo"  ><img class="user_img" width="36" height="36" src="<?php echo $image_path;?>" ></a>
							</div>
							
							<div class="user_profile">
								<p align="left" style="padding-left:50px;padding-bottom:2px">
									<?php if(isset($value->UniqueUserId) && $value->UniqueUserId !='') { echo 'Guest'.$value->id; } else if(isset($userName) && $userName != '') { echo  '<a class="recordView" href="UserDetail?viewId='.$value->id.'">'.trim($userName).'</a>'; } else echo '-';?>
								</p>
								<div class="userAction" style="display:block" id="userAction">
								<?php if(isset($value->UniqueUserId) && $value->UniqueUserId =='') { ?>	
								<?php if(isset($value->Status)	&&	$value->Status == 1 && isset($value->VerificationStatus) && $value->VerificationStatus == 0){ ?>
										<a class="" alt="Not verified" title="Not verified" style="color:gray;"><i class="fa fa-lg fa-check fa-lg" ></i></a>
										
								<?php } else if(isset($value->Status)	&&	$value->Status == 1) { ?>
										<a class="userIcon" alt=" Active" title="Active User" onclick="javascript:return confirm('Are you sure want to change the status?')" href="UserList?editId=<?php echo $value->id;?>&status=2"><i class="fa fa-user fa-lg"></i></a>
								<?php } else if(isset($value->Status)	&&	$value->Status == 2){ ?>
										<a class="userIcon" style="color:gray"  title="Inactive User" alt="Inactive User" onclick="javascript:return confirm('Are you sure want to change the status?')" href="UserList?editId=<?php echo $value->id;?>&status=1"><i class="fa fa-user fa-lg"></i></a>
								<?php } ?>	
								<?php } ?>	
								<?php if((!isset($value->UniqueUserId) || $value->UniqueUserId =='') &&  $value->Status != 3) { ?>
									<a href="UserManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="editUser"><i class="fa fa-edit fa-lg"></i></a>
								<?php } ?>	
								<?php if(!isset($value->UniqueUserId) || $value->UniqueUserId =='') { ?>
									<a href="UserDetail?viewId=<?php if(isset($value->id) && $value->id != '') echo $value->id; if(isset($value->UniqueUserId) && $value->UniqueUserId !='') echo '&UniqueUser=1'; ?>" title="View" alt="View" class="<?php  if($value->Status != 3) echo 'viewUser'; else echo 'viewUse'; ?>"><i class="fa fa-search-plus fa-lg"></i></a>
								<?php } ?>
								<?php if($value->Status != 3) { ?>
									<a onclick="javascript:return confirm('Are you sure to delete?')" href="UserList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" <?php if((!isset($value->UniqueUserId) || (isset($value->UniqueUserId) && $value->UniqueUserId =='')) ) echo ' class="deleteUser" '; ?>><i class="fa fa-trash-o fa-lg"></i></a>
								<?php } ?>	
								<?php if(isset($chatIds) && is_array($chatIds) && count($chatIds) > 0 && in_array($value->id,$chatIds)) { ?>
									<a href="Messages?cs=1&uid=<?php echo $value->id; ?>" class="chatUser" title="Chat" alt="Chat" ><i class="fa fa-comments fa-lg" ></i></a>
									<?php } else { ?>
									<a href="javascript:void(0);" style="color:gray; cursor:auto;" title="No Chat" class="chatUser" alt="No Chat" ><i class="fa fa-comments fa-lg"></i></a>
									<?php }?>
								<?php if(isset($value->tcount) && $value->tcount > 0) { ?>
									<a href="TournamentList?cs=2&createdId=<?php echo $value->id; ?>&tournament_type=2" class="chatUser userCreated" title="Tournaments List" ><i class="fa fa-trophy fa-lg" ></i></a>
									<?php } else { ?>
									<a href="javascript:void(0);" style="color:gray; cursor:auto;" title="No Tournaments" class="chatUser" ><i class="fa fa-trophy fa-lg"></i></a>
									<?php }?>
									<?php if(isset($value->Status)	&&	$value->Status != 1 && isset($value->VerificationStatus)	&&	$value->VerificationStatus == 0) { ?>
										<a class="chatUser" alt="Not verified" title="Not verified" style="color:gray;"><i class="fa fa-lg fa-check fa-lg" ></i></a>
									<?php } else {?>
										<!--<a href="PaymentHistory?cs=1&UserId=<?php echo $value->id;?>" style="" title="Payment History" class="chatUser" >PH</a>-->
									<?php } ?>
								</div>
							</div>
						</td>

						<td valign="top"><?php if(isset($value->Email) && $value->Email != '' ){ echo $value->Email;}else echo '-';?></td>
						<td valign="top"><?php 
								if(isset($value->FBId) && $value->FBId != '')
									echo $value->FBId;
								else			
									echo " - "; 
							?>
						</td>

						<td valign="top"><?php if(isset($value->Location) && $value->Location != '' && $value->Location != '(null)'){ echo $value->Location; }else echo '-';?></td>	
						<td valign="top">
						<div><div <?php echo $style; ?>><strong>Won TiLT$&nbsp;</strong></div><strong>:&nbsp;</strong>
							<?php 	
							if(isset($coinsCountArray) && is_array($coinsCountArray) && count($coinsCountArray) > 0 && array_key_exists($value->id,$coinsCountArray) && $coinsCountArray[$value->id]['tiltCoin'] > 0) 
				 						echo '<a href="UserCoins?cs=1&UserId='.$value->id.'&userName='.$userName.'&tiltCoins='.$coinsCountArray[$value->id]['tiltCoin'].'" class="pop_up">'.number_format($coinsCountArray[$value->id]['tiltCoin']).'</a>'; 
									else echo '0'; 
							?>
						</div>
						<div><div <?php echo $style; ?>><strong>Won Virtual Coins&nbsp;</strong></div><strong>:&nbsp;</strong>
							<?php 	
							if(isset($coinsCountArray) && is_array($coinsCountArray) && count($coinsCountArray) > 0 && array_key_exists($value->id,$coinsCountArray) && $coinsCountArray[$value->id]['virtCoin'] > 0) 
				 						echo '<a href="UserCoins?cs=1&UserId='.$value->id.'&userName='.$userName.'&virtCoins='.$coinsCountArray[$value->id]['virtCoin'].'" class="pop_up">'.number_format($coinsCountArray[$value->id]['virtCoin']).'</a>'; 
									else echo '0';
							?>
						</div>
						<div><div <?php echo $style; ?>><strong>Won Custom Prize&nbsp;</strong></div><strong>:&nbsp;</strong>
							<?php 	
							if(isset($coinsCountArray) && is_array($coinsCountArray) && count($coinsCountArray) > 0 && array_key_exists($value->id,$coinsCountArray) && $coinsCountArray[$value->id]['customCount'] > 0) 
				 						echo '<a href="UserCoins?cs=1&UserId='.$value->id.'&userName='.$userName.'&customCount='.$coinsCountArray[$value->id]['customCount'].'" class="pop_up">'.number_format($coinsCountArray[$value->id]['customCount']).'</a>'; 
									else echo '0'; 
							?>
						</div>
						<div><div <?php echo $style; ?>><strong>Redeemed &nbsp;</strong></div><strong>:&nbsp;</strong>
							<?php 	if(isset($redeemResult) && is_array($redeemResult) && count($redeemResult) > 0 && array_key_exists($value->id,$redeemResult)) 
										echo '<a href="UserPurchaseList?cs=1&userId='.$value->id.'&userName='.$userName.'&totalCoins='.$redeemResult[$value->id].'" class="purchase_pop_up">'.number_format($redeemResult[$value->id]).'</a>'; 
									else echo '0'; 
							?>
						</div>
						<div><div <?php echo $style; ?>><strong>TiLT$ Balance&nbsp;</div>:&nbsp; <?php if(isset($value->Coins)	&&	$value->Coins !='') echo number_format($value->Coins); else echo '0';?></strong></div>
						<div><div <?php echo $style; ?>><strong>Virtual Coins Balance&nbsp;</div>:&nbsp; <?php if(isset($value->VirtualCoins)	&&	$value->VirtualCoins !='') echo number_format($value->VirtualCoins); else echo '0';?></strong></div>
						</td>
						<td valign="top"><?php if(isset($value->Platform) && $value->Platform!='' && isset($platform_array[$value->Platform])){ echo $platform_array[$value->Platform]; }else echo '-';?></td>	
						<td valign="top"><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
					</tr>
					<?php } ?> 																		
				</table>
				<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0){ 
						bulk_action($statusArray);
				?>
				<?php } ?>
				</form>
				<?php } else { ?>	
					<tr>
						<td colspan="16" align="center" style="color:red;">No User(s) Found</td>
					</tr>
				<?php } ?>
			</div>
			
			</td>
		</tr>
	</table>
       
<?php commonFooter(); ?>
<script type="text/javascript">
$(".fancybox").colorbox({
	title:true,
	maxWidth:"50%", 
	maxHeight:"50%"
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
   
  $(document).ready(function() {		
		$(".pop_up").colorbox(
			{
				iframe:true,
				width:"60%", 
				height:"85%",
				title:true,
		});
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
	
$(".userCreated").colorbox(
{
			iframe:true,
			width:"90%", 
			height:"75%",
			title:true
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
