<?php
ob_start();
require_once('includes/CommonIncludes.php');
require_once('controllers/TournamentController.php');
developer_login_check();
$tourManageObj=new TournamentController();
if(isset($_GET['tType']) && !empty($_GET['tType']) && isset($_GET['tId']) && !empty($_GET['tId']) && $_GET['tId']>0){
	if($_GET['tType']=='start') {
		$sql='';
		$sql.="StartDate='".date('Y-m-d H:i')."', ";
		$sql.="StartTime='".date('Y-m-d H:i')."', ";
		$sql.="TournamentStatus='2', ";
		$sql.="DateModified='".date('Y-m-d H:i:s')."'";
		$condition= " id = ".$_GET['tId'];
		$tourManageObj->updateTournamentDetail($sql,$condition);
	}
	if($_GET['tType']=='end') {
		$sql='';
		$sql.="EndDate='".date('Y-m-d H:i')."', ";
		$sql.="EndTime='".date('Y-m-d H:i')."', ";
		$sql.="TournamentStatus='3', ";
		$sql.="DateModified='".date('Y-m-d H:i:s')."'";
		$condition= " id = ".$_GET['tId'];
		$tourManageObj->updateTournamentDetail($sql,$condition);
	}
}
ob_clean();
header("Location: ".$_SERVER['HTTP_REFERER']);
?>