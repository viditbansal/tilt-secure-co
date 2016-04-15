<?php
require("twitter/twitteroauth.php");
require("../admin/config/config.php");
session_start();

$twitteroauth = new TwitterOAuth(CONSUMER_KEY , CONSUMER_SECRET); // No need to change anything in this line.
$request_token = $twitteroauth->getRequestToken(GAME_SITE_PATH.'/includes/getTwitterData.php');
$_SESSION['oauth_token'] = $request_token['oauth_token']; // Save value in session variable
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
if(isset($_GET['Content']))
	$_SESSION['Post_content']		= $_GET['Content'];
else
	$_SESSION['Post_content']		= '';

if ($twitteroauth->http_code == 200) {
    $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
    header('Location: ' . $url);
} else {
    die('ERROR: Some thing goes wrong.');
}

?>
