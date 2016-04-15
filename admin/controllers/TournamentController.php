<?php
class TournamentController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTotalRecordCount();
	}
	function getTournamentList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentList($fields,$condition);
	}
	function deleteTournamentReleatedEntries($delete_id)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->deleteTournamentReleatedEntries($delete_id);
	}
	function updateUserDetails($update_string,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateUserDetails($update_string,$condition);
	}
	function selectBrandDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->selectBrandDetails($fields,$condition);
	}
	function insertTournamentDetails($postvalues)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertTournamentDetails($postvalues);
	}
	function selectGameDetails($field,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->selectGameDetails($field,$condition);
	}
	function getTournamentDetails($field,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentDetails($field,$condition);
	}

	function updateTournamentDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateTournamentDetails($fields,$condition);
	}
	function getTournamentPlayers($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentPlayers($fields,$condition);
	}
	function getTournamentChatCount($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentChatCount($fields,$condition);
	}
	function getChatList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getChatList($fields,$condition);
	}
	function tournamentList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->tournamentList($fields,$condition);
	}
	function tournamentPlayers($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->tournamentPlayers($fields,$condition);
	}
	function coinsWinList($fields,$condition,$leftjoin)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->coinsWinList($fields,$condition,$leftjoin);
	}
	function getTournamentPlayersCount($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentPlayersCount($fields,$condition);
	}
	function selectPinCode($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->selectPinCode($fields,$condition);
	}
	function checkRulesEntry($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->checkRulesEntry($fields,$condition);
	}
	function insertRules($values)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertRules($values);
	}
	function updateTournamentRules($updateString,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateTournamentRules($updateString,$condition);
	}
	function getCountryList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getCountryList($fields,$condition);
	}
	function getStateList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getStateList($fields,$condition);
	}
	function getRulesList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getRulesList($fields,$condition);
	}
	function getRulesDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getRulesDetails($fields,$condition);
	}
	function getRules($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getRules($fields,$condition);
	}
	function selectTournamentRule($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->selectTournamentRule($fields,$condition);
	}
	function selectTournament($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->selectTournament($fields,$condition);
	}
	function getTournamentUserList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentUserList($fields,$condition);
	}
	function selectTournamentList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->selectTournamentList($fields,$condition);
	}
	function getUserTournamentList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getUserTournamentList($fields,$condition);
	}
	function getgamedevloperTournamentList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getgamedevloperTournamentList($fields,$condition);
	}
	function getUserTournamentDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getUserTournamentDetails($fields,$condition);
	}
	function getGameDeveloperDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getGameDeveloperDetails($fields,$condition);
	}
	function getGameTournamentList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getGameTournamentList($fields,$condition);
	}
	function getLocationRestrict($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getLocationRestrict($fields,$condition);
	}
	function getTournamentsCoupon($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentsCoupon($fields,$condition);
	}
	function getCustomPrize($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getCustomPrize($fields,$condition);
	}
  function getCustomAd($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getCustomAd($fields,$condition);
	}
	function insertYoutubeLink($queryString)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertYoutubeLink($queryString);
	}
	function updateTournamentCouponAdLink($updateString,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateTournamentCouponAdLink($updateString,$condition);
	}
	function getTournamentPlayed($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentPlayed($fields,$condition);
	}
	function updateCustomPrizeDetails($updateString,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateCustomPrizeDetails($updateString,$condition);
	}
	function insertCustomPrize($queryString)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertCustomPrize($queryString);
	}
	function getRestrictedLocation($condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getRestrictedLocation($condition);
	}
	function updateRestrictedLocation($fields,$con)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateRestrictedLocation($fields,$con);
	}
	function insertRestrictedLocation($fields)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertRestrictedLocation($fields);
	}
	function checkLocationRestriction($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->checkLocationRestriction($fields,$condition);
	}
	function getGameDeveloper($fields, $condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getGameDeveloper($fields,$condition);
	}
	function getEliminationPlayerList($fields, $condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getEliminationPlayerList($fields,$condition);
	}
	function getEliminationPlayersCount($fields, $condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getEliminationPlayersCount($fields,$condition);
	}
	function getEliminationPoints($fields, $condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getEliminationPoints($fields,$condition);
	}
	function getEliminationPlayedEntry($fields, $condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getEliminationPlayedEntry($fields,$condition);
	}
	function getTournamentPlayedCount($fields, $condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentPlayedCount($fields,$condition);
	}
}?>
