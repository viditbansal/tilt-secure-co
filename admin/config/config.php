<?php
/**
 * configuration variables
 *
 * This file has constants and global variable used throughout the application.
 *
 */
define("TITLE","MGC");
if (isset($_SERVER['HTTPS']) && ($_SERVER["HTTPS"] == 'on' ) )
	$site = 'https://';
elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
   $site = 'https://';
else
	$site = 'http://';

if($_SERVER['SERVER_ADDR']=='172.21.4.104'){
	define('BASE_URL',$site.$_SERVER['HTTP_HOST']);
	define('ADMIN_SITE_PATH',  $site.$_SERVER['HTTP_HOST'].'/TiLTPortals/admin');
	define('ADMIN_ABS_PATH',  'C:/wamp/www/TiLTPortals/admin');
	define('SITE_PATH',  $site.$_SERVER['HTTP_HOST'].'/TiLTPortals');
	define('ACTIVATION_LINK_PATH',  $site.$_SERVER['HTTP_HOST'].'/TiLT');
	define('VERIFICATION_LINK_PATH',  $site.$_SERVER['HTTP_HOST'].'/TiLTPortals');
	define('GAME_SITE_PATH',$site.$_SERVER['HTTP_HOST'].'/TiLTPortals');
	define('ABS_PATH',  'C:/wamp/www/TiLTPortals');
	define('SERVER',  0);
	define('GOBALUSERID', 1);
	define('GLOBALUSERID', 1);
}else{
	define('BASE_URL',$site.$_SERVER['HTTP_HOST']);
	if(file_exists('/home/ami/dev')){
		define('ADMIN_SITE_PATH',  $site.$_SERVER['HTTP_HOST'].'/Tilt-Web/admin');
		define('ABS_PATH',  '/home/ami/dev/Tilt-Web');
	}else{
		define('ADMIN_SITE_PATH',  $site.$_SERVER['HTTP_HOST'].'/admin');
		define('ABS_PATH',  '/var/www/html');
	}
	define('ADMIN_ABS_PATH',  '/var/www/html/admin');
	define('SITE_PATH',  $site.$_SERVER['HTTP_HOST']);
	define('VERIFICATION_LINK_PATH',  'https://secure.tilt.co');
	define('ACTIVATION_LINK_PATH',  'https://api.tilt.co');
	define('GAME_SITE_PATH',$site.$_SERVER['HTTP_HOST'].'');
	define('SERVER',  1);
	define('GOBALUSERID', 1);
	define('GLOBALUSERID', 1);
}
define('REGION','us-west-2');

//script ans style path
define('SITE_TITLE', 'TiLT');
define('ADMIN_SCRIPT_PATH', ADMIN_SITE_PATH.'/webresources/js/');
define('ADMIN_STYLE_PATH', ADMIN_SITE_PATH.'/webresources/css/');
define('ADMIN_IMAGE_PATH', ADMIN_SITE_PATH.'/webresources/images/');

//Images related constants
define('UPLOAD_USER_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/');
define('UPLOAD_USER_THUMB_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/thumbnail/');

define('UPLOAD_GAME_PATH_REL', ABS_PATH.'/admin/webresources/uploads/games/');
define('UPLOAD_GIFTCARD_PATH_REL',    ABS_PATH.'/admin/webresources/uploads/giftcards/');
define('UPLOAD_GIFTCARD_PATH', ADMIN_SITE_PATH.      '/webresources/uploads/giftcards/');
define('UPLOAD_GAME_THUMB_PATH_REL', ABS_PATH.'/admin/webresources/uploads/games/thumbnail/');
define('UPLOAD_DEFAULT_PATH_REL', ABS_PATH.'/admin/webresources/uploads/defaultuserimages/');
define('UPLOAD_DEFAULT_THUMB_PATH_REL', ABS_PATH.'/admin/webresources/uploads/defaultuserimages/thumbnail/');

define('UPLOAD_GAME_CERTIFICATE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/certificate/');

//Game Related paths
define('UPLOAD_COUPON_PATH_REL', ABS_PATH.'/webresources/uploads/coupons/');
define('UPLOAD_BANNER_PATH_REL', ABS_PATH.'/webresources/uploads/banner/');
define('UPLOAD_YOUTUBE_LINK_PATH_REL', ABS_PATH.'/webresources/uploads/youtubelink/');
define('UPLOAD_CUSTOM_PRIZE_PATH_REL', ABS_PATH.'/webresources/uploads/customprize/');
define('UPLOAD_DEVELOPER_PATH_REL', ABS_PATH.'/webresources/uploads/gamedevelopers/');
define('UPLOAD_DEVELOPER_THUMB_PATH_REL', ABS_PATH.'/webresources/uploads/gamedevelopers/thumbnail/');
//Image for Group chat
define('UPLOAD_GROUP_CHAT_PATH_REL', ABS_PATH.'/admin/webresources/uploads/chats/');
define('UPLOAD_GROUP_CHAT_THUMB_PATH_REL', ABS_PATH.'/admin/webresources/uploads/chats/thumbnail/');

define('UPLOAD_WEBSITE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/website/');

define('TEMP_USER_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/temp/');
define('TEMP_USER_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/temp/');

define('UPLOAD_SDK_PATH_REL', ABS_PATH.'/admin/webresources/uploads/sdk/');

if ($_SERVER['HTTP_HOST'] == '172.21.4.104'){
	define('BUCKET_NAME','mgcdemo');
	define('STRIPE_DEV_SECRET_KEY','sk_test_UH0Z11SACdcIlH3EGoQdLzpF');
	define('STRIPE_DEV_PUBLISH_KEY','pk_test_orRtRe8BjId7Sr8FHLkL3HDZ');
}
else{
	define('BUCKET_NAME','tiltlive');
	define('STRIPE_DEV_SECRET_KEY','sk_live_SsICRROu60pXVF5llUPIdY19');
	define('STRIPE_DEV_PUBLISH_KEY','pk_live_r4RPs23m8aKhUUgt7B0L7HYp');
}
//d1r2msc5ngxhh1.cloudfront.net
if($_SERVER['SERVER_ADDR']=='172.21.4.104') {
	define('SITE_PATH_UPLOAD',  SITE_PATH.'/admin/webresources/uploads/');
	define('ABS_PATH_UPLOAD',  ABS_PATH.'/admin/webresources/uploads/');
	define('GAME_PATH_UPLOAD', SITE_PATH.'/webresources/uploads/');
	define('ABS_GAME_PATH_UPLOAD', ABS_PATH.'/webresources/uploads/');
	define('S3_PATH','https://s3-us-west-2.amazonaws.com/'.BUCKET_NAME.'/');
} else {
	define('SITE_PATH_UPLOAD',  'https://d3q9j1xoc4b817.cloudfront.net/');
	define('ABS_PATH_UPLOAD',  'https://d3q9j1xoc4b817.cloudfront.net/');
	define('GAME_PATH_UPLOAD', 'https://d3q9j1xoc4b817.cloudfront.net/');
	define('ABS_GAME_PATH_UPLOAD', 'https://d3q9j1xoc4b817.cloudfront.net/');
	define('S3_PATH','https://s3.amazonaws.com/'.BUCKET_NAME.'/');
}

define('FLAG_IMAGE_PATH', ADMIN_IMAGE_PATH.'flags/');
define('FLAG_IMAGE_PATH_REL', ADMIN_IMAGE_PATH.'flags/');

/*define('FLAG_IMAGE_PATH', SITE_PATH_UPLOAD.'flags/');
define('FLAG_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'flags/');*/

define('USER_IMAGE_PATH', SITE_PATH_UPLOAD.'users/');
define('USER_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'users/');
define('USER_THUMB_IMAGE_PATH', SITE_PATH_UPLOAD.'users/thumbnail/');
define('USER_THUMB_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'users/thumbnail/');

define('GAMES_IMAGE_PATH', SITE_PATH_UPLOAD.'games/');
define('GAMES_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'games/');
define('GAMES_THUMB_IMAGE_PATH', SITE_PATH_UPLOAD.'games/thumbnail/');
define('GAMES_THUMB_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'games/thumbnail/');

define('DEFAULT_USER_IMAGE_PATH', SITE_PATH_UPLOAD.'defaultuserimages/');
define('DEFAULT_USER_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'defaultuserimages/');
define('DEFAULT_USER_THUMB_IMAGE_PATH', SITE_PATH_UPLOAD.'defaultuserimages/thumbnail/');
define('DEFAULT_USER_THUMB_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'defaultuserimages/thumbnail/');

define('COUPON_IMAGE_PATH', GAME_PATH_UPLOAD.'coupons/');
define('COUPON_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'coupons/');

define('BANNER_IMAGE_PATH', GAME_PATH_UPLOAD.'banner/');
define('BANNER_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'banner/');

define('YOUTUBE_LINK_IMAGE_PATH', GAME_PATH_UPLOAD.'youtubelink/');
define('YOUTUBE_LINK_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'youtubelink/');

define('GROUP_CHAT_IMAGE_PATH', SITE_PATH_UPLOAD.'chats/');
define('GROUP_CHAT_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'chats/');
define('GROUP_CHAT_THUMB_IMAGE_PATH', SITE_PATH_UPLOAD.'chats/thumbnail/');
define('GROUP_CHAT_THUMB_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'chats/thumbnail/');

define('DEVELOPER_IMAGE_PATH', GAME_PATH_UPLOAD.'gamedevelopers/');
define('DEVELOPER_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'gamedevelopers/');
define('DEVELOPER_THUMB_IMAGE_PATH', GAME_PATH_UPLOAD.'gamedevelopers/thumbnail/');
define('DEVELOPER_THUMB_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'gamedevelopers/thumbnail/');

define('GAME_CERTIFICATE_PATH', SITE_PATH_UPLOAD.'certificate/');
define('S3_GAME_CERTIFICATE_PATH', S3_PATH.'certificate/');
define('GAME_CERTIFICATE_PATH_REL', ABS_PATH_UPLOAD.'certificate/');
define('S3_GAME_CERTIFICATE_PATH_REL', S3_PATH.'certificate/');
define('CUSTOM_PRIZE_IMAGE_PATH', GAME_PATH_UPLOAD.'customprize/');
define('CUSTOM_PRIZE_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'customprize/');

//Image for website
define('WEBSITE_PATH', SITE_PATH_UPLOAD.'website/');
define('WEBSITE_PATH_REL', ABS_PATH_UPLOAD.'website/');

define('SDK_FILE_PATH', SITE_PATH_UPLOAD.'sdk/');
define('SDK_FILE_PATH_REL', ABS_PATH_UPLOAD.'sdk/');

define('LIMIT',100);
define('PERPAGE',25);
define('PASSPHRASE_LENGTH',8);
define('ADMIN_PER_PAGE_LIMIT', 10);


define('GYFTAPIKEY', 'ym6x59t698tq4h3spb3pz5hw');
define('GYFTSECRETE', 'ZddmxmF7nh');

// define('GYFTAPIKEY', '5gjz26mafca9zvcwd7rvkquc');
// define('GYFTSECRETE', 'jRmrAGVB9X');

define('ADDITIONALCOINS', 500);

/*---------- Social network config details ----------*/
define('FB_APP_ID','');
define('CONSUMER_KEY','qtqPkx7XJPlKt15pZeNykWbu1'); // LjMYQSWAjgsnoQ8pk7AaWYN10
define('CONSUMER_SECRET','TSe87Q7gJjA069FKDdnD7wknZRg0Jc9TYopS25euFA3QkqUPiW');// s3pr7zOCNWwJ27iTNv7xfzz9cKlM2bHEr9OmDpBT4D5I4prpEv
/*---------- Social network config details ----------*/

//Encrypt word
define('ENCRYPTSALT',      'saltisgood');
global $admin_per_page_array;
$admin_per_page_array = array(10,50,100,200,250);
define('ADMIN_PER_PAGE_ARRAY', 'return ' . var_export($admin_per_page_array, 1) . ';');//define constant array
global $userStatus;
global $statusArray;
global $notification_msg;
global $notification_msg_class;
global $gameStatus;
global $gameResultStatus;
global $device_type_array;
global $game_type;
global $tournamentStatus;
global $tournamentActionArray;
global $cardStatus;
global $cardStatusArray;
global $platform_array;
global $gameStatus_array;
$userStatus				=	array('1'=>'Active','2'=>'Inactive','3'=>'Deleted','4'=>'Incomplete');
$statusArray			=	array('1'=>'Active','2'=>'Inactive','3'=>'Delete');
$cardStatus				=	array('1'=>'Visible','2'=>'Invisible');
$cardStatusArray		=	array('1'=>'Visible','2'=>'Invisible','3'=>'Delete');
$notification_msg		=	array('1'=>'added successfully','2'=>'updated successfully','3'=>'deleted successfully','4'=>'status changed successfully','5'=>'message sent successfully','6'=>'Approved successfully','7'=>'Rejected successfully','12'=>'removed successfully','13'=>" can't delete since some of the tournaments are active with this game.");
$notification_msg_class	=	array('1'=>'success_msg','2'=>'success_msg','3'=>'error_msg','4'=>'success_msg','5'=>'success_msg','6'=>'success_msg','7'=>'error_msg','12'=>'error_msg','13'=>'error_msg');
$gameStatus				=	array('1'=>'Playing','2'=>'Completed');
$gameResultStatus		=	array('1'=>'Won','2'=>'Lost');
$device_type_array		=	array(1=> 'iOS', 2=> 'Web');
$game_type				=	array('1'=> 'High score', '2'=> 'Elimination');
$tournamentStatus		=	array('0'=>'Upcoming','1'=>'Started','3'=>'Closed');
$tournamentActionArray	=	array('3'=>'Delete');
$brandStatus			=	array('1'=>'Approved','2'=>'Not yet approved');
$brandActionArray		=	array('4'=>'Inactive','3'=>'Delete');
$platform_array			=	array(0=> 'Web',1=> 'iOS', 2=> 'Android');
$gameStatus_array       =   array('1'=>'Approved','2'=>'Pending','3'=>'Deleted','4'=>'Rejected');
global $pricePercentageArray;
$pricePercentageArray   =   array('1'=>'45','2'=>'20','3'=>'11','4'=>'7','5'=>'5','6'=>'4','7'=>'3','8'=>'2','9'=>'2','10'=>'1');
global $positionArray;
$positionArray   		=   array('1'=>'st','2'=>'nd','3'=>'rd','4'=>'th');
global $cardVisibility;
$cardVisibility		=	array('1'=>'Visible','2'=>'Invisible');
$cardAvailability	=	array('1'=>'Available','2'=>'Not Available');

global $platform_type_rray;
$platform_type_rray			=	array('ios'=> '1','android'=> '2');

global $inAppStatusArray;
$inAppStatusArray			=	array('1'=>'Active','2'=>'Inactive','3'=>'Delete');

global $AppGameId;
global $applicationARN;
global $applicationARNSDK;
if($_SERVER['HTTP_HOST'] == '172.21.4.104') {

	$applicationARN	=	array(
								'ios' 		=> 	'arn:aws:sns:us-west-2:211166319390:app/APNS/Tilt-beta-Application',
								'android'	=>	'arn:aws:sns:us-west-2:211166319390:app/GCM/Tilt-TiltFish-Production-Android'
							);

	$applicationARNSDK	=	array(
								'ios' 		=> 	'arn:aws:sns:us-west-2:211166319390:app/APNS/Tilt-beta-fish',
								'android'	=>	'arn:aws:sns:us-west-2:211166319390:app/GCM/Tilt-TiltFish-Production-Android'
							);
}
else{
	$applicationARN	=	array(
								'ios' 		=> 	'arn:aws:sns:us-west-2:211166319390:app/APNS/Tilt-beta-Application',
								'android'	=>	'arn:aws:sns:us-west-2:211166319390:app/GCM/Tilt-TiltFish-Production-Android'
							);

	$applicationARNSDK	=	array(
								'ios' 		=> 	'arn:aws:sns:us-west-2:211166319390:app/APNS/Tilt-beta-fish',
								'android'	=>	'arn:aws:sns:us-west-2:211166319390:app/GCM/Tilt-TiltFish-Production-Android'
							);
}
global $methodArray;
$methodArray = array('POST','DELETE','GET','PUT');
global $cronStatus;
$cronStatus  = array('1'=>'In Progress','2'=>'Completed');
global $pinStatus;
$pinStatus	=	array('0'=> 'Not used','1'=> 'Used' );
define('PIN_LENGTH',8);
define('USID',236);
define('CUSTOMPRIZE_TYPE',4);
global $coinType;
$coinType = array('1'=> 'TiLT$','2'=> 'Virtual Coins' );

global $notificationArray;
$notificationArray		=	array(	'1'=>'TournamentStart',
									'2'=>'TournamentEnd',
									'3'=>'BrandNewTournament',
									'4'=>'CouponWon',
									'5'=>'ContactWinner',
									'6'=>'NumberOfCoins',
									'7'=>'InviteFriend',
									'8'=>'FBFriendsJoin',
									'9'=>'BeatHighScore',
									'10'=>'ChatNotification',
									'11'=>'GroupNotification',
									'12'=>'EmailNotification'
									);
global $packageArray;
$packageArray = array('100','500','1000','2000','5000' );
global $paymentHistoryArray;
$paymentHistoryArray		=	array(	'1'=>'Create Tournament',
										'2'=>'Win Prize',
										'3'=>'Join Tournament',
										'4'=>'Purchase Coins',
										'5'=>'Redeems',
										'6'=>'Admin Add',
										'7'=>'Admin Remove'
									);

 define('ADMIN_APPROVE_MAIL', 'pending@tilt.co');			// uncomment for server
// define('ADMIN_APPROVE_MAIL', 'pending@tilt.co,uhanesan@gmail.com,uhanesan@hotmail.com,uhanesan@yahoo.com,uhanesan@rediffmail.com');			// uncomment for server
  define('REDEEM_MAIL', 'ask@tilt.co,qa@alttabmobile.com');
/*------APP VERSION----*/
global $device_name_array,$app_type_array;
$device_name_array		= array('1'=>'IOS','2'=>'Android');
$app_type_array			= array('1'=>'Live','2'=>'Beta','3'=>'Local');

/*-------DISPLAY TEXT FOR ELIMINATION PROCESS--------*/
global $eliminationDisplay;
$eliminationDisplay = array('1'=>'Waiting for results','2'=>'Next round','3'=>'Eliminated','4'=>'You won');

/* Display status for Game Developer listing and detail page */
global $gameStatusArray;
$gameStatusArray	= array('0'=>'Not Verified','1'=>'Active','4'=>'Inactive');
?>
