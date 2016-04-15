<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$mediaObj   =   new LogController();
$condition	=	'';

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_username']);
	unset($_SESSION['mgc_sess_user_email']);
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);

if(isset($_POST['Search'])	&&	$_POST['Search'] != '')	{
	$_SESSION['mgc_sess_username'] = $_SESSION['mgc_sess_user_email'] ='';
	$pagingParam  = "?id=".$_GET['id'];
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['username'])	&&	$_POST['username'])
		$_SESSION['mgc_sess_username']	=	trim($_POST['username']);
	if(isset($_POST['email'])	&&	$_POST['email'])
		$_SESSION['mgc_sess_user_email']	=	trim($_POST['email']);	
}
if(isset($_SESSION['mgc_sess_username'])	&&	$_SESSION['mgc_sess_username'])
	$condition .= " and (u.FirstName LIKE '%".$_SESSION['mgc_sess_username']."%' OR	u.LastName LIKE '%".$_SESSION['mgc_sess_username']."%' OR CONCAT( u.FirstName,  ' ', u.LastName ) LIKE  '%".$_SESSION['mgc_sess_username']."%')";
if(isset($_SESSION['mgc_sess_user_email'])	&&	$_SESSION['mgc_sess_user_email'])
	$condition	.=	' AND u.email	LIKE "'.$_SESSION['mgc_sess_user_email'].'%" ';
	
$fields		= ' u.id,SUM( CASE WHEN mi.Type =1 THEN 1 ELSE 0 END ) AS impCount, u . * '; 
$pagingParam  = "?id=".$_GET['id'];

$condition 	.= ' AND mi.`fkTournamentsId` ='.$_GET["id"].' AND u.Status !=3 AND mi.type=1 ';
$mediaTrackResult	= $mediaObj->getMediaImpression($fields,$condition);
$tot_rec 		 	= $mediaObj->getTotalRecordCount();

?>

<body class="popup_bg" style="overflow-x:hidden;">
			<div class="box-header">
				<h2><i class="fa fa-list"></i>Impression List</h2>
			</div>
			<div class="clear">
				<div class="clear">
					<table cellspacing="0" cellpadding="0" width="98%" border="0" align="center" class="headertable">
						
						<tr>
							<td colspan="2" align="center">
								<form name="user_search" action="<?php echo 'ImpressionList'.$pagingParam;?>&cs=1" method="post">
									<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" class="filter_form">							
										<tr><td height="15"></td></tr>
										<tr>
											<td>
												<td width="10%" style="padding-left:20px;"><label>User Name</label></td>
												<td width="3%" align="center">:</td>
												<td height="40" align="left">
													<div class="controls span8"><input type="text" class="input w95" name="username" id="username"  value="<?php  if(isset($_SESSION['mgc_sess_username']) && $_SESSION['mgc_sess_username'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_username']);  ?>" ></div>
												</td>
												<td width="10%" style="padding-left:20px;"><label>Email</label></td>
												<td width="3%" align="center">:</td>
												<td>
													<div class="controls span8"><input type="text" class="input w95" name="email" id="email"  value="<?php  if(isset($_SESSION['mgc_sess_user_email']) && $_SESSION['mgc_sess_user_email'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_user_email']);  ?>" ></div>
												</td>
											</td>
										</tr>
										<tr><td height="10"></td></tr>
										<tr>
											<td align="center" colspan="7"><input type="submit" class="submit_button" name="Search" id="Search" value="Search" title="Search"></td>
										</tr>
										<tr><td height="15"></td></tr>
									</table>
								</form>
							</td>
						</tr>
						<tr><td height="20"></td></tr>
						<tr>
							<td colspan="2">
								<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center">
									<tr>
										<td width="20%" align="left">
											<?php if(isset($mediaTrackResult) && is_array($mediaTrackResult) && count($mediaTrackResult) > 0){ ?>
											<span class="totl_txt">No. of User(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></span>
											<?php } ?>
												<?php if(isset($mediaTrackResult) && is_array($mediaTrackResult) && count($mediaTrackResult) > 0 ) {										
												pagingControlLatest($tot_rec,'ImpressionList'.$pagingParam); ?>
												<?php }?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr><td height="20"></td></tr>
						<tr>
							<td colspan="2">
								
							</td>
						</tr>
					</table>
				</div>
				<div class="clear" style="width: 100%">
					<form action="ImpressionList" class="l_form" name="impression_list" id="impression_list"  method="post"> 
						<div class="table-responsive">
							<table cellpadding="0" cellspacing="0" border="0" width="100%" class="">
								<tr>
									<td>
										<table cellpadding="0" cellspacing="0" border="0" class="product_list report user_table" width="98%" align="center">
											<tr>
												<th align="center" width="1%" class="text-center">#</th>
												<th align="left" width="15%">User Name</th>
												<th align="left" width="15%">Email</th>
												<th align="left" width="15%">Count</th>
											</tr>
											<?php if(isset($mediaTrackResult) && is_array($mediaTrackResult) && count($mediaTrackResult) > 0 ) { 
													 foreach($mediaTrackResult as $key=>$value){
														$userName	=	'';
													if(isset($value->UniqueUserId) && $value->UniqueUserId != '')
														$userName	=	'Guest'.$value->id;
													else if(isset($value->FirstName)	&&	isset($value->LastName)) 	
														$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
													else if(isset($value->FirstName))	
														$userName	=	 ucfirst($value->FirstName);
													else if(isset($value->LastName))	
														$userName	=	ucfirst($value->LastName);
											 ?>									
											<tr>
												<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
												<td><?php echo $userName; ?></td>
												<td><?php if(isset($value->Email) && $value->Email != '') echo $value->Email; else echo '-';?></td>
												<td><?php if(isset($value->impCount) && $value->impCount != '') echo $value->impCount; else echo '-';?></td>
											</tr>
											<?php } ?>
										</table>
									<?php } else { ?>	
									<tr>
										<td colspan="4" align="center" style="color:red;">No Impression(s) Found</td>
									</tr>
								</td>
							</tr>
						</table>
						<?php } ?>
					</div>
				</form>
			</div>
			<?php commonFooter(); ?>	
</body>
<script type="text/javascript">
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