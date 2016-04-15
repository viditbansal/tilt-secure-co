<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/BrandController.php');
require_once('controllers/AdminController.php');
require_once('controllers/GameController.php');
require_once('controllers/TournamentController.php');
$gameObj  		=   new GameController();
brand_login_check();
$brandObj  			=   new BrandController();
$adminLoginObj		=	new AdminController();	
$tournamentObj  	=   new TournamentController();
$countryArray 	=	$usStateArray = array();
$generalTerms	=	"Hi all,<br><br>\n \t This {TOURNAMENT_NAME} tournament starts on {START_DATE} and will be end by {END_DATE}. <br><br><br>\n\n \t Maximum number of palyers allowed are {MAX_PLAYERS}, tournament winners  total prize coins {PRIZE_COINS}.";
$privacypolicyContent	=	'';//' {PRIVACY_POLICY} ';
$gamerulesContent	=	' {GAME_RULES} ';
$fields	=	' * ';
$where	= 	' id = 1 ';
$templateArray	=	array();
$setting_details	=	$adminLoginObj->getRulesTemplate($fields,$where);
if(isset($setting_details) && is_array($setting_details) && count($setting_details)>0){
	foreach($setting_details as $key => $value){
		$gamerulesContent	=	$value->GameRules;
		$generalTerms	=	$value->TermsAndConditions;
		$templateArray[]	=	$value->GameRules;
		$templateArray[]	=	$value->TermsAndConditions;
	}
}

//$terms_conditions	=	"Hi all,\n \t This {TOURNAMENT_NAME} tournament starts on {START_DATE} and will be end by {END_DATE}. \n\n \t Maximum number of palyers allowed are {MAX_PLAYER}, tournament winners  total prize coins {PRIZE_COINS}."
$fields			=	' id,Country';
$conditions		=	' Status = 1 ';
$countryList	=	$tournamentObj->getCountryList($fields,$conditions);
if(!empty($countryList))	{
	foreach($countryList as $key=>$value)	{
		$countryArray[$value->id]	=	$value->Country;
	}
	//$countryArray	=	asort ($countryArray);
	asort ($countryArray);
}

$fields			=	' id,State';
$conditions		=	' Status = 1 AND fkCountriesId = 236 ';
$stateList	=	$tournamentObj->getStateList($fields,$conditions);
if(!empty($stateList))	{
	foreach($stateList as $key=>$value)	{
		$usStateArray[$value->id]	=	$value->State;
	}
}

//$countryArray	=	array(1=>'Afghanistan',2=>'Australia',3=>'US',4=>'Zimbabwe');
//$usStateArray	=	array(1=>'Alabama',2=>'New York',3=>'Washington');

$user_image_name = $oldUserName = $message	=	$photoUpdateString = '';
$display = 'none';
$tabshow = '0';
$amount		=	$assignedPrize = 0;
$delete_condition  = 0;
$userNameExist = 0 ;
$brand_id	= $givenLong = $givenLat = $givenLocation = '';
//if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
//}

$fields		=	' * ';
$condition	=	' id = 1';
$settingInfo		=	$adminLoginObj->getSettingDetails($fields,$condition);
$conversionValue	=	$settingInfo[0]->ConversionValue;
$distance	=	$settingInfo[0]->Distance;

if(isset($_POST['deleteId']) && $_POST['deleteId'] != ''){
	$delete_id = $_POST['deleteId'];
	$fields		=	'Status = 3';
	$condition	=	' id = '.$delete_id.'';
	$deleteTournament	=	$brandObj->updateTournamentDetail($fields,$condition);	
}
 if(isset($_POST['editId']) && $_POST['editId'] != '' ){
	$_SESSION['brand_edit_tournament']	=	$_POST['editId'];
}

 if(isset($_POST['editId']) && $_POST['editId'] != '' ){
	$condition       		= "  AND t.id = ".$_POST['editId']." and t.Status in (1,2) LIMIT 1 ";
	$field					=	' t.*,t.id as tournament_id,g.Name as gameName,b.id as brandId,b.Name as brandName,b.Amount,t.PrivacyPolicy,t.TermsAndCondition,t.TournamentRule';
	//$field					=	' t.*,t.id as tournament_id,g.Name as gameName,b.Name as brandName,b.Amount';
	$tournamentDetailsResult= $tournamentObj->getTournamentDetails($field,$condition);
	if($_SERVER["REMOTE_ADDR"]	==	"172.21.4.145")	{
		//echo "<pre>=====>";print_r($tournamentDetailsResult);echo "<=====</pre>";
	}
	
	//$tournamentDetailsResult= $brandObj->getTournamentDetails($field,$condition);
	if(isset($tournamentDetailsResult) && is_array($tournamentDetailsResult) && count($tournamentDetailsResult) > 0){
		$Id					=	$tournamentDetailsResult[0]->id;
		$brand       		=	$tournamentDetailsResult[0]->brandName;
		$game				=	$tournamentDetailsResult[0]->gameName;
		$maxplayers			=	$tournamentDetailsResult[0]->MaxPlayers;
		$entryfee      		= 	$tournamentDetailsResult[0]->EntryFee;
		$startdate       	= 	$tournamentDetailsResult[0]->StartDate;
		$enddate  			= 	$tournamentDetailsResult[0]->EndDate;
		$prize    			= 	$tournamentDetailsResult[0]->Prize;
		$gametype    		= 	$tournamentDetailsResult[0]->GameType;
		$featured    		= 	$tournamentDetailsResult[0]->Type;
		$elimination		=	$tournamentDetailsResult[0]->Elimination;
		$tournamentStatus	=	$tournamentDetailsResult[0]->TournamentStatus;//$value->TournamentStatus
	}	
	if(isset($_POST['editId'])	&&	$_POST['editId'] != '')	{
		$jsonArray	=	array();
		//if($_SERVER['REMOTE_ADDR']=='172.21.4.116'){
			if(isset($tournamentDetailsResult) && is_array($tournamentDetailsResult) && count($tournamentDetailsResult) > 0){
				$brandId		=	$tournamentDetailsResult[0]->brandId;
				$Id				=	$tournamentDetailsResult[0]->id;
				$fields			=	'*';
				$condition		=	' fkTournamentsId ='.$Id.' AND fkBrandsId = '.$brandId.' AND Status = 1 ';
				$usEntryResult	=	$tournamentObj->checkRulesEntry($fields,$condition);
				
				foreach($tournamentDetailsResult[0] as $key=>$value){
					$jsonArray[$key]	=	$value;
				}
			}
			//echo '<pre>';print_r($tournamentDetailsResult);echo '</pre>';
			//echo json_encode($jsonArray);
			$jsonArray	=	array();
			$jsonCountry	=	array();
			$jsonArray[0]	=	$tournamentDetailsResult;
			$jsonArray[1]	=	$usEntryResult;
			$jsonArray[2]	=	$countryArray;
			$jsonArray[3]	=	$usStateArray;
			$jsonArray[4]	=	$templateArray;
			echo json_encode($jsonArray);
			die();
			/*
			$div	=	'<div class="">';
			$tableStart	=	'<table cellpadding="0" cellspacing="7" id="inputParam" border="0" width="100%" align="center">
								<tr align="center" height="30">
									<td class="col_1" align="left"><div class="fleft">&nbsp;</div><div class="fleft">Country</div></td>
									<td class="col_2" align="left">Terms and Conditions</td>
									<td class="col_3" align="left">Game Rules</td>
								</tr>';
			$tableEnd	=	'</table></div>';
			$rulesEntry	=	'';
			if(isset($usEntryResult) && is_array($usEntryResult) && count($usEntryResult) > 0){
			
				foreach($usEntryResult as $key=>$value){
					$rowStatr	=	'<tr align="center" class="clone" clone="'.$key.'">';
					
				}
			}
			*/
			/*
			
			<div class="">	
									<table cellpadding="0" cellspacing="7" id="inputParam" border="0" width="100%" align="center">
										<tr align="center" height="30">
											<td class="col_1" align="left"><div class="fleft">&nbsp;</div><div class="fleft">Country</div></td>
											<td class="col_2" align="left">Terms and Conditions</td>
											<td class="col_3" align="left">Game Rules</td>
										</tr>
										<?php $rowCount	=	1; for($index = 0;$index < $rowCount;$index++) { ?>
										<tr align="center" class="clone" clone="<?php echo $index;?>">
											<td valign="top" align="left" class="col_1">
												<div class="fleft">
													<a href="javascript:void(0)" onclick="delCountry(this)"><i class="fa fa-lg  fa-minus-circle"></i></a>
													<span id="new_0" style="display:none" class="addNewRule" ><a href="javascript:void(0)" onclick="addCountry(this)"><i class="fa fa-lg fa-plus-circle"></i></a></span>&nbsp;&nbsp;

												</div>
												<div class="fleft">
													<select name="country[]" tabindex="10" style="width:130px;" class="country" id="country_<?php echo $index; ?>" onchange="countryShow(<?php echo $index;?>);">
														<option value="">Select</option>
														<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
															foreach($countryArray as $countryId => $country) {  ?>
															<option value="<?php echo $countryId; ?>" <?php   //echo 'Selected';  ?>><?php echo $country; ?></option>
														<?php 	} ?>
													</select>
													<br>
													<span id='field_name_empty' class="error_empty"></span>
													<span class="slabel" id="state_label_<?php echo $index; ?>" style="display:none;">State</span>
													<br>
													<select name="state[]" tabindex="10" class="state" style="display:none; width:130px;" id="state_<?php echo $index; ?>">
														<option value="">Select</option>
														<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
															foreach($usStateArray as $stateId => $state) {  ?>
															<option value="<?php echo $stateId; ?>" <?php   //echo 'Selected';  ?>><?php echo $state; ?></option>
														<?php 	} ?>
													</select>
													<span id='sample_data_empty' class="error_empty"></span>
												</div>
											</td>
											<td valign="top" class="terms_td col_2" id="terms_td_<?php echo $index; ?>" align="left">
												<textarea id="terms_0" class="terms textarea-full"  rows="4" cols="45" tabindex="11" name="tournamentConditions[]"><?php if(isset($explanationArr) && is_array($explanationArr)) echo htmlspecialchars($explanationArr[$index]);?></textarea>
											</td>
											<td valign="top" class="rules_td col_3" id="rules_td_<?php echo $index; ?>" align="left">
												<textarea id="rules_0" class="rules textarea-full"  rows="4" cols="45" tabindex="11" name="tournamentRules[]"><?php if(isset($explanationArr) && is_array($explanationArr)) echo htmlspecialchars($explanationArr[$index]);?></textarea>
											</td>
										</tr>
										<?php } ?>
										<input id="countryOldIds" name="countryOldIds" type="hidden" value="">
										<input id="stateOldIds" name="stateDeletedIds" type="hidden" value="">
									</table>
								</div>
			
			
			
			
			
			
			
			*/
	/*	}
		else{
			$jsonArray	=	array();
			$jsonArray[]	=	$tournamentDetailsResult;
			echo json_encode($jsonArray);
			//echo json_encode($tournamentDetailsResult);
		}
	*/
		/*
		if(isset($tournamentDetailsResult) && is_array($tournamentDetailsResult) && count($tournamentDetailsResult) > 0){
			if(isset($tournamentDetailsResult[0]->TournamentStatus)	&&	$tournamentDetailsResult[0]->TournamentStatus !=3){
				echo json_encode($tournamentDetailsResult);
				die();
			}
			else {
				unset($_SESSION['brand_edit_tournament']);
				//header('location:tournament#tournament_list');
				header('location:index.php');
				die();
			}
		}
		*/
	}
}
$fields			=	'*';
$condition		=	'Status = 1 ';
$gameDetails	=	$brandObj->selectGameDetails($fields,$condition);
$fields			=	' Amount ';
$condition		=	' id = '.$_SESSION['sess_brand_user_id'];
$brandDetails	=	$brandObj->getSingleBrand($fields,$condition);
$amount			=	$brandDetails[0]->Amount;
$toc_rec		=	$brandObj->getTotalRecordCount();
if(isset($_SESSION['sess_brand_user_id']) && $_SESSION['sess_brand_user_id'] != ''){
$brand_id		=	$_SESSION['sess_brand_user_id'];
}
setPagingControlValues('t.id',ADMIN_PER_PAGE_LIMIT);
/*
$fields   				= " count(tp.fktournamentsId) as playersCount,tp.PlayerCurrentHighScore,g.id as gameId,g.Name as gameName,b.id as brandId,b.Name as brandName,b.Amount,t.*";
$condition 				= "  t.Status != '3' and fkBrandsId = '".$brand_id."' ";
$playersList  	= 	$brandObj->getTournamentList($fields,$condition);
$tot_rec		=	$brandObj->getTotalRecordCount();
$items_per_group = 10;
$total_groups 	= ceil($tot_rec/$items_per_group);
*/
$condition       = " and Status !=3";
$field			 = " id as gameId,Name ";
$gameListArray  = $gameObj->selectGameDetails($field,$condition);
commonHead();
top_header();
//echo '<pre>';print_r($_SESSION);echo '</pre>';
?>
<script src="https://checkout.stripe.com/v2/checkout.js"></script>
<body>
<!-- <div id="loading" style="display:none;"><i class="fa fa-spinner fa-spin fa-4x"></i></div>  -->
	<div class="container fluid-width" id="container">
		<div id='tabshow'><input type="hidden" id="alreadyexist" value="<?php echo $tabshow; ?>"></div>
			<div class="tabbable">
				<div class="tab-content" id='list_content'>
					<h2 id="heading_title">My Tournament</h2>
						<div id="tournament_search">
							<form name="search_category" id="search_category" action="" method="post">
				
					<div class="search_box">
						<div class="row-fluid " >
							<div class="span12">
								<div class="">
									<div class="row-fluid">
										<div class="span5 row-fluid">
											<label class="control-label span4">Tournament</label>
											<div class="controls span8"><input  type="text" class="input w70" title="tournament name" name="tournament" id="tournament" value="<?php if(isset($_SESSION['brand_sess_tournament_name']) && $_SESSION['brand_sess_tournament_name'] != '') echo $_SESSION['brand_sess_tournament_name'];?>"></div>
										</div>
																			
										<div class="span4 row-fluid">
											<label class="control-label  span4">Start Date</label>
											<div class="controls  span8"><input  type="text" class="input medium datepicker w70" autocomplete="off"  title="Select Date" id="startdate" name="from_date" value="<?php if(isset($_SESSION['brand_sess_tournament_fromDate']) && $_SESSION['brand_sess_tournament_fromDate'] != '') echo date('m/d/Y',strtotime($_SESSION['brand_sess_tournament_fromDate']));?>"></div>
										</div>
										
										<div class="span3 row-fluid">
										<div class="controls span12"><input type="checkbox" id="pinBased_tournament" name="pinBased_tournament" <?php //if(isset($_SESSION['brand_sess_pinbased_check'])	&&	$_SESSION['brand_sess_pinbased_check'] == 1) { echo 'checked'; } ?> /> <label for="pinBased_tournament">Pin Based</label></div>
										</div>
									</div>
									
									<div class="row-fluid">
									
									<div class="span5 row-fluid">
										<label class="control-label span4">Game</label>
										<div class="controls span8">
											<select name="game" id="game" class="input w70">
											<option value="">Select</option>
											<?php if(isset($gameListArray) && is_array($gameListArray) && count($gameListArray) > 0){
													foreach($gameListArray as $key => $value) { ?>
															<option value="<?php echo $value->Name; ?>" <?php if(isset($_SESSION['brand_sess_tournament_game']) && $_SESSION['brand_sess_tournament_game'] != ''	&&	$_SESSION['brand_sess_tournament_game'] == $value->Name) echo 'selected'; ?>><?php echo $value->Name; ?></option>
												<?php	}
												}?>
											</select>
										</div>
									</div>
									
									<div class="span4 row-fluid">
											<label class="control-label span4">End Date</label>
											<div class="controls span8"><input type="text" class="input medium datepicker w70" autocomplete="off"   title="Select Date" id="enddate" name="to_date" value="<?php if(isset($_SESSION['brand_sess_tournament_endDate']) && $_SESSION['brand_sess_tournament_endDate'] != '') echo date('m/d/Y',strtotime($_SESSION['brand_sess_tournament_endDate']));?>"></div>
										</div>
									<div class="span3 row-fluid">
											<div class="controls span8"><input type="checkbox" id="locationbased_tournament" name="locationbased_tournament" <?php //if(isset($_SESSION['brand_sess_locationbased_check'])	&&	$_SESSION['brand_sess_locationbased_check'] == 1) { echo 'checked'; } ?> /> <label for="locationbased_tournament">Location Based</label></div>
										</div>									
									
									<div class="span12" align="center">
									<input type="button" onclick="submit_search()" class="btn btn-primary btn-large" name="Search" id="Search" value="Search">
									</div>
									</div>
									
								</div>
							</div>
						</div>
					</div>
				
				   
					</form>
				</div>
					<p class="msg_hgt" style="height:auto;"><span id='message' style="display:none;margin:0px 0 15px" class="success_msg"><?php echo $message; ?></span></p>
			
						<div class="tab-pane active" id="formcontrols"  >
							
						</div>
						<div class="tab-pane" id="pagingControls">
						</div>
						<div class="tab-pane" id="jscontrols">
						<div ></div>
							<form class="form-horizontal" id="edit-profile"  method="post" action="#tournament_add" onsubmit="return addTournament();" ><!-- onclick="return addTournament();"onsubmit="return addTournament();" -->
								<fieldset>
									<?php if(isset($_SESSION['brand_edit_tournament']) && $_SESSION['brand_edit_tournament'] != ''){?>
									<input type="hidden" id="edit_page" name="edit_page" value="<?php echo $_SESSION['brand_edit_tournament']; ?>">
									<?php } ?>
									<div class="control-group" style="margin:0">											
										<label for="password1" class="control-label" >Select Game</label>
										<div class="controls select_game" id='game_select'>											
											<h4>Most Popular <span class="dot_line"> </span></h4>
											<ul>
										<?php if(isset($gameDetails) && count($gameDetails) > 0){
													foreach ($gameDetails as $g_key=>$g_val){
														$game_logo	=	$g_val->Photo;
														$game_image_path = GAMES_IMAGE_PATH.'no_game.jpeg';
														if($game_logo != '' ){
															if (!SERVER){
																if(file_exists(UPLOAD_GAMES_PATH_REL.$game_logo)){
																	$game_image_path = GAMES_IMAGE_PATH.$game_logo;
																}
															}
															else{
																if(image_exists(10,$game_logo))
																	$game_image_path = GAMES_IMAGE_PATH.$game_logo;
															}
														}
														?>
											
												<li>
													<label for="game_id_<?php echo $g_val->id;?>" title="<?php echo $g_val->Name;?>">
														<img src="<?php echo $game_image_path;?>" width="60" height="60" alt="" ><?php echo displayText($g_val->Name,9);?>
													</label>
													<input type="Radio" name="game_id" required="required" id="game_id_<?php echo $g_val->id;?>"  value="<?php echo $g_val->id;?>">
												</li>
												
											
											<?php } } ?>
											</ul>
										</div>
									</div>
						
									<div class="control-group">											
										<div class="controls">											
											<p class="spliter">&nbsp;</p>
										</div>
									</div>
							
							<!-- - -->
							<div class="control-group">											
								<label for="password1" class="control-label">Tournament Name</label>
								<div class="controls search"><input type="text" placeholder="e.g.McDonald's Flappy Bird" id="tournament_name" name="tournament_name" class="span6" required></div>
								<p align="center" id="tournament_msg"></p>
							</div>
							<!-- - -->
							<div class="control-group">
							
								<div class="controls">											
									<p class="spliter">&nbsp;</p>
								</div>
							</div>
							<div class="control-group">											
								<label for="password1" class="control-label">Max. Players</label>
								<div class="controls search" >	
										<div class="select_inc">
										<input style="width:96%;" type="text" onkeypress="return isNumberKey(event);" class="" required name="max_player" id="max_player" value="2"><div class="inc button-inc"><i class="fa fa-plus "></i></div><div class="dec button-inc"><i class="fa fa-minus "></i></div></div>
										
								<!--<div class="span3">Total fee <strong>$1.50</strong> (5c per user)</div>-->
								</div>
							</div>
							
							
							<div class="control-group">											
								<div class="controls">											
									<p class="spliter">&nbsp;</p>
								</div>
							</div>
							<div class="control-group">											
								<label for="password1" class="control-label">No.of Turns</label>
								<div class="controls search">	
										<div class="select_inc">
										<input type="text" style="width:96%;" onkeypress="return isNumberKey(event);" class="w30" required name="no_of_turns" id="no_of_turns" value="3"><div class="inc button-inc"><i class="fa fa-plus "></i></div><div class="dec button-inc"><i class="fa fa-minus "></i></div></div>	
										
										<div class="span3"><strong>Per day</strong></div>
								</div>
							</div>
							<div class="control-group">											
								<div class="controls">											
									<p class="spliter">&nbsp;</p>
								</div>
							</div>
							
							<div class="control-group">											
								<label for="password1" class="control-label">Prize(Coins)</label>
								<div class="controls search">	
								<div  class="select_inc">
									<input type="text" style="width:96%;" onkeypress="return isNumberKey(event);" class="w30" name="prize" id="prize" required placeholder="e.g. 15000" value=""><div class="inc button-inc"><i class="fa fa-plus "></i></div><div class="dec button-inc"><i class="fa fa-minus "></i></div>
									<input type='hidden' id='assignedCoins' name='assignedCoins' value='<?php echo $assignedPrize; ?>' >
								</div>		
										<div class="span3" id="myamount">You have <strong><?php echo number_format($amount);?></strong> coins
										<input type='hidden' value='<?php echo $amount; ?>' name='current_amount' id='current_amount'>
										&nbsp;&nbsp;
										<!--<script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
										  data-key="<?php //echo $stripe['publishable_key']; ?>"
										  data-amount="5000" data-description="One year's subscription"></script>-->
										<a href="javascript:void(0);" onkeypress="return isNumberKey(event);" onclick='showpayment();' id="buymore" class="" style="font-size:11px;">Buy More</a>
										</div><Br>
										<div id='payment_block' style='display:none;'><br>
												<input onkeypress="return isNumberKey(event);" type='text' name='buy_amount' class='w30' id='buy_amount' value=''>
												<input type='hidden' name='conversionvalue' class='' id='conversionvalue' value='<?php echo $conversionValue;?> '>	
												<button style='padding-left:10px;margin-left:5px;width:65px;margin-bottom:0' class="btn btn-primary" type="button" id='customButton' name="Buy" value="Buy">Buy</button>
										</div> 
								<!--<div class="clear"> <a href="#" class="" style="font-size:11px;">Or add custom prize instead of coins</a> </div>-->
								</div>
							
							</div>
							<div class="control-group">											
								<div class="controls">											
									<p class="spliter">&nbsp;</p>
								</div>
							</div>
							<div class="control-group">											
								<label for="free" class="control-label" style="padding-top:4px">Entry Fees</label>
									<div class="controls">
										<label for="free">
										<input type="radio" name="entry_type" id="free"  value="1" checked="checked" onclick="show_pay(1);">&nbsp;&nbsp;Free&nbsp;&nbsp;&nbsp;</label>
										<label for="pay">
										<input type="radio" name="entry_type" id="pay" value="2" onclick="show_pay(2);" disabled='true'>&nbsp;&nbsp;Pay</label>										
										
									</div>
								<div class="controls search" id="pay_type" style="display:none">		
										<input type="text" onkeypress="return isNumberKey(event);" class="w30" name="entry_fee" id="entry_fee" required value="1000">&nbsp;&nbsp;coins										
										
										</br>User will &quot;pay&quot;<strong>1000</strong> coins to enter the tournament
								</div>
							</div>
							<div class="control-group">											
								<div class="controls">											
									<p class="spliter">&nbsp;</p>
								</div>
							</div>
							<div class="control-group">											
								<div class="controls">											
										<input type="Checkbox" onchange="return check_generate_pin(this);" name="pin_chk" id="pin_chk"  value="1" <?php //echo 'checked';?>>&nbsp;&nbsp;<label for="pin_chk">PIN Required</label>
										<?php //if(isset($_SESSION['brand_edit_tournament']) && $_SESSION['brand_edit_tournament'] != '') { ?>
										&nbsp;&nbsp;&nbsp;<a id="generatepin" style="display:none" href="GeneratePin?tournamentId=<?php //echo $_SESSION['brand_edit_tournament'];?>" title="Generate Pin" class="fancybox-manual-b fancybox">Generate PIN</a>
										<?php //} ?>
										<p class="user_txt">User will need to enter correct code to be able to join the tournament. List of codes will be generated and sent to your email address</p>
								</div>
							</div>
							<div class="control-group">											
								<div class="controls">											
									<p class="spliter">&nbsp;</p>
								</div>
							</div>
							<div class="control-group">									
								<label for="location" class="control-label" style="padding-top:2px">Location</label>
								<div class="controls">
									<input type="Checkbox" onclick="return search();"  id="location" name="location" value="1">&nbsp;&nbsp;<label for="location">Location restricted</label>
									<div id='share_search' style="display:none;">
										<table border="0" width="70%" align="">
											<tbody>
											<tr>	
												<th>Action</th>
												<th align="left">Country</th>
												<th align="left">State/Location</th>
											</tr>
											<tr id="location_clone" class="clone" clone="1" style="display:none">
												<td width="10%" align="center">
													<a href="javascript:void(0)" class="locminus" onclick="manageLocation(this,'2')" id="minus_clone" style="display:none;"><i class="fa fa-lg fa-minus-circle"></i></a>
													<a href="javascript:void(0)" class="locplus" onclick="manageLocation(this,'1')" id="plus_clone"><i class="fa fa-lg fa-plus-circle"></i></a>																										
												</td>
												<td width="20%" align="left">
													<select name="country_clone" tabindex="10" class="country" id="country_loc_clone" onchange="locationShow('1',1);">
														<option value="">Select</option>
														<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
															foreach($countryArray as $countryId => $country) {  
																?>
															<option value="<?php echo $countryId; ?>" <?php if($countryId == 1) echo 'Selected';  ?>><?php echo $country; ?></option>
														<?php 	} ?>
													</select>
												</td>
												<td width="40%" align="left">
													<div id="col_loc_clone_3" style="<?php if(!isset($currentLocationid)) echo "display:none;";  ?>">
														<select name="state_clone" tabindex="10" class="state" id="state_loc_clone" style="display:none;" onchange="locationShow('1',2);">
															<option value="">Select</option>
															<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
																foreach($usStateArray as $stateId => $state) {  ?>
																<option value="<?php echo $stateId; ?>" <?php   //echo 'Selected';  ?>><?php echo $state; ?></option>
															<?php 	} ?>
														</select>
														<input type='search' class="locationsearch" id='locationsearch_clone' name='locationsearch_clone' value="" onkeypress="autoCompleteLocation(1)"> 
														<input type='hidden' class="latitude" id='latitude_clone' name='latitude_clone[]' value=""> 
														<input type='hidden' class="longitude" id='longitude_clone' name='longitude_clone[]' value=""> 
													</div>
												</td>																								
											</tr>
											</tbody>
											<tbody id="RestrictedLocationContent">
												<tr id="location_1" class="clone" clone="1">
													<td width="10%" align="center">
														<a href="javascript:void(0)" class="locminus" onclick="manageLocation(this,'2')" id="minus_1" style="display:none;"><i class="fa fa-lg fa-minus-circle"></i></a>
														<a href="javascript:void(0)" class="locplus" onclick="manageLocation(this,'1')" id="plus_1"><i class="fa fa-lg fa-plus-circle"></i></a>																										
													</td>
													<td width="20%" align="left">
														<select name="countryLocation[]" tabindex="10" class="country" id="country_loc_1" onchange="locationShow('1',1);">
															<option value="">Select</option>
															<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
																foreach($countryArray as $countryId => $country) {  
																	?>
																<option value="<?php echo $countryId; ?>" <?php if(isset($currentLocationid) && $currentLocationid==$countryId) echo 'Selected';  ?>><?php echo $country; ?></option>
															<?php 	} ?>
														</select>
													</td>
													<td width="40%" align="left">
														<div id="col_loc_1_3" style="<?php if(!isset($currentLocationid)) echo "display:none;";  ?>">
															<select name="stateLocation[]" tabindex="10" class="state" id="state_loc_1" style="display:none;" onchange="locationShow('1',2);">
																<option value="">Select</option>
																<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
																	foreach($usStateArray as $stateId => $state) {  ?>
																	<option value="<?php echo $stateId; ?>" <?php   //echo 'Selected';  ?>><?php echo $state; ?></option>
																<?php 	} ?>
															</select>
															<input type='search' class="locationsearch" id='locationsearch_1' name='locationsearch[]' value="" onkeypress="autoCompleteLocation(1)"> 
															<input type='hidden' class="latitude" id='latitude_1' name='latitude[]' value=""> 
															<input type='hidden' class="longitude" id='longitude_1' name='longitude[]' value=""> 
														</div>
													</td>																								
												</tr>
												<input type="hidden" name="totLoc" id="totLoc" value="1">
											</tbody>
										</table>										
									</div>
									
									<div id='location_error'></div>
									<p>If you set the exact address then users will able to play only if they are in the perimeter of <?php echo $distance;?>m from this address.</p>
								</div>
								
							</div>
							<div class="control-group">											
								<div class="controls">											
									<p class="spliter">&nbsp;</p>
								</div>
							</div>
							<div class="control-group">									
								<label for="time" class="control-label">Time</label>
								<div class="controls">		
									<span><input id="start_time" type="text" class="w30" autocomplete="off" onkeypress="return isCalender(event);" required="required" ></span>	to	
									<span><input id="end_time" type="text" class="w30"   autocomplete="off" onkeypress="return isCalender(event);" required="required" ></span>	
								</div>
							</div>
							<div class="control-group">
								<div class="controls">											
									<p class="spliter">&nbsp;</p>
								</div>
							</div>
							<div class="control-group">
								<label for="privacy_policy" class="control-label">Privacy Policy</label>
								<div class="controls">											
										<textarea name="privacy_policy" class="w-full" id="privacy_policy" rows="6" cols="130"><?php echo $privacypolicyContent; ?></textarea>
								</div>
							</div>
							
						<!--  BEGIN: for rules management -->	
							<div class="control-group f14  ">
								<h2 id="heading_title">Tournament rules</h2>
								<div class="">	
								<input type='hidden' value='<?php echo $generalTerms; ?>' name='terms_cond_template' id='terms_cond_template'>
								<input type='hidden' value='<?php echo $gamerulesContent; ?>' name='game_rules_template' id='game_rules_template'>
									<table cellpadding="0" cellspacing="7" id="inputParam" border="0" width="100%" align="center">
										<tr align="center" height="30">
											<td class="col_1" align="left"><div class="fleft">&nbsp;</div><div class="fleft">All&nbsp;&nbsp;</div></td>
											<td class="col_2" align="left">Terms and Condition</td>
											<td class="col_3" align="left">Game Rules</td>
										</tr>
										<tr>
											<td class="col_1">&nbsp;</td>
											<td class="col_2"><textarea class="textarea-full" id="terms_condition" rows="2" cols="32" tabindex="11" name="terms_condition"><?php echo $generalTerms; ?></textarea></td>
											<td class="col_3 content_ipad"><textarea class="textarea-full" id="game_rules" rows="2" cols="32" tabindex="11" name="game_rules"><?php echo $gamerulesContent; ?></textarea></td>
										</tr>
									</table>
								</div>		
							</div>		
							<div class="control-group f14" id="tournament_rules_block">						
								
								<div class="">	
									<table cellpadding="0" cellspacing="7" id="inputParam" border="0" width="100%" align="center">
										<tr align="center" height="30">
											<td class="col_1" align="left"><div class="fleft">&nbsp;</div><div class="fleft">Country</div></td>
											<td class="col_2" align="left">Terms and Conditions</td>
											<td class="col_3" align="left">Game Rules</td>
										</tr>
										<?php $rowCount	=	1; for($index = 0;$index < $rowCount;$index++) { ?>
										<tr align="center" class="clone" clone="<?php echo $index;?>">
											<td valign="top" align="left" class="col_1">
												<div class="fleft">
													<a href="javascript:void(0)" onclick="delCountry(this)"><i class="fa fa-lg  fa-minus-circle"></i></a>
													<span id="new_0" style="display:none" class="addNewRule" ><a href="javascript:void(0)" onclick="addCountry(this)"><i class="fa fa-lg fa-plus-circle"></i></a></span>&nbsp;&nbsp;

												</div>
												<div class="fleft">
													<select name="country[]" tabindex="10" style="width:130px;" class="country" id="country_<?php echo $index; ?>" onchange="countryShow(<?php echo $index;?>);">
														<option value="">Select</option>
														<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
															foreach($countryArray as $countryId => $country) {  ?>
															<option value="<?php echo $countryId; ?>" <?php   //echo 'Selected';  ?>><?php echo $country; ?></option>
														<?php 	} ?>
													</select>
													<br>
													<span id='field_name_empty' class="error_empty"></span>
													<span class="slabel" id="state_label_<?php echo $index; ?>" style="display:none;">State</span>
													<br>
													<select name="state[]" tabindex="10" class="state" style="display:none; width:130px;" id="state_<?php echo $index; ?>">
														<option value="">Select</option>
														<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
															foreach($usStateArray as $stateId => $state) {  ?>
															<option value="<?php echo $stateId; ?>" <?php   //echo 'Selected';  ?>><?php echo $state; ?></option>
														<?php 	} ?>
													</select>
													<span id='sample_data_empty' class="error_empty"></span>
												</div>
											</td>
											<td valign="top" class="terms_td col_2" id="terms_td_<?php echo $index; ?>" align="left">
												<textarea id="terms_0" class="terms textarea-full"  rows="4" cols="45" tabindex="11" name="tournamentConditions[]"><?php if(isset($explanationArr) && is_array($explanationArr)) echo htmlspecialchars($explanationArr[$index]);?></textarea>
											</td>
											<td valign="top" class="rules_td col_3" id="rules_td_<?php echo $index; ?>" align="left">
												<textarea id="rules_0" class="rules textarea-full"  rows="4" cols="45" tabindex="11" name="tournamentRules[]"><?php if(isset($explanationArr) && is_array($explanationArr)) echo htmlspecialchars($explanationArr[$index]);?></textarea>
											</td>
										</tr>
										<?php } ?>
										<input id="countryOldIds" name="countryOldIds" type="hidden" value="">
										<input id="stateOldIds" name="stateDeletedIds" type="hidden" value="">
									</table>
								</div>
							</div>
						<!--  END: for rules management -->	
							<div class="form-actions no-padding" align="center">
										<input type="hidden" id="edit_id" name="edit_id">
			 							<button class="btn btn-primary btn-large" type="submit" id="save_button" name="add" value="save" >Save <?php if(isset($tournamentStatus) &&	($$tournamentStatus != '')) echo $tournamentStatus; ?></button> 
										<button class="btn btn-large" onclick="return show_tournament_list(1);" type="button">Cancel</button><!--  type="submit" name="add" value="save" -->
							</div> <!-- /form-actions -->
						</fieldset>
					</form>
				</div>
				<div class="tab-pane active" id="result"  ></div>
			</div>
		</div>
	</div> <!-- /widget-content -->
	   
 <?php 
commonFooter();
?>

<script>
//redirect(2);
//$('#generatepin').prop('checked', true);
check_generate_pin('#pin_chk');
function check_generate_pin(thisval) {
	if(location.hash.slice(1) == 'tournament_edit'){
		if(!$(thisval).is(':checked')) {
			$('#generatepin').hide();
		}
		else
			$('#generatepin').show();
	}
	if($(thisval).is(':checked')) {
		$('#location').prop('checked', false);
		$('#share_search').hide();
	}
	
}
	// BEGIN: strip payment call
  var handler = StripeCheckout.configure({
	key: '<?php echo $stripe['publishable_key']; ?>',
	//image: '/square-image.png',
	token: function(token, args) {
		var amount	=	parseFloat($('#buy_amount').val());
		if($.trim($('#buy_amount').val()) == ''){
			alert('Enter valid coins');
			$('#buy_amount').focus();
			return false;
		}
		$.post('../brand/models/AjaxAction.php',{action:'PAYMENT_MODULE', 
			stripeToken:token.id, amount:parseFloat(amount), brandId:'<?php echo $brand_id; ?>', email:token.email,brandName : '<?php echo ((isset($_SESSION['sess_brand_name'])) ? $_SESSION['sess_brand_name'] : '') ?>'
		},
		function(result){
			if($.trim(result) == 'Error') {
				alert('Problem in processing payment. Please try again.');
			}
			else {
				$('#buy_amount').val('');
				$('#payment_block').hide();
				$('#myamount strong').html(result);
				$('#current_amount').val(result);
				//alert(result);
			}
		});
		// Use the token to create the charge with a server-side script.
		// You can access the token ID with `token.id`
	}
  });
	// End: strip payment call
  
  // BEGIN: To validate the buy text box
  document.getElementById('customButton').addEventListener('click', function(e) {
	// Open Checkout with further options
	var convert = parseFloat($('#conversionvalue').val());
	var buy		= parseFloat($('#buy_amount').val());	
	var amount	=	buy / convert ; // parseFloat($('#buy_amount').val());

	if($.trim(amount) == ''	||	$.trim(amount) == 'NaN' )	{
		alert('Enter valid coins');
		$('#buy_amount').focus();
		return false;		
	}
		
	handler.open({
	  name: 'Tilt Brand Portal',
	  description: 'Buy coins',
	  amount: (parseFloat(amount) * 100)
	});
	e.preventDefault();
  });
	$(document).mouseup(function (e)
	{
		var container = $(".stripe_checkout_app");

		if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
		{
			container.hide();
		}
	});
function showpayment()	{
	//$('#payment_block').show();
	$('#payment_block').toggle();
}
 $('.fancybox').fancybox({
		type : 'ajax',
		helpers: { 
        title: null
    }
	   });
/*
	//type : 'iframe'
		autoSize : false,
		width:500,
		height:600,
 overlayShow: false,
    frameWidth: 500, // set the width
    frameHeight: 100,
	   maxWidth    : 700,
        maxHeight   : 500,
      //  fitToView   : false,
        width       : '70%',
        height      : '70%',
        autoSize    : false,
        closeClick  : false,
        openEffect  : 'none',
        closeEffect : 'none'
*/
	   
function show_pay(val)
{
  if(val == 1)
  	 $('#pay_type').hide();
  	
 else
   $('#pay_type').show();
}

$(function(){
	$('#start_time').datetimepicker({
  		format:'m/d/Y H:i',
  		minDate:0,
  		onShow:function( ct ){
   			this.setOptions({
   			})
   		},
 	});
	var logic = function( currentDateTime ){
		var starting_time	=	$('#start_time').val();
		var ending_time		=	$('#end_time').val();
		start_dArr = starting_time.split(" ");  // ex input "2010-01-18"
		start_DateArr = start_dArr[0];
		start_TimeArr = start_dArr[1];
		end_dArr = ending_time.split(" ");  // ex input "2010-01-18"
		end_DateArr = end_dArr[0];
		end_TimeArr = end_dArr[1];
		if(start_DateArr == end_DateArr){
			 tme_new		=	start_TimeArr;
		}
		else
			 tme_new		=	false;
		if(start_DateArr != '' && start_TimeArr != ''){
			this.setOptions({
				minDate:start_DateArr,
				minTime:tme_new
			});
		}else
			this.setOptions({
				Date:start_DateArr
			});
	};
	$('#end_time').datetimepicker({
		onChangeDateTime:logic,
		onShow:logic
	});
});


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
 $(document).ready(function(){
 //first_int();
 
 	
});

function testInit(){
 tinymce.init({
	height 	: "200",
	mode : "specific_textareas",
	selector: "textarea", statusbar: false, menubar:false,
	plugins	: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
				],
	toolbar: "insertfile undo redo styleselect | bold italic  alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link "
	});
}
function initEditor(id_val){
	tinymce.init({
	height 	: "200",
	mode : "specific_textareas",
	selector: "textarea#"+id_val, statusbar: false, menubar:false,
	plugins	: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
				],
	toolbar: "insertfile undo redo styleselect | bold italic  alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link "
	});
	
	
}
<?php 
if(isset($setting_details) && is_array($setting_details) && count($setting_details)>0){
	foreach($setting_details as $key => $value){
?>
var termsTemplate =	'<?php $value->TermsAndConditions; ?>';
var privacyTemplate = '<?php $value->GameRules; ?>';
//alert('11111111'+);
<?php	}
}
?>

</script>
</body>
</html>