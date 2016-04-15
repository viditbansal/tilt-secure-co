<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminControllerObj =   new AdminController();

$content_1	= $content_2 =	$content_3 = '';

//Add/Update Privacy Policy page content
if(isset($_POST['website_privacy_submit']) && $_POST['website_privacy_submit'] != '' )
{	
	$_POST       = unEscapeSpecialCharacters($_POST);
    $_POST       = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['privacypolicy_id']) && is_array($_POST['privacypolicy_id']) && count($_POST['privacypolicy_id']) > 0){
		$today	=	date('Y-m-d H:i:s');
		$updated_id = '';
		$values = '';
		foreach($_POST['privacypolicy_id'] as $key => $value){
			if(trim($_POST['title'][$key]) != '' && trim($_POST['content'][$key])){
				if(trim($_POST['privacypolicy_id'][$key]) > 0){ //update
					$updated_id 	.= trim($_POST['privacypolicy_id'][$key]).',';
					$condition      =   " id = ".trim($_POST['privacypolicy_id'][$key]);
					$updateString   =   ' Title = "'.trim($_POST['title'][$key]).'",Content = "'.trim($_POST['content'][$key]).'",DateModified="'.$today.'"';
					$adminControllerObj->updateWebsitePrivacyPolicy($updateString,$condition);
				}else{
					//insert
					$values	.= "(null,'".$_POST['title'][$key]."','".$_POST['content'][$key]."',1,'".$today."','".$today."'),";
				}
			}
		}
		
		//delete 
		if($updated_id != ''){
			$condition	=	" id NOT IN ( ".substr($updated_id, 0 ,-1)." ) ";
			$adminControllerObj->deleteWebsitePrivacyPolicy($condition);
		}
		//insert
		if($values != ''){
			$values = substr($values, 0 ,-1);
			$adminControllerObj->addWebsitePrivacyPolicy($values);
		}
	}
	
	$_SESSION['notification_msg_code']	=	1;
	header('location:PrivacyPolicy?msg=1');
	die();
}

//get Privacy Policy page content
$fields		  =	" * ";
$where		  =	" Status = 1 ";
$privacypolicy_details = $adminControllerObj->getWebsitePrivacyPolicy($fields, $where); 
commonHead();
?>
<body>
<?php top_header(); ?>	
	<div class="box-header"><h2><i class="fa fa-pencil-square-o" ></i>Privacy Policy </h2></div>
	<div class="clear"></div>
	<form name="website_privacy_form" id="website_privacy_form" action="" method="post" enctype="multipart/form-data">
		<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">
			
		  <tr><td align="center">
		  <table cellpadding="5" cellspacing="5" align="center" border="0" width="80%">	
			<tr><td  height="40" align="center" colspan="3" valign="top">
			<?php displayNotification('Privacy Policy '); ?>
			</td></tr>
			
			<tr>
				<td align="center" class="" valign="top" width="12%">&nbsp;	</td>
				<td align="left" valign="top" width="23%"><label>Title</label></td>
				<td align="left" valign="top" width="65%"><label>Content</label></td>
			</tr>	
			<tr><td height="20" colspan="2"></td></tr>
			<?php if(isset($privacypolicy_details) && is_array($privacypolicy_details) && count($privacypolicy_details)>0){ 
				foreach($privacypolicy_details as $key=>$value){ ?>
				<tr class="clone">
					<td align="center" class="" valign="top" width="12%">
						<a href="javascript:void(0)" onclick="delPolicy(this)" title="Delete"><i class="fa fa-lg  text-red  fa-minus-circle"></i></a>
						<span class="addNewRule" ><a href="javascript:void(0)" onclick="addPolicy(this)" title="Add"><i class="fa fa-lg text-green fa-plus-circle"></i></a></span>&nbsp;&nbsp;
						<input type="hidden" name="privacypolicy_id[]" value="<?php echo $value->id; ?>"/>
					</td>
					<td height="60" valign="top" align="left" width="23%">
						<input type="text" class="input" id="policy_title" style="width:210px" name="title[]" value="<?php echo $value->Title; ?>" />
					</td>
					<td height="60" valign="top" align="left" width="65%"> 
						<textarea class="add_cms" id="policy_content" rows="15" cols="90" name="content[]"><?php echo $value->Content; ?></textarea>
					</td>
				</tr>
			<? }
			} else { ?>
				<tr class="clone">
					<td align="center" class="" valign="top" width="12%">
						<a href="javascript:void(0)" onclick="delPolicy(this)"><i class="fa fa-lg  text-red  fa-minus-circle"></i></a>
						<span class="addNewRule" ><a href="javascript:void(0)" onclick="addPolicy(this)"><i class="fa fa-lg text-green fa-plus-circle"></i></a></span>&nbsp;&nbsp;
						<input type="hidden" name="privacypolicy_id[]" value=""/>
					</td>
					<td height="60" valign="top" align="left" width="23%">
						<input type="text" class="input" id="policy_title" style="width:210px" name="title[]" value="" />
					</td>
					<td height="60" valign="top" align="left" width="65%">
						<textarea class="add_cms term_content" id="policy_content" rows="15" cols="90" name="content[]"></textarea>
					</td>
				</tr>
			<? } ?>
			<tr><td height="20" colspan="2"></td></tr>
			<tr>
			<td colspan="2"></td>
			<td align="left">
			<input type="submit" class="submit_button" name="website_privacy_submit" id="website_privacy_submit" value="Submit" title="Submit" onClick="return submitForm();" alt="Submit" />
			<a href="UserList?cs=1" class="submit_button" name="Cancel" id="Cancel" value="Cancel" title="Cancel" alt="Cancel" tabindex="NaN">Cancel</a>
			</td>
			</tr>
		</td></tr>
		</table>
		<tr><td height="10"></td></tr>
		</table>
	</form>
<?php commonFooter(); ?>
<script type="text/javascript">
function submitForm(){
	var errorflag = 1;	
	$("tr.clone").each(function() {
		var policyContent 	=	$(this).find("textarea").val();			
		var policyTitle		=	$(this).find(".input").eq(0).val();		
		if(policyContent != "" && policyTitle != "")
			errorflag = 0;
	});	
	if(errorflag == 1){
		alert("Atleast one row of title and content is required");
		return false;
	}
	return true;
}
$(document).ready(function() {
	$(".addNewRule").hide();
	$(".addNewRule:last").show();
});
</script>
</html>