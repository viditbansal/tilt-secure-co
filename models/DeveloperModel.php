<?php
class DeveloperModel extends Model
{
	function checkGameDeveloperLogin($where)
	{
	 $sql	=	"SELECT * FROM {$this->gameDeveTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);

		if (count($result) == 0) return false;
		return $result;
	}
	function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function regDeveloperDetails($register_values)
	{
	 $sql	 =	"insert into  {$this->gameDeveTable}  set ";
		if(isset($register_values['company'])	&&	trim($register_values['company']!=""))
			$sql	.=	"Company 			= 	'".trim($register_values['company'])."',";
		if(isset($register_values['contact_name'])	&&	trim($register_values['contact_name']!=""))
			$sql	.=  "Name			=	'".trim($register_values['contact_name'])."',";
		if(isset($register_values['email'])	&&	trim($register_values['email']!=""))
			$sql 	.=	"Email 			= 	'".trim($register_values['email'])."',";
		if(isset($register_values['password'])	&&	trim($register_values['password']!=""))
			$sql 	.=	"Password 			= 	'".trim($register_values['password'])."',";
		if(isset($register_values['actual_password'])	&&	trim($register_values['actual_password']!=""))
			$sql 	.=	"ActualPassword 			= 	'".trim($register_values['actual_password'])."',";
		$sql 	.=	" Status 			= 	1,
					  DateCreated 		= 	'".date('Y-m-d H:i:s')."',
					  DateModified		= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
        return $insertId;
	}
	function updateGameDevDetails($update_string,$condition){
		$sql	 =	"update {$this->gameDeveTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function selectSingleDeveloper($field,$condition){
		$sql	 =	"select ".$field." from {$this->gameDeveTable} where ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function insertGamePaymentDetails($values){
		$sql	 =	" INSERT INTO {$this->gamePaymentTable} ( fkDeveloperId,CustomerId,CustomerToken,CustomerEmail,Coins,Amount,Commission,CustomerResponse,ChargeResponse,CreatedDate ) values ".$values;
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function insertPaymentHistoryDetails($inputArray)	{
		$today 	=	date('Y-m-d H:i:s');
		$query	=	" insert into {$this->paymentHistoryTable}  set ".$inputArray ;
		$this->result = $this->insertInto($query);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function getBrandFollowList($fields, $condition)	{
		$sql	=	"select ".$fields." from {$this->followTable} as f
						left join {$this->userTable} as u on (u.id = f.fkUsersId)
						WHERE ".$condition;
		$result = 	$this->sqlQueryArray($sql);
		if($result) return $result;
		else false;
	}
	function getUserDetailsForPN($fields, $condition)	{
		$sql	=	"select ".$fields." from {$this->deviceTokenTable} as d 
						WHERE d.Status = 1 ".$condition; //
		$result = 	$this->sqlQueryArray($sql);
		if($result) return $result;
		else false;
	}
	function updateBadgeToken($condition)	{
		$sql	=	"update {$this->deviceTokenTable} set Badge = Badge + 1 where Token = '".$condition."'";
		$this->updateInto($sql);
		return true;
	}
}
?>