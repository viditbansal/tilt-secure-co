<?php
class ReportController extends Controller
{
	function getTotalRecordCount(){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getTotalRecordCount();
	}
	function getDeveloperUsersReport($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getDeveloperUsersReport($fields,$condition);	
	}
	function getUserCoinsCount($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getUserCoinsCount($fields,$condition);	
	}
	function getGameReportList($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getGameReportList($fields,$condition);	
	}
	function getGameTournaments($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getGameTournaments($fields,$condition);	
	}
	function getGamePlayers($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getGamePlayers($fields,$condition);	
	}
	function getGameWinnerDetail($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getGameWinnerDetail($fields,$condition);	
	}
	function getTournamentReportList($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getTournamentReportList($fields,$condition);	
	}
	function getTournamentPlayers($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getTournamentPlayers($fields,$condition);	
	}
	function getTournamentWinners($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getTournamentWinners($fields,$condition);	
	}
	function getCustomPrizeDetails($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getCustomPrizeDetails($fields,$condition);	
	}
	function selectUserDetails($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->selectUserDetails($fields,$condition);	
	}
	function getTournamentElimPlayers($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getTournamentElimPlayers($fields,$condition);	
	}
	function getElimPlayerScore($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getElimPlayerScore($fields,$condition);	
	}
	function getTournamentElimWinners($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getTournamentElimWinners($fields,$condition);	
	}
	function getTournamentPlayed($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getTournamentPlayed($fields,$condition);	
	}
	function getGamePlayersCount($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getGamePlayersCount($fields,$condition);	
	}
	function getUserswinTournamentreport($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getUserswinTournamentreport($fields,$condition);	
	}
	function getBrandUsersTournamentlist($fields,$condition){
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getBrandUsersTournamentlist($fields,$condition);	
	}
	function getGameUsersTournaments($fields,$condition)
	{
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getGameUsersTournaments($fields,$condition);
	}
	function getGameWinnerTournaments($fields,$condition)
	{
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getGameWinnerTournaments($fields,$condition);
	}
	function getPurchaseList($fields,$condition)
	{
		if (!isset($this->ReportModelObj))
			$this->loadModel('ReportModel', 'ReportModelObj');
		if ($this->ReportModelObj)
			return $this->ReportModelObj->getPurchaseList($fields,$condition);
	}
}
?>