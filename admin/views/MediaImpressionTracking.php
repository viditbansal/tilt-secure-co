<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$mediaObj   =   new LogController();
$display   	=   'none';
$class 		=  $msg    = $cover_path = $userName = '';

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
}

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['sess_turns_tournament_name']);
	unset($_SESSION['sess_turns_total']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['tournamentName'])){
		$_SESSION['sess_media_tournament_name']     = trim($_POST['tournamentName']);
	}
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields		= 'mi.id,sum(case when mi.Type=0 then 1 else 0 end) as clickCount, sum(case when mi.Type=1 then 1 else 0 end) as impCount,
				g.Name as gameName,g.Photo,t.TournamentName,mi.CreatedDate,mi.fkTournamentsId as TournamentsId,tcal.CouponAdLink,
				tcal.fkTournamentsId,tcal.File';

$condition 	= ' and u.Status != 3 and t.Status !=3 and tcal.Status !=3 and tcal.Type = 2';
$mediaTrackResult	= $mediaObj->mediaTrackDetails($fields,$condition);
$tot_rec 		 	= $mediaObj->getTotalRecordCount();
?>

<body>
	<?php top_header(); ?>
						 <div class="box-header"><h2><i class="fa fa-list"></i>Media Impression Tracking</h2></div>
				            <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
								
								<tr>
									<td colspan="2">
									<form name="search_category" action="MediaImpressionTracking" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>
													<td align="left" style="padding-left:20px;width:50px;"><label>Tournament Name</label></td>
													<td align="center" style="width:2px;">:</td>
													<td align="left" height="40" style="width:200px">
														<input type="text"  class="input" autocomplete="off"   title="Tournament Name" id="tournamentName" name="tournamentName" value="<?php if(isset($_POST['tournamentName']) && $_POST['tournamentName'] != '') echo stripslashes($_POST['tournamentName']);?>">
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
												<?php if(isset($mediaTrackResult) && is_array($mediaTrackResult) && count($mediaTrackResult) > 0){ ?>
												<td align="left" width="20%">No. of Media(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(isset($mediaTrackResult)	&&	is_array($mediaTrackResult) && count($mediaTrackResult) > 0 ) {
														 	pagingControlLatest($tot_rec,'MediaImpressionTracking'); ?>
														<?php }?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td colspan="2">
										<div class="tbl_scroll">											
											  <form action="MediaImpressionTracking" class="l_form" name="MediaImpressionTrackingForm" id="MediaImpressionTrackingForm"  method="post"> 
											<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions" id="fixed">
												<tr>
													<th width="1%" align="center" class="text-center">#</th>
													<th width="10%">Tournament Name</th>
													<th width="10%">Game</th>
													<th width="15%">Banner Content</th>
													<th width="15%">Banner Ad</th>
													<th width="5%">Click Count</th>
													<th width="5%">Impression Count</th>
													<th width="5%">Date Created</th> 
												<?php if(isset($mediaTrackResult) && is_array($mediaTrackResult) && count($mediaTrackResult) > 0 ) { 
														 foreach($mediaTrackResult as $key=>$value){ 
												?>									
												<tr >
													<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
													<td valign="top"><?php if(isset($value->TournamentName) && $value->TournamentName != ''){ 
																echo  ucfirst($value->TournamentName);
													}else echo '-';?> </td>
													<?php 
															$game_image_path = ADMIN_IMAGE_PATH.'add_game.jpg';
															$game_original_path = ADMIN_IMAGE_PATH.'add_game.jpg';
															$game_photo = $value->Photo;
															if(isset($game_photo) && $game_photo != ''){
																$game_image = $game_photo;		
																$game_image_path_rel = GAMES_THUMB_IMAGE_PATH_REL.$game_image;
																$game_original_path_rel = GAMES_IMAGE_PATH_REL.$game_image;
																if(SERVER){
																	if(image_exists(1,$game_image)){
																		$game_image_path = GAMES_THUMB_IMAGE_PATH.$game_image;
																		$game_original_path = GAMES_IMAGE_PATH.$game_image;
																	}
																
																}
																else if(file_exists($game_image_path_rel)){
																		$game_image_path = GAMES_THUMB_IMAGE_PATH.$game_image;
																		$game_original_path = GAMES_IMAGE_PATH.$game_image;
																}
															}?>
													<td valign="" >
													<div style="background-size:cover;float:left">
													<a <?php if(isset($game_original_path) && $game_original_path != ADMIN_IMAGE_PATH.'add_game.jpg' ) { ?> href="<?php echo $game_original_path; ?>" class="fancybox"  <?php } ?> title="View Photo"><img class="user_img" width="36" height="36" src="<?php echo $game_image_path;?>"></a>
													</div>
													<div class="user_profile">
														<p align="left" style="padding-left:50px">
															<?php if(isset($value->gameName) && $value->gameName != ''){ 
																		echo  $value->gameName;
															}else echo '-';?>
														</p>
													</div>
													</td>
													<td valign="top">
													<?php 
													if(isset($value->CouponAdLink) && $value->CouponAdLink != ''){ 
															echo  nl2br(displayText($value->CouponAdLink,200));
													}else  { echo '-'; }?> </td>
													<td valign="top">
													<?php 
															$photo 			= $value->File;
															$ext = pathinfo($photo, PATHINFO_EXTENSION);
															
															$image_path 	= ADMIN_IMAGE_PATH.'no_user.jpeg';
															$original_path 	= ADMIN_IMAGE_PATH.'no_user.jpeg';
															if(isset($photo) && $photo != ''){
																$user_image 		= $photo;
																$image_path_rel 	= BANNER_IMAGE_PATH_REL.$value->fkTournamentsId.'/'.$user_image;
																$original_path_rel 	= BANNER_IMAGE_PATH_REL.$value->fkTournamentsId.'/'.$user_image;
																if(SERVER){
																	if(image_exists(12,$user_image)){
																		$image_path 	= BANNER_IMAGE_PATH.$value->fkTournamentsId.'/'.$user_image;
																		$original_path 	= BANNER_IMAGE_PATH.$value->fkTournamentsId.'/'.$user_image;
																	}
																}
																else if(file_exists($image_path_rel)){
																		$image_path 	= BANNER_IMAGE_PATH.$value->fkTournamentsId.'/'.$user_image;
																		$original_path 	= BANNER_IMAGE_PATH.$value->fkTournamentsId.'/'.$user_image;
																}
															}
															
														   if($ext == 'mp4') {
																echo '<a href="'.$image_path.'" class="video_pop_up"  title="View Photo">'.$photo.'</a>';
														   }else  { ?>
													<a <?php if(isset($original_path) && $original_path != ADMIN_IMAGE_PATH.'no_user.jpeg' ) { ?> href="<?php echo $original_path; ?>" class="fancybox"  <?php } ?> title="View Photo"><img class="user_img"  height="66" src="<?php echo $image_path;?>"></a>
													<?php }?>
													</td>
													<td valign="top" align="center"><?php if(isset($value->clickCount) && $value->clickCount != '0'){ ?>
														<a href='ImpressionClickList?cs=1&id=<?php echo $value->TournamentsId; ?>' class='count'><?php echo $value->clickCount; ?> </a> <?php }else { echo '0';} ?>
													</td>
													<td valign="top" align="center"><?php if(isset($value->impCount) && $value->impCount != '0'){ ?>
														<a href='ImpressionList?cs=1&id=<?php echo $value->TournamentsId; ?>' class='count'><?php echo $value->impCount; ?> </a> <?php }else { echo '0';} ?>
													</td>
													<td valign="top"><?php if(isset($value->CreatedDate) && $value->CreatedDate != '0000-00-00 00:00:00'){ 
															echo date('m/d/Y',strtotime($value->CreatedDate)); }else { echo '-';} ?></td>
												</tr>
												<?php } ?>
												<?php }else{ ?>	
													<tr>
														<td colspan="16" align="center" style="color:red;">No Result(s) Found</td>
													</tr>													
												<?php    } ?>
												
												</table>
												</form>
											</div>
											
										</td>
									</tr>
								
				            </table>
<?php commonFooter(); ?>
<script type="text/javascript">
	$(".fancybox").colorbox({
		title:true,
		maxWidth:"50%", 
		maxHeight:"50%"
	});	
	$(".count").colorbox({
		iframe:true,
		width:"50%", 
		height:"70%"
	});	
	$(".video_pop_up").colorbox({
		iframe:true,
		width:"45%", 
		height:"65%",
		title:true
	});
 </script>
</html>
