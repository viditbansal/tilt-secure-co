<?php
class CoinsModel extends Model
{
	function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function updateUserDetails($update_string,$condition){
		$sql	 =	"update {$this->userTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function updateGameDeveloperDetails($update_string,$condition){
		$sql	 =	"update {$this->gameDeveTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function selectUserDetails($field,$condition){
		$sql	 =	"select ".$field." from {$this->userTable} where ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function selectGameDeveloperDetails($field,$condition){
		$sql	 =	"select ".$field." from {$this->gameDeveTable} where ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function insertVirtualCoins($coins,$id) 
	{
		$time 	 =  date('Y-m-d  H:i:s');
		$sql	 =	"INSERT INTO virtualcoins values('','".$id."','".$coins."','1','".$time."')";
		$this->updateInto($sql);
	}
	function insertTiltDollarCoin($postValues) 
	{		
		
		$sql	 =	"insert into  tiltdollardeveloper set ";
		if(isset($postValues['userId'])	&&	trim($postValues['userId']!=""))
			$sql	.=	" fkGameDevelopersId 			= 	'".$postValues['userId']."',";
		if(isset($postValues['coin'])	&&	trim($postValues['coin']!=""))			
			$sql	.=  " TiltDollar			=	'".$postValues['coin']."',";
		if(isset($postValues['remove'])	&&	trim($postValues['remove']==1))
			$sql 	.=	"	Status 			= 	2,";
		else
			$sql 	.=	"	Status 			= 	1,";

		$sql 	.=	"	DateCreated 		= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function insertVirtualCoin($postValues) 
	{
		$sql	 =	"insert into  {$this->virtCoinsTable}  set ";
		if(isset($postValues['devBrandId'])	&&	trim($postValues['devBrandId']!=""))
			$sql	.=	" fkDevelopersId 			= 	'".$postValues['devBrandId']."',";
		if(isset($postValues['brandId'])	&&	trim($postValues['brandId']!=""))
			$sql	.=	" fkBrandsId 			= 	'".$postValues['brandId']."',";
		if(isset($postValues['userId'])	&&	trim($postValues['userId']!=""))
			$sql	.=	" fkUsersId 			= 	'".$postValues['userId']."',";
		if(isset($postValues['coin'])	&&	trim($postValues['coin']!=""))			
			$sql	.=  " VirtCoins			=	'".$postValues['coin']."',";
		if(isset($postValues['remove'])	&&	trim($postValues['remove']==1))
			$sql 	.=	"	Status 			= 	2,";
		else
			$sql 	.=	"	Status 			= 	1,";

		$sql 	.=	"	DateCreated 		= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function insertTiltCoin($postValues) 
	{
		$sql	 =	"insert into  {$this->tiltCoinsTable}  set ";
		if(isset($postValues['userId'])	&&	trim($postValues['userId']!=""))
			$sql	.=	" fkUsersId 			= 	'".$postValues['userId']."',";
		if(isset($postValues['coin'])	&&	trim($postValues['coin']!=""))			
			$sql	.=  " TiltCoins			=	'".$postValues['coin']."',";
		if(isset($postValues['remove'])	&&	trim($postValues['remove']==1))
			$sql 	.=	"	Status 			= 	2,";
		else
			$sql 	.=	"	Status 			= 	1,";
			
		$sql 	.=	"	DateCreated 		= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function getVirtCoinList($fields,$condition)
	{				 
		$limit_clause = '';
		$sorting_clause = ' vc.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['mgc_sess_user_name']) && $_SESSION['mgc_sess_user_name'] != '')
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_user_name']."%' OR	u.LastName LIKE '%".$_SESSION['mgc_sess_user_name']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_user_name']."%')";
		if(isset($_SESSION['mgc_sess_user_registerdate']) && $_SESSION['mgc_sess_user_registerdate'] != '')
			$condition .= " and date(vc.DateCreated) = '".$_SESSION['mgc_sess_user_registerdate']."'";			
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		$sql = " select SQL_CALC_FOUND_ROWS ".$fields." from {$this->virtCoinsTable} as vc 
				LEFT JOIN  {$this->userTable} as u ON (u.id = vc.fkUsersId )
				WHERE 1 ".$condition."  
				ORDER BY ".$sorting_clause." ".$limit_clause;		
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTiltCoinsList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' tc.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
			
		if(isset($_SESSION['mgc_sess_user_name']) && $_SESSION['mgc_sess_user_name'] != '')
		$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_user_name']."%' OR	u.LastName LIKE '%".$_SESSION['mgc_sess_user_name']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_user_name']."%')";
		
		if(isset($_SESSION['mgc_coin_assigned_date']) && $_SESSION['mgc_coin_assigned_date'] != '')
			$condition .= " and date(tc.DateCreated) = '".$_SESSION['mgc_coin_assigned_date']."'";	

		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." 
				from {$this->tiltCoinsTable} as tc
				LEFT JOIN {$this->userTable} as u ON (u.id=tc.fkUsersId)
				WHERE ".$condition." 
				ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getCountryList($fields,$condition)	{
		$sql	=	"SELECT ".$fields.' from countries WHERE '.$condition.' order by Country ';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getStateList($fields,$condition)	{
		$sql	=	"SELECT ".$fields.' from states WHERE '.$condition.' ORDER BY State ';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertRestrictedCountries($values){
		$sql	 =	" INSERT INTO {$this->locRestrictTable} ( fkCountriesId,fkStatesId,Status,DateCreated,DateModified) ".$values;
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function updateCountryStatus($updateString,$condition){
		$sql	 =	"update {$this->locRestrictTable}  set ".$updateString." where ".$condition;
			$this->updateInto($sql);
	}
	function checkCountryEntry($fields,$condition)	{
		$sql	=	"SELECT ".$fields." from {$this->locRestrictTable} WHERE ".$condition.' ';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getRestCountries($fields,$condition)	{
		$sql	=	" SELECT ".$fields." from {$this->locRestrictTable}  as rc
					LEFT JOIN {$this->countriesTable} as c ON (c.id=rc.fkCountriesId)
					LEFT JOIN {$this->statesTable} as s ON (rc.fkStatesId=s.id AND c.id=s.fkCountriesId)
					WHERE ".$condition.' ';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function updateTiltCountryStatus($updateString,$condition){
		$sql	 =	"update {$this->tiltlocationrestrictionTable}  set ".$updateString." where ".$condition;
			$this->updateInto($sql);
	}
	function getTiltRestCountries($fields,$condition)	{
		$sql	=	" SELECT ".$fields." from {$this->tiltlocationrestrictionTable}  as rc
					LEFT JOIN {$this->countriesTable} as c ON (c.id=rc.fkCountriesId)
					LEFT JOIN {$this->statesTable} as s ON (rc.fkStatesId=s.id AND c.id=s.fkCountriesId)
					WHERE ".$condition.' ';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function checkTiltCountryEntry($fields,$condition)	{
		$sql	=	"SELECT ".$fields." from {$this->tiltlocationrestrictionTable} WHERE ".$condition.' ';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function inserTtiltRestrictedCountries($values){
		$sql	 =	" INSERT INTO {$this->tiltlocationrestrictionTable} ( fkCountriesId,fkStatesId,Status,DateCreated,DateModified) ".$values;
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function getTournamentDetails($fields,$condition)
	{
		$limit_clause= '';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_r_tournament_name']) && $_SESSION['mgc_sess_r_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_r_tournament_name']."%'";
		if(isset($_SESSION['mgc_sess_r_prize']) && $_SESSION['mgc_sess_r_prize'] != '')
			$condition .= " and t.Prize >= '".$_SESSION['mgc_sess_r_prize']."'";	
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." 
				from tournaments as t
				LEFT JOIN games AS g ON ( t.fkGamesId = g.id )
				left join tournamentsstats as ts  on (t.id = ts.fkTournamentsId)				
				WHERE 1 ".$condition."  group by t.id,t.fkUsersId
				ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getPlayersDetails($fields,$condition)
	{
		$limit_clause= '';
		$sorting_clause = 'tp.id desc';
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		$sql = "select SQL_CALC_FOUND_ROWS distinct".$fields." 
				from tournamentsplayed as tp 
				left join users as u on (u.id = tp.fkUsersId)
				WHERE 1  ".$condition." 
				group by u.id
				ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function winnersList($fields,$condition)
	{
		$sorting_clause = 'ts.id desc';
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." 
				from tournamentsstats as ts
				left join tournaments as t on (t.id = ts.fkTournamentsId)
				WHERE 1 ".$condition." 
				group by t.id
				ORDER BY ".$sorting_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getWinnersDetails($fields,$condition)
	{
		$sorting_clause = 'ts.Prize desc';
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." 
				from users as u 
				left join tournamentsstats as ts on (u.id = ts.fkUsersId) 
				left join tournaments as t on (t.id = ts.fkTournamentsId)
				WHERE 1 ".$condition." 
				group by u.id
				ORDER BY ".$sorting_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertPaymentHistroy($postValues) 
	{
		$sql	 =	"insert into  {$this->payHistTable} set ";
		$purchsedBy = " PurchasedBy 		= 	'0',";
		$devBrandId	=	" BrandDeveloperId 	= 	'0',";
		if(isset($postValues['brandId']))
			$devBrandId	=	" BrandDeveloperId 	= 	'".$postValues['brandId']."',";
		if(isset($postValues['devBrandId'])){
			$purchsedBy = " PurchasedBy 		= 	'2',";
			$devBrandId	=	" BrandDeveloperId 	= 	'".$postValues['devBrandId']."',";
		}
		$sql	.=	$purchsedBy.$devBrandId."
					fkUsersId 			= 	'".$postValues['userId']."',
					Type 				= 	'".$postValues['type']."',
					Coins				=	'".$postValues['coin']."',
					CoinType 			= 	'".$postValues['ctype']."',
					DateCreated 		= 	'".date('Y-m-d H:i:s')."'";
															
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function selectBrandDetails($field,$condition){
		$sql	 =	"select ".$field." from {$this->brandTable} where ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function updateBrandDetails($update_string,$condition){
		$sql	 =	"update {$this->brandTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function getBrandVirtCoinList($fields,$condition)
	{				 
		$limit_clause = '';
		$sorting_clause = ' vc.id desc';
		if(isset($_SESSION['mgc_sess_user_registerdate']) && $_SESSION['mgc_sess_user_registerdate'] != '')
			$condition .= " and date(vc.DateCreated) = '".$_SESSION['mgc_sess_user_registerdate']."'";
		if(isset($_SESSION['mgc_sess_brand_name']) && $_SESSION['mgc_sess_brand_name'] != '')
			$condition .= " and b.BrandName LIKE '%".$_SESSION['mgc_sess_brand_name']."%'";
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		$sql = " select SQL_CALC_FOUND_ROWS ".$fields." from {$this->virtCoinsTable} as vc 
				LEFT JOIN  {$this->brandTable} as b ON (b.id = vc.fkBrandsId)
				WHERE 1 ".$condition."  
				ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getEliminationPlayedEntry($fields,$condition)
	{
		 $sql	=	" SELECT ".$fields." FROM {$this->tournamentsPlayedTable} as tp LEFT JOIN {$this->eliminationPlayerTable} as ept on (tp.id = ept.fkTournamentsPlayedId) WHERE 1 ".$condition."  GROUP BY tp.fkTournamentsId";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	
	function getTournamentPlayersCount($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsPlayedTable} as tp WHERE ".$condition." GROUP BY tp.fkTournamentsId";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getDevBrandVirtCoinList($fields,$condition)
	{				 
		$limit_clause = '';
		$sorting_clause = ' vc.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_user_registerdate']) && $_SESSION['mgc_sess_user_registerdate'] != '')
			$condition .= " and date(vc.DateCreated) = '".$_SESSION['mgc_sess_user_registerdate']."'";
		if(isset($_SESSION['mgc_sess_devBrand_name']) && $_SESSION['mgc_sess_devBrand_name'] != '')
			$condition .= " and db.Company LIKE '%".$_SESSION['mgc_sess_devBrand_name']."%'";
		
		$sql = " select SQL_CALC_FOUND_ROWS ".$fields." from {$this->virtCoinsTable} as vc 
				LEFT JOIN  {$this->gameDeveTable} as db ON (db.id = vc.fkDevelopersId)
				WHERE 1 ".$condition."  
				ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	
}
?>