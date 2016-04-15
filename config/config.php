<?php
/**
 * configuration variables
 *
 * This file has constants and global variable used throughout the application.
 *
 */
define("TITLE","Titl");
if (isset($_SERVER['HTTPS']) && ($_SERVER["HTTPS"] == 'on' ) )
	$site = 'https://';
elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
   $site = 'https://';
else
	$site = 'http://';

if($_SERVER['SERVER_ADDR']=='::1') //172.21.4.104')
{
	define('BASE_URL',$site.$_SERVER['HTTP_HOST']);
	define('ADMIN_SITE_PATH',  $site.$_SERVER['HTTP_HOST'].'/admin');
	define('ADMIN_ABS_PATH',  '/Applications/MAMP/htdocs/tilt-secure/admin');
	define('SITE_PATH',  $site.$_SERVER['HTTP_HOST'].'/');
	define('BRAND_SITE_PATH',$site.$_SERVER['HTTP_HOST'].'/brand');
	define('GAME_SITE_PATH',$site.$_SERVER['HTTP_HOST'].'/');
	define('GAME_ABS_PATH',  '/Applications/MAMP/htdocs/tilt-secure');
	//define('ABS_PATH',  'C:/wamp/www/TiLTPortals');
	define('ABS_PATH',  '/Applications/MAMP/htdocs/tilt-secure');
	define('SERVER',  0);
	define('GOBALUSERID', 1);
	define('GLOBALUSERID', 1);
	define('WEB_SITE_PATH',   'http://172.21.4.100/MGCWebsite/');
	$stripe = array(
	  "secret_key"      => "sk_test_UH0Z11SACdcIlH3EGoQdLzpF",
	  "publishable_key" => "pk_test_orRtRe8BjId7Sr8FHLkL3HDZ"
	);
}
else
{
	define('BASE_URL',$site.$_SERVER['HTTP_HOST']);
	define('ADMIN_SITE_PATH',  $site.$_SERVER['HTTP_HOST'].'/admin');
	define('ADMIN_ABS_PATH',  '/var/www/html/admin');
	define('GAME_ABS_PATH',  '/var/www/html');
	define('SITE_PATH',  $site.$_SERVER['HTTP_HOST']);
	define('BRAND_SITE_PATH',$site.$_SERVER['HTTP_HOST'].'/brand');
	define('GAME_SITE_PATH',$site.$_SERVER['HTTP_HOST']);
	define('ABS_PATH',  '/var/www/html');
	define('SERVER',  1);
	define('GOBALUSERID', 1);
	define('GLOBALUSERID', 1);
	define('WEB_SITE_PATH',   'http://tilt.co/');
	$stripe = array(
	  "secret_key"      => "sk_live_SsICRROu60pXVF5llUPIdY19",
	  "publishable_key" => "pk_live_r4RPs23m8aKhUUgt7B0L7HYp"
	);
}
define('REGION','us-west-2');

/** BEGIN: STIPE PAYMENT INTEGRATION **/
Stripe::setApiKey($stripe['secret_key']);
/** END: STIPE PAYMENT INTEGRATION **/

//script ans style path
define('SITE_TITLE', 'Titl');
define('GAME_SCRIPT_PATH', GAME_SITE_PATH.'/webresources/js/');
define('GAME_STYLE_PATH', GAME_SITE_PATH.'/webresources/css/');
define('GAME_IMAGE_PATH', GAME_SITE_PATH.'/webresources/images/');

define('UPLOAD_DEVELOPER_PATH_REL', ABS_PATH.'/webresources/uploads/gamedevelopers/');
define('UPLOAD_DEVELOPER_THUMB_PATH_REL', ABS_PATH.'/webresources/uploads/gamedevelopers/thumbnail/');
define('UPLOAD_GAMES_PATH_REL', ABS_PATH.'/admin/webresources/uploads/games/');
define('UPLOAD_GAMES_THUMB_PATH_REL', ABS_PATH.'/admin/webresources/uploads/games/thumbnail/');

define('UPLOAD_COUPON_PATH_REL', ABS_PATH.'/webresources/uploads/coupons/');
define('UPLOAD_BANNER_PATH_REL', ABS_PATH.'/webresources/uploads/banner/');
define('UPLOAD_YOUTUBE_LINK_PATH_REL', ABS_PATH.'/webresources/uploads/youtubelink/');

define('UPLOAD_CUSTOM_PRIZE_PATH_REL', ABS_PATH.'/webresources/uploads/customprize/');

define('UPLOAD_GAME_CERTIFICATE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/certificate/');

define('TEMP_IMAGE_PATH', SITE_PATH.'/webresources/uploads/temp/');	
define('TEMP_IMAGE_PATH_REL', ABS_PATH.'/webresources/uploads/temp/');

if ($_SERVER['HTTP_HOST'] == '::1'){
	define('BUCKET_NAME','mgcdemo');
}
else{
	define('BUCKET_NAME','tiltlive');
}
if($_SERVER['SERVER_ADDR']=='::1') {
	define('SITE_PATH_UPLOAD',  SITE_PATH.'/admin/webresources/uploads/');
	define('ABS_PATH_UPLOAD',  ABS_PATH.'/admin/webresources/uploads/');
	define('BRAND_PATH_UPLOAD', SITE_PATH.'/brand/webresources/uploads/');
	define('ABS_BRAND_PATH_UPLOAD', ABS_PATH.'/brand/webresources/uploads/');
	define('GAME_PATH_UPLOAD', SITE_PATH.'/webresources/uploads/');
	define('ABS_GAME_PATH_UPLOAD', ABS_PATH.'/webresources/uploads/');
} else {
	define('SITE_PATH_UPLOAD',  'https://d3q9j1xoc4b817.cloudfront.net/');
	define('ABS_PATH_UPLOAD',  'https://d3q9j1xoc4b817.cloudfront.net/');
	define('BRAND_PATH_UPLOAD',  'https://d3q9j1xoc4b817.cloudfront.net/');
	define('ABS_BRAND_PATH_UPLOAD', 'https://d3q9j1xoc4b817.cloudfront.net/');
	define('GAME_PATH_UPLOAD', 'https://d3q9j1xoc4b817.cloudfront.net/');
	define('ABS_GAME_PATH_UPLOAD', 'https://d3q9j1xoc4b817.cloudfront.net/');
}

define('DEVELOPER_IMAGE_PATH', GAME_PATH_UPLOAD.'gamedevelopers/');
define('DEVELOPER_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'gamedevelopers/');
define('DEVELOPER_THUMB_IMAGE_PATH', GAME_PATH_UPLOAD.'gamedevelopers/thumbnail/');	
define('DEVELOPER_THUMB_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'gamedevelopers/thumbnail/');

define('GAMES_IMAGE_PATH', SITE_PATH_UPLOAD.'games/');	
define('GAMES_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'games/');
define('GAMES_THUMB_IMAGE_PATH', SITE_PATH_UPLOAD.'games/thumbnail/');	
define('GAMES_THUMB_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'games/thumbnail/');

define('BANNER_IMAGE_PATH', GAME_PATH_UPLOAD.'banner/');
define('BANNER_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'banner/');

define('COUPON_IMAGE_PATH', GAME_PATH_UPLOAD.'coupons/');
define('COUPON_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'coupons/');

define('YOUTUBE_LINK_IMAGE_PATH', GAME_PATH_UPLOAD.'youtubelink/');
define('YOUTUBE_LINK_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'youtubelink/');

define('CUSTOM_PRIZE_IMAGE_PATH', GAME_PATH_UPLOAD.'customprize/');
define('CUSTOM_PRIZE_IMAGE_PATH_REL', ABS_GAME_PATH_UPLOAD.'customprize/');

define('ADMIN_IMAGE_PATH', SITE_PATH.'/admin/webresources/images/');
/* 
define('FLAG_IMAGE_PATH', ADMIN_IMAGE_PATH.'flags/');	
define('FLAG_IMAGE_PATH_REL', ADMIN_IMAGE_PATH.'flags/');
*/
define('USER_IMAGE_PATH', SITE_PATH_UPLOAD.'users/');	
define('USER_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'users/');
define('USER_THUMB_IMAGE_PATH', SITE_PATH_UPLOAD.'users/thumbnail/');	
define('USER_THUMB_IMAGE_PATH_REL', ABS_PATH_UPLOAD.'users/thumbnail/');

define('BRANDS_IMAGE_PATH', BRAND_PATH_UPLOAD.'brands/');	
define('BRANDS_IMAGE_PATH_REL', ABS_BRAND_PATH_UPLOAD.'brands/');
define('BRANDS_THUMB_IMAGE_PATH', BRAND_PATH_UPLOAD.'brands/thumbnail/');	
define('BRANDS_THUMB_IMAGE_PATH_REL', ABS_BRAND_PATH_UPLOAD.'brands/thumbnail/');

define('SDK_FILE_PATH', SITE_PATH_UPLOAD.'sdk/');
define('SDK_FILE_PATH_REL', ABS_PATH_UPLOAD.'sdk/');

define('ABS_PDF_PATH_UPLOAD', ABS_PATH.'/webresources/uploads/pdf/');
define('PDF_PATH', GAME_PATH_UPLOAD.'pdf/');
define('PDF_PATH_REL', ABS_GAME_PATH_UPLOAD.'pdf/');

define('LIMIT',100);
define('PERPAGE',25);
define('PASSPHRASE_LENGTH',8);
define('ADMIN_PER_PAGE_LIMIT', 10);
define('ADDITIONALCOINS', 500);
define('USID',236);
define('PIN_LENGTH',8);

define('GYFTAPIKEY', 'ym6x59t698tq4h3spb3pz5hw');
define('GYFTSECRETE', 'ZddmxmF7nh');
define('TIMEZONELATLNG','https://maps.googleapis.com/maps/api/timezone/json?key=AIzaSyBIj9HsTSxKzRAbw6Ls_XJMCcJZ8FY5zrE');
define('USLAT',40.7127);
define('USLONG',-74.0059);


/*---------- Social network config details ----------*/
define('FB_APP_ID','');
define('CONSUMER_KEY','LjMYQSWAjgsnoQ8pk7AaWYN10');
define('CONSUMER_SECRET','s3pr7zOCNWwJ27iTNv7xfzz9cKlM2bHEr9OmDpBT4D5I4prpEv');
/*---------- Social network config details ----------*/

//Encrypt word
define('ENCRYPTSALT',      'saltisgood');
global $admin_per_page_array;
$admin_per_page_array = array(10,50,100,200,250);
define('ADMIN_PER_PAGE_ARRAY', 'return ' . var_export($admin_per_page_array, 1) . ';');//define constant array
global $notification_msg;
global $notification_msg_class;
global $tournamentStatus;
global $gameStatusArray;
global $gamelistStatus_array;
global $pinStatus;
$gameStatusArray		=	array('1'=>'Active','2'=>'Inactive');
$notification_msg		=	array('1'=>'added successfully','2'=>'updated successfully','3'=>'deleted successfully',
									'4'=>'status changed successfully','5'=>'message sent successfully','6'=>'Approved successfully',
									'7'=>'Rejected successfully','11'=>'Thank you for registering. Please verify your account by activating the link sent to your E-mail id.',
									'12'=>'added successfully. Admin will approve your game soon');

$notification_msg_class	=		array('1'=>'success_msg no-padding','2'=>'success_msg no-padding','3'=>'error_msg','4'=>'success_msg no-padding','5'=>'success_msg',
									'6'=>'success_msg no-padding','7'=>'error_msg','11'=>'success_msg no-padding','12'=>'success_msg no-padding');
$tournamentStatus		=	array('0'=>'Upcoming','1'=>'Ongoing','3'=>'Finished');
$developerType			=	array('1'=>'User','3'=>'Developer & Brand');
$gamelistStatus_array   =   array('1'=>'Approved','2'=>'Pending','3'=>'Deleted','4'=>'Rejected');
$pinStatus				=	array('0'=> 'Not used','1'=> 'Used' );

define('ADMIN_APPROVE_MAIL', 'pending@tilt.co');			// uncomment for server
// define('ADMIN_APPROVE_MAIL', 'pending@tilt.co,uhanesan@gmail.com,uhanesan@hotmail.com,uhanesan@yahoo.com,uhanesan@rediffmail.com');			// Comment after testing
define('ADMIN_ASK_MAIL', 'ask@tilt.co');		// uncomment for server
//define('ADMIN_ASK_MAIL', 'ask@tilt.co,desmondmiles222@gmail.com,uhanesan@gmail.com');		// Comment after testing
?>
