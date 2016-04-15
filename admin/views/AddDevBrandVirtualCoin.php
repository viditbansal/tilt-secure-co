<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/CoinsController.php');
$coinsManageObj   =   new CoinsController();
admin_login_check();
$field_focus	=	'coin';
$param	= $class	= $pass_error	= $devBrandId	= $successUrl	= "";
$coin	= $userType	= 1;
$devBrandArray	= array();
$coinType	= 2;
$virtualCoins	= 0;
if(isset($_GET['devBrandId'])	&&	!empty($_GET['devBrandId'])){
	$devBrandId		=	$_GET['devBrandId'];
	if(isset($_GET['back']) && $_GET['back'] !=''){
		$back       =  $_GET['back'];
		$param		= "?vircoin=1&devBrandId=".$devBrandId."&back=".$back;
		$successUrl = "GameDeveloperDetail??vircoin=1&viewId=".$devBrandId."&back=".$back;
	}else{
		$param		= "?vircoin=1&devBrandId=".$devBrandId;
		$successUrl = "GameDeveloperDetail?vircoin=1&viewId=".$devBrandId; 
	}
	$userType	=	1;
}else if(isset($_GET['refer'])){
	$successUrl	=	"VirtualCoinList?vircoin=1";
	$param		=	"?vircoin=1&refer=1";
}
commonHead();
if(isset($_POST['devBrandId']) &&	$_POST['devBrandId'] !='' && isset($_POST['coin']) && $_POST['coin'] !=''){ 
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	$_POST['userId'] = 0;
	$insertId	= $coinsManageObj->insertVirtualCoin($_POST);
	if($insertId){
		$condition	=	" id = ".$_POST['devBrandId']." ";
		if(isset($_POST['remove'])	&&	trim($_POST['remove']==1)){
			$paymentArray = array("coin"=>$_POST['coin'], "userId"=>0,"devBrandId"=>$_POST['devBrandId'], "ctype"=>2, "type"=>7);
			$update_string = " VirtualCoins = VirtualCoins - ".$_POST['coin']." ";
			$condition	.=	" AND VirtualCoins >= ".$_POST['coin']." ";
			$_SESSION['notification_msg_code']	=	12;
		}
		else{
			$paymentArray = array("coin"=>$_POST['coin'], "userId"=>0, "devBrandId"=>$_POST['devBrandId'], "ctype"=>2, "type"=>6);
			$_SESSION['notification_msg_code']	=	1;
			$update_string = " VirtualCoins = VirtualCoins +".$_POST['coin']." ";
			$_SESSION['notification_msg_code']	=	1;
		}
		$coinsManageObj->insertPaymentHistroy($paymentArray);
		$updateStatus	=	$coinsManageObj->updateGameDeveloperDetails($update_string,$condition);		
	}
	if($successUrl !=''){ ?>
	<script type="text/javascript">
		window.parent.location.href = '<?php echo $successUrl; ?>';
	</script>
<?php	die();
	}else{
		header("location:VirtualCoinList?cs=1");
		die();
	}
}
$fields		= 	" id,Company,VirtualCoins,Email ";
if($devBrandId)
	$condition	=	"  id=".$devBrandId." AND Status != '3' ORDER BY Company ";
else
	$condition	=	"  Status != '3' ORDER BY Company ";
$devBrandListResult	=	$coinsManageObj->selectGameDeveloperDetails($fields,$condition);
$jsonArray		=	array();
$virtualCoins	=	0;
if(isset($devBrandListResult) && is_array($devBrandListResult) && count($devBrandListResult) > 0 ) {
	foreach($devBrandListResult as $key=>$value){
		$devBrandName	=	'';
		if(isset($value->Company)	&&	!empty($value->Company)) 	
			$devBrandName	=	ucfirst($value->Company);
		if(isset($value->VirtualCoins)) $virtualCoins	=	$value->VirtualCoins;
		$devBrandArray[$value->id]	=	$devBrandName;
		$jsonArray[$key]['id']	=	$value->id;
		$jsonArray[$key]['devBrand'] =	$devBrandName;
	}
	$userjsonArray	=	json_encode($jsonArray);
} 
?>
<body >
<?php if(!isset($_GET['devBrandId'])	&&	!isset($_GET['refer'])){	top_header(); } ?>
	<div class="box-header">
		<h2><?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1) echo '<i class="fa fa-minus-circle"></i> Remove';  else echo '<i class="fa fa-plus-circle"></i>Add';?>&nbsp;Virtual Coins</h2>
	</div>
	<div class="clear">
	<form name="add_virtual_coin_form" id="add_virtual_coin_form" action="AddDevBrandVirtualCoin<?php echo $param;?>" method="post">
	<?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1){ ?> <input type="hidden" id="remove" value="1" name="remove"> 
		<?php if(isset($virtualCoins) && $virtualCoins !=0) echo '<input type="hidden" id="max_coins" value="'.$virtualCoins.'" name="max_coins">';?> 
	<?php }?>
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
			<tr><td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" width="70%">
					<tr><td colspan="3" align="center" height="35"><div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($pass_error) && $pass_error != '') echo $pass_error;  ?></span></div></td></tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td width="22%"  height="60"  align="left"  valign="top"><label>Developer & Brand &nbsp;<span class="required_field"></span></label></td>
						<td width="5%" align="center"  valign="top">:</td>
						<td width="" align="left"  valign="top">
						<?php if(isset($_GET['devBrandId'])	&&	$_GET['devBrandId'] !=''){ 
								if(isset($devBrandArray) && is_array($devBrandArray) && isset($devBrandArray[$_GET['devBrandId']])){ ?>
									<input type="hidden" id="devBrandId" name="devBrandId" value="<?php echo $_GET['devBrandId'];?>">
									<input type="hidden" id="userExist" name="userExist" value="<?php echo $_GET['devBrandId'];?>">
									<?php $name	=	$devBrandArray[$_GET['devBrandId']];
									$name	=	trim($name);
									$name	=	rtrim($name,' ');
									if( $name !='') echo ucFirst($name); else echo '-';//'Developer & Brand'.$_GET['devBrandId']; ?>
						<?php 	}
						}else { ?>
							<input type="text" id="searchDevBrandId" name="searchDevBrandId">
							<input type="hidden" id="devBrandId" name="devBrandId">
						<?php } ?>
						</td>
					</tr>
					<tr>
						<td height="50"  align="left"  valign="top"><label>Virtual Coin(s)&nbsp;<span class="required_field">*</span></label></td>
						<td  align="center"  valign="top">:</td>
						<td align="left"  valign="top" height="70">
							<div class="select_inc error_align">
								<input type="text" onkeypress="return isNumberKey(event);" class="input" id="coin" name="coin" maxlength="8" value="<?php if(isset($coin) && $coin != '') echo $coin;  ?>" <?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1 && isset($virtualCoins) && $virtualCoins !=0) echo 'max="'.$virtualCoins.'"';?> min="1" onpaste="return false" ondrop="event.dataTransfer.dropEffect='none';event.stopPropagation(); event.preventDefault();">
								<div class="inc button-inc"><a onclick="increment('coin');"><i class="fa fa-plus "></i></a></div>
								<div class="dec button-inc"><a onclick="decrement('coin');"><i class="fa fa-minus "></i></a></div> 
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center">
							<?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1){ ?>
							<input type="hidden" id="remove_coins" name="remove_coins" value="1">
							<input type="Submit" name="Submit" value="Remove" id="Submit" class="submit_button">
							<?php }else { ?>
							<input type="Submit" name="Submit" value="Add" id="Submit" class="submit_button">
							<?php }?>
						</td>
					</tr>
					<tr><td colspan="3" align="center" height="85"></td></tr>
				</table>
				</td>
			</tr>
		</table>
	</form>
	</div>	
<?php commonFooter(); ?>
<script type="text/javascript">
$(document).ready(function() {		
	 $("#searchDevBrandId").tokenInput(<?php echo $userjsonArray;?>,{
		tokenLimit : 1,
		animateDropdown:true,
		propertyToSearch: "devBrand",
		preventDuplicates: true,
		onAdd: function (item) {
			$("#devBrandId").val(item.id);
			return item;
		},
		onDelete: function (item) {
			$("#devBrandId").val('');
		},
		onResult:function (item)	{
			return item;
		},
		noResultsText: "No result"
	 }); 
	$(function(){
	   var bodyHeight = $('body').height();
	   var maxHeight = '864';
	   if(bodyHeight<maxHeight) { setHeight = bodyHeight; } 
	   else { setHeight = maxHeight; }
		parent.$.colorbox.resize({
			innerWidth:$('body').width(),
			innerHeight:setHeight
		});
	});
	$('#add_virtual_coin_form').submit(function() {
		var coin = $("#coin").val();
		if($("#remove_coins").val() != 1 && $("#devBrandId").val() != '' && coin && coin !='' && coin != '0' && (coin && coin.length < 8) ) showLoader();
	});	 
});
</script>
</html>
