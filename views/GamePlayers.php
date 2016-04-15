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
	destroyPagingControlsVariables();
	unset($_SESSION['tilt_sess_gameReport_players']);
	unset($_SESSION['tilt_sess_gameReport_email']);
	unset($_SESSION['tilt_sess_gameReport_ptournaments']);
}
$fields1    = " u.id, u.`Email` , u.`FirstName` , u.`LastName`, u.UniqueUserId,t.fkGamesId ";
$condition1 = "";
$condition1 = " t.Status = 1 AND t.fkDevelopersId = ".$_SESSION['tilt_developer_id']." AND  a.ActionType = 2 ";
if(isset($_SESSION['tilt_sess_gameReport_fromDate']) && $_SESSION['tilt_sess_gameReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_gameReport_endDate']) && $_SESSION['tilt_sess_gameReport_endDate'] != '')
	$condition1 .= " AND ( date(t.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_gameReport_fromDate']))."' and date(t.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_gameReport_endDate']))."')";
$pagingParam	=	'';
if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
	$condition1 .=  " AND t.fkGamesId = ".$_GET['viewId']." ";
	$pagingParam	=	'?viewId='.$_GET['viewId'];
	if(isset($_GET['gameName']) && $_GET['gameName'] != '')
		$pagingParam	.=	'&gameName='.$_GET['gameName'];
}

if(isset($_POST['Search'])	&&	$_POST['Search'] != '')	{
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['username']))
		$_SESSION['tilt_sess_gameReport_players']	=	trim($_POST['username']);
	if(isset($_POST['email']))
		$_SESSION['tilt_sess_gameReport_email']	=	trim($_POST['email']);
	if(isset($_POST['tournament_name'])	)
		$_SESSION['tilt_sess_gameReport_ptournaments']	=	trim($_POST['tournament_name']);
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$gamePlayersResult  = $reportObj->getGamePlayers($fields1,$condition1);
$tot_rec 		 = $reportObj->getTotalRecordCount();
$userIds	= '';
$userTourArray = array();
if(isset($gamePlayersResult) && is_array($gamePlayersResult) && count($gamePlayersResult) > 0 ) { 
		foreach($gamePlayersResult as $key1=>$value1){ //Get User Ids
			if(isset($value1->id) && !empty($value1->id))	$userIds .= $value1->id.',';
		}
		$userIds 	= rtrim($userIds,',');
		if(!empty($userIds)){ //Get User tournaments
			$fields 	= " a.fkUsersId as id,t.TournamentName ";
			$condition1 .= " AND a.fkUsersId IN({$userIds}) ";
			$userTourRes = $reportObj->getGameUsersTournaments($fields,$condition1);
			if(isset($userTourRes) && is_array($userTourRes) && count($userTourRes) > 0 ) { 
				foreach($userTourRes as $key2=>$value2){ 
					if(isset($value2->id) && !empty($value2->id) && isset($value2->TournamentName) && !empty($value2->TournamentName))
						$userTourArray[$value2->id][] = $value2->TournamentName;
				}
			}
		}
	}
?>
<body class="popup_bg" style="height:auto;">
	<section class="content-header">
		<h2 id="heading_title"><i class="fa fa-list"></i>&nbsp;Players List<?php if(isset($_GET['gameName']) && $_GET['gameName'] != '')	echo ' - '.$_GET['gameName']; ?></h2>
	</section>	
	<section class="content col-md-12 box-center develop_page">
		<div style="height: 20px" class="clear"></div>
		<div class="search_group">
			<form class="tournament" method="post" action="<?php echo 'GamePlayers'.$pagingParam;?>&cs=1">
				<div class="box-body col-md-7 box-center">
					<div class="form-group col-sm-4">
						<input type="text" class="form-control input w95" placeholder="User Name" name="username" id="username"  value="<?php  if(isset($_SESSION['tilt_sess_gameReport_players']) && $_SESSION['tilt_sess_gameReport_players'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_gameReport_players']);  ?>" >
					</div>
					<div class="form-group col-sm-4">
						<input type="text" placeholder="Email" class="form-control input w95" name="email" id="email"  value="<?php  if(isset($_SESSION['tilt_sess_gameReport_email']) && $_SESSION['tilt_sess_gameReport_email'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_gameReport_email']);  ?>" >
					</div>
					<div class="form-group col-sm-4">
						<input type="text" class="form-control input w95" placeholder="Tournament Name" name="tournament_name" id="tournament_name"  value="<?php  if(isset($_SESSION['tilt_sess_gameReport_ptournaments']) && $_SESSION['tilt_sess_gameReport_ptournaments'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_gameReport_ptournaments']);  ?>" >
					</div>
					<div class="box-footer clear" align="center">
						<input type="submit"  class="btn btn-green" name="Search" id="Search" value="Search" title="Search">
					</div>
				</div>
			</form>
		</div>
		<div class="">
				<?php if(isset($gamePlayersResult) && is_array($gamePlayersResult) && count($gamePlayersResult) > 0){ ?>
				No. of Player(s)&nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div class="clear" style="height: 20px"></div>
		<div class="clear" style="width: 100%">
			<form action="GamePlayers" class="l_form" name="TournamentPlayedUsersList" id="TournamentPlayedUsersList"  method="post"> 
				<input type="hidden" name="action_hidden" id="action_hidden" value="">
				<div class="table-responsive">
					<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-responsive <?php if(isset($gamePlayersResult) && is_array($gamePlayersResult) && count($gamePlayersResult) > 5 ) echo 'scroll'; ?>" width="100%">
					<tr>
						<th align="center" width="1%" class="text-center">#</th>
						<th align="left" width="15%">User Name</th>
						<th align="left" width="15%">Email</th>
						<th align="left" width="15%">Tournament Name</th>
					</tr>
					<?php if(isset($gamePlayersResult) && is_array($gamePlayersResult) && count($gamePlayersResult) > 0 ) { 
							 foreach($gamePlayersResult as $key=>$value){
								$userName = $email = '-'; $userId = $tournaments = '';
								if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
									$userName = 'Guest'.$value->id;
								else if((isset($value->FirstName)	&& !empty($value->FirstName)) 	&& (isset($value->LastName)	&& !empty($value->LastName)) )
									$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
								else if(isset($value->FirstName)	&& !empty($value->FirstName) )	
									$userName	=	 ucfirst($value->FirstName);
								else if(isset($value->LastName)	&& !empty($value->LastName) )	
									$userName	=	ucfirst($value->LastName);
								if(isset($value->Email)	&& !empty($value->Email) )	
									$email = $value->Email;
								if(array_key_exists($value->id,$userTourArray) && is_array($userTourArray[$value->id]) && count($userTourArray[$value->id]))
									$tournaments = implode($userTourArray[$value->id],', ');
					 ?>									
					<tr id="test_id_<?php echo $value->id;?>">
						<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td><?php echo $userName; ?></td>
						<td><?php echo $email;?></td>
						<td><div style="float: left; word-wrap: break-word; white-space: normal; width: 360px;"><?php echo $tournaments;?></div></td>
					</tr>
					<?php } ?> 																		
				</table>
				<?php } else { ?>	
					<tr>
						<td colspan="16" align="center" class="error">No Player(s) Found</td>
					</tr>
					</table>
				<?php } ?>
				</div>
			</form>
		</div>
		<div class="col-xs-12 clear">
			<?php if(isset($gamePlayersResult) && is_array($gamePlayersResult) && count($gamePlayersResult) > 0 ) {
				pagingControlLatest($tot_rec,'GamePlayers'.$pagingParam); ?>
			<?php }?>
		</div>
		<br>
	</section>
<?php commonFooter(); ?>
</html>
