<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/ReportController.php');
$reportObj  		=   new ReportController();
developer_login_check();
$developer_id = '';
if(isset($_GET['cs']) && $_GET['cs']=='1' ) { // unset all session variables
	destroyPagingControlsVariables();
	unset($_SESSION['tilt_sess_tourReport_tournament']);
	unset($_SESSION['tilt_sess_tourReport_fromDate']);
	unset($_SESSION['tilt_sess_tourReport_endDate']);
}
if(isset($_SESSION['tilt_developer_id']) && $_SESSION['tilt_developer_id'] != ''){
	$developer_id		=	$_SESSION['tilt_developer_id'];
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){ // Handle search options
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['Tournament']))
		$_SESSION['tilt_sess_tourReport_tournament'] 	=	trim($_POST['Tournament']);
	if(isset($_POST['from_date']) && $_POST['from_date'] != ''){
		$validate_date = dateValidation($_POST['from_date']);
		if($validate_date == 1)
			$_SESSION['tilt_sess_tourReport_fromDate']	= $_POST['from_date'];	//$date;
		else 
			$_SESSION['tilt_sess_tourReport_fromDate']	= '';
	}
	else 
		$_SESSION['tilt_sess_tourReport_fromDate']	= '';

	if(isset($_POST['to_date']) && $_POST['to_date'] != ''){
		$validate_date = dateValidation($_POST['to_date']);
		if($validate_date == 1)
			$_SESSION['tilt_sess_tourReport_endDate']	= $_POST['to_date'];	//$date;
		else 
			$_SESSION['tilt_sess_tourReport_endDate']	= '';
	}
	else 
		$_SESSION['tilt_sess_tourReport_endDate']	= '';
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$tot_rec	=	$searchUserIds	=	'';
$ids					=	"";
$fields   				= 	" t.id,t.TournamentName,gd.Name,t.PlayersJoined,t.id as tId,tp.id as fktpId,tp.fkTournamentsId as tpId,tp.fkUsersId,t.fkDevelopersId,
								count(Distinct tp.fkUsersId) as userCount,count(tp.id) as turnsCount, count( DISTINCT ts.fkUsersId ) AS winsCount,
								t.GameType,tp.id as fkTournamentsPlayedId,t.Type,count(DISTINCT ep.fkUsersId) as euserCount,ep.fkTournamentsPlayedId,max(RoundTurn) as eturns";
$condition 				= 	" fkDevelopersId = '".$developer_id."' AND t.Status = 1 AND tp.fkTournamentsId !=0 AND ((ep.fkUsersId !='') OR (tp.fkUsersId !=0)) ";//
$tournamentListResult  	= 	$reportObj->getTournamentReportList($fields,$condition);
$tot_rec				=	$reportObj->getTotalRecordCount();
$elimCountDetails	=	array();
commonHead();

?>
<style>
.fancybox-inner{overflow: auto !important;height: 470px !important} 
</style>
<body  class="skin-black">
	<?php top_header(); ?>
	
	<section class="content-header">
		<h2 align="center">Tournament Report</h2>
	</section>
   	<section class="content col-md-12 box-center develop_page">
		<div class="search_group">
			<form class="tournament" method="post" action="">
				<div class="box-body col-md-7 box-center">
					<div class="form-group col-sm-4">
						<input  type="text" class="form-control" placeholder="Tournament" title="Tournament name" name="Tournament" id="Tournament" value="<?php if(isset($_SESSION['tilt_sess_tourReport_tournament']) && $_SESSION['tilt_sess_tourReport_tournament'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_tourReport_tournament']);?>">
					</div>
					<div class="form-group col-sm-4">
						<input  type="text" class="form-control" placeholder="Start Date" autocomplete="off"  title="Select Date" id="startdate" name="from_date" value="<?php if(isset($_SESSION['tilt_sess_tourReport_fromDate']) && $_SESSION['tilt_sess_tourReport_fromDate'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_tourReport_fromDate']));?>" onkeypress="return dateField(event);" >
					</div>
					<div class="form-group col-sm-4">
						<input type="text" class="form-control" autocomplete="off"  placeholder="End Date" title="Select Date" id="enddate" name="to_date" value="<?php if(isset($_SESSION['tilt_sess_tourReport_endDate']) && $_SESSION['tilt_sess_tourReport_endDate'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_tourReport_endDate']));?>" onkeypress="return dateField(event);" >
					</div>
				</div>
				<div class="box-footer clear" align="center">
					<input type="submit"  class="btn btn-green" name="Search" id="Search" value="Search" title="Search">
				</div>
			</form>
		</div>
		<div style="height: 20px" class="clear"></div>
		<div class="col-xs-12 no-padding">
				<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0){ ?>
				Total Tournament(s)&nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div style="height: 20px" class="clear"></div>
	<div id="pagingResult" class="clear" style="width: 100%">
		
		<form name="list_header" id="game_list_frm" action="" method="">
			<input type="hidden" name="action_hidden" id="action_hidden" value="">
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-responsive" width="100%">
					<tr>
						<th class="text-center" width="2%">#</th>
						<th align="left" width="30%">Tournament Name</th>
						<th class="text-center" width="12%">Total No. of Players</th>
						<th class="text-center" width="12%">Total No. of Turns / Rounds</th>
						<th class="text-center" width="16%">Total No. of Winners</th>
					</tr>
					<tr>
					<?php if(!empty($tournamentListResult)) {
							foreach($tournamentListResult as $key=>$value)	{	
								$playersCount	=	0;
								$turnsCount	=	0;
								$winCount	=	0;
								$custom = $elimination = '';
								$playCount = 0;
								if(isset($value->Type) && $value->Type == 4) $custom = '&custom=1';	
								if(isset($value->GameType) && $value->GameType == 2){
									$elimination = '&elimination=1';
									if(isset($value->fkTournamentsPlayedId))
										$elimination .= '&playedId='.$value->fkTournamentsPlayedId;
									 if(isset($value->euserCount) && $value->euserCount > 0) 
									$playersCount 	= '<a href="TournamentPlayers?cs=1&elimination=1&viewId='.$value->fkTournamentsPlayedId.'&tournamentName='.$value->TournamentName.'" class="tournaments" title="Tournament Players"><i class="fa fa-user"></i> '.number_format($value->euserCount).'</a>';
									if(isset($value->eturns) && $value->eturns > 0) 	 $turnsCount 	= number_format($value->eturns);
								}else{
									if(isset($value->turnsCount) && $value->turnsCount > 0){ $turnsCount = number_format($value->turnsCount);};
									if(isset($value->userCount) && $value->userCount >0){ $playersCount = '<a href="TournamentPlayers?cs=1&type=1&viewId='.$value->tId.'&tournamentName='.$value->TournamentName.'" class="tournaments" title="Tournament Players"><i class="fa fa-user"></i> '.number_format($value->userCount).'</a>';}
								}
								if(isset($value->winsCount) && $value->winsCount >0)
									$winCount = '<a href="TournamentWinners?cs=1&type=1&viewId='.$value->tId.'&tournamentName='.$value->TournamentName.$elimination.$custom.'" class="tournaments" title="Tournament Winners"><i class="fa fa-trophy"></i> '.number_format($value->winsCount).'</a>';
							?>
							<td width="" class="text-center" valign="">
								<?php if(isset($_SESSION['curpage'])	&&	$_SESSION['curpage'] !=''	&&	isset($_SESSION['perpage'])	&&	$_SESSION['perpage'] !='')echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?>
							</td>
							<td  align="left"><?php if(isset($value->TournamentName) && $value->TournamentName != ''){ echo ucFirst($value->TournamentName);} else echo '-';?></td>
							<td class="text-center"><?php  echo $playersCount;	?></td>
							<td class="" style="text-align:right;padding-right:5%"><?php echo (isset($value->GameType) && $value->GameType == 2) ? ($turnsCount > 1 ? $turnsCount.' Rounds' : $turnsCount.' Round') : ($turnsCount > 1 ? $turnsCount.' Turns' : $turnsCount.' Turn');	?></td>
							<td class="text-center"><?php echo $winCount;?></td>
						</tr>
					<?php } 
					}
					else { ?>
						<tr><td align="center" colspan="5" class="error">No Tournament(s) Found</td></tr>
					<?php } ?>
			</table>
			
		</div>

		</form>
		</div>
				<div class="col-xs-12 clear">
			<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0 ) {
				pagingControlLatest($tot_rec,'TournamentReport'); ?>
			<?php }?>
		</div>
		<br />
	</section>
	<?php   footerLinks(); commonFooter(); ?>
<script>
 $(function(){
	$('#startdate').datetimepicker({
  		format:'m/d/Y',
  		maxDate:'today',
  		onShow:function( ct ){
   			this.setOptions({
   			})
   		},
		timepicker:false,
 	});
	var logic = function( currentDateTime ){
		var starting_time	=	$('#startdate').val();
		var ending_time		=	$('#enddate').val();
		start_dArr = starting_time.split(" ");  
		start_DateArr = start_dArr[0];
		start_TimeArr = start_dArr[1];
		end_dArr = ending_time.split(" ");  
		end_DateArr = end_dArr[0];
		end_TimeArr = end_dArr[1];
		if(start_DateArr == end_DateArr){
			 tme_new		=	start_TimeArr;
		}
		else
			 tme_new		=	false;
		if(start_DateArr != '' && start_TimeArr != ''){
			this.setOptions({
				minDate:start_DateArr,
				minTime:tme_new
			});
		}else
			this.setOptions({
				Date:start_DateArr
			});
	};
	$('#enddate').datetimepicker({
		format:'m/d/Y',
		onChangeDateTime:logic,
		onShow:logic,
		timepicker:false,
	});
	$('.tournaments').fancybox({
		'width': '850',
		'height': '340',
		'type': 'iframe'
	 });
});
</script>
</html>
