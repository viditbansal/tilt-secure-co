<?php 
require_once('includes/CommonIncludes.php');
commonHead();
require_once('controllers/ReportController.php');
$reportObj  		=   new ReportController();
if(!isset($_SESSION['tilt_developer_id'])) { ?>
<script type="text/javascript">
   self.parent.location.href='index.php';
</script>
<?php
}
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	unset($_SESSION['tilt_sess_tourReport_playedDate']);
	unset($_SESSION['tilt_sess_tourReport_playerEmail']);
	unset($_SESSION['tilt_sess_tourReport_playername']);
	destroyPagingControlsVariables();
}

$condition1 = $condition = " ";
$pagingParam	=	'';
$type	=	1;
if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
	
	$pagingParam	=	'?viewId='.$_GET['viewId'];
	if(isset($_GET['elimination'])){
		$type = 2;
		$pagingParam	.=	'&elimination=1';
		$condition .= " and fkTournamentsPlayedId = ".$_GET['viewId']." " ;
		$condition1 .= " and fkTournamentsPlayedId = ".$_GET['viewId']." " ;
	}else{
		$condition .= " and tp.fkTournamentsId = ".$_GET['viewId']." " ;
	}
	if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '')
		$pagingParam	.=	'&tournamentName='.$_GET['tournamentName'];
}
if(isset($_POST['Search'])	&&	$_POST['Search'] != '')	{
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['username']))
		$_SESSION['tilt_sess_tourReport_playername']	=	trim($_POST['username']);
	if(isset($_POST['email']))
		$_SESSION['tilt_sess_tourReport_playerEmail']	=	trim($_POST['email']);
	if(isset($_POST['dateplayed']))
		$_SESSION['tilt_sess_tourReport_playedDate']	=	$_POST['dateplayed'];
}
if(isset($_SESSION['tilt_sess_tourReport_playername'])	&&	$_SESSION['tilt_sess_tourReport_playername'])
	$condition .= " and (u.FirstName LIKE '%".$_SESSION['tilt_sess_tourReport_playername']."%' OR	u.LastName LIKE '%".$_SESSION['tilt_sess_tourReport_playername']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['tilt_sess_tourReport_playername']."%')";
if(isset($_SESSION['tilt_sess_tourReport_playerEmail'])	&&	$_SESSION['tilt_sess_tourReport_playerEmail'])
	$condition	.=	' AND u.email	LIKE "'.$_SESSION['tilt_sess_tourReport_playerEmail'].'%" ';
if(isset($_SESSION['tilt_sess_tourReport_playedDate'])	&&	$_SESSION['tilt_sess_tourReport_playedDate']){
	$condition	.=	' AND DATE_FORMAT(tp.DatePlayed,"%Y-%m-%d") = "'.date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_playedDate'])).'" ';
	$condition1	.=	' AND DATE_FORMAT(tp.DatePlayed,"%Y-%m-%d") = "'.date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_playedDate'])).'" ';
}
if(isset($_SESSION['tilt_sess_tourReport_fromDate']) && $_SESSION['tilt_sess_tourReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_tourReport_endDate']) && $_SESSION['tilt_sess_tourReport_endDate'] != ''){
	$condition .= " AND ((date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_fromDate']))."' and date(tp.DatePlayed) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_endDate']))."') OR tp.fkUsersId = 0  ) ";
	$condition1 .= " AND ((date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_fromDate']))."' and date(tp.DatePlayed) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_endDate']))."') OR tp.fkUsersId = 0  ) ";
}else if(isset($_SESSION['tilt_sess_tourReport_fromDate']) && $_SESSION['tilt_sess_tourReport_fromDate'] != ''){
	$condition .= " AND ( date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_fromDate']))."' OR tp.fkUsersId = 0 )";
	$condition1 .= " AND ( date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_fromDate']))."' OR tp.fkUsersId = 0 )";
}else if(isset($_SESSION['tilt_sess_tourReport_endDate']) && $_SESSION['tilt_sess_tourReport_endDate'] != ''){
	$condition .= "AND ((date(tp.DatePlayed) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_endDate']))."') OR tp.fkUsersId = 0) ";
	$condition1 .= "AND ((date(tp.DatePlayed) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_endDate']))."') OR tp.fkUsersId = 0) ";
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$tournamentPlayersResult	=	'';
if($type == 2 ){
	$scoreArray	= array(); //tp for Elimination
	$fields    = " tp.id,tp.fkUsersId,tp.DatePlayed,u.UniqueUserId,u.FirstName,u.LastName,u.Email,u.id as userId,u.Status";
	$tournamentPlayersResult  = $reportObj->getTournamentElimPlayers($fields,$condition." GROUP BY tp.fkUsersId ");
	$tot_rec 		 = $reportObj->getTotalRecordCount();
	$ids	=	'';
	if(isset($tournamentPlayersResult) && is_array($tournamentPlayersResult) && count($tournamentPlayersResult) > 0 ) { 
		foreach($tournamentPlayersResult as $key=>$value){
			if(isset($value->userId) && !empty($value->userId))
				$ids	.=	$value->userId.',';
		}
		$ids = rtrim($ids,',');
		if(!empty($ids)){
			$fields    = " fkUsersId,RoundTurn,Points,DatePlayed";
			$condition1 .= " AND fkUsersId IN (".$ids.")";
			$scoreResult  = $reportObj->getElimPlayerScore($fields,$condition1);
			if(isset($scoreResult) && is_array($scoreResult) && count($scoreResult) > 0){
				 foreach($scoreResult as $sKey=>$sValue){
					$scoreArray[$sValue->fkUsersId][] = array('turn'=>$sValue->RoundTurn,'score'=>$sValue->Points,'DatePlayed'=>$sValue->DatePlayed);
				 }
			}
		}
	}
}
else{
	$fields    = " tp.id,count(u.id) as turns,tp.PlayerCurrentHighScore,TournamentHighScore,u.UniqueUserId,tp.DatePlayed,u.FirstName,u.LastName,u.Email,u.id as userId,u.Status ";
	$tournamentPlayersResult  = $reportObj->getTournamentPlayers($fields,$condition);
	$tot_rec 		 = $reportObj->getTotalRecordCount();
}
?>
<body class="popup_bg" style="overflow-x:hidden;">
	<section class="content-header">
		<h2 id="heading_title"><i class="fa fa-list"></i>&nbsp;Players List<?php if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '')	echo ' - '.$_GET['tournamentName']; ?></h2>
	</section>	
	<section class="content col-md-12 box-center develop_page">
		<div style="height: 20px" class="clear"></div>
		<div class="search_group">
			<form class="tournament" method="post" action="<?php echo 'TournamentPlayers'.$pagingParam;?>&cs=1">
				<div class="box-body col-md-7 box-center">
					<div class="form-group col-sm-4">
						<input type="text" class="input w95 form-control" title="User Name" placeholder="User Name" name="username" id="username"  value="<?php  if(isset($_SESSION['tilt_sess_tourReport_playername']) && $_SESSION['tilt_sess_tourReport_playername'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_tourReport_playername']);  ?>" >
					</div>
					<div class="form-group col-sm-4">
						<input type="text" class="input w95 form-control" title="Email" placeholder="Email" name="email" id="email"  value="<?php  if(isset($_SESSION['tilt_sess_tourReport_playerEmail']) && $_SESSION['tilt_sess_tourReport_playerEmail'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_tourReport_playerEmail']);  ?>" >
					</div>
					<div class="form-group col-sm-4">
						<input type="text" placeholder="Date Played" autocomplete="off" maxlength="10" class="input medium datepicker w95 form-control" name="dateplayed" id="dateplayed" title="Date Played" value="<?php if(isset($_SESSION['tilt_sess_tourReport_playedDate']) && $_SESSION['tilt_sess_tourReport_playedDate'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_tourReport_playedDate'])); else echo '';?>"  onkeypress="return dateField(event);" >
					</div>
				<div class="box-footer clear" align="center">
					<input type="submit"  class="btn btn-green" name="Search" id="Search" value="Search" title="Search">
				</div>
			</form>
		</div>	
		<div class="col-xs-12 no-padding">
				<?php if(isset($tournamentPlayersResult) && is_array($tournamentPlayersResult) && count($tournamentPlayersResult) > 0){ ?>
				No. of Player(s)&nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div class="clear" style="height: 20px"></div>
		<div class="clear" style="width: 100%">
			<form action="TournamentPlayers" class="l_form" name="TournamentPlayedUsersList" id="TournamentPlayedUsersList"  method="post"> 
				<div class="table-responsive">
					<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-responsive <?php if(isset($tournamentPlayersResult) && is_array($tournamentPlayersResult) && count($tournamentPlayersResult) > 5) echo 'scroll'; ?>" width="100%">
					<tr>
						<th class="text-center" width="3%" class="text-center">#</th>
						<th align="left" width="15%">User Name</th>
						<th class="text-center1" width="15%">Email</th>
						<th class="text-center1" width="5%">Score</th>
						<th class="text-center1" width="3%">Date Played</th>
					</tr>
					<?php if(isset($tournamentPlayersResult) && is_array($tournamentPlayersResult) && count($tournamentPlayersResult) > 0 ) { 
							 foreach($tournamentPlayersResult as $key=>$value){
								$userName	=	'';
							if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
								$userName = 'Guest'.$value->userId;
							else if(isset($value->FirstName)	&&	isset($value->LastName)) 	
								$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
							else if(isset($value->FirstName))	
								$userName	=	 ucfirst($value->FirstName);
							else if(isset($value->LastName))	
								$userName	=	ucfirst($value->LastName); ?>
					<tr id="test_id_<?php echo $value->id;?>">
						<td valign="top" class="text-center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td valign="middle" ><?php echo $userName; ?></td>
						<td valign="middle" class="text-center1"><?php if(isset($value->Email) && $value->Email != '') echo $value->Email; else echo '-';?></td>
						<td valign="top" class="text-center1">
						<?php if($type == 2) { 
								if(isset($scoreArray[$value->userId]) && is_array($scoreArray[$value->userId]) && count($scoreArray[$value->userId]) > 0){
									foreach($scoreArray[$value->userId] as $key1=>$scoreDetail){ ?>
									<p><strong>Round&nbsp;<?php echo $scoreDetail['turn'];?>&nbsp;:&nbsp;</strong> <?php echo number_format($scoreDetail['score']);?></p>
						<?php 		}
								}
							}else{ if(isset($value->TournamentHighScore) && $value->TournamentHighScore != ''){ echo number_format($value->TournamentHighScore); } else echo '-';} ?>
						</td>	
					<td valign="top" class="text-center1">
					<?php if($type == 2) { 
								if(isset($scoreArray[$value->userId]) && is_array($scoreArray[$value->userId]) && count($scoreArray[$value->userId]) > 0){
									foreach($scoreArray[$value->userId] as $key1=>$scoreDetail){ ?>
									<p><?php if(isset($scoreDetail['DatePlayed']) && $scoreDetail['DatePlayed'] != '0000-00-00 00:00:00' ) echo date('m/d/Y',strtotime($scoreDetail['DatePlayed'])); else echo '-';?></p>	
						<?php 		}
								}
							}else{ if(isset($value->DatePlayed) && $value->DatePlayed != '0000-00-00 00:00:00' ) echo date('m/d/Y',strtotime($value->DatePlayed));}
							?>
						</td>	
					</tr>
					<?php } ?> 																		
				<?php } else { ?>	
					<tr>
						<td colspan="16" class="error" align="center">No Player(s) Found</td>
					</tr>
				<?php } ?>
					</table>
				</div>
			</form>
		</div>
		<div class="col-xs-12 clear">
			<?php if(isset($tournamentPlayersResult) && is_array($tournamentPlayersResult) && count($tournamentPlayersResult) > 0 ) {
				pagingControlLatest($tot_rec,'TournamentPlayers'.$pagingParam); ?>
			<?php }?>
		</div>
		
	</section>
<?php commonFooter(); ?>
<script type="text/javascript">
 $('#dateplayed').datetimepicker({
	format:'m/d/Y',
	maxDate:'today',
	onShow:function( ct ){
		this.setOptions({
		})
	},
	timepicker:false,
});
</script>
</html>
