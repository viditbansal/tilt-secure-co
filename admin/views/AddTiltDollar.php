<?php
require_once('includes/CommonIncludes.php');
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
require_once('controllers/CoinsController.php');
$coinsManageObj   =   new CoinsController();
admin_login_check();
$param	=	$class			=	$pass_error	=	$userName	=	'';
$coin			=	1;
$maxCoins = 0;
if(isset($_GET['id'])	&&	$_GET['id'] !=''){
	$userId		=	$_GET['id'];
	$param		=	"?id=".$userId;
	$successUrl	=	"GameDeveloperDetail?viewId=".$userId;
}

$fields		= 	" id,Name,Company,Amount";
if(isset($userId)) {
	$condition	=	"  id=".$userId." AND Status != '3' AND Status != '4' ORDER BY Name ";

$userListResult	=	$coinsManageObj->selectGameDeveloperDetails($fields,$condition);
}
if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ) {
	foreach($userListResult as $key=>$value){
		$userName	=	'';
		if(isset($value->Company)	&&	isset($value->Company)) 	
			$userName	=	$value->Company != '' ? ucfirst($value->Company) : '-';
		if(isset($_GET['id']) &&	isset($value->id) && $_GET['id'] == $value->id){
			if(isset($value->Amount)) { $maxCoins	=	$value->Amount; }
		}
	}
}

if(isset($_POST['userId']) &&	$_POST['userId'] !=''	&&	isset($_POST['coin']) &&	$_POST['coin'] !=''	){
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	$insertId		=	$coinsManageObj->insertTiltDollarCoin($_POST);
	
	if(isset($insertId) && $insertId!=''){
	if(isset($_POST['remove']) && $_POST['remove']==1) {
		$condition	=	" id = ".$_POST['userId']." ";
		$update_string = " Amount = Amount -".$_POST['coin']." ";
		$updateStatus	=	$coinsManageObj->updateGameDeveloperDetails($update_string,$condition);
		$_SESSION['notification_msg_code']	=	12;
	}else if(isset($_POST['sec_password']) &&	$_POST['sec_password'] !='') {
		$fields		  	=	" * ";
		$where		  	=	" 1 ";
		$secondaryPass 	= 	"";
		$passwordDetails = $adminLoginObj->getAdminDetails($fields,$where);
		if(isset($passwordDetails) && is_array($passwordDetails) && count($passwordDetails)>0){
				$secondaryPass 	= 	$passwordDetails[0]->SecondaryPassword;
		}
		if($secondaryPass !=''	&&	$_POST['sec_password'] == $secondaryPass){ //$sec_password
			$condition	=	" id = ".$_POST['userId']." ";
			$update_string = " Amount = Amount +".$_POST['coin']." ";
			$updateStatus	=	$coinsManageObj->updateGameDeveloperDetails($update_string,$condition);
			$_SESSION['notification_msg_code']	=	1;	
		}else{
			$successUrl = '';
			$pass_error	=	'Invalid secondary password';
			$class	= "error_msg";
		}
	}
	if($successUrl !=''){
	?>
	<script type="text/javascript">
		window.parent.location.href = '<?php echo $successUrl; ?>';
	</script>
<?php	}
}
}
commonHead();

?>

<body >
<?php if(!isset($_GET['id'])	&&	!isset($_GET['refer'])){	top_header(); } ?>
	<div class="box-header">
		<h2><?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1) echo '<i class="fa fa-minus-circle"></i> Remove';  else  echo '<i class="fa fa-plus-circle"></i>Add';?>&nbsp;TiLT$</h2>
	</div>
	<div class="clear">
	<form name="add_tilt_dollar_form" id="add_tilt_dollar_form" action="AddTiltDollar<?php echo $param;?>" method="post">
	<?php if(isset($_GET['remove'])	&&	$_GET['remove'] == 1){ ?> <input type="hidden" id="remove" value="1" name="remove"> 
	<?php } ?>
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
			<tr><td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" width="50%">
					<tr><td colspan="3" align="center" height="35"><div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($pass_error) && $pass_error != '') echo $pass_error;  ?></span></div></td></tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td width="22%"  height="60"  align="left"  valign="top"><label>Developer & Brand&nbsp;<span class="required_field">*</span></label></td>
						<td width="5%" align="center"  valign="top">:</td>
						<td width="" align="left"  valign="top">
							<input type="hidden" id="userId" name="userId" value="<?php echo $_GET['id'];?>">
							<?php echo $userName;?>
						</td>						
					</tr>
					<tr>
						<td height="50"  align="left"  valign="top"><label>TiLT$&nbsp;<span class="required_field">*</span></label></td>
						<td  align="center"  valign="top">:</td>
						<td align="left"  valign="top" height="70">
							<input type="hidden" id="dollarvalue" value="<?php if(isset($_GET['remove'])) echo $value->Amount; ?>">
							<div class="select_inc">
								<input type="text" class="input" id="coin" name="coin"  value="<?php if(isset($coin) && $coin != '') echo $coin;  ?>" onkeypress="return isTiltCoinField(event,this)">
								<div class="inc button-inc"><a  onclick="increment('coin');"><i class="fa fa-plus "></i></a></div><div class="dec button-inc"><a  onclick="decrement('coin');"><i class="fa fa-minus "></i></a></div> 															
							</div>
							<br><br><span id="coin_empty" class="error" style="display:none; color:#ff5555;padding-top:6px;">TiLT$ is required</span>							
						</td>
					</tr>
					<tr>
						<?php if(isset($_GET['remove']) && $_GET['remove'] == '1') { ?>
						<input type="hidden" id="submit_status" value="0" name="submit_status">
						<input type="hidden" id="remove_tilt" name="remove_tilt" value="1">
							<td colspan="3" align="center">
							<input type="hidden" id="oldCoins" name="oldCoins" value="<?php if(isset($maxCoins) && $maxCoins !=0) echo $maxCoins; ?>">
							<a href="#" class="submit_button" onClick="return validateForm('remove');" >Remove</a>
							</td>
						<?php } else { ?>
						<input type="hidden" id="submit_status" value="1" name="submit_status">
						<td align="center" colspan="3">
							<input type="hidden" id="hidden_password" name="sec_password" value="">
							<span id="show_popup"><a href="#secondary_password"	class="pop_up_password" style="display:none" >Add</a></span>
								<a href="javascript:void(0);" class="submit_button" onClick="return validateForm('add');" >Add</a>
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
											<input type="submit" class="submit_button" name="submit" id="submit" value="Proceed" onClick="secValidateForm();" title="Proceed" alt="Proceed">
										</td>
									</tr>
									<tr><td height="10"></td></tr>
								</table>
							</div>
						</div>
						</td>
						<?php } ?>
						
					</tr>
					<tr><td height="30">&nbsp;</td>
				</table>
			</tr>
		</table>
	</form>
	</div>
</body>

<?php commonFooter(); ?>
<script type="text/javascript">
function validateForm(type){
	var id		=	$("#userId").val();
	var coins	=	$("#coin").val();
	var oldval = $("#oldCoins").val();
	if(id ==''	&&	(coins =='' || coins == 0)){
		$("#user_empty").show();
		if($("#coin").val() == '0')
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
	else if($("#coin").val() == '0' || $("#coin").val() == ''){		
		if($("#coin").val() == '0')
			$("#coin_empty").html('TiLT$ must be minimum one');
		else
			$("#coin_empty").html('TiLT$ is required');
		$("#coin_empty").show();
		return false;
	}
	else if(coins.length >8 && type !='remove' ){
		$("#coin_empty").html('TiLT$ maximum allowed is 99,999,999');
		$("#coin_empty").show();
		$("#user_empty").hide();
		return false;	
	}
	else{
		if(type && type =='add'){
			$("#submit_status").val(0);
			$("#pwd_empty").hide();
			$('#show_popup a').trigger('click');
		}
		else
		removeTilt();
	}
}
$(".pop_up").colorbox({
	inline:true,
	width:"70%", 
	height:"45%",
	title:true
});
$(".pop_up_password").colorbox(
{
		inline:true,
		width:"70%", 
		height:"45%",
		title:true,
		onClosed:function(){$("#submit_status").val(1);}
});
function secValidateForm(){
	$("#pwd_empty").hide();
	$("#hidden_password").val($("#sec_password").val());
	if($("#hidden_password").val() !=''){
		$("#add_tilt_dollar_form").submit();
		$("#pwd_empty").hide();
	}
	else
		$("#pwd_empty").show();
}
function checksubmit(evt){
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if(charCode == 13)
		secValidateForm();
}
$('#add_tilt_dollar_form').submit(function() {showLoader();});
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
			if(remove && remove == 1){ 
			validateForm('remove');
			event.preventDefault();
			 return false;
			}
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
		var newval = $("#coin").val();
		var oldval = $("#oldCoins").val();				
		if(oldval !=''	&&  parseInt(newval) > parseInt(oldval)) {					
			$("#coin_empty").text("Please enter TiLT$ less than or equal to "+$("#oldCoins").val());
			$("#coin_empty").show();
			return false;			
		}else {
			$("#add_tilt_dollar_form").submit();						
			return true;	
		}
	}
}

</script>
</html>