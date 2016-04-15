<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
require_once("includes/phmagick.php");
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
}
$usId			=	USID;
$tourRules		=	"Test tournament template";
$tournamentRules = $termsAndConditions = $gameRules =  "";
$fields			=	' * ';
$where			= 	' id = 1 ';
$tempDetail		=	$adminLoginObj->getDistance($fields,$where);
if(isset($tempDetail) && is_array($tempDetail) && count($tempDetail)>0){
	$tourRules			=	$tempDetail[0]->TournamentRules;
	$tournamentRules	=	$tempDetail[0]->TournamentRules;
	$termsAndConditions	=	$tempDetail[0]->TermsAndConditions;
	$gameRules			=	$tempDetail[0]->GameRules;
}

if(isset($_POST['submit']) && $_POST['submit'] != ''){
	//BEGIN : 
	if(isset($_POST['tournamentId'])	&&	$_POST['tournamentId'] !=''){			
		$tournamentId	=	$_POST['tournamentId'];
		$today			=	date('Y-m-d H:i:s');
		
		if(isset($_POST['tournamentRule']) && $_POST['tournamentRule']!='') {				
				$updatestring = "GftRules='".addslashes($_POST['tournamentRule'])."'";
				$condition    =  'id='.$tournamentId;		
				$tournamentObj->updateTournamentDetails($updatestring , $condition);
		}
		if(isset($_POST['country'])	&&	is_array($_POST['country'])	&& count($_POST['country']) > 0 
			&&	isset($_POST['tournamentRules'])	&&	is_array($_POST['tournamentRules'])	&& count($_POST['tournamentRules']) > 0	){
			$countryIds		=	$_POST['country'];
			$rulesList		=	$_POST['tournamentRules'];
			$stateFlag		=	0;
			$countryEntry	=	$usEntryPair	=	$usStateIds	=	$otherCountryIds	=	'';
			$countryEntries	= $usEntries = $existStateArrays = $existArrays = array();
			$unsetCountryArray = $unsetStateArray = $tempArray = array();
			
			if(isset($_GET['editId']) && $_GET['editId'] != '' ){						// unset active country list for the edit mode
				$updateString	=	" Status=2 ,DateModified='".$today."' ";
				$condition	=	" fkTournamentsId=".$tournamentId."  ";
				$tournamentObj->updateTournamentRules($updateString,$condition);
			}
			
			if(isset($_POST['state'])	&&	is_array($_POST['state'])	&& count($_POST['state']) > 0){
				$stateFlag	=	1;
				$stateIds	=	$_POST['state'];
			}
			//deleted ids
			$unsetCountries	=	$unsetStates	=	'';
			if(isset($_POST['countryDeletedIds'])	&&	$_POST['countryDeletedIds'] != '' ){  // keep tracking deleted ids  (country)
				$tempArray	=	explode(',',$_POST['countryDeletedIds']);
				if(isset($tempArray)	&&	is_array($tempArray)	&& count($tempArray) > 0){
					foreach($tempArray as $delCountryId){
						if(!in_array($delCountryId,$countryIds))
							$unsetCountries	.=	$delCountryId.',';
					}
				}
			}
			if(isset($_POST['stateDeletedIds'])	&&	$_POST['stateDeletedIds'] != '' ){ // keep tracking deleted ids  (US state)
				$tempArray	=	explode(',',$_POST['stateDeletedIds']);
				if(isset($tempArray)	&&	is_array($tempArray)	&& count($tempArray) > 0){
					foreach($tempArray as $delStateId){
						if(!in_array($delStateId,$stateIds))
							$unsetStates	.=	$delStateId.',';
					}
				}
			}
			
			$unsetCountries	=	rtrim($unsetCountries,',');
			if($unsetCountries !=''){
				$updateString	=	" Status=2 ,DateModified='".$today."' ";
				$condition	=	" fkTournamentsId=".$tournamentId."  AND fkCountriesId in (".$unsetCountries.")";
				$tournamentObj->updateTournamentRules($updateString,$condition);
			}
			$unsetStates	=	rtrim($unsetStates,',');
			if($unsetStates !=''){
				$updateString	=	" Status=2 ,DateModified='".$today."' ";
				$condition	=	" fkTournamentsId=".$tournamentId."  AND fkCountriesId =".$usId." AND fkStatesId in (".$unsetStates.")";
				$tournamentObj->updateTournamentRules($updateString,$condition);
			}
			$countryState	=	array();
			foreach($countryIds as $key =>$id){
				if($id !=''){
					if($id==$usId){
						if(!array_key_exists($stateIds[$key],$usEntries)){
							$countryState[]	=	array($id,$stateIds[$key]);
							if($stateFlag && isset($stateIds[$key])	&&	$stateIds[$key] !=''){
								$tour_rule	=	$tour_condition	=	'';
								if(isset($rulesList[$key]))
									$tour_rule	=	$rulesList[$key];
								$usEntries[$stateIds[$key]]	=	array('stateId'=>$stateIds[$key],'rule'=>addslashes($tour_rule));
								$usEntryPair		.=	$stateIds[$key].',';
							}
						}
					}
					else {
						if(!array_key_exists($id,$countryEntries)){
							$countryState[]	=	array($id,$stateIds[$key]);
							$countryEntry	.=	$id.',';
							$tour_rule	=	$tour_condition	=	'';
							if(isset($rulesList[$key]))
								$tour_rule	=	$rulesList[$key];
							$countryEntries[$id]	=	array('rule'=>addslashes($tour_rule));
						}
					}
				}
			}
			$countryEntry	=	rtrim($countryEntry,',');
			if($countryEntry){	
				$fields			=	'*';
				$condition		=	'fkCountriesId IN ('.$countryEntry.') AND fkTournamentsId ='.$tournamentId.'  ';
				$countryEntryResult		=	$tournamentObj->checkRulesEntry($fields,$condition);
				if(isset($countryEntryResult) && is_array($countryEntryResult) && count($countryEntryResult) > 0){
					foreach($countryEntryResult as $existKey => $existCountry){
						$existArrays[]	=	$existCountry->fkCountriesId;
					}
				}
			}
			$usEntryPair	=	rtrim($usEntryPair,',');
			if($usEntryPair !=''){
				$fields			=	'*';
				$condition		=	'fkStatesId IN ('.$usEntryPair.') AND fkTournamentsId ='.$tournamentId.'  AND fkCountriesId = '.$usId.' ';//AND fkBrandsId = 0
				$usEntryResult		=	$tournamentObj->checkRulesEntry($fields,$condition);
				if(isset($usEntryResult) && is_array($usEntryResult) && count($usEntryResult) > 0){
					foreach($usEntryResult as $existStateKey => $existState){
						$existStateArrays[]	=	$existState->fkStatesId;
					}
				}
			}
			$stateUpdateArray	=	array();
			$stateValues		= 	$countryValues	=	'';
			
			
			
			if(isset($countryState)	&&	is_array($countryState)	&& count($countryState) >0 ){
				foreach($countryState as $key =>$entry){
					if($entry[0] !=''){
						if($entry[0]==$usId){
							if(in_array($entry[1],$existStateArrays)	&&	isset($usEntries[$entry[1]])){
								$updateString	=	" GftRules='".$usEntries[$entry[1]]['rule']."',DateModified='".$today."',Status=1 ";
								$condition	=	" fkTournamentsId=".$tournamentId."  AND fkCountriesId=".$entry[0]." AND fkStatesId=".$usEntries[$entry[1]]['stateId']." ";
								$tournamentObj->updateTournamentRules($updateString,$condition);
							}
							else{	// new entry to state
								if(isset($usEntries[$entry[1]])){
									$stateValues		.=	"(".$tournamentId.",0,".$entry[0].",".$usEntries[$entry[1]]['stateId'].",'".addslashes($gameRules)."','".addslashes($termsAndConditions)."','".$usEntries[$entry[1]]['rule']."','','".$today."','".$today."',1),";
								}
							}
							
						}
						else {
						$id	=	$entry[0];
							if(in_array($id,$existArrays)	&&	isset($countryEntries[$id])){	//update country rules
								$updateString	=	" GftRules='".$countryEntries[$id]['rule']."',DateModified='".$today."',Status=1 ";
								$condition	=	" fkTournamentsId=".$tournamentId." AND  fkCountriesId=".$id." ";
								$tournamentObj->updateTournamentRules($updateString,$condition);
							}
							else { // new entry to country
								if(isset($countryEntries[$id])){
									$countryValues		.=	"(".$tournamentId.",0,".$id.",0,'".addslashes($gameRules)."','".addslashes($termsAndConditions)."','".$countryEntries[$id]['rule']."','','".$today."','".$today."',1),";
								}
							}
						}
					}
				}
			}
			$stateValues	=	rtrim($stateValues,',');
			$countryValues	=	rtrim($countryValues,',');
			
			if(isset($_POST['tournamentRule']) && $_POST['tournamentRule']!='') {
				$post_values['tournamentgft'] =  $_POST['tournamentRule'];
			}
			
			if($stateValues !=''){
				$stateValues	=	' VALUES '.rtrim($stateValues,',');
				$tournamentObj->insertRules($stateValues);
			}
			if($countryValues !=''){
				$countryValues	=	' VALUES '.rtrim($countryValues,',');
				$tournamentObj->insertRules($countryValues);
			}
			$_SESSION['notification_msg_code']	=	1;
			if(isset($_POST['editId']))
				$_SESSION['notification_msg_code']	=	2;
			header("location:TournamentRulesList?cs=1");
			die();
		}else { //invalid details
		}
	}
	else { //invalid details
	}
	//END :
}
//Get country,state,template details
$field_focus	=	'brand';
$class			=	$ExistCondition		=	$location	=	'';
$gametype		=	0;
$entryfee_flag	=	$startDateFlag	=	$endDateFlag	=	$dateFlag	=	0;


if(isset($_GET['editId'])	&&	$_GET['editId'] !=''){
	$tournamentId	=	$_GET['editId'];
	$fields    = " id,TournamentName";
	$condition = " id=".$tournamentId." AND Status !=3 ";
	$tournamentDetail	=	$tournamentObj->selectTournament($fields,$condition);
	$tournament	=	$tournamentDetail[0]->TournamentName;
	
	
	$fields    = " tr.id,t.id as tournamentId,tr.fkTournamentsId,tr.fkCountriesId,tr.fkStatesId,tr.TournamentRules,tr.GftRules,tr.DateCreated,t.TournamentName,t.DateCreated";
	$condition = " tr.fkTournamentsId = ".$tournamentId." AND tr.Status = 1  AND t.Status !=3 ";
	$tournamentRulesDetail	=	$tournamentObj->selectTournamentRule($fields,$condition);	
	$fields  = "GftRules ";
	$condition = "id=".$tournamentId;	
	$tournamentDetail    = $tournamentObj->selectTournament($fields,$condition);
	foreach($tournamentDetail	as $rulesKey=>$rulesDetails){
		$tournmentrule = $rulesDetails->GftRules;								
	}	
	
}
else {
	$fields			= " id ,TournamentName, GftRules";
	$condition      = " AND TournamentName !='' and TournamentStatus !=3 AND Status !=3";
	$tournamentList  = $tournamentObj->tournamentList($fields,$condition);
	$tournamentArray	=	array();
	if(isset($tournamentList) && is_array($tournamentList) && count($tournamentList) > 0 ) {
		$tournamentArray	=	json_encode($tournamentList);
	}
}
$fields			=	' id,Country';
$conditions		=	' Status = 1 ';
$countryList	=	$tournamentObj->getCountryList($fields,$conditions);
if(!empty($countryList))	{
	foreach($countryList as $key=>$value)	{
		$countryArray[$value->id]	=	$value->Country;
	}
	asort ($countryArray);
}

$fields			=	' id,State';
$conditions		=	' Status = 1 AND fkCountriesId = 236 ';
$stateList	=	$tournamentObj->getStateList($fields,$conditions);
if(!empty($stateList))	{
	foreach($stateList as $key=>$value)	{
		$usStateArray[$value->id]	=	$value->State;
	}
}

$tournmentrules = '';
	if(isset($tournamentRulesDetail)	&& is_array($tournamentRulesDetail)	&&	count($tournamentRulesDetail)>0) { 
		foreach($tournamentRulesDetail	as $rulesKey=>$rulesDetails){
			$tournmentrules .= $rulesDetails->TournamentRules;								
		}			
	}	

if(isset($_GET['back']) && $_GET['back'] == 1 && isset($_GET['editId']) && $_GET['editId'] != ''){
	$href_page = "TournamentRulesDetail?viewId=".$_GET['editId'];
}
?>
<body>
	<?php top_header(); ?>	
	<div class="box-header">
		<h2><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo "<i class='fa fa-edit'></i> Edit "; else echo "<i class='fa fa-plus-circle'></i> Add ";?>Tournament Rules</h2>
	</div>
	 <div style="clear:both">
	<form name="add_rules_form" id="add_rules_form" action="" method="post" onsubmit="return saveEditor();">
	<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) { ?>
		<input type="Hidden" name="editId" id="editId" value="1">
	<?php } ?>
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="98%">
			<tr>
				<td align="center">
					<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">
						
						<tr><td colspan="6" align="center" valign="top" class="msg_height	">
							<div class="<?php echo $class;  ?> w50">
								<span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span>
							</div>
						</td></tr>
						<tr>
							<td width="15%" height="50" align="left"  valign="top"><label>Tournament <span class="required_field">*</span></label></td>
							<td width="3%" align="center"  valign="top">:</td>
							<td width="82%" align="left"  height="40"  valign="top">
							<?php if(isset($_GET['editId'])	&&	$_GET['editId'] !=''){ 
								echo $tournament; ?>
								<input type="hidden" id="tournamentId" name="tournamentId" value="<?php echo $_GET['editId']; ?>">
							<?php } else { ?>
								<input type="text" id="searchTournament" name="searchTournament">
								<input type="hidden" id="tournamentId" name="tournamentId">
							<?php } ?>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="40" id="all_rules_title">
							<td valign="top" align="left"><label>Countries</label><td>
							<td align="left"  valign="top"><label>Tournament Rules</label></td>						
							<td>&nbsp;
								<input type='hidden' value="<?php echo $tourRules; ?>" name='tour_rules_template' id='tour_rules_template'>							
							</td>
						</tr>
						<tr id="all_rules_template" >
							<td colspan="4" align="center" id="inputParam1All">
								<table cellpadding="0" cellspacing="7" id="inputParam1" border="0" width="100%" align="center">
									<tr>
										<td  width="20%" valign="top" align="left">All</td>					
										<td valign="top" class="terms_td col_2" id="terms_td_0" align="left">
											<textarea id="terms" class="tour_rule textarea-full"  rows="4" cols="45" tabindex="11" name="tournamentRule"><?php if(isset($tournmentrule) && $tournmentrule!='')  echo $tournmentrule; else echo $tourRules; ?></textarea>
										</td>									
									</tr>								
								</table>
							</td>								
						</tr>					
						<tr id="all_rules_countries">
						<?php $index = 0;?>
							<td colspan="4" align="center" id="inputParamCountry">
								<table cellpadding="0" cellspacing="7" id="inputParam" border="0" width="100%" align="center">
						<?php if(isset($tournamentRulesDetail)	&& is_array($tournamentRulesDetail)	&&	count($tournamentRulesDetail)>0) { 
								$index	=	count($tournamentRulesDetail);
								$oldCountryIds	=	$oldStateIds	=	'';
								foreach($tournamentRulesDetail	as $rulesKey=>$rulesDetails){
									$usFlag	=	1;
									if($rulesDetails->fkCountriesId == $usId){ $usFlag = 0;	$oldStateIds	=	$rulesDetails->fkStatesId; }
									else 
										$oldCountryIds	.=	$rulesDetails->fkCountriesId.',';
						?>
									<tr align="center" class="clone" clone="<?php echo $rulesKey;?>">
										<td align="left" valign="top" width="20%">
											<div class="fleft">
												<a href="javascript:void(0)" onclick="delCountry(this)"><i class="fa fa-lg  fa-minus-circle text-red"></i></a>
												<span id="new_0" class="addNewRule" style="display:none"><a href="javascript:void(0)" onclick="addCountry(this)"><i class="fa fa-lg text-green fa-plus-circle"></i></a></span>&nbsp;&nbsp;
											</div>
											<div class="fleft">
												<select name="country[]" tabindex="10" style="width:130px;" class="country" id="country_<?php echo $rulesKey; ?>" onchange="countryShow(<?php echo $rulesKey;?>);">
													<option value="">Select</option>
													<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
														foreach($countryArray as $countryId => $country) {  ?>
														<option value="<?php echo $countryId; ?>" <?php   if($countryId==$rulesDetails->fkCountriesId) { echo 'Selected';  } ?>><?php echo $country; ?></option>
													<?php 	} ?>
												</select>
												<br>
												<span id='field_name_empty' class="error_empty"></span>
												<span class="slabel" id="state_label_<?php echo $rulesKey; ?>" <?php if($usFlag) { ?>style="display:none;" <?php } ?>>State</span>
												<br>
												<select name="state[]" tabindex="10" class="state" <?php if($usFlag) { ?>style="display:none;width:130px;" <?php } ?>style="width:130px;" id="state_<?php echo $rulesKey; ?>">
													<option value="">Select</option>
													<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
														foreach($usStateArray as $stateId => $state) {  ?>
														<option value="<?php echo $stateId; ?>" <?php   if($stateId==$rulesDetails->fkStatesId) echo 'Selected';  ?>><?php echo $state; ?></option>
													<?php 	} ?>
												</select>
												<span id='sample_data_empty' class="error_empty"></span>
											</div>
										</td>
										<td valign="top" class="terms_td col_2" id="terms_td_<?php echo $rulesKey; ?>" align="left">
											<textarea id="terms_<?php echo $rulesKey; ?>" class="tour_rules textarea-full"  rows="4" cols="45" tabindex="11" name="tournamentRules[]"><?php echo $rulesDetails->GftRules; ?></textarea>
										</td>								
									</tr>
						<?php 	} // end foreach
							  } //end if tournament details validation	?>
									<tr align="center" class="clone" clone="<?php echo $index;?>">
										<td align="left" valign="top" width="20%">
											<div class="fleft">
												<a href="javascript:void(0)" onclick="delCountry(this)"><i class="fa fa-lg  text-red  fa-minus-circle"></i></a>
												<span id="new_0" class="addNewRule" ><a href="javascript:void(0)" onclick="addCountry(this)"><i class="fa fa-lg text-green fa-plus-circle"></i></a></span>&nbsp;&nbsp;
											</div>
											<div class="fleft">
												<select name="country[]" tabindex="10" style="width:130px;" class="country" id="country_<?php echo $index; ?>" onchange="countryShow(<?php echo $index;?>);">
													<option value="">Select</option>
													<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
														foreach($countryArray as $countryId => $country) {  ?>
														<option value="<?php echo $countryId; ?>"><?php echo $country; ?></option>
													<?php 	} ?>
												</select>
												<br>
												<span id='field_name_empty' class="error_empty"></span>
												<span class="slabel" id="state_label_<?php echo $index; ?>" style="display:none;">State</span>
												<br>
												<select name="state[]" tabindex="10" class="state" style="display:none; width:130px;" id="state_<?php echo $index; ?>">
													<option value="">Select</option>
													<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
														foreach($usStateArray as $stateId => $state) {  ?>
														<option value="<?php echo $stateId; ?>"><?php echo $state; ?></option>
													<?php 	} ?>
												</select>
												<span id='sample_data_empty' class="error_empty"></span>
											</div>
										</td>
										<td valign="top" class="terms_td col_2" id="terms_td_<?php echo $index; ?>" align="left">
											<textarea id="terms_<?php echo $index; ?>" class="tour_rules textarea-full"  rows="4" cols="45" tabindex="11" name="tournamentRules[]"><?php echo $tourRules; ?></textarea>
										</td>								
									</tr>
									<input id="countryDeletedIds" name="countryDeletedIds" type="hidden" value="">
									<input id="stateDeletedIds" name="stateDeletedIds" type="hidden" value="">
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr >
				<td colspan="6" align="center">
				<div id="submit_buttons">
				<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
					<input type="submit" class="submit_button" name="submit" id="submit" value="Save" title="Save" alt="Save">
				<?php } else { ?>
				<input type="submit" class="submit_button" name="submit" id="submit" value="Add" onClick="return submitForm();" title="Add" alt="Add">
				<?php } ?>
				<a href="<?php if(isset($href_page) && $href_page != '' ) echo $href_page; else echo 'TournamentRulesList?cs=1';?>"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
				</div>
			</td>
			</tr>
			<?php if(!isset($_GET['editId'])){ ?>
			<tr >
				<td id="back_button" colspan="2" align="left" style="padding-left:27%; display:none">
					<div >
					<a href="<?php if(isset($href_page) && $href_page != '' ) echo $href_page; else echo 'TournamentRulesList?cs=1';?>"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
					</div>
				</td>
				<td colspan="4" align="center">&nbsp;</td>
			</tr>	
			<?php } ?>
							  
		</table>
	</form>	
	</div>
<?php commonFooter(); ?>
<script type="text/javascript">
<?php if(!isset($_GET['editId'])){ ?>

$("#searchTournament").tokenInput(<?php echo $tournamentArray;?>,{
	tokenLimit : 1,
	animateDropdown:true,
	propertyToSearch: "TournamentName",
	preventDuplicates: true,
	onAdd: function (item) {
	$("#tournamentId").val(item.id);
		retainValues(item.id);
		$("#back_button").hide();
		return item;
		},
	onDelete: function (item) {
		$("#tournamentId").val('');
		$("#all_rules_title").hide();
		$("#all_rules_template").hide();
		$("#all_rules_countries").hide();
		$("#submit_buttons").hide();
		$("#back_button").show();
		},
	onResult:function (item)	{
		return item;
	},
	noResultsText: "No result"
});
$("#all_rules_title").hide();
$("#all_rules_template").hide();
$("#all_rules_countries").hide();
$("#submit_buttons").hide();
$("#back_button").show();
<?php } else { ?>
testInit();	 
<?php } ?>

function testInit(){
 tinymce.init({
	height 	: "300",
	width	: "350",
	mode : "specific_textareas",
	selector: "textarea", statusbar: false, menubar:false,
	plugins	: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
				],
	toolbar: "insertfile undo redo styleselect | bold italic  alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link "
	});
}
function submitForm(){
	var empty = 0;
	tinyMCE.triggerSave();
	var flag	=	0;
	if($("#tournamentId").val() !='') {
		$("#inputParam tr").each(function() {
			 var tourRules 	=	$(this).find("textarea.tour_rules").eq(0).val();
			if(empty !=1 ){
				if($(this).find("select.country")){	
					var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
					if(cvalue != '' &&	cvalue=='United States'){
						if($(this).find("select.state")){	
							var svalue	=	$(this).find("select.state option:selected").eq(0).text();
							if(svalue!='' && svalue=='Select'){
								empty = 3;
							}
						}
					}
					else if(cvalue != '' &&	cvalue=='Select'){
						empty = 2;
					}
				}
				if(tourRules != '' && empty != 3 && empty != 2 ){
					empty = 1;
					flag=1;
				}
			}
		});
		if(empty==1){
			return true;
		}
		else if(empty == 2){
			alert('Please select country');
			return false;
		}
		else if(empty == 2){
			alert('Please select state');
			return false;
		}
		else{
			alert('Atleast one record needed');
			return false;
		}
	}
}

function saveEditor(){
	tinyMCE.triggerSave();
}
</script>
</html>
