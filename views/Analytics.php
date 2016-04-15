<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/GameController.php');
$gameManageObj   =   new GameController();
developer_login_check();
$devId		=	$_SESSION['tilt_developer_id'];
$firstId	=	$class		=	$photoUpdateString	=	'';

$fields			=	"*";
$condition		=	" AND fkDevelopersId = ".$devId." AND DevelopedBy = 1";
$gameDetails	=	$gameManageObj->getGameList($fields,$condition);
$tournamentId	=	'';
commonHead();
?>
<body  class="skin-black">
	<?php top_header(); ?>
	
	<section class="content-header">
		<?php if(isset($gameDetails) && is_array($gameDetails) && count($gameDetails) > 0){ ?><h2 align="center">Select Game</h2><?php } ?>
	</section>
   	<section class="content col-sm-11 col-lg-10 box-center">
		<div class="col-xs-12" style="position:relative">
			<div class="form-actions no-padding jcarousel" align="center">
				<ul id="game_list">
				<?php if(isset($gameDetails) && is_array($gameDetails) && count($gameDetails) > 0){
					foreach ($gameDetails as $g_key=>$g_val){
						if($g_key == 0){
							$firstId	=	'game_id_'.$g_val->id;
							$gameId	=	$g_val->id;
						}
						$game_logo	=	$g_val->Photo;
						$game_image_path = GAMES_IMAGE_PATH.'no_game.jpeg';
						if($game_logo != '' ){
							if (!SERVER){
								if(file_exists(UPLOAD_GAMES_PATH_REL.$game_logo)){
									$game_image_path = GAMES_IMAGE_PATH.$game_logo;
								}
							}
						else {
								if(image_exists(10,$game_logo))
								$game_image_path = GAMES_IMAGE_PATH.$game_logo;
							}
						}
				?>
					<li>
						<label for="game_id_<?php echo $g_val->id;?>" title="<?php echo $g_val->Name;?>">
						<img src="<?php echo $game_image_path;?>" width="60" height="60" alt="" class="inactive" onclick="gameDetails(this,$('#game_id_<?php echo $g_val->id;?>').val());" ><span><?php echo displayText($g_val->Name,9);?></span>
						</label>
						<input type="Radio" name="game_id" required="required" id="game_id_<?php echo $g_val->id;?>" <?php if(isset($gameId) && $gameId == $g_val->id) echo 'checked';?> value="<?php echo $g_val->id;?>">
					</li>
				<?php } } ?>
				</ul>
			</div>
			<a href="#" class="jcarousel-control-prev">&lsaquo;</a>
			<a href="#" class="jcarousel-control-next">&rsaquo;</a>
		</div>
		<div class="col-xs-12">
			<div align="center"><br><br><img src="webresources/images/map.png" class="resize" width="785" height="433" alt=""></div>
			
			<br><br>
			<p id="game_description">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum
			</p>
			
			<div align="center"><br><br><img src="webresources/images/people.png" class="resize" width="361" height="293" alt=""></div>
		
			<p><br><br></p>
		</div>
	</section>
						  	
<?php   footerLinks(); commonFooter(); ?>
<script>
<?php if(!empty($firstId)){ ?>
gameDetails("#<?php echo $firstId; ?>",<?php echo $gameId; ?>);
<?php } ?>
</script>
</html>
