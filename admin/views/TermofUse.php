<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminControllerObj =   new AdminController();

$content_1	= $content_2 =	$content_3 = '';

//add/update Term of Use page content
if(isset($_POST['website_term_submit']) && $_POST['website_term_submit'] != '' ){	
	$_POST       = unEscapeSpecialCharacters($_POST);
    $_POST       = escapeSpecialCharacters($_POST);
	
	if(isset($_POST['term_id']) && is_array($_POST['term_id']) && count($_POST['term_id']) > 0){
		$today	=	date('Y-m-d H:i:s');
		$updated_id = '';
		$values = '';
		foreach($_POST['term_id'] as $key => $value){
			if(trim($_POST['title'][$key]) != '' && trim($_POST['content'][$key])){
				if(trim($_POST['term_id'][$key]) > 0){
					$updated_id 	.= trim($_POST['term_id'][$key]).',';
					$condition      =   " id = ".trim($_POST['term_id'][$key]);	
					$updateString   =   ' Title = "'.trim($_POST['title'][$key]).'",Content = "'.trim($_POST['content'][$key]).'",DateModified="'.$today.'"';
					$adminControllerObj->updateWebsiteTermofUse($updateString,$condition);	
				}else{
					$values	.= "(null,'".$_POST['title'][$key]."','".$_POST['content'][$key]."',1,'".$today."','".$today."'),";
				}
			}
		}
		if($updated_id != ''){
			$condition	=	" id NOT IN ( ".substr($updated_id, 0 ,-1)." ) ";
			$adminControllerObj->deleteWebsiteTermofUse($condition);
		}
		if($values != ''){
			$values = substr($values, 0 ,-1);
			$adminControllerObj->addWebsiteTermofUse($values);
		}
	}
	
	$_SESSION['notification_msg_code']	=	1;
	header('location:TermofUse?msg=1');
	die();
}

//get Term of Use page content
$fields		  =	" * ";
$where		  =	" Status = 1 ";
$term_details = $adminControllerObj->getWebsiteTermofUse($fields, $where); 
commonHead();
?>
<body>
<?php top_header(); ?>	
	<div class="box-header"><h2><i class="fa fa-pencil-square-o" ></i>Terms Of Use</h2></div>
	<div class="clear"></div>
	<form name="website_term_form" id="website_term_form" action="" method="post" enctype="multipart/form-data">
		<table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">
		 <tr><td align="center">
		  <table cellpadding="5" cellspacing="5" align="center" border="0" width="80%">	
			<tr><td  valign="top" align="center" colspan="3">
			<?php displayNotification('Term of Use '); ?>
			</td></tr>
			
			<tr>
				<td align="center" class="" valign="top" width="12%">&nbsp;	</td>
				<td align="left" valign="top" width="23%"><label>Title</label></td>
				<td align="left" valign="top" width="65%"><label>Content</label></td>
			</tr>	
			<tr><td height="20" colspan="2"></td></tr>
			<?php if(isset($term_details) && is_array($term_details) && count($term_details)>0){ 
				foreach($term_details as $key=>$value){ ?>
				<tr class="clone">
					<td align="center" class="" valign="top" width="12%">
						<a href="javascript:void(0)" onclick="delTerms(this)" title="Delete"><i class="fa fa-lg  text-red  fa-minus-circle"></i></a>
						<span class="addNewRule" ><a href="javascript:void(0)" onclick="addTerms(this)" title="Add"><i class="fa fa-lg text-green fa-plus-circle"></i></a></span>&nbsp;&nbsp;
						<input type="hidden" name="term_id[]" value="<?php echo $value->id; ?>"/>
					</td>
					<td height="60" valign="top" align="left" width="23%">
						<input type="text" class="input term_title" style="width:210px" name="title[]" value="<?php echo $value->Title; ?>" />
					</td>
					<td height="60" valign="top" align="left">
						<textarea class="add_cms term_content" rows="15" cols="89" name="content[]"><?php echo $value->Content; ?></textarea>
					</td>
				</tr>
			<? }
			} else { ?>
				<tr class="clone">
					<td align="center" class="" valign="top" width="12%">
						<a href="javascript:void(0)" onclick="delTerms(this)"><i class="fa fa-lg  text-red  fa-minus-circle"></i></a>
						<span class="addNewRule" ><a href="javascript:void(0)" onclick="addTerms(this)"><i class="fa fa-lg text-green fa-plus-circle"></i></a></span>&nbsp;&nbsp;
						<input type="hidden" name="term_id[]" value=""/>
					</td>
					<td height="60" valign="top" align="left" width="23%">
						<input type="text" class="input term_title" style="width:210px" name="title[]" value="" />
					</td>
					<td height="60" valign="top" align="left" width="65%">
						<textarea class="add_cms term_content" rows="15" cols="89" name="content[]"></textarea>
					</td>
				</tr>
			<? } ?>
			<tr><td height="20" colspan="2"></td></tr>
			<tr>
			<td colspan="2"></td>
			<td align="left">
			<input type="submit" class="submit_button" name="website_term_submit" id="website_term_submit" value="Submit" title="Submit" onClick="return submitForm();" alt="Submit" />
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
		
		var termContent 	=	$(this).find("textarea.term_content").eq(0).val();
		var termTitle		=	$(this).find("input.term_title").eq(0).val();
		if(termContent != "" && termTitle != "")
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