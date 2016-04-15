<?php
class TournamentModel extends Model
{
	function selectTournament($fields,$condition) {
		$sql	=	" SELECT ".$fields." FROM {$this->tournamentsTable} WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertTournamentDetails($post_values)
	{
		$sql	 =	"insert into  {$this->tournamentsTable}  set ";
		if(isset($post_values['tournament_name'])	&&	trim($post_values['tournament_name']!=""))
			$sql	.=	"TournamentName 	= 	'".trim($post_values['tournament_name'])."',";
		if(isset($post_values['tournamentType'])	&&	trim($post_values['tournamentType']!=""))
			$sql	.=	"TournamentType 	= 	'".trim($post_values['tournamentType'])."',";
		if(isset($post_values['fkDevelopersId'])	&&	trim($post_values['fkDevelopersId']!=""))
			$sql	.=  "fkDevelopersId			=	'".trim($post_values['fkDevelopersId'])."',";
		if(isset($post_values['game_id'])	&&	trim($post_values['game_id']!=""))
			$sql	.=  "fkGamesId			=	'".trim($post_values['game_id'])."',";
		if(isset($post_values['tilt_prize'])	&&	trim($post_values['tilt_prize']!=""))
			$sql 	.=	"Prize 				= 	'".trim($post_values['tilt_prize'])."',";
		$sql 	.=	"GameType 			= 	'1',";
		if(isset($post_values['start_time'])	&&	trim($post_values['start_time'])!=""){
			$sql	.=	"StartDate	=	'".date('Y-m-d H:i',strtotime($post_values['start_time']))."',";
			$sql	.=	"StartTime	=	'".date('Y-m-d H:i',strtotime($post_values['start_time']))."',";
		}
		else if(isset($post_values['tournamentType'])	&&	trim($post_values['tournamentType']== 2 ))
		{
			$sql	.=	"StartDate	=	'2038-01-01 03:14:07',"; // till 19th day
			$sql	.=	"StartTime	=	'2038-01-01 03:14:07',";
		}
		if(isset($post_values['end_time'])	&&	trim($post_values['end_time'])!=""){
			$sql	.=	"EndDate	=	'".date('Y-m-d H:i',strtotime($post_values['end_time']))."',";
			$sql	.=	"EndTime	=	'".date('Y-m-d H:i',strtotime($post_values['end_time']))."',";
		}
		else if(isset($post_values['tournamentType'])	&&	trim($post_values['tournamentType']== 2 ))
		{
			$sql	.=	"EndDate	=	'2038-01-02 03:14:07',";
			$sql	.=	"EndTime	=	'2038-01-02 03:14:07',";
		}
		if(isset($post_values['gen_terms_condition'])	&& $post_values['gen_terms_condition'] !='')
			$sql	.=	" TermsAndCondition =	'".$post_values['gen_terms_condition']."', ";
		if(isset($post_values['gen_game_rules'])	&& $post_values['gen_game_rules'] !='')
			$sql	.=	" TournamentRule =	'".$post_values['gen_game_rules']."', ";
		if(isset($post_values['gen_tournmentrules'])	&& $post_values['gen_tournmentrules'] !='')
			$sql	.=	" GftRules =	'".$post_values['gen_tournmentrules']."', ";
		if(isset($post_values['gen_privacy_policy_tab'])	&& $post_values['gen_privacy_policy_tab'] !='')
			$sql	.=	" PrivacyPolicy =	'".$post_values['gen_privacy_policy_tab']."', ";

		if(isset($post_values['requiresdownloadpartnersapp'])	&& $post_values['requiresdownloadpartnersapp'] !='')
			$sql	.=	" requiresdownloadpartnersapp =	'".$post_values['requiresdownloadpartnersapp']."', ";
		if(isset($post_values['partnerappiospackageid'])	&& $post_values['partnerappiospackageid'] !='')
			$sql	.=	" partnerappiospackageid =	'".$post_values['partnerappiospackageid']."', ";
		if(isset($post_values['partnerappandroidpackageid'])	&& $post_values['partnerappandroidpackageid'] !='')
			$sql	.=	" partnerappandroidpackageid =	'".$post_values['partnerappandroidpackageid']."', ";
		if(isset($post_values['partnerappiosurl'])	&& $post_values['partnerappiosurl'] !='')
			$sql	.=	" partnerappiosurl =	'".$post_values['partnerappiosurl']."', ";
		if(isset($post_values['partnerappandroidurl'])	&& $post_values['partnerappandroidurl'] !='')
			$sql	.=	" partnerappandroidurl =	'".$post_values['partnerappandroidurl']."', ";
		// if(isset($post_values['marquee'])	&& $post_values['marquee'] !='')
		// 	$sql	.=	" marquee =	'".$post_values['marquee']."', ";

		$sql	.=	"MaxPlayers		=	'100',";
		$sql	.=	"TotalTurns		=	'3',";
		$sql 	.=	"Type 			= 	'2',";
		$sql	.=	"FeeType 		= 	'1', ";
		$sql 	.=	"Status 		= 	1,
					CreatedBy 		=	3,
					DateCreated 	= 	'".date('Y-m-d H:i:s')."',
					DateModified	= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		return $this->sqlInsertId();
	}
	function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function updateTournamentDetail($updateString,$condition)	{
		$sql	=	" Update {$this->tournamentsTable} set ".$updateString." where ".$condition;
		$this->updateInto($sql);
	}
	function getTournamentList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' t.id desc';
		$groupBy	=	" t.id ";
		if(!empty($_SESSION['ordertype'])){
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
			if($_SESSION['orderby'] == 'Prize1'){
				$sorting_clause = 'Type ASC, Prize '. $_SESSION['ordertype'];
			}
			if($_SESSION['orderby'] == 'Prize2'){
				$sorting_clause = 'Type DESC,Prize '. $_SESSION['ordertype'];
			}
		}
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType'])){
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		}
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['tilt_sess_tournament_name']) && $_SESSION['tilt_sess_tournament_name'] != '')
			$condition .= " and t.TournamentName LIKE '%".$_SESSION['tilt_sess_tournament_name']."%' ";
		if(isset($_SESSION['tilt_sess_tournament_game']) && $_SESSION['tilt_sess_tournament_game'] != '')
			$condition .= " and g.Name LIKE '%".$_SESSION['tilt_sess_tournament_game']."%' ";

		if(isset($_SESSION['tilt_sess_tournament_fromDate']) && $_SESSION['tilt_sess_tournament_fromDate'] != ''	&&	isset($_SESSION['tilt_sess_tournament_endDate']) && $_SESSION['tilt_sess_tournament_endDate'] != ''){
			$condition .= " AND ((date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tournament_fromDate']))."' and date(t.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tournament_endDate']))."') ) ";
		}
		else if(isset($_SESSION['tilt_sess_tournament_fromDate']) && $_SESSION['tilt_sess_tournament_fromDate'] != '')
			$condition .= " AND date(t.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tournament_fromDate']))."'";
		else if(isset($_SESSION['tilt_sess_tournament_endDate']) && $_SESSION['tilt_sess_tournament_endDate'] != '')
			$condition .= " AND date(t.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['tilt_sess_tournament_endDate']))."'";


		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->tournamentsTable} as t
						LEFT JOIN {$this->gameTable} as g on (t.fkGamesId=g.id)
						LEFT JOIN {$this->tournamentsPlayedTable} as tp ON (tp.fkTournamentsId=t.id)
						WHERE 1 ".$condition." GROUP BY ".$groupBy." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function selectTournamentDetails($fields,$condition) {
		$sql	=	"	SELECT ".$fields." FROM {$this->tournamentsTable} as t WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectCountryDetails($fields,$condition) {
		$sql	=	"	SELECT ".$fields." FROM {$this->countriesTable} as c
						WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertCouponBannerDetails($post_values){
		$sql	 =	"insert into  {$this->tournamentCoupAdLinkTable}  set ";
		if(isset($post_values['coupon_code'])	&&	trim($post_values['coupon_code']!=""))
			$sql	.=	"CouponCode 	= 	'".$post_values['coupon_code']."',";
		if(isset($post_values['youtube_link'])	&&	trim($post_values['youtube_link']!=""))
			$sql	.=	"YoutubeLink 	= 	'".$post_values['youtube_link']."',";
		$sql 	.=	" Status 			= 	1,
						CreatedBy 		=	3,
						DateCreated 	= 	'".date('Y-m-d H:i:s')."',
						DateModified	= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function insertCouponBannerLink($queryString){
		$sql	 =	" INSERT INTO {$this->tournamentCoupAdLinkTable} ( fkTournamentsId,fkBrandsId,CouponAdLink,URL,File,CouponTitle,CouponStartDate,CouponEndDate,Type,InputType,Status,DateCreated,DateModified,CouponLimit) ".$queryString;
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function insertPinCode($values){
		$sql	 =	" INSERT INTO {$this->pinStatsTable} ( Pincode,DateCreated,Status,fkTournamentsId,fkUsersId) ".$values;
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function updateCouponBannerLink($updateString,$condition)	{
		$sql	=	" Update {$this->tournamentCoupAdLinkTable} set ".$updateString." where ".$condition;
		$this->updateInto($sql);
	}
	function updatePinCode($updateString,$condition)	{
		$sql	=	" Update {$this->pinStatsTable} set ".$updateString." where ".$condition;
		$this->updateInto($sql);
	}
	function checkCouponBannerLink($fields,$condition) {
		$sql	=	"	SELECT ".$fields." FROM {$this->tournamentCoupAdLinkTable}
						WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function checkPinCode($fields,$condition) {
		$sql	=	"	SELECT ".$fields." FROM {$this->pinStatsTable}
						WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertCustomPrize($queryString){
		$sql	 =	" INSERT INTO {$this->customPrizeTable} (fkBrandsId,fkDevelopersId,fkTournamentsId,PrizeTitle,PrizeImage,PrizeDescription,PrizeOrder,Status,DateCreated,DateModified) ".$queryString;
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function updateCustomPrizeDetails($updateString,$condition)	{
		$sql	=	" Update {$this->customPrizeTable} set ".$updateString." where ".$condition;
		$this->updateInto($sql);
	}
	function updateCustomAdDetails($sql)	{
		$this->updateInto($sql);
	}
	function getCustomPrizeDetails($fields,$condition) {
		$sql	=	"	SELECT ".$fields." FROM {$this->customPrizeTable}
						WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getCustomAdDetails($fields,$condition) {
		$sql	=	"	SELECT ".$fields." FROM tournamentcustomad
						WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getMgcBackgroundImageDetails($fields,$condition) {
		$sql	=	"	SELECT ".$fields." FROM tournamentbackgroundimage
						WHERE ".$condition;
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
	function deleteTournaments($delete_id){
		$update_string 	= " Status = 3,DateModified ='".date('Y-m-d H:i')."' ";
		$condition 		= " id IN(".$delete_id.") ";
		$sql	 =	"update {$this->tournamentsTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
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
	function checkLocationRestriction($fields,$condition) {
		$sql	=	"	SELECT ".$fields." FROM {$this->locRestrictTable}
						WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getEliminationPlayed($fields,$condition)
	{
		$sql	=	" SELECT ".$fields." FROM `tournamentsplayed` as tp
		LEFT JOIN  eliminationplayer as ep on (tp.id = ep.fkTournamentsPlayedId)
		WHERE 1 ".$condition." GROUP BY `fkTournamentsId`";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function getElimPlayersCount($field,$condition){
		$sql	 =	"select ".$field." from {$this->eliminationTable} where ".$condition;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function insertRestrictedLocation($fields)
	{
		$sql	=	" insert into {$this->locationRestrictionTable}  set ".$fields.", Status=1, DateCreated='".date('Y-m-d H:i:s')."', DateModified='".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	}
	function getRestrictedLocation($condition)
	{
		$sql	=	" SELECT SQL_CALC_FOUND_ROWS * FROM {$this->locationRestrictionTable} where 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);

		if(count($result) == 0) return false;
		else return $result;
	}
	function deleteRestrictedLocation($con)
	{
		$upsql	=	"update {$this->locationRestrictionTable} set Status=2 where id not in (".$ids.") and ".$con;
		$this->updateInto($upsql);
	}
	function updateRestrictedLocation($fields,$con)
	{
		$upsql	=	"update {$this->locationRestrictionTable} set ".$fields." where ".$con;
		$this->updateInto($upsql);
	}
	function getPinCode($fields,$condition) {
		$sql	=	"SELECT ".$fields." FROM {$this->pinStatsTable} WHERE ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		return $result;
	}
	function selectPinCode($fields,$condition) {
		$sql	=	"SELECT ".$fields." FROM {$this->pinStatsTable} WHERE ".$condition." LIMIT 0,10";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	// -------- generate pin for tournament -------------
	function generateTournamentPins($tournamentsId){
		$remain	=	$rem	=	10;
		$fields			=	" PinCode ";
		$condition		=	" fkTournamentsId	=	".$tournamentsId." AND Status = 0 ";
		$unUsedPincode	=	$this->selectPinCode($fields,$condition);
		if(isset($unUsedPincode)	&&	is_array($unUsedPincode) &&	count($unUsedPincode)>0) ;
		else{
			if($remain>0){
				$replaceArray	=	array();
				while($remain>0){

					$j	=	1;
					$tempCodes	=	"";
					while($j<=$remain){
						$passphrase = getPassphrase(PIN_LENGTH);
						while(in_array($passphrase, $replaceArray))
							$passphrase = getPassphrase(PIN_LENGTH);
						$tempArray[] = $passphrase;
						$tempCodes	.=	"'".$passphrase."',";
						$j++;
					}
					if($tempCodes	!=''){
						$fields			=	" PinCode ";
						$condition		=	" PinCode IN (".rtrim($tempCodes,',').") ";
						$pincodeResult	=	$this->getPinCode($fields,$condition);
					}
					if(isset($pincodeResult)	&&	is_array($pincodeResult) &&	count($pincodeResult)>0){
						foreach($pincodeResult as $pin){
							if(in_array($pin, $tempArray))	;
							else $replaceArray[]	=	$pin;
						}
					}
					else {

						foreach($tempArray as $pin){
							 $replaceArray[]	=	$pin;
						}
					}
					$tot	=	count($replaceArray);
					$remain	=	$rem - $tot;
				}
				$today			=	date('Y-m-d');
				$values			=	" VALUES ";
				foreach($replaceArray as $key2 => $pincodes){
					$values		.=	"('".$pincodes."','".$today."',0,".$tournamentsId.",0),";
				}
				$values	=	rtrim($values,',');
				$this->insertPinCode($values);
			}
		}
	}
	//-------------------- generate pin for tournament -------------
}
?>
