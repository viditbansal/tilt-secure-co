<?php 
ob_start();
require_once('../includes/AdminCommonIncludes.php');
if (isset($_GET['action']) && ($_GET['action'] == 'LOAD_CONVERSATION')) {
   require_once('../controllers/MessageController.php');
	$messageModelObj   =   new MessageController();
    $j = 0;
    setPagingControlValues('CreatedDate', ADMIN_PER_PAGE_LIMIT);
    $from_user_id = $_GET['FromUserId'];
    $to_user_id = $_SESSION['tilt_ses_con_to_user_id'] = $_GET['ToUserId'];
    $msg_result = $users_conversation_lists = $messageModelObj->messageLists($from_user_id, $to_user_id);
    include('../views/MessageViewAll.php');
}
if (isset($_GET['action']) && ($_GET['action'] == 'GET_LIKE_SHARE_COMMENT')) {
	require_once('../controllers/HashTagController.php');
	$hashTagObj   =   new HashTagController();
	$page_limit   = 10;
	$next_page    = 0;
	$type_name = $limit_clause  = '';
	if((isset($_GET['Page']) && $_GET['Page'] == 'Prev' ) ){
		$_SESSION['tilt_sess_post_paging']   = $_SESSION['tilt_sess_post_paging'] - $page_limit;
	}
	else if((isset($_GET['Page']) && $_GET['Page'] == 'Next' ) ){
		$_SESSION['tilt_sess_post_paging']   = $_SESSION['tilt_sess_post_paging'] + $page_limit;
		$next_page = $_SESSION['tilt_sess_post_paging']+ $page_limit;
	}
	else{
		$_SESSION['tilt_sess_post_paging']    = 0;
		$next_page =0;
	}
	$limit_clause = " limit ".$_SESSION['tilt_sess_post_paging'].",".$page_limit;	
	if(!isset($_GET['PageName']))
		$_GET['PageName'] = '';
	if(isset($_GET['PostId']) && $_GET['PostId'] != '' ){
		$postId = $_GET['PostId'];	
		if(isset($_GET['Type']) && $_GET['Type'] == 2){
			$type_name = 'Shares';
			$type	   = 2;
			$field     = " cls . * , ut.id AS user_id, ut.UserName, ut.Photo ";
			$condition = " AND cls.fkPostId = ".$postId." AND ut.Status = 1 ";
			$shareList = $hashTagObj->getLikeCommentShareDetail($field,$condition,2,$limit_clause);
			$tot_rec   = $hashTagObj->getTotalRecordCount();
			if(isset($shareList) && is_array($shareList) && count($shareList)>0){
				$user_listing = $shareList;
			}
		}
		if(isset($_GET['Type']) && $_GET['Type'] == 1){
			$type_name = 'Likes';
			$type	   = 1;
			$field     = " cls . * , ut.id AS user_id, ut.UserName, ut.Photo ";
			$condition = " AND cls.fkPostId = ".$postId." AND ut.Status = 1 ";
			$likeList  = $hashTagObj->getLikeCommentShareDetail($field,$condition,1,$limit_clause);
			$tot_rec   = $hashTagObj->getTotalRecordCount();
			if(isset($likeList) && is_array($likeList) && count($likeList)>0){
				$user_listing = $likeList;
			}
		}
		if(isset($_GET['Type']) && $_GET['Type'] == 3){
			$type_name		 = 'Comments';
			$type	   		 = 3;
			$field           = " cls.*, cls.CreatedDate as comment_date, ut.UserName, ut.Photo ";
			$condition       = " and cls.fkPostId = ".$postId." and ut.Status = 1 ";			
			$hashTagComments = $hashTagObj->getLikeCommentShareDetail($field,$condition,3,$limit_clause);
			$tot_rec   		 = $hashTagObj->getTotalRecordCount();
			if(isset($hashTagComments) && is_array($hashTagComments) && count($hashTagComments)>0){
				$user_listing = $hashTagComments;
			}
		}
		if(isset($_GET['Type']) && $_GET['Type'] == 4){
			$type_name		 	= 'Reposted Users';
			$type	   			= 4;
			$field           	= " count(cls.fkUserId) as user_count,ut.UserName, ut.Photo ";
			$condition       	= " and cls.OriginalPostId = ".$postId." and ut.Status = 1 and cls.Status = 1 group by cls.fkUserId ";
			$hashTagPost		= $hashTagObj->getLikeCommentShareDetail($field,$condition,4,$limit_clause);
			$tot_rec   			= $hashTagObj->getTotalRecordCount();
			if(isset($hashTagPost) && is_array($hashTagPost) && count($hashTagPost)>0){
				$user_listing = $hashTagPost;
			}
		}
	}
	?>
	<table align="center" cellpadding="10" cellspacing="0" border="0" width="100%">
	<?php
	if(isset($user_listing) && is_array($user_listing) && count($user_listing) > 0 ){ 
	?>		
			<tr><td height="20"></td></tr>
			<tr><td colspan="2" style="padding-left:3%;"><h2><?php echo $type_name;?></h2></td></tr>
			<tr height="20"><td colspan="2"></td></tr>		
			<?php if($tot_rec > $page_limit){ ?>
			<tr>
				<td align="left" width="50%">
			<?php if(isset($_SESSION['tilt_sess_post_paging'] ) && $_SESSION['tilt_sess_post_paging']  != '' && $_SESSION['tilt_sess_post_paging']  > 0 ) { ?>
			<a href="javascript:void(0);" onclick="return getShareLikeCommentPaging(<?php echo $_GET['PostId']; ?>,<?php echo $type;?>,'Prev','<?php echo $_GET['PageName']; ?>');" style="padding-left:10px;color:#e8276a;"><u><< Previous</u></a>
			<?php } ?>
				</td>
				<td align="right" width="50%">
			<?php if(isset($_SESSION['tilt_sess_post_paging']) && $next_page < $tot_rec ) { ?>
			<a href="javascript:void(0);" onclick="return getShareLikeCommentPaging(<?php echo $_GET['PostId']; ?>,<?php echo $type;?>,'Next','<?php echo $_GET['PageName']; ?>');" style="padding-right:10px;color:#e8276a;"><u>Next >></u></a>
			<?php } ?>
				</td>
			</tr>
			<tr height="20"><td colspan="2"></td></tr>		
			<?php }	
				 foreach($user_listing as $value){	
						$name	 	 = $value->UserName;
						$hashId 	 = $value->fkHashtagId;
						if($type_name == 'Shares'){
							if($value->ShareType == '1')
								$name .= " - via Facebook";
							if($value->ShareType == '2')
								$name .= " - via Twitter";
							if($value->ShareType == '3')
								$name .= " - via Email";
						}
						if($type_name == 'Reposted Users'){
							if($value->user_count > '1')
								$name .= " ( ".$value->user_count." ) ";
						}
						$userPhoto	 = $value->Photo;
						if(isset($userPhoto) && $userPhoto != '')
						{
							$userImage = $userPhoto;
							if(image_exists(1,$userImage)){
								$profile_image = USER_THUMB_IMAGE_PATH.$userImage;
							}
							else
								$profile_image = ADMIN_IMAGE_PATH.'no_user.jpeg';
						}
						else
							$profile_image = ADMIN_IMAGE_PATH.'no_user.jpeg';
			if($_GET['PageName'] == 'hashId')
				$viewId = $hashId;
			else if($_GET['PageName'] == 'post')
				$viewId = $postId;
			else
				$viewId = $postId;
				
			if($type_name == 'Shares' || $type_name == 'Likes' || $type_name == 'Reposted Users'){
			?>
			
			<tr>				
				<td  width="8%"  valign="top" style="padding-left:7px;"><a href="UserDetail?viewId=<?php if(isset($value->fkUserId) && $value->fkUserId != '') echo $value->fkUserId; ?>&<?php echo $_GET['PageName']; ?>=<?php if(isset($viewId)) echo $viewId; ?>" title="User Name" alt="User Name" style="color:#e8276a;"><img class="profile_img" width="30" height="30" src="<?php echo $profile_image; ?>" alt="Image" /></a></td>
				<td width="65%" align="left"><label style="color:#e8276a;"><a href="UserDetail?viewId=<?php if(isset($value->fkUserId) && $value->fkUserId != '') echo $value->fkUserId; ?>&<?php echo $_GET['PageName']; ?>=<?php if(isset($viewId)) echo $viewId; ?>" title="User Name" alt="User Name" style="color:#e8276a;"><?php echo $name;?></a></label></td>		
			</tr>			
			<tr height="10"><td></td></tr>	
			<?php } else { 
			$gmt_current_created_time = convertIntocheckinGmtSite($value->comment_date);
			$time		 =  displayDate($gmt_current_created_time,$_SESSION['mgc_ses_from_timeZone']);
			?>				
			<tr>	
			 <td colspan="2" class="popup_border">
			 	<table align="center" cellpadding="0" cellspacing="0" width="100%" border="0">
					<tr><td height="10"></td></tr>
					<tr>				
					<td width="10%" align="center" style="padding-right:10px;padding-left:7px;" valign="top">
						<a href="UserDetail?viewId=<?php if(isset($value->fkUserId) && $value->fkUserId != '') echo $value->fkUserId; ?>&<?php echo $_GET['PageName']; ?>=<?php if(isset($viewId)) echo $viewId; ?>" title="User Name" alt="User Name" style="color:#e8276a;"><img width="30" height="30" src="<?php echo $profile_image; ?>" alt="Image" /></a>
					</td>
					<td width="40%" class="header_block">													
						<label style="color:#e8276a;" class="user_name"><a href="UserDetail?viewId=<?php if(isset($value->fkUserId) && $value->fkUserId != '') echo $value->fkUserId; ?>&<?php echo $_GET['PageName']; ?>=<?php if(isset($viewId)) echo $viewId; ?>" title="User Name" alt="User Name" style="color:#e8276a;"><?php echo $name;?></a></label>
					</td>	
				   <td width="30%" valign="top"> <span class="date date_comment"><?php echo $time;?></span></td>			    
				</tr>
				<tr>
				
				<td colspan="3">
					<div class="popup_text"><?php echo (getCommentTextEmoji('web',$value->Comments,$value->Platform));?></div></td></tr>
				<tr><td height="10"></td></tr>
			</table>
			</td>
			</tr>
			<?php } } 
			 if($tot_rec > $page_limit){ ?>
			<tr>
				<td align="left" width="50%">
			<?php if(isset($_SESSION['tilt_sess_post_paging'] ) && $_SESSION['tilt_sess_post_paging']  != '' && $_SESSION['tilt_sess_post_paging']  > 0 ) { ?>
			<a href="javascript:void(0);" onclick="return getShareLikeCommentPaging(<?php echo $_GET['PostId']; ?>,<?php echo $type;?>,'Prev','<?php echo $_GET['PageName']; ?>');" style="padding-left:10px;color:#e8276a;"><u><< Previous</u></a>
			<?php } ?>
				</td>
				<td align="right" width="50%">
			<?php if(isset($_SESSION['tilt_sess_post_paging']) && $next_page < $tot_rec ) { ?>
			<a href="javascript:void(0);" onclick="return getShareLikeCommentPaging(<?php echo $_GET['PostId']; ?>,<?php echo $type;?>,'Next','<?php echo $_GET['PageName']; ?>');" style="padding-right:10px;color:#e8276a;"><u>Next >></u></a>
			<?php } ?>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<?php } }
			else{ ?>
			<tr>				
				<td width="25%"></td>	
				<td colspan="2" align="left" style="color:#ff0000;"> No <?php echo $type_name; ?> users found</td>	
							    
			</tr>
  <?php	} ?>
  </table>
 <?php
}

if(isset($_GET['action']) && $_GET['action'] == 'SET_ORDERING_WEBSERVICE'){
	$order_value = 0;
	require_once('../controllers/ServiceController.php');
	$serviceObj   =   new ServiceController();
	$ExistCondition = '';
	$service_exists = '0';
	if(isset($_GET['orderValue']) && $_GET['orderValue'] != '')
		$order_value = $_GET['orderValue'];
	if(isset($_GET['serviceId']) && $_GET['serviceId'] != '')
		$service_id = $_GET['serviceId'];
	if($order_value != '' && $service_id != '' )
		$ExistCondition = " and Ordering = ".$order_value." and id != ".$service_id." and Ordering!='0' ";		
	$field = " Ordering ";	
	$alreadyExist   = $serviceObj->selectServiceDetails($field,$ExistCondition);	
	if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0){
			$service_exists = 1;
	}	
	if($service_exists != '1'){
		if($order_value != '' && $service_id != '' ){
			$update_string 	    = " Ordering = ".$order_value;
			$condition 		    = " id = ".$service_id;
			$OrderingResult     = $serviceObj->updateServiceDetails($update_string,$condition);
		}
	}
	echo $service_exists;
}
// Remove Country 
if(isset($_POST['action']) && $_POST['action'] == 'REMOVE_COUNTRY'){
	if(isset($_POST['countryId']) && !empty($_POST['countryId'])){
		require_once('../controllers/CoinsController.php');
		$coinsManageObj   =   new CoinsController();
		$updateString	=	' Status	=	2';
		$condition		=	' fkCountriesId = '.$_POST['countryId'].'';
		if(isset($_POST['restriction']) && $_POST['restriction'] == 'tilt_location_restrict')
				$coinsManageObj->updateTiltCountryStatus($updateString,$condition);
		else 
			   $coinsManageObj->updateCountryStatus($updateString,$condition);
			 
		echo '1';
	}
	else echo '2';
}
// Remove US states
if(isset($_POST['action']) && $_POST['action'] == 'REMOVE_STATE'){
	if(isset($_POST['countryId']) && !empty($_POST['countryId'])	&&	isset($_POST['stateId']) && !empty($_POST['stateId']) ){
		require_once('../controllers/CoinsController.php');
		$coinsManageObj   =   new CoinsController();
		$updateString	=	' Status	=	2';
		$condition		=	' fkCountriesId = '.$_POST['countryId'].' AND fkStatesId = '.$_POST['stateId'].' ';
		if(isset($_POST['restriction']) && $_POST['restriction'] == 'tilt_location_restrict')
				$coinsManageObj->updateTiltCountryStatus($updateString,$condition);
		else 
			   $coinsManageObj->updateCountryStatus($updateString,$condition);
		echo '1';
	}
	else echo '2';
}
if(isset($_POST['action']) && $_POST['action'] == 'LOAD_TOURNAMENT_RULES_ALL'){
	require_once('../controllers/TournamentController.php');
	$tournamentObj   =   new TournamentController();
	require_once('../controllers/AdminController.php');
	$adminLoginObj   =   new AdminController();
	
	$tournamentId	=	$_POST['selid'];
	$fields  = "GftRules ";
	$condition = "id=".$tournamentId;	
	$tournamentDetail    = $tournamentObj->selectTournament($fields,$condition);
	foreach($tournamentDetail	as $rulesKey=>$rulesDetails){
		$tournmentrule = $rulesDetails->GftRules;								
	}
	
	$fields			=	' * ';
	$where			= 	' id = 1 ';
	$tempDetail		=	$adminLoginObj->getDistance($fields,$where);
	if(isset($tempDetail) && is_array($tempDetail) && count($tempDetail)>0){
		$tourRules			=	$tempDetail[0]->TournamentRules;
	}
?>
	<table cellpadding="0" cellspacing="7" id="inputParam1" border="0" width="100%" align="center">
		<tr>
			<td  width="20%" valign="top" align="left">All</td>					
			<td valign="top" class="terms_td col_2" id="terms_td_0" align="left">
				<textarea id="terms" class="tour_rule textarea-full"  rows="4" cols="45" tabindex="11" name="tournamentRule"><?php if(isset($tournmentrule) && $tournmentrule!='')  echo $tournmentrule; else echo $tourRules; ?></textarea>
			</td>									
		</tr>								
	</table>
<?php 
}
if(isset($_POST['action']) && $_POST['action'] == 'LOAD_TOURNAMENT_RULES'){
	require_once('../controllers/TournamentController.php');
	$tournamentObj   =   new TournamentController();
	require_once('../controllers/AdminController.php');
	$adminLoginObj   =   new AdminController();
	$fields			=	' id,Country';
	$conditions		=	' Status = 1 ';
	$countryList	=	$tournamentObj->getCountryList($fields,$conditions);
	if(!empty($countryList))	{
		foreach($countryList as $key=>$value)	{
			$countryArray[$value->id]	=	$value->Country;
		}
		asort ($countryArray);
	}

	$fields			=	' id,State';
	$conditions		=	' Status = 1 AND fkCountriesId = '. USID ;
	$stateList	=	$tournamentObj->getStateList($fields,$conditions);
	if(!empty($stateList))	{
		foreach($stateList as $key=>$value)	{
			$usStateArray[$value->id]	=	$value->State;
		}
	}
	
	$fields			=	' * ';
	$where			= 	' id = 1 ';
	$tempDetail		=	$adminLoginObj->getDistance($fields,$where);
	if(isset($tempDetail) && is_array($tempDetail) && count($tempDetail)>0){
		$tourRules			=	$tempDetail[0]->TournamentRules;
	}
	$usId			=	USID;
	$tournamentId = $_POST['selid'];
	$fields    = " tr.id,t.id as tournamentId,tr.fkTournamentsId,tr.fkCountriesId,tr.fkStatesId,tr.TournamentRules,tr.GftRules,tr.DateCreated,t.TournamentName,t.DateCreated";
	$condition = " tr.fkTournamentsId = ".$tournamentId." AND tr.Status = 1  AND t.Status !=3 ";
	$tournamentRulesDetail	=	$tournamentObj->selectTournamentRule($fields,$condition);	
	$index=0;
	?>
	
	<table cellpadding="0" cellspacing="7" id="inputParam" border="0" width="100%" align="center">
				<?php if(isset($tournamentRulesDetail)	&& is_array($tournamentRulesDetail)	&&	count($tournamentRulesDetail)>0) { 
						$index	=	count($tournamentRulesDetail);
						$oldCountryIds	=	$oldStateIds	=	'';
						foreach($tournamentRulesDetail	as $rulesKey=>$rulesDetails){
							$usFlag	=	1;
							if($rulesDetails->fkCountriesId == $usId){ $usFlag = 0;	$oldStateIds	=	$rulesDetails->fkStatesId; }
							else 
								$oldCountryIds	.=	$rulesDetails->fkCountriesId.',';
				?>
							<tr align="center" class="clone" clone="<?php echo $rulesKey;?>">
								<td align="left" valign="top" width="20%">
									<div class="fleft">
										<a href="javascript:void(0)" onclick="delCountry(this)"><i class="fa fa-lg  fa-minus-circle text-red"></i></a>
										<span id="new_0" class="addNewRule" style="display:none"><a href="javascript:void(0)" onclick="addCountry(this)"><i class="fa fa-lg text-green fa-plus-circle"></i></a></span>&nbsp;&nbsp;
									</div>
									<div class="fleft">
										<select name="country[]" tabindex="10" style="width:130px;" class="country" id="country_<?php echo $rulesKey; ?>" onchange="countryShow(<?php echo $rulesKey;?>);">
											<option value="">Select</option>
											<?php  if(isset($countryArray)	&& is_array($countryArray)	&&	count($countryArray) >0 )
												foreach($countryArray as $countryId => $country) {  ?>
												<option value="<?php echo $countryId; ?>" <?php   if($countryId==$rulesDetails->fkCountriesId) { echo 'Selected';  } ?>><?php echo $country; ?></option>
											<?php 	} ?>
										</select>
										<br>
										<span id='field_name_empty' class="error_empty"></span>
										<span class="slabel" id="state_label_<?php echo $rulesKey; ?>" <?php if($usFlag) { ?>style="display:none;" <?php } ?>>State</span>
										<br>
										<select name="state[]" tabindex="10" class="state" <?php if($usFlag) { ?>style="display:none;width:130px;" <?php } ?>style="width:130px;" id="state_<?php echo $rulesKey; ?>">
											<option value="">Select</option>
											<?php  if(isset($usStateArray)	&& is_array($usStateArray)	&&	count($usStateArray) >0 )
												foreach($usStateArray as $stateId => $state) {  ?>
												<option value="<?php echo $stateId; ?>" <?php   if($stateId==$rulesDetails->fkStatesId) echo 'Selected';  ?>><?php echo $state; ?></option>
											<?php 	} ?>
										</select>
										<span id='sample_data_empty' class="error_empty"></span>
									</div>
								</td>
								<td valign="top" class="terms_td col_2" id="terms_td_<?php echo $rulesKey; ?>" align="left">
									<textarea id="terms_<?php echo $rulesKey; ?>" class="tour_rules textarea-full"  rows="4" cols="45" tabindex="11" name="tournamentRules[]"><?php echo $rulesDetails->GftRules; ?></textarea>
								</td>								
							</tr>
				<?php	} // end foreach
					  } //end if tournament details validation
					  ?>
							<tr align="center" class="clone" clone="<?php echo $index;?>">
								<td align="left" valign="top" width="20%">
									<div class="fleft">
										<a href="javascript:void(0)" onclick="delCountry(this)"><i class="fa fa-lg  text-red  fa-minus-circle"></i></a>
										<span id="new_0" class="addNewRule" ><a href="javascript:void(0)" onclick="addCountry(this)"><i class="fa fa-lg text-green fa-plus-circle"></i></a></span>&nbsp;&nbsp;
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
									<textarea id="terms_<?php echo $index; ?>" class="tour_rules textarea-full"  rows="4" cols="45" tabindex="11" name="tournamentRules[]"><?php echo $tourRules; ?></textarea>
								</td>								
							</tr>
							<input id="countryDeletedIds" name="countryDeletedIds" type="hidden" value="">
							<input id="stateDeletedIds" name="stateDeletedIds" type="hidden" value="">
						</table>
<script type="text/javascript">						
						testInit();	 
function testInit(){
 tinymce.init({
	height 	: "300",
	width	: "350",
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
</script>
<?php 	
}

if(isset($_GET['action'])	&&	$_GET['action']	&&	$_GET['action'] == 'SEARCH_LOCATION' )	{
	require_once('../includes/FoursquareAPI.class.php');
	$client_key = "aad82a6f9e878e0187c5616cd6e0eb3515ff3938";
	$client_secret = "e3256f12849d4e633e91772a24fac43d736f2dd8";
	$foursquare = new FoursquareAPI($client_key,$client_secret);
	
	$searchkey = $latitude	= $longitude	= '';
	$response_json = 0;
	if(isset($_GET['curlat'])	&&	$_GET['curlat'] != '')
		$latitude	=	$_GET['curlat'];
	if(isset($_GET['curlong'])	&&	$_GET['curlong'] != '')
		$longitude	=	$_GET['curlong'];
	if((trim($latitude) == '' || $longitude == '') && (isset($_GET['location']) && $_GET['location'] != ''))
		list($latitude,$longitude) = $foursquare->GeoLocate($_GET['location']);
	
	if(isset($_GET['term'])	&&	$_GET['term'] != '')	
		$searchkey	=	$_GET['term'];
	
	$quryString = "&query=".urlencode($_GET['term']);
	$limit	= 	"&limit=5";
	$radiusString = "&radius=10000000";
	$locationResponse = getFourSquareInfo($latitude,$longitude,$quryString,$limit,$radiusString);
	if(!empty($locationResponse)) {
		foreach($locationResponse as $key=>$val) {
			$jsonResponseArray[$key]['id'] 		= $val['location']['lat'].','.$val['location']['lng'];
			$label_name	=	$val['name'];
			if(isset($val['location']))	{
				$label_value = '';
				if(isset($val['location']['address']))	{
					$label_value	.= $val['location']['address'].', ';
				}
				if(isset($val['location']['city']))	{
					$label_value	.= $val['location']['city'].', ';
				}
				if(isset($val['location']['state']))	{
					$label_value	.= $val['location']['state'].', ';
				}
				if(isset($val['location']['country']))	{
					$label_value	.= $val['location']['country'].', ';
				}
				
				$jsonResponseArray[$key]['value'] 	= $label_name.", ".rtrim($label_value,', ');
				$jsonResponseArray[$key]['label'] 	= $label_name.", ".rtrim($label_value,', ');
			}
		}
		$response_json =	json_encode($jsonResponseArray);
	}
	echo $response_json;
}
/******** BEGIN : Check Game already exist *******/
if(isset($_POST['action'])	&&	$_POST['action'] == 'CHECK_GAME' ) {
	require_once('../controllers/GameController.php');
	$gameManageObj   	=   new GameController();
	$alreadyExist		=	0;
	$condition			=	'';
	if(isset($_POST['edit_id'])	&&	$_POST['edit_id'] > 0 )	{
		$condition	.=	' id <> '.$_POST['edit_id'].'  AND ';
	}
	$fields				=	' id,Name ';
	$condition			.=	" Name ='".trim($_POST['game_name'])."' AND  Status !=3 ";
	$checkGameExist	=	$gameManageObj->selectGameDetails($fields,$condition);
	if(isset($checkGameExist)	&&	is_array($checkGameExist)	&&	count($checkGameExist) > 0){
		$alreadyExist	=	1;
	}
	echo $alreadyExist;
}
/******** END 	: Check Game already exist *******/
?>