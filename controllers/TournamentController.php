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
	function insertTournamentDetails($post_values)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertTournamentDetails($post_values);
	}
	function updateTournamentDetail($updateString,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateTournamentDetail($updateString,$condition);
	}
	function selectTournament($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->selectTournament($fields,$condition);
	}
	function getTournamentList($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentList($fields,$condition);
	}
	function insertLocRestrictDetails($post_values)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertLocRestrictDetails($post_values);
	}
	function updateLocRestrictDetails($updateString,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateLocRestrictDetails($updateString,$condition);
	}
	function selectTournamentDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->selectTournamentDetails($fields,$condition);
	}
	function selectCountryDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->selectCountryDetails($fields,$condition);
	}
	function insertCouponBannerLink($queryString)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertCouponBannerLink($queryString);
	}
	function insertPinCode($values)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertPinCode($values);
	}
	function updateCouponBannerLink($updateString,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateCouponBannerLink($updateString,$condition);
	}
	function checkCouponBannerLink($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->checkCouponBannerLink($fields,$condition);
	}
	function checkPinCode($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->checkPinCode($fields,$condition);
	}
	function updatePinCode($updateString,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updatePinCode($updateString,$condition);
	}
	function insertCustomPrize($values)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertCustomPrize($values);
	}
	function updateCustomPrizeDetails($updateString,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateCustomPrizeDetails($updateString,$condition);
	}
	function updateCustomAdDetails($sql)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateCustomAdDetails($sql);
	}
	function getCustomPrizeDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getCustomPrizeDetails($fields,$condition);
	}
	function getCustomAdDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getCustomAdDetails($fields,$condition);
	}
	function getMgcBackgroundImageDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getMgcBackgroundImageDetails($fields,$condition);
	}
	function deleteTournaments($delete_id)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->deleteTournaments($delete_id);
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
	function updateTournamentDetails($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateTournamentDetails($fields,$condition);
	}
	function checkLocationRestriction($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->checkLocationRestriction($fields,$condition);
	}
	function getTournamentPlayed($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentPlayed($fields,$condition);
	}
	function getEliminationPlayed($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getEliminationPlayed($fields,$condition);
	}
	function getElimPlayersCount($fields,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getElimPlayersCount($fields,$condition);
	}
	function insertRestrictedLocation($fields)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->insertRestrictedLocation($fields);
	}
	function getRestrictedLocation($fields)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getRestrictedLocation($fields);
	}
	function deleteRestrictedLocation($con)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->deleteRestrictedLocation($con);
	}
	function updateRestrictedLocation($fields,$con)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->updateRestrictedLocation($fields,$con);
	}
	function getPinCode($fields, $condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getPinCode($fields, $condition);
	}
	function selectPinCode($field,$condition)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->selectPinCode($field,$condition);
	}
	function getTournamentPin($length)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->getTournamentPin($length);
	}
	function generateTournamentPins($id)
	{
		if (!isset($this->TournamentModelObj))
			$this->loadModel('TournamentModel', 'TournamentModelObj');
		if ($this->TournamentModelObj)
			return $this->TournamentModelObj->generateTournamentPins($id);
	}
}
?>
