<?php
require("twitter/twitteroauth.php");
require "../admin/config/config.php";

session_start();
if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {

    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
    $_SESSION['access_token'] = $access_token;
	$_SESSION['oauth_token']	= $access_token['oauth_token'];
	$_SESSION['oauth_token_secret']	= $access_token['oauth_token_secret'];
    $user_info = $twitteroauth->get('account/verify_credentials');
    if (isset($user_info->error)) {
        header('Location: login-twitter.php');
    } else {
	   $twitter_otoken=$_SESSION['oauth_token'];
	   $twitter_otoken_secret=$_SESSION['oauth_token_secret'];
	   require("twitter-api-php-master/TwitterAPIExchange.php");
	    error_reporting(E_ALL);
		ini_Set('display_errors','on');
		$url = 'https://api.twitter.com/1.1/statuses/update_with_media.json';
		$requestMethod = 'POST';
		$settings = array(
	   	 	'oauth_access_token' => $twitter_otoken,
	    	'oauth_access_token_secret' => $twitter_otoken_secret,
	    	'consumer_key' => CONSUMER_KEY,
	    	'consumer_secret' => CONSUMER_SECRET
		);
		if(isset($_SESSION['Post_content']))
			$Message	= $_SESSION['Post_content'];
		else
			$Message	= 'Tournament';
		if($_SESSION['tilt_developer_logo']=='developer_logo.png') 
			$image = GAME_IMAGE_PATH.$_SESSION['tilt_developer_logo']; 
		else $image = DEVELOPER_IMAGE_PATH.$_SESSION['tilt_developer_logo']; 
		
		$data = file_get_contents($image);
		$postfields = array(
			'x_auth_mode' => 'reverse_auth',
	    	'screen_name' => 'MGC-TiLT',
			'status' => $Message,
			'media'	=> $data,
			// ""
		);
		$twitter = new TwitterAPIExchange($settings);
		$post = $twitter->setPostfields($postfields)
          		  ->buildOauth($url, $requestMethod)
           		 ->performRequest();
	    }
	?>
	 <script>
		self.close();
	</script> 
<?php
} 
 else {
    // Something's missing, go back to square 1
    header('Location: login-twitter.php');
}
?>
