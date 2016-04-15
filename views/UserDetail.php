<?php 
require_once('includes/CommonIncludes.php');
require_once('controllers/ReportController.php');
$reportObj  		=   new ReportController();
if(!isset($_SESSION['tilt_developer_id'])) { ?>
<script type="text/javascript">
   self.parent.location.href='index.php';
</script>
<?php
}
commonHead();
$original_image_path =  $original_cover_image_path = $actualPassword = '';

if(isset($_GET['id']) && $_GET['id'] != '' ){
	$condition       	= " id = ".$_GET['id']."  LIMIT 1 "; //no need check status for winner list
	$field				=	' * ';
	$userDetailsResult  = $reportObj->selectUserDetails($field,$condition);
	
	if(isset($userDetailsResult) && is_array($userDetailsResult) && count($userDetailsResult) > 0){
		$userId					=	$userDetailsResult[0]->id;
		if(isset($userDetailsResult[0]->UniqueUserId) && $userDetailsResult[0]->UniqueUserId !='') {
			$guestuser				=	'Guest'.$userDetailsResult[0]->id;
		}
		$firstname       		=	$userDetailsResult[0]->FirstName;
		$lastname				=	$userDetailsResult[0]->LastName;
		$email      			= 	$userDetailsResult[0]->Email;
		$fbId       			= 	$userDetailsResult[0]->FBId;
		$location  				= 	$userDetailsResult[0]->Location;
		$dateCreated    		= 	$userDetailsResult[0]->DateCreated;
		$virtualCoins			=	$userDetailsResult[0]->VirtualCoins;
		$tiltCoins				=	$userDetailsResult[0]->Coins;
		$uniqueUser	=	1;
		if(isset($userDetailsResult[0]->UniqueUserId) &&	$userDetailsResult[0]->UniqueUserId !='')
			$uniqueUser	=	0;
		if(isset($userDetailsResult[0]->Photo) && $userDetailsResult[0]->Photo != ''){
			$user_image = $userDetailsResult[0]->Photo;
			if(image_exists(3,$user_image))
				$original_image_path = USER_IMAGE_PATH.$user_image;
			else
				$original_image_path = '';			
			if(image_exists(1,$user_image)){
				$image_path = USER_THUMB_IMAGE_PATH.$user_image;
			}
			else
				$image_path = GAME_IMAGE_PATH.'no_user.jpeg';
		}
		else
			$image_path = GAME_IMAGE_PATH.'no_user.jpeg';
	}	
}


?>
<body class="popup_bg" style="overflow-x:hidden;">
		<div class="tab-content" id='list_content' style="border:0px;margin:0px;">
			<h2 id="heading_title"><i class="fa fa-search"></i>&nbsp;Winner Detail</h2>
							 
			<table align="center" cellpadding="0" cellspacing="0" border="0" class="list headertable" width="98%">							        
			<tr><td align="center"><?php displayNotification('Coins'); ?></td></tr>
			<tr><td height="10"></td></tr>
				<tr>
					<td align="center">
						<table cellpadding="0" cellspacing="0" align="center" border="0" width="95%">
							<tr>
								<td align="left" valign="top" width="17%"><label>First Name</label></td>
								<td align="center" valign="top" width="3%">:</td>
								<td align="left" valign="top" width="30%"><?php if(isset($guestuser) && $guestuser!='') echo $guestuser; else if(isset($firstname) && $firstname != '') echo $firstname; else echo '-'; ?></td>										
								<td align="left" valign="top" width="17%"><label>Last Name</label></td>
								<td align="center" valign="top" width="3%">:</td>										
								<td align="left" valign="top" width="30%"><?php if(isset($lastname) && $lastname != '') echo $lastname;  else echo '-'; ?></td>										
							</tr>
							<tr><td height="15"></td></tr>
							<tr>
								<td align="left" valign="top" ><label>Email</label></td>
								<td align="center" valign="top">:</td>										
								<td align="left" valign="top"><?php if(isset($email) && $email != '') echo $email;  else echo '-'; ?></td>
								<td  align="left" valign="top"><label>Registered Date</label></td>
								<td align="center" valign="top">:</td>
								<td align="left"   valign="top"><?php if(isset($dateCreated) && $dateCreated != '' ) echo date('m/d/Y',strtotime($dateCreated)); else echo '-'; ?></a></td>
							</tr>
							<tr><td height="15"></td></tr>
							<tr>
								<td align="left" valign="top"><label>Facebook Id</label></td>
								<td align="center" valign="top">:</td>
								<td align="left" valign="top" ><?php  if(isset($fbId) && $fbId != '' ) echo $fbId; else echo '-';  ?></td>
								<?php if(isset($uniqueUser) && $uniqueUser !=0){ ?>
								<td  align="left" valign="top"><label>TiLT$</label></td>
								<td align="center" valign="top">:</td>
								<td align="left"   valign="top"><?php if(isset($tiltCoins) && $tiltCoins != '' ) echo number_format($tiltCoins); else echo '0'; ?></a></td>
								<?php } else { ?>
								<td align="left" valign="top"><label>Location</label></td>
								<td align="center" valign="top">:</td>
								<td align="left" valign="top"><?php if(isset($location) && $location != '') echo $location; else echo '-'; ?></td>
								<?php } ?>
							</tr>
							<tr><td height="15"></td></tr>
							<tr>
								<td align="left" valign="top"><label>Virtual Coins</label></td>
								<td align="center" valign="top">:</td>
								<td align="left" valign="top" ><?php  if(isset($virtualCoins) && $virtualCoins != '' ) echo number_format($virtualCoins); else echo '0';  ?></td>
								<?php if(isset($uniqueUser) && $uniqueUser !=0){ ?>
								<td align="left" valign="top"><label>Location</label></td>
								<td align="center" valign="top">:</td>
								<td align="left" valign="top"><?php if(isset($location) && $location != '') echo $location; else echo '-'; ?></td>
								<?php } ?>
							</tr>												
							<tr><td height="15"></td></tr>	
							<tr>
								<td align="left" valign="top"><label>Photo</label></td>
								<td align="center" valign="top">:</td>
								<td align="left" valign="top">
								<?php if(isset($image_path) && $image_path != '') { ?> <img class="img_border" width="75" height="75" src="<?php echo $image_path;?>"><?php } ?>
								</td>
								
								</tr>
								<tr><td height="15"></td></tr>
								<tr>										
									<td colspan="6" align="center">
									<?php $params	=	'';
									if(isset($_GET['gameId']) && $_GET['gameId'] !='' ){ 
										$params	.=	'Winnerslist?id='.$_GET['gameId'];
										if(isset($_GET['gameName']) && $_GET['gameName'] !='' ) 
											$params	.=	'&gameName='.$_GET['gameName'];
											$params	.=	'&cs=1';
											
									}
									else if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
										$params  = "TournamentWinners?viewId=".$_GET['viewId'];
										if(isset($_GET['tournamentName']) && $_GET['tournamentName'] != '')
											$params	.=	'&tournamentName='.$_GET['tournamentName'];
										if(isset($_GET['elimination']) && isset($_GET['playedId']) && !empty($_GET['playedId']) )
											$params	.=	'&elimination=1&playedId='.$_GET['playedId'];
										if(isset($_GET['custom']) ){$params	.=	'&custom=1';}
									}
									?>
										<button class="btn btn-green" type="button" id="back" name="back" value="Back" onclick="javascript:window.location.href='<?php echo $params;?>&cs=1'" title="Back">Back</button>
									</td>
								</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
						  
<?php commonFooter(); ?>
</html>
