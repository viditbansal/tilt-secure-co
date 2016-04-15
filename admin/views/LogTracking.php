<?php 
ini_set("memory_limit","512M");
ini_set ( 'max_execution_time', 3000);
set_time_limit(1000000);
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$logObj   =   new LogController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
global $link_type_array;
$today		= getCurrentTime('America/New_York','Y-m-d');
$where		=	' ';
$userName	=	'';
$deviceType	=	'';
$searchUserIds	=	'';
$logUserTokens	=	'';
$logUserArray	=	array();
$logUserTokensArray	=	array();
$searchUserIdsArray = array();
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_logtrack_to_date']);
	unset($_SESSION['mgc_sess_logtrack_from_date']);
	unset($_SESSION['mgc_sess_logtrack_searchUserName']);
	unset($_SESSION['mgc_sess_logtrack_searchIP']);
	unset($_SESSION['mgc_sess_logtrack_SearchResponse']);
	unset($_SESSION['mgc_sess_logtrack_method']);
	unset($_SESSION['mgc_sess_logtrack_searchUrl']);
	
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);

	$_SESSION['mgc_sess_logtrack_to_date']      	= $_POST['to_date'];
	$_SESSION['mgc_sess_logtrack_from_date']     	= $_POST['from_date']; 
	$_SESSION['mgc_sess_logtrack_searchUserName']	= trim($_POST['searchUserName']);
	$_SESSION['mgc_sess_logtrack_searchIP']      	= trim($_POST['searchIP']);
	$_SESSION['mgc_sess_logtrack_SearchResponse']   = trim($_POST['searchResponse']);
	$_SESSION['mgc_sess_logtrack_method']      		= $_POST['urlMethod'];
	$_SESSION['mgc_sess_logtrack_searchUrl']      	= trim($_POST['searchUrl']);
}

if(!isset($_SESSION['mgc_sess_logtrack_to_date'])) 
	$_SESSION['mgc_sess_logtrack_to_date']	=	$today;
if(!isset($_SESSION['mgc_sess_logtrack_from_date'])) 
	$_SESSION['mgc_sess_logtrack_from_date']	=	$today;

if(isset($_SESSION['mgc_sess_logtrack_searchUserName'])	&&	$_SESSION['mgc_sess_logtrack_searchUserName']	!=''){
	$fields		= 	' u.id,atk.access_token ';
	$condition = " (u.FirstName LIKE '%".$_SESSION['mgc_sess_logtrack_searchUserName']."%' OR	u.LastName LIKE '%".$_SESSION['mgc_sess_logtrack_searchUserName']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_logtrack_searchUserName']."%')";
	$usersList	=	$logObj->selectUserDetails($fields,$condition);
	if(isset($usersList) && is_array($usersList) && count($usersList) > 0){ 
		foreach($usersList as $userKey=>$userValue){
			if(isset($userValue->access_token)	&&	$userValue->access_token !='')
				$searchUserIdsArray[]	=	$userValue->access_token;
		}
		$searchUserIdsArray	=	array_unique($searchUserIdsArray);
		foreach($searchUserIdsArray as $searchKey=>$searchValue)
			$searchUserIds .= '"'.$searchValue.'",';
		if(isset($searchUserIds)	&&	$searchUserIds !='')
			$searchUserIds .= rtrim($searchUserIds,',');
		else
			$searchUserIds	=	'noresult';
	}
	else $searchUserIds	=	'noresult';
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
if($searchUserIds	!=	'noresult') {
$searchUserIds	=	rtrim($searchUserIds,',');
	if(	$searchUserIds !=	'')
		$where      .=	" AND ( l.user IN(".$searchUserIds.") AND l.user !='' )";
		
	$logtracksResult	=	$logObj->logtrackDetails($where);
	
	$logtrackCount	=	$logObj->logtrackCount($where);
	$tot_rec		=	$logtrackCount[0]->count;
	
	if($tot_rec==0 && !is_array($logtracksResult)) {
		$_SESSION['curpage'] = 1;
		$logtracksResult	=	$logObj->logtrackDetails($where);
	}
}
$fields		=	" atk.access_token,u.FirstName,u.LastName,ac.device_type ";
if(isset($logtracksResult) && is_array($logtracksResult) && count($logtracksResult) > 0){ 
	foreach($logtracksResult as $key=>$value){
		if(isset($value->user)	&&	$value->user !='')
			$logUserTokensArray[]	=	$value->user;
	}
	$logUserTokensArray	=	array_unique($logUserTokensArray);
	foreach($logUserTokensArray as $key1=>$value1){
		$logUserTokens .= '"'.$value1.'",';
	}
	$logUserTokens .= rtrim($logUserTokens,',');
	if(isset($logUserTokens)	&&	$logUserTokens !='')
		$logUsersResult	=	$logObj->logUsersDetails($fields,$logUserTokens);
	if(isset($logUsersResult) && is_array($logUsersResult) && count($logUsersResult) > 0){ 
		foreach($logUsersResult as $userKey=>$userValue){
			if(isset($userValue->access_token)	&& $userValue->access_token != ''){
				if((isset($userValue->FirstName)	&& $userValue->FirstName != '') 	&& (isset($userValue->LastName)	&& $userValue->LastName != '') )
					$userName	=	ucfirst($userValue->FirstName).' '.ucfirst($userValue->LastName);
				else if((isset($userValue->FirstName)	&& $userValue->FirstName != '') )	
					$userName	=	 ucfirst($userValue->FirstName);
				else if((isset($userValue->LastName)	&& $userValue->LastName != '') )	
					$userName	=	ucfirst($userValue->LastName);
				if(isset($userValue->device_type)	&& $userValue->device_type != ''){
					$deviceType	=	$userValue->device_type;
				}
				$logUserArray[$userValue->access_token]	=	array('user'=>$userName,'device_type'=>$deviceType);
			}
		}
	}
}
?>
<body>
	<?php top_header(); ?>
		<div class="box-header"><h2><i class="fa fa-list"></i>Log Tracking</h2></div>
		<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
			
			<tr>
				<td colspan="2">
					<form name="search_category" action="LogTracking" method="post">
					   <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
							<tr><td height="15"></td></tr>
							<tr>													
								<td width="10%" style="padding-left:20px;"><label>User</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<input  type="text" class="input " title="User name" name="searchUserName" value="<?php if(isset($_SESSION['mgc_sess_logtrack_searchUserName']) && $_SESSION['mgc_sess_logtrack_searchUserName'] != '') echo stripslashes(htmlentities($_SESSION['mgc_sess_logtrack_searchUserName']));?>">
								</td>
								<td width="10%" style="padding-left:20px;"><label>IP Address</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<input type="text" class="input "  title="IP Address" name="searchIP" value="<?php if(isset($_SESSION['mgc_sess_logtrack_searchIP']) && $_SESSION['mgc_sess_logtrack_searchIP'] != '') echo stripslashes(htmlentities($_SESSION['mgc_sess_logtrack_searchIP']));?>">
								</td>
								<td width="10%" style="padding-left:20px;"><label>Search Response</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<input type="text" class="input "  title="Response" name="searchResponse" value="<?php if(isset($_SESSION['mgc_sess_logtrack_SearchResponse']) && $_SESSION['mgc_sess_logtrack_SearchResponse'] != '') echo stripslashes(htmlentities($_SESSION['mgc_sess_logtrack_SearchResponse']));?>">
								</td>
							</tr>
							<tr>
								<td width="10%" style="padding-left:20px;"><label>Start Date</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<input  type="text" class="input medium datepicker w35" style="width:100px" autocomplete="off"  title="Select Date" id="startdate" name="from_date" value="<?php if(isset($_SESSION['mgc_sess_logtrack_from_date']) && $_SESSION['mgc_sess_logtrack_from_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_logtrack_from_date']));?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
								</td>
								<td width="10%" style="padding-left:20px;"><label>End Date</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<input type="text" class="input medium datepicker w35" style="width:100px" autocomplete="off"   title="Select Date" id="enddate" name="to_date" value="<?php if(isset($_SESSION['mgc_sess_logtrack_to_date']) && $_SESSION['mgc_sess_logtrack_to_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_logtrack_to_date']));?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
								</td>
								<td width="10%" style="padding-left:20px;"><label>Method</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<select id="method" title="Method" name="urlMethod">
										<option value="">Select</option>
										<?php foreach($methodArray as $key=>$value){?>
										<option value="<?php echo $value;?>" <?php if(isset($_SESSION['mgc_sess_logtrack_method']) && ($_SESSION['mgc_sess_logtrack_method']== $value	)) echo 'selected';?>><?php echo $value;?></option>
										<?php }?>
										
									</select>
								</td>
							</tr>
							<tr>													
								<td width="10%" style="padding-left:20px;"><label>Url</label></td>
								<td width="2%" align="center">:</td>
								<td align="left"  height="40">
									<input  type="text" class="input " title="Url" name="searchUrl" value="<?php if(isset($_SESSION['mgc_sess_logtrack_searchUrl']) && $_SESSION['mgc_sess_logtrack_searchUrl'] != '') echo stripslashes(htmlentities($_SESSION['mgc_sess_logtrack_searchUrl']));?>">
								</td>
							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td align="center" colspan="9" ><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td>
							</tr>
							<tr><td height="15"></td></tr>
						 </table>
					</form>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td colspan="2">
					<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
						<tr>
							<?php if(isset($logtracksResult) && is_array($logtracksResult) && count($logtracksResult) > 0){ ?>
							<td align="left" width="20%">No. of Log(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
							<?php } ?>
							<td align="center">
									<?php if(isset($logtracksResult)	&&	is_array($logtracksResult) && count($logtracksResult) > 0 ) {
										pagingControlLatest($tot_rec,'LogTracking'); ?>
									<?php }?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td colspan="2">
					<div class="tbl_scroll">
					<form action="LogTracking" class="l_form" name="LogTrackingForm" id="LogTrackingForm"  method="post"> 
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
						<tr>
							<th width="1%" class="algn_cntr">S.no</th>
							<th width="15%">User</th>
							<th width="23%">URL</th>
							<th width="30%">Data</th>
							<th width="5%">Device</th>
							<th width="20%">Time</th>
							<th width="6%">Duration&nbsp;&nbsp;</th>
						</tr>
					<?php 	if(isset($logtracksResult) && is_array($logtracksResult) && count($logtracksResult) > 0 ) { 
								foreach($logtracksResult as $key=>$value){
									$userName	=	'';$deviceType = 0;
									if(isset($value->user)	&&	$value->user !='' && array_key_exists($value->user,$logUserArray)){
										$deviceType	=	$logUserArray[$value->user]['device_type'];
										$userName	=	$logUserArray[$value->user]['user'];
									}
					?>									
						<tr>
							<td class="algn_cntr"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
							<td align="left">
								<?php  if(isset($userName)	&&	$userName !='') echo $userName;
										else echo '-';?>								<br><br>
								<p><b class="head_color">IP :</b>
								<?php if(isset($value->ip_address)	&&	$value->ip_address !='') echo $value->ip_address; else echo '-';?>
								</p>
							</td>
							<td align="left">
								<?php if(isset($value->log_stat)	&&	 ($value->log_stat ==1	||	$value->log_stat ==2)){ 
											echo '-';
										} else {?>
								<p class="brk_wrd brk_wrd_cell"><?php 	if(isset($value->url)	&&	$value->url !='') {
											if (SERVER)		echo "https://".$value->url;
											else 			echo "http://".$value->url;
										}
										else echo '-';?>
								</p><br>
								<p><b class="head_color">Method : </b><?php if(isset($value->method)	&&	$value->method !='') echo $value->method; else echo '-';?></p>
								<?php }?>
							</td>
							<td align="left" class="brk_wrd_cell">
								<?php if(isset($value->log_stat)	&&	 ($value->log_stat ==1	||	$value->log_stat ==2)){ 
									echo '-';
								} else {?>
								<p class="brk_wrd brk_wrd_cell"><b class="head_color">Request : </b><?php if(isset($value->content)	&&	$value->content !='') echo ''.$value->content.'<br><br>'; else echo '-<br><br>';?></p>
								<div class="brk_wrd brk_wrd_cell response_msg" ><b class="head_color">Response : </b>
									<?php if(isset($value->response)	&&	$value->response !='') { ?>
										<div class="more_content">
											<?php echo substr($value->response,0,1000); if(strlen($value->response)>1000) echo '...'; ?>
										</div> 
										<?php if(strlen($value->response)>1000) { ?>
											<a href="javascript:void(0);" class="more" style="float: right" title="More..">More..</a>
											<div class="hide_content" style="display:none">
												<?php echo $value->response; ?>
												<a href="javascript:void(0);" class="hide" style="float: right;" title="Hide">Hide</a>
											</div> 
											
									   <?php } 
									   } else echo '-';?>
								 </div>
								<?php } ?>
							</td>
							<td align="left">
								<?php 
									if(isset($deviceType)	&&	$deviceType !='') {
											if(array_key_exists($deviceType, $device_type_array))
												echo $device_type_array[$deviceType];
											else echo '-';
									  }else echo '-';
								?>
							</td>
							<td align="center">
									<div class="div_no_wrap log_time">
									<?php if(isset($value->log_stat)	&&	 $value->log_stat ==1	){
												if(isset($value->start_time) && $value->start_time != '0000-00-00 00:00:00'){
													$gmt_current_start_time = convertIntocheckinGmtSite($value->start_time);
													$start_time	=  displayConversationDateTime($gmt_current_start_time,$_SESSION['mgc_ses_from_timeZone']);
													echo '<br>'.$start_time; 
												}else echo '<br>-';
										} 
										else if(isset($value->log_stat)	&&	 $value->log_stat ==2	){
												if(isset($value->end_time) && $value->end_time != '0000-00-00 00:00:00'){
													$gmt_current_end_time = convertIntocheckinGmtSite($value->end_time);
													$end_time	=  displayConversationDateTime($gmt_current_end_time,$_SESSION['mgc_ses_from_timeZone']);
													echo '<br>'.$end_time; 
												}else echo '<br>-';
										} 
										else { ?>
									<?php 	if(isset($value->start_time) && $value->start_time != '0000-00-00 00:00:00'){
												echo date('m/d/Y H:i:s',strtotime($value->start_time));
											}else echo '-';?>
										<p align="center" style="margin:0 0 0 1px;">to</p>
									<?php 	if(isset($value->end_time) && $value->end_time != '0000-00-00 00:00:00'){
												echo date('m/d/Y H:i:s',strtotime($value->end_time));
											}else echo '-';?>
									<?php } ?>
									</div>
							</td>
							<td align="left" ><?php if(isset($value->execution_time)	&&	$value->execution_time > 0) echo round($value->execution_time, 3).' sec'; else echo '-';?></td>
						</tr>
						<?php } // end for each record
						} else { // end record set empty check ?>	
							<tr>
								<td colspan="16" align="center" style="color:red;">No Result(s) Found</td>
							</tr>
				<?php   } ?>
					</table>
					</form>
					</div>
				
				</td>
			</tr>
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
 
 $(".more").click(function() {
	$(this).hide();
	$(this).prev(".more_content").hide();
	$(this).next(".hide_content").show();
 });
 
 $(".hide").click(function() {
	$(this).parent().prev(".more").show();
	$(this).parent().prev(".more").prev(".more_content").show();
	$(this).parent().hide();
 });
   </script>
</html>
