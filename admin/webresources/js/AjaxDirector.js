if ("https:" == location.protocol)
    var protocolPath  = 'https://';
else
    var protocolPath  = 'http://';

if (window.location.hostname == '172.21.4.104') {
	var  path       = protocolPath + '172.21.4.104/TiLTPortals/admin/';
	var actionPath	= protocolPath + '172.21.4.104/TiLTPortals/admin/';
}
else {
   var  path = protocolPath+''+window.location.hostname+'/admin/';
    var actionPath	= protocolPath+''+window.location.hostname+'/admin/';

}
function addRow(ref)
{
	var field_name 	= "field_name_clone_";
	var sample_data = "sample_data_clone_";
	var explanation	= "explanation_clone_";
	var count 		= 0;
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	$(ref).closest("table").find("tr").each(function() {
		var text 	=	$(this).find("input").eq(0).val();
		if(text == "")
			empty = 1;
	});
	if(empty == 0)
	{
		var length		= node.attr("clone");
		var tabindex	= $("#method").attr("tabindex");
		var clonedRow 	= node.clone(true);
		clonedRow.insertAfter(node);
		if(length >= 0) 	{
			count = (+length)+1;
			clonedRow.attr("clone",count);
			clonedRow.find("input").val("");
			clonedRow.find("select").val(0);
			//clonedRow.find("select").attr("id",count);
			clonedRow.find("textarea").text("");
			node.find("a.add_new").attr("style","display:none");
			settabindex(tabindex);
		}
	}
	else
		alert("Please fill the row to add new row");
}

function delRow(ref)
{	
	var count	=	$("#inputParam tr").length;
	if(count > 2)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool){
			var node 		= $(ref).closest("tr");
			var row = $(ref).closest('tr');
			var prev = row.prev();
			var ids	=	node.find("a.add_new").attr("style");
			if(ids	!= 'display:none'){
				prev.find("a.add_new").show();
			}
			$(ref).closest("tr").remove();
			//$(ref).closest("tr").remove();
		}
	}
	else
		alert("Atleast one row is required");
}
function addProdRow(ref)
{
	var count 		= 0;
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	var i = 0;
	$(ref).closest("table").find("tr").each(function() {
		if(i !=0){
			var text 	=	$(this).find("input.productId").eq(0).val();
			var text1 	=	$(this).find("input.product_name").eq(0).val();
			var text2 	=	$(this).find("textarea.product_desc").val();
			var text3 	=	$(this).find("input.product_price").val();
			if(text == "") 		 empty = 1;
			else if(text1 == "") empty = 1;
			else if(text2 == "") empty = 1;
			else if(text3 == "" || text3 == '0') empty = 1;
		}
		i++;
	});
	if(empty == 0)
	{
		var length		= node.attr("clone");
		var clonedRow 	= node.clone(true);
			clonedRow.find(".error").remove();
			//$(".error").remove();
		clonedRow.insertAfter(node);
		if(length >= 0) 	{
			count = (+length)+1;
			clonedRow.attr("clone",count);
			clonedRow.find("input").val("");
			clonedRow.find("input:checkbox").attr('checked', false);
			clonedRow.find("input:checkbox").attr('onclick', "setStatus(this,"+count+")");
			clonedRow.find("input.prodStatusHidden").attr('id', "hiddenStatus"+count);
			clonedRow.find("textarea").text("");
			node.find("a.add_new").attr("style","display:none");
		}
	}
	else
		alert("Please fill the row to add new row");
}

function deleteProdRow(ref)
{	
	var count	=	$("#product_list tr").length;
	if(count > 2)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool){
			var node 		= $(ref).closest("tr");
			var row = $(ref).closest('tr');
			var prev = row.prev();
			var ids	=	node.find("a.add_new").attr("style");
			if(ids	!= 'display:none'){
				prev.find("a.add_new").show();
			}
			$(ref).closest("tr").remove();
		}
	}
	else
		alert("Atleast one row is required");
}
$(document).ready(function() {
	var tabindex = $("#method").attr("tabindex");
	settabindex(+tabindex+1);
	showHideInputParam();
	$('#admin_invites').hide();
	if ($('#methodparam').is(":checked")){
		//$(".inputParamDefault").show();
		$(".inputParamMultiple").hide();   		
		$(".jsonInput").show();
	}
	//For Method Change Event
	$("#method").change(function() {
		showHideInputParam();
	});
	$("#methodparam").change(function() {
		showHideInputMethodParam();
	});
	gametype();
	$("#entryfee").hide();
	entryfee();
	$("#gametype").change(function() {
		gametype();
	});
	$("#entryfee_free").change(function() {
		entryfee();
	});
	$("#entryfee_paid").change(function() {
		entryfee();
	});
	
});
function showHideInputParam() {
	var value = $("#method").val();
	$(".jsonInput").hide();
		if(value == "GET" || value == "POST") {
			$(".inputParamDefault").hide();
			$(".inputParamMultiple").show();
			$(".inputMethodParamMultiple").show();
		}
		else {
			$(".inputParamDefault").show();
			$(".inputParamMultiple").hide();
			$(".inputMethodParamMultiple").hide();
		}
}
function showHideInputMethodParam() {

	if ($('#methodparam').is(":checked")){
		$(".inputParamMultiple").hide();   		
		$(".jsonInput").show();
	}
	else {
		$(".jsonInput").hide();
		$(".inputParamMultiple").show();

	}
}
function gametype(){
	var value = $("#gametype").val();
	if(value == 2) {
		$("#elimination").show();
	}else $("#elimination").hide();
}
function entryfee(){
	var free = $("#entryfee_free").val();
	var paid = $("#entryfee_paid").val();
	if($("#entryfee_free").attr('checked'))
		$("#entryfee").hide();
	else if($("#entryfee_paid").attr('checked'))
		$("#entryfee").show();
}
function settabindex(index) {
	$("#inputParam").find("tr").not(":eq(0)").each(function() {
		$(this).find("input").eq(0).attr("tabindex",index++);
		$(this).find("input").eq(1).attr("tabindex",index++);
		$(this).find("select").eq(0).attr("tabindex",index++);
		$(this).find("textarea").eq(0).attr("tabindex",index++);
		$("#output_param").attr("tabindex",index++);
		$("#Save,#Add").attr("tabindex",index++);
		$("#Back").attr("tabindex",index++);
		
	});
}

function ajaxAdminFileUploadProcess(process_pram)
{	
	var loadingIMG  =  '<span class="photo_load load_upimg"><img  src="'+path+'webresources/images/fetch_loader.gif" width="24" height="24" alt=""></span>';	
    $(loadingIMG).insertAfter($("#"+process_pram+"_img"));
    $("#"+process_pram+"_img").hide();	
   // alert(process_pram);
	var hiddenVal = $("#empty_"+process_pram).val();
	//console.log('level 000'+hiddenVal);
    $.ajaxFileUpload
    ({
        url:actionPath+'models/DoAjaxAdminFileUpload.php',
        secureuri:false,
        fileElementId:process_pram,
        dataType: 'json',
        data:{
			
            process:process_pram
        },
		success: function (data)
        {
			//alert('11111111');
           	if(typeof(data.error) != 'undefined')
            {
			    if(data.error != '')
                {
                    alert(data.error);
					if($('#'+process_pram+'_upload').val() == '')
						$("#empty_"+process_pram).val(hiddenVal);
					
					 $("#empty_"+process_pram).val(hiddenVal);
				//	console.log('level 11111');
                }else
                {
				//	console.log('level 2222');
					if(hiddenVal=='') {
					//console.log('level 4444444');
						$("#empty_"+process_pram).val(1);
					}
					var result	=	data.msg.split("####");
					if(process_pram == 'cover_photo'){
						var img	='<img  src="'+path+'/webresources/uploads/temp/'+result[0] +'.'+ result[1]+'?rnd='+Math.random()+'" width="200" height="75" />\n\
                                        <input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0] +'.'+ result[1]+'" />';
					}
					else if(process_pram == 'about_us' || process_pram == 'bg_image' || process_pram == 'video_image'){
						var img	='<img  src="'+path+'/webresources/uploads/temp/'+result[0] +'.'+ result[1]+'?rnd='+Math.random()+'" width="75" height="75" />\n\
                            <input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0] +'.'+ result[1]+'" /><input id="'+process_pram+'_validate" type="hidden" value="'+result[0] +'.'+ result[1]+'" name="'+process_pram+'_validate"> ';
					}
					else{
						var img	='<img  src="'+path+'/webresources/uploads/temp/'+result[0] +'.'+ result[1]+'?rnd='+Math.random()+'" width="75" height="75" />\n\
                                        <input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0] +'.'+ result[1]+'" />';
				//	console.log('level 3333'+process_pram);
					}
					if (process_pram.match(/^custom_prize([0-9]+)$/)) {
						// do something
						$("#name_"+process_pram).val(result[0] +'.'+ result[1]);
						//$("#name_"+process_pram).val(process_pram);
					}else if(process_pram.match(/^user_image([0-9]+)$/)){
						$("#temp_"+process_pram).val(result[0] +'.'+ result[1]);
						$("#flag_"+process_pram).val(1);
					}
					
					$("#"+process_pram+"_img").html(img);
	                $("#no_"+process_pram).remove();
				}
                $(".photo_load").remove();
                $("#"+process_pram+"_img").show();
            }else {
			//	console.log('level 55555'+process_pram);
			}
        },
        error: function (data, status, e)
        {
           alert(e);
        }
    });
//alert('123443654');
    return false;
}


function ajaxVideoUploadProcess(fileName,process_pram)
{	
	var loadingIMG  =  '<span class="photo_load load_upimg" id="videoloaderdis"><img  src="'+path+'webresources/images/fetch_loader.gif" width="24" height="24" alt=""></span>';	
    $(loadingIMG).insertAfter($("#"+process_pram+"_img"));
		
	$("#"+process_pram+"_img").hide();	
	
	var fileInput = document.getElementById(process_pram);	
	var image_type = (fileInput.files[0].type).split('/');
	if(image_type[0] == 'video'){
		if(fileInput.files[0].size > 10485760){
			alert('Video size should be less than 10 MB ');
			$("#videoloaderdis").remove();
		}
		else {
			$.ajaxFileUpload
			({
				url:actionPath+'models/DoAjaxAdminFileUpload.php?video=1',
				secureuri:false,
				fileElementId:process_pram,
				dataType: 'json',
				data:{
					process:process_pram
				},
				success: function (data)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							alert(data.error);
							//have to empty all parameter
						}else
						{
							var result	=	data.msg.split("####");
							
							if(process_pram == 'promo_video'){
								var video = '<input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0]+'.'+ result[1]+'" /><input id="'+process_pram+'_validate" type="hidden" value="'+result[0] +'.'+ result[1]+'" name="'+process_pram+'_validate"> ';								
							}else{
								var video = '<input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0]+'.'+ result[1]+'" />';
								
							}							
							$("#"+process_pram+"_img").html(video);	
							$("#videoloaderdis").remove();					
						}
					}
				},
				error: function (data, status, e)
				{				  
				   alert(e);
				    $("#videoloaderdis").remove();
				}
			});
		}
		
		return false;
	}
	else{		
		 alert('Upload video files only');
		  $("#videoloaderdis").remove();
	}	
    return false;
}

function uploadImageFile(fileName,process_pram)
{	
	$('#process_pram').html(fileName);
	var fileInput = document.getElementById(process_pram)	
	var image_type = (fileInput.files[0].type).split('/');
	
	if(image_type[0] != 'image'){
		alert('Upload only image files')
		$('#'+process_pram).val('');			
		$('#'+process_pram).html('No file selected');	
	}	
}

function left_pannel(id_name,page_name)
{
	if( ($('#'+id_name).attr('class') == 'nav nav-tabs nav-stacked slideArrow')||  ($('#'+id_name).attr('class') == 'nav nav-tabs nav-stacked main-menu slideArrow') ){
		$('#'+id_name).attr('style','display:block;');
		$('#'+id_name).toggleClass('slideArrow');
		$('#'+id_name+'_span').removeClass('downarrowclass');
		$('#'+id_name+'_span').addClass('upArrow');
	}
	else{
		$('#'+id_name).attr('style','display:none;');
		$('#'+id_name).toggleClass('slideArrow');
		$('#'+id_name+'_span').removeClass('upArrow');
		$('#'+id_name+'_span').addClass('downarrowclass');
	}
}

function showaction(rowid)	{
	$('#userAction_'+rowid).css("display","block");
//	alert('#userAction_'+rowid);
}
function hideaction(rowid)	{
	$('#userAction_'+rowid).css("display","none");
}
function validateinputparam()	{
	var flag	=	$('.inputParamMultiple').attr('style');
	if (flag=='display: none;') {
		return true;
	}
	else {
		var getFlag	=	$("#method").val();
		if(getFlag == 'GET'){	
			$('#field_name_empty').html('');
			$('#sample_data_empty').html('');
			return true;	
		}
		var fieldname	=	$('#field_name').val();
		var sampledata	=	$('#sample_data').val();
		if(fieldname	==''	&&	sampledata	==	''){
			$('#sample_data_empty').html('Sample Data is required');
			$('#field_name_empty').html('Field Name is required');
			return false;
		}
		else if(fieldname	==''){
			$('#field_name_empty').html('Field Name is required');
			$('#sample_data_empty').html('');
			return false;
		}
		else if(sampledata	==''){
			$('#sample_data_empty').html('Sample Data is required');
			$('#field_name_empty').html('');
			return false;
		}
		else{
			$('#field_name_empty').html('');
			$('#sample_data_empty').html('');
			return true;
		}
	}

}

function isActionSelected(){
	var flag       = 0;
	//var action_flag  = 0;
	var a	=	$( "#bulk_action option:selected" ).text();
	if (a=='Bulk Actions') {
	      //  action_flag  = 1;
			alert('Select any action');
			return false;
	}
	else {
		$("input[name='checkedrecords[]']").each(function(){
			if($(this).attr('checked')){
				flag = 1;	
			}
		});
		if(flag == 0){
			alert('Select atleast a single record');
			return false;
		}	
	}
}
function loadMessage(to_user_id,from_user_id){
	if($('#filter_user_name').length) {
		$('#filter_user_name option[value='+to_user_id+']').attr('selected',true);
	}
    $('.users').removeClass('sel');
    $('.user_'+to_user_id).addClass('sel');
    $('.loader').show();
	$.ajax({
        type: "GET",
        url: actionPath+"/models/AjaxAction.php?action=LOAD_CONVERSATION",
        data: 'FromUserId='+from_user_id+'&ToUserId='+to_user_id,
        success: function(data) {
           $('.scroll').html(data);
			$('.scroll_content').animate({
                   scrollTop: $('.scroll_content')[0].scrollHeight
               }, 800);
			$('.loader').hide();
			//$('.user_'+to_user_id).removeClass('unread');
        }
    });
}

function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
        return true;
    }
function settingValidation(evt,id){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
        
        return true;
}
function countryShow(evt){
	var stext	=	$('#country_'+evt+' option:selected').text();
	if(stext=='United States'){
		$('#state_'+evt).show();
		$('#state_label_'+evt).show();
	}
	else {
		$('#state_'+evt).hide();
		$('#state_label_'+evt).hide();
	}
}
function addCountry(ref)
{	
	 tinyMCE.triggerSave();			//commented 20140828
	var count 		= 0;
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	var n			= node.attr("clone");
	$(ref).closest("table").find("tr").each(function() {
		
		// var gftRules 	=	$(this).find("textarea.gft_rules").eq(0).val();
		var tourRules 	=	$(this).find("textarea.tour_rules").eq(0).val();
		if(tourRules == "")
			empty = 1;
		
		if($(this).find("select.country")){	
			var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
			if(cvalue != '' &&	cvalue=='United States'){
				if($(this).find("select.state")){	
					var svalue	=	$(this).find("select.state option:selected").eq(0).text();
					if(svalue!='' && svalue=='Select'){ //no State choosen
						empty = 3;
					}
				}
			}
			else if(cvalue != '' &&	cvalue=='Select'){ //no country choosen
				empty = 2;
			}
		}
		//console.log('-------'+cvalue);
	});
	
	if(empty == 0)
	{
		var length		= node.attr("clone");
		var tabindex	= $("#method").attr("tabindex");
		var clonedRow 	= node.clone(true);
		clonedRow.insertAfter(node);
		//tinyMCE.triggerSave();
		if(length >= 0) 	{
			var t_template			=	$('#tour_rules_template').val();
			var g_template			=	$('#gft_rules_template').val();
			count = (+length)+1;
			clonedRow.attr("clone",count);
			clonedRow.find("input").val("");
			clonedRow.find("select").val(0);
			clonedRow.find("select.country").attr("id",'country_'+count);
			clonedRow.find("select.country").attr("onchange",'countryShow('+count+')');
			clonedRow.find("select.state").attr("id",'state_'+count);
			clonedRow.find("select.state").attr("style",'display:none;width:130px;');
			clonedRow.find("span.slabel").attr("id",'state_label_'+count);
			clonedRow.find("span.slabel").attr("style",'display:none;');
			clonedRow.find("td.terms_td").attr("id",'terms_td_'+count);
			clonedRow.find("td.rules_td").attr("id",'rules_td_'+count);//Template
			//document.getElementById('rules_td_'+count).innerHTML = '<textarea id="rules_0" class="gft_rules rules" style="width:250px" rows="4" cols="45" tabindex="11" name="gftRules[]">'+g_template+'</textarea>';
			document.getElementById('terms_td_'+count).innerHTML = '<textarea id="terms_0" class="tour_rules terms" style="width:250px" rows="4" cols="45" tabindex="11" name="tournamentRules[]">'+t_template+'</textarea>';
			clonedRow.find("textarea.tour_rules").attr("id",'terms_'+count);
			clonedRow.find("textarea.gft_rules").attr("id",'rules_'+count);
			clonedRow.find("span.addNewRule").attr("id",'new_'+count);
			$('#new_'+length).hide();
			//testInit();
			$('state_'+count).hide();
			settabindex(tabindex);
			testInit();
		}
	}
	else if(empty==2)
		alert("Please select country");
	else if(empty==3)
		alert("Please select state");
	else if(empty==1)
		alert("Provide terms and conditions/rules");
}
//*****************************
function delCountry(ref,n)
{	
	var count	=	$("#inputParam tr").length;
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	//if(count > 2)
	if(count > 1)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool){
			
			var id	=	$(ref).closest("tr").attr("clone");
			id	=	parseInt($.trim(id));
			if(id == '0' || id !=''){
				var countryId	=	$('#country_'+id+' :selected').val();
				var countryText	=	$('#country_'+id+' :selected').text();
				if(countryText=='United States'){
					var stateId	=	$('#state_'+id+' :selected').val();
					var oldIds = '';
					oldIds = $("#stateDeletedIds").val();
					oldIds	=	$.trim(oldIds);
					if(oldIds !=''){
						oldIds	=	oldIds+','+stateId;
						var arr	=	oldIds.split(',');
						var arr1 = [];
						$.each(arr, function(i, el){
							if($.inArray(el, arr1) === -1) arr1.push(el);
						});
						var newValue = '';		
						for(var i = 0; i < arr1.length; i++) {	newValue += arr1[i]+',';	}	
						newValue = newValue.substring(0, newValue.length - 1);
						$("#stateDeletedIds").val(newValue);
					}
					else{
						$("#stateDeletedIds").val(stateId);
					}
				}else{
					var oldIds = '';
					oldIds = $("#countryDeletedIds").val();
					oldIds	=	$.trim(oldIds);
					if(oldIds !=''){
						oldIds	=	oldIds+','+countryId;
						var arr	=	oldIds.split(',');
						var arr1 = [];
						$.each(arr, function(i, el){
							if($.inArray(el, arr1) === -1) arr1.push(el);
						});
						var newValue = '';		
						for(var i = 0; i < arr1.length; i++) {			
								newValue += arr1[i]+',';							
						}	
						newValue = newValue.substring(0, newValue.length - 1);
						$("#countryDeletedIds").val(newValue);
					}
					else{
						$("#countryDeletedIds").val(countryId);
					}
				}
			}
			var row = $(ref).closest('tr');
			var prev = row.prev();
			var n			= node.attr("clone");
			var ids	=	node.find("span.addNewRule").attr("style");
			if(ids	!= 'display:none'){
				prev.find("span.addNewRule").show();
			}
			$(ref).closest("tr").remove();
		}
	}
	else
		alert("Atleast one row is required");
}

//*****************************
/* Increment decrement function Start */
function decrement(id){
	var inc	=	1;
	var coins	=	$("#"+id).val();
	if(coins !=''	&&	coins > inc){
		coins	=	coins - inc;
		$("#"+id).val(coins);
	}
}
function increment(id){
	var inc	=	1;
	var coins	=	$("#"+id).val();
	if(coins !=''){
		var attr = $("#"+id).attr('max');
		if (typeof attr !== typeof undefined && attr !== false) {
			if(parseInt(coins) < attr)
				coins	=	parseInt(coins) + inc;
			else
				coins	=	parseInt(coins);
			
		}else 
			coins	=	parseInt(coins) + inc;
		$("#"+id).val(coins);
	}
	else{
		coin = inc;
		$("#"+id).val(coin);
	}
}
/* Increment decrement function End */

function remove_country(id,restrict_page){	
	var count = 0;
	$.post(actionPath+'models/AjaxAction.php?rand='+Math.random(),
		{ action:'REMOVE_COUNTRY',countryId	: id,restriction : restrict_page},
		function(result){
			$('.rest_country'+id).remove();
			$("#countries.res_countries > div[id]").each(function(){
				count++;
			});
			if(count==0){
				$("#countries.res_countries").html(' - ');
			}
		}
	);
}
function remove_state(countryId,stateId,restrict_page){
var count=0;
	$.post(actionPath+'models/AjaxAction.php?rand='+Math.random(),
		{ action:'REMOVE_STATE',countryId	: countryId,stateId:stateId,restriction : restrict_page},
		function(result){
			if($.trim(result) == '1'){
				$('.rest_state'+stateId).remove();
				$("#states.res_countries > div[id]").each(function(){
					count++;
				});
				if(count==0){
					$("#states.res_countries").html(' - ');
					var count1 = 0;
					$('.rest_country'+236).remove();
					$("#countries.res_countries > div[id]").each(function(){count1++;});
					if(count1==0){
						$("#countries.res_countries").html(' - ');
					}
				}
			}
		}
	);
}

function retainValues(selid){
	// alert('ttt --- '+ selid );
	$.ajax({
        type: "POST",
        url: actionPath+"models/AjaxAction.php",
		data: 'selid='+selid+'&action=LOAD_TOURNAMENT_RULES_ALL',
        success: function(data) {
			$('#inputParam1All').html(data);
			$.ajax({
				type: "POST",
				url: actionPath+"models/AjaxAction.php",
				data: 'selid='+selid+'&action=LOAD_TOURNAMENT_RULES',
				success: function(data) {
					$('#inputParamCountry').html(data);
					$("#all_rules_title").show();
					$("#all_rules_template").show();
					$("#all_rules_countries").show();
					$("#submit_buttons").show();
				}
			});
        }
    });
}

//*****************************
function addTerms(ref)
{	
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	$(ref).closest("table").find("tr").each(function() {
		
		var termContent 	=	$(this).find("textarea.term_content").eq(0).val();
		if(termContent == "")
			empty = 2;
		
		var termTitle		=	$(this).find("input.term_title").eq(0).val();
		if(termTitle == "")
			empty = 1;
	});
	
	if(empty == 0)
	{
		var clonedRow 	= node.clone(true);
		clonedRow.find("input, textarea").val("");
		clonedRow.insertAfter(node);
		
		$(".addNewRule").hide();
		$(".addNewRule:last").show();
	}
	else if(empty==1)
		alert("Provide term title for all row");
	else if(empty==2)
		alert("Provide term content for all row");
		
}
//*****************************
function delTerms(ref)
{	
	var count	=	$("tr.clone").length;
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	if(count > 1)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool){
			$(ref).closest("tr").remove();
		}
		
		$(".addNewRule").hide();
		$(".addNewRule:last").show();
	}
	else
		alert("Atleast one row is required");
}


//******************* Add Rule GameManage***********************

function addRule(ref)
{
	var count 		= 0;
	var node 		= $(ref).closest("tr");
	var empty		= 0;
	$(ref).closest("table").find("tr.rule").each(function() {
		var text1 	=	$(this).find("input").eq(0).val();
		var text2 	=	$(this).find("input").eq(1).val();
		if($.trim(text1) == "" || $.trim(text2) == "")
			empty = 1;
		//if($(this).find("input").eq(1).val() == "")
			//empty = 2;
	});
	if(empty == 0)
	{
		var length		= node.attr("clone");
		//var tabindex	= $("#method").attr("tabindex");
		var clonedRow 	= node.clone(true);
		clonedRow.insertAfter(node);
		if(length >= 0) 	{
			count = (+length)+1;
			clonedRow.attr("clone",count);
			clonedRow.find("input").val("");
			$(".addrule").hide();
			$(".addrule:last").show();						
		}
	}
	else
		alert("Please fill the Elimination Rule and HighScore Rule to add new row");
}

//*************** Remove rule GameManage *********************

function delRule(ref)
{	
	//var count	=	$("tr.clone").length;
	var count	=   $(ref).closest("table").find("tr").length;
	var node 	=   $(ref).closest("tr");
	var empty	=	0;
		
	if(count > 2)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool){
			$(ref).closest("tr").remove();
			$(".addrule").hide();
			$(".addrule:last").show();
		}	
	}
	else{
		//alert("Atleast one row is required");
		node.find('input').each(function() {
			this.value = '';
		});
	}
}

//******************* Add Image GameManage ***********************

function addImage(ref)
{
	var count 		= 0;
	var node 		= $(ref).closest("tr");
	
	var empty		= 0;
	$(ref).closest("table").find("tr").each(function() {		
		//alert($(this).find("input").eq(0).val());
		var text 	 =	$(this).find("input").eq(0).val();			 
		var cloneval = $(this).attr("clone");		
		var oldfile  = $("#oldgame_temp_file"+cloneval).val();		
		if(text == "" && ((typeof oldfile === typeof undefined) || oldfile == "") )
			empty = 1;		
	});
	if(empty == 0)
	{
		var length		= node.attr("clone");		
		var clonedRow 	= node.clone(true);
		clonedRow.insertAfter(node);
		if(length >= 0) 	{			
			count = (+length)+1;
			clonedRow.attr("clone",count);			
			clonedRow.find("input").val("");			
			clonedRow.find("div[id=game_image"+length+"_img]").attr("id","game_image"+count+"_img");	
			clonedRow.find("div[id=game_image"+count+"_img]").html("");
			clonedRow.find("input").eq(0).attr("id","game_image"+count);
			clonedRow.find("input").eq(0).attr("name","game_image"+count);
			clonedRow.find("input").eq(1).attr("id","game_temp_file"+count);			
			$(".addimg").hide();
			$(".addimg:last").show();	
		}		
	}
	else
		alert("Please fill the row to add new row");
}

//******************* Add Image GameManage ***********************

function delImage(ref)
{	
	//var count	=	$("tr.clone").length;
	var count	=	$(ref).closest("table").find("tr").length;
	var node 	=   $(ref).closest("tr");
	var empty	=	0;
	if(count > 1)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool){
			$(ref).closest("tr").remove();
			$(".addimg").hide();
			$(".addimg:last").show();	
		}	
		
	}
	else
		alert("Atleast one row is required");
}


//*****************************
function addPolicy(ref)
{	
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	$(ref).closest("table").find(".clone").each(function() {		
		var policyContent 	=	$(this).find("textarea").val();			
		var policyTitle		=	$(this).find(".input").eq(0).val();			
		if(policyContent == "" || policyTitle == "")
			empty = 1;		
	});
	
	if(empty == 0)
	{
		var clonedRow 	= node.clone(true);
		clonedRow.find("input, textarea").val("");
		clonedRow.insertAfter(node);
		
		$(".addNewRule").hide();
		$(".addNewRule:last").show();
	}
	else
		alert("Please fill the row to add new row");
		
}
//*****************************
function delPolicy(ref)
{	
	var count	=	$("tr.clone").length;
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	if(count > 1)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool){
			$(ref).closest("tr").remove();
		}
		
		$(".addNewRule").hide();
		$(".addNewRule:last").show();
	}
	else
		alert("Atleast one row is required");
}
// Certificate Upload
function ajaxCertificateUploadProcess(fileName,process_pram)
{	
	var fileInput = document.getElementById(process_pram);	
	var image_type = (fileInput.files[0].type).split('/');
	var ext = fileName.split('.').pop();
	var hiddenVal = $("#empty_"+process_pram).val();
	//return false;
	if(ext == 'p12'){
		$("#empty_"+process_pram).val(1);
		$("#status_"+process_pram).html('Uploaded');
		$("#flag_"+process_pram).val(1);
		$(".certificate_name").remove();
		$("#status_"+process_pram).removeClass('cert_inactive').addClass('cert_active');
		
		return true;
	}
	else {
		$("#flag_"+process_pram).val(0);
		 alert('Upload p12 certificate files only');
	}
}
function tournament_blockdisp(refBlock,ref){
	//console.log('---tournament_blockdisp---'+refBlock);
	$(".terms_cond").hide();
	$(".game_rules").hide();
	$(".tour_rules").hide();
	$(".tour_privacy").hide();
	$("."+refBlock).show();
	$("#termsandcond_link").removeClass("active");
	$("#gamerules_link").removeClass("active");
	$("#tourrules_link").removeClass("active");
	$("#privacypolicy_link").removeClass("active");
	$(ref).addClass("active");
	
/* 	$("termsandcond_link").removeClass("active");
	$(".game_rules").removeClass("active");
	$(".tour_rules").removeClass("active");
	$(".tour_privacy").removeClass("active");
	$("."+refBlock).addClass("active"); */
}
function addCountryRule(ref)
{
	var count 		= 0;
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	var n			= node.attr("clone");
	$(ref).closest("table").find("tr").each(function() {
		if($(this).find("select.country")){	
			var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
			if(cvalue != '' &&	cvalue=='United States'){
				if($(this).find("select.state")){	
					var svalue	=	$(this).find("select.state option:selected").eq(0).text();
					if(svalue!='' && svalue=='Select'){ //no State choosen
						empty = 3;
					}
				}
			}
			else if(cvalue != '' &&	cvalue=='Select'){ //no country choosen
				empty = 2;
			}
		}
	});
	if(empty == 0)
	{
		var length		= node.attr("clone");
		//var tabindex	= $("#method").attr("tabindex");
		var clonedRow 	= node.clone(true);
		clonedRow.insertAfter(node);
		tinyMCE.triggerSave();
		if(length >= 0) 	{
			var t_template			=	$('#terms_cond_template').val();
			var g_template			=	$('#game_rules_template').val();
			var tour_tempate 		=	$('#tournament_rules_template').val();
			var privacy_tempate 	=	$('#privacy_rules_template').val();
			count = (+length)+1;
			clonedRow.attr("clone",count);
			clonedRow.find("input").val("");
			clonedRow.find("select").val(0);
			clonedRow.find("select.country").attr("id",'country_'+count);
			clonedRow.find("select.country").attr("onchange",'countryShow('+count+')');
			clonedRow.find("select.state").attr("id",'state_'+count);
			clonedRow.find("select.state").attr("style",'display:none;width:150px;');
			clonedRow.find("span.slabel").attr("id",'state_label_'+count);
			clonedRow.find("span.slabel").attr("style",'display:none;');
			/* clonedRow.find("td.terms_condition").attr("id",'terms_condition_'+count);
			clonedRow.find("td.game_rules").attr("id",'game_rules_'+count);//gameTemplate termsTemplate
			clonedRow.find("td.tournment_rules").attr("id",'tournment_rules_'+count);
			clonedRow.find("td.privacy_policy").attr("id",'privacy_policy_'+count); */
			clonedRow.find("td.terms_td").attr("id",'terms_td_'+count);
			clonedRow.find("td.rules_td").attr("id",'rules_td_'+count);//gameTemplate termsTemplate
			clonedRow.find("td.tournamentrules_td").attr("id",'tournamentrules_td_'+count);
			clonedRow.find("td.privacypolicy_td").attr("id",'privacypolicy_td_'+count);
			document.getElementById('rules_td_'+count).innerHTML = '<textarea id="terms_condition_'+count+'" class="rules textEditor" style="width:250px" rows="4" cols="45" tabindex="11" name="tournamentRules[]">'+g_template+'</textarea>';
			document.getElementById('terms_td_'+count).innerHTML = '<textarea id="game_rules_'+count+'" class="terms textEditor" style="width:250px" rows="4" cols="45" tabindex="11" name="tournamentConditions[]">'+t_template+'</textarea>';
			document.getElementById('tournamentrules_td_'+count).innerHTML = '<textarea id="tournment_rules_'+count+'" class="tournamentrules textEditor" rows="4" cols="45" tabindex="11" name="thirdtournamentRules[]">'+tour_tempate+'</textarea>';
			document.getElementById('privacypolicy_td_'+count).innerHTML = '<textarea id="privacy_policy_'+count+'" class="privacy-policy textEditor" rows="4" cols="45" tabindex="11" name="privacy_policy_arr[]">'+privacy_tempate+'</textarea>';
			/* clonedRow.find("textarea.terms").attr("id",'terms_'+count);
			clonedRow.find("textarea.rules").attr("id",'rules_'+count);
			clonedRow.find("textarea.tournment_rules_").attr("id",'tournamentrules_'+count);
			clonedRow.find("textarea.privacy_policy_").attr("id",'privacypolicy_'+count); */
			clonedRow.find("span.addNewRule").attr("id",'new_'+count);
			$('#new_'+length).hide();
			initEditor('terms_condition_'+count);
			initEditor('game_rules_'+count);
			initEditor('tournment_rules_'+count);
			initEditor('privacy_policy_'+count);
			$('state_'+count).hide();
			//settabindex(tabindex);
		}
	}
	else if(empty==2)
		alert("Please select country");
	else if(empty==3)
		alert("Please select state");
	else if(empty==1)
		alert("Provide terms and conditions/rules");
}

function delCountryRule(ref)
{	
	var count	=	$("#inputParam tr").length;
	console.log('----->'+$(ref).closest("table tr").length);
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	 console.log('-- count -->'+ count );
	if(count > 3)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool){
			
			//var id	=	$(ref).closest("tr").attr("clone");
			var row = $(ref).closest('tr');
			var prev = row.prev();
			var n			= node.attr("clone");
			var ids	=	node.find("span.addNewRule").attr("style");
			if(ids	!= 'display:none'){
				prev.find("span.addNewRule").show();
			}
			$(ref).closest("tr").remove();
		}
	}
	else
		alert("Atleast one row is required");
}

function initEditor(id_val){
	tinymce.init({
	height 	: "200",
	mode : "specific_textareas",
	selector: "textarea#"+id_val, statusbar: false, menubar:false,
	plugins	: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
				],
	toolbar: "insertfile undo redo styleselect | bold italic  alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link "
	});	
}

//************ banner hide show ************
function bannerHideShow(){
	var selVal	=	$('#banner_type option:selected').val();
	if(selVal !=''	&&	selVal !='undefined'){
		if(selVal == '1'){
			$('.banner_text_td').show();
			$('.banner_image_td').hide();
			//banner_link_label,
		}
		else if(selVal == '2'){
			$('.banner_image_td').show();
			$('.banner_text_td').hide();
		}
	}else{
		$('.banner_text_td').hide();
		$('.banner_image_td').hide();
		//$('.banner_link_label').hide();
	}
}
//************ youtube hide show ************
function youtubeHideShow(id){
	var selVal	=	$('#youtube_type option:selected').val();
	if(selVal !=''	&&	selVal !='undefined'){
		$('.youtube_image_img').show();
		if(selVal == '1'){
			$('.youtube_text_td').show();
			$('.youtube_code_td').hide();
		}
		else if(selVal == '2'){
			$('.youtube_code_td').show();
			$('.youtube_text_td').hide();
		}
	}else{
		$('.youtube_image_img').hide();
		$('.youtube_code_td').hide();
		$('.youtube_text_td').hide();
	}
}
function ajaxImageVideoUploadProcess(fileName,process_pram)
{	
	//var loadingIMG  =  '<span class="photo_load load_upimg"><i class="fa fa-spinner fa-spin fa-2x"></i></span>';	
	var loadingIMG  =  '<span class="photo_load load_upimg"><img  src="'+path+'webresources/images/fetch_loader.gif" width="24" height="24" alt=""></span>';	
    $(loadingIMG).insertAfter($("#"+process_pram+"_img"));
    $("#"+process_pram+"_img").hide();	
	var fileInput = document.getElementById(process_pram);	
	var image_type = (fileInput.files[0].type).split('/');
	if(image_type[0] == 'video'){
		if(fileInput.files[0].size > 10485760){
			alert('Video size should be less than 10 MB ');
			//if(process_param == 'banner_file') $("#submit").show();
			 $(".photo_load").remove();
             $("#"+process_pram+"_img").show();
		}
		else {
			if(process_pram == 'banner_file') $("#submit").hide();
			$.ajaxFileUpload
			({
				url:actionPath+'models/DoAjaxAdminFileUpload.php?video=1',
				secureuri:false,
				fileElementId:process_pram,
				dataType: 'json',
				data:{
					process:process_pram
				},
				success: function (data)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							alert(data.error);
							if(process_pram == 'banner_file') $("#submit").show();
							//have to empty all parameter
						}else
						{
							var result	=	data.msg.split("####");
							var video = '<input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0]+'.'+ result[1]+'" />';
							$("#"+process_pram+"_img").html(video);
							if(process_pram == 'banner_file') $("#submit").show();
						}
						$(".photo_load").remove();
						$("#"+process_pram+"_img").show();
					}
				},
				error: function (data, status, e)
				{
				   alert(e);
				   if(process_pram == 'banner_file') $("#submit").show();
				}
			});
		}
		return false;
	}
	else{
	
		var hiddenVal = $("#empty_"+process_pram).val();
		$.ajaxFileUpload
		({
			url:actionPath+'models/DoAjaxAdminFileUpload.php',
			secureuri:false,
			fileElementId:process_pram,
			dataType: 'json',
			data:{
				
				process:process_pram
			},
			success: function (data)
			{
				if(typeof(data.error) != 'undefined')
				{

					if(data.error != '')
					{
						alert(data.error);
						if($('#'+process_pram+'_upload').val() == '')
							$("#empty_"+process_pram).val(hiddenVal);
						
						 $("#empty_"+process_pram).val(hiddenVal);
						
					}else
					{
						if(hiddenVal=='') {
							$("#empty_"+process_pram).val(1);
						}
						var result	=	data.msg.split("####");
						//alert(result[0] +'.'+ result[1]);
						if(process_pram == 'cover_photo'){
							var img	='<img  src="'+path+'/webresources/uploads/temp/'+result[0] +'.'+ result[1]+'?rnd='+Math.random()+'" width="200" height="75" />\n\
											<input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0] +'.'+ result[1]+'" />';
						}
						else if(process_pram == 'coupon_file'	&& (result[1]=='plain' || result[1]=='text')){
							var img	='<input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0] +'.'+ result[1]+'" />';
						}
						else{

							var img	='<img  src="'+path+'/webresources/uploads/temp/'+result[0] +'.'+ result[1]+'?rnd='+Math.random()+'" width="75" height="75" />\n\
											<input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0] +'.'+ result[1]+'" />';
						}
						
						
						$("#"+process_pram+"_img").html(img);
						$("#no_"+process_pram).remove();
					}
					$(".photo_load").remove();
					$("#"+process_pram+"_img").show();
				}
			},
			error: function (data, status, e)
			{
			   alert(e);
			}
		});
	}

    return false;
}

//************* add custom prize row ***********
function addPrizeRow(ref)
{
//custom_prizeTable
	var count 		= 	0;
	var node 		= 	$(ref).closest("tr");
	var empty		=	0;
	var tableId		=	$(ref).closest("table").attr("id");
	var focusField	=	'';
	$(ref).closest("table").find("tr").each(function() {
		var n = $(this).attr('clone');
		//console.log('---->'+n);
		if(n!=0){ // to skip titles row
			if(focusField	== ''){
				if(empty==0){ //check title
					var title	=	$("#prize_title"+n).val();
					if(!title || title ==''){
						focusField	=	'#prize_title'+n;
						empty =	1;
					}
				}
				if(empty==0){ //check image
					prizeImage	=	$("#custom_prize"+n+"_upload").val();
					if(!prizeImage || prizeImage ==''){
						focusField	=	'#custom_prize'+n;
						empty =	2;
					}
				}
				if(empty==0){ //check description
					prizeDesc	=	$("#custom_prizeDes"+n).val();
					if(!prizeDesc || prizeDesc ==''){
						focusField	=	'#custom_prizeDes'+n;
						empty =	3;
					}
				}
			}
		}
	});
	if(empty == 0)
	{
		var length		= node.attr("clone");
		var clonedRow 	= node.clone(true);
		
		//Custom_prize num rows validate
		var customprizelength =  0;		
		if($("#tournament_type").val() == 4){
			if($("#maxplayer").val() < ($("#custom_prizeTable tr").length))
				customprizelength = 1;
		}
		if(customprizelength == 0){
			clonedRow.insertAfter(node);
			if(length >= 0) {
				count = (+length)+1;
				clonedRow.attr("clone",count);
				clonedRow.find("input").val("");
				clonedRow.find("td.upload_td").html("");
				var uploadtd	=	'<div id="custom_prize'+count+'_img" ></div>'+
										'<input type="file" class="upload w90" id="custom_prize'+count+'" name="custom_prize'+count+'" onchange="return ajaxAdminFileUploadProcess('+"'custom_prize"+count+"');"+'">'+
									'<input type="hidden" value="" id="name_custom_prize'+count+'" name="tempCustomPrize[]" class="prize_file_name w90">'+
									'<input type="hidden" name="oldCustomPrize[]" value="">';
				clonedRow.find("td.upload_td").html(uploadtd);
				clonedRow.find("td input.prize_title").attr("id","prize_title"+count);
				clonedRow.find("td textarea.custom_description").attr("id","custom_prizeDes"+count);
				clonedRow.find("textarea").text("");
				node.find("a.add_new").attr("style","display:none");
			}
	   }else
	 	 alert("Custom Prize cannot exceed Max. Players ");		
	}
	else{
		$(focusField).focus();
		alert("Please fill the row to add new row");
	}
}

function deletePrizeRow(ref)
{	
	var tableId	=	$(ref).closest("table").attr("id");
	var count	=	$("#"+tableId+" tr").length;
	if(count > 2)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool){
			
			var node 		= $(ref).closest("tr");
			var row = $(ref).closest('tr');
			var prev = row.prev();
			var ids	=	node.find("a.add_new").attr("style");
			//console.log('------>'+ids);
			if(ids	!= 'display:none'){
				prev.find("a.add_new").show();
			}
			$(ref).closest("tr").remove();
		}
	}
	else
		alert("Atleast one row is required");
}
function locationShow(evt,type){
	var stext	=	$('#country_loc_'+evt+' option:selected').text();
	if(type == 1) {		
		if(stext=='United States'){	
			$('#state_loc_'+evt).show();
			/* if(evt == '1'){
				//$('#state_loc_'+evt).attr("required");
			} */
			$('#locationsearch_'+evt).hide();
			$('#locationsearch_'+evt).val('');
			$('#col_loc_'+evt+'_3').show();
		}
		else {
			$('#locationsearch_'+evt).hide();
			$('#locationsearch_'+evt).val('');
			$('#state_loc_'+evt).hide();
			$('#state_loc_'+evt).val('');
			$('#col_loc_'+evt+'_3').show();			
		}
		st_loc_show	=	0;
		$('#RestrictedLocationContent').closest("table").find("tr.clone").each(function() {
			var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
			if(cvalue == 'United States')
				st_loc_show	=	1;
		});
		if(st_loc_show == 1)
			$('#state_location').show();
		else {
			$('#state_location').hide();
			$('#location_state').hide();
		}
	} else {
		var stattext	=	$('#state_loc_'+evt+' option:selected').text();
		var search		=	stattext+','+stext;	
		$.ajax({
				type: "GET",
				url: actionPath+"models/AjaxAction.php?countryname="+search+"&action=COUNTRY",
				data: '',
				async: false,
				success: function (json_data){
						//alert(json_data);
						if(json_data != '') {
						resultdata	=	json_data.split('###');
							if(resultdata[0] != 0)
								$('#latitude_'+evt).val(resultdata[0].trim());
							if(resultdata[1] != 0)
								$('#longitude_'+evt).val(resultdata[1].trim());
								$("#temp_hidden_flag").val(0);
						}
				}			
			});
		$('#locationsearch_'+evt).show();
		$('#locationsearch_'+evt).val('');
		st_loc_show	=	0;
		$('#RestrictedLocationContent').closest("table").find("tr.clone").each(function() {
			var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
			if(cvalue == 'United States')
				st_loc_show	=	1;
		});
		if(st_loc_show == 1) {
			$('#location_state').show();
			$('#col_loc_'+evt+'_4').show();			
		}
		else{
			$('#location_state').hide();
			$('#col_loc_'+evt+'_4').hide();			
		}
	}
}
function manageLocation(ref,type) {
	if(type == 1) { // add row

		var count 		= 	0;
		var node 		= 	$('#location_clone').closest("tr");
		var empty		=	0;
		curtotLoc	=	parseInt($('#totLoc').val());

		$(ref).closest("table").find("tr.clone").each(function() {
			
			var text = cvalue = 0;
			var text 	=	$(this).find("input").eq(2).val();
			var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
			
			if(text == "" && cvalue == 'United States')
				empty = 1;
			//alert(text + ' --- ' + cvalue + ' --- ' +empty);
			if($(this).find("select.country")){	
				var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
				if(cvalue != '' &&	cvalue=='United States'){
					if($(this).find("select.state")){	
						var svalue	=	$(this).find("select.state option:selected").eq(0).text();
						if(svalue!='' && svalue=='Select'){ //no State choosen
							empty = 3;
						}
					}
				}
				else if(cvalue != '' &&	cvalue=='Select'){ //no country choosen
					empty = 2;
				}
			}
		});
		if(empty == 0)
		{
			var length		= node.attr("clone");
			var tabindex	= $("#method").attr("tabindex");
			var clonedRow 	= node.clone(true);
			//clonedRow.insertAfter('#location_'+curtotLoc);
			clonedRow.find("input.locationsearch").each(function() {
				//alert('test');
				$(this).removeClass('ui-autocomplete-input valid');
				$(this).removeAttr('style');
				$(this).removeAttr('autocomplete');
				
			});
			clonedRow.appendTo('#RestrictedLocationContent');
			if(length >= 0) 	{
				count	=	curtotLoc + 1;
				clonedRow.attr("clone",count);
				clonedRow.attr("style",'');
				clonedRow.attr("id",'location_'+count);
				clonedRow.find("a.locplus").attr("id",'plus_'+count);
				clonedRow.find("a.locminus").attr("id",'minus_'+count);
				clonedRow.find("a.locminus").attr("style",'');
				clonedRow.find("select").val(0);
				clonedRow.find("select.country").attr("id",'country_loc_'+count);
				clonedRow.find("select.country").attr("name",'countryLocation[]');
				clonedRow.find("select.country").attr("onchange",'locationShow('+count+',\'1\')');
				clonedRow.find("select.state").attr("id",'state_loc_'+count);
				clonedRow.find("select.state").attr("name",'stateLocation[]');
				clonedRow.find("select.state").attr("onchange",'locationShow('+count+',\'2\')');
				clonedRow.find("select.state").attr("style",'display:none;');
				clonedRow.find("div.state").attr("id",'col_loc_'+count+'_3');
				clonedRow.find("div.location").attr("id",'col_loc_'+count+'_4');
				clonedRow.find("input.locationsearch").attr("id",'locationsearch_'+count);
				clonedRow.find("input.locationsearch").attr("name",'locationsearch[]');
				clonedRow.find("input.locationsearch").attr("onkeypress",'autoCompleteLocation('+count+')');
				clonedRow.find("input.locationsearch").val("");
				clonedRow.find("input.latitude").attr("id",'latitude_'+count);
				clonedRow.find("input.latitude").attr("name",'latitude[]');
				clonedRow.find("input.latitude").val('');
				clonedRow.find("input.longitude").attr("id",'longitude_'+count);
				clonedRow.find("input.longitude").attr("name",'longitude[]');
				clonedRow.find("input.longitude").val('');
				$('#col_loc_'+count+'_3').hide();
				$('#col_loc_'+count+'_4').hide();
				$('#plus_'+curtotLoc).hide();
				$('#plus_'+length).hide();
				$('#minus_'+curtotLoc).show();
				$('#totLoc').val(count)
			}			
			
		}
		else if(empty==2)
			alert("Please select country");
		else if(empty==3)
			alert("Please select state");
		else if(empty==1)
			alert("Please enter location");
	} else {
		var rinc	=	0;
		$(ref).closest("table").find("tr.clone").each(function() {			
			rinc	=	rinc + 1;
		});
		if(rinc == 2) {
			alert('Atleast one row is required');
			return false;
		}
		
		//remove row
		var node 	= $(ref).closest("tr");
		var n		= node.attr("clone");
		totLoc		=	parseInt($('#totLoc').val());
		$('#location_'+n).remove();
		for(i=totLoc;i>=1;i--) {
			if ($('#location_'+i).length) {
				$('#plus_'+i).show();
				$('#minus_'+i).show();
				break;
			}
		}
		
		st_loc_show	=	0;
		$('#location_clone').closest("table").find("tr.clone").each(function() {
			var cvalue	=	$(this).find("select.country option:selected").eq(0).text();
			if(cvalue == 'United States')
				st_loc_show	=	1;
		});
		if(st_loc_show == 1) {
			$('#location_state').show();
			$('#state_location').show();
		}
		else{
			$('#location_state').hide();
			$('#state_location').hide();
		}
	}
}



function autoCompleteLocation(txtid) {
//console.log('---'+txtid+'---');
	$( "#locationsearch_"+txtid ).autocomplete({
	  minLength: 2,
	  source: function( request, response ) {
//console.log('---00---');
		var cache = {};
		var tempFlag	=	$("#temp_hidden_flag").val();
		if(tempFlag && tempFlag == 0){
			var currentlat	=	$('#latitude_'+txtid).val();
			var currentlong	=	$('#longitude_'+txtid).val();
			 $( "#temp_hidden_lat").val(currentlat);
			$( "#temp_hidden_long").val(currentlong);
		}else{
			var currentlat	=	$('#temp_hidden_lat').val();
			var currentlong	=	$('#temp_hidden_long').val();
		}
		var location	=	$('#locationsearch_'+txtid).val();
		if(($.trim(currentlat) == '' || $.trim(currentlong) == '') && $.trim(location) == '')	{
			alert('latitude & longitude not valid');
			return false;
		}
		var term = request.term;
		$( "#locationsearch_id").val(txtid);
		var searchtext	=	$( "#locationsearch_"+txtid ).val();
		var countrytext	=	$('#country_loc_'+txtid+' option:selected').text();
		if(searchtext.length > 2)	{
		// console.log('---11---');
			$("#temp_hidden_flag").val(1);
			if($('#state_loc_'+txtid).length){
				var stattext	=	$('#state_loc_'+txtid+' option:selected').text();
				if(stattext && stattext !='' && stattext != 'Select')
					searchtext = searchtext+','+stattext;
			}
			searchtext = searchtext+','+countrytext;
		// console.log('---22---'+searchtext);
			$("#loading").show();
			$.ajax({
				type: "GET",
				url: actionPath+"models/AjaxAction.php?countryname="+searchtext+"&action=COUNTRY",
				data: '',
				async: false,
				success: function (json_data){
		// console.log('---33---');
						if(json_data != '') {
						resultdata	=	json_data.split('###');
		// console.log('---444---');
							if(resultdata[0] != 0)
								$( "#temp_hidden_lat").val(resultdata[0].trim());
							if(resultdata[1] != 0)
								$( "#temp_hidden_long").val(resultdata[1].trim());
						}
						var currentlat	=	$( "#temp_hidden_lat").val();
						var currentlong	=	$( "#temp_hidden_long").val();
						var location	=	$('#locationsearch_'+txtid).val();
						$.getJSON( actionPath+'models/AjaxAction.php?curlat='+currentlat+'&curlong='+currentlong+'&location='+location+'&action=SEARCH_LOCATION&rand='+Math.random(), request, function( data, status, xhr ) {
		// console.log('---55---');
							response( data );
							$("#loading").hide();
						});
				}			
			});
		}
	},
	  select: function( event,ui )
		{
			var choosed = ui.item.id;
			var choosed_location = ui.item.label;
			$('#locationsearch_'+txtid).val(choosed_location);
			var position =  choosed.split(",");
			$('#selected_lat').val(position[0]);
			$('#selected_lng').val(position[1]);
			$('#latitude_'+txtid).val(position[0]);
			$('#longitude_'+txtid).val(position[1]);
			$("#locationsearch_id").val('');
			//$('#foursquare_selected').val('1');
			
			/* $.ajax({
					type: "GET",
					url: actionPath+"models/AjaxAction.php?countryname="+choosed_location+"&action=COUNTRY",
					data: '',
					success: function (json_data){
							//alert(json_data);
							if(json_data != '') {
							resultdata	=	json_data.split('###');
								if(resultdata[0] != 0)
									$('#latitude_'+txtid).val(resultdata[0].trim());
								if(resultdata[1] != 0)
									$('#longitude_'+txtid).val(resultdata[1].trim());
							}
					}			
				}); */
		}
	});
}
function dateField(evt)
{
	var charCode = (evt.which) ? evt.which : evt.keyCode
	 if( charCode == 8 || charCode == 46 ){
		return true;
	}
	return false;
}

function ajaxSdkUploadProcess(fileName,process_pram)
{	
	var fileInput = document.getElementById(process_pram);	
	var image_type = (fileInput.files[0].type).split('/');
	var ext = fileName.split('.').pop();
	$('#'+process_pram+'_img').html('');
	if(ext == 'zip'){
		if(fileInput.files[0].size > 157286400){
			alert('SDK size should be less than or equal to 150 MB ');
			$("#status_"+process_pram).val(0);
		}else{
			$("#status_"+process_pram).val(1);
			return true;
		}
	}
	else {
		$("#status_"+process_pram).val(0);
		 alert('Please upload ZIP SDK file only');
	}
}
function ajaxDocUploadProcess(fileName,process_pram)
{	
	var fileInput = document.getElementById(process_pram);	
	var image_type = (fileInput.files[0].type).split('/');
	var ext = fileName.split('.').pop();
	$('#'+process_pram+'_img').html('');
	if(ext == 'doc' || ext == 'docx'){
		if(fileInput.files[0].size > 5242880){
			alert('Document size should be less than or equal to 5 MB ');
			$("#status_"+process_pram).val(0);
		}else{
			$("#status_"+process_pram).val(1);
			return true;
		}
	}
	else {
		$("#status_"+process_pram).val(0);
		 alert('Please upload DOC, DOCX document file only');
	}
}
function isAlphaNumericDot(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode
	var inp = String.fromCharCode(charCode);
	if(/[a-zA-Z0-9-. ]/.test(inp))
        return true;
    return false;
}
function timeField(evt)
{
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	//if (charCode == 58 || charCode == 37 || charCode == 39) return true;
	if (charCode == 58) return true;
	if (charCode > 31 && (charCode < 48 || charCode > 57))
	return false;
	return true;
}
function showLoader()
{
    if($.browser.msie){var loaderBlock = $('<div id="loading" style="display:none;"></div> ');}
    else{var loaderBlock = $('<div id="loading" style="display:none;"></div> ');}
    loaderBlock.append('<i class="fa fa-spinner fa-spin fa-4x"></i>');
    $('body').append(loaderBlock);
    $('#loading').show();
}
function removeLoader(){$('#loading').remove();}

//******************* Add Image Default user image ***********************

function addUserImage(ref)
{
	var count 		= 0;
	var node 		= $(ref).closest("tr");
	
	var empty		= 0;
	$(ref).closest("table").find("tr").each(function() {
		var text 	 =	$(this).find("input").eq(0).val();			 
		var cloneval = $(this).attr("clone");		
		var oldfile  = $("#olduser_temp_file"+cloneval).val();		
		if(text == "" && ((typeof oldfile === typeof undefined) || oldfile == "") )
			empty = 1;		
	});
	if(empty == 0)
	{
		var length		= node.attr("clone");		
		var clonedRow 	= node.clone(true);
		clonedRow.insertAfter(node);
		if(length >= 0) 	{			
			count = (+length)+1;
			clonedRow.attr("clone",count);			
			clonedRow.find("input").val("");			
			clonedRow.find("div[id=user_image"+length+"_img]").attr("id","user_image"+count+"_img");	
			clonedRow.find("div[id=user_image"+count+"_img]").html("");
			clonedRow.find("input").eq(0).attr("id","user_image"+count);
			clonedRow.find("input").eq(0).attr("name","user_image"+count);
			clonedRow.find("input").eq(1).attr("id","temp_user_image"+count);			
			clonedRow.find("input").eq(4).attr("id","flag_user_image"+count);			
			clonedRow.find("input").eq(4).val("");			
			$(".addimg").hide();
			$(".addimg:last").show();	
		}		
	}
	else
		alert("Please upload a image");
}

//******************* Delete Image Default user image ***********************

function delUserImage(ref)
{
	var count	=	$(ref).closest("table").find("tr").length;
	var node 	=   $(ref).closest("tr");
	var empty	=	0;
	if(count > 10)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool){
			$(ref).closest("tr").remove();
			$(".addimg").hide();
			$(".addimg:last").show();	
		}
	}
	else
		alert("Atleast 10 image is required");
}
