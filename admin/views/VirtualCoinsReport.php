<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/CoinsController.php');
$coinObj  		=   new CoinsController();
$arr			= array();
$display   		=   'none';
$class  		=  $msg  = $statistics = '';
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_r_tournament_name']);
	unset($_SESSION['mgc_sess_r_prize']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['tournament_name']))
		$_SESSION['mgc_sess_r_tournament_name'] =	trim($_POST['tournament_name']);
	if(isset($_POST['prize']))
		$_SESSION['mgc_sess_r_prize'] 			=	trim($_POST['prize']);
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    		= " t.id,t.id as tournamentId, t.Prize, t.TournamentName,t.GameType,g.Name,count(distinct (CASE WHEN ts.fkUsersId > 0 then ts.fkUsersId end) ) as wins";
$condition 		= " AND t.Type = 3 AND t.TournamentName !='' AND t.CreatedBy IN(1,3) ";
$getDetails		=	$coinObj->getTournamentDetails($fields,$condition);
$tot_rec		=	$coinObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($getDetails)) {
	$_SESSION['curpage'] 	= 1;
	$getDetails  			= $coinObj->getTournamentDetails($fields,$condition);
}
$process	= array();
$hid = '';
$eid = '';
if(isset($getDetails) && is_array($getDetails) && count($getDetails) >0){
	foreach($getDetails as $key=>$value){
		if($value->GameType == 2)
			$eid .=  $value->tournamentId.',';
		else
			$hid .=  $value->tournamentId.',';
	}
}
$PlayerArray = array();
if($hid != ''){
	$fields		 		= " fkTournamentsId, count(distinct fkUsersId) as pcount ";
	$condition			= " tp.`fkTournamentsId` IN (".trim($hid,',').") ";
	$getHighScoreCount  = $coinObj->getTournamentPlayersCount($fields,$condition);
	if(is_array($getHighScoreCount) && count($getHighScoreCount) > 0){
		foreach($getHighScoreCount as $key => $value){
			$PlayerArray[$value->fkTournamentsId] = $value->pcount;
		}
	}
}
if($eid != ''){
	$fields		 		= " fkTournamentsId, count(distinct ept.fkUsersId) as pcount ";
	$condition			= " AND tp.`fkTournamentsId` IN (".trim($eid,',').") ";
	$getEliScoreCount  	= $coinObj->getEliminationPlayedEntry($fields,$condition);
	if(is_array($getEliScoreCount) && count($getEliScoreCount) > 0){
		foreach($getEliScoreCount as $key => $value){
			$PlayerArray[$value->fkTournamentsId] = $value->pcount;
		}
	}
}
?>
<body>
<?php if(!isset($_GET['statistics']))	top_header(); ?>
	<div class="box-header"><h2><i class="fa fa-list"></i>Virtual Coins Tournament Report</h2></div>
	<div class="clear">
	<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="">
		
		<tr>
			<td valign="top" align="center" colspan="2">
				<form name="search_category" action="VirtualCoinsReport<?php echo $statistics;?>" method="post">
				  <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
						<tr><td height="15"></td></tr>
						<tr>													
							<td width="10%" style="padding-left:20px;"><label>Tournament Name</label></td>
							<td width="1%" align="center">:</td>
							<td align="left" width="20%" height="40">
								<input type="text" class="input" name="tournament_name" id="tournament_name"  value="<?php if(isset($_SESSION['mgc_sess_r_tournament_name']) && $_SESSION['mgc_sess_r_tournament_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_r_tournament_name']);  ?>">
							</td>
							<td width="10%" style="padding-left:20px;"><label>Prize</label></td>
							<td width="1%" align="center">:</td>
							<td align="left" width="20%" height="40">
								<input type="text" class="input" name="prize" id="prize" onkeypress="return isNumberKey(event);" value="<?php if(isset($_SESSION['mgc_sess_r_prize']) && $_SESSION['mgc_sess_r_prize'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_r_prize']);  ?>">
							</td>
						</tr>
						<tr><td align="center" colspan="9" style="padding-top:20px"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
						<tr><td height="15"></td></tr>
					 </table>
				  </form>	
			</td>
		</tr>
		<tr><td height="20"></td></tr>
		<tr>
			<td colspan="2">
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<?php if(isset($getDetails) && is_array($getDetails) && count($getDetails) > 0){ ?>
						<td align="left" width="20%">No. of Tournament(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
								<?php if(isset($getDetails) && is_array($getDetails) && count($getDetails) > 0 ) {
									pagingControlLatest($tot_rec,'VirtualCoinsReport'.$statistics); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="10"></td></tr>
		<tr><td colspan= '2' align="center">
			<?php displayNotification('Brand'); ?>
			</td></tr>
		<tr><td height="10"></td></tr>
		<tr>
			<td colspan="2">
			<div class="tbl_scroll">
			  <form action="BrandList" class="l_form" name="BrandListForm" id="BrandListForm"  method="post"> 
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr align="left">
						<th align="center" width="3%" class="text-center">#</th>
						<th align="left" width="28%">Tournament Name</th>
						<th width="20%" align="left">Game</th>
						<th width="20%" align="left">No. of Players</th>
						<th width="20%" align="left">No. of Winners</th>
						<th align="left" width="25%">Total Prize(Virtual Coins)</th>
					</tr>
					<?php if(isset($getDetails) && is_array($getDetails) && count($getDetails) > 0 ) {
					 foreach($getDetails as $key=>$value){ 
					 ?>	
					<tr>
						<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td align="left"><?php if(isset($value->TournamentName)	&&	$value->TournamentName!=''){	echo $value->TournamentName; } else { echo "-"; } ?></td>
						<td align="left"><?php if(isset($value->Name)	&&	$value->Name!=''){	echo $value->Name; } else { echo "-"; } ?></td>
						<td align="left">
							<?php $total_players	=	isset($PlayerArray[$value->tournamentId]) && $PlayerArray[$value->tournamentId] > 0 ? $PlayerArray[$value->tournamentId] : '0';
							if($total_players > 0 ){ ?>
							<a href="PlayersList?cs=1&tournamentId=<?php echo $value->tournamentId;?>&gametype=<?php echo $value->GameType;?>&tournamentName=<?php echo $value->TournamentName;?>" class="tournament_list_pop_up" name="Tournaments" id="Tournaments" title="Number of Players" alt="Tournaments"><i class="fa fa-user"></i>&nbsp;<?php	echo $total_players; ?></a>&nbsp;
						<?php } else { echo "-";} ?>
						</td>
						<td align="left">
							<?php if(isset($value->wins) && $value->wins > 0){ ?>
							<a href="PlayersList?cs=1&winnerId=<?php echo $value->tournamentId;?>" class="tournament_list_pop_up" name="Tournaments" id="Tournaments" title="Number of Winners" alt="Tournaments"><i class="fa fa-user"></i>&nbsp;<?php  echo $value->wins; ?></a><?php } else echo '-'; ?>&nbsp;
						</td>
						<td align="left" style="padding-right:15px;"><?php if(isset($value->Prize)	&&	$value->Prize!=''){	echo number_format($value->Prize); } else { echo "-"; } ?></td>
					</tr>
					<?php }
					}else { ?>	
					<tr><td colspan="16" align="center" style="color:red;">No Result(s) Found</td></tr>
				<?php } ?>
				</table>
				</form>
				</div>
		
	</table>
	</div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".brand_image_pop_up").colorbox({title:true});
jQuery(function(){
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[id^=test_id_]').hover(function(){
		jQuery(this).find("div.userAction a").css("display","inline-block");
    }, function(){
        jQuery(this).find("div.userAction a").hide();
    });
});
	$(document).ready(function() {		
		$(".tournament_list_pop_up").colorbox(
			{
				iframe:true,
				width:"53%", 
				height:"45%",
				title:true
		});
});
$(function(){
   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '580';
   var maxWidth  = '900';
   if(bodyHeight<maxHeight) {   	
   	setHeight = bodyHeight;
   } else {
   		setHeight = maxHeight;
   }
   if(bodyWidth>maxWidth) {
   		setWidth = bodyWidth;
   } else {
   		setWidth = maxWidth;
   }
   parent.$.colorbox.resize({
        innerWidth :setWidth,
        innerHeight:setHeight
    });
});
</script>
</html>
