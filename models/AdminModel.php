<?php
class AdminModel extends Model
{
	function getAdminDetails($fields,$where)
	{
		$sql	=	"SELECT $fields FROM  {$this->adminTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);

		if (count($result) == 0) return false;
		return $result;
	}
	function getSettingDetails($fields,$where)	{
		$sql = "select $fields from settings where ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function getDistance($fields,$where)
	{
		$sql = "select $fields from {$this->settingsTable} where ".$where;
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
	function getCMS($fields,$where)
	{
		$sql = "select $fields from {$this->staticpagesTable} where ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
}
?>