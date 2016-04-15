<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminControllerObj =   new AdminController();
$developerContent 	= 	$mediaContent	=	$msg	=	'';
$old_videoImage		=	$video_image_path	=	$oldbg_img  = $old_video  =  $oldbg_img_path = $old_video_path = $downloadContent = '';
$developer_content 	= 	$media_content 	= 	'';

//get Home page content
$fields		  =	" * ";
$where		  =	" id = 1 ";
$home_details = $adminControllerObj->getWebsiteHome($fields, $where);
if(isset($home_details) && is_array($home_details) && count($home_details)>0){
	foreach($home_details as $key=>$value){
		$developerContent 	= $value->DeveloperContent;
		$mediaContent	  	= $value->MediaContent;
		$downloadContent	= $value->DownloadContent;
		$oldbg_img        	= $value->Image;
		$old_video        	= $value->Video;
		$old_videoImage		= $value->VideoImage;
		
		if($oldbg_img != ''){
			if(SERVER){
				if(image_exists(18,$oldbg_img))
					$oldbg_img_path = WEBSITE_PATH.$oldbg_img;
			}
			else
			{
				if(file_exists(WEBSITE_PATH_REL.$oldbg_img))
					$oldbg_img_path = WEBSITE_PATH.$oldbg_img;
			}
		}
		if($old_video != ''){
			if(SERVER){
				if(image_exists(18,$old_video))
					$old_video_path = WEBSITE_PATH.$old_video;
			}
			else
			{
				if(file_exists(WEBSITE_PATH_REL.$old_video))
					$old_video_path = WEBSITE_PATH.$old_video;
			}
		}
		if($old_videoImage !=''){
			if(SERVER){
				if(image_exists(18,$old_videoImage)){
					$video_image_path = WEBSITE_PATH.$old_videoImage;
				}
			
			}
			else if(file_exists(WEBSITE_PATH_REL.$old_videoImage)){
					$video_image_path = WEBSITE_PATH.$old_videoImage;
			}
		}
	}
}

//update home page content
if(isset($_POST['website_home_submit']) && $_POST['website_home_submit'] != '' )
{	
	$fileName = $videoName ='';
	
	$_POST       = unEscapeSpecialCharacters($_POST);
    $_POST       = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['developer_content']) &&	$_POST['developer_content'] != '')
		$developer_content	=	$_POST['developer_content'];
	if(isset($_POST['media_content']) &&	$_POST['media_content'] != '')
		$media_content	 =	$_POST['media_content'];
	if(isset($_POST['download_content']) &&	$_POST['download_content'] != '')
		$download_content =	$_POST['download_content'];
	
	//promo video upload
	if(isset($_POST['promo_video_upload']) && $_POST['promo_video_upload'] !=''){
		$imageName = '';
		$temp_image_path  = TEMP_USER_IMAGE_PATH_REL.$_POST['promo_video_upload'];
		$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
		$uploadPath	=	UPLOAD_WEBSITE_PATH_REL;
		if (!file_exists($uploadPath)){
			mkdir ($uploadPath, 0777);
		}
		$videoName	=	'homevideo_'.time().'.'.$ext;
		$imageName 	=	 $videoName;
		$image_path =	$uploadPath.$imageName;
		copy($temp_image_path,$image_path);
		
		if(SERVER){
			if($old_video != '') {
				if(image_exists(18,$old_video)){
					deleteImages(18,$old_video);
				}
			}
			uploadImageToS3($image_path,18,$imageName);
			unlink($image_path);
		}else if($old_video != ''){
			if(file_exists(WEBSITE_PATH_REL.$old_video))
				unlink(WEBSITE_PATH_REL.$old_video);
		}
		unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['promo_video_upload']);
	}
	
	//background image upload
	if(isset($_POST['bg_image_upload']) && $_POST['bg_image_upload'] !=''){
		$imageName = '';
		$temp_image_path  = TEMP_USER_IMAGE_PATH_REL . $_POST['bg_image_upload'];
		$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
		$uploadPath	=	UPLOAD_WEBSITE_PATH_REL;
		if (!file_exists($uploadPath)){
			mkdir ($uploadPath, 0777);
		}
		$fileName	= 'backgroundimage_'.time().'.'.$ext;
		$imageName 	= $fileName;
		$image_path = $uploadPath.$imageName;
		copy($temp_image_path,$image_path);
		if(SERVER){
			if($oldbg_img != '') {
				if(image_exists(18,$oldbg_img)){
					deleteImages(18,$oldbg_img);
				}
			}
			uploadImageToS3($image_path,18,$imageName);
			unlink($image_path);
		}
		else	if($oldbg_img != ''){
			if(file_exists(WEBSITE_PATH_REL.$oldbg_img))
				unlink(WEBSITE_PATH_REL.$oldbg_img);
	    }
		unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['bg_image_upload']);
	}
	//Promo video image upload
	$videoFileName	=	'';
	if(isset($_POST['video_image_upload']) && $_POST['video_image_upload'] !=''){
		$imageName = '';
		$temp_image_path  = TEMP_USER_IMAGE_PATH_REL . $_POST['video_image_upload'];
		$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
		$uploadPath	=	UPLOAD_WEBSITE_PATH_REL;
		if (!file_exists($uploadPath)){
			mkdir ($uploadPath, 0777);
		}
		$imageName 	=	$videoFileName = 'videoimage_'.time().'.'.$ext;
		$image_path = $uploadPath.$imageName;
		copy($temp_image_path,$image_path);
		if(SERVER){
			if($oldImage != '') {
				if(image_exists(18,$oldImage)){
					deleteImages(18,$oldImage);
				}
			}
			uploadImageToS3($image_path,18,$imageName);
			unlink($image_path);
	   }
		else if($oldImage != ''){
			if(file_exists(WEBSITE_PATH_REL.$oldImage))
				unlink(WEBSITE_PATH_REL.$oldImage);
		}
		unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['video_image_upload']);
	}
	
	$condition      =   " id = 1 ";	
	$updateString   =   ' DeveloperContent = "'.$developer_content.'",MediaContent = "'.$media_content.'",DownloadContent = "'.$download_content.'",DateModified="'.date('Y-m-d H:i:s').'"';	
	if($fileName != '')
		$updateString   .= ' , Image = "'.$fileName.'"';
	if($videoName != ''){
		$updateString   .= ' , Video = "'.$videoName.'"';
	}
	if($videoFileName != '')
		$updateString   .= ' , VideoImage = "'.$videoFileName.'"';
	$adminControllerObj->updateWebsiteHome($updateString,$condition);
	$_SESSION['notification_msg_code']	=	2;
	header('location:WebsiteHome');
	die();
}
commonHead();
?>
<body>
<?php top_header(); ?>	
	<div class="box-header"><h2><i class="fa fa-pencil-square-o" ></i>Home</h2></div>
	<div class="clear"></div>
	<form name="website_home_form" id="website_home_form" action="" method="post" enctype="multipart/form-data">
		<table align="center" cellpadding="0" cellspacing="0" border="0" width="98%" class="form_page list headertable">
	
		<tr><td align="center">
			<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">							 
				<tr><td  valign="top" align="center" colspan="3" class="msg_height"><?php displayNotification('Home Content '); ?></td></tr>
				<tr>
					<td align="left" width="15%" valign="top"><label>Promo Video <span class="required_field">*</span></label></td>
					<td align="center" valign="top" width="3%">:</td>
					<td height="60" valign="top" align="left" width="82%">
						<div style="clear: both;float: left"> 
							<input type="file" class="upload w90" id="promo_video" name="promo_video" onchange="return ajaxVideoUploadProcess(this.value,'promo_video');" > 
						</div>
						<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
							<div id="promo_video_img">		
							<?php  if(isset($old_video_path) && $old_video_path != ''){  ?>
								<a href="<?php echo $old_video_path; ?>" title="Click here" class="video_pop_up" alt="Click here" ><?php  if(isset($old_video)){ echo $old_video; }  ?></a>
							<?php  }  ?>
								<input id="promo_video_validate" type="hidden" name="promo_video_validate" value="<?php if(isset($old_video)) echo $old_video ; ?>" >						
							</div>
						</div>
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Promo Video Image <span class="required_field">*</span></label></td>
					<td align="center" valign="top">:</td>
					<td height="60" valign="top" align="left">
						<div style="clear: both;float: left"> 
							<input type="file" class="upload w90" id="video_image" name="video_image" onchange="return ajaxAdminFileUploadProcess('video_image');"> 
						</div>
						<div style="width:250px;">(Minimum width 700)</div>
						<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
							<div id="video_image_img">		
							<?php  if(isset($video_image_path) && $video_image_path != ''){ ?>
								<a href="<?php echo $video_image_path; ?>" class="bg_image_pop_up" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $video_image_path;  ?>" width="75" height="75" alt="Image"/></a>
							<?php  }  ?>	
							<input id="video_image_validate" type="hidden" name="video_image_validate" value="<?php if(isset($old_videoImage)) echo $old_videoImage ; ?>" >
							</div>						
						</div>					
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Developers Area Content <span class="required_field">*</span></label></td>
					<td align="center" class="" valign="top">:</td>
					<td height="60" valign="top" align="left">
						<textarea class="add_cms" id="developer_content" rows="15" cols="106" name="developer_content"><?php echo $developerContent; ?></textarea>
					</td>
				</tr>			
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Media Area Content <span class="required_field">*</span></label></td>
					<td align="center" valign="top">:</td>
					<td height="60" valign="top" align="left">
						<textarea class="add_cms" id="media_content" rows="15" cols="106" name="media_content"><?php echo $mediaContent; ?></textarea>
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Download Now Content <span class="required_field">*</span></label></td>
					<td align="center" valign="top">:</td>
					<td height="60" valign="top" align="left" width="75%">
						<textarea class="add_cms" id="download_content" rows="15" cols="106" name="download_content"><?php echo $downloadContent; ?></textarea>
					</td>
				</tr>	
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Background Image <span class="required_field">*</span></label></td>
					<td align="center" valign="top">:</td>
					<td height="60" valign="top" align="left">
						<div style="clear: both;float: left"> 
							<input type="file" class="upload w90" id="bg_image" name="bg_image" onchange="return ajaxAdminFileUploadProcess('bg_image');"> 
						</div>
						<div style="width:250px;">(Minimum width 700)</div>
						<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
							<div id="bg_image_img">		
							<?php  if(isset($oldbg_img_path) && $oldbg_img_path != ''){  ?>
									<a href="<?php echo $oldbg_img_path; ?>" class="bg_image_pop_up" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $oldbg_img_path;  ?>" width="75" height="75" alt="Image"/></a>
							<?php  }  ?>	
								<input id="bg_image_validate" type="hidden" name="bg_image_validate" value="<?php if(isset($oldbg_img)) echo $oldbg_img ; ?>" >						
							</div>
						</div>					
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
				<td colspan="2"></td>
				<td align="left">
				<input type="submit" class="submit_button" name="website_home_submit" id="website_home_submit" value="Submit" title="Submit" alt="Submit" />
				<a href="UserList?cs=1" class="submit_button" name="Cancel" id="Cancel" value="Cancel" title="Cancel" alt="Cancel" tabindex="NaN">Cancel</a>
				</td>
				</tr>
			</table>
		</tr></td>
		<tr><td height="10"></td></tr>
		</table>
	</form>
<?php commonFooter(); ?>
<script type="text/javascript">
	$(".bg_image_pop_up").colorbox({
		title:true
		});
	$(".video_pop_up").colorbox({
		iframe:true,
		width:"45%", 
		height:"65%",
		title:true
	});
</script> 
</html>