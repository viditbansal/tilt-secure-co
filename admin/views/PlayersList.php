<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj	=   new TournamentController();
require_once('controllers/CoinsController.php');
$coinObj  		=   new CoinsController();
require_once('controllers/LogController.php');
$logObj  		= new LogController();
$display   		= 'none';
$class  		= $msg  = $statistics = '';
$today 			= date('Y-m-d');
$userArray		= $userTourArray	= array();
if(isset($_GET['cs']) && $_GET['cs'] == '1'){
	unset($_SESSION['mgc_sess_report_playerName']);
	unset($_SESSION['mgc_sess_report_tournamentName']);
	destroyPagingControlsVariables();
}

if(isset($_POST['Search']) && $_POST['Search'] != ''){ // Handle search option for both popup and page
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['player_name']))
		$_SESSION['mgc_sess_report_playerName'] 	=	trim($_POST['player_name']);
	if(isset($_POST['tournament_name']))
		$_SESSION['mgc_sess_report_tournamentName'] 	=	trim($_POST['tournament_name']);
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
if(isset($_GET['tournamentId']) && $_GET['tournamentId'] != ''){
	
	$condition		= " and tp.fkTournamentsId = ".$_GET['tournamentId'];
	if(isset($_SESSION['mgc_sess_report_playerName']) && $_SESSION['mgc_sess_report_playerName'] != '')
		$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_report_playerName']."%' ||	u.LastName LIKE '%".$_SESSION['mgc_sess_report_playerName']."%' || CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_report_playerName']."%')";
	if(isset($_GET['gametype']) && $_GET['gametype'] != '' && $_GET['gametype'] == 2 ) {
		$fields			=	' u.FirstName,u.LastName,u.Email,u.UniqueUserId, u.id as userId, tp.id as tourPlayId ';
		$getDetails		=	$tournamentObj->getEliminationPlayerList($fields,$condition);
		$tot_rec		=	$tournamentObj->getTotalRecordCount();
		$statistics		= 	"?tournamentId=".$_GET['tournamentId'];
		if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '')
			$playerT 		=  $_GET['tournamentName'];
		else
			$playerT 		=  '-';
		$tourPlayId = $userIds = '' ;
		if(isset($getDetails) && is_array($getDetails) && count($getDetails) > 0) {
			$tourPlayId	=	$getDetails[0]->tourPlayId;
			foreach($getDetails as $key => $value){
				$userIds .= $value->userId.',';
			}
			$userIds = rtrim($userIds, ',');
		}
		if($tourPlayId != '' && $userIds != ''){
			$fields			=	' `RoundTurn`, `Points`, `DatePlayed`, `fkUsersId` ';
			$condition		=	' AND fkTournamentsPlayedId = '.$tourPlayId.' AND fkUsersId in ('.$userIds.')'; 
			$eliminscore	=	$tournamentObj->getEliminationPoints($fields,$condition);
			if(is_array($eliminscore) && count($eliminscore) > 0){
				foreach($eliminscore as $key => $value){
					$eliminscoreArr[$value->fkUsersId][$value->RoundTurn]['points']	=	$value->Points;
					$eliminscoreArr[$value->fkUsersId][$value->RoundTurn]['date']	=	$value->DatePlayed;
				}
			}
		}
		
	} else {
		$fields 		= " u.id,u.FirstName,u.LastName,tp.id,tp.fkTournamentsId,tp.TournamentHighScore,u.UniqueUserId,u.id as userId ";
		$getDetails		=	$coinObj->getPlayersDetails($fields,$condition);
		$tot_rec			=	$coinObj->getTotalRecordCount();
		$statistics		= "?tournamentId=".$_GET['tournamentId'];
		$playerT 		=  (isset($getDetails[0]->TournamentName))?$getDetails[0]->TournamentName:'';
	}
	
	if(isset($_GET['tournamentName'])){
		$playerT	=	ucFirst($_GET['tournamentName']);
	}
	
}else if(isset($_GET['winnerId']) && $_GET['winnerId'] != ''){
	$fields 		= " u.id,u.FirstName,u.LastName,ts.Prize,t.TournamentName,u.UniqueUserId,u.id as userId  ";
	$condition		= " and ts.fkTournamentsId = ".$_GET['winnerId'];
	if(isset($_SESSION['mgc_sess_report_playerName']) && $_SESSION['mgc_sess_report_playerName'] != '')
		$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_report_playerName']."%' ||	u.LastName LIKE '%".$_SESSION['mgc_sess_report_playerName']."%' || CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_report_playerName']."%')";
	$getDetails		=	$coinObj->getWinnersDetails($fields,$condition);
	$tot_rec			=	$coinObj->getTotalRecordCount();
	$statistics		= "?winnerId=".$_GET['winnerId'];
	$winnerT		= (isset($getDetails[0]->TournamentName))?$getDetails[0]->TournamentName:'';
}
else if(isset($_GET['playersGameId']) && $_GET['playersGameId'] != ''){
	$fields    = " u.id,u.id as userId,u.`Email` , u.`FirstName` , u.`LastName`, u.UniqueUserId,t.fkGamesId,t.GameType,a.fkPlayedId ";
	$condition = " t.fkGamesId = ".$_GET['playersGameId']." AND t.Type=3 AND t.Status = 1 AND a.ActionType = 2  AND t.CreatedBy in (1,3)  ";
	if(isset($_SESSION['mgc_sess_report_tournamentName']) && $_SESSION['mgc_sess_report_tournamentName'] != '')
		$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_report_tournamentName']."%' ";
	
	$pagingParam	=	'';
	$gamePlayersResult	=	'';
	$getDetails  = $logObj->getGamePlayers($fields,$condition);
	$tot_rec			=	$logObj->getTotalRecordCount();
	$statistics		= "?playersGameId=".$_GET['playersGameId'];
	$tids='';
	
	if(isset($getDetails) && is_array($getDetails) && count($getDetails) > 0){
		foreach($getDetails as $key1 => $value1){
			if(isset($value1->id) && !empty($value1->id))	$userArray[] = $value1->id;
		}
		if(count($userArray)>0){//Get players tournament and high scores
			$userIds		= implode($userArray,',');
			$fields			= " t.id,tp.fkUsersId as userId,t.TournamentName,tp.TournamentHighScore as HighScore";
			$condition1		= " t.fkGamesId = ".$_GET['playersGameId']." AND t.Type=3 AND t.Status = 1 AND t.GameType = 1 AND tp.fkUsersId IN(".$userIds.")  AND t.CreatedBy in (1,3) ";
			if(isset($_SESSION['mgc_sess_report_tournamentName']) && $_SESSION['mgc_sess_report_tournamentName'] != '')
				$condition1 .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_report_tournamentName']."%' ";
			$highScoreRes 	= $logObj->getHighScorePlayerTournament($fields,$condition1);
			$fields			= " t.id,a.fkUsersId as userId,t.TournamentName, max(ep.Points) as HighScore ";
			$condition2		= $condition." AND t.GameType = 2 AND a.fkUsersId IN(".$userIds.")  AND t.CreatedBy in (1,3) ";
			$elimRes 		= $logObj->getElimPlayerTournament($fields,$condition2);
			if(isset($highScoreRes) && is_array($highScoreRes) && count($highScoreRes) > 0){ //Get high score tournament details
				foreach($highScoreRes as $key2 => $value2){
					if(isset($value2->id) && !empty($value2->id) && isset($value2->userId) && !empty($value2->userId) ){
						if(array_key_exists($value2->userId,$userTourArray)){
							$userTourArray[$value2->userId]['tournaments']	.= ', '.$value2->TournamentName;
							$userTourArray[$value2->userId]['highScore']	.= ', '.$value2->HighScore;
						}
						else {	
							$userTourArray[$value2->userId]['tournaments']	= $value2->TournamentName;
							$userTourArray[$value2->userId]['highScore']	= $value2->HighScore;
						}
					}
				}
			}
			if(isset($elimRes) && is_array($elimRes) && count($elimRes) > 0){ //Get Elimination tournament details
				foreach($elimRes as $key3 => $value3){
					if(isset($value3->id) && !empty($value3->id) && isset($value3->userId) && !empty($value3->userId) ){
						if(array_key_exists($value3->userId,$userTourArray)){
							$userTourArray[$value3->userId]['tournaments'] .= ','.$value3->TournamentName;
							$userTourArray[$value3->userId]['highScore'] .= ','.$value3->HighScore;
						}
						else {	
							$userTourArray[$value3->userId]['tournaments'] = $value3->TournamentName;
							$userTourArray[$value3->userId]['highScore'] = $value3->HighScore;
						}
					}
				}
			}
		}
	}
	if(isset($_GET['gameName'])){
		$playerT	=	ucFirst($_GET['gameName']);
		$statistics		.= "&gameName=".$_GET['gameName'];
	}
}
else if(isset($_GET['winnerGameId']) && $_GET['winnerGameId'] != ''){
	$fields    = " u.id, u.Email,u.FirstName,u.LastName,u.UniqueUserId,u.id as userId ";
	$condition = " t.fkGamesId = ".$_GET['winnerGameId']." AND t.Type=3 AND t.Status = 1 AND a.fkUsersId > 0 AND a.ActionType = 3  AND t.CreatedBy in (1,3) ";
	if(isset($_SESSION['mgc_sess_report_playerName']) && $_SESSION['mgc_sess_report_playerName'] != '')
		$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_report_playerName']."%' ||	u.LastName LIKE '%".$_SESSION['mgc_sess_report_playerName']."%' || CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_report_playerName']."%')";
	if(isset($_SESSION['mgc_sess_report_tournamentName']) && $_SESSION['mgc_sess_report_tournamentName'] != '')
		$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_report_tournamentName']."%' ";
		
	$pagingParam	=	'';
	$gamePlayersResult	=	'';
	$getDetails  = $logObj->getGameWinners($fields,$condition);
	$tot_rec			=	$coinObj->getTotalRecordCount();
	$statistics		= "?winnerGameId=".$_GET['winnerGameId'];
	if(isset($_GET['gameName'])){
		$winnerT	=	ucFirst($_GET['gameName']);
		$statistics		.= "&gameName=".$_GET['gameName'];
	}
	$userIds	=	'';
	if(isset($getDetails) && is_array($getDetails) && count($getDetails) > 0) {
		foreach($getDetails as $w_key => $w_value) {
			$userIds	.= $w_value->id.',';
		}
		$userIds	=	rtrim($userIds, ',');
		if($userIds != '') {
			$fields2		= "t.id, ts.fkUsersId as userId, t.TournamentName, ts.Prize";
			$condition2		= " t.fkGamesId = ".$_GET['winnerGameId']." AND t.Type=3 AND t.Status = 1 AND ts.fkUsersId IN(".$userIds.")  AND t.CreatedBy in (1,3) ";
			if(isset($_SESSION['mgc_sess_report_tournamentName']) && $_SESSION['mgc_sess_report_tournamentName'] != '')
				$condition2 .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_report_tournamentName']."%' ";
			$winnersResult 	= $logObj->getWinnersPlayedTournament($fields2,$condition2);
			if(isset($winnersResult) && is_array($winnersResult) && count($winnersResult) > 0) {
				foreach($winnersResult as $wr_key => $wr_value) {
					$userTourArray[$wr_value->userId]['tournaments'][]	= $wr_value->TournamentName;
					$userTourArray[$wr_value->userId]['Prize'][]		= $wr_value->Prize;
				}
			}
		}
	}
}
?>
<body>
<div class="box-header"><h2><i class="fa fa-list"></i><?php if(isset($_GET['tournamentId'])  || isset($_GET['playersGameId'])){echo (!empty($playerT)?ucwords($playerT)." -  Players":"Players"); }else echo (!empty($winnerT)?ucwords($winnerT)." - Winners":"Winners"); ?></h2></div>
	<div class="clear">
	<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="">
		
		<tr>
			<td valign="top" align="center" colspan="2">
				<form name="search_player" action="" method="post">
					<table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">	
						<tr><td height="20"></td></tr>
						<tr>													
							<td width="7%" style="padding-left:20px;"><label>Player Name</label></td>
							<td width="2%" align="center">:</td>
							<td align="left"  height="40">
								<input type="text" name="player_name" id="player_name" value="<?php  if(isset($_SESSION['mgc_sess_report_playerName']) && $_SESSION['mgc_sess_report_playerName'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_report_playerName']);  ?>" class="input" maxlength="50" <?php if(!isset($_GET['playersGameId']) && !isset($_GET['winnerGameId'])){ ?> style="width:40%" <?php } ?> >
							</td>
							<?php if(isset($_GET['playersGameId']) && $_GET['playersGameId'] != '' || isset($_GET['winnerGameId']) && $_GET['winnerGameId'] != ''){ ?>
								<td width="7%" style="padding-left:20px;"><label>Tournament Name</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<input type="text" name="tournament_name" id="player_name" value="<?php  if(isset($_SESSION['mgc_sess_report_tournamentName']) && $_SESSION['mgc_sess_report_tournamentName'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_report_tournamentName']);  ?>" class="input" maxlength="50">
								</td>
							<?php } ?>
						</tr>
						
						<tr><td height="10"></td></tr>
						<tr>
							<td align="center" colspan="9" ><input type="submit" class="submit_button" title="Search" name="Search" id="Search" value="Search"></td>
						</tr>
						<tr><td height="15"></td></tr>
						
					</table>
				</form>
			</td>
		</tr>
		
		<tr><td height="15"></td></tr>
		<tr>
			<td colspan="2">
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<?php if(isset($getDetails) && is_array($getDetails) && count($getDetails) > 0){ ?>
						<td align="left" width="20%">No. of <?php if(isset($_GET['tournamentId']) || isset($_GET['playersGameId'])){echo  "Player(s)"; }else if(isset($_GET['winnerId']) || isset($_GET['winnerGameId'])) echo "Winner(s)"; ?>&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
								<?php if(isset($getDetails) && is_array($getDetails) && count($getDetails) > 0 ) {
									pagingControlLatest($tot_rec,'PlayersList'.$statistics); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="10"></td></tr>
		<tr><td colspan= '2' align="center"><?php displayNotification('PlayersList'); ?></td></tr>
		<tr><td height="5"></td></tr>
		<tr>
			<td colspan="2">
			<div class="tbl_scroll">
			  <form action="PlayersList" class="l_form" name="PlayersList" id="PlayersListForm"  method="post"> 
			  	<input type="Hidden" name="user_id" id="user_id" value="<?php if(isset($_GET['tournamentId']) && $_GET['tournamentId'] != '' ) echo $_GET['tournamentId'];?>">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr align="left">
						<th align="center" width="3%" class="text-center">#</th>
						<th  align="left" width="28%">Player Name</th>
						<th align="left" width="28%"><?php if(isset($_GET['tournamentId']) && $_GET['tournamentId'] != '') echo 'Score'; else if(isset($_GET['winnerGameId']) || isset($_GET['playersGameId'])) echo 'Tournament Name'; else echo ' Prize '.(isset($_GET['winnerId'])?'(Virtual Coins)':''); ?></th>
						 <?php if(isset($_GET['playersGameId']) && $_GET['playersGameId'] != '') { ?>
						<th  align="left" width="10%">High Score</th>
						<?php } ?>
						<?php if(isset($_GET['winnerGameId']) && $_GET['winnerGameId'] != '') { ?>
						<th  align="left" width="10%">Virtual Coins</th>
						<?php } ?>
					</tr>
					<?php if(isset($getDetails) && is_array($getDetails) && count($getDetails) > 0 ) {
					 foreach($getDetails as $key=>$value){ ?>	
					<tr>
						<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td align="left"><?php 
						if(isset($value->UniqueUserId)	&&	$value->UniqueUserId!=''){
							echo "Guest".$value->userId;
						}else if(isset($value->FirstName)	&&	$value->FirstName!='' || isset($value->LastName)	&&	$value->LastName!='' ){	echo ucfirst($value->FirstName).' '.ucfirst($value->LastName); } else { echo "-"; } ?></td>
						<td align="left">
							<?php
								if(isset($_GET['tournamentId']) && $_GET['tournamentId'] != '') {
									if(isset($_GET['gametype']) && $_GET['gametype'] != '' && $_GET['gametype'] == 2 ) {
										if(isset($eliminscoreArr[$value->userId]) && is_array($eliminscoreArr[$value->userId]) && count($eliminscoreArr[$value->userId]) >0 ){
											foreach($eliminscoreArr[$value->userId] as $skey => $svalue){
												echo "<p><strong>Round&nbsp;".$skey."&nbsp;:&nbsp;</strong>".number_format($svalue['points'])."</p>";
											}
										}
									}
									else if(isset($value->TournamentHighScore) && $value->TournamentHighScore!='') {
										echo $value->TournamentHighScore; } else { echo "-";
									} 
								}
								else if(isset($_GET['winnerId']) && $_GET['winnerId'] != '') {
									if(isset($value->Prize)	&&	$value->Prize>0){	echo $value->Prize; } else { echo "-"; } //echo $value->Prize.'(Virtual Coins)';
								}
								else if(isset($_GET['playersGameId']) && $_GET['playersGameId'] != ''){
									if(array_key_exists($value->userId,$userTourArray) && !empty($userTourArray[$value->userId]['tournaments'])){
										echo $userTourArray[$value->userId]['tournaments'];
									} else echo ' - ';
								}
								else {
									if(isset($userTourArray[$value->userId]['tournaments'])) {
										echo implode($userTourArray[$value->userId]['tournaments'],', ');
									} else {
										echo "-";
									}
								}
							?>
						</td>
					<?php if(isset($_GET['playersGameId']) && $_GET['playersGameId'] != '') { ?>
						 <td>
					<?php 	if(array_key_exists($value->userId,$userTourArray) && $userTourArray[$value->userId]['highScore'] !='')
								echo $userTourArray[$value->userId]['highScore'];
							else echo ' - '; ?>
						 </td>
					<?php } ?>
						<?php if(isset($_GET['winnerGameId']) && $_GET['winnerGameId'] != '') { ?>
						<td>
							<div style="width: 170px;float:left;word-wrap:break-word;">
								<?php
									if(isset($userTourArray[$value->userId]['Prize'])) {
										echo implode($userTourArray[$value->userId]['Prize'],', ');
									} else {
										echo "-";
									}
								?>
								
							</div>
						</td>
						<?php } ?>
					</tr>
					<?php } 
					}else { ?>	
					<tr><td colspan="16" align="center" style="color:red;">No Result(s) Found</td></tr>
				<?php } ?>
				</table>
				</form>
				</div>
		<div style="height: 20px;"></div>
	</table>
	</div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".brand_image_pop_up").colorbox({title:true});
jQuery(function() {
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[id^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
	$(document).ready(function() {		
		$(".tournament_list_pop_up").colorbox(
			{
				iframe:true,
				width:"73%", 
				height:"45%",
				title:true
		});
});
$(function(){
   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '580';
   var maxWidth  = '900';
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
