<?php
class TournamentModel extends Model
{
	function getTournamentList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_tournament_name']) && $_SESSION['mgc_sess_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_tournament_name']."%' ";
		if(isset($_SESSION['mgc_sess_tournament_game']) && $_SESSION['mgc_sess_tournament_game'] != '')
			$condition .= " and t.fkGamesId = '".$_SESSION['mgc_sess_tournament_game']."' ";
		if(isset($_SESSION['mgc_sess_brand']) && $_SESSION['mgc_sess_brand'] != '')
			$condition .= " and t.fkBrandsId =".$_SESSION['mgc_sess_brand']." ";
		if(isset($_SESSION['mgc_sess_gameType']) && $_SESSION['mgc_sess_gameType'] != '')
			$condition .= " and t.GameType = '".$_SESSION['mgc_sess_gameType']."' ";

		if(isset($_SESSION['mgc_sess_tournament_status']) && $_SESSION['mgc_sess_tournament_status'] != ''){

			$today	=	date('Y-m-d H:i:s');
			$todayWithoutMin	=	date('Y-m-d');
			if($_SESSION['mgc_sess_tournament_status']==0){
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d %H:%i:%s') > '".$today."'	AND t.TournamentStatus != 3) ";
			}
			else if($_SESSION['mgc_sess_tournament_status'] == 1){
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d %H:%i:%s') <= '".$today."'	AND t.TournamentStatus != 3) ";
			}
			else if($_SESSION['mgc_sess_tournament_status'] == 3){
				$condition .= " and t.TournamentStatus = 3 ";
			}

		}

		if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != ''	&&	isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != ''){
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."' and date(t.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."'";
		else if(isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."'";

		if(isset($_SESSION['mgc_sess_pinbased_check'])	&&	$_SESSION['mgc_sess_pinbased_check'] == 1 &&	isset($_SESSION['mgc_sess_locationrestrict_check'])	&&	$_SESSION['mgc_sess_locationrestrict_check']==1 )	{
			$condition	.=	" AND LocationRestrict = 1 AND PIN = 1 ";
		}
		else if(isset($_SESSION['mgc_sess_pinbased_check'])	&&	$_SESSION['mgc_sess_pinbased_check'] == 1)
			$condition	.=	" AND PIN = 1 ";
		else if(isset($_SESSION['mgc_sess_locationrestrict_check'])	&&	$_SESSION['mgc_sess_locationrestrict_check']==1 )
			$condition	.=	" AND LocationRestrict = 1 ";

		if(isset($_SESSION['mgc_sess_locationbased_check'])	&&	$_SESSION['mgc_sess_locationbased_check']==1 )
			$condition	.=	" AND LocationBased = 1 ";
		//******** for Statistics ************
		if(isset($_SESSION['statistics_tournaments'])){
			if(isset($_SESSION['mgc_sess_from_date']) && $_SESSION['mgc_sess_from_date'] != ''	&&	isset($_SESSION['mgc_sess_to_date']) && $_SESSION['mgc_sess_to_date'] != ''){
				$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_from_date']))."' and date(t.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_to_date']))."') ) ";
			}
			else if(isset($_SESSION['mgc_sess_from_date']) && $_SESSION['mgc_sess_from_date'] != '')
				$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_from_date']))."'";
			else if(isset($_SESSION['mgc_sess_to_date']) && $_SESSION['mgc_sess_to_date'] != '')
				$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_to_date']))."'";
		}
		if(isset($_SESSION['mgc_sess_tournament_gameType']) && $_SESSION['mgc_sess_tournament_gameType'] != '')
			$condition	.=	" AND GameType = ".$_SESSION['mgc_sess_tournament_gameType']." ";
		if(isset($_SESSION['mgc_sess_tournament_prizeType']) && $_SESSION['mgc_sess_tournament_prizeType'] != '')
			$condition	.=	" AND Type = ".$_SESSION['mgc_sess_tournament_prizeType']." ";

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t
						LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)
						LEFT JOIN {$this->brandTable} as b on (t.fkBrandsId=b.id)
						WHERE 1 ".$condition." GROUP BY t.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function getUserTournamentList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_tournament_name']) && $_SESSION['mgc_sess_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_tournament_name']."%' ";
		if(isset($_SESSION['mgc_sess_tournament_game']) && $_SESSION['mgc_sess_tournament_game'] != '')
			$condition .= " and t.fkGamesId = '".$_SESSION['mgc_sess_tournament_game']."' ";
		if(isset($_SESSION['mgc_sess_tournament_user']) && $_SESSION['mgc_sess_tournament_user'] != '')
			$condition .= " and (u.FirstName LIKE '%".trim($_SESSION['mgc_sess_tournament_user'])."%' OR	u.LastName LIKE '%".trim($_SESSION['mgc_sess_tournament_user'])."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".trim($_SESSION['mgc_sess_tournament_user'])."%')";
		if(isset($_SESSION['mgc_sess_gameType']) && $_SESSION['mgc_sess_gameType'] != '')
			$condition .= " and t.GameType = '".$_SESSION['mgc_sess_gameType']."' ";
		if(isset($_SESSION['mgc_sess_tournament_status']) && $_SESSION['mgc_sess_tournament_status'] != ''){
			$today	=	date('Y-m-d H:i:s');
			$todayWithoutMin	=	date('Y-m-d');
			if($_SESSION['mgc_sess_tournament_status']==0)
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d') > '".$today."'	AND t.TournamentStatus != 3) ";
			else if($_SESSION['mgc_sess_tournament_status'] == 1)
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d %H:%i:%s') <= '".$today."'	AND t.TournamentStatus != 3) ";
			else if($_SESSION['mgc_sess_tournament_status'] == 3)	$condition .= " and t.TournamentStatus = 3 ";
		}

		if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != ''	&&	isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '')
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."' and date(t.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."') ) ";
		else if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."'";
		else if(isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."'";

		if(isset($_SESSION['mgc_sess_tournament_gameType']) && $_SESSION['mgc_sess_tournament_gameType'] != '')
			$condition	.=	" AND GameType = ".$_SESSION['mgc_sess_tournament_gameType']." ";
		if(isset($_SESSION['mgc_sess_tournament_prizeType']) && $_SESSION['mgc_sess_tournament_prizeType'] != '')
			$condition	.=	" AND Type = ".$_SESSION['mgc_sess_tournament_prizeType']." ";
		//******** for Statistics ************
		if(isset($_SESSION['statistics_tournaments'])){
			if(isset($_SESSION['mgc_sess_from_date']) && $_SESSION['mgc_sess_from_date'] != ''	&&	isset($_SESSION['mgc_sess_to_date']) && $_SESSION['mgc_sess_to_date'] != ''){
				$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_from_date']))."' and date(t.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_to_date']))."') ) ";
			}
			else if(isset($_SESSION['mgc_sess_from_date']) && $_SESSION['mgc_sess_from_date'] != '')
				$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_from_date']))."'";
			else if(isset($_SESSION['mgc_sess_to_date']) && $_SESSION['mgc_sess_to_date'] != '')
				$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_to_date']))."'";
		}
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t
						LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)
						LEFT JOIN {$this->userTable} as u on (t.fkUsersId=u.id)
						WHERE 1 ".$condition." GROUP BY t.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function getgamedevloperTournamentList($fields,$condition)
	{
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_tournament_name']) && $_SESSION['mgc_sess_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_tournament_name']."%' ";
		if(isset($_SESSION['mgc_sess_tournament_game']) && $_SESSION['mgc_sess_tournament_game'] != '')
			$condition .= " and t.fkGamesId = '".$_SESSION['mgc_sess_tournament_game']."' ";
		if(isset($_SESSION['mgc_sess_game_developer']) && $_SESSION['mgc_sess_game_developer'] != '')
			$condition .= " and (gd.Company LIKE '%".$_SESSION['mgc_sess_game_developer']."%')";
		if(isset($_SESSION['mgc_sess_gameType']) && $_SESSION['mgc_sess_gameType'] != '')
			$condition .= " and t.GameType = '".$_SESSION['mgc_sess_gameType']."' ";
		if(isset($_SESSION['mgc_sess_tournament_status']) && $_SESSION['mgc_sess_tournament_status'] != ''){
			$today	=	date('Y-m-d H:i:s');
			$todayWithoutMin	=	date('Y-m-d');
			if($_SESSION['mgc_sess_tournament_status']==0)
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d %H:%i:%s') > '".$today."'	AND t.TournamentStatus != 3) ";
			else if($_SESSION['mgc_sess_tournament_status'] == 1)
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d %H:%i:%s') <= '".$today."'	AND t.TournamentStatus != 3) ";
			else if($_SESSION['mgc_sess_tournament_status'] == 3)
				$condition .= " and t.TournamentStatus = 3 ";
		}
		if(isset($_SESSION['mgc_sess_pinbased_check'])	&&	$_SESSION['mgc_sess_pinbased_check'] == 1 &&	isset($_SESSION['mgc_sess_locationrestrict_check'])	&&	$_SESSION['mgc_sess_locationrestrict_check']==1 )	{
			$condition	.=	" AND LocationRestrict = 1 AND PIN = 1 ";
		}
		else if(isset($_SESSION['mgc_sess_pinbased_check'])	&&	$_SESSION['mgc_sess_pinbased_check'] == 1)
			$condition	.=	" AND PIN = 1 ";
		else if(isset($_SESSION['mgc_sess_locationrestrict_check'])	&&	$_SESSION['mgc_sess_locationrestrict_check']==1 )
			$condition	.=	" AND LocationRestrict = 1 ";

		if(isset($_SESSION['mgc_sess_locationbased_check'])	&&	$_SESSION['mgc_sess_locationbased_check']==1 )
			$condition	.=	" AND LocationBased = 1 ";
		//******** for Statistics ************
		if(isset($_SESSION['statistics_tournaments'])){
			if(isset($_SESSION['mgc_sess_from_date']) && $_SESSION['mgc_sess_from_date'] != ''	&&	isset($_SESSION['mgc_sess_to_date']) && $_SESSION['mgc_sess_to_date'] != ''){
				$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_from_date']))."' and date(t.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_to_date']))."') ) ";
			}
			else if(isset($_SESSION['mgc_sess_from_date']) && $_SESSION['mgc_sess_from_date'] != '')
				$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_from_date']))."'";
			else if(isset($_SESSION['mgc_sess_to_date']) && $_SESSION['mgc_sess_to_date'] != '')
				$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_to_date']))."'";
		}
		if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != ''	&&	isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '')
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."' and date(t.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."') ) ";
		else if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."'";
		else if(isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."'";

		if(isset($_SESSION['mgc_sess_tournament_gameType']) && $_SESSION['mgc_sess_tournament_gameType'] != '')
			$condition	.=	" AND GameType = ".$_SESSION['mgc_sess_tournament_gameType']." ";
		if(isset($_SESSION['mgc_sess_tournament_prizeType']) && $_SESSION['mgc_sess_tournament_prizeType'] != '')
			$condition	.=	" AND Type = ".$_SESSION['mgc_sess_tournament_prizeType']." ";

			$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t
						LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)
						LEFT JOIN {$this->gameDeveTable} as gd on (t.fkDevelopersId=gd.id)
						WHERE 1 ".$condition." GROUP BY t.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function deleteTournamentReleatedEntries($delete_id)
	{
		$update_string 	= " Status = 3 ";
		$condition 		= " id IN(".$delete_id.") ";
		$sql	 =	"update {$this->tournamentsTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function updateUserDetails($update_string,$condition){
		$sql	 =	"update {$this->tournamentsTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function selectBrandDetails($fields,$condition)
	{
		$sql	=	"SELECT ".$fields." FROM  {$this->brandTable} WHERE 1 ".$condition." ORDER BY BrandName ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectGameDetails($field,$condition)
	{
		$sql	=	"SELECT ".$field." FROM  {$this->gameTable} WHERE 1 ".$condition." ORDER BY Name ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertTournamentDetails($post_values)
	{
		$sql	 =	"insert into  {$this->tournamentsTable}  set ";
		if(isset($post_values['tournament'])	&&	trim($post_values['tournament']!=""))
			$sql	.=	"TournamentName 	= 	'".$post_values['tournament']."',";
		if(isset($post_values['tournament'])	&&	trim($post_values['tournament']!=""))
			$sql	.=	"GftRules	= 	'".$post_values['tournament']."',";
		if(isset($post_values['brand'])	&&	trim($post_values['brand']!=""))
			$sql	.=	"fkBrandsId 		= 	'".$post_values['brand']."',";
		if(isset($post_values['game'])	&&	trim($post_values['game']!=""))
			$sql	.=  "fkGamesId			=	'".$post_values['game']."',";
		if(isset($post_values['maxplayer'])	&&	trim($post_values['maxplayer']!=""))
			$sql	.=	"MaxPlayers			=	'".$post_values['maxplayer']."',";
		if(isset($post_values['turns'])	&&	trim($post_values['turns']!=""))
			$sql	.=	"TotalTurns			=	'".$post_values['turns']."',";
		if(isset($post_values['entryfee'])	&&	trim($post_values['entryfee']!=""))
			$sql 	.=	"EntryFee 			= 	'".$post_values['entryfee']."',";
		if(isset($post_values['prize'])	&&	trim($post_values['prize']!=""))
			$sql 	.=	"Prize 				= 	'".$post_values['prize']."',";
			if(isset($post_values['gametype'])	&&	trim($post_values['gametype']!=""))
			$sql 	.=	"GameType 			= 	'".$post_values['gametype']."',";
		if(isset($post_values['elimination'])	&&	trim($post_values['elimination'])!="")
			$sql	.=	"Elimination		=	'".$post_values['elimination']."',";
		if(isset($post_values['tournamenttype'])	&&	trim($post_values['tournamenttype'])!="")
			$sql 	.=	"Type				=	1, ";
		if(isset($post_values['startdate'])	&&	trim($post_values['startdate'])!="")
			$sql	.=	"StartDate	=	'".date('Y-m-d H:i',strtotime($post_values['startdate']))."',";
		if(isset($post_values['enddate'])	&&	trim($post_values['enddate'])!="")
			$sql	.=	"EndDate	=	'".date('Y-m-d H:i',strtotime($post_values['enddate']))."',";
		if(isset($post_values['entryfee_flag'])	&&	trim($post_values['entryfee_flag']!=""))
			$sql	.=	"FeeType			=	'".$post_values['entryfee_flag']."',";
		$sql 	.=	" Status 			= 	1,
						DateCreated 		= 	'".date('Y-m-d H:i:s')."',
						  DateModified		= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function getTournamentDetails($fields,$condition)
	{
		$sql	=	"SELECT ".$fields." FROM  {$this->tournamentsTable} as t
					LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)
					WHERE 1 ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getUserTournamentDetails($fields,$condition)
	{
		$sql	=	"SELECT ".$fields." FROM  {$this->tournamentsTable} as t
					LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)
					LEFT JOIN {$this->userTable} as u on (t.fkUsersId=u.id)
					WHERE 1 ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameDeveloperDetails($fields,$condition)
	{
		$sql	=	"SELECT ".$fields." FROM  {$this->tournamentsTable} as t
					LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)
					LEFT JOIN {$this->gameDeveTable} as gd on (t.fkDevelopersId=gd.id)
					WHERE 1 ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function updateTournamentDetails($update_string,$condition)
	{
		$sql	 =	"update {$this->tournamentsTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function getTournamentPlayers($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsPlayedTable} as tp
					LEFT JOIN {$this->userTable} as u ON (tp.fkUsersId=u.id)
					WHERE 1 ".$condition."  ".'GROUP BY u.id ORDER BY '.$sorting_clause.' '.$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTournamentChatCount($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM `tournaments` as t
						LEFT JOIN chats as c ON(t.id=c.fkTournamentsId)
						WHERE 1  ".$condition." Group BY c.fkTournamentsId";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTournamentPlayersCount($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsPlayedTable} as tp WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(isset($result) && is_array($result) && count($result) > 0 ) {
			$resultArray	=	array();
			foreach($result as $key=>$value){
				if (array_key_exists($value->fkTournamentsId, $resultArray)) {
					if (!isset($resultArray[$value->fkTournamentsId][$value->fkUsersId]))
						$resultArray[$value->fkTournamentsId][$value->fkUsersId] = 1;
				}
				else	$resultArray[$value->fkTournamentsId][$value->fkUsersId] = 1;
			}
			if(isset($resultArray) && is_array($resultArray) && count($resultArray) > 0 ) {
				$resultCountArray	=	array();
				foreach($resultArray as $key=>$countArray){
					if(isset($countArray) && is_array($countArray))
						$resultCountArray[$key]	=	count($countArray);
				}
				return $resultCountArray;
			}
			else return false;
		}
		else false;
	}
	function getChatList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' c.id desc';

		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_chat_username'])	&&	$_SESSION['mgc_sess_chat_username'])
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_chat_username']."%' OR	u.LastName LIKE '%".$_SESSION['mgc_sess_chat_username']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_chat_username']."%')";
		if(isset($_SESSION['mgc_sess_chat_email'])	&&	$_SESSION['mgc_sess_chat_email'])
			$condition	.=	' AND u.email	LIKE "'.$_SESSION['mgc_sess_chat_email'].'%" ';
		if(isset($_SESSION['mgc_sess_chat_date'])	&&	$_SESSION['mgc_sess_chat_date'])
			$condition	.=	' AND DATE_FORMAT(c.DateCreated,"%Y-%m-%d") = "'.date('Y-m-d',strtotime($_SESSION['mgc_sess_chat_date'])).'" ';
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->chatsTable} as c
						LEFT JOIN {$this->userTable} as u ON (c.fkUsersId=u.id)
						WHERE 1".$condition." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function tournamentList($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsTable} as t
						WHERE 1 ".$condition." ORDER BY TournamentName ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function tournamentPlayers($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM tournamentsplayed as tp
					LEFT JOIN users as u ON (tp.fkUsersId=u.id)
					WHERE 1 ".$condition."  Order BY tp.id DESC";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function coinsWinList($fields,$condition,$leftjoin)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_coins_tournament']) && $_SESSION['mgc_sess_coins_tournament'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_coins_tournament']."%' ";

		if(isset($_SESSION['mgc_sess_coin_date'])	&&	$_SESSION['mgc_sess_coin_date'])
			$condition	.=	' AND DATE_FORMAT(ts.DateCreated,"%Y-%m-%d") = "'.date('Y-m-d',strtotime($_SESSION['mgc_sess_coin_date'])).'" ';


		$sql	 =	"select SQL_CALC_FOUND_ROWS ".$fields." from {$this->tournamentsStatsTable} as ts
						LEFT JOIN {$this->tournamentsTable} as t ON(ts.fkTournamentsId=t.id)
						LEFT JOIN {$this->tournamentsPlayedTable} as tp ON (tp.fkTournamentsId=ts.fkTournamentsId)
						LEFT JOIN {$this->eliminationPlayerTable} as ept ON (tp.id=ept.fkTournamentsPlayedId)
						".$leftjoin."
						where ".$condition."
						GROUP BY tp.fktournamentsId
						ORDER BY ".$sorting_clause." ".$limit_clause;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function selectPinCode($fields,$condition) {
		$leftJoin	=	'';
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_pinCode_status'])	&&	$_SESSION['mgc_pinCode_status']	==1	){
			$leftJoin = " LEFT JOIN {$this->userTable} as u ON(p.fkUsersId=u.id) ";
		}
		$sql	=	"SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->pinStatsTable} as p ".$leftJoin."
						WHERE 1 ".$condition."
						ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function checkRulesEntry($fields,$condition) {

		$sql	=	"SELECT ".$fields." FROM {$this->tournamentRulesTable} WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertRules($values){
		$sql	 =	" INSERT INTO {$this->tournamentRulesTable} ( fkTournamentsId,fkBrandsId,fkCountriesId,fkStatesId,TournamentRules,TermsAndConditions,GftRules,PrivacyPolicy,DateCreated,DateModified,Status) ".$values;
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function updateTournamentRules($updateString,$condition)
	{
		$sql	=	" update  {$this->tournamentRulesTable}  set ".$updateString." WHERE ".$condition;
		$this->result = $this->insertInto($sql);
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
	function getRulesList($fields,$condition) {

		$limit_clause='';
		$sorting_clause = ' t.TournamentName asc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_rule_tournament']) && $_SESSION['mgc_sess_rule_tournament'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_rule_tournament']."%' ";

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentRulesTable}  as tr
					LEFT JOIN {$this->tournamentsTable} as t on (tr.fkTournamentsId=t.id)
					WHERE ".$condition." GROUP BY tr.fkTournamentsId
					ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getRules($fields,$condition) {

		$sql	=	" SELECT ".$fields." FROM {$this->tournamentRulesTable}  as tr
					LEFT JOIN {$this->countriesTable} as c on (tr.fkCountriesId=c.id)
					WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getRulesDetails($fields,$condition)	{
		$sql	=	"SELECT ".$fields." from {$this->tournamentRulesTable} as tr
					WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectTournamentRule($fields,$condition) {
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentRulesTable}  as tr
					LEFT JOIN {$this->tournamentsTable} as t on (tr.fkTournamentsId=t.id)
					WHERE  ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectTournament($fields,$condition) {
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsTable} WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}

	function getTournamentUserList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' t.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsPlayedTable} as tp
					LEFT JOIN {$this->userTable} as u ON (tp.fkUsersId=u.id)
					LEFT JOIN {$this->tournamentsTable} as t ON (tp.fkTournamentsId=t.id)
					WHERE 1 ".$condition."  ".'GROUP BY t.id,u.id ORDER BY '.$sorting_clause.' '.$limit_clause;

		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectTournamentList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' t.id asc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['sess_turns_tournament_name']) && $_SESSION['sess_turns_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".trim($_SESSION['sess_turns_tournament_name'])."%' ";
		$having = "";
		$tfields = "";
		$groupby	= "";
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS distinct ".$tfields." ".$fields." ,u.Photo as userPhoto FROM {$this->tournamentsTable} as t
						LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)
						LEFT JOIN gamedevelopers as gd on (gd.id=t.fkDevelopersId)
						LEFT JOIN {$this->userTable} as u on (u.id=t.fkUsersId)
						WHERE 1 and ".$condition." ".$groupby." ".$having."  ORDER BY ".$sorting_clause.$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameTournamentList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' t.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_game_tour']) && $_SESSION['mgc_sess_game_tour'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_game_tour']."%' ";

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t
					WHERE 1 ".$condition."  ".'GROUP BY t.id ORDER BY '.$sorting_clause.' '.$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getLocationRestrict($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM  {$this->locationRestrictionTable} AS lr
							LEFT JOIN  {$this->countriesTable} AS c ON ( lr.`fkCountriesId` = c.id )
							LEFT JOIN  {$this->statesTable} AS s ON ( lr.`fkStatesId` = s.id AND s.`fkCountriesId` = c.id )
							WHERE 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function getTournamentsCoupon($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM  {$this->tournamentsCouponadLinkTable}
							WHERE 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function getCustomPrize($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM  {$this->tournamentCustomPrizeTable}
							WHERE 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function getCustomAd($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM  tournamentcustomad 
							WHERE 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function updateTournamentCouponAdLink($updateString,$condition)	{
		$sql	=	" Update {$this->tournamentsCouponadLinkTable} set ".$updateString." where ".$condition;
		$this->updateInto($sql);
	}
	function insertYoutubeLink($queryString){
		$sql	 =	" INSERT INTO {$this->tournamentsCouponadLinkTable} ( fkTournamentsId,fkBrandsId,CouponAdLink,URL,File,CouponTitle,CouponStartDate,CouponEndDate,Type,InputType,Status,DateCreated,DateModified,CouponLimit) ".$queryString;
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function getTournamentPlayed($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsPlayedTable} as tp WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function updateCustomPrizeDetails($updateString,$condition)	{
		$sql	=	" Update {$this->tournamentCustomPrizeTable} set ".$updateString." where ".$condition;
		$this->updateInto($sql);
	}
	function insertCustomPrize($queryString){
		$sql	 =	" INSERT INTO {$this->tournamentCustomPrizeTable} (fkBrandsId,fkDevelopersId,fkTournamentsId,PrizeTitle,PrizeImage,PrizeDescription,PrizeOrder,Status,DateCreated,DateModified) ".$queryString;
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function getRestrictedLocation($condition)
	{
		$sql	=	" SELECT * FROM {$this->locationRestrictionTable} where 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function insertRestrictedLocation($fields)
	{
		$sql	=	" insert into {$this->locationRestrictionTable}  set ".$fields.", Status=1, DateCreated='".date('Y-m-d H:i:s')."', DateModified='".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function updateRestrictedLocation($fields,$con)
	{
		$upsql	=	"update {$this->locationRestrictionTable} set ".$fields." where ".$con;
		$this->updateInto($upsql);
	}
	function getGameDeveloper($fields, $condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->gameDeveTable} where 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function checkLocationRestriction($fields,$condition) {
		$sql	=	"	SELECT ".$fields." FROM {$this->locationRestrictionTable}
						WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getEliminationPlayerList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM eliminationplayer as ep
						LEFT JOIN  `tournamentsplayed` as tp on (tp.id = ep.fkTournamentsPlayedId)
						LEFT JOIN users as u on (u.id=ep.fkUsersId)
						WHERE 1".$condition." GROUP BY ep.fkUsersId ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getEliminationPlayersCount($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM  `eliminationtable` as et LEFT JOIN tournamentsplayed AS tp  ON ( et.fkTournamentsPlayedId = tp.id) WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getEliminationPoints($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM eliminationplayer WHERE 1 ".$condition;
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
	function getTournamentPlayedCount($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsPlayedTable} as tp WHERE ".$condition." GROUP BY tp.fkTournamentsId";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
}
?>
