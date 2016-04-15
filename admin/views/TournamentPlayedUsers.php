<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
		unset($_SESSION['mgc_sess_tournament_played']);
		unset($_SESSION['mgc_sess_tournament_email']);
		unset($_SESSION['mgc_sess_username']);
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " tp.id,count(u.id) as turns,tp.PlayerCurrentHighScore,TournamentHighScore,tp.DateCreated,tp.DatePlayed,u.FirstName,u.LastName,u.Email,u.id as userId,u.Status,u.UniqueUserId ";
$condition = " ";
$pagingParam	=	'';
if(isset($_GET['viewId']) && $_GET['viewId'] != ''){
	$condition .= " and tp.fkTournamentsId = ".$_GET['viewId']." " ;
	$pagingParam	=	'?viewId='.$_GET['viewId'];
	if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '')
		$pagingParam	.=	'&tournamentName='.$_GET['tournamentName'];
	if(isset($_GET['type']) && $_GET['type'] != '')
		$pagingParam	.=	'&type='.$_GET['type'];
}

if(isset($_POST['Search'])	&&	$_POST['Search'] != '')	{
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['username']))
		$_SESSION['mgc_sess_username']	=	trim($_POST['username']);
	if(isset($_POST['email']))
		$_SESSION['mgc_sess_tournament_email']	=	trim($_POST['email']);
	if(isset($_POST['dateplayed']))
		$_SESSION['mgc_sess_tournament_played']	=	$_POST['dateplayed'];
}

if(isset($_GET['type']) && $_GET['type'] != '' && $_GET['type'] == 'elimination'){
		
		if(isset($_SESSION['mgc_sess_username'])	&&	$_SESSION['mgc_sess_username'])
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_username']."%' ||	u.LastName LIKE '%".$_SESSION['mgc_sess_username']."%' || CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_username']."%')";
			
		if(isset($_SESSION['mgc_sess_tournament_email'])	&&	$_SESSION['mgc_sess_tournament_email'])
			$condition	.=	' AND u.email	LIKE "'.$_SESSION['mgc_sess_tournament_email'].'%" ';
		if(isset($_SESSION['mgc_sess_tournament_played'])	&&	$_SESSION['mgc_sess_tournament_played'])
			$condition	.=	' AND ep.DatePlayed = "'.date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_played'])).'" ';
		
		 $fields		=	' u.FirstName,u.LastName,u.Email,u.UniqueUserId, u.id as userId, tp.id as tourPlayId ';
		 $condition		.=	' AND tp.fktournamentsId= '.$_GET['viewId'].' ';
		 $tournamentPlayersResult   =	$tournamentObj->getEliminationPlayerList($fields,$condition);
		 $tot_rec		=	$tournamentObj->getTotalRecordCount();
		 
		$tourPlayId = $userIds = '' ;
		if(isset($tournamentPlayersResult) && is_array($tournamentPlayersResult) && count($tournamentPlayersResult) > 0) {
			$tourPlayId	=	$tournamentPlayersResult[0]->tourPlayId;
			foreach($tournamentPlayersResult as $key => $value){
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
		
}
else{
	if(isset($_SESSION['mgc_sess_username'])	&&	$_SESSION['mgc_sess_username'])
		$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_username']."%' ||	u.LastName LIKE '%".$_SESSION['mgc_sess_username']."%' || CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_username']."%')";
	if(isset($_SESSION['mgc_sess_tournament_email'])	&&	$_SESSION['mgc_sess_tournament_email'])
		$condition	.=	' AND u.email	LIKE "'.$_SESSION['mgc_sess_tournament_email'].'%" ';
	if(isset($_SESSION['mgc_sess_tournament_played'])	&&	$_SESSION['mgc_sess_tournament_played'])
		$condition	.=	' AND tp.DatePlayed = "'.date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_played'])).'" ';
	
	$tournamentPlayersResult  = $tournamentObj->getTournamentPlayers($fields,$condition);
	$tot_rec 		 = $tournamentObj->getTotalRecordCount();
	if($tot_rec!=0 && !is_array($tournamentPlayersResult)) {
		$_SESSION['curpage'] = 1;
		$tournamentPlayersResult  = $tournamentObj->getTournamentPlayers($fields,$condition);
	}
}
?>
<body class="popup_bg" >

						<?php if(!isset($_GET['from'])){?>
						 <div class="box-header" style="padding:10px"><h2><i class="fa fa-list"></i>Players List<?php if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '')	echo ' - '.$_GET['tournamentName']; ?></h2>
						 </div>
						 <?php } ?>
						 <div class="clear">
				            <table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" class="headertable">
								
								<tr>
									<td valign="top" align="center" colspan="2">
										
										<form name="player_search" action="<?php echo 'TournamentPlayedUsers'.$pagingParam;?>" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>													
													<td width="10%" style="padding-left:20px;"><label>User Name</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input" name="username" id="username"  value="<?php  if(isset($_SESSION['mgc_sess_username']) && $_SESSION['mgc_sess_username'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_username']);  ?>" maxlength="150" >
													</td>
													
													<td width="10%" style="padding-left:20px;"><label>Email</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input" name="email" id="email"  value="<?php  if(isset($_SESSION['mgc_sess_tournament_email']) && $_SESSION['mgc_sess_tournament_email'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_tournament_email']);  ?>" maxlength="50">
													</td>													
													 <td width="10%" style="padding-left:20px;" align="left"><label>Date Played</label></td>
													<td width="2%" align="center">:</td>
													<td height="40" align="left" >
														<input style="width:90px;" type="text" autocomplete="off" maxlength="10" class="input datepicker" name="dateplayed" id="dateplayed" title="Date Played" value="<?php if(isset($_SESSION['mgc_sess_tournament_played']) && $_SESSION['mgc_sess_tournament_played'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_tournament_played'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
													</td> 
												</tr>
												<tr><td height="10"></td></tr>
												<tr>
													<td align="center" colspan="9" ><input type="submit" class="submit_button" name="Search" id="Search" value="Search" title="Search"></td>
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
												<?php if(isset($tournamentPlayersResult) && is_array($tournamentPlayersResult) && count($tournamentPlayersResult) > 0){ ?>
												<td align="left" width="20%">No. of Player(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(isset($tournamentPlayersResult) && is_array($tournamentPlayersResult) && count($tournamentPlayersResult) > 0 ) {
														 	pagingControlLatest($tot_rec,'TournamentPlayedUsers'.$pagingParam); ?>
														<?php }?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td height="10"></td></tr>
								<tr><td colspan= '2' align="center">
									<?php displayNotification('Tournament'); ?>
									</td></tr>
								<tr><td height="10"></td></tr>
								<tr>
									<td colspan="2">
									<form action="TournamentPlayedUsers" class="l_form" name="TournamentPlayedUsersList" id="TournamentPlayedUsersList"  method="post"> 
										<div class="tbl_scroll">
									  
										<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
											<tr>
												<th align="center" width="3%" class="text-center">#</th>
												<th width="15%">Player</th>
												<th width="15%">Email</th>
												<th width="5%"><?php if(isset($_GET['type']) && $_GET['type'] != '' && $_GET['type'] == 'elimination') { echo 'Score'; } else echo SortColumn('TournamentHighScore','Score'); ?></th>
												<th width="3%">Date Played</th>
											</tr>
											<?php if(isset($tournamentPlayersResult) && is_array($tournamentPlayersResult) && count($tournamentPlayersResult) > 0 ) { 
													 foreach($tournamentPlayersResult as $key=>$value){
														$userName	=	'';
														
													if(isset($value->UniqueUserId) && $value->UniqueUserId != ''){
														if(isset($value->userId) && $value->userId != '')
																$userName =  "Guest".$value->userId;
													}else{
														if(isset($value->FirstName)	&&	isset($value->LastName)) 	
															$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
														else if(isset($value->FirstName))	
															$userName	=	 ucfirst($value->FirstName);
														else if(isset($value->LastName))	
															$userName	=	ucfirst($value->LastName);
													}
													
													$tScore = $tDate = '';
												if(isset($_GET['type']) && $_GET['type'] != '' && $_GET['type'] == 'elimination') {	
													if(isset($eliminscoreArr[$value->userId]) && is_array($eliminscoreArr[$value->userId]) && count($eliminscoreArr[$value->userId]) >0 ){
														foreach($eliminscoreArr[$value->userId] as $skey => $svalue){
															$tScore	.= "<p><strong>Round&nbsp;".$skey."&nbsp;:&nbsp;</strong>".number_format($svalue['points'])."</p>";
															$tDate	.= "<p>".date('m/d/Y',strtotime($svalue['date']))."</p>";
														}
												 	}
												 }
											 ?>									
											<tr id="test_id_<?php if(isset($value->id)) echo $value->id;?>">
												<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
												<td><?php if(isset($userName) && $userName != '') { ?>
												<?php if(isset($value->userId) && $value->userId != '') {?>
											<?php if(isset($value->UniqueUserId) && $value->UniqueUserId !='')	echo $userName;
											else if(isset($value->Status) && $value->Status != 3) { ?>
											
											<a href="#" onclick="location.href='UserDetail?viewId=<?php echo $value->userId;?>&referList=1&back=TournamentPlayedUsers';"><?php echo $userName; ?></a>
											<?php } else { echo $userName; } ?>
											<?php } ?>
												<?php }else echo '-';?></td>
												<td><?php if(isset($value->Email) && $value->Email != '') echo $value->Email; else echo '-';?></td>
												<td valign="top" class="text-center1">
											<?php if(isset($_GET['type']) && $_GET['type'] != '' && $_GET['type'] == 'elimination') {
													echo $tScore;
												} else {?>
												<?php if(isset($value->TournamentHighScore) && $value->TournamentHighScore != ''){ echo number_format($value->TournamentHighScore); } else echo '-'; }?></td>	
											<td valign="top">
											<?php 	if(isset($_GET['type']) && $_GET['type'] != '' && $_GET['type'] == 'elimination') {														
														echo $tDate;
													}else {
														if(isset($value->DatePlayed) && $value->DatePlayed != '0000-00-00 00:00:00'){
															echo date('m/d/Y',strtotime($value->DatePlayed));
														}
													}
												?>
												</td>	
											</tr>
											<?php } ?> 																		
										</table>
										<?php if(!isset($_GET['from'])) { 
										 if(isset($tournamentPlayersResult) && is_array($tournamentPlayersResult) && count($tournamentPlayersResult) > 0){  ?>
										<?php } } ?>
										<?php } else { ?>	
											<tr>
												<td colspan="16" align="center" style="color:red;">No Player(s) Found</td>
											</tr>
											</table>
										<?php } ?>
										</div>
										</form>
									</td>
								</tr>
								<tr><td height="10"></td></tr>
				            </table>
							 </div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".user_image_pop_up").colorbox({title:true});

$("#dateplayed").datepicker({
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

$(document).ready(function() {		
	$(".pop_up").colorbox(
		{
			iframe:true,
			width:"50%", 
			height:"45%",
			title:true
	});
	$(".players_pop_up").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"80%",
				title:true,
		});
});
$(function(){

   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '600';
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
function close_this()
{
self.close();
}
$(".detailUser").click(function(){
	var hre	=	$(".detailUser").attr("href");
 	window.parent.location.href = hre+'&back=1';
});
</script>
</html>
