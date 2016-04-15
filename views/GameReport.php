<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/ReportController.php');
$reportObj  		=   new ReportController();
developer_login_check();
$developer_id = '';
if(isset($_GET['cs']) && $_GET['cs']=='1' ) { // unset all session variables
	destroyPagingControlsVariables();
	unset($_SESSION['tilt_sess_gameReport_Game']);
	unset($_SESSION['tilt_sess_gameReport_fromDate']);
	unset($_SESSION['tilt_sess_gameReport_endDate']);
}

if(isset($_SESSION['tilt_developer_id']) && $_SESSION['tilt_developer_id'] != ''){
	$developer_id		=	$_SESSION['tilt_developer_id'];
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){ // Handle search options
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['Game']))
		$_SESSION['tilt_sess_gameReport_Game'] 	=	trim($_POST['Game']);
	if(isset($_POST['from_date']) && $_POST['from_date'] != ''){
		$validate_date = dateValidation($_POST['from_date']);
		if($validate_date == 1)
			$_SESSION['tilt_sess_gameReport_fromDate']	= $_POST['from_date'];	//$date;
		else 
			$_SESSION['tilt_sess_gameReport_fromDate']	= '';
	}
	else 
		$_SESSION['tilt_sess_gameReport_fromDate']	= '';

	if(isset($_POST['to_date']) && $_POST['to_date'] != ''){
		$validate_date = dateValidation($_POST['to_date']);
		if($validate_date == 1)
			$_SESSION['tilt_sess_gameReport_endDate']	= $_POST['to_date'];	//$date;
		else 
			$_SESSION['tilt_sess_gameReport_endDate']	= '';
	}
	else 
		$_SESSION['tilt_sess_gameReport_endDate']	= '';
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$tot_rec	=	$searchUserIds	=	'';
$ids					=	"";
$condition 				= 	" t.fkDevelopersId = '".$developer_id."' AND t.id !=''  AND g.Status != 3 AND t.Status = 1 ";
$fields = " t.id,t.fkGamesId,t.fkBrandsId,g.Name, t.id as tourId,g.id as gameId, 
				count( DISTINCT t.id) AS tourCount,
				count(distinct (CASE WHEN a.ActionType = 2 THEN a.fkUsersId END)) as playersCount,
				count(distinct (CASE WHEN a.ActionType = 3 THEN a.fkUsersId END)) as winnerCount";
$gameListResult  		= 	$reportObj->getGameReportList($fields,$condition);
$tot_rec				=	$reportObj->getTotalRecordCount();
$playerCountArr = array();
commonHead();
?>
<style>
.fancybox-inner{overflow: auto !important;height: 470px !important} 
</style>
<body  class="skin-black">
	<?php top_header(); ?>
	
	<section class="content-header">
		<h2 align="center">Game Report</h2>
	</section>
   	<section class="content col-md-12 box-center develop_page">
		<div style="height: 20px" class="clear"></div>
		<div class="search_group">
			<form class="tournament" method="post" action="GameReport">
				<div class="box-body col-md-7 box-center">
					<div class="form-group col-sm-4">
						<input  type="text" class="form-control" placeholder="Game" title="Game name" name="Game" id="Game" value="<?php if(isset($_SESSION['tilt_sess_gameReport_Game']) && $_SESSION['tilt_sess_gameReport_Game'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_gameReport_Game']);?>">
					</div>
					<div class="form-group col-sm-4">
						<input  type="text" class="form-control" placeholder="Start Date" autocomplete="off"  title="Select Date" id="startdate" name="from_date" value="<?php if(isset($_SESSION['tilt_sess_gameReport_fromDate']) && $_SESSION['tilt_sess_gameReport_fromDate'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_gameReport_fromDate']));?>" onkeypress="return dateField(event);" >
					</div>
					<div class="form-group col-sm-4">
						<input type="text" class="form-control" autocomplete="off"  placeholder="End Date" title="Select Date" id="enddate" name="to_date" value="<?php if(isset($_SESSION['tilt_sess_gameReport_endDate']) && $_SESSION['tilt_sess_gameReport_endDate'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_gameReport_endDate']));?>" onkeypress="return dateField(event);" >
					</div>
				</div>
				<div class="box-footer clear" align="center">
					<input type="submit"  class="btn btn-green" name="Search" id="Search" value="Search" title="Search">
				</div>
			</form>
		</div>
		<div style="height: 20px" class="clear"></div>
		<div class="col-xs-12 no-padding">
				<?php if(isset($gameListResult) && is_array($gameListResult) && count($gameListResult) > 0){ ?>
				Total Game(s)&nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div style="height: 20px" class="clear"></div>
	<div id="pagingResult" class="clear" style="width: 100%">
		
		<form name="list_header" id="game_list_frm" action="" method="">
			<input type="hidden" name="action_hidden" id="action_hidden" value="">
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-responsive" width="100%">
					<tr>
						<th class="text-center" width="1%">#</th>
						<th align="left" width="13%">Game</th>
						<th class="text-center" width="8%">Total Tournaments</th>
						<th class="text-center" width="8%">Total No. of Players</th>
						<th class="text-center" width="8%">Total No. of Winners</th>
					</tr>
					<tr>
					<?php if(!empty($gameListResult)) { 										
							foreach($gameListResult as $key=>$value)	{ 
								$playersCount = 0;
								if(isset($playerCountArr[$value->gameId]))
									$playersCount = $playerCountArr[$value->gameId];
							?>
							<td width="" class="text-center" valign="">
								<?php if(isset($_SESSION['curpage'])	&&	$_SESSION['curpage'] !=''	&&	isset($_SESSION['perpage'])	&&	$_SESSION['perpage'] !='')echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?>
							</td>
							<td align="left"><?php 	if(isset($value->Name) && $value->Name != '') echo ucFirst($value->Name); else { echo " - "; } ?></td>
							<td class="text-center"><?php 	if(isset($value->tourCount) && $value->tourCount != '')					{ echo '<a href="GameTournaments?cs=1&type=2&viewId='.$value->fkGamesId.'&gameName='.$value->Name.'" class="tournaments" title="Tournament List"><i class="fa fa-trophy"></i> '.number_format($value->tourCount).'</a>'; } else echo '0';?></td>
							<td class="text-center"><?php 	if(isset($value->playersCount) && $value->playersCount > 0){
												echo '<a href="GamePlayers?cs=1&viewId='.$value->fkGamesId.'&gameName='.$value->Name.'" class="tournaments" title="Game Players" ><i class="fa fa-user"></i> '.number_format($value->playersCount).'</a>'; } else echo '0';
							?></td> 
							<td class="text-center"><?php 	if(isset($value->winnerCount) && $value->winnerCount != 0) echo "<a href='Winnerslist?cs=1&id=".$value->fkGamesId."&gameName=".$value->Name."' class='tournaments' title='Game Winners'><i class='fa fa-user'></i> ". number_format($value->winnerCount)."</a>";  else echo '0';?></td>
						</tr>
					<?php } 
					}
					else { ?>
						<tr><td align="center" colspan="13" class="error">No Game(s) Found</td></tr>
					<?php } ?>
			</table>
		</div>
		</form>
		</div>
		<div class="col-xs-12 clear">
			<?php if(isset($gameListResult) && is_array($gameListResult) && count($gameListResult) > 0 ) {
				pagingControlLatest($tot_rec,'GameReport'); ?>
			<?php }?>
		</div><br>
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
		'height': '300',
		'maxWidth': '100%', 
		'type': 'iframe'
	 });
});
</script>
</html>
