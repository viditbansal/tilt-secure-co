<?php
class GameModel extends Model
{
   function getLeadersBoardList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' g.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		if(isset($_SESSION['mgc_sess_gameStatus']) && $_SESSION['mgc_sess_gameStatus'] != '')
			$condition .= " and g.Status = '".$_SESSION['mgc_sess_gameStatus']."' ";
		if(isset($_SESSION['mgc_sess_gameResult']) && $_SESSION['mgc_sess_gameResult'] != '')
			$condition .= " and g.GameStatus = '".$_SESSION['mgc_sess_gameResult']."' ";
		if(isset($_SESSION['mgc_sess_userName']) && $_SESSION['mgc_sess_userName'] != '')
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_user_name']."%' OR	u.LastName LIKE '%".$_SESSION['mgc_sess_user_name']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_user_name']."%')";
		if(isset($_SESSION['mgc_sess_game_startTime']) && $_SESSION['mgc_sess_game_startTime'] != ''	&&	isset($_SESSION['mgc_sess_game_endTime']) && $_SESSION['mgc_sess_game_endTime'] != ''){
			$condition .= " AND ((date(g.StartTime) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_game_startTime']))."' and date(g.EndTime) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_game_endTime']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_game_startTime']) && $_SESSION['mgc_sess_game_startTime'] != '')
			$condition .= " AND date(g.StartTime) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_game_startTime']))."'";
		else if(isset($_SESSION['mgc_sess_game_endTime']) && $_SESSION['mgc_sess_game_endTime'] != '')
			$condition .= " AND date(g.EndTime) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_game_endTime']))."'";

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->gameTable} as g 
					  LEFT JOIN {$this->userTable} as u ON (g.fkUsersId=u.id)
					  WHERE 1 ".$condition." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
   function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function getGameList($fields,$condition)	{
		$limit_clause='';
		$sorting_clause = ' g.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_game_name']) && $_SESSION['mgc_sess_game_name'] != '')
			$condition .= " and g.Name LIKE '%".$_SESSION['mgc_sess_game_name']."%' ";
		if(isset($_SESSION['mgc_sess_email']) && $_SESSION['mgc_sess_email'] != '')
			$condition .= " and g.Email LIKE '%".$_SESSION['mgc_sess_email']."%' ";
		if(isset($_SESSION['mgc_sess_user_status']) && $_SESSION['mgc_sess_user_status'] != '')
			$condition .= " and g.Status = '".$_SESSION['mgc_sess_user_status']."' ";
		if(isset($_SESSION['mgc_sess_game_tiltKey']) && $_SESSION['mgc_sess_game_tiltKey'] != '')
			$condition .= " and g.TiltKey LIKE '%".$_SESSION['mgc_sess_game_tiltKey']."%' ";
		if(isset($_SESSION['mgc_sess_game_createdBy']) && $_SESSION['mgc_sess_game_createdBy'] != ''){
			if($_SESSION['mgc_sess_game_createdBy'] == 'admin'){
				$condition .= " and g.fkDevelopersId = 0 ";
			}
			else {
				$condition .= " and gd.Company LIKE '%".$_SESSION['mgc_sess_game_createdBy']."%'";
			}
		}

		// for Statistics listing
		if(isset($_SESSION['mgc_sess_from_date']) && $_SESSION['mgc_sess_from_date'] != ''	&&	isset($_SESSION['mgc_sess_to_date']) && $_SESSION['mgc_sess_to_date'] != ''){
			$condition .= " AND ((date(g.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_from_date']))."' and date(g.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_to_date']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_from_date']) && $_SESSION['mgc_sess_from_date'] != '')
			$condition .= " AND date(g.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_to_date']) && $_SESSION['mgc_sess_to_date'] != '')
			$condition .= " AND date(g.DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_to_date']))."'";
		// end
			
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM games as g
						LEFT JOIN {$this->gameDeveTable} as gd ON(g.fkDevelopersId = gd.id AND g.fkDevelopersId !=0)
						LEFT JOIN gamespushnotification as gp ON (g.id = gp.fkGamesId AND gp.Status = 1 AND gp.Platform = 1)
						WHERE 1 ".$condition." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;		
	}
	
	function selectGameDetails($field,$condition){
		$sql	 =	'select '.$field.' from games where '.$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function insertGameDetails($input)	{
		$today	=	date('Y-m-d H:i:s');
		$sql	=	' insert into games set ';
		$tiltkey	=	$this->getTiltKey();
		if(isset($input['gamename'])	&&	$input['gamename']!= '')
			$sql	.=	' Name = 	"'.trim($input['gamename']).'",';
		if(isset($tiltkey)	&&	$tiltkey!= '')
			$sql	.=	' TiltKey = "'.trim($tiltkey).'",';
		if(isset($input['iOnOff'])){
			$sql .= ' IosStatus            	=	"'.trim($input['iOnOff']).'",';
			if($input['iOnOff'] == 1){
				if(isset($input['bundle']))
					$sql .= ' Bundle        =	"'.trim($input['bundle']).'",';
				if(isset($input['itunesurl']))
					$sql .= ' ITunesUrl 	=	"'.trim($input['itunesurl']).'",';
			}
		}
		if(isset($input['aOnOff'])){
			$sql .= ' AndroidStatus         =	"'.trim($input['aOnOff']).'",';
			if($input['aOnOff'] == 1){
				if(isset($input['androidurl']))
					$sql .= ' AndroidUrl        =	"'.trim($input['androidurl']).'",';
				if(isset($input['package']))
					$sql .= ' Package        =	"'.trim($input['package']).'",';
			}
		}
		
		if(isset($input['gamedescription'])	&&	$input['gamedescription']!= '')
			$sql	.=	' Description = "'.trim($input['gamedescription']).'",';
		
		if(isset($input['play_time'])	&&	$input['play_time']!= '')
			$sql	.=	' PlayTime = "'.trim($input['play_time']).'",';
		$sql	.=	' Status = 1 , DateCreated = "'.$today.'", DateModified = "'.$today.'" ';
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function updateGameDetails($update_string,$condition){
		$sql	 =	"update games  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function getSingleGameDetails($field,$condition){
		$sql	 =	'select '.$field.' from games where '.$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function changeGameStatus($deleteid,$updateStatus)	{
		$sql	=	'Update games set Status = '.$updateStatus.' WHERE id in ('.$deleteid.')';
		$this->updateInto($sql);
		return true;	
	}
	function getTiltKey()	{
		$tiltkey	=	getPassphrase(6);
		if($tiltkey	!= '')	{
			$sql	=	'SELECT id from games WHERE TiltKey = "'.$tiltkey.'" ';
			$result = 	$this->sqlQueryArray($sql);
			if(!empty($result))
				$this->getTiltKey();
			else	
				return $tiltkey;
		}
	}
	function getDeveloperList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' gd.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_search_dev_name']) && $_SESSION['mgc_sess_search_dev_name'] != '')
			$condition .= " and ( gd.UserName LIKE '%".$_SESSION['mgc_sess_search_dev_name']."%' || gd.Name LIKE '%".$_SESSION['mgc_sess_search_dev_name']."%') ";
		if(isset($_SESSION['mgc_sess_search_company_name']) && $_SESSION['mgc_sess_search_company_name'] != '')
			$condition .= " and gd.Company LIKE '%".$_SESSION['mgc_sess_search_company_name']."%' ";
		if(isset($_SESSION['mgc_sess_search_status']) && $_SESSION['mgc_sess_search_status'] != ''){
			if($_SESSION['mgc_sess_search_status'] == 0)
				$condition .= "  and gd.Status = 1 and gd.VerificationStatus = '".$_SESSION['mgc_sess_search_status']."' ";
			else if($_SESSION['mgc_sess_search_status'] == 1) {
				$condition .= " and gd.Status = '".$_SESSION['mgc_sess_search_status']."' and gd.VerificationStatus = 1 ";
			} else {
				$condition .= " and gd.Status = '".$_SESSION['mgc_sess_search_status']."' ";
			}
		}
		if(isset($_SESSION['mgc_sess_search_registerdate']) && $_SESSION['mgc_sess_search_registerdate'] != '')
			$condition .= " and date(gd.DateCreated) = '".date('Y-m-d',strtotime($_SESSION['mgc_sess_search_registerdate']))."' ";
			
		$sql	=	'SELECT SQL_CALC_FOUND_ROWS '.$fields.' FROM 
					 gamedevelopers as gd LEFT JOIN games as g on (g.fkDevelopersId = gd.id AND g.DevelopedBy = 1 AND g.Status =1)
					 WHERE 1 '.$condition.' GROUP BY gd.id ORDER BY '.$sorting_clause.' '.$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;		
	}
	function approveDeveloper($id,$val) {
		$sql	 =	"update gamedevelopers  set Status = '".$val."' where id in (".$id.")";
		$this->updateInto($sql);
	}
	function getGameDetails($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		$sql	=	"SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM 
					 games as g
					 LEFT JOIN {$this->tournamentsTable} as t ON(g.id=t.fkGamesId AND t.Status != 3 AND CreatedBy IN (1,3))
					 WHERE 1 ".$condition." GROUP BY g.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameArray($fields,$condition)
	{
		$sql	=	'SELECT '.$fields.' FROM 
					 games as g
					 WHERE 1 '.$condition.' ORDER BY g.id ';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getBrandArray($fields,$condition)
	{
		$sql	=	'SELECT '.$fields.' FROM 
					 brands as b
					 WHERE 1 '.$condition.' ORDER BY b.id ';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameUser($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(tp.DatePlayed) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(tp.DatePlayed) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
	
		if(isset($_SESSION['mgc_sess_global_report_game']) && $_SESSION['mgc_sess_global_report_game'] != '')
			$condition .= " and fkGamesId = ".$_SESSION['mgc_sess_global_report_game'];
			
		$sql	=	'SELECT SQL_CALC_FOUND_ROWS '.$fields.' FROM tournamentsplayed as tp
					 left join eliminationplayer as ep on (tp.id = ep.fkTournamentsPlayedId)
					 left join users as u on (u.id = tp.fkUsersId)
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getBrandUser($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_global_report_game']) && $_SESSION['mgc_sess_global_report_game'] != '')
			$condition .= " and fkGamesId = ".$_SESSION['mgc_sess_global_report_game'];	
		$sql	=	'SELECT SQL_CALC_FOUND_ROWS '.$fields.' FROM tournaments as t
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function userWinsCount($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(ts.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(ts.DateCreated) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(ts.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(ts.DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
		
		$sql	=	'SELECT SQL_CALC_FOUND_ROWS '.$fields.' FROM tournamentsstats as ts
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getUserList($fields,$condition)
	{
		$sql	=	'SELECT '.$fields.' FROM users as u
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTotalCoins($fields,$condition,$where)
	{
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(ph.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(ph.DateCreated) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(ph.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(ph.DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
		
		$sql	=	'SELECT '.$fields.' FROM paymenthistory as ph
					 WHERE 1 '.$where.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function winnerIds($fields,$condition)
	{
		$sql	=	'SELECT SQL_CALC_FOUND_ROWS '.$fields.' FROM tournamentsplayed as tp
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTurnsList($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(tp.DatePlayed) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(tp.DatePlayed) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
		if(isset($_SESSION['mgc_sess_global_report_game']) && $_SESSION['mgc_sess_global_report_game'] != '')
			$condition .= " and t.fkGamesId = ".$_SESSION['mgc_sess_global_report_game'];		
		$sql	=	'SELECT '.$fields.' FROM tournamentsplayed as tp LEFT JOIN tournaments as t ON ( t.id = tp.fkTournamentsId )
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectGameDeveloper($where)
	{
	 $sql	=	"SELECT * FROM {$this->gameDeveTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function getUserTourn($fields,$condition,$where)
	{
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(t.EndDate) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
		if(isset($_SESSION['mgc_sess_global_report_game']) && $_SESSION['mgc_sess_global_report_game'] != '')
			$condition .= " and t.fkGamesId = ".$_SESSION['mgc_sess_global_report_game'];	
		$sql	=	'SELECT '.$fields.' FROM tournaments as t
					 WHERE 1 '.$condition.$where;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getUserCoinsBalance($fields,$condition)
	{
		$sql	=	'SELECT '.$fields.' FROM users as u
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getRedeemedUserCoins($fields,$condition)
	{
		$sql	=	'SELECT '.$fields.' FROM redeems as r
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTotalCoinsIapp($fields,$condition)
	{
		$sql	=	'SELECT '.$fields.' FROM inapppurchasedetails as iap
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function SingleGameDeveDetails($condition) {
			$sql	 =	'select * from gamedevelopers where '.$condition;
			$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function getGameDeveDetails($fields,$condition) {
			$sql	 =	'select '.$fields.' from  games as g
							left join gamedevelopers as gd on (gd.id = g.fkDevelopersId )
							where '.$condition;
			$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function insertGameRules($values) {
		$sql	=	"INSERT INTO {$this->GameRulesTable} 
						(fkGamesId,EliminationRules,HighscoreRules,Status,CreateDate)
					 	VALUES ".$values;
		$this->insertInto($sql);
		
	}
	function selectGameRules($where)
	{
		$sql	=	"SELECT * FROM {$this->GameRulesTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function deleteGameRules($id) {
		$sql	=	"DELETE FROM {$this->GameRulesTable}
						WHERE fkGamesId = ".$id;
		$this->deleteInto($sql);
	}
	function updateGameRules($update_string,$id) {
		$sql	 =	"update {$this->GameRulesTable}  set ".$update_string." WHERE fkGamesId = ".$id;
		$this->updateInto($sql);
	}
	function updateGameDevDetails($update_string,$condition) {
		$sql	 =	"update {$this->gameDeveTable}  set ".$update_string." WHERE ".$condition;
		$this->updateInto($sql);
	}
	function selectGameFiles($where)
	{
		$sql	=	"SELECT * FROM {$this->GameFilesTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function insertGameFiles($values) {
		$sql	=	"INSERT INTO {$this->GameFilesTable} 
						(fkGamesId,Image,Status,CreateDate)
					 	VALUES ".$values;
		$this->insertInto($sql);
		$insertId = $this->sqlInsertId();
        return $insertId;
	}
	function updateGameFiles($update_string,$condition) {
		$sql	 =	"update {$this->GameFilesTable}  set ".$update_string." WHERE ".$condition;
		$this->updateInto($sql);
	}
	function userWinnerCount($fields,$condition)
	{	
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(t.EndDate) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
		
		if(isset($_SESSION['mgc_sess_global_report_game']) && $_SESSION['mgc_sess_global_report_game'] != '')
			$condition .= " and t.fkGamesId = ".$_SESSION['mgc_sess_global_report_game'];
		$sql	=	'SELECT '.$fields.' FROM tournamentsstats as ts LEFT JOIN tournaments AS t ON ( ts.fkTournamentsId = t.`id`)
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}	
	function insertGamePushNotification($input)	{
		$today	=	date('Y-m-d H:i:s');
		$sql	=	' insert into gamespushnotification set ';
		if(isset($input['tiltkey'])	&&	$input['tiltkey'] != '')
			$sql	.=	' fkGamesKey = "'.trim($input['tiltkey']).'",';
		if(isset($input['game_id'])	&&	$input['game_id']!= '')
			$sql	.=	' fkGamesId = "'.trim($input['game_id']).'",';
		if(isset($input['arnkey'])	&&	$input['arnkey']!= '')
			$sql	.=	' ARNKey = "'.trim($input['arnkey']).'",';
		if(isset($input['gcmkey'])	&&	$input['gcmkey']!= '')
			$sql	.=	' GCMkey = "'.trim($input['gcmkey']).'",';
		if(isset($input['platform'])	&&	$input['platform']!= '')
			$sql	.=	' Platform = "'.trim($input['platform']).'",';
		if(isset($input['password'])	&&	$input['password']!='')
			$sql	.=	' CertificatePassword = "'.trim($input['password']).'",';
		$sql	.=	' Status = 1 , DateCreated = "'.$today.'", DateModified = "'.$today.'" ';
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function updateGamePushNotification($update_string,$condition){
		$sql	 =	"update gamespushnotification set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}	
	function selectGamePushNotification($field,$condition){
		$sql	 =	'select '.$field.' from gamespushnotification where '.$condition;
		$result  = 	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getRoundsList($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(ep.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(ep.DatePlayed) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(ep.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(ep.DatePlayed) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
		if(isset($_SESSION['mgc_sess_global_report_game']) && $_SESSION['mgc_sess_global_report_game'] != '')
			$condition .= " and t.fkGamesId = ".$_SESSION['mgc_sess_global_report_game'];		
		$sql	=	'SELECT '.$fields.' FROM eliminationplayer as ep LEFT JOIN  tournamentsplayed as tp ON (tp.id = ep.fkTournamentsPlayedId AND tp.fkUsersId = 0) 
						LEFT JOIN tournaments as t ON ( t.id = tp.fkTournamentsId )
						WHERE 1 '.$condition." GROUP BY fkTournamentsId ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function gameDeveloperDetails($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->gameDeveTable} WHERE ".$condition;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function getGameDevCommission($fields,$condition)	{
		$sql	=	' SELECT '.$fields.' from gamepayments AS gp WHERE 1 '.$condition;
		$result = 	$this->sqlQueryArray($sql);
		if($result) return $result;
		else false;
	}
	function getTournamentUsers($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(tp.DatePlayed) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(tp.DatePlayed) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
		if(isset($_SESSION['mgc_sess_global_report_game']) && $_SESSION['mgc_sess_global_report_game'] != '')
			$condition .= " and t.fkGamesId = ".$_SESSION['mgc_sess_global_report_game'];
		$sql	=	"SELECT ".$fields." FROM tournamentsplayed as tp
					 LEFT JOIN  tournaments as t ON (t.id=tp.fkTournamentsId)
					 left join eliminationplayer as ep on (tp.id = ep.fkTournamentsPlayedId)
					 WHERE 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getActiveTournamentUsers($fields,$condition) {
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(t.EndDate) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
		if(isset($_SESSION['mgc_sess_global_report_game']) && $_SESSION['mgc_sess_global_report_game'] != '')
			$condition .= " and t.fkGamesId = ".$_SESSION['mgc_sess_global_report_game'];
		$sql	=	"SELECT ".$fields."
										FROM activities as a
					 					LEFT JOIN  tournaments as t ON (t.id=a.fkActionId)
										LEFT JOIN users as u ON (u.id = a.fkUsersId)
										WHERE 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getPlayerCount($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != ''	&&	isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != ''){
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."' and date(t.EndDate) <='".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_start']))."'";
		else if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_global_report_end']))."'";
		if(isset($_SESSION['mgc_sess_global_report_game']) && $_SESSION['mgc_sess_global_report_game'] != '')
			$condition .= " and t.fkGamesId = ".$_SESSION['mgc_sess_global_report_game'];	
		$sql	=	'SELECT '.$fields.' FROM activities as a LEFT JOIN tournaments as t ON ( t.id = a.fkActionId )
					 WHERE 1 '.$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
}
?>