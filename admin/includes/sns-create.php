<?php
require_once __DIR__ . '/aws.phar';
//require 'vendor/autoload.php';
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;
// Create a cache adapter that stores data on the filesystem
$cacheAdapter = new DoctrineCacheAdapter(new FilesystemCache('/var/www/html/admin/includes/tmp/cache/'));
// Provide a credentials.cache to cache credentials to the file system
$sns = Aws\Sns\SnsClient::factory(array(
	'credentials.cache' => $cacheAdapter,
	'region' => REGION
));
try
  {
	$result = $sns->createPlatformEndpoint(array(
	    'PlatformApplicationArn' => $PlatformApplicationArn,
	    'Token' => $Token,
	    'CustomUserData' => $CustomUserData.' / '.date('m-d-Y H:i:s'),
	    'Attributes' => array(
	        // Associative array of custom 'String' key names
	        //'String' => 'string',
	        // ... repeated
	    ),
	));
	return $result['EndpointArn'];
		
  }
catch (Exception $e)
{
	//  echo'<pre>';print_r($data);echo'</pre>';
	//echo"<br>";
	//print( $Token. " - Failed: " . $e->getMessage() . "!\n");
	
	if(strstr($e->getMessage(),'different attributes')){
		// code to check for attribute
		$text = $e->getMessage();
		$textval = explode('Endpoint',$text);
		$textvalue = explode('already',$textval[1]);
		$endpoint = trim($textvalue[0]);
		//echo '-endpoint-->'.$endpoint.'<---';
		$result = $sns->getEndpointAttributes(array(
	    	'EndpointArn' => $endpoint,
		));
		//echo'<pre>';print_r($result['Attributes']);echo'</pre>';
		if($result['Attributes']['Enabled'] == 'true'){
			return '';
		}
		else{
			$result = $sns->deleteEndpoint(array(
			    // EndpointArn is required
			    'EndpointArn' => $endpoint,
			));
			return 2;//endpoint delete
		}
	}
	else
	  return '';
}
?>