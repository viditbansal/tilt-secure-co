<?php
function commonHead() { 
header('Content-Type:text/html; charset=UTF-8');
ini_set('default_charset', 'UTF-8');
?><!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="https://www.facebook.com/2008/fbml" lang="en" <?php if(isset($_GET['popup'])) {?> style="height:auto;min-height:0;" <?}?>>
	<head>
  	<meta charset="utf-8">
 	<meta http-equiv="cleartype" content="on">
    <title>TiLT</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"> 
	
    <meta name="apple-mobile-web-app-capable" content="yes">	
	<meta property="og:description" content="MGC -Tilt - Share Tournaments" />  
	<meta property="og:title" content="Tournaments" /> 
	<meta property="og:url" id="meta_url" content="<?php echo BASE_URL;?>" /> 
	
	<link rel="icon" href="<?php echo GAME_IMAGE_PATH; ?>favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo GAME_IMAGE_PATH; ?>favicon.ico" type="image/x-icon" />
	<link href="<?php echo GAME_STYLE_PATH;?>minify.css" rel="stylesheet" type="text/css">
	<link href="<?php echo GAME_STYLE_PATH;?>jquery-ui.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo GAME_STYLE_PATH;?>Font-Awesome/css/font-awesome.css" rel="stylesheet" type="text/css">
	
	
</head>
<?php } 
function top_header() {
	$page = getCurrPage(); 
	
	$game_menu  = ''; $develop_menu  = ''; $tourn_menu  = ''; $analytics_menu  = ''; $support_menu  = $report_menu = $purchase_menu = '';
	
	if(isset($page) && $page=='GameManage' ||  $page=='AddGame' || $page=='GameList' || $page=='GameUsageReport' ) {
	 $game_menu = 'class="active"';
	 if($page=='GameManage')	$game_dev_list = 'sel';
	 else if($page=='AddGame')	$game_add = 'sel';
	 else if($page=='GameList')	$game_list = 'sel';
	 else if($page=='GameUsageReport')	$game_usage_list = 'sel';
	} 
	if(isset($page) && $page=='Developing' )  {
	 $develop_menu = 'class="active"';
	}
	if(isset($page) && $page=='PurchaseStats' )  {
	 $purchase_menu = 'class="active"';
	}
	if(isset($page) && $page=='TournamentList' || $page=='TournamentManage' ) {
	 $tourn_menu = 'class="active"';
	 if($page=='TournamentList')	$tournament_list = 'sel';
	 else if($page=='TournamentManage')	$tournament_manage = 'sel';
	}
	if(isset($page) && $page=='Analytics' ) {
	 $analytics_menu = 'class="active"';
	}
	if(isset($page) && $page=='Support' )  {
	 $support_menu = 'class="active"';
	}
	if(isset($page) && $page=='UserReport' || $page=='GameReport' || $page=='TournamentReport' ) {
	 $report_menu = 'class="active"';
	 if($page=='UserReport')	$user_report = 'sel';
	 else if($page=='GameReport')	$game_report = 'sel';
	 else if($page=='TournamentReport')	$tour_report = 'sel';
	}
	require_once('controllers/DeveloperController.php');
	
	if(isset($_SESSION['tilt_developer_id']) && $_SESSION['tilt_developer_id'] != ''){
		$gameDevObj   =   new DeveloperController();
		$devId	=	$_SESSION['tilt_developer_id'];
		$Condition	=	' id = '.$devId.' ';
		$gameDevDetails    	=	$gameDevObj->checkGameDeveloperLogin($Condition);
		if(is_array($gameDevDetails) && count($gameDevDetails) > 0){
			$_SESSION['tilt_developer_name'] 	=	$gameDevDetails[0]->Name;
			$_SESSION['tilt_developer_company']	=	$gameDevDetails[0]->Company;
			$_SESSION['tilt_developer_email'] 	=	$gameDevDetails[0]->Email; //mgc_admin_user_email
			$_SESSION['tilt_developer_amount'] 	=	$gameDevDetails[0]->Amount; 
			$_SESSION['tilt_developer_coins']	=	$gameDevDetails[0]->VirtualCoins;
			$devImage =	'developer_logo.png';
			if(!empty($gameDevDetails[0]->Photo)){
				$devPhoto = $gameDevDetails[0]->Photo;
				if (SERVER){
					if(image_exists(15,$devPhoto))
						$devImage = $devPhoto;
				} else {
					if(file_exists(DEVELOPER_IMAGE_PATH_REL.$devPhoto))
						$devImage	=	$devPhoto;
				}
			}
			$_SESSION['tilt_developer_logo'] =	$devImage;
		}
	}
	?>
	<div class="page-wrap">
		<header class="header">
			<div <?php if(isset($_SESSION['tilt_developer_id']) && $_SESSION['tilt_developer_id'] !='') { ?>  class="" <? } else { ?>class="content" <? }?> >
            <a href="GameList?cs=1" class="logo">
             	<img src="webresources/images/Tilt_Logo.png" width="108" height="33" alt="">
            </a>
            <nav class="navbar navbar-static-top" role="navigation">
                <div class="navbar-right">
					
					
                    <ul class="nav">
						<?php if(isset($_SESSION['tilt_developer_id']) && $_SESSION['tilt_developer_id'] !='') { ?> <!-- after login -->
                        <li>
                            <a href="<?php echo GAME_SITE_PATH;?>/GameDeveloper" id='brandadd' <?php if(isset($page)	&&	$page=='developer') echo 'class="active_menu"'; ?>>
							<span class="brand_logo">
							<?php if(isset($_SESSION['tilt_developer_logo']) && trim($_SESSION['tilt_developer_logo']) != ''){ ?>
								<img src="<?php if($_SESSION['tilt_developer_logo']=='developer_logo.png') echo GAME_IMAGE_PATH.$_SESSION['tilt_developer_logo']; else echo DEVELOPER_IMAGE_PATH.$_SESSION['tilt_developer_logo']; ?>" width="58" height="58" alt="" />
							<?php } ?>
							</span>
							<span class="name"><?php if(isset($_SESSION['tilt_developer_company'])	&&	$_SESSION['tilt_developer_company'] != '') { echo ucfirst(stripslashes($_SESSION['tilt_developer_company'])); } else { echo "Profile"; } ?></span>
						</a>
                        </li>
						<li><span>Balance:</span></li>
						<li><div style="border : 1px solid #d4d4d4;padding : 0 10px;line-height:28px;margin-top:10px; height: 33px;">
						
							<span id="dev_tilt_coins" style="color:#fff;"><?php if(isset($_SESSION['tilt_developer_amount'])) echo number_format($_SESSION['tilt_developer_amount']); else echo '0'; ?></span>&nbsp;<img align="" src="webresources/images/card.png" width="29" height="21" alt="">&nbsp;&nbsp;/&nbsp;&nbsp;<?php if(isset($_SESSION['tilt_developer_coins'])) echo number_format($_SESSION['tilt_developer_coins']); else echo '0'; ?> Virtual Coins</div>
							
						</li>
						<li><a title="Buy More" alt="Buy More" class="btn btn-gray" href="<?php echo GAME_SITE_PATH;?>/PurchaseCoins">Buy More</a></li>
						
						<li><a class="logout" title="Logout" href="Logout">Logout</a></li>
						<li style="display:none" class="mobile_view">
							<a class="menu-button navbar-btn sidebar-toggle" style="display:none;" href="javascript:void(0)" title="Menu">
			                    <span class="sr-only">Menu</span>
			                    <span class="icon-bar"></span>
			                    <span class="icon-bar"></span>
			                    <span class="icon-bar"></span>
			                </a>
							<div class="mobilemenu">
								<ul>
									<li><span>Balance:</span></li>
									<li><div style="border : 1px solid #d4d4d4;padding : 0 10px;line-height:28px;height: 33px;">  <span id="dev_tilt_coins" style="color:#fff;"><?php if(isset($_SESSION['tilt_developer_amount'])) echo number_format($_SESSION['tilt_developer_amount']); else echo '0'; ?></span>&nbsp;<img src="webresources/images/card.png" width="29" height="21" alt="">&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<?php if(isset($_SESSION['tilt_developer_coins'])) echo number_format($_SESSION['tilt_developer_coins']); else echo '0'; ?> Virtual Coins</li>
									<li><a title="Buy more" alt="Buy more" class="btn btn-gray" href="<?php echo GAME_SITE_PATH;?>/PurchaseCoins">Buy more</a></li>
									<li style="float:right;"><a class="logout" title="Logout" href="Logout">Logout</a></li>
									
								</ul>
							</div>
						</li>
						
						
						<?php } else { ?>
							
							<?php if(isset($page) && $page=='Login' ||  $page=='' || $page=='ForgotPassword') { ?>
							<li><a href="Register" title="Sign Up">Sign Up</a></li>
							<?php } else { ?>
							<li><a href="Login" title="Login">Login</a></li>
							<? } ?>
							
						<? } ?>
                    </ul>
					
                </div>
            </nav>
			</div>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
			<aside class="right-side strech">
			<?php if(isset($_SESSION['tilt_developer_id']) && $_SESSION['tilt_developer_id'] !='') { 
			?> 
				
			<div class="nav-tabs-custom col-md-11 box-center" align="center">
				<ul class="nav nav-tabs">
					<li <?php echo $game_menu;?>>
						<a >Games</a>
						<ul>
							<li><a class="<?php if(isset($game_list) && $game_list != '') echo $game_list; ?>"  href="GameList?cs=1" title="Game List">Game List</a></li>
							<li><a class="<?php if(isset($game_add) && $game_add != '') echo $game_add; ?>"  href="AddGame?cs=1" title="Add Game">Add Game</a></li>
							<li><a class="<?php if(isset($game_dev_list) && $game_dev_list != '') echo $game_dev_list; ?>"  href="GameManage?cs=1" title="Game Rental">Game Rental</a></li>
							<li><a class="<?php if(isset($game_usage_list) && $game_usage_list != '') echo $game_usage_list; ?>"  href="GameUsageReport?cs=1" title="Game Usage Report">Game Usage Report</a></li>
						</ul> 
					</li>
					<li <?php echo $develop_menu;?>><a href="Developing" title="Developing">Developing</a></li>
					<li <?php echo $tourn_menu;?>>
						<a>Tournament</a>
						<ul>
							<li><a class="<?php if(isset($tournament_list) && $tournament_list != '') echo $tournament_list; ?>"  href="TournamentList?cs=1" title="Tournament List">Tournament List</a></li>
							<li><a class="<?php if(isset($tournament_manage) && $tournament_manage != '') echo $tournament_manage; ?>"  href="TournamentManage" title="Add Tournament">Create Tournament</a></li>
						</ul> 
					</li>
					<li <?php echo $support_menu;?>><a href="Support" title="Support">Support</a></li>
				
	<li <?php echo $report_menu;?>>
						<a title="Reports">Reports</a>
						<ul class="report_sub_menu">
							<li><a class="<?php if(isset($user_report) && $user_report != '') echo $user_report; ?>"  href="<?php echo GAME_SITE_PATH;?>/UserReport?cs=1" title="User Report">User Report</a></li>
							<li><a class="<?php if(isset($game_report) && $game_report != '') echo $game_report; ?>"  href="<?php echo GAME_SITE_PATH;?>/GameReport?cs=1" title="Game Report">Game Report</a></li>
							<li><a class="<?php if(isset($tour_report) && $tour_report != '') echo $tour_report; ?>"  href="<?php echo GAME_SITE_PATH;?>/TournamentReport?cs=1" title="Tournament Report">Tournament Report</a></li>
						</ul>
					</li>
					<li <?php echo $purchase_menu;?>><a href="PurchaseStats?cs=1" title="Purchase History">Purchase History</a></li>
				</ul>
			</div>
			<?php   } ?>
			
 <?php } function footerLinks() { ?>
 			</aside>
	        </div>
		</div>
		<div id="site-footer">
			<div class="clear copy"> &copy; <?php echo date('Y'); ?> TiLT, Inc. </div>
		</div>
 
 <?php }
 function commonFooter() { 
 $page = getCurrPage();
 ?>
    </body>
	
	<script src="<?php echo GAME_SCRIPT_PATH; ?>jquery-minify.js" type="text/javascript"></script>
	<script src="<?php echo GAME_SCRIPT_PATH; ?>minify.js" type="text/javascript"></script>
	<script src="<?php echo GAME_SCRIPT_PATH; ?>fancybox/jquery.fancybox.js" type="text/javascript"></script>
	<script src="<?php echo GAME_SCRIPT_PATH; ?>jquery.bootstrap.wizard.js"></script>
	<script src="<?php echo GAME_SCRIPT_PATH; ?>bootstrap.min.js"></script>
	<script type="text/javascript">
	<?php if(isset($_SESSION['tilt_developer_timeZone']) && !empty($_SESSION['tilt_developer_timeZone'])) { ?>
		var USOFFSET = "<?php echo abs($_SESSION['tilt_developer_timeZone']);?>";
	<?php }else { ?>
		var USOFFSET = 5;
	<?php } ?>
	</script>
	<?php $page = getCurrPage();
		if(isset($page ) && ($page == 'TournamentManage')){ ?>
				<script src="<?php echo GAME_SCRIPT_PATH; ?>tinymce/tinymce.min.js" type="text/javascript"></script>
			<script type="text/javascript">
				 window.fbAsyncInit = function() {
					  FB.init({
						appId      : '749748428444823',
						cookie     : true,  
						xfbml      : true,  
						version    : 'v2.1' 
					  });
				 };
				(function(d, s, id){
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) {return;}
					js = d.createElement(s); js.id = id;
					js.src = "//connect.facebook.net/en_US/all.js";
					fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			</script>

			<script>
				if(!window.FB)
				{
					document.write('<script src="<?php echo GAME_SCRIPT_PATH; ?>all.js"><\/script>');
				}
			</script>
	<?php } ?>
	<script src="<?php echo GAME_SCRIPT_PATH; ?>jquery.datetimepicker.js" type="text/javascript"></script>
	<script>
		var isTouch 	= 	(/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase())),
		evt_type 	= 	isTouch ? "touchstart" : "click",
		resize_evt	=	isTouch ? "orientationchange" : "resize",
		isMove		=	false;
		$(document).ready(function(){
			$('.mobilemenu').hide();
			$	=	jQuery.noConflict();
			function closeMenuOnDocClick(e) {
				var tar			=	$(e.target),
				isIcon		=	tar.hasClass("menu-button"),
				isContainer	=	tar.find("div.mobilemenu").hasClass("mobilemenu");
				if( !isIcon && !isContainer && !isMove ) {
					$(".menu-button").trigger(evt_type);
				}
			}
			function openCloseMenu(e) {
				var navIcon	=	$(".menu-button"),
				menu	=	$("div.mobilemenu");
				e.stopPropagation();
				e.preventDefault();
				navIcon.off(evt_type,openCloseMenu);
				if( menu.hasClass("active") ) {
					menu.slideUp(function() { navIcon.on(evt_type,openCloseMenu); }).removeClass("active");
					$(document).off(evt_type,closeMenuOnDocClick);
				} else {
					menu.slideDown( function() { navIcon.on(evt_type,openCloseMenu); }).addClass("active");
					$(document).off(evt_type,closeMenuOnDocClick).on(evt_type,closeMenuOnDocClick);
				}
			}
			if( isTouch )
			$(document).on('touchstart',function() { isMove = false; }).on('touchmove',function() { isMove = true; });
			var navIcon	=	$(".menu-button"),
				menu	=	$(".mobilemenu");
			if( navIcon.length > 0 &&  menu.length > 0 ) {
				navIcon.on(evt_type,openCloseMenu);
			}
		});
		(function($){
			$(window).load(function(){
				$.mCustomScrollbar.defaults.theme="light-2";
				
				$(".scroll_content").mCustomScrollbar({
					axis:"x",
					advanced:{autoExpandHorizontalScroll:true}
				});
			});
		})(jQuery);
	</script>
	<script src="<?php echo GAME_SCRIPT_PATH; ?>shared/js/modernizr.com/Modernizr-2.5.3.forms.js" type="text/javascript"></script>
	<script src="<?php echo GAME_SCRIPT_PATH; ?>shared/js/html5Forms.js" data-webforms2-support="validation,range,color,placeholder" data-webforms2-force-js-validation="true" type="text/javascript"></script>
	<?php 
		/* don't delete it */
		//if ($_SERVER['HTTP_HOST'] == '172.21.4.104') {
			global $GLOBAL_REQUESTS_QUERIES;
			if(isset($_GET['echo']) && $_GET['echo']!='') {
				echo "====QUERY===<pre>";  print_r($GLOBAL_REQUESTS_QUERIES);  echo "</pre>";
			}
		//}
	?>
 <?php }
/*
Purpose : To Perform BulkAction in form
Need to check 
1. form name eg.	<form action="GoalList" class="l_form" name="GoalList" id="GoalList"  method="post">
2. select all check box  <input onclick="checkAllRecords('GoalList');" type="Checkbox" name="checkAll"/>
3. <input id="checkedrecords[]" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  " type="checkbox" />
4. bulk_action($AcctionsArrayList)
*/
function bulk_action($Actions) { ?>
	<table border="0" cellpadding="0" cellspacing="0"  class="">
		<tr><td height="10"></td></tr>
		<tr align="">
			<td align="left" style="padding-top: 7px;">
					<select name="bulk_action" id="bulk_action"  title="Select Action" >
						<option value="">Bulk Actions</option>
						<?php foreach($Actions as $key => $action) { ?>
						<option value="<?php echo $key; ?>"><?php echo $action; ?></option>
								<?php }?>
					</select>
			</td>
			<td align="left" style="padding-left:20px;">
				<input type="submit" onclick="return isActionSelected();" class="submit_button" name="do_action" id="Apply" value="Apply" title="Apply" alt="Apply">&nbsp;&nbsp;													
			</td>
		</tr>
		<tr><td height="10"></td></tr>
	</table>
<?php } ?>

<?php
/********************************************************
  * Function Name: Notification Message
  * Purpose: To display notifications like (Insert/update/Delete/Status change)
  * Paramters :
  *			Need to Notification Session code
  * Output : Returns notification mgs block in table format.
  *******************************************************/
function displayNotification($prefix = ''){
global $notification_msg_class;
global $notification_msg;
	if(isset($_SESSION['notification_msg_code'])	&&	$_SESSION['notification_msg_code']!=''){ 
		$msgCode	=	$_SESSION['notification_msg_code'];
		if( isset($notification_msg_class[$msgCode])	&&	isset($notification_msg[$msgCode]) ){ ?>
			<div class="<?php  echo $notification_msg_class[$msgCode];  ?> w50"><span style="display:block;"><?php echo $prefix.' '.$notification_msg[$msgCode];  ?></span></div>
<?php 	}
		unset($_SESSION['notification_msg_code']);
	}
}
?>