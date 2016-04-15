<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/CoinsController.php');
$coinsManageObj   =   new CoinsController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && ($_GET['cs']=='1' || $_GET['cs']=='2' )) {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_user_platform']);
	unset($_SESSION['mgc_sess_user_name']);
	unset($_SESSION['mgc_sess_devBrand_name']);
	unset($_SESSION['mgc_sess_email']);
	unset($_SESSION['mgc_sess_user_status']);
	unset($_SESSION['mgc_sess_user_registerdate']);
	unset($_SESSION['mgc_sess_virtualCoinList_type']);
}
if(isset($_GET['type'])	&&	$_GET['type']	!=''){		// For user created pop up
	$_SESSION['mgc_sess_virtualCoinList_type']	=	$_GET['type'];
}
$listType	=	1;
if(isset($_SESSION['mgc_sess_virtualCoinList_type']) && $_SESSION['mgc_sess_virtualCoinList_type'] !='')
	$listType	=	$_SESSION['mgc_sess_virtualCoinList_type'];
else $_SESSION['mgc_sess_virtualCoinList_type'] = 1;

if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	
	//To remove special characters from the posted data
	$_POST	= unEscapeSpecialCharacters($_POST);
    $_POST	= escapeSpecialCharacters($_POST);
	
	if(isset($_POST['ses_username']))
		$_SESSION['mgc_sess_user_name'] 	=	trim($_POST['ses_username']);
	if(isset($_POST['ses_devbrandname']))
		$_SESSION['mgc_sess_devBrand_name'] 	=	trim($_POST['ses_devbrandname']);
		
	if(isset($_POST['ses_date']) && $_POST['ses_date'] != ''){
		$validate_date = dateValidation($_POST['ses_date']);
		if($validate_date == 1){
			$date = date('Y-m-d',strtotime($_POST['ses_date']));
			if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
				$_SESSION['mgc_sess_user_registerdate']	= $date;
			else 
				$_SESSION['mgc_sess_user_registerdate']	= '';
		}
		else 
			$_SESSION['mgc_sess_user_registerdate']	= '';
	}
	else 
		$_SESSION['mgc_sess_user_registerdate']	= '';
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$brandActive = '';
$userActive = '';
if($listType == 2){
	$brandActive = 'active';
	$userActive = '';
	$fields    = " vc.*,db.id as devBrandId,db.Name as devBrandName,db.Email,db.Company,db.Photo, db.Status as devBrandStatus";
	$condition = " AND vc.Status = 1 AND fkDevelopersId != '' AND fkUsersId = 0 ";	
	$virtualcoinsList = $coinsManageObj->getDevBrandVirtCoinList($fields,$condition);
}
else{
	$brandActive = '';
	$userActive = 'active';
	$fields    = " vc.*, u.id as UserId,u.FirstName , u.LastName , u.Photo,UniqueUserId, u.Status as userstatus";
	$condition = " AND vc.Status = 1  AND fkUsersId != 0 AND  fkBrandsId = 0";
	$virtualcoinsList  = $coinsManageObj->getVirtCoinList($fields,$condition);
}
$tot_rec 		 = $coinsManageObj->getTotalRecordCount();
?>
<body>
<?php top_header(); ?>
	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i>Virtual Coins List</h2>
	 	<span class="fright">	
		<?php  if($listType == 2){?>	
			<a  class="addvirtcoins" href="AddDevBrandVirtualCoin?refer=1" title="Add Virtual Coins"><i class="fa fa-plus-circle"></i> Add Virtual Coins</a>
		<?php } else {?>	
			<a class="addvirtcoins" href="AddVirtualCoin?refer=1"  title="Add Virtual Coins"><i class="fa fa-plus-circle"></i> Add Virtual Coins</a>		
		<?php } ?>
		</span>
	</div>
    <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
		<tr><td  class="filter_form">
		<form name="search_category" action="VirtualCoinList" method="post">
			<table align="center" cellpadding="6" cellspacing="0" border="0" width="100%">									       
				<tr><td></td></tr>
				<tr>	
					<?php  if($listType == 2){?>												
						<td width="10%" align="left" style="padding-left:20px;"><label>Developer & Brand</label></td>
						<td width="2%" align="center">:</td>
						<td align="left" height="40"><input type="text" class="input" name="ses_devbrandname" id="ses_devbrandname"  value="<?php  if(isset($_SESSION['mgc_sess_devBrand_name']) && $_SESSION['mgc_sess_devBrand_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_devBrand_name']);  ?>"></td>
					<?php } else { ?>
						<td width="10%" align="left" style="padding-left:20px;"><label>User</label></td>
						<td width="2%" align="center">:</td>
						<td align="left" height="40"><input type="text" class="input" name="ses_username" id="ses_username"  value="<?php  if(isset($_SESSION['mgc_sess_user_name']) && $_SESSION['mgc_sess_user_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_user_name']);  ?>" ></td>
					<?php }?>
					<td width="10%" align="left" style="padding-left:20px;"><label>Assigned Date</label></td>
					<td width="2%" align="center">:</td>
					<td align="left" height="40">
						<input  type="text" autocomplete="off"  maxlength="10" class="input w50" name="ses_date" id="ses_date" title="Select Date" value="<?php if(isset($_SESSION['mgc_sess_user_registerdate']) && $_SESSION['mgc_sess_user_registerdate'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_sess_user_registerdate'])); else echo '';?>" onkeypress="return dateField(event);" > (mm/dd/yyyy)
					</td>
				</tr>
				<tr><td align="center" colspan="6" style="padding-top:20px"><input type="submit" class="submit_button" name="Search" id="Search" title="Search" value="Search"></td></tr>
				<tr><td></td></tr>
			</table>
			</form>
		</td></tr>
		<tr><td height="20"></td></tr>
		<tr><td>
			<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
				<tr>
					<?php if(isset($virtualcoinsList) && is_array($virtualcoinsList) && count($virtualcoinsList) > 0){ ?>
					<td align="left" width="20%"> No. of <?php if($listType == 2) echo 'Developer & Brand';  else  echo 'User'; ?>(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
					<?php } ?>
					<td align="center">
							<?php if(isset($virtualcoinsList) && is_array($virtualcoinsList) && count($virtualcoinsList) > 0 ) {
								pagingControlLatest($tot_rec,'VirtualCoinList'); ?>
							<?php }?>
					</td>
				</tr>
			</table>
		</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td colspan= '2' align="center">
			<?php displayNotification('Virtual Coins'); ?>
		</td></tr>
		<tr>
			<td>
			<div class="nav-tabs-custom">
				<ul class="nav nav-styles">
					<li class=" <?php echo $userActive; ?>"><a href="VirtualCoinList?type=1&cs=1">User Virtual Coins</a></li>
					<li class=" <?php echo $brandActive; ?>"><a href="VirtualCoinList?type=2&cs=2">Developer & Brand Virtual Coins</a></li>
				</ul>
			<div class="tbl_scroll tab-content">		
			  <form action="VirtualCoinList" class="l_form" name="VirtualCoinList" id="VirtualCoinListForm"  method="post"> 
				<table border="0" cellpadding="1" cellspacing="0" width="100%"  class="user_table user_actions" align="center">
					<tr align="left">
						<th align="center" width="3%" class="text-center" width="4%">#</th>												
						<th width="35%"><?php if($listType == 2) echo SortColumn('Name','Developer & Brand'); else echo SortColumn('FirstName','User'); ?></th>
						<th><?php echo SortColumn('VirtCoins','Virtual Coins'); ?></th>	
						<th width="20%" >Assigned Date</th>						
					</tr>
					<?php if(isset($virtualcoinsList) && is_array($virtualcoinsList) && count($virtualcoinsList) > 0 ) { 
							foreach($virtualcoinsList as $key=>$value){
								if(isset($value->UserId) && $value->UserId !='') {
									$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
									$original_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
									$photo = $value->Photo;
									if(isset($photo) && $photo != ''){
										$user_image = $photo;		
										$image_path_rel = USER_THUMB_IMAGE_PATH_REL.$user_image;
										$original_path_rel = USER_IMAGE_PATH_REL.$user_image;
										if(SERVER){
											if(image_exists(1,$user_image)){
												$image_path = USER_THUMB_IMAGE_PATH.$user_image;
												$original_path = USER_IMAGE_PATH.$user_image;
											}									
										}
										else if(file_exists($image_path_rel)){
												$image_path = USER_THUMB_IMAGE_PATH.$user_image;
												$original_path = USER_IMAGE_PATH.$user_image;
										}
									}
									$userName	=	'';
									if(isset($value->UniqueUserId) && $value->UniqueUserId !='') { $userName	= 'Guest'.$value->UserId; } 
									else{
										if(isset($value->FirstName)	&&	isset($value->LastName)) 	
											$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
										else if(isset($value->FirstName))	
											$userName	=	 ucfirst($value->FirstName);
										else if(isset($value->LastName))	
											$userName	=	ucfirst($value->LastName);
									}
								}else{
									$image_path = ADMIN_IMAGE_PATH.'developer_logo.png';
									$original_path = '';
									$photo = isset($value->Photo) ? $value->Photo : '' ;
									if(isset($photo) && $photo != ''){
										$devBrand_image = $photo;		
										$image_path_rel = DEVELOPER_THUMB_IMAGE_PATH_REL.$devBrand_image;
										$original_path_rel = DEVELOPER_IMAGE_PATH_REL.$devBrand_image;
										if(SERVER){
											if(image_exists(5,$devBrand_image)){
												$image_path		= DEVELOPER_THUMB_IMAGE_PATH.$devBrand_image;
												$original_path 	= DEVELOPER_IMAGE_PATH.$devBrand_image;
											}
										}
										else if(file_exists($image_path_rel)){
												$image_path		= DEVELOPER_THUMB_IMAGE_PATH.$devBrand_image;
												$original_path 	= DEVELOPER_IMAGE_PATH.$devBrand_image;
										}
									}
								} ?>									
					<tr id="test_id_<?php echo $value->id;?>"	>
						<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						 <td valign="top" align="center" >	
						<?php if(isset($value->UserId) && $value->UserId !='') { ?>						
							<div  style="<?php if(isset($cover_path) && $cover_path != '' ) { ?>
											background: url('<?php echo $cover_path;?>') no-repeat;<?php 
										} else { 
											?>background: none no-repeat; 
								<?php 	} ?>;background-size:cover;float:left">
								<a <?php if(isset($original_path) && $original_path != ADMIN_IMAGE_PATH.'no_user.jpeg' ) { ?> href="<?php echo $original_path; ?>" class="fancybox"  <?php } ?> title="View Photo"  ><img class="user_img" width="36" height="36" src="<?php echo $image_path;?>" ></a>
							</div>
												
							<div class="user_profile">
								<p align="left" style="padding-left:50px;padding-bottom:2px">
									<?php $uniqueFlag	=	'';
									if(isset($value->UniqueUserId) && $value->UniqueUserId !='') $uniqueFlag	=	'&UniqueUser=1';
									
									if(isset($userName) && $userName != '')	
										 if($value->userstatus == 3){
											echo trim($userName);
										}else if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
											echo $userName;
										else 
											echo '<a class="recordView" href="UserDetail?viewId='.$value->UserId.'&back=VirtualCoinList'.$uniqueFlag.'">'.trim($userName).'</a>'; 
									else echo '-';?>
								</p>								 
							</div>
						<?php } else {?>
								<div  style="background: none no-repeat; ;background-size:cover;float:left">
								<a <?php if(isset($original_path) && !empty($original_path) && $original_path != ADMIN_IMAGE_PATH.'developer_logo.png' ) { ?> href="<?php echo $original_path; ?>" class="brand_image_pop_up"  <?php } else echo ' href="javascript:void(0);" '; ?> title="View Photo"  ><img class="user_img" width="36" height="36" src="<?php echo $image_path;?>" ></a>
								</div>
								<div class="user_profile">
								<p align="left" style="padding-left:50px">
								
								<?php 	
								if(isset($value->Company) && $value->Company != '' && $value->devBrandStatus  != 3)	{ ?>
								<a href="#" class="recordView" onclick="window.parent.location.href='GameDeveloperDetail?back=VirtualCoinList&viewId=<?php echo $value->devBrandId;?>';"><?php echo trim($value->Company); ?></a><?php } else if(isset($value->Company) && $value->Company != '') echo trim($value->Company);  else echo '-';?></p>
								</div>							
						<?php }?>
						</td>
						<td valign="top"><?php if(isset($value->VirtCoins) && $value->VirtCoins != '' ){ echo number_format($value->VirtCoins);}else echo '-';?></td>						
						<td valign="top"><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
					</tr>
					<?php } ?> 																		
				</table>
				</div>
				</form>
				<?php } else { ?>	
					<tr>
						<td colspan="16" align="center" style="color:red;">No Record(s) Found</td>
					</tr>					
				<?php } ?>
			</div>
			</td>
		</tr>
	</table>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".fancybox").colorbox({
	title:true,
	maxWidth:"50%", 
	maxHeight:"50%"
});	  
$(".brand_image_pop_up").colorbox({title:true}); 
$(".addvirtcoins").colorbox({
		iframe:true,
		width:"700", 
		height:"400",
		title:true,
});
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
	jQuery('table.user_actions tr[id^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");
	   
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
</script>
</html>
