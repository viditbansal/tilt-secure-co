<?php 
ini_set('max_execution_time', 7200);
ini_set('memory_limit', '2048M');
require_once('includes/CommonIncludes.php'); 
require_once('controllers/AdminController.php');
require_once('controllers/DeveloperController.php');
require_once('controllers/TournamentController.php');
require_once('controllers/GameController.php');
require_once('includes/MPDF57/mpdf.php');
$tournamentObj	=   new TournamentController();
$gameDevObj   	=   new DeveloperController();
$adminLoginObj	=	new AdminController();
$gameObj  =   new GameController();
$pdflocation1 = $pdfDevDetail = $pdflocation2 = '';
$tournamentPin = 'NO PIN REQUIRED';
$filePath = $filesize = $title = $BannerImage =	'';
if(!isset($_SESSION['tilt_developer_id']) || ( isset($_SESSION['tilt_developer_id']) && $_SESSION['tilt_developer_id'] == '')){
	header("Location:index"); 
	die();
}
$developerId		=	$_SESSION['tilt_developer_id'];

if(isset($_GET['tourid']))				// PIN Based
{
	$file_id = $_GET['tourid'];
	if(isset($_GET['pin']) && $_GET['pin'] != '')
		$tournamentPin = $_GET['pin'];
	if($file_id > 0) {
	
		$TournamentDetailsArray = $tournamentObj->selectTournament("id,fkGamesId,Prize,StartDate,TournamentName, Type ",'id="'.$file_id.'"');
		
		if(isset($TournamentDetailsArray) && count($TournamentDetailsArray) > 0) {
			
			$tournamentid = $TournamentDetailsArray[0]->id;
			$prizeImagePath = $customPrize	=	'';
			if(isset($TournamentDetailsArray[0]->Type) && $TournamentDetailsArray[0]->Type == 4){
				$titlCoin = '.tdisplay{display:none}';
				$titlCoin .= '.virtual{display:none}';
			}else if(isset($TournamentDetailsArray[0]->Type) && $TournamentDetailsArray[0]->Type == 3){
				$titlCoin = '.custom{display:none}';
				$titlCoin .= '.tdisplay{display:none}';
			}else{
				$titlCoin = '.custom{display:none}';
				$titlCoin .= '.virtual{display:none}';
			}
			
			//Game Developer details
			$pdfDevDetail	= $gameDevObj->selectSingleDeveloper("Photo,Company","id=".$developerId);
			$dev_image	=	isset($pdfDevDetail[0]->Photo) && !empty($pdfDevDetail[0]->Photo)? 'thumbnail/'.$pdfDevDetail[0]->Photo : '';
			$pdfDevImage	=	GAME_IMAGE_PATH."developer_logo.png";
			if($dev_image != '' ){
				if (!SERVER){
					if(file_exists(DEVELOPER_IMAGE_PATH_REL.$dev_image))
						$pdfDevImage = DEVELOPER_IMAGE_PATH.$dev_image;
				}
				else{
					if(image_exists(15,$dev_image))
						$pdfDevImage = DEVELOPER_IMAGE_PATH.$dev_image;
				}
			}
			///game details	
			$pdfgameDetail	=	$gameObj->selectGameDetails("Name, Photo"," id=".$TournamentDetailsArray[0]->fkGamesId);
			$game_logo	=	isset($pdfgameDetail[0]->Photo) ? $pdfgameDetail[0]->Photo : '';
			$game_image_path = GAME_IMAGE_PATH.'add_game.png';
			if($game_logo != '' ){
				if (!SERVER){
					if(file_exists(GAMES_IMAGE_PATH_REL.$game_logo))
						$game_image_path = GAMES_IMAGE_PATH.$game_logo;
				}
				else{
					if(image_exists(10,$game_logo))
						$game_image_path = GAMES_IMAGE_PATH.$game_logo;
				}
			}
			//coins default settings
			$conversionValue = $coinConverted = '0';
			$setting_details	=	$adminLoginObj->getSettingDetails(" ConversionValue ",' id = 1 ');
			if(isset($setting_details) && is_array($setting_details) && count($setting_details)>0){
				foreach($setting_details as $key => $value){
					$conversionValue	=	$value->ConversionValue;
				}
			}
			$coinConverted = ($TournamentDetailsArray[0]->Prize/$conversionValue);
			
			$where	= ' id = 5 ';
			$pdfContent	=	$adminLoginObj->getCMS(' Content ',$where);

			$BannerDetailsArray = $tournamentObj->checkCouponBannerLink("File"," Type=2 AND InputType = 2 AND Status = 1 AND fkTournamentsId = ".$file_id);
			$file_types_array = array("jpeg","pjpeg","jpg","png");
			$BannerImage = '';
			if(is_array($BannerDetailsArray) && count($BannerDetailsArray) > 0 && $BannerDetailsArray[0]->File != ''){
				$fileExt = getFileExtension(basename($BannerDetailsArray[0]->File));
				if(in_array($fileExt, $file_types_array)){
					$BannerImage = ".banner {background:url(".BANNER_IMAGE_PATH.$file_id."/".$BannerDetailsArray[0]->File.") no-repeat;background-position: center top;background-size:400px auto;background-image-resize:4;} .banner td{height:120px;}";
				}
			}

			$htmlContent	= "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
								<html>
								<head>
									<title>".$TournamentDetailsArray[0]->TournamentName." Tournament</title>
									<meta content='text/html; charset=utf-8' http-equiv='Content-Type'>
									<style>
										html,body {padding:0;margin:0;background:#D1D1D1;width:100%;height:100%;font:normal 16px 'nevis',arial;border:0;border-spacing:0;position:relative}
										.blue_top {color:#ffffff;background:#174a8e;font:normal 20px 'nevis',arial}
										.blue_top td{color:#ffffff;font:normal 20px 'nevis',arial;text-align: justify;}
										tr,td,table,div,span,img{padding:0;margin:0;border:0;border-spacing:0}".$BannerImage.$titlCoin."
									</style>
								</head>
								<body>".$pdfContent[0]->Content."</body>
								</html>";
			$tourName 		= ucfirst($TournamentDetailsArray[0]->TournamentName);
			$gameName 		= $pdfgameDetail[0]->Name;
			$tourName 		= replaceAccentByCode($tourName);
			$gameName 		= replaceAccentByCode($pdfgameDetail[0]->Name);
			
			$htmlContent	= str_replace("{TOURNAMENT_NAME}",$tourName.' Tournament',$htmlContent);
			$htmlContent	= str_replace("{TOURNAMENT_DATE}","Starts on ".date("F d, Y", strtotime($TournamentDetailsArray[0]->StartDate)),$htmlContent);
			$htmlContent	= str_replace("{TILT_COIN}",number_format($TournamentDetailsArray[0]->Prize),$htmlContent);
			$htmlContent	= str_replace("{TILT_COIN_CONVERTED}",number_format($coinConverted),$htmlContent);
			$htmlContent	= str_replace("{BRAND_IMAGE}",$pdfDevImage,$htmlContent);
			$htmlContent	= str_replace("{GAME_IMAGE}",$game_image_path,$htmlContent);
			$htmlContent	= str_replace("{GAME-NAME}",$gameName,$htmlContent);
			$htmlContent	= str_replace("{ADMIN_IMAGE_PATH}",GAME_IMAGE_PATH,$htmlContent);
			
			$htmlContent	= str_replace("{HEIGHT}",'30',$htmlContent);
			$htmlContent	= str_replace("{TOURNAMENT_PIN}",$tournamentPin,$htmlContent);
			$htmlContent	= str_replace("{COUPONIMAGE}",$prizeImagePath,$htmlContent);
			$htmlContent	= str_replace("{CUSTOMPRIZE}",$customPrize,$htmlContent);
			$htmlContent 	= iconv('Windows-1252', 'UTF-8//TRANSLIT',$htmlContent);
			
			if ( !file_exists(ABS_PDF_PATH_UPLOAD.$tournamentid) ){
				mkdir(ABS_PDF_PATH_UPLOAD.$tournamentid, 0777);
			}
			$fileName		= $tournamentid.'_'.time().'.pdf';
			$pdfName 		= $tournamentid.'/'.$fileName;
			ob_start();
			ob_clean();	
			$mpdf	= new mPDF('utf-8','A4','','Trebuchet MS' , 5 , 5 , 5 , 5 , 0 , 0);
			$mpdf->SetDisplayMode('fullwidth');//fullpage, fullwidth
			$mpdf->debug = true;
			$mpdf->WriteHTML($htmlContent);
			$destFilePath 	= ABS_PDF_PATH_UPLOAD.$pdfName;
			$mpdf->Output($destFilePath,'F');
			
			if (file_exists($destFilePath) ){
				if (SERVER){
					if($pdfName!='') {
						if(image_exists(13,$pdfName))
							deleteImages(13,$pdfName);
					}
					uploadImageToS3($destFilePath,13,$pdfName);
					unlink($destFilePath);
				}
				$tournamentObj->updateTournamentDetail("PdfName='".$fileName."'","id=".$tournamentid);
		    }
		}
		$fileExistsPath	= PDF_PATH_REL.$tournamentid.'/'.$fileName;
		 if(file_exists($fileExistsPath)){
			$filesize		= filesize($fileExistsPath);
		} 
	}
}
else if(isset($_GET['id']) && isset($_GET['pin'])){									//multiple download
	$file_id = $_GET['id'];
	$PinDetailsArray = $tournamentObj->getPinCode("*","id IN(".$_GET['pin'].") ");//checkPinCode
	if($file_id > 0 && is_array($PinDetailsArray) && count($PinDetailsArray) > 0) {
	
		$TournamentDetailsArray = $tournamentObj->selectTournament("id,fkGamesId,Prize,StartDate,TournamentName, Type ",'id="'.$file_id.'"');
		$dbfileName = '';
		if(isset($TournamentDetailsArray) && count($TournamentDetailsArray) > 0) {
			$tournamentid = $TournamentDetailsArray[0]->id;
			//Game Developer details
			$pdfDevDetail	= $gameDevObj->selectSingleDeveloper("Photo,Company","id=".$developerId);
			$prizeImagePath = $customPrize	=	'';
			if(isset($TournamentDetailsArray[0]->Type) && $TournamentDetailsArray[0]->Type == 4){
				$titlCoin = '.tdisplay{display:none}';
				$titlCoin .= '.virtual{display:none}';
			}else if(isset($TournamentDetailsArray[0]->Type) && $TournamentDetailsArray[0]->Type == 3){
				$titlCoin = '.custom{display:none}';
				$titlCoin .= '.tdisplay{display:none}';
			}else{
				$titlCoin = '.custom{display:none}';
				$titlCoin .= '.virtual{display:none}';
			}
				
			$dev_image	=	isset($pdfDevDetail[0]->Photo) && !empty($pdfDevDetail[0]->Photo)? 'thumbnail/'.$pdfDevDetail[0]->Photo : '';
			$pdfDevImage	=	GAME_IMAGE_PATH."developer_logo.png";
			if($dev_image != '' ){
				if (!SERVER){
					if(file_exists(DEVELOPER_IMAGE_PATH_REL.$dev_image))
						$pdfDevImage = DEVELOPER_IMAGE_PATH.$dev_image;
				}
				else{
					if(image_exists(15,$dev_image))
						$pdfDevImage = DEVELOPER_IMAGE_PATH.$dev_image;
				}
			}
			///game details	
			$pdfgameDetail	=	$gameObj->selectGameDetails("Name, Photo"," id=".$TournamentDetailsArray[0]->fkGamesId);
			$game_logo	=	isset($pdfgameDetail[0]->Photo) ? $pdfgameDetail[0]->Photo : '';
			$game_image_path = GAME_IMAGE_PATH.'add_game.png';
			if($game_logo != '' ){
				if (!SERVER){
					if(file_exists(GAMES_IMAGE_PATH_REL.$game_logo))
						$game_image_path = GAMES_IMAGE_PATH.$game_logo;
				}
				else{
					if(image_exists(10,$game_logo))
						$game_image_path = GAMES_IMAGE_PATH.$game_logo;
				}
			}
			$conversionValue = $coinConverted = '0';
			$setting_details	=	$adminLoginObj->getSettingDetails(" ConversionValue ",' id = 1 ');
			if(isset($setting_details) && is_array($setting_details) && count($setting_details)>0){
				foreach($setting_details as $key => $value){
					$conversionValue	=	$value->ConversionValue;
				}
			}
			$coinConverted = ($TournamentDetailsArray[0]->Prize/$conversionValue);
			
			$where	= ' id = 5 ';
			$pdfContent	=	$adminLoginObj->getCMS(' Content ',$where);
			
			$BannerDetailsArray = $tournamentObj->checkCouponBannerLink("File"," Type=2 AND InputType = 2 AND Status = 1 AND fkTournamentsId = ".$file_id);
			$file_types_array = array("jpeg","pjpeg","jpg","png");
			$BannerImage = '';
			if(is_array($BannerDetailsArray) && count($BannerDetailsArray) > 0 && $BannerDetailsArray[0]->File != ''){
				$fileExt = getFileExtension(basename($BannerDetailsArray[0]->File));
				if(in_array($fileExt, $file_types_array)){
					$BannerImage = ".banner {background:url(".BANNER_IMAGE_PATH.$file_id."/".$BannerDetailsArray[0]->File.") no-repeat;background-position: center top;background-size:400px auto;background-image-resize:4;} .banner td{height:120px;}";
				}
			}
			$htmlContent	= "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
								<html>
								<head>
									<title>".$TournamentDetailsArray[0]->TournamentName." Tournament</title>
									<meta content='text/html; charset=utf-8' http-equiv='Content-Type'>
									<style>
										html,body {padding:0;margin:0;background:#D1D1D1;width:100%;height:100%;font:normal 16px 'nevis',arial;border:0;border-spacing:0;position:relative}
										.blue_top {color:#ffffff;background:#174a8e;font:normal 20px 'nevis',arial}
										.blue_top td{color:#ffffff;font:normal 20px 'nevis',arial;text-align: justify;}
										tr,td,table,div,span,img{padding:0;margin:0;border:0;border-spacing:0}".$BannerImage.$titlCoin."
									</style>
								</head>
								<body>".$pdfContent[0]->Content."</body>
								</html>";
											
			$tourName 		= ucfirst($TournamentDetailsArray[0]->TournamentName);
			$gameName 		= $pdfgameDetail[0]->Name;
			$tourName 		= replaceAccentByCode($tourName);
			$gameName 		= replaceAccentByCode($pdfgameDetail[0]->Name);
			
			$htmlContent	= str_replace("{TOURNAMENT_NAME}",$tourName.' Tournament',$htmlContent);
			$htmlContent	= str_replace("{TOURNAMENT_DATE}","Starts on ".date("F d, Y", strtotime($TournamentDetailsArray[0]->StartDate)),$htmlContent);
			$htmlContent	= str_replace("{TILT_COIN}",number_format($TournamentDetailsArray[0]->Prize),$htmlContent);
			$htmlContent	= str_replace("{TILT_COIN_CONVERTED}",number_format($coinConverted),$htmlContent);
			$htmlContent	= str_replace("{BRAND_IMAGE}",$pdfDevImage,$htmlContent);
			$htmlContent	= str_replace("{GAME_IMAGE}",$game_image_path,$htmlContent);
			$htmlContent	= str_replace("{GAME-NAME}",$gameName,$htmlContent);
			//$htmlContent	= str_replace("{TOURNAMENT_LOCATION}",$pdflocation,$htmlContent);
			$htmlContent	= str_replace("{ADMIN_IMAGE_PATH}",GAME_IMAGE_PATH,$htmlContent);
			
			$htmlContent	= str_replace("{HEIGHT}",'30',$htmlContent);
			$htmlContent	= str_replace("{COUPONIMAGE}",$prizeImagePath,$htmlContent);
			$htmlContent	= str_replace("{CUSTOMPRIZE}",$customPrize,$htmlContent);
			$archive_files	= array();
			$archive_file_name = $tournamentid.'_'.time().'.zip';
			$zip = new ZipArchive();
			
			//create the file and throw the error if unsuccessful
			if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE )!==TRUE) {
				exit("cannot open <$archive_file_name>\n");
			}
			
			foreach ($PinDetailsArray as $pkey=>$pval){
				$htmlpinContent		= str_replace("{TOURNAMENT_PIN}",$pval->PinCode,$htmlContent);
				$htmlpinContent 	= iconv('Windows-1252', 'UTF-8//TRANSLIT',$htmlpinContent);
				
				if ( !file_exists(ABS_PDF_PATH_UPLOAD.$tournamentid) ){
					mkdir(ABS_PDF_PATH_UPLOAD.$tournamentid, 0777);
				}
				$fileName	= $tournamentid.'_'.$pval->id.'_'.time().'.pdf';
				if($dbfileName == '')
					$dbfileName = $fileName;
				$pdfName 			= $tournamentid.'/'.$fileName;
				$archive_files[]	= $pdfName;
				ob_start();
				ob_clean();	
				$mpdf	= new mPDF('utf-8','A4','','Trebuchet MS' , 5 , 5 , 5 , 5 , 0 , 0);
				$mpdf->SetDisplayMode('fullwidth');//fullpage, fullwidth
				$mpdf->debug = true;
				$mpdf->WriteHTML($htmlpinContent);
				$destFilePath 	= ABS_PDF_PATH_UPLOAD.$pdfName;
				$mpdf->Output($destFilePath,'F');
				
				if(!is_readable($destFilePath)) die('File not found or inaccessible!');
				$zip->addFile($destFilePath, $fileName);
			}
			$zip->close();
		}
		
		if(is_array($archive_files) && count($archive_files) > 0){
			foreach($archive_files as $files){
				if (file_exists(ABS_PDF_PATH_UPLOAD.$files) ){
					if (SERVER){
						if($pdfName!='') {
							if(image_exists(13,$files))
								deleteImages(13,$files);
						}
						uploadImageToS3(ABS_PDF_PATH_UPLOAD.$files,13,$files);
						unlink(ABS_PDF_PATH_UPLOAD.$files);
					}
				} 
			}
			if(isset($dbfileName) && $dbfileName > 0 && isset($tournamentid) && $tournamentid > 0)
				$tournamentObj->updateTournamentDetail("PdfName='".$dbfileName."'","id=".$tournamentid);
		}

		$fileExistsPath = $archive_file_name;
		if(file_exists($fileExistsPath))
			$filesize		= filesize($fileExistsPath);
	}
}
forcedownload($fileExistsPath,$filesize);
?>