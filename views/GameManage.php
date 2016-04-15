<?php 
require_once('includes/CommonIncludes.php');
require_once("includes/phmagick.php");
require_once('controllers/GameController.php');
$gameManageObj   =   new GameController();
developer_login_check();
$devId		=	$_SESSION['tilt_developer_id'];
$class		=	'';
$fields		=	"*";
$condition	=	" AND fkDevelopersId = ".$devId." AND DevelopedBy = 1";
$gameDetails	=	$gameManageObj->getGameList($fields,$condition);

if(isset($_POST['submit'])	&&	$_POST['submit']!="")	{
	$_POST          =   unEscapeSpecialCharacters($_POST);
	$_POST         	=   escapeSpecialCharacters($_POST);
	if(isset($_POST['game_id'])	&&	$_POST['game_id']!="" && isset($_POST['rental'])	&&	$_POST['rental']!="" ){
		$updateString	=	" RentalFee	=	".$_POST['rental']." ";
		$condition		=	" id = ".$_POST['game_id']." ";
		$gameManageObj->updateGameDetails($updateString,$condition);
		$_SESSION['notification_msg_code']	=	2;
		header("Location:GameManage"); die();
	}
}
commonHead();
?>
<body class="skin-black">
<?php top_header(); ?>
	<section class="content-header">
		<h2 class="no-padding" align="center">Game Rental</h2>
	</section>
   	<section class="content">
		<div class="">
			<form name="game_manage_form" id="game_manage_form" action="" method="post" data-webforms2-force-js-validation="true">
				<div class="box-body col-md-11 col-lg-8  box-center">
					<div class="clear" style="height:54px;"><?php displayNotification('Game '); ?></div>
					<div class="form-group">
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
										<li class="inactive" >
											<div>
											<label for="hided_game_id_<?php echo $g_val->id;?>" title="<?php echo $g_val->Name;?>">
											
												<img src="<?php echo $game_image_path;?>" width="84" height="84" alt=""  onclick="selectGame(this,'<?php echo $g_val->id;?>');"><br><?php echo displayText($g_val->Name,9);?>
											 <input  type="Radio" name="game_id" required="required" id="game_id_<?php echo $g_val->id;?>"  value="<?php echo $g_val->id;?>">
											</label> 
											<span style="display:inline"><input  type="hidden"  name="game_rental_<?php echo $g_val->id;?>"  id="game_rental_<?php echo $g_val->id;?>"  value="<?php echo $g_val->RentalFee;?>"></span>
											</div>
											<span class="rental-free">
												<?php if($g_val->RentalFee > 0) { ?>
													$<?php echo number_format($g_val->RentalFee,2); ?>&nbsp;USD
												<?php } else { ?>
													FREE
												<?php } ?>
											</span>
										</li>
									<?php } } ?>
									<li class="no-padding"><a href="AddGamePopup"  class="addNewGame" title="Add New Game"></a></li>
								</ul>
						</div>
					</div>
					<div class="form-group col-md-8 box-center">
						<label class="col-sm-7 no-padding">Set Game Rental Cost Per Tournament</label>
						<div class="col-sm-5 no-padding text-right">
							<input type="number"  min="0" placeholder="0.00" max="10000" maxlength="5" onkeypress="return isNumberKey(event)" id="rental" value="" name="rental" class="form-control  inline" style="width:100px" required>&nbsp;&nbsp; USD
							<a href="#" class="question_icon"></a>
							<p align="center" id="rental_msg"></p>
						</div>
					</div>
				</div>
				<div class="form-group col-xs-12 text-center">
					<p class="help-block">Game Rental charge won't be applied until after Beta</p>
				</div>
				<div class="box-footer clear" align="center"><br>
					<input type="submit" class="btn btn-green" name="submit" id="submit" value="Save" title="Save" alt="Save">
				</div>
			</form>
		</div>
	</section>
<?php footerLinks(); commonFooter(); ?>
<script>

$(document).ready(function() {
	$(".addNewGame").fancybox({
				'width': 650,
				'height':350,
				'maxWidth': '100%',
				'maxHeight':'598',
				'scrolling': 'auto',			
				'type': 'iframe',
				'fitToView': true,
				'autoSize': true,
				'afterClose': function() { 
					window.location.reload();
				}
		});
});
 $( window ).load( selectGameWidth('game_list') );
 
</script>
</html>