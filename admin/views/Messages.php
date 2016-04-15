<?php
require_once('includes/CommonIncludes.php');
require_once('controllers/MessageController.php');
$messageModelObj   =   new messageController();
require_once('controllers/UserController.php');
$userModelObj   =   new userController();
$j=0;
$msg_result =  $chatName = $time  = $latest = $userId = $deleteClass = $pageControl	=  '';
$deletedIds = array();
$delete_id  = 0;
if(!isset($_GET['group']) && !isset($_GET['friends'])){
	$_GET['friends'] = 1;
}
if(isset($_GET["cs"]) && $_GET["cs"]==1)
	unset($_SESSION['mgc_sess_chat_name']);
if(isset($_GET["uid"]) && $_GET["uid"]!=''){
	$_SESSION['broadtags_ses_from_id'] = '';
	$pageControl	=	'?uid='.$_GET["uid"];
	$from_user_id = $_GET["uid"];
	destroyPagingControlsVariables();
	$field     = ' FirstName,LastName,UniqueUserId,id as userId ';
	$condition = ' id = '.$_GET["uid"];
	$from_users_name  = $userModelObj->selectUserDetails($field,$condition);
	if(isset($from_users_name) && is_array($from_users_name ) && count($from_users_name) > 0){
		if(isset($from_users_name[0]->UniqueUserId) && $from_users_name[0]->UniqueUserId !='') { $chatName	=	 'Guest'.$from_users_name[0]->userId; } 
		else {
			if(isset($from_users_name[0]->FirstName)	&&	isset($from_users_name[0]->LastName)) 	
				$chatName	=	ucfirst($from_users_name[0]->FirstName).' '.ucfirst($from_users_name[0]->LastName);
			else if(isset($from_users_name[0]->FirstName))	
				$chatName	=	 ucfirst($from_users_name[0]->FirstName);
			else if(isset($from_users_name[0]->LastName))	
				$chatName	=	ucfirst($from_users_name[0]->LastName);
		}
	}
	if(isset($_POST['Search']) && $_POST['Search'] != ''){
			$_POST          = unEscapeSpecialCharacters($_POST);
			$_POST          = escapeSpecialCharacters($_POST);
		if(isset($_POST['user']))
			$_SESSION['mgc_sess_chat_name'] 	=	$_POST['user'];
	}
	$users_chat_lists = $messageModelObj->getUsersChatLists($from_user_id);
	if(isset($users_chat_lists) && is_array($users_chat_lists ) && count($users_chat_lists) > 0){
		$msg_result = sorting($users_chat_lists ,'DateCreated');
		$msg_result = array_reverse($msg_result);
	}	
	if(!isset($_SESSION['mgc_ses_from_timeZone']) || $_SESSION['mgc_ses_from_timeZone'] == ''){
	}
}
 else {
	header('location:UserList?cs=1');
	die();
}
?>
<?php commonHead();?>
<body>
<?php top_header(); ?>
<div class="box-header">
 	<h2><i class="fa fa-list"></i>Chat List <?php if(isset($chatName)	&&	$chatName !='') echo ' - '.$chatName; ?></h2>
</div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="">
		
	<tr>
		<td class="chat_list " style="padding-left:10px;">
		 	<ul class="tabs">
			</ul>					
		</td>
	</tr>
	<tr>
		<td align="center" >
			<form name="search_category" action="Messages<?php if(isset($pageControl)) echo $pageControl; ?>" method="post">
				<table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="90%">
					<tr><td height="15"></td></tr>
					<tr >													
						<td width="7%" style="padding-left:20px;"><label>User</label></td>
						<td width="1%" align="center">:</td>
						<td align="left" width="23%" height="40">
							<input type="text" class="input" name="user" id="user"  value="<?php  if(isset($_SESSION['mgc_sess_chat_name']) && $_SESSION['mgc_sess_chat_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_chat_name']);  ?>" >
						</td>
					</tr>
					<tr><td align="center" colspan="3" style="padding-top:20px"><input type="submit" title="Search" class="submit_button" name="Search" id="Search" value="Search"></td></tr>
					<tr><td height="10"></td></tr>
				</table>
			</form>
		</td>
	</tr>
	<tr><td height="30"></td></tr>
	<tr>
		<td valign="top" align="left">				
				<div class="conver_detail">				
				<ul class="left" ><?php
				if(isset($_GET['friends']) && $_GET['friends'] == 1){
					if(is_array($msg_result) && count($msg_result) > 0){ 
						foreach($msg_result as $key=>$value){
							if($value->user_id !='') {
									$time		=  displayConversationTime($value->message_sent_date);
									$user_image = $value->Photo;
									$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
									if(isset($user_image) && $user_image != '')
									{
										if ($_SERVER['HTTP_HOST'] == '172.21.4.104'){
											if(file_exists(USER_THUMB_IMAGE_PATH_REL.$user_image)){
												$image_path = USER_THUMB_IMAGE_PATH.$user_image;
											}
										}
										else{
											if(image_exists(1,$user_image)){
												$image_path = USER_THUMB_IMAGE_PATH.$user_image;
											}
										}
									}
									$name = '';
									if(isset($value->FirstName))
										$name = ucfirst($value->FirstName);
									if(isset($value->LastName))
										$name   .= ' '.ucfirst($value->LastName);
									if($name!='')
										$name = displayText($name,'30','1');
									else
										$name = $value->Email;
									$type = $value->Type;
									if($type=='2'){
										$message = getCommentTextEmoji('web', $value->Message, $value->Platform) ;
										if($message == '#?@?#1#?@?#' )
											$message = "Contact Shared successfully";
										else if($message == '#?@?#0#?@?#' )
											$message = "Contact has been unshared successfully";
									}
									else if($type == '1'){
										$message = 'Image Transfer...';
									}	
									$j++;
							if($key == 0) {
								$latest = $value->user_id;
							}
							$deleteClass	=	2;
						?>
							<li class="users user_<?php echo $value->user_id;?> <?php  if($deleteClass == '3') { if($value->Status == 2 )  echo 'unread '; } ?>"  onclick="loadMessage('<?php echo $value->user_id;?>','<?php echo $from_user_id; ?>');" style="cursor:pointer;clear:both;"> <!-- class="sel" -->
								<span class="img"><img class="img_border" src="<?php echo $image_path;?>" width="30" height="30" alt="image"></span>
								<span class="text">	
									<span class="user_name"><?php if(isset($value->UniqueUserId) && $value->UniqueUserId !='') { echo 'Guest'.$value->userId; } else echo $name;?></span>
									<span class="date"><?php echo $time;?></span>
									<span class="text_dummy"><?php echo $message; ?></span>
								</span>
							</li>
						<?php }
						}
					} 
					else{ ?>
					<li class="no_msgfound_conver"> No Message Found</li>
			<?php	}
				}			
				else{ ?>
					<li class="no_msgfound_conver"> No Message Found</li>
			<?php	} ?>
				</ul>
				<div class="right">
					<div style="display:block;" class="loader">
							<div class="load_img">
								<img src="<?php echo ADMIN_IMAGE_PATH; ?>loader_image.gif" width="50" height="50" alt="image"> 
							</div>
						</div> 
					
					<div class="scroll">
						
					</div>
				</div>	 
			</div>
		</td>
	</tr>
	<tr><td height="10"></td></tr>
	<?php if(isset($_GET['friends']) && $_GET['friends'] == 1){ ?>
	<tr>
		<td style="padding-left:1%;">
			<table width="33%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td align="center" style="white-space:nowrap;margin-right:20px;"> <span style="color:#236CB0;"><i class="fa fa-envelope fa-lg"></i> Unread Messages</span></td>					
				</tr>
			</table>
		</td>
	</tr>
	<?php } ?>
	<tr >
		<td valign="middle" align="center">
		<a href="UserList?user_back=1" title="Back" tabindex="2" alt="Back" id="back" class="submit_button">Back</a>
		</td>
	</tr>
	<tr><td height="15"  ></td></tr>
	</table>
</body>
<?php commonFooter(); ?>
<script>
	<?php if(!isset($_GET['group'])) { ?>
		loadMessage('<?php echo $latest;?>','<?php echo $from_user_id; ?>');	
	<?php }?>
	<?php if(isset($_GET['group'])) { ?>
	<?php }?>
</script>
</html>

	