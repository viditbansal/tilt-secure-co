<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();

require_once('controllers/GameController.php');
$gameObj   =   new GameController();

$display   =   'none';
$class  =  $msg    = $cover_path = $statistics = '';
$updateStatus	=	1;

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_search_game_name']);
	unset($_SESSION['mgc_sess_search_company_name']);
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
if(isset($_GET['devId']) && $_GET['devId'] !=''){
$fields    		= "g.id,g.*,count(t.id) as tcount";
$condition 		= " AND  g.Status =1 AND g.fkDevelopersId =".$_GET['devId'];
$gameDeveloper  = $gameObj->getGameDetails($fields,$condition);
$statistics		= "?devId=".$_GET['devId'];
}
$tot_rec 		 	= $gameObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($gameDeveloper)) {
	$_SESSION['curpage'] = 1;
	$gameDeveloper  = $gameObj->getGameDetails($fields,$condition);
}
?>
<body>
	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i>Game Details</h2>
	</div>
	<div class="clear">
			<table border="0" cellpadding="0" cellspacing="0" align="center" width="98%" >
			<tr><td>
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr><td height="10"></td></tr>
					<tr>
						<?php if(isset($gameDeveloper) && is_array($gameDeveloper) && count($gameDeveloper) > 0){ ?>
						<td align="left" width="22%">No. Of Game(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
								<?php if(isset($gameDeveloper) && is_array($gameDeveloper) && count($gameDeveloper) > 0 ) {
								 	pagingControlLatest($tot_rec,'GameDetails'.$statistics); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td></tr>
			<tr><td height="10"></td></tr>
			<tr><td colspan= '2' align="center">
				<?php displayNotification('Developer'); ?>
			</td></tr>
			<tr><td height="5"></td></tr>
			<tr><td>
			<div class="tbl_scroll">
				  <form action="GameDetails" class="l_form" name="GameDetails" id="GameDetails"  method="post"> 
				  <input type="Hidden" name="devid" id="devid" value="<?php if(isset($_GET['devId']) && $_GET['devId'] != '' ) echo $_GET['devId'];?>">
					<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" class="user_table user_actions">
						<tr align="left">
							<th align="center" width="2%" style="text-align:center">#</th>												
							<th width="38%">Game Name</th>
							<th width="22%">Game Image</th>
							<th width="38%">No. of Tournaments</th>
						</tr>
						<?php if(isset($gameDeveloper) && is_array($gameDeveloper) && count($gameDeveloper) > 0 ) { ?>
						<?php foreach($gameDeveloper as $key=>$value){
									$image_path = ADMIN_IMAGE_PATH.'add_game.jpg';
									$original_path = ADMIN_IMAGE_PATH.'add_game.jpg';
									$photo = $value->Photo;
									if(isset($photo) && $photo != ''){
										$user_image = $photo;		
										$image_path_rel 	= GAMES_THUMB_IMAGE_PATH_REL.$user_image;
										$original_path_rel 	= GAMES_IMAGE_PATH_REL.$user_image;
										if(SERVER){
											if(image_exists(1,$user_image)){
												$image_path 	= GAMES_THUMB_IMAGE_PATH.$user_image;
												$original_path 	= GAMES_IMAGE_PATH.$user_image;
											}
										}
										else if(file_exists($image_path_rel)){
												$image_path 	= GAMES_THUMB_IMAGE_PATH.$user_image;
												$original_path 	= GAMES_IMAGE_PATH.$user_image;
										}
									}
						 ?>									
					<tr>
							<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td valign="top"><?php if(isset($value->Name) && $value->Name != ''){ echo $value->Name; }else echo '-';?></td>
							<td valign="top">
							<img class="user_img"  width="36" height="36" src="<?php echo $image_path;?>">
							</td>
							<td valign="top"><?php if(isset($value->tcount) && $value->tcount != 0){ echo $value->tcount; }else echo '-';?></td>
						</tr>
						<?php } ?> 																		
					</table>
					 <tr><td height="20"></td></tr>
					<tr><td height="20"></td></tr>
					</form>
					<?php } else { ?>	
						<tr>
							<td colspan="4" align="center" style="color:red;">No Result(s) Found</td>
						</tr>
					<?php } ?>
				</div>
				
			</td></tr>
           </table>
       </div>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".fancybox").colorbox({title:true});	
	$(function(){

   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '580';
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
