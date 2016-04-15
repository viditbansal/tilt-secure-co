<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/BrandController.php');
$brandObj   =   new BrandController();
require_once('controllers/GameController.php');
$gameObj   =   new GameController();
$display   =   'none';
$class  =  $msg    = $cover_path = $tournamentIds = '' ;
$updateStatus	=	1;
top_header();
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_global_report_start']);
	unset($_SESSION['mgc_sess_global_report_end']);
	unset($_SESSION['mgc_sess_global_report_game']);
	unset($_SESSION['mgc_sess_global_report_brand']);
	unset($_SESSION['mgc_sess_global_report_dev']);
	if(isset($_SESSION['mgc_ses_from_timeZone']))
		unset($_SESSION['mgc_ses_from_timeZone']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['game']))
		$_SESSION['mgc_sess_global_report_game'] 	=	$_POST['game'];
	
	if(isset($_POST['brand']))
		$_SESSION['mgc_sess_global_report_brand'] 	=	$_POST['brand'];
	if(isset($_POST['developer']))
		$_SESSION['mgc_sess_global_report_dev'] 	=	$_POST['developer'];
		
	if(isset($_POST['startdate']) && $_POST['startdate'] != ''){
	
		$validate_date = dateValidation($_POST['startdate']);
		if($validate_date == 1)
			$_SESSION['mgc_sess_global_report_start']	= $_POST['startdate'];	//$date;
		else 
			$_SESSION['mgc_sess_global_report_start']	= '';
	}
	else 
		$_SESSION['mgc_sess_global_report_start']	= '';

	if(isset($_POST['enddate']) && $_POST['enddate'] != ''){
		$validate_date = dateValidation($_POST['enddate']);
		if($validate_date == 1)
			$_SESSION['mgc_sess_global_report_end']	= $_POST['enddate'];	//$date;
		else 
			$_SESSION['mgc_sess_global_report_end']	= '';
	}
	else 
		$_SESSION['mgc_sess_global_report_end']	= '';
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$game_fields		=	"*";
$game_condition	=	" AND g.Status = 1 ";
$gameList	=	$gameObj->getGameArray($game_fields,$game_condition);

$fields		=	"*";
$condition	=	" AND b.Status = 1 ";
$brandList	=	$gameObj->getBrandArray($fields,$condition);
$fields = " id,Name";
$condition	=	" Status = 1 ";
$devList	=	$gameObj->gameDeveloperDetails($fields,$condition);
$brandField			= "t.id as tournamentId,t.CreatedBy";
$brandCondition		= "";
$tourIds = '';
$tourUserCount = 0;
$searchBrand		= $gameObj->getBrandUser($brandField,$brandCondition);
$tournamentprocess	= $brandTourIds	= $userTourIds	= $deveTourIds	= array();
if(isset($searchBrand) && is_array($searchBrand) && count($searchBrand) >0){
	$tournamentIds = '';
	foreach($searchBrand as $scankey=>$scanvalue){
		$tournamentprocess[$scankey] = (array)$scanvalue;
		if($scanvalue->tournamentId != ''){
			$tournamentIds .= $scanvalue->tournamentId.',';
			//CreatedBy -> 0-Brand,1-User created, 3-Game Developer
			if($scanvalue->CreatedBy != '' && $scanvalue->CreatedBy == 0)	{ $brandTourIds[] = $scanvalue->tournamentId; } 
			else if($scanvalue->CreatedBy != '' && $scanvalue->CreatedBy == 1){ $userTourIds[] = $scanvalue->tournamentId; } 
			else if($scanvalue->CreatedBy != '' && $scanvalue->CreatedBy == 3){ $deveTourIds[] = $scanvalue->tournamentId; } 
		}
	}
	$tournamentIds	=	trim($tournamentIds,',');
}
/*--total user based on tournaments played----*/
if($tournamentIds != '' ){
	$gameUserField 		= "tp.id,count(distinct u.id) as userCount, GROUP_CONCAT(distinct tp.fkTournamentsId) as tourIds ";
	if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '' && isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''){
		$gameUserCondition	= " and ( t.fkBrandsId = ".$_SESSION['mgc_sess_global_report_brand']." OR t.fkDevelopersId = ".$_SESSION['mgc_sess_global_report_dev']." ) ";
	}
	else if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != ''){
		$gameUserCondition	= " and t.fkBrandsId = ".$_SESSION['mgc_sess_global_report_brand']." ";
	}
	else if(isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''){
		$gameUserCondition	= " and t.fkDevelopersId = ".$_SESSION['mgc_sess_global_report_dev']." ";
	}
	else{
		$gameUserCondition	= " and tp.fkTournamentsId in (".$tournamentIds.")";
	}
	$gameUserCondition	.= " AND tp.fkTournamentsId !='' AND ((ep.fkUsersId !='') OR (tp.fkUsersId !=0))";
	$searchGame			= $gameObj->getTournamentUsers($gameUserField,$gameUserCondition);
	if(isset($searchGame) && is_array($searchGame) && count($searchGame)>0){
		$tourUserCount = $searchGame[0]->userCount;
		if(isset($searchGame[0]->tourIds) && $searchGame[0]->tourIds !='')
			$tourIds = $searchGame[0]->tourIds;
	}
	//$searchGame			= $gameObj->getGameUser($gameUserField,$gameUserCondition);
}
$tiltWonUserCount =  $virtualWonUserCount = $customWonUserCount = 0;
$brandTotalPrize = $brandVirtualCoinsPrize =  $brandCustomPrize = 0;
$devTotalPrize = $devVirtualCoinsPrize =  0;
$brandTiltWinCount = $vcWonUserCount = 0;
$userCountArray = $tiltUserCountArray = $vcUserCountArray = $customUserCountArray = array();
/*---winners count----*/
if($tournamentIds != '' ){
	$winsUserField 		= "*";
	$winsCondition		= "and tp.fkTournamentsId in (".$tournamentIds.") ";
	$winid				= $gameObj->winnerIds($winsUserField,$winsCondition);
	$winprocess	= array();
	if(isset($winid) && is_array($winid) && count($winid) >0){
		$winIds = '';
		foreach($winid as $scankey=>$scanvalue){
			$winprocess[$scankey] = (array)$scanvalue;
			if($scanvalue->fkTournamentsId != '')
				$winIds .= $scanvalue->fkTournamentsId.',';
		}
		$winIds	=	trim($winIds,',');
		if($winIds != ''){
			$winIdsArray = explode(',', $winIds);
			$winIdsArray = array_unique($winIdsArray);
			$winIds = implode($winIdsArray, ',');
		}
		if((((!isset($_SESSION['mgc_sess_global_report_dev']) || $_SESSION['mgc_sess_global_report_dev'] == '') && 
			(!isset($_SESSION['mgc_sess_global_report_brand']) || $_SESSION['mgc_sess_global_report_brand'] == ''))) || ( isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '' && isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')){
			$winIds = $tourIds ;
		}
		if(isset($winIds) && !empty($winIds)){
		//Update on 13-01-2015
		//BEGIN : Brand Details
		$winsCusGame = $winsVirGame  = $winsGame = array();
			$winField 		= "ts.id,ts.fkUsersId, (CASE WHEN t.Type = 2 THEN count(distinct ts.fkUsersId) END) as TiltWinCount, (CASE WHEN t.Type = 2 THEN sum(ts.Prize) END) as PrizeMoney,GROUP_CONCAT(DISTINCT ts.fkUsersId,'') as tiltUsersWin";
			$winCond		= "and ts.fkTournamentsId in (".$winIds.") and ts.Status = 1 AND t.Type = 2 AND t.CreatedBY = 0 ";
			if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '')
				$winCond .= " and fkBrandsId = ".$_SESSION['mgc_sess_global_report_brand'];
			$winsGame		= $gameObj->userWinnerCount($winField,$winCond);
			
			$winVirField 		= "(CASE WHEN t.Type = 3 THEN count(distinct ts.fkUsersId) END) as VirtualWinCount,sum(ts.Prize)  as PrizeMoney,GROUP_CONCAT(DISTINCT ts.fkUsersId,'') as vcUsersWin";
			$winVirCond		= "and ts.fkTournamentsId in (".$winIds.") and ts.Status = 1 AND t.Type = 3  AND t.CreatedBY = 0 ";
			if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '')
				$winVirCond .= " and fkBrandsId = ".$_SESSION['mgc_sess_global_report_brand'];
			$winsVirGame		= $gameObj->userWinnerCount($winVirField,$winVirCond);
			
			$winVirCond		= "and ts.fkTournamentsId in (".$winIds.") and ts.Status = 1 AND t.Type = 4  AND t.CreatedBY = 0 ";
			if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '')
				$winVirCond .= " and fkBrandsId = ".$_SESSION['mgc_sess_global_report_brand'];
			$winCusField 		= "(CASE WHEN t.Type = 4 THEN count(distinct ts.fkUsersId) END) as CustomWinCount,GROUP_CONCAT(DISTINCT ts.fkUsersId,'') as customUsersWin";
			$winsCusGame		= $gameObj->userWinnerCount($winCusField,$winVirCond);
			if(isset($winsGame) && is_array($winsGame) && count($winsGame) > 0 ) { 
				if(isset($winsGame[0]->PrizeMoney) && $winsGame[0]->PrizeMoney!='') 
					$brandTotalPrize = $winsGame[0]->PrizeMoney;
				//if(!isset($_SESSION['mgc_sess_global_report_dev']) || $_SESSION['mgc_sess_global_report_dev'] == ''){
				if((!isset($_SESSION['mgc_sess_global_report_dev']) || $_SESSION['mgc_sess_global_report_dev'] == '') || ( isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '' && isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')){
					if(isset($winsGame[0]->tiltUsersWin) && $winsGame[0]->tiltUsersWin !='')	{
						$userArray = array();
						$userArray =  explode(',', $winsGame[0]->tiltUsersWin);
						$tiltUserCountArray = array_unique(array_merge($tiltUserCountArray,$userArray));
					}
				}
			}
			if(1){ echo '<br>======111=====><pre>';print_r($userArray);echo '</pre>'; }
			if(isset($winsVirGame) && is_array($winsVirGame) && count($winsVirGame) > 0 ) { 
				if(isset($winsVirGame[0]->PrizeMoney) && $winsVirGame[0]->PrizeMoney!='') 
					$brandVirtualCoinsPrize = $winsVirGame[0]->PrizeMoney;
				//if(!isset($_SESSION['mgc_sess_global_report_dev']) || $_SESSION['mgc_sess_global_report_dev'] == ''){
				if((!isset($_SESSION['mgc_sess_global_report_dev']) || $_SESSION['mgc_sess_global_report_dev'] == '') || ( isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '' && isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')){
					if(isset($winsVirGame[0]->vcUsersWin) && $winsVirGame[0]->vcUsersWin !='')	{
						$userArray = array();
						$userArray =  explode(',', $winsVirGame[0]->vcUsersWin);
						$vcUserCountArray = array_unique(array_merge($vcUserCountArray,$userArray));
					}
				}
			}
			if(1){ echo '<br>=====222======><pre>';print_r($userArray);echo '</pre>'; }
			//if(!isset($_SESSION['mgc_sess_global_report_dev']) || $_SESSION['mgc_sess_global_report_dev'] == ''){
			if((!isset($_SESSION['mgc_sess_global_report_dev']) || $_SESSION['mgc_sess_global_report_dev'] == '') || ( isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '' && isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')){
				if(isset($winsCusGame) && is_array($winsCusGame) && count($winsCusGame) > 0 ) { 
					if(isset($winsCusGame[0]->customUsersWin) && $winsCusGame[0]->customUsersWin !='')	{
						$userArray = array();
						$userArray =  explode(',', $winsCusGame[0]->customUsersWin);
						$customUserCountArray = array_unique(array_merge($customUserCountArray,$userArray));
					}
				}
			}
			if(1){ echo '<br>=====333======><pre>';print_r($userArray);echo '</pre>'; }
			if(1) echo '<br>'.__LINE__.'---111----'.count($tiltUserCountArray).'------';
			if(1) echo '<br>'.__LINE__.'---222----'.count($vcUserCountArray).'------';
			if(1) echo '<br>'.__LINE__.'---333----'.count($customUserCountArray).'------';
		//END : Brand Details
		//BEGIN : GameDeveloper Details
		$winsCusGame = $winsVirGame  = $winsGame = array();
			$winField 		= "ts.id,ts.fkUsersId, (CASE WHEN t.Type = 2 THEN count(distinct ts.fkUsersId) END) as TiltWinCount, (CASE WHEN t.Type = 2 THEN sum(ts.Prize) END) as PrizeMoney,GROUP_CONCAT(DISTINCT ts.fkUsersId,'') as tiltUsersWin";
				$winCond		= " and ts.Status = 1 AND t.Type = 2 AND t.CreatedBY = 3 ";
			if((isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')){ 
				$winCond		.= " AND t.fkDevelopersId = ".$_SESSION['mgc_sess_global_report_dev']." ";
			}
			else $winCond		.= " and ts.fkTournamentsId in (".$winIds.")";
			$winsGame		= $gameObj->userWinnerCount($winField,$winCond);
			
			$winVirField 		= "(CASE WHEN t.Type = 3 THEN count(distinct ts.fkUsersId) END) as VirtualWinCount,sum(ts.Prize)  as PrizeMoney,GROUP_CONCAT(DISTINCT ts.fkUsersId,'') as vcUsersWin";
			$winVirCond		= " and ts.Status = 1 AND t.Type = 3  AND t.CreatedBY = 3 ";
			if((isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')){
				$winVirCond		.= " AND t.fkDevelopersId = ".$_SESSION['mgc_sess_global_report_dev']." ";
			}else
				$winVirCond		.= " and ts.fkTournamentsId in (".$winIds.")";
			$winsVirGame		= $gameObj->userWinnerCount($winVirField,$winVirCond);
			//if(!isset($_SESSION['mgc_sess_global_report_brand']) || $_SESSION['mgc_sess_global_report_brand'] == ''){
			if((!isset($_SESSION['mgc_sess_global_report_brand']) || $_SESSION['mgc_sess_global_report_brand'] == '') || ( isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '' && isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')){
				$winCustomCond		= " and ts.Status = 1 AND t.Type = 4  AND t.CreatedBY = 3 ";
				if((isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''))
					$winCustomCond	.= " AND t.fkDevelopersId = ".$_SESSION['mgc_sess_global_report_dev']." ";
				else $winCustomCond	.= " and ts.fkTournamentsId in (".$winIds.") ";
				$winCusField 		= "(CASE WHEN t.Type = 4 THEN count(distinct ts.fkUsersId) END) as CustomWinCount,GROUP_CONCAT(DISTINCT ts.fkUsersId,'') as customUsersWin";
				$winsCusGame		= $gameObj->userWinnerCount($winCusField,$winCustomCond);
			}
			if(isset($winsGame) && is_array($winsGame) && count($winsGame) > 0 ) { 
				if(isset($winsGame[0]->PrizeMoney) && $winsGame[0]->PrizeMoney!='') 
					$devTotalPrize = $winsGame[0]->PrizeMoney;
				//if(!isset($_SESSION['mgc_sess_global_report_brand']) || $_SESSION['mgc_sess_global_report_brand'] == ''){
				if((!isset($_SESSION['mgc_sess_global_report_brand']) || $_SESSION['mgc_sess_global_report_brand'] == '') || ( isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '' && isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')){
					if(isset($winsGame[0]->tiltUsersWin) && $winsGame[0]->tiltUsersWin !='')	{
						$userArray = array();
						$userArray =  explode(',', $winsGame[0]->tiltUsersWin);
						$tiltUserCountArray = array_unique(array_merge($tiltUserCountArray,$userArray));
					}
				}
			}
			if(1){ echo '<br>======4444=====><pre>';print_r($userArray);echo '</pre>'; }
			if(isset($winsVirGame) && is_array($winsVirGame) && count($winsVirGame) > 0 ) { 
				if(isset($winsVirGame[0]->PrizeMoney) && $winsVirGame[0]->PrizeMoney!='') 
					$devVirtualCoinsPrize = $winsVirGame[0]->PrizeMoney;
				//if(!isset($_SESSION['mgc_sess_global_report_brand']) || $_SESSION['mgc_sess_global_report_brand'] == ''){
				if((!isset($_SESSION['mgc_sess_global_report_brand']) || $_SESSION['mgc_sess_global_report_brand'] == '') || ( isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '' && isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')){
					if(isset($winsVirGame[0]->vcUsersWin) && $winsVirGame[0]->vcUsersWin !='')	{
						$userArray = array();
						$userArray =  explode(',', $winsVirGame[0]->vcUsersWin);
						$vcUserCountArray = array_unique(array_merge($vcUserCountArray,$userArray));
					}
				}
			}
			if(1){ echo '<br>=====555======><pre>';print_r($userArray);echo '</pre>'; }
			if(isset($winsCusGame) && is_array($winsCusGame) && count($winsCusGame) > 0 ) { 
				if(isset($winsCusGame[0]->customUsersWin) && $winsCusGame[0]->customUsersWin !='')	{
					$userArray = array();
					$userArray =  explode(',', $winsCusGame[0]->customUsersWin);
					$customUserCountArray = array_unique(array_merge($customUserCountArray,$userArray));
				}
			}
			if(1){ echo '<br>=====6666======><pre>';print_r($userArray);echo '</pre>'; }
			if(1) echo '<br>'.__LINE__.'---111----'.count($tiltUserCountArray).'------';
			if(1) echo '<br>'.__LINE__.'---222----'.count($vcUserCountArray).'------';
			if(1) echo '<br>'.__LINE__.'---333----'.count($customUserCountArray).'------';
		//END : GameDeveloper Details
		//BEGIN : UserCreated Details
		$winsCusGame = $winsVirGame  = $winsGame = array();
		if((!isset($_SESSION['mgc_sess_global_report_brand']) || $_SESSION['mgc_sess_global_report_brand'] == '') && (!isset($_SESSION['mgc_sess_global_report_dev']) || $_SESSION['mgc_sess_global_report_dev'] == '')){
			$winField 		= "ts.id,ts.fkUsersId, (CASE WHEN t.Type = 2 THEN count(distinct ts.fkUsersId) END) as TiltWinCount, (CASE WHEN t.Type = 2 THEN sum(ts.Prize) END) as PrizeMoney,GROUP_CONCAT(DISTINCT ts.fkUsersId,'') as tiltUsersWin";
			$winCond		= "and ts.fkTournamentsId in (".$winIds.") and ts.Status = 1 AND t.Type = 2 AND t.CreatedBY = 2 ";
			$winsGame		= $gameObj->userWinnerCount($winField,$winCond);
			
			$winVirField 		= "(CASE WHEN t.Type = 3 THEN count(distinct ts.fkUsersId) END) as VirtualWinCount,sum(ts.Prize)  as PrizeMoney,GROUP_CONCAT(DISTINCT ts.fkUsersId,'') as vcUsersWin";
			$winVirCond		= "and ts.fkTournamentsId in (".$winIds.") and ts.Status = 1 AND t.Type = 3  AND t.CreatedBY = 2 ";
			$winsVirGame		= $gameObj->userWinnerCount($winVirField,$winVirCond);
			
			$winVirCond		= "and ts.fkTournamentsId in (".$winIds.") and ts.Status = 1 AND t.Type = 4  AND t.CreatedBY = 2 ";
			$winCusField 		= "(CASE WHEN t.Type = 4 THEN count(distinct ts.fkUsersId) END) as CustomWinCount,GROUP_CONCAT(DISTINCT ts.fkUsersId,'') as customUsersWin";
			$winsCusGame		= $gameObj->userWinnerCount($winCusField,$winVirCond);
			if(isset($winsGame) && is_array($winsGame) && count($winsGame) > 0 ) { 
				if(isset($winsGame[0]->tiltUsersWin) && $winsGame[0]->tiltUsersWin !='')	{
					$userArray = array();
					$userArray =  explode(',', $winsGame[0]->tiltUsersWin);
					$tiltUserCountArray = array_unique(array_merge($tiltUserCountArray,$userArray));
				}
			}
			if(1){ echo '<br>====7777=======><pre>';print_r($userArray);echo '</pre>'; }
			if(isset($winsVirGame) && is_array($winsVirGame) && count($winsVirGame) > 0 ) { 
				if(isset($winsVirGame[0]->vcUsersWin) && $winsVirGame[0]->vcUsersWin !='')	{
					$userArray = array();
					$userArray =  explode(',', $winsVirGame[0]->vcUsersWin);
					$vcUserCountArray = array_unique(array_merge($vcUserCountArray,$userArray));
				}
			}
			if(1){ echo '<br>=====888888======><pre>';print_r($userArray);echo '</pre>'; }
			if(isset($winsCusGame) && is_array($winsCusGame) && count($winsCusGame) > 0 ) { 
				if(isset($winsCusGame[0]->customUsersWin) && $winsCusGame[0]->customUsersWin !='')	{
					$userArray = array();
					$userArray =  explode(',', $winsCusGame[0]->customUsersWin);
					$customUserCountArray = array_unique(array_merge($customUserCountArray,$userArray));
				}
			}
			if(1){ echo '<br>=====9999999999======><pre>';print_r($userArray);echo '</pre>'; }
		}	
			if(1) echo '<br>'.__LINE__.'---111----'.count($tiltUserCountArray).'------';
			if(1) echo '<br>'.__LINE__.'---222----'.count($vcUserCountArray).'------';
			if(1) echo '<br>'.__LINE__.'---333----'.count($customUserCountArray).'------';
			$tiltWonUserCount 	= count($tiltUserCountArray);
			$virtualWonUserCount= count($vcUserCountArray);
			$customWonUserCount = count($customUserCountArray);
		//END : UserCreated Details
		}//new If for filter
	}
}
/*---total active user----*/
$field		= "*,count(u.id) as totalUser";
$cond 		= " and u.Status = 1";
$totalUser	=  $gameObj->getUserList($field,$cond);

/*---user purchase coins - stripe,iap/brand purchased coins----*/
$purchaseField	=	"*,sum(Coins) as TotalCoins ";
$purchaseCond	=	" and CoinType = 1 and Type = 4 and PurchasedBy = 0";
$brandCond		=	" and  CoinType = 1 and Type = 4 and PurchasedBy = 1";
$globalCond		= 	" and CoinType  = 1 and Type = 4 ";
$stripeResult	= $gameObj->getTotalCoins($purchaseField,$purchaseCond,'');
$globalCoinsPurchased = $gameObj->getTotalCoins($purchaseField,$globalCond,'');
if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != ''){
	$where 			= "and BrandDeveloperId = ".$_SESSION['mgc_sess_global_report_brand'];
}else{
	$where		= '';
}
$brandStripCoins		= $gameObj->getTotalCoins($purchaseField,$brandCond,$where);
//Update on 12-01-2015
/*---Game developer purchase coins - stripe----*/
$devStripeTilt = 0;
$field		=	" sum(Coins) as TotalCoins ";
$condition	=	" and CoinType = 1 and Type = 4 and PurchasedBy = 2";
if((isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''))
	$condition	.= " AND BrandDeveloperId =".$_SESSION['mgc_sess_global_report_dev']."";
$devStripeResult	= $gameObj->getTotalCoins($field,$condition,'');
if(isset($devStripeResult) && is_array($devStripeResult) && count($devStripeResult) > 0 ) {
	if(isset($devStripeResult[0]->TotalCoins) && $devStripeResult[0]->TotalCoins!='') 
		$devStripeTilt = $devStripeResult[0]->TotalCoins;
}
/*--------ITunes/google play---*/
$iAppField		=  	"*,sum(PackagePrice) as appCoins";
$iAppCond		=	" and  Platform = 1 ";
$googleCond		=	" and  Platform = 2 ";
$iAppResult		= 	$gameObj->getTotalCoinsIapp($iAppField,$iAppCond);
$googlPlayResult	= 	$gameObj->getTotalCoinsIapp($iAppField,$googleCond);

/*----total brands ----*/
if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != '')
	$bcondition		= " id = ".$_SESSION['mgc_sess_global_report_brand']." ";
else $bcondition		= "Status = 1";
$bfields		= "*, count(id) as brandTotal ";
$totalBrands	= $brandObj->getSingleBrand($bfields,$bcondition);
/*----total Developers ----*/
$devCount = 0;
$dcondition		= "globalreport";
$condition		= "Status = 1";
$fields		= " count(id) as devTotal ";
if((isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''))
	$condition	.= " AND id =".$_SESSION['mgc_sess_global_report_dev']."";
$totalDeveloper	= $gameObj->gameDeveloperDetails($fields,$condition);
if(isset($totalDeveloper) && is_array($totalDeveloper) && count($totalDeveloper) > 0 ) {
	if(isset($totalDeveloper[0]->devTotal) && $totalDeveloper[0]->devTotal!='') 
		$devCount = $totalDeveloper[0]->devTotal;
}

/*---- User coins balance---- */
$balFields			= "*, sum(Coins) as coinsBal, sum(VirtualCoins) as virtualCoinsBal " ;
$balCondition		= " and Status = 1";
$userCoinsBalance	= $gameObj->getUserCoinsBalance($balFields,$balCondition);


/*---- brand coins balance---- */
$balFields		= " sum(Amount) as brandBalance,sum(VirtualCoins) as brandVCBalance ";
$balCondition	= " Status = 1";
$brandsBalance	= $brandObj->getBrandBalance($balFields,$balCondition);
$brandTiltBalance = $brandVCBalance = 0;
if(isset($brandsBalance) && is_array($brandsBalance) && count($brandsBalance) > 0 ) { 
	if(isset($brandsBalance[0]->brandBalance) && $brandsBalance[0]->brandBalance!='') 
		$brandTiltBalance = $brandsBalance[0]->brandBalance;
	if(isset($brandsBalance[0]->brandVCBalance) && $brandsBalance[0]->brandVCBalance!='') 
		$brandVCBalance = $brandsBalance[0]->brandVCBalance;
}
/*---- Game Developer TiLT$ balance---- */
$deveTiltBalance = $devVCBalance = 0;
$fields		= " sum(Amount) as devBalance,sum(VirtualCoins) as devVCBalance ";
$condition	= " Status = 1 ";
if((isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''))
	$condition	.= " AND id =".$_SESSION['mgc_sess_global_report_dev']."";
$deveTiltBalanceRes	= $gameObj->gameDeveloperDetails($fields,$condition);
if(isset($deveTiltBalanceRes) && is_array($deveTiltBalanceRes) && count($deveTiltBalanceRes) > 0 ) { 
	if(isset($deveTiltBalanceRes[0]->devBalance) && $deveTiltBalanceRes[0]->devBalance!='') 
		$deveTiltBalance = $deveTiltBalanceRes[0]->devBalance;
	if(isset($deveTiltBalanceRes[0]->devVCBalance) && $deveTiltBalanceRes[0]->devVCBalance!='') 
		$devVCBalance = $deveTiltBalanceRes[0]->devVCBalance;
}
/*---User created tournaments--- */
$userTournFields	= "count(t.id) as userCreatedTournaments,group_concat(CASE WHEN t.GameType != 2 THEN id END) as userTournIds, group_concat(CASE WHEN t.GameType = 2 THEN id END) as userEliTournIds";
$userTournCondition	= " and t.CreatedBy = 1 and status = 1";
$userTournaments	= $gameObj->getUserTourn($userTournFields,$userTournCondition,'');
if(isset($userTournaments) && is_array($userTournaments) && isset($userTournaments[0]->userTournIds) && !empty($userTournaments[0]->userTournIds)){
	$turnsField			= "*,count(tp.id) as turnsCount, count(distinct tp.fkUsersId) as userCount";
	$turnsCond			= "and fkTournamentsId in (".rtrim($userTournaments[0]->userTournIds,',').")";
	$userTurnsCount		= $gameObj->getTurnsList($turnsField,$turnsCond);
}
if(isset($userTournaments) && is_array($userTournaments) && isset($userTournaments[0]->userEliTournIds) && !empty($userTournaments[0]->userEliTournIds)){
	$field		= " MAX(RoundTurn) as turnsCount, count(distinct ep.fkUsersId) as UserCount, GROUP_CONCAT(ep.fkUsersId,'') as usersIds";
	$condition		= " and tp.fkTournamentsId in (".rtrim($userTournaments[0]->userEliTournIds,',').") AND ep.fkUsersId != 0";
	$userEliTurnsCount		= $gameObj->getRoundsList($field,$condition);
}
/*---Brand created tournaments--- */
$brandTournFields	= "count(t.id) as brandCreatedTournaments,group_concat(CASE WHEN t.GameType != 2 THEN id END) as brandTournIds, group_concat(CASE WHEN t.GameType = 2 THEN id END) as brandEliTournIds";
$brandTournCondition	= " and t.CreatedBy = 0 and status = 1";
if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != ''){
	$where 			= " and fkBrandsId = ".$_SESSION['mgc_sess_global_report_brand'];
}else{
	$where		= '';
}
$brandTournaments	= $gameObj->getUserTourn($brandTournFields,$brandTournCondition,$where);
if($brandTournaments[0]->brandTournIds != '' ){
	$brandTurnsField		= "*,count(tp.id) as turnsCount, count(distinct fkUsersId) as brandUserCount";
	$brandTurnsCond			= " and fkTournamentsId in (".rtrim($brandTournaments[0]->brandTournIds,',').") AND fkUsersId != 0";
	$brandTurnsCount		= $gameObj->getTurnsList($brandTurnsField,$brandTurnsCond);
}
if($brandTournaments[0]->brandEliTournIds != '' ){
	$brandEliTurnsField		= "MAX(RoundTurn) as turnsCount, count(distinct ep.fkUsersId) as brandUserCount, GROUP_CONCAT(ep.fkUsersId,'') as usersIds";
	$brandEliTurnsCond		= " and tp.fkTournamentsId in (".rtrim($brandTournaments[0]->brandEliTournIds,',').") AND ep.fkUsersId != 0";
	$brandEliTurnsCount		= $gameObj->getRoundsList($brandEliTurnsField,$brandEliTurnsCond);
}
//********* BEGIN : Game Developer Created **********
$fields	= "count(t.id) as developerCreatedTournaments,group_concat(CASE WHEN t.GameType != 2 THEN id END) as devTournIds, group_concat(CASE WHEN t.GameType = 2 THEN id END) as devEliTournIds";
$condition	= " and t.CreatedBy = 3 and status = 1";
if(isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''){
	$where 			= " and t.fkDevelopersId = ".$_SESSION['mgc_sess_global_report_dev']."";
}else{
	$where		= '';
}
$developerTournaments	= $gameObj->getUserTourn($fields,$condition,$where);
if($developerTournaments[0]->devTournIds != '' ){
	$devTurnsField		= "count(tp.id) as turnsCount, count(distinct fkUsersId) as devUserCount";
	$devTurnsCond			= " and fkTournamentsId in (".rtrim($developerTournaments[0]->devTournIds,',').") AND fkUsersId != 0";
	$devTurnsCount		= $gameObj->getTurnsList($devTurnsField,$devTurnsCond);
}
if($developerTournaments[0]->devEliTournIds != '' ){
	$devEliTurnsField		= "MAX(RoundTurn) as turnsCount, count(distinct ep.fkUsersId) as devUserCount, GROUP_CONCAT(ep.fkUsersId,'') as usersIds ";
	$devEliTurnsCond		= " and tp.fkTournamentsId in (".rtrim($developerTournaments[0]->devEliTournIds,',').") AND ep.fkUsersId != 0";
	$devEliTurnsCount		= $gameObj->getRoundsList($devEliTurnsField,$devEliTurnsCond);
}
//********* END : Game Developer Created **********
$devUserCountArray	=	array();
$devUserCount	= 	$devRoundCount	=	0;
if(ipAddress()=='172.21.4.116'){}
	//Brand userCount,Round Count
	$brandUserCountArray	=	array();
	$brandUserCount	= 	$brandRoundCount	=	0;
	if(isset($brandEliTurnsCount) && is_array($brandEliTurnsCount) && count($brandEliTurnsCount) > 0){
		foreach($brandEliTurnsCount as $key1 => $value1) {
			if(isset($value1->usersIds)	&&	!empty($value1->usersIds)){
				$userArray = array();
				$userArray =  explode(',', $value1->usersIds);
				$brandUserCountArray = array_unique(array_merge($brandUserCountArray,$userArray));
				$brandRoundCount	+= $value1->turnsCount;
			}
		}
	}
	if(isset($brandTurnsCount) && is_array($brandTurnsCount) && count($brandTurnsCount) > 0 ) { 
		if(isset($brandTurnsCount[0]->brandUserCount) && $brandTurnsCount[0]->brandUserCount!=''){
			$brandUserCount	=	$brandTurnsCount[0]->brandUserCount;
		}
	}
	if(isset($brandUserCountArray) && is_array($brandUserCountArray) && count($brandUserCountArray) > 0){
		$brandUserCount	+=	count($brandUserCountArray);
	}
	//User Created userCount,Round Count
	$UCUserCountArray	=	array();
	$UCUserCount	= 	$userRoundCount	=	0;
	if(isset($userEliTurnsCount) && is_array($userEliTurnsCount) && count($userEliTurnsCount) > 0){
		foreach($userEliTurnsCount as $key2 => $value2) {
			if(isset($value2->usersIds)	&&	!empty($value2->usersIds)){
				$userArray = array();
				$userArray =  explode(',', $value2->usersIds);
				$UCUserCountArray = array_unique(array_merge($UCUserCountArray,$userArray));
				$userRoundCount	+= $value2->turnsCount;
			}
		}
	}
	if(isset($userTurnsCount) && is_array($userTurnsCount) && count($userTurnsCount) > 0 ) { 
		if(isset($userTurnsCount[0]->userCount) && $userTurnsCount[0]->userCount!=''){
			$UCUserCount	=	$userTurnsCount[0]->userCount;
		}
	}
	if(isset($UCUserCountArray) && is_array($UCUserCountArray) && count($UCUserCountArray) > 0){
		$UCUserCount	+=	count($UCUserCountArray);
	}
	//GameDeveloper userCount,Round Count
	if(isset($devEliTurnsCount) && is_array($devEliTurnsCount) && count($devEliTurnsCount) > 0){
		foreach($devEliTurnsCount as $key0 => $value0) {
			if(isset($value0->usersIds)	&&	!empty($value0->usersIds)){
				$userArray = array();
				$userArray =  explode(',', $value0->usersIds);
				$devUserCountArray = array_unique(array_merge($devUserCountArray,$userArray));
				$devRoundCount	+= $value0->turnsCount;
			}
		}
	}
	if(isset($devTurnsCount) && is_array($devTurnsCount) && count($devTurnsCount) > 0 ) { 
		if(isset($devTurnsCount[0]->devUserCount) && $devTurnsCount[0]->devUserCount!=''){
			$devUserCount	=	$devTurnsCount[0]->devUserCount;
		}
	}
	if(isset($devUserCountArray) && is_array($devUserCountArray) && count($devUserCountArray) > 0){
		$devUserCount	+=	count($devUserCountArray);
	}
/*---User coins Redeems--- */
$fields				= "count(fkUsersId) as userRedeem, sum(CoinsUsed) as globalRedeem";
$condition			= " and Status = 1";
$userCoinsRedeem	= $gameObj->getRedeemedUserCoins($fields,$condition);
$globalCoinsBalance =  abs($userCoinsRedeem[0]->globalRedeem - $globalCoinsPurchased[0]->TotalCoins);
/*---Revenue from Brand --- */
$commField			=	"sum(Commission) as brandCommission";
$commCond			= 	"and Status = 1";
$brandCommission	=	$brandObj->getBrandCommission($commField,$commCond);

/*---Revenue from game Developer --- */
$commissionFromDev	=	0;
$commField			=	"sum(Commission) as commissionFromDev";
$commCond			= 	" ";
$gameDevCommissionRes	=	$gameObj->getGameDevCommission($commField,$commCond);
if(isset($gameDevCommissionRes) && is_array($gameDevCommissionRes) && count($gameDevCommissionRes) > 0 ) { 
	if(isset($gameDevCommissionRes[0]->commissionFromDev) && $gameDevCommissionRes[0]->commissionFromDev!=''){
		$commissionFromDev	=	$gameDevCommissionRes[0]->commissionFromDev;
	}
}
?>
<body >
	<div class="box-header"><h2><i class="fa fa-list"></i>Global Report</h2></div>
		<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" >
			
			<tr>
				<td valign="top" align="center" colspan="2" class="filter_form" >
					<form name="search_category" id="search_form" action="GlobalReport" method="post">
					   <table align="center" cellpadding="0" cellspacing="0" border="0" class="" width="97%">									       
							<tr><td height="15"></td></tr>
							<tr>	
								<td width="5%" align="left"><label>Start Date</label></td>
								<td width="2%" align="center">:</td>
								<td height="40" align="left">
									<input style="width:190px;" type="text" autocomplete="off" maxlength="10" class="input datepicker" name="startdate" id="startdate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_global_report_start']) && $_SESSION['mgc_sess_global_report_start'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_global_report_start'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
								</td>
								<td width="5%"><label>End Date</label></td>
								<td width="2%" align="center">:</td>
								<td height="40" align="left">
									<input style="width:190px" type="text" autocomplete="off"  maxlength="10" class="input datepicker" name="enddate" id="enddate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_global_report_end']) && $_SESSION['mgc_sess_global_report_end'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_global_report_end'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
								</td>
								
								<td  align="left" ><label>Game</label></td>
								<td width="2%" align="center">:</td>
								<td >
									<select name="game" id="game" class="form-control">
									<option value="">All Games</option>
									<?php if(isset($gameList) && is_array($gameList) && count($gameList) > 0){
											foreach($gameList as $key => $value) { ?>
												<option value="<?php echo $value->id; ?>" <?php if(isset($_SESSION['mgc_sess_global_report_game']) && $_SESSION['mgc_sess_global_report_game'] != ''	&&	$_SESSION['mgc_sess_global_report_game'] == $value->id) echo 'selected'; ?>><?php echo $value->Name; ?></option>
										<?php }
										}  ?>
									</select>
								</td>
								
							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td><label>Brand</label></td>
								<td width="2%"  align="center">:</td>
								<td>
										<select name="brand" id="brand" class="form-control" style="width: 62%">
										<option value="">All Brands</option>
										<?php if(isset($brandList) && is_array($brandList) && count($brandList) > 0){
												foreach($brandList as $key => $value) { 
													if($value->BrandName != '') { ?>
														<option value="<?php echo $value->id; ?>" <?php if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != ''	&&	$_SESSION['mgc_sess_global_report_brand'] == $value->id) echo 'selected'; ?>><?php echo $value->BrandName; ?></option>
											<?php 	}
												}
											}  ?>
										</select>
								</td>
								
								<td><label>Game Developer</label></td>
								<td width="2%"  align="center">:</td>
								<td>
										<select name="developer" id="developer" class="form-control" style="width: 62%">
										<option value="">All Game Developer</option>
										<?php if(isset($devList) && is_array($devList) && count($devList) > 0){
												foreach($devList as $key1 => $value1) { 
													if($value1->Name != '') { ?>
														<option value="<?php echo $value1->id; ?>" <?php if(isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''	&&	$_SESSION['mgc_sess_global_report_dev'] == $value1->id) echo 'selected'; ?>><?php echo $value1->Name; ?></option>
											<?php 	}
												}
											}  ?>
										</select>
								</td>
								
							</tr>
							<tr><td align="center" colspan="9" style="padding-top:20px" ><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
							<tr><td height="15"></td></tr>
						 </table>
						</form>
					</td>
				</tr>
						<tr><td height="10"></td></tr>
								<tr></tr>
								<tr><td height="20"></td></tr>
								</table>
								<form action="" class="l_form" name="GlobalReportForm" id="globalReportForm"  method="post"> 
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Active User in Tournaments</th>
											</tr>
											<tr>
												<td width="60%">Total Active User</td>
												<td><?php if($tourUserCount !='') echo number_format($tourUserCount); else echo "0";?></td>
											<!--
											<?php /* if(isset($searchGame) && is_array($searchGame) && count($searchGame) > 0 ) { 
								 							$temp1 = $temp2 = array();
															foreach($searchGame as $key=>$value){ 
																if($value->userIds != '')
																	$temp1 = explode(',', $value->userIds);
																if($value->elimIds != '')
																	$temp2 = explode(',', $value->elimIds);
																$count = count(array_unique(array_merge($temp1, $temp2))); */
																?>
												<td><?php 		//echo isset($count) && $count > 0 ? $count : 0; //if(isset($value->userCount) && $value->userCount!='') echo number_format($value->userCount+$value->eliminationcount); else echo "0"; ?></td>
												<?php 		//}
													 //}else { ?>
												<td><?php //echo "0";?></td>
												<?php //} ?>
												-->
											</tr> 
										</table>
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Total Winners</th>
											</tr>
											<tr>
												
												<td width="60%">Total No. of Users Won TiLT$</td>
												<td><?php if($tiltWonUserCount !='') echo number_format($tiltWonUserCount); else echo "0";?></td>
												<!--
												<?php //if(isset($winsGame) && is_array($winsGame) && count($winsGame) > 0 ) { ?>
												<td><?php //if(isset($winsGame[0]->TiltWinCount) && $winsGame[0]->TiltWinCount!='') echo number_format($winsGame[0]->TiltWinCount); else echo "0"; ?></td>
												<?php  //}else { ?>
												<td><?php //echo "0";?></td>
												<?php //} ?>
												-->
												
											</tr> 
											<tr>
												
												<td width="60%">Total No. of Users Won Virtual Coins</td>
												<td><?php if($virtualWonUserCount !='') echo number_format($virtualWonUserCount); else echo "0";?></td>
												<!-- 
												<?php //if(isset($winsVirGame) && is_array($winsVirGame) && count($winsVirGame) > 0 ) { ?>
												<td><?php //if(isset($winsVirGame[0]->VirtualWinCount) && $winsVirGame[0]->VirtualWinCount!='') echo number_format($winsVirGame[0]->VirtualWinCount); else echo "0"; ?></td>
												<?php  //}else { ?>
												<td><?php //echo "0";?></td>
												<?php //} ?>
												-->
											</tr> 
											<tr>
												
												<td width="60%">Total No. of Users Won Custom Prize</td>
												<td><?php if(isset($customWonUserCount) && !empty($customWonUserCount)) echo $customWonUserCount; else echo '0';?></td>
												<!--
												<?php //if(isset($winsCusGame) && is_array($winsCusGame) && count($winsCusGame) > 0 ) { ?>
												<td><?php //if(isset($winsCusGame[0]->CustomWinCount) && $winsCusGame[0]->CustomWinCount!='') echo number_format($winsCusGame[0]->CustomWinCount); else echo "0"; ?></td>
												<?php  //}else { ?>
												<td><?php //echo "0";?></td>
												<?php //} ?>
												-->
												
											</tr> 
										</table> 
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Total Active User </th>
											</tr>
											<tr>
												<td width="60%">Total No. of Active Users</td>
												<?php if(isset($totalUser) && is_array($totalUser) && count($totalUser) > 0 ) { ?>
												<td><?php if(isset($totalUser[0]->totalUser) && $totalUser[0]->totalUser!='') echo number_format($totalUser[0]->totalUser); else echo "0"; ?></td>
												<?php  }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
										</table>
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">User Purchased TiLT$ / Virtual Coins</th>
											</tr>
											<tr>
												<td width="60%">Total User TiLT$ Purchased from Website (Stripe)</td>
												<?php if(isset($stripeResult) && is_array($stripeResult) && count($stripeResult) > 0 ) { ?>
												<td><?php if(isset($stripeResult[0]->TotalCoins) && $stripeResult[0]->TotalCoins!='') echo number_format($stripeResult[0]->TotalCoins); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>												
												<td width="30%">Total User TiLT$ Purchased from iTunes Store</td>
												<?php if(isset($iAppResult) && is_array($iAppResult) && count($iAppResult) > 0 ) { ?>
												<td><?php if(isset($iAppResult[0]->appCoins) && $iAppResult[0]->appCoins!='') echo number_format($iAppResult[0]->appCoins); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr>
											<tr>												
												<td width="30%">Total User TiLT$ Purchased from Google Play</td>
												<?php if(isset($googlPlayResult) && is_array($googlPlayResult) && count($googlPlayResult) > 0 ) { ?>
												<td><?php if(isset($googlPlayResult[0]->appCoins) && $googlPlayResult[0]->appCoins!='') echo number_format($googlPlayResult[0]->appCoins); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr>
											<tr>												
												<td width="30%">Total User Virtual Coins Balance </td>
												<?php if(isset($userCoinsBalance) && is_array($userCoinsBalance) && count($userCoinsBalance) > 0 ) { ?>
												<td><?php if(isset($userCoinsBalance[0]->virtualCoinsBal) && $userCoinsBalance[0]->virtualCoinsBal!='') echo number_format($userCoinsBalance[0]->virtualCoinsBal); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr>
											<tr>												
												<td width="30%">Total User TiLT$ Balance </td>
												<?php if(isset($userCoinsBalance) && is_array($userCoinsBalance) && count($userCoinsBalance) > 0 ) { ?>
												<td><?php if(isset($userCoinsBalance[0]->coinsBal) && $userCoinsBalance[0]->coinsBal!='') echo number_format($userCoinsBalance[0]->coinsBal); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr>
											<tr>												
												<td width="30%">Total User TiLT$ Redeemed for Gift Cards</td>
												<?php if(isset($userCoinsRedeem) && is_array($userCoinsRedeem) && count($userCoinsRedeem) > 0 ) { ?>
												<td><?php if(isset($userCoinsRedeem[0]->userRedeem) && $userCoinsRedeem[0]->userRedeem!='') echo number_format($userCoinsRedeem[0]->userRedeem); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr>
											
										</table>
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Brand Details</th>
											</tr>
											<tr>
												<td width="60%">Total No. of Brands</td>
												<?php if(isset($totalBrands) && is_array($totalBrands) && count($totalBrands) > 0 ) {  ?>
												<td><?php if(isset($totalBrands[0]->brandTotal) && $totalBrands[0]->brandTotal!='') echo number_format($totalBrands[0]->brandTotal); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>
												<td width="30%">Total Brand TiLT$ given as Prizes</td>
												<td><?php if($brandTotalPrize !='') echo number_format($brandTotalPrize); else echo "0";?></td>
												<!--
												<?php //if(isset($winsGame) && is_array($winsGame) && count($winsGame) > 0 ) { ?>
												<td><?php //if(isset($winsGame[0]->PrizeMoney) && $winsGame[0]->PrizeMoney!='') echo number_format($winsGame[0]->PrizeMoney); else echo "0"; ?></td>
												<?php  //}else { ?>
												<td><?php //echo "0";?></td>
												<?php //} ?>
												-->
											</tr>
											<tr>
												<td width="30%">Total Brand Virtual Coins given as Prizes</td>
												<td><?php if($brandVirtualCoinsPrize !='') echo number_format($brandVirtualCoinsPrize); else echo "0";?></td>
											</tr>
											<tr>
												<td width="30%">Total Brand TiLT$ Purchased from Website (Stripe)</td>
												<?php if(isset($brandStripCoins) && is_array($brandStripCoins) && count($brandStripCoins) > 0 ) { ?>
												<td><?php if(isset($brandStripCoins[0]->TotalCoins) && $brandStripCoins[0]->TotalCoins!='') echo number_format($brandStripCoins[0]->TotalCoins); else echo "0"; ?></td>
												<?php  }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr>
											<tr>
												<td width="30%">Total Brand TiLT$ Balance</td>
												<td><?php if($brandTiltBalance !='') echo number_format($brandTiltBalance,2); else echo "0";?></td>
												<!--
												<?php //if(isset($brandsBalance) && is_array($brandsBalance) && count($brandsBalance) > 0 ) { ?>
												<td><?php //if(isset($brandsBalance[0]->brandBalance) && $brandsBalance[0]->brandBalance!='') echo number_format($brandsBalance[0]->brandBalance,2); else echo "0"; ?></td>
												<?php  //}else { ?>
												<td><?php //echo "0";?></td>
												<?php //} ?>
												-->
											</tr>
											<tr>
												<td width="30%">Total Brand Virtual Coins Balance</td>
												<td><?php if($brandVCBalance !='') echo number_format($brandVCBalance,2); else echo "0";?></td>
											</tr>
											<tr>
												<td width="30%">Total Revenue from Brand</td>
												<?php if(isset($brandCommission) && is_array($brandCommission) && count($brandCommission) > 0 ) { ?>
												<td><?php if(isset($brandCommission[0]->brandCommission) && $brandCommission[0]->brandCommission!='') echo "$".number_format($brandCommission[0]->brandCommission,2); else echo "0"; ?></td>
												<?php  }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr>
										</table>
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Game Developer Details</th>
											</tr>
											<tr>
												<td width="60%">Total No. of Game Developer</td>
												<td><?php if(isset($devCount) && $devCount !='') echo number_format($devCount); else echo "0";?></td>
											</tr> 
											<tr>
												<td width="30%">Total Game Developer TiLT$ given as Prizes</td>
												<td><?php if(isset($devTotalPrize) && $devTotalPrize !='') echo number_format($devTotalPrize); else echo "0";?></td>
											</tr>
											<tr>
												<td width="30%">Total Game Developer Virtual Coins given as Prizes</td>
												<td><?php if(isset($devVirtualCoinsPrize) && $devVirtualCoinsPrize !='') echo number_format($devVirtualCoinsPrize); else echo "0";?></td>
											</tr>
											<tr>
												<td width="30%">Total Game Developer TiLT$ Purchased from developer portal (Stripe)</td>
												<td><?php if(isset($devStripeTilt) && $devStripeTilt !='') echo number_format($devStripeTilt); else echo "0";?></td>
											</tr>
											<tr>
												<td width="30%">Total Game Developer TiLT$ Balance</td>
												<td><?php if(isset($deveTiltBalance) && $deveTiltBalance !='') echo number_format($deveTiltBalance,2); else echo "0";?></td>
											</tr>
											<tr>
												<td width="30%">Total Game Developer Virtual Coins Balance</td>
												<td><?php if(isset($devVCBalance) && $devVCBalance !='') echo number_format($devVCBalance,2); else echo "0";?></td>
											</tr>
											<tr>
												<td width="30%">Total Revenue from Game Developer</td>
												<td><?php if(isset($commissionFromDev) && $commissionFromDev !='') echo '$'.number_format($commissionFromDev,2); else echo "0";?></td>
											</tr>
										</table>
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Brand Created Tournaments</th>
											</tr>
											<tr>
												<td width="60%">Total No. of Brand Created Tournaments</td>
												<?php if(isset($brandTournaments) && is_array($brandTournaments) && count($brandTournaments) > 0 ) { ?>
												<td><?php if(isset($brandTournaments[0]->brandCreatedTournaments) && $brandTournaments[0]->brandCreatedTournaments!='') echo number_format($brandTournaments[0]->brandCreatedTournaments); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>
												<td width="30%">Total No. of Brand Created Tournaments Turns Played</td>
												<?php if(isset($brandTurnsCount) && is_array($brandTurnsCount) && count($brandTurnsCount) > 0 ) { ?>
												<td><?php if(isset($brandTurnsCount[0]->turnsCount) && $brandTurnsCount[0]->turnsCount!='') echo number_format($brandTurnsCount[0]->turnsCount); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>
												<td width="30%">Total No. of Brand Created Tournaments Rounds Played</td>
												<td><?php echo number_format($brandRoundCount);?></td>
											</tr> 
											<tr>
												<td width="30%">Total No. of Users Playing Brand Created Tournaments</td>
												<td><?php echo number_format($brandUserCount);?></td>
												<!--
												<?php //if(isset($brandTurnsCount) && is_array($brandTurnsCount) && count($brandTurnsCount) > 0 ) { ?>
												<td><?php //if(isset($brandTurnsCount[0]->brandUserCount) && $brandTurnsCount[0]->brandUserCount!='') echo number_format($brandTurnsCount[0]->brandUserCount); else echo "0"; ?></td>
												<?php //}else { ?>
												<td><?php //echo "0";?></td>
												<?php //} ?>
												-->
											</tr> 
										</table>
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">User Created Tournaments</th>
											</tr>
											<tr>
												<td width="60%">Total No. of User Created Tournaments</td>
												<?php if(isset($userTournaments) && is_array($userTournaments) && count($userTournaments) > 0 ) { ?>
												<td><?php if(isset($userTournaments[0]->userCreatedTournaments) && $userTournaments[0]->userCreatedTournaments!='') echo number_format($userTournaments[0]->userCreatedTournaments); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>
												<td width="30%">Total No. of User Created Tournaments Turns Played</td>
												<?php if(isset($userTurnsCount) && is_array($userTurnsCount) && count($userTurnsCount) > 0 ) { ?>
												<td><?php if(isset($userTurnsCount[0]->turnsCount) && $userTurnsCount[0]->turnsCount!='') echo number_format($userTurnsCount[0]->turnsCount); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>
												<td width="30%">Total No. of User Created Tournaments Rounds Played</td>
												<td><?php echo number_format($userRoundCount);?></td>
											</tr> 
											<tr>
												<td width="30%">Total No. of Users Playing User Created Tournaments</td>
												<td><?php echo number_format($UCUserCount);?></td>
												<!--
												<?php //if(isset($userTurnsCount) && is_array($userTurnsCount) && count($userTurnsCount) > 0 ) { ?>
												<td><?php //if(isset($userTurnsCount[0]->userCount) && $userTurnsCount[0]->userCount!='') echo number_format($userTurnsCount[0]->userCount); else echo "0"; ?></td>
												<?php //}else { ?>
												<td><?php //echo "0";?></td>
												<?php //} ?>
												-->
											</tr> 
										</table>
										<!--  BEGIN: Game Developer created -->
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Game Developer Created Tournaments</th>
											</tr>
											<tr>
												<td width="60%">Total No. of Game Developer Created Tournaments</td>
												<?php if(isset($developerTournaments) && is_array($developerTournaments) && count($developerTournaments) > 0 ) { ?>
												<td><?php if(isset($developerTournaments[0]->developerCreatedTournaments) && $developerTournaments[0]->developerCreatedTournaments!='') echo number_format($developerTournaments[0]->developerCreatedTournaments); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>
												<td width="30%">Total No. of Game Developer Created Tournaments Turns Played</td>
												<?php if(isset($devTurnsCount) && is_array($devTurnsCount) && count($devTurnsCount) > 0 ) { ?>
												<td><?php if(isset($devTurnsCount[0]->turnsCount) && $devTurnsCount[0]->turnsCount!='') echo number_format($devTurnsCount[0]->turnsCount); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>
												<td width="30%">Total No. of Game Developer Created Tournaments Rounds Played</td>
												<td><?php echo number_format($devRoundCount);?></td>
											</tr> 
											<tr>
												<td width="30%">Total No. of Users Playing Game Developer Created Tournaments</td>
												<td><?php echo number_format($devUserCount);?></td>
												<!--
												<?php //if(isset($devTurnsCount) && is_array($devTurnsCount) && count($devTurnsCount) > 0 ) { ?>
												<td><?php //if(isset($devTurnsCount[0]->devUserCount) && $devTurnsCount[0]->devUserCount!='') echo number_format($devTurnsCount[0]->devUserCount); else echo "0"; ?></td>
												<?php //}else { ?>
												<td><?php //echo "0";?></td>
												<?php //} ?>
												-->
											</tr> 
										</table>
										<!--  END: Game Developer created -->
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Global TiLT$</th>
											</tr>
											<tr>
												<td width="60%">Global TiLT$ Purchased</td>
												<?php if(isset($globalCoinsPurchased) && is_array($globalCoinsPurchased) && count($globalCoinsPurchased) > 0 ) { ?>
												<td><?php if(isset($globalCoinsPurchased[0]->TotalCoins) && $globalCoinsPurchased[0]->TotalCoins!='') echo number_format($globalCoinsPurchased[0]->TotalCoins); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>												
												<td width="30%">Global TiLT$ Redeemed</td>
												<?php if(isset($userCoinsRedeem) && is_array($userCoinsRedeem) && count($userCoinsRedeem) > 0 ) { ?>
												<td><?php if(isset($userCoinsRedeem[0]->globalRedeem) && $userCoinsRedeem[0]->globalRedeem!='') echo number_format($userCoinsRedeem[0]->globalRedeem); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr>
											<tr>
												<td width="30%">Global TiLT$ Currently Held in System</td>
												<td><?php echo (!empty($globalCoinsBalance)?number_format($globalCoinsBalance):"0"); ?></td>
											</tr>
										</table>
								 </form>	
				</td>
			</tr>
			<tr><td height="20"></td></tr>
		</table>
	
<?php commonFooter(); ?>
<script type="text/javascript">
$("#startdate").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
	onSelect		: function (dateText, inst) {
						$('#enddate').datepicker("option", 'minDate', new Date(dateText));
						},
    onClose			: function () { $(this).focus(); },

    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	closeText		:   "Close"
 });
 $("#enddate").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
	onSelect		: function () { },
    onClose			: function () { $(this).focus(); },
    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	closeText		:   "Close"
 });

 jQuery(function() {
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[id^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
//self.close();
$(".detailUser").on('click',function(){
	var hre	=	$(".detailUser").attr("href");
 	window.parent.location.href = hre+'&back=1';
});
</script>
</html>
