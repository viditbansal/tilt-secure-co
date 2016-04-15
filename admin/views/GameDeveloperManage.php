<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/GameController.php');
require_once("includes/phmagick.php");
$gameObj   =   new GameController();
admin_login_check();
commonHead();
$original_path = '';
$interestStatus		 =	$entryfee_flag	=	0;
$error_msg	=	$class = $field_focus	=	'';

if(isset($_POST['submit'])	&&	$_POST['submit']!=""){
	$_POST          	=   unEscapeSpecialCharacters($_POST);
	$_POST         		=   escapeSpecialCharacters($_POST);
	$editId 			= 	$_GET['editId'];
	$name       		=	trim($_POST['contactname']);
	$companyname		=	trim($_POST['companyname']);
	if(isset($_POST['password']) && !empty($_POST['password']) && trim($_POST['password']) !='' ){
		$password		=	trim($_POST['password']);
		$sqlpsw			=	" ActualPassword = '".$password."', Password = '".sha1($password)."',";
	}
	$updateString = " Name = '".$name."', ".$sqlpsw." Company	 = '".$companyname."'";
	$condition = "id = '".trim($_POST['game_id'])."'";
	$insert_id = $editId;
	$photoUpdateString	= $oldImage = "";
	if (isset($_POST['developer_photo_upload']) && !empty($_POST['developer_photo_upload'])) {
		$imageName 				= $insert_id . '_' .time() . '.png';
		$temp_image_path 		= TEMP_USER_IMAGE_PATH_REL . $_POST['developer_photo_upload'];
		$image_path 			= UPLOAD_DEVELOPER_PATH_REL . $imageName;
		$imageThumbPath     	= UPLOAD_DEVELOPER_THUMB_PATH_REL.$imageName;
		if ( !file_exists(UPLOAD_DEVELOPER_PATH_REL) ){
			mkdir(UPLOAD_DEVELOPER_PATH_REL, 0777);
		}
		if ( !file_exists(UPLOAD_DEVELOPER_THUMB_PATH_REL) ){
			mkdir(UPLOAD_DEVELOPER_THUMB_PATH_REL, 0777);
		}
		copy($temp_image_path,$image_path);
		$phMagick = new phMagick($image_path);
		$phMagick->setDestination($imageThumbPath)->resize(120,120);
		if (isset($_POST['old_developer_photo']) && !empty($_POST['old_developer_photo'])) 
			$oldImage = $_POST['old_developer_photo'];
		if (SERVER){
			if($oldImage!='') {
				if(image_exists(15,$oldImage)) {
					deleteImages(15,$oldImage);
				}
				if(image_exists(23,$oldImage)) {
					deleteImages(23,$oldImage);
				}
			}
			uploadImageToS3($imageThumbPath,15,'thumbnail/'.$imageName);
			uploadImageToS3($image_path,15,$imageName);
			unlink($image_path);
			unlink($imageThumbPath);
		}else if(!empty($oldImage)){
			if ( file_exists(UPLOAD_DEVELOPER_PATH_REL.$oldImage) )
				unlink(UPLOAD_DEVELOPER_PATH_REL.$oldImage);
			if ( file_exists(UPLOAD_DEVELOPER_THUMB_PATH_REL.$oldImage) )
				unlink(UPLOAD_DEVELOPER_THUMB_PATH_REL.$oldImage);
		}
		$photoUpdateString	.= " ,Photo = '" . $imageName . "'";
		unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['developer_photo_upload']);
	}
	if($photoUpdateString!='')
	{
		$updateString .= $photoUpdateString;
	}
	$updateString .= ",DateModified = '".date('Y-m-d H:i:s')."'";
	$gameObj->updateGameDevDetails($updateString, $condition);
	$_SESSION['notification_msg_code']	=	2;
	header("location:GameDeveloperList");
	die();
}else{
	if(isset($_GET['editId']) && $_GET['editId'] != '' ){
		$where      			= "   id = ".$_GET['editId']." and Status in (0,1,2,4) LIMIT 1 ";
		$gamedeveloperResult  	= $gameObj->SingleGameDeveDetails($where);
		$photo 					= "";
		if(isset($gamedeveloperResult) && is_array($gamedeveloperResult) && count($gamedeveloperResult) > 0){
			$name       		= $gamedeveloperResult[0]->Name;
			$companyname		= $gamedeveloperResult[0]->Company;
			$email       		= $gamedeveloperResult[0]->Email;
			$password			= $gamedeveloperResult[0]->ActualPassword;
			$photo 				= $gamedeveloperResult[0]->Photo;
		}
		$image_path 	= ADMIN_IMAGE_PATH.'developer_logo.png';
		if($photo != ''){								
			$image_path_orginal 		= DEVELOPER_IMAGE_PATH.$photo;			
			$image_path_abs		 	= DEVELOPER_IMAGE_PATH_REL.$photo;			
			$image_thumb_path 	= DEVELOPER_THUMB_IMAGE_PATH.$photo;			
			if(SERVER){
				if(image_exists(15,$photo)){
					$image_path 		  = $image_thumb_path;	
					$original_image_path  = $image_path_orginal;
				}else{
				}
			}else {
				if(file_exists($image_path_abs)){
					$image_path 		  = $image_thumb_path;	
					$original_image_path  = $image_path_orginal;	
				}
			}
		}
	}	
}	
?>
<body onload="return fieldfocus('<?php echo $field_focus; ?>');">
	<?php top_header(); ?>
	 <div class="box-header"><h2><i class='fa fa-search'></i>Edit Developer & Brand</h2></div>
	 <div class="clear">
	 <form name="game_developer_manage_form" id="game_developer_manage_form" action="" method="post">
		<input style="display:none">
		<input type="password" style="display:none">
		<input type="Hidden" name="game_id" id="game_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="list headertable" width="98%">							        
			
			<tr>
				<td align="center">
					<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">
						<tr><td colspan="6" class="msg_height" align="center"><div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span></div></td></tr>
						<tr>
							<td align="left" valign="top" width="15%"><label>Company Name</label></td>
							<td align="center" valign="top" width="3%">:</td>										
							<td align="left" valign="top" width="32%"><input type="text" name='companyname' class="input" value="<?php if(isset($companyname) && $companyname != '') echo $companyname;  else echo ''; ?>" id="companyname"></td>
							<td align="left" valign="top"  width="15%"><label>Email<span class="required_field">&nbsp;*</span></label></td>
							<td align="center" valign="top" width="3%">:</td>										
							<td align="left" valign="top" width="32%"><input type="text" name='email' class="input" value="<?php if(isset($email) && $email != '') echo $email; else echo ''; ?>" id="email" disabled></td>
						</tr>
						<tr><td height="20"></td></tr>
						<tr>
							<td align="left" valign="top"><label>Contact Name</label></td>
							<td align="center" valign="top">:</td>										
							<td align="left" valign="top"><input type="text" name='contactname' class="input" value="<?php if(isset($name) && $name != '') echo $name; else echo ''; ?>" id="contactname"> </td>
							<td align="left" valign="top" ><label>Password</label></td>
							<td align="center" valign="top">:</td>										
							<td align="left" valign="top"><input type="password" name='password' class="input" value="<?php //if(isset($password) && $password != '') echo $password; else echo ''; ?>" id="password" maxlength="20"></td>	
						</tr>
						<tr><td height="20"></td></tr>
						<tr>
							<td align="left" valign="top" width="15%"><label>Photo</label></td>
							<td align="center" valign="top" width="3%">:</td>										
							
							<td align="left" valign="top">
								<input type="file" name='developer_photo' id="developer_photo" onchange="return ajaxAdminFileUploadProcess('developer_photo');">
								<p class="help-block">(Minimum dimension 100x100)</p>
								<div id="developer_photo_img" style="padding-top:5px;">
									<?php if(isset($original_image_path) && $original_image_path != '' ) { ?>
										<a href="<?php echo $original_image_path; ?>" class="developer_pop_up" title="Click here" alt="Click here"><img src="<?php echo $image_path; ?>" width="75" height="75" alt="Image"/></a>
									<?php }else { ?>
										<img src="<?php echo $image_path; ?>" width="75" height="75" title="No logo" alt="No logo"/>
									<?php } ?>
								</div>
								<input type="Hidden" name="old_developer_photo" id="old_developer_photo" value="<?php if(isset($photo) && $photo != '') { echo $photo; }  ?>" />
							</td>
						</tr>
						<tr><td height="20"></td></tr>						
						<tr>										
						<td colspan="6" align="center">		
							<input type="submit" class="submit_button" name="submit" id="submit" value="Save" title="Save" alt="Save">
							<a href="<?php if(isset($_GET['back']) &&	$_GET['back'] !=''){ echo $_GET['back'];} else {echo "GameDeveloperList";} ?>" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
							
						</td>
					</tr>	
					<tr><td height="35"></td></tr>
					</table>
				</td>
			</tr>	
		</table>	
	</form>
</body>
<?php commonFooter();  ?>
<script type="text/javascript">
$(".developer_pop_up").colorbox({
	title:true,
	maxWidth:"45%", 
	maxHeight:"60%"
});
</script>