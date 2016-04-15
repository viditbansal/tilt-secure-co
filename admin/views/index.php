<?php


/**
 * Load configuration
 */
require_once('../../config.php');
require_once "../../admin/includes/CommonFunctions.php";

/**
 * Load libraries
 */
require('../../vendor/autoload.php');                   // composer library
require_once '../../lib/tiltApi.php';               	// application
require_once '../../lib/Helpers/PasswordHelper.php';    // password helper
require_once '../../lib/tiltApiResponse.php';       	// response
require_once '../../lib/tiltApiResponseMeta.php';   	// response meta
require_once '../../lib/ModelBaseInterface.php';        // base interface class for RedBean models
require_once '../../lib/Model_Users.php';             // Model: Account
use Helpers\ResponseHelper as ResponseHelper;
/**
 * Initialize application
 *
 * We set $startAuthServer to true to start the authorization server
 */
tiltApi::init(true);
$app = new \Slim\Slim();

/**
 * Check the Facebook and linkedIn Id callback function
 */
$checkLoginCallBack = function($email,$password,$FBId,$deviceToken,$endpointARN,$platform) {

    return Model_Users::checkLogin($email,$password,$FBId,$deviceToken,$endpointARN,$platform);
};

/**
 *
 * This is how you obtain an authentication token
 * We are doing a post to the following endpoint: /oauth2/password/token
 * When doing so we pass the following parameters:
 
 * - ClientId
 * - ClientSecret
 * - FBId
 * - TwitterId
 * - UserName
 * - Password
 *
 * path: /oauth2/password/token
 */
$app->post('/token', function () use ($app, $checkLoginCallBack) {
	try {

        $req = $app->request();
		$res = $app->response();
		$res['Content-Type'] = 'application/json';

        // grab the authorization server from the api
        $authServer = tiltApi::$authServer;
		
        // We are going for this flow in oauth 2.0: Resource Owner Password Credentials Grant
        $grant = new League\OAuth2\Server\Grant\Password($authServer);

        // this is where we check the Facebook and linkedIn Id
		
		$user_id = $grant->setVerifyCredentialsCallback($checkLoginCallBack);
        $authServer->addGrantType($grant);
        // get the response from the server
        $response = $authServer->getGrantType('password')->completeFlow();
		/** TO ASSIGN THE RESPONSES TO A FUNCTION **/
		ResponseHelper::setResponse(json_encode($response));
		/** END TO ASSIGN THE RESPONSES TO A FUNCTION **/
        echo json_encode($response);

    }
    catch (League\OAuth2\Server\Exception\ClientException $e) {

        // Get the http status code based on the oauth error
        $status = tiltApi::$errorCodeLookup[$e->getCode()];
		tiltApi::showError($e, $status);
    }
    catch (Exception $e) {

        // Something went wrong
        tiltApi::showError($e);

    }
});

/**
 * Start the Slim Application
 */
$app->run();