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
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['tilt_sess_gameReport_winplayers']);
	unset($_SESSION['tilt_sess_gameReport_winemail']);
	unset($_SESSION['tilt_sess_gameReport_wintournaments']);
}
$pagingParam	= $userIds	= $developer_id = '';
$userTourArray 	= array();
$pagingParam  = "?id=".$_GET['id'];
if(isset($_SESSION['tilt_developer_id']) && $_SESSION['tilt_developer_id'] != ''){
	$developer_id		=	$_SESSION['tilt_developer_id'];
}
if(isset($_GET['gameName']) && $_GET['gameName'] != '')
	$pagingParam	.=	'&gameName='.$_GET['gameName'];

$fields    = " DISTINCT u.id, u.Email,u.FirstName,u.LastName,u.UniqueUserId,u.id as userId ";
$condition = " t.Status = 1 AND a.ActionType = 3 AND t.fkDevelopersId = '".$developer_id."'";
if(isset($_GET['id']) && $_GET['id'] != '' ){
	$condition .= " and t.fkGamesId IN (".$_GET['id'].") ";
	if(isset($_SESSION['tilt_sess_gameReport_fromDate']) && $_SESSION['tilt_sess_gameReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_gameReport_endDate']) && $_SESSION['tilt_sess_gameReport_endDate'] != '')
		$condition .= " AND ( date(t.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_gameReport_fromDate']))."' and date(t.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_gameReport_endDate']))."')";
}
		

if(isset($_POST['Search'])	&&	$_POST['Search'] != '')	{
	destroyPagingControlsVariables();
	$_SESSION['tilt_sess_gameReport_winplayers'] = $_SESSION['tilt_sess_gameReport_winemail'] = $_SESSION['tilt_sess_gameReport_wintournaments'] = '';
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['username']))
		$_SESSION['tilt_sess_gameReport_winplayers']	=	trim($_POST['username']);
	if(isset($_POST['email']))
		$_SESSION['tilt_sess_gameReport_winemail']	=	trim($_POST['email']);	
	if(isset($_POST['tournament_name']))
		$_SESSION['tilt_sess_gameReport_wintournaments']	=	trim($_POST['tournament_name']);
}


if(isset($_SESSION['tilt_sess_gameReport_wintournaments'])	&&	$_SESSION['tilt_sess_gameReport_wintournaments'])
	$condition	.=	' AND t.TournamentName	LIKE "%'.$_SESSION['tilt_sess_gameReport_wintournaments'].'%" ';
	
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$gameWinnersResult  = $reportObj->getGameWinnerDetail($fields,$condition);
$tot_rec 		 	= $reportObj->getTotalRecordCount();

if(isset($gameWinnersResult) && is_array($gameWinnersResult) && count($gameWinnersResult) > 0 ) { 
	 foreach($gameWinnersResult as $key1=>$value1){ //Get User Ids
		if(isset($value1->id) && !empty($value1->id))	$userIds .= $value1->id.',';
	 }
	 $userIds 	= rtrim($userIds,',');
	 if(!empty($userIds)){ //Get User tournaments
		$fields 	= " a.fkUsersId as id,t.TournamentName ";
		$condition .= " AND a.fkUsersId IN({$userIds}) AND a.ActionType = 3 ";
		$userTourRes = $reportObj->getGameWinnerTournaments($fields,$condition);
		if(isset($userTourRes) && is_array($userTourRes) && count($userTourRes) > 0 ) { 
			foreach($userTourRes as $key2=>$value2){ 
				if(isset($value2->id) && !empty($value2->id) && isset($value2->TournamentName) && !empty($value2->TournamentName))
					$userTourArray[$value2->id][] = $value2->TournamentName;
			}
		}
	 }
}
?>

<body class="popup_bg" style="overflow-x:hidden;">
	<section class="content-header">	
		<h2 id="heading_title"><i class="fa fa-list"></i>&nbsp;Winners List<?php if(isset($_GET['gameName']) && $_GET['gameName'] != '')	echo ' - '.$_GET['gameName']; ?></h2>
	</section>	
	<section class="content-header">
		<div style="height: 20px" class="clear"></div>
		<div class="search_group">
			<form class="tournament" method="post" action="<?php echo 'Winnerslist'.$pagingParam;?>&cs=1">
				<div class="box-body col-md-7 box-center">
					<div class="form-group col-sm-4">
						<input type="text" class="form-control input w95" placeholder="User Name" name="username" id="username"  value="<?php  if(isset($_SESSION['tilt_sess_gameReport_winplayers']) && $_SESSION['tilt_sess_gameReport_winplayers'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_gameReport_winplayers']);  ?>" >
					</div>
					<div class="form-group col-sm-4">
						<input type="text" placeholder="Email" class="form-control input w95" name="email" id="email"  value="<?php  if(isset($_SESSION['tilt_sess_gameReport_winemail']) && $_SESSION['tilt_sess_gameReport_winemail'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_gameReport_winemail']);  ?>" >
					</div>
					<div class="form-group col-sm-4">
						<input type="text" class="form-control input w95" placeholder="Tournament Name" name="tournament_name" id="tournament_name"  value="<?php  if(isset($_SESSION['tilt_sess_gameReport_wintournaments']) && $_SESSION['tilt_sess_gameReport_wintournaments'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_gameReport_wintournaments']);  ?>" >
					</div>
					<div class="box-footer clear" align="center">
						<input type="submit"  class="btn btn-green" name="Search" id="Search" value="Search" title="Search">
					</div>
				</div>
			</form>
		</div>
		<div class="">
				<?php if(isset($gameWinnersResult) && is_array($gameWinnersResult) && count($gameWinnersResult) > 0){ ?>
				No. of Winner(s)&nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div style="height: 20px" class="clear"></div>
		<div class="clear" style="width: 100%">
			<form action="WinnersList" class="l_form" name="WinnersList" id="WinnersList"  method="post"> 
				<div class="table-responsive">
					<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-responsive <?php if(isset($gameWinnersResult) && is_array($gameWinnersResult) && count($gameWinnersResult) > 5) echo 'scroll'; ?>" width="100%">
					<tr>
						<th align="center" width="1%" class="text-center">#</th>
						<th align="left" width="15%">User Name</th>
						<th align="left" width="15%">Email</th>
						<th align="left" width="15%">Tournament Name</th>
					</tr>
					<?php if(isset($gameWinnersResult) && is_array($gameWinnersResult) && count($gameWinnersResult) > 0 ) { 
							 foreach($gameWinnersResult as $key=>$value){
							$userName = $email = ' - ';
							if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
								$userName = 'Guest'.$value->id;
							else if(isset($value->FirstName)	&&	isset($value->LastName)) 	
								$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
							else if(isset($value->FirstName))	
								$userName	=	 ucfirst($value->FirstName);
							else if(isset($value->LastName))	
								$userName	=	ucfirst($value->LastName);
							if(array_key_exists($value->id,$userTourArray) && is_array($userTourArray[$value->id]) && count($userTourArray[$value->id]))
								$tournaments = implode($userTourArray[$value->id],', ');
					 ?>									
					<tr>
						<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td><?php if(isset($value->UniqueUserId) && $value->UniqueUserId !='') echo $userName; else { ?>
						<a class="winners" href="UserDetail?id=<?php echo  $value->userId; ?>&gameId=<?php echo  $_GET['id']; ?>&gameName=<?php echo  $_GET['gameName']; ?>"><?php echo $userName; ?></a>
						<?php } ?>
						</td>
						<td><?php if(isset($value->Email) && $value->Email != '') echo $value->Email; else echo '-';?></td>
						<td><?php echo $tournaments; ?></td>
					</tr>
					<?php } ?>
					</table>
				<?php } else { ?>	
					<tr>
						<td colspan="16" align="center" class="error">No Winner(s) Found</td>
					</tr>
					</table>
				<?php } ?>
				</div>
			</form>
		</div>
		<div class="col-xs-12 clear">
			<?php if(isset($gameWinnersResult) && is_array($gameWinnersResult) && count($gameWinnersResult) > 0 ) {
				pagingControlLatest($tot_rec,'Winnerslist'.$pagingParam); ?>
			<?php }?>
		</div><br>
	</section>	
	<?php commonFooter(); ?>	
</body>