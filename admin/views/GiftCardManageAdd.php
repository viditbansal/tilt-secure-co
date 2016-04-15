<?php
require_once('includes/CommonIncludes.php');
require_once('controllers/GiftCardController.php');
$inAppObj	=	new GiftCardController();
admin_login_check();
commonHead();
$alreadyExist = 0;
$error = $class = "";
function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
// also take care to save uploaded gamecard files to UPLOAD_GAMECARD_PATH_REL
if(isset($_GET['editId'])	&&	$_GET['editId']!="")	{
	//Check Aready exist
	$fields = " * ";
	$condition = " id =".$_GET['editId']."";
	$productDetail = $inAppObj->selectGiftCardDetails($fields,$condition);
	if(isset($productDetail) && !empty($productDetail) && is_array($productDetail)){
		$productArray['GiftCardMerchantId']	=	$productDetail[0]->GiftCardMerchantId;
		$productArray['CurrencyCode']	=	$productDetail[0]->CurrencyCode;
		$productArray['GiftCardName']	=	$productDetail[0]->GiftCardName;
		$productArray['Amount']		    =	$productDetail[0]->Amount;
		$productArray['CoverImage']		=	$productDetail[0]->CoverImage;
		$productArray['Status']	    	=	$productDetail[0]->Status;
		$alreadyExistArray[] = $productArray;
	}
}
if(isset($_POST['submit'])	&&	$_POST['submit']!="")	{
	$post_values =  escapeSpecialCharacters(unEscapeSpecialCharacters($_POST));
	$GiftCardMerchantIdArray = $newmerchantnameArray = $CurrencyCodeArray = $GiftCardNameArray = 	$AmountArray = 	$CoverImageArray = 	$StatusArray = 	$VisibilityArray = array();
	$oldEntry = $GiftCardMerchantId = $newmerchantname = $CurrencyCode = $GiftCardName = $Amount = $CoverImage = $Status = $Visibility = array();
	error_log('U1-41 00-->'.var_export($post_values, true));
	if(isset($post_values['productId']) && is_array($post_values['productId']) && count($post_values['productId'])>0){
		error_log('U1-41 -->'.var_export($post_values, true));
		error_log('U1-41 FILES----->'.var_export($_FILES, true));
		$CardId = $post_values['productId'];
		if(isset($post_values['GiftCardMerchantId'  ]) && is_array($post_values['GiftCardMerchantId'  ]) && count($post_values['GiftCardMerchantId'  ])>0) $GiftCardMerchantIdArray   = $post_values['GiftCardMerchantId'  ];
		if(isset($post_values['CurrencyCode']) && is_array($post_values['CurrencyCode']) && count($post_values['CurrencyCode'])>0) $CurrencyCodeArray = $post_values['CurrencyCode'];
		if(isset($post_values['newmerchantname']) && is_array($post_values['newmerchantname']) && count($post_values['newmerchantname'])>0) $newmerchantnameArray = $post_values['newmerchantname'];
		if(isset($post_values['GiftCardName']) && is_array($post_values['GiftCardName']) && count($post_values['GiftCardName'])>0) $GiftCardNameArray = $post_values['GiftCardName'];
		if(isset($post_values['Amount'      ]) && is_array($post_values['Amount'      ]) && count($post_values['Amount'      ])>0) $AmountArray       = $post_values['Amount'      ];
		if(isset($post_values['CoverImage'  ]) && is_array($post_values['CoverImage'  ]) && count($post_values['CoverImage'  ])>0) $CoverImageArray   = $post_values['CoverImage'  ];
		if(isset($post_values['Status'      ]) && is_array($post_values['Status'      ]) && count($post_values['Status'      ])>0) $StatusArray       = $post_values['Status'      ];
		$alreadyExistArray = array();
		foreach($CardId as $productKey =>$productId){
			error_log('U1-41 --> foreach cardID:'. var_export($CardId, true));
			// upload image for GiftCard
			if ( !file_exists(UPLOAD_GIFTCARD_PATH_REL) ){
				mkdir (UPLOAD_GIFTCARD_PATH_REL, 0777, true);
			}
			if(getimagesize($_FILES["CoverImage"]["tmp_name"][0])!==false){
				$temp_image_path = $_FILES["CoverImage"]["tmp_name"][0];
				$ext = pathinfo($_FILES["CoverImage"]["name"][0], PATHINFO_EXTENSION);
				$filename = date("Ymd_His") . '_' . generateRandomString();
				$image_path = UPLOAD_GIFTCARD_PATH_REL.$filename.'.'.$ext;
				$image_url  = UPLOAD_GIFTCARD_PATH.$filename.'.'.$ext;//$productId
				// error_log('copy FROM!---->>'.$temp_image_path);
				// error_log('copy TO  !---->>'.$image_path);
				copy($temp_image_path,$image_path);
			}
			if(getimagesize($_FILES["newmerchanticon"]["tmp_name"][0])!==false){
				$temp_image_path = $_FILES["newmerchanticon"]["tmp_name"][0];
				$ext = pathinfo($_FILES["newmerchanticon"]["name"][0], PATHINFO_EXTENSION);
				$filename = 'newmerchanticon' . date("Ymd_His") . '_' . generateRandomString();
				$image_path = UPLOAD_GIFTCARD_PATH_REL.$filename.'.'.$ext;
				$newmerchanticon_image_url  = UPLOAD_GIFTCARD_PATH.$filename.'.'.$ext;//$productId
				error_log('copy FROM!---->>'.$temp_image_path);
				error_log('copy TO  !---->>'.$image_path);
				copy($temp_image_path,$image_path);
			}

			$productName = $productDesc = '';
			$productStatus = 2;
			$productId = trim($productId);
			if(!empty($GiftCardMerchantIdArray[$productKey])){
				error_log('U1-433 found GiftCardMerchantId...');
				$productArray['GiftCardId'  ]	=	trim($productId);
				$productArray['GiftCardMerchantId'  ]	=	"";
				$productArray['newmerchantname'  ]	=	"";
				$productArray['CurrencyCode']	=	"";
				$productArray['GiftCardName']	=	"";
				$productArray['Amount'      ]	=	"";
				$productArray['CoverImage'  ]	=	"";
				$productArray['Status'      ]	=	"1";
				if(isset($GiftCardMerchantIdArray[$productKey]) && trim($GiftCardMerchantIdArray[$productKey]) !='')
					$productArray['GiftCardMerchantId']	=	trim($GiftCardMerchantIdArray[$productKey]);
				if(isset($CurrencyCodeArray[$productKey]) && trim($CurrencyCodeArray[$productKey]) !=''){
					$productArray['CurrencyCode']	=	trim($CurrencyCodeArray[$productKey]);
				}
				if(isset($GiftCardNameArray[$productKey]) && trim($GiftCardNameArray[$productKey]) !=''){
					$productArray['GiftCardName']	=	trim($GiftCardNameArray[$productKey]);
				}

				if(isset($newmerchantnameArray[$productKey]) && trim($newmerchantnameArray[$productKey]) !=''){
					$productArray['newmerchantname']	=	trim($newmerchantnameArray[$productKey]);
				}
				if(isset($newmerchanticonArray[$productKey]) && trim($newmerchanticonArray[$productKey]) !=''){
					$productArray['newmerchanticon']	=	trim($newmerchanticonArray[$productKey]);
				}

				if(isset($AmountArray[$productKey]) && trim($AmountArray[$productKey]) !=''){
					$productArray['Amount']	=	round(trim($AmountArray[$productKey]),2);
				}
				if(getimagesize($_FILES["CoverImage"]["tmp_name"][0])!==false){
					$productArray['CoverImage']	=	$image_url;
				}
				if(isset($StatusArray[$productKey]) && trim($StatusArray[$productKey]) !=''){
					$productArray['Status']	=	trim($StatusArray[$productKey]);
				}
				if(!empty($productArray['GiftCardMerchantId']) && !empty($productArray['Amount']) ){
					//Check Aready exist
					$fields = "id,CardId ";
					if(isset($_GET['editId']) && $_GET['editId'] !="")
						 $condition = " id <> ".$_GET['editId']." AND CardId ='".$productId."'";
					else $condition = " CardId ='".$productId."'";
					$productRes = $inAppObj->selectGiftCardDetails($fields,$condition);
          // if new merchant, insert it first and find out the new ID
          if($productArray['GiftCardMerchantId']==-1){
            error_log('U1-433 NEW Mercant!!!!');
            error_log("name: ".$productArray['newmerchantname']);
            error_log("newmerchanticon_image_url: ".$newmerchanticon_image_url);
            $newmerchantid = $inAppObj->createNewMerchant($productArray['newmerchantname'], $newmerchanticon_image_url);
            error_log('U1-433 NEW Mercant ID===>>'.$newmerchantid);
            $productArray['GiftCardMerchantId'] = $newmerchantid;
          }
					if(isset($productRes) && !empty($productRes) && is_array($productRes)){ //already exist
						$alreadyExistArray[] = $productArray;
						$alreadyExist = 1;
						error_log('U1-41 --> already exist');
					}else{ //New entries
						error_log('U1-433');
						if(isset($_GET['editId']) && $_GET['editId'] !=""){
							$update_string	=	"";
							$update_string	.=	" GiftCardMerchantId = '".$productArray['GiftCardMerchantId']."',";
							$update_string	.=	" CurrencyCode = '".$productArray['CurrencyCode']."',";
							$update_string	.=	" GiftCardName = '".$productArray['GiftCardName']."',";
							$update_string	.=	" Amount = '".$productArray['Amount']."',";
							if($productArray['CoverImage']!==''){ $update_string	.=	" CoverImage = '".$productArray['CoverImage']."',"; }
							$update_string	.=	" Status = '".$productArray['Status']."',";
							$update_string	.=	" DateModified			 =	'".date('Y-m-d H:i:s')."'";
							$condition	= " id = ".$_GET['editId']." ";
							error_log('UPDATE-GiftCard::-->'.$update_string.';;;;'.$condition);
							$inAppObj->updateGiftCardDetails($update_string,$condition);
							$_SESSION['notification_msg_code']	=	2; ?>
						<script type="text/javascript">
							window.parent.location.href = 'GiftCardList';
						</script>
				<?php 	die();
						}
						else{
							error_log('ADD NEW:::'.var_export($productArray, true));
							$inAppObj->insertGiftCardDetails($productArray);
							$_SESSION['notification_msg_code']	=	1;
						}
					}
				}
			}
		}
	}
	if($alreadyExist == 0) {
		header("location:GiftCardList");
		die();
	}
	else{
		$class = "error_msg";
		$msg = "GiftCard Id already exist";
	}
}
$merchantList = $inAppObj->selectGiftCardMerchantDetails('*','1');
// error_log('$merchantList::'.var_export($merchantList, true));
?>
<body >
<?php if(!isset($_GET['editId'])) top_header(); ?>
	<div class="box-header">
		<h2><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo '<i class="fa fa-edit"></i>Edit '; else echo '<i class="fa fa-plus-circle"></i>Add ';?>GiftCard</h2>
	</div>
	<div class="clear">
		<form name="add_inapp_form" id="add_inapp_form" action="" method="post" onsubmit="return submitGiftCardDetails();" enctype="multipart/form-data">
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="98%">
			<tr><td align="center"><div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($msg) && $msg != '') echo $msg;  ?></span></div></td></tr>
			<tr><td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" id="giftcard_list" width="80%">
					<tr height="50" clone="0">
					<?php if(!isset($_GET['editId'])){ ?>	<th width="15%" colspan="2">&nbsp;</th><?php } ?>
						<!-- <th width="10%" align="left">
							<label style="padding-left: 5px">GiftCard Id<span class="required_field">&nbsp;*</span></label>
						</th> -->
						<th width="10%" align="left">
							<label style="padding-left: 5px">Merchant<span class="required_field">&nbsp;*</span></label>
						</th>
						<th width="10%" align="left">
							<label style="padding-left: 5px">Amount<span class="required_field">&nbsp;*</span></label>
						</th>
						<th width="7%" align="left"><label style="padding-left: 5px">Currency Code</label></th>
						<th width="7%" align="left"><label style="padding-left: 5px">Gift Card Name</label></th>
						<th width="7%" align="left"><label style="padding-left: 5px">Cover Image</label></th>
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
						<!-- <td align="left" width="10%" valign="top"> -->
							<!-- 161 -->
							<input type="hidden" class="input productId product-group" name="productId[]" id="productId" maxlength="50" onkeypress="return isFloatNumber(event,this,8);" value="<?php if(isset($_GET['editId']) && !empty($_GET['editId'])) echo unEscapeSpecialCharacters($_GET['editId']); ?>">
						<!-- </td> -->
						<td align="left" width="10%" valign="top">
							<!-- drop down list of $merchantList -->
							<select class="merchantList">
							  <option value="-1">* Create New Merchant or select from list *</option>
								<?php
								foreach ($merchantList as $key => $value) {
									echo "<option value='{$value->id}'>{$value->id} - {$value->MerchantName}</option>";
								}
								?>
							</select>
							<input type="hidden" class="input GiftCardMerchantId" style="width:40px; display: none;" name="GiftCardMerchantId[]" id="GiftCardMerchantId" maxlength="100" value="<?php if(isset($productDetails['GiftCardMerchantId']) && !empty($productDetails['GiftCardMerchantId'])) echo unEscapeSpecialCharacters($productDetails['GiftCardMerchantId']); ?>">
							<div class="newmerchant" style="display:none;background-color: lightblue;padding: 5px;">
								New Merchant Name and Icon<br>
								<input type="text" name="newmerchantname[]" class="input newmerchantname" value="">
								<input type="file" name="newmerchanticon[]" class="input newmerchanticon" value="">
							</div>
						</td>
						<td align="left" width="10%" valign="top" style="padding-right:10px;"><input type="text" class="input Amount" name="Amount[]" id="Amount" maxlength="11" onkeypress="return isFloatNumber(event,this,8);" value="<?php if(isset($productDetails['Amount']) && !empty($productDetails['Amount'])) echo $productDetails['Amount']; ?>" onpaste="return false;" title=" invalid"></td>

						<td align="left" width="10%" valign="top"><input type="text" class="input CurrencyCode" style="width:100px;" name="CurrencyCode[]" id="CurrencyCode" maxlength="100" value="<?php if(isset($productDetails['CurrencyCode']) && !empty($productDetails['CurrencyCode'])) echo unEscapeSpecialCharacters($productDetails['CurrencyCode']); ?>"></td>
						<td align="left" width="10%" valign="top"><input type="text" class="input GiftCardName" style="width:100px;" name="GiftCardName[]" id="GiftCardName" maxlength="100" value="<?php if(isset($productDetails['GiftCardName']) && !empty($productDetails['GiftCardName'])) echo unEscapeSpecialCharacters($productDetails['GiftCardName']); ?>"></td>
						<td align="left" width="10%" valign="top">
							<input type="file" class="input CoverImage"   style="width:100px;" name="CoverImage[]"   id="CoverImage"   maxlength="100" value="<?php if(isset($productDetails['CoverImage'])   && !empty($productDetails['CoverImage']))   echo unEscapeSpecialCharacters($productDetails['CoverImage']);   ?>">
						</td>
						<td height="50" width="10%" align="left"  valign="top" style="padding-left:18px;">
						<input type="checkbox" class="prodStatus" onclick="setStatus(this,<?php echo ($key+1);?>)" <?php if(isset($productDetails['giftcard_status']) && $productDetails['giftcard_status'] == 1) echo "checked"; ?>>
						<input type="hidden" name="giftcard_status[]" class="prodStatusHidden" id="hiddenStatus1" value="<?php if(isset($productDetails['giftcard_status']) && !empty($productDetails['giftcard_status'])) echo $productDetails['giftcard_status']; ?>">
						</td>
					</tr>
					<?php } //End foreach
					}else { ?>
					<tr height="125" clone="1">
						<td align="right" width="4%" valign="top"><a href="javascript:void(0)" onclick="deleteProdRow(this)"><i class="fa fa-lg text-red  fa-minus-circle"></i></a>&nbsp;</td>
						<td align="left" width="7%" valign="top"><a href="javascript:void(0)" onclick="addProdRow(this)" class="add_new" ><i class="fa text-green fa-lg fa-plus-circle"></i></a>&nbsp;</td>
						<!-- <td align="left" width="50%" valign="top"> -->
							<!-- 191 -->
							<input type="hidden" class="input productId" name="productId[]" id="productId" maxlength="50" onkeypress="return isFloatNumber(event,this,8);" value="">
						<!-- </td> -->
						<td align="left" width="25%" valign="top">
							<!-- drop down list of $merchantList -->
							<select class="merchantList">
							  <option value="-1">* Create New Merchant or select from list *</option>
								<?php
								// error_log('$merchantList 2 :: '.var_export($merchantList, true));
								foreach ($merchantList as $key => $value) {
									echo "<option value='{$value->id}'>{$value->id} - {$value->MerchantName}</option>";
								}
								?>
							</select>
							<input type="hidden" class="input GiftCardMerchantId" style="width:40px; display: none;" name="GiftCardMerchantId[]" id="GiftCardMerchantId" maxlength="100" value="">
							<div class="newmerchant" style="display:none;background-color: lightblue;padding: 5px;">
								New Merchant Name and Icon<br>
								<input type="text" name="newmerchantname" class="input newmerchantname" value="">
								<input type="file" name="newmerchanticon" class="input newmerchanticon" value="">
							</div>
						</td>
						<td align="left" width="10%" valign="top" style="padding-right:10px;">
							<input type="text" onpaste="return false;" class="input Amount" name="Amount[]" id="Amount" maxlength="100" onkeypress="return isFloatNumber(event,this,8);" value="" >
						</td>
						<td align="left" width="10%" valign="top" style="padding-right:10px;">
							<input type="text" onpaste="return false;" class="input CurrencyCode" name="CurrencyCode[]" id="CurrencyCode" maxlength="100" value="" >
						</td>
						<td align="left" width="10%" valign="top" style="padding-right:10px;">
							<input type="text" onpaste="return false;" class="input GiftCardName" name="GiftCardName[]" id="GiftCardName" maxlength="100" value="" >
						</td>
						<td align="left" width="10%" valign="top" style="padding-right:10px;">
							<input type="file" class="input CoverImage"   style="width:100px;" name="CoverImage[]"  id="CoverImage" maxlength="100" value="">
						</td>
					<td height="50" width="10%" align="left"  valign="top" style="padding-left:18px;">
						<input type="checkbox" class="prodStatus" onclick="setStatus(this,1)">
						<input type="hidden" name="giftcard_status[]" class="prodStatusHidden" id="hiddenStatus1" value="0">
					</td>
					<!-- <td height="50" width="10%" align="left"  valign="top" style="padding-left:18px;">
						<input type="checkbox" class="prodVisibility" onclick="setVisibility(this,1)">
						<input type="hidden" name="giftcard_visibility[]" class="prodVisibilityHidden" id="hiddenVisibility1" value="0">
					</td> -->
					</tr>
					<?php } ?>
				</table>
			</td></tr>
			</tr><td height="20"></td></tr>
			</tr><td align="center">
			<input type="submit" class="submit_button" name="submit" id="submit" value="Save" title="Save" alt="Save" >&nbsp;&nbsp;
			<?php if(!isset($_GET['editId'])){ ?>
			<a href="GiftCardList" class="submit_button" title="Back" alt="Back">Back</a>
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
	// initialize merchant input text field
	var originlMerchantId = $('.GiftCardMerchantId').val();
	$(document).on('change','.merchantList', function(e){
		var newval = $(this).val();
		$(this).next().val(newval);// set dropdown value in the next input box (possible multiple inserts)
		if(newval==-1){
			// user wants to create new merchant
			$(this).next().next().show();
		}else{
			$(this).next().next().hide();
		}
	});
	$('.merchantList').val(originlMerchantId).change();
});
function setStatus(ref,id){
	if ($(ref).is(':checked'))
		 $("#hiddenStatus"+id).val(1);
	else $("#hiddenStatus"+id).val(0);
}
function submitGiftCardDetails(){
	var i = 0;
	$(".error").remove();
	var len = $("#giftcard_list tr").length;
	var errorFlag = 1;
	var submitFlag = 1;
	$("#giftcard_list tr").each(function() {
		if(i !=0){
			var text1 	=	$(this).find("input.GiftCardMerchantId").eq(0).val();
			var text3 	=	$(this).find("input.Amount").val();
			if((i+1) == len && i!= 1) {
				if(text == "" && text1 == "" && text2 == "" && (text3 == "" || text3 == '0') )
				{
					errorFlag = 0;
				}
			}
			if(errorFlag == 1){
				if(text1 == "") {
					submitFlag = 0;
					$(this).find("input.GiftCardMerchantId").after('<span class="error" for="GiftCardMerchantId" generated="true">Merchant Id is required</span>');
				}
				if(text3 == ""){
					submitFlag = 0;
					$(this).find("input.Amount").after('<span class="error" for="Amount" generated="true">Prize is required</span>');
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
