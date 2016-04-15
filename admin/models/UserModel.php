<?php
class UserModel extends Model
{
   function getUserList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

			if(isset($_SESSION['mgc_sess_user_name']) && $_SESSION['mgc_sess_user_name'] != '')
			$condition .= " and (u.FirstName LIKE '%".trim($_SESSION['mgc_sess_user_name'])."%' OR	u.LastName LIKE '%".trim($_SESSION['mgc_sess_user_name'])."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".trim($_SESSION['mgc_sess_user_name'])."%')";

			if(isset($_SESSION['mgc_sess_email']) && $_SESSION['mgc_sess_email'] != '')
			$condition .= " and u.Email LIKE '%".$_SESSION['mgc_sess_email']."%' ";
			
			if(isset($_SESSION['mgc_sess_user_status']) && $_SESSION['mgc_sess_user_status'] != ''){
				if($_SESSION['mgc_sess_user_status'] == 0)
					$condition .= " and u.VerificationStatus = '".$_SESSION['mgc_sess_user_status']."' ";
				else 
					$condition .= " and u.Status = '".$_SESSION['mgc_sess_user_status']."'  and u.VerificationStatus = 1 ";	
			}
			
			if(isset($_SESSION['mgc_sess_user_registerdate']) && $_SESSION['mgc_sess_user_registerdate'] != '')
			$condition .= " and date(u.DateCreated) = '".$_SESSION['mgc_sess_user_registerdate']."'";	
		
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." 
				from {$this->userTable} as u
				WHERE 1 ".$condition." 
				group by u.id 
				ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
   function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function updateUserDetails($update_string,$condition){
		$sql	 =	"update {$this->userTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function selectUserDetails($field,$condition){
		$sql	 =	"select ".$field." from {$this->userTable} where ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function insertUserDetails($register_values){
		$sql	 =	"insert into  {$this->userTable}  set ";
		if(isset($register_values['username'])	&&	trim($register_values['username']!=""))
			$sql	.=	"UserName 			= 	'".$register_values['username']."',";
		if(isset($register_values['firstname'])	&&	trim($register_values['firstname']!=""))			
			$sql	.=  "FirstName			=	'".$register_values['firstname']."',";
		if(isset($register_values['lastname'])	&&	trim($register_values['lastname']!=""))			
			$sql	.=	"LastName			=	'".$register_values['lastname']."',";
		if(isset($register_values['email'])	&&	trim($register_values['email']!=""))			
			$sql 	.=	"Email 			= 	'".$register_values['email']."',";
		if(isset($register_values['gender'])	&&	trim($register_values['gender']!=""))			
			$sql 	.=	"Gender 		= 	'".$register_values['gender']."',";
			if(isset($register_values['ipaddress'])	&&	trim($register_values['ipaddress']!=""))			
			$sql 	.=	"IpAddress 		= 	'".$register_values['ipaddress']."',";
		if(isset($register_values['fbid'])	&&	trim($register_values['fbid'])!="")
			$sql	.=	"FBId			=	'".$register_values['fbid']."',";
		if(isset($register_values['linkedid'])	&&	trim($register_values['linkedid'])!="")
			$sql 	.=	"LinkedInId		=	'".$register_values['linkedid']."',";
		if(isset($register_values['twitterid'])	&&	trim($register_values['twitterid'])!="")
			$sql	.=	"TwitterId	=	'".$register_values['twitterid']."',";
		if(isset($register_values['googleid'])	&&	trim($register_values['googleid'])!="")
			$sql	.=	"GooglePlusId	=	'".$register_values['googleid']."',";			
		if(isset($register_values['interest'])	&&	trim($register_values['interest']!=""))			
			$sql	.=  "Interest			=	'".$register_values['interest']."',";
		if(isset($register_values['company'])	&&	trim($register_values['company']!=""))			
			$sql	.=  "Company			=	'".$register_values['company']."',";
		if(isset($register_values['title'])	&&	trim($register_values['title']!=""))			
			$sql	.=  "Title			=	'".$register_values['title']."',";
		if(isset($register_values['location'])	&&	trim($register_values['location']!=""))			
			$sql	.=  "Location			=	'".$register_values['location']."',";
		if(isset($register_values['DefaultTilt'])	&&	trim($register_values['DefaultTilt']!=""))			
			$sql	.=  "Coins			=	'".$register_values['DefaultTilt']."',";
		if(isset($register_values['DefaultVirtualCoins'])	&&	trim($register_values['DefaultVirtualCoins']!=""))			
			$sql	.=  "VirtualCoins		=	'".$register_values['DefaultVirtualCoins']."',";	
			$sql 	.=	" Status 			= 	1,
						  DateCreated 		= 	'".date('Y-m-d H:i:s')."',
						  DateModified		= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function getUserDetails($fields, $condition)
	{
			$sql	 =	"SELECT ".$fields." FROM {$this->userTable} AS user
						 WHERE 1 ".$condition;
		$result = 	$this->sqlQueryArray($sql);

			if($result) return $result;
			else false;
	}
	function selectWordDetails(){
		$sql	 =	"select * from {$this->wordsTable} where 1 order by rand() limit 1 ";
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function selectContactDetails($fields, $condition)
	{
		$sql	 =	"SELECT ".$fields." FROM {$this->contactTable} AS ct
					JOIN {$this->userTable} AS ut ON ( ut.id = ct.ContactId )
					WHERE ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	
	function getUserHashDetails($fields, $condition)
	{
			$sql	 =	"SELECT ".$fields." FROM {$this->userTable} AS user
						LEFT JOIN cards as c ON (user.id=c.fkUsersId)
						LEFT JOIN userinterests as ui ON (user.id=ui.fkUsersId)
						WHERE 1 ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	
	function deleteUserReleatedEntries($userId){
		$like_postIds = $like_hashIds = $follow_hashIds = $hashIds = $postIds = '';
		$update_string 	= " Status = 3 ";
		$condition 		= " id IN(".$userId.") ";
		$this->updateUserDetails($update_string,$condition);
	}
	function getActivityDetails($fields, $condition)
	{
		$sql	 =	"SELECT ".$fields." FROM {$this->activityTable} AS At
					WHERE 1 ".$condition. "group by ActivityType" ;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function getActivity($userId){
	
		$postQuery = "select id from hashtagpost where fkUsersId = $userId";
		$postResult = $this->sqlQueryArray($postQuery);
		$postIds = '0';
		if(is_array($postResult) && count($postResult) > 0){
			$postIds = '';
			foreach($postResult as $key=>$value){
				$postIds .= $value->id.',';
			}
			$postIds = rtrim($postIds,',');
		}
		$contactQuery = "select ContactId from contact where  	fkUsersId = $userId and ContactType = 1";
		$contactResult = $this->sqlQueryArray($contactQuery);
		$contactIds = '0';
		if(is_array($contactResult) && count($contactResult) > 0){
			$contactIds = '';
			foreach($contactResult as $key=>$value){
				$contactIds .= $value->ContactId.',';
			}
			$contactIds = rtrim($contactIds,',');
		}
		
		$blockUserIds 	= $blockCondition = ''; 
		$blockQuery = "select * from contact where (fkUsersId ='".$userId."' or ContactId ='".$userId."') and ContactType = 2 ";
		$blockArray	=  $this->sqlQueryArray($blockQuery);
		if($blockArray && count($blockArray) > 0){
			foreach($blockArray as $key=>$value){
				if($value->fkUsersId != $userId)
					$blockUserIds .= $value->fkUsersId.',';
				if($value->ContactId != $userId)
					$blockUserIds .= $value->ContactId.',';
			}
		}
		
		if($blockUserIds != '')
		{
			$blockUserIds = rtrim($blockUserIds,',');
			$blockCondition = " and u.id not in (".$blockUserIds.") ";
		}
		$query = "Select a.id as actId,a.ActivityType,u.id as userId,u.Photo,u.UserName,a.ActivityDate,
				  hp.PostType,h.HashtagName,h.OriginalHashtag,h.id as hashId,hp.id as postId,hp.ImagePath
				  from activity as a
				  left join hashtagpost as hp on (hp.id = a.fkActionId and hp.Status=1)
				  left join hashtags as h on ((
				  								(h.id = a.fkActionId and a.ActivityType  = 5) 
												or 
												(h.id = a.fkProcessId and a.ActivityType  = 4) 
												or 
												(h.id = hp.fkHashtagId and (a.ActivityType  = 1 or a.ActivityType  = 2))
												) 
											 and (h.Status=1 or h.Status=2)) 
				  left join user as u on (u.id = a.fkUsersId)
				  where 
				  (
					  (a.fkActionId in ($postIds) and (a.ActivityType = 1 or  a.ActivityType = 2 ))  
					  or 
					  (a.fkUsersId in ($contactIds) and (a.ActivityType = 4  or a.ActivityType =5 )) 
					  or 
					  (a.fkActionId = $userId and  a.ActivityType =3) 
				  ) 
				  and a.fkUsersId !=$userId $blockCondition and u.Status = '1' group by actId order by a.id desc limit 0,50";
		$result = 	$this->sqlQueryArray($query);
		if($result) return $result;
			else false;
	}
	function changeUsersStatus($userIds,$updateStatus){
		$update_string 	= " Status =  ".$updateStatus;
		$condition 		= " id IN(".$userIds.") ";
		$this->updateUserDetails($update_string,$condition);
	}
	function getCoinsCount($field,$condition){
		$sql	 =	"select ".$field." from {$this->tournamentsStatsTable}  ts
					LEFT JOIN {$this->tournamentsTable} as t ON (t.id=ts.fkTournamentsId)
					where ".$condition." GROUP BY ts.fkUsersId ";
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function getPurchaseList($field,$condition){
		$sql	 =	"select ".$field." from {$this->redeemsTable} as r 
						LEFT JOIN giftcards as gc on (gc.id = r.fkGiftCardsId) 
						where ".$condition." GROUP BY fkUsersId ";
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
	
	function turnsUserList($fields,$condition)
	{
		$sorting_clause = 'u.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['sess_turns_user_name']) && $_SESSION['sess_turns_user_name'] != '')
			$condition .= " and (FirstName LIKE '%".trim($_SESSION['sess_turns_user_name'])."%' OR	LastName LIKE '%".trim($_SESSION['sess_turns_user_name'])."%' OR CONCAT( FirstName,  ' ', LastName ) LIKE  '%".trim($_SESSION['sess_turns_user_name'])."%')";

		if(isset($_SESSION['sess_turns_total']) && $_SESSION['sess_turns_total'] != '') {
			$having	= " having total_turns = ".$_SESSION['sess_turns_total']." ";
		} else {
			$having = "";
		}
		$sql = "select ".$fields." 
				from tournamentsplayed as tp
				left join users as u on (u.id = tp.fkUsersId)
				LEFT JOIN tournaments AS t ON ( tp.fkTournamentsId = t.id )
				WHERE 1 and ".$condition." 
				group by tp.fkUsersId,tp.fkTournamentsId ".$having."order by tp.id desc, tp.DatePlayed desc,tp.fkTournamentsId,tp.fkUsersId";//.$limit_clause;

		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function userTournamentCount($fields,$condition) {
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsTable} WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getPaymentHistory($fields,$condition)
	{
		$sorting_clause = ' id desc';
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." 
				from {$this->payHistTable} as ph
				WHERE 1 ".$condition." 
				group by Type,CoinType
				ORDER BY ".$sorting_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function paymentHistoryList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = 'id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." 
				from {$this->payHistTable} as ph
				WHERE 1 ".$condition." 
				ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function roundsUserList($fields,$condition)
	{
		$sorting_clause = 'u.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['sess_turns_user_name']) && $_SESSION['sess_turns_user_name'] != '')
			$condition .= " and (FirstName LIKE '%".trim($_SESSION['sess_turns_user_name'])."%' OR	LastName LIKE '%".trim($_SESSION['sess_turns_user_name'])."%' OR CONCAT( FirstName,  ' ', LastName ) LIKE  '%".trim($_SESSION['sess_turns_user_name'])."%')";

		if(isset($_SESSION['sess_turns_total']) && $_SESSION['sess_turns_total'] != '') {
			$having	= " having total_turns = ".$_SESSION['sess_turns_total']." ";
		} else {
			$having = "";
		}
		$sql = "select  ".$fields." 
				from eliminationplayer as ep LEFT JOIN tournamentsplayed as tp ON (tp.id = ep.fkTournamentsPlayedId AND ep.fkUsersId > 0  AND tp.fkUsersId = 0)
				left join users as u on (u.id = ep.fkUsersId) 
				LEFT JOIN tournaments AS t ON ( tp.fkTournamentsId = t.id ) 
				WHERE 1 and ".$condition." 
				group by ep.fkUsersId,tp.fkTournamentsId ".$having."order by ep.id desc, ep.DatePlayed desc, tp.fkTournamentsId,ep.fkUsersId";//.$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function turnLastDate($fields,$condition)
	{
		$sql	=	'SELECT '.$fields.' FROM tournamentsplayed as tp
					 WHERE '.$condition.' ORDER BY id DESC LIMIT 0,1';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function roundLastDate($fields,$condition)
	{
		$sql	=	'SELECT '.$fields.' FROM eliminationplayer as tp
					 WHERE '.$condition.' ORDER BY id DESC LIMIT 0,1';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getUserListDetail($fields,$condition)
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
			$condition .= " and (u.FirstName LIKE '%".trim($_SESSION['mgc_sess_user_name'])."%' OR	u.LastName LIKE '%".trim($_SESSION['mgc_sess_user_name'])."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".trim($_SESSION['mgc_sess_user_name'])."%')";

			if(isset($_SESSION['mgc_sess_email']) && $_SESSION['mgc_sess_email'] != '')
			$condition .= " and u.Email LIKE '%".$_SESSION['mgc_sess_email']."%' ";
			
			if(isset($_SESSION['mgc_sess_user_status']) && $_SESSION['mgc_sess_user_status'] != ''){
				if($_SESSION['mgc_sess_user_status'] == 0)
					$condition .= " and u.VerificationStatus = '".$_SESSION['mgc_sess_user_status']."' ";
				else 
					$condition .= " and u.Status = '".$_SESSION['mgc_sess_user_status']."'  and u.VerificationStatus = 1 ";	
			}
			
			if(isset($_SESSION['mgc_sess_user_registerdate']) && $_SESSION['mgc_sess_user_registerdate'] != '')
			$condition .= " and date(u.DateCreated) = '".$_SESSION['mgc_sess_user_registerdate']."'";	
		
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." 
				from {$this->userTable} as u LEFT JOIN tournaments as t ON (u.id=t.fkUsersId AND t.CreatedBy=1)
				WHERE 1 ".$condition." 
				group by u.id 
				ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function userTurnsAndRoundsList($fields,$condition) {
		$having	= "";
		if(isset($_SESSION['sess_turns_user_name']) && $_SESSION['sess_turns_user_name'] != '')
			$condition .= " and (FirstName LIKE '%".trim($_SESSION['sess_turns_user_name'])."%' OR LastName LIKE '%".trim($_SESSION['sess_turns_user_name'])."%' OR CONCAT( FirstName,  ' ', LastName ) LIKE '%".trim($_SESSION['sess_turns_user_name'])."%')";
		if(isset($_SESSION['sess_turns_total']) && $_SESSION['sess_turns_total'] != '') {
			$having	= "HAVING hs_total_turns = ".$_SESSION['sess_turns_total']." OR el_total_turns = ".$_SESSION['sess_turns_total'];
		}
		$sql = "select  ".$fields." 
							FROM tournamentsplayed as tp
							LEFT JOIN eliminationplayer as ep ON (tp.id = ep.fkTournamentsPlayedId AND ep.fkUsersId > 0  AND tp.fkUsersId = 0)
							LEFT JOIN users as u on (u.id = tp.fkUsersId OR u.id = ep.fkUsersId)
							LEFT JOIN tournaments AS t ON ( tp.fkTournamentsId = t.id )
							WHERE 1 and ".$condition." 
							GROUP BY tp.fkUsersId,ep.fkUsersId,tp.fkTournamentsId ".$having." ORDER BY tp.DatePlayed desc,ep.DatePlayed desc, tp.fkTournamentsId,tp.fkUsersId, ep.fkUsersId";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
}
?>