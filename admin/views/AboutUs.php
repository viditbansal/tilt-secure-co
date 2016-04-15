<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
$class =  '';

$aboutuscontent = $tiltgamersContent = $tiltdevelopersContent = $tiltmediaContent = $explainContent = $aboutus_image = $oldfilename = $aboutustitleimage = $aboutus_title_image = '';

// get about us content
$fields		  =	" * ";
$where		  =	" 1 ";
$Aboutus_details = $adminLoginObj->getAboutUs($fields,$where);
if(isset($Aboutus_details) && is_array($Aboutus_details) && count($Aboutus_details)>0){
	foreach($Aboutus_details as $key => $value){		
		$aboutuscontent 		= $value->AboutUs;
		$tiltgamersContent 		= $value->TiltGamers;
		$tiltdevelopersContent 	= $value->TiltDevelopers;
		$tiltmediaContent 		= $value->TiltMedia;
		$explainContent 		= $value->TiltExplain;
		$oldfilename 			= $value->Image;
		$aboutustitleimage 		= $value->AboutusImage;
		
		if($oldfilename != ''){
			if(SERVER){
				if(image_exists(18,$oldfilename))
					$aboutus_image = WEBSITE_PATH.$oldfilename;
			}
			else{
				if(file_exists(WEBSITE_PATH_REL.$oldfilename))
					$aboutus_image = WEBSITE_PATH.$oldfilename;
			}
		}
		
		if($aboutustitleimage != ''){
			if(SERVER){
				if(image_exists(18,$aboutustitleimage))
					$aboutus_title_image = WEBSITE_PATH.$aboutustitleimage;
			}
			else
			{
				if(file_exists(WEBSITE_PATH_REL.$aboutustitleimage))
					$aboutus_title_image = WEBSITE_PATH.$aboutustitleimage;
			}
		}
	}
}

//update about us
if(isset($_POST['about_us_submit']) && $_POST['about_us_submit'] != '' )
{	
	$_POST   = unEscapeSpecialCharacters($_POST);
    $_POST   = escapeSpecialCharacters($_POST);	
	$fileName = '';
	$aboutus_filename = '';
	$insert_id = '1';
	
	if(isset($_POST['about_usContent']) &&	$_POST['about_usContent'] != '')
		$aboutuscontent	=	$_POST['about_usContent'];	
	if(isset($_POST['tilt_gamers']) &&	$_POST['tilt_gamers'] != '')
		$tiltgamersContent = $_POST['tilt_gamers'];
	if(isset($_POST['tilt_developers']) &&	$_POST['tilt_developers'] != '')
		$tiltdevelopersContent = $_POST['tilt_developers'];
	if(isset($_POST['tilt_media']) &&	$_POST['tilt_media'] != '')
		$tiltmediaContent = $_POST['tilt_media'];
	if(isset($_POST['explain']) &&	$_POST['explain'] != '')
		$explainContent = $_POST['explain'];
	
	// upload 
	if(isset($_POST['about_us_upload']) && $_POST['about_us_upload'] !=''){
		$temp_image_path  = TEMP_USER_IMAGE_PATH_REL . $_POST['about_us_upload'];
		$ext = pathinfo($temp_image_path, PATHINFO_EXTENSION);
		$uploadPath	=	UPLOAD_WEBSITE_PATH_REL;
		if (!file_exists($uploadPath)){
			mkdir ($uploadPath, 0777);
		}
		$fileName	=	'aboutus_' .$insert_id.'_'.time().'.'.$ext;
		$imageName 	=	 $fileName;
		$image_path =	$uploadPath.$imageName;
		copy($temp_image_path,$image_path);
		
		if(SERVER){
			if($oldfilename != '') {
				if(image_exists(18,$oldfilename)){
					deleteImages(18,$oldfilename);
				}
			}
			uploadImageToS3($image_path,18,$imageName);
			unlink($image_path);
		}
		else if($oldfilename != ''){
			if(file_exists(WEBSITE_PATH_REL.$oldfilename))
				unlink(WEBSITE_PATH_REL.$oldfilename);
		}
		unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['about_us_upload']);
	}
	
	
	if(isset($_POST['aboutus_title_upload']) && $_POST['aboutus_title_upload'] !=''){
		$temp_image_path  = TEMP_USER_IMAGE_PATH_REL . $_POST['aboutus_title_upload'];
		$ext              = pathinfo($temp_image_path, PATHINFO_EXTENSION);
		$uploadPath       =	UPLOAD_WEBSITE_PATH_REL;
		if (!file_exists($uploadPath)){
			mkdir ($uploadPath, 0777);
		}
		$aboutus_filename =	'aboutus_title_' .$insert_id.'_'.time().'.'.$ext;
		$imageName        =	$aboutus_filename;
		$image_path       =	$uploadPath.$imageName;
		copy($temp_image_path,$image_path);
		
		if(SERVER){
			if($aboutustitleimage != '') {
				if(image_exists(18,$aboutustitleimage)){
					deleteImages(18,$aboutustitleimage);
				}
			}
			uploadImageToS3($image_path,18,$imageName);
			unlink($image_path);
		}
		else if($aboutustitleimage != ''){
			if(file_exists(WEBSITE_PATH_REL.$aboutustitleimage))
				unlink(WEBSITE_PATH_REL.$aboutustitleimage);
		}
		unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['aboutus_title_upload']);
	}
	
	$condition      =   " id = 1 ";	
	$updateString   =   ' AboutUs = "'.$aboutuscontent.'",TiltGamers = "'.$tiltgamersContent.'", TiltDevelopers = "'.$tiltdevelopersContent.'",TiltMedia="'.$tiltmediaContent.'",TiltExplain = "'.$explainContent.'",DateModified="'.date('Y-m-d H:i:s').'"';	
	if($fileName != '')
	  $updateString   .= ' , Image = "'.$fileName.'"';
	if($aboutus_filename != '')
	  $updateString   .= ' , AboutusImage = "'.$aboutus_filename.'"';
	  
	//update 
	$adminLoginObj->updateAboutUs($updateString,$condition);
	$_SESSION['notification_msg_code']	=	2;
	header('location:AboutUs');die();
}
commonHead(); ?>
<body>
	<?php top_header(); ?>
	<div class="box-header"><h2><i class="fa fa-pencil-square-o" ></i>About Us</h2></div>
	<div class="clear">
	<form name="about_us_form" id="about_us_form" action="" method="post" >
	<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">
		<tr><td align="center">
			<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">							 
				<tr><td align="center" colspan="3" valign="top" class="msg_height"><?php displayNotification('About Us Content '); ?></td></tr>
				<tr>
				<td align="left" valign="top" width="15%"><label>About Us Title Image <span class="required_field">*</span></label></td>
				<td align="center" valign="top" width="3%">:</td>
				<td valign="top" width="82%">
					<div class="upload fleft">
					<div style="clear: both;float: left"> <input type="file"  name="aboutus_title" id="aboutus_title" title="About us Title Image" onclick="" onchange="return ajaxAdminFileUploadProcess('aboutus_title');"  />
					</div>					
					<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
						<div id="aboutus_title_img">		
						<?php  if(isset($aboutus_title_image) && $aboutus_title_image != ''){  ?>
								<a href="<?php echo $aboutus_title_image; ?>" class="image_pop_up" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $aboutus_title_image;  ?>" width="75" height="75" alt="Image"/></a>
						<?php  }  ?>
						<input id="aboutus_title_validate" type="hidden" name="aboutus_title_validate" value="<?php if(isset($aboutustitleimage)) echo $aboutustitleimage ; ?>" >				
						</div>
				   </div>					
				</td></tr> 
				<tr><td height="20"></td></tr>			
				<tr>
				<td align="left" valign="top"><label>About Us <span class="required_field">*</span></label></td>
				<td align="center" valign="top">:</td>
				<td height="60" valign="top" align="left" >
					<textarea class="add_cms" rows="15" cols="106" id="about_usContent" name="about_usContent"><?php echo $aboutuscontent; ?></textarea>
				</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
				<td align="left" valign="top"><label>TiLT For Gamers <span class="required_field">*</span></label></td>
				<td align="center" valign="top">:</td>
				<td height="60" valign="top" align="left">
					<textarea class="add_cms" rows="15" cols="106" id="tilt_gamers"  name="tilt_gamers"><?php echo $tiltgamersContent; ?></textarea>
				</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
				<td align="left" valign="top"><label>TiLT For Developers <span class="required_field">*</span></label></td>
				<td align="center" valign="top">:</td>
				<td height="60" valign="top" align="left" >
					<textarea class="add_cms" rows="15" cols="106" id="tilt_developers"  name="tilt_developers"><?php echo $tiltdevelopersContent; ?></textarea>
				</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
				<td align="left" valign="top"><label>TiLT For Media <span class="required_field">*</span></label></td>
				<td align="center" valign="top">:</td>
				<td height="60" valign="top" align="left" >
					<textarea class="add_cms" rows="15" cols="106" id="tilt_media"  name="tilt_media"><?php echo $tiltmediaContent; ?></textarea>
				</td>
				</tr>				
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top"><label>Explain <span class="required_field">*</span></label></td>
					<td align="center" valign="top">:</td>
					<td height="60" valign="top" align="left" >
						<textarea class="add_cms" rows="15" cols="106" id="explain" name="explain"><?php echo $explainContent; ?></textarea>
					</td>
				</tr>		
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
				<td align="left" valign="top"><label>About us Image <span class="required_field">*</span></label></td>
				<td align="center" valign="top">:</td>
				<td>
					<div class="upload fleft">
						<div style="clear: both;float: left"> 
							<input type="file"  name="about_us" id="about_us" title="About us Image" onclick="" onchange="return ajaxAdminFileUploadProcess('about_us');"  />
						</div>
						<div style="width:280px;">(Minimum width 200 and Maximum Width 300)</div>
						<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
							<div id="about_us_img">		
							<?php  if(isset($aboutus_image) && $aboutus_image != ''){  ?>
										<a href="<?php echo $aboutus_image; ?>" class="image_pop_up" title="Click here" alt="Click here" ><img class="img_border" src="<?php  echo $aboutus_image;  ?>" width="75" height="75" alt="Image"/></a>
							<?php  }  ?>
							<input id="about_us_validate" type="hidden" name="about_us_validate" value="<?php if(isset($oldfilename)) echo $oldfilename ; ?>" >				
							</div>
					   </div>					
				</td></tr>				
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
				<td colspan="2"></td>
				<td align="left">
				<input type="submit" class="submit_button" name="about_us_submit" id="about_us_submit" value="Submit" title="Submit" alt="Submit" />
				<a href="UserList?cs=1" class="submit_button" name="Cancel" id="Cancel" value="Cancel" title="Cancel" alt="Cancel" tabindex="NaN">Cancel</a>
				</td>
				</tr>
			</table>
		</td></tr>
		<tr><td height="10"></td></tr>
	</table>
	</form>	
	</div>
<?php commonFooter(); ?>
<script type="text/javascript">
	$(".image_pop_up").colorbox({
		title:true
		});
</script> 
</html>