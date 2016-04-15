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

//Get country,state,template details
$field_focus	=	'brand';
$class			=	$ExistCondition		=	$location	=	'';
$gametype		=	0;
$entryfee_flag	=	$startDateFlag	=	$endDateFlag	=	$dateFlag	=	0;


if(isset($_GET['viewId'])	&&	$_GET['viewId'] !=''){
	$tournamentId	=	$_GET['viewId'];
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
	
?>
<body>
	<?php top_header(); ?>	
	<div class="box-header">
		<h2><i class="fa fa-search"></i> <h2>View Tournament Rules</h2><h2>
	</div>
	 <div style="clear:both">
	<form name="add_rules_form" id="add_rules_form" action="" method="post" onsubmit="return saveEditor();">
	<?php if(isset($_GET['viewId']) && $_GET['viewId'] != '' ) { ?>
		<input type="Hidden" name="viewId" id="viewId" value="1">
	<?php } ?>
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="98%">
			<tr><td align="center">
				<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">
					<tr><td class="msg_height"></td></tr>
					<tr>
						<td width="15%" height="50" align="left"  valign="top"><label>Tournament</label></td>
						<td align="center" valign="top" width="3%">:</td>
						<td width="" align="left"  height="40"  valign="top" width="82%">
						<?php if(isset($_GET['viewId'])	&&	$_GET['viewId'] !=''){ 
							echo $tournament; ?>
						<?php } ?>
						</td>
					</tr>
					<tr id="all_rules_title">
						<td height="40" valign="top" align="left"><label>Countries</label></td>
						<td>&nbsp;</td>
						<td align="left"  valign="top"><label>Tournament Rules</label></td>	
					</tr>
					<tr id="all_rules_template" >
						<td colspan="3" align="center" id="inputParam1All">
							<table cellpadding="0" cellspacing="7" id="inputParam1" border="0" width="100%" align="center">
								<tr>
									<td  width="18%" valign="top" align="left">All</td>		
									<td width="2%">&nbsp;</td>									
									<td valign="top" class="terms_td col_2" id="terms_td_0" align="left">
										<?php if(isset($tournmentrule) && $tournmentrule!='')  echo $tournmentrule; else echo $tourRules; ?>
									</td>									
								</tr>								
							</table>
						</td>								
					</tr>					
					<tr id="all_rules_countries">
					<?php $index = 0;?>
					<td colspan="3" align="center" id="inputParamCountry">
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
								<td align="left" valign="top" width="18%">
									<div class="fleft">
											<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
												foreach($countryArray as $countryId => $country) {  ?>
												<?php if($countryId==$rulesDetails->fkCountriesId) echo $country; ?>
											<?php 	} ?>
											<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
												foreach($usStateArray as $stateId => $state) {  ?>
												<?php if($stateId==$rulesDetails->fkStatesId) echo " - ".$state; ?>
											<?php 	} ?>
									</div>
								</td>
								<td width="2%">&nbsp;</td>	
								<td valign="top" class="terms_td col_2" id="terms_td_<?php echo $rulesKey; ?>" align="left">
									<?php echo $rulesDetails->GftRules; ?>
								</td>								
							</tr>
				<?php 	} // end foreach
					  } //end if tournament details validation	?>
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
				<?php if(isset($_GET['viewId']) && $_GET['viewId'] != ''){ ?>
					<a href="TournamentRulesManage?editId=<?php echo $_GET['viewId']; ?>&back=1" title="Edit" alt="Edit" class="submit_button">Edit</a>
				<?php } ?>
				<a href="TournamentRulesList?cs=1"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
				</div>
			</td>
			</tr>
							  
		</table>
	</form>	
	</div>
<?php commonFooter(); ?>

</html>
