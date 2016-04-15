<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/AdminController.php');
require_once("includes/phmagick.php");
admin_login_check();
commonHead();
require_once('controllers/CoinsController.php');
$coinsManageObj   =   new CoinsController();
$class			=	"";
$countriesList	=	$countriesArray	=	$statesArray	=	array();
$usId	=	236;
$countries	=	$states	=	$usLabel	=	'';
if(isset($_POST['usId']) &&	$_POST['usId'] !=''){
	$updateString	=	' Status	=	2';
	$condition		=	' fkCountriesId = '.$_POST['usId'].'';
	$coinsManageObj->updateCountryStatus($updateString,$condition);
	$_SESSION['notification_msg_code']	=	2;
	header("location:LocationRestriction?cs=1");
		die();
}
$fields			=	' rc.*,c.Country,s.State ';
$condition		=	' rc.Status = 1 ';
$countriesListResult		=	$coinsManageObj->getRestCountries($fields,$condition);
if(isset($countriesListResult) && is_array($countriesListResult) && count($countriesListResult) > 0){
	foreach($countriesListResult as $key=>$values){
		if($values->fkCountriesId==$usId){
			$statesArray[$values->fkStatesId]	=	$values->State;
			$usLabel		=	$values->Country;
		}
		else if($values->Country !=''){
			
			$countriesArray[$values->fkCountriesId]	=	$values->Country;
		}
	}
	if($usLabel !=''){
		$countriesArray[$usId]	=	$usLabel;
	}
	asort($statesArray);
	asort($countriesArray);
}
?>
<body >
	<?php top_header();  ?>
	<div class="box-header">
		<h2><i class="fa fa-list"></i>Location restriction for buying GFT$</h2>
		<span class="fright">
			<a href="ManageRestriction" class="restrictCountries" title="Restrict Country"><i class="fa fa-plus-circle"></i>&nbsp;Restrict Country</a>
		</span>
	</div>
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" width="95%">
					<tr><td colspan="6" align="center">
						<?php displayNotification('Restricted Country List');	?>
					</td></tr>
					<tr> <td height="20"> </td></tr>
					<tr>
						<td width="12%" height="50" align="left"  valign="top"><label>Country</label></td>
						<td width="2%" align="center"  valign="top">:</td>
						<td width="80%" align="left"  height="40"  valign="top">
							<div class="res_countries" id="countries">
							<?php if(isset($countriesArray)	&&	is_array($countriesArray) && count($countriesArray)>0	){ 
										foreach($countriesArray as $countryKey=>$countryValue)
										{ 
							?>				<div  id="<?php echo $countryKey; ?>" class="res_countries_delete rest_country<?php echo $countryKey; ?>" >
												<input type="text"  name="countries[]" value="<?php echo displayText($countryValue,'25'); ?>" readonly />
												<input type="hidden"  name="countriesId[]" value="<?php echo $countryKey; ?>" />
												<?php if($countryKey == $usId) {?>
												<form name="remove_us" id="remove_us" action="" method="post">
												<input type="hidden"  name="usId" value="<?php echo $countryKey; ?>" />
													<a onclick="javascript:if(confirm('Are you sure want to delete some states belongs to this country?')) $('#remove_us').submit();" title="Delete" class="remove"><i class="fa fa-times-circle"></i></a>
														<?php }else { ?>
															<a onclick="remove_country(<?php echo $countryKey ?>,'location_restrict');" class="remove" title="Delete"><i class="fa fa-times-circle"></i></a>
														<?php } ?>
												</form>	
											</div>
							<?php		} 
									}else echo ' - ';
							?>
							</div>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
						<tr id="state_block">
							<td width="12%" height="50" align="left"  valign="top"><label>US State </label></td>
							<td width="2%" align="center"  valign="top">:</td>
							<td width="80%" align="left"  height="40"  valign="top">
							<div class="res_countries" id="states" >	
							<?php 	if(isset($statesArray)	&&	is_array($statesArray) && count($statesArray)>0	){ 
										foreach($statesArray as $stateKey=>$stateValue)
										{ 
							?>				<div  id="<?php echo $stateKey; ?>" class="res_countries_delete rest_state<?php echo $stateKey; ?>" >
												<input type="text"  name="states[]" value="<?php echo displayText($stateValue,'25'); ?>" readonly />
												<input type="hidden"  name="statesId[]" value="<?php echo $stateKey; ?>" />
												<a onclick="remove_state(<?php echo $usId; ?>,<?php echo $stateKey; ?>,'location_restrict');" class="remove" title="Delete"><i class="fa fa-times-circle"></i></a>
											</div>
							<?php		}
									}else echo ' - ';
							?>
							</div>
							</td>
						</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr><td height="10"></td></tr>				  
		</table>
<?php commonFooter(); ?>
<script type="text/javascript">
$(document).ready(function() {		
		$(".restrictCountries").colorbox(
		{
				iframe:true,
				width:"700", 
				height:"350",
				title:true,
		});
});
</script>
</html>
