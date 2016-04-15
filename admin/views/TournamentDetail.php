<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/UserController.php');
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
require_once('controllers/CoinsController.php');
$coinsObj   =   new TournamentController();
admin_login_check();
commonHead();
$locationBased		=	$countryState = '';
$original_image_path =  $original_cover_image_path = $actualPassword = $string = '';
$interestStatus		 =	$entryfee_flag	=	0;
$entryfee = $back	= '';
if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
	$condition       = "  AND t.id = ".$_GET['viewId']." and t.Status in (1,2) LIMIT 1 ";
	if(isset($_GET['createdType'])  && $_GET['createdType'] == 1){
		$field				=	' g.Name as gameName,TournamentName,t.MaxPlayers,TotalTurns,EntryFee,StartDate,EndDate,Prize,GameType,Type,Elimination,SharedLocation,LocationRestrict,PIN,u.FirstName,u.LastName,LocationBased,FeeType, t.TournamentLocation, t.fkCountriesId, t.fkStatesId, t.DelayTime, g.PlayTime ';
		$tournamentDetailsResult  = $tournamentObj->getUserTournamentDetails($field,$condition);
	}else if(isset($_GET['createdType'])  && $_GET['createdType'] == 3){
		$fields				=	'g.Name as gameName,TournamentName,t.MaxPlayers,TotalTurns,EntryFee,StartDate,EndDate,Prize,GameType,Type,Elimination,SharedLocation,LocationRestrict,PIN,gd.Company as Name,LocationBased, FeeType, t.TournamentLocation, t.fkCountriesId, t.fkStatesId,t.DelayTime, g.PlayTime ';		
		$tournamentDetailsResult  = $tournamentObj->getGameDeveloperDetails($fields,$condition);
	}
	if(isset($tournamentDetailsResult) && is_array($tournamentDetailsResult) && count($tournamentDetailsResult) > 0){
		$tournament    		=	$tournamentDetailsResult[0]->TournamentName;
		if(isset($_GET['createdType']) && $_GET['createdType'] == 1 ){
			$userName	=	'';
			if(isset($tournamentDetailsResult[0]->FirstName)	&&	isset($tournamentDetailsResult[0]->LastName)) 	
				$userName	=	ucfirst($tournamentDetailsResult[0]->FirstName).' '.ucfirst($tournamentDetailsResult[0]->LastName);
			else if(isset($tournamentDetailsResult[0]->FirstName))	
				$userName	=	 ucfirst($tournamentDetailsResult[0]->FirstName);
			else if(isset($tournamentDetailsResult[0]->LastName))	
				$userName	=	ucfirst($tournamentDetailsResult[0]->LastName);
		}
		else if(isset($_GET['createdType']) && $_GET['createdType'] == 3 ){
			if(isset($tournamentDetailsResult[0]->Name)) 	
				$userName	=	ucfirst($tournamentDetailsResult[0]->Name);
		}
		$game				=	$tournamentDetailsResult[0]->gameName;
		$maxplayers			=	$tournamentDetailsResult[0]->MaxPlayers;
		$turns       		=	$tournamentDetailsResult[0]->TotalTurns;
		$entryfee_flag   	=	$tournamentDetailsResult[0]->FeeType;
		$startdate       	= 	$tournamentDetailsResult[0]->StartDate;
		$enddate  			= 	$tournamentDetailsResult[0]->EndDate;
		$prize    			= 	$tournamentDetailsResult[0]->Prize;
		$gametype    		= 	$tournamentDetailsResult[0]->GameType;
		$featured    		= 	$tournamentDetailsResult[0]->Type;
		$elimination		=	$tournamentDetailsResult[0]->Elimination;
		$entryfee      		= 	$tournamentDetailsResult[0]->EntryFee;
		$can_we_start		=	isset($tournamentDetailsResult[0]->DelayTime) ? $tournamentDetailsResult[0]->DelayTime : '';
		$play_time			=	isset($tournamentDetailsResult[0]->PlayTime) ? $tournamentDetailsResult[0]->PlayTime : '';
		if($tournamentDetailsResult[0]->PIN==1	&&	$tournamentDetailsResult[0]->LocationRestrict==1)
			$string	=	'PIN Based & Location restricted';
		else if($tournamentDetailsResult[0]->PIN==1)
			$string	=	'PIN Based';
		else if($tournamentDetailsResult[0]->LocationRestrict==1)
			$string	=	'Location restricted';
		else if($tournamentDetailsResult[0]->LocationBased==1){
			$string	=	'Location Based';
			$country	=	$coinsObj->getCountryList(' Country ', ' id = '.$tournamentDetailsResult[0]->fkCountriesId.' AND Status = 1 ');
			$state		=	$coinsObj->getStateList(' State ', ' id = '.$tournamentDetailsResult[0]->fkStatesId.' AND fkCountriesId = '.$tournamentDetailsResult[0]->fkCountriesId.'  AND Status = 1 ');
			$locationBased		=	$tournamentDetailsResult[0]->TournamentLocation;
			if(is_array($state) && isset($state[0]->State) && $state[0]->State !='')
				$countryState		= 	$state[0]->State;
			if(is_array($country) && isset($country[0]->Country) && $country[0]->Country !='')
				if(!empty($countryState))
					 $countryState		.= 	", ".$country[0]->Country;
				else $countryState		= 	$country[0]->Country;
		}
		else
			$string	=	'';
		
		if($featured == 4){
			$condition	=	" AND fkTournamentsId = ".$_GET['viewId']." AND Status = 1";
			$customPrizeResult  = $tournamentObj->getCustomPrize(' * ',$condition);
			
		}
		if($tournamentDetailsResult[0]->LocationRestrict == 1){
			$locationRestrictResult	=	$tournamentObj->getLocationRestrict(' `LocationValue` , Country, s.State ',' AND `fkTournamentsId` ='.$_GET['viewId'].' AND lr.Status = 1');
			$locationRestrict	=	'';
			if(is_array($locationRestrictResult) && count($locationRestrictResult)>0){
				foreach($locationRestrictResult as $key=>$value){
					$temp = '';
					$temp	.=	$value->LocationValue;
					if($temp != '')
						$temp	.=	", ".$value->State;
					else
						$temp	.=	$value->State;
					if($temp != '')
						$temp	.=	", ".$value->Country;
					else
						$temp	.=	$value->Country;
					if($temp != ''){
						if($locationRestrict != '')
							$locationRestrict	.= "<br>".$temp;
						else
							$locationRestrict	.= $temp;
					}
				}
			}
		}
		$tournamentCoupon	=	$tournamentObj->getTournamentsCoupon(' `CouponAdLink` , `URL` , `File` , `CouponTitle` , `CouponLimit` , `Type` , `InputType` , `CouponStartDate` , `CouponEndDate` ',' AND `fkTournamentsId` ='.$_GET['viewId'] .' AND Status = 1 ');
		$couponDetail =	$bannerDetail	=	$youtubeDetail	= array();
		if(is_array($tournamentCoupon) && count($tournamentCoupon)>0){
			
			foreach($tournamentCoupon as $key=>$value ){
				if($value->Type == 1){
					$couponDetail['CouponAdLink']		= $value->CouponAdLink;
					$couponDetail['CouponTitle'] 		= $value->CouponTitle;
					$couponDetail['File'] 				= $value->File;
					$couponDetail['CouponLimit'] 		= $value->CouponLimit;
					if(isset($value->CouponStartDate)	&&	$value->CouponStartDate !='0000-00-00 00:00:00'){
						$couponDetail['CouponStartDate'] 	=	date('m/d/Y',strtotime($value->CouponStartDate));
					}
					if(isset($value->CouponEndDate)	&&	$value->CouponEndDate !='0000-00-00 00:00:00'){
						$couponDetail['CouponEndDate']	=	date('m/d/Y',strtotime($value->CouponEndDate));
					}
				}
				if($value->Type == 2){
					$bannerDetail['InputType']			= $value->InputType;
					$bannerDetail['URL'] 				= $value->URL;
					$bannerDetail['File'] 				= $value->File;
					$bannerDetail['CouponAdLink'] 		= $value->CouponAdLink;
				}
				if($value->Type == 3){
					$youtubeDetail['InputType']			= $value->InputType;
					$youtubeDetail['URL'] 				= $value->URL;
					$youtubeDetail['CouponAdLink'] 		= $value->CouponAdLink;
					$youtubeDetail['File'] 				= $value->File;
				}
			}
		}
	}	
}
if(isset($_GET['back']) && $_GET['back'] == ''){
	$href_page	=	'BrandList';
	$back		=	'back=1';
}
$from = '';
if((isset($_GET['from']) && $_GET['from'] != '') && (isset($_GET['brand_id']) && $_GET['brand_id'] != '' )  ){
	$from = '?from='.$_GET['from'].'&brand_id='.$_GET['brand_id'];
	if(isset($_GET['hide_players']) && $_GET['hide_players'] != ''){
		$from .= '&hide_players='.$_GET['hide_players'];
	}
}
?>
<body>
	<?php if((isset($_GET['back']) && $_GET['back'] == 'TournamentList') || (isset($from) && $from != '')){} else { ?>
<?php	top_header();   } ?>
							 <div class="box-header"><h2><i class='fa fa-search'></i>Tournament Details</h2></div>
						  		<div class="clear">
								 <table align="center" cellpadding="0" cellspacing="0" border="0" class="list " width="98%">			        
									<tr>
										<td align="center">
											<table cellpadding="0" cellspacing="0" align="center" border="0" width="80%">
												<tr><td class="msg_height"></td></tr>
												<tr>
													<td align="left" valign="top" width="15%"><label>Tournament Name</label></td>
													<td align="center" valign="top" width="3%">:</td>										
													<td align="left" valign="top" width="32%">
														<?php if(isset($tournament) && $tournament != '') echo ucfirst($tournament);  else echo '-'; ?>
													</td>
												<?php if(isset($_GET['createdType']) && $_GET['createdType'] == 1 ){?>
													<td align="left" valign="top" width="15%"><label>User</label></td>
													<td align="center" valign="top" width="3%">:</td>
													<td align="left" valign="top" width="32%"><?php if(isset($userName) && $userName != '') echo ucfirst($userName); else echo '-'; ?></td>
											<?php } else if(isset($_GET['createdType']) && $_GET['createdType'] == 3 ){?>
														<td align="left" valign="top" width="15%"><label>Developer & Brand</label></td>
														<td align="center" valign="top" width="3%">:</td>
														<td align="left" valign="top" width="32%"><?php if(isset($userName) && $userName != '') echo ucfirst($userName); else echo '-'; ?></td>
											<?php } ?>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top" width="15%"><label>Game</label></td>
													<td align="center" valign="top" width="3%">:</td>										
													<td align="left" valign="top"><?php if(isset($game) && $game != '') echo $game;  else echo '-'; ?></td>
													<td align="left" valign="top"><label>Game Type</label></td>
														<td align="center" valign="top">:</td>
														<td align="left" valign="top">
															<?php 
														if(isset($gametype) && $gametype != '0') {
															if(isset($game_type[$gametype])) {
																echo $game_type[$gametype]; 
															}
															else  echo '-';
														}else echo '-';?>
													</td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top" ><label>No. of Turns <span>(per day)</span></label></td>
													<td align="center" valign="top">:</td>										
													<td align="left" valign="top"><?php if(isset($gametype) && $gametype == 2 ) echo "1"; else if(isset($turns) && $turns != '0') echo $turns; else echo '-'; ?></td>
													<td align="left" valign="top" ><label>Maximum Players</label></td>
													<td align="center" valign="top">:</td>										
													<td align="left" valign="top"><?php if(isset($maxplayers) && $maxplayers != '0') echo $maxplayers; else echo '-'; ?></td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top"><label>Start Date</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top" ><?php  if(isset($startdate) && $startdate != '0000-00-00 00:00:00' ) echo date('m/d/Y H:i',strtotime($startdate)); else echo '-';  ?></td>
													<?php if(isset($gametype) && $gametype == '2' ) { ?>
														<td align="left" valign="top"><label>Can Start</label></td>
														<td align="center" valign="top">:</td>
														<td align="left" valign="top"><?php if(isset($can_we_start) && $can_we_start != '00:00:00' && $can_we_start != '') echo $can_we_start; else echo '-'; ?></td>
													<?php } else { ?>
														<td  align="left" valign="top"><label>End Date</label></td>
														<td align="center" valign="top">:</td>
														<td align="left"   valign="top"><?php if(isset($enddate) && $enddate != '0000-00-00 00:00:00') echo date('m/d/Y H:i',strtotime($enddate));  else echo '-'; ?></a></td>
													<?php } ?>
												</tr>	
												<tr><td height="20"></td></tr>	
												<tr>
													<td align="left" valign="top"><label>Prize</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><?php 
														 if(isset($featured)) { echo ($featured == 3 && isset($prize) && $prize != '') ? number_format($prize)." Virtual Coins" : (($featured == 4) ? "Custom"  : (($featured == 2 && isset($prize) && $prize != '') ? number_format($prize)." TiLT$" : '-')) ; } else echo '-';	
													?></td>
													<td align="left" valign="top"><label>Entry Fee</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top">
													<?php 
														if(isset($entryfee) && $entryfee != '0'){
															if(isset($featured) && $featured == 3){echo number_format($entryfee)." Virtual Coins" ; } else echo $entryfee." TiLT$";
														}
														else 
														echo 'Free'; 
													?></td>	
													</tr>
													<tr><td height="20"></td></tr>
													<tr>
														<?php if(isset($_GET['createdType']) && $_GET['createdType'] != 1 ){?>
														<td align="left" valign="top"><label>Tournament Type</label></td>
														<td align="center" valign="top">:</td>
														<td align="left" valign="top"><?php if(isset($string) && $string != '') echo $string;  else echo 'Normal Tournament'; ?></td>
														<?php }?>
													<?php if(isset($gametype) && $gametype == '2' && isset($_GET['createdType']) && $_GET['createdType'] != 1 ) {	?>
														<td align="left" valign="top"><label>Play Time</label></td>
														<td align="center" valign="top">:</td>
														<td align="left" valign="top"><?php if(isset($play_time) && $play_time != '00:00:00' && $play_time != '') echo $play_time; else echo '-'; ?></td>
													<?php } ?>
													</tr>
													<?php if(isset($locationBased)	&&	$locationBased!='')	{	?>
														<tr><td height="20"></td></tr>
														<tr>
														<td align="left" valign="top"><label>Country/State</label></td>
														<td align="center" valign="top">:</td>
														<td align="left" valign="top"><?php if(!empty($countryState)) echo $countryState; else echo ' - ';?></td>
														<td align="left" valign="top"><label>Location</label></td>
														<td align="center" valign="top">:</td>
														<td align="left" valign="top" colspan="4"><?php if(isset($locationBased) && $locationBased != '') echo $locationBased;  else echo '-'; ?></td></tr>
													<?php } ?>
													<?php if(isset($locationRestrict)	&&	$locationRestrict!='')	{	?>
														<tr><td height="20"></td></tr>
														<tr><td align="left" valign="top"><label>Location Restrict</label></td>
														<td align="center" valign="top">:</td>
														<td align="left" valign="top" colspan="4"><?php if(isset($locationRestrict) && $locationRestrict != '') echo $locationRestrict;  else echo '-'; ?></td></tr>	
													<?php } ?>
													<?php if($featured == 4 && isset($customPrizeResult) && is_array($customPrizeResult) && count($customPrizeResult)>0){ ?>
														<tr><td height="20"></td></tr>
														<tr><th colspan="6" align="left"><h2>Custom Prize</h2></th></tr>
														<tr><td height="20"></td></tr>
														<tr><td colspan="6">
															<table cellpadding="0" cellspacing="0" align="center" border="0" width="100%">
															<?php foreach($customPrizeResult as $key=>$value) {
																
															?>
																<tr>
																	<td align="left" valign="top" width="10%"><label>Prize name/title</label></td>
																	<td align="center" valign="top" width="3%">:</td>
																	<td align="left" valign="top" width="20%"><?php if(isset($value->PrizeTitle) && $value->PrizeTitle != '') echo $value->PrizeTitle;  else echo '-'; ?></td>
																	<td align="left" valign="top" width="10%"><label>Image</label></td>
																	<td align="center" valign="top" width="3%">:</td>
																	<td align="left" valign="top" width="15%">
																	<?php if(isset($value->PrizeImage) && $value->PrizeImage != ''){
																		$prizeImage = $_GET['viewId'].'/'.$value->PrizeImage;
																		if(SERVER){
																			if(image_exists(17,$prizeImage)){
																				$prize_image_path = CUSTOM_PRIZE_IMAGE_PATH.$prizeImage;
																			}
																		}
																		else if(file_exists(CUSTOM_PRIZE_IMAGE_PATH_REL.$prizeImage)){
																				$prize_image_path = CUSTOM_PRIZE_IMAGE_PATH.$prizeImage;
																		}
																		?> 
																		<a href=<?php if(isset($prize_image_path) && $prize_image_path != '') { echo '"'.$prize_image_path.'"'; ?> class="image_pop_up" <?php } else { ?>"Javascript:void(0);"<?php } ?> title="Click here" alt="Click here" >
																		<img  src="<?php echo CUSTOM_PRIZE_IMAGE_PATH.$prizeImage; ?>" width="75" height="75" >
																		</a>
																	<?php } else echo '-'; ?>
																	</td>
																	<td align="left" valign="top" width="10%" ><label>Description</label></td>
																	<td align="center" valign="top" width="3%">:</td>
																	<td align="left" valign="top" class="brk_wrd_cell"><?php if(isset($value->PrizeDescription) && $value->PrizeDescription != '') echo $value->PrizeDescription ;  else echo '-'; ?></td>
																</tr>
																<tr><td height="20"></td></tr>
															<?php } ?>
															</table>
														</td></tr>
													<?php } ?>
													<?php if(is_array($couponDetail) && count($couponDetail)>0){ ?>
														<tr><td height="20"></td></tr>
														<tr><th colspan="6" align="left"><h2>Coupon</h2></th></tr>
														<tr><td height="20"></td></tr>
														<tr>
															<td align="left" valign="top"><label>Title</label></td>
															<td align="center" valign="top">:</td>
															<td align="left" valign="top"><?php if(isset($couponDetail['CouponTitle']) && $couponDetail['CouponTitle'] != '') echo $couponDetail['CouponTitle'];  else echo '-'; ?></td>
															<td align="left" valign="top"><label>Limit</label></td>
															<td align="center" valign="top">:</td>
															<td align="left" valign="top"><?php if(isset($couponDetail['CouponLimit']) && $couponDetail['CouponLimit'] > '0') echo $couponDetail['CouponLimit'];  else echo '-'; ?></td>
															
														</tr>
														<tr><td height="20"></td></tr>
														<tr>
															<td align="left" valign="top"><label>Image</label></td>
															<td align="center" valign="top">:</td>
															<td align="left" valign="top">
															
															<?php if(isset($couponDetail['File']) && $couponDetail['File'] != '') {
																$couponImage = $_GET['viewId'].'/'.$couponDetail['File'];
																if(SERVER){
																	if(image_exists(11,$couponImage)){
																		$coupon_image_path = COUPON_IMAGE_PATH.$couponImage;
																	}
																}
																else if(file_exists(COUPON_IMAGE_PATH_REL.$couponImage)){
																		$coupon_image_path = COUPON_IMAGE_PATH.$couponImage;
																}
																?> 
																<a href=<?php if(isset($coupon_image_path) && $coupon_image_path != '') { echo '"'.$coupon_image_path.'"'; ?> class="image_pop_up" <?php } else { ?>"Javascript:void(0);"<?php } ?> title="Click here" alt="Click here" >
																<img  src="<?php echo COUPON_IMAGE_PATH.$couponImage; ?>" width="75" height="75" >
																</a>
															<?php } else echo '-'; ?>
															</td>
															<td align="left" valign="top"><label>Description</label></td>
															<td align="center" valign="top">:</td>
															<td align="left" valign="top" class="brk_wrd_cell"><?php if(isset($couponDetail['CouponAdLink']) && $couponDetail['CouponAdLink'] != '') echo $couponDetail['CouponAdLink'];  else echo '-'; ?></td>
														</tr>
														<tr><td height="20"></td></tr>
														<tr>
															<td align="left" valign="top"><label>Start Date</label></td>
															<td align="center" valign="top">:</td>
															<td align="left" valign="top"><?php if(isset($couponDetail['CouponStartDate']) && $couponDetail['CouponStartDate'] != '') echo $couponDetail['CouponStartDate'];  else echo '-'; ?></td>
															<td align="left" valign="top"><label>End Date</label></td>
															<td align="center" valign="top">:</td>
															<td align="left" valign="top"><?php if(isset($couponDetail['CouponEndDate']) && $couponDetail['CouponEndDate'] != '') echo $couponDetail['CouponEndDate'];  else echo '-'; ?></td>
														</tr>
													<?php } 
													if(is_array($bannerDetail) && count($bannerDetail)>0){ ?>
														<tr><td height="20"></td></tr>
														<tr><th colspan="6" align="left"><h2>Banner ad</h2></th></tr>
														<tr><td height="20"></td></tr>
														<tr>
														<?php if(isset($bannerDetail['InputType']) && $bannerDetail['InputType'] == '2') { ?>
															<td align="left" valign="top"><label>Image/Video</label></td>
															<td align="center" valign="top">:</td>
															<td align="left" valign="top">
																<?php if(isset($bannerDetail['File']) && $bannerDetail['File'] != ''){
																	$bannerImage = $_GET['viewId'].'/'.$bannerDetail['File'];
																	if(SERVER){
																		if(image_exists(12,$bannerImage)){
																			$banner_image_path = BANNER_IMAGE_PATH.$bannerImage;
																		}
																	
																	}
																	else if(file_exists(BANNER_IMAGE_PATH_REL.$bannerImage)){
																			$banner_image_path = BANNER_IMAGE_PATH.$bannerImage;
																	}
																	$photo 	= $bannerDetail['File'];
																	$ext = pathinfo($photo, PATHINFO_EXTENSION);
																	if($ext == 'mp4') { ?>
																		<a href=<?php if(isset($banner_image_path) && $banner_image_path != '') { echo '"'.$banner_image_path.'"'; ?> class="interest_pop_up" <?php } else { ?>"Javascript:void(0);"<?php } ?> title="Click here" alt="Click here" ><?php echo $photo; ?></a>
																	<?php }else  { ?>
																		<a href=<?php if(isset($banner_image_path) && $banner_image_path != '') { echo '"'.$banner_image_path.'"'; ?> class="image_pop_up" <?php } else { ?>"Javascript:void(0);"<?php } ?> title="Click here" alt="Click here" >
																			<img  src="<?php echo BANNER_IMAGE_PATH.$bannerImage; ?>" width="75" height="75" >
																		</a>
																	<?php } 
																} else echo '-'; ?>
															</td>
															<td align="left" valign="top"><label>Text/Link</label></td>
															<td align="center" valign="top">:</td>
															<td align="left" valign="top" class="brk_wrd_cell"><?php if(isset($bannerDetail['URL']) && $bannerDetail['URL'] != '') echo $bannerDetail['URL'];  else echo '-'; ?></td>
														<?php } else { ?>
															<td align="left" valign="top"><label>Text</label></td>
															<td align="center" valign="top">:</td>
															<td align="left" valign="top"><?php if(isset($bannerDetail['CouponAdLink']) && $bannerDetail['CouponAdLink'] != '') echo $bannerDetail['CouponAdLink'];  else echo '-'; ?></td>
														<?php } ?>
														</tr>
														
													<?php } 
													if(is_array($youtubeDetail) && count($youtubeDetail)>0){ ?>
														<tr><td height="20"></td></tr>
														<tr><th colspan="6" align="left"><h2>Youtube Link</h2></th></tr>
														<tr><td height="20"></td></tr>
														<tr>
															<td align="left" valign="top"><label>Image</label></td>
															<td align="center" valign="top">:</td>
															<td align="left" valign="top">
															<?php if(isset($youtubeDetail['File']) && $youtubeDetail['File'] != '') {
																$couponImage = $_GET['viewId'].'/'.$youtubeDetail['File'];
																if(SERVER){
																	if(image_exists(14,$couponImage)){
																		$youtube_image_path = YOUTUBE_LINK_IMAGE_PATH.$couponImage;
																	}
																}
																else if(file_exists(YOUTUBE_LINK_IMAGE_PATH_REL.$couponImage)){
																		$youtube_image_path = YOUTUBE_LINK_IMAGE_PATH.$couponImage;
																}
															?> 
																<a href=<?php if(isset($youtube_image_path) && $youtube_image_path != '') { echo '"'.$youtube_image_path.'"'; ?> class="image_pop_up" <?php } else { ?>"Javascript:void(0);"<?php } ?> title="Click here" alt="Click here" >
																<img  src="<?php echo YOUTUBE_LINK_IMAGE_PATH.$couponImage; ?>" width="75" height="75" >
																</a>
															<?php } else echo '-'; ?>
															</td>
															<?php if(isset($youtubeDetail['InputType']) && $youtubeDetail['InputType'] == 1){ ?>
																<td align="left" valign="top"><label>URL</label></td>
																<td align="center" valign="top">:</td>
																<td align="left" valign="top" class="brk_wrd_cell"><?php if(isset($youtubeDetail['URL']) && $youtubeDetail['URL'] != '') echo $youtubeDetail['URL'];  else echo '-'; ?></td>
															<?php } else {?>
																<td align="left" valign="top"><label>Embedded Code</label></td>
																<td align="center" valign="top">:</td>
																<td align="left" valign="top" class="brk_wrd_cell"><?php if(isset($youtubeDetail['CouponAdLink']) && $youtubeDetail['CouponAdLink'] != '') echo $youtubeDetail['CouponAdLink'];  else echo '-'; ?></td>
															<?php } ?>
														</tr>
													<?php } ?>
													<tr><td height="20"></td></tr>
											</table>
										</td>
									</tr>
									<tr><td height="20"></td></tr>														
									<?php if(isset($interestStatus) && $interestStatus > 0){?>
									<tr>										
										<td colspan="6" align="center">
											<a href="UserInterest?uid=<?php if(isset($userId) && $userId != '') echo $userId; ?>" class="interest_pop_up cboxElement submit_button"  alt="Interest" title="Interest" >Interest</a>
										</td>
									</tr>
									<tr><td height="20"></td></tr>
									<?php }?>
									<tr>										
										<td colspan="6" align="center">	
											<?php if(isset($_GET['createdType']) ){
												$createdBy = '';
												if($_GET['createdType'] == 2) $createdBy = "&createdBy=brand";
												else if($_GET['createdType'] == 3) $createdBy = "&createdBy=developer";
											if(isset($from) && $from == '' && isset($_GET['createdType']) && $_GET['createdType'] != 1 && (isset($_GET['elink'])) && $_GET['elink'] == 1) { ?>
												<a href="TournamentManage?editId=<?php if(isset($_GET['viewId']) && $_GET['viewId'] != '') echo $_GET['viewId'].$createdBy;  ?>&<?php echo $back;?>	" title="Edit" alt="Edit" class="submit_button">Edit</a>
											<?php }
											} ?>
											<?php if(isset($_GET['referList'])	&&	$_GET['referList']==1	&&	isset($_SESSION['referPage'])	&&	$_SESSION['referPage']!=''){?>
											<a href="<?php echo $_SESSION['referPage'];?>" class="submit_button referpage" name="Back" id="Back" title="Back" alt="Back" >Back </a>
											<?php 	unset($_SESSION['referPage']);
											} else if(isset($from) && $from != ''){ ?>
												<a href="TournamentList<?php echo $from; ?>" class="submit_button tournament_list" name="Back" id="Back" title="Back" alt="Back" >Back </a>	
											<?php } else { ?>
												<?php if(isset($_GET['back']) && $_GET['back'] == 'TournamentList'){?>	  
												<a href="TournamentList<?php echo $from; ?>" class="submit_button tournament_list" name="Back" id="Back" title="Back" alt="Back" >Back </a>
												<?php } else {?>	  
												<a href="<?php if(isset($href_page) && $href_page != '' ) echo $href_page; else echo 'TournamentList';?>" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
												<?php } ?>	  
											<?php } ?>
										</td>
									</tr>		
									<tr><td height="35"></td></tr>						   
								</table>
							</div>
						  	
<?php commonFooter(); ?>
<script type="text/javascript">	
	$(document).ready(function() {		
		$(".interest_pop_up").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"80%",
				title:true,
		});
		$(".user_photo_pop_up").colorbox({title:true});
	});	
	$(".image_pop_up").colorbox({
		title:true,
		maxWidth:"65%", 
		maxHeight:"50%"
		});
	$(function(){

	   var bodyHeight = $('body').height();
	   var bodyWidth  = $('body').width();
	   var maxHeight = '580';
	   var maxWidth  = '1100';
	   if(bodyHeight<maxHeight) {   	
		setHeight = bodyHeight;
	   } else {
			setHeight = maxHeight;
	   }
	   if(bodyWidth>maxWidth) {
			setWidth = bodyWidth;
	   } else {
			setWidth = maxWidth;
	   }
	   parent.$.colorbox.resize({
			innerWidth :setWidth,
			innerHeight:setHeight
		});
	});
</script>
</html>
