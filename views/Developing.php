<?php
require_once('includes/CommonIncludes.php');
require_once("includes/phmagick.php");
require_once('controllers/GameController.php');
require_once('controllers/TournamentController.php');
developer_login_check();
require_once('controllers/AdminController.php');
$adminControllerObj =   new AdminController();
$isdk 	= 	$asdk	=	$idoc		=	$adoc	=	$iver  = $aver  =  $ilog = $alog =	$class = '';

//get SDK page content
$fields		  =	" * ";
$where		  =	" id = 1 ";
$sdk_details = $adminControllerObj->getSdkDetail($fields, $where);
if(isset($sdk_details) && is_array($sdk_details) && count($sdk_details)>0){
	foreach($sdk_details as $key=>$value){
		$isdk 	= $value->IphoneSdk;	
		$asdk	= $value->AndroidSdk;
		$idoc	= $value->IphoneDocument;
		$adoc	= $value->AndroidDocument;
		$iver  	= $value->IphoneVersion;
		$aver  	= $value->AndroidVersion;
		$ilog 	= $value->IphoneLog;
		$alog 	= $value->AndroidLog;
		
		if($isdk !=''){
			if(SERVER){
				if(image_exists(21,'iphone/'.$isdk)){
					$isdk_path = SDK_FILE_PATH.'iphone/'.$isdk;
				}
			
			}
			else if(file_exists(SDK_FILE_PATH_REL.'iphone/'.$isdk)){
					$isdk_path = SDK_FILE_PATH.'iphone/'.$isdk;
			}
		}
		
		if($idoc !=''){
			if(SERVER){
				if(image_exists(21,'iphone/'.$idoc)){
					$idoc_path = SDK_FILE_PATH.'iphone/'.$idoc;
				}
			
			}
			else if(file_exists(SDK_FILE_PATH_REL.'iphone/'.$idoc)){
					$idoc_path = SDK_FILE_PATH.'iphone/'.$idoc;
			}
		}
		
		if($asdk !=''){
			if(SERVER){
				if(image_exists(21,'android/'.$asdk)){
					$asdk_path = SDK_FILE_PATH.'android/'.$asdk;
				}
			
			}
			else if(file_exists(SDK_FILE_PATH_REL.'android/'.$asdk)){
					$asdk_path = SDK_FILE_PATH.'android/'.$asdk;
			}
		}
		
		if($adoc !=''){
			if(SERVER){
				if(image_exists(21,'android/'.$adoc)){
					$adoc_path = SDK_FILE_PATH.'android/'.$adoc;
				}
			
			}
			else if(file_exists(SDK_FILE_PATH_REL.'android/'.$adoc)){
					$adoc_path = SDK_FILE_PATH.'android/'.$adoc;
			}
		}
	}
}

commonHead();
?>
<body  class="skin-black">
	<?php top_header(); ?>
	
	<section class="content-header">
		<h2 align="center">Docs</h2>
	</section>
   	<section class="content col-md-9 col-lg-7 box-center develop_page">
		<div class="col-xs-6 col-sm-6">
			<div class="bg-light-blue sm-box">
				<h3>iOS developers guide</h3>
				<p>Download the developer guide and follow the steps to integrate SDK for the game.</p>
				<?php  if(isset($idoc_path) && $idoc_path != ''){ ?>
				<a href="<?php  echo $idoc_path; ?>" title="Click here to download" target="<?php echo '_blank'; ?>" class="" alt="Click here to download"><img src="webresources/images/download_icon.png" width="43" height="43" alt=""></a>
				<?php } else { ?>
				<a href="javascript:void(0)" style="cursor:default"><img src="webresources/images/download_icon.png" width="43" height="43" alt=""></a>
				<?php } ?>
			</div>
		</div>
		
		<div class="col-xs-6 col-sm-6">
			<div class="bg-light-blue sm-box">
				<h3>Android developers guide</h3>
				<p>Download the developer guide and follow the steps to integrate SDK for the game.</p>
				<?php  if(isset($adoc_path) && $adoc_path != ''){ ?>
				<a href="<?php  echo $adoc_path; ?>" title="Click here to download" target="<?php echo '_blank'; ?>" class="" alt="Click here to download"><img src="webresources/images/download_icon.png" width="43" height="43" alt=""></a>
				<?php } else { ?>
				<a href="javascript:void(0)" style="cursor:default"><img src="webresources/images/download_icon.png" width="43" height="43" alt=""></a>
				<?php } ?>
			</div>
		</div>
		<div class="clear"><br><br><br><br></div>
	</section>
	
	<section class=" bg-dark-gray clear develop_page" style="margin-bottom:-75px;">
		<div class="col-md-7 col-lg-6 box-center">
			<section class="content-header">
				<h2 align="center"><br><br>SDK<br><br></h2>
			</section>
			
			<div class="col-xs-6 col-sm-6">
				<div class="bg-white sm-box">
					<h3>iOS SDK</h3>
					<p class="version"><?php if(isset($iver) && $iver != ''){ echo "ver ".$iver; } ?></p>
					<?php  if(isset($ilog) && $ilog != ''){ ?>
						<p class="change_log"><a href="ChangeLog?type=1" title="Change Log" class="" alt="Change Log">Change Log</a></p>
					<?php } ?>
					<?php  if(isset($isdk_path) && $isdk_path != ''){ ?>
					<a href="<?php  echo $isdk_path; ?>" title="Click here to download" target="<?php echo '_blank'; ?>" class="" alt="Click here to download"><img src="webresources/images/download_icon.png" width="43" height="43" alt=""></a>
					<?php } else { ?>
					<a href="javascript:void(0)" style="cursor:default"><img src="webresources/images/download_icon.png" width="43" height="43" alt=""></a>
					<?php } ?><br><br>
				</div>
			</div>
			
			<div class="col-xs-6 col-sm-6">
				<div class="bg-white sm-box">
					<h3>Android SDK</h3>
					<p class="version"><?php if(isset($aver) && $aver != ''){ echo "ver ".$aver; } ?></p>
					<?php  if(isset($alog) && $alog != ''){ ?>
						<p class="change_log"><a href="ChangeLog?type=2" title="Change Log" class="" alt="Change Log">Change Log</a></p>
					<?php } ?>
					<?php  if(isset($asdk_path) && $asdk_path != ''){ ?>
					<a href="<?php  echo $asdk_path; ?>" title="Click here to download" target="<?php echo '_blank'; ?>" class="" alt="Click here to download"><img src="webresources/images/download_icon.png" width="43" height="43" alt=""></a>
					<?php } else { ?>
					<a href="javascript:void(0)" style="cursor:default"><img src="webresources/images/download_icon.png" width="43" height="43" alt=""></a>
					<?php } ?><br><br>
				</div>
			</div>
			<div class="clear"><br><br><br><br></div>
		</div>
	</section>
						  	
<?php   footerLinks(); commonFooter(); ?>
</html>
