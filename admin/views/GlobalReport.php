<?php 

ini_set('memory_limit', '1024M');

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
$game_fields	=	"*";
$game_condition	=	" AND g.Status = 1 ";
$gameList		=	$gameObj->getGameArray($game_fields,$game_condition);

$fields			=	" id,Company";
$condition		=	" Status = 1 ";
$devList		=	$gameObj->gameDeveloperDetails($fields,$condition);

$brandField		=	"t.id as tournamentId,t.CreatedBy";
$brandCondition	=	"";
$tourIds		=	'';
$tourUserCount	=	0;
$userArray		=	array();

/*--total user based on tournaments played----*/
	if(isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''){
		$gameUserCondition	= " and t.fkDevelopersId = ".$_SESSION['mgc_sess_global_report_dev']." ";
	}
	else{
		$gameUserCondition	= " ";
	}
	$gameUserField	 	= " count(Distinct a.fkUsersId) activeUser ";
	$gameUserCondition	.= " AND a.fkUsersId > 0 AND t.Status = 1 AND u.Status =1 AND u.VerificationStatus = 1 AND a.ActionType = 2 ";
	$playerCountRes		= $gameObj->getActiveTournamentUsers($gameUserField, $gameUserCondition);
	if(isset($playerCountRes) && is_array($playerCountRes) && count($playerCountRes) > 0) {
		$tourUserCount = $playerCountRes[0]->activeUser;
	} 
$tiltWonUserCount =  $virtualWonUserCount = $customWonUserCount = 0;
$devTotalPrize = $devVirtualCoinsPrize =  0;
$brandTiltWinCount = $vcWonUserCount = 0;
$userCountArray = $tiltUserCountArray = $vcUserCountArray = $customUserCountArray = array();

/*---winners count----*/
		//BEGIN : Winners Count
		$tWinField = 	"count(distinct (CASE WHEN t.Type = 2 THEN ts.fkUsersId END)) as TiltWinCount, 
						count(distinct (CASE WHEN t.Type = 3 THEN ts.fkUsersId END)) as VirtWinCount, 
						count(distinct (CASE WHEN t.Type = 4 THEN ts.fkUsersId END)) as CoustWinCount ";
		$tWinCond	=	" and ts.Status = 1 AND t.CreatedBY in (1,3) ";
		if(isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')
			$tWinCond .= " and fkDevelopersId = ".$_SESSION['mgc_sess_global_report_dev'];
			
		$twinsGame		= $gameObj->userWinnerCount($tWinField,$tWinCond);
		//END : Winners Count
			//Game Developer
			$winsCusGame = $winsVirGame  = $winsGame = array();
			$winField 		= "sum((CASE WHEN t.Type = 2 THEN ts.Prize ELSE 0 END)) as PrizeMoney,
								sum((CASE WHEN t.Type = 3 THEN ts.Prize ELSE 0 END)) as vcoins";
			$winCond		= " and ts.Status = 1 AND t.CreatedBY = 3 ";
			if(isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != '')
				$winCond .= " and fkDevelopersId = ".$_SESSION['mgc_sess_global_report_dev'];
			$winsGame		= $gameObj->userWinnerCount($winField,$winCond);
			if(isset($winsGame) && is_array($winsGame) && count($winsGame) > 0 ) { 
				if(isset($winsGame[0]->PrizeMoney) && !empty($winsGame[0]->PrizeMoney))
					$devTotalPrize = $winsGame[0]->PrizeMoney;
				if(isset($winsGame[0]->vcoins) && !empty($winsGame[0]->vcoins))
					$devVirtualCoinsPrize = $winsGame[0]->vcoins;
			}
/*---total active user----*/
$field		= "count(u.id) as totalUser";
$cond 		= " and u.Status = 1 AND u.VerificationStatus = 1 ";
$totalUser	=  $gameObj->getUserList($field,$cond);

/*---user purchase coins - stripe,iap/brand purchased coins----*/
$purchaseField	=	" sum(Coins) as TotalCoins ";
$purchaseCond	=	" and CoinType = 1 and Type = 4 and PurchasedBy = 0";
$brandCond		=	" and  CoinType = 1 and Type = 4 and PurchasedBy = 1";
$globalCond		= 	" and CoinType  = 1 and Type = 4 ";
$globalCond1	= 	" and CoinType  = 2 and Type = 4 ";
$stripeResult	= $gameObj->getTotalCoins($purchaseField,$purchaseCond,'');
$globalCoinsPurchased = $gameObj->getTotalCoins($purchaseField,$globalCond,'');
$globalVCPurchased = $gameObj->getTotalCoins($purchaseField ,$globalCond1,'');

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
$iAppField		=  	"sum(PackagePrice) as appCoins";
$iAppCond		=	" and  Platform = 1 ";
$googleCond		=	" and  Platform = 2 ";
$iAppResult		= 	$gameObj->getTotalCoinsIapp($iAppField,$iAppCond);
$googlPlayResult	= 	$gameObj->getTotalCoinsIapp($iAppField,$googleCond);
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
$balFields			= " sum(Coins) as coinsBal, sum(VirtualCoins) as virtualCoinsBal " ;
$balCondition		= " and Status = 1";
$userCoinsBalance	= $gameObj->getUserCoinsBalance($balFields,$balCondition);
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
$userTournFields	= "count(t.id) as userCreatedTournaments ";
$userTournCondition	= " and t.CreatedBy = 1 and status = 1";
$userTournaments	= $gameObj->getUserTourn($userTournFields,$userTournCondition,'');

//Turn count
$turnsField			= "count(tp.id) as turnsCount";
$turnsCond			= " AND t.CreatedBy = 1 and t.status = 1 AND tp.fkUsersId != 0 ";
$userTurnsCount		= $gameObj->getTurnsList($turnsField,$turnsCond);

//Round Count
$field		= " MAX(RoundTurn) as turnsCount ";
$condition		= " and ep.fkUsersId != 0 AND t.CreatedBy = 1 and t.status = 1 ";
$userEliTurnsCount		= $gameObj->getRoundsList($field,$condition);

$userRoundCount	=	0;
if(isset($userEliTurnsCount) && is_array($userEliTurnsCount) && count($userEliTurnsCount) > 0){
	foreach($userEliTurnsCount as $key2 => $value2) {
		$userRoundCount	+= $value2->turnsCount;
	}
}

//Player Count
$field			= " count(Distinct a.fkUsersId) as usersCount ";
$condition			= " AND t.CreatedBy = 1 and t.status = 1 AND a.fkUsersId != 0 AND a.ActionType = 2 ";
$userPlayerCount		= $gameObj->getPlayerCount($field,$condition);

/*---Brand created tournaments--- */
$brandTournFields	= "count(t.id) as brandCreatedTournaments ";
$brandTournCondition	= " and t.CreatedBy = 0 and status = 1";
if(isset($_SESSION['mgc_sess_global_report_brand']) && $_SESSION['mgc_sess_global_report_brand'] != ''){
	$where 			= " and t.fkBrandsId = ".$_SESSION['mgc_sess_global_report_brand'];
}else
	$where		= '';

//********* BEGIN : Game Developer Created **********
$fields	= "count(t.id) as developerCreatedTournaments ";
$condition	= " and t.CreatedBy = 3 and status = 1";
if(isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''){
	$where 			= " and t.fkDevelopersId = ".$_SESSION['mgc_sess_global_report_dev']."";
}else
	$where		= '';
$developerTournaments	= $gameObj->getUserTourn($fields,$condition,$where);

//Turn count
$devTurnsField		= "count(tp.id) as turnsCount ";
$devTurnsCond			= " AND t.CreatedBy = 3 and t.status = 1 AND tp.fkUsersId != 0 ".$where;
$devTurnsCount		= $gameObj->getTurnsList($devTurnsField,$devTurnsCond);

//Round Count
$devEliTurnsField		= "MAX(RoundTurn) as turnsCount ";
$devEliTurnsCond		= " and ep.fkUsersId != 0 AND t.CreatedBy = 3 and t.status = 1 ".$where;
$devEliTurnsCount		= $gameObj->getRoundsList($devEliTurnsField,$devEliTurnsCond);

$devRoundCount	=	0;
if(isset($devEliTurnsCount) && is_array($devEliTurnsCount) && count($devEliTurnsCount) > 0){
	foreach($devEliTurnsCount as $key2 => $value2) {
		$devRoundCount	+= $value2->turnsCount;
	}
}

//Player Count
$field			= " count(Distinct a.fkUsersId) as usersCount ";
$condition			= " AND t.CreatedBy = 3 and t.status = 1 AND a.fkUsersId != 0 AND a.ActionType = 2 ";
$gamePlayerCount		= $gameObj->getPlayerCount($field,$condition);
//********* END : Game Developer Created **********

/*---User coins Redeems--- */
$fields				= "count(DISTINCT fkUsersId) as userRedeem, sum(CoinsUsed) as globalRedeem";
$condition			= " and Status = 1";
$userCoinsRedeem	= $gameObj->getRedeemedUserCoins($fields,$condition);
$globalCoinsBalance =  abs($userCoinsRedeem[0]->globalRedeem - $globalCoinsPurchased[0]->TotalCoins);

/*---Revenue from game Developer --- */
$commissionFromDev	=	0;
$commField			=	"sum(Commission) as commissionFromDev";
$commCond			= 	" ";
if(isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''){
	$commCond 		.= " and fkDeveloperId = ".$_SESSION['mgc_sess_global_report_dev']."";
}
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
								<td><label>Developer & Brand</label></td>
								<td width="2%"  align="center">:</td>
								<td>
										<select name="developer" id="developer" class="form-control" style="width: 62%">
										<option value="">All Developer & Brand</option>
										<?php if(isset($devList) && is_array($devList) && count($devList) > 0){
												foreach($devList as $key1 => $value1) { 
													if($value1->Company != '') { ?>
														<option value="<?php echo $value1->id; ?>" <?php if(isset($_SESSION['mgc_sess_global_report_dev']) && $_SESSION['mgc_sess_global_report_dev'] != ''	&&	$_SESSION['mgc_sess_global_report_dev'] == $value1->id) echo 'selected'; ?>><?php echo $value1->Company; ?></option>
											<?php 	}
												}
											} ?>
										</select>
								</td>
								<td align="center" colspan="3"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td>
							</tr>
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
											</tr> 
										</table>
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Total Winners</th>
											</tr>
											<tr>
												
												<td width="60%">Total No. of Users Won TiLT$</td>
												
												<td>
													<?php if(isset($twinsGame) && is_array($twinsGame) && count($twinsGame) > 0 ){
															if(isset($twinsGame[0]->TiltWinCount) && $twinsGame[0]->TiltWinCount > 0) echo number_format($twinsGame[0]->TiltWinCount); else echo "0";
													}else echo "0";?>
												</td>
											</tr> 
											<tr>
												
												<td width="60%">Total No. of Users Won Virtual Coins</td>
												<td>
													<?php if(isset($twinsGame) && is_array($twinsGame) && count($twinsGame) > 0 ){
															if(isset($twinsGame[0]->VirtWinCount) && $twinsGame[0]->VirtWinCount > 0) echo number_format($twinsGame[0]->VirtWinCount); else echo "0";
													}else echo "0";?>
												</td>
											</tr> 
											<tr>
												<td width="60%">Total No. of Users Won Custom Prize</td>
												<td>
													<?php if(isset($twinsGame) && is_array($twinsGame) && count($twinsGame) > 0 ){
															if(isset($twinsGame[0]->CoustWinCount) && $twinsGame[0]->CoustWinCount > 0) echo number_format($twinsGame[0]->CoustWinCount); else echo "0";
													}else echo "0";?>
												</td>
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
												<th colspan ="3">Developer & Brand Details</th>
											</tr>
											<tr>
												<td width="60%">Total No. of Developer & Brand</td>
												<td><?php if(isset($devCount) && $devCount !='') echo number_format($devCount); else echo "0";?></td>
											</tr> 
											<tr>
												<td width="30%">Total Developer & Brand TiLT$ given as Prizes</td>
												<td><?php if(isset($devTotalPrize) && $devTotalPrize !='') echo number_format($devTotalPrize); else echo "0";?></td>
											</tr>
											<tr>
												<td width="30%">Total Developer & Brand Virtual Coins given as Prizes</td>
												<td><?php if(isset($devVirtualCoinsPrize) && $devVirtualCoinsPrize !='') echo number_format($devVirtualCoinsPrize); else echo "0";?></td>
											</tr>
											<tr>
												<td width="30%">Total Developer & Brand TiLT$ Purchased from Website (Stripe)</td>
												<td><?php if(isset($devStripeTilt) && $devStripeTilt !='') echo number_format($devStripeTilt); else echo "0";?></td>
											</tr>
											<tr>
												<td width="30%">Total Developer & Brand TiLT$ Balance</td>
												<td><?php if(isset($deveTiltBalance) && $deveTiltBalance !='') echo number_format($deveTiltBalance); else echo "0"; ?></td>
											</tr>
											<tr>
												<td width="30%">Total Developer & Brand Virtual Coins Balance</td>
												<td><?php if(isset($devVCBalance) && $devVCBalance !='') echo number_format($devVCBalance); else echo "0"; ?></td>
											</tr>
											<tr>
												<td width="30%">Total Revenue from Developer & Brand</td>
												<td><?php if(isset($commissionFromDev) && $commissionFromDev !='') echo '$'.number_format($commissionFromDev,2); else echo "0";?></td>
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
												<td>
													<?php if(isset($userPlayerCount) && is_array($userPlayerCount) && isset($userPlayerCount[0]->usersCount) && $userPlayerCount[0]->usersCount!='' ) { 
														echo number_format($userPlayerCount[0]->usersCount);
													}else{
														echo "0";
													}
													?>
												</td>
											</tr> 
										</table>
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Developer & Brand Created Tournaments</th>
											</tr>
											<tr>
												<td width="60%">Total No. of Developer & Brand Created Tournaments</td>
												<?php if(isset($developerTournaments) && is_array($developerTournaments) && count($developerTournaments) > 0 ) { ?>
												<td><?php if(isset($developerTournaments[0]->developerCreatedTournaments) && $developerTournaments[0]->developerCreatedTournaments!='') echo number_format($developerTournaments[0]->developerCreatedTournaments); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>
												<td width="30%">Total No. of Developer & Brand Created Tournaments Turns Played</td>
												<?php if(isset($devTurnsCount) && is_array($devTurnsCount) && count($devTurnsCount) > 0 ) { ?>
												<td><?php if(isset($devTurnsCount[0]->turnsCount) && $devTurnsCount[0]->turnsCount!='') echo number_format($devTurnsCount[0]->turnsCount); else echo "0"; ?></td>
												<?php }else { ?>
												<td><?php echo "0";?></td>
												<?php } ?>
											</tr> 
											<tr>
												<td width="30%">Total No. of Developer & Brand Created Tournaments Rounds Played</td>
												<td><?php echo number_format($devRoundCount);?></td>
											</tr> 
											<tr>
												<td width="30%">Total No. of Users Playing Developer & Brand Created Tournaments</td>
												<td>
													<?php if(isset($gamePlayerCount) && is_array($gamePlayerCount) && isset($gamePlayerCount[0]->usersCount) && $gamePlayerCount[0]->usersCount!='' ) { 
														echo number_format($gamePlayerCount[0]->usersCount);
													}else{
														echo "0";
													}
													?>
												</td>
											</tr> 
										</table>
										<div style="height:20px"></div>
										<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center" class="user_table user_actions">
											<tr>
												<th colspan ="3">Global TiLT$ / Virtual Coins</th>
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
												<td width="30%">Global Virtual Coins Purchased</td>
												<td>
												<?php if(isset($globalVCPurchased) && is_array($globalVCPurchased) && count($globalVCPurchased) > 0 ) { if(isset($globalVCPurchased[0]->TotalCoins) && $globalVCPurchased[0]->TotalCoins!='') echo number_format($globalVCPurchased[0]->TotalCoins); else echo "0";}else { echo "0";} ?>
												</td>
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
$(".detailUser").on('click',function(){
	var hre	=	$(".detailUser").attr("href");
 	window.parent.location.href = hre+'&back=1';
});
</script>
</html>
