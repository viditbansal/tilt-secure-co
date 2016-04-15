<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/TournamentController.php');
$tournamentObj   =   new TournamentController();

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_usertournament_tournament']);
	unset($_SESSION['mgc_sess_usertournament_player']);
	unset($_SESSION['mgc_sess_usertournament_turns']);
}
 setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT); 
 
$fields = "tp.fkTournamentsId,tp.fkUsersId,t.TournamentName,u.firstname,t.DateCreated" ;
$PlayersListResult = $tournamentObj->getTournamentUserList($fields,'');

if(isset($PlayersListResult) && is_array($PlayersListResult) && count($PlayersListResult) > 0){
		foreach($PlayersListResult as $key => $value){
			$PlayersListArray[$value->fkTournamentsId][]	=	$value;
		}
	}

$tot_rec 		  = $tournamentObj->getTotalRecordCount();


?>
<body>
<?php top_header(); ?>
	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i>Tournament List</h2>
	</div>
    <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
		<tr><td height="10"></td></tr>
		<tr><td class="filter_form" >
			<form name="search_category" action="TournamentUserList" method="post">
			<table align="center" cellpadding="6" cellspacing="0" border="0"width="98%">									       
				<tr><td></td></tr>
				<tr>													
					<td width="5%" ><label>Tournament</label></td>
					<td width="1%" align="center">:</td>
					<td align="left"  width="15%">
						<input type="text" class="input" name="tournament" id="tournament"  value="" >
					</td>
					<td  align="left" width="5%"><label>Players</label></td>
					<td width="1%" align="center">:</td>
					<td  width="15%">
						<input type="text" class="input" name="players" id="players"  value="" >
					</td>
				</tr>
					<td width="5%" ><label>Turns</label></td>
					<td width="1%" align="center">:</td>
					<td align="left"  width="15%">
						<input type="text" class="input" name="turns" id="turns"  value="" >
					</td>
					<td align="center" colspan='3'><input type="submit" class="submit_button" name="Search" id="Search" value="Search"></td>
				</tr>
				<tr><td></td></tr>
			 </table>
			 </form>
		</td></tr>
		<tr><td height="20"></td></tr>
		<tr><td>
			<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
				<tr>
					<?php if(isset($PlayersListResult) && is_array($PlayersListResult) && count($PlayersListResult) > 0){ ?>
					<td align="left" width="20%">No. of records found &nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
					<?php } ?>
					<td align="center">
							<?php if(isset($PlayersListResult) && is_array($PlayersListResult) && count($PlayersListResult) > 0 ) {
								pagingControlLatest($tot_rec,'TournamentUserList'); ?>
							<?php }?>
					</td>
				</tr>
			</table>
		</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td colspan= '2' align="center">
		</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td>
			<div class="tbl_scroll">
			
			  <form action="TournamentUserList" class="l_form" name="TournamentUserListForm" id="TournamentUserListForm"  method="post"> 
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr align="left">
						<th align="center" width="3%" class="text-center">#</th>												
						<th width="30%">Tournament </th>
						<th width="30%">Players</th>
						<th width="30%">No. of Turns</th>
						<th width="10%">Assigned Date </th>
					</tr>
					
			<?php 	if(isset($rulesListResult) && is_array($rulesListResult) && count($rulesListResult) > 0){
						foreach($rulesListResult as $key => $value){ 
							if(isset($rulesListArray[$value->fkTournamentsId]) && is_array($rulesListArray[$value->fkTournamentsId]) && count($rulesListArray[$value->fkTournamentsId])>0){
								$rowSpan	=	0;
								$rowSpan	=	count($rulesListArray[$value->fkTournamentsId]);
								if($rowSpan!=1){
			?>
									<tr class="test_id_<?php echo $value->id;?>"	>
										<td style="background-color:#fff" rowspan="<?php echo $rowSpan+1; ?>" valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
										<td style="background-color:#fff" rowspan="<?php echo $rowSpan+1; ?>" valign="top" align="center" >
											<p align="left" ><?php if(isset($value->TournamentName) && $value->TournamentName != '' && 0)	echo '<a class="recordView" href="TournamentDetail?viewId='.$value->fkTournamentsId.'&back=TournamentRulesList">'.trim($value->TournamentName).'</a>'; else echo $value->TournamentName;?></p>
											<div class="userAction" style="display:block; padding-top:8px; min-height:20px;" id="userAction">
												<a id="edit_<?php echo $value->id;?>" href="TournamentRulesManage?editId=<?php echo $value->fkTournamentsId; ?>" title="Edit Rules" alt="" class="editUse"><i class="fa fa-edit fa-lg"></i></a>
											</div>
										</td>
									</tr>
			<?php 					foreach($rulesListArray[$value->fkTournamentsId] as $countryKey=>$countryDetails) { ?>
										<tr class="test_id_<?php echo $value->id;?>" >
										<td >
			<?php 						if($countryDetails->fkCountriesId == $usId	&& isset($stateArray[$countryDetails->fkStatesId]))
												echo $countryDetails->Country.' - '.$stateArray[$countryDetails->fkStatesId];
											else echo $countryDetails->Country;?>
										</td>	
										<td valign="top"><?php if(isset($countryDetails->DateCreated) && $countryDetails->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($countryDetails->DateCreated)); }else echo '-';?></td>
										</tr>
			<?php 					} //End of for each ?>	
			<?php 				}  //End of row span more than one record 
								else { ?>
			<?php 					foreach($rulesListArray[$value->fkTournamentsId] as $countryKey=>$countryDetails) { ?>
										<tr class="test_id_<?php echo $value->id;?>" >
										<td style="background-color:#fff" valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
										<td style="background-color:#fff" valign="top" align="center" >
											<p align="left" ><?php if(isset($value->TournamentName) && $value->TournamentName != '' && 0)	echo '<a class="recordView" href="TournamentDetail?viewId='.$value->fkTournamentsId.'&back=TournamentRulesList">'.trim($value->TournamentName).'</a>'; else echo $value->TournamentName;?></p>
											<div class="userAction" style="display:block; padding-top:8px; min-height:20px;" id="userAction">
												<a id="edit_<?php echo $value->id;?>" href="TournamentRulesManage?editId=<?php echo $value->fkTournamentsId; ?>" title="Edit Rules" alt="" class="editUse"><i class="fa fa-edit fa-lg"></i></a>
											</div>
										</td>
										<td valign="middle" >
			<?php 						if($countryDetails->fkCountriesId == $usId	&& isset($stateArray[$countryDetails->fkStatesId]))
												echo $countryDetails->Country.' - '.$stateArray[$countryDetails->fkStatesId];
											else echo $countryDetails->Country;?>
										</td>	
										<td valign="middle"><?php if(isset($countryDetails->DateCreated) && $countryDetails->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($countryDetails->DateCreated)); }else echo '-';?></td>
										</tr>
			<?php 					} //End of for each ?>
			<?php 				} ?>
			<?php 			} // row span if end 
						} // foreach loop end ?> 																		

			<?php 	} else { ?>	
							<tr><td colspan="16" align="center" style="color:red;">No Record(s) Found</td></tr>
			<?php 	} ?>
				</table>
			</form>
			</div>
			
			</td>
		</tr>
	</table>
       
<?php commonFooter(); ?>
<script type="text/javascript">

$("#ses_date").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	maxDate			:	"0",
	closeText		:   "Close"
   });
jQuery(function() {
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[class^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");
	   
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
</script>
</html>
