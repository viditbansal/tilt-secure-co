<?php 
require_once('includes/CommonIncludes.php');
$tournamentsId	=	'';
$form	=	'';

require_once('controllers/TournamentController.php');
$tournamentObj  		=   new TournamentController();

if(isset($_GET['tourStatus']) && $_GET['tourStatus'] =='3' && isset($_GET['tournamentId']) && $_GET['tournamentId'] !=''){
	$tournamentsId	=	$_GET['tournamentId'];
	$generatedPins = $tournamentObj->getPinCode(" id, PinCode ", " Status=0 AND fkTournamentsId=".$tournamentsId); ?>
		<body>
			<div class="pinCode_popup  popup_small" id="pinCode_popup"><!--   -->
				<table align="center" cellpadding="0" cellspacing="0" border="0" class="list" width="100%">	
					<tr><td colspan="3"><h2 style="text-align:center;">Pin Code List</h2></td></tr>
					
					<tr><td colspan="3">
						<div id="pinCodeControl" <?php if(isset($generatedPins) && is_array($generatedPins) && count($generatedPins) > 10){ ?> class="popup_scroll" style="overflow: scroll;" <?php } ?>>
							<table cellpadding="5" cellspacing="0" border="0" class="pop_up_list table table-striped" id="pincodeContainer" width="100%">
								<tr style="background:none repeat scroll 0 0 #1C4478;">
									<th align="center" width="5%" style="text-align:center;">#	<i class="fa fa-sorted-icon"></i></th>
									<th align="left" colspan="3" style="padding-left:0px;font-weight:bold;">Pin Code <i class="fa fa-sorted-icon"></i></th>
								</tr>
								<?php 
									$i= $sno = 1;
									if(isset($generatedPins) && is_array($generatedPins) && count($generatedPins) > 0){ 
										foreach($generatedPins as $key=>$value) { ?>
											<tr>
												<td align="center" width="2%"><?php echo $sno; ?></td>
												<td align="left" width="20%" style="font-weight:bold;"><?php echo $value->PinCode; ?></td>
												<td width="5%"><a href='forcedownload?pin=<?php echo $value->PinCode; ?>&tourid=<?php echo $tournamentsId;?>' target="_blank" title="PDF"><i class="fa fa-file-pdf-o"></i></a></td>
												<td align="center" width="10%"><input type="checkbox" name="pins[]" class="pins" id="pin_<?php echo $sno; ?>" value="<?php echo $key; ?>" /></td>
											</tr>
											<?php 
											$i++;	$sno++;
										}
									} else { ?>
										<tr><td height="15" align="center" colspan="4" ><div class="error_msg" id="noCodeFound" >No Pin Code(s) Found</div></td></tr>
										<?php 
									} ?>
											
							</table>
						</div>
					</td>
					</tr>
					
					<?php if(isset($generatedPins) && is_array($generatedPins) && count($generatedPins) > 0){  ?>
					<tr>
						<td colspan="3" align="center">
							<div id="moreOption_gen">
							<a href="javascript:void(0);" id="downloadPdf" onclick="return downloadPdf()" title="Download">Download</a>
							</div>
						</td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</body>
	
	
	<?php
} else {

		if(isset($_GET['tournamentId'])	&&	$_GET['tournamentId']	!=''	){
			$tournamentsId	=	$_GET['tournamentId'];
			$form	=	"?tournamentId=".$tournamentsId;
		}
		if(isset($_POST['pincode_status'])	&&	$_POST['pincode_status']	!=''	){
			$_SESSION['pinCode_status']	=	$_POST['pincode_status'];
		}
		else 
			$_SESSION['pinCode_status']	=	0;
		?>
		<body>
			<div class="pinCode_popup " id="pinCode_popup">
				<table align="center" cellpadding="0" cellspacing="0" border="0" class="list" width="100%">	
					<tr><td colspan="3"><h2 style="text-align:center;">Pin Code List</h2></td></tr>
					<tr>
						<td height="2" colspan="3">
							<input type="hidden" name="codeNumber" id="codeNumber" value="0" >
							<input type="hidden" id="showIds" name="showIds" value="">
						</td>
					</tr>
					<form name="pin_code_status" id="pin_code_status" action="GeneratePin<?php echo $form; ?>" class="fancybox" method="post">
						<tr>
							<td colspan="3">
								<table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">			
									<tr>
										<td height="5">
											<input type="hidden" id="tournamentId" name="tournamentId" value="<?php echo $tournamentsId; ?>">
										</td>
									</tr>
									<tr>													
										<td width="15%" valign="top"><label>Status</label></td>
										<td width="3%" align="center"  valign="top">&nbsp;:&nbsp;</td>
										<td align="left" valign="top"  height="40" >
											<select name="pincode_status"  id="pincode_status" class="input" tabindex="2" title="Select Status" >
											<?php foreach($pinStatus as $key => $pin_status) { ?>
												<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['pinCode_status']) && $_SESSION['pinCode_status'] != '' && $_SESSION['pinCode_status'] == $key) echo 'Selected';  ?>><?php echo $pin_status; ?></option>
											<?php }?>
											</select>
										</td>
									</tr>
									<tr><td height="2"></td></tr>
								</table>
							</td>
						</tr>
					</form>
					<tr>
						<td colspan="3">
							<div id="pinCodeControl" class="">
								<table cellpadding="0" cellspacing="0" border="0" class="pop_up_list table table-striped" id="pincodeContainer" width="100%">
									<tr style="background:none repeat scroll 0 0 #1C4478;">
										<th align="center" width="5%" style="text-align:center;">#	<i class="fa fa-sorted-icon"></i></th>
										<th align="left" colspan="3" style="padding-left:0px;font-weight:bold;">Pin Code <i class="fa fa-sorted-icon"></i></th>
									</tr>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td height="15" align="center" colspan="3" >
							<div class="error_msg col-xs-11" style="display:none" id="noCodeFound" >No PIN Code(s) Found</div>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center">
							<div class="more_down_pin" id="moreOption_gen" style="display:none;">
							<a class="more_pin" href="#tournament_list" id="addPinCode" onclick="return addMorePinCode(<?php echo $tournamentsId;?>)" title="More">More</a>
							<a class="download_pin" href="javascript:void(0);" id="downloadPdf" onclick="return downloadPdf()" title="Download">Download</a>
							</div>
							<div id="moreOption_show" style="display:none" >
							<a href="#tournament_list" id="addPinCode" onclick="return showUsedPinCode(<?php echo $tournamentsId;?>)" title="More">More</a>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</body>
		<?php 
		if(isset($_SESSION['pinCode_status'])	&&	$_SESSION['pinCode_status'] !=''	&&	$_SESSION['pinCode_status'] ==1 ){ ?>
			<script type="text/javascript">
			$(document).ready(function() {	
				showUsedPinCode(<?php echo $tournamentsId;?>);
			});

			</script>
			<?php 
		} else { ?>
			<script type="text/javascript">
			$(document).ready(function() {
				addMorePinCode(<?php echo $tournamentsId;?>);
			});
			</script>
			<?php 
		} ?>
		<script type="text/javascript">
		$(function() {
			$('#pincode_status').change(function() {
				var e = document.getElementById("pincode_status");
				var strUser = e.options[e.selectedIndex].value;
				if(strUser==1){ resetListing(1); }
				else { resetListing(2); }
			});
		});
		</script>
		<?php 
	}
?>
	<script type="text/javascript">
	function downloadPdf(){
			var pins = '';
			$(".pins:checked").each(function() {
				pins += $(this).val()+',';
			});
			if(pins != ''){
				pins = pins.substring(0,pins.length-1);
				window.open('forcedownload?pin='+pins+'&id='+$("#tournamentId").val(),'_blank'); 
			}else{
				alert("Please select Pin Code");
			}
			
	} 
	</script>
	</html>