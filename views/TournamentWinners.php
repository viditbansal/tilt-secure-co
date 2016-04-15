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
	unset($_SESSION['tilt_sess_tourReport_winUser']);
	unset($_SESSION['tilt_sess_tourReport_winEmail']);
}
$pagingParam	=	'';
$condition1 = $condition = "";
$playedId	=	'';
$type = 1;
if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
	$pagingParam  = "?viewId=".$_GET['viewId'];
	$condition .= " ts.fkTournamentsId IN (".$_GET['viewId'].") ";
	if(isset($_GET['elimination']) && isset($_GET['playedId']) && !empty($_GET['playedId']) ){
		$type = 2;
		$pagingParam	.=	'&elimination=1&playedId='.$_GET['playedId'];
		$condition1 .= " and fkTournamentsPlayedId = ".$_GET['playedId']." " ;
		$playedId = $_GET['playedId'];
	}
}
if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '')
		$pagingParam	.=	'&tournamentName='.$_GET['tournamentName'];
if(isset($_GET['custom']) ){$pagingParam	.=	'&custom=1';}
		

if(isset($_POST['Search'])	&&	$_POST['Search'] != '')	{
	destroyPagingControlsVariables();
	$_SESSION['tilt_sess_tourReport_winUser'] = $_SESSION['tilt_sess_tourReport_winEmail'] = $_SESSION['mgc_sess_user_tournaments'] = '';
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['username']))
		$_SESSION['tilt_sess_tourReport_winUser']	=	trim($_POST['username']);
	if(isset($_POST['email']))
		$_SESSION['tilt_sess_tourReport_winEmail']	=	trim($_POST['email']);	
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
if(isset($_SESSION['tilt_sess_tourReport_winUser'])	&&	$_SESSION['tilt_sess_tourReport_winUser'])
	$condition .= " and (u.FirstName LIKE '%".$_SESSION['tilt_sess_tourReport_winUser']."%' OR	u.LastName LIKE '%".$_SESSION['tilt_sess_tourReport_winUser']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['tilt_sess_tourReport_winUser']."%')";
if(isset($_SESSION['tilt_sess_tourReport_winEmail'])	&&	$_SESSION['tilt_sess_tourReport_winEmail'])
	$condition	.=	' AND u.email	LIKE "'.$_SESSION['tilt_sess_tourReport_winEmail'].'%" ';
if(isset($_SESSION['tilt_sess_tourReport_fromDate']) && $_SESSION['tilt_sess_tourReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_tourReport_endDate']) && $_SESSION['tilt_sess_tourReport_endDate'] != ''){
			$condition .= " AND ((date(t.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_fromDate']))."' and date(t.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_endDate']))."') ) ";
		}
if(isset($_GET['custom']) ){
	$acsDesc	=	" Group BY u.id ORDER BY ts.Prize ASC ";
}else{
	$acsDesc	=	" Group BY u.id ORDER BY ts.Prize DESC ";
}
if($type == 2){
	$fields    = "u.id,u.Email,u.UniqueUserId,u.FirstName,u.LastName,u.id as userId,ts.Prize,u.Status,t.Type ";
	$tourWinnersList  = $reportObj->getTournamentElimWinners($fields,$condition." ".$acsDesc);
	$tot_rec 		 = $reportObj->getTotalRecordCount();
}	
else {
	$fields    = "u.id,u.Email,u.UniqueUserId,u.FirstName,u.LastName,u.id as userId,ts.Prize,u.Status,tp.TournamentHighScore,tp.id as fkTournamentsPlayedId,t.Type ";
	$tourWinnersList  = $reportObj->getTournamentWinners($fields,$condition." ".$acsDesc);
	$tot_rec 		 = $reportObj->getTotalRecordCount();
}

$prizeType = 0;
$scoreArray = $customPrize = array();
if(isset($tourWinnersList) && is_array($tourWinnersList) && count($tourWinnersList) > 0 ){
	if(isset($tourWinnersList[0]->Type) && $tourWinnersList[0]->Type == 4){
		$prizeType = 1;
		$customPrizeResult = $reportObj->getCustomPrizeDetails(' PrizeTitle, PrizeImage, PrizeDescription, PrizeOrder ', ' fkTournamentsId = '.$_GET['viewId']);
		if(isset($customPrizeResult) && is_array($customPrizeResult) && count($customPrizeResult) > 0 ){
			foreach($customPrizeResult as $key=>$value){
				$customPrize[$value->PrizeOrder]['image'] = $value->PrizeImage;
				if($value->PrizeImage !=''){
					if(SERVER){
						if(image_exists(17,$_GET['viewId'].'/'.$value->PrizeImage)){
							$customPrize[$value->PrizeOrder]['image'] = '<img class="img_border" src="'.CUSTOM_PRIZE_IMAGE_PATH.$_GET['viewId'].'/'.$value->PrizeImage.'" width="35" height="35" alt="Image"/>';
						}else
							$customPrize[$value->PrizeOrder]['image'] = '';
					}
					else {
						if(file_exists(CUSTOM_PRIZE_IMAGE_PATH_REL.$_GET['viewId'].'/'.$value->PrizeImage)){
							$customPrize[$value->PrizeOrder]['image'] = '<img class="img_border" src="'.CUSTOM_PRIZE_IMAGE_PATH.$_GET['viewId'].'/'.$value->PrizeImage.'" width="35" height="35" alt="Image"/>';
						}else
							$customPrize[$value->PrizeOrder]['image'] = '';
					}
				}else
					$customPrize[$value->PrizeOrder]['image'] = '';
			}
		}
	}
	if($type == 2 && !empty($playedId)){
		$ids = '';
		foreach($tourWinnersList as $key1=>$value1){
			if(isset($value1->userId) && !empty($value1->userId))
				$ids	.=	$value1->userId.',';
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
?>
<body class="popup_bg" style="overflow-x:hidden;">
	<section class="content-header">
		<h2 id="heading_title"><i class="fa fa-list"></i>&nbsp;Winners List<?php if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '')	echo ' - '.ucFirst($_GET['tournamentName']); ?></h2>
	</section>	
	<section class="content col-md-12 box-center develop_page">
		<div style="height: 20px" class="clear"></div>
		<div class="search_group">
			<form class="tournament" method="post" action="<?php echo 'TournamentWinners'.$pagingParam;?>&cs=1">
				<div class="box-body col-sm-8 box-center">
					<div class="form-group col-sm-6">
						<input type="text" class="input w95 form-control" title="User Name" placeholder="User Name" name="username" id="username"  value="<?php  if(isset($_SESSION['tilt_sess_tourReport_winUser']) && $_SESSION['tilt_sess_tourReport_winUser'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_tourReport_winUser']);  ?>" >
					</div>
					<div class="form-group col-sm-6">
						<input type="text" class="input w95 form-control" title="Email" placeholder="Email" name="email" id="email"  value="<?php  if(isset($_SESSION['tilt_sess_tourReport_winEmail']) && $_SESSION['tilt_sess_tourReport_winEmail'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_tourReport_winEmail']);  ?>" >
					</div>
					<div class="box-footer clear" align="center">
					<input type="submit"  class="btn btn-green" name="Search" id="Search" value="Search" title="Search">
					</div>
				</div>
			</form>
		</div>
		<div class="col-xs-12 no-padding">
				<?php if(isset($tourWinnersList) && is_array($tourWinnersList) && count($tourWinnersList) > 0){ ?>
				No. of Winner(s)&nbsp;:&nbsp;<strong><?php echo $tot_rec; ?></strong>
				<?php } ?>
		</div>
		<div class="clear" style="height: 20px"></div>
		<div class="clear" style="width: 100%">
			<form action="TournamentWinners" class="l_form" name="TournamentWinners" id="TournamentWinners"  method="post"> 
				<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-responsive <?php if(isset($tourWinnersList) && is_array($tourWinnersList) && count($tourWinnersList) > 5) echo 'scroll'; ?>" width="100%">
				<tr>
					<th align="center" width="1%" class="text-center">#</th>
					<th align="left" width="15%">User Name</th>
					<th align="left" width="15%">Email</th>
					<th align="left" width="15%">Score</th>
					<th align="left" width="15%">Prize</th>
					<?php if($prizeType == 1) { ?>
						<th align="left" width="15%">Prize Order</th>
						<th align="left" width="11%">Prize Image</th>
					<?php } ?>
				</tr>
				<?php if(isset($tourWinnersList) && is_array($tourWinnersList) && count($tourWinnersList) > 0 ) { 
						 foreach($tourWinnersList as $key=>$value){
							$userName	=	'';
						if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
								$userName = 'Guest'.$value->id;
						else if(isset($value->FirstName)	&&	isset($value->LastName)) 	
							$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
						else if(isset($value->FirstName))	
							$userName	=	 ucfirst($value->FirstName);
						else if(isset($value->LastName))	
							$userName	=	ucfirst($value->LastName);
				 ?>			
				<tr>
					<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
					<td><?php if(isset($value->UniqueUserId) && $value->UniqueUserId !='') echo $userName; else { ?>
					<a class="winners" href="UserDetail<?php echo $pagingParam; ?>&id=<?php echo  $value->userId; ?>"><?php echo $userName; ?></a>
					<?php } ?>
					</td>
					<td><?php if(isset($value->Email) && $value->Email != '') echo $value->Email; else echo '-';?></td>
					<td valign="top" ><?php if($type == 2 ){ foreach($scoreArray[$value->userId] as $key1=>$scoreDetail) {?>
							<p>	<strong>Round&nbsp;<?php echo $scoreDetail['turn'];?>&nbsp;:&nbsp;</strong> <?php echo number_format($scoreDetail['score']);?></p>
						<?php } } else if(isset($value->TournamentHighScore) && $value->TournamentHighScore != '') echo number_format($value->TournamentHighScore); else echo '-';?>
					</td>
					<td><?php if(isset($value->Prize) && $value->Prize != 0) echo (isset($value->Type) && $value->Type == 4)? 'Custom' : ((isset($value->Type) && $value->Type == 3) ? number_format($value->Prize).' Virtual Coins' : number_format($value->Prize).' TiLT$') ; else echo '-';?></td>
					<?php if(isset($value->Type) && $value->Type == 4){ ?>
						<td><?php echo (isset($value->Prize) && $value->Prize > 0)? $value->Prize : '-'; ?></td>
						<td class="text-center"><?php echo (isset($customPrize[$value->Prize]['image']) && $customPrize[$value->Prize]['image'] != '')? $customPrize[$value->Prize]['image'] : '-'; ?></td>
					<?php } ?>
				</tr>
				<?php } ?>
			<?php } else { ?>	
				<tr>
					<td colspan="16" align="center" class="error">No Winner(s) Found</td>
				</tr>
			<?php } ?>
				</table>
			</div>
			</form>
		</div>
		<div class="col-xs-12 clear">
			<?php if(isset($tourWinnersList) && is_array($tourWinnersList) && count($tourWinnersList) > 0 ) {
				pagingControlLatest($tot_rec,'TournamentWinners'.$pagingParam); ?>
			<?php }?>
		</div>
	</section>
	<?php commonFooter(); ?>	
</body>