<?php
class UserController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getTotalRecordCount();
	}
	function getUserList($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getUserList($fields,$condition);
	}
	function updateUserDetails($update_string,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->updateUserDetails($update_string,$condition);
	}
	function getUserDetails($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getUserDetails($fields,$condition);
	}
	function selectUserDetails($field,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->selectUserDetails($field,$condition);
	}
	function selectWordDetails()
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->selectWordDetails();
	}
	function insertUserDetails($register_values)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->insertUserDetails($register_values);
	}
	function selectContactDetails($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->selectContactDetails($fields,$condition);
	}
	function getUserHashDetails($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getUserHashDetails($fields,$condition);
	}
	function deleteUserReleatedEntries($userId)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->deleteUserReleatedEntries($userId);
	}
	function getActivityDetails($fields, $condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getActivityDetails($fields, $condition);
	}
	function getActivity($userId)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getActivity($userId);
	}
	function changeUsersStatus($userIds,$updateStatus)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->changeUsersStatus($userIds,$updateStatus);
	}
	function getCoinsCount($field,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getCoinsCount($field,$condition);
	}
	function getPurchaseList($field,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getPurchaseList($field,$condition);
	}
	function insertVirtualCoins($coins,$id) 
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->insertVirtualCoins($coins,$id);
	}
	function turnsUserList($field,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->turnsUserList($field,$condition);
	}
	function getPaymentHistory($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getPaymentHistory($fields,$condition);
	}
	function paymentHistoryList($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->paymentHistoryList($fields,$condition);
	}
	function roundsUserList($field,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->roundsUserList($field,$condition);
	}
	function turnLastDate($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->turnLastDate($fields,$condition);
	}
	function roundLastDate($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->roundLastDate($fields,$condition);
	}
	function getUserListDetail($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getUserListDetail($fields,$condition);
	}
	function userTurnsAndRoundsList($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->userTurnsAndRoundsList($fields,$condition);
	}
}
?>