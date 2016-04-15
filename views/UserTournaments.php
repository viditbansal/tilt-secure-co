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
	unset($_SESSION['tilt_sess_gameReport_tournament']);
	unset($_SESSION['tilt_sess_gameReport_created']);
	unset($_SESSION['tilt_sess_gameReport_tourStatus']);
	destroyPagingControlsVariables();
}

$condition 	= '';
$fields   	=  " DISTINCT t.id , a.fkUsersId,t.TournamentName, t.TournamentStatus, t.DateCreated, t.StartDate, t.EndDate, t.fkBrandsId";

$pagingParam	=	'';
$devId		=	$_SESSION['tilt_developer_id'];
$condition .= "  t.fkDevelopersId = ".$devId." AND t.Status = 1 ";
if(isset($_GET['viewId']) && $_GET['viewId'] != '' && isset($_GET['type']) && $_GET['type'] !=''){
	$condition .= " and a.fkUsersId = ".$_GET['viewId']." AND a.ActionType = ".$_GET['type']." " ;
	$pagingParam	=	'?viewId='.$_GET['viewId'];
	$pagingParam	.=	'&type='.$_GET['type']; 
	if(isset($_GET['username']) && $_GET['username'] != '')
		$pagingParam	.=	'&username='.$_GET['username'];
	
}
 
if(isset($_POST['Search'])	&&	$_POST['Search'] != '')	{
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['tournament']))
		$_SESSION['tilt_sess_gameReport_tournament']	=	trim($_POST['tournament']);
	if(isset($_POST['datecreated']))
		$_SESSION['tilt_sess_gameReport_created']	=	$_POST['datecreated'];
	if(isset($_POST['tournament_status']))
		$_SESSION['tilt_sess_gameReport_tourStatus']	=	$_POST['tournament_status'];
}

if(isset($_SESSION['tilt_sess_gameReport_tournament'])	&&	$_SESSION['tilt_sess_gameReport_tournament'])
	$condition	.=	' AND t.TournamentName	LIKE "%'.$_SESSION['tilt_sess_gameReport_tournament'].'%" ';
if(isset($_SESSION['tilt_sess_gameReport_created'])	&&	$_SESSION['tilt_sess_gameReport_created'])
	$condition	.=	' AND date(t.DateCreated) = "'.date('Y-m-d',strtotime($_SESSION['tilt_sess_gameReport_created'])).'" ';
if(isset($_SESSION['tilt_sess_userReport_fromDate']) && $_SESSION['tilt_sess_userReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_userReport_endDate']) && $_SESSION['tilt_sess_userReport_endDate'] != '')
	$condition .= " AND ((date(ActivityDate) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_userReport_fromDate']))."' and date(ActivityDate) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_userReport_endDate']))."') ) ";

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$tournamentResult	=	$tourids  = '';
$tot_rec ='';
$tournamentResult  	= 	$reportObj->getBrandUsersTournamentlist($fields,$condition);
$tot_rec			=	$reportObj->getTotalRecordCount();

$today	= date('Y-m-d H:i');
?>
<body class="popup_bg" style="">
	<section class="content-header">
		<h2 id="heading_title"><i class="fa fa-list"></i>&nbsp;Tournament List<?php if(isset($_GET['gameName']) && $_GET['gameName'] != '')	echo ' - '.$_GET['gameName']; ?></h2>
	</section>	
	<section class="content col-md-12 box-center develop_page">
		<div class="clear" style="height: 20px"></div>
		<div class="search_group">
			<form class="tournament" method="post" action="<?php echo 'UserTournaments'.$pagingParam;?>">
				<div class="box-body col-md-7 box-center">
					<div class="form-group col-sm-4">
						<input  type="text" class="form-control" placeholder="Tournament Name" title="tournament name" name="tournament" id="tournament" value="<?php if(isset($_SESSION['tilt_sess_gameReport_tournament']) && $_SESSION['tilt_sess_gameReport_tournament'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_gameReport_tournament']);?>">
					</div>
					<div class="form-group col-sm-4">
						<select name="tournament_status" title="Tournament Status" id="tournament_status" class="input w95 form-control">
							<option value="">Status</option>
							<?php if(isset($tournamentStatus) && is_array($tournamentStatus) && count($tournamentStatus) > 0){
									foreach($tournamentStatus as $statKey => $statValue) { ?>
											<option value="<?php echo $statKey; ?>" <?php  if(isset($_SESSION['tilt_sess_gameReport_tourStatus']) && $_SESSION['tilt_sess_gameReport_tourStatus'] != ''	&&	$_SESSION['tilt_sess_gameReport_tourStatus'] == $statKey) echo 'selected'; ?>><?php echo $statValue; ?></option>
								<?php	}
								}?>
						</select>
					</div>
					<div class="form-group col-sm-4">
						<input type="text" autocomplete="off" maxlength="10" placeholder="Date Created" class="input medium datepicker w95 form-control" name="datecreated" id="datecreated" title="Date created" value="<?php if(isset($_SESSION['tilt_sess_gameReport_created']) && $_SESSION['tilt_sess_gameReport_created'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_gameReport_created'])); else echo '';?>" onkeypress="return dateField(event);" ></div>
					</div>
				<div class="box-footer clear" align="center">
					<input type="submit"  class="btn btn-green" name="Search" id="Search" value="Search" title="Search">
				</div>
			</form>
		</div>	
		<div class="col-xs-12 no-padding">
				<?php if(isset($tournamentResult) && is_array($tournamentResult) && count($tournamentResult) > 0){ ?>
				No. of Tournament(s)&nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div class="clear" style="height: 20px"></div>
		<div id="pagingResult" class="clear" style="width: 100%">
			<form action="UserTournaments" class="l_form" name="TournamentPlayedUsersList" id="TournamentPlayedUsersList"  method="post"> 
				<input type="hidden" name="action_hidden" id="action_hidden" value="">
				<div class="table-responsive">
					<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-responsive <?php if(isset($tournamentResult) && is_array($tournamentResult) && count($tournamentResult) > 5) echo 'scroll'; ?>" width="100%">
						<tr>
						<th class="text-center" width="1%" class="text-center">#</th>
						<th align="left" width="15%">Tournament Name</th>
						<th class="text-center" width="5%">Status</th>
						<th class="text-center" width="3%">Date Created</th>
					</tr>
					<?php if(isset($tournamentResult) && is_array($tournamentResult) && count($tournamentResult) > 0 ) { 
							 foreach($tournamentResult as $key=>$value){
								$userName	=	'';
					 ?>									
					<tr id="test_id_<?php echo $value->id;?>">
						<td valign="top" class="text-center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td><?php if(isset($value->TournamentName) && $value->TournamentName != '') echo ucFirst($value->TournamentName); else echo '-'; ?> </td>
						<td valign="top" class="text-center">
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
										}
									} else echo '-';
							?>
						</td>
						<td valign="top" class="text-center">
						<?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00' ){
							echo date('m/d/Y',strtotime($value->DateCreated));
								}
							?>
						</td>	
					</tr>
					<?php } ?> 																		
				</table>
				<?php } else { ?>	
					<tr>
						<td colspan="16" align="center" class="error">No Tournament(s) Found</td>
					</tr>
					</table>
				<?php } ?>
				</div>
			</form>
		</div>
		<div class="col-xs-12 clear">
			<?php if(isset($tournamentResult) && is_array($tournamentResult) && count($tournamentResult) > 0 ) {
				pagingControlLatest($tot_rec,'UserTournaments'.$pagingParam); ?>
			<?php }?>
		</div><br>
		</section>
<?php commonFooter(); ?>
<script type="text/javascript">
$('#datecreated').datetimepicker({
	format:'m/d/Y',
	maxDate:'today',
	onShow:function( ct ){
		this.setOptions({
		})
	},
	timepicker:false,
});
var height = $('body').height();
parent.$('.fancybox-inner').height(height+40);
</script>
</html>
