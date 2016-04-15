<?php
    ob_start();
	session_start();
	foreach($_SESSION as $sessionVariable=>$value){
		if(stristr ($sessionVariable,'tilt_')){
			unset($_SESSION[$sessionVariable]);
		}
	}
	if( function_exists('destroyPagingControlsVariables') ) destroyPagingControlsVariables();
	session_destroy();
	header("location:index.php");
    die();
?>