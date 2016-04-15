<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/LogController.php');
$statisticsObj   =   new LogController();
admin_login_check();
commonHead();
top_header();
if(isset($_GET['cs']) && ($_GET['cs']=='1') ) { // unset all session variables
	unset($_SESSION['mgc_sess_statistic_from_date']);
	unset($_SESSION['mgc_sess_statistic_to_date']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	$_SESSION['mgc_sess_statistic_from_date']  = $_POST['startdate'];
	$_SESSION['mgc_sess_statistic_to_date']    = $_POST['enddate'];
}
else{
$todayWithoutMin	= getCurrentTime('America/New_York','Y-m-d');
	$_SESSION['mgc_sess_statistic_from_date']      = $todayWithoutMin;
	$_SESSION['mgc_sess_statistic_to_date']        = $todayWithoutMin;
}
$fields		=	" count(id) as totalUsers ";
$condition	=	" Status IN (1,2) AND UniqueUserId = '' ";
$userCountResult	=	$statisticsObj->usersCount($fields,$condition);
if(isset($userCountResult)	&&	is_array($userCountResult)	&&	count($userCountResult) >0 )
	$totalUser	=	$userCountResult[0]->totalUsers;

$fields		=	" count(t.id) as totalTournaments ";
$condition = "  t.Status IN (1,2) AND CreatedBy in (1,3) ";
$tournamentCountResult	=	$statisticsObj->tournamentsCount($fields,$condition);
if(isset($tournamentCountResult)	&&	is_array($tournamentCountResult)	&&	count($tournamentCountResult) >0 )
	$totalTournament	=	$tournamentCountResult[0]->totalTournaments;

$fields		=	" count(id) as totalGames ";
$condition	=	" Status in(1,2,4)  ";
$gameCountResult	=	$statisticsObj->gamesCount($fields,$condition);
if(isset($gameCountResult)	&&	is_array($gameCountResult)	&&	count($gameCountResult) >0 )
	$totalGame	=	$gameCountResult[0]->totalGames;

$fields		=	" count(t.id) as activeTournaments ";
$condition = "  t.Status != '3' AND TournamentStatus != 3 AND CreatedBy in (1,3) ";
$activeTournamentsCountResult	=	$statisticsObj->activeTournamentsCount($fields,$condition);
if(isset($activeTournamentsCountResult)	&&	is_array($activeTournamentsCountResult)	&&	count($activeTournamentsCountResult) >0 )
	$activeTournaments	=	$activeTournamentsCountResult[0]->activeTournaments;

$condition = " Status in(1,2,4) ";
$devCountResult	=	$statisticsObj->developerCount($condition);
if(isset($devCountResult)	&&	is_array($devCountResult)	&&	count($devCountResult) >0 )
	$devCount	=	$devCountResult[0]->devCount;

?>
<body >
<div class="box-header"><h2><i class="fa fa-list"></i>Statistics Report</h2></div>
		<div class="clear">
		<table cellpadding="0" cellspacing="0" border="0" width="98%"   align="center" >
			
			<tr>
				<td valign="top" align="center" colspan="2">
					<form name="search_category" action="Statistics" method="post">
					   <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
							<tr><td height="15"></td></tr>
							<tr>													
								<td width="10%" style="padding-left:20px;" align="left"><label>Start Date</label></td>
								<td width="2%" align="center">:</td>
								<td height="40" align="left" >
									<input style="width:120px;" type="text" autocomplete="off" maxlength="10" class="input datepicker" name="startdate" id="startdate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_statistic_from_date']) && $_SESSION['mgc_sess_statistic_from_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_statistic_from_date'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
								</td>
								<td width="10%" style="padding-left:20px;" ><label>End Date</label></td>
								<td width="2%" align="center">:</td>
								<td height="40" align="left" >
									<input style="width:120px" type="text" autocomplete="off"  maxlength="10" class="input datepicker" name="enddate" id="enddate" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_statistic_to_date']) && $_SESSION['mgc_sess_statistic_to_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_statistic_to_date'])); else echo '';?>" onkeypress="return dateField(event);"> (mm/dd/yyyy)
								</td>
							</tr>
							<tr><td align="center" colspan="6" style="padding-top:20px"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
							<tr><td height="15"></td></tr>
						 </table>
					  </form>	
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align="center">
				<div class="width_table">
					 <table border="1" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions" >
						<tr>
							<th width="85%" align="left">Process</th>
							<th align="center">Count</th>
						</tr>
						<tr>
							<td>No. of Registered User(s) </td>
							<td ><?php  if(isset($totalUser) && $totalUser > 0){ echo '(<a href="RegisteredUsers?cs=1&stat=1" class="pop_up">'.$totalUser.'</a>)'; } else  echo '0' ;?></td>		
						</tr>
						<tr>
							<td>No. of Developer & Brand(s)</td>
							<td ><?php if(isset($devCount) && $devCount > 0){ echo '(<a href="GameDeveloperList?statistics=1&cs=1" class="pop_up">'.$devCount.'</a>)'; }  else echo '0';?></td>		
						</tr>
						<tr>
							<td>No. of Game(s)</td>
							<td ><?php if(isset($totalGame) && $totalGame > 0){ echo '(<a href="GameList?statistics=1&cs=1" class="pop_up">'.$totalGame.'</a>)'; }  else echo '0';?></td>		
						</tr>
						<tr>
							<td>No. of Tournament(s)</td>
							<td ><?php if(isset($totalTournament) && $totalTournament > 0){ echo '(<a href="TournamentListStatistics?statistics=1&cs=1" class="pop_up_tournamnet">'.$totalTournament.'</a>)'; }  else echo '0';?></td>		
						</tr>
						<tr>
							<td>No. of Active Tournament(s)</td>
							<td ><?php if(isset($activeTournaments) && $activeTournaments > 0){ echo '(<a href="TournamentListStatistics?active=1&cs=1" class="pop_up_tournamnet">'.$activeTournaments.'</a>)'; }  else echo '0';?></td>		
						</tr>																	
					</table>
			
				</div>
				</td>
			</tr>
			
		</table>
		</div>
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
	yearRange		:	"c-30:c+1",
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
	yearRange		:	"c-30:c+1",
	closeText		:   "Close"
 });
$(".pop_up").colorbox(
	{
		iframe:true,
		width:"70%", 
		height:"70%",
		title:true,
});
$(".pop_up_tournamnet").colorbox(
{
		iframe:true,
		width:"85%", 
		height:"70%",
		title:true,
});

</script>
</html>