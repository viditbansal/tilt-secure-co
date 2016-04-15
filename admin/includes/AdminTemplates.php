<?php

function commonHead() {
ini_set('max_execution_time', 3600);
ini_set('memory_limit', '1024M');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?php echo SITE_TITLE; ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<link rel="icon" href="<?php echo ADMIN_IMAGE_PATH; ?>favicon-tilt.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo ADMIN_IMAGE_PATH; ?>favicon-tilt.ico" type="image/x-icon" />

	<link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>minify.css">
	<?php $page = getCurrPage();
		  if(isset($page ) && ( $page == 'GeneralSettings')){ ?>
	<link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>jquery-ui-timepicker-addon.css">
	 <link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>smooth-jquery-ui.css">
	<?php }?>
	<link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>jquery.mCustomScrollbar.css">




</head>
<?php }
   function top_header() {
		$page = getCurrPage();
		if(isset($_GET['st']) && $_GET['st']!='') {
			$page_st = 'st='.$_GET['st'];
		}
   ?>
   	<div class="adm_wrap">
		<div class="adm_head">
			<a href="#" class="adm_menu"><i class="fa fa-bars"></i></a>
			<a class="logo" title="MGC - TiLT" href="login"> <img width="108" height="33" alt="" src="webresources/images/Tilt_Logo.png"> <!-- MGC - TiLT --> </a>
			<?php
			switch(HOST_NAME.'_'.DATABASE_NAME){
				case 'localhost_tiltdb':
					echo 'AmiH localhost DB';
					break;
				case 'aa1vo4uao1prtm6.cslj5bbd5oqi.us-west-2.rds.amazonaws.com_ebdb':
					echo 'AWS BETA DB';
					break;
				case 'localhost_tilt':
					echo 'Bogdan_db';
					break;
				default:
					echo 'unknown DATABASE_NAME, production?';
					break;
			}
			?>
			<div class="adm_detail">
				<span><i class="fa fa-user"></i> Welcome <strong>Admin,</strong> </span>
			</div>
			<?php side_bar()?>
		</div>
		<div class="adm_content">
			<div class="adm_right">
				<div id="content_3" class="content">

<?php } function commonFooter() { ?>

				</div>
			</div>
		</div>
	</div>
</body>

	<script src="<?php echo ADMIN_SCRIPT_PATH; ?>minify-jquery-all.js" type="text/javascript"></script>
	<script src="<?php echo ADMIN_SCRIPT_PATH; ?>minify-custom-js.js" type="text/javascript"></script>
		<?php $page = getCurrPage();
			if(isset($page ) && ( $page == 'GeneralSettings' ||	$page == 'TournamentRulesManage' ||	$page == 'StaticPages' ||	$page == 'TournamentManage')){ ?>
				<script src="<?php echo ADMIN_SCRIPT_PATH; ?>tinymce/tinymce.min.js" type="text/javascript"></script>
		<?php }	?>
<?php
	/* don't delete it */
	//if ($_SERVER['HTTP_HOST'] == '172.21.4.104') {
		global $GLOBAL_REQUESTS_QUERIES;
		if(isset($_GET['echo']) && $_GET['echo']!='') {
			echo "====QUERY===<pre>";  print_r($GLOBAL_REQUESTS_QUERIES);  echo "</pre>";
		}
	//}
?>
<script>
		(function($){
			$(window).load(function(){
				$.mCustomScrollbar.defaults.theme="light-2"; //set "light-2" as the default theme

				$(".scroll_content").mCustomScrollbar({
					axis:"x",
					advanced:{autoExpandHorizontalScroll:true}
				});
			});
		})(jQuery);
	</script>
<script type="text/javascript">
$(document).bind('cbox_open', function () {
    $('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function () {
    $('html').css({ overflow: 'auto' });
});
</script>
<?php }

	function side_bar(){
		$page = getCurrPage();
		//echo  $page;
	if(isset($page ) && $page == 'GeneralSettings')
		$general_settings		=	'sel';
	if(isset($page ) && $page == 'ChangePassword')
		$change_pwd				=	'sel';
	if(isset($page ) && $page == 'StaticPages')
		$cms_page				=	'sel';
	if(isset($page ) && $page == 'Versions')
		$version_page			=	'sel';
	if(isset($page ) && $page == 'Sdk')
		$sdk_page				=	'sel';
	if(isset($page ) && $page == 'DefaultImages')
		$def_img_page			=	'sel';
	if(isset($page ) && ($page == 'UserList'	||	$page == 'Messages'))
		$user_list			=	'sel';
	if(isset($page ) && ( $page == 'UserManage')	||	$page == 'UserDetail'){
			$add_user				=	'sel';
	}
	if(isset($page ) && ( $page == 'TournamentList'	||	$page == 'TournamentManage'	||	$page == 'TournamentDetail'))
		$tournament_list			=	'sel';
	if(isset($page ) && $page == 'Commission')
		$commission_page			=	'sel';
	if(isset($page ) && $page == 'Distance')
		$distance_page			=	'sel';
	if(isset($page ) && $page == 'LeaderBoard')
		$leader_board_list			=	'sel';
	if(isset($page ) && $page == 'LogTracking' )
		$log_list		=	'sel';
	if(isset($page ) && $page == 'TournamentStatistics' )
		$tournament_stat		=	'sel';
	if(isset($page ) && $page == 'GiftCardList' )
		$card_list		=	'sel';
	if(isset($page ) && $page == 'InAppPackages' )
		$inapp_products		=	'sel';
	if(isset($page ) && $page == 'InAppPackageList' )
		$inapp_product_list		=	'sel';

	if(isset($page ) && $page == 'VirtualCoinList')
		$vcoin_list		=	'sel';
	if(isset($page ) && $page == 'VirtualCoinManage')
		$vcoin_manage	=	'sel';
	if(isset($page ) && $page == 'LocationRestriction')
		$loc_restriction	=	'sel';
	if(isset($page ) && $page == 'TiltLocationRestriction')
		$tilt_loc_restriction	=	'sel';
	if(isset($page ) && $page == 'AddTiltCoin')
		$add_tiltCoin	=	'sel';
	if(isset($page ) && $page == 'TiltCoinsList')
		$tiltCoin_list	=	'sel';
	if(isset($page ) && $page == 'GameList')
		$game_list			=	'sel';
	if(isset($page ) && $page == 'GameManage')
		$game_add			=	'sel';
	if(isset($page ) && $page == 'GameDeveloperList')
		$game_dev_list		=	'sel';
	if(isset($page ) && $page == 'Statistics' )
		$statistics		=	'sel';
	if(isset($page ) && $page == 'RedeemStatistics')
		$redeem_stat	=	'sel';
	if(isset($page ) && $page == 'PurchaseCoinStatistics')
		$purchase_stat	=	'sel';
	if(isset($page ) && $page == 'CommissionStatistics')
		$commisson_stat	=	'sel';
	if(isset($page ) && $page == 'CronTracking')
		$cron_list	=	'sel';
	if(isset($page ) && $page == 'TurnsList')
		$turns_list	=	'sel';

	if(isset($page ) && $page == 'VirtualCoinsReport')
		$virtual_report	=	'sel';
	if(isset($page ) && $page == 'VirtualCoinsGameReport')
		$vir_game_report	=	'sel';
	/*if(isset($page ) && $page == 'TiltDollarReport')
		$tiltdollar_report	 =	'sel';
	if(isset($page ) && $page == 'TiltDollarGameReport')
		$tiltdollar_game_report	=	'sel';*/
	if(isset($page ) && $page == 'IAPTracking' )
		$iap_list		=	'sel';
	if(isset($page ) && $page == 'MediaImpressionTracking')
		$media_list	=	'sel';
	if(isset($page ) && $page == 'GlobalReport')
		$global_report	=	'sel';
	if(isset($page ) && ($page == 'ServiceList' || $page=='ServiceDetail' ))
	    $service_list		=	'sel';
	if(isset($page ) && $page == 'ServiceManage' )	{
		 if(isset($_GET['editId']) && $_GET['editId'] != '')
		 	$service_add		=	'sel';
		 else
			$service_add		=	'sel';
	}
	if($page == 'TournamentRulesList'){
		$tour_rules_list	=	'sel';
	}

	if($page=='TournamentRulesManage'){
		$tour_rules_manage	=	'sel';
	}
	if($page=='WebsiteHome'){
		$website_home	=	'sel';
	}
	if(isset($page ) && ($page == 'AboutUs'))
		$website_aboutus	=	'sel';
	if(isset($page ) && ($page == 'Developer'))
		$developer	=	'sel';
	if(isset($page ) && ($page == 'Media'))
		$media	=	'sel';
	if(isset($page ) && ($page == 'TermofUse'))
		$term_of_use	=	'sel';
	if(isset($page ) && ($page == 'PrivacyPolicy'))
		$privacy_policy	=	'sel';

	if(isset($page ) && $page == 'WinnerStatistics')
		$winner_stat	=	'sel';
		?>
			<ul class="nav-hori">
				<li>
					 <a  href="javascript:void(0);" <?php if(($page == 'GeneralSettings') || ($page == 'ChangePassword') || ($page == 'StaticPages') || ($page == 'Versions') || ($page == 'Sdk') || ($page == 'DefaultImages')) { ?> class="sel" <?php } else { ?> class=""<?php } ?> ><i class="fa fa-gears"></i> Settings</a>
					<ul>
						<li><span class="menu_arrow"></span><a class="<?php if(isset($general_settings) && $general_settings != '') echo $general_settings; ?>" href="<?php echo ADMIN_SITE_PATH;?>/GeneralSettings" title="General Settings">General Settings</a></li>
						<li><a class="<?php if(isset($change_pwd) && $change_pwd != '') echo $change_pwd; ?>" href="<?php echo ADMIN_SITE_PATH;?>/ChangePassword" title="Change Password">Change Password</a></li>
						<li><a class="<?php if(isset($cms_page) && $cms_page != '') echo $cms_page; ?>" href="<?php echo ADMIN_SITE_PATH;?>/StaticPages" title="CMS">CMS</a></li>
						<li><a class="<?php if(isset($version_page) && $version_page != '') echo $version_page; ?>" href="<?php echo ADMIN_SITE_PATH;?>/Versions" title="Versions">Versions</a></li>
						<li><a class="<?php if(isset($sdk_page) && $sdk_page != '') echo $sdk_page; ?>" href="<?php echo ADMIN_SITE_PATH;?>/Sdk" title="SDK">SDK</a></li>
						<li><a class="<?php if(isset($def_img_page) && $def_img_page != '') echo $def_img_page; ?>" href="<?php echo ADMIN_SITE_PATH;?>/DefaultImages" title="Users Default Image">Users Default Image</a></li>
					</ul>
				</li>
				<li>
					 <a href="javascript:void(0);" <?php if(($page == 'UserList')	||	($page=='UserManage') ||	($page == 'UserDetail') ||	$page == 'Messages' ) { ?> class="sel" <?php } else { ?> class=""<?php } ?>><i class="fa fa-user"></i> User</a>
					<ul>
						<li><span class="menu_arrow"></span><a class="<?php if(isset($user_list) && $user_list != '') echo $user_list; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/UsersList?cs=1" title="User List">User List</a></li>
						<li><a class="<?php if(isset($add_user) && $add_user != '') echo $add_user; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/UserManage?cs=1" title="Add User">Add User</a></li>
					</ul>
				</li>
				<li>
					 <a href="javascript:void(0);" <?php if(($page == 'TournamentList')	||	($page=='TournamentManage') ||	($page == 'TournamentDetail')  ) { ?>class="sel" <?php } else { ?> class=""<?php } ?>><i class="fa fa-trophy"></i> Tournament</a>
					<ul>
						<li><span class="menu_arrow"></span><a class="<?php if(isset($tournament_list) && $tournament_list != '') echo $tournament_list; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/TournamentList?cs=2" title="Tournament List">Tournament List</a></li>
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);"<?php if(($page == 'GameList')	||		($page == 'GameDetail') ||		($page == 'GameManage') || ($page == 'GameDeveloperList')  ) { ?>  class="sel" <?php } else { ?> class=""<?php } ?>><i class="fa fa-gamepad"></i> Game</a>
					<ul>
						<li><span class="menu_arrow"></span><a class="<?php if(isset($game_list) && $game_list != '') echo $game_list; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/GameList?cs=1" title="Game List">Game List</a></li>
						<li><a class="<?php if(isset($game_add) && $game_add != '') echo $game_add; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/GameManage?cs=1" title="Add Game">Add Game</a></li>
						<li><a class="<?php if(isset($game_dev_list) && $game_dev_list != '') echo $game_dev_list; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/GameDeveloperList?cs=1" title="Developer & Brand List">Developer & Brand List</a></li>
					</ul>
				</li>

				<li>
					 <a href="javascript:void(0);" <?php if(($page == 'GiftCardList') || ($page == 'VirtualCoinList')||($page=='VirtualCoinManage')	||	($page=='TiltCoinsList') ||	($page=='AddTiltCoin')  ||	($page=='LocationRestriction') ||	($page=='TiltLocationRestriction') || ($page == 'TournamentRulesList')||($page=='TournamentRulesManage') || ($page == 'InAppPackages') || ($page == 'InAppPackageList')  ) { ?>  class="sel" <?php } else { ?> class=""<?php } ?>><i class="fa fa-reorder"></i> Management</a>
					<ul>
						<li><span class="menu_arrow"></span><a  class="<?php if(isset($card_list) && $card_list != '') echo $card_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/GiftCardList?cs=1 " title="Gift Card List">Gift Card List</a></li>
						<li><a  class="<?php if(isset($inapp_product_list) && $inapp_product_list != '') echo $inapp_product_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/InAppPackageList?cs=1 " title="InApp Product List">InApp Product List</a></li>
						<li>
							<a href="javascript:void(0);" <?php if(($page == 'VirtualCoinList')||($page=='VirtualCoinManage')	||	($page=='TiltCoinsList') ||	($page=='AddTiltCoin')  ||	($page=='LocationRestriction') ||	($page=='TiltLocationRestriction'))  { ?> class="sel" <?php } else { ?> class=""<?php } ?>>Coin Management <i class="fa fa-angle-right"></i></a>
							<ul>
								<li><span class="menu_arrow-left"></span><a class="<?php if(isset($vcoin_list) && $vcoin_list != '') echo $vcoin_list; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/VirtualCoinList?cs=1" title="Virtual Coins List">Virtual Coins List</a></li>
								<li><a class="<?php if(isset($tiltCoin_list) && $tiltCoin_list != '') echo $tiltCoin_list; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/TiltCoinsList?cs=1" title="TiLT$ List">TiLT$ List</a></li>
								<li><a class="<?php if(isset($loc_restriction) && $loc_restriction != '') echo $loc_restriction; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/LocationRestriction?cs=1" title="Location restriction for buying GFT$">Location restriction for buying GFT$</a></li>
								<li><a class="<?php if(isset($tilt_loc_restriction) && $tilt_loc_restriction != '') echo $tilt_loc_restriction; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/TiltLocationRestriction?cs=1" title="Location restriction to buy/play with TiLT$">Location restriction to buy/play with TiLT$<!-- Tilt Location Restriction --></a></li>
							</ul>
						</li>
						<li> <a href="javascript:void(0);" <?php if(($page == 'TournamentRulesList')||($page=='TournamentRulesManage') )  { ?> class="sel" <?php } else { ?> class=""<?php } ?>>Rules Management <i class="fa fa-angle-right"></i></a>
							<ul>
								<li><span class="menu_arrow-left"></span><a class="<?php if(isset($tour_rules_list) && $tour_rules_list != '') echo $tour_rules_list; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/TournamentRulesList?cs=1" title="Tournament Rules List">Tournament Rules List</a></li>
								<li><a class="<?php if(isset($tour_rules_manage) && $tour_rules_manage != '') echo $tour_rules_manage; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/TournamentRulesManage?cs=1" title="Add Tournament Rules">Add Tournament Rules</a></li>
							</ul>
						</li>
					</ul>
				</li>

				<li>
					 <a href="javascript:void(0);"<?php if(($page == 'ServiceList') || ($page == 'ServiceManage') || ($page == 'ServiceDetail') ) { ?> class="sel" <?php } else { ?> class=""<?php } ?>><i class="fa fa-wrench"></i> Service</a>
					<ul>
						<li><span class="menu_arrow"></span><a class="<?php if(isset($service_list) && $service_list != '') echo $service_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/ServiceList?cs=1" title="Service List">Service List</a></li>
						<li><a class="<?php if(isset($service_add) && $service_add != '') echo $service_add; ?>" href="<?php echo ADMIN_SITE_PATH;?>/ServiceManage?cs=1" title="Add Service">Add Service</a></li>
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" <?php if(($page == 'LogTracking') 	||	$page == 'CronTracking'  || $page == 'TurnsList'  || $page == 'MediaImpressionTracking' || $page == 'IAPTracking' ) { ?>class="sel" <?php } else { ?> class=""<?php } ?>><i class="fa fa-signal"></i> Tracking</a>
					<ul>
						<li><span class="menu_arrow"></span><a  class="<?php if(isset($log_list) && $log_list != '') echo $log_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/LogTracking?cs=1 " title="Log Tracking">Log Tracking</a></li>
						<li><a  class="<?php if(isset($cron_list) && $cron_list != '') echo $cron_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/CronTracking?cs=1 " title="Cron Tracking">Cron Tracking</a></li>

						<li><a  class="<?php if(isset($iap_list) && $iap_list != '') echo $iap_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/IAPTracking?cs=1 " title="IAP Tracking">IAP Tracking</a></li>
						<li><a  class="<?php if(isset($turns_list) && $turns_list != '') echo $turns_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/TurnsList?cs=1 " title="Turns / Rounds List">Turns / Rounds List</a></li>
						<li><a  class="<?php if(isset($media_list) && $media_list != '') echo $media_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/MediaImpressionTracking?cs=1 " title="Media Impression Tracking">Media Impression Tracking</a></li>

					</ul>
				</li>
				<!-- statistics -->
				<li>
					 <a  href="javascript:void(0);" <?php if(($page == 'GlobalReport') ||	($page == 'TournamentStatistics')	||	($page == 'Statistics')	||	($page == 'RedeemStatistics')  ||	($page == 'PurchaseCoinStatistics')	||	$page == 'CommissionStatistics' ||	$page == 'WinnerStatistics' || $page == 'VirtualCoinsReport' || $page == 'VirtualCoinsGameReport' || $page == 'TiltDollarReport' || $page == 'TiltDollarGameReport' ) { ?> class="sel" <?php } else { ?> class=""<?php } ?>><i class="fa fa-bar-chart-o"></i> Report/Statistics</a>
					<ul>
						<li><span class="menu_arrow"></span><a  class="<?php if(isset($global_report) && $global_report != '') echo $global_report; ?>" href="<?php echo ADMIN_SITE_PATH;?>/GlobalReport?cs=1 " title="Global Report">Global Report</a></li>
						<li><a  class="<?php if(isset($tournament_stat) && $tournament_stat != '') echo $tournament_stat; ?>" href="<?php echo ADMIN_SITE_PATH;?>/TournamentStatistics?cs=1 " title="Tournament Statistics">Tournament Statistics</a></li>
						<li><a  class="<?php if(isset($winner_stat) && $winner_stat != '') echo $winner_stat; ?>" href="<?php echo ADMIN_SITE_PATH;?>/WinnerStatistics?cs=1 " title="Winner Statistics">Winner Statistics</a></li>
						<li><a  class="<?php if(isset($redeem_stat) && $redeem_stat != '') echo $redeem_stat; ?>" href="<?php echo ADMIN_SITE_PATH;?>/RedeemStatistics?cs=1 " title="Redeem Statistics">Redeem Statistics</a></li>
						<li><a  class="<?php if(isset($purchase_stat) && $purchase_stat != '') echo $purchase_stat; ?>" href="<?php echo ADMIN_SITE_PATH;?>/PurchaseCoinStatistics?cs=1 " title="Developer & Brand Purchase Statistics">Developer & Brand Purchase Statistics</a></li>
						<li><a  class="<?php if(isset($commisson_stat) && $commisson_stat != '') echo $commisson_stat; ?>" href="<?php echo ADMIN_SITE_PATH;?>/CommissionStatistics?cs=1 " title="Commission Statistics">Commission Statistics</a></li>
						<li><a  class="<?php if(isset($statistics) && $statistics != '') echo $statistics; ?>" href="<?php echo ADMIN_SITE_PATH;?>/Statistics?cs=1 " title="Statistics Report">Statistics Report</a></li>
						<li><a  class="<?php if(isset($virtual_report) && $virtual_report != '') echo $virtual_report; ?>" href="<?php echo ADMIN_SITE_PATH;?>/VirtualCoinsReport?cs=1 " title="Virtual Coins Tournament Report">Virtual Coins Tournament Report</a></li>
						<li><a  class="<?php if(isset($vir_game_report) && $vir_game_report != '') echo $vir_game_report; ?>" href="<?php echo ADMIN_SITE_PATH;?>/VirtualCoinsGameReport?cs=1 " title="Virtual Coins Game Report">Virtual Coins Game Report</a></li>
					</ul>
				</li>
				<li>
					 <a  href="javascript:void(0);" <?php if(($page == 'WebsiteHome') || ($page == 'AboutUs') || ($page == 'Developer') || ($page == 'Media') || ($page == 'TermofUse')  || ($page == 'PrivacyPolicy')) { ?> class="sel" <?php } else { ?> class=""<?php } ?> ><i class="fa fa-globe"></i> Website</a>
					<ul>
						<li><span class="menu_arrow"></span><a class="<?php if(isset($website_home) && $website_home != '') echo $website_home; ?>" href="<?php echo ADMIN_SITE_PATH;?>/WebsiteHome" title="Home">Home</a></li>
						<li><a class="<?php if(isset($website_aboutus) && $website_aboutus != '') echo $website_aboutus ; ?>" href="<?php echo ADMIN_SITE_PATH;?>/AboutUs" title="About Us">About Us</a></li>
						<li><a class="<?php if(isset($developer) && $developer != '') echo $developer ; ?>" href="<?php echo ADMIN_SITE_PATH;?>/Developer" title="Developer">Developer</a></li>
						<li><a class="<?php if(isset($media) && $media != '') echo $media ; ?>" href="<?php echo ADMIN_SITE_PATH;?>/Media" title="Media">Media</a></li>
						<li><a class="<?php if(isset($term_of_use) && $term_of_use != '') echo $term_of_use ; ?>" href="<?php echo ADMIN_SITE_PATH;?>/TermofUse" title="Terms Of Use">Terms Of Use</a></li>
						<li><a class="<?php if(isset($privacy_policy) && $privacy_policy != '') echo $privacy_policy ; ?>" href="<?php echo ADMIN_SITE_PATH;?>/PrivacyPolicy" title="Privacy Policy">Privacy Policy</a></li>
					</ul>
				</li>
				<li class="logout-right"><a href="Logout" title="Logout"><i class="fa fa-power-off"></i> Logout</a></li>
			</ul>

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
	<table border="0" cellpadding="0" cellspacing="0" width="98%" align="center"  class="">
		<tr><td height="10"></td></tr>
		<tr align="">
			<td align="left"  width="5%"> <!-- style="padding-top: 7px;" -->
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
<?php }

/********************************************************
  * Function Name: Notification Message
  * Purpose: To display notifications like (Insert/update/Delete/Status change)
  * Paramters :
  * Need to Set Notification Session code
  * Output : Returns notification mgs block in table format.
  *******************************************************/
function displayNotification($prefix = ''){
global $notification_msg_class;
global $notification_msg;
	if(isset($_SESSION['notification_msg_code'])	&&	$_SESSION['notification_msg_code']!=''){
		$msgCode	=	$_SESSION['notification_msg_code'];
		if( isset($notification_msg_class[$msgCode])	&&	isset($notification_msg[$msgCode]) ){ ?>
			<div class="<?php  echo $notification_msg_class[$msgCode];  ?> w50"><span style="display:block;"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $prefix.' '.$notification_msg[$msgCode];  ?></span></div>
<?php 	}
		unset($_SESSION['notification_msg_code']);
	}
}
//TournamentStatistics
	function commonHeadResponsive(){
 	?>
<!DOCTYPE html>
<html>
       <head>
            <title><?php echo SITE_TITLE; ?></title>
			<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
			<link rel="icon" href="<?php echo ADMIN_IMAGE_PATH; ?>favicon-tilt.ico" type="image/x-icon" />
			<link rel="shortcut icon" href="<?php echo ADMIN_IMAGE_PATH; ?>favicon-tilt.ico" type="image/x-icon" />

			<link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>admin_styles.css">
			<link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>font/font-awesome.css"> <!-- for all icons -->
<?php
 }
?>
