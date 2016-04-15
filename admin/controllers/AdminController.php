<?php
class AdminController extends Controller
{
    function checkAdminLogin($where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->checkAdminLogin($where);
	}
	function getAdminDetails($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getAdminDetails($fields,$where);
	}
	function updateAdminDetails($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateAdminDetails($fields,$where);
	}	
	function getCMS($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getCMS($fields,$where);
	}	
	function updateCMSDetails($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateCMSDetails($update_string,$where);
	}
	function getDistance($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getDistance($fields,$where);
	}
	function insertDistance($val)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->insertDistance($val);
	}
	function updateDistanceDetails($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateDistanceDetails($update_string,$where);
	}
	function getCommission($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getCommission($fields,$where);
	}
	function insertCommission($val)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->insertCommission($val);
	}
	function updateCommissionDetails($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateCommissionDetails($update_string,$where);
	}
	function updateAboutUs($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateAboutUs($update_string,$where);
	}
	function getAboutUs($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getAboutUs($fields,$where);
	}
	function updateWebsiteHome($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateWebsiteHome($update_string,$where);
	}
	function getWebsiteHome($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getWebsiteHome($fields,$where);
	}
	function updateWebsiteDeveloper($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateWebsiteDeveloper($update_string,$where);
	}
	function getWebsiteDeveloper($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getWebsiteDeveloper($fields,$where);
	}
	function updateWebsiteMedia($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateWebsiteMedia($update_string,$where);
	}
	function getWebsiteMedia($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getWebsiteMedia($fields,$where);
	}
	function updateWebsiteTermofUse($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateWebsiteTermofUse($update_string,$where);
	}
	function deleteWebsiteTermofUse($ids)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->deleteWebsiteTermofUse($ids);
	}
	function addWebsiteTermofUse($ids)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->addWebsiteTermofUse($ids);
	}
	function getWebsiteTermofUse($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getWebsiteTermofUse($fields,$where);
	}
	function updateWebsitePrivacyPolicy($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateWebsitePrivacyPolicy($update_string,$where);
	}
	function deleteWebsitePrivacyPolicy($ids)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->deleteWebsitePrivacyPolicy($ids);
	}
	function addWebsitePrivacyPolicy($ids)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->addWebsitePrivacyPolicy($ids);
	}
	function getWebsitePrivacyPolicy($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getWebsitePrivacyPolicy($fields,$where);
	}
	function getSdkDetail($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getSdkDetail($fields,$where);
	}
	function updateSdkDetail($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateSdkDetail($update_string,$where);
	}
	function updateDefaultImage($update_string,$condition)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateDefaultImage($update_string,$condition);
	}
	function insertDefaultImage($values)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->insertDefaultImage($values);
	}
	function selectDefaultImage($fields, $cond)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->selectDefaultImage($fields, $cond);
	}
}
?>