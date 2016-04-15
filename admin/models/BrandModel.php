<?php
class BrandModel extends Model
{
    function checkUserExist($fields,$condition)	{
		$sql	=	'SELECT '.$fields." FROM {$this->brandTable} WHERE ".$condition;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	
	function updateBrandDetail($string,$condition)	{
		$sql	=	" Update {$this->brandTable} set ".$string." where ".$condition;
		$this->updateInto($sql);
	}
	function getBrandDetails($fields,$condition)	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_brand_name']) && $_SESSION['mgc_sess_brand_name'] != '')
			$condition .= " and b.BrandName LIKE '%".$_SESSION['mgc_sess_brand_name']."%' ";
		if(isset($_SESSION['mgc_sess_brand_user_name']) && $_SESSION['mgc_sess_brand_user_name'] != '')
			$condition .= " and b.UserName LIKE '%".$_SESSION['mgc_sess_brand_user_name']."%' ";
		if(isset($_SESSION['mgc_sess_brand_location']) && $_SESSION['mgc_sess_brand_location'] != '')
			$condition .= " and b.Location LIKE '%".$_SESSION['mgc_sess_brand_location']."%' ";
		if(isset($_SESSION['mgc_sess_brand_status']) && $_SESSION['mgc_sess_brand_status'] != ''){
			if($_SESSION['mgc_sess_brand_status'] == 0)
				$condition .= " and b.VerificationStatus = '".$_SESSION['mgc_sess_brand_status']."' ";
			else
				$condition .= " and b.Status = '".$_SESSION['mgc_sess_brand_status']."' and b.VerificationStatus = 1 ";
		}
		if(isset($_SESSION['mgc_sess_brand_registerdate']) && $_SESSION['mgc_sess_brand_registerdate'] != '')
			$condition .= " and date(b.DateCreated) = '".date('Y-m-d',strtotime($_SESSION['mgc_sess_brand_registerdate']))."' ";
		
		if(isset($_SESSION['mgc_sess_from_date']) && $_SESSION['mgc_sess_from_date'] != ''	&&	isset($_SESSION['mgc_sess_to_date']) && $_SESSION['mgc_sess_to_date'] != ''){
			$condition .= " AND ((date(b.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_from_date']))."' and date(b.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_to_date']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_from_date']) && $_SESSION['mgc_sess_from_date'] != '')
			$condition .= " AND date(b.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_to_date']) && $_SESSION['mgc_sess_to_date'] != '')
			$condition .= " AND date(b.DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_to_date']))."'";

		$sql	=	"SELECT SQL_CALC_FOUND_ROWS  ".$fields." FROM {$this->brandTable} AS b 
					LEFT JOIN  {$this->tournamentsTable} AS t ON(b.id = t.fkBrandsId and t.Status != 3)
					WHERE  1 ".$condition." GROUP BY b.id ORDER BY ".$sorting_clause.$limit_clause;
		$result = 	$this->sqlQueryArray($sql);
		if($result) return $result;
		else false;
	}
	function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function SingleBrandDetails($where)
	{
	 $sql	=	"SELECT * FROM {$this->brandTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function getSingleBrand($fields,$condition)	{
		$sql	=	' SELECT '.$fields.' from brands WHERE '.$condition;
		$result = 	$this->sqlQueryArray($sql);
		if($result) return $result;
		else false;
	}
	function getBrandBalance($fields,$condition)	{
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(b.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(b.DateCreated) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(b.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(b.DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
		
		if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '')
			$condition .= " and b.id = ".$_SESSION['mgc_sess_global_report_brand'];

		$sql	=	' SELECT '.$fields.' from brands AS b WHERE '.$condition;
		$result = 	$this->sqlQueryArray($sql);
		if($result) return $result;
		else false;
	}
	function getBrandCommission($fields,$condition)	{
		if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '')
			$condition .= " and fkBrandId = ".$_SESSION['mgc_sess_global_report_brand'];
			
		$sql	=	' SELECT '.$fields.' from brandpayments AS bp WHERE 1 '.$condition;
		$result = 	$this->sqlQueryArray($sql);
		if($result) return $result;
		else false;
	}
}
?>