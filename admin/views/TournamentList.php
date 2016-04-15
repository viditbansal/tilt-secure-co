<?php
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj 		= new TournamentController();
$elimTournaments	= $eliminationPlayerCount = array();
$devBrandActive	= $userActive	= $gamedeveloperActive	= $brandfrom = '';

if(isset($_SESSION['referPage']))
	unset($_SESSION['referPage']);
$_SESSION['referPage']	=	'TournamentList';
if(isset($_GET['cs']) && $_GET['cs']=='2') {						// list page redirect from menus or User list
	unset($_SESSION['mgc_sess_tournament_createdBy']);
	unset($_SESSION['mgc_sess_tournament_createdUser']);
}
if(isset($_GET['cs']) && ($_GET['cs']=='1' || $_GET['cs']=='2') ) { // unset all session variables
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_tournament_game']);
	unset($_SESSION['mgc_sess_brand']);
	unset($_SESSION['mgc_sess_tournament_name']);
	unset($_SESSION['mgc_sess_tournament_user']);
	unset($_SESSION['mgc_sess_game_developer']);
	unset($_SESSION['mgc_sess_gameType']);
	unset($_SESSION['mgc_sess_user_status']);
	unset($_SESSION['mgc_sess_tournament_status']);
	unset($_SESSION['mgc_sess_tournament_start']);
	unset($_SESSION['mgc_sess_tournament_end']);
	unset($_SESSION['mgc_sess_locationbased_check']);
	unset($_SESSION['mgc_sess_pinbased_check']);
	unset($_SESSION['mgc_sess_locationrestrict_check']);
	unset($_SESSION['mgc_sess_tournament_gameType']);
	unset($_SESSION['mgc_sess_tournament_prizeType']);

	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
else if(isset($_GET['cs']) && $_GET['cs']=='3'){
	destroyPagingControlsVariables();
}
$createdFlag	= 1;
$createdBy		= 0;
if(isset($_GET['createdId'])	&&	$_GET['createdId']	!=''){		// From userlist created tournament pop up
	$_SESSION['mgc_sess_tournament_createdUser']	=	$_GET['createdId'];
}
if(isset($_SESSION['mgc_sess_tournament_createdUser']) && $_SESSION['mgc_sess_tournament_createdUser'] !='')
	$createdBy	=	2;
if(isset($_GET['devBrandId']) && $_GET['devBrandId'] != '') //brand_id as devBrandId
	$createdBy	=	1;
if($createdBy == 0){ 												// When listing not a user created popup
	if(isset($_GET['tournament_type'])	&&	$_GET['tournament_type'] !=''){		// to handle user created and brand created
		$_SESSION['mgc_sess_tournament_createdBy']	=	$_GET['tournament_type'];
	}
	if(!isset($_SESSION['mgc_sess_tournament_createdBy'])) {
		$_SESSION['mgc_sess_tournament_createdBy']	=	1;
		$userActive		=	'';
		$devBrandActive	=	'active'; //$brandActive as $devBrandActive
	}
	$createdFlag	= 1;											// To maintain paging controls
	if(isset($_SESSION['mgc_sess_tournament_createdBy']) && $_SESSION['mgc_sess_tournament_createdBy'] !='') {
		if($_SESSION['mgc_sess_tournament_createdBy'] == 1){
			$createdFlag	=	1;
			$devBrandActive	=	'active';
			$userActive		=	'';
		}
		else if($_SESSION['mgc_sess_tournament_createdBy'] == 2){
			$createdFlag	=	2;
			$userActive		=	'active';
			$devBrandActive	=	'';
		}

	}
}
else {
	if($createdBy == 1)
		$createdFlag	=	1;
	else if($createdBy == 2)
		$createdFlag	=	2;
	else
		$createdFlag	=	1;
}

// from statistics page
if(isset($_GET['statistics']))		$_SESSION['statistics_tournaments']	=	1;
if(!isset($_GET['statistics']))		unset($_SESSION['statistics_tournaments']);
if(!isset($_GET['statistics'])	&&	!isset($_GET['active'])) {
	unset($_SESSION['mgc_sess_from_date']);
	unset($_SESSION['mgc_sess_to_date']);
	unset($_SESSION['statistics_tournaments']);
}

//common details for all block
$condition       = " and Status !=3";
$field			 = " id as gameId,Name ";
$gameDetailsResult  = $tournamentObj->selectGameDetails($field,$condition);
//Search
if(isset($_POST['Search']) && $_POST['Search'] != ''){ // Handle search option for both popup and page
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);

	if(isset($_POST['tournament']))
		$_SESSION['mgc_sess_tournament_name'] 	=	trim($_POST['tournament']);
	if(isset($_POST['game']))
		$_SESSION['mgc_sess_tournament_game'] 	=	$_POST['game'];
	if(isset($_POST['brand']))
		$_SESSION['mgc_sess_brand']	    	=	$_POST['brand'];
	if(isset($_POST['gameType']))
		$_SESSION['mgc_sess_gameType']	=	$_POST['gameType'];
	if(isset($_POST['tournament_status']))
		$_SESSION['mgc_sess_tournament_status']	=	$_POST['tournament_status'];
	if(isset($_POST['tournament_user']))
		$_SESSION['mgc_sess_tournament_user']	=	trim($_POST['tournament_user']);
	if(isset($_POST['game_developer']))
		$_SESSION['mgc_sess_game_developer']	=	trim($_POST['game_developer']);

	if(isset($_POST['startdate']) && $_POST['startdate'] != ''){
		$validate_date = dateValidation($_POST['startdate']);
		if($validate_date == 1)
			$_SESSION['mgc_sess_tournament_start']	= $_POST['startdate'];	//$date;
		else
			$_SESSION['mgc_sess_tournament_start']	= '';
	}
	else
		$_SESSION['mgc_sess_tournament_start']	= '';

	if(isset($_POST['enddate']) && $_POST['enddate'] != ''){
		$validate_date = dateValidation($_POST['enddate']);
		if($validate_date == 1)
			$_SESSION['mgc_sess_tournament_end']	= $_POST['enddate'];	//$date;
		else
			$_SESSION['mgc_sess_tournament_end']	= '';
	}
	else
		$_SESSION['mgc_sess_tournament_end']	= '';

	if(isset($_POST['pinbased_check']))
		$_SESSION['mgc_sess_pinbased_check']	=	1;
	else
		$_SESSION['mgc_sess_pinbased_check']	=	0;

	if(isset($_POST['locationrestict_check']))
		$_SESSION['mgc_sess_locationrestrict_check']	=	1;
	else
		$_SESSION['mgc_sess_locationrestrict_check']	=	0;

	if(isset($_POST['locationbased_check']))
		$_SESSION['mgc_sess_locationbased_check']	=	1;
	else
		$_SESSION['mgc_sess_locationbased_check']	=	0;
	if(isset($_POST['tournamentstatus']))
		$_SESSION['mgc_sess_tournamentstatus']	    =	$_POST['tournamentstatus'];
	if(isset($_POST['search_gameType']))
		$_SESSION['mgc_sess_tournament_gameType'] 	=	$_POST['search_gameType'];
	if(isset($_POST['search_prizeType']))
		$_SESSION['mgc_sess_tournament_prizeType'] 	=	$_POST['search_prizeType'];
}
//Handle delete and bulk action delete
if(isset($_POST['do_action']) && $_POST['do_action'] != '')	{
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
		$Ids	=	implode(',',$_POST['checkedrecords']);
		if($_POST['bulk_action']==3)
			$delete_id = $Ids;
	}
}
if(isset($_GET['delId']) && $_GET['delId']!='')
	$delete_id      = $_GET['delId'];

if(isset($delete_id) && $delete_id != ''){
	$tournamentObj->deleteTournamentReleatedEntries($delete_id);
	$_SESSION['notification_msg_code']	=	3;
	header("location:TournamentList");
	die();
}
$todayWithoutMin	= getCurrentTime('America/New_York','Y-m-d');
$today				= getCurrentTime('America/New_York','Y-m-d H:i:s');
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
if($createdFlag == 1){ // Developer & Brand created
	$fields    = " t.id,t.id as tournamentId,t.TournamentName,t.PIN,t.Type,t.fkUsersId,t.fkGamesId,t.fkDevelopersId,t.CreatedBy,
					t.MaxPlayers,t.EntryFee, t.Prize,t.StartDate,t.EndDate,t.GameType,t.Elimination,t.DateCreated, t.PIN, t.LocationBased, t.Status, t.TournamentStatus, t.TotalTurns, t.FeeType, t.LocationRestrict,t.CurrentHighestScore, g.Name as gameName, gd.id as gamedeveId,  gd.Company as Name,gd.Status as gamedevstaus,g.Status as gameStatus " ;
	$condition = " AND t.CreatedBy = 3 and t.Status != '3' ";
	if(isset($_GET['active']) && $_GET['active'] == 1 ){
		$condition .= " AND t.TournamentStatus !=3 ";
	}
	$tournamentListResult  = $tournamentObj->getgamedevloperTournamentList($fields,$condition);
}
else if($createdFlag == 2){  //User created
	$fields    = "t.id,t.id as tournamentId,t.TournamentName,t.PIN,t.Type,t.fkUsersId,t.fkGamesId,t.fkDevelopersId,t.CreatedBy,
					t.MaxPlayers,t.EntryFee, t.Prize,t.StartDate,t.EndDate,t.GameType,t.Elimination,t.DateCreated, t.PIN, t.LocationBased, t.Status, t.TournamentStatus, t.TotalTurns, t.FeeType, t.LocationRestrict,t.CurrentHighestScore, g.Name as gameName,u.FirstName,u.LastName,u.Status as userstatus,u.id as userId,u.UniqueUserId,g.Status as gameStatus  ";
	$condition = " AND t.CreatedBy = 1 AND  UniqueUserId = '' and t.Status != '3' ";
	if(isset($_SESSION['mgc_sess_tournament_createdUser']) && $_SESSION['mgc_sess_tournament_createdUser'] !='')
		$condition .= " AND t.fkUsersId =".$_SESSION['mgc_sess_tournament_createdUser'];
	if(isset($_GET['active']) && $_GET['active'] == 1 ){
		$condition .= " AND t.TournamentStatus !=3 ";
	}
	$tournamentListResult  = $tournamentObj->getUserTournamentList($fields,$condition);
}
$tot_rec 		= $tournamentObj->getTotalRecordCount();
$ids			=  $elimTourIds =	"";
$createdUser	=	'';

if(isset($tournamentListResult)	&&	is_array($tournamentListResult)	&&	count($tournamentListResult) > 0){

	if($createdFlag == 1)
		$createdUser	=	$tournamentListResult[0]->Name;
	else if($createdFlag == 2)
		$createdUser	=	$tournamentListResult[0]->FirstName.' '.$tournamentListResult[0]->LastName;
	$tourIds	=	'';
	foreach($tournamentListResult as $key =>$value){
		$ids	.= $value->id.',';
		if(isset($value->GameType) && $value->GameType == 2){
			$elimTourIds	.=	$value->id.',';
			if(isset($value->TournamentStatus) && $value->TournamentStatus != 3)
				$tourIds	.=	$value->id.',';
		}
	}

	$ids	=	rtrim($ids,',');
	if($ids != ''){
		$fields   				= 	" t.id, c.fkTournamentsId,count(c.id) as chatsCount ";
		$condition 				= 	" and c.fkTournamentsId IN(".$ids.") ";
		$tournamentChatCount  	= 	$tournamentObj->getTournamentChatCount($fields,$condition);
		if(isset($tournamentChatCount)	&&	is_array($tournamentChatCount)	&& count($tournamentChatCount)>0){
			$countArray	=	array();
			foreach($tournamentChatCount as $key =>$value){
				$countArray[$value->fkTournamentsId]	=	$value->chatsCount;
			}
		}
		$fields   				= 	" tp.id,tp.fkUsersId,tp.fkTournamentsId";
		$condition 				= 	" tp.fkTournamentsId IN(".$ids.") ";
		$tournamentPlayerCount  = 	$tournamentObj->getTournamentPlayersCount($fields,$condition);
	}

	if($elimTourIds != ''){
		$elimTourIds	=	rtrim($elimTourIds,',');
		$fields		=	" count(DISTINCT ept.fkUsersId) as userCount, tp.`fkTournamentsId`,tp.`TournamentHighScore`,Elimination ";
		$condition	=	" AND tp.`fkTournamentsId` IN(".$elimTourIds.") ";
		$eliminationplayerscounts =	$tournamentObj->getEliminationPlayedEntry($fields,$condition);
		if(isset($eliminationplayerscounts) && is_array($eliminationplayerscounts) && count($eliminationplayerscounts)>0){
			foreach($eliminationplayerscounts as $eliminationtourKey =>$eliminationtourValue){
					$eliminationPlayerCount[$eliminationtourValue->fkTournamentsId] = $eliminationtourValue->userCount;
			}
		}
	}
}
//paging controls when user from
$from = '';
if((isset($_GET['from']) && $_GET['from'] != '') && (isset($_GET['devBrandId']) && $_GET['devBrandId'] != '' )  ){
	$from = '?from='.$_GET['from'].'&brand_id='.$_GET['devBrandId'];
	if(isset($_GET['hide_players']) && $_GET['hide_players'] != ''){
		$from .= '&hide_players='.$_GET['hide_players'];
	}
	$brandfrom = '&'.ltrim($from,'?');
}
else if(isset($_GET['statistics'])	&&	$_GET['statistics']	==	1){
	$from	=	'?statistics=1';
}
else if(isset($_GET['active']) && $_GET['active'] == 1 ){
	$from	=	'?active=1';
}
else if(isset($_GET['createdId'])	&&	$_GET['createdId']	!=''){
	$from	=	'?createdId='.$_GET['createdId'];
}
?>
<body class="<?php if(isset($_GET['from'])) echo 'popup_bg'; ?>" >

	<?php if(!isset($_GET['from'])	&&	!isset($_GET['statistics'])	&&	!isset($_GET['active']) &&	$createdBy == 0 ) top_header();   ?>
		<?php if(isset($_GET['from'])){?>
				<div class="box-header"><h2><i class="fa fa-list"></i>
							<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0){ echo $tournamentListResult[0]->brandName.' - '; }?>
							Tournament List</h2>
						 </div>
						 <?php } else if(isset($_GET['statistics'])	||	isset($_GET['active'])){ ?>
						 <div class="box-header"><h2><i class="fa fa-list"></i>
							<?php if(isset($_GET['active'])) echo 'Active '; ?>Tournament List</h2>
						 </div>
						 <?php } else if($createdBy){ ?>
						 <div class="box-header"><h2><i class="fa fa-list"></i>
							Tournament List Created By <?php  echo $createdUser; ?></h2>
						 </div>
						 <?php } else { ?>
						 <div class="box-header"><h2><i class="fa fa-list"></i>Tournament List</h2></div>
						 <?php } ?>
						 <div class="clear">
				            <table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" >

								<tr>
									<td valign="top" align="center" colspan="2">

										<form name="search_category" action="TournamentList<?php if(isset($_GET['from'])	||	isset($_GET['statistics'])	||	isset($_GET['active']) || $createdBy) echo $from; ?>" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">
												<tr><td height="15"></td></tr>
												<tr>
													<td width="7%" style="padding-left:20px;"><label>Tournament</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input" name="tournament" id="tournament"  value="<?php  if(isset($_SESSION['mgc_sess_tournament_name']) && $_SESSION['mgc_sess_tournament_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_tournament_name']);  ?>" maxlength="100">
													</td>
													<td width="7%" style="padding-left:20px;"><label>Game</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40" width="20%">
													<select name="game" id="game">
															<option value="">Select</option>
															<?php if(isset($gameDetailsResult) && is_array($gameDetailsResult) && count($gameDetailsResult) > 0){
																	foreach($gameDetailsResult as $key => $value) {
																		if(isset($value->Name) && !empty($value->Name)){ ?>
																			<option value="<?php echo $value->gameId; ?>" <?php
																				if(isset($_SESSION['mgc_sess_tournament_game']) && $_SESSION['mgc_sess_tournament_game'] != ''	&&	$_SESSION['mgc_sess_tournament_game'] == $value->gameId) echo 'selected'; ?>><?php echo $value->Name; ?></option>
																<?php	}
																	}
																}?>
													</select>
													</td>
												<?php if($createdBy==0) { ?>
													<?php if($createdFlag ==2) { ?>
														<?php if(!isset($_GET['active'])){?>
															<td width="10%" style="padding-left:20px;"><label>User</label></td>
															<td width="2%" align="center">:</td>
															<td align="left"  height="40">
																<input type="text" class="input" name="tournament_user" id="tournament_user"  value="<?php  if(isset($_SESSION['mgc_sess_tournament_user']) && $_SESSION['mgc_sess_tournament_user'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_tournament_user']);  ?>" maxlength="100">
															</td>
														<?php }?>
													<?php } else if($createdFlag == 1) { ?>
														<td width="10%" style="padding-left:20px;"><label>Developer & Brand</label></td>
															<td width="2%" align="center">:</td>
															<td align="left"  height="40">
																<input type="text" class="input" name="game_developer" id="game_developer"  value="<?php  if(isset($_SESSION['mgc_sess_game_developer']) && $_SESSION['mgc_sess_game_developer'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_game_developer']);  ?>" maxlength="100" >
															</td>
													<?php }?>
												<?php }else {
														if(isset($_GET['from']) || $createdBy == 1) { ?>
														<td width="10%" style="padding-left:20px;"><label>Status</label></td>
															<td width="2%" align="center">:</td>
															<td align="left"  height="40">
																<select name="tournament_status" id="tournament_status" style="width:200px;">
																	<option value="">Select</option>
																	<?php if(isset($tournamentStatus) && is_array($tournamentStatus) && count($tournamentStatus) > 0){
																			foreach($tournamentStatus as $key => $value) { ?>
																					<option value="<?php echo $key; ?>" <?php
																						if(isset($_SESSION['mgc_sess_tournament_status']) && $_SESSION['mgc_sess_tournament_status'] != ''	&&	$_SESSION['mgc_sess_tournament_status'] == $key) echo 'selected'; ?>><?php echo $value; ?></option>
																		<?php	}
																		}?>
																</select>
															</td>
														<?php } ?>
												<?php } ?>

												</tr>
												<tr><td height="10"></td></tr>
												<tr>

													<td width="10%" style="padding-left:20px;" align="left"><label>Start Date</label></td>
													<td width="2%" align="center">:</td>
													<td height="40" align="left" >
														<input style="width:90px;" type="text" autocomplete="off" maxlength="10" class="input datepicker" name="startdate" id="startdate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_tournament_start'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
													</td>
													<td width="10%" style="padding-left:20px;" ><label>End Date</label></td>
													<td width="2%" align="center">:</td>
													<td height="40" align="left" >
														<input style="width:90px" type="text" autocomplete="off"  maxlength="10" class="input datepicker" name="enddate" id="enddate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_tournament_end'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
													</td>
											<?php  	if(($createdFlag ==2 || $createdFlag ==3 )) {
														if(!isset($_GET['active']) && $createdBy != 1){ ?>
															<td width="10%" style="padding-left:20px;"><label>Status</label></td>
															<td width="2%" align="center">:</td>
															<td align="left"  height="40">
																<select name="tournament_status" id="tournament_status" style="width:200px;">
																	<option value="">Select</option>
																	<?php if(isset($tournamentStatus) && is_array($tournamentStatus) && count($tournamentStatus) > 0){
																			foreach($tournamentStatus as $key => $value) { ?>
																					<option value="<?php echo $key; ?>" <?php
																						if(isset($_SESSION['mgc_sess_tournament_status']) && $_SESSION['mgc_sess_tournament_status'] != ''	&&	$_SESSION['mgc_sess_tournament_status'] == $key) echo 'selected'; ?>><?php echo $value; ?></option>
																		<?php	}
																		}?>
																</select>
															</td>
											<?php 		}
													} else {?>
														<td height="40" align="left" colspan='3' style="padding-left:20px;">
															<span><input type="checkbox" id="pinbased_check" onchange="tour_search_form('pin')" name="pinbased_check"
															<?php if(isset($_SESSION['mgc_sess_pinbased_check'])	&&	$_SESSION['mgc_sess_pinbased_check'] == 1) { echo 'checked'; } ?> style="vertical-align: middle;" />
															<label for="pinbased_check" style="vertical-align: middle;">Pin Based</label></span>
															&nbsp;&nbsp;<span><input type="checkbox" id="locationrestict_check" onchange="tour_search_form('pin')" name="locationrestict_check"
															<?php if(isset($_SESSION['mgc_sess_locationrestrict_check'])	&&	$_SESSION['mgc_sess_locationrestrict_check'] == 1) { echo 'checked'; } ?> style="vertical-align: middle;" />
															<label for="locationrestict_check" style="vertical-align: middle;">Location Restricted</label></span>
															&nbsp;&nbsp;<span><input type="checkbox" id="locationbased_check" onchange="tour_search_form('loc')" name="locationbased_check"
															<?php if(isset($_SESSION['mgc_sess_locationbased_check'])	&&	$_SESSION['mgc_sess_locationbased_check'] == 1) { echo 'checked'; } ?> style="vertical-align: middle;"/>
															<label for="locationbased_check" style="vertical-align: middle;">Location Based</label></span>
														</td>
											<?php 	} ?>
												</tr>
												<tr><td height="10"></td></tr>
												<tr>
													<td width="10%" style="padding-left:20px;"><label>Game Type</label></td>
													<td width="2%" align="center">:</td>
													<td>
													<?php $searchGameType = 0;
															if(isset($_SESSION['mgc_sess_tournament_gameType']) && $_SESSION['mgc_sess_tournament_gameType'] != ''){
																if($_SESSION['mgc_sess_tournament_gameType'] == 1) $searchGameType = 1;
																else if($_SESSION['mgc_sess_tournament_gameType'] == 2) $searchGameType = 2;
															} ?>
														<select name="search_gameType" id="search_gameType" style="width:200px;">
															<option value="">Select</option>
															<option value="1" <?php if($searchGameType == 1) echo 'selected';?> >High Score</option>
															<option value="2" <?php if($searchGameType == 2) echo 'selected';?> >Elimination</option>
														</select>
													</td>
													<td width="10%" style="padding-left:20px;"><label>Prize Type</label></td>
													<td width="2%" align="center">:</td>
													<td>
														<?php $searchPrizeType = 0;
														if(isset($_SESSION['mgc_sess_tournament_prizeType']) && $_SESSION['mgc_sess_tournament_prizeType'] != ''){
															if($_SESSION['mgc_sess_tournament_prizeType'] == 2) $searchPrizeType = 2;
															else if($_SESSION['mgc_sess_tournament_prizeType'] == 3) $searchPrizeType = 3;
															else if($_SESSION['mgc_sess_tournament_prizeType'] == 4) $searchPrizeType = 4;
														} ?>
														<select name="search_prizeType" id="search_prizeType" style="width:200px;">
															<option value="">Select</option>
															<option value="2" <?php if($searchPrizeType == 2) echo 'selected';?> >TiLT$</option>
															<option value="3" <?php if($searchPrizeType == 3) echo 'selected';?> >Virtual Coins</option>
															<option value="4" <?php if($searchPrizeType == 4) echo 'selected';?> >Custom</option>
														</select>
													</td>
													<?php  	if($createdFlag ==3) { ?>
														<td height="40" align="left" colspan='3' style="padding-left:20px;">
															<span><input type="checkbox" id="locationrestict_check" onchange="tour_search_form('pin')" name="locationrestict_check"
															<?php if(isset($_SESSION['mgc_sess_locationrestrict_check'])	&&	$_SESSION['mgc_sess_locationrestrict_check'] == 1) { echo 'checked'; } ?> style="vertical-align:middle;"/>
															<label for="locationrestict_check" style="vertical-align:middle;">Location Restricted</label></span>
														<td>
													<?php } ?>
													<?php if($createdFlag !=2 && $createdFlag !=3) {
													if(!isset($_GET['active']) && !isset($_GET['from'])){?>
															<td width="10%" style="padding-left:20px;"><label>Status</label></td>
															<td width="2%" align="center">:</td>
															<td align="left"  height="40">
																<select name="tournament_status" id="tournament_status" style="width:200px;">
																	<option value="">Select</option>
																	<?php if(isset($tournamentStatus) && is_array($tournamentStatus) && count($tournamentStatus) > 0){
																			foreach($tournamentStatus as $key => $value) { ?>
																					<option value="<?php echo $key; ?>" <?php
																					if(isset($_SESSION['mgc_sess_tournament_status']) && $_SESSION['mgc_sess_tournament_status'] != ''	&&	$_SESSION['mgc_sess_tournament_status'] == $key) echo 'selected'; ?>><?php echo $value; ?></option>
																		<?php	}
																		}?>
																</select>
															</td>
													<?php } } ?>
												</tr>
												<tr><td height="10"></td></tr>
												<tr>
													<td align="center" colspan="9" ><input type="submit" class="submit_button" name="Search" title="Search" id="Search" value="Search"></td>
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
														<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0 ) {
														 	if(isset($_GET['from'])	||	isset($_GET['statistics'])	||	isset($_GET['active'])) pagingControlLatest($tot_rec,'TournamentList'.$from);
															else pagingControlLatest($tot_rec,'TournamentList'); ?>
														<?php }?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td height="10"></td></tr>
								<tr><td colspan= '2' align="center">
									<?php displayNotification('Tournament'); ?>
									</td>
								</tr>
								<tr><td height="10"></td></tr>
								<tr>
									<td colspan="2">
									<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
									<tr>
										<td>
									<?php if($createdBy == 0){ ?>
									<div class="nav-tabs-custom">
		            					<ul class="nav nav-styles">
											<li class=" <?php echo $devBrandActive; ?>"><a href="TournamentList?tournament_type=1&cs=3">Developer & Brand</a></li>
											<li class=" <?php echo $userActive; ?>"><a href="TournamentList?tournament_type=2&cs=3">User Created</a></li>
										</ul>
									<?php } ?>
								<div class="tbl_scroll tab-content">
									 <form action="TournamentList" class="l_form" name="TournamentListForm" id="TournamentListForm"  method="post">
										<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
											<tr align="left">
											<?php if(!isset($_GET['from'])	&&	!isset($_GET['statistics'])	&&	!isset($_GET['active']) &&	!isset($_GET['createdId']) &&	$createdBy == 0 ) { ?>
												<th align="center" class="text-center" width="3%"><input onclick="checkAllRecords('TournamentListForm');" type="Checkbox" name="checkAll"/></th>
											<?php } ?>
												<th align="center" width="3%" class="text-center">#</th>
												<th width="28%"><?php echo SortColumn('TournamentName','Tournament'); ?></th>
												<th width="12%">Prize</th>
												<th width="7%"><?php echo SortColumn('EntryFee','Entry Fee'); ?></th>
												<th width="10%">Game Type</th>
												<th width="5%">Max Players</th>
												<th width="8%">Status</th>
												<th width="4%"><?php echo SortColumn('CurrentHighestScore','High Score'); ?></th>
												<th width="8%">Date</th>
												<?php if(!isset($_GET['statistics'])	&&	!isset($_GET['active']) &&	$createdBy == 0){
												if(isset($_GET['hide_players'])	&&	$_GET['hide_players']==1 ) ; else { ?>
												<th width="7%">Players</th>
												<?php }?>
												<th width="10%">Chats</th>
												<?php } ?>
												<th width="5%">Created Date</th>
											</tr>
											<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0 ) {
													 foreach($tournamentListResult as $key=>$value){
														if($createdFlag ==1){// Developer & Brand created
															$userName	= '-';
															if(isset($value->Name)	&&	$value->Name !='')
																$userName	=	ucfirst($value->Name);
														}
														else if($createdFlag ==2){// User created
															$userName	=	' - ';
															if(isset($value->FirstName)	&&	$value->FirstName !='' && isset($value->LastName) && $value->LastName !='')
																$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
															else if(isset($value->FirstName) && $value->FirstName !='')
																$userName	=	 ucfirst($value->FirstName);
															else if(isset($value->LastName) && $value->LastName!='')
																$userName	=	ucfirst($value->LastName);
														}
														$editStatus	=	1;
														if($value->gameStatus  == 3)
															$editStatus	=	0;
														else if($value->TournamentStatus == 3)
															$editStatus	=	0;
														else if(date('Y-m-d H:i',strtotime($value->StartDate)) <= $today)
															$editStatus	=	0;
														$tournamentLink	=	' - '; $linkType = '';
														$linkBack	=	'';
														$elink = '&elink='.$editStatus;
														if(isset($value->id)	&&	$value->id !=''	&&	isset($value->TournamentName) && $value->TournamentName != ''){
															if($createdBy == 1)$linkBack	= '&back=TournamentList';
															if($createdFlag == 1) $linkType = 3;
															else if($createdFlag == 2) $linkType = 1;
															$tournamentLink	='<a class="recordView" href="TournamentDetail?viewId='.$value->id.'&createdType='.$linkType.$linkBack.$brandfrom.$elink.'" title="View" alt="View"  >'.ucfirst($value->TournamentName).'</a>';
														}
														$viewClass = '';
														$tourViewEditLink	=	'';
														$tourEditLink = '';
														$backlinks	=	'';
														$tourEditViewType = '';
														if($createdFlag ==1){ // Developer & Brand created
															$tourEditViewType = 3;
																if($createdBy == 1) {
																	$backlinks	=	'&back=TournamentList';
																}else{
																	if($editStatus) {
																		$viewClass =	'viewUser';
																		$tourEditLink = '<a href="TournamentManage?editId='.$value->id.'&createdBy=developer" title="Edit" alt="Edit" class=""><i class="fa fa-edit fa-lg"></i></a>';
																	}
																}
														}
														else if($createdFlag == 2){ // User created
															$tourEditViewType = 1;
															if($createdBy == 1) {
																$backlinks	=	'&back=TournamentList';
															}
														}
														$tourViewEditLink	= $tourEditLink.'<a class="recordView '.$viewClass.'" href="TournamentDetail?viewId='.$value->id.'&createdType='.$tourEditViewType.$backlinks.$elink.'" title="View" alt="View"  ><i class="fa fa-search-plus fa-lg"></i></a>';
																											?>
											<tr id="test_id_<?php echo $value->id;?>"	>
												<?php if(!isset($_GET['from'])	&&	!isset($_GET['statistics'])	&&	!isset($_GET['active']) &&	!isset($_GET['createdId']) &&	$createdBy == 0) { ?>
												<td valign="top" align="center"><input id="checkedrecords" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" /></td>
												<?php } ?>
												<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
												<td valign="top">
												<?php if(isset($value->id)	&&	$value->id !=''	&&	isset($value->TournamentName) && $value->TournamentName != ''){ ?>
													<p align="left" ><?php echo '<strong>'.$value->id.':</strong> '.$tournamentLink; ?></p>
													<p align="left" ><strong>Game&nbsp;:</strong>
														<?php if(isset($value->gameName) && $value->gameName != '') echo $value->gameName; else echo '-';?>
													</p>
													<?php if($createdFlag ==2) { ?>
													<?php if($createdBy == 0) {?>
														<p align="left" ><strong>User&nbsp;:</strong>
														<?php  if(isset($value->UniqueUserId) && $value->UniqueUserId !='' && isset($value->userId)) { echo 'Guest'.$value->userId; } else if(isset($userName) && $userName != '' && $value->userstatus != 3 ) { echo  '<a class="recordView" href="UserDetail?viewId='.$value->userId.'&back=TournamentList">'.trim($userName).'</a>'; } else  echo $userName;?>
														</p>
													<?php } ?>
													<?php }else  if($createdFlag ==1) {
																 if($createdBy == 0){ ?>
															<p align="left" ><strong>Developer & Brand&nbsp;:</strong>

														<?php if(isset($value->gamedeveId) && $value->gamedeveId !='' && isset($value->gamedevstaus) && $value->gamedevstaus == 1) echo '<a href="GameDeveloperDetail?viewId='.$value->gamedeveId.'&back=TournamentList" >'.$userName.'</a>'; else echo $userName; ?>
														</p>
													<?php   }
													 } ?>
													<?php if(!isset($_GET['from'])	&&	!isset($_GET['statistics'])	&&	!isset($_GET['active']) &&	$createdBy == 0) { ?>
													<div class="userAction" style="display:block; padding-top:8px; min-height:20px;" id="userAction">
															<?php echo $tourViewEditLink; if($editStatus) {?>
															<a onclick="javascript:return confirm('Are you sure to delete?')" href="TournamentList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="deleteUser"><i class="fa fa-trash-o fa-lg"></i></a>
															<?php } ?>
														<?php if(isset($value->LocationBased) && $value->LocationBased == 1) { ?>
															<a href='javascript:void(0)' title='Location Based' style='cursor:default;' class="viewUser"><i class="fa fa-map-marker fa-lg" ></i></a>
														<?php } ?>
														<?php if(isset($value->PIN) && $value->PIN == 1) { ?>
															<a href="GeneratePin?tournamentId=<?php echo $value->id; ?>&tournamentName=<?php if(isset($value->TournamentName) && $value->TournamentName != '') echo $value->TournamentName; ?>&cs=1" title="Pin Based" alt="Pin Based" class=" pop_up viewUser"><i class="fa fa-external-link fa-lg"></i></a>
														<?php } ?>
														<?php if(isset($value->LocationRestrict) && $value->LocationRestrict == 1) { ?>
															<a href="javascript:void(0)" title="Location Restricted" style="cursor:default;" class="viewUser"><i style="color:red;" class="fa fa-map-marker fa-lg"></i></a>
														<?php } ?>
													</div>
													<?php } ?>
												<?php } else echo '-'; ?></td>
												<td valign="top" align="left" style="padding-right:15px;">
												<?php if(isset($value->Type)) { echo ( $value->Type == 3 && isset($value->Prize) && $value->Prize != '') ? number_format($value->Prize)." Virtual Coins" : (($value->Type == 4) ? "Custom"  : (($value->Type == 2 && isset($value->Prize) && $value->Prize != '') ? number_format($value->Prize)." TiLT$" : '-')) ; } else echo '-';	?>
												</td>
												<td valign="top" align="center" style="padding-right:15px;"><?php if(isset($value->EntryFee) && $value->EntryFee != 0){ echo number_format($value->EntryFee); } else echo ' Free ';?></td>
												<td valign="top" align="left" style="padding-right:15px;"><?php if(isset($value->GameType)) { echo ( $value->GameType == 1) ? " High Score " : (($value->GameType == 2) ? "Elimination"  : ' - ') ; } else echo '-'; ?></td>
												<td valign="top" align="center"><?php if(isset($value->MaxPlayers) && $value->MaxPlayers > 0){ echo number_format($value->MaxPlayers); } else echo '-';?></td>
												<td valign="top">
												<?php
													if(isset($value->TournamentStatus) && $value->TournamentStatus != ''){
														if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00' ){
															if($value->TournamentStatus==3)
																echo $tournamentStatus['3'];
															else if(date('Y-m-d H:i',strtotime($value->StartDate)) > $today	){
																echo $tournamentStatus['0'];
															}
															else if(date('Y-m-d H:i',strtotime($value->StartDate)) <= $today){
																echo $tournamentStatus['1'];
															}
														} else echo '-';
													} else echo '-';?>
												</td>
												<td valign="top" align="left"><?php if(isset($value->CurrentHighestScore) && $value->CurrentHighestScore != 0){ echo number_format($value->CurrentHighestScore); } else echo '-';?></td>
												<td valign="top" class="text-center">
												<?php if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->StartDate)); }else echo '-';?>
												<?php if(isset($value->EndDate) && $value->EndDate != '0000-00-00 00:00:00'){?>
												<p align="center" style="margin:0 0 0 1px;">to</p>
												<?php echo date('m/d/Y',strtotime($value->EndDate)); } ?>
												</td>
											<?php if(!isset($_GET['statistics'])	&&	!isset($_GET['active']) &&	$createdBy == 0){
													if(isset($_GET['hide_players'])	&&	$_GET['hide_players']==1 ) ; else {
											 ?>
												<td align="center" align="center">
											<?php if(isset($value->GameType) &&  $value->GameType == 2){
													if(isset($eliminationPlayerCount) && is_array($eliminationPlayerCount)	&&	isset($eliminationPlayerCount[$value->tournamentId])	&&	$eliminationPlayerCount[$value->tournamentId] > 0) { ?>
														<a href="TournamentPlayedUsers?viewId=<?php echo $value->id; if(isset($value->TournamentName) && $value->TournamentName != '')echo '&tournamentName='.$value->TournamentName;?>&type=elimination&cs=1" class="players_popup" title="Tournament Players" alt="Tournament Players" class="editUse"><i class="fa fa-group fa-lg"></i>&nbsp;&nbsp;<?php echo $eliminationPlayerCount[$value->tournamentId]; ?></a>
													<?php } else  echo '-'; ?>
											<?php 	} else if(isset($tournamentPlayerCount)	&& is_array($tournamentPlayerCount)	&&	isset($tournamentPlayerCount[$value->tournamentId])	&&	$tournamentPlayerCount[$value->tournamentId] > 0) {	?>
														<a href="TournamentPlayedUsers?viewId=<?php echo $value->id; if(isset($value->TournamentName) && $value->TournamentName != '')echo '&tournamentName='.$value->TournamentName;?>&type=highscore&cs=1" class="players_popup" title="Tournament Players" alt="Tournament Players" class="editUse"><i class="fa fa-group fa-lg"></i>&nbsp;&nbsp;<?php echo $tournamentPlayerCount[$value->tournamentId]; ?></a>
											<?php   }else echo '-';?></td>
											<?php } ?>
												<td align="center">
													<?php
													if(isset($countArray)	&& is_array($countArray)	&&	isset($countArray[$value->tournamentId])	&&	isset($countArray[$value->tournamentId]) > 0) { ?>
														<?php if(isset($_GET['devBrandId']) && $_GET['devBrandId'] != '' ){ ?>
														<a href="TournamentChats?viewId=<?php echo $value->id; if(isset($value->TournamentName)	&&	$value->TournamentName != '') echo '&tournamentName='.$value->TournamentName; ?>&cs=1<?php if(isset($_GET['devBrandId']) && $_GET['devBrandId'] != '' ) echo '&brand_id='.$_GET['devBrandId'];?>" title="Tournament Chats" class="tournament_list_pop_up" alt="Tournament Chats"><i class="fa fa-comments fa-lg"></i>&nbsp;&nbsp;<?php echo $countArray[$value->tournamentId]; ?></a>
														<?php } else { ?>
														<a href="TournamentChats?viewId=<?php echo $value->id; if(isset($value->TournamentName)	&&	$value->TournamentName != '') echo '&tournamentName='.$value->TournamentName; ?>&cs=1<?php if(isset($_GET['devBrandId']) && $_GET['devBrandId'] != '' ) echo '&brand_id='.$_GET['devBrandId'];?>" title="Tournament Chats" class="tournament_chat_list" alt="Tournament Chats"><i class="fa fa-comments fa-lg"></i>&nbsp;&nbsp;<?php echo $countArray[$value->tournamentId]; ?></a>
														<?php }?>
											<?php } else echo '-'; ?>
												</td>
											<?php } ?>
											<td valign="top" class="text-center">
												<?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?>
											</td>
											</tr>
											<?php } ?>
										</table>
											<?php 	if(!isset($_GET['from'])	&&	!isset($_GET['statistics'])	&&	!isset($_GET['active']) && !$createdBy) {
														if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0)
															bulk_action($tournamentActionArray);
													} ?>
										</form>
										<?php } else { ?>
											<tr>
												<td colspan="16" align="center" style="color:red;">No Tournament(s) Found</td>
											</tr>
										<?php } ?>
										</div>
									<?php if($createdBy == 0){ ?>
										</div>
									<?php } ?>
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
$(document).ready(function() {
	$(".pop_up").colorbox(
		{
			iframe:true,
			width:"60%",
			height:"45%",
			title:true
	});
	$(".players_popup").colorbox(
			{
				iframe:true,
				width:"50%",
				height:"45%",
				title:true,
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
$(".detailUser").on('click',function(){
	var hre	=	$(this).attr("href");
});

$(".tournament_chat_list").colorbox({
		iframe:true,
		width:"73%",
		height:"45%",
		title:true
});
function tour_search_form(searchType){
	if(searchType == 'loc'){
		$('#locationrestict_check').prop('checked', false);
		$('#pinbased_check').prop('checked', false);
	}else
		$('#locationbased_check').prop('checked', false);
}
</script>
</html>
