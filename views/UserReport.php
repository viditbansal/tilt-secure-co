<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/ReportController.php');
$reportObj  		=   new ReportController();
developer_login_check();
$developer_id = '';
if(isset($_GET['cs']) && $_GET['cs']=='1' ) { // unset all session variables
	destroyPagingControlsVariables();
	unset($_SESSION['tilt_sess_userReport_user']);
	unset($_SESSION['tilt_sess_userReport_fromDate']);
	unset($_SESSION['tilt_sess_userReport_endDate']);
}
if(isset($_SESSION['tilt_developer_id']) && $_SESSION['tilt_developer_id'] != ''){
	$developer_id		=	$_SESSION['tilt_developer_id'];
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){ // Handle search options
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['username']))
		$_SESSION['tilt_sess_userReport_user'] 	=	trim($_POST['username']);
	if(isset($_POST['from_date']) && $_POST['from_date'] != ''){
		$validate_date = dateValidation($_POST['from_date']);
		if($validate_date == 1)
			$_SESSION['tilt_sess_userReport_fromDate']	= $_POST['from_date'];	//$date;
		else 
			$_SESSION['tilt_sess_userReport_fromDate']	= '';
	}
	else 
		$_SESSION['tilt_sess_userReport_fromDate']	= '';

	if(isset($_POST['to_date']) && $_POST['to_date'] != ''){
		$validate_date = dateValidation($_POST['to_date']);
		if($validate_date == 1)
			$_SESSION['tilt_sess_userReport_endDate']	= $_POST['to_date'];	//$date;
		else 
			$_SESSION['tilt_sess_userReport_endDate']	= '';
	}
	else 
		$_SESSION['tilt_sess_userReport_endDate']	= '';
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$tot_rec	=	$searchUserIds	=	'';
$ids					=	"";
$fields   				= 	" a.id,t.id as tId,count( DISTINCT (case when ActionType = 2 then t.id end)) as tourCount,u.id as uId,a.fkUsersId,a.fkActionId,
								t.TournamentName,t.fkDevelopersId,u.FirstName,u.LastName,u.Email,u.UniqueUserId, 
								count(distinct (CASE WHEN ActionType = 3 THEN t.id END)) as wintour,
								count(distinct (CASE WHEN ActionType = 4 THEN t.id END)) as losstour ";
$condition 				=  " t.fkDevelopersId = '".$developer_id."' AND t.Status = 1 AND a.fkUsersId > 0 ";
$userListResult  		= 	$reportObj->getDeveloperUsersReport($fields,$condition);
$tot_rec				=	$reportObj->getTotalRecordCount();
$userCoinsArray	=	array();
$userWinLossArray	=	array();
if(isset($userListResult)	&&	is_array($userListResult)	&&	count($userListResult) > 0){
	foreach($userListResult as $key =>$value){	$ids	.= $value->fkUsersId.',';	}
	$ids	=	rtrim($ids,',');
	if($ids != ''){
		// coins calculation
		$fields   				= 	" * ";
		$condition 				= 	" fkUsersId IN(".$ids.")  AND Type IN(2,3) ";//Type : 2- win,3-joined(Played), CoinType : 1-Tilt,2-virtual
		$condition 				.= 	" AND BrandDeveloperId = ".$developer_id." AND PurchasedBy = 2 ";
		$coinsCount  	= 	$reportObj->getUserCoinsCount($fields,$condition);
		if(isset($coinsCount)	&&	is_array($coinsCount)	&& count($coinsCount)>0){
			foreach($coinsCount as $key1 =>$value1){
				if(array_key_exists($value1->fkUsersId,$userCoinsArray)){
					if($value1->Type==3){
						if($value1->CoinType==1){
							$userCoinsArray[$value1->fkUsersId]['TCP'] += $value1->Coins;
						}
						else if($value1->CoinType==2){
							$userCoinsArray[$value1->fkUsersId]['VCP'] += $value1->Coins;
						}
					}
					else if($value1->Type==2 ){
						if($value1->CoinType==1){
							$userCoinsArray[$value1->fkUsersId]['TCG'] += $value1->Coins;
						}
						else if($value1->CoinType==2){
							$userCoinsArray[$value1->fkUsersId]['VCG'] += $value1->Coins;
						}
					}
				}
				else {
					$tiltCoinGain	=	$tiltCoinPlayed	=	$virtCoinGain	=	$virtCoinPlayed	=	0;
					if($value1->Type==3){
						if($value1->CoinType==1){
							$tiltCoinPlayed	=	$value1->Coins;
						}
						else if($value1->CoinType==2){
							$virtCoinPlayed	=	$value1->Coins;
						}
					}
					else if($value1->Type==2 ){
						if($value1->CoinType==1){
							$tiltCoinGain	=	$value1->Coins;
						}
						else if($value1->CoinType==2){
							$virtCoinGain	=	$value1->Coins;
						}
					}
					$userCoinsArray[$value1->fkUsersId]	=	array('TCG'=>$tiltCoinGain,'TCP'=>$tiltCoinPlayed,'VCG'=>$virtCoinGain,'VCP'=>$virtCoinPlayed);
				}
			}
		}
	}
}
commonHead();
?>
<style>
.fancybox-inner{overflow: auto !important;height: 470px !important} 
</style>
<body  class="skin-black" style="">
	<?php top_header(); ?>
	
	<section class="content-header">
		<h2 align="center">User Report</h2>
	</section>
   	<section class="content col-md-12 box-center develop_page">
		<div style="height: 20px" class="clear"></div>
		<div class="search_group">
			<form class="tournament" method="post" action="UserReport">
				<div class="box-body col-md-7 box-center">
					<div class="form-group col-sm-4">
						<input  type="text" class="form-control" placeholder="User Name" title="User Name" name="username" id="username" value="<?php if(isset($_SESSION['tilt_sess_userReport_user']) && $_SESSION['tilt_sess_userReport_user'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_userReport_user']);?>">
					</div>
					<div class="form-group col-sm-4">
						<input  type="text" class="form-control" placeholder="Start Date" autocomplete="off"  title="Select Date" id="startdate" name="from_date" value="<?php if(isset($_SESSION['tilt_sess_userReport_fromDate']) && $_SESSION['tilt_sess_userReport_fromDate'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_userReport_fromDate']));?>" onkeypress="return dateField(event);" >
					</div>
					<div class="form-group col-sm-4">
						<input type="text" class="form-control" autocomplete="off"  placeholder="End Date" title="Select Date" id="enddate" name="to_date" value="<?php if(isset($_SESSION['tilt_sess_userReport_endDate']) && $_SESSION['tilt_sess_userReport_endDate'] != '') echo date('m/d/Y',strtotime($_SESSION['tilt_sess_userReport_endDate']));?>" onkeypress="return dateField(event);">
					</div>
				</div>
				<div class="box-footer clear" align="center">
					<input type="submit"  class="btn btn-green" name="Search" id="Search" value="Search" title="Search">
				</div>
			</form>
		</div>
		<div class="clear" style="height: 20px"></div>
		<div class="col-xs-12 no-padding">
				<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0){ ?>
				Total User(s)&nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div class="clear" style="height: 20px"></div>
		<div id="pagingResult" class="clear" style="width: 100%"> 
			<div class="table-responsive">
			<form name="list_header" id="user_list_frm" action="" method="">
				<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-responsive" width="100%">
					<tr>
						<th class="text-center" width="2%">#</th>
						<th class="text-left" width="13%">User Name</th>
						<th class="text-center" width="8%">Total Tournaments</th>
						<th class="text-center" width="9%">Total Tournaments Won</th>
						<th class="text-center" width="9%">Total Tournaments Loss</th>
						<th class="text-center" width="7%">Total TiLT$ Gained</th>
						<th class="text-center" width="8%">Total TiLT$ Played</th>
						<th class="text-center" width="10%">Total Virtual Coins Gained</th>
						<th class="text-center" width="10%">Total Virtual Coins Played</th>
					</tr>
					<tr>
					<?php if(!empty($userListResult)) {
							foreach($userListResult as $key=>$value)	{	
								$userName	=	'';
								if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
									$userName = 'Guest'.$value->uId;
								else if(isset($value->FirstName)	&&	isset($value->LastName) && $value->FirstName !='' && $value->LastName !='') 	
									$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
								else if(isset($value->FirstName) && $value->FirstName !='')	
									$userName	=	 ucfirst($value->FirstName);
								else if(isset($value->LastName) && $value->LastName !='')	
									$userName	=	ucfirst($value->LastName);
					?>
							<td width="" class="text-center" valign="">
								<?php if(isset($_SESSION['curpage'])	&&	$_SESSION['curpage'] !=''	&&	isset($_SESSION['perpage'])	&&	$_SESSION['perpage'] !='')echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?>
							</td>
							<td class="text-left"><?php if(!empty($userName)){	echo $userName; } else { echo " - "; } ?></td>
							<td class="text-center"><?php 	if(isset($value->tourCount) && $value->tourCount != '')	{ echo '<a href="UserTournaments?cs=1&type=2&viewId='.$value->fkUsersId.'&username='.$userName.'" class="tournaments" title="Tournaments"><i class="fa fa-trophy"></i> '.number_format($value->tourCount).'</a>'; } else echo '0'; ?></td>
							<td class="text-center"><?php 	if(isset($value->wintour)  && $value->wintour > 0) echo '<a href="UserwinlossReport?cs=1&type=3&viewId='.$value->fkUsersId.'&username='.$userName.'" class="tournaments" title="Tournament Won"><i class="fa fa-trophy"></i> '.number_format($value->wintour).'</a>'; else echo '0';?></td>
							<td class="text-center"><?php 	if(isset($value->losstour) && $value->losstour > 0) echo '<a href="UserwinlossReport?cs=1&type=4&viewId='.$value->fkUsersId.'&username='.$userName.'" class="tournaments" title="Tournament Loss"><i class="fa fa-trophy"></i> '.number_format($value->losstour).'</a>';else echo '0'; ?></td>
							
							<td class="text-center"><?php 	if(array_key_exists($value->fkUsersId,$userCoinsArray)) echo number_format($userCoinsArray[$value->fkUsersId]['TCG']); 	else echo '0';?></td>
							<td class="text-center"><?php 	if(array_key_exists($value->fkUsersId,$userCoinsArray)) echo number_format($userCoinsArray[$value->fkUsersId]['TCP']);	else echo '0';?></td>
							<td class="text-center"><?php 	if(array_key_exists($value->fkUsersId,$userCoinsArray)) echo number_format($userCoinsArray[$value->fkUsersId]['VCG']); else echo '0';?></td>
							<td class="text-center"><?php 	if(array_key_exists($value->fkUsersId,$userCoinsArray)) echo number_format($userCoinsArray[$value->fkUsersId]['VCP']); else echo '0';?></td>
						</tr>
					<?php } 
					}
					else { ?>
						<tr><td align="center" colspan="13" class="error">No User(s) Found</td></tr>
					<?php } ?>
			</table>
			</form>
			</div>
		</div>
		<div class="col-xs-12 clear">
			<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ) {
				pagingControlLatest($tot_rec,'UserReport'); ?>
			<?php }?>
		</div><br>
	</section>
	<?php   footerLinks(); commonFooter(); ?>
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
	$('.tournaments').fancybox({
		'width': '850',
		'height': '300',
		'maxWidth': '100%', 
		'type': 'iframe'
	 });
});
</script>
</html>
