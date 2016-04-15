<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
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

//update SDK page content
if(isset($_POST['sdk_submit']) && $_POST['sdk_submit'] != '' )
{	
	$update = '';
	$alreadyExist = 0;
	$_POST       = unEscapeSpecialCharacters($_POST);
    $_POST       = escapeSpecialCharacters($_POST);
	if(isset($_FILES)){
		if($alreadyExist == 0){
			if ( !file_exists(UPLOAD_SDK_PATH_REL) ){
				mkdir(UPLOAD_SDK_PATH_REL, 0777);
			}
			if ( !file_exists(UPLOAD_SDK_PATH_REL.'iphone/') ){
				mkdir(UPLOAD_SDK_PATH_REL.'iphone/', 0777);
			}
			if ( !file_exists(UPLOAD_SDK_PATH_REL.'android/') ){
				mkdir(UPLOAD_SDK_PATH_REL.'android/', 0777);
			}
			//iPhone SDK
			if (isset($_FILES['isdk']) && $_FILES['isdk']['name'] != '') {
				$target_path 		= UPLOAD_SDK_PATH_REL.'iphone/'.$_FILES['isdk']['name'];
				move_uploaded_file($_FILES["isdk"]["tmp_name"], $target_path);
				if (SERVER){
					uploadImageToS3($target_path,21,'iphone/'.$_FILES['isdk']['name']);
					unlink($target_path);
				}
				$update .= " IphoneSdk = '".addslashes($_FILES['isdk']['name'])."', ";
			}
			//iPhone Document
			if (isset($_FILES['idoc']) && $_FILES['idoc']['name'] != '') {
				$ext = pathinfo($_FILES['idoc']['name'], PATHINFO_EXTENSION);
				$docName = 'Doc_'.time().'.'.$ext;
				$target_path 		= UPLOAD_SDK_PATH_REL.'iphone/'.$docName;
				move_uploaded_file($_FILES["idoc"]["tmp_name"], $target_path);
				if (SERVER){
					uploadImageToS3($target_path,21,'iphone/'.$docName);
					unlink($target_path);
				}
				$update .= " IphoneDocument = '".addslashes($docName)."', ";
			}
			//Android SDK
			if (isset($_FILES['asdk']) && $_FILES['asdk']['name'] != '') {
				$target_path 		= UPLOAD_SDK_PATH_REL.'android/'.$_FILES['asdk']['name'];
				move_uploaded_file($_FILES["asdk"]["tmp_name"], $target_path);
				if (SERVER){
					uploadImageToS3($target_path,21,'android/'.$_FILES['asdk']['name']);
					unlink($target_path);
				}
				$update .= " AndroidSdk = '".addslashes($_FILES['asdk']['name'])."', ";
			}
			//Android Document
			if (isset($_FILES['adoc']) && $_FILES['adoc']['name'] != '') {
				$ext = pathinfo($_FILES['adoc']['name'], PATHINFO_EXTENSION);
				$docName = 'Doc_'.time().'.'.$ext;
				$target_path 		= UPLOAD_SDK_PATH_REL.'android/'.$docName;
				move_uploaded_file($_FILES["adoc"]["tmp_name"], $target_path);
				if (SERVER){
					uploadImageToS3($target_path,21,'android/'.$docName);
					unlink($target_path);
				}
				$update .= "AndroidDocument = '".addslashes($docName)."', ";
			}
			$updateString   =   ' IphoneVersion = "'.$_POST['iver'].'",IphoneLog = "'.$_POST['ilog'].'",AndroidVersion = "'.$_POST['aver'].'",AndroidLog = "'.$_POST['alog'].'", '.$update.' DateModified="'.date('Y-m-d H:i:s').'"';	
			$condition      =   " id = 1 ";
			$adminControllerObj->updateSdkDetail($updateString,$condition);
			$_SESSION['notification_msg_code']	=	2;
			header('location:Sdk');
			die();
		}else{  // already exist condition
			$class = "error_msg";
			if ($alreadyExist == 1){
				$error_msg   = "Iphone SDK already exists";
			}
			else if($alreadyExist	==	2	)	{
				$error_msg   = "Android SDK already exists";
			}
		}
	}
}

commonHead();
?>
<body>
<?php top_header(); ?>	
	<div class="box-header"><h2><i class="fa fa-pencil-square-o" ></i>SDK</h2></div>
	<div class="clear"></div>
	<form name="sdk_form1" id="sdk_form1" action="" method="post" enctype="multipart/form-data">
		<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">
	
		<tr><td align="center">
			<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">							 
				<tr><td colspan="7" valign="top" class="msg_height" align="center">
					<?php displayNotification('SDK Content '); ?>
					<div class="<?php echo $class;  ?> w50">
						<span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span>
					</div>
				</td></tr>
				<tr><td colspan="3" height="50" align="left" valign="top"><h2>iPhone</h2></td>	<td colspan="3">&nbsp;</td></tr>
				<tr>
					<td align="left" width="15%" valign="top">
						<label>SDK
						<span class="required_field"></span></label>
					</td>
					<td align="center" valign="top" width="10%">:</td>
					<td height="60" valign="top" align="left" width="75%" >
					<div style="clear: both;float: left"> <input type="file" class="upload w90" id="isdk" name="isdk" onchange="return ajaxSdkUploadProcess(this.value,'isdk');" > </div>
							<input id="status_isdk" type="hidden" name="status_isdk" value="<?php if(isset($isdk_path)) echo 1; else echo 0; ?>" >						
					<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
						<div id="isdk_img">		
						<?php  if(isset($isdk_path) && $isdk_path != ''){  ?>
							<a href="<?php echo $isdk_path; ?>" title="Click here" target="_blank" class="" alt="Click here" ><?php echo $isdk; ?></a>
						<?php  }  ?>						
						</div>
					</div>
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top">
						<label>Document
						<span class="required_field"></span></label>
					</td>
					<td align="center" valign="top">:</td>
					<td height="60" valign="top" align="left">
					<div style="clear: both;float: left"> <input type="file" class="upload w90" id="idoc" name="idoc" onchange="return ajaxDocUploadProcess(this.value,'idoc');"> </div>
					<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
						<div id="idoc_img">		
						<?php  if(isset($idoc_path) && $idoc_path != ''){  ?>
							<a href="<?php echo $idoc_path; ?>" title="Click here" target="_blank" class="" alt="Click here" ><?php echo $idoc; ?></a>
						<?php  }  ?>						
						</div>
							<input id="status_idoc" type="hidden" name="status_idoc" value="<?php if(isset($idoc_path)) echo 1; else echo 0; ?>" >						
					</div>					
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top">
						<label>Version
						<span class="required_field"></span></label>
					</td>
					<td align="center" class="" valign="top">:</td>
					<td height="60" valign="top" align="left">
						<input type="text" class="input" id="iver" name="iver" maxlength="8" style="width:30%" onpaste="return false;" onkeypress="return isAlphaNumericDot(event);" value="<?php if(isset($iver) && $iver != '')  echo $iver; ?>">
					</td>
				</tr>			
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top">
						<label>Change Log
						<span class="required_field"></span></label>
					</td>
					<td align="center" class="" valign="top">:</td>
					<td height="60" valign="top" align="left">
						<textarea class="add_cms" id="ilog" rows="15" cols="106" name="ilog"><?php echo $ilog; ?></textarea>
					</td>
				</tr>
				<tr><td height="25" colspan="2"></td></tr>
				<tr><td colspan="3" height="50" align="left" valign="top"><h2>Android</h2></td>	<td colspan="3">&nbsp;</td></tr>
				<tr>
					<td align="left" valign="top">
						<label>SDK
						<span class="required_field"></span></label>
					</td>
					<td align="center" valign="top">:</td>
					<td height="60" valign="top" align="left">
					<div style="clear: both;float: left"> <input type="file" class="upload w90" id="asdk" name="asdk" onchange="return ajaxSdkUploadProcess(this.value,'asdk');" > </div>
					<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
						<div id="asdk_img">		
						<?php  if(isset($asdk_path) && $asdk_path != ''){  ?>
							<a href="<?php echo $asdk_path; ?>" title="Click here" target="_blank" class="" alt="Click here" ><?php echo $asdk; ?></a>
						<?php  }  ?>						
						</div>
							<input id="status_asdk" type="hidden" name="status_asdk" value="<?php if(isset($asdk_path)) echo 1; else echo 0; ?>" >						
					</div>
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top">
						<label>Document
						<span class="required_field"></span></label>
					</td>
					<td align="center" valign="top">:</td>
					<td height="60" valign="top" align="left">
					<div style="clear: both;float: left"> <input type="file" class="upload w90" id="adoc" name="adoc" onchange="return ajaxDocUploadProcess(this.value,'adoc');"> </div>
					<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
						<div id="adoc_img">		
						<?php  if(isset($adoc_path) && $adoc_path != ''){  ?>
							<a href="<?php echo $adoc_path; ?>" title="Click here" target="_blank" class="" alt="Click here" ><?php echo $adoc; ?></a>
						<?php  }  ?>						
						</div>
							<input id="status_adoc" type="hidden" name="status_adoc" value="<?php if(isset($adoc_path)) echo 1; else echo 0; ?>" >						
					</div>					
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top">
						<label>Version
						<span class="required_field"></span></label>
					</td>
					<td align="center" valign="top">:</td>
					<td height="60" valign="top" align="left">
						<input type="text" class="input" id="aver" name="aver" maxlength="8" style="width:30%" onpaste="return false;" onkeypress="return isAlphaNumericDot(event);" value="<?php if(isset($aver) && $aver != '')  echo $aver; ?>">
					</td>
				</tr>			
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
					<td align="left" valign="top">
						<label>Change Log
						<span class="required_field"></span></label>
					</td>
					<td align="center" valign="top">:</td>
					<td height="60" valign="top" align="left">
						<textarea class="alog" id="alog" rows="15" cols="106" name="alog"><?php echo $alog; ?></textarea>
					</td>
				</tr>
				<tr><td height="20" colspan="2"></td></tr>
				<tr>
				<td colspan="2"></td>
				<td align="left">
				<input type="submit" class="submit_button" name="sdk_submit" id="sdk_submit" value="Submit" title="Submit" alt="Submit" />
				<a href="UserList?cs=1" class="submit_button" name="Cancel" id="Cancel" value="Cancel" title="Cancel" alt="Cancel" tabindex="NaN">Cancel</a>
				</td>
				</tr>
			</table>
		</tr></td>
		<tr><td height="35"></td></tr>
		</table>
	</form>
<?php commonFooter(); ?>
</html>