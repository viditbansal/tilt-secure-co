<?php 
ini_set('max_execution_time', 3600);
ini_set('memory_limit', '1024M');
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$tournamentStataObj   =   new LogController();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
// Begins : Variable declaration
$display		=   'none';
$class			=	'';
$msg    		=	'';
$cover_path		=	'';
$updateStatus	=	1;
$winListArray	=	array();
$playersArray	=	array();
$turnsArray		=	array();
$temp_array		=	array();
$tourIdArr		=	array();
// Ends : Variable declaration
top_header();

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_tournament_name']);
	unset($_SESSION['mgc_sess_tournament_start']);
	unset($_SESSION['mgc_sess_tournament_end']);
	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['tournament']))
		$_SESSION['mgc_sess_tournament_name'] 	=	trim($_POST['tournament']);

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
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);

//START : Getting recent tournament list
$fields    = " t.id,t.TournamentName,t.StartDate,t.EndDate,t.PIN,t.LocationRestrict,t.LocationBased, t.GameType,t.Type,t.TournamentStatus ";
$condition = " and t.Status != '3'  AND t.CreatedBy IN(1,3)  ";
$tournamentResult  = $tournamentStataObj->getTournamentList($fields,$condition);
$tot_rec 		 = $tournamentStataObj->getTotalRecordCount();

if($tot_rec!=0 && !is_array($tournamentResult)) {
	$_SESSION['curpage'] = 1;
	$tournamentResult  = $tournamentStataObj->getTournamentList($fields,$condition);
	$tot_rec 		 = $tournamentStataObj->getTotalRecordCount();
}
//END : Getting recent tournament list

//START : Construction tournaments and tournament played Details
$ids	=	'';
$elimPIds = $elimIds = $hsIds = $eIds = $overalltournamentId = '';
$elTourPIdArray = array();
if(isset($tournamentResult)	&&	is_array($tournamentResult)	&&	count($tournamentResult) > 0){
	foreach($tournamentResult as $key=>$value){		
		$overalltournamentId .= $value->id.',';
		$tournamentListResult[$value->id] = $value;
		$tournamentListResult[$value->id]->tournamentPlayedId = '';
	}
	//Getting tournament played details
	if($overalltournamentId != '') {
		$overalltournamentId = trim($overalltournamentId,',');
		$fields = " tp.fkTournamentsId,tp.id as tournamentPlayedId ";
		$condition = " tp.fkTournamentsId in (".$overalltournamentId.") group by tp.fkTournamentsId";
		$tournamentPlayedResult  = $tournamentObj->getTournamentPlayed($fields,$condition);
		if(is_array($tournamentPlayedResult) && count($tournamentPlayedResult) > 0 ){
			foreach($tournamentPlayedResult as $key=>$value){
				$tournamentListResult[$value->fkTournamentsId]->tournamentPlayedId = $value->tournamentPlayedId;					
			}
		}
	}
}

//END : Construction tournaments and tournament played Details

if(isset($tournamentListResult)	&&	is_array($tournamentListResult)	&&	count($tournamentListResult) > 0) {
	foreach($tournamentListResult as $key=>$value) {
		if(isset($value->tournamentPlayedId) && !empty($value->tournamentPlayedId)){
			$ids	.= $value->id.',';
			if(isset($value->GameType) && $value->GameType == 2){
				$elimIds	.=	$value->id.',';
				if(isset($value->tournamentPlayedId) && !empty($value->tournamentPlayedId)){
					$elimPIds	.=	$value->tournamentPlayedId.','; //After stats table included
					$elTourPIdArray[$value->tournamentPlayedId] = $value->id;
				}
			}
			else $hsIds	.=	$value->id.',';
		}
	}			
	$ids		=	rtrim($ids,','); 
	$hsIds		=	rtrim($hsIds,',');
	$elimIds	=	rtrim($elimIds,',');
	$elimPIds	=	rtrim($elimPIds,',');		
	if($ids != ''){
		if(!empty($hsIds)){ //HighScore Tournament Played Entry
			$fields    = " tp.id,tp.fkTournamentsId as tournamentsId,tp.finalScore,tp.StartTime,tp.EndTime,tp.DatePlayed,tp.TournamentHighScore, tp.PlayerCurrentHighScore,u.FirstName,u.LastName,u.Email,u.id as userId,tp.Status, u.UniqueUserId,t.TotalTurns,tp.fkUsersId,tp.id as turnsPlayed ";
			$condition = " and tp.fkTournamentsId IN (".$hsIds.") AND t.CreatedBy IN(1,3)" ;
			$tournamentPlayersList  	= 	$tournamentStataObj->tournamentPlayers($fields,$condition);
			if(isset($tournamentPlayersList) && is_array($tournamentPlayersList) && count($tournamentPlayersList) > 0) {
				$j = 0;
				foreach($tournamentPlayersList as $hs_key => $hs_value) {
					// Begin : Construct players array
					$playersArray[$hs_value->tournamentsId][] = $hs_value;
					// End : Construct players array
					
					// Begin : Construct HS tournaments players turns array
					$t_id	=	$hs_value->tournamentsId;
					$u_id	=	$hs_value->fkUsersId;
					if(isset($temp_array[$t_id][$u_id])) {
						$position	=	$temp_array[$t_id][$u_id];
						$turnsArray[$t_id][$position]->turnsPlayed++;
						$turnsArray[$t_id][$position]->Status		=	$hs_value->Status;
					} else {
						$hs_value->turnsPlayed = 1; //Customized field to calculate turns 07-02-2015
						if(!in_array($t_id, $tourIdArr)) {
							$tourIdArr[] = $t_id;
							$j = 0;
						} else {
							$j = count($turnsArray[$t_id]);
						}
						$turnsArray[$t_id][$j]		=	$hs_value;
						$temp_array[$t_id][$u_id]	=	$j;
						$j++;
					}
					// End : Construct HS tournaments players turns array
				}
			}				
		}
		if($elimIds != ''){	//Elimination tournament Played Entry
			$fields		=	" ept.id as epId,ept.fkTournamentsPlayedId,ept.fkUsersId,ept.RoundTurn as turnsPlayed,ept.Points, ept.RoundHighScore, ept.DatePlayed, ept.StartTime, ept.EndTime,tp.fkTournamentsId as tournamentsId,tp.fkTournamentsId, u.FirstName,u.LastName,u.Email,u.id as userId,u.Status,ept.Points as PlayerCurrentHighScore,ept.Points as TournamentHighScore,u.UniqueUserId ";
			$condition	=	" and tp.fkTournamentsId IN (".$elimIds.") ";
			$eliminationPlayersRes =	$tournamentStataObj->getEliminationPlayedEntry($fields,$condition);
			// Begin : Construct Elimation Tournaments players turns array
			if(isset($eliminationPlayersRes) && is_array($eliminationPlayersRes) && count($eliminationPlayersRes) > 0) {
				$j = 0;
				foreach($eliminationPlayersRes as $e_key => $e_value) {
					$playersArray[$e_value->tournamentsId][] = $e_value;
					// Begin : Turns array
					$t_id	=	$e_value->tournamentsId;
					$u_id	=	$e_value->fkUsersId;
					if(isset($temp_array[$t_id][$u_id])) {
						$position	=	$temp_array[$t_id][$u_id];
						if($turnsArray[$t_id][$position]->TournamentHighScore < $e_value->TournamentHighScore)
							$turnsArray[$t_id][$position]->TournamentHighScore	=	$e_value->TournamentHighScore;
						if($turnsArray[$t_id][$position]->turnsPlayed < $e_value->turnsPlayed)
							$turnsArray[$t_id][$position]->turnsPlayed	=	$e_value->turnsPlayed;
					} else {
						if(!in_array($t_id, $tourIdArr)) {
							$tourIdArr[] = $t_id;
							$j = 0;
						} else {
							$j = count($turnsArray[$t_id]);
						}
						$turnsArray[$t_id][$j]		=	$e_value;
						$temp_array[$t_id][$u_id]	=	$j;
						$j++;
					}
					// End : Turns array
				}
			}
			// End : Construct Elimation Tournaments players turns array

		}
		$fields    = " tour_stat.fkUsersId,tour_stat.fkTournamentsId ";
		$condition = " and tour_stat.fkTournamentsId IN (".$ids.") " ;
		$winList  	= 	$tournamentStataObj->tournamentWinList($fields,$condition);
		if(isset($winList)	&&	is_array($winList)	&&	count($winList) > 0){
			foreach($winList as $winKey=>$winValue){
				$winListArray[$winValue->fkTournamentsId][]	=	$winValue->fkUsersId;
			}
		}
	}
	$playersListArray =	array();
	$i = 0;
	foreach($tournamentListResult as $key=>$value){
		
		$playersListArray[$i]['id']			=	$value->id;
		$playersListArray[$i]['tournament']	=	$value->TournamentName;
		$playersListArray[$i]['startDate']	=	$value->StartDate;
		$playersListArray[$i]['endDate']		=	$value->EndDate;
		$playersListArray[$i]['pinBased']			=	$value->PIN;
		$playersListArray[$i]['locationRestrict']	=	$value->LocationRestrict;
		$playersListArray[$i]['LocationBased']	=   $value->LocationBased;
		$playersListArray[$i]['gameType']		=	$value->GameType;
		$playersListArray[$i]['tourType']		=	$value->Type;
		$playersListArray[$i]['status']		=	$value->TournamentStatus;			
		if(isset($value->tournamentPlayedId) && !empty($value->tournamentPlayedId)){//Played entry
			if($value->GameType == 2){
				//Elimination Tournaments
				$playersListArray[$i]['players']		=	(isset($playersArray[$value->id]))? $playersArray[$value->id] : array();
				$playersListArray[$i]['playersTurns']	=	(isset($turnsArray[$value->id]))? $turnsArray[$value->id] : array();					
				
			} else { //HighScore Tournaments
				$playersListArray[$i]['players']		=	(isset($playersArray[$value->id]))? $playersArray[$value->id] : array();
				$startDate	=	strtotime($value->StartDate);
				$endDate	=	strtotime($value->EndDate);
				if(date('m/d/Y',strtotime($value->StartDate))	==	date('m/d/Y',strtotime($value->EndDate))){
					$days	=	1;
				}
				else {
					$diff = abs($endDate - $startDate);
					$days = floor(($diff)/ (60*60*24));
				}
				$playersListArray[$i]['totalDays']		=	$days;
				$playersListArray[$i]['playersTurns']	=	(isset($turnsArray[$value->id]))? $turnsArray[$value->id] : array();					
			}
		}
		$i++;
	}
}
?>
<body >

	<div class="box-header"><h2><i class="fa fa-list"></i>Tournament Statistics</h2></div>
		<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" >
			
			<tr>
				<td valign="top" align="center" colspan="2">
					<form name="search_category" action="TournamentStatistics" method="post">
					   <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
							<tr><td height="15"></td></tr>
							<tr>													
								<td width="10%" style="padding-left:20px;"><label>Tournament</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<input type="text" class="input" name="tournament" id="tournament"  value="<?php  if(isset($_SESSION['mgc_sess_tournament_name']) && $_SESSION['mgc_sess_tournament_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_tournament_name']);  ?>" >
								</td>
								<td width="10%" style="padding-left:20px;" align="left"><label>Start Date</label></td>
								<td width="2%" align="center">:</td>
								<td height="40" align="left" width="16%">
									<input style="width:90px;" type="text" autocomplete="off" maxlength="10" class="input datepicker" name="startdate" id="startdate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_tournament_start'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
								</td>
								<td width="10%" style="padding-left:20px;" ><label>End Date</label></td>
								<td width="2%" align="center">:</td>
								<td height="40" align="left" >
									<input style="width:90px" type="text" autocomplete="off"  maxlength="10" class="input datepicker" name="enddate" id="enddate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_tournament_end'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
								</td>
							</tr>
							<tr><td align="center" colspan="9" style="padding-top:20px"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
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
											pagingControlLatest($tot_rec,'TournamentStatistics'); ?>
									<?php }?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td colspan="2">
				
			<div class="tbl_scroll">
				<form action="TournamentStatistics" class="l_form" name="TournamentStatisticsForm" id="TournamentStatisticsForm"  method="post"> 
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions tournamnent">

						<tr align="left">
							<th align="center" width="1%" style="text-align:center">#</th>
							<th width="15%"><?php echo SortColumn('TournamentName','Tournament Name'); ?></th>
							<th width="12%">Start Date</th>
							<th width="12%">End Date</th>
							<th width="12%">Tournament Type</th>
						</tr>
						<?php  if(isset($playersListArray) && is_array($playersListArray) && count($playersListArray) > 0 ) { 
								 foreach($playersListArray as $key=>$value){  ?>
						<tr>
							<td valign="top" align="center" ><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td valign="top"><?php if(isset($value['tournament']) && $value['tournament'] != ''){ echo $value['tournament']; } else echo '-';?></td>	
							<td valign="top"><?php if(isset($value['startDate']) && $value['startDate'] != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value['startDate'])); }else echo '-';?></td>
							<td valign="top"><?php if(isset($value['endDate']) && $value['endDate'] != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value['endDate'])); }else echo '-';?></td>
							<td valign="top">
							<?php 
							if(isset($value['pinBased']) && $value['pinBased'] == 1	&&	isset($value['locationRestrict'])	&&	$value['locationRestrict'] == 1) 
									echo 'PIN Based & Location Restricted';
							else if(isset($value['pinBased']) && $value['pinBased'] == 1)
									echo 'PIN Based';
							else if(isset($value['LocationBased'])	&&	$value['LocationBased'] == 1)
									echo 'Location Based';
							else if(isset($value['locationRestrict']) && $value['locationRestrict'] == 1)
									echo 'Location Restricted';
							else
									echo 'Normal Tournament';
							?>
							</td>
						</tr>
						<?php if(isset($value['players'])	&&	is_array($value['players'])	&&	count($value['players']) >0 ) { ?>
					<tr class="inner_tbl_list">
						<td></td>
						<td colspan="4">
							<h2>Player List</h2>
							<table border="0" " cellpadding="0" cellspacing="0" width="97%" class="user_table user_actions">
							<tr>
								<th width="2%" align="center" style="text-align:center">#</th>
								<th width="17%">Player</th>
								<th width="22%">Email</th>
								<th width="12%">Date Played</th>
								<th width="10%">Start Time</th>
								<th width="10%">End Time</th>
								<th width="12%">Current Score</th>
								<?php if(!isset($value['gameType']) || $value['gameType'] != 2) { ?>
								<th width="15%">High Score</th>
								<?php } ?>
							</tr>
							<?php $i=1; foreach($value['players'] as $playerDetails){ 
								$userName	=	'';
								if(isset($playerDetails->UniqueUserId) && !empty($playerDetails->UniqueUserId)){
									$userName = 'Guest'.$playerDetails->userId;
								}else if(isset($playerDetails->FirstName)	&&	isset($playerDetails->LastName)) 	
									$userName	=	ucfirst($playerDetails->FirstName).' '.ucfirst($playerDetails->LastName);
								else if(isset($playerDetails->FirstName))	
									$userName	=	 ucfirst($playerDetails->FirstName);
								else if(isset($playerDetails->LastName))	
									$userName	=	ucfirst($playerDetails->LastName);
							?> 
							<tr>
								<td align="center"><?php echo $i;$i++; ?></td>
								<td><?php if(isset($userName)	&&	$userName	!= '') echo $userName; ?></td>
								<td><?php if(isset($playerDetails->Email)	&&	$playerDetails->Email	!= '') echo $playerDetails->Email; ?></td>
								<td><?php if(isset($playerDetails->DatePlayed) && $playerDetails->DatePlayed != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($playerDetails->DatePlayed)); }else echo '-'; ?></td>
								<td><?php if(isset($playerDetails->StartTime)	&&	$playerDetails->StartTime	!= '') echo $playerDetails->StartTime; ?></td>
								<td><?php if(isset($playerDetails->EndTime)	&&	$playerDetails->EndTime	!= '') echo $playerDetails->EndTime; ?></td>
								<td><?php if(isset($playerDetails->PlayerCurrentHighScore)	&&	$playerDetails->PlayerCurrentHighScore	!= '') echo number_format($playerDetails->PlayerCurrentHighScore); ?></td>
								<?php if(!isset($value['gameType']) || $value['gameType'] != 2) { ?>
								<td><?php if(isset($playerDetails->finalScore)	&&	$playerDetails->finalScore	!= '') echo number_format($playerDetails->finalScore); ?></td>
								<?php }?>
							</tr>
							<?php } // players List loop?>
							</table>
						</td>
					</tr>
								<?php } //players List exist if end?>
					<?php if(isset($value['playersTurns'])	&&	is_array($value['playersTurns'])	&&	count($value['playersTurns']) >0 ) { ?>
					<tr class="inner_tbl_list">
						<td></td>
						<td colspan="4">
							<h2>Player Turns</h2>
							<table border="0" " cellpadding="0" cellspacing="0" width="97%" class="user_table user_actions">
							<tr> 
								<th width="2%" align="center" style="text-align:center">#</th>
								<th width="17%">Player</th>
								<th width="22%">Email</th>
								<th width="12%">Last Played</th>
								<th width="10%">Last Played Score</th>
								<th width="10%">High Score</th>
								<th width="12%">Tournament Status</th>
								<th width="15%">
								<?php if(isset($value['gameType']) && $value['gameType'] == 2) echo 'Total Rounds Played'; else{?>
								Turns Used (Max
								<?php 
								$maxTurns	=	0;

								if(isset($playersListArray[$key]['totalDays'])	&&	$playersListArray[$key]['totalDays']	!= 0  && isset($value['playersTurns'][0]->TotalTurns) &&	$value['playersTurns'][0]->TotalTurns	!=0 )
								{
									$maxTurns	= $playersListArray[$key]['totalDays']*$value['playersTurns'][0]->TotalTurns; echo $maxTurns;
									
								} ?> )
								<?php } ?>
								</th>
							</tr>
							<?php 
							$winStatusKey	=	0;
							if(isset($winListArray[$value['id']]) || (isset($value['status']) && $value['status'] == 3))
								$winStatusKey	=	1;
							$i=1; foreach($value['playersTurns'] as $playerTurnDetails){ 
								$userName	=	'';
								if(isset($playerTurnDetails->UniqueUserId) && !empty($playerTurnDetails->UniqueUserId)){
									$userName = 'Guest'.$playerTurnDetails->userId;
								}else if(isset($playerTurnDetails->FirstName)	&&	isset($playerTurnDetails->LastName)) 	
									$userName	=	ucfirst($playerTurnDetails->FirstName).' '.ucfirst($playerTurnDetails->LastName);
								else if(isset($playerTurnDetails->FirstName))	
									$userName	=	 ucfirst($playerTurnDetails->FirstName);
								else if(isset($playerTurnDetails->LastName))	
									$userName	=	ucfirst($playerTurnDetails->LastName);
								 $winStatus	=	'-';
								 if($winStatusKey){
									if(isset($playerTurnDetails->tournamentsId)	&&	isset($playerTurnDetails->fkUsersId)	&&	isset($winListArray[$playerTurnDetails->tournamentsId])	&& in_array($playerTurnDetails->fkUsersId,$winListArray[$playerTurnDetails->tournamentsId])) 
										$winStatus	=	$gameResultStatus[1];
									else 
									$winStatus	=	$gameResultStatus[2];
								}
								
							?> 
							<tr>
								<td align="center"><?php echo $i;$i++; ?></td>
								<td><?php if(isset($userName)	&&	$userName	!= '') echo $userName; ?></td>
								<td><?php if(isset($playerTurnDetails->Email)	&&	$playerTurnDetails->Email	!= '') echo $playerTurnDetails->Email; ?></td>
								<td><?php if(isset($playerTurnDetails->DatePlayed) && $playerTurnDetails->DatePlayed != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($playerTurnDetails->DatePlayed)); }else echo '-'; ?></td>
								<td><?php if(isset($playerTurnDetails->PlayerCurrentHighScore)	&&	$playerTurnDetails->PlayerCurrentHighScore	!= '') echo number_format($playerTurnDetails->PlayerCurrentHighScore); ?></td>
								<td><?php if(isset($playerTurnDetails->TournamentHighScore)	&&	$playerTurnDetails->TournamentHighScore	!= '') echo number_format($playerTurnDetails->TournamentHighScore); ?></td>
								<td><?php 
									echo $winStatus;
									?>
								</td>
								<td><?php if(isset($playerTurnDetails->turnsPlayed)	&&	$playerTurnDetails->turnsPlayed	!= ''	) echo $playerTurnDetails->turnsPlayed; ?></td>
							</tr>
							<?php } // players List loop?>
							</table>
						</td>
					</tr>
								<?php } //players List exist if end?>
						<?php } // tournament foreach ?> 																		
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
		</table>
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
//self.close();
$(".detailUser").on('click',function(){
	var hre	=	$(".detailUser").attr("href");
 	window.parent.location.href = hre+'&back=1';
});
</script>
</html>
