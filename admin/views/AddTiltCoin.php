<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
require_once('controllers/CoinsController.php');
$coinsManageObj   =   new CoinsController();
admin_login_check();
$field_focus	=	'coin';
$param	=	$class	=	$pass_error	=	'';
$coin			=	1;
$userId			=	'';
$userArray		=	array();
$successUrl		=	"";
if(isset($_GET['id']) && $_GET['id'] !='' && isset($_GET['back']) && $_GET['back'] !=''){
	$back       =   $_GET['back'];
	$userId		=	$_GET['id'];
	$param		=	"?id=".$userId."&back=".$back;
	$successUrl	=	"UserDetail?back=".$back."&viewId=".$userId."&tilt=1";
}
else if(isset($_GET['id'])	&&	$_GET['id'] !=''){
	$userId		=	$_GET['id'];
	$param		=	"?id=".$userId;
	$successUrl	=	"UserDetail?viewId=".$userId."&tilt=1";
}
else if(isset($_GET['refer'])){
	$successUrl	=	"TiltCoinsList";
	$param		=	"?refer=1";
}
commonHead();
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
}
if(isset($_POST['userId']) &&	$_POST['userId'] !=''	&&	isset($_POST['coin']) &&	$_POST['coin'] !=''	){
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	if(isset($_POST['remove'])	&&	trim($_POST['remove']==1)){
		$insertId		=	$coinsManageObj->insertTiltCoin($_POST);
		if($insertId){
			$paymentArray = array("coin"=>$_POST['coin'], "userId"=>$_POST['userId'], "ctype"=>1, "type"=>7);
			$coinsManageObj->insertPaymentHistroy($paymentArray);
			$update_string = " Coins = Coins - ".$_POST['coin']." ";
			$condition	=	" id = ".$_POST['userId']." AND Coins >=".$_POST['coin']." ";
			$updateStatus	=	$coinsManageObj->updateUserDetails($update_string,$condition);
		}
		$_SESSION['notification_msg_code']	=	12;
		if($successUrl !=''){
		?>
		<script type="text/javascript">
			window.parent.location.href = '<?php echo $successUrl; ?>';
		</script>
<?php	
			die();
		}else{

			
			header("location:TiltCoinsList");
			die();
		}
	
	}else if(isset($_POST['sec_password']) &&	$_POST['sec_password'] !='') {
		$fields		  	=	" * ";
		$where		  	=	" 1 ";
		$secondaryPass 	= 	"";
		$passwordDetails = $adminLoginObj->getAdminDetails($fields,$where);
		if(isset($passwordDetails) && is_array($passwordDetails) && count($passwordDetails)>0){
				$secondaryPass 	= 	$passwordDetails[0]->SecondaryPassword;
		}
		if($secondaryPass !=''	&&	$_POST['sec_password'] == $secondaryPass){ //$sec_password
			$insertId		=	$coinsManageObj->insertTiltCoin($_POST);
			if($insertId){
				$paymentArray = array("coin"=>$_POST['coin'], "userId"=>$_POST['userId'], "ctype"=>1, "type"=>6);
				$coinsManageObj->insertPaymentHistroy($paymentArray);
				$update_string = " Coins = Coins +".$_POST['coin']." ";
				$condition	=	" id = ".$_POST['userId']." ";
				$updateStatus	=	$coinsManageObj->updateUserDetails($update_string,$condition);
			}
			$_SESSION['notification_msg_code']	=	1;
			if($successUrl !=''){
			?>
			<script type="text/javascript">
				window.parent.location.href = '<?php echo $successUrl; ?>';
			</script>
	<?php	
				die();
			}else{
				header("location:TiltCoinsList");
				die();
			}
		}
	}
	if(isset($_POST['coin']))
		$coin	=	$_POST['coin'];
	if(isset($_POST['userId']))
		$userId	=	$_POST['userId'];
	$pass_error	=	'Invalid secondary password';
	$class	= "error_msg";

}
$fields		= 	" id,FirstName,LastName,Coins ";
$condition	=	"  (FirstName !='' OR LastName !='') AND Status != '3' ORDER BY FirstName ";
$userListResult	=	$coinsManageObj->selectUserDetails($fields,$condition);
$jsonArray	=	array();
$maxCoins	=	0;
if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ) {
	foreach($userListResult as $key=>$value){
		$userName	=	'';
		if(isset($value->FirstName)	&&	isset($value->LastName)) 	
			$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
		else if(isset($value->FirstName))	
			$userName	=	 ucfirst($value->FirstName);
		else if(isset($value->LastName))	
			$userName	=	ucfirst($value->LastName);
		if(isset($_GET['id']) &&	isset($value->id) && $_GET['id'] == $value->id){
			if(isset($value->Coins)) { $maxCoins	=	$value->Coins; }
		}
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
		<h2><?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1) echo '<i class="fa fa-minus-circle"></i> Remove';  else echo '<i class="fa fa-plus-circle"></i>Add';?>&nbsp; TiLT$</h2>
	</div>
	<div class="clear">
	<form name="add_tilt_coin_form" id="add_tilt_coin_form" action="AddTiltCoin<?php echo $param;?>" method="post">
		<?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1){ ?> <input type="hidden" id="remove" value="1" name="remove">
		<input type="hidden" id="submit_status" value="0" name="submit_status">
		<?php }else{ ?>
		<input type="hidden" id="submit_status" value="1" name="submit_status">
		<?php } ?>
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
			<tr><td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" width="50%">
					<tr><td height="30"></td></tr>
					<tr><td colspan="3" align="center"><div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($pass_error) && $pass_error != '') echo $pass_error;  ?></span></div></td></tr>
					<tr><td height="20"></td></tr> 
					<tr>
						<td width="22%"  height="50"  align="left"  valign="top"><label>User&nbsp;<span class="required_field">*</span></label></td>
						<td width="3%" align="center"  valign="top">:</td>
						<td width="" align="left"  valign="top" height="60">
						<?php if(isset($_GET['id'])	&&	$_GET['id'] !=''){ 
								if(isset($userArray) && is_array($userArray) && isset($userArray[$_GET['id']])){ ?>
									<input type="hidden" id="userId" name="userId" value="<?php echo $_GET['id'];?>">
									<?php echo $userArray[$_GET['id']]; ?>
						<?php 	}
						} else  { ?>
							<input type="text" id="searchUser" name="searchUser" >
							<input type="hidden" id="userId" name="userId" >
							<span id="user_empty" class="error" style="display:none;padding-top:2px;">User is required</span>
						<?php } ?>
						</td>
					</tr>
					<tr>
						<td height="50"  align="left"  valign="top"><label>TiLT$&nbsp;<span class="required_field">*</span></label></td>
						<td align="center"  valign="top">:</td>
						<td align="left" valign="top" height="70">
							<div class="select_inc error_align">
								<input type="text" class="input" id="coins" name="coin"  value="<?php if(isset($coin) && $coin != '') echo $coin;  ?>" <?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1 && isset($maxCoins) && $maxCoins !=0) ;?> onkeypress="return isTiltCoinField(event,this)"  onpaste="return false" ondrop="event.dataTransfer.dropEffect='none';event.stopPropagation(); event.preventDefault();">
								<div class="inc button-inc"><a onclick="increment('coins');"><i class="fa fa-plus "></i></a></div>
								<div class="dec button-inc"><a onclick="decrement('coins');"><i class="fa fa-minus "></i></a></div>
							</div>
							<br><br><span id="coin_empty" class="error" style="display:none; color:#ff5555;padding-top:6px;">TiLT$ must be minimum one</span>
						</td>
					</tr>
					<?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1) { ?>
					<tr>
						<td align="center" colspan="3">
						<a href="#" class="submit_button" onClick="return validateForm('remove');" >Remove</a>
						<input type="hidden" id="remove_tilt" name="remove_tilt" value="1">
						<input type="hidden" id="oldCoins" name="oldCoins" value="<?php if(isset($maxCoins) && $maxCoins !=0) echo $maxCoins; ?>">
						</td>
					</tr>	
					<?php }else{ ?>
					<tr>
						<td align="center" colspan="3">
							<input type="hidden" id="hidden_password" name="sec_password" value="">
							<span id="show_popup"><a href="#secondary_password"	class="pop_up_password" style="display:none" >Add</a></span>
								<a href="#" class="submit_button" onClick="return validateForm('add');" >Add</a>
							<div style="display:none;">
							<div id="secondary_password">
								<table cellpadding="0" cellspacing="0" align="center" border="0">
									<tr><td height="20"></td></tr>
									<tr>
										<td width="30%"  height="50"  align="right"  valign="top" nowrap><label>Secondary Password&nbsp;<span class="required_field">*</span></label></td>
										<td width="8%" align="center"  valign="top">:</td>
										<td width="45%" align="left" valign="top">
											<input type="password" class="input" id="sec_password" name="sec_password" value="" onkeypress="return checksubmit(event);">
											<br><span id="pwd_empty" style="display:none; color:red">Secondary Password is required</span>
										</td>
									</tr>
									<tr>
										<td colspan="3" align="center">
											<input type="submit" class="submit_button" name="submit" id="submit" value="Proceed" onClick="submitForm();" title="Proceed" alt="Proceed">
										</td>
									</tr>
									<tr><td height="10"></td></tr>
								</table>
							</div>
						</div>
						
						</td>
					</tr>
					<?php } ?>
				</table>
				</td>
			</tr>
			<tr><td height="70"></td></tr>
		</table>
	</form>
	</div>
<?php 
commonFooter(); 

?>
<script type="text/javascript">
$(document).ready(function() {		
<?php if(isset($_GET['id'])	||	isset($_GET['refer'])){ ?>
		$(".pop_up").colorbox(
			{
				inline:true,
				width:"70%", 
				height:"45%",
				title:true,
		});
<?php } else {?>
		$(".pop_up").colorbox(
		{
				inline:true,
				width:"40%", 
				height:"35%",
				title:true,
		});
<?php } ?>
		
	});
$(".pop_up_password").colorbox(
{
		inline:true,
		width:"70%", 
		height:"45%",
		title:true,
		onClosed:function(){$("#submit_status").val(1);}
});

<?php if(isset($_POST['userId']) && $_POST['userId'] != '') { ?>
$("#userId").val(<?php echo $_POST['userId']; ?>);
	$("#searchUser").tokenInput(<?php echo $userjsonArray;?>,{
		tokenLimit : 1,
		propertyToSearch: "user",
		onAdd: function (item) {
		$("#userId").val(item.id);
			return item;
			},
		onDelete: function (item) {
			$("#userId").val('');
			},
		prePopulate: [
			{id: <?php echo $_POST['userId']; ?>, user: "<?php echo $userArray[$_POST['userId']]; ?>"}
		]
	});
<?php } else { ?>
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

<?php } ?>
function validateForm(type){
	$("#coin_empty").html('TiLT$ is required');
	var id		=	$("#userId").val();
	var coins	=	$("#coins").val();
	var oldval = $("#oldCoins").val();
	if(id ==''	&&	(coins =='' || coins == 0)){
		$("#user_empty").show();
		if($("#coins").val() == '0')
			$("#coin_empty").html('TiLT$ must be minimum one');
		else
			$("#coin_empty").html('TiLT$ is required');
		$("#coin_empty").show();
		return false;
	}
	else if(id ==''){
		$("#user_empty").show();
		$("#coin_empty").hide();
		return false;	
	}
	else if(coins =='' || coins == 0){
		if($("#coins").val() == 0)
			$("#coin_empty").html('TiLT$ must be minimum one');
		else
			$("#coin_empty").html('TiLT$ is required');
		$("#coin_empty").show();
		$("#user_empty").hide();
		return false;	
	}else if(coins.length >8 && type !='remove' ){
		$("#coin_empty").html('TiLT$ maximum allowed is 99,999,999');
		$("#coin_empty").show();
		$("#user_empty").hide();
		return false;	
	}
	else if(id !=''	&&	(coins !='' || coins != 0)){
		$("#user_empty").hide();
		$("#coin_empty").hide();
		var submitStatus = $("#submit_status").val();
		if(type && type =='add'){
				$("#submit_status").val(0);
				$("#pwd_empty").hide();
			$('#show_popup a').trigger('click');
		}
		else{ 
			removeTilt();
		}
	}
}
function submitForm(){
	$("#pwd_empty").hide();
	$("#submit_status").val(0);
	$("#hidden_password").val($("#sec_password").val());
	if($("#hidden_password").val() !=''){
		$("#add_tilt_coin_form").submit();
		$("#pwd_empty").hide();
	}
	else{
		$("#pwd_empty").show();
	}
}
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
function checksubmit(evt){
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if(charCode == 13){
		submitForm();
	}
}
$('#add_tilt_coin_form').submit(function() {
	if($("#remove_tilt").val() != 1)
		showLoader();
});
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
	var submitStatus = $("#submit_status").val();
		if(submitStatus && submitStatus !='0'){
			validateForm('add');
			  event.preventDefault();
			  return false;
		}else{
		var remove = $("#remove_tilt").val();
		if(remove && remove == 1){ validateForm('remove');}
		}
    }
  });
});
function removeTilt(){	
	if($("#coin").val() == '0' || $("#coin").val() == ''){	
		$("#coin_empty").html('TiLT$ is required');
		$("#coin_empty").show();
		return false;
	}else{	
		var oldval = newval =  '';
			var newval = $("#coins").val();
			var oldval = $("#oldCoins").val();				
			if(oldval !=''	&&  parseInt(newval) > parseInt(oldval)) {					
				$("#coin_empty").text("Please enter TiLT$ less than or equal to "+$("#oldCoins").val());
				$("#coin_empty").show();
				return false;			
			}else {
				$("#add_tilt_coin_form").submit();						
				return true;	
			}
	}
}
</script>
</html>
