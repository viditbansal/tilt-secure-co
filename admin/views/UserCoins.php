<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
$pagingParam	=	'';
$totalCoins		=	0;
$leftjoin		=	'';
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_coins_tournament']);
	unset($_SESSION['mgc_sess_coin_date']);
	unset($_SESSION['mgc_sess_coin_type']);
	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
setPagingControlValues(' id',ADMIN_PER_PAGE_LIMIT);

if(isset($_POST['Search'])	&&	$_POST['Search'] != '')	{
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['tournament']))
		$_SESSION['mgc_sess_coins_tournament']	=	trim($_POST['tournament']);
	if(isset($_POST['coinWin_date']))
		$_SESSION['mgc_sess_coin_date']	=	$_POST['coinWin_date'];
}

$fields    = " ts.id,tp.TournamentHighScore,ts.Prize,ts.DateCreated,t.TournamentName,t.Type,t.GameType,t.id as tid,max(ept.Points) as points ";
$condition = " ";
$totalTiltCoins		=	$totalVirtCoins		=	0;
if(isset($_GET['UserId']) && $_GET['UserId'] != ''){
	$condition .= " ts.fkUsersId = ".$_GET['UserId']." AND (tp.fkUsersId = ts.fkUsersId OR ept.fkUsersId = ts.fkUsersId )";
	if(isset($_GET['customCount'])){
		$condition .= ' and t.Type = 4 ';
	}else if(isset($_GET['tiltCoins'])){
		$condition .= ' and t.Type = 2 ';
	}else if(isset($_GET['virtCoins'])){
		$condition .= ' and t.Type = 3 ';
	}
	$pagingParam	=	'?UserId='.$_GET['UserId'];
	if(isset($_GET['userName']) && $_GET['userName'] != '')
		$pagingParam	.=	'&userName='.$_GET['userName'];
	if(isset($_GET['customCount']) && $_GET['customCount'] != ''){
		$pagingParam	.=	'&customCount='.$_GET['customCount'];
		$customCount		=	$_GET['customCount'];
		$fields   		 	= " ts.id,tp.TournamentHighScore,ts.Prize,ts.DateCreated,t.TournamentName,t.Type,t.GameType,t.id as tid,tc.PrizeImage,tc.PrizeTitle,max(ept.Points) as points  ";
		$leftjoin			= " LEFT JOIN tournamentcustomprize AS tc ON ( ts.fkTournamentsId = tc.fkTournamentsId  and  ts.Prize = tc.PrizeOrder ) ";
	}
	if(isset($_GET['tiltCoins']) && $_GET['tiltCoins'] != ''){
		$pagingParam	.=	'&tiltCoins='.$_GET['tiltCoins'];
		$totalTiltCoins		=	$_GET['tiltCoins'];
	}
	if(isset($_GET['virtCoins']) && $_GET['virtCoins'] != ''){
		$pagingParam	.=	'&virtCoins='.$_GET['virtCoins'];
		$totalVirtCoins		=	$_GET['virtCoins'];
	}
	$coinWinResult	=	$tournamentObj->coinsWinList($fields,$condition,$leftjoin);
	$tot_rec 		 = $tournamentObj->getTotalRecordCount();
}
?>
<body class="popup_bg">
	<div class="box-header"><h2><i class="fa fa-list"></i><?php if(isset($_GET['userName']) && $_GET['userName'] != '')	echo $_GET['userName'].' - '; ?>Tournament Won List</h2>
	<h2 style="float:right" >
	<?php if(isset($totalTiltCoins)	&&	$totalTiltCoins > '0') { ?> Total TiLT$&nbsp;:&nbsp;<span class="total_value"><?php echo  number_format($totalTiltCoins); ?></span>&nbsp;&nbsp;<?php } ?>
	<?php if(isset($totalVirtCoins)	&&	$totalVirtCoins > '0') { ?> Total Virtual Coins&nbsp;:&nbsp;<span class="total_value"> <?php echo  number_format($totalVirtCoins);?></span>&nbsp;&nbsp;<?php } ?>
	
	<?php if(isset($customCount)	&&	$customCount > '0') { ?> Custom Prize&nbsp;:&nbsp;<span class="total_value"> <?php echo  number_format($customCount);?></span>&nbsp;&nbsp;<?php } ?>
	</h2>
	</div>
	<div class="clear">
	<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" class="headertable">
		
		<tr>
			<td valign="top" align="center" colspan="2">
				
				<form name="player_search" action="<?php echo 'UserCoins'.$pagingParam;?>&cs=1" method="post">
				   <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
						<tr><td height="15"></td></tr>
						<tr>													
							<td width="7%" style="padding-left:20px;"><label>Tournament Name</label></td>
							<td width="2%" align="center">:</td>
							<td align="left"  height="40">
								<input type="text" class="input" name="tournament" id="tournament"  value="<?php  if(isset($_SESSION['mgc_sess_coins_tournament']) && $_SESSION['mgc_sess_coins_tournament'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_coins_tournament']);  ?>" >
							</td>
							<td width="10%" style="padding-left:20px;" align="left"><label>Date Played</label></td>
							<td width="2%" align="center">:</td>
							<td height="40"  align="left" >
								<input style="width:90px;" type="text" autocomplete="off" maxlength="10" class="input datepicker" name="coinWin_date" id="coinWin_date" title="" value="<?php if(isset($_SESSION['mgc_sess_coin_date']) && $_SESSION['mgc_sess_coin_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_coin_date'])); else echo '';?>" > (mm/dd/yyyy)
							</td>
						</tr>
						<tr>
							<td align="center" colspan="9" style="padding-top:20px" ><input type="submit" class="submit_button" title="Search" name="Search" id="Search" value="Search"></td>
						</tr>
						<tr><td height="10"></td></tr>
					 </table>
				  </form>	
			</td>
		</tr>
		<tr><td height="20"></td></tr>
		<tr>
			<td colspan="2">
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<?php if(isset($coinWinResult) && is_array($coinWinResult) && count($coinWinResult) > 0){ ?>
						<td align="left" width="30%">No. of Won Tournament(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
								<?php if(isset($coinWinResult) && is_array($coinWinResult) && count($coinWinResult) > 0 ) {
									pagingControlLatest($tot_rec,'UserCoins'.$pagingParam); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="20"></td></tr>
		<tr>
			<td colspan="2">
			<form action="UserCoins" class="l_form" name="UserCoinsList" id="UserCoinsList"  method="post"> 
				<div class="tbl_scroll">
			  
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr>
						<th  align="center" width="3%" class="text-center">#</th>
						<th  width="30%">Tournament Name</th>
						<?php if(isset($_GET['tiltCoins'])){ ?>
							<th width="6%" >TiLT$</th>
						<?php }else if(isset($_GET['virtCoins'])){ ?>
							<th width="6%" >Virtual Coins</th>
						<?php }else if(isset($_GET['customCount'])){ ?>
							<th width="6%" >Position</th>
							<th width="10%" >Prize Title</th>
							<th width="10%">Prize Image</th>
						<?php } ?>
						<th  width="15%">High Score</th>
						<th width="15%">Date Played</th>
					</tr>
					<?php if(isset($coinWinResult) && is_array($coinWinResult) && count($coinWinResult) > 0 ) { 
							 foreach($coinWinResult as $key=>$value){
					 ?>									
					<tr id="test_id_<?php echo $value->id;?>">
						<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td ><?php if(isset($value->TournamentName) && $value->TournamentName != '') echo $value->TournamentName; else echo '-';?></td>
						<td align="left" ><?php if(isset($value->Prize) && $value->Prize != 0){ echo number_format($value->Prize); } else echo '0';?></td>	
						<?php if(isset($_GET['customCount'])){ ?>
							<td align="left" ><?php if(isset($value->PrizeTitle) && $value->PrizeTitle != ''){ echo $value->PrizeTitle; } else echo '-';?></td>	
							<td align="left" style="text-align:center"><?php if(isset($value->PrizeImage) && $value->PrizeImage != ''){ 
								$prize_image_path = '';
								$prizeImage = $value->tid.'/'.$value->PrizeImage;
								if(SERVER){
									if(image_exists(17,$prizeImage)){
										$prize_image_path = CUSTOM_PRIZE_IMAGE_PATH.$prizeImage;
									}
								}
								else if(file_exists(CUSTOM_PRIZE_IMAGE_PATH_REL.$prizeImage)){
										$prize_image_path = CUSTOM_PRIZE_IMAGE_PATH.$prizeImage;
								}
								?> 
								<?php if(isset($prize_image_path) && $prize_image_path != '') { ?> 
									<img  src="<?php echo CUSTOM_PRIZE_IMAGE_PATH.$prizeImage; ?>" width="25" height="25" >
								<?php } else echo '-'; ?>
								
							<?php } else echo '-';?></td>	
						<?php } ?>
						<td align="left" >
							<?php 
								if($value->GameType == 2)
									if(isset($value->points) && $value->points != '') echo number_format($value->points); else echo '-';
								else
									if(isset($value->TournamentHighScore) && $value->TournamentHighScore != '') echo number_format($value->TournamentHighScore); else echo '-';
							?>
						</td>
					<td valign="top">
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
						<td colspan="16" align="center" style="color:red;">No Tournament(s) Found</td>
					</tr>
				</table>
				<?php } ?>
				</div>
				</form>
			</td>
		</tr>
		<tr><td height="10"></td></tr>
	</table>
 </div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".user_image_pop_up").colorbox({title:true});

$("#coinWin_date").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
	onSelect		: function (dateText, inst) {
						$('#enddate').datepicker("option", 'minDate', new Date(dateText));
						},
    onClose			: function () { $(this).focus(); },

    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	closeText		:   "Close"
 });

$(document).ready(function() {		
	$(".pop_up").colorbox(
		{
			iframe:true,
			width:"60%", 
			height:"55%",
			title:true
	});
	$(".players_pop_up").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"80%",
				title:true,
		});
});
$(function(){

   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '550';
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
function close_this()
{
self.close();
}
$(".detailUser").click(function(){
	var hre	=	$(".detailUser").attr("href");
 	window.parent.location.href = hre+'&back=1';
});
</script>
</html>
