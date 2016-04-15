<?php
ob_start();
require_once('includes/CommonIncludes.php');
if (isset($_GET['gameId']) && ($_GET['gameId'] != '' && isset($_GET['fileName']) && $_GET['fileName'] !='' )) {
	$certificatePath = '';
	$gameId 		 = $_GET['gameId'];
	$certificateName = urldecode($_GET['fileName']);
	if(SERVER){
		if(image_exists(19,$gameId.'/'.$certificateName))
			$certificatePath = GAME_CERTIFICATE_PATH.$gameId.'/'.$certificateName;
		else 
			$certificatePath = '';
	}else{
		if(file_exists(UPLOAD_GAME_CERTIFICATE_PATH_REL.$gameId.'/'.$certificateName))
			$certificatePath = GAME_CERTIFICATE_PATH.$gameId.'/'.$certificateName;
		else
			$certificatePath = '';
	}
	if(!empty($certificatePath)){
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/x-pkcs12");
		header("Content-Disposition: attachment; filename=\"".$certificateName."\";" );
		header("Content-Transfer-Encoding: binary");
		readfile($certificatePath);
		exit();
	}else echo '<br><h1 align="center">File not available that you are trying to download !</h1>'; 
}			
?>