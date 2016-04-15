<?php
class AdminController extends Controller
{
	function getAdminDetails($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getAdminDetails($fields,$where);
	}
	function getSettingDetails($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getSettingDetails($fields,$where);
	}
	function getDistance($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getDistance($fields,$where);
	}
	function getSdkDetail($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getSdkDetail($fields,$where);
	}
	function getCMS($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getCMS($fields,$where);
	}
}
?>