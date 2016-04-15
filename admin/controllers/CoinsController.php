<?php
class CoinsController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getTotalRecordCount();
	}
	function updateUserDetails($update_string,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->updateUserDetails($update_string,$condition);
	}
	//updateGameDeveloperDetails
	function updateGameDeveloperDetails($update_string,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->updateGameDeveloperDetails($update_string,$condition);
	}
	function selectUserDetails($field,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->selectUserDetails($field,$condition);
	}
	function selectBrandDetails($field,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->selectBrandDetails($field,$condition);
	}
	function selectGameDeveloperDetails($field,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->selectGameDeveloperDetails($field,$condition);
	}
	function getCoinsCount($field,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getCoinsCount($field,$condition);
	}
	function insertTiltCoin($postValues) 
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->insertTiltCoin($postValues);
	}
	function getVirtCoinList($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getVirtCoinList($fields,$condition);
	}
	function getTiltCoinsList($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getTiltCoinsList($fields,$condition);
	}
	function insertVirtualCoin($postValues) 
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->insertVirtualCoin($postValues);
	}
	function insertTiltDollarCoin($postValues) 
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->insertTiltDollarCoin($postValues);
	}
	function getCountryList($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getCountryList($fields,$condition);
	}
	function getStateList($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getStateList($fields,$condition);
	}
	function insertRestrictedCountries($value)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->insertRestrictedCountries($value);
	}
	function updateCountryStatus($updateString,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->updateCountryStatus($updateString,$condition);
	}
	function checkCountryEntry($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->checkCountryEntry($fields,$condition);
	}
	function getRestCountries($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getRestCountries($fields,$condition);
	}
	function updateTiltCountryStatus($updateString,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->updateTiltCountryStatus($updateString,$condition);
	}
	function getTiltRestCountries($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getTiltRestCountries($fields,$condition);
	}
	function checkTiltCountryEntry($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->checkTiltCountryEntry($fields,$condition);
	}
	function insertTiltRestrictedCountries($value)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->insertTiltRestrictedCountries($value);
	}
	function getTournamentDetails($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getTournamentDetails($fields,$condition);
	}
	function getPlayersDetails($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getPlayersDetails($fields,$condition);
	}
	function winnersList($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->winnersList($fields,$condition);
	}
	function getWinnersDetails($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getWinnersDetails($fields,$condition);
	}
	function getGameVirtualCoinReport($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getGameVirtualCoinReport($fields,$condition);
	}
	function insertPaymentHistroy($value) 
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->insertPaymentHistroy($value);
	}
	function updateBrandDetails($update_string,$condition) 
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->updateBrandDetails($update_string,$condition);
	}
	function getBrandVirtCoinList($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getBrandVirtCoinList($fields,$condition);
	}
	function getEliminationPlayedEntry($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getEliminationPlayedEntry($fields,$condition);
	}
	function getTournamentPlayersCount($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getTournamentPlayersCount($fields,$condition);
	}
	function getDevBrandVirtCoinList($fields,$condition)
	{
		if (!isset($this->CoinsModelObj))
			$this->loadModel('CoinsModel', 'CoinsModelObj');
		if ($this->CoinsModelObj)
			return $this->CoinsModelObj->getDevBrandVirtCoinList($fields,$condition);
	}
}
?>