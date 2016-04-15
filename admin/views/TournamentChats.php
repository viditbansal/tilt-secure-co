<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_chat_date']);
	unset($_SESSION['mgc_sess_chat_email']);
	unset($_SESSION['mgc_sess_chat_username']);
	if(isset($_SESSION['brand_ses_from_timeZone']))
		unset($_SESSION['brand_ses_from_timeZone']);
	
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = "c.id,c.*,u.FirstName,u.LastName,u.Email";
$condition = " ";
$pagingParam	=	'';
$form	=	'';
if(isset($_GET['viewId']) && $_GET['viewId'] != ''){
	$condition .= " and c.fkTournamentsId = ".$_GET['viewId']." " ;
	$pagingParam	=	'?viewId='.$_GET['viewId'];
	if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '')	
		$pagingParam	.=	'&tournamentName='.$_GET['tournamentName'];
	if( (isset($_GET['brand_id']) && $_GET['brand_id'] != '' )  ){
		$pagingParam .= '&brand_id='.$_GET['brand_id'];
	}
	if(isset($_POST['Search'])	&&	$_POST['Search'] != '')	{
			$_POST          = unEscapeSpecialCharacters($_POST);
			$_POST          = escapeSpecialCharacters($_POST);
		if(isset($_POST['username'])	&&	$_POST['username'])
			$_SESSION['mgc_sess_chat_username']	=	trim($_POST['username']);
		if(isset($_POST['email'])	&&	$_POST['email'])
			$_SESSION['mgc_sess_chat_email']	=	trim($_POST['email']);
		if(isset($_POST['chat_date'])	&&	$_POST['chat_date'] != '')
			$_SESSION['mgc_sess_chat_date']	=	$_POST['chat_date'];
	}
	$fields		=	'c.id,c.Message,c.Platform,c.DateCreated,u.FirstName,u.LastName,u.Email,u.UniqueUserId, u.id as userId ';
	$playersList	=	$tournamentObj->getChatList($fields,$condition); 
	$tot_rec		=	$tournamentObj->getTotalRecordCount();
	if($tot_rec!=0 && !is_array($playersList)) {
		$_SESSION['curpage'] = 1;
		$playersList  = $tournamentObj->getChatList($fields,$condition);
	}
}
$from = '';
?>
<body class="popup_bg" >

	<div class="box-header"><h2><i class="fa fa-list"></i>Chat List<?php if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '') echo ' - '.$_GET['tournamentName'];?></h2></div>
 <div class="clear">
	<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" class="headertable">
		
		<tr>
			<td valign="top" align="center" colspan="2">
				<form name="player_search" action="<?php echo 'TournamentChats'.$pagingParam;?>&cs=1" method="post">
				   <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
						<tr><td height="15"></td></tr>
						<tr>													
							<td width="10%" style="padding-left:20px;"><label>User Name</label></td>
							<td width="3%" align="center">:</td>
							<td align="left"  height="40">
								<input type="text" class="input" name="username" id="username"  value="<?php  if(isset($_SESSION['mgc_sess_chat_username']) && $_SESSION['mgc_sess_chat_username'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_chat_username']);  ?>" >
							</td>
							
							<td width="10%" style="padding-left:20px;"><label>Email</label></td>
							<td width="3%" align="center">:</td>
							<td align="left"  height="40">
								<input type="text" class="input" name="email" id="email"  value="<?php  if(isset($_SESSION['mgc_sess_chat_email']) && $_SESSION['mgc_sess_chat_email'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_chat_email']);  ?>" >
							</td>
							
							<td width="10%" style="padding-left:20px;" align="left"><label>Date</label></td>
							<td width="3%" align="center">:</td>
							<td height="40" align="left" >
								<input style="width:90px;" type="text" autocomplete="off" maxlength="10" class="input datepicker" name="chat_date" id="chat_date" value="<?php if(isset($_SESSION['mgc_sess_chat_date']) && $_SESSION['mgc_sess_chat_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_chat_date'])); else echo '';?>" > (mm/dd/yyyy)
							</td>
						</tr>
						<tr>
							<td align="center" colspan="9" style="padding-top:20px"><input type="submit" class="submit_button" title="Search" name="Search" id="Search" value="Search"></td>
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
						<?php if(isset($playersList) && is_array($playersList) && count($playersList) > 0){ ?>
						<td align="left" width="20%">No. of Chat(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
						<td align="center">
								<?php if(isset($playersList) && is_array($playersList) && count($playersList) > 0 ) {
									pagingControlLatest($tot_rec,'TournamentChats'.$pagingParam); ?>
								<?php }?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="10"></td></tr>
		<tr><td colspan= '2' align="center">
			</td></tr>
		<tr><td height="10"></td></tr>
		<tr>
			<td colspan="2">
			<form action="TournamentChats.<?php echo $pagingParam;?>" class="l_form" name="TournamentPlayedUsersList" id="TournamentPlayedUsersList"  method="post"> 
				<div class="tbl_scroll">
			  
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr>
						<th align="center" class="text-center" width="4%">	<label>#</label></th>
						<th align="left" width="15%">	<label>User Name</label></th>
						<th align="left" width="15%">	<label>Email</label></th>
						<th  align="left" width="15%">	<label>Message</label></th>
						<th  align="left" width="8%">	<label>Date</label>	</th>
					</tr>
					<?php if(isset($playersList) && is_array($playersList) && count($playersList) > 0 ) {
				foreach($playersList as $key=>$value)	{
					$userName	=	'';
					if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
						$userName = 'Guest'.$value->userId;
					else if(isset($value->FirstName)	&&	isset($value->LastName)) 	
						$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
					else if(isset($value->FirstName))	
						$userName	=	 ucfirst($value->FirstName);
					else if(isset($value->LastName))	
						$userName	=	ucfirst($value->LastName);
				?>
			<tr>
				<td align="center" width="4%" >
					<?php if(isset($_SESSION['curpage'])	&&	$_SESSION['curpage'] !=''	&&	isset($_SESSION['perpage'])	&&	$_SESSION['perpage'] !='')echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?>
				</td>
				<td align="left" width="15%"><?php if(!empty($userName)) echo $userName; else { echo " - "; } ?></td>
				<td align="left" width="15%"><?php if(isset($value->Email)	&&	$value->Email!=''){	echo $value->Email; } else { echo " - "; } ?></td>
				<td  align="left" width="25%"><div class="brk_wrd brk_wrd_cell response_msg" >	
				<?php  	if(isset($value->Message) && $value->Message != '')	{ 
							echo html_entities(getCommentTextEmoji('web',$value->Message,$value->Platform,0));
						} else echo '-';?></div></td>
				<td  align="left" width="8%">	
				<?php 	if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00')	{ 
								echo date('m/d/Y H:i',strtotime($value->DateCreated)); 	
						} else echo '-';?></td>
			</tr>
		<?php } 
		}
		else { ?>
			<tr><td align="center" colspan="13" style="color:red;">No Chat(s) Found</td></tr>
		<?php } ?>
		</table>
				</div>
				</form>
			</td>
		</tr>
	<?php if(isset($_GET['brand_id']) && $_GET['brand_id'] != '' ) {?>		
		<tr ><td height="20"></td></tr>
		<tr ><td colspan="2" align="center">
			<a href="<?php  echo 'TournamentList?from=1&cs=1&hide_players=1&brand_id='.$_GET['brand_id'];?>"  class="tournament_list_pop_up submit_button">Back</a>
		</td></tr>
	<?php } ?>
		<tr ><td height="20"></td></tr>
	</table>
	 </div>
<?php commonFooter(); ?>
<script type="text/javascript">
 $("#chat_date").datepicker({
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
$(function(){
   var bodyHeight = $('body').height();
   var bodyWidth  = $('body').width();
   var maxHeight = '600';
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
