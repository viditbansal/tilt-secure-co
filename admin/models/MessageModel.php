
<?php
class MessageModel extends Model
{
   function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function selectMessageDetails($user_id){
		$sql	 =	" SELECT * FROM {$this->chatsTable} where fkTournamentsId=0 AND fkUsersId in (".$user_id.") or tofkUsersId in (".$user_id.") ";
		$result = 	$this->sqlQueryArray($sql); 
		if(is_array($result) && count($result) > 0  )
            return $result;
        else
            return FALSE;
	}
	
	function getUsersChatLists($from_user_id){
			$blockUserIds	=	'';
			$blockCondition = "";
			if($blockUserIds  != '')
				$blockCondition = " and fromUserId not in (".$blockUserIds.") and toUserId not in (".$blockUserIds.") ";
			else
				$blockUserIds 	= 0;
			$searchIds	=	'';
			$condition1	=	$condition2	=	$ids = $msg_resulting = '';
			if(isset($_SESSION['mgc_sess_chat_name'])	&&	$_SESSION['mgc_sess_chat_name']	!=''){
				$sqlSearch	=	" SELECT id FROM {$this->userTable} WHERE (FirstName LIKE '%".trim($_SESSION['mgc_sess_chat_name'])."%' OR LastName LIKE '%".trim($_SESSION['mgc_sess_chat_name'])."%' OR CONCAT( FirstName,  ' ', LastName ) LIKE  '%".trim($_SESSION['mgc_sess_chat_name'])."%' )";
				
				$search_result	=  $this->sqlQueryArray($sqlSearch);
				if(isset($search_result) && is_array($search_result) && count($search_result) > 0 ){
					foreach($search_result as $searchkey=>$searchId){
						$searchIds .= $searchId->id.",";
					}
					$searchIds	=	rtrim($searchIds,',');
					if(isset($searchIds)	&&	$searchIds	!=''){
						$condition1	=	"AND tofkUsersId IN	(".$searchIds.") ";
						$condition2	=	"AND fkUsersId IN	(".$searchIds.") ";
					}
				}
				else {
					$condition1	=	"AND tofkUsersId IN	(0) ";
					$condition2	=	"AND fkUsersId IN	(0) ";
				}
				
			}
			
			
			$sql1 = "SELECT DISTINCT `tofkUsersId` as sent_to_ids FROM {$this->chatsTable} WHERE `fkUsersId` = ".$from_user_id." and tofkUsersId not in (".$blockUserIds.") AND fkTournamentsId=0 ".$condition1;
			$sql_result1	=  $this->sqlQueryArray($sql1);
			if(isset($sql_result1) && is_array($sql_result1) && count($sql_result1) > 0 ){
				foreach($sql_result1 as $sentkey=>$sentvalue){
					$ids .= $sentvalue->sent_to_ids.",";
				}
			}	
			$sql2 = "SELECT DISTINCT `fkUsersId` as sent_from_ids FROM {$this->chatsTable} WHERE `tofkUsersId` = ".$from_user_id." and fkUsersId not in (".$blockUserIds.")  AND fkTournamentsId=0 ".$condition2;
			$sql_result2	=  $this->sqlQueryArray($sql2);		
			if(isset($sql_result2) && is_array($sql_result2) && count($sql_result2) > 0 ){
				foreach($sql_result2 as $fromkey=>$fromvalue){
					$ids .= $fromvalue->sent_from_ids.",";
				}
			}	
			$id_array = explode(',',rtrim($ids,','));
			$result = array_unique($id_array);
			if(is_array($result) && count($result) > 0){
				foreach($result as $key=>$value){
					if($value !='' && $value !='0'){
						$msg_query = "select c.*,u.FirstName,u.id as user_id,u.UniqueUserId,u.id as userId,u.LastName,u.DateModified,u.Photo,c.DateCreated as message_sent_date
									  from {$this->chatsTable} as c
									  left join {$this->userTable} as u on (u.id =  ".$value." or u.id = ".$from_user_id.")
									  where (`fkUsersId`=  ".$from_user_id ." and  `tofkUsersId` = ".$value."  and u.id =  ".$value."  ) or (`tofkUsersId`=  ".$from_user_id ." and  `fkUsersId` = ".$value." and u.id =  ".$value." ) and u.id != ' '  AND fkTournamentsId=0 and u.Status in (1,2) order by c.id desc limit 0,1"; 
						$resulting =  $this->sqlQueryArray($msg_query);
						if(is_array($resulting) && count($resulting) > 0){
						$resulting = $resulting[0];
							$msg_resulting[] = $resulting;
						}
					}
				}
			}
			else{
				$msg_resulting = '';
			}
		return $msg_resulting;
	}
	function messageLists($from_user_id,$to_user_id){
		$condition = '';
		$limit_clause	=	'';
		$sorting_clause = 'DateCreated asc';
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
      	if($from_user_id !='' && $to_user_id !='') { 
			$message_sql = "Select SQL_CALC_FOUND_ROWS c.* ,u.DateModified as user_modify,u.FirstName,u.UniqueUserId,u.id as userId ,u.LastName,u.Photo,c.DateCreated as message_sent_date 
									from {$this->chatsTable} as c
									 left join {$this->userTable} as u on (u.id =  ".$to_user_id." or u.id = ".$from_user_id.") 
									 where (c.`fkUsersId`= ".$from_user_id." and  c.`tofkUsersId`= ".$to_user_id." and u.id =  ".$from_user_id.") or (c.fkUsersId = ".$to_user_id." and  c.`tofkUsersId`= ".$from_user_id." and u.id = ".$to_user_id.")  AND fkTournamentsId=0 and u.Status in (1,2) 
									 order by ".$sorting_clause." ".$limit_clause;
			$message_result	=  $this->sqlQueryArray($message_sql);
			if(is_array($message_result) && count($message_result) > 0  )
	            return $message_result;
	        else
	            return FALSE;
		}
		else{
			return FALSE;
		}
	}
	function updateMessageReadStatus($to_user_id,$from_user_id){
		$condtion  = "(`fromUserId` = '".$from_user_id."' and `toUserId` =  '".$to_user_id."' ) or (`fromUserId` = '".$to_user_id."' and `toUserId` =  '".$from_user_id."' )"; 
		$sql       = "update {$this->messageTable} set ReadStatus = '1' where ".$condtion;
		$result	   =  $this->updateInto($sql);
		if(is_array($result) && count($result) > 0  )
            return $result;
        else
            return FALSE;
	}
	
	function getBlockedIds($userId){
		/* Start : newly added for blocked user */
		$blockUserIds 	= ''; 
		$sql	    = "select * from contact where (fkUserId ='".$userId."' or ContactId ='".$userId."') and ContactType = 2 ";
		$blockArray = 	$this->sqlQueryArray($sql); 
		if($blockArray){
			foreach($blockArray as $key=>$value){
				if($value->fkUserId != $userId)
					$blockUserIds .= $value->fkUserId.',';
				if($value->ContactId != $userId)
					$blockUserIds .= $value->ContactId.',';
			}
		}
		if($blockUserIds != '')
		{
			$blockUserIds = rtrim($blockUserIds,',');
		}
		return $blockUserIds;
		/* End : newly added for blocked user */
	}
}
?>