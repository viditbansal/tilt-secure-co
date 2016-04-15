<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
$_SESSION['referPage']	=	'TournamentList';
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_game_tour']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['tournament']))
		$_SESSION['mgc_sess_game_tour'] 	=	trim($_POST['tournament']);
}
$gameName	=	'';
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);

if((isset($_GET['gameId']) && $_GET['gameId'] != '')   ){
$fields    = " t.id,t.TournamentName,t.fkBrandsId as brandId,t.DateCreated";
$condition = " AND t.fkGamesId = ".$_GET['gameId']." and t.Status != '3' AND t.CreatedBy in (1,3) ";
if(isset($_GET['type']) && $_GET['type'] != '')
	$condition .= " AND t.Type = ".$_GET['type'];
$tournamentListResult  = $tournamentObj->getGameTournamentList($fields,$condition);
$tot_rec 		 = $tournamentObj->getTotalRecordCount();
$from = '';
$from .= '?gameId='.$_GET['gameId'];
	if(isset($_GET['type']) && $_GET['type'] != '')
		$from .= '&type='.$_GET['type'];

	if(isset($_GET['gameName']) && $_GET['gameName'] != '' ){ 
		$from .= '&gameName='.$_GET['gameName']; 
		$gameName	= ucFirst($_GET['gameName']);
	}
}
?>
<body  >
	
	 <div class="clear"></div>
	 <div class="box-header"><h2><i class="fa fa-list"></i> <?php if(isset($gameName) && $gameName !='' ) echo $gameName.' - '?>Tournament List</h2></div>
		<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" >
			
			<tr>
				<td valign="top" align="center" colspan="2">
					
					<form name="search_category" action="GameTournamentList<?php if(isset($_GET['gameId'])) echo $from; ?>" method="post">
					   <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
							<tr><td height="15"></td></tr>
							<tr>													
								<td width="7%" style="padding-left:20px;"><label>Tournament Name</label></td>
								<td width="3%" align="center">:</td>
								<td align="left"  height="40">
									<input type="text" class="input" name="tournament" id="tournament"  value="<?php  if(isset($_SESSION['mgc_sess_game_tour']) && $_SESSION['mgc_sess_game_tour'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_game_tour']);  ?>" style="width:50%" >
								</td>
							</tr>
							<tr><td height="10"></td></tr>
							<tr><td align="center" colspan="5"><input type="submit" class="submit_button" name="Search" title="Search" id="Search" value="Search"></td></tr>
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
							<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0){ ?>
							<td align="left" width="26%">No. of Tournament(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
							<?php } ?>
							<td align="center">
						<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0 ) {
										pagingControlLatest($tot_rec,'GameTournamentList'.$from);
									 } ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td colspan="2">
				
			<div class="tbl_scroll">
				  <form action="GameTournamentList" class="l_form" name="TournamentListForm" id="TournamentListForm"  method="post"> 
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
						<tr align="left">
							<th align="center" width="1%" style="text-align:center">#</th>
							<th width="15%"><?php echo SortColumn('TournamentName','Tournament Name'); ?></th>
							<th width="12%"><?php echo SortColumn('DateCreated','Created Date'); ?></th>
						</tr>
						<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0 ) { 
								 foreach($tournamentListResult as $key=>$value){
						 ?>									
						<tr id="test_id_<?php echo $value->id;?>"	>
							<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td valign="top">
							<?php if(isset($value->id)	&&	$value->id !=''	&&	isset($value->TournamentName) && $value->TournamentName != ''){ ?>
								<p align="left" >
								<?php echo ucfirst($value->TournamentName); ?>
								</p>
							<?php } else echo '-'; ?></td>
							<td valign="top"><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
						</tr>
						<?php } ?> 																		
					</table>
					</form>
					<?php } else { ?>	
						<tr>
							<td colspan="16" align="center" style="color:red;">No Tournament(s) Found</td>
						</tr>
					<?php } ?>
					</div>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
<?php commonFooter(); ?>
<script type="text/javascript">
$("#created_date").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
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
 jQuery(function() {
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[id^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");
	   
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
$(function(){

   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '650';
   var maxWidth  = '1100';
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
