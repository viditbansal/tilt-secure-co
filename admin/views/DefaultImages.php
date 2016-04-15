<?php 
require_once("includes/phmagick.php");
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminControllerObj =   new AdminController();
$image_array = $image_patharray = $imageid_array = $image_thumb_patharray = array();

//add/update Default user image
if(isset($_POST['default_image_submit']) && $_POST['default_image_submit'] != '' ){	
	$_POST  = unEscapeSpecialCharacters($_POST);
    $_POST  = escapeSpecialCharacters($_POST);
	$today	=	date('Y-m-d H:i:s');
	//insert multiple file upload
	$tempFileArray	=	$_POST['temp_user_image'];
	$imageidArray   =   $_POST['image_id'];
	$oldtempFileArray	=	$_POST['olduser_temp_file'];
	//Get all image detail
	$fields	= " * ";
	$cond	= "Status = 1";
	$gamefilesResult  = $adminControllerObj->selectDefaultImage($fields,$cond);
	if(is_array($gamefilesResult) && isset($gamefilesResult) && count($gamefilesResult)>0){
		foreach($gamefilesResult as $imageval)
		{
			$image_array[]		=	$imageval->Image;
			$imageid_array[]	=	$imageval->id;
		}
	}
	if(isset($imageid_array) &&  $imageid_array != ''){
		foreach($imageid_array as $imgkey=>$imgvalue){
			if(!(in_array($imgvalue,$imageidArray))){ 
			   $updatestatus  = " Status = 2, DateModified= '".$today."' "; 
			   $updatecondition = " id = '".$imgvalue."' ";
			   $adminControllerObj->updateDefaultImage($updatestatus,$updatecondition);
			   if(isset($image_array[$imgkey]) && $image_array[$imgkey] != ''){					
				$oldImage = $image_array[$imgkey];
				if (SERVER){
					if($oldImage!='') {
						if(image_exists(22,$oldImage)) {
							deleteImages(22,$oldImage);
						}
						if(image_exists(22,"thumbnail/".$oldImage)) {
							deleteImages(22,"thumbnail/".$oldImage);
						}
					}
				}
				else if ( $oldImage !='' && file_exists(DEFAULT_USER_IMAGE_PATH_REL.$oldImage) ){
					unlink(DEFAULT_USER_IMAGE_PATH_REL.'/'.$oldImage);
					if(file_exists(DEFAULT_USER_THUMB_IMAGE_PATH_REL.$oldImage))
						unlink(DEFAULT_USER_THUMB_IMAGE_PATH_REL.'/'.$oldImage);
				}
			  }
			}
		}
	}
	if(isset($tempFileArray)	&& is_array($tempFileArray) &&  isset($oldtempFileArray) &&  is_array($oldtempFileArray) ){
	   foreach($tempFileArray as $key=>$value){
			if(isset($tempFileArray[$key]) && 	$tempFileArray[$key] != ''){
				$oldImage = $entryId = $imageName = '';
				if(isset($value) && $value !=''){
					if(isset($oldtempFileArray[$key]) && ($oldtempFileArray[$key]!= '')){
						if(isset($imageidArray[$key]) && $imageidArray[$key] != ''){
							$oldImage	=	$oldtempFileArray[$key];
							$entryId	=	$imageidArray[$key];
						}
					}
					if($entryId == ''){
						$entryId	    =   $adminControllerObj->insertDefaultImage($today);
					}
					if($entryId){
						if ( !file_exists(UPLOAD_DEFAULT_PATH_REL) ){
							mkdir (UPLOAD_DEFAULT_PATH_REL, 0777);
						}
						if ( !file_exists(UPLOAD_DEFAULT_THUMB_PATH_REL) ){
							mkdir (UPLOAD_DEFAULT_THUMB_PATH_REL, 0777);
						}
						$imageName			=	$entryId.'_'.time().'.png';
						$temp_image_path 	=	TEMP_USER_IMAGE_PATH_REL.$value;
						$image_path 		=	UPLOAD_DEFAULT_PATH_REL.$imageName;
						$imageThumbPath     =	UPLOAD_DEFAULT_THUMB_PATH_REL.$imageName;
						copy($temp_image_path,$image_path);
						$phMagick = new phMagick($image_path);
						$phMagick->setDestination($imageThumbPath)->resize(100,100);
						if (SERVER){
							uploadImageToS3($image_path,22,$imageName); // image_path
							uploadImageToS3($imageThumbPath,22,"thumbnail/".$imageName); // image_path
							unlink($image_path);
							unlink($imageThumbPath);
						}
						if($imageName !=''){
							$updateString	= " Image= '".$imageName."' ";	
							$updatecondition = " id = ".$entryId;
							$adminControllerObj->updateDefaultImage($updateString,$updatecondition);
						}
						unlink($temp_image_path);
						if(!empty($oldImage)){
							if (SERVER){
								if(image_exists(22,$oldImage)) {
									deleteImages(22,$oldImage);
								}
								if(image_exists(22,"thumbnail/".$oldImage)) {
									deleteImages(22,"thumbnail/".$oldImage);
								}
							}
							else if (file_exists(DEFAULT_USER_IMAGE_PATH_REL.$oldImage) ){
								unlink(DEFAULT_USER_IMAGE_PATH_REL.'/'.$oldImage);
								if(file_exists(DEFAULT_USER_THUMB_IMAGE_PATH_REL.$oldImage))
									unlink(DEFAULT_USER_THUMB_IMAGE_PATH_REL.'/'.$oldImage);
							}
						}
					}
				}
			}
		}
	}
	$_SESSION['notification_msg_code']	=	1;
	header('location:DefaultImages');
	die();
}

//Get all image detail
$fields	= " * ";
$cond	= "Status = 1";
$gamefilesResult  = $adminControllerObj->selectDefaultImage($fields,$cond);
if(is_array($gamefilesResult) && isset($gamefilesResult) && count($gamefilesResult)>0){
	foreach($gamefilesResult as $imageval)
	{
		$image_array[]		=	$imageval->Image;
		$image_patharray[]	=	DEFAULT_USER_IMAGE_PATH.$imageval->Image;
		$image_thumb_patharray[]	=	DEFAULT_USER_THUMB_IMAGE_PATH.$imageval->Image;
		$imageid_array[]	=	$imageval->id;
	}
}
commonHead();
?>
<body>
<?php top_header(); ?>	
	<div class="box-header" style="margin-bottom:4px;"><h2><i class="fa fa-pencil-square-o" ></i>Users Default Image</h2></div>
	<div class="clear"></div>
	<form name="user_image_form" id="user_image_form" action="" method="post" enctype="multipart/form-data">
		<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">
		<tr><td align="right" style="padding-right:10px;color:#000;font-size:14px;"><span style="color:red;">*</span> Note : Minimum dimension 100x100</td></tr>
		 <tr><td align="center">
		  <table cellpadding="5" cellspacing="5" align="center" border="0" width="92%">	
			<tr><td  valign="top" align="center" colspan="3">
			<?php displayNotification('Users Default Image '); ?>
			</td></tr>	
			<tr><td height="20" colspan="3"></td></tr>
			<tr>							
				<td  style="padding-left:0px;" colspan="3">
					<table id="user_table" width="100%">
					<?php if(is_array($image_array) && isset($image_array) && count($image_array)>0){ 
							for($index = 0;$index < count($image_array);$index++){
							 if(isset($image_array[$index]) && $image_array[$index] != ''){
								$hideadd = "style='display:none;'";
								if($index == count($image_array)-1)
									$hideadd = '';
							?>
						<tr  height="90" class="clone file_upload" clone="<?php echo $index ;?>" id="fileupload" width="50%">	
							<td align="left" height="60" valign="bottom" width="100%">												
								<div id="user_image<?php echo $index ;?>_img" class="user_image">
									<a <?php if(isset($image_patharray[$index]) && $image_patharray[$index] != '') { ?> href="<?php echo $image_patharray[$index]; ?>" class="user_pop_up"<?php } else { ?>href="Javascript:void(0);"<?php } ?> title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $image_thumb_patharray[$index];  ?>" width="75" height="75" alt="Image"/></a>
								</div>
								<div class="browser_image">
								<input type="file"  name="user_image<?php echo $index ;?>" id="user_image<?php echo $index ;?>" title="User Image" onclick="" value="<?php echo $image_array[$index];?>" onchange="return ajaxAdminFileUploadProcess(this.id);"  />
								<input type="hidden" value="" name="temp_user_image[]"  id="temp_user_image<?php echo $index ;?>">
								<input type="hidden" value="<?php echo $image_array[$index];?>" name="olduser_temp_file[]"  id="olduser_temp_file<?php echo $index ;?>"> 
								<input type="Hidden" value="<?php echo $imageid_array[$index];?>" id="image_id" name = "image_id[]" >
								<input type="Hidden" value="1" class="flag_user_image" id="flag_user_image<?php echo $index ;?>" name = "image_flag[]" >
								</div>																																			
							</td>	
							<td width="5%" valign="top"><a href="javascript:void(0)" onclick="delUserImage(this)"  ><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>	
							<td width="5%" valign="top"><a href="javascript:void(0)" onclick="addUserImage(this)"  class="addimg" <?php echo $hideadd; ?>><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td> 																	
						 </tr>		
					<?php   } }
					} else {?>		
							<tr  height="90" class="clone file_upload" clone="1" id="fileupload" width="50%">		
							<td align="left" width="100%"  height="60" valign="bottom">												
								<div id="user_image1_img" class="user_image"></div>
								<div class="browser_image">
								<input type="file"  name="user_image1" id="user_image1" title="User Image" onclick="" onchange="return ajaxAdminFileUploadProcess(this.id);"  />
								<input type="hidden" value="" name="temp_user_image[]"  id="temp_user_image1">
								<input type="hidden" value="" name="olduser_temp_file[]"  id="olduser_temp_file1">
								<input type="Hidden" value="" id="image_id" name = "image_id[]" >
								<input type="Hidden" value="" class="flag_user_image" id="flag_user_image1" name = "image_flag[]" >
								</div>																																			
							</td>
							<td width="5%" valign="top"><a href="javascript:void(0)" onclick="delUserImage(this)"  ><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>
							<td width="5%" valign="top"><a href="javascript:void(0)" onclick="addUserImage(this)" class="addimg"><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td> 																																	
						 </tr>		
					<?php }?>								
					</table>
				</td>
			</tr>
			<tr><td height="20" colspan="2"></td></tr>
			<tr>
			<td align="center" colspan="3">
			<input type="submit" class="submit_button" name="default_image_submit" id="default_image_submit" value="Submit" title="Submit" onClick="return submitForm();" alt="Submit" />
			<a href="UserList?cs=1" class="submit_button" name="Cancel" id="Cancel" value="Cancel" title="Cancel" alt="Cancel" tabindex="NaN">Cancel</a>
			</td>
			</tr>
		</td></tr>
		</table>
		<tr><td height="10"></td></tr>
		</table>
	</form>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".user_pop_up").colorbox({title:true , maxWidth:"70%", maxHeight:"60%"});
function submitForm(){
	var flagCount = 0;
	$("#user_table tr .flag_user_image").each(function() {
		if($(this).val() == 1)
			flagCount++;
	});
	if(flagCount < 10){
		alert("Atleast 10 image is required");
		return false;
	}
	return true;
}
</script>
</html>