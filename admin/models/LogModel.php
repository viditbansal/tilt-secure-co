<?php
class LogModel extends Model
{
   function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function logtrackDetails($where)
	{
		$limit_clause = '';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		// search date
		if(isset($_SESSION['mgc_sess_logtrack_from_date']) && $_SESSION['mgc_sess_logtrack_from_date'] != ''	&&	isset($_SESSION['mgc_sess_logtrack_to_date']) && $_SESSION['mgc_sess_logtrack_to_date'] != '')
			$where .= " AND ((date(l.start_time) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_logtrack_from_date']))."' and date(l.end_time) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_logtrack_to_date']))."') ) ";
		else if(isset($_SESSION['mgc_sess_logtrack_from_date']) && $_SESSION['mgc_sess_logtrack_from_date'] != '')
			$where .= " AND date(l.start_time) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_logtrack_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_logtrack_to_date']) && $_SESSION['mgc_sess_logtrack_to_date'] != '')
			$where .= " AND date(l.end_time) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_logtrack_to_date']))."'";
		if(isset($_SESSION['mgc_sess_logtrack_searchIP']) && $_SESSION['mgc_sess_logtrack_searchIP'] != '')
			$where .= " and l.ip_address LIKE '%".$_SESSION['mgc_sess_logtrack_searchIP']."%' ";
		if(isset($_SESSION['mgc_sess_logtrack_SearchResponse']) && $_SESSION['mgc_sess_logtrack_SearchResponse'] != '')
			$where .= " and l.response LIKE '%".$_SESSION['mgc_sess_logtrack_SearchResponse']."%' ";
		if(isset($_SESSION['mgc_sess_logtrack_method']) && $_SESSION['mgc_sess_logtrack_method'] != '')
			$where .= " and l.method LIKE '%".$_SESSION['mgc_sess_logtrack_method']."%' ";
		if(isset($_SESSION['mgc_sess_logtrack_searchUrl']) && $_SESSION['mgc_sess_logtrack_searchUrl'] != '')
			$where .= " and l.url LIKE '%".$_SESSION['mgc_sess_logtrack_searchUrl']."%' ";
			
		$sql	=	"SELECT l.id,l.id as logId,l.*
					FROM {$this->logTable} as l 
					WHERE 1 ".$where." ORDER BY ".$sorting_clause.$limit_clause;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function logUsersDetails($fields,$logUserTokens){
		$sql	=	"	SELECT ".$fields." FROM {$this->oauthSessionAccessTokensTable} as atk  
						LEFT JOIN {$this->oauthSessionTable} as ses on ( ses.id = atk.session_id ) 
						LEFT JOIN {$this->userTable} as u on (u.id = ses.owner_id) 
						LEFT JOIN {$this->oauthClientsTable} as ac on(ac.id=ses.client_id)
						WHERE atk.access_token IN(".$logUserTokens.") ";
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function selectUserDetails($field,$condition){
		$sql	 =	"select ".$field." from {$this->userTable} as u 
					LEFT JOIN {$this->oauthSessionTable} as ses on ( u.id = ses.owner_id ) 
					LEFT JOIN {$this->oauthSessionAccessTokensTable} as atk  ON(ses.id=atk.session_id)
					where ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function tournamentList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' t.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_tournament_name']) && $_SESSION['mgc_sess_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_tournament_name']."%' ";
		
		if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != ''	&&	isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != ''){
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."' and date(t.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."'";
		else if(isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."'";

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields.",count(tp.fkTournamentsId) as playersCount FROM {$this->tournamentsTable} as t 
						Left Join {$this->tournamentsPlayedTable} as tp ON (t.id=tp.fkTournamentsId) 
						WHERE 1 ".$condition." GROUP BY t.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) {	return false;	}
		else {	return $result;
		}
	}
	
	function getTournamentList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' t.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_tournament_name']) && $_SESSION['mgc_sess_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_tournament_name']."%' ";
		
		if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != ''	&&	isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != ''){
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."' and date(t.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."'";
		else if(isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."'";

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t 
						WHERE 1 ".$condition." GROUP BY t.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) {	return false;	}
		else {	return $result;
		}
	}
	
	
	function tournamentPlayers($fields,$condition)
	{
		$sql	=	"SELECT ".$fields." FROM {$this->tournamentsPlayedTable} as tp 
					Left JOIN {$this->tournamentsTable} as t on (t.id=tp.fkTournamentsId)
					LEFT JOIN {$this->userTable} as u ON (tp.fkUsersId=u.id) 
					WHERE 1 ".$condition."  Order BY tp.id DESC";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function playersTurnsDetails($fields,$condition)
	{
		$sql	=	"SELECT ".$fields." FROM {$this->tournamentsPlayedTable} as tp 
					Left JOIN {$this->tournamentsTable} as t on (t.id=tp.fkTournamentsId)
					LEFT JOIN {$this->userTable} as u ON (tp.fkUsersId=u.id) 
					WHERE 1 ".$condition."  GROUP BY tp.DateCreated,tp.fkUsersId,tp.fkTournamentsId Order BY tp.id DESC";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function tournamentWinList($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsStatsTable} as tour_stat 
					WHERE 1 ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function usersCount($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != ''){
			$condition .= " AND ((date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."' and date(DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '')
			$condition .= " AND date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '')
			$condition .= " AND date(DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."'";
			
		$sql	=	" SELECT ".$fields." FROM {$this->userTable}  
					WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function brandsCount($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != ''){
			$condition .= " AND ((date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."' and date(DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '')
			$condition .= " AND date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '')
			$condition .= " AND date(DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."'";
			
		$sql	=	" SELECT ".$fields." FROM {$this->brandTable}  
					WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function tournamentsCount($fields,$condition)
	{
		
		if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != ''){
			$condition .= " AND ((date(t.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."' and date(t.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '')
			$condition .= " AND date(t.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '')
			$condition .= " AND date(t.DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."'";
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsTable}  as t WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function gamesCount($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != ''){
			$condition .= " AND ((date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."' and date(DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '')
			$condition .= " AND date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '')
			$condition .= " AND date(DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."'";
			
		$sql	=	" SELECT ".$fields." FROM {$this->gameTable}  
					WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function activeTournamentsCount($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '') {
			$from_date	=	date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']));
			$to_date	=	date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']));
			$condition .= " AND (
								(date(StartDate) <= '".$from_date."' AND date(EndDate) >= '".$from_date."') OR
								(date(StartDate) <= '".$to_date."' AND date(EndDate) >= '".$to_date."') OR
								(date(StartDate) >= '".$from_date."' AND date(StartDate) <= '".$to_date."') OR
								(date(EndDate) >= '".$from_date."' AND date(EndDate) <= '".$to_date."')
								 
								)";
		} else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '') {
			$from_date	=	date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']));
			$condition .=	" AND (date(StartDate) >= '".$from_date."' OR date(EndDate) >= '".$from_date."' OR date( EndDate ) = '0000-00-00' )";
		} else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '') {
			$to_date	=	date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']));
			$condition .=	" AND ( (date(EndDate) <= '".$to_date."' AND date(EndDate) != '0000-00-00') OR (date(EndDate) >= '".$to_date."' AND date(StartDate) <= '".$to_date."') OR (date(StartDate) <= '".$to_date."' AND GameType = 2))";
		}
	
		$sql	=	" 	SELECT ".$fields." FROM {$this->tournamentsTable} as t 
					WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getUserList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' u.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

			if(isset($_SESSION['mgc_sess_user_name']) && $_SESSION['mgc_sess_user_name'] != '')
				$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_user_name']."%' OR	u.LastName LIKE '%".$_SESSION['mgc_sess_user_name']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_user_name']."%')";
			if(isset($_SESSION['mgc_sess_email']) && $_SESSION['mgc_sess_email'] != '')
			$condition .= " and u.Email LIKE '%".$_SESSION['mgc_sess_email']."%' ";
			if(isset($_SESSION['mgc_sess_reg_user_status']) && $_SESSION['mgc_sess_reg_user_status'] != ''){
				if($_SESSION['mgc_sess_reg_user_status'] == 0)
					$condition .= " and u.VerificationStatus = '".$_SESSION['mgc_sess_reg_user_status']."' ";
				else 
					$condition .= " and u.Status = '".$_SESSION['mgc_sess_reg_user_status']."'  and u.VerificationStatus = 1 ";	
			}
			

		if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != ''){
			$condition .= " AND ((date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."' and date(DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '')
			$condition .= " AND date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '')
			$condition .= " AND date(DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."'";
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." from {$this->userTable} as u
				WHERE 1 ".$condition." group by u.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function redeemList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' r.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_redeemed_giftcard']) && $_SESSION['mgc_sess_redeemed_giftcard'] != '')
			$condition .= " and gc.GiftCardName LIKE '%".$_SESSION['mgc_sess_redeemed_giftcard']."%' ";
		if(isset($_SESSION['mgc_sess_redeemed_user']) && $_SESSION['mgc_sess_redeemed_user'] != '')
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_redeemed_user']."%' OR	u.LastName LIKE '%".$_SESSION['mgc_sess_redeemed_user']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_redeemed_user']."%')";
		if(isset($_SESSION['mgc_sess_redeemed_date']) && $_SESSION['mgc_sess_redeemed_date'] != '')
			$condition .= " and date(r.DateCreated) = '".date('Y-m-d',strtotime($_SESSION['mgc_sess_redeemed_date']))."'";	

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->redeemsTable} as r 
						LEFT JOIN giftcards as gc on(gc.id=r.fkGiftCardsId)
						LEFT JOIN users as u on(u.id=r.fkUsersId)
						WHERE ".$condition." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTotalCoins($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_redeemed_giftcard']) && $_SESSION['mgc_sess_redeemed_giftcard'] != '')
			$condition .= " and gc.GiftCardName LIKE '%".$_SESSION['mgc_sess_redeemed_giftcard']."%' ";
		if(isset($_SESSION['mgc_sess_redeemed_user']) && $_SESSION['mgc_sess_redeemed_user'] != '')
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_redeemed_user']."%' OR	u.LastName LIKE '%".$_SESSION['mgc_sess_redeemed_user']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_redeemed_user']."%')";
		if(isset($_SESSION['mgc_sess_redeemed_date']) && $_SESSION['mgc_sess_redeemed_date'] != '')
			$condition .= " and date(r.DateCreated) = '".date('Y-m-d',strtotime($_SESSION['mgc_sess_redeemed_date']))."'";	
		$sql	=	" SELECT ".$fields." FROM {$this->redeemsTable} as r 
						LEFT JOIN giftcards as gc on(gc.id=r.fkGiftCardsId)
						LEFT JOIN users as u on(u.id=r.fkUsersId)
						WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getPurchasedCoinsList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_purchase_brand']) && $_SESSION['mgc_sess_purchase_brand'] != '')
			$condition .= " and gd.Company LIKE '%".$_SESSION['mgc_sess_purchase_brand']."%' ";
		if(isset($_SESSION['mgc_sess_purchased_user']) && $_SESSION['mgc_sess_purchased_user'] != '')
			$condition .= " and ( gd.UserName LIKE '%".$_SESSION['mgc_sess_purchased_user']."%') ";
		if(isset($_SESSION['mgc_sess_purchased_date']) && $_SESSION['mgc_sess_purchased_date'] != '')
			$condition .= " and date(gp.CreatedDate) = '".date('Y-m-d',strtotime($_SESSION['mgc_sess_purchased_date']))."'";	
		
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->gamepaymentsTable} as gp 
						LEFT JOIN {$this->gameDeveTable} as gd on(gd.id=gp.fkDeveloperId)
						WHERE ".$condition." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTotalPurchaseAmount($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_purchase_brand']) && $_SESSION['mgc_sess_purchase_brand'] != '')
			$condition .= " and gd.Company LIKE '%".$_SESSION['mgc_sess_purchase_brand']."%' ";
		if(isset($_SESSION['mgc_sess_purchased_user']) && $_SESSION['mgc_sess_purchased_user'] != '')
			$condition .= " and ( gd.UserName LIKE '%".$_SESSION['mgc_sess_purchased_user']."%') ";
		if(isset($_SESSION['mgc_sess_purchased_date']) && $_SESSION['mgc_sess_purchased_date'] != '')
			$condition .= " and date(gp.CreatedDate) = '".date('Y-m-d',strtotime($_SESSION['mgc_sess_purchased_date']))."'";
		$sql	=	" SELECT ".$fields." FROM {$this->gamepaymentsTable} as gp 
						LEFT JOIN {$this->gameDeveTable} as gd on(gd.id=gp.fkDeveloperId)
						WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTotalCommissionAmount($fields,$condition)
	{
		if(isset($_SESSION['mgc_sess_commission_brand']) && $_SESSION['mgc_sess_commission_brand'] != '')
			$condition .= " and gd.Company LIKE '%".$_SESSION['mgc_sess_commission_brand']."%' ";
		if(isset($_SESSION['mgc_sess_commission_date']) && $_SESSION['mgc_sess_commission_date'] != '')
			$condition .= " and date(gp.CreatedDate) = '".date('Y-m-d',strtotime($_SESSION['mgc_sess_commission_date']))."'";
		$sql	=	" SELECT ".$fields." FROM {$this->gamepaymentsTable} as gp 
						LEFT JOIN {$this->gameDeveTable} as gd on(gd.id=gp.fkDeveloperId)
						WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getCommissionList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		if(isset($_SESSION['mgc_sess_commission_brand']) && $_SESSION['mgc_sess_commission_brand'] != '')
			$condition .= " and gd.Company LIKE '%".$_SESSION['mgc_sess_commission_brand']."%' ";
		if(isset($_SESSION['mgc_sess_commission_date']) && $_SESSION['mgc_sess_commission_date'] != '')
			$condition .= " and date(gp.CreatedDate) = '".date('Y-m-d',strtotime($_SESSION['mgc_sess_commission_date']))."'";	
		
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields."  FROM {$this->gamepaymentsTable} as gp 
						LEFT JOIN {$this->gameDeveTable} as gd on(gd.id=gp.fkDeveloperId)
						WHERE ".$condition." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function cronTrackDetails($where)
	{
		$limit_clause = '';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		if(isset($_SESSION['mgc_sess_cron_fromDate']) && $_SESSION['mgc_sess_cron_fromDate'] != ''	&&	isset($_SESSION['mgc_sess_cron_toDate']) && $_SESSION['mgc_sess_cron_toDate'] != ''){
			$where .= " AND ((date(c.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_cron_fromDate']))."' and date(c.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_cron_toDate']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_cron_fromDate']) && $_SESSION['mgc_sess_cron_fromDate'] != '')
			$where .= " AND date(c.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_cron_fromDate']))."'";
		else if(isset($_SESSION['mgc_sess_cron_toDate']) && $_SESSION['mgc_sess_cron_toDate'] != '')
			$where .= " AND date(c.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_cron_toDate']))."'";
		
		if(isset($_SESSION['mgc_sess_cron_status']) && $_SESSION['mgc_sess_cron_status'] != '')
			$where .= " and c.Status =".$_SESSION['mgc_sess_cron_status']." ";
		
		$sql	=	"SELECT SQL_CALC_FOUND_ROWS c.id,c.*
					FROM {$this->cronTable} as c 
					WHERE  1 ".$where." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	
	
	function tournamentLeaderBoard($fields,$condition)	{
		$sql = "SELECT ".$fields." 
					from {$this->tournamentsStatsTable} as ts
					LEFT JOIN  {$this->tournamentsPlayedTable} as tp on (ts.fkTournamentsId = tp.fkTournamentsId AND ts.fkUsersId=tp.fkUsersId)
					LEFT JOIN {$this->userTable} as u on (tp.fkUsersId = u.id) 
					where 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;			
	}
	
	function finishedTournamentList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' t.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_tournament_name']) && $_SESSION['mgc_sess_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['mgc_sess_tournament_name']."%' ";
		
		if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != ''	&&	isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != ''){
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."' and date(t.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_tournament_start']) && $_SESSION['mgc_sess_tournament_start'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_start']))."'";
		else if(isset($_SESSION['mgc_sess_tournament_end']) && $_SESSION['mgc_sess_tournament_end'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_end']))."'";
		
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsStatsTable} as ts
						LEFT JOIN {$this->tournamentsTable} as t on (ts.fkTournamentsId = t.id)
						WHERE 1 ".$condition." GROUP BY t.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) {	return false;	}
		else {	return $result;
		}
	}
	
	function tournamentPrizeDetails($fields,$condition)	{
		$sql	=	"SELECT ".$fields."	FROM {$this->tournamentsStatsTable} as ts LEFT JOIN {$this->tournamentsTable} as t on t.id = ts.fkTournamentsId
							WHERE 1 ".$condition."
							GROUP BY ts.fkUsersId, ts.fkTournamentsId
							ORDER BY ts.`id` DESC";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) {	return false;	}
		else {	return $result;
		}
	}

	function iapTrackDetails($where)
	{
		$limit_clause = '';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_iaptrack_userName']) && !empty($_SESSION['mgc_sess_iaptrack_userName'])){
			$where .= " and (u.FirstName LIKE '%".trim($_SESSION['mgc_sess_iaptrack_userName'])."%' OR	u.LastName LIKE '%".trim($_SESSION['mgc_sess_iaptrack_userName'])."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".trim($_SESSION['mgc_sess_iaptrack_userName'])."%')";
		}
		if(isset($_SESSION['mgc_sess_iaptrack_receiptId']) && !empty($_SESSION['mgc_sess_iaptrack_receiptId'])){
			$where .= " and c.TransactionReceiptId LIKE '%".$_SESSION['mgc_sess_iaptrack_receiptId']."%' ";
		}
		if(isset($_SESSION['mgc_sess_iaptrack_price']) && $_SESSION['mgc_sess_iaptrack_price'] !=''){
			$where .= " and ( c.PackagePrice = '".$_SESSION['mgc_sess_iaptrack_price']."' ) ";
		}
		if(isset($_SESSION['mgc_sess_iaptrack_date']) && !empty($_SESSION['mgc_sess_iaptrack_date'])){
			$where .= " AND date(c.CreatedDate) =  '".date("Y-m-d",strtotime($_SESSION['mgc_sess_iaptrack_date']))."'";
		}
		
		$sql	=	"SELECT SQL_CALC_FOUND_ROWS c.id,c.*,u.FirstName,u.LastName,u.id as userId, u.UniqueUserId, u.Status 
					FROM {$this->iapTable} as c 
					LEFT JOIN {$this->userTable} as u ON c.fkUsersId=u.id
					WHERE  1 ".$where." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function mediaTrackDetails($fields,$condition)
	{
		$limit_clause 	= '';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		if(isset($_SESSION['sess_media_tournament_name']) && $_SESSION['sess_media_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['sess_media_tournament_name']."%' ";
			
		$sql	= "SELECT SQL_CALC_FOUND_ROWS ".$fields."
					FROM mediaimpressiontracking as mi 
					LEFT JOIN users as u on (u.id = mi.fkUsersId)
					LEFT JOIN tournaments as t on (t.id = mi.fkTournamentsId)
					LEFT JOIN tournamentscouponadlink as tcal on (tcal.id = mi.fkAdId)
					LEFT JOIN games as g on (g.id = t.fkGamesId)
					WHERE  1 ".$condition." GROUP BY mi.fkTournamentsId ORDER BY ".$sorting_clause." ".$limit_clause;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function getGameVirtualCoinReport($fields,$condition)
	{
		$limit_clause= '';
		$sorting_clause = 'g.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_report_game']) && $_SESSION['mgc_sess_report_game'] != '')
			$condition .= " and g.Name LIKE '%".$_SESSION['mgc_sess_report_game']."%'";
		if(isset($_SESSION['mgc_sess_report_coin']) && $_SESSION['mgc_sess_report_coin'] != '')
			$condition .= " and t.Prize >= '".$_SESSION['mgc_sess_report_coin']."'";	
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM  {$this->tournamentsTable} as t
						LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)
						WHERE  ".$condition." GROUP BY t.fkGamesId ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectPlayWinLossEntry($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM  {$this->activitiesTable} as a
						LEFT JOIN {$this->tournamentsTable} as t on (t.id=a.fkActionId)
						WHERE ".$condition." GROUP BY t.fkGamesId";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGamePlayers($fields,$condition)
	{	
		 $limit_clause='';
		$sorting_clause = ' t.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
				
		if(isset($_SESSION['mgc_sess_report_playerName']) && $_SESSION['mgc_sess_report_playerName'] != '')
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_report_playerName']."%' ||	u.LastName LIKE '%".$_SESSION['mgc_sess_report_playerName']."%' || CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_report_playerName']."%')";
		
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM  {$this->activitiesTable} as a Left Join  
						{$this->tournamentsTable} as t ON ( t.id = a.fkActionId ) 
						LEFT JOIN {$this->userTable} as u on ( a.fkUsersId = u.id )
						WHERE  ".$condition." GROUP BY u.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameWinners($fields,$condition)
	{	
		 $limit_clause='';
		$sorting_clause = ' t.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM  {$this->activitiesTable} as a
						LEFT JOIN {$this->userTable} as u on ( a.fkUsersId = u.id )
						LEFT JOIN {$this->tournamentsTable} as t on (t.id = a.fkActionId)
						WHERE  ".$condition." GROUP BY u.id ORDER BY ".$sorting_clause." ".$limit_clause;

		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getTournamentReportList($fields,$condition)
	{
		 $limit_clause = $leftJoin = '';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_report_tournamentName']) && $_SESSION['mgc_sess_report_tournamentName'] != '')
			$condition .= " and t.TournamentName LIKE '%".trim($_SESSION['mgc_sess_report_tournamentName'])."%' ";
		if(isset($_SESSION['mgc_sess_report_tournamentGame']) && $_SESSION['mgc_sess_report_tournamentGame'] != '')
			$condition .= " and g.Name LIKE '%".$_SESSION['mgc_sess_report_tournamentGame']."%' ";
		if(isset($_SESSION['mgc_sess_report_tourbrand']) && $_SESSION['mgc_sess_report_tourbrand'] != ''){
			$leftJoin	=	"LEFT JOIN {$this->gameDeveTable} as gd on (t.fkDevelopersId=gd.id)".
			$condition .= " and gd.Company LIKE '%".$_SESSION['mgc_sess_report_tourbrand']."%' AND gd.id !='' ";
		}
		if(isset($_SESSION['mgc_sess_report_tournamentuser']) && $_SESSION['mgc_sess_report_tournamentuser'] != ''){
			$leftJoin	=	"LEFT JOIN {$this->userTable} as u on (t.fkUsersId=u.id)".
			$condition .= " and (u.FirstName LIKE '%".trim($_SESSION['mgc_sess_report_tournamentuser'])."%' OR	u.LastName LIKE '%".trim($_SESSION['mgc_sess_report_tournamentuser'])."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".trim($_SESSION['mgc_sess_report_tournamentuser'])."%')";
		}
		//******** for Statistics ************
		if(isset($_SESSION['statistics_tournaments'])){
			if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != ''){
				$condition .= " AND ((date(t.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."' and date(t.DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."') ) ";
			}
			else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '')
				$condition .= " AND date(t.DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."'";
			else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '')
				$condition .= " AND date(t.DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."'";
		}
		if(isset($_SESSION['mgc_sess_report_tourStatus']) && $_SESSION['mgc_sess_report_tourStatus'] != ''){
			$today	=	date('Y-m-d H:i:s');
			$todayWithoutMin	=	date('Y-m-d');
			if($_SESSION['mgc_sess_report_tourStatus']==0){
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d') > '".$today."'	AND t.TournamentStatus != 3) ";
			}
			else if($_SESSION['mgc_sess_report_tourStatus'] == 1){
				$condition .= " and (DATE_FORMAT(t.StartDate,'%Y-%m-%d %H:%i:%s') <= '".$today."'	AND t.TournamentStatus != 3) ";
			}
			else if($_SESSION['mgc_sess_report_tourStatus'] == 3){
				$condition .= " and t.TournamentStatus = 3 ";
			}
		}
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t 
						LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)".
						$leftJoin
						." WHERE 1 ".$condition." GROUP BY t.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectUserDetail($field,$condition){
		$sql	 =	"select ".$field." from {$this->userTable} where ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function selectDeveloperDetails($field,$condition){
		$sql	 =	'select '.$field." from {$this->gameDeveTable} where ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function selectBrandDetails($fields,$condition)	{
		$sql	=	'SELECT '.$fields." FROM {$this->brandTable} WHERE ".$condition;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function getMediaImpression($fields,$condition)
	{
		$limit_clause 	= '';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
			
		$sql	= "SELECT SQL_CALC_FOUND_ROWS ".$fields."
					FROM `mediaimpressiontracking` AS mi
					LEFT JOIN users AS u ON ( u.id = mi.fkUsersId )
					WHERE  1 ".$condition." GROUP BY u.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function logtrackCount($where)
	{		 
		if(isset($_SESSION['mgc_sess_logtrack_from_date']) && $_SESSION['mgc_sess_logtrack_from_date'] != ''	&&	isset($_SESSION['mgc_sess_logtrack_to_date']) && $_SESSION['mgc_sess_logtrack_to_date'] != '')
			$where .= " AND ((date(l.start_time) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_logtrack_from_date']))."' and date(l.end_time) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_logtrack_to_date']))."') ) ";
		else if(isset($_SESSION['mgc_sess_logtrack_from_date']) && $_SESSION['mgc_sess_logtrack_from_date'] != '')
			$where .= " AND date(l.start_time) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_logtrack_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_logtrack_to_date']) && $_SESSION['mgc_sess_logtrack_to_date'] != '')
			$where .= " AND date(l.end_time) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_logtrack_to_date']))."'";
		if(isset($_SESSION['mgc_sess_logtrack_searchIP']) && $_SESSION['mgc_sess_logtrack_searchIP'] != '')
			$where .= " and l.ip_address LIKE '%".$_SESSION['mgc_sess_logtrack_searchIP']."%' ";
		if(isset($_SESSION['mgc_sess_logtrack_SearchResponse']) && $_SESSION['mgc_sess_logtrack_SearchResponse'] != '')
			$where .= " and l.response LIKE '%".$_SESSION['mgc_sess_logtrack_SearchResponse']."%' ";
		if(isset($_SESSION['mgc_sess_logtrack_method']) && $_SESSION['mgc_sess_logtrack_method'] != '')
			$where .= " and l.method LIKE '%".$_SESSION['mgc_sess_logtrack_method']."%' ";
		if(isset($_SESSION['mgc_sess_logtrack_searchUrl']) && $_SESSION['mgc_sess_logtrack_searchUrl'] != '')
			$where .= " and l.url LIKE '%".$_SESSION['mgc_sess_logtrack_searchUrl']."%' ";
		
		$sql	=	"SELECT COUNT(id) as count
					FROM {$this->logTable} as l 
					WHERE 1 ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function getPlayersDetail($fields,$condition)
	{	
				
		$sql	=	" SELECT ".$fields." 
						FROM `tournamentsplayed` AS tp
						LEFT JOIN tournaments AS t ON ( t.id = tp.fkTournamentsId )
						WHERE 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function developerCount($condition)
	{
		if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != ''	&&	isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != ''){
			$condition .= " AND ((date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."' and date(DateCreated) <= '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."') ) ";
		}
		else if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '')
			$condition .= " AND date(DateCreated) >=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_from_date']))."'";
		else if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '')
			$condition .= " AND date(DateCreated) <=  '".date('Y-m-d',strtotime($_SESSION['mgc_sess_statistic_to_date']))."'";
			
		$sql	=	" SELECT count(id) as devCount FROM {$this->gameDeveTable}  
					WHERE ".$condition." ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getEliminationPlayedEntry($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsPlayedTable} as tp 
						LEFT JOIN {$this->eliminationPlayerTable} as ept on (tp.id = ept.fkTournamentsPlayedId)
						LEFT JOIN {$this->userTable} as u on (u.id = ept.fkUsersId)
						WHERE 1 ".$condition." Order BY ept.id DESC ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getEliminationPlayers($fields,$condition)
	{
		  $sql	=	" SELECT ".$fields." FROM {$this->tournamentsStatsTable} as ts
						LEFT JOIN {$this->tournamentsPlayedTable} as tp  on(tp.fkTournamentsId = ts.fkTournamentsId) 
						LEFT JOIN {$this->eliminationPlayerTable} as ept  on(tp.id = ept.fkTournamentsPlayedId AND ept.fkUsersId=ts.fkUsersId) 
						LEFT JOIN {$this->userTable} as u on(ts.fkUsersId=u.id) 
						WHERE 1 ".$condition." ORDER BY ts.Prize DESC"; 
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getElimPlayersDetail($fields,$condition)
	{	
		$sql	=	" SELECT ".$fields." 
						FROM {$this->tournamentsTable} AS t
						LEFT JOIN {$this->tournamentsPlayedTable} AS tp ON ( t.id = tp.fkTournamentsId )
						LEFT JOIN {$this->eliminationPlayerTable} as ep  on(ep.fkTournamentsPlayedId = tp.id)
						WHERE 1 ".$condition." GROUP BY ep.fkUsersId,tp.fkTournamentsId ";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getGameUserAndWinnerCount($fields,$condition) {
		$sql	=	"SELECT ".$fields."
										FROM  {$this->activitiesTable} as a
										LEFT JOIN {$this->tournamentsTable} as t on (a.fkActionId = t.id)
										WHERE  ".$condition." GROUP BY t.fkGamesId ORDER BY t.fkGamesId desc";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getHighScorePlayerTournament($fields,$condition) {
		$sql	=	" SELECT ".$fields."
					FROM  {$this->tournamentsTable} as t
					LEFT JOIN {$this->tournamentsPlayedTable} as tp  on (tp.fkTournamentsId = t.id)
					WHERE   ".$condition." GROUP BY tp.fkTournamentsId,tp.fkUsersId ORDER BY id desc";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getElimPlayerTournament($fields,$condition) {
		$sql	=	" SELECT ".$fields."
					FROM  {$this->activitiesTable} as a
					LEFT JOIN {$this->eliminationPlayerTable} as ep on (a.fkPlayedId = ep.id)
					LEFT JOIN {$this->tournamentsTable} as t  on (a.fkActionId = t.id)
					WHERE   ".$condition." GROUP BY a.fkActionId,a.fkUsersId ORDER BY id desc";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getWinnersPlayedTournament($fields,$condition) {
		$sql	=	"SELECT ".$fields."
					FROM {$this->tournamentsTable} as t
					LEFT JOIN {$this->tournamentsStatsTable} as ts on (t.id = ts.fkTournamentsId)
					WHERE   ".$condition." GROUP BY ts.fkTournamentsId, ts.fkUsersId ORDER BY id desc";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
}
?>