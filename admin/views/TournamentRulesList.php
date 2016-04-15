<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$rulesManageObj   =   new TournamentController();
$display   =   'none';
$class  =  $msg    = $cover_path = $searchCountryIds	=	'';
$updateStatus	=	1;
$usId	=	USID;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_rule_tournament']);
	unset($_SESSION['mgc_sess_rule_country']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['tournament']))
		$_SESSION['mgc_sess_rule_tournament'] 	=	trim($_POST['tournament']);
	if(isset($_POST['country']))
		$_SESSION['mgc_sess_rule_country'] 	=	trim($_POST['country']);
}
if(isset($_SESSION['mgc_sess_rule_country'])	&&	$_SESSION['mgc_sess_rule_country']	!=''){
	$fields		= 	' id ';
	$condition	=	" Country LIKE '%".$_SESSION['mgc_sess_rule_country']."%' AND Status = 1 ";
	$countryList	=	$rulesManageObj->getCountryList($fields,$condition);
	if(isset($countryList) && is_array($countryList) && count($countryList) > 0){ 
		foreach($countryList as $countryKey=>$countryValue){
			$searchCountryIds	.=	$countryValue->id.',';
		}
	}
	else $searchCountryIds	=	'noresult';
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " tr.id,t.TournamentName,tr.fkTournamentsId,tr.fkCountriesId,tr.fkStatesId,tr.TournamentRules,tr.DateCreated,t.TournamentName,t.DateCreated";
$condition = " tr.Status = 1 AND t.Status !=3 AND tr.TournamentRules != '' ";
if($searchCountryIds	!=	'noresult') {
	$searchCountryIds	=	rtrim($searchCountryIds,',');
	if(	$searchCountryIds !=	'')
		$condition      .=	" AND ( tr.fkCountriesId IN(".$searchCountryIds.") )";
	$rulesListResult  = $rulesManageObj->getRulesList($fields,$condition);
	$tot_rec 		 = $rulesManageObj->getTotalRecordCount();
	if($tot_rec!=0 && !is_array($rulesListResult)) {
		$_SESSION['curpage'] = 1;
		$rulesListResult  = $rulesManageObj->getRulesList($fields,$condition);
	}
}

$stateIds	=	$tournamentIds	=	'';
if(isset($rulesListResult) && is_array($rulesListResult) && count($rulesListResult) > 0){
	foreach($rulesListResult as $key => $value){
		$tournamentIds	.=	$value->fkTournamentsId.',';
	}
}
$tournamentIds	=	rtrim($tournamentIds,',');
$rulesListArray	=	array();
if($tournamentIds!=''){
	$fields    = " tr.id,tr.fkTournamentsId,tr.fkCountriesId,tr.fkStatesId,tr.TournamentRules,tr.DateCreated,c.Country ";
	$condition = " tr.fkTournamentsId IN($tournamentIds) AND tr.Status = 1 AND c.Status = 1 ";
	if(	$searchCountryIds !=	''	&&	$searchCountryIds	!=	'noresult')
		$condition      .=	" AND ( tr.fkCountriesId IN(".$searchCountryIds.") )";
	$rulesList  = $rulesManageObj->getRules($fields,$condition);
	if(isset($rulesList) && is_array($rulesList) && count($rulesList) > 0){
		foreach($rulesList as $key => $value){
			$rulesListArray[$value->fkTournamentsId][]	=	$value;
			if($value->fkCountriesId == $usId){
				$stateIds	.=	$value->fkStatesId.',';
			}
		}
	}
}
$stateIds	=	rtrim($stateIds,',');
$stateArray	=	array();
if($stateIds !=''){
	$fields    = " * ";
	$condition = " id IN(".$stateIds.") AND fkCountriesId = ".$usId." AND Status = 1 ";
	$stateResult  = $rulesManageObj->getStateList($fields,$condition);
	if(isset($stateResult) && is_array($stateResult) && count($stateResult) > 0){
		foreach($stateResult as $stateKey => $stateValue){
			$stateArray[$stateValue->id]	=	$stateValue->State;
		}
	}
}
?>
<body>
<?php top_header(); ?>
	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i>Tournament Rules List</h2>
		<span class="fright"><a href="TournamentRulesManage" title="Add Tournament Rules"><i class="fa fa-plus-circle"></i> Add Tournament Rules</a></span>
	</div>
    <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
		
		<tr><td class="filter_form" >
			<form name="search_category" action="TournamentRulesList" method="post">
			<table align="center" cellpadding="6" cellspacing="0" border="0"width="98%">									       
				<tr><td></td></tr>
				<tr>													
					<td width="5%" ><label>Tournament</label></td>
					<td width="1%" align="center">:</td>
					<td align="left"  width="15%">
						<input type="text" class="input" name="tournament" id="tournament"  value="<?php  if(isset($_SESSION['mgc_sess_rule_tournament']) && $_SESSION['mgc_sess_rule_tournament'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_rule_tournament']);  ?>" >
					</td>
					<td  align="left" width="5%"><label>Country</label></td>
					<td width="1%" align="center">:</td>
					<td  width="15%">
						<input type="text" class="input" name="country" id="country"  value="<?php  if(isset($_SESSION['mgc_sess_rule_country']) && $_SESSION['mgc_sess_rule_country'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_rule_country']);  ?>" >
					</td>
				</tr>
				<tr><td align="center" colspan="6" style="padding-top:20px"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
				<tr><td></td></tr>
			 </table>
			 </form>
		</td></tr>
		<tr><td height="20"></td></tr>
		<tr><td>
			<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
				<tr>
					<?php if(isset($rulesListResult) && is_array($rulesListResult) && count($rulesListResult) > 0){ ?>
					<td align="left" width="20%">No. of Tournament Rule(s) &nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
					<?php } ?>
					<td align="center">
							<?php if(isset($rulesListResult) && is_array($rulesListResult) && count($rulesListResult) > 0 ) {
								pagingControlLatest($tot_rec,'TournamentRulesList'); ?>
							<?php }?>
					</td>
				</tr>
			</table>
		</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td colspan= '2' align="center">
			<?php displayNotification('Tournament Rules'); ?>
		</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td>
			<div class="tbl_scroll">
			
			  <form action="TournamentRulesList" class="l_form" name="TournamentRulesListForm" id="TournamentRulesListForm"  method="post"> 
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr align="left">
						<th align="center" width="3%" class="text-center">#</th>												
						<th width="30%"><?php echo SortColumn('TournamentName','Tournament'); ?></th>
						<th width="30%">Country/State</th>
						<th width="10%"><?php echo SortColumn('tr.DateCreated','Assigned Date'); ?></th>
					</tr>
					
			<?php 	if(isset($rulesListResult) && is_array($rulesListResult) && count($rulesListResult) > 0){
						foreach($rulesListResult as $key => $value){ 
							if(isset($rulesListArray[$value->fkTournamentsId]) && is_array($rulesListArray[$value->fkTournamentsId]) && count($rulesListArray[$value->fkTournamentsId])>0){
								$rowSpan	=	0;
								$rowSpan	=	count($rulesListArray[$value->fkTournamentsId]);
								if($rowSpan!=1){
			?>
									<tr class="test_id_<?php echo $value->id;?>"	>
										<td style="background-color:#fff" rowspan="<?php echo $rowSpan+1; ?>" valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
										<td style="background-color:#fff" rowspan="<?php echo $rowSpan+1; ?>" valign="top" align="center" >
											<p align="left" ><?php if(isset($value->TournamentName) && $value->TournamentName != '' && 0)	echo '<a class="recordView" href="TournamentDetail?viewId='.$value->fkTournamentsId.'&back=TournamentRulesList">'.trim($value->TournamentName).'</a>'; else echo $value->TournamentName;?></p>
											<div class="userAction" style="display:block; padding-top:8px; min-height:20px;" id="userAction">
												<a id="edit_<?php echo $value->id;?>" href="TournamentRulesManage?editId=<?php echo $value->fkTournamentsId; ?>" title="Edit Rules" alt="" class="editUse"><i class="fa fa-edit fa-lg"></i></a>
												<a id="view_<?php echo $value->id;?>" href="TournamentRulesDetail?viewId=<?php echo $value->fkTournamentsId; ?>" title="View Rules" alt="" class="viewUser"><i class="fa fa-search-plus fa-lg"></i></a>
											</div>
										</td>
									</tr>
			<?php 					foreach($rulesListArray[$value->fkTournamentsId] as $countryKey=>$countryDetails) { ?>
										<tr class="test_id_<?php echo $value->id;?>" >
										<td >
			<?php 						if($countryDetails->fkCountriesId == $usId	&& isset($stateArray[$countryDetails->fkStatesId]))
												echo $countryDetails->Country.' - '.$stateArray[$countryDetails->fkStatesId];
											else echo $countryDetails->Country;?>
										</td>	
										<td valign="top"><?php if(isset($countryDetails->DateCreated) && $countryDetails->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($countryDetails->DateCreated)); }else echo '-';?></td>
										</tr>
			<?php 					} //End of for each ?>	
			<?php 				}  //End of row span more than one record 
								else { ?>
			<?php 					foreach($rulesListArray[$value->fkTournamentsId] as $countryKey=>$countryDetails) { ?>
										<tr class="test_id_<?php echo $value->id;?>" >
										<td style="background-color:#fff" valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
										<td style="background-color:#fff" valign="top" align="center" >
											<p align="left" ><?php if(isset($value->TournamentName) && $value->TournamentName != '' && 0)	echo '<a class="recordView" href="TournamentDetail?viewId='.$value->fkTournamentsId.'&back=TournamentRulesList">'.trim($value->TournamentName).'</a>'; else echo $value->TournamentName;?></p>
											<div class="userAction" style="display:block; padding-top:8px; min-height:20px;" id="userAction">
												<a id="edit_<?php echo $value->id;?>" href="TournamentRulesManage?editId=<?php echo $value->fkTournamentsId; ?>" title="Edit Rules" alt="" class="editUse"><i class="fa fa-edit fa-lg"></i></a>
												<a id="view_<?php echo $value->id;?>" href="TournamentRulesDetail?viewId=<?php echo $value->fkTournamentsId; ?>" title="View Rules" alt="" class="viewUser"><i class="fa fa-search-plus fa-lg"></i></a>
											</div>
										</td>
										<td valign="middle" >
			<?php 						if($countryDetails->fkCountriesId == $usId	&& isset($stateArray[$countryDetails->fkStatesId]))
												echo $countryDetails->Country.' - '.$stateArray[$countryDetails->fkStatesId];
											else echo $countryDetails->Country;?>
										</td>	
										<td valign="middle"><?php if(isset($countryDetails->DateCreated) && $countryDetails->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($countryDetails->DateCreated)); }else echo '-';?></td>
										</tr>
			<?php 					} //End of for each ?>
			<?php 				} ?>
			<?php 			} // row span if end 
						} // foreach loop end ?> 																		

			<?php 	} else { ?>	
							<tr><td colspan="16" align="center" style="color:red;">No Record(s) Found</td></tr>
			<?php 	} ?>
				</table>
			</form>
			</div>
			
			</td>
		</tr>
	</table>
       
<?php commonFooter(); ?>
<script type="text/javascript">

$("#ses_date").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	maxDate			:	"0",
	closeText		:   "Close"
   });
jQuery(function() {
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[class^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");
	   
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
</script>
</html>
