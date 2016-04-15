<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();

require_once('controllers/GameController.php');
$gameObj   =   new GameController();
require_once('controllers/TournamentController.php');
$tourObj   =   new TournamentController();

require_once('controllers/AdminController.php');
$adminObj   =   new AdminController();

$display		=   'none';
$class			=	$msg	=	$cover_path = '';
$updateStatus	=	1;
if(!isset($_GET['statistics'])) {
	unset($_SESSION['mgc_sess_from_date']);
	unset($_SESSION['mgc_sess_to_date']);
}
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_game_name']);
	unset($_SESSION['mgc_sess_user_name']);
	unset($_SESSION['mgc_sess_user_status']);
	unset($_SESSION['mgc_sess_location']);
	unset($_SESSION['mgc_sess_user_registerdate']);
	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['ses_gamename']))
		$_SESSION['mgc_sess_game_name'] 	=	trim($_POST['ses_gamename']);
	if(isset($_POST['ses_status']))
		$_SESSION['mgc_sess_user_status']	=	$_POST['ses_status'];
	if(isset($_POST['tilt_key']))
		$_SESSION['mgc_sess_game_tiltKey']	=	trim($_POST['tilt_key']);
	if(isset($_POST['created_by']))
		$_SESSION['mgc_sess_game_createdBy']	=	trim($_POST['created_by']);	
}

if(isset($_POST['do_action']) && $_POST['do_action'] != '')	{
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
		$Ids	=	implode(',',$_POST['checkedrecords']);
	}
}
if(isset($_GET['approveId']) && $_GET['approveId'] !=''){
	$delete_id		=	$_GET['approveId'];
	$updateStatus	= 1;
	$_SESSION['notification_msg_code']	=	6;
}else if(isset($_GET['disApproveId']) && $_GET['disApproveId']!=''){
	$delete_id	=	$_GET['disApproveId'];
	$updateStatus = 2;
	$_SESSION['notification_msg_code']	=	4;
}
else if(isset($_GET['rejectId']) && $_GET['rejectId']!=''){
	$delete_id	=	$_GET['rejectId'];
	$updateStatus = 4;
	$_SESSION['notification_msg_code']	=	7;
}


if(isset($_GET['delId']) && $_GET['delId']!='')	{
	$delete_id      = $_GET['delId'];
	$updateStatus 	= 3;
	$_SESSION['notification_msg_code']	=	3;
}
$today = date('Y-m-d H:i');
$count = $notifFlag = 0;

$newDeleteId	=	'';
if(isset($delete_id) && $delete_id != ''){	
	if($updateStatus == 3){
		$deleteIdsArr	=	explode(',',$delete_id);
		if(isset($deleteIdsArr) && is_array($deleteIdsArr) && count($deleteIdsArr)>0){
			$count	=	count($deleteIdsArr);
			foreach($deleteIdsArr as $deleteKey =>$delete_id){
				$fields = "id,fkGamesId,TournamentName,TournamentStatus,Status,GameType,Type,StartDate,EndDate";
				$condition = " AND fkGamesId = ".$delete_id." AND Status = 1 ";
				$tourListRes	=	$tourObj->tournamentList($fields,$condition);
				if(isset($tourListRes) && is_array($tourListRes) && count($tourListRes)>0) ;
				else { $newDeleteId .= $delete_id.','; $notifFlag++;}
			}
			$newDeleteId = rtrim($newDeleteId,',');
			if(!empty($newDeleteId)){
				$gameObj->changeGameStatus($newDeleteId,$updateStatus);
			}
			 if($count != $notifFlag)
				$_SESSION['notification_msg_code']	= 13;
			else 
				$_SESSION['notification_msg_code']	= 3;
		}
	}else{
		$gameObj->changeGameStatus($delete_id,$updateStatus);
	}
	header("location:GameList");
	die();
}

if(isset($_GET['editId']) && $_GET['editId']!=''	&& isset($_GET['status'])	&&	$_GET['status']!=''){
	$condition = " id = ".$_GET['editId'];
	$update_string = " Status = ".$_GET['status'];
	$gameListResult  = $gameObj->updateGameDetails($update_string,$condition);
	$_SESSION['notification_msg_code']	=	4;
	header("location:GameList");
	die();
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " g.*,gd.UserName,gd.Company,gd.Status as devStatus,gp.Certificate as certificateName";
$condition = " AND g.Status not in(3,5) ";
if(isset($_GET['statistics']) && $_GET['statistics'] == '1'){
	$condition = ' and g.Status not in(3,5) ';
	if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != ''){
			$condition .= " AND ((date(g.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."' and date(g.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '')
			$condition .= " AND date(g.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '')
			$condition .= " AND date(g.DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."'";
}
$gameListResult  = $gameObj->getGameList($fields,$condition);
$tot_rec 		 = $gameObj->getTotalRecordCount();
$statistics	=	'';
if(isset($_GET['statistics'])	&&	$_GET['statistics']	==	1){
	$statistics	=	'?statistics=1';
}
$gameStatusArray	= array('1'=>'Active','4'=>'Inactive');
?>
<body>
<?php if(!isset($_GET['statistics']))
			top_header(); 
?>						 
	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i>Game List</h2>
	<?php if(!isset($_GET['statistics'])) { ?>	
	 	<span class="fright"><a href="GameManage" title="Add Game"><i class="fa fa-plus-circle"></i> Add Game</a></span>
	<?php } ?>
	</div>
	<div class="clear">
           <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
			
			
			<tr><td class="filter_form" >
				<form name="search_category" action="GameList<?php echo $statistics;?>" method="post">
            	<table align="center" cellpadding="6" cellspacing="0" border="0"width="98%">									       
					<tr><td></td></tr>
					<tr>													
						<td width="10%" ><label>Game Name</label></td>
						<td width="2%" align="center">:</td>
						<td align="left" width="20%">
							<input type="text" class="input" name="ses_gamename" id="ses_gamename"  value="<?php  if(isset($_SESSION['mgc_sess_game_name']) && $_SESSION['mgc_sess_game_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_game_name']);  ?>" >
						</td>
						<td width="10%"><label>Status</label></td>
						<td width="2%" align="center">:</td>
						<td width="30%" >
							<select name="ses_status" id="ses_status" tabindex="2" title="Select Status" style="width:40%;">
								<option value="">Select</option>
							<?php $i=1; 
									foreach($gameStatusArray as $key => $user_status) { 
										?>
								<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_user_status']) && $_SESSION['mgc_sess_user_status'] != '' && $_SESSION['mgc_sess_user_status'] == $key) echo 'Selected';  ?>><?php echo $user_status; ?></option>
							<?php 	 
									}?>
							</select>
						</td>
					</tr>
					<tr><td height="10" style="padding: 0"></td></tr>
					<tr>
						<td width="15%" ><label>Game Key</label></td>
						<td width="2%" align="center">:</td>
						<td align="left" width="33%">
							<input type="text" class="input" name="tilt_key" id="tilt_key"  value="<?php  if(isset($_SESSION['mgc_sess_game_tiltKey']) && $_SESSION['mgc_sess_game_tiltKey'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_game_tiltKey']);  ?>" >
						</td>
						<td width="15%" ><label>Created By</label></td>
						<td width="2%" align="center">:</td>
						<td align="left" width="33%">
							<input type="text" class="input" id="created_by" name="created_by"  value="<?php  if(isset($_SESSION['mgc_sess_game_createdBy']) && $_SESSION['mgc_sess_game_createdBy'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_game_createdBy']);  ?>" >
						</td>
					</tr>
					<tr><td align="center" colspan="9" style="padding-top:20px"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
					<tr><td></td></tr>
				 </table>
				 </form>
			</td></tr> 
			<tr><td height="20"></td></tr>
			<tr><td>
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<?php if(isset($gameListResult) && is_array($gameListResult) && count($gameListResult) > 0){ ?>
						<td align="left" width="20%">No. of Game(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
								<?php if(isset($gameListResult) && is_array($gameListResult) && count($gameListResult) > 0 ) {
								 	pagingControlLatest($tot_rec,'GameList'.$statistics); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td></tr>
			<tr><td height="10"></td></tr>
			<tr><td colspan= '2' align="center">
				<?php displayNotification('Game'); ?>
			</td></tr>
			<tr><td height="10"></td></tr>
			<tr><td>
			<div class="tbl_scroll">
				
				  <form action="GameList<?php echo $statistics;?>" class="l_form" name="GameListForm" id="GameListForm"  method="post"> 
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
						<tr align="left">
						<?php if(!isset($_GET['statistics'])) { ?>
						<?php } ?>
							<th align="center" style="text-align:center" width="3%">#</th>												
							<th width="17%"><?php echo SortColumn('Name','Game'); ?></th>
							<th width="13%">Created By</th>
							<th width="12%">iTunes URL</th>
							 <th width="10%">Status</th>
							<th width="7%">Game Key</th>
							<th width="10%">Push Certificate(.p12)</th>
							
						</tr>
						<?php if(isset($gameListResult) && is_array($gameListResult) && count($gameListResult) > 0 ) { ?>
						
						<?php foreach($gameListResult as $key=>$value){
									$image_path = ADMIN_IMAGE_PATH.'add_game.jpg';
									$original_path = ADMIN_IMAGE_PATH.'add_game.jpg';
									$photo = $value->Photo;
									if(isset($photo) && $photo != ''){
										$user_image = $photo;		
										$image_path_rel = GAMES_THUMB_IMAGE_PATH_REL.$user_image;
										$original_path_rel = GAMES_IMAGE_PATH_REL.$user_image;
										if(SERVER){
											if(image_exists(1,$user_image)){
												$image_path = GAMES_THUMB_IMAGE_PATH.$user_image;
												$original_path = GAMES_IMAGE_PATH.$user_image;
											}
										
										}
										else if(file_exists($image_path_rel)){
												$image_path = GAMES_THUMB_IMAGE_PATH.$user_image;
												$original_path = GAMES_IMAGE_PATH.$user_image;
										}
									}
									$userName	= $certificateName = '';
									$gameId 	= $value->id;
									if(isset($value->certificateName) && !empty($value->certificateName)){
										$certificateName = $value->certificateName;
										if(SERVER){
											if(!image_exists(19,$gameId.'/'.$certificateName)) 
												$certificateName = '';
										}else{
											if(!file_exists(GAME_CERTIFICATE_PATH_REL.$gameId.'/'.$certificateName))
												$certificateName = '';
										}
									}
						 ?>									
						<tr id="test_id_<?php echo $value->id;?>"	>
						<?php if(!isset($_GET['statistics'])) { ?>
						<?php } ?>
							<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							
							<td valign="top" align="center" >
								
								<div  style="<?php if(isset($cover_path) && $cover_path != '' ) { ?>
														background: url('<?php echo $cover_path;?>') no-repeat;<?php 
													} else { 
														?>background: none no-repeat; 
											<?php 	} ?>;background-size:cover;float:left">
									
									<?php if(isset($_GET['statistics'])) { ?>
									<img class="user_img" width="36" height="36" src="<?php echo $image_path;?>" >
									<?php }else{ ?>
									<a <?php if(isset($original_path) && $original_path != ADMIN_IMAGE_PATH.'add_game.jpg' ) { ?> href="<?php echo $original_path; ?>" class="gamelogo"  title="View Photo" <?php } ?> ><img class="user_img" width="36" height="36" src="<?php echo $image_path;?>" ></a>
									<?php } ?>
								</div>
								<div class="user_profile">
									<p align="left" style="padding-left:50px">
										<?php if(isset($value->Name) && $value->Name != '')		{	?>
										<a href="javascript:void(0);" class="recordView <?php if(isset($_GET['statistics']) && $_GET['statistics'] == '1'){ echo 'game_list_pop_up'; } ?>" onclick="location.href='GameDetail?viewId=<?php echo $value->id; if(isset($_GET['statistics']) && $_GET['statistics'] == '1'){ echo '&statistics=1'; }?>';"><?php echo trim($value->Name); } else echo ' - '; ?></a>			
									</p>
							<?php if(!isset($_GET['statistics'])) { ?>
									<div class="userAction" style="display:block" id="userAction">
									<?php if(isset($value->Status)	&&	$value->Status == 4) { ?>			
											<a class="userIcon" style="color:gray"  title="Inactive Game" alt="Inactive Game" onclick="javascript:return confirm('Are you sure to active this game?')" href="GameList?editId=<?php echo $value->id;?>&status=1"><i class="fa fa-gamepad fa-2x"></i></a>
									<?php } else { ?>
											<a class="userIcon" alt="Active Game" title="Active Game" onclick="javascript:return confirm('Are you sure to inactive this game?')" href="GameList?editId=<?php echo $value->id;?>&status=4"><i class="fa fa-gamepad  fa-2x"></i></a>
									<?php } ?>
										<a href="GameManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="editUser"><i class="fa fa-edit fa-lg"></i></a>
										<a href="GameDetail?viewId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="View" alt="View" class="viewUser"><i class="fa fa-search-plus fa-lg"></i></a>
									</div>
							<?php } ?>
								</div>
							</td>
							<td valign="top">
							<?php if(isset($value->DevelopedBy) && $value->DevelopedBy != 0 ){ 
									if(isset($value->fkDevelopersId) && $value->fkDevelopersId != 0 ){ 
										if(isset($value->devStatus) && $value->devStatus == 1 )
											if(!isset($_GET['statistics'])) {
												if($value->Company != '')
													echo '<a href="GameDeveloperDetail?viewId='.$value->fkDevelopersId.'&back=GameList" >'.ucFirst($value->Company).'</a>'; 
												else
													echo '-';
											}
											else
												echo ucFirst($value->Company);
										else echo ucFirst($value->Company);
									} else echo 'Admin';
								} else echo 'Admin'; ?></td>
							<td valign="top" class="brk_wrd"><?php if(isset($value->ITunesUrl) && $value->ITunesUrl != ''){ echo $value->ITunesUrl; }else echo '-';?></td>	
							 <td valign="top"> 
								<?php if($value->Status != '' && isset($gameStatusArray[$value->Status])) 
											echo $gameStatusArray[$value->Status];
									  else	echo "-"; ?>
							 </td> 
							<td valign="top"><?php if(isset($value->TiltKey) && $value->TiltKey != ''){ echo $value->TiltKey; }else echo '-';?></td>
							<td valign="top" align="center" ><p class="certificate_name"><?php if(isset($value->IosStatus) && $value->IosStatus == 1 && !empty($certificateName)) echo '&nbsp;<a target="_blank" href="Download?gameId='.$value->id.'&fileName='.urlencode($certificateName).'" ><i class="fa fa-download fa-2x"></i></a>'; ?></p></td>
							
						</tr>
						<?php } ?> 																		
					</table>
					 
					</form>
					<?php } else { ?>	
						<tr>
							<td colspan="16" align="center" style="color:red;">No Game(s) Found</td>
						</tr>
					<?php } ?>
				</div>
				
			</td></tr>
           </table>
       </div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".gamelogo").colorbox({
	title:true,
	maxWidth:"50%", 
	maxHeight:"50%"
});	
$("#ses_date").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	maxDate			:	"0",
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
   var maxHeight = '550';
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

$(document).ready(function() {		
		$(".game_list_pop_up").colorbox(
			{
				iframe:true,
				width:"73%", 
				height:"45%",
				title:true
		});
});
	
</script>
</html>
