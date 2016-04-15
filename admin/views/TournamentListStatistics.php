<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
require_once('controllers/LogController.php');
$reportObj   =   new LogController();
$display   =   'none';
$class  =  $msg    = $cover_path = $elimIds = $hIds = '';
$updateStatus	=	1;
$statistics	=	'';
$tz = new DateTimeZone('America/New_York');
$date = new DateTime();
$today	= $date->format('Y-m-d');
$brandActive	=	$userActive	=	$gamedeveloperActive = '';
if(isset($_GET['cs']) && ($_GET['cs']=='1') ) { // unset all session variables
	unset($_SESSION['mgc_sess_report_tournamentGame']);
	unset($_SESSION['mgc_sess_report_tournamentName']);
	unset($_SESSION['mgc_sess_report_tourbrand']);
	unset($_SESSION['mgc_sess_report_tourStatus']);
	destroyPagingControlsVariables();
	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
// from statistics page
if(isset($_GET['statistics']))		$_SESSION['statistics_tournaments']	=	1;
if(!isset($_GET['statistics']))		unset($_SESSION['statistics_tournaments']);
if(!isset($_GET['statistics'])	&&	!isset($_GET['active'])) {
	unset($_SESSION['mgc_sess_statistic_from_date']);
	unset($_SESSION['mgc_sess_statistic_to_date']);
	unset($_SESSION['statistics_tournaments']);
	unset($_SESSION['mgc_sess_report_tourStatus']);
}
//common details for all block
$condition       = " and Status in(1) ";
$field			 = " id as brandId,BrandName ";
$brandDetails 	 = $tournamentObj->selectBrandDetails($field,$condition);
$condition       = " and Status !=3";
$field			 = " id as gameId,Name ";
$gameDetailsResult  = $tournamentObj->selectGameDetails($field,$condition);

if(isset($_POST['Search']) && $_POST['Search'] != ''){ // Handle search option for both popup and page
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['tournament']))
		$_SESSION['mgc_sess_report_tournamentName'] 	=	trim($_POST['tournament']);
	if(isset($_POST['game']))
		$_SESSION['mgc_sess_report_tournamentGame'] 	=	$_POST['game'];
	if(isset($_POST['brand']))
		$_SESSION['mgc_sess_report_tourbrand']	    	=	$_POST['brand'];
	if(isset($_POST['gameType']))
		$_SESSION['mgc_sess_gameType']	=	$_POST['gameType'];
	if(isset($_POST['tournament_status']))
		$_SESSION['mgc_sess_report_tourStatus']	=	$_POST['tournament_status'];
	if(isset($_POST['tournament_user']))
		$_SESSION['mgc_sess_tournament_user']	=	trim($_POST['tournament_user']);
	if(isset($_POST['game_developer']))
		$_SESSION['mgc_sess_game_developer']	=	trim($_POST['game_developer']);
	
	if(isset($_POST['user']))
		$_SESSION['mgc_sess_report_tournamentuser'] 	=	$_POST['user'];
	if(isset($_POST['startdate']) && $_POST['startdate'] != ''){
		$validate_date = dateValidation($_POST['startdate']);
		if($validate_date == 1)
			$_SESSION['mgc_sess_tournament_start']	= $_POST['startdate'];
		else 
			$_SESSION['mgc_sess_tournament_start']	= '';
	}
	else 
		$_SESSION['mgc_sess_tournament_start']	= '';

	if(isset($_POST['enddate']) && $_POST['enddate'] != ''){
		$validate_date = dateValidation($_POST['enddate']);
		if($validate_date == 1)
			$_SESSION['mgc_sess_tournament_end']	= $_POST['enddate'];
		else 
			$_SESSION['mgc_sess_tournament_end']	= '';
	}
	else 
		$_SESSION['mgc_sess_tournament_end']	= '';
	
	if(isset($_POST['pinbased_check']))
		$_SESSION['mgc_sess_pinbased_check']	=	1;
	else
		$_SESSION['mgc_sess_pinbased_check']	=	0;
		
	if(isset($_POST['locationbased_check']))
		$_SESSION['mgc_sess_locationbased_check']	=	1;
	else
		$_SESSION['mgc_sess_locationbased_check']	=	0;
	if(isset($_POST['tournamentstatus']))
		$_SESSION['mgc_sess_tournamentstatus']	    	=	$_POST['tournamentstatus'];		
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$todayWithoutMin	= getCurrentTime('America/New_York','Y-m-d');
$today				= getCurrentTime('America/New_York','Y-m-d H:i:s');
$fields    			= "t.id,t.TournamentName,t.fkUsersId,t.fkBrandsId,t.fkDevelopersId,t.CreatedBy,t.Prize,t.EntryFee,t.Type,t.MinPlayers,
						t.MaxPlayers,t.Status,t.CurrentHighestScore,t.StartDate,t.EndDate,t.DateCreated,
						t.TournamentStatus,t.PIN,g.Name,t.GameType";
$condition = " AND  t.Status != '3' AND CreatedBy in (1,3) ";

if(isset($_GET['active']) && $_GET['active'] == 1 ){
	$condition .= " AND t.TournamentStatus in(0,1,2) ";
	if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '') {
		$from_date	=	date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']));
		$to_date	=	date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']));
		$condition .= " AND (
							(date(StartDate) <= '".$from_date."' AND date(EndDate) >= '".$from_date."') OR
							(date(StartDate) <= '".$to_date."' AND date(EndDate) >= '".$to_date."') OR
							(date(StartDate) >= '".$from_date."' AND date(StartDate) <= '".$to_date."') OR
							(date(EndDate) >= '".$from_date."' AND date(EndDate) <= '".$to_date."')
							
							)";
	} else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '') {
		$from_date	=	date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']));
		$condition .=	" AND (date(StartDate) >= '".$from_date."' OR date(EndDate) >= '".$from_date."' OR date( EndDate ) = '0000-00-00' )";
	} else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '') {
		$to_date	=	date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']));
		$condition .=	" AND ( (date(EndDate) <= '".$to_date."' AND date(EndDate) != '0000-00-00') OR (date(EndDate) >= '".$to_date."' AND date(StartDate) <= '".$to_date."') OR (date(StartDate) <= '".$to_date."'  AND GameType = 2))";
	}
}
			
$tournamentListResult  = $reportObj->getTournamentReportList($fields,$condition);
$tot_rec 		= $reportObj->getTotalRecordCount();
$ids			=	$userIds	=	$brandIds	=	$devIds	=	"";
$createdUser	=	'';
if(isset($tournamentListResult)	&&	is_array($tournamentListResult)	&&	count($tournamentListResult) > 0){
	foreach($tournamentListResult as $key =>$value){	
		$ids	.= $value->id.',';	
		if(isset($value->CreatedBy) && $value->CreatedBy ==1){
			if(isset($value->fkUsersId) && $value->fkUsersId !=0) $userIds	.=	$value->fkUsersId.',';
		}else if(isset($value->CreatedBy) && $value->CreatedBy ==3){
			if(isset($value->fkDevelopersId) && $value->fkDevelopersId !=0) $devIds	.=	$value->fkDevelopersId.',';
		}
		if(isset($value->GameType) && $value->GameType == 2){
			$elimIds	.= $value->id.',';
		}else{
			$hIds		.= $value->id.',';
		}
	}
	$ids		=	rtrim($ids,',');
	$userIds	=	rtrim($userIds,',');
	$devIds		=	rtrim($devIds,',');
	$userArray	=	$brandUserArray	=	$devArray	=	array();
	if($userIds !=''){
		$usertemp	=	explode(',',$userIds);
		$usertemp	=	array_unique($usertemp);
		$userIds		=	implode($usertemp,',');
		$fields	=	"FirstName,LastName,UniqueUserId,id";
		$condition	=	" id IN (".$userIds.")";
		$userDetailsResult  = $reportObj->selectUserDetail($fields,$condition);
		if(isset($userDetailsResult)	&&	is_array($userDetailsResult)	&&	count($userDetailsResult) > 0){
			foreach($userDetailsResult as $key =>$value){
				if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
					$userArray[$value->id]	=	"Guest".$value->id;
				else
					$userArray[$value->id]	=	$value->FirstName.' '.$value->LastName;
			}
		}
	}
	if($devIds !=''){
		$devtemp	=	explode(',',$devIds);
		$devtemp	=	array_unique($devtemp);
		$devIds		=	implode($devtemp,','); 
		$fields	=	"id,Company";
		$condition	=	" id IN (".$devIds.")";
		$devDetailsResult  = $reportObj->selectDeveloperDetails($fields,$condition);
		if(isset($devDetailsResult)	&&	is_array($devDetailsResult)	&&	count($devDetailsResult) > 0){
			foreach($devDetailsResult as $key =>$value){
				$devArray[$value->id]	=	$value->Company;
			}
		}
	}
	$elimIds	=	rtrim($elimIds,',');
	if($elimIds != ''){
		$fields		=	" count(DISTINCT ept.fkUsersId) as userCount, tp.`fkTournamentsId`,tp.`TournamentHighScore`,Elimination ";
		$condition	=	" AND tp.`fkTournamentsId` IN(".$elimIds.") ";
		$eliminationplayerscounts =	$tournamentObj->getEliminationPlayedEntry($fields,$condition);
		if(isset($eliminationplayerscounts) && is_array($eliminationplayerscounts) && count($eliminationplayerscounts)>0){
			foreach($eliminationplayerscounts as $eliminationtourKey =>$eliminationtourValue){
					$eliminationPlayerCount[$eliminationtourValue->fkTournamentsId] = $eliminationtourValue->userCount;
			}
		}
	}

	$hIds	=	rtrim($hIds,',');
	if($hIds != ''){
		$fields		=	" count(DISTINCT tp.fkUsersId) as userCount, tp.`fkTournamentsId` ";
		$condition	=	" tp.`fkTournamentsId` IN (".$hIds.") ";
		$eliminationplayerscounts =	$tournamentObj->getTournamentPlayedCount($fields,$condition);
		if(isset($eliminationplayerscounts) && is_array($eliminationplayerscounts) && count($eliminationplayerscounts)>0){
			foreach($eliminationplayerscounts as $eliminationtourKey =>$eliminationtourValue){
					$eliminationPlayerCount[$eliminationtourValue->fkTournamentsId] = $eliminationtourValue->userCount;
			}
		}
	}

}
//paging controls when user from 
$from = '';
if(isset($_GET['statistics'])	&&	$_GET['statistics']	==	1){
	$from	=	'?statistics=1';
}
else if(isset($_GET['active']) && $_GET['active'] == 1 ){
	$from	=	'?active=1';
}
?>
<body class="popup_bg" >
	 <div class="box-header"><h2><i class="fa fa-list"></i>Tournament List</h2></div>
		<div class="clear">
			<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" >
				
				<tr>
					<td valign="top" align="center" colspan="2">
						
						<form name="search_category" action="TournamentListStatistics<?php if(isset($_GET['statistics'])	||	isset($_GET['active'])) echo $from; ?>" method="post">
						   <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
								<tr><td height="15"></td></tr>
								<tr>													
									<td width="7%" style="padding-left:20px;"><label>Tournament</label></td>
									<td width="2%" align="center">:</td>
									<td align="left"  height="40" width="18%">
										<input type="text" class="input" name="tournament" id="tournament"  value="<?php  if(isset($_SESSION['mgc_sess_report_tournamentName']) && $_SESSION['mgc_sess_report_tournamentName'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_report_tournamentName']);  ?>" >
									</td>
								
									<td width="7%" style="padding-left:20px;"><label>Game</label></td>
									<td width="2%" align="center">:</td>
									<td align="left"  height="40" width="18%">
									<select name="game" id="game" style="width:88%;">
											<option value="">Select</option>
											<?php if(isset($gameDetailsResult) && is_array($gameDetailsResult) && count($gameDetailsResult) > 0){
													foreach($gameDetailsResult as $key => $value) { ?>
															<option value="<?php echo $value->Name; ?>" <?php
																if(isset($_SESSION['mgc_sess_report_tournamentGame']) && $_SESSION['mgc_sess_report_tournamentGame'] != ''	&&	$_SESSION['mgc_sess_report_tournamentGame'] == $value->Name) echo 'selected'; ?>><?php echo $value->Name; ?></option>
												<?php	}
												}?>
									</select>
									</td>
										
									<td width="7%" style="padding-left:20px;"><label>Developer & Brand</label></td>
										<td width="2%" align="center">:</td>
										<td align="left"  height="40"  width="18%">
											<input type="text" class="input" name="brand" id="brand"  value="<?php  if(isset($_SESSION['mgc_sess_report_tourbrand']) && $_SESSION['mgc_sess_report_tourbrand'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_report_tourbrand']);  ?>" >
										</td>
									<?php if(!isset($_GET['active'])){ ?>
												<td width="7%" style="padding-left:20px;"><label>Status</label></td>
												<td width="2%" align="center">:</td>
												<td align="left"  height="40"  width="18%">
													<select name="tournament_status" id="tournament_status" style="width:88%;">
														<option value="">Select</option>
														<?php if(isset($tournamentStatus) && is_array($tournamentStatus) && count($tournamentStatus) > 0){
																foreach($tournamentStatus as $key => $value) { ?>
																		<option value="<?php echo $key; ?>" <?php
																			if(isset($_SESSION['mgc_sess_report_tourStatus']) && $_SESSION['mgc_sess_report_tourStatus'] != ''	&&	$_SESSION['mgc_sess_report_tourStatus'] == $key) echo 'selected'; ?>><?php echo $value; ?></option>
															<?php	}
															}?>
													</select>
												</td>
								<?php 		}  ?>
								</tr>
								<tr><td height="10"></td></tr>
								<tr>
									<td align="center" colspan="12" ><input type="submit" class="submit_button" name="Search" id="Search" value="Search" title="Search"></td>
								</tr>
								<tr><td height="15"></td></tr>
							 </table>
						  </form>	
					</td>
				</tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td colspan="2">
						<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
							<tr>
								<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0){ ?>
								<td align="left" width="20%">No. of Tournament(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
								<?php } ?>
								<td align="center">
								<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0 ) { pagingControlLatest($tot_rec,'TournamentListStatistics'.$from);  } ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td colspan="2">
					<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<td>
				<div class="tbl_scroll tab-content">
					  <form action="TournamentListStatistics" class="l_form" name="TournamentListStatisticsForm" id="TournamentListStatisticsForm"  method="post"> 
						<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">

							<tr align="left">
								<th align="center" width="3%" class="text-center">#</th>
								<th width="19%"><?php echo SortColumn('TournamentName','Tournament'); ?></th>
								<th width="28%">Created By</th>
								<th width="9%">Game</th>
								<th width="5%">Prize</th>
								<th width="5%"><?php echo SortColumn('EntryFee','Entry Fee'); ?></th>
								<th width="4%">Game Type</th>
								<th width="5%">Max Players</th>
								<th width="8%">Status</th>
								<th width="4%"><?php echo SortColumn('CurrentHighestScore','High Score'); ?></th>
								<th width="7%">Date</th>
								<th width="4%">Players</th>
								<th width="5%">Created Date</th>
							</tr>
							<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0 ) { 
									 foreach($tournamentListResult as $key=>$value){
											$userName	=	' - ';
							 ?>									
							<tr id="test_id_<?php echo $value->id;?>"	>
								<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
								<td valign="top">
									<?php if(isset($value->TournamentName) && $value->TournamentName != '') echo ucFirst($value->TournamentName); else echo '-';?>
								</td>
								<td valign="top" align="left" >
								<?php if(isset($value->CreatedBy) && $value->CreatedBy ==1){
										echo '<strong>User : </strong>'; 
										if(array_key_exists($value->fkUsersId,$userArray)){
											echo ucFirst($userArray[$value->fkUsersId]);
										}
									}else if(isset($value->CreatedBy) && $value->CreatedBy ==3){
										echo '<strong>Developer & Brand : </strong>'; 
										if(array_key_exists($value->fkDevelopersId,$devArray)){
											echo $devArray[$value->fkDevelopersId] != '' ? ucFirst($devArray[$value->fkDevelopersId]) : '-';
										}else echo '-';
									}else echo '-';?></td>	
								<td valign="top" align="left" ><?php if(isset($value->Name) && $value->Name !=''){ echo $value->Name; } else echo '-';?></td>	
								<td valign="top" align="left">
												<?php if(isset($value->Type)) { echo ( $value->Type == 3 && isset($value->Prize) && $value->Prize != '') ? number_format($value->Prize)." Virtual Coins" : (($value->Type == 4) ? "Custom"  : (($value->Type == 2 && isset($value->Prize) && $value->Prize != '') ? number_format($value->Prize)." TiLT$" : '-')) ; } else echo '-';	?>
								</td>
								<td valign="top" align="left" ><?php if(isset($value->EntryFee) && $value->EntryFee != 0){ echo number_format($value->EntryFee); } else echo 'Free';?></td>	
								<td valign="top" align="left" >
								<?php if(isset($value->GameType)){ echo  $value->GameType == 1  ? 'High Score' : ($value->GameType == 2 ? 'Elimination' : '-') ; } else echo '-'; ?>
								</td>
								<td valign="top" align="left"><?php if(isset($value->MaxPlayers) && $value->MaxPlayers > 0){ echo number_format($value->MaxPlayers); } else echo '-';?></td>	
								<td valign="top">
							<?php 	if(isset($value->TournamentStatus) && $value->TournamentStatus != ''){
										if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00' ){
											if($value->TournamentStatus==3)
												echo $tournamentStatus['3'];
											else if(date('Y-m-d',strtotime($value->StartDate)) > $today	){
												echo $tournamentStatus['0'];
											}
											else if(date('Y-m-d',strtotime($value->StartDate)) <= $today){
												echo $tournamentStatus['1'];
											}
										}
									} else echo '-';?>
								</td>	
								<td valign="top" align="left"><?php if(isset($value->CurrentHighestScore) && $value->CurrentHighestScore != 0){ echo number_format($value->CurrentHighestScore); } else echo '-';?></td>
								<td valign="top" class="text-center">
								<?php if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->StartDate)); }else echo '-';?>
								<?php if(isset($value->EndDate) && $value->EndDate != '0000-00-00 00:00:00'){ ?> 
								<p align="center" style="margin:0 0 0 1px;">to</p>
								<?php echo date('m/d/Y',strtotime($value->EndDate)); }?>
								</td>
								<td valign="top" align="right" style="padding-right:15px;"><?php 
									if(isset($eliminationPlayerCount) && is_array($eliminationPlayerCount)	&&	isset($eliminationPlayerCount[$value->id])	&&	$eliminationPlayerCount[$value->id] > 0)
										echo number_format($eliminationPlayerCount[$value->id]);
									else echo ' - ';?></td>	
								<td valign="top" class="text-center">
									<?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?>
								</td>
							</tr>
							<?php } ?> 																		
						</table>
						</form>
						<?php } else { ?>	
							<tr>
								<td colspan="16" align="center" style="color:red;">No Tournament(s) Found</td>
							</tr>
						<?php } ?>
						</div>
					</td>
				</tr>
					</td>
				</tr>
			</table>
			</td>
			</tr>
		</table>
	</div>
<div style="height: 10px;"></div>
<?php commonFooter(); ?>
<script type="text/javascript">
$("#startdate").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
	onSelect		: function (dateText, inst) {
						$('#enddate').datepicker("option", 'minDate', new Date(dateText));
						},
    onClose			: function () { $(this).focus(); },

    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	closeText		:   "Close"
 });
 $("#enddate").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
	onSelect		: function () { },
    onClose			: function () { $(this).focus(); },
    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	closeText		:   "Close"
 });
 jQuery(function() {
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[id^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");
	   
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
$(function(){

   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '580';
   var maxWidth  = '1100';
   if(bodyHeight<maxHeight) {   	
   	setHeight = bodyHeight;
   } else {
   		setHeight = maxHeight;
   }
   if(bodyWidth>maxWidth) {
   		setWidth = bodyWidth;
   } else {
   		setWidth = maxWidth;
   }
   parent.$.colorbox.resize({
        innerWidth :setWidth,
        innerHeight:setHeight
    });
});
</script>
</html>
