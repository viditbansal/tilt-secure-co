<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/GiftCardController.php');
$inAppObj	=	new GiftCardController();
admin_login_check();
commonHead();
$alreadyExist = 0;
$error = $class = "";
if(isset($_GET['editId'])	&&	$_GET['editId']!="")	{
	//Check Aready exist
	$fields = " * ";
	$condition = " id =".$_GET['editId']."";
	$productDetail = $inAppObj->selectInAppDetails($fields,$condition);
	if(isset($productDetail) && !empty($productDetail) && is_array($productDetail)){
				$productArray['ProductId']		=	$productDetail[0]->ProductId;
				$productArray['product_name']	=	$productDetail[0]->Name;
				$productArray['product_desc']	=	$productDetail[0]->Description;
				$productArray['product_price']	=	$productDetail[0]->Price;
				$productArray['product_status']	=	$productDetail[0]->Status;
				$alreadyExistArray[] = $productArray;
	}
}
if(isset($_POST['submit'])	&&	$_POST['submit']!="")	{
	$post_values	=	$_POST;
	$post_values    =   unEscapeSpecialCharacters($post_values);
	$post_values    =   escapeSpecialCharacters($post_values);
	$productIdArray = $oldEntry = $productNameArray = $productDescArray = $productStatusArray = $productPriceArray = array();
	if(isset($post_values['productId']) && is_array($post_values['productId']) && count($post_values['productId'])>0){
		$productIdArray = $post_values['productId'];
		if(isset($post_values['entryId'])  && is_array($post_values['entryId']) && count($post_values['entryId'])>0) $oldEntry = $post_values['entryId'];
		if(isset($post_values['product_name']) &&  is_array($post_values['product_name']) && count($post_values['product_name'])>0) $productNameArray = $post_values['product_name'];
		if(isset($post_values['product_desc']) &&  is_array($post_values['product_desc']) && count($post_values['product_desc'])>0) $productDescArray = $post_values['product_desc'];
		if(isset($post_values['product_status']) &&  is_array($post_values['product_status']) && count($post_values['product_status'])>0) $productStatusArray = $post_values['product_status'];
		if(isset($post_values['product_price']) &&  is_array($post_values['product_price']) && count($post_values['product_price'])>0) $productPriceArray = $post_values['product_price'];
			$alreadyExistArray = array();
		foreach($productIdArray as $productKey =>$productId){
			$productName = $productDesc = '';
			$productStatus = 2;
			$productId = trim($productId);
			if(!empty($productId)){
				$productArray['ProductId']	=	trim($productId);
				$productArray['product_name']	=	"";
				$productArray['product_desc']	=	"";
				$productArray['product_price']	=	"";
				$productArray['product_status']	=	"2";
				if(isset($productNameArray[$productKey]) && trim($productNameArray[$productKey]) !='')
					$productArray['product_name']	=	trim($productNameArray[$productKey]);
				if(isset($productDescArray[$productKey]) && trim($productDescArray[$productKey]) !='')
					$productArray['product_desc']	=	trim($productDescArray[$productKey]);
				if(isset($productStatusArray[$productKey]) && $productStatusArray[$productKey] =='1')
					$productArray['product_status']	=	$productStatusArray[$productKey];
				if(isset($productPriceArray[$productKey]) && trim($productPriceArray[$productKey]) !=''){
					$productArray['product_price']	=	trim($productPriceArray[$productKey]);
					$productArray['product_price']	=	round($productArray['product_price'],2);
				}
				if(!empty($productArray['product_name']) && !empty($productArray['product_desc']) && !empty($productArray['product_price']) ){
					//Check Aready exist
					$fields = "id,ProductId ";
					if(isset($_GET['editId']) && $_GET['editId'] !="")
						 $condition = " id <> ".$_GET['editId']." AND ProductId ='".$productId."'";
					else $condition = " ProductId ='".$productId."'";
					$productRes = $inAppObj->selectInAppDetails($fields,$condition);
					if(isset($productRes) && !empty($productRes) && is_array($productRes)){ //already exist
							$alreadyExistArray[] = $productArray;
							$alreadyExist = 1;
					}else{ //New entries
						if(isset($_GET['editId']) && $_GET['editId'] !=""){
							$update_string	=	"";
							$update_string	.=	" ProductId = '".$productArray['ProductId']."',";
							$update_string	.=	" Name = '".$productArray['product_name']."',";
							$update_string	.=	" Description = '".$productArray['product_desc']."',";
							$update_string	.=	" Price = '".$productArray['product_price']."',";
							$update_string	.=	" Status = '".$productArray['product_status']."',";
							$update_string	.=	" DateModified			 =	'".date('Y-m-d H:i:s')."'";
							$condition	= " id = ".$_GET['editId']." ";
							$inAppObj->updateInAppDetails($update_string,$condition);
							$_SESSION['notification_msg_code']	=	2; ?>
						<script type="text/javascript">
							window.parent.location.href = 'InAppPackageList';
						</script>
				<?php 	die();
						}
						else{
							$inAppObj->insertInAppDetails($productArray);
							$_SESSION['notification_msg_code']	=	1;
						}
					}
				}//Update on 05-01-2015 Purpose: Restrict uncompleted entries
			}
		}
	}
	if($alreadyExist == 0) {
		header("location:InAppPackageList");
		die();
	}
	else{
		$class = "error_msg";
		$msg = "Product Id already exist";
	}
}
?>
<body >
<?php if(!isset($_GET['editId'])) top_header(); ?>
	<div class="box-header">
		<h2><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo '<i class="fa fa-edit"></i>Edit '; else echo '<i class="fa fa-plus-circle"></i>Add ';?>InApp Product</h2>
	</div>
	<div class="clear">
		<form name="add_inapp_form" id="add_inapp_form" action="" method="post" onsubmit="return submitProductDetails();">
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="98%">
			<tr><td align="center"><div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($msg) && $msg != '') echo $msg;  ?></span></div></td></tr>
			<tr><td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" id="product_list" width="80%">
					<tr height="50" clone="0">
					<?php if(!isset($_GET['editId'])){ ?>	<th width="15%" colspan="2">&nbsp;</th><?php } ?>
						<th width="15%" align="left">
							<label style="padding-left: 5px">Product Id<span class="required_field">&nbsp;*</span></label>
						</th>
						<th width="20%" align="left">
							<label style="padding-left: 5px">Product Name<span class="required_field">&nbsp;*</span></label>
						</th>
						<th width="25%" align="left">
							<label style="padding-left: 5px">Product Description<span class="required_field">&nbsp;*</span></label>
						</th>
						<th width="13%" align="left">
							<label style="padding-left: 5px">Price<span class="required_field">&nbsp;*</span></label>
						</th>
						<th width="10%" align="left"><label style="padding-left: 5px">Status</label></th>
					</tr>
					<?php if(isset($alreadyExistArray) && is_array($alreadyExistArray) && count($alreadyExistArray)>0) {
						$count = count($alreadyExistArray);
							foreach($alreadyExistArray as $key =>$productDetails){
								if($count == ($key+1))
									 $addMore = "";
								else $addMore = 'style="display:none"';
					?>
					<tr height="130" clone="<?php echo ($key+1);?>">
						<?php if(!isset($_GET['editId'])){ ?>
						<td align="right" width="4%" valign="top"><a href="javascript:void(0)" onclick="deleteProdRow(this)"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>
						<td align="left" width="7%" valign="top"><a href="javascript:void(0)" onclick="addProdRow(this)" class="add_new" <?php echo $addMore; ?>><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td> 
						<?php } ?>
						<td align="left" width="20%" valign="top">
							<input type="text" class="input productId product-group" name="productId[]" id="productId" maxlength="50" value="<?php if(isset($productDetails['ProductId']) && !empty($productDetails['ProductId'])) echo unEscapeSpecialCharacters($productDetails['ProductId']); ?>">
						</td>
						<td align="left" width="28%" valign="top"><input type="text" class="input product_name" style="width:240px;" name="product_name[]" id="product_name" maxlength="100" value="<?php if(isset($productDetails['product_name']) && !empty($productDetails['product_name'])) echo unEscapeSpecialCharacters($productDetails['product_name']); ?>"></td>
						<td align="left" width="25%" valign="top" ><textarea cols="43" rows="4" maxlength="1000" name="product_desc[]" id="product_desc" class="product_desc product-group"><?php if(isset($productDetails['product_desc']) && !empty($productDetails['product_desc'])) echo unEscapeSpecialCharacters($productDetails['product_desc']); ?></textarea></td>
						<td align="left" width="10%" valign="top" style="padding-right:10px;"><input type="text" class="input product_price" name="product_price[]" id="product_price" maxlength="11" onkeypress="return isFloatNumber(event,this,8);" value="<?php if(isset($productDetails['product_price']) && !empty($productDetails['product_price'])) echo $productDetails['product_price']; ?>" onpaste="return false;" title=" invalid"></td> 
						<td height="50" width="10%" align="left"  valign="top" style="padding-left:18px;">
						<input type="checkbox" class="prodStatus" onclick="setStatus(this,<?php echo ($key+1);?>)" <?php if(isset($productDetails['product_status']) && $productDetails['product_status'] == 1) echo "checked"; ?>>
						<input type="hidden" name="product_status[]" class="prodStatusHidden" id="hiddenStatus1" value="<?php if(isset($productDetails['product_status']) && !empty($productDetails['product_status'])) echo $productDetails['product_status']; ?>">
						</td>
					</tr>
					<?php } //End foreach
					}else { ?>
					<tr height="125" clone="1">
						<td align="right" width="4%" valign="top"><a href="javascript:void(0)" onclick="deleteProdRow(this)"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>
						<td align="left" width="7%" valign="top"><a href="javascript:void(0)" onclick="addProdRow(this)" class="add_new" ><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td> 
						<td align="left" width="20%" valign="top">
						<input type="text" class="input productId" name="productId[]" id="productId" maxlength="50" value="">
						</td>
						<td align="left" width="25%" valign="top"><input type="text" class="input product_name" style="width:240px;" name="product_name[]" id="product_name" maxlength="100" value=""></td>
						<td align="left" width="30%" valign="top" ><textarea cols="43" rows="4" name="product_desc[]" id="product_desc" class="product_desc" maxlength="1000"></textarea></td>
						<td align="left" width="10%" valign="top" style="padding-right:10px;"><input type="text" onpaste="return false;" class="input product_price" name="product_price[]" id="product_price" maxlength="11" onkeypress="return isFloatNumber(event,this,8);" value="" ></td>
						<td height="50" width="10%" align="left"  valign="top" style="padding-left:18px;">
						<input type="checkbox" class="prodStatus" onclick="setStatus(this,1)">
						<input type="hidden" name="product_status[]" class="prodStatusHidden" id="hiddenStatus1" value="0">
						</td>
					</tr>
					<?php } ?>
				</table>
			</td></tr>
			</tr><td height="20"></td></tr>
			</tr><td align="center">
			<input type="submit" class="submit_button" name="submit" id="submit" value="Save" title="Save" alt="Save" >&nbsp;&nbsp;
			<?php if(!isset($_GET['editId'])){ ?>
			<a href="InAppPackageList" class="submit_button" title="Back" alt="Back">Back</a>
			<?php }else{?>
			<?php }?>
			</td></tr>
			</tr><td height="20"></td></tr>
			
		</table>
		</form>
	</div>
</div>	
						  	
<?php commonFooter(); ?>
<script type="text/javascript">
$(function(){
   var bodyHeight = $('body').height();
   var maxHeight = '564';
   if(bodyHeight<maxHeight) {
   	setHeight = bodyHeight;
   } else {
   		setHeight = maxHeight;
   }
    parent.$.colorbox.resize({
        innerWidth:$('body').width(),
        innerHeight:setHeight
    });
});
function setStatus(ref,id){
	if ($(ref).is(':checked')) 
		 $("#hiddenStatus"+id).val(1);
	else $("#hiddenStatus"+id).val(0);
}
function submitProductDetails(){
	var i = 0;
	$(".error").remove();
	var len = $("#product_list tr").length;
	var errorFlag = 1;
	var submitFlag = 1;
	$("#product_list tr").each(function() {
		if(i !=0){
			var text 	=	$(this).find("input.productId").eq(0).val();
			var text1 	=	$(this).find("input.product_name").eq(0).val();
			var text2 	=	$(this).find("textarea.product_desc").val();
			var text3 	=	$(this).find("input.product_price").val();
			if((i+1) == len && i!= 1) {
				if(text == "" && text1 == "" && text2 == "" && (text3 == "" || text3 == '0') ) 
				{	
					errorFlag = 0;
				}
			}
			if(errorFlag == 1){
				if(text == "") {
					submitFlag = 0;
					$(this).find("input.productId").after('<span class="error" generated="true">Product Id is required</span>');
				}
				if(text1 == "") {
					submitFlag = 0;
					$(this).find("input.product_name").after('<span class="error" for="product_name" generated="true">Product Name is required</span>');
				}
				if(text2 == ""){
					submitFlag = 0;
					$(this).find("textarea.product_desc").after('<span class="error" for="product_desc" generated="true">Product Description is required</span>');
				}
				if(text3 == ""){
					submitFlag = 0;
					$(this).find("input.product_price").after('<span class="error" for="product_price" generated="true">Prize is required</span>');
				}
			}
		}
		i++;
	});
	if(submitFlag) return true;
	else return false;
}
</script>
</html>