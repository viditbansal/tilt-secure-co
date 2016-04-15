<?php
class AdminModel extends Model
{
    function checkAdminLogin($where)
	{
	 $sql	=	"SELECT * FROM {$this->adminTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);

		if (count($result) == 0) return false;
		return $result;
	}
	function getAdminDetails($fields,$where)
	{
		$sql	=	"SELECT $fields FROM  {$this->adminTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);

		if (count($result) == 0) return false;
		return $result;
	}
	function updateAdminDetails($fields,$where)
	{
		$sql = "UPDATE {$this->adminTable} SET $fields where ".$where;
		$this->updateInto($sql);
	}	
	function getCMS($fields,$where)
	{
		$sql = "select $fields from {$this->staticpagesTable} where ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function updateCMSDetails($update_string,$where)
	{
		$sql = "UPDATE {$this->staticpagesTable} SET $update_string where ".$where;
		$this->updateInto($sql);
	}
	function getDistance($fields,$where)
	{
		$sql = "select $fields from {$this->settingsTable} where ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function insertDistance($val)
	{
		$sql = "insert into {$this->settingsTable}(Distance,Status,DateCreated) values('".$val."','1','".date('Y-m-d H:i:s')."')";
		$result = 	$this->sqlQueryArray($sql);
		$_POST = array();
		if (count($result) == 0) return false;
		return $result;
	}
	function updateDistanceDetails($update_string,$where)
	{
		$sql = "UPDATE {$this->settingsTable} SET $update_string ,DateModified='".date('Y-m-d H:i:s')."' where ".$where;
		$this->updateInto($sql);
	}
	function getCommission($fields,$where)
	{
		$sql = "select $fields from {$this->settingsTable} where ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function insertCommission($val)
	{
		$sql = "insert into {$this->settingsTable}(Commission,Status,DateCreated) values('".$val."','2','".date('Y-m-d H:i:s')."')";
		$result = 	$this->sqlQueryArray($sql);
		$_POST = array();
		if (count($result) == 0) return false;
		return $result;
	}
	function updateCommissionDetails($update_string,$where)
	{
		$sql = "UPDATE {$this->settingsTable} SET $update_string ,DateModified='".date('Y-m-d H:i:s')."' where ".$where;
		$this->updateInto($sql);
	}
	
	function updateAboutUs($update_string,$where)
	{
		$sql = "UPDATE {$this->aboutusTable} SET $update_string  where ".$where;
		$this->updateInto($sql);
	}
	function getAboutUs($fields,$where)
	{
		$sql	=	"SELECT ".$fields." FROM  {$this->aboutusTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function updateWebsiteHome($update_string,$where)
	{
		$sql = "UPDATE {$this->websiteHomeTable} SET $update_string  where ".$where;
		$this->updateInto($sql);
	}
	function getWebsiteHome($fields,$where)
	{
		$sql	=	"SELECT ".$fields." FROM  {$this->websiteHomeTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function updateWebsiteDeveloper($update_string,$where)
	{
		$sql = "UPDATE {$this->websiteDeveloperTable} SET $update_string  where ".$where;
		$this->updateInto($sql);
	}
	function getWebsiteDeveloper($fields,$where)
	{
		$sql	=	"SELECT $fields FROM  {$this->websiteDeveloperTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function updateWebsiteMedia($update_string,$where)
	{
		$sql = "UPDATE {$this->websiteMediaTable} SET $update_string  where ".$where;
		$this->updateInto($sql);
	}
	function getWebsiteMedia($fields,$where)
	{
		$sql	=	"SELECT $fields FROM  {$this->websiteMediaTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function updateWebsiteTermofUse($update_string,$where)
	{
		$sql = "UPDATE {$this->websiteTermofUseTable} SET $update_string  where ".$where;
		$this->updateInto($sql);
	}
	function deleteWebsiteTermofUse($where)
	{
		$sql = "UPDATE {$this->websiteTermofUseTable} SET Status = 2  where ".$where;
		$this->updateInto($sql);
	}
	function addWebsiteTermofUse($values)
	{
		$sql	=	"insert into {$this->websiteTermofUseTable} (`id`, `Title`, `Content`, `Status`, `DateCreated`, `DateModified`) VALUE ".$values;
		$this->result = $this->insertInto($sql);
	}
	function getWebsiteTermofUse($fields,$where)
	{
		$sql	=	"SELECT $fields FROM  {$this->websiteTermofUseTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function updateWebsitePrivacyPolicy($update_string,$where)
	{
		$sql = "UPDATE {$this->websitePrivacyPolicyTable} SET $update_string  where ".$where;
		$this->updateInto($sql);
	}
	function deleteWebsitePrivacyPolicy($where)
	{
		$sql = "UPDATE {$this->websitePrivacyPolicyTable} SET Status = 2  where ".$where;
		$this->updateInto($sql);
	}
	function addWebsitePrivacyPolicy($values)
	{
		$sql	=	"insert into {$this->websitePrivacyPolicyTable} (`id`, `Title`, `Content`, `Status`, `DateCreated`, `DateModified`) VALUE ".$values;
		$this->result = $this->insertInto($sql);
	}
	function getWebsitePrivacyPolicy($fields,$where)
	{
		$sql	=	"SELECT $fields FROM  {$this->websitePrivacyPolicyTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function getSdkDetail($fields,$where)
	{
		$sql	=	"SELECT $fields FROM  {$this->sdkTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function updateSdkDetail($update_string,$where)
	{
		$sql = "UPDATE {$this->sdkTable} SET $update_string  where ".$where;
		$this->updateInto($sql);
	}
	function selectDefaultImage($fields, $cond)
	{
		$sql	=	"SELECT ".$fields." FROM {$this->userDefaultImageTable} WHERE ".$cond;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function insertDefaultImage($values) {
		$sql	=	"INSERT INTO {$this->userDefaultImageTable} SET Status = 1, DateCreated= '".$values."', DateModified= '".$values."'";
		$this->insertInto($sql);
		$insertId = $this->sqlInsertId();
        return $insertId;
	}
	function updateDefaultImage($update_string,$condition) {
		$sql	 =	"update {$this->userDefaultImageTable}  set ".$update_string." WHERE ".$condition;
		$this->updateInto($sql);
	}
}
?>