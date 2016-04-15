<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/UserController.php');
admin_login_check();
commonHead();
$userObj   =   new userController();
$original_image_path =  $original_cover_image_path = $actualPassword = '';
$interestStatus		 =	0;

if(isset($_GET['referList']) && $_GET['referList'] == 1 ){
	unset($_SESSION['mgc_sess_user_platform']);
	unset($_SESSION['mgc_sess_user_name']);
	unset($_SESSION['mgc_sess_email']);
	unset($_SESSION['mgc_sess_user_status']);
	unset($_SESSION['mgc_sess_location']);
	unset($_SESSION['mgc_sess_user_registerdate']);
}
if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
	$condition       	= "  AND user.Id = ".$_GET['viewId']." LIMIT 1 ";
	$field				=	' user.* ';
	$userDetailsResult  = $userObj->getUserDetails($field,$condition);
	
	if(isset($userDetailsResult) && is_array($userDetailsResult) && count($userDetailsResult) > 0){
		$userId					=	$userDetailsResult[0]->id;
		if(isset($userDetailsResult[0]->UniqueUserId) && $userDetailsResult[0]->UniqueUserId !='') {
			$guestuser				=	'Guest'.$userDetailsResult[0]->id;
		}
		$firstname       		=	$userDetailsResult[0]->FirstName;
		$lastname				=	$userDetailsResult[0]->LastName;
		$password				=	$userDetailsResult[0]->ActualPassword;
		$email      			= 	$userDetailsResult[0]->Email;
		$fbId       			= 	$userDetailsResult[0]->FBId;
		$location  				= 	$userDetailsResult[0]->Location;
		$dateCreated    		= 	$userDetailsResult[0]->DateCreated;
		$virtualCoins			=	$userDetailsResult[0]->VirtualCoins;
		$tiltCoins				=	$userDetailsResult[0]->Coins;
		$Status					=	$userDetailsResult[0]->Status;
		if(isset($userDetailsResult[0]->VerificationStatus) && $userDetailsResult[0]->VerificationStatus == 0)
			$Status				=	3;
		
		$uniqueUser	=	1;
		if(isset($userDetailsResult[0]->UniqueUserId) &&	$userDetailsResult[0]->UniqueUserId !='')
			$uniqueUser	=	0;
		if(isset($userDetailsResult[0]->Photo) && $userDetailsResult[0]->Photo != ''){
			$user_image = $userDetailsResult[0]->Photo;
			if(SERVER){
				if(image_exists(3,$user_image))
					$original_image_path = USER_IMAGE_PATH.$user_image;
				else
					$original_image_path = '';			
				if(image_exists(1,$user_image)){
					$image_path = USER_THUMB_IMAGE_PATH.$user_image;
				}
				else
					$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
			}else{
				if(file_exists(USER_IMAGE_PATH_REL.$user_image))
					$original_image_path = USER_IMAGE_PATH.$user_image;
				else
					$original_image_path = '';			
				if(file_exists(USER_THUMB_IMAGE_PATH_REL.$user_image)){
					$image_path = USER_THUMB_IMAGE_PATH.$user_image;
				}
				else
					$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
			}
		}
		else
			$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
	}	
}
if(isset($_GET['back']) && $_GET['back'] != 'TournamentPlayedUsers')
	$href_page	= 'VirtualCoinList';
else if(isset($_GET['back']) && $_GET['back'] == 'TournamentPlayedUsers')
	$href_page	= 'TournamentPlayedUsers';

?>
<body>
	<?php if(!isset($_GET['back']) || ( $_GET['back'] != 'RegisteredUsers' && $_GET['back'] != 'TournamentPlayedUsers') ){
		top_header(); 
	}?>
	
							 <div class="box-header"><h2>
							 	<i class='fa fa-search'></i>View User</h2>								
								<?php if(isset($Status) && $Status !=3 && (!isset($_GET['back']) || $_GET['back'] == '')){ ?>
									<span class="fright">
										<a class="addvirtcoins" href="AddVirtualCoin?id=<?php echo $_GET['viewId']; ?>&back=<?php if(isset($_GET['back'])) echo $_GET['back']; ?>"  title="Add Virtual Coins"><i class="fa fa-plus-circle"></i> Add Virtual Coins</a>
										<?php if(isset($virtualCoins) && !empty($virtualCoins) && $virtualCoins >0) { ?>
											<a class="addvirtcoins" href="AddVirtualCoin?id=<?php echo $_GET['viewId']; ?>&back=<?php if(isset($_GET['back'])) echo $_GET['back']; ?>&remove=1"  title="Remove Virtual Coins"><i class="fa fa-minus-circle"></i> Remove Virtual Coins</a>
										<?php } ?>
										<?php if(isset($uniqueUser) && $uniqueUser !=0){ ?>
											<a class="addTiltCoins" href="AddTiltCoin?id=<?php echo $_GET['viewId']; ?>&back=<?php if(isset($_GET['back'])) echo $_GET['back']; ?>"  title="Add TiLT$"><i class="fa fa-plus-circle"></i> Add TiLT$</a>
											<?php if(isset($tiltCoins) && !empty($tiltCoins) && $tiltCoins >0) { ?>
											<a class="addTiltCoins" href="AddTiltCoin?id=<?php echo $_GET['viewId']; ?>&back=<?php if(isset($_GET['back'])) echo $_GET['back']; ?>&remove=1"  title="Remove TiLT$"><i class="fa fa-minus-circle"></i> Remove TiLT$</a>
											<?php } ?>
										<?php } ?>
									</span>
								<?php } ?>
							 </div>
							 <div class="clear">
							 
						  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="list headertable" width="98%">							        
									
								<?php if(isset($_GET['tilt'])) { ?>	
								<tr><td align="center"><?php displayNotification('TiLT$'); ?></td></tr>
								<?php } else{ ?>
								<tr><td align="center"><?php displayNotification('Virtual Coins'); ?></td></tr>
								<?php } ?>
								<tr><td class="msg_height"></td></tr>
									<tr>
										<td align="center">
											<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">
												
												<tr>
													<td align="left" valign="top" width="15%"><label>First Name</label></td>
													<td align="center" valign="top" width="3%">:</td>
													<td align="left" valign="top" width="32%">
														<?php if(isset($guestuser) && $guestuser!='') echo $guestuser; else if(isset($firstname) && $firstname != '') echo $firstname; else echo '-'; ?>
													</td>										
													<td align="left" valign="top" width="15%"><label>Last Name</label></td>
													<td align="center" valign="top" width="3%">:</td>										
													<td align="left" valign="top" width="32%">
														<?php if(isset($lastname) && $lastname != '') echo $lastname;  else echo '-'; ?>
													</td>										
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top" ><label>Email</label></td>
													<td align="center" valign="top">:</td>										
													<td align="left" valign="top"><?php if(isset($email) && $email != '') echo $email;  else echo '-'; ?></td>
													<td align="left" valign="top"><label>Password</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><?php if(isset($password) && $password != '0') echo $password; else echo '-'; ?></td>									
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top"><label>Facebook Id</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top" ><?php  if(isset($fbId) && $fbId != '' ) echo $fbId; else echo '-';  ?></td>
													<td  align="left" valign="top"><label>Registered Date</label></td>
													<td align="center" valign="top">:</td>
													<td align="left"   valign="top"><?php if(isset($dateCreated) && $dateCreated != '' ) echo date('m/d/Y',strtotime($dateCreated)); else echo '-'; ?></a></td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top"><label>Virtual Coins</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top" ><?php  if(isset($virtualCoins) && $virtualCoins != '' ) echo number_format($virtualCoins); else echo '0';  ?></td>
													<?php if(isset($uniqueUser) && $uniqueUser !=0){ ?>
													<td  align="left" valign="top"><label>TiLT$</label></td>
													<td align="center" valign="top">:</td>
													<td align="left"   valign="top"><?php if(isset($tiltCoins) && $tiltCoins != '' ) echo number_format($tiltCoins); else echo '0'; ?></a></td>
													<?php } else { ?>
													<td align="left" valign="top"><label>Location</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><?php if(isset($location) && $location != '') echo $location; else echo '-'; ?></td>
													<?php } ?>
												</tr>												
												<tr><td height="20"></td></tr>	
												<tr>
													<td align="left" valign="top"><label>Photo</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top">
													<?php if(isset($_GET['back'])) { ?>
														<?php if(isset($image_path) && $image_path != '') { ?> <img class="img_border" width="75" height="75" src="<?php echo $image_path;?>"><?php } ?>
													<?php } else { ?>
														<a <?php if(isset($original_image_path) && $original_image_path != '') { ?> href="<?php echo $original_image_path; ?>" class="user_photo_pop_up"<?php } else { ?> href="Javascript:void(0);"<?php } ?> title="Click here" alt="Click here" ><?php if(isset($image_path) && $image_path != '') { ?> <img class="img_border" width="75" height="75" src="<?php echo $image_path;?>"><?php } ?></a>
													<?php } ?>
													</td>
													<?php if(isset($uniqueUser) && $uniqueUser !=0){ ?>
													<td align="left" valign="top"><label>Location</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><?php if(isset($location) && $location != '') echo $location; else echo '-'; ?></td>
													<?php } ?>
													</tr>
													<tr><td height="20"></td></tr>
											</table>
										</td>
									</tr>
									<tr><td height="20"></td></tr>														
									<?php if(isset($interestStatus) && $interestStatus > 0){?>
									<tr>										
										<td colspan="6" align="center">
											<a href="UserInterest?uid=<?php if(isset($userId) && $userId != '') echo $userId; ?>" class="interest_pop_up cboxElement submit_button"  alt="Interest" title="Interest" >Interest</a>
										</td>
									</tr>
									<tr><td height="20"></td></tr>
									<?php }?>
									<tr>										
										<td colspan="6" align="center">	
											<?php if(isset($uniqueUser) && $uniqueUser !=0 && isset($Status) && $Status !=3 && (!isset($_GET['back']) || $_GET['back'] == '')) { ?>
													<a href="UserManage?editId=<?php if(isset($_GET['viewId']) && $_GET['viewId'] != '') { echo $_GET['viewId']; } if(isset($_GET['back']) && $_GET['back'] !=''){ echo '&back='.$_GET['back'];}  ?>" title="Edit" alt="Edit" class="submit_button">Edit</a>			
											<?php } ?>
											<?php if(isset($_GET['back'])	&&	$_GET['back']== 'TournamentPlayedUsers'){ ?>
														<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
											<?php } else if(isset($_GET['referList'])	&&	$_GET['referList']==1	&&	isset($_SESSION['referPage'])	&&	$_SESSION['referPage']!=''){ ?>
														<a href="<?php echo $_SESSION['referPage'];?>" class="submit_button referpage" name="Back" id="Back" title="Back" alt="Back" >Back </a>
														<?php 	unset($_SESSION['referPage']);
												  } else if(isset($_GET['back'])	&&	$_GET['back'] !=''){?>
														<a href="<?php echo $_GET['back'];?>" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
											<?php } else { ?>
														<a href="<?php if(isset($href_page) && $href_page != '' ) echo $href_page; else echo 'UserList';?>" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
											<?php } ?>
										</td>
									</tr>		
									<tr><td height="10"></td></tr>						   
								</table>
						  </div>
<?php commonFooter(); ?>
<script type="text/javascript">	
	$(document).ready(function() {		
		$(".interest_pop_up").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"80%",
				title:true,
		});
		$(".user_photo_pop_up").colorbox({title:true});
		$(".interest_pop_up").colorbox(
		{
				iframe:true,
				width:"73%", 
				height:"45%",
				title:true
		});
		$(".addvirtcoins").colorbox({
				iframe:true,
				width:"40%", 
				height:"45%",
				title:true,
		});
		$(".addTiltCoins").colorbox(
		{
				iframe:true,
				width:"600", 
				height:"400",
				title:true,
		});
	});	
$(function(){

   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '580';
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
</script>
</html>
