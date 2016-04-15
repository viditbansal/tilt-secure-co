<?php
require_once __DIR__ . '/aws.phar';
//require 'vendor/autoload.php';
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;
// Create a cache adapter that stores data on the filesystem
$cacheAdapter = new DoctrineCacheAdapter(new FilesystemCache('/tmp/cache'));
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
	    'CustomUserData' => $CustomUserData,
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
	return '';
}
?>