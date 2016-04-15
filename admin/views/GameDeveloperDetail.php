<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/GameController.php');
$gameObj   =   new GameController();
admin_login_check();
commonHead();
$original_path = '';
$image_path 	= ADMIN_IMAGE_PATH.'developer_logo.png';
$interestStatus		 =	$entryfee_flag	=	0;
$popup = 1;
if(isset($_GET['statistics'])	&&	$_GET['statistics']	==	1){
	$statistics	=	'?statistics=1';
	$popup = 0;
}
if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
	$where      			= 	"   id = ".$_GET['viewId']." and Status in (0,1,2,4) LIMIT 1 ";
	$gamedeveloperResult  	= 	$gameObj->SingleGameDeveDetails($where);
	if(isset($gamedeveloperResult) && is_array($gamedeveloperResult) && count($gamedeveloperResult) > 0){
		$name       		=	$gamedeveloperResult[0]->Name;
		$companyname		=	$gamedeveloperResult[0]->Company;
		$email       		=	$gamedeveloperResult[0]->Email;
		$amount				=	$gamedeveloperResult[0]->Amount;
		$status				=	$gamedeveloperResult[0]->Status;
		$virtualCoins		=	$gamedeveloperResult[0]->VirtualCoins;
		$verificationStatus	=	$gamedeveloperResult[0]->VerificationStatus;
		$regDate			=	$gamedeveloperResult[0]->DateCreated;
	}
	$image_path = ADMIN_IMAGE_PATH.'developer_logo.png';
	$original_path = "";
	$photo = $gamedeveloperResult[0]->Photo;
	if(isset($photo) && $photo != ''){
		$user_image = $photo;		
		$image_path_rel 	= DEVELOPER_THUMB_IMAGE_PATH_REL.$user_image;
		$original_path_rel 	= DEVELOPER_IMAGE_PATH_REL.$user_image;
		if(SERVER){
			if(image_exists(15,$user_image)){
				$image_path 	= DEVELOPER_THUMB_IMAGE_PATH.$user_image;
				$original_path 	= DEVELOPER_IMAGE_PATH.$user_image;
			}
		}
		else if(file_exists($image_path_rel)){
				$image_path 	= DEVELOPER_THUMB_IMAGE_PATH.$user_image;
				$original_path 	= DEVELOPER_IMAGE_PATH.$user_image;
		}
	}
}	
?>
<body>
	<?php if($popup) top_header(); ?>
	 <div class="box-header"><h2><i class='fa fa-search'></i>Developer & Brand Details</h2>
	 <?php if($popup){ 
				if(isset($status) && $status == 1) { ?>
		<span class="fright">
			<a class="addvirtcoins" href="AddDevBrandVirtualCoin?devBrandId=<?php echo $_GET['viewId']; ?>&back=<?php if(isset($_GET['back'])) echo $_GET['back']; ?>"  title="Add Virtual Coins"><i class="fa fa-plus-circle"></i> Add Virtual Coins</a>
			<?php if(isset($virtualCoins) && !empty($virtualCoins) && $virtualCoins >0) { ?>
				<a class="addvirtcoins" href="AddDevBrandVirtualCoin?devBrandId=<?php echo $_GET['viewId']; ?>&back=<?php if(isset($_GET['back'])) echo $_GET['back']; ?>&remove=1"  title="Remove Virtual Coins"><i class="fa fa-minus-circle"></i> Remove Virtual Coins</a>
			<?php } ?>
			<a class="addTiltDollar" href="AddTiltDollar?id=<?php echo $_GET['viewId']; ?>"  title="Add TiLT$"><i class="fa fa-plus-circle"></i> Add TiLT$</a>
			<?php if($gamedeveloperResult[0]->Amount != 0 && $gamedeveloperResult[0]->Amount !='') {?>
				<a class="addTiltDollar" href="AddTiltDollar?id=<?php echo $_GET['viewId']; ?>&remove=1"  title="Remove TiLT$"><i class="fa fa-minus-circle"></i> Remove TiLT$</a>
			<?php } ?>
		</span>
	<?php } 
		} ?>	
	 </div>
	 <div class="clear">
  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="list headertable" width="98%">							        	
			<tr>
				<td align="center">
					<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">
						<?php if(isset($_GET['vircoin'])) { ?>	
						<tr><td align="center" colspan="6" class="msg_height"><?php displayNotification('Virtual Coins'); ?></td></tr>
						<?php } else{ ?>
						<tr><td align="center" colspan="6" class="msg_height"><?php displayNotification('TiLT$'); ?></td></tr>
						<?php } ?>
						<tr>
							<td align="left" valign="top" width="15%"><label>Company Name</label></td>
							<td align="center" valign="top" width="3%">:</td>										
							<td align="left" valign="top" width="32%"><?php if(isset($companyname) && $companyname != '') echo $companyname;  else echo '-'; ?></td>
							<td align="left" valign="top" width="15%"><label>Email</label></td>
							<td align="center" valign="top" width="3%">:</td>										
							<td align="left" valign="top" width="32%"><?php if(isset($email) && $email != '') echo $email; else echo '-'; ?></td>
						</tr>
						<tr><td height="20"></td></tr>
						<tr>
							<td align="left" valign="top"><label>Contact Name</label></td>
							<td align="center" valign="top">:</td>										
							<td align="left" valign="top"><?php if(isset($name) && $name != '') echo ucFirst($name);  else echo '-'; ?></td>
							<td align="left" valign="top" ><label>Status</label></td>
							<td align="center" valign="top">:</td>										
							<td align="left" valign="top"><?php if((isset($verificationStatus) && $verificationStatus == 0) && (isset($status) && $status == 1)) {
										echo 'Not Verified';
									} else if(isset($status) && $status !='' && isset($gameStatusArray[$status])) {
										echo $gameStatusArray[$status];
									} else echo '-'; ?></td>
						</tr>
						<tr><td height="20"></td></tr>
						<tr>
							<td align="left" valign="top" ><label>TiLT$</label></td>
							<td align="center" valign="top">:</td>										
							<td align="left" valign="top"><?php echo (isset($amount) && !empty($amount)) ? number_format($amount) : '-'; ?></td>
							<td align="left" valign="top" ><label>Virtual Coins</label></td>
							<td align="center" valign="top">:</td>										
							<td align="left" valign="top"><?php echo (isset($virtualCoins) && $virtualCoins != '') ? number_format($virtualCoins) : 0; ?></td>
						</tr>
						<tr><td height="20"></td></tr>						
						<tr>
							<td align="left" valign="top" ><label>Image</label></td>
							<td align="center" valign="top">:</td>										
							<td align="left" valign="top"><a <?php if(isset($original_path) && $original_path != '') { ?> href="<?php echo $original_path; ?>" class="photo_pop_up"<?php } else { ?> href="Javascript:void(0);"<?php } ?> title="Click here" alt="Click here" ><?php if(isset($image_path) && $image_path != '') { ?> <img class="img_border" width="75" height="75" src="<?php echo $image_path;?>"><?php } ?></a></td>
							<td align="left" valign="top" ><label>Registered Date</label></td>
							<td align="center" valign="top">:</td>										
							<td align="left" valign="top"><?php if(isset($regDate) && $regDate != '' && $regDate != '0000-00-00 00:00:00' ) echo date('m/d/Y',strtotime($regDate)); else echo '-'; ?></td>
						</tr>
						<tr><td height="20"></td></tr>						
						<tr><td height="20"></td></tr>						
						<tr>										
						<td colspan="6" align="center">		
						<?php if($popup) { ?>
							<?php if(isset($_GET['back']) && $_GET['back'] != '' ) {?>
							<a href="GameDeveloperManage?<?php echo 'back='.$_GET['back'];?><?php echo '&editId='.$_GET['viewId'];?>"  class="submit_button" name="Edit" id="Edit"  value="Edit" title="Edit" alt="Edit">Edit </a>
							<a href="<?php echo $_GET['back'];?>" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
							
							<?php } else { ?>
							<?php if(isset($_GET['viewId']) && $_GET['viewId'] != ''){ ?>
								<a href="GameDeveloperManage?<?php echo 'editId='.$_GET['viewId'];?>"  class="submit_button" name="Edit" id="Edit"  value="Edit" title="Edit" alt="Edit">Edit </a>
							<?php } ?>
							<a href="GameDeveloperList" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
							<?php } ?>
						<?php }else{ ?>
							<a href="GameDeveloperList?statistics=1" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
						<?php } ?>
						</td>
					</tr>		
					</table>
				</td>
			</tr>
			<tr><td align="center">&nbsp;</td></tr>
				
		</table>	
</body>
<?php commonFooter();  ?>
<script type="text/javascript">	
	$(document).ready(function() {	
		$(".photo_pop_up").colorbox({title:true});
		$(".addTiltDollar").colorbox(
		{
				iframe:true,
				 width:"50%", 
				 height:"50%",
				 title:true,
		});
		$(".addvirtcoins").colorbox({
				iframe:true,
				width:"40%", 
				height:"45%",
				title:true,
		});
		$(function(){
		   var bodyHeight = $('body').height();
		   var bodyWidth  = $('body').width();
		   var maxHeight = '750';
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
	});
</script>