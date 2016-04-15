<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/GameController.php');
$gameObj   =   new GameController();
developer_login_check();
$devId		=	$_SESSION['tilt_developer_id'];
$class		=	$stats = '';
$today		= date('Y-m-d H:i');
if(isset($_GET['cs']) && $_GET['cs']=='1' ) { // unset all session variables
	destroyPagingControlsVariables();
	unset($_SESSION['tilt_sess_report_tournament_name']);
	unset($_SESSION['tilt_sess_report_developer_name']);
	unset($_SESSION['tilt_sess_report_developer_type']);
}

if(isset($_POST['Search']) && $_POST['Search'] != ''){ // Handle search options
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);

	if(isset($_POST['tournament']))
		$_SESSION['tilt_sess_report_tournament_name'] 	=	trim($_POST['tournament']);
	if(isset($_POST['developer']))
		$_SESSION['tilt_sess_report_developer_name'] 	=	trim($_POST['developer']);
	if(isset($_POST['developertype']))
		$_SESSION['tilt_sess_report_developer_type']	=	$_POST['developertype'];
}
setPagingControlValues('t.id',ADMIN_PER_PAGE_LIMIT);
$gameName	=	'';
if(isset($_GET['gameId']) && $_GET['gameId'] !=''){
$fields				=	"t.fkGamesId,t.CreatedBy,t.TournamentName,t.DateCreated as tourDate,u.FirstName,u.LastName,u.Photo as userPhoto,gd.Company as Name,gd.Photo as devPhoto, t.StartDate, t.EndDate, t.TournamentStatus";
$condition			=	" AND t.fkGamesId =".$_GET['gameId']."  AND t.Status !=3 AND t.CreatedBy in (1,3)";
$tournamentList		=	$gameObj->getTournamentList($fields,$condition);
$stats				="?gameId=".$_GET['gameId'];
	if(isset($_GET['gameName']) && $_GET['gameName'] !=''){
		$stats		.= "&gameName=".$_GET['gameName'];
		$gameName	=	ucFirst($_GET['gameName']);
	}
}
$tot_rec 			= 	$gameObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($tournamentList)) {
	$tournamentList  = $gameObj->getTournamentList($fields,$condition);
}
commonHead();
?>
<body class="skin-black">
<?php top_header(); ?>
	<section class="content-header">
		<h2 align="center">Tournament List - <?php echo $gameName; ?></h2>
	</section>
   	<section class="content">
		<div class="row search_group">
			<form class="gameusage" method="post" action="GameUsageDetails<?php echo $stats;?>">
				<div class="box-body col-md-8 col-lg-5 box-center">
					<div class="form-group col-sm-4">
						<input  type="text" class="form-control" placeholder="Tournament Name" title="Tournament Name" name="tournament" id="tournament" value="<?php if(isset($_SESSION['tilt_sess_report_tournament_name']) && $_SESSION['tilt_sess_report_tournament_name'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_report_tournament_name']);?>">
					</div>
					<div class="form-group col-sm-4">
						<input  type="text" class="form-control" placeholder="Developer Name" title="Developer name" name="developer" id="Developer" value="<?php if(isset($_SESSION['tilt_sess_report_developer_name']) && $_SESSION['tilt_sess_report_developer_name'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_report_developer_name']);?>">
					</div>
					<div class="form-group col-sm-4">
						<select name="developertype" id="developer type"  class="form-control">
							<option value="">Select Developer Type</option>
							<?php
							foreach($developerType as $key => $value) { ?>
								<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['tilt_sess_report_developer_type']) && $_SESSION['tilt_sess_report_developer_type'] != '' && $_SESSION['tilt_sess_report_developer_type'] == $key) echo 'Selected';  ?>><?php echo $value; ?></option>
							<?php }?>
						</select>
					</div>
				</div> 
				<div class="box-footer clear" align="center">
					<input type="submit" class="btn btn-green" name="Search" id="Search" title="Search" value="Search">
					<input type="button" class="btn btn-green" name="Back" id="Back" value="Back" title="Back" onclick="location.href='GameUsageReport?cs=1'">
				</div>
			</form> 
		</div>
		<div class="clear" style="height: 20px;"></div>
		<div class="col-lg-2 col-sm-12">
				<?php if(isset($tournamentList) && is_array($tournamentList) && count($tournamentList) > 0){ ?>
				Total Tournament(s) &nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div class="col-lg-8 col-sm-12"><?php displayNotification('Tournament '); ?></div>
		<div class="clear" style="height: 20px;"></div>
		<div class="col-xs-12">
		<div class="table-responsive">
			<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-responsive" width="100%">
				<tr>
					<th align="center" width="3%">#</th>
					<th align="left" width="20%">Tournament Name</th>
					<th align="left" width="20%">Developer</th>
					<th align="left" width="15%">Developer Type</th>
					<th align="left" width="7%">Created Date</th>
					<th align="left" width="7%">Start Date</th>
					<th align="left" width="7%">End Date</th>
					<th align="left" width="7%">Status</th>
					</tr>
			<?php if(!empty($tournamentList)) {?>
				<?php 
						foreach($tournamentList as $key=>$value)	{ ?>
					<tr>
						<td width="" align="center" valign="">
							<?php if(isset($_SESSION['curpage'])	&&	$_SESSION['curpage'] !=''	&&	isset($_SESSION['perpage'])	&&	$_SESSION['perpage'] !='')echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?>
						</td>
						<td align="left" ><?php if(isset($value->TournamentName)	&&	$value->TournamentName!=''){	echo $value->TournamentName; } else { echo " - "; } ?></td>
							<?php if(!empty($value->FirstName) || !empty($value->LastName)) {?>
								<td align="left"><?php if(isset($value->FirstName)	&&	$value->FirstName!='' || isset($value->LastName)	&&	$value->LastName!=''){	echo $value->FirstName.' '.$value->LastName; } else { echo " - "; }?></td>
							<?php } else{?>
								<td align="left" ><?php if(isset($value->Name)	&&	$value->Name!=''){	echo $value->Name; } else { echo " - "; } ?></td>
							<?php } ?>
								<td align="left" ><?php if(isset($value->CreatedBy)	&&	$value->CreatedBy !=''){	
								 if($value->CreatedBy == 1) echo "User"; else echo "Developer & Brand";  }  ?></td>
								 <td align="left" ><?php if(isset($value->tourDate)	&&	$value->tourDate!=''){	echo date('m/d/Y',strtotime($value->tourDate)); } else { echo " - "; } ?></td>
								 <td  align="left">	<?php 	if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00')	{ echo date('m/d/Y',strtotime($value->StartDate)); 	} else echo '-';?></td>
								<td  align="left">	<?php 	if(isset($value->EndDate) && $value->EndDate != '0000-00-00 00:00:00')		{ echo date('m/d/Y',strtotime($value->EndDate)); 	} else echo '-';?></td>
								<td valign="top">
								<?php 	if(isset($value->TournamentStatus) && $value->TournamentStatus != ''){
											if(isset($value->EndDate) && $value->EndDate != '0000-00-00 00:00:00' &&	isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00' ){
												if($value->TournamentStatus==3)
													echo $tournamentStatus['3'];
												else if(date('Y-m-d H:i',strtotime($value->StartDate)) > $today	){
													echo $tournamentStatus['0'];
												}
												else if(date('Y-m-d H:i',strtotime($value->StartDate)) <= $today){
													echo $tournamentStatus['1'];
												}
											}
										} else echo '-';?>
								</td>
				</tr>
				<?php } ?>
		<?php }	else { ?>
			<tr><td colspan="9" align="center"><div class="error">No Result(s) Found</div></td></tr>
		<?php } ?>
		</table>
		</div>
	</div>
	<div class="col-xs-12 clear">
		<?php if(isset($tournamentList) && is_array($tournamentList) && count($tournamentList) > 0 ) {
			pagingControlLatest($tot_rec,'GameUsageDetails'.$stats); ?>
		<?php }?>
	</div>
	<div align="center" class="clear"></div>
</section>
<?php footerLinks(); commonFooter(); ?>
</html>