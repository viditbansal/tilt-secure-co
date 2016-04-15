<?php ob_start();
ERROR_REPORTING(E_ALL);
if($_SERVER['SERVER_ADDR'] != '//172.21.4.104') {
	$dir = sys_get_temp_dir();
	session_save_path($dir);
}
if ((isset($_GET['page'])) && ($_GET['page'] != '') ){
	if(file_exists('views/'.$_GET['page'].'.php')){	
		require_once('views/'.$_GET['page'].'.php');
	}
	else {	
		header("location:Login");
	}
}else {
	//echo $_SERVER['SERVER_ADDR'];
	//exit();
	header("location:Login");
}
