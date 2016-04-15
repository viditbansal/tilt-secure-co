<?php
class LogController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getTotalRecordCount();
	}
	function logtrackDetails($where)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->logtrackDetails($where);
	}
	function tournamentList($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->tournamentList($fields,$condition);
	}
	function getTournamentList($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getTournamentList($fields,$condition);
	}
	
	function tournamentPlayers($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->tournamentPlayers($fields,$condition);
	}
	function playersTurnsDetails($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->playersTurnsDetails($fields,$condition);
	}
	function usersCount($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->usersCount($fields,$condition);
	}
	function brandsCount($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->brandsCount($fields,$condition);
	}
	function tournamentsCount($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->tournamentsCount($fields,$condition);
	}
	function gamesCount($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->gamesCount($fields,$condition);
	}
	function activeTournamentsCount($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->activeTournamentsCount($fields,$condition);
	}
	function getUserList($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getUserList($fields,$condition);
	}
	function redeemList($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->redeemList($fields,$condition);
	}
	function getTotalCoins($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getTotalCoins($fields,$condition);
	}
	function getPurchasedCoinsList($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getPurchasedCoinsList($fields,$condition);
	}
	function getTotalPurchaseAmount($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getTotalPurchaseAmount($fields,$condition);
	}
	function getTotalCommissionAmount($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getTotalCommissionAmount($fields,$condition);
	}
	function getCommissionList($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getCommissionList($fields,$condition);
	}
	function cronTrackDetails($where)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->cronTrackDetails($where);
	}
	function logUsersDetails($fields,$logUserTokens)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->logUsersDetails($fields,$logUserTokens);
	}
	function selectUserDetails($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->selectUserDetails($fields,$condition);
	}
	function tournamentWinList($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->tournamentWinList($fields,$condition);
	}
	function tournamentLeaderBoard($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->tournamentLeaderBoard($fields,$condition);
	}
	function finishedTournamentList($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->finishedTournamentList($fields,$condition);
	}
	function tournamentPrizeDetails($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->tournamentPrizeDetails($fields,$condition);
	}
	function iapTrackDetails($where)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->iapTrackDetails($where);
	}
	function mediaTrackDetails($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->mediaTrackDetails($fields,$condition);
	}
	function selectPlayWinLossEntry($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->selectPlayWinLossEntry($fields,$condition);
	}	
	function getGameVirtualCoinReport($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getGameVirtualCoinReport($fields,$condition);
	}
	function getGamePlayers($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getGamePlayers($fields,$condition);
	}
	function getGameWinners($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getGameWinners($fields,$condition);
	}
	function getTournamentReportList($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getTournamentReportList($fields,$condition);
	}
	function selectUserDetail($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->selectUserDetail($fields,$condition);
	}
	function selectDeveloperDetails($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->selectDeveloperDetails($fields,$condition);
	}
	function selectBrandDetails($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->selectBrandDetails($fields,$condition);
	}
	function getMediaImpression($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getMediaImpression($fields,$condition);
	}
	function logtrackCount($where)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->logtrackCount($where);
	}
	function getPlayersDetail($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getPlayersDetail($fields,$condition);
	}
	function developerCount($condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->developerCount($condition);
	}
	function getEliminationPlayedEntry($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getEliminationPlayedEntry($fields,$condition);
	}
	function getEliminationPlayers($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getEliminationPlayers($fields,$condition);
	}
	function getElimPlayersDetail($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getElimPlayersDetail($fields,$condition);
	}
	function getGameUserAndWinnerCount($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getGameUserAndWinnerCount($fields,$condition);
	}
	function getHighScorePlayerTournament($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getHighScorePlayerTournament($fields,$condition);
	}
	function getElimPlayerTournament($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getElimPlayerTournament($fields,$condition);
	}
	function getWinnersPlayedTournament($fields,$condition)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getWinnersPlayedTournament($fields,$condition);
	}
}
?>