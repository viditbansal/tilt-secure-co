<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$msg = '';
$adminLoginObj   =   new AdminController();
$fields = '*';
$where  = '1';
$static_details  = $adminLoginObj->getCMS($fields,$where);
if(isset($_POST['cms_submit']) && $_POST['cms_submit'] == 'Submit' ){
		$_POST          =   unEscapeSpecialCharacters($_POST);
   		$_POST          =   escapeSpecialCharacters($_POST);
		$updateString   =   " Content  = '".$_POST['cms_about']."' ,DateModified = '".date('Y-m-d H:i:s')."'";
		$condition      =   " id = 1 ";
		$adminLoginObj->updateCMSDetails($updateString,$condition);
		$updateString   =   " Content  = '".$_POST['cms_privacy']."' ,DateModified = '".date('Y-m-d H:i:s')."'";
		$condition      =   " id = 2 ";
		$adminLoginObj->updateCMSDetails($updateString,$condition);
		$updateString   =   " Content  = '".$_POST['cms_terms']."' ,DateModified = '".date('Y-m-d H:i:s')."'";
		$condition      =   " id = 3 ";
		$adminLoginObj->updateCMSDetails($updateString,$condition);
		
		/* $updateString   =   " Content  = '".$_POST['tournament_pdf']."' ,DateModified = '".date('Y-m-d H:i:s')."'";
		$updateString   =   " Content  = '".str_replace("&nbsp;"," ",$_POST['tournament_pdf'])."' ,DateModified = '".date('Y-m-d H:i:s')."'";
		$condition      =   " id = 5 "; */
		$adminLoginObj->updateCMSDetails($updateString,$condition);
		header('location:StaticPages?msg=1');
}
if(isset($_GET['msg']) && $_GET['msg'] != '')
	$msg = "CMS updated successfully";
commonHead(); ?>
<body>
	<?php top_header(); ?>
							 <div class="box-header"><h2><i class="fa fa-pencil-square-o"></i>CMS </h2></div>
							 <div class="clear">
							  <!-- <form name="cms_form" id="cms_form" action="" method="post" onSubmit="return saveEditor();"> -->
							  <form name="cms_form" id="cms_form" action="" method="post">
							  	 <table align="center" cellpadding="0" cellspacing="0" border="0" width="98%" class="form_page list headertable">
										<tr>
											<td align="center">
												<table cellpadding="0" cellspacing="0" border="0" width="80%" align="center">
													<tr><td class="msg_height50" valign="top" align="center" colspan="3">
													<?php if($msg !='') { ?><div class="success_msg" align="center"><span><i class="fa fa-lg"></i>&nbsp;&nbsp;<?php echo $msg;?></span></div><?php  } ?>
													</td></tr>
													<?php if(isset($static_details) && is_array($static_details) && count($static_details)>0 ) { ?>
													<tr>
														<td align="left" width="15%" valign="top">
															<label><?php if(isset($static_details[0]->PageName) && $static_details[0]->PageName != '' ) echo $static_details[0]->PageName;?>
															<span class="required_field">*</span></label>
														</td>
														<td  width="5%" align="center" class="" valign="top">:</td>
														<td width="80%" height="60" valign="top" align="left"><textarea  class="add_cms" name="cms_about" id="cms_about" rows="15" cols="106"><?php if(isset($static_details[0]->Content) && $static_details[0]->Content != '' ) echo $static_details[0]->Content;?></textarea></td>
													</tr>
													<tr><td height="20"></td></tr>	
													<tr>
														<td align="left"  valign="top">
															<label><?php if(isset($static_details[1]->PageName) && $static_details[1]->PageName != '' ) echo $static_details[1]->PageName;?>
															<span class="required_field">*</span></label>
															</td>
														<td align="center" class="" valign="top">:</td>
														<td height="60" valign="top" align="left"><textarea  class="add_cms" name="cms_privacy" id="cms_privacy" rows="15" cols="106"><?php if(isset($static_details[1]->Content) && $static_details[1]->Content != '' ) echo $static_details[1]->Content;?></textarea></td>
													</tr>
													<tr><td height="20"></td></tr>									
													<tr>
													<tr>
														<td align="left"  valign="top">
															<label><?php if(isset($static_details[2]->PageName) && $static_details[2]->PageName != '' ) echo $static_details[2]->PageName;?>
															<span class="required_field">*</span></label>
														</td>
														<td align="center" class="" valign="top">:</td>
														<td height="60" valign="top" align="left"><textarea  class="add_cms" name="cms_terms" id="cms_terms" rows="15" cols="106"><?php if(isset($static_details[2]->Content) && $static_details[2]->Content != '' ) echo $static_details[2]->Content;?></textarea></td>
													</tr>
													<tr><td height="20" colspan="2"></td></tr>
													<!-- <tr>
														<td align="left" valign="top"><label>Tournament PDF</label></td>
														<td align="center" class="" valign="top" width="3%">:</td>
														<td height="60" valign="top" align="left" >
															<textarea class="add_cms menu" id="tournament_pdf" name="tournament_pdf"><?php //if(isset($static_details[4]->Content) && $static_details[4]->Content != '' ) echo $static_details[4]->Content;?></textarea>
														</td>
													</tr>
													<tr><td height="20"></td></tr>	 -->							
													<tr>
														<td colspan="2"></td>
														<td align="left">
															<input type="submit" class="submit_button" name="cms_submit" id="cms_submit" value="Submit" title="Submit" alt="Submit" />
															<a href="UserList?cs=1" class="submit_button" name="Cancel" id="Cancel" value="Cancel" title="Cancel" alt="Cancel" tabindex="NaN">Cancel</a>
														</td>
													</tr>		
													<?php  } else { ?>
													<tr><td align="center" colspan="3">
													<div class="error_msg" align="center"><span><?php echo "No Static Content Found";?></span></div>
													</td></tr>
													<?php } ?>
													<tr><td height="35"></td></tr>
											</table>
										</td>
									</tr>					  
								</table>
							  </form>
							  </div>	
						  	
<?php commonFooter(); ?>
<script>
	/* tinymce.init({
	height 	: "250",
	width	: 765,
	mode : "specific_textareas",
	selector: ".menu", statusbar: false, importcss_append: true,
	plugins	: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
				],
	toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
	});
	function saveEditor(){
		tinyMCE.triggerSave();
	} */
</script>
</html>