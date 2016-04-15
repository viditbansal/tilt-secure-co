<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/AdminController.php');
require_once("includes/phmagick.php");
require_once('controllers/BrandController.php');
$brandObj  		=   new BrandController();
admin_login_check();
commonHead();
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
}
$error_msg	=	$class = $field_focus	=	'';
$brandImage	=	$user_image_name = $oldBrandImage = $message	=	$photoUpdateString = '';
$userName = $firstName = $brandName = $password = $email = $location = $city = $country = $phone = $state = $address = $brandId = $logo_thumb = $logo_original = '';
if(isset($_POST['brand_id'])	&&	$_POST['brand_id'] != '' )	{
	$condition	=	' id <> '.$_POST['brand_id'].' and ';
	$update		=	1;
	$alreadyExist	=	0;
	$insertId	=	$_POST['brand_id'];
	if(isset($_POST['username']))	
		$userName	=	$_POST['username'];
	if(isset($_POST['firstname']))
		$firstName	=	$_POST['firstname'];
	if(isset($_POST['email']))
		$email		=	$_POST['email'];
	if(isset($_POST['brandname']))
		$brandName	=	$_POST['brandname'];
	if(isset($_POST['location'])	)
		$location	=	$_POST['location'];
	if(isset($_POST['city']))
		$city		=	$_POST['city'];
	if(isset($_POST['state']))
		$state		=	$_POST['state'];
	if(isset($_POST['country']))
		$country	=	$_POST['country'];
	if(isset($_POST['phone']))
		$phone		=	$_POST['phone'];
	if(isset($_POST['address']))
		$address	=	$_POST['address'];
	$fields				=	' id,UserName,Email,BrandName ';
	$condition			.=	' ( UserName 	= "'.$_POST['username'].'" ';
	$condition			.=	' OR BrandName 	= "'.$_POST['brandname'].'" ) ';
	$condition			.=	' and Status != 3 ';
	$checkBrandExist	=	$brandObj->checkUserExist($fields,$condition);
	if(!empty($checkBrandExist))	{
		if(strcasecmp($checkBrandExist[0]->BrandName, $_POST['brandname']) == 0)
			$alreadyExist	=	3;
		if(strcasecmp($checkBrandExist[0]->UserName, $_POST['username']) == 0)
			$alreadyExist	=	1;
		
	}
	$query =	'';
	if($alreadyExist == 0)	{
		if(isset($_POST['username'])	)
			$query	.=	' UserName = "'.trim($_POST['username']).'", ';
		if(isset($_POST['firstname']))
			$query	.=	' Name = "'.trim($_POST['firstname']).'", ';
		if(isset($_POST['brandname']))
			$query	.=	' BrandName = "'.trim($_POST['brandname']).'",  ';		
		if(isset($_POST['password'])	&&	$_POST['password'] != '')
			$query	.=	' Password = "'.sha1(trim($_POST['password']).ENCRYPTSALT).'", ActualPassword = "'.trim($_POST['password']).'", ';			
		if(isset($_POST['location'])	)
			$query	.=	' Location = "'.trim($_POST['location']).'", ';
		if(isset($_POST['city'])	)
			$query	.=	' City = "'.trim($_POST['city']).'", ';
		if(isset($_POST['state'])	)
			$query	.=	' State = "'.trim($_POST['state']).'", ';
		if(isset($_POST['country']))
			$query	.=	' Country = "'.trim($_POST['country']).'", ';
		if(isset($_POST['address']))
			$query	.=	' Address = "'.trim($_POST['address']).'", ';
		if(isset($_POST['phone']))
			$query	.=	' Phone = "'.trim($_POST['phone']).'", ';
		$query	.=	' DateModified = "'.date('Y-m-d H:i:s').'" ';
		$condition 			= "id = ".$insertId;
		$updateBrand		=	$brandObj->updateBrandDetail($query,$condition);
		$alreadyExist		=	3;
		$_SESSION['sess_brand_name'] = $_POST['brandname'];
		if (isset($_POST['brand_logo_upload']) && !empty($_POST['brand_logo_upload'])) {
			$imageName 					=	$insertId . '_' .time() . '.png';
			if ( !file_exists(UPLOAD_BRANDS_PATH_REL.$insertId) ){
				mkdir (UPLOAD_BRANDS_PATH_REL.$insertId, 0777);
			}
			$temp_image_path 			=	TEMP_USER_IMAGE_PATH_REL . $_POST['brand_logo_upload'];
			$image_path 				=	UPLOAD_BRANDS_PATH_REL.$insertId.'/original_'.$imageName;
			$imageThumbPath     		=	UPLOAD_BRANDS_PATH_REL.$insertId.'/'.$imageName;
			$oldBrandImage				=	$_POST['old_brand_logo'];
			
			copy($temp_image_path,$image_path);
			
			$phMagick = new phMagick($image_path);
			$phMagick->setDestination($imageThumbPath)->resize(100,100);
			if (SERVER){
				if($oldBrandImage!='') {
					if(image_exists(4,$insertId.'/original_'.$oldBrandImage)) {
						deleteImages(4,$insertId.'/original_'.$oldBrandImage);
					}
					if(image_exists(4,$insertId.'/'.$oldBrandImage)) {
						deleteImages(4,$insertId.'/'.$oldBrandImage);
					}
				}
				uploadImageToS3($image_path,4,$insertId.'/original_'.$imageName); // image_path					
				uploadImageToS3($imageThumbPath,4,$insertId.'/'.$imageName);
				unlink($image_path);
				unlink($imageThumbPath);
			}else if($oldBrandImage!=''){
				if ( file_exists(UPLOAD_BRANDS_PATH_REL.$insertId.'/'.$oldBrandImage) ){
					unlink(UPLOAD_BRANDS_PATH_REL.$insertId.'/'.$oldBrandImage);
				}
				if ( file_exists(UPLOAD_BRANDS_PATH_REL.$insertId.'/original_'.$oldBrandImage) ){
					unlink(UPLOAD_BRANDS_PATH_REL.$insertId.'/original_'.$oldBrandImage);
				}
			}
			$photoUpdateString	= ' Logo = "' . $imageName . '", DateModified = "'.date('Y-m-d H:i:s').'" ';
			unlink(TEMP_USER_IMAGE_PATH_REL. $_POST['brand_logo_upload']);
		}
		if($photoUpdateString!='')
		{
			$condition 			= "id = ".$insertId;
			$brandObj->updateBrandDetail($photoUpdateString,$condition);
		}
		$_SESSION['notification_msg_code']	=	2;
	header("location:BrandList");
	die();
	}else{  // already exist condition
		$class = "error_msg";
		if ($alreadyExist == 1){
			$error_msg   = "User Name already exists";
			$field_focus = 'username';
		}
		else if($alreadyExist	==	2	)	{
			$error_msg   = "Email already exists";
			$field_focus = 'email';
		}
		else if($alreadyExist	==	3	)	{
			$error_msg   = "Brand Name already exists";
			$field_focus = 'brandname';
		}
	}
}else{
if(isset($_GET['editId'])	&&	$_GET['editId'] != ''){
	$brandId	=	$_GET['editId'];
	$fields		=	' * ';
	$condition	=	' id = '.$brandId;
	$getBrandDetails		=	$brandObj->getSingleBrand($fields,$condition);
	$logo_thumb = 	$logo_original	=	ADMIN_IMAGE_PATH.'no_brand.jpeg';
	if(!empty($getBrandDetails))	{
		$userName	=	$getBrandDetails[0]->UserName;
		$firstName	=	$getBrandDetails[0]->Name;
		$password	=	$getBrandDetails[0]->ActualPassword;
		$email		=	$getBrandDetails[0]->Email;
		$brandName	=	$getBrandDetails[0]->BrandName;
		$location	=	$getBrandDetails[0]->Location;
		$city		=	$getBrandDetails[0]->City;
		$state		=	$getBrandDetails[0]->State;
		$country	=	$getBrandDetails[0]->Country;
		$phone		=	$getBrandDetails[0]->Phone;
		$address	=	$getBrandDetails[0]->Address;
		if($getBrandDetails[0]->Logo != '')	{
			$brandImage	=	$getBrandDetails[0]->Logo;
			$logo_thumb		=	BRANDS_IMAGE_PATH.$brandId.'/'.$getBrandDetails[0]->Logo;
			$logo_original	=	BRANDS_IMAGE_PATH.$brandId.'/original_'.$getBrandDetails[0]->Logo;
		}
	}
}
}
?>
<body onload="return fieldfocus('<?php echo $field_focus; ?>');">
	<?php top_header(); ?>
	<div class="box-header">
		<h2><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo "<i class='fa fa-edit'></i> Edit "; else echo "<i class='fa fa-plus-circle'></i> Add ";?>Brand</h2>
	</div>
	<div class="clear">
	<form name="brand_manage_form" id="brand_manage_form" action="" method="post">
		<input type="Hidden" name="brand_id" id="brand_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
			<tr><td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">
					<tr>
						<td colspan="7" align="center" valign="top" class="msg_height">
							<div class="<?php echo $class;  ?> w50">
								<span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span>
							</div>
						</td>
					</tr>
					<tr>
						<td width="15%" height="50" align="left"  valign="top">
							<label>User Name<span class="required_field">&nbsp;*</span></label>
						</td>
						<td width="3%" align="center"  valign="top">:</td>
						<td width="32%" align="left"  height="40"  valign="top">
							<input type="text" name='username' class="input" value="<?php echo $userName; ?>" id="username">
						</td>
						<td height="50" width="15%" align="left"  valign="top"><label>Name<span class="required_field">&nbsp;*</span></label></td>
						<td align="center" width="3%" valign="top">:</td>
						<td align="left"  height="40"  valign="top" width="32%">
							<input type="text" name='firstname' value="<?php echo $firstName; ?>" id="firstname" class="input" >
						</td>
					</tr>
					<tr>
						<td height="50" align="left"  valign="top"><label>Brand Name<span class="required_field">&nbsp;*</span></label></td>
						<td width="2%" align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top">
							<input type="text" name='brandname' value="<?php echo $brandName; ?>" id="brandname" class="input" >
						</td>
						<td height="50" align="left"  valign="top"><label>Password<span class="required_field">&nbsp;*</span></label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top" >
							<input type="password" name='password' value="<?php echo $password; ?>" id="password" maxlength="20" class="input" >
						</td>
					</tr>
					<tr>
						<td height="50" align="left"  valign="top"><label>Email<span class="required_field">&nbsp;*</span></label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top" >
							<input type="email" name='email' value="<?php echo $email; ?>" id="email" class="input" disabled>
						</td>
						<td height="50" align="left"  valign="top"><label>Address</label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top" >
							<input type="text" name='address' value="<?php echo $address; ?>" id="address" class="input" >
						</td>
					</tr>
					<tr>
						<td height="50" align="left"  valign="top"><label>Location</label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top">
						<input type="text" name='location' value="<?php echo $location; ?>" id="location" class="input" >
						</td>
						<td height="50" align="left"  valign="top"><label>City</label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top" >
							<input type="text" name='city' value="<?php echo $city; ?>" id="city"  class="input" >
						</td>
					</tr>
					<tr>
						<td height="50" align="left"  valign="top"><label>State</label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top">
						<input type="text" name='state' value="<?php echo $state; ?>" id="state"  class="input" >
						</td>
						<td height="50" align="left"  valign="top"><label>Country</label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top" >
							<input type="text" name='country' value="<?php echo $country; ?>" id="country"  class="input" >
						</td>
					</tr>
					<tr>
						<td height="50" align="left"  valign="top"><label>Phone</label></td>
						<td align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top">
						<input type="tel" onkeypress="return isNumberKey(event);" name='phone' value="<?php echo $phone; ?>" id="phone"  class="input" >
						</td>
						<td height="50" align="left"  valign="top"><label>Logo</label></td>
						<td  align="center"  valign="top">:</td>
						<td align="left"  height="40"  valign="top">
						<input type="file" name='brand_logo' id="brand_logo" onchange="return ajaxAdminFileUploadProcess('brand_logo');">
							<div id="brand_logo_img" style="padding-top:5px;">
								<?php  if(isset($logo_thumb) && $logo_thumb != ''){  ?>
									<a href="<?php if(isset($getBrandDetails[0]->Logo) && $getBrandDetails[0]->Logo != '' ) { 
													echo $logo_original; ?>" class="brand_pop_up" title="Click here" alt="Click here"
											<?php } 
											else { ?>Javascript:void(0);" style="cursor:auto;" title="No logo" alt="No logo" <?php } ?>  ><img src="<?php  echo $logo_thumb;  ?>" width="75" height="75" alt="Image"/></a>
								<?php  }  ?>
								</div>
							<input type="Hidden" name="empty_brand_logo" id="empty_brand_logo" value="<?php  if(isset($logo_thumb) && $logo_thumb != '') { echo $logo_thumb; }  ?>" />
							<input type="Hidden" name="old_brand_logo" id="old_brand_logo" value="<?php  if(isset($brandImage) && $brandImage != '') { echo $brandImage; }  ?>" />
						</td>
						
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td colspan="6" align="center">
				<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ $href_page= 'BrandList';?>
					<input type="submit" class="submit_button" name="submit" id="submit" value="Save" title="Save" alt="Save">
				<?php } else { ?>
				<input type="submit" class="submit_button" name="submit" id="submit" value="Add" title="Add" alt="Add">
				<?php } ?>
				<a href="<?php if(isset($href_page) && $href_page != '' ) echo $href_page; else echo 'BrandList?cs=1';?>"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
			</td>
		</tr>	
			<tr><td height="35"></td></tr>				  
		</table>
	</form>	
</div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".brand_pop_up").colorbox({
	title:true,
	maxWidth:"50%", 
	maxHeight:"50%"
});
</script>
</html>
