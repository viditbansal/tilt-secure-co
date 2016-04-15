<?php
class BrandController extends Controller
{
	function checkUserExist($fields,$condition)
	{
		if (!isset($this->BrandModelObj))
			$this->loadModel('BrandModel', 'BrandModelObj');
		if ($this->BrandModelObj)
			return $this->BrandModelObj->checkUserExist($fields,$condition);
	}
	function updateBrandDetail($string,$condition)
	{
		if (!isset($this->BrandModelObj))
			$this->loadModel('BrandModel', 'BrandModelObj');
		if ($this->BrandModelObj)
			return $this->BrandModelObj->updateBrandDetail($string,$condition);
	}
	function getBrandDetails($fields,$condition)
	{
		if (!isset($this->BrandModelObj))
			$this->loadModel('BrandModel', 'BrandModelObj');
		if ($this->BrandModelObj)
			return $this->BrandModelObj->getBrandDetails($fields,$condition);
	}
	function getTotalRecordCount()
	{
		if (!isset($this->BrandModelObj))
			$this->loadModel('BrandModel', 'BrandModelObj');
		if ($this->BrandModelObj)
			return $this->BrandModelObj->getTotalRecordCount();
	}
	function SingleBrandDetails($where)
	{
		if (!isset($this->BrandModelObj))
			$this->loadModel('BrandModel', 'BrandModelObj');
		if ($this->BrandModelObj)
			return $this->BrandModelObj->SingleBrandDetails($where);
	}
	function getSingleBrand($fields,$condition)
	{
		if (!isset($this->BrandModelObj))
			$this->loadModel('BrandModel', 'BrandModelObj');
		if ($this->BrandModelObj)
			return $this->BrandModelObj->getSingleBrand($fields,$condition);
	}
	function getBrandBalance($fields,$condition)
	{
		if (!isset($this->BrandModelObj))
			$this->loadModel('BrandModel', 'BrandModelObj');
		if ($this->BrandModelObj)
			return $this->BrandModelObj->getBrandBalance($fields,$condition);
	}
	function getBrandCommission($fields,$condition)
	{
		if (!isset($this->BrandModelObj))
			$this->loadModel('BrandModel', 'BrandModelObj');
		if ($this->BrandModelObj)
			return $this->BrandModelObj->getBrandCommission($fields,$condition);
	}
	
}
?>