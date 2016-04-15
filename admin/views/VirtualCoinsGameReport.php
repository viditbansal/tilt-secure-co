<?php 
	require_once('includes/CommonIncludes.php');
	admin_login_check();
	commonHead();
	require_once('controllers/LogController.php');
	$logObj  	=   new LogController();
	$arr		=	array();
	$display   	=   'none';
	$class  	=	'';
	$msg		=	'';
	$param		=	'';
	$tot_rec	=	0;
	if(isset($_GET['cs']) && $_GET['cs']=='1') {
		destroyPagingControlsVariables();
		unset($_SESSION['mgc_sess_report_game']);
		unset($_SESSION['mgc_sess_report_coin']);
	}
	if(isset($_POST['Search']) && $_POST['Search'] != '') {
		destroyPagingControlsVariables();
		$_POST	= unEscapeSpecialCharacters($_POST);
	    $_POST	= escapeSpecialCharacters($_POST);
		if(isset($_POST['game_name']))
			$_SESSION['mgc_sess_report_game']	=	trim($_POST['game_name']);
		if(isset($_POST['coin']))
			$_SESSION['mgc_sess_report_coin']	=	trim($_POST['coin']);
	}
	setPagingControlValues('g.id',ADMIN_PER_PAGE_LIMIT);
	$fields		=	" t.id as tourId, g.id AS gameId, g.name, count(t.id) AS tourCount, sum(t.Prize) AS Prize ";
	$condition	= 	" t.Type = 3 AND t.Status != 3 AND g.id !=''  AND t.CreatedBy IN(1,3) ";
	$gameVirtCoinReport		=	$logObj->getGameVirtualCoinReport($fields,$condition);
	$tot_rec				=	$logObj->getTotalRecordCount();
	if(isset($gameVirtCoinReport) && is_array($gameVirtCoinReport) && count($gameVirtCoinReport) > 0) {
		$gameReportArray	=	array();
		$gameIds			=	'';
		foreach($gameVirtCoinReport as $key => $value) {
			$gameIds	.= $value->gameId.',';
			$gameReportArray[$value->gameId]['fkGamesId']	=	$value->gameId;
			$gameReportArray[$value->gameId]['Name']		=	$value->name;
			$gameReportArray[$value->gameId]['tourCount']	=	$value->tourCount;
			$gameReportArray[$value->gameId]['userCount']	=	0;
			$gameReportArray[$value->gameId]['winCount']	=	0;
			$gameReportArray[$value->gameId]['Prize']		=	$value->Prize;
		}
		$gameIds	=	rtrim($gameIds,',');
		$fields2	=	" t.fkGamesId, count(DISTINCT(CASE WHEN a.fkUsersId > 0 and a.ActionType = 3 then a.fkUsersId end)) AS winCount, count(DISTINCT(CASE WHEN a.fkUsersId > 0 and a.ActionType = 2 then a.fkUsersId end)) AS userCount ";
		$condition2	= 	" t.Type = 3 AND t.Status = 1 AND t.fkGamesId in(".$gameIds.") AND ActionType in(2,3)  AND t.CreatedBy IN(1,3) ";
		$gameStatisticsReport	=	$logObj->getGameUserAndWinnerCount($fields2,$condition2);
		if(isset($gameStatisticsReport) && is_array($gameStatisticsReport) && count($gameStatisticsReport) > 0) {
			foreach($gameStatisticsReport as $s_key => $s_value) {
				$gameReportArray[$s_value->fkGamesId]['userCount']	=	$s_value->userCount;
				$gameReportArray[$s_value->fkGamesId]['winCount']	=	$s_value->winCount;
			}
		}
	}
?>
<body>
<?php top_header(); ?>
	<div class="box-header"><h2><i class="fa fa-list"></i>Virtual Coins Game Report</h2></div>
	<div class="clear">
	<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="">
		
		<tr>
			<td valign="top" align="center" colspan="2">
				<form name="search_category" action="VirtualCoinsGameReport<?php echo $param;?>" method="post">
				  <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
						<tr><td height="15"></td></tr>
						<tr>	
							
							<td align="left" style="padding-left:20px;width:50px"><label>Game Name</label></td>
							<td align="center"style="width:4px">:</td>
							<td align="left" height="40" style="width:200px">
								<input type="text" class="input" name="game_name" id="game_name"  value="<?php if(isset($_SESSION['mgc_sess_report_game']) && $_SESSION['mgc_sess_report_game'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_report_game']);  ?>">
							</td>
							<td width="400"></td>
						</tr>
						<tr><td align="center" colspan="4" style="padding-top:20px"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
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
						<?php if($tot_rec > 0) { ?>
							<td align="left" width="20%">No. of Game(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
							<?php if($tot_rec > 0) { pagingControlLatest($tot_rec,'VirtualCoinsGameReport'.$param); } ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="20"></td></tr>
		<tr>
			<td colspan="2">
			<div class="tbl_scroll">
			  <form action="VirtualCoinsGameReport" class="l_form" name="VirtualCoinsReportForm" id="VirtualCoinsReportForm"  method="post"> 
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr align="left">
						<th align="center" width="3%" class="text-center">#</th>
						<th align="left" width="28%">Game Name</th>
						<th width="20%" align="left">No. of Tournaments</th>
						<th width="20%" align="left">No. of Players</th>
						<th width="20%" align="left">No. of Winners</th>
						<th align="left" width="25%"><?php echo SortColumn('Prize','Total Virtual Coins');?></th>
					</tr>
					<?php
						$i = 0;
						if(count($gameReportArray) > 0 ) {
							foreach($gameReportArray as $key =>$value) {
					?>
							<tr>
								<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$i+1;?></td>
								<td align="left"><?php if($value['Name'] != '') echo $value['Name']; else echo "-"; ?></td>
								<td align="left" style="padding-left:15px;">
									<?php
										if($value['tourCount'] > 0) { 
											echo '<a href="GameTournamentList?cs=1&type=3&gameId='.$value['fkGamesId'].'&gameName='.$value['Name'].'" class="game_tournament" ><i class="fa fa-trophy"></i> '.$value['tourCount'].'</a>';
										} else { echo "-"; }
									?>
								</td>
								<td align="left" style="padding-left:15px;">
									<?php
										if($value['userCount'] != 0) { 
											echo '<a href="PlayersList?cs=1&type=3&playersGameId='.$value['fkGamesId'].'&gameName='.$value['Name'].'" class="game_players" ><i class="fa fa-user"></i> '.$value['userCount'].'</a>';
										} else { echo "-"; }
									?>
								</td>
								<td align="left" style="padding-left:15px;">
									<?php
										if($value['winCount'] != 0) { 
											echo '<a href="PlayersList?cs=1&type=3&winnerGameId='.$value['fkGamesId'].'&gameName='.$value['Name'].'" class="game_players" ><i class="fa fa-user"></i> '.$value['winCount'].'</a>';
										} else { echo "-"; }
									?>
								</td> 
								<td align="left" style="padding-left:15px;"><?php if($value['Prize'] != '') echo number_format($value['Prize']); else echo "-"; ?></td>
							</tr>
						<?php
							$i++;
							}
						} else { ?>	
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
		$(".game_players").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"45%",
				title:true
		});
		$(".game_tournament").colorbox(
			{
				iframe:true,
				width:"60%", 
				height:"35%",
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
