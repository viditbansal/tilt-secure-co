<?php
class DeveloperController extends Controller
{
	function checkGameDeveloperLogin($where)
	{
		if (!isset($this->DeveloperModelObj))
			$this->loadModel('DeveloperModel', 'DeveloperModelObj');
		if ($this->DeveloperModelObj)
			return $this->DeveloperModelObj->checkGameDeveloperLogin($where);
	}
	function regDeveloperDetails($post_values)
	{
		if (!isset($this->DeveloperModelObj))
			$this->loadModel('DeveloperModel', 'DeveloperModelObj');
		if ($this->DeveloperModelObj)
			return $this->DeveloperModelObj->regDeveloperDetails($post_values);
	}
	function getTotalRecordCount()
	{
		if (!isset($this->DeveloperModelObj))
			$this->loadModel('DeveloperModel', 'DeveloperModelObj');
		if ($this->DeveloperModelObj)
			return $this->DeveloperModelObj->getTotalRecordCount();
	}
	function updateGameDevDetails($update_string,$condition)
	{
		if (!isset($this->DeveloperModelObj))
			$this->loadModel('DeveloperModel', 'DeveloperModelObj');
		if ($this->DeveloperModelObj)
			return $this->DeveloperModelObj->updateGameDevDetails($update_string,$condition);
	}
	function selectSingleDeveloper($field,$condition)
	{
		if (!isset($this->DeveloperModelObj))
			$this->loadModel('DeveloperModel', 'DeveloperModelObj');
		if ($this->DeveloperModelObj)
			return $this->DeveloperModelObj->selectSingleDeveloper($field,$condition);
	}
	function insertGamePaymentDetails($values)
	{
		if (!isset($this->DeveloperModelObj))
			$this->loadModel('DeveloperModel', 'DeveloperModelObj');
		if ($this->DeveloperModelObj)
			return $this->DeveloperModelObj->insertGamePaymentDetails($values);
	}
	function insertPaymentHistoryDetails($inputArray)
	{
		if (!isset($this->DeveloperModelObj))
			$this->loadModel('DeveloperModel', 'DeveloperModelObj');
		if ($this->DeveloperModelObj)
			return $this->DeveloperModelObj->insertPaymentHistoryDetails($inputArray);
	}
	function getBrandFollowList($fields, $condition)
	{
		if (!isset($this->DeveloperModelObj))
			$this->loadModel('DeveloperModel', 'DeveloperModelObj');
		if ($this->DeveloperModelObj)
			return $this->DeveloperModelObj->getBrandFollowList($fields, $condition);
	}
	function getUserDetailsForPN($fields, $condition)
	{
		if (!isset($this->DeveloperModelObj))
			$this->loadModel('DeveloperModel', 'DeveloperModelObj');
		if ($this->DeveloperModelObj)
			return $this->DeveloperModelObj->getUserDetailsForPN($fields, $condition);
	}
	function updateBadgeToken($condition)
	{
		if (!isset($this->DeveloperModelObj))
			$this->loadModel('DeveloperModel', 'DeveloperModelObj');
		if ($this->DeveloperModelObj)
			return $this->DeveloperModelObj->updateBadgeToken($condition);
	}
	
}
?>