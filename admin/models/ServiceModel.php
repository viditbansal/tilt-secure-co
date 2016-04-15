<?php
class ServiceModel extends Model
{
   function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function getServiceList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_search_process']) && $_SESSION['mgc_sess_search_process'] != '')
			$condition .= " and Process LIKE '%".$_SESSION['mgc_sess_search_process']."%' ";
		if(isset($_SESSION['mgc_sess_search_module']) && $_SESSION['mgc_sess_search_module'] != '')
			$condition .= " and Module LIKE '%".$_SESSION['mgc_sess_search_module']."%' ";
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." 
						from {$this->oauthClientEndpointsTable}
				WHERE 1".$condition." group by id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function deleteServiceDetails($condition)
	{
		$sql = "DELETE FROM {$this->oauthClientEndpointsTable} WHERE ".$condition;
		$this->deleteInto($sql);
	}
	function selectServiceDetails($field,$condition)
	{
		$sql = "select ".$field." 
						from {$this->oauthClientEndpointsTable}
				WHERE 1".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectServiceParamsDetails($field,$condition)
	{
		$sql	=	"SELECT ".$field."
						FROM {$this->oauthClientEndpointsParamsTable} ocept
						RIGHT JOIN {$this->oauthClientEndpointsTable} ocet
						ON (ocet.id = ocept.fkEndpointId)
						WHERE 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertServiceDetails($values){
		$sql_order = "select max(Ordering) as max_order from {$this->oauthClientEndpointsTable}";
		$result	=	$this->sqlQueryArray($sql_order);
		$result[0]->max_order = $result[0]->max_order+1;
		$sql	 =	"insert into  {$this->oauthClientEndpointsTable}  set	Process 	  = '".trim($values['process'])."',
																			ServicePath	  = '".trim($values['service_path'])."',
																			Method		  = '".trim($values['method'])."',
																			InputParam	  = '".trim($values['input_param'])."',
																			OutputParam	  = '".trim($values['output_param'])."',
																			Module		  = '".trim($_POST['module_name'])."',
																			Ordering	  = ".$result[0]->max_order.",
																			Authorization = '".trim($_POST['authorization'])."',
																			Aspects		  = '".trim($_POST['aspects'])."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
        return $insertId;
	}
	function insertServiceParamsDetails($values) {
		$sql	=	"INSERT INTO {$this->oauthClientEndpointsParamsTable} 
						(fkEndpointId,FieldName,SampleData,Required,Explanation)
					 	VALUES ".$values;
		$this->insertInto($sql);
	}
	function insertJsonServiceParamsDetails($values) {
		$sql	 =	"INSERT INTO {$this->oauthClientEndpointsParamsTable}  set	".$values;
		$this->updateInto($sql);
	}
	function deleteServiceParamsDetails($id) {
		$sql	=	"DELETE FROM {$this->oauthClientEndpointsParamsTable}
						WHERE fkEndpointId = ".$id;
		$this->deleteInto($sql);
	}
	function updateServiceDetails($update_string,$condition){
		$sql	 =	"update {$this->oauthClientEndpointsTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	/*------APP VERSION------*/
	function getAppversionList()
	{
		$query = "SELECT * FROM appversions WHERE 1 ORDER BY id desc";
		$result = $this->sqlQueryArray($query);
		if(is_array($result) && count($result)>0 ) return $result;
		else return false;
	}
	function checkAppVersionExists($device_type,$app_type,$game_key = '') // admin, api
	{
		if($game_key == '')
			$game_key = 0;
		$query = "SELECT * FROM appversions WHERE device_type = '".$device_type."' AND app_type = '".$app_type."' and app_game_key = '".$game_key."'";
		$result = $this->sqlQueryArray($query);
		if(is_array($result) && count($result)>0 ) return $result;
		else return false;
	}
	
	function addAppversion($postArray)
	{
		if($postArray['game_key'] == '')
			$postArray['game_key'] = 0;
		$query = "INSERT INTO appversions SET	device_type		= '".trim($postArray['device_type'])."',
												app_type		= '".trim($postArray['status'])."',
												version			= '".trim($postArray['device_version'])."',
												build			= '".trim($postArray['device_build'])."',
												app_game_key	= '".trim($postArray['game_key'])."'";
		$this->insertInto($query);
	}
	
	function updateAppversion($postArray,$id)
	{
		if($postArray['game_key']  == '')
			$game_key = 0;
		$query = "UPDATE appversions SET	app_type		= '".trim($postArray['status'])."',
											version			= '".trim($postArray['device_version'])."',
											build			= '".trim($postArray['device_build'])."',
											app_game_key	= '".trim($postArray['game_key'])."'
											where id = '".$id."' ";
		$this->updateInto($query);
	}
	function deleteAppversion($id)
	{
		$query = "DELETE FROM appversions where id = '".$id."' ";
		$this->deleteInto($query);
	}
}

?>