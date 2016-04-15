

<?php
class GameModel extends Model
{
	function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function getGameList($fields,$condition)	{
		$sql	=	' SELECT SQL_CALC_FOUND_ROWS '.$fields.' FROM games as g 
						WHERE 1 and Status = 1 '.$condition.' ORDER BY id DESC';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;		
	}
	function getTourGameList($fields,$condition)	{
		$sql	=	' SELECT SQL_CALC_FOUND_ROWS '.$fields.' FROM games as g 
						WHERE 1 and Status = 1 '.$condition.' ORDER BY Name ASC';
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;		
	}
	function GameList($fields,$condition)	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		if(isset($_SESSION['tilt_sess_game_name']) && $_SESSION['tilt_sess_game_name'] != '')
			$condition .= " and g.Name LIKE '%".$_SESSION['tilt_sess_game_name']."%' ";
		
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->gameTable} as g 
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
		if(isset($input['developerId'])	&&	$input['developerId']!= '')
			$sql	.=	' fkDevelopersId = 	"'.trim($input['developerId']).'",';
		if(isset($input['gamename'])	&&	$input['gamename']!= '')
			$sql	.=	' Name = 	"'.trim($input['gamename']).'",';
		if(isset($input['iosswitch'])	&&	$input['iosswitch']!= '')
			$sql	.=	' IosStatus = 	"1",';
		else
			$sql	.=	' IosStatus = 	"0",';
		if(isset($input['androidswitch'])	&&	$input['androidswitch']!= '')
			$sql	.=	' AndroidStatus = "1",';	
		else
			$sql	.=	' AndroidStatus = "0",';
		if(isset($tiltkey)	&&	$tiltkey!= '')
			$sql	.=	' TiltKey = "'.trim($tiltkey).'",';
		$sql	.=	' PlayTime = "01:00:00",';
		$sql	.=	' DevelopedBy = 1,Status = 1 , DateCreated = "'.$today.'", DateModified = "'.$today.'" ';
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function updateGameDetails($update_string,$condition){
		$sql	 =	"update games  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
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
	function selectWordDetails(){
		$sql	 =	"select * from {$this->wordsTable} where 1 order by rand() limit 1 ";
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function getTournamentList($fields,$condition)	{
		$limit_clause='';
		$sorting_clause = 't.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		if(isset($_SESSION['tilt_sess_report_tournament_name']) && $_SESSION['tilt_sess_report_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['tilt_sess_report_tournament_name']."%' ";
		if(isset($_SESSION['tilt_sess_report_developer_name']) && $_SESSION['tilt_sess_report_developer_name'] != '')
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['tilt_sess_report_developer_name']."%' OR	u.LastName LIKE '%".$_SESSION['tilt_sess_report_developer_name']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['tilt_sess_report_developer_name']."%' OR gd.Company LIKE '%".$_SESSION['tilt_sess_report_developer_name']."%' )";
		if(isset($_SESSION['tilt_sess_report_developer_type']) && $_SESSION['tilt_sess_report_developer_type'] != '')
			$condition .= " and t.CreatedBy = '".$_SESSION['tilt_sess_report_developer_type']."' ";
		
		$sql	=	' SELECT SQL_CALC_FOUND_ROWS '.$fields.' 
					FROM tournaments as t
					LEFT JOIN gamedevelopers as gd on (t.fkDevelopersId = gd.id AND t.CreatedBy = 3 AND gd.Status !=3)
					LEFT JOIN users as u on (t.fkUsersId = u.id AND t.CreatedBy = 1 AND u.Status !=3)
					WHERE 1 '.$condition.' ORDER BY '.$sorting_clause.' '.$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;		
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
	function updateGameRules($update_string,$id) {
		$sql	 =	"update {$this->GameRulesTable}  set ".$update_string." WHERE fkGamesId = ".$id;
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
}
?>