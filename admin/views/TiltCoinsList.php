<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/CoinsController.php');
$coinsManageObj   =   new CoinsController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['mgc_sess_user_name']);
	unset($_SESSION['mgc_coin_assigned_date']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	
	//To remove special characters from the posted data
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['ses_username']))
		$_SESSION['mgc_sess_user_name'] 	=	trim($_POST['ses_username']);
	if(isset($_POST['ses_date']) && $_POST['ses_date'] != ''){
		$validate_date = dateValidation($_POST['ses_date']);
		if($validate_date == 1){
			$date = date('Y-m-d',strtotime($_POST['ses_date']));
			if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
				$_SESSION['mgc_coin_assigned_date']	= $date;
			else 
				$_SESSION['mgc_coin_assigned_date']	= '';
		}
		else 
			$_SESSION['mgc_coin_assigned_date']	= '';
	}
	else 
		$_SESSION['mgc_coin_assigned_date']	= '';
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " tc.*,u.FirstName,u.LastName,u.Email,u.id as UserId,u.Status as userstatus,u.UniqueUserId ";
$condition = " tc.Status =1 ";
$coinsListResult  = $coinsManageObj->getTiltCoinsList($fields,$condition);
$tot_rec 		 = $coinsManageObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($coinsListResult)) {
	$_SESSION['curpage'] = 1;
	$coinsListResult  = $coinsManageObj->getTiltCoinsList($fields,$condition);
}
?>
<body>
<?php top_header(); ?>
	 <div class="box-header">
	 	<h2><i class="fa fa-list"></i>TiLT$ List</h2>
	 	<span class="fright"><a href="AddTiltCoin?refer=1" class="addTiltCoins" title="Add TiLT$"><i class="fa fa-plus-circle"></i> Add TiLT$</a></span>
	</div>
    <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
		
		<tr><td class="filter_form" >
			<form name="search_category" action="TiltCoinsList" method="post">
			<table align="center" cellpadding="6" cellspacing="0" border="0"width="100%">									       
				<tr><td></td></tr>
				<tr>													
					<td width="5%" style="padding-left:20px;" ><label>User</label></td>
					<td width="1%" align="center" >:</td>
					<td align="left" width="15%">
						<input type="text" class="input" name="ses_username" id="ses_username"  value="<?php  if(isset($_SESSION['mgc_sess_user_name']) && $_SESSION['mgc_sess_user_name'] != '') echo unEscapeSpecialCharacters($_SESSION['mgc_sess_user_name']);  ?>" >
					</td>
					<td  align="left" width="5%" style="padding-left:20px;"><label>Assigned Date</label></td>
					<td width="1%" align="center" >:</td>
					<td align="left" width="15%">
						<input  type="text" autocomplete="off"  maxlength="10" class="input w50" name="ses_date" id="ses_date" title="Select Date" value="<?php if(isset($_SESSION['mgc_coin_assigned_date']) && $_SESSION['mgc_coin_assigned_date'] != '') echo date('m/d/Y',strtotime($_SESSION['mgc_coin_assigned_date'])); else echo '';?>" onkeypress="return dateField(event);">  (mm/dd/yyyy)
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
					<?php if(isset($coinsListResult) && is_array($coinsListResult) && count($coinsListResult) > 0){ ?>
					<td align="left" width="20%">No. of User(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
					<?php } ?>
					<td align="center">
							<?php if(isset($coinsListResult) && is_array($coinsListResult) && count($coinsListResult) > 0 ) {
								pagingControlLatest($tot_rec,'TiltCoinsList'); ?>
							<?php }?>
					</td>
				</tr>
			</table>
		</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td colspan= '2' align="center">
			<?php displayNotification('TiLT$'); ?>
		</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td>
			<div class="tbl_scroll">
			
			  <form action="TiltCoinsList" class="l_form" name="TiltCoinsListForm" id="TiltCoinsListForm"  method="post"> 
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
					<tr align="left">
						<th align="center" width="3%" class="text-center">#</th>												
						<th width="30%"><?php echo SortColumn('FirstName','User'); ?></th>
						<th width="30%"><?php echo SortColumn('Email','Email'); ?></th>
						<th width="10%"><?php echo SortColumn('TiltCoins','TiLT$'); ?></th>
						<th width="10%"><?php echo SortColumn('DateCreated','Assigned Date'); ?></th>
					</tr>
					
					<?php if(isset($coinsListResult) && is_array($coinsListResult) && count($coinsListResult) > 0 ) { ?>
					
					<?php foreach($coinsListResult as $key=>$value){
							$userName	=	' - ';
							if(isset($value->UniqueUserId) && $value->UniqueUserId !='')
								$userName = 'Guest'.$value->id;
							else if(isset($value->FirstName)	&&	isset($value->LastName)) 	
								$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
							else if(isset($value->FirstName))	
								$userName	=	 ucfirst($value->FirstName);
							else if(isset($value->LastName))	
								$userName	=	ucfirst($value->LastName);
					 ?>									
					<tr id="test_id_<?php echo $value->id;?>"	>
						<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td valign="top" align="center" >
								<p align="left" >
									<?php if(isset($value->UniqueUserId) && $value->UniqueUserId !='') 	echo $userName;
									else if(isset($value->UserId) && $value->UserId != '' && $value->userstatus != 3 )	echo '<a class="recordView" href="UserDetail?viewId='.$value->UserId.'&back=TiltCoinsList">'.trim($userName).'</a>'; else echo $userName;?>
								</p>
						</td>
						<td valign="top"><?php if(isset($value->Email) && $value->Email != '' ){ echo $value->Email;}else echo '-';?></td>
						<td valign="top"><?php if(isset($value->TiltCoins) && $value->TiltCoins != '' ){ echo number_format($value->TiltCoins);}else echo '0';?></td>
						<td valign="top"><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
					</tr>
					<?php } ?> 																		
				</table>
				<?php if(isset($coinsListResult) && is_array($coinsListResult) && count($coinsListResult) > 0){ 
				?>
				<?php } ?>
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
  $(document).ready(function() {		
		$(".addTiltCoins").colorbox(
		{
				iframe:true,
				width:"700", 
				height:"400",
				title:true,
		});
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
