<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$userObj   =   new LogController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
	unset($_SESSION['mgc_sess_user_name']);
	unset($_SESSION['mgc_sess_email']);
	unset($_SESSION['mgc_sess_reg_user_status']);
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
		$_SESSION['mgc_sess_reg_user_status']	    	=	trim($_POST['ses_status']);
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " u.*";
$condition = " AND Status !=3 AND UniqueUserId = '' ";
$userListResult  = $userObj->getUserList($fields,$condition);

$tot_rec 		 = $userObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($userListResult)) {
	$_SESSION['curpage'] = 1;
	$userListResult  = $userObj->getUserList($fields,$condition);
}
?>
<body class="popup_bg" >
		 <div class="box-header"><h2><i class="fa fa-list"></i>User List</h2></div>
		 <div class="clear">
           <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
			<tr><td height="10"></td></tr>
			<tr><td class="filter_form" >
				<form name="search_category" action="RegisteredUsers" method="post">
            	<table align="center" cellpadding="6" cellspacing="0" border="0"width="98%">									       
					<tr><td></td></tr>
					<tr>													
						<td width="7%" ><label>User</label></td>
						<td width="2%" align="center">:</td>
						<td align="left" >
							<input type="text" class="input" name="ses_username" id="ses_username"  value="<?php  if(isset($_SESSION['mgc_sess_user_name']) && $_SESSION['mgc_sess_user_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_user_name']);  ?>" >
						</td>
						<td width="7%" ><label>Email</label></td>
						<td width="2%" align="center">:</td>
						<td align="left" >
							<input type="text" class="input" id="ses_email" name="ses_email"  value="<?php  if(isset($_SESSION['mgc_sess_email']) && $_SESSION['mgc_sess_email'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_email']);  ?>" >
						</td>
						<td  width="7%" style="padding-left:20px;"><label>Status</label></td>
						<td width="1%" align="center">:</td>
						<td width="">
							<select name="ses_status" id="ses_status" tabindex="2" title="Select Status" class="w51">
								<option value="">Select</option>
								<option value="0" <?php  if(isset($_SESSION['mgc_sess_reg_user_status']) && $_SESSION['mgc_sess_reg_user_status'] != '' && $_SESSION['mgc_sess_reg_user_status'] == '0') echo 'Selected';  ?>>Not Verified</option>
							<?php $i=1; 
									foreach($userStatus as $key => $user_status) { 
										if($i<=2) {?>
								<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_reg_user_status']) && $_SESSION['mgc_sess_reg_user_status'] != '' && $_SESSION['mgc_sess_reg_user_status'] == $key) echo 'Selected';  ?>><?php echo $user_status; ?></option>
							<?php 		} $i++; 
									}?>
							</select>
						</td>
						
					</tr>
					<tr><td  align="center" colspan="9"><input type="submit" class="submit_button" name="Search" id="Search" value="Search"></td></td></tr>
					<tr><td height="10"></td></tr>
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
								 	pagingControlLatest($tot_rec,'RegisteredUsers'); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td></tr>
			<tr><td height="10"></td></tr>
			<tr><td>
			<div class="tbl_scroll">
				
				  <form action="UserList" class="l_form" name="UserListForm" id="UserListForm"  method="post"> 
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
						<tr align="left">
							<th align="center" width="3%" class="text-center">#</th>												
							<th width="18%"><?php echo SortColumn('FirstName','User'); ?></th>
							<th width="18%"><?php echo SortColumn('Email','Email'); ?></th>
							<th width="18%"><?php echo 'Status'; ?></th>
							<th width="18%">Registered Date</th>
						</tr>
						<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ) { ?>
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
								if(isset($value->FirstName)	&&	isset($value->LastName)) 	
									$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
								else if(isset($value->FirstName))	
									$userName	=	 ucfirst($value->FirstName);
								else if(isset($value->LastName))	
									$userName	=	ucfirst($value->LastName);
								
						 ?>									
						<tr id="test_id_<?php echo $value->id;?>"	>
							<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td valign="top" align="center" >
								<div  style="<?php if(isset($cover_path) && $cover_path != '' ) { ?>
														background: url('<?php echo $cover_path;?>') no-repeat;<?php 
													} else { 
														?>background: none no-repeat; 
											<?php 	} ?>;background-size:cover;float:left">
											<img class="user_img" width="36" height="36" src="<?php echo $image_path;?>" >
								</div>
								
								<div class="user_profile">
									<p align="left" style="padding-left:50px">
										<?php if(isset($userName) && $userName != '')	{
											if($value->UniqueUserId != ''){
												echo "Guest".$value->id;
											}else if($value->Status == 3) {?>
											<?php echo trim($userName); 
											} else {?>
											<a href="#" class="recordView" onclick="location.href='UserDetail?viewId=<?php echo $value->id;?>&back=RegisteredUsers';"><?php echo trim($userName); 	
											} ?> </a>
											
											<?php } else echo ' - '; ?>
									</p>
								</div>
							</td>
							<td valign="top"><?php if(isset($value->Email) && $value->Email != '' ){ echo $value->Email;}else echo '-';?></td>
							<td valign="top"><?php if(isset($value->VerificationStatus) && $value->VerificationStatus == 0){echo  'Not Verified'; } else if(isset($value->Status)){ echo $value->Status == 1 ? 'Active' : ($value->Status == 2 ? 'Inactive' : '-') ;}else echo '-';?></td>
							<td valign="top"><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
						</tr>
						<?php } ?> 
						<?php } else { ?>	
						<tr>
							<td colspan="16" align="center" style="color:red;">No User(s) Found</td>
						</tr>
					<?php } ?>
					</table>
					</form>
					
					
						
				</div>
			</td></tr>
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
function close_this()
{
self.close();
}
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
