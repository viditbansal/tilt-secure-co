<?php
ini_set('max_execution_time', 3600);
//$webservice = "http://172.21.4.104/MGC";
$webservice = "http://tiltbeta.elasticbeanstalk.com";
//$webservice = "https://api.tilt.co";

$count = 0;
date_default_timezone_set("America/New_York");
if(isset($_GET['Count']) && $_GET['Count'] > 0)
	$count = $_GET['Count']; //User create limit
else{
	echo "------->Please provide 'count' parameter in url(e.g : \"UserRegister.php?Count=10\") "; die;
}

if(isset($_GET['Indexing']) && $_GET['Indexing'] > 0)
	$indexing = $_GET['Indexing']; //User create limit
else{
	echo "------->Please provide 'Indexing' parameter in url(e.g : \"UserRegister.php?Indexing=10\") "; die;
}
$ActualPassword  = 'testing' ;

$insertedId = array();
$insertcount = 0;

$i = 1;
do{
	//Start : Assigning value to params
	$email = ($indexing + $i)."@alttabmobile.com";	
	$datas = array();
	$datas['Email'] 	= $email;
	$datas['Password'] 	= $ActualPassword;
	$datas['FirstName'] = ($indexing + $i);
	$datas['LastName'] 	= 'alttabmobile';
	$datas['Testing'] 	= '1';	
	//End : Assigning value to params
	
	$url						=	$webservice.'/v3/users/';
	$signUpResponse 			= 	curlRequest($url, 'POST', $datas);
	if(isset($signUpResponse) && is_array($signUpResponse) && $signUpResponse['meta']['code'] == 201){ 
		$insertcount++;
	}
	$i++;
	
	//Start : If all user email adress is already exists means need to end the loop
	if($i > ($count + 50)){
		$insertcount = $count;
	}
	//End : If all user email adress is already exists means need to end the loop
	
}while($insertcount != $count);
echo "<pre style='color:green'>Line No : ".__LINE__."<br>"; print_r($insertedId); echo "</pre>"; die;


function curlRequest($url, $method, $data = null, $access_token = '')
{
//echo '<pre>'; print_r($data); exit;
	$handle = curl_init();
	curl_setopt($handle, CURLOPT_URL, $url);
	if ($access_token != '') {
		// headers and data (this is API dependent, some uses XML)
		if ($method == 'PUT') {
		$headers = array(
						'Accept: application/json',
						'Content-Type: application/json',
						'Authorization: '.$access_token,
						);
		} else {
			$headers = array(
						'Authorization: '.$access_token
						);
		}
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	} 
	
	
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
	//curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)"); 
	curl_setopt($handle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); 
	
	switch($method) {
		case 'GET':
		break;
		case 'POST':
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		break;
		case 'PUT':
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		break;
		case 'DELETE':
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
		break;
	}
	$response = curl_exec($handle);
	
	$response = json_decode($response, true);
	return $response; 
}

?>
