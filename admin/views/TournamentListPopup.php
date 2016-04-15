<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;

if(isset($_SESSION['referPage']))
	unset($_SESSION['referPage']);
$_SESSION['referPage']	=	'TournamentList';
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_tournament_game']);
	unset($_SESSION['mgc_sess_brand']);
	unset($_SESSION['mgc_sess_tournament_name']);
	unset($_SESSION['mgc_sess_gameType']);
	unset($_SESSION['mgc_sess_user_status']);
	unset($_SESSION['mgc_sess_tournament_status']);
	unset($_SESSION['mgc_sess_tournament_start']);
	unset($_SESSION['mgc_sess_tournament_end']);

	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}

$condition       = " and Status !=3";
$field			 = " id as brandId,BrandName ";
$brandDetailsResult  = $tournamentObj->selectBrandDetails($field,$condition);
$condition       = " and Status !=3";
$field			 = " id as gameId,Name ";
$gameDetailsResult  = $tournamentObj->selectGameDetails($field,$condition);
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['tournament']))
		$_SESSION['mgc_sess_tournament_name'] 	=	$_POST['tournament'];
	if(isset($_POST['game']))
		$_SESSION['mgc_sess_tournament_game'] 	=	$_POST['game'];
	if(isset($_POST['brand']))
		$_SESSION['mgc_sess_brand']	    	=	$_POST['brand'];
	if(isset($_POST['gameType']))
		$_SESSION['mgc_sess_gameType']	=	$_POST['gameType'];
	if(isset($_POST['tournament_status']))
		$_SESSION['mgc_sess_tournament_status']	=	$_POST['tournament_status'];
	
	if(isset($_POST['startdate']) && $_POST['startdate'] != ''){
		$validate_date = dateValidation($_POST['startdate']);
		if($validate_date == 1)
			$_SESSION['mgc_sess_tournament_start']	= $_POST['startdate'];
		else 
			$_SESSION['mgc_sess_tournament_start']	= '';
	}
	else 
		$_SESSION['mgc_sess_tournament_start']	= '';

	if(isset($_POST['enddate']) && $_POST['enddate'] != ''){
		$validate_date = dateValidation($_POST['enddate']);
		if($validate_date == 1)
			$_SESSION['mgc_sess_tournament_end']	= $_POST['enddate'];
		else 
			$_SESSION['mgc_sess_tournament_end']	= '';
	}
	else 
		$_SESSION['mgc_sess_tournament_end']	= '';
}
if(isset($_POST['do_action']) && $_POST['do_action'] != '')	{
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
		$Ids	=	implode(',',$_POST['checkedrecords']);
		if($_POST['bulk_action']==3)
			$delete_id = $Ids;
	}
}

if(isset($_GET['delId']) && $_GET['delId']!='')
	$delete_id      = $_GET['delId'];

if(isset($delete_id) && $delete_id != ''){	
	$tournamentObj->deleteTournamentReleatedEntries($delete_id);
	$_SESSION['notification_msg_code']	=	3;
	header("location:TournamentList");
	die();
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " count(tp.id) as userCount, t.id as tournamentId,t.TournamentName, g.Name as gameName,t.fkBrandsId as brandId,b.BrandName as brandName,t.*";
$condition = " and t.Status != '3' AND b.Status != 3 AND g.Status != 3 ";
if(isset($_GET['brand_id']) && $_GET['brand_id'] != ''){
	$condition .= " and t.fkBrandsId = ".$_GET['brand_id']." " ;
}
$tournamentListResult  = $tournamentObj->getTournamentList($fields,$condition);
$tot_rec 		 = $tournamentObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($tournamentListResult)) {
	$_SESSION['curpage'] = 1;
	$tournamentListResult  = $tournamentObj->getTournamentList($fields,$condition);
}
$ids					=	"";
if(isset($tournamentListResult)	&&	is_array($tournamentListResult)	&&	count($tournamentListResult) > 0){
	foreach($tournamentListResult as $key =>$value){	$ids	.= $value->id.',';	}
	$ids	=	rtrim($ids,',');
	if($ids != ''){
		$fields   				= 	" t.id, c.fkTournamentsId,count(c.id) as chatsCount ";
		$condition 				= 	" and c.fkTournamentsId IN(".$ids.") ";
		$tournamentChatCount  	= 	$tournamentObj->getTournamentChatCount($fields,$condition);
		if(isset($tournamentChatCount)	&&	is_array($tournamentChatCount)	&& count($tournamentChatCount)>0){
			$countArray	=	array();
			foreach($tournamentChatCount as $key =>$value){
				$countArray[$value->fkTournamentsId]	=	$value->chatsCount;
			}
		}
	}
}
$from = '';

if((isset($_GET['from']) && $_GET['from'] != '') && (isset($_GET['brand_id']) && $_GET['brand_id'] != '' )  ){
	$from .= '?from='.$_GET['from'].'&brand_id='.$_GET['brand_id'];
}
?>
<body class="<?php if(isset($_GET['from'])) echo 'popup_bg'; ?>" >

					<?php if(!isset($_GET['from'])){?>
						<?php top_header(); ?>
					<?php } ?>
					
						<?php if(isset($_GET['from'])){?>
						 <div class="box-header"><h2><i class="fa fa-list"></i>
							<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0){ echo $tournamentListResult[0]->brandName.' - '; }?>
							Tournament List</h2>
						 </div>
						 <?php } else { ?>
						 <div class="box-header"><h2><i class="fa fa-list"></i>Tournament List</h2>
						 <span style="float:right"><a href="TournamentManage" title="Add Tournament"><strong><i class="fa fa-plus-circle"></i> Add Tournament</strong></a></span>
						 </div>
						 <?php } ?>
						 
				            <table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" >
								<tr><td height="20"></td></tr>
								<tr>
									<td valign="top" align="center" colspan="2">
										
										<form name="search_category" action="TournamentList<?php if(isset($_GET['from'])) echo $from; ?>" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>													
													<td width="7%" style="padding-left:20px;"><label>Tournament</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input" name="tournament" id="tournament"  value="<?php  if(isset($_SESSION['mgc_sess_tournament_name']) && $_SESSION['mgc_sess_tournament_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_tournament_name']);  ?>" >
													</td>
													<td width="7%" style="padding-left:20px;"><label>Game</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40">
													<select name="game" id="game" style="width:200px;">
															<option value="">Select</option>
															<?php if(isset($gameDetailsResult) && is_array($gameDetailsResult) && count($gameDetailsResult) > 0){
																	foreach($gameDetailsResult as $key => $value) { ?>
																			<option value="<?php echo $value->Name; ?>" <?php
																				if(isset($_SESSION['mgc_sess_tournament_game']) && $_SESSION['mgc_sess_tournament_game'] != ''	&&	$_SESSION['mgc_sess_tournament_game'] == $value->Name) echo 'selected'; ?>><?php echo $value->Name; ?></option>
																<?php	}
																}?>
													</select>
													</td>
													<?php if(!isset($_GET['from'])) { ?>
													<td width="10%" style="padding-left:20px;"><label>Brand</label></td>
													<td width="2%" align="center">:</td>
													<td align="left"  height="40">
														<select name="brand" id="brand" style="width:88%;">
															<option value="">Select</option>
															<?php if(isset($brandDetailsResult) && is_array($brandDetailsResult) && count($brandDetailsResult) > 0){
																	foreach($brandDetailsResult as $key => $value) { ?>
																			<option value="<?php echo $value->BrandName; ?>" <?php
																				if(isset($_SESSION['mgc_sess_brand']) && $_SESSION['mgc_sess_brand'] != ''	&&	$_SESSION['mgc_sess_brand'] == $value->BrandName) echo 'selected'; ?>><?php echo $value->BrandName; ?></option>
																<?php	}
																}?>
														</select>
													</td>
													<?php } ?>
												</tr>
												<tr><td height="10"></td></tr>
												<tr>

													<td width="10%" style="padding-left:20px;" align="left"><label>Start Date</label></td>
													<td width="2%" align="center">:</td>
													<td height="40" align="left" >
														<input style="width:90px;" type="text" autocomplete="off" maxlength="10" class="input datepicker" name="startdate" id="startdate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_tournament_start'])); else echo '';?>" > (mm/dd/yyyy)
													</td>
													<td width="10%" style="padding-left:20px;" ><label>End Date</label></td>
													<td width="2%" align="center">:</td>
													<td height="40" align="left" >
														<input style="width:90px" type="text" autocomplete="off"  maxlength="10" class="input datepicker" name="enddate" id="enddate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_tournament_end'])); else echo '';?>" > (mm/dd/yyyy)
													</td>
												</tr>
												<tr><td height="10"></td></tr>
												<tr>
													<td align="center" colspan="9" ><input type="submit" class="submit_button" name="Search" id="Search" value="Search"></td>
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
												<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0){ ?>
												<td align="left" width="20%">No. of Tournament(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0 ) {
														 	if(isset($_GET['from'])) pagingControlLatest($tot_rec,'TournamentList'.$from); 
															else pagingControlLatest($tot_rec,'TournamentList'); ?>
														<?php }?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td height="10"></td></tr>
								<tr><td colspan= '2' align="center">
									<?php displayNotification('Tournament'); ?>
									</td></tr>
								<tr><td height="10"></td></tr>
								<tr>
									<td colspan="2">
									
								<div class="tbl_scroll">
									  <form action="TournamentList" class="l_form" name="TournamentListForm" id="TournamentListForm"  method="post"> 
										<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">

											<tr align="left">
											<?php if(!isset($_GET['from'])) { ?>
												<th align="center" style="text-align:center" width="3%"><input onclick="checkAllRecords('TournamentListForm');" type="Checkbox" name="checkAll"/></th>
											<?php } ?>
												<th align="center" width="3%">#</th>
												<th width="15%"><?php echo SortColumn('TournamentName','Name'); ?></th>
												<th width="15%">Game</th>
												<?php if(!isset($_GET['from'])) { ?>
												<th width="14%">Brand</th>
												<?php } ?>
												<th width="9%">Prize ($)</th>
												<th width="3%">Max Players</th>
												<th width="10%">Status</th>
												<th width="3%">High Score</th>
												<th width="12%">Start Date</th>
												<th width="12%">End Date</th>
												<?php if(isset($_GET['hide_players'])	&&	$_GET['hide_players']==1 ) ; else { ?>
												<th width="12%">Players</th>
												<?php }?>
												<th width="12%">Chats</th>
											</tr>
											<?php if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0 ) { 
													 foreach($tournamentListResult as $key=>$value){
											 ?>									
											<tr id="test_id_<?php echo $value->id;?>"	>
												<?php if(!isset($_GET['from'])) { ?>
												<td valign="top" align="center"><input id="checkedrecords" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" /></td>
												<?php } ?>
												<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
												<td valign="top">
												<?php if(isset($value->id)	&&	$value->id !=''	&&	isset($value->TournamentName) && $value->TournamentName != ''){ ?>
													<p align="left" >
														<a href="TournamentDetail?viewId=<?php echo $value->id; ?>" title="View" alt="View" class="detailUser" ><?php echo ucfirst($value->TournamentName); ?></a>
													<?php if(isset($value->Type) && $value->Type ==1) echo  '&nbsp;<span class="required_field">*</span>';?>
													</p>
													<?php if(!isset($_GET['from'])) { ?>
													<div class="userAction" style="display:block" id="userAction">
															<a href="TournamentManage?editId=<?php echo $value->id; ?>" title="Edit" alt="Edit" class="editUse"><i class="fa fa-edit fa-lg"></i></a>
															<a href="TournamentDetail?viewId=<?php echo $value->id; ?>" title="View" alt="View" class="viewUser"><i class="fa fa-search-plus fa-lg"></i></a>
															<a onclick="javascript:return confirm('Are you sure to delete?')" href="TournamentList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="deleteUser"><i class="fa fa-trash-o fa-lg"></i></a>
													</div>
													<?php } ?>
												<?php } else echo '-'; ?></td>
												<td><?php if(isset($value->gameName) && $value->gameName != '') echo $value->gameName; else echo '-';?></td>
												<?php if(!isset($_GET['from'])) { ?>
													
												<td valign="top">
												<?php if(isset($value->brandId) && $value->brandId != ''	&&	isset($value->brandName) && $value->brandName != '') { 
													echo '<a class="recordView" style="" href="BrandDetail?viewId='.$value->brandId.'&back=TournamentList">'.trim($value->brandName).'</a>';
													?>
													
												<?php } ?>
												
												</td>
											
											<?php } ?>
												<td valign="top"><?php if(isset($value->Prize) && $value->Prize != 0){ echo $value->Prize; } else echo '-';?></td>	
												<td valign="top"><?php if(isset($value->MaxPlayers) && $value->MaxPlayers != ''){ echo $value->MaxPlayers; } else echo '-';?></td>
											<td valign="top">
											<?php 	if(isset($value->TournamentStatus) && $value->TournamentStatus != ''){
														if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00' ){
															if(date('Y-m-d H:i:s',strtotime($value->StartDate)) < date('Y-m-d')	&&	date('Y-m-d H:i:s',strtotime($value->EndDate)) < date('Y-m-d')	){
																echo $tournamentStatus['3'];
															}
															else if(date('Y-m-d',strtotime($value->StartDate)) <= date('Y-m-d H:i:s')	&&	date('Y-m-d H:i:s',strtotime($value->EndDate)) > date('Y-m-d')){
																echo $tournamentStatus['1'];
															}
															else if(date('Y-m-d',strtotime($value->StartDate)) > date('Y-m-d H:i:s')	&&	date('Y-m-d H:i:s',strtotime($value->EndDate)) > date('Y-m-d')){
																echo $tournamentStatus['0'];
															}
														
														}
													} else echo '-';?>
												</td>	
												<td valign="top"><?php if(isset($value->CurrentHighestScore) && $value->CurrentHighestScore != 0){ echo $value->CurrentHighestScore; } else echo '-';?></td>
												<td valign="top"><?php if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->StartDate)); }else echo '-';?></td>
												<td valign="top"><?php if(isset($value->EndDate) && $value->EndDate != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->EndDate)); }else echo '-';?></td>
											<?php if(isset($_GET['hide_players'])	&&	$_GET['hide_players']==1 ) ; else { ?>
												<td><?php if(isset($value->userCount) && $value->userCount != 0){?> 
												<a href="TournamentPlayedUsers?viewId=<?php echo $value->id; ?>&cs=1" class="players_popup" title="Users played" alt="Users played" class="editUse"><?php echo $value->userCount; ?></a>
												<?php }else echo '-';?></td>
											<?php } ?>
												<td>
													<?php 
													if(isset($countArray)	&& is_array($countArray)	&&	isset($countArray[$value->tournamentId])	&&	isset($countArray[$value->tournamentId]) > 0) { ?>
														<?php if(isset($_GET['brand_id']) && $_GET['brand_id'] != '' ){ ?>
														<a href="TournamentChats?viewId=<?php echo $value->id; if(isset($value->TournamentName)	&&	$value->TournamentName != '') echo '&tournamentName='.$value->TournamentName; ?>&cs=1<?php if(isset($_GET['brand_id']) && $_GET['brand_id'] != '' ) echo '&brand_id='.$_GET['brand_id'];?>" title="Tournament chats" class="tournament_list_pop_up" alt="Chats"><?php echo $countArray[$value->tournamentId]; ?></a>
														<?php } else { ?>
														<a href="TournamentChats?viewId=<?php echo $value->id; if(isset($value->TournamentName)	&&	$value->TournamentName != '') echo '&tournamentName='.$value->TournamentName; ?>&cs=1<?php if(isset($_GET['brand_id']) && $_GET['brand_id'] != '' ) echo '&brand_id='.$_GET['brand_id'];?>" title="Tournament chats" class="tournament_chat_list" alt="Chats"><?php echo $countArray[$value->tournamentId]; ?></a>
														<?php }?>
											<?php } else echo '-'; ?>
												</td>
											</tr>
											<?php } ?> 																		
										</table>
										<?php if(!isset($_GET['from'])) { 
										 if(isset($tournamentListResult) && is_array($tournamentListResult) && count($tournamentListResult) > 0){ 
												bulk_action($tournamentActionArray); ?>
										<?php } } ?>
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
$(".user_image_pop_up").colorbox({title:true});
$("#startdate").datepicker({
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
 $("#enddate").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
	onSelect		: function () { },
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
$(document).ready(function() {		
	$(".pop_up").colorbox(
		{
			iframe:true,
			width:"70%", 
			height:"45%",
			title:true
	});
	$(".players_popup").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"45%",
				title:true,
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
$(".detailUser").on('click',function(){
	var hre	=	$(".detailUser").attr("href");
 	window.parent.location.href = hre+'&back=1';
});
$(".tournament_chat_list").colorbox(
			{
				iframe:true,
				width:"73%", 
				height:"45%",
				title:true
		});

</script>
</html>
