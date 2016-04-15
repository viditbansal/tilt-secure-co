<?php 
require_once('includes/CommonIncludes.php');
developer_login_check();
require_once('controllers/AdminController.php');
$adminControllerObj =   new AdminController();
$ilog = $alog = '';

//get SDK page content
$fields		  =	" IphoneLog, AndroidLog ";
$where		  =	" id = 1 ";
$sdk_details = $adminControllerObj->getSdkDetail($fields, $where);
if(isset($sdk_details) && is_array($sdk_details) && count($sdk_details)>0){
	foreach($sdk_details as $key=>$value){
		$ilog 	= $value->IphoneLog;
		$alog 	= $value->AndroidLog;
	}
}

commonHead();
?>
<body  class="skin-black">
	<?php top_header(); ?>
	
	<section class="content-header">
		<h2 align="center">Change Logs</h2>
	</section>
   	<section class="content col-md-9 col-lg-7 box-center develop_page">
		<div class="col-xs-12">
			<div>
				<?php  if(isset($_GET['type']) && $_GET['type'] == 1){
					if(isset($ilog) && $ilog != ''){  echo $ilog; } else { echo ""; }
				} else {
					if(isset($alog) && $alog != ''){  echo $alog; } else { echo ""; } 
				} ?>
			</div>
		</div>
		<div class="clear"><br><br><br><br></div>
	</section>
	
	<div class="box-footer clear" align="center">
		<input type="button" class="btn btn-green" name="Back" id="Back" value="Back" title="Back" onclick="location.href='Developing'">
	</div>
	
						  	
<?php   footerLinks(); commonFooter(); ?>
</html>
