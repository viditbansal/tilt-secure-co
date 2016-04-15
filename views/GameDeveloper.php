<?php 
require_once('includes/CommonIncludes.php');
require_once("includes/phmagick.php");
require_once('controllers/DeveloperController.php');
$gameDevObj   =   new DeveloperController();
developer_login_check();
$devId				=	$_SESSION['tilt_developer_id'];
$photoUpdateString 	=	'';
$error_msg			=	'';
$Condition			=	' id = '.$devId.' ';
$gameDevDetails    	=	$gameDevObj->checkGameDeveloperLogin($Condition);
if(isset($gameDevDetails) && is_array($gameDevDetails) && count($gameDevDetails)>0){ //edit pre populate details
	$company			=	$gameDevDetails[0]->Company;
	$contactName		=	$gameDevDetails[0]->Name;
	$email				=	$gameDevDetails[0]->Email;
	$devPhoto			=	$gameDevDetails[0]->Photo;
	$original_image_path	=	'NOIMAGE';
	$user_image_path		=	GAME_IMAGE_PATH.'developer_logo.png';
	if($devPhoto !=''){
		if (SERVER){
			if(image_exists(15,$devPhoto))
				$original_image_path = DEVELOPER_IMAGE_PATH.$devPhoto;
			if(image_exists(16,$devPhoto))
				$user_image_path = DEVELOPER_THUMB_IMAGE_PATH.$devPhoto;
		} else {
			if(file_exists(DEVELOPER_IMAGE_PATH_REL.$devPhoto))
				$original_image_path	=	DEVELOPER_IMAGE_PATH.$devPhoto;
			if(file_exists(DEVELOPER_THUMB_IMAGE_PATH_REL.$devPhoto))
				$user_image_path	=	DEVELOPER_THUMB_IMAGE_PATH.$devPhoto;
		}
	}
}
if(isset($_POST['submit'])	&&	$_POST['submit']!="")
{
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	$company 		=	trim($_POST['company']);
	$contactName	=	trim($_POST['contact_name']);
	$condition		=	' id= '.$devId." ";
	$fields			=	"Company		=	'".trim($_POST['company'])."',
						Name 			=	'".trim($_POST['contact_name'])."',
						DateModified	=	'".date('Y-m-d H:i:s')."'";
	if($_POST['password'] !=''){
		$fields		.=	" ,Password 	= 	'".sha1(trim($_POST['password']))."',
						 ActualPassword	=	'".trim($_POST['password'])."'";
	}
	$_SESSION['tilt_developer_name'] 	= $_POST['contact_name'];
	$_SESSION['tilt_developer_company']	= $_POST['company'];
	$gameDevObj->updateGameDevDetails($fields,$condition);
	$insert_id = $devId;
	if(isset($insert_id)	&&	$insert_id!='')	{
		$photoUpdateString	=	"";
		if (isset($_POST['developer_photo_upload']) && !empty($_POST['developer_photo_upload'])) {
			$imageName 				= $insert_id . '_' .time() . '.png';
			$temp_image_path 		= TEMP_IMAGE_PATH_REL . $_POST['developer_photo_upload'];
			$image_path 			= UPLOAD_DEVELOPER_PATH_REL . $imageName;
			$imageThumbPath     	= UPLOAD_DEVELOPER_THUMB_PATH_REL.$imageName;
			$oldUserName			= $_POST['name_developer_photo'];
			if ( !file_exists(UPLOAD_DEVELOPER_PATH_REL) ){
				mkdir (UPLOAD_DEVELOPER_PATH_REL, 0777);
			}
			if ( !file_exists(UPLOAD_DEVELOPER_THUMB_PATH_REL) ){
				mkdir (UPLOAD_DEVELOPER_THUMB_PATH_REL, 0777);
			}
			copy($temp_image_path,$image_path);
			
			$phMagick = new phMagick($image_path);
			$phMagick->setDestination($imageThumbPath)->resize(120,120);
			
			if (SERVER){
				if($oldUserName!='') {
					if(image_exists(15,$oldUserName)) {
						deleteImages(15,$oldUserName);
					}
					if(image_exists(16,$oldUserName)) {
						deleteImages(16,$oldUserName);
					}
				}
				uploadImageToS3($image_path,15,$imageName);
				uploadImageToS3($imageThumbPath,16,$imageName);
				unlink($imageThumbPath);
				unlink($image_path);
			}
			else if ( $oldUserName !='' ){
				if(file_exists(DEVELOPER_IMAGE_PATH_REL.$oldUserName) )
					unlink(DEVELOPER_IMAGE_PATH_REL.$oldUserName);
				if(file_exists(DEVELOPER_THUMB_IMAGE_PATH_REL.$oldUserName) )
					unlink(DEVELOPER_THUMB_IMAGE_PATH_REL.$oldUserName);
			}
			$_SESSION['tilt_developer_logo']	=	$imageName;
			$photoUpdateString	= " Photo = '" . $imageName . "'";
			unlink(TEMP_IMAGE_PATH_REL . $_POST['developer_photo_upload']);
		}
		if($photoUpdateString!=''){
			$condition 			= "id = ".$insert_id;
			$gameDevObj->updateGameDevDetails($photoUpdateString,$condition);
		}
	}
	$_SESSION['notification_msg_code']	=	2;
	header("location:GameDeveloper");
	die();
}
commonHead();
?>
<body class="skin-black">
<?php top_header(); ?>
	<section class="content-header">
		<h1 align="center">Edit Profile</h1>
	</section>
   	<section class="content">
		<div class="">
			<form name="edit_profile_form" id="edit_profile_for" action="" method="post" data-webforms2-force-js-validation="true">
				<input style="display:none">
				<input type="password" style="display:none">
				<div class="box-body game-form-style col-md-7 col-lg-5 box-center">
					<div class=""><?php displayNotification('Profile '); ?></div>
					<div align="center" style="position:relative;width:120px;margin:auto;height:120px" class="profile_photo">
						<div id="developer_photo_img">
							<?php if(isset($original_image_path) && $original_image_path != '' && $original_image_path	!=	'NOIMAGE'){  ?>
									<a title="Click here" alt="Click here" ><img class="img_border" src="<?php echo $user_image_path; ?>" width="120" height="120" style="-webkit-border-radius: 60px; -moz-border-radius: 60px; -khtml-border-radius: 60px;border-radius: 60px;" alt="Image"/></a>
							<?php } else { ?>
									<img class="img_border" src="<?php echo $user_image_path; ?>" width="120" height="120" style="-webkit-border-radius: 60px; -moz-border-radius: 60px; -khtml-border-radius: 60px;border-radius: 60px;" alt="Image"/>
							<?php } ?>
						</div>
						<?php if(isset($_POST['developer_photo_upload']) && $_POST['developer_photo_upload'] != ''){  ?><input type="Hidden" name="developer_photo_upload" id="developer_photo_upload" value="<?php echo $_POST['developer_photo_upload']; ?>"><?php } ?>
						<input type="Hidden" name="empty_developer_photo" id="empty_developer_photo" value="<?php if(isset($devPhoto) && $devPhoto != '') { echo $devPhoto; }  ?>" />
						<input type="Hidden" name="name_developer_photo" id="name_developer_photo" value="<?php if(isset($devPhoto) && $devPhoto != '') { echo $devPhoto; } ?>" />
						<span class="error" for="empty_developer_photo" generated="true" style="display: none">Developer Image is required</span>
						<div class="hover_icon" style="position:absolute;bottom:0;text-align:center;width:100%;display: none">
							<div class="" style="background:rgba(0,0,0,0.3);width:120px;height:120px;border-radius: 60px;position:absolute;bottom:0px;z-index:1"></div>
							<div class="" style="position:relative;z-index:10;bottom:40px; cursor: default;">
								<a href="javascript:void(0)" title="Edit Profile" class="edit profile_pos"><i class="fa fa-edit fa-lg"></i> <input type="file"  name="developer_photo" id="developer_photo" title="Change Image" onchange="return ajaxAdminFileUploadProcess('developer_photo');"  /></a>&nbsp;&nbsp;&nbsp;
								<?php if(isset($original_image_path) && $original_image_path != '' && $original_image_path != 'NOIMAGE') {?>
								<span id='dev_img' class="profile_photo_pos"><a  href="<?php echo $original_image_path; ?>" class="view fancybox" title="View"><i class="fa fa-search fa-lg"></i></a></span>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php if($error_msg !='') { ?><div class="error_msg"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $error_msg;?></div><?php  } ?>
					<div class="form-group">
						<label class="col-xs-6 col-sm-6">Company Name</label>
						<div class="col-xs-6 col-sm-6 text-right">
							<input type="text" class="form-control inline" name="company" id="company" maxlength="100" value="<?php if(isset($company) && $company != '') echo $company;  ?>" autocomplete="off" >
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-6 col-sm-6">Contact Name</label>
						<div class="col-xs-6 col-sm-6 text-right">
							<input type="text" class="form-control inline" name="contact_name" id="contact_name" maxlength="50" value="<?php if(isset($contactName) && $contactName != '') echo $contactName;  ?>" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-6 col-sm-6"><span class="required_field">*</span>&nbsp;E-mail</label>
						<div class="col-xs-6 col-sm-6 text-right">
							<input type="text" class="form-control inline" name="email" required id="email" maxlength="75" value="<?php if(isset($email) && $email != '') echo $email;  ?>" disabled>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-6 col-sm-6">Password</label>
						<div class="col-xs-6 col-sm-6 text-right">
							<input type="password" class="form-control inline" name="password" id="password" maxlength="20" value="<?php if(isset($password) && $password != '') echo $password;  ?>" oninput="passwordMinLength('password');" autocomplete="off" ondrop="return false;" onpaste="return false;">
						</div>
					</div>
				</div>
				<div class="box-footer" align="center">
					<input type="submit" class="btn btn-green" name="submit" id="submit"  value="Save Profile" title="Save Profile" >
				</div>
			</form>
		</div>
	</section>
<?php footerLinks(); commonFooter(); ?>
<script>
$('.fancybox').fancybox({
	helpers: { 
		title: null
	}
});
</script>
</html>