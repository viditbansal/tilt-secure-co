<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/AdminController.php');
require_once("includes/phmagick.php");
admin_login_check();
commonHead();
require_once('controllers/CoinsController.php');
$coinsManageObj   =   new CoinsController();
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
}
$usId	        =	USID;
$field_focus	=	'country';
$class			=	$ExistCondition		=	$location		=	$photoUpdateString	=	'';
$gametype		=	0;
$entryfee_flag	=	$startDateFlag	=	$endDateFlag	=	$dateFlag	=	0;

if(isset($_POST['country'])	&&	!empty($_POST['country']))
{
	$_POST      =   unEscapeSpecialCharacters($_POST);
	$_POST      =   escapeSpecialCharacters($_POST);
	$usFlag		=	$countryFlag	= $countryId	=	0;
	if(is_array($_POST['country']) &&	count($_POST['country']) > 0){
		$countryIds		=	$_POST['country'];
		$countryFlag	=	1;
		if(in_array($usId,$countryIds)){
			$usFlag		=	1;
			$countryId	=	$usId;
			$countryIds = array_diff($countryIds, array($usId));
		}
	}
	else{
		$countryId	=	$_POST['country'];
		$usFlag		=	1;
	}
	$today	=	date('Y-m-d H:i:s');
	if($countryId == $usId){
		if(isset($_POST['state'])	&&	is_array($_POST['state'])	&&	count($_POST['state'])>0){
			$stateIdsArray	=	$_POST['state'];
			$stateIds		=	implode(',',$_POST['state']);
			if($stateIds ==''){
				$updateString	=	' Status	=	2';
				$condition		=	' fkCountriesId = '.$usId.'';
				$coinsManageObj->updateTiltCountryStatus($updateString,$condition);
			}
			else {
				$existArray		=	$newEntryArray	=	$oldStateIdsArray	=	array();
				$newArray		=	array();
				$setActive		=	$unsetStates	=	'';
				if(isset($_POST['oldStateIds'])	&&	$_POST['oldStateIds']!=""){
					$oldStateIds			=	$_POST['oldStateIds'];
					$oldStateIdsArray		=	explode(',',$_POST['oldStateIds']);
					if(isset($oldStateIdsArray)	&&	is_array($oldStateIdsArray)	&& count($oldStateIdsArray) > 0){
						foreach($oldStateIdsArray as $oldStateId){
							if(!in_array($oldStateId,$stateIdsArray))
								$unsetStates	.=	$oldStateId.',';
							else 
								$activeIds[]	=	$oldStateId;
						}
					}
				}
				//Unset Missed State from previous
				$unsetStates	=	rtrim($unsetStates,',');
				if($unsetStates !=''){
					$updateString	=	' Status	=	2';
					$condition		=	' fkCountriesId = '.$countryId.' AND fkStatesId in ('.$unsetStates.')';
					$coinsManageObj->updateTiltCountryStatus($updateString,$condition);
				}
				$fields			=	'*';
				$condition		=	' fkCountriesId = '.$countryId.' AND fkStatesId in ('.$stateIds.')';
				$usEntryResult		=	$coinsManageObj->checkTiltCountryEntry($fields,$condition);
				$newEntry		=	"";
				if(isset($usEntryResult) && is_array($usEntryResult) && count($usEntryResult) > 0){
					foreach($usEntryResult as $key => $value){
							$existArray[]	=	$value->fkStatesId;
					}
				}
				foreach($stateIdsArray as $key => $stateValue){
					if(in_array($stateValue,$existArray)){
						$setActive		.=	$stateValue.',';
					}
					else {
						$newEntryArray[]	=	$stateValue;
						$newEntry	.=	"(".$countryId.",".$stateValue.",1,'".$today."','".$today."'),";
					}
				}
				$newEntry	=	rtrim($newEntry,',');
				if($newEntry !=''){ //insert new entry
					$stateValues	=	' VALUES '.$newEntry;
					$coinsManageObj->insertTiltRestrictedCountries($stateValues);
				}
				$setActive	=	rtrim($setActive,',');
				if($setActive !=''){ //Update old entry status
					$updateString	=	" Status	=	1, DateModified = '".$today."'";
					$condition		=	' fkCountriesId = '.$countryId.' AND fkStatesId in ('.$setActive.')';
					$usEntryResult		=	$coinsManageObj->updateTiltCountryStatus($updateString,$condition);
				}
			}
		}
	}
	else if($countryFlag){
		$updateString	=	' Status	=	2';
		$condition		=	' fkCountriesId = '.$usId.'';
		$coinsManageObj->updateTiltCountryStatus($updateString,$condition);
	}
	if(isset($countryIds)	&&	is_array($countryIds) &&	count($countryIds) > 0){ // Edit with more than one countries
		$existcountries	=	array();
		$activeCountry	=	$newCountry	=	$unsetCountries	=	'';
		$ids		=	implode(',',$countryIds);
		//**********************************
		if(isset($_POST['oldCountryIds'])	&&	$_POST['oldCountryIds']!=""){
			$oldCountryIds			=	$_POST['oldCountryIds'];
			$oldCountryIdsArray		=	explode(',',$_POST['oldCountryIds']);
			if(isset($oldCountryIdsArray)	&&	is_array($oldCountryIdsArray)	&& count($oldCountryIdsArray) > 0){
				foreach($oldCountryIdsArray as $oldCountryId){
					if($oldCountryId != $usId &&	$oldCountryId !=0){
						if(!in_array($oldCountryId,$countryIds))
							$unsetCountries	.=	$oldCountryId.',';
						else 
							$activeIds[]	=	$oldCountryId;
					}
				}
			}
		}
		//Unset Missed State from previous
		$unsetCountries	=	rtrim($unsetCountries,',');
		if($unsetCountries !=''){
			$updateString	=	' Status	=	2';
			$condition		=	' fkCountriesId  in ('.$unsetCountries.')';
			$coinsManageObj->updateTiltCountryStatus($updateString,$condition);
		}
		
		//**********************************
		// Check country entry exist or not
		$fields			=	'*';
		$condition		=	'fkCountriesId IN ('.$ids.') ';
		$countryResult		=	$coinsManageObj->checkTiltCountryEntry($fields,$condition);
		if(isset($countryResult) && is_array($countryResult) && count($countryResult) > 0){
			foreach($countryResult as $key => $countryValue){
					$existcountries[]	=	$countryValue->fkCountriesId;
			}
		}
		foreach($countryIds as $ckey => $cValue){
			if(in_array($cValue,$existcountries)){
				$activeCountry		.=	$cValue.',';
			}
			else {
				$newCountry	.=	"(".$cValue.",0,1,'".$today."','".$today."'),";
			}
		}
		$newCountry	=	rtrim($newCountry,',');
		if($newCountry !=''){ //insert new entry
			$stateValues	=	' VALUES '.$newCountry;
			$coinsManageObj->insertTiltRestrictedCountries($stateValues);
		}
		$activeCountry	=	rtrim($activeCountry,',');
		if($activeCountry !=''){ //Update old entry status
			$updateString	=	" Status	=	1, DateModified = '".$today."'";
			$condition		=	' fkCountriesId in ('.$activeCountry.')';
			$usEntryResult	=	$coinsManageObj->updateTiltCountryStatus($updateString,$condition);
		}
	}
	else if($countryId != $usId)
	{
		$fields			=	'*';
		$condition		=	'fkCountriesId = '.$countryId.' ';
		$usEntryResult		=	$coinsManageObj->checkTiltCountryEntry($fields,$condition);
		
		if(isset($usEntryResult) && is_array($usEntryResult) && count($usEntryResult) > 0){ // check entry exist
			$updateString	=	" Status	=	1, DateModified = '".$today."'";
			$condition		=	'fkCountriesId = '.$countryId.' ';
			$usEntryResult		=	$coinsManageObj->updateTiltCountryStatus($updateString,$condition);
		}
		else { // new entry
			$newEntry	=	" VALUES (".$countryId.",0,1,'".$today."','".$today."')";
			$coinsManageObj->insertTiltRestrictedCountries($newEntry);
		}
	} 
	$_SESSION['notification_msg_code']	=	2;
	?>
	
	<script type="text/javascript">
		window.parent.location.href = 'TiltLocationRestriction';
	</script>	
<?php 	
}
//ManageRestriction
$fields			=	' id,Country';
$conditions		=	' Status = 1 ';
$countryList	=	$coinsManageObj->getCountryList($fields,$conditions);

$fields			=	' id,State';
$conditions		=	' Status = 1 AND fkCountriesId = '.$usId;
$stateList		=	$coinsManageObj->getStateList($fields,$conditions);

$fields			=	' fkCountriesId,fkStatesId ';
$condition		=	'  Status = 1 ';
$selected		=	$coinsManageObj->checkTiltCountryEntry($fields,$condition);
$selCountriesArray	=	$selectedArray	=	array();
$selectedIds	=	'';
$countriesId	=	'';
if(isset($selected) && is_array($selected) && count($selected) > 0){
	$usFlag	=	0;
	foreach($selected as $key1 => $selectedVal){
		if($selectedVal->fkCountriesId == $usId){
				$selectedArray[]	=	$selectedVal->fkStatesId;
				$selectedIds	   .=	$selectedVal->fkStatesId.',';
				$usFlag	=	1;
		}
		else if($selectedVal->fkCountriesId != 0){
			$countriesId		   .=	$selectedVal->fkCountriesId.',';
			$selCountriesArray[]	=	$selectedVal->fkCountriesId;
		}
	}
	if($usFlag){
			$countriesId		   .=	$usId.',';
			$selCountriesArray[]	=	$usId;
	}
	$selectedIds	=	rtrim($selectedIds,',');
	$countriesId	=	rtrim($countriesId,',');
}
?>
<body >
	<div class="box-header">
		<h2><i class="fa fa-plus-circle"></i>Tilt Location Restriction</h2>
	</div>
	<div class="clear">
	<form name="tilt_loc_restriction_form" id="tilt_loc_restriction_form" action="" onSubmit="return validateState();" method="post">
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" width="65%">
					<tr> <td height="10"> </td></tr>
					<tr><td colspan="6" align="center"><div class="<?php echo $class;  ?> w50"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span></div></td></tr>
					<tr>
						<td width="17%" height="50" align="left"  valign="top"><label>Country <span class="required_field">*</span></label></td>
						<td width="5%" align="center"  valign="top">:</td>
						<td width="" align="left"  height="40"  valign="top">
							<select name="country" id="country" class="input">
								<option value="">Select</option>
								<?php if(isset($countryList) && is_array($countryList) && count($countryList) > 0){
											foreach($countryList as $key => $value) { if($value->id == $usId){
											?>														
													<option value="<?php echo $value->id; ?>"><?php echo $value->Country; ?></option>
										<?php	}else
												if(!(in_array($value->id,$selCountriesArray))){
											?>		
												<option value="<?php echo $value->id; ?>"><?php echo $value->Country; ?></option>	
										<?php	}
											}
										}?>
							</select>
						</td>
					</tr>
					<tr> <td height="10"> </td></tr>
						<tr id="state_block" style="display:none">
							<td height="50" align="left"  valign="top"><label>State <span class="required_field">*</span></label></td>
							<td align="center"  valign="top">:</td>
							<td align="left"  height="40"  valign="top">
								<input type="hidden" value="<?php echo $selectedIds; ?>" name="oldStateIds">
								<select name="state[]" id="state" class="input" multiple  style="height:100px">
									<option value="">Select</option>
									<?php if(isset($stateList) && is_array($stateList) && count($stateList) > 0){
											foreach($stateList as $key => $stateValue) { ?>
													<option value="<?php echo $stateValue->id; ?>" <?php if(in_array($stateValue->id,$selectedArray)) echo  'selected';?>><?php echo $stateValue->State; ?></option>
										<?php	}
										}?>
								</select>
								<div id="state_empty" style="display:none;color:red">State is required</div>
							</td>
						</tr>
				</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td colspan="6" align="center">
					<input type="submit" class="submit_button" name="submit" id="submit" value="Add Country" title="Add Country" alt="Add Country">
				</td>
			</tr>	
			<tr><td height="10"></td></tr>				  
		</table>
	</form>	
</div>
<?php commonFooter(); ?>
<script type="text/javascript">
var usId	=	'<?php echo $usId; ?>';
$("#country ").change(function(e){
	if($(this).val()== usId){
		$("#state_block").show();
		$("#state_empty").hide();
	}
	else {
		$("#state_empty").hide();
		$("#state_block").hide();
	}
});

function validateState(){
	var countryId = $('#country > option:selected').val();
	if(countryId){
		if(countryId==usId){
			var options = $('#state > option:selected');
			console.log($('#state > option:selected').val());
			 if(options.length == 0	|| $('#state > option:selected').val() ==''){
				 $("#state_empty").show();
				 return false;
			 }
		}
		return true;
	}
	return false;

}
</script>
</html>
