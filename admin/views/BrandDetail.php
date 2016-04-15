<?php 
header("Location:UserList?cs=1");die();
require_once('includes/CommonIncludes.php');
require_once('controllers/UserController.php');
require_once('controllers/BrandController.php');
$brandObj   =   new BrandController();
admin_login_check();
commonHead();
$original_image_path =  $original_cover_image_path = $actualPassword = '';
$interestStatus		 =	$entryfee_flag	=	0;
$brandStatus	=	1;
if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
	$where      			= 	"   id = ".$_GET['viewId']." and Status in (0,1,2,4) LIMIT 1 ";
	$brandDetailsResult  	= 	$brandObj->SingleBrandDetails($where);
	if(isset($brandDetailsResult) && is_array($brandDetailsResult) && count($brandDetailsResult) > 0){
		$brand       		=	$brandDetailsResult[0]->BrandName;
		$userName			=	$brandDetailsResult[0]->UserName;
		$name				=	$brandDetailsResult[0]->Name;
		$email       		=	$brandDetailsResult[0]->Email;
		$location			=	$brandDetailsResult[0]->Location;
		$address      		= 	$brandDetailsResult[0]->Address;
		$city		       	= 	$brandDetailsResult[0]->City;
		$state  			= 	$brandDetailsResult[0]->State;
		$country    		= 	$brandDetailsResult[0]->Country;
		$phone    			= 	$brandDetailsResult[0]->Phone;
		$amount    			= 	$brandDetailsResult[0]->Amount;
		$virtualCoin    	= 	$brandDetailsResult[0]->VirtualCoins;
		$brandStatus		= 	$brandDetailsResult[0]->Status;
		if(isset($brandDetailsResult[0]->Logo) && $brandDetailsResult[0]->Logo != ''){
			$brand_id    = $brandDetailsResult[0]->id;
			$brand_image = $brandDetailsResult[0]->Logo;
			$original_image_path = '';
			$image_path = ADMIN_IMAGE_PATH.'no_brand.jpeg';
			if(SERVER){
				if(image_exists(3,$brand_image))
					$original_image_path = BRANDS_IMAGE_PATH.$brand_id.'/original_'.$brand_image;
				if(image_exists(1,$brand_image))
					$image_path = BRANDS_IMAGE_PATH.$brand_id.'/'.$brand_image;
			}
			else{
				if(file_exists(BRANDS_IMAGE_PATH_REL.$brand_id.'/original_'.$brand_image))
					$original_image_path = BRANDS_IMAGE_PATH.$brand_id.'/original_'.$brand_image;
				if(file_exists(BRANDS_IMAGE_PATH_REL.$brand_id.'/'.$brand_image))
					$image_path = BRANDS_IMAGE_PATH.$brand_id.'/'.$brand_image;
			}
		}
		else
			$image_path = ADMIN_IMAGE_PATH.'no_brand.jpeg';
	}	
}

?>
<body>
	<?php if(!isset($_GET['statistics']))	top_header(); ?>
	 <div class="box-header"><h2><i class='fa fa-search'></i>Brand Details</h2>
	 <?php if($brandStatus == 1 && !isset($_GET['statistics'])) {?>
		<span class="fright">
		<a class="addvirtcoins" href="AddBrandTiltVCoins?userType=brand&coinType=2&userId=<?php echo $_GET['viewId']; ?>&back=<?php if(isset($_GET['back'])) echo $_GET['back']; ?>"  title="Add Virtual Coins"><i class="fa fa-plus-circle"></i> Add Virtual Coins</a>
		<?php if(isset($virtualCoin) && !empty($virtualCoin) && $virtualCoin >0) { ?>
		<a class="addvirtcoins" href="AddBrandTiltVCoins?userType=brand&coinType=2&remove=1&userId=<?php echo $_GET['viewId']; ?>&back=<?php if(isset($_GET['back'])) echo $_GET['back']; ?>"  title="Remove Virtual Coins"><i class="fa fa-minus-circle"></i> Remove Virtual Coins</a>
		<?php } ?>
		</span>
	<?php } ?>	
	 </div>
	 <div class="clear">
  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="list headertable" width="98%">							        
			
			<tr><td align="center"><?php displayNotification('Virtual Coins'); ?></td></tr>
			<tr><td class="msg_height"></td></tr>
			<tr>
				<td align="center">
					<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">
						
						<tr>
							<td align="left" valign="top" width="15%"><label>Brand</label></td>
							<td align="center" valign="top" width="3%">:</td>										
							<td align="left" valign="top" width="32%">
								<?php if(isset($brand) && $brand != '') echo $brand;  else echo '-'; ?>
							</td>
							<td align="left" valign="top" width="15%"><label>User Name</label></td>
							<td align="center" valign="top" width="3%">:</td>
							<td align="left" valign="top" width="32%">
								<?php if(isset($userName) && $userName != '') echo $userName; else echo '-'; ?>
							</td>
						</tr>
						<tr><td height="20"></td></tr>
						<tr>
							<td align="left" valign="top" width="15%"><label>Name</label></td>
							<td align="center" valign="top" width="3%">:</td>										
							<td align="left" valign="top"><?php if(isset($name) && $name != '') echo $name;  else echo '-'; ?></td>
							<td align="left" valign="top" ><label>Email</label></td>
							<td align="center" valign="top">:</td>										
							<td align="left" valign="top"><?php if(isset($email) && $email != '') echo $email; else echo '-'; ?></td>

						</tr>
						<tr><td height="20"></td></tr>
						<tr>
							<td align="left" valign="top" ><label>Location</label></td>
							<td align="center" valign="top">:</td>										
							<td align="left" valign="top"><?php if(isset($location) && $location != '') echo $location; else echo '-'; ?></td>
							<td align="left" valign="top"><label>Address</label></td>
							<td align="center" valign="top">:</td>
							<td align="left" valign="top"><?php if(isset($address) && $address != '') echo $address; else echo '-'; ?></td>									
						</tr>
						<tr><td height="20"></td></tr>
						<tr>
							<td align="left" valign="top"><label>City</label></td>
							<td align="center" valign="top">:</td>
							<td align="left" valign="top" ><?php if(isset($city) && $city != '') echo $city; else echo '-'; ?></td>
							<td  align="left" valign="top"><label>State</label></td>
							<td align="center" valign="top">:</td>
							<td align="left"   valign="top"><?php if(isset($state) && $state != '') echo $state;  else echo '-'; ?></a></td>
						</tr>	
						<tr><td height="20"></td></tr>	
						<tr>
							<td align="left" valign="top"><label>Country</label></td>
							<td align="center" valign="top">:</td>
							<td align="left" valign="top" ><?php if(isset($country) && $country != '') echo $country; else echo '-'; ?></td>
							<td  align="left" valign="top"><label>Phone</label></td>
							<td align="center" valign="top">:</td>
							<td align="left"   valign="top"><?php if(isset($phone) && $phone != '') echo $phone;  else echo '-'; ?></a></td>
						</tr>
						<tr><td height="20"></td></tr>	
						<tr>
							<td align="left" valign="top"><label>TiLT$</label></td>
							<td align="center" valign="top">:</td>
							<td align="left" valign="top" ><?php if(isset($amount) && !empty($amount) && $amount != 0) echo number_format($amount);  else echo '-'; ?></td>
							<td  align="left" valign="top"><label>Virtual Coin</label></td>
							<td align="center" valign="top">:</td>
							<td align="left"   valign="top"><?php if(isset($virtualCoin) && !empty($virtualCoin) && $virtualCoin != 0) echo number_format($virtualCoin);  else echo '-'; ?></a></td>
						</tr>
						<tr><td height="20"></td></tr>
						<tr>
							<td align="left" valign="top"><label>Logo</label></td>
							<td align="center" valign="top">:</td>
							<td align="left" valign="top">
								<a <?php if(isset($original_image_path) && $original_image_path != '') {?> href="<?php echo $original_image_path; ?>" class="brand_photo_pop_up" <?php } else { ?> href="Javascript:void(0);" style="cursor:auto;" <?php } ?> title="Click here" alt="Click here">
									<?php if(isset($image_path) && $image_path != '') { ?> 
										<img class="img_border" width="75" height="75" src="<?php echo $image_path;?>">
									<?php } ?>
								</a>
							</td>
						</tr>
						<tr><td height="20"></td></tr>	
					</table>
				</td>
			</tr>
																	
			<tr>										
				<td colspan="6" align="center">	
					
					<?php if(isset($_GET['statistics'])){ ?>
							<a href="BrandList?statistics=1&cs=1" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
						<? } else if(isset($_GET['back']) && $_GET['back'] != '' ) { ?>
					<a href="<?php echo $_GET['back'];?>" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
					<?php } else { ?>
					<a href="BrandManage?editId=<?php if(isset($_GET['viewId']) && $_GET['viewId'] != '') echo $_GET['viewId']; ?>" title="Edit" alt="Edit" class="submit_button" >Edit</a>					
					<a href="BrandList" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
					<?php } ?>					
					
				</td>
			</tr>		
			<tr><td height="35"></td></tr>						   
		</table>
  </div>	
<?php commonFooter(); ?>
<script type="text/javascript">	
	$(document).ready(function() {		
		$(".tournament_list_pop_up").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"45%",
				title:true
		});
		$(".brand_photo_pop_up").colorbox({
			maxwidth:"70%",
			maxheight:"70%",
			title:true
		});
		$(".addvirtcoins").colorbox({
				iframe:true,
				width:"40%", 
				height:"45%",
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
