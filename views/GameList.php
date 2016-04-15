<?php 
require_once('includes/CommonIncludes.php');
developer_login_check();
commonHead();

require_once('controllers/GameController.php');
$gameObj   =   new GameController();

$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['tilt_sess_game_name']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['ses_gamename']))
		$_SESSION['tilt_sess_game_name'] 	=	trim($_POST['ses_gamename']);
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$devId		=	$_SESSION['tilt_developer_id'];
$fields     = 	" g.*";
$condition	=	" AND fkDevelopersId = ".$devId." AND DevelopedBy = 1 AND g.Status = 1  "; //g.Status != 3  //g.Status = 1
$gameListResult  = $gameObj->GameList($fields,$condition);
$tot_rec 		 = $gameObj->getTotalRecordCount();
$statistics	=	'';
?>
<body class="skin-black">
<?php 	top_header(); ?>	
	<section class="content-header">
		<h2 align="center">Game List</h2>
	</section>
   	<section class="content">
		<div class="row search_group">
			<form name="search_category" action="GameList<?php echo $statistics;?>" method="post">
				<div class="box-body col-md-6 col-lg-4 box-center">
					<div class="form-group col-sm-6 box-center marg30">
						<input type="text" class="form-control" placeholder="Game Name" name="ses_gamename" id="ses_gamename"  value="<?php  if(isset($_SESSION['tilt_sess_game_name']) && $_SESSION['tilt_sess_game_name'] != '') echo unEscapeSpecialCharacters($_SESSION['tilt_sess_game_name']);  ?>" >
					</div>
				</div>
				<div class="box-footer clear" align="center">
					<input type="submit" class="btn btn-green" name="Search" id="Search" title="Search" alt="Search" value="Search">
				</div>
			</form>
		</div>
		<div style="height: 20px" class="clear"></div>
		<div class="col-lg-3 col-sm-12">
			<?php if(isset($gameListResult) && is_array($gameListResult) && count($gameListResult) > 0){ ?>
			No. of Game(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong>
			<?php } ?>
		</div>
		<div class="col-lg-8 col-sm-12"><?php displayNotification('Game'); ?></div>
		<div style="height: 20px" class="clear"></div>
		<div class="col-xs-12">
			<form action="GameList<?php echo $statistics;?>" class="l_form" name="GameListForm" id="GameListForm"  method="post"> 
			<div class=" table-responsive no-margin">
				<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-responsive" width="100%">
					<tr>
						<th align="center" width="3%">#</th>
						<th align="center">Game</th>
						<th align="center">iTunes URL</th>
						<th align="center">Game Key</th>						
						<th align="center" style="text-align:center;">Action</th>
					</tr>
					<?php if(isset($gameListResult) && is_array($gameListResult) && count($gameListResult) > 0 ) { ?>
					<?php foreach($gameListResult as $key=>$value){
						$image_path = GAME_IMAGE_PATH.'add_game.jpg';
						$original_path = GAME_IMAGE_PATH.'add_game.jpg';
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
						 ?>	
						 <tr id="test_id_<?php echo $value->id;?>"	>
						 	<td width="3%"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td width="20%">
								<div class="wrapp_rext" style="width:230px;"><a <?php if(isset($original_path) && $original_path != GAME_IMAGE_PATH.'add_game.jpg' ) { ?> href="<?php echo $original_path; ?>" class="gamelogo"  title="View Photo" <?php } ?> ><img class="user_img" width="36" height="36" src="<?php echo $image_path;?>" ></a>
								<div class="game_name">
								<?php if(isset($value->Name) && $value->Name != '')		{	
										 echo trim($value->Name); 
									} else echo ' - '; ?>									
								</div>
								</div>
							</td>
							<td valign="top" width="50%"><div class="wrapp_rext" style="width:550px;"><?php if(isset($value->ITunesUrl) && $value->ITunesUrl != ''){ echo $value->ITunesUrl; }else echo '-';?></div></td>
							<td valign="top" width="10%"><div class="wrapp_rext" style="width: 120px;"><?php if(isset($value->TiltKey) && $value->TiltKey != ''){ echo $value->TiltKey; }else echo '-';?></div></td>							
							<td align="center"><div style="width: 55px;"><a href="AddGame?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit"><i class="fa fa-pencil"></i></a></div></td>
						</tr>
						<?php } 
						} else { ?>	
						<tr><td class="error" align="center" colspan="8">No Game(s) Found</td></tr>
						<?php } ?>						
				</table>
			</div>
			</form>
			
		</div>
			<div class="col-xs-12"><br>
			<?php if(isset($gameListResult) && is_array($gameListResult) && count($gameListResult) > 0 ) { pagingControlLatest($tot_rec,'GameList'.$statistics);  } ?>
		</div>
		
		<div class="clear" align="center"><br><a href="AddGame" class="btn btn-green" title="Add Game">Add Game</a></div>
		
		
	</section>
		
<?php footerLinks();  commonFooter(); ?>
<script>
 $('.gamelogo').fancybox({
		helpers: { 
        title: null
    },
	maxWidth: "80%",
	maxHeight: "70%"
});
</script>
</html>
