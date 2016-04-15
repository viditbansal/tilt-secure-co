<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/AdminController.php');
developer_login_check();
$adminManageObj  =   new AdminController();
$fields	=	'GameDeveloperSupport'; $where	= 	' id = 1 ';
$SettingscontentArray =	array();
$settingsRes	     =	$adminManageObj->getDistance($fields,$where);
if(isset($settingsRes) && is_array($settingsRes) && count($settingsRes)>0){
		$developer_support	=	$settingsRes[0]->GameDeveloperSupport;		
}
commonHead();
?>
<body  class="skin-black">
	<?php top_header(); ?>
	<section class="row content box-center content-header">
		<h2 align="center">Support</h2>
	</section>	  	
	<section class="content col-md-8 box-center develop_page">
		<?php echo $developer_support;  ?>	
	</section>
<?php   footerLinks(); commonFooter(); ?>
</html>
