<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/CoinsController.php');
$coinsManageObj   =   new CoinsController();
admin_login_check();
$field_focus	=	'coin';
$param	=	$class			=	$pass_error	=	'';
$coin			=	1;
$userId			=	'';
$userArray		=	array();
$successUrl		=	"";
if(isset($_GET['id']) && $_GET['id'] !='' && isset($_GET['back']) && $_GET['back'] !=''){
	$back       =   $_GET['back'];
	$userId		=	$_GET['id'];
	$param		=	"?id=".$userId."&back=".$back;
	$successUrl	=	"UserDetail?back=".$back."&viewId=".$userId;
}
else if(isset($_GET['id'])	&&	$_GET['id'] !=''){
	$userId		=	$_GET['id'];
	$param		=	"?id=".$userId;
	$successUrl	=	"UserDetail?viewId=".$userId;
}
else if(isset($_GET['refer'])){
	$successUrl	=	"VirtualCoinList";
	$param		=	"?refer=1";
}
commonHead();
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
}

if(isset($_POST['userId']) &&	$_POST['userId'] !=''	&&	isset($_POST['coin']) &&	$_POST['coin'] !=''	){
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	$insertId		=	$coinsManageObj->insertVirtualCoin($_POST);
	if($insertId){
		$condition	=	" id = ".$_POST['userId']." ";
		if(isset($_POST['remove'])	&&	trim($_POST['remove']==1)){
			$paymentArray = array("coin"=>$_POST['coin'], "userId"=>$_POST['userId'], "ctype"=>2, "type"=>7);
			$update_string = " VirtualCoins = VirtualCoins - ".$_POST['coin']." ";
			$condition	.=	" AND VirtualCoins >= ".$_POST['coin']." ";
			$_SESSION['notification_msg_code']	=	12;
		}
		else{
			$paymentArray = array("coin"=>$_POST['coin'], "userId"=>$_POST['userId'], "ctype"=>2, "type"=>6);
			$_SESSION['notification_msg_code']	=	1;
			$update_string = " VirtualCoins = VirtualCoins +".$_POST['coin']." ";
		}
		$coinsManageObj->insertPaymentHistroy($paymentArray);
		$updateStatus	=	$coinsManageObj->updateUserDetails($update_string,$condition);
	}
	if($successUrl !=''){
	?>
	<script type="text/javascript">
		window.parent.location.href = '<?php echo $successUrl; ?>';
	</script>
<?php	}else{
		header("location:VirtualCoinList?cs=1");
		die();
	}
}
$fields		= 	" id,FirstName,LastName,VirtualCoins ";
if($userId)
	$condition	=	"  id=".$userId." AND Status != '3' ORDER BY FirstName ";
else
	$condition	=	"  (FirstName !='' OR LastName !='') AND Status != '3' ORDER BY FirstName ";
$userListResult	=	$coinsManageObj->selectUserDetails($fields,$condition);
$jsonArray		=	array();
$virtualCoins	=	0;
if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ) {
	foreach($userListResult as $key=>$value){
		$userName	=	'';
		if(isset($value->FirstName)	&&	isset($value->LastName)) 	
			$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
		else if(isset($value->FirstName))	
			$userName	=	 ucfirst($value->FirstName);
		else if(isset($value->LastName))	
			$userName	=	ucfirst($value->LastName);
		if(isset($value->VirtualCoins)) $virtualCoins	=	$value->VirtualCoins;
		$userArray[$value->id]	=	$userName;
		$jsonArray[$key]['id']	=	$value->id;
		$jsonArray[$key]['user']	=	$userName;
	}
	$userjsonArray	=	json_encode($jsonArray);
}
?>
<body >
<?php if(!isset($_GET['id'])	&&	!isset($_GET['refer'])){	top_header(); } ?>
	<div class="box-header">
		<h2><?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1) echo '<i class="fa fa-minus-circle"></i> Remove';  else echo '<i class="fa fa-plus-circle"></i>Add';?>&nbsp;Virtual Coins</h2>
	</div>
	<div class="clear">
	<form name="add_virtual_coin_form" id="add_virtual_coin_form" action="AddVirtualCoin<?php echo $param;?>" method="post">
	<?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1){ ?> <input type="hidden" id="remove" value="1" name="remove"> 
		<?php if(isset($virtualCoins) && $virtualCoins !=0) echo '<input type="hidden" id="max_coins" value="'.$virtualCoins.'" name="max_coins">';?> 
	<?php }?>
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
			<tr><td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" width="50%">
					<tr><td colspan="3" align="center" height="35"><div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($pass_error) && $pass_error != '') echo $pass_error;  ?></span></div></td></tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td width="22%"  height="60"  align="left"  valign="top"><label>User&nbsp;<span class="required_field">*</span></label></td>
						<td width="3%" align="center"  valign="top">:</td>
						<td width="" align="left"  valign="top">
						<?php if(isset($_GET['id'])	&&	$_GET['id'] !=''){ 
								if(isset($userArray) && is_array($userArray) && isset($userArray[$_GET['id']])){ ?>
									<input type="hidden" id="userId" name="userId" value="<?php echo $_GET['id'];?>">
									<input type="hidden" id="userExist" name="userExist" value="<?php echo $_GET['id'];?>">
									<?php $name	=	$userArray[$_GET['id']];
									$name	=	trim($name);
									$name	=	rtrim($name,' ');
									if( $name !='') echo $name; else echo 'Guest'.$_GET['id']; ?>
						<?php 	}
						}else { ?>
						<input type="text" id="searchUser" name="searchUser">
						<input type="hidden" id="userId" name="userId">
						<?php } ?>
						</td>
					</tr>
					<tr>
						<td height="50"  align="left"  valign="top"><label>Virtual Coin(s)&nbsp;<span class="required_field">*</span></label></td>
						<td  align="center"  valign="top">:</td>
						<td align="left"  valign="top" height="70">
							<div class="select_inc error_align width_full">
								<input type="text" onkeypress="return isNumberKey(event);" class="input" id="coin" name="coin"  value="<?php if(isset($coin) && $coin != '') echo $coin;  ?>" <?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1 && isset($virtualCoins) && $virtualCoins !=0) echo 'max="'.$virtualCoins.'"';?> min="1" onpaste="return false" ondrop="event.dataTransfer.dropEffect='none';event.stopPropagation(); event.preventDefault();" autocomplete="off" maxlength="8">
								<div class="inc button-inc"><a  onclick="increment('coin');"><i class="fa fa-plus "></i></a></div><div class="dec button-inc"><a  onclick="decrement('coin');"><i class="fa fa-minus "></i></a></div> 
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center">
						<?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1){ ?>
						<input type="Submit" name="Submit" value="Remove" id="Submit" class="submit_button" >
						<input type="hidden" id="remove_coins" name="remove_coins" value="1">
						<?php }else { ?>
						<input type="Submit" name="Submit" value="Add" id="Submit" class="submit_button" >
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
<?php 
commonFooter(); 

?>
<script type="text/javascript">
$(document).ready(function() {		
		
	});
	
	$("#searchUser").tokenInput(<?php echo $userjsonArray;?>,{
		tokenLimit : 1,
		animateDropdown:true,
		propertyToSearch: "user",
		preventDuplicates: true,
		onAdd: function (item) {
		$("#userId").val(item.id);
			return item;
		},
		onDelete: function (item) {
			$("#userId").val('');
		},
		onResult:function (item)	{
			return item;
		},
		noResultsText: "No result"
	 });
$(function(){
   var bodyHeight = $('body').height();
   var maxHeight = '864';
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
$('#add_virtual_coin_form').submit(function() {
	var coin = $("#coin").val();
	if($("#remove_coins").val() != 1 && $("#userId").val() != '' && coin && coin !='' && coin != '0' && (coin && coin.length < 8) ) showLoader();
});	 
</script>
</html>
