<?php 
require_once("includes/phmagick.php");
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminControllerObj =   new AdminController();
$image_array = $image_patharray = $imageid_array = $image_thumb_patharray = array();
//Get all image detail
$fields	= " * ";
$cond	= "Status = 1 ORDER BY RAND() LIMIT 10";
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
$oldImage = "";
if(isset($_GET['oldImage']) && !empty($_GET['oldImage'])) $oldImage = $_GET['oldImage'];
commonHead();
?>
<body>
		<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">
			<tr>
				<td align="center" style="padding:0px;">
				<div class="">
					<div class="box-header"><h2 style="border:0">Default Image</h2></div>
					<div class="default_image">
						<?php if(is_array($image_array) && isset($image_array) && count($image_array)>0){ 
								echo '<div class="row_img">';
								for($index = 0;$index < count($image_array);$index++){ ?>
									<div id="user_image<?php echo $index ;?>_img" class="user_image">
									<span id="default_image_<?php echo $index ;?>" onclick="onSelectImage('<?php echo $index ;?>');">
										<img class="img_border" src="<?php  echo $image_thumb_patharray[$index];  ?>" width="75" height="75" alt="Image" />
									</span>
									</div>
									<input type="hidden" id="image_path_<?php echo $index ;?>" value="<?php  echo $image_array[$index];  ?>">
						<?php   	if((($index+1) % 5 )== 0 && (($index+1) < count($image_array))) echo '</div><div class="row_img clear">';
								}
								echo "</div>";
							} else {?>		
								<div class="no_image_found"> No Default image available</div>
						<?php }?>	
					</div>
					<div class="clear"></div>
					<div class="box-header" style="margin-top:25px;"><h2 style="border:0">Choose System Image</h2></div>
					<div class="sys_image">
						<div class="upload fleft" id="test_replace">
							<div style="clear: both;float: left"> 
								<input type="file"  name="user_photo" id="user_photo" title="User Photo" onchange="return ajaxAdminFileUploadProcess('user_photo');"  /> 
							</div>
							<div style="text-align:left;padding-top:4px;float:left">(Minimum dimension 100x100)</div>
							<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px" id="block_user_photo">
								<div id="user_photo_img"></div>
							</div>
							<input type="Hidden" name="empty_user_photo" id="empty_user_photo" value="<?php  echo $oldImage;   ?>" />
						</div>
					</div>
				</div>
				</td>
			</tr>		
			<tr><td height="10"></td></tr>
		</table>
<?php commonFooter(); ?>
<script type="text/javascript">
	function onSelectImage(ref){
		window.parent.$("#source_user_photo").html('<div id="user_photo_img">'+$("#default_image_"+ref).html()+'</div>');
		var empty = window.parent.$("#empty_user_photo").val();
		if(empty == '')
			window.parent.$("#empty_user_photo").val(1);
		window.parent.$("#default_image_path").val($("#image_path_"+ref).val());
		parent.$.colorbox.close();
	}
	$(function(){
	   var bodyHeight = $('body').height();
	   var bodyWidth  = $('body').width();
	   var maxHeight = '580';
	   var maxWidth  = '450';
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