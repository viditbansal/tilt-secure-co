<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$tournamentStataObj   =   new LogController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
top_header();
$winListArray		=	array();
$elimPlayerArray	=	array();
$tourTypeInfo		=	array();
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
}
$playerPrizeDetail = array();
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " t.id,t.TournamentName,t.StartDate,t.EndDate,t.PIN,t.LocationRestrict,t.GameType,t.Type ";
$condition = " AND t.CreatedBy IN(1,3) ";
$tournamentListResult  = $tournamentStataObj->finishedTournamentList($fields,$condition);

$tot_rec	= $tournamentStataObj->getTotalRecordCount();
$ids	=	$elimIds = $hsIds = $eIds = '';
$elTourIdArray = array();
	if(isset($tournamentListResult)	&&	is_array($tournamentListResult)	&&	count($tournamentListResult) > 0){
		foreach($tournamentListResult as $key=>$value){
				$tourTypeInfo[$value->id] = $value->Type;
				$ids	.= $value->id.',';
				if(isset($value->GameType) && $value->GameType == 2){
					$eIds	.=	$value->id.',';
				}
				else $hsIds	.=	$value->id.',';
		}
		$hsIds		=	rtrim($hsIds,',');
		$eIds		=	rtrim($eIds,',');
		$elimIds	=	$eIds;
		$ids		=	rtrim($ids,',');
		if($ids != ''){
			// BEGIN : To get the list of players in the tournament with there scores
			if(!empty($hsIds)){ //HighScore Tournament
				$fields		=	" tp.fkTournamentsId as tournamentsId,tp.id as playedId,u.id as userId,u.FirstName,u.LastName,u.Email,tp.PlayerCurrentHighScore,tp.DatePlayed,u.UniqueUserId,tp.StartTime as tpStartTime,tp.EndTime as tpEndTime,ts.Prize ";
				$condition		=	" AND tp.fkTournamentsId IN (".$hsIds." ) 
										AND tp.PlayerCurrentHighScore = tp.TournamentHighScore order by tp.TournamentHighScore desc";
				$tournamentPlayersList  	= 	$tournamentStataObj->tournamentLeaderBoard($fields,$condition);
				// Begin : Construct players array
				$hsPlayersArray	=	array();
				if(isset($tournamentPlayersList) && is_array($tournamentPlayersList) && count($tournamentPlayersList) > 0) {
					foreach($tournamentPlayersList as $hs_key => $hs_value) {
						// Begin : Prize Info
						if($tourTypeInfo[$hs_value->tournamentsId] == 2)
							$playerPrizeDetail[$hs_value->tournamentsId][$hs_value->userId] = number_format($hs_value->Prize)." TiLT$" ;
						else if($tourTypeInfo[$hs_value->tournamentsId] == 3)
							$playerPrizeDetail[$hs_value->tournamentsId][$hs_value->userId] = number_format($hs_value->Prize)." Virtual Coins";
						else if($tourTypeInfo[$hs_value->tournamentsId] == 4)
							$playerPrizeDetail[$hs_value->tournamentsId][$hs_value->userId] = "Custom" ;
						// End : Prize Info
						
						if(isset($hsPlayersArray[$hs_value->tournamentsId])) {
							if(!search_array($hs_value->userId, $hsPlayersArray[$hs_value->tournamentsId])) {
								$hsPlayersArray[$hs_value->tournamentsId][] = $hs_value;
							}
						} else {
							$hsPlayersArray[$hs_value->tournamentsId][] = $hs_value;
						}
					}
				}
				// End : Construct players array
			}
			$elimIds	=	rtrim($elimIds,',');
			if($elimIds != ''){	//Elimination tournament
				$fields		=	" ts.fkUsersId,ept.RoundTurn,ept.Points,ept.fkTournamentsPlayedId,ept.DatePlayed ,u.FirstName,u.LastName,u.Email,UniqueUserId,u.id as UserId,ept.StartTime,ept.EndTime,ts.fkTournamentsId,ts.Prize";
				$condition	=	" AND ts.fkTournamentsId IN(".$elimIds.") AND ts.fkTournamentsId !=''";
				$eliminationPlayersRes1 =	$tournamentStataObj->getEliminationPlayers($fields,$condition);
				if(isset($eliminationPlayersRes1) && is_array($eliminationPlayersRes1) && count($eliminationPlayersRes1)>0){
					foreach($eliminationPlayersRes1 as $key1 =>$value1){
						if(isset($value1->fkTournamentsId) && !empty($value1->fkTournamentsId)){
							$tourId = $value1->fkTournamentsId;
							$elimPlayerArr[$tourId][$value1->fkUsersId][] = $value1; 
							// Begin : Prize Info
							if($tourTypeInfo[$tourId] == 2)
								$playerPrizeDetail[$tourId][$value1->fkUsersId] = number_format($value1->Prize)." TiLT$" ;
							else if($tourTypeInfo[$tourId] == 3)
								$playerPrizeDetail[$tourId][$value1->fkUsersId] = number_format($value1->Prize)." Virtual Coins";
							else if($tourTypeInfo[$tourId] == 4)
								$playerPrizeDetail[$tourId][$value1->fkUsersId] = "Custom" ;
							// End : Prize Info
						}
					}
				}
			}
			// END : To get the list of players in the tournament with there scores
		}
		$playersListArray =	array();
		foreach($tournamentListResult as $key=>$value){
			$playersListArray[$key]['id']			=	$value->id;
			$playersListArray[$key]['tournament']	=	$value->TournamentName;
			$playersListArray[$key]['startDate']	=	$value->StartDate;
			$playersListArray[$key]['endDate']		=	$value->EndDate;
			$playersListArray[$key]['pinBased']			=	$value->PIN;
			$playersListArray[$key]['locationRestrict']	=	$value->LocationRestrict;
			$playersListArray[$key]['gameType']		=	$value->GameType;
			$playersListArray[$key]['tourType']		=	$value->Type;
			$playersArray	=	array();
			$turnsArray		=	array();
			$playerIdArray	=	array();
			if($value->GameType == 2){	//Elimination
				if(isset($elimPlayerArr)	&&	is_array($elimPlayerArr)	&&	count($elimPlayerArr) > 0){
					$i=0;
					if(array_key_exists($value->id,$elimPlayerArr) && is_array($elimPlayerArr)){
						foreach($elimPlayerArr[$value->id] as $key3=>$value3){
							if(is_array($value3)){
								if(isset($value3[0]->UniqueUserId) && !empty($value3[0]->UniqueUserId)){
									$name = 'Guest'.$value3[0]->UserId;
								}else $name = $value3[0]->FirstName.' '.$value3[0]->LastName;
								$tempTurns = array();
								foreach($value3 as $key4=>$value4){
									if(isset($value4->fkTournamentsPlayedId) && !empty($value4->fkTournamentsPlayedId))
										$tempTurns[$value4->RoundTurn] = array($value4->RoundTurn,$value4->Points);
								}
								ksort($tempTurns);
								$playersArray[$i]	=	array('userId'=>$value3[0]->UserId,'userName'=>ucFirst($name),'email'=>$value3[0]->Email,'datePlayed'=>$value3[0]->DatePlayed,'startTime'=>$value3[0]->StartTime,'endTime'=>$value3[0]->EndTime,'turnsDetails'=>$tempTurns);
							}
							$i++;
						}
					}
					$playersListArray[$key]['players']	=	$playersArray;
				}
			}
			else{	//HighScore
				$playersListArray[$key]['players']	=	(isset($hsPlayersArray[$value->id]))? $hsPlayersArray[$value->id] : array();
			}
		}
	}

function search_array($search_val, $search_arr) {
	foreach($search_arr as $element) {
		if($element->userId == $search_val) return true;
	}
}
?>
<body >

	<div class="box-header"><h2><i class="fa fa-list"></i>Winner Statistics</h2></div>
		<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" >
			
			<tr>
				<td valign="top" align="center" colspan="2">
					<form name="search_category" action="WinnerStatistics" method="post">
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
											pagingControlLatest($tot_rec,'WinnerStatistics'); ?>
									<?php }?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr><td colspan= '2' align="center">
			</td></tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td colspan="2">
				
			<div class="tbl_scroll">
				<form action="WinnerStatistics" class="l_form" name="WinnerStatistics" id="WinnerStatistics"  method="post"> 
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions tournamnent">

						<tr align="left">
							<th align="center" width="1%" style="text-align:center">#</th>
							<th width="15%"><?php echo SortColumn('TournamentName','Tournament Name'); ?></th>
							<th width="12%">Start Date</th>
							<th width="12%">End Date</th>
							<th width="12%">Tournament Type</th>
						</tr>
						<?php if(isset($playersListArray) && is_array($playersListArray) && count($playersListArray) > 0 ) { 
								 foreach($playersListArray as $key=>$value){ ?>
						<tr>
							<td valign="top" align="center" ><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td valign="top"><?php if(isset($value['tournament']) && $value['tournament'] != ''){ echo $value['tournament']; } else echo '-';?></td>	
							<td valign="top"><?php if(isset($value['startDate']) && $value['startDate'] != '0000-00-00 00:00:00'){ echo date('m/d/Y H:i:s',strtotime($value['startDate'])); }else echo '-';?></td>
							<td valign="top"><?php if(isset($value['endDate']) && $value['endDate'] != '0000-00-00 00:00:00'){ echo date('m/d/Y H:i:s',strtotime($value['endDate'])); }else echo '-';?></td>
							<td valign="top">
							<?php if(isset($value['pinBased']) && $value['pinBased'] == 1	&&	isset($value['locationRestcit'])	&&	$value['locationRestrict']==1) 
									echo 'PIN Based & Location Based';
							else if(isset($value['pinBased']) && $value['pinBased'] == 1)
									echo 'PIN Based';
							else if(isset($value['locationRestcit']) && $value['locationRestcit'] == 1)
								echo 'Location Based';
							else
								echo 'Normal Tournament';
							?>
							</td>
						</tr>
						<?php if(isset($value['players'])	&&	is_array($value['players'])	&&	count($value['players']) >0 ) { ?>
						<tr class="inner_tbl_list">
						<td></td>
						<td colspan="4">
							<h2>Winner List</h2>
							<table border="0" " cellpadding="0" cellspacing="0" width="97%" class="user_table user_actions">
							<tr>
								<th width="2%" align="center" style="text-align:center">#</th>
								<th width="17%">Player</th>
								<th width="22%">Email</th>
								<th width="12%">Date Played</th>
								<th width="10%">Start Time</th>
								<th width="10%">End Time</th>
								<th width="12%">Player Score</th>
								<th width="15%">Prize</td>
							</tr>
							
					<?php if($value['gameType'] == 2){//Elimination Players 
							$i=1; foreach($value['players'] as $key5 =>$playerDetails){ 
					?>
							<tr>
								<td align="center"><?php echo $i;$i++; ?></td>
								<td><?php  echo $playerDetails['userName']; ?></td>
								<td><?php  echo $playerDetails['email']; ?></td>
								<td><?php if($playerDetails['datePlayed'] != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($playerDetails['datePlayed'])); }else echo '-'; ?></td>
								<td><?php if($playerDetails['startTime'] != '00:00:00'){ echo $playerDetails['startTime']; }else echo '-';  ?></td>
								<td><?php if($playerDetails['endTime'] != '00:00:00'){ echo $playerDetails['endTime']; }else echo '-';  ?></td>
								<td><?php if(!empty($playerDetails['turnsDetails']) && is_array($playerDetails['turnsDetails'])) {
										foreach($playerDetails['turnsDetails'] as $turnsDetails)
										 echo '<p><strong>Round&nbsp;'.$turnsDetails[0].':</strong>&nbsp;'.number_format($turnsDetails[1]).'</p>';
								}else echo '-';?></td>
								<td><?php if(isset($playerPrizeDetail[$value['id']][$playerDetails['userId']])	&&	$playerPrizeDetail[$value['id']][$playerDetails['userId']]	!= '') echo $playerPrizeDetail[$value['id']][$playerDetails['userId']]; 
								else if($value['tourType'] == 2) echo '0 TiLT$';
								else if($value['tourType'] == 3) echo '0 Virtual Coins';
								else if($value['tourType'] == 4) echo 'Custom';
								else echo ' - '; ?>
								</td>
							</tr>
						<?php } //end foreach ?>	
						<?php	}else{//HighScore Players
							$i=1; foreach($value['players'] as $playerDetails){ 
								$userName	=	'';
								if($playerDetails->UniqueUserId != '')
									$userName	=	'Guest'.$playerDetails->userId;
								else if(isset($playerDetails->FirstName)	&&	isset($playerDetails->LastName)) 	
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
								<td><?php 
								if(isset($playerDetails->tpStartTime) && $playerDetails->tpStartTime != '00:00:00'){ echo $playerDetails->tpStartTime; }else echo '-';
								?></td>
								<td><?php 
								if(isset($playerDetails->tpEndTime) && $playerDetails->tpEndTime != '00:00:00'){ echo $playerDetails->tpEndTime; }else echo '-';
								?></td>
								<td><?php if(isset($playerDetails->PlayerCurrentHighScore)	&&	$playerDetails->PlayerCurrentHighScore	!= '') echo number_format($playerDetails->PlayerCurrentHighScore); ?></td>
								<td> <?php if(isset($playerPrizeDetail[$value['id']][$playerDetails->userId])	&&	$playerPrizeDetail[$value['id']][$playerDetails->userId]	!= '') echo $playerPrizeDetail[$value['id']][$playerDetails->userId]; 
									else if($value['tourType'] == 2) echo '0 TiLT$';
									else if($value['tourType'] == 3) echo '0 Virtual Coins';
									else if($value['tourType'] == 4) echo 'Custom';
									else echo ' - '; ?> 
								</td>
							</tr>
							<?php } // players List loop?>
							<?php } // end HighScore Players?>
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
$(".detailUser").on('click',function(){
	var hre	=	$(".detailUser").attr("href");
 	window.parent.location.href = hre+'&back=1';
});
</script>
</html>
