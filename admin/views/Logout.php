<?php
    ob_start();
	session_start();
	foreach($_SESSION as $sessionVariable=>$value){
		if(stristr ($sessionVariable,'mgc_')){
			unset($_SESSION[$sessionVariable]);
		}
	}
	if( function_exists('destroyPagingControlsVariables') ) destroyPagingControlsVariables();
	header("location:index.php");
    die();
?>