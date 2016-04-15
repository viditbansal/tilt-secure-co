<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/UserController.php');
$userObj   =   new UserController();
require_once('controllers/TournamentController.php');
$turnsObj   =   new TournamentController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
global $link_type_array;
$display	=	"none";
$today		=	date('m-d-Y');
$where		=	' ';

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['sess_turns_tournament_name']);
	unset($_SESSION['sess_turns_user_name']);
	unset($_SESSION['sess_turns_total']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	$_SESSION['sess_turns_tournament_name']     = trim($_POST['tournament_name']);
	$_SESSION['sess_turns_user_name']     		= trim($_POST['user_name']); 
	$_SESSION['sess_turns_total']   			= trim($_POST['turns_total']); 
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$tourId = '';
$userIds = '';
if((isset($_SESSION['sess_turns_user_name']) && !empty($_SESSION['sess_turns_user_name'])) || (isset($_SESSION['sess_turns_total']) && $_SESSION['sess_turns_total'] !='') ){
	$condition  	= " t.CreatedBy in (1,3) ";
	$field			= " count(tp.id) as total_turns,u.FirstName,u.LastName,tp.id,tp.fkTournamentsId as TournamentId,u.id as UserId,u.UniqueUserId,GROUP_CONCAT(tp.DatePlayed) as DatePlayed";
	$turnsDetails 	= $userObj->turnsUserList($field,$condition);
	$tourIdArray = array();
	if(isset($turnsDetails) && is_array($turnsDetails) && count($turnsDetails) > 0){
		foreach($turnsDetails as $key0=>$value0){
			$tourIdArray[] = $value0->TournamentId;
			$userIds .= $value0->UserId.',';
		}
	}
	$condition  	= " t.CreatedBy in (1,3) ";
	$field			= " max(RoundTurn) as total_turns,u.FirstName,u.LastName,tp.id,tp.fkTournamentsId as TournamentId,u.id as UserId,u.UniqueUserId,GROUP_CONCAT(ep.DatePlayed) as DatePlayed ";
	$turnsDetails1 	= $userObj->roundsUserList($field,$condition);
	if(isset($turnsDetails1) && is_array($turnsDetails1) && count($turnsDetails1) > 0){
		foreach($turnsDetails1 as $key1=>$value1){
			$tourIdArray[] = $value1->TournamentId;
			$userIds .= $value1->UserId.',';
		}
	}
	if(is_array($tourIdArray) && count($tourIdArray) > 0){
		$tourIdArray = array_unique($tourIdArray);
		$tourId = implode(',', $tourIdArray);
	}
	if(empty($tourId))
		$tourId = 'noresult';
}

$fields				= ' t.id, t.TournamentName,t.CreatedBy, t.id as tournamentId, t.GameType, g.Photo as gamePhoto, g.Name as gameName,u.FirstName as userName,u.LastName as userLastName,gd.Company as devName';
if($tourId	!=	'noresult') {
	$tourId	=	rtrim($tourId,',');
	$condition			= 'g.Status = 1 and t.Status = 1 and t.CreatedBy in (1,3) ';
	if(!empty($tourId)) 
	$condition			.= ' AND t.id IN('.$tourId.') ';
	if(!empty($userIds)) {
		$userIds	=	rtrim($userIds,',');
	}
	$turnsResult		= $turnsObj->selectTournamentList($fields,$condition);
	$tot_rec 		 	= $turnsObj->getTotalRecordCount();
	if($tot_rec==0 && !is_array($turnsResult)) {
		$_SESSION['curpage'] = 1;
	$turnsResult	=	$turnsObj->selectTournamentList($fields,$condition);
	}
}
$turnsprocess	= array();
if(isset($turnsResult) && is_array($turnsResult) && count($turnsResult) >0){
	$turnsIds = $hid = $eid = '';
	foreach($turnsResult as $scankey=>$scanvalue){
		$turnsprocess[$scankey] = (array)$scanvalue;
		if($scanvalue->tournamentId != ''){
			$turnsIds .= $scanvalue->tournamentId.',';
			if($scanvalue->GameType == 2){
				$eid .= $scanvalue->tournamentId.',';
			}else{
				$hid .= $scanvalue->tournamentId.',';
			}
		}
	}
	$turnsIds	=	trim($turnsIds,',');
	$eid	=	trim($eid,',');
	$hid	=	trim($hid,',');
	if($hid != ''){
		$condition  	= " tp.fkTournamentsId in (".$hid.") and t.CreatedBy in (1,3)";
		$field			= " count(tp.id) as total_turns,u.FirstName,u.LastName,tp.id,tp.fkTournamentsId as TournamentId,u.id as UserId,u.UniqueUserId,GROUP_CONCAT(tp.DatePlayed) as DatePlayed";
		$turnsDetails 	= $userObj->turnsUserList($field,$condition);
		
		if(isset($turnsDetails) && is_array($turnsDetails) && count($turnsDetails) > 0){
			foreach($turnsprocess as $scankey=>$scanval){
				$i = 0;
				if($scanval['tournamentId'] != ''){
					$receiptIdArray = explode(',',$scanval['tournamentId']);
					foreach($turnsDetails as $key=>$value){
						if(in_array($value->TournamentId,$receiptIdArray)){
							if($value->UniqueUserId != '')
								$userArr = 'Guest'.$value->UserId;
							else
								$userArr = ucfirst($value->FirstName).' '.$value->LastName;
							$turnsprocess[$scankey]['tournament'][$userArr][$i]['tt']				= ($value->total_turns > 1) ? $value->total_turns.' Turns' : $value->total_turns.' Turn';
							$dateArray = explode(',',$value->DatePlayed);
							$mostRecent = 0;
							$mostDate = '';
							if(is_array($dateArray) && $dateArray != ''){
								foreach($dateArray as $date){
								  $curDate = strtotime($date);
								  if ($curDate > $mostRecent) {
									 $mostRecent = $curDate;
									 $mostDate	= $date;
								  }
								}
							}
							$turnsprocess[$scankey]['tournament'][$userArr][$i]['dateplayed']		= isset($mostDate) && $mostDate != '0000-00-00 00:00:00' ?$mostDate : '';
							$i++;
						}
					}
				}
			}
		}
	}
	
	if($eid != ''){
		$condition  	= " tp.fkTournamentsId in (".$eid.") and t.CreatedBy in (1,3)";
		$field			= " max(RoundTurn) as total_turns,u.FirstName,u.LastName,tp.id,tp.fkTournamentsId as TournamentId,u.id as UserId,u.UniqueUserId,GROUP_CONCAT(ep.DatePlayed) as DatePlayed ";
		$turnsDetails 	= $userObj->roundsUserList($field,$condition);
		if(isset($turnsDetails) && is_array($turnsDetails) && count($turnsDetails) > 0){
			foreach($turnsprocess as $scankey=>$scanval){
				$i = 0;
				if($scanval['tournamentId'] != ''){
					$receiptIdArray = explode(',',$scanval['tournamentId']);
					foreach($turnsDetails as $key=>$value){
						if(in_array($value->TournamentId,$receiptIdArray)){
							if($value->UniqueUserId != '')
								$userArr = 'Guest'.$value->UserId;
							else
								$userArr = ucfirst($value->FirstName).' '.$value->LastName;
							$turnsprocess[$scankey]['tournament'][$userArr][$i]['tt']				= ($value->total_turns > 1) ? $value->total_turns.' Rounds' : $value->total_turns.' Round';
							$dateArray = explode(',',$value->DatePlayed);
							$mostRecent = 0;
							$mostDate = '';
							if(is_array($dateArray) && $dateArray != ''){
								foreach($dateArray as $date){
								  $curDate = strtotime($date);
								  if ($curDate > $mostRecent) {
									 $mostRecent = $curDate;
									 $mostDate	= $date;
								  }
								}
							}
							$turnsprocess[$scankey]['tournament'][$userArr][$i]['dateplayed']		= isset($mostDate) && $mostDate != '0000-00-00 00:00:00' ?$mostDate : '';
							$i++;
						}
					}
				}
			}
		}
	}
}
?>
<?php top_header(); ?>
				         <div class="box-header"><h2><i class="fa fa-list"></i>Turns / Rounds List</h2></div>
						 	<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
								
								<tr>
				                    <td valign="top" align="center" colspan="2">										
										 <form name="turns" id="turns" action="TurnsList" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>
													<td width="10%" style="padding-left:20px;"><label>Tournament Name</label></td>
													<td width="1.3%" align="center">:</td>
													<td align="left"  height="40" width="15%" >
														<input style=" width: 75%;"  type="text" class="input " title="" name="tournament_name" value="<?php if(isset($_SESSION['sess_turns_tournament_name']) && $_SESSION['sess_turns_tournament_name'] != '') echo unEscapeSpecialCharacters($_SESSION['sess_turns_tournament_name']);?>">
													</td>
													<td width="10%"><label>User Name</label></td>
													<td width="1.3%" align="center">:</td>
													<td align="left"  height="40"  width="15%" >
														<input style=" width: 75%;"  type="text" class="input " title="" name="user_name" value="<?php if(isset($_SESSION['sess_turns_user_name']) && $_SESSION['sess_turns_user_name'] != '') echo unEscapeSpecialCharacters($_SESSION['sess_turns_user_name']);?>">
													</td>
													<td width="10%" style="padding-left:20px;"><label>Total No. of Turn(s) / Round(s)</label></td>
													<td width="1.3%" align="center">:</td>
													<td align="left"  height="40" width="15%" >
														<input style=" width: 75%;"  type="text" class="input " title="" name="turns_total" value="<?php if(isset($_SESSION['sess_turns_total']) && $_SESSION['sess_turns_total'] != '') echo unEscapeSpecialCharacters($_SESSION['sess_turns_total']);?>" onkeypress="return isNumberKey(event);" maxlength="10">
													</td>
												</tr>
												<tr><td align="center" colspan="9" style="padding-top:20px"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
												<tr><td height="10"></td></tr>
											 </table>
										  </form>	
				                    </td>
				               	</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td colspan="2">
										<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
											<tr>
												 <?php if(isset($turnsprocess) && is_array($turnsprocess) && count($turnsprocess) > 0){ ?>
												<td align="left" width="20%">No. of Tournament(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(isset($turnsprocess)	&&	is_array($turnsprocess) && count($turnsprocess) > 0 ) {
														 	pagingControlLatest($tot_rec,'TurnsList'); ?>
														<?php }?>
												</td>
											</tr>
											
										</table>
									</td>
								</tr>
								<tr><td colspan= '2' align="center"><div class="<?php  echo $class;  ?> w50"><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php if(isset($msg) && $msg != '') echo $msg;  ?></div></td></tr>
								<tr>
								</table>
									  <form action="LogTracking" class="l_form" name="LogTrackingForm" id="LogTrackingForm"  method="post"> 
										<table border="0" cellpadding="0" cellspacing="0" width="97%" align="center" class="user_table user_actions">
											<tr>
												<th width="1%" align="center" style="padding: 0;text-align: center">#</th>
												<th width="20%">Tournament</th>
												<th width="7%">Game</th>
												<th width="15%">User Name</th>
												<th width="12%">Total No. of Turn(s) / Round(s)</th>
												<th width="12%">Date Played</th>
											</tr>
										
											<?php if(isset($turnsprocess) && is_array($turnsprocess) && count($turnsprocess) > 0 ) { 
													foreach($turnsprocess as $key=>$value){
											 ?>									
								<tr>
									<td align="center" valign="top" nowrap><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
									<td><?php if(isset($value['TournamentName']) && $value['TournamentName'] != '' ) echo "<p>".$value['TournamentName']."</p>"; else echo '<p>-</p>';
											if($value['CreatedBy'] == 3){
												$devName = $value['devName'] != '' ? ucfirst($value['devName']) : '-';
												echo "<p><Strong>Developer & Brand : </Strong>".$devName. "</p>";
											}else{
												echo "<p><Strong>User : </Strong>".ucfirst($value['userName']).' '.$value['userLastName']."</p>";
											}
										?>
										
									</td>
									<?php 
										$image_path 	= ADMIN_IMAGE_PATH.'add_game.jpg';
										$original_path 	= ADMIN_IMAGE_PATH.'add_game.jpg';
										$photo 			= $value['gamePhoto'];
										if(isset($photo) && $photo != ''){
											$user_image 		= $photo;		
											$image_path_rel		= GAMES_THUMB_IMAGE_PATH_REL.$user_image;
											$original_path_rel 	= GAMES_IMAGE_PATH_REL.$user_image;
											if(SERVER){
												if(image_exists(7,$user_image)){
													$image_path 	= GAMES_THUMB_IMAGE_PATH.$user_image;
													$original_path 	= GAMES_IMAGE_PATH.$user_image;
												}
											}
											else if(file_exists($image_path_rel)){
													$image_path 	= GAMES_THUMB_IMAGE_PATH.$user_image;
													$original_path 	= GAMES_IMAGE_PATH.$user_image;
											}
										}
								?>
								<td>
										<?php if(isset($original_path) && $original_path != '' ) { ?><img class="user_img" width="36" 
										title="<?php echo $value['gameName'];?>" height="36" src="<?php echo $image_path;?>"><?php } else echo '-';?> 
								</td>
										<?php if(isset($value['tournament']) && is_array($value['tournament']) && count($value['tournament']) > 0){
													$userName = $turns = $date = '';
													foreach($value['tournament'] as $receiptkey => $receiptvalue){ 
														$new = array();
														$new = (array_keys($receiptvalue));
															foreach($new as $k=>$v){ 
																$userName .= (isset($receiptkey) && !empty($receiptkey)) ? '<p>'.$receiptkey.'</p>' : '<p>-</p>';
																$turns .= (isset($receiptvalue[$v]['tt']) && !empty($receiptvalue[$v]['tt'])) ? '<p>'.$receiptvalue[$v]['tt'].'</p>' : '<p>-</p>'; 
																$date .= (isset($receiptvalue[$v]['dateplayed']) && $receiptvalue[$v]['dateplayed'] != '0000-00-00 00:00:00')? '<p>'.date('m/d/Y',strtotime($receiptvalue[$v]['dateplayed'])).'</p>': '<p>-</p>';
															}
													}
													echo '<td>'.$userName.'</td>';
													echo '<td>'.$turns.'</td>';
													echo '<td>'.$date.'</td>';
										} else { ?>
													<td  width="">-</td>
													<td width="">-</td>
													<td width="">-</td></tr>
										<?php } ?>	
								<?php  }?>	
								
								<?php } else { ?>
								
								</tr>	
								<tr><td colspan="7" align="center" style="color:red;">No Turns(s) / Round(s) Found</td><?php } ?></tr>	
										</table>
									</form>
									
				        </div>
						
<?php commonFooter(); ?>
<script type="text/javascript">
$(".datepicker").datepicker({
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
	closeText		:   "Close"
   });
   </script>
</html>
