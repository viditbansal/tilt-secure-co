<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/TournamentController.php');
$tourObj   =   new TournamentController();
require_once('controllers/GameController.php');
$gameObj   =   new GameController();
developer_login_check();
$devId		=	$_SESSION['tilt_developer_id'];
$class		=	$stats ='';

if(isset($_GET['cs']) && $_GET['cs']=='1' ) { // unset all session variables
	destroyPagingControlsVariables();
	unset($_SESSION['tilt_sess_tournament_name']);
	unset($_SESSION['tilt_sess_tournament_fromDate']);
	unset($_SESSION['tilt_sess_tournament_endDate']);
	unset($_SESSION['tilt_sess_tournament_game']);
}
if(isset($_GET['delId']) && !empty($_GET['delId'])){
	$delete_id      = $_GET['delId'];
	$tourObj->deleteTournaments($delete_id);
	$_SESSION['notification_msg_code']	=	3;
	header("location:TournamentList");
	die();
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){ // Handle search options
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['tournament']))
		$_SESSION['tilt_sess_tournament_name'] 	=	trim($_POST['tournament']);
	if(isset($_POST['game']))
		$_SESSION['tilt_sess_tournament_game'] 	=	$_POST['game'];
	if(isset($_POST['brand']))
		$_SESSION['mgc_sess_brand']	    	=	$_POST['brand'];
	if(isset($_POST['from_date']) && $_POST['from_date'] != ''){
		$validate_date = dateValidation($_POST['from_date']);
		if($validate_date == 1)
			$_SESSION['tilt_sess_tournament_fromDate']	= $_POST['from_date'];	//$date;
		else 
			$_SESSION['tilt_sess_tournament_fromDate']	= '';
	}
	else 
		$_SESSION['tilt_sess_tournament_fromDate']	= '';

	if(isset($_POST['to_date']) && $_POST['to_date'] != ''){
		$validate_date = dateValidation($_POST['to_date']);
		if($validate_date == 1)
			$_SESSION['tilt_sess_tournament_endDate']	= $_POST['to_date'];	//$date;
		else 
			$_SESSION['tilt_sess_tournament_endDate']	= '';
	}
	else 
		$_SESSION['tilt_sess_tournament_endDate']	= '';
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields		=	"t.id,g.Name as gameName,t.TournamentName,t.TournamentType,t.Prize,t.MaxPlayers,t.MinPlayers,t.TotalTurns,t.CurrentHighestScore,t.StartDate,t.EndDate, 
					t.TournamentStatus,t.Type,g.Status as gameStatus,t.GameType";
$condition	=	" AND t.fkDevelopersId = ".$devId." AND t.Status != '3' ";
$tournamentListResult =	$tourObj->getTournamentList($fields,$condition);
$tot_rec 		= $tourObj->getTotalRecordCount();
$today			= date('Y-m-d H:i');
$fields		=	"*";
$condition	=	" ";
$gameList	=	$gameObj->getTourGameList($fields,$condition);
$elimTournaments	=	$countDetails	=	array();

if(isset($tournamentListResult)	&&	is_array($tournamentListResult)	&&	count($tournamentListResult) > 0){
	$ids	=	$hids	=	$eids	=	'';
	foreach($tournamentListResult as $key =>$value){	
		$ids	.= $value->id.',';	
		if($value->GameType == 2)
			$eids	.= $value->id.',';	
		else
			$hids	.= $value->id.',';	
	}
	$ids	=	rtrim($ids,',');
	$hids	=	rtrim($hids,',');
	$eids	=	rtrim($eids,',');
	if($ids != ''){
		//Restrict edit Option
		if($hids != ''){
			$fields		=	" `TournamentHighScore`,`fkTournamentsId`,EndTime,count(distinct fkUsersId) as playersCount ";
			$condition	=	" fkTournamentsId IN(".$hids.") GROUP BY fkTournamentsId ";
			$eliminationTourResult	=	$tourObj->getTournamentPlayed($fields,$condition);
			if(isset($eliminationTourResult) && is_array($eliminationTourResult) && count($eliminationTourResult)>0){
				foreach($eliminationTourResult as $tourKey =>$tourValue){
					$elimTournaments[]	=	$tourValue->fkTournamentsId;
					$countDetails[$tourValue->fkTournamentsId] = array('HighScore'=>$tourValue->TournamentHighScore,'playersCount'=>$tourValue->playersCount);
					
				}
			}
		}
		if($eids != ''){
			$fields		=	" Count(Distinct ep.fkUsersId) as playercount, max(RoundTurn) as maxRound, tp.`fkTournamentsId`,tp.`TournamentHighScore`";
			$condition	=	" AND tp.`fkTournamentsId` IN(".$eids.") AND ep.fkTournamentsPlayedId !='' ";
			$eliTourResult	=	$tourObj->getEliminationPlayed($fields,$condition);
			if(isset($eliTourResult) && is_array($eliTourResult) && count($eliTourResult)>0){
				foreach($eliTourResult as $tourKey =>$tourValue){
					$turns	=	'';
					if(isset($tourValue->maxRound) && !empty($tourValue->maxRound) ) $turns	=	$tourValue->maxRound;
					$countDetails[$tourValue->fkTournamentsId] = array('HighScore'=>$tourValue->TournamentHighScore,'playersCount'=>$tourValue->playercount,'turns'=>$turns);
				}
			}
		}
	}
}
commonHead();
?>
<body class="skin-black">
<?php top_header(); ?>
	<section class="content-header">
		<h2 align="center">Search Tournament</h2>
	</section>
   	<section class="content">
		<div class="search_group">
			<form class="tournament" method="post" action="TournamentList">
				<div class="box-body col-md-5 box-center">
					<div class="form-group col-sm-7">
						<input  type="text" class="form-control" placeholder="Tournament Name" title="Tournament Name" name="tournament" id="tournament" maxlength="50" value="<?php if(isset($_SESSION['tilt_sess_tournament_name']) && $_SESSION['tilt_sess_tournament_name'] != '') echo $_SESSION['tilt_sess_tournament_name'];?>">
					</div>
					<div class="form-group col-sm-5">
						<input  type="text" class="form-control" placeholder="Start Date" onKeyPress="return dateField(event);" autocomplete="off"  title="Start Date" id="startdate" name="from_date" value="<?php if(isset($_SESSION['tilt_sess_tournament_fromDate']) && $_SESSION['tilt_sess_tournament_fromDate'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_tournament_fromDate']));?>">
					</div>
					<div class="form-group col-sm-7">
						<select name="game" id="game" class="form-control">
							<option value="">Select Game</option>
							<?php if(isset($gameList) && is_array($gameList) && count($gameList) > 0){
									foreach($gameList as $key => $value) { if(trim($value->Name) != '') { ?>
										<option value="<?php echo $value->Name; ?>" <?php if(isset($_SESSION['tilt_sess_tournament_game']) && $_SESSION['tilt_sess_tournament_game'] != ''	&&	$_SESSION['tilt_sess_tournament_game'] == $value->Name) echo 'selected'; ?>><?php echo $value->Name; ?></option>
									<?php } }
								}  ?>
						</select>
					</div>
					<div class="form-group col-sm-5">
						<input type="text" class="form-control" autocomplete="off"  onkeypress="return dateField(event);" placeholder="End Date" title="End Date" id="enddate" name="to_date" value="<?php if(isset($_SESSION['tilt_sess_tournament_endDate']) && $_SESSION['tilt_sess_tournament_endDate'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_tournament_endDate']));?>">
					</div>
				</div>
				<div class="box-footer clear" align="center">
					<input type="submit"  class="btn btn-green" name="Search" id="Search" title="Search" value="Search">
				</div>
			</form>
		</div>
		<div class="clear" style="height: 20px"></div>
		<div class="col-lg-3 col-sm-12">
				<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0){ ?>
				Total Tournament(s) &nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div class="col-lg-8 col-sm-12"><?php displayNotification('Tournament '); ?></div>
		<div class="clear" style="height: 20px"></div>
		<div class="col-xs-12">
		<div class="table-responsive">
			
			<table cellpadding="0" cellspacing="0" align="center" border="0" class="table table-striped table-responsive" width="100%">
				<tr>
					<th align="center" width="2%">#</th>
					<th align="left" width="15%">Tournament Name</th>
					<th align="left" width="10%"><?php echo SortColumn('Name','Game'); ?></th>
					<th align="left" width="6%">TiLT$</th>
					<th align="left" width="9%">Virtual Coins</th>
					<th align="center" width="9%">Prize</th>
					<th align="center" width="3%">Players</th>
					<th align="left" width="6%"><?php echo SortColumn('MaxPlayers','Max Players'); ?></th>
					<th align="center" width="9%">No. of Turns / Rounds</th>
					<th align="center" width="6%">Highest Score</th>
					<th align="left" width="8%"><?php echo SortColumn('StartDate','Start Date'); ?></th>
					<th align="left" width="8%"><?php echo SortColumn('EndDate','End Date'); ?></th>
					<th align="left" width="6%" ><?php echo SortColumn('TournamentStatus','Status'); ?></th>
					<th align="left" style="text-align:center" >Action</th>
				</tr>
				<tr>
				<?php if(!empty($tournamentListResult)) {
						foreach($tournamentListResult as $key=>$value)	{	?>
						<td width="" align="center" valign="">
							<?php if(isset($_SESSION['curpage'])	&&	$_SESSION['curpage'] !=''	&&	isset($_SESSION['perpage'])	&&	$_SESSION['perpage'] !='')echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?>
						</td>
						<td align="left" ><?php if(isset($value->TournamentName)	&&	$value->TournamentName!=''){	echo $value->TournamentName; } else { echo " - "; } ?></td>
						<td align="left"><?php if(isset($value->gameName)	&&	$value->gameName!=''){	echo ucFirst($value->gameName); } else { echo " - "; } ?></td>
						<td  align="left">	<?php  	if(isset($value->Type) && $value->Type == 2 && isset($value->Prize) && $value->Prize != 0)			{ echo number_format($value->Prize); } else echo '-';?></td>
						<td  align="left">	<?php  	if(isset($value->Type) && ($value->Type == 3) && isset($value->Prize) && $value->Prize != 0)		{ echo number_format($value->Prize); } else echo '-';?></td>
						<td  align="left">
							<?php  	if(isset($value->Type)) { echo ( $value->Type == 3 && isset($value->Prize) && $value->Prize != '') ? $value->Prize." Virtual Coins" : (($value->Type == 4) ? "Custom"  : (($value->Type == 2 && isset($value->Prize) && $value->Prize != '') ? $value->Prize." TiLT$" : '-')) ; } else echo '-';?> 
						</td>
						<td  align="center">
							<?php 	if(isset($countDetails[$value->id]) && !empty($countDetails[$value->id]['playersCount']) && $countDetails[$value->id]['playersCount'] !=0){ ?>
							<?php echo $countDetails[$value->id]['playersCount'];?>
							<?php  } else echo '-';?></p>
						</td>
						<td  align="left"><?php 	if(isset($value->MaxPlayers) && $value->MaxPlayers != '')					{ echo number_format($value->MaxPlayers); 			} else echo '0';?></td>
						<td  align="left">
						<?php echo (isset($value->GameType) && $value->GameType == 2) ? ( isset($countDetails[$value->id]) && !empty($countDetails[$value->id]['turns']) ? 
						($countDetails[$value->id]['turns'] > 1 ? $countDetails[$value->id]['turns'].' Rounds' : $countDetails[$value->id]['turns'].' Round' )
						:(($value->MaxPlayers - 1) > 1 ? $value->MaxPlayers - 1 .' Rounds' : $value->MaxPlayers - 1 .' Round')) : ( isset($value->TotalTurns) && $value->TotalTurns > 0 ? ($value->TotalTurns > 1 ? $value->TotalTurns.' Turns' : $value->TotalTurns.' Turn') : '0'.' Turn' );?>
						</td>
						<td  align="left"><?php 	if(isset($value->CurrentHighestScore) && $value->CurrentHighestScore != '')	{ echo number_format($value->CurrentHighestScore); 	} else echo '0';?></td>
						<td  align="left">	<?php 	if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00' && $value->StartDate != '2038-01-01 03:14:07')	{ echo date('m/d/Y',strtotime($value->StartDate)); 	} else echo '-';?></td>
						<td  align="left">	<?php 	if(isset($value->EndDate) && $value->EndDate != '0000-00-00 00:00:00' && $value->EndDate != '2038-01-02 03:14:07')		{ echo date('m/d/Y',strtotime($value->EndDate)); 	} else echo '-';?></td>
							<td valign="top">
					<?php 	if(isset($value->TournamentStatus) && $value->TournamentStatus != ''){
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
							} else echo '-';?>
						</td>
						<td align="left">
						<?php
						if($value->gameStatus!=3 && isset($value->TournamentStatus) && $value->TournamentStatus != 3 && isset($value->TournamentType) && $value->TournamentType==2) 
						{
							if(isset($value->StartDate) && date('Y-m-d H:i',strtotime($value->StartDate)) == '2038-01-01 03:14' ){
								echo '<div style="text-align: center"><a href="TournamentStartEnd?tType=start&tId='.$value->id.'" title="Start">Start</a></div>'; 
							}
							if(isset($value->StartDate) && date('Y-m-d H:i',strtotime($value->StartDate)) != '2038-01-01 03:14' && isset($value->EndDate) && date('Y-m-d H:i',strtotime($value->EndDate)) == '2038-01-02 03:14'){
								echo '<div style="text-align: center"><a href="TournamentStartEnd?tType=end&tId='.$value->id.'" title="End">End</a></div>'; 
							}
						}
						$editStatus	=	1;
						if($value->gameStatus  == 3)												$editStatus	=	0;
						else if(isset($value->TournamentStatus)	&&	$value->TournamentStatus == 3)	$editStatus	=	0;
						else if(in_array($value->id,$elimTournaments))								$editStatus	=	0;
						else if(date('Y-m-d H:i',strtotime($value->StartDate)) <= $today && $value->StartDate != '0000-00-00 00:00:00')			$editStatus	=	0;
						if($editStatus)	echo '<div style="text-align: center"><a href="TournamentManage?editId='.$value->id.'" title="Edit"><i class="fa fa-pencil"></i></a></div>'; 
						else	echo '<div style="text-align: center"><a href="javascript:void(0)"  title="Edit"><i class="fa fa-pencil" style="color:gray;cursor:default" ></i></a></div>'; ?>
						</td>
					</tr>
				<?php } 
				}	else { ?>
						<tr><td class="error" align="center" colspan="15">No Tournament(s) Found</td></tr>
				<?php } ?>
		</table>
		
		
	</div>
	</div>
	<div class="col-xs-12 clear">
		<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0 ) {
			pagingControlLatest($tot_rec,'TournamentList'); ?>
		<?php }?>
	</div>
	<div class="clear" align="center"><br><a href="TournamentManage" class="btn btn-green" title="Create Tournament">Create Tournament</a></div>
</section>

	

<?php footerLinks(); commonFooter(); ?>
<script>
 $(function(){
	$('#startdate').datetimepicker({
  		format:'m/d/Y',
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
});
</script>
</html>