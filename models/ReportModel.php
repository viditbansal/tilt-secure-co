<?php
class ReportModel extends Model
{
	function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function getDeveloperUsersReport($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
			
		 if(isset($_SESSION['tilt_sess_userReport_user']) && $_SESSION['tilt_sess_userReport_user'] != '')
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['tilt_sess_userReport_user']."%' OR	u.LastName LIKE '%".$_SESSION['tilt_sess_userReport_user']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['tilt_sess_userReport_user']."%')";
		if(isset($_SESSION['tilt_sess_userReport_fromDate']) && $_SESSION['tilt_sess_userReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_userReport_endDate']) && $_SESSION['tilt_sess_userReport_endDate'] != '')
			$condition .= " AND ((date(ActivityDate) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_userReport_fromDate']))."' and date(ActivityDate) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_userReport_endDate']))."') ) ";
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t 
						LEFT JOIN {$this->activityTable} as a on (a.fkActionId=t.id)
						LEFT JOIN {$this->userTable} as u on (a.fkUsersId=u.id)
						WHERE  ".$condition." AND a.fkActionId !='' AND  a.ActionType in (2,3,4) GROUP BY a.fkUsersId ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getUserWinLossCount($fields,$condition)
	{
		 if(isset($_SESSION['tilt_sess_userReport_fromDate']) && $_SESSION['tilt_sess_userReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_userReport_endDate']) && $_SESSION['tilt_sess_userReport_endDate'] != '')
			$condition .= " AND ((date(ActivityDate) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_userReport_fromDate']))."' and date(ActivityDate) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_userReport_endDate']))."') ) "; 
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsTable} as t 
						LEFT JOIN {$this->activityTable} as a on (t.id=a.fkActionId)
						WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getUserCoinsCount($fields,$condition)
	{
		 if(isset($_SESSION['tilt_sess_userReport_fromDate']) && $_SESSION['tilt_sess_userReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_userReport_endDate']) && $_SESSION['tilt_sess_userReport_endDate'] != '')
			$condition .= " AND ((date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_userReport_fromDate']))."' and date(DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_userReport_endDate']))."') ) "; 
		$sql	=	" SELECT ".$fields." FROM {$this->paymentHistoryTable} WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameReportList($fields,$condition)
	{	
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
			
		if(isset($_SESSION['tilt_sess_gameReport_Game']) && $_SESSION['tilt_sess_gameReport_Game'] != '')
			$condition .= " and g.Name LIKE '%".$_SESSION['tilt_sess_gameReport_Game']."%'  ";
		
		if(isset($_SESSION['tilt_sess_gameReport_fromDate']) && $_SESSION['tilt_sess_gameReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_gameReport_endDate']) && $_SESSION['tilt_sess_gameReport_endDate'] != '')
				$condition .= " AND ( date(t.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_gameReport_fromDate']))."' and date(t.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_gameReport_endDate']))."')";
		
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM  {$this->tournamentsTable} as t
						LEFT JOIN activities as a on (a.fkActionId = t.id) 
						LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)
						WHERE  ".$condition." GROUP BY t.fkGamesId ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameTournaments($fields,$condition)
	{	
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		 if(isset($_SESSION['tilt_sess_gameReport_tourStatus']) && $_SESSION['tilt_sess_gameReport_tourStatus'] != ''){
			$today	=	date('Y-m-d');
			$today	=	date('Y-m-d H:i');
			$todayWithoutMin	=	date('Y-m-d');
			if($_SESSION['tilt_sess_gameReport_tourStatus']==0){
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d H%:%i') > '".$today."'	AND t.TournamentStatus != 3) ";
			}
			else if($_SESSION['tilt_sess_gameReport_tourStatus'] == 1){
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d H%:%i') <= '".$today."'	AND t.TournamentStatus != 3) ";
			}
			else if($_SESSION['tilt_sess_gameReport_tourStatus'] == 3){
				$condition .= " and t.TournamentStatus = 3 ";
			}
		} 
		
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM  {$this->tournamentsTable} as t
						WHERE  ".$condition." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGamePlayers($fields,$condition)
	{	
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
						
		if(!empty($_SESSION['tilt_sess_gameReport_players']))
			$condition .= " AND (FirstName LIKE '%".$_SESSION['tilt_sess_gameReport_players']."%' OR	LastName LIKE '%".$_SESSION['tilt_sess_gameReport_players']."%' OR CONCAT( FirstName,  ' ', LastName ) LIKE  '%".$_SESSION['tilt_sess_gameReport_players']."%')";
		if(!empty($_SESSION['tilt_sess_gameReport_email'])) {
				 $condition	.=	' AND email	LIKE "%'.$_SESSION['tilt_sess_gameReport_email'].'%" ';
		}
		if(isset($_SESSION['tilt_sess_gameReport_ptournaments'])	&&	$_SESSION['tilt_sess_gameReport_ptournaments'])
			$condition	.=	' AND t.TournamentName	LIKE "%'.$_SESSION['tilt_sess_gameReport_ptournaments'].'%" ';
	
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} AS t
						LEFT JOIN {$this->activityTable} AS a ON ( t.id = a.fkActionId )
						LEFT JOIN {$this->userTable} as u ON ( a.fkUsersId = u.id )
						WHERE  ".$condition." Group BY u.id ORDER BY id ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameWinnerDetail($fields,$condition)
	{	
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['tilt_sess_gameReport_winplayers'])	&&	$_SESSION['tilt_sess_gameReport_winplayers'])
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['tilt_sess_gameReport_winplayers']."%' OR	u.LastName LIKE '%".$_SESSION['tilt_sess_gameReport_winplayers']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['tilt_sess_gameReport_winplayers']."%')";
		if(isset($_SESSION['tilt_sess_gameReport_winemail'])	&&	$_SESSION['tilt_sess_gameReport_winemail'])
			$condition	.=	' AND u.email	LIKE "%'.$_SESSION['tilt_sess_gameReport_winemail'].'%" ';

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM  {$this->tournamentsTable} as t
						LEFT JOIN {$this->activityTable} as a on (a.fkActionId=t.id )
						LEFT JOIN {$this->userTable} as u on (a.fkUsersId=u.id)
						WHERE ".$condition." 
						Group BY u.id ORDER BY ".$sorting_clause." ".$limit_clause;	
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTournamentReportList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['tilt_sess_tourReport_tournament']) && $_SESSION['tilt_sess_tourReport_tournament'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['tilt_sess_tourReport_tournament']."%'  ";
		if(isset($_SESSION['tilt_sess_tourReport_fromDate']) && $_SESSION['tilt_sess_tourReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_tourReport_endDate']) && $_SESSION['tilt_sess_tourReport_endDate'] != ''){
			$condition .= " AND ((date(t.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_fromDate']))."' and date(t.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_endDate']))."') ) ";
		}
		$leftCond  = $leftCond1 = '';
		if(isset($_SESSION['tilt_sess_tourReport_fromDate']) && $_SESSION['tilt_sess_tourReport_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_tourReport_endDate']) && $_SESSION['tilt_sess_tourReport_endDate'] != ''){
			$leftCond = " AND ((date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_fromDate']))."' and date(tp.DatePlayed) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_endDate']))."') OR tp.fkUsersId = 0  ) ";
			$leftCond1 = " AND ((date(ep.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_fromDate']))."' and date(ep.DatePlayed) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_endDate']))."') ) ";
		}else  if(isset($_SESSION['tilt_sess_tourReport_fromDate']) && $_SESSION['tilt_sess_tourReport_fromDate'] != ''){
			$leftCond .= " AND ( date(tp.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_fromDate']))."' OR tp.fkUsersId = 0 )";
			$leftCond1 .= " AND date(ep.DatePlayed) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_fromDate']))."'";
		}else if(isset($_SESSION['tilt_sess_tourReport_endDate']) && $_SESSION['tilt_sess_tourReport_endDate'] != ''){
			$leftCond .= "AND ((date(tp.DatePlayed) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_endDate']))."') OR tp.fkUsersId = 0) ";
			$leftCond1 .= "AND date(ep.DatePlayed) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tourReport_endDate']))."'";
		}
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t 
						LEFT JOIN {$this->gameDeveTable} as gd on (t.fkDevelopersId=gd.id)
						LEFT JOIN {$this->tournamentsPlayedTable} as tp on (tp.fktournamentsId=t.id ".$leftCond.")
						LEFT JOIN {$this->eliminationPlayerTable} as ep on (ep.fkTournamentsPlayedId=tp.id ".$leftCond1.")
						LEFT JOIN {$this->tournamentsStatsTable} AS ts ON ( ts.fkTournamentsId = tp.fkTournamentsId AND ts.fkUsersId > 0)
						WHERE  ".$condition." GROUP BY t.id ORDER BY t.id DESC ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
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
	function getTournamentWinners($fields,$condition)
	{	
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
			
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM 
						{$this->tournamentsStatsTable} AS ts 
						LEFT JOIN {$this->userTable} AS u ON ( ts.fkUsersId = u.id ) 
						LEFT JOIN {$this->tournamentsPlayedTable} AS tp ON ( tp.fkUsersId=u.id AND tp.fkTournamentsId = ts.fkTournamentsId ) 
						LEFT JOIN {$this->tournamentsTable} AS t ON ( ts.fkTournamentsId = t.id ) 
						WHERE ".$condition." ".$limit_clause;	
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getCustomPrizeDetails($fields,$condition) {
		$sql	=	"	SELECT ".$fields." FROM {$this->customPrizeTable} WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectUserDetails($field,$condition){
		$sql	 =	"select ".$field." from {$this->userTable} where ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function getTournamentElimPlayers($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		 if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType']; 
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->eliminationPlayerTable} as tp 
					LEFT JOIN {$this->userTable} as u ON (tp.fkUsersId=u.id) 
					WHERE 1 ".$condition.' ORDER BY '.$sorting_clause.' '.$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getElimPlayerScore($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->eliminationPlayerTable} as tp WHERE 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTournamentElimWinners($fields,$condition)
	{	
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
			
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM 
						{$this->tournamentsStatsTable} AS ts 
						LEFT JOIN {$this->userTable} AS u ON ( ts.fkUsersId = u.id ) 
						LEFT JOIN {$this->tournamentsTable} AS t ON ( ts.fkTournamentsId = t.id ) 
						WHERE ".$condition."  ".$limit_clause;	
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTournamentPlayed($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsPlayedTable} as tp WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGamePlayersCount($fields,$condition)
	{	
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsTable} as t 
						LEFT JOIN {$this->tournamentsPlayedTable} AS tp ON (tp.fkTournamentsId=t.id)
						LEFT JOIN {$this->eliminationPlayerTable} as ep ON ( tp.id = ep.fkTournamentsPlayedId  )
						WHERE  ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getUserswinTournamentreport($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		
		if(isset($_SESSION['tilt_sess_gameReport_tourStatus']) && $_SESSION['tilt_sess_gameReport_tourStatus'] != ''){
			$today	=	date('Y-m-d');
			if($_SESSION['tilt_sess_gameReport_tourStatus']==0){
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d ') > '".$today."'	AND t.TournamentStatus != 3) ";
			}
			else if($_SESSION['tilt_sess_gameReport_tourStatus'] == 1){
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d') <= '".$today."'	AND t.TournamentStatus != 3) ";
			}
			else if($_SESSION['tilt_sess_gameReport_tourStatus'] == 3){
				$condition .= " and t.TournamentStatus = 3 ";
			}
		}
		
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t 
					  LEFT JOIN  {$this->activityTable} AS a ON (a.fkActionId = t.id)
					  WHERE  ".$condition." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
			else return $result;
	}
	function getBrandUsersTournamentlist($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		if(isset($_SESSION['tilt_sess_gameReport_tourStatus']) && $_SESSION['tilt_sess_gameReport_tourStatus'] != ''){
			$today	=	date('Y-m-d');
			if($_SESSION['tilt_sess_gameReport_tourStatus']==0){
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d ') > '".$today."'	AND t.TournamentStatus != 3) ";
			}
			else if($_SESSION['tilt_sess_gameReport_tourStatus'] == 1){
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d') <= '".$today."'	AND t.TournamentStatus != 3) ";
			}
			else if($_SESSION['tilt_sess_gameReport_tourStatus'] == 3){
				$condition .= " and t.TournamentStatus = 3 ";
			}
		}
		
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t 
					  LEFT JOIN {$this->activityTable} AS a ON (a.fkActionId = t.id)
					  WHERE  ".$condition."GROUP BY t.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);	
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameUsersTournaments($fields,$condition)
	{
		if(isset($_SESSION['tilt_sess_gameReport_ptournaments'])	&&	$_SESSION['tilt_sess_gameReport_ptournaments'])
			$condition	.=	' AND t.TournamentName	LIKE "%'.$_SESSION['tilt_sess_gameReport_ptournaments'].'%" ';
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsTable} as t 
					  LEFT JOIN {$this->activityTable} AS a ON (a.fkActionId = t.id)
					  WHERE  ".$condition."GROUP BY a.fkUsersId,a.fkActionId ORDER BY id ";
						
		$result	=	$this->sqlQueryArray($sql);	
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameWinnerTournaments($fields,$condition)
	{	
		$sql	=	" SELECT  ".$fields." 
						FROM {$this->activityTable} AS a 
						LEFT JOIN {$this->tournamentsTable} as t on (t.id = a.fkActionId) 
						WHERE ".$condition." ORDER BY a.fkUsersId desc ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getPurchaseList($fields,$condition)	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['tilt_sess_purchase_amount'])	&&	$_SESSION['tilt_sess_purchase_amount'] != '')
			$condition	.=	' AND Amount = "'.$_SESSION['tilt_sess_purchase_amount'].'" ';
		if(isset($_SESSION['tilt_sess_purchase_date'])	&&	$_SESSION['tilt_sess_purchase_date'] != '')
				$condition	.=	' AND date(CreatedDate) = "'.date('Y-m-d',strtotime($_SESSION['tilt_sess_purchase_date'])).'" ';
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->gamePaymentsTable} 
						where ".$condition." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
		
	}
}
?>