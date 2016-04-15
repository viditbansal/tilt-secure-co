<?php 
require_once('includes/CommonIncludes.php');
require_once("includes/phmagick.php");
require_once('controllers/GameController.php');
$gameManageObj   =   new GameController();
developer_login_check();
$devId			=	$_SESSION['tilt_developer_id'];
$class			=	'';
$fields			=	"*";
$condition		=	" AND fkDevelopersId = ".$devId." AND DevelopedBy = 1 ";
$gameDetails	=	$gameManageObj->getGameList($fields,$condition);
commonHead();
?>
<body class="skin-black">
<?php top_header(); ?>
	<section class="content-header">
		<h2 align="center"><?php if(isset($gameDetails) && is_array($gameDetails) && count($gameDetails) > 0){ echo "Game Usage Report";} else echo "No Game Found"; ?></h2>
	</section>
   	<section class="content">
		<div class="">
			<form name="game_usage_form" id="game_usage_form" action="" method="post">
				<div class="box-body col-md-8 no-padding box-center">
					<div class="clear"></div>
					<div class="form-group col-sm-12">
						<div class="scroll_content">
							<ul id="game_list" class="game_list">
								<?php if(isset($gameDetails) && is_array($gameDetails) && count($gameDetails) > 0){
											foreach ($gameDetails as $g_key=>$g_val){
												$game_logo	=	$g_val->Photo;
												$game_image_path = GAME_IMAGE_PATH.'add_game.jpg';
												if($game_logo != '' ){
													if (!SERVER){
														if(file_exists(UPLOAD_GAMES_PATH_REL.$game_logo)){
															$game_image_path = GAMES_IMAGE_PATH.$game_logo;
														}
													}
													else{
														if(image_exists(10,$game_logo))
															$game_image_path = GAMES_IMAGE_PATH.$game_logo;
													}
												}
												?>
										<li>
											<div>
												<a href="GameUsageDetails?cs=1&gameId=<?php echo $g_val->id;?>&gameName=<?php echo $g_val->Name;?>"  name="gamelist" id="gamelist" title="" alt="gamelist"><img src="<?php echo $game_image_path;?>" width="84" height="84" alt="" ><br><?php echo displayText($g_val->Name,9);?></a>&nbsp;
											</div>
										</li>
									<?php } } ?>
									
								</ul>
						</div>
					</div>
			</form>
		</div>
	</section>
<?php footerLinks(); commonFooter(); ?>	
<script>
$( window ).load( selectGameWidth('game_list') );
function resetgames(){
	$("#game_listing").html($("#new").html());
	$("#gameListSlider1").flexisel();
}
</script>

</html>