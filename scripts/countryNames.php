<?php
error_reporting(E_ALL);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes

require_once('../admin/includes/CommonFunctions.php');
require_once('../admin/config/db_config.php');
require_once('../admin/config/config.php');

$mysql_server 	= HOST_NAME;
$mysql_user 	= USER_NAME;
$mysql_pass 	= PASSWORD;
$mysql_name 	= DATABASE_NAME;
mysql_connect($mysql_server,$mysql_user,$mysql_pass) or die('Error in connecting MySQL');
$db_con = mysql_select_db($mysql_name) or die('Error in selecting databse MySQL');


$url 				= "https://api.theprintful.com/countries";
$urlResult			=	curl_Request($url,'GET');

$result 		= 	json_decode($urlResult,1);
$countryInfo	=	$result['result'];

if(!empty($countryInfo))	{
	echo "<Br>Total Country Count: ".count($countryInfo)."<BR>";
	$today				=	date('Y-m-d H:i:s');
	$insertValues		=	'';
	$i = 0;
	$insertQuery		=	'insert into countries (Country,CountryCode,Status,DateCreated,DateModified) values ';
	foreach($countryInfo as $key=>$value)	{
		if(isset($value['states'])	&&	$value['states'] != '')	{
			$countryStateArray[]	=	$value;
		}
		else	{
			$insertValues	.=	' ("'.$value['name'].'","'.$value['code'].'","1","'.$today.'","'.$today.'"),';
			$i++;
			if($i >= 100)	{
				$insertValues	=	trim($insertValues,',');
				$query	=	$insertQuery.$insertValues;
				mysql_query($query);
				$insertValues = '';
				$i = 0;
			}
		}
	}
	$insertValues	=	trim($insertValues,',');
	$query			=	$insertQuery.$insertValues;
	mysql_query($query);
}

if(!empty($countryStateArray))	{
	$today				=	date('Y-m-d H:i:s');
	$insertValues		=	'';
	$i = 0;
	foreach($countryStateArray as $key=>$value)	{
		$insertQuery	=	'insert into countries (Country,CountryCode,Status,DateCreated,DateModified) values ("'.$value['name'].'","'.$value['code'].'","1","'.$today.'","'.$today.'")';
		mysql_query($insertQuery);
		$countryId		=	mysql_insert_id();
		if(isset($value['states'])	&&	$value['states']!='')	{
			$insertStateValues		=	'';
			$insertStateQuery		=	'insert into states (fkCountriesId,State,Status,DateCreated,DateModified) values';
			foreach($value['states'] as $skey=>$svalue)	{
				$insertStateValues	.=	'("'.$countryId.'","'.$svalue['name'].'","1","'.$today.'","'.$today.'"),';
				if($i >= 100)	{
					$insertStateValues	=	trim($insertStateValues,',');
					$query				=	$insertStateQuery.$insertStateValues;
					mysql_query($query);
					$insertStateValues = '';
					$i = 0;
				}
				
			}
			$insertStateValues	=	trim($insertStateValues,',');
			$query				=	$insertStateQuery.$insertStateValues;
			mysql_query($query);
		}
	}
}
echo "<Br><Br> Script executed successfully";

function curl_Request($url, $method, $data = null, $access_token = '')
{
	$handle = curl_init();
	curl_setopt($handle, CURLOPT_URL, $url);
	if ($access_token != '') {
		# headers and data (this is API dependent, some uses XML)
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
	return $response; 
}
?>


