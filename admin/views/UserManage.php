<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/UserController.php');
require_once('controllers/AdminController.php');
require_once('controllers/CoinsController.php');
require_once("includes/phmagick.php");
admin_login_check();
commonHead();

$adminLoginObj  =   new AdminController();
$userObj   		=   new UserController();
$coinsManageObj =   new CoinsController();
$field_focus	=	'username';
$class			=	$ExistCondition		=	$location		=	$photoUpdateString	=	$user_image_name = '';
$email_exists 	= 	$facebookid_exist	=	$linkedid_exist	=	$twitter_exist		=	$googleplus_exist	=	$userName_exists	=	0;
$fields	= " * ";
$cond	= "Status = 1 ORDER BY RAND() LIMIT 10";
$gamefilesResult  = $adminLoginObj->selectDefaultImage($fields,$cond);
if(is_array($gamefilesResult) && isset($gamefilesResult) && count($gamefilesResult)>0){
	foreach($gamefilesResult as $imageval)
	{
		$image_array[]		=	$imageval->Image;
		$image_patharray[]	=	DEFAULT_USER_IMAGE_PATH.$imageval->Image;
		$image_thumb_patharray[]	=	DEFAULT_USER_THUMB_IMAGE_PATH.$imageval->Image;
		$imageid_array[]	=	$imageval->id;
	}
}

if(isset($_GET['editId']) && $_GET['editId'] != '' ){
	$condition       = " id = ".$_GET['editId']." and Status in (1,2,4)";
	$field			 = " * ";
	$userDetailsResult  = $userObj->selectUserDetails($field,$condition);
	if(isset($userDetailsResult) && is_array($userDetailsResult) && count($userDetailsResult) > 0){
		$firstname 	=	$userDetailsResult[0]->FirstName;
		$lastname	=	$userDetailsResult[0]->LastName;
		$email      =	$userDetailsResult[0]->Email;
		$location   =	$userDetailsResult[0]->Location;
		$fbId   	=	$userDetailsResult[0]->FBId;
		if(isset($userDetailsResult[0]->Photo) && $userDetailsResult[0]->Photo != ''){
			$user_image_name = $userDetailsResult[0]->Photo;
			if(SERVER){
				if(image_exists(3,$user_image_name))
					$original_image_path = USER_IMAGE_PATH.$user_image_name;
				else
					$original_image_path = '';
				if(image_exists(1,$user_image_name)){
					$user_image_path = USER_THUMB_IMAGE_PATH.$user_image_name;
				}
			}else{
				if(file_exists(USER_IMAGE_PATH_REL.$user_image_name))
					$original_image_path = USER_IMAGE_PATH.$user_image_name;
				else
					$original_image_path = '';
				if(file_exists(USER_THUMB_IMAGE_PATH_REL.$user_image_name)){
					$user_image_path = USER_THUMB_IMAGE_PATH.$user_image_name;
				}
			}
		}
	}
}
if(isset($_POST['submit'])	&&	$_POST['submit']!="")
{
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	$ip_address     =   ipAddress();
	
		if(isset($_POST['email']) && $_POST['email'] != '')
			$ExistCondition .= " ( Email = '".$_POST['email']."' ";
		if($_POST['fbid'] != '')
			$ExistCondition  .= " or FBId = '".$_POST['fbid']."' ";	
		if($_POST['submit'] == 'Save')
			$id_exists = ") and id != '".$_POST['user_id']."' and Status in (1,2,4) ";
		else
			$id_exists = " ) and Status in (1,2,4) ";
			
		$firstname 			=	($_POST['firstname']);
		$lastname			=	($_POST['lastname']);
		$email      		=	(isset($_POST['email']) && $_POST['email'] != '')? $_POST['email'] : '';
		$location   		=	($_POST['location']);
		$fbId				=	($_POST['fbid']);
		$field 				= 	" * ";	
		$ExistCondition 	.=	$id_exists;
		
		// To check the already exist condition for the user email address and there fb id
		$alreadyExist    	=	$userObj->selectUserDetails($field,$ExistCondition);	
		
		if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0)	{
			if(($alreadyExist[0]->Email == $_POST['email']) && ($_POST['email'] != ''))
				$email_exists 			=	1;
			if(($alreadyExist[0]->FBId == $_POST['fbid']) && ($_POST['fbid'] != ''))	
				$facebookid_exist		=	1;
		}

		if($email_exists != '1' && $facebookid_exist != '1'	)	{
			if($_POST['submit'] == 'Save')	{		
				if(isset($_POST['user_id']) && $_POST['user_id'] != '')	{
					$fields = "FirstName            =	'".$_POST['firstname']."',
								LastName 			=	'".$_POST['lastname']."',
								Location			=	'".$_POST['location']."',
								IpAddress 			=	'".$ip_address."',
								FBId				=	'".$_POST['fbid']."',
								DateModified		=	'".date('Y-m-d H:i:s')."'";
					$userObj->updateUserDetails($fields,$condition);
					$insert_id = $_POST['user_id'];
				$_SESSION['notification_msg_code']	=	2;
				}
			}
			if($_POST['submit']		==	'Add')	{
				$setting_result 					=	$adminLoginObj->getDistance(' DefaultTilt, DefaultVirtualCoins ',' id=1 ');
				$_POST['DefaultTilt'] 			=	$setting_result[0]->DefaultTilt;
				$_POST['DefaultVirtualCoins'] 	=	$setting_result[0]->DefaultVirtualCoins;

				$_POST['ipaddress'] =	$ip_address;
				$insert_id   		=	$userObj->insertUserDetails($_POST);
				
				if($_POST['DefaultTilt'] > 0){
					$tiltArray = array("coin" =>$_POST['DefaultTilt'], "userId"=>$insert_id);
					$coinsManageObj->insertTiltCoin($tiltArray);
					$paymentArray = array("coin"=>$_POST['DefaultTilt'], "userId"=>$insert_id, "ctype"=>1, "type"=>6);
					$coinsManageObj->insertPaymentHistroy($paymentArray);
				}
				
				if($_POST['DefaultVirtualCoins'] > 0){
					$virtualcoinsArray = array("coin"=>$_POST['DefaultVirtualCoins'], "userId"=>$insert_id);
					$coinsManageObj->insertVirtualCoin($virtualcoinsArray);
					$paymentArray = array("coin"=>$_POST['DefaultVirtualCoins'], "userId"=>$insert_id, "ctype"=>2, "type"=>6);
					$coinsManageObj->insertPaymentHistroy($paymentArray);
				}
				
				$wordResult 	    =	$userObj->selectWordDetails();	
				$word               =	$wordResult[0]->words;
				$numeric            =	'1234567890';
				$numbers            =	substr(str_shuffle($numeric), 0, 3);
				$actualPassword     =	trim($word.$numbers).$insert_id;
				$password			=	sha1($actualPassword.ENCRYPTSALT);
				$updateString 		=	" Password = '" . $password . "',ActualPassword = '". $actualPassword . "' ";
				$condition 			=	"id = ".$insert_id;

				// To upload the user table with the generated password
				$userObj->updateUserDetails($updateString,$condition);
				
				$date_now 						=	date('Y-m-d H:i:s');
				$fields 						=	'*';
				$condition 						=	' 1';
				$login_result 					=	$adminLoginObj->getAdminDetails($fields,$condition);
				$mailContentArray['name'] 		=	ucfirst($_POST['firstname'])." ".ucfirst($_POST['lastname']);
				$mailContentArray['toemail'] 	=	$_POST['email'];
				$mailContentArray['password'] 	=	$actualPassword;
				$mailContentArray['subject'] 	=	'Registration';
				$mailContentArray['userType']	=	'User';
				$mailContentArray['from'] 		=	$login_result[0]->EmailAddress;
				$mailContentArray['fileName']	=	'registration.html';
				$mailContentArray['link']		=	'/ActivateUser.php?UID='.encode($insert_id).'&Type=1';
				sendMail($mailContentArray,'2');
				$_SESSION['notification_msg_code']	=	1;
			}

			if(isset($insert_id)	&&	$insert_id!='')	{
				if (isset($_POST['user_photo_upload']) && !empty($_POST['user_photo_upload'])) {
					$imageName 				= $insert_id . '_' .time() . '.png';
					$temp_image_path 		= TEMP_USER_IMAGE_PATH_REL . $_POST['user_photo_upload'];
					$image_path 			= UPLOAD_USER_PATH_REL . $imageName;
					$imageThumbPath     	= UPLOAD_USER_THUMB_PATH_REL.$imageName;
					$oldUserName			= $_POST['name_user_photo'];
					if ( !file_exists(UPLOAD_USER_PATH_REL) ){
						mkdir (UPLOAD_USER_PATH_REL, 0777);
					}
					if ( !file_exists(UPLOAD_USER_THUMB_PATH_REL) ){
						mkdir (UPLOAD_USER_THUMB_PATH_REL, 0777);
					}
					
					copy($temp_image_path,$image_path);
					
					$phMagick = new phMagick($image_path);
					$phMagick->setDestination($imageThumbPath)->resize(100,100);
					
					if ($_SERVER['HTTP_HOST'] != '172.21.4.104'){
						if($oldUserName!='') {
							if(image_exists(1,$oldUserName)) {
								deleteImages(1,$oldUserName);
							}
							if(image_exists(3,$oldUserName)) {
								deleteImages(3,$oldUserName);
							}
						}
						
						uploadImageToS3($imageThumbPath,1,$imageName);					
						uploadImageToS3($image_path,3,$imageName);
						unlink($image_path);
						unlink($imageThumbPath);
					}
					else if($oldUserName!='') {
						if ( file_exists(UPLOAD_USER_PATH_REL.$oldUserName) )
							unlink(UPLOAD_USER_PATH_REL.$oldUserName);
						if ( file_exists(UPLOAD_USER_THUMB_PATH_REL.$oldUserName) )
							unlink(UPLOAD_USER_THUMB_PATH_REL.$oldUserName);
					}
					$photoUpdateString	.= " Photo = '" . $imageName . "'";
					unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['user_photo_upload']);
				}else if(isset($_POST['default_image_path']) && !empty($_POST['default_image_path'])){
					if ( !file_exists(UPLOAD_USER_PATH_REL) ){
						mkdir (UPLOAD_USER_PATH_REL, 0777);
					}
					if ( !file_exists(UPLOAD_USER_THUMB_PATH_REL) ){
						mkdir (UPLOAD_USER_THUMB_PATH_REL, 0777);
					}
					$imageName 					= $insert_id . '_' .time() . '.png';
					$default_image_path 		= DEFAULT_USER_IMAGE_PATH .$_POST['default_image_path'];
					$default_thumb_image_path 	= DEFAULT_USER_THUMB_IMAGE_PATH .$_POST['default_image_path'];
					$image_path 				= UPLOAD_USER_PATH_REL . $imageName;
					$imageThumbPath     		= UPLOAD_USER_THUMB_PATH_REL.$imageName;
					$oldUserName				= $_POST['name_user_photo'];
					if ($_SERVER['HTTP_HOST'] != '172.21.4.104'){
						if($oldUserName!='') {
							if(image_exists(1,$oldUserName)) {
								deleteImages(1,$oldUserName);
							}
							if(image_exists(3,$oldUserName)) {
								deleteImages(3,$oldUserName);
							}
						}
						//Get image from s3 and write to server path
						file_put_contents($image_path,file_get_contents($default_image_path));
						file_put_contents($imageThumbPath,file_get_contents($default_thumb_image_path));
						//Move files from server path to s3 and unlink server path image
						uploadImageToS3($imageThumbPath,1,$imageName);					
						uploadImageToS3($image_path,3,$imageName);
						unlink($image_path);
						unlink($imageThumbPath);
					}
					else {
						copy(UPLOAD_DEFAULT_PATH_REL.$_POST['default_image_path'],$image_path);
						copy(UPLOAD_DEFAULT_THUMB_PATH_REL.$_POST['default_image_path'],$imageThumbPath);
						if($oldUserName!='') {
							if ( file_exists(UPLOAD_USER_PATH_REL.$oldUserName) )
								unlink(UPLOAD_USER_PATH_REL.$oldUserName);
							if ( file_exists(UPLOAD_USER_THUMB_PATH_REL.$oldUserName) )
								unlink(UPLOAD_USER_THUMB_PATH_REL.$oldUserName);
						}
					}
					$photoUpdateString	.= " Photo = '" . $imageName . "'";
				}
				if($photoUpdateString!='')
				{
					$condition 			= "id = ".$insert_id;
					$userObj->updateUserDetails($photoUpdateString,$condition);
				}
			}
			$pageval	=	"UserList";
			header("location:".$pageval);
			die();
		}	//	End of Already Exist condition
		else{
			if($email_exists == 1){
				$error_msg   = "Email address already exists";
				$field_focus = 'email';
			}
			else if($facebookid_exist	==	1	)	{
				$error_msg   = "Facebook Id already exists";
				$field_focus = 'fbid';
			}
			else if($linkedid_exist	==	1	)	{
				$error_msg   = "LinkedIn Id already exists";
				$field_focus = 'linkedid';
			}
			else if ($userName_exists == 1){
				$error_msg   = "Username already exists";
				$field_focus = 'username';
			}
			else if ($googleplus_exist == 1){
				$error_msg   = "GooglPlus Id already exists";
				$field_focus = 'googleid';
			}
			else if ($twitter_exist == 1){
				$error_msg   = "Twitter Id already exists";
				$field_focus = 'twitterid';
			}
			$display = "block";
			$class   = "error_msg";
		}
}
?>
<body onload="return fieldfocus('<?php echo $field_focus; ?>');">
	<?php top_header(); ?>
		
			<div class="box-header">
				<h2>
					<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo '<i class="fa fa-edit"></i>Edit '; else echo '<i class="fa fa-plus-circle"></i>Add ';?>User</h2>
			</div>
			<div class="clear">
				<form name="add_user_form" id="add_user_form" action="" method="post">
					<input type="Hidden" name="user_id" id="user_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
					 <table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="98%">
						<tr>
							<td align="center" valign="top">
								<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">
									<tr><td colspan="7" align="center" valign="top" class="msg_height">
										<div class="<?php echo $class;  ?> w50">
											<span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span>
										</div>
									</td></tr>
									<tr>
										<td  height="50" width="15%" align="left"  valign="top"><label>First Name&nbsp;<span class="required_field">*</span></label></td>
										<td width="3%" align="center"  valign="top">:</td>
										<td align="left"  height="40"  valign="top" width="32%">
											<input type="text" class="input" name="firstname" id="firstname" maxlength="50" value="<?php if(isset($firstname) && $firstname != '') echo $firstname;  ?>" >
										</td>
						
										<td height="50" align="left" valign="top" width="15%"><label>Last Name&nbsp;<span class="required_field">*</span></label></td>
										<td  align="center"  valign="top" width="3%">:</td>
										<td align="left"  height="40"  valign="top" width="32%">
											<input type="text" class="input" id="lastname" name="lastname" maxlength="30" value="<?php if(isset($lastname) && $lastname != '' ) echo $lastname;  ?>" >
										</td>
									</tr>						
									<tr>
										<td height="50" align="left"  valign="top"><label>Email&nbsp;<span class="required_field">*</span></label></td>
										<td align="center"  valign="top">:</td>
										<td align="left"  height="40"  valign="top">
											<input type="text"  id="email" name="email" maxlength="90" value="<?php if(isset($email) && $email != '') echo $email;  ?>" <?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo 'readonly class="input disabled"'; else echo ' class="input" '; ?>>
										</td>
										
										<td height="50" align="left"  valign="top"><label>Location</label></td>
										<td align="center"  valign="top">:</td>
										<td align="left"  height="40"  valign="top">
											<input type="text" class="input" id="location" name="location" value="<?php if(isset($location) && $location != '') echo $location;  ?>" >
										</td>
									</tr>
									<tr>
										<td height="50" align="left"  valign="top"><label>Facebook Id</label></td>
										<td align="center"  valign="top">:</td>
										<td align="left"  height="40"  valign="top">
											<input type="text" class="input" name="fbid" id="fbid" maxlength="90" value="<?php  if(isset($fbId) && $fbId != '' ) echo $fbId;   ?>">
										</td>
										
										<td height="60"  align="left"  valign="top"  valign="top"><label>Photo&nbsp;<span class="required_field">*</span></label></td>
										<td  align="center" valign="top">:</td>
										<td align="left"  height="60" valign="top">
											<div class="upload fleft">
												<input type="hidden" id="default_image_path" name="default_image_path" value="">
												<div style="clear: both;float: left"><a class="choose_image" href="ChooseDefaultImages<?php if(isset($original_image_path) && $original_image_path != '') echo '?oldImage='.$user_image_name?>" >Choose Image</a></div>
												<span class="error" for="empty_user_photo" generated="true" style="display: none">User Image is required</span>
												<div class="fakefile_photo" id="source_user_photo" style="float: left;clear: both;margin-top: 5px">
													<div id="user_photo_img">
														<?php  if(isset($user_image_path) && $user_image_path != ''){  ?>
															<a href="<?php if(isset($original_image_path) && $original_image_path != '') { echo $original_image_path; ?>" class="user_photo_pop_up"<?php } else { ?>Javascript:void(0);<?php } ?>" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $user_image_path;  ?>" width="75" height="75" alt="Image"/></a>
														<?php  }  ?>
													</div>
												</div>
											</div>
											
										<?php  if(isset($_POST['user_photo_upload']) && $_POST['user_photo_upload'] != ''){  ?><input type="Hidden" name="user_photo_upload" id="user_photo_upload" value="<?php  echo $_POST['user_photo_upload'];  ?>"><?php  }  ?>
										<input type="Hidden" name="empty_user_photo" id="empty_user_photo" value="<?php  if(isset($user_image_name) && $user_image_name != '') { echo $user_image_name; }  ?>" />
										<input type="Hidden" name="name_user_photo" id="name_user_photo" value="<?php  if(isset($user_image_name) && $user_image_name != '') { echo $user_image_name; }  ?>" />
										</td>
									</tr>
									
									<tr>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="6" align="center">
							<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
								<input type="submit" class="submit_button" name="submit" id="submit" value="Save" title="Save" alt="Save">
							<?php } else { ?>
							<input type="submit" class="submit_button" name="submit" id="submit" value="Add" title="Add" alt="Add">
							<?php } ?>
							<a href="<?php if(isset($_GET['back']) &&	$_GET['back'] !=''){ echo $_GET['back'];} else {echo "UserList";} ?>"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
						</td>
					</tr>	
						<tr><td height="10"></td></tr>				  
					</table>
				</form>
			</div>	
						  	
<?php commonFooter(); ?>
<script type="text/javascript">
$(".user_photo_pop_up").colorbox({title:true});

$(document).ready(function() {		
	$(".pop_up").colorbox(
		{
			iframe:true,
			width:"50%", 
			height:"45%",
			title:true
	});
	$(".choose_image").colorbox({
		iframe:true,
		width:"40%", 
		height:"45%",
		title:true
	});
	 colorbox = $(".choose_image").colorbox;
});
</script>
</html>