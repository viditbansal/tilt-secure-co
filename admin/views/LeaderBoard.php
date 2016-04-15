<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/GameController.php');
$gameObj   =   new GameController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;

if(isset($_SESSION['referPage']))
	unset($_SESSION['referPage']);
$_SESSION['referPage']	=	'LeaderBoard';

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_userName']);
	unset($_SESSION['mgc_sess_gameStatus']);
	unset($_SESSION['mgc_sess_gameResult']);
	unset($_SESSION['mgc_sess_game_startTime']);
	unset($_SESSION['mgc_sess_game_endTime']);
	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
	
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['username']))
		$_SESSION['mgc_sess_userName'] 	 =	$_POST['username'];
	if(isset($_POST['game_status']))
		$_SESSION['mgc_sess_gameStatus'] =	$_POST['game_status'];
	if(isset($_POST['game_result']))
		$_SESSION['mgc_sess_gameResult'] =	$_POST['game_result'];
	if(isset($_POST['startdate']))
		$_SESSION['mgc_sess_game_startTime']      	= $_POST['startdate'];
	if(isset($_POST['enddate']))
		$_SESSION['mgc_sess_game_endTime']     	= $_POST['enddate']; 

}
if(!isset($_SESSION['mgc_sess_game_startTime'])) 
	$_SESSION['mgc_sess_game_startTime']	=	date('Y-m-d');
if(!isset($_SESSION['mgc_sess_game_endTime'])) 
	$_SESSION['mgc_sess_game_endTime']	=	date('Y-m-d');

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " u.id as userId,u.FirstName,u.LastName,u.Photo,u.Email,g.* ";
$condition = " and u.Status != '3' ";

$leaderBoardResult  = $gameObj->getLeadersBoardList($fields,$condition);
$tot_rec 		 = $gameObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($leaderBoardResult)) {
	$_SESSION['curpage'] = 1;
	$leaderBoardResult  = $gameObj->getLeadersBoardList($fields,$condition);
}
?>
<body>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">					
					<tr><td colspan="2" class="headermenu"><?php top_header(); ?></td></tr>
					<tr><td height="30"></td></tr>
				    <tr>
					<td colspan="2">
						 <div class="left_menu sidebar-nav" style="float:left;margin-left:30px;margin-right:20px;"><?php side_bar()?></div>
 						 <div id="content_3" class="content">
						 <div class="box-header"><h2><i class="icon_userlist"></i>Leader Board</h2>
						 <span style="float:right"></span></div>
				            <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
								<tr><td height="20"></td></tr>
								<tr>
									<td colspan="2">
										<form name="search_category" action="LeaderBoard" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>													
													<td width="7%" style="padding-left:20px;"><label>User</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input" name="username" id="username"  value="<?php  if(isset($_SESSION['mgc_sess_userName']) && $_SESSION['mgc_sess_userName'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_userName']);  ?>" >
				
													</td>
													<td width="10%" style="padding-left:20px;"><label>Game Status</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<select name="game_status" id="game_status" tabindex="2" title="Select Game Status" style="width:100px;">
															<option value="">Select</option>
														<?php if( isset($gameStatus) )
																foreach($gameStatus as $key => $game_status) { ?>
															<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_gameStatus']) && $_SESSION['mgc_sess_gameStatus'] != '' && $_SESSION['mgc_sess_gameStatus'] == $key) echo 'Selected';  ?>><?php echo $game_status; ?></option>
														<?php 	} ?>
														</select>
													</td>
													<td width="10%" style="padding-left:20px;"><label>Result</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<select name="game_result" id="game_result" tabindex="2" title="Game Result" style="width:80px;">
															<option value="">Select</option>
														<?php if( isset($gameResultStatus) )
																foreach($gameResultStatus as $key => $game_status) { ?>
															<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['mgc_sess_gameResult']) && $_SESSION['mgc_sess_gameResult'] != '' && $_SESSION['mgc_sess_gameResult'] == $key) echo 'Selected';  ?>><?php echo $game_status; ?></option>
														<?php 	}	?>
														</select>
													</td>
												</tr>
												<tr><td height="10"></td></tr>
												<tr>
													<td width="10%" style="padding-left:20px;" align="left"><label>Start Date</label></td>
													<td width="3%" align="center">:</td>
													<td height="40" align="left" >
														<input style="width:150px" type="text"  maxlength="10" class="input" name="startdate" id="startdate" title="Select start date" value="<?php if(isset($_SESSION['mgc_sess_game_startTime']) && $_SESSION['mgc_sess_game_startTime'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_game_startTime'])); else echo '';?>" > (mm/dd/yyyy)
													</td>																									
													<td width="10%" style="padding-left:20px;" align="left"><label>End Date</label></td>
													<td width="3%" align="center">:</td>
													<td height="40" align="left" colspan="4">
														<input style="width:150px" type="text"  maxlength="10" class="input" name="enddate" id="enddate" title="Select end date" value="<?php if(isset($_SESSION['mgc_sess_game_endTime']) && $_SESSION['mgc_sess_game_endTime'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_game_endTime'])); else echo '';?>" > (mm/dd/yyyy)
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
												<?php if(isset($leaderBoardResult) && is_array($leaderBoardResult) && count($leaderBoardResult) > 0){ ?>
												<td align="left" width="20%">No. of User(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(isset($leaderBoardResult) && is_array($leaderBoardResult) && count($leaderBoardResult) > 0 ) {
														 	pagingControlLatest($tot_rec,'LeaderBoard'); ?>
														<?php }?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td height="10"></td></tr>
								<tr>	<td colspan= '2' align="center">	<?php displayNotification(); ?> </td>	</tr>
								<tr><td height="10"></td></tr>
								<tr>
									<td colspan="2">
									<?php if(isset($leaderBoardResult) && is_array($leaderBoardResult) && count($leaderBoardResult) > 0 ) { ?>
									  <form action="LeaderBoard" class="l_form" name="LeaderBoardForm" id="LeaderBoardForm"  method="post"> 
										<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
											<tr align="left">
												<th align="center" width="1%">#</th>
												<th width="15%"><?php echo SortColumn('FirstName','User');?></th>
												<th width="8%"><?php echo SortColumn('StartTime','Start Time');?></th>
												<th width="8%"><?php echo SortColumn('EndTime','End Time'); ?></th>
												<th width="6%"><?php echo SortColumn('Points','Points'); ?></th>
												<th width="5%"><?php echo SortColumn('Status','Status'); ?></th>
												<th width="5%"><?php echo SortColumn('GameStatus','Result'); ?></th>
											</tr>
											<?php foreach($leaderBoardResult as $key=>$value){
													$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
														$original_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
														$photo = $value->Photo;
														if(isset($photo) && $photo != ''){
															$user_image = $photo;		
															$image_path_rel = USER_THUMB_IMAGE_PATH_REL.$user_image;
															$original_path_rel = USER_IMAGE_PATH_REL.$user_image;
															if(SERVER){
																if(image_exists(1,$user_image)){
																	$image_path = USER_THUMB_IMAGE_PATH.$user_image;
																	$original_path = USER_IMAGE_PATH.$user_image;
																}
															
															}
															else if(file_exists($image_path_rel)){
																	$image_path = USER_THUMB_IMAGE_PATH.$user_image;
																	$original_path = USER_IMAGE_PATH.$user_image;
															}
														}
													$userName	=	'';
													if(isset($value->FirstName)	&&	isset($value->LastName)	&&	$value->LastName !=''	&&	$value->FirstName !='') 	
														$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
													else if(isset($value->FirstName)		&&	$value->FirstName !='')	
														$userName	=	 ucfirst($value->FirstName);
													else if(isset($value->LastName)	&&	$value->LastName !='')	
														$userName	=	ucfirst($value->LastName);
											 ?>									
											<tr id="test_id_<?php echo $value->id;?>"	>
												<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
												<td valign="top" >	
													<div  style="<?php if(isset($cover_path) && $cover_path != '' ) { ?>
																			background: url('<?php echo $cover_path;?>') no-repeat;<?php 
																		} else { 
																			?>background: none no-repeat; 
																<?php 	} ?>;background-size:cover;float:left">
																<a <?php if(isset($original_path) && $original_path != ADMIN_IMAGE_PATH.'no_user.jpeg' ) { ?> href="<?php echo $original_path; ?>" class="user_image_pop_up"  <?php } ?> title="View Photo"  ><img  class="img_border" width="36" height="36" src="<?php echo $image_path;?>" ></a>
													</div>
													<div class="user_profile">
														
														<p align="left" style="padding-left:50px; ">
													<?php 	if(isset($userName) && $userName != '' ){
																if(isset($value->userId) && $value->userId != '' )
																{
																	echo '<a href="UserDetail?viewId='.$value->userId.'&referList=1" >'.$userName.'</a>';
																}else echo  $userName;
															}
														?>
														</p>
														<p align="left" style="padding-left:50px; margin-top:8px;">
													<?php 	if(isset($value->Email) && $value->Email != '' ){ 
																if(isset($value->userId) && $value->userId != '' &&	$userName == '')
																{
																	echo '<a href="UserDetail?viewId='.$value->userId.'&referList=1" style="color:#737373;">'.$value->Email.'</a>';
																}else echo $value->Email;
															}else echo '-';?>
														</p>
													</div>

												</td>
												<td valign="top">
												  <?php if(isset($value->StartTime) && $value->StartTime != '0000-00-00 00:00:00'){
															$gmt_current_start_time = convertIntocheckinGmtSite($value->StartTime);
															$start_time	=  displayConversationDateTime($gmt_current_start_time,$_SESSION['mgc_ses_from_timeZone']);
															echo $start_time; 
														
														
														}else echo '-';?>
												</td>
												<td valign="top">
												  <?php if(isset($value->EndTime) && $value->EndTime != '0000-00-00 00:00:00'){
															$gmt_current_start_time = convertIntocheckinGmtSite($value->EndTime);
															$end_time	=  displayConversationDateTime($gmt_current_start_time,$_SESSION['mgc_ses_from_timeZone']);
															echo $end_time; 
														
														}else echo '-';?>
												</td>
												<td valign="top"><?php if(isset($value->Points) && $value->Points != '' ){ echo $value->Points;}else echo '-';?></td>
												<td valign="top"><?php if(isset($value->Status) && $value->Status != '' ){ if( isset($gameStatus[$value->Status]) )echo $gameStatus[$value->Status];else echo '-';}else echo '-';?></td>
												<td valign="top">
												<?php 
												if(isset($value->Status) && $value->Status == 2 ){
													if(isset($value->GameStatus) && $value->GameStatus != '' ){ 
														if( isset($gameResultStatus[$value->GameStatus]) )
															echo $gameResultStatus[$value->GameStatus];
														else echo '-';
													}else echo '-';
												}else echo 'Playing';
												?>
												</td>
											</tr>
											<?php } ?> 																		
										</table>
										<tr><td height="30"></td></tr>
										<?php if(isset($leaderBoardResult) && is_array($leaderBoardResult) && count($leaderBoardResult) > 0){ ?>

										<?php } ?>
										</form>
										<?php } else { ?>	
											<tr>
												<td colspan="16" align="center" style="color:red;">No Record(s) Found</td>
											</tr>
											<tr><td height="30"></td></tr>
										<?php } ?>
									</td>
								</tr>
				            </table>
				        </div>
				     </td></tr>
				</table>
			</td>
		</tr>
		<tr><td height="20"></td></tr>
	</table>
</body>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".user_image_pop_up").colorbox({title:true});
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
</script>
</html>
