<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/DeveloperController.php');
require_once('controllers/AdminController.php');

developer_login_check();

$adminLoginObj	=	new AdminController();	
$devObj   		=   new DeveloperController();
$display = 'none';
$tabshow = '0';
$amount		=	$assignedPrize = 0;
$delete_condition  = 0;
$userNameExist = 0 ;
$brand_id	= $givenLong = $givenLat = $givenLocation = '';
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
}
$fields		=	' * ';
$condition	=	' id = 1';
$settingInfo		=	$adminLoginObj->getSettingDetails($fields,$condition);
$conversionValue	=	$settingInfo[0]->ConversionValue;


$fields			=	' Amount ';
$condition		=	' id = '.$_SESSION['tilt_developer_id'];
$developerDet	=	$devObj->selectSingleDeveloper($fields,$condition);
$amount			=	$developerDet[0]->Amount;
$developer_id   = '';
if(isset($_SESSION['tilt_developer_id']) && $_SESSION['tilt_developer_id'] != ''){
$developer_id		=	$_SESSION['tilt_developer_id'];
}
commonHead();
?>
<script src="https://checkout.stripe.com/v2/checkout.js"></script>
	
<body onload="return fieldfocus('buy_amount');" class="skin-black"> 
	<div id="loading" style="display:none;">
		<div class="loading_bg"></div>
		<div class="load_icon"></div>
	</div> 
	<?php top_header(); ?>
	<section class="content-header">
		<h2 align="center">Purchase TiLT$</h2>
	</section>
	
	<section class="content">
		<div class="game-form-style">
			<form name="purchase_coins" id="purchase_coins" action="" method="post">
				<div class="box-body col-md-7 box-center">
					
					<div class="form-group">
						
						<div id='payment_block'>
							<div class="col-sm-4">Buy TiLT$</div>
							<div class="col-sm-4">
							<input type='text' class="form-control" name='buy_amount' class='' id='buy_amount' onkeypress="return isNumberKey(event);" value='' maxlength="8">
							</div>
							<input type='hidden' name='conversionvalue' class='' id='conversionvalue' value='<?php echo $conversionValue;?> '>
							
							<div id="myamount"  class="col-sm-4 text-left">You have <strong><?php echo number_format($amount);?></strong> TiLT$</div>
							<input type='hidden' value='<?php echo $amount; ?>' name='current_amount' id='current_amount'>
						</div> 
					</div>
				</div>
				<div class="box-footer" align="center">
						<input type="submit" class="btn btn-green" name="customButton" id='customButton' value="Buy" title="Buy" alt="Buy">
				</div>
			</form>
		</div>
	</section>	
	
	   
<?php 
 footerLinks();commonFooter();
?>

<script>
  var handler = StripeCheckout.configure({
	key: '<?php echo $stripe['publishable_key']; ?>',
	token: function(token, args) {
		var amount	=	parseFloat($('#buy_amount').val());
		if($.trim($('#buy_amount').val()) == ''){
			alert('Enter valid TiLT$');
			$('#buy_amount').focus();
			return false;
		} 
		$('#loading').show();
		$.post(actionPath+'/models/AjaxAction.php',{action:'PAYMENT_MODULE', 
			stripeToken:token.id, amount:parseFloat($.trim(amount)), developerId:'<?php echo $developer_id; ?>', email:token.email,developerName : '<?php echo ((isset($_SESSION['tilt_developer_name'])) ? $_SESSION['tilt_developer_name'] : '') ?>'
		},
		function(result){
			if($.trim(result) == 'Error') {
				alert('Problem in processing payment. Please try again.');
			}
			else {
				$('#buy_amount').val('');
				$('#myamount strong').html(result);
				$('#dev_tilt_coins').html(result);
			}
			 $('#loading').hide();
		});
	}
  });
  
  document.getElementById('customButton').addEventListener('click', function(e) {
	var convert = parseFloat($('#conversionvalue').val());
	var buy		= parseFloat($('#buy_amount').val());	
	var amount	=	parseFloat(buy / convert).toFixed(2);
	if($.trim(amount) == ''	||	$.trim(amount) == 'NaN' )	{
		alert('Enter valid TiLT$');
		$('#buy_amount').focus();
		return false;		
	}else if(buy < convert) {
		alert('TiLT$ must be greater than or equal to '+convert);
		$('#buy_amount').focus();
		return false;
	}else{
		handler.open({
		  name: 'Developer & Brand',
		  description: 'Buy TiLT$',
		  amount: parseFloat(amount * 100).toFixed(0)
		});
	}
	e.preventDefault();
  });
  
function showpayment()	{
	$('#payment_block').toggle();
}
 $('.fancybox').fancybox({
		type : 'ajax',
	   });
   
function show_pay(val)
{
  if(val == 1)
  	 $('#pay_type').hide();
  	
 else
   $('#pay_type').show();
}
</script>
</body>
</html>