$(document).ready(function(){

 $.validator.addMethod("nameRegexp", function(value, element) {
		return this.optional(element) || !(/:|\?|\\|\*|\"|<|>|\||%/g.test(value));
    });
//--------------Login ----------------Start
$("#admin_login_form").validate({
		rules:{
			user_name		:	{ required:true},
			password		:	{ required:true}
		},
		messages:{
			user_name		:	{ required:'User Name is required' },
			password		:	{ required:'Password is required'}
		}

	});
//--------------Login----------------End

//--------------Forget Password---------------start
$("#forget_password_form").validate({
		rules:{
			email       :	{ required:true,email:true }
			// email          :	{ required:true,email:true },
          },
		messages:{
			email       :	{ required:'Email is required',email:'Please enter a valid Email address.'}
			//email      :	{ required:'Email Address is required',email:'Please enter a valid email address.'},
		}
	});
//--------------Forget Password----------------End
//--------------Change Password---------------start
$("#change_password_form").validate({
		rules:{			
			old_password        :	{ required:true},
            new_password     	:   { required:true,minlength:6,maxlength:20},
            confirm_password    :	{ required:true,minlength:6, equalTo:'#new_password'},
			/*
			new_password		:	{ required:{  
										depends: function(){
										if($("#sec_old_password").val() !='') {	return true;	}
										else false;
										}
										}},
			confirm_password		:	{ required:{  
										depends: function(){
										if($("#sec_old_password").val() !='') {	return true;	}
										else false;
										}
										}},
			*/
		},
		messages:{
			old_password		:	{ required:'Old Password is required' },
			new_password		:	{ required:'New Password is required',minlength:'New Password should have atleast 6 characters',maxlength:'New Password can have maximum 20 characters'},
			confirm_password    :   { required:'Confirm Password is required',minlength:'Confirm Password should have atleast 6 characters',equalTo:'Password mismatch' }
		}
	});
//--------------Change Password----------------End
//--------------General Settings---------------start
$("#general_settings_form").validate({
		rules:{
			email       			:	{ required:true,email:true },
			new_sec_password		:	{ required:{  
											depends: function(){
											if($("#old_sec_password").val() !='') {	return true;	}
											else false;
											}
											},minlength:6,maxlength:20},
			confirm_sec_password	:	{ required:{  
											depends: function(){
											if($("#old_sec_password").val() !='') {	return true;	}
											else false;
											}
											},minlength:6,equalTo:'#new_sec_password'},
			Distance     			:   { required:true},
			PlayableDistance    	:   { required:true, playNotZero:true},
			Commission     			:   {required:true,comNotZero:true },
			ConversionValue			:   {required:true,convNotZero:true },
			tour_endtime			:   {required:true,min:1,validateEndTime:true},
			default_tilt    		:   { required:true, tiltNotZero:true},
			default_virtual_coins   :   { required:true, virtualCoinNotZero:true},
			canstart     			:   { required:true,validateTime:true,validTime:true},
			// playtime     			:   { required:true,validateTime:true,compareTime:''},
			// nextroundstarttime		:   { required:true,validateTime:true}, 
			
		},
		messages:{
			email       			:	{ required:'Email address is required',email:'Please enter a valid Email address.'},
			new_sec_password     	:   { required:'New Secondary Password is required',minlength:'Confirm Password should have atleast 6 characters',maxlength:'Password can have maximum 20 characters'},
			confirm_sec_password    :   { required:'Confirm Secondary Password is required',minlength:'Confirm Password should have atleast 6 characters',equalTo:'Password mismatch'},
			Distance     			:   { required:'Distance is required'},
			PlayableDistance		:   { required:'Playable Distance is required', playNotZero:'Playable Distance value should be greater than zero'},
			Commission     			:   { required:'Commission is required',comNotZero:'Commission should be greater than zero'},
			ConversionValue			:   { required:'Conversion value is required',convNotZero:'Conversion value should be greater than zero'},
			tour_endtime	        :   { required:'Default End Time is required',min:"Default End Time should be minimum 1hr",validateEndTime:'Default End Time should be less than or equal to 24'},
			default_tilt			:   { required:'Default TiLT$ is required',tiltNotZero:'Default TiLT$ should be greater than zero'},
			default_virtual_coins	:   { required:'Default Virtual Coins is required',virtualCoinNotZero:'Default Virtual Coins should be greater than zero'},
			canstart     			:   { required:'Can Start is required',validateTime:'Please provide the correct time format',validTime:'Please provide the Valid Time'},
			// playtime     			:   { required:'Play Time is required',validateTime:'Please provide the correct time format',compareTime:'Play Time must be greater than Can Start'},
			// nextroundstarttime		:   { required:'Next Round Start Time is required',validateTime:'Please provide the correct time format'},
		}
	});
	$.validator.addMethod("compareTime", function(value, element) {
		var temp1 = $("#canstart").val().replace(/:/g, '')
		var temp2 = $("#playtime").val().replace(/:/g, '')
		if(temp1 >= temp2)
			return false;
		else
			return true;
    });
	$.validator.addMethod("validateTime", function(value, element) {
		var timeFormat      =   /^([0-9][0-9]):([0-5][0-9]):([0-5][0-9])$/;
		if ( ( timeFormat.test(value) ) !== true) 
			return false;
		else
			return true;
    });
	$.validator.addMethod("comNotZero", function(){
		if(parseInt($("#Commission").val()) > 0) { 
				return true;	
			}
			else {
			return false;
			}
	});
	$.validator.addMethod("convNotZero", function(){
		if(parseInt($("#ConversionValue").val()) > 0) { 
				return true;	
			}
			else {
			return false;
			}
	});
	$.validator.addMethod("validateEndTime", function(){
		 if($("#tour_endtime").val() != ''){
		 if(parseInt($("#tour_endtime").val()) <= 24) { 
				return true;	
			}
			else {
			return false;
			}
		}
		else 
		 return true;
	});
	$.validator.addMethod("playNotZero", function(){
		if(parseInt($("#PlayableDistance").val()) > 0) { 
				return true;	
			}
			else {
			return false;
			}
	});
	$.validator.addMethod("tiltNotZero", function(){
		if(parseInt($("#default_tilt").val()) > 0) { 
				return true;	
			}
			else {
			return false;
			}
	});
	$.validator.addMethod("virtualCoinNotZero", function(){
		if(parseInt($("#default_virtual_coins").val()) > 0) { 
				return true;	
			}
			else {
			return false;
			}
	});
//--------------Change Password----------------End
//--------------CMS---------------start
$("#cms_form").validate({
		rules:{			
			cms_about       :	{ required:true},		
			cms_privacy     :	{ required:true},		
			cms_terms       :	{ required:true}			
		},
		messages:{
			cms_about       :	{ required:'About is required'},
			cms_privacy     :	{ required:'Privacy Policy is required'},
			cms_terms       :	{ required:'Terms of Service is required'}
		}
	});
//--------------CMS----------------End

//--------------Add User---------------start
$("#add_user_form").validate({
		rules:{
		firstname               :	{ required:true},
		lastname           		:	{ required:true },
		email        	   		:	{ required:true,email:true},
		empty_user_photo        :	{ required:true}
            /*fb_id     		   :    { required:{  
											depends: function(element){
												if(($("#fb_id").val() == '') && ($("#twitter_id").val() == '')) return true;
												else false;
   												}
										}},*/
		},
		messages:{
		firstname       	    :	{ required:'First Name is required'},
		lastname          		:	{ required:'Last Name is required'},
		email					:	{ required:'Email is required' },
		empty_user_photo        :	{ required:'Photo is required'},
			/*fb_id				:	{ required:'Facebook Id is required' }
			*/
		}
	});
//--------------Add user----------------End

//--------------Add tournament---------------start
$("#add_tournament_form").validate({
		rules:{			
			tournament        	:	{ required:true, nameValidate:true},
			brand        		:	{ required:true},
            game     			:   { required:true},
            maxplayer    		:	{ required:true,eliminationValidate:true},
			turns    			:	{ required:true},
			entryfee			:	{ required:{  
										depends: function(element){
										if($("#entryfee_paid").attr('checked')) {	return true;	}
										else false;
										}
										}},
			startdate   		:   { required:true },
			enddate    			:   { required:true},
			
			prize    			:   { required:true },
			gametype    		:   { required:true },
			elimination			:	{ required:{  
										depends: function(element){
										if(($("#gametype").val() == 2) ) {	return true;	}
										else false;
										}
										}},
			loc_restrict			:	{ required:{  
										depends: function(element){
												
												var chk = $("input[name=location_restriction]:checked").val();	
												$("#country").focus();
												if(chk == 1){
													var l1 = $("#latitude").val();
													var l2  = $("#longitude").val();
													if(l1 =='' && l2 ==''){
														$("#country").focus();
														return true;
													}else	return false;
												}else	return false;
											}
										}
									},
			coupon_title		:	{ required:{  
										depends: function(element){
												var cLimit = $("#coupon_limit").val();
												var cText = $("#coupon_text").val();
												var cSDate = $("#coupon_startdate").val();
												var cEDate = $("#coupon_enddate").val();
												if((cLimit && cLimit !='') ||(cText && cText !='') ||(cSDate && cSDate !='') ||(cEDate && cEDate !='')) {	return true;	}
												else return false;
											}
										}
									},
			coupon_limit		:	{ required:{  
										depends: function(element){
												var title = $("#coupon_title").val();
												if(title && title !='') {	return true;	}
												else return false;
											}
										},min:1,lessThan:"#maxplayer"
									},
			coupon_text		:	{ required:{  
										depends: function(element){
												var title = $("#coupon_title").val();
												if(title && title !='') {	return true;	}
												else return false;
											}
										}
									},
			coupon_file		:	{ required:{  
										depends: function(element){
												var title = $("#coupon_title").val();
												if(title && title !='') {	
													var imageExist = $("#empty_coupon_file").val();
													if(imageExist && imageExist !='') {	
														return false;	
													}else return true;	
												}
												else return false;
											}
										}
									},
			coupon_startdate		:	{ required:{  
										depends: function(element){
												var title = $("#coupon_title").val();
												if(title && title !='') {	return true;	}
												else return false;
											}
										}
									},
			coupon_enddate		:	{ required:{  
										depends: function(element){
												var title = $("#coupon_title").val();
												if(title && title !='') {	return true;	}
												else return false;
											}
										}
									},
			banner_type			:	{ required:{  
										depends: function(element){
												var bFile = $("#empty_banner_file").val();
												var bText = $("#banner_text").val();
												var bLink = $("#banner_link").val();
												if((bText && bText !='') ||(bFile && bFile !='') ||(bLink && bLink !='') ) {	
												return true;	
												}
												else {
												return false;
												}
											}
										}
									},
			banner_text			:	{ required:{  depends: function(element){ var bType = $("#banner_type").val();
											if(bType && bType == '1') return true; else return false;
											}
										}
									},
			empty_banner_file	:	{ required:{  depends: function(element){var bType = $("#banner_type").val();
											if(bType && bType == '2'){
												if(element.value == 1)
													 return false;
												else return true;
											}
											else return false;
											}
										}
									},
			banner_link			:	{ required:{  
										depends: function(element){
										var bType = $("#banner_type").val();
											if(bType && bType == '2'){
												return true;
											}
											else return false;
											}
										}
									},
			
			youtube_type			:	{ required:{  
										depends: function(element){
												var ytFile = $("#empty_link_file").val();
												var ytText = $("#youtube_text").val();
												var ytLink = $("#youtube_code0").val();
												if((ytText && ytText !='') ||(ytFile && ytFile !='') ||(ytLink && ytLink !='') ) {	
												return true;	
												}
												else {
												return false;
												}
											}
										}
									},
			youtube_text			:	{ required:{  depends: function(element){ var ytType = $("#youtube_type").val();
											if(ytType && ytType == '1') return true; else return false;
											}
										},url:true
									},
			empty_link_file	:	{ required:{  depends: function(element){var ytType = $("#youtube_type").val();
											if(ytType && (ytType == '1' || ytType == '2')){
												if(element.value == 1)
													 return false;
												else return true;
											}
											else return false;
											}
										}
									},
			youtube_code			:	{ required:{  
										depends: function(element){
										var ytType = $("#youtube_type").val();
											if(ytType && ytType == '2'){
												return true;
											}
											else return false;
											}
										}
									},
			// play_time			:	{ required:true, timeformatplay:true, delayvalidate:true},
			can_we_start		:	{ required:true, timeformatcan:true,validTime:true},
			// next_start_time		:	{ required:true, timeformatnext:true, mintime:true},
			'prizeTitle[]'		: 	{required: true},
			'tempCustomPrize[]'	: 	{required: {
											depends: function(element){
												var elemId = element.id;
												elemId = elemId.replace("name_", "");
												var imgId = elemId+'_upload';
												if(imgId.length){
												   var status = $("#"+imgId).val();
												   if(status && status !=''){ return false; }
												   else { return true;}
												}
												else return true;
											}
										}
									},
			'prizeDesc[]'		: 	{required: true},
		},
		messages:{
			tournament			:	{ required:'Tournament is required',nameValidate:"Tournament already exist" },
			brand				:	{ required:'Brand is required' },
			game				:	{ required:'Game is required'},
			maxplayer    		:   { required:'Max player is required',eliminationValidate:"Max number of players as power of Min number of players" },
			turns    			:   { required:'No. of Turns is required' },
			entryfee			:	{ required:'Entry Fee is required'},
			startdate   		:   { required:'Start Date is required' },
			enddate    			:   { required:'End Date is required' },
			prize    			:   { required:'Prize is required' },
			gametype    		:   { required:'Game Type is required' },
			elimination			:	{ required:'Elimination is required' },
			loc_restrict		: 	{ required: "Set Location to Restrict"},
			coupon_title		:	{ required:'Coupon Title is required' },
			coupon_limit		:	{ required:'Limit is required',min:'Limit should be grater than zero',lessThan:'Limit should be less than or equal to Maximum Player' },
			coupon_file			:	{ required:'Image is required' },
			coupon_text			:	{ required:'Description is required' },
			coupon_startdate	:	{ required:'Start Date is required' },
			coupon_enddate		:	{ required:'End Date is required' },
			banner_type			:	{ required:'Banner type is required' },
			banner_text			:	{ required:'Text is required' },
			empty_banner_file	:	{ required:'Image/Video is required' },
			banner_link			:	{ required:'Text/Link is required' },
			youtube_type		:	{ required:'Youttube Link type is required' },
			youtube_text		:	{ required:'URL is required',url:"Please enter valid URL" },
			empty_link_file		:	{ required:'Image is required' },
			youtube_code		:	{ required:'Embedded Code is required' },
			// play_time			:	{ required:'Play Time is required', timeformatplay:'Please provide the correct time format', delayvalidate:'Play Time must be greater than Can Start'},
			can_we_start		:	{ required:'Can Start is required', timeformatcan:'Please provide the correct time format', validTime:'Please provide the Valid Time'},
			// next_start_time		:	{ required:'Next Round Start Time is required', timeformatnext:'Please provide the correct time format', mintime: 'Next Round Start Time must be greater than or equal to 15 minutes'},
			'prizeTitle[]'		: 	{ required: "Prize Title is required"},
			'tempCustomPrize[]'	: 	{ required: "Prize Image is required"},
			'prizeDesc[]'		: 	{ required: "Prize Description is required"},
		}
	});
	//-------------- Begin : lessThan ----------------
$.validator.addMethod("lessThan", 
	function(value, element, params) {
		maxValue = parseInt($(params).val());
		if(value <= maxValue ){
		return true;
		}
		else {
		return false; 
		}
	});
//-------------- End : LessThan ----------------

	$.validator.addMethod("timeformatplay", function(){
		var timeFormat      =   /^([0-9][0-9]):([0-5][0-9]):([0-5][0-9])$/;
		if (( timeFormat.test($("#play_time").val()) ) !== true) {
			return false;
		}else
			return true;
	});
	$.validator.addMethod("timeformatcan", function(){
		var timeFormat      =   /^([0-9][0-9]):([0-5][0-9]):([0-5][0-9])$/;
		if (( timeFormat.test($("#can_we_start").val()) ) !== true) {
			return false;
		}else
			return true;
	});
	
	$.validator.addMethod("timeformatnext", function(){
		var timeFormat      =   /^([0-9][0-9]):([0-5][0-9]):([0-5][0-9])$/;
		if (( timeFormat.test($("#next_start_time").val()) ) !== true) {
			return false;
		}else
			return true;
	});
	
	$.validator.addMethod("delayvalidate", function(){
		var timeFormat      =   /^([0-9][0-9]):([0-5][0-9]):([0-5][0-9])$/;
		if (!(( timeFormat.test($("#can_we_start").val()) ) !== true)) {
			//alert($("#can_we_start").val() + ' -- ' + $("#play_time").val())
			var temp1 = $("#can_we_start").val().replace(/:/g, '')
			var temp2 = $("#play_time").val().replace(/:/g, '')
			if(temp1 >= temp2)
				return false;
			else return true;
		}
		else
			return true;
	});
	
	$.validator.addMethod("mintime", function(){
		var temp1 = $("#next_start_time").val().split(':');
		if(temp1[0] == 0 && temp1[1] < 15)
			return false; 
		else
			return true;
	});
	
$.validator.addMethod("eliminationValidate", function(){
		var gameType = document.getElementById('game_type').value;
	//if(($("#game_type").val() == 2) ) {	
	//	console.log('--00---'+minValue+'--11---'+maxValue);
	if((gameType == 2) ) {	
		var maxValue = document.getElementById('maxplayer').value;
		var minValue = document.getElementById('minimum_player').value;
		var powValue = 0;
		var powFlag  = 1;
		for(var i=1; powValue<maxValue; i++){				
			powValue = Math.pow(minValue, i);
			if(powValue == maxValue){
				powFlag = 0;
				break;
			}
		}			
		if(powFlag != 0){
			return false;
		}else {
			return true;
		}
	}
	else return true;
});

$.validator.addMethod("nameValidate", function(){
	nameflag = 0
	if($.trim($('#tournament').val()) != ''){
		var edit_id = 0;
		if($('#tournament_id').val() != undefined && $('#tournament_id').val() != '' && $('#tournament_id').val() != 0)
			edit_id = $('#tournament_id').val();
		//alert($('#tournament').val() + ' ---- ' + edit_id);
		$.ajax({
				url:actionPath+'models/AjaxAction.php?rand='+Math.random(),
				type: 'POST',
				async: false,
				data: {action:'CHECK_TOURNAMENT',tour_name: $('#tournament').val(), edit_id: edit_id },
				success: function(result){	
					if($.trim(result) != 0){
						nameflag = 1;
					}
				}
		});
	}
	if(nameflag == 1)
		return false;
	else
		return true;
	return true;
});
//--------------Add tournament----------------End

//$("#enddate").rules('add', { greaterThan: "#startdate" });
jQuery.validator.addMethod("greaterThan", 
function(value, element, params) {

    if (!/Invalid|NaN/.test(new Date(value))) {
        return new Date(value) > new Date($(params).val());
    }

    return isNaN(value) && isNaN($(params).val()) 
        || (Number(value) > Number($(params).val())); 
},'Must be greater than {0}.');




-// ---------------------- BEGIN : GAME ADD -----------------------
$("#add_game_form").validate({
		rules:{
			gamename				:	{ required:true,gameExist:true },
			email        			:	{ required:true,email:true},
			play_time        		:	{ required:true,timeformatplay:true,validTime:true},
			//empty_game_photo		:	{ required:true},
			empty_game_certificate  :   { required:{  
										depends: function(element){																				
											if($("#iOn").is(":checked") ){
												return true;
											}									
											else return false;
										}
										}},			
			hidden_gamerules        :	{ required:{  
										depends: function(element){
											var chkempty = 0;	
											$(".game_rules").find("tr.rule").each(function() {		
												var text1 	=	$(this).find("input").eq(0).val();
												var text2 	=	$(this).find("input").eq(1).val();
												if($.trim(text1) == "" || $.trim(text2) == "")
													chkempty = 1;	
											});
											if(chkempty == 1){
												$("html, body").animate({ scrollTop: $(".game_rules").height() });
												return true;
											}									
											else return false;
										}
										}},
			
		},
		messages:{
			gamename          		:	{ required:'Game Name is required', gameExist:'Game already exist'},
			email					:	{ required:'Email is required' },
			play_time				:	{ required:'Play Time is required',timeformatplay:'Please provide the correct time format' ,validTime:'Please provide the Valid Time'},
			empty_game_certificate  :   { required:'Apple Push Certificate is required'},
			hidden_gamerules 		:	{ required:'Please fill the Elimination Rule and High Score Rule'},		
		}		
	});
	$.validator.addMethod("gameExist", function(){
		nameflag = 1;
		if($.trim($('#gamename').val()) != ''){
			var edit_id = 0;
			if($('#game_id').val() != undefined && $('#game_id').val() != '' && $('#game_id').val() != 0)
				edit_id = $('#game_id').val();
			$.ajax({
				url:actionPath+'models/AjaxAction.php?rand='+Math.random(),
				type: 'POST',
				async: false,
				data: {action:'CHECK_GAME',game_name: $('#gamename').val(), edit_id: edit_id },
				success: function(result){	
					if($.trim(result) == 1){
						nameflag = 0;
					}
				}
			});
		}
		if(nameflag == 1) return true;
		else return false;
		return true;
	});
	$.validator.addMethod("validTime", function(element){
		if(element == '00:00:00')
			return false;
		return true;
	});
// -----------------------END: GAME ADD -----------------------

//--------------Add Service---------------start
$("#add_service_form").validate({
		rules:{
			process			:	{ required:true},			
			service_path	:	{ required:true},       
			method			:	{ required:true},
			module_name		:	{ required:true},
			sample_data		:	{ required:true},
			output_param	:	{ required:true}
		},
		messages:{
			process       	:	{ required:'Purpose is required'},
			service_path	:	{ required:'Endpoint is required'},
			method			:	{ required:'Method is required'},
			module_name		:	{ required:'Module Name is required'},
			sample_data		:	{ required:'Field Name is required'},
			output_param	:	{ required:'Output Param is required'}
		}
});
//--------------Add Service----------------End
//--------------Add virtual coin---------------start
$("#add_virtual_coin_form").validate({
		rules:{
			userId       :	{ required:true},
			brandId      :	{ required:true},
			devBrandId   :	{ required:true},
			coin     	 :	{ required:true,min:1,customMaxLength:true, maxlength:8},
          },
		messages:{
			userId       :	{ required:'User is required'},
			brandId      :	{ required:'Brand is required'},
			devBrandId   :	{ required:'Developer & Brand is required'},
			coin      	 :	{ required:'Virtual Coin is required',min:'Virtual Coin must be minimum one',customMaxLength:'Virtual Coins maximum allowed is testing 99,999,999',maxlength:'Virtual coins maximum allowed is 99,999,999.'},
		}
	});
$.validator.addMethod("notZero", function(){
		if(parseInt($("#coin").val()) > 0) { 
				return true;	
			}
			else {
			return false;
			}
	});
$.validator.addMethod("customMaxLength", function(value, element){
		var userExist = $("#userExist").val();
		if(userExist && userExist !='0'){
			if(value.length > 8)
			return false;
		}
			return true;
	});
//--------------Add virtual coin----------------End
//--------------Add tilt coin---------------start
$("#add_tilt_coin_form").validate({
		rules:{
			userId       :	{ required:true},
			//coin     	 :	{ required:true},
          },
		messages:{
			userId       :	{ required:'User is required'},
			//coin      :	{ required:'Coin is required'},
		}
	});
//--------------Add tilt coin----------------End
//--------------Add tilt dollar coin---------------start
$("#add_tilt_dollar_form").validate({
		rules:{
			userId       :	{ required:true},
			//coins     	 :	{ required:true,max:99999999,trigerPopup:true},
          },
		messages:{
			userId       :	{ required:'User is required'},
			//coins      :	{ required:'Coin ttttttt is required',max:"Maximum allowed is 99,999,999",trigerPopup:"sfdsgfdgdf"}
		}
	});
//--------------Add tilt dollar coin----------------End
//-------------- tournament Restriction ----------------start
$("#loc_restriction_form").validate({
		rules:{
			country      :	{ required: true},
			state      :	{ required: true},
          },
		messages:{
			country      :	{ required:'Country is required'},
			state       :	{ required:'State is required'},
		}
	});
//-------------- tournament Restriction ----------------End
//-------------- tournament rules manage  ----------------start
$("#add_rules_form").validate({
		rules:{
			tournamentId      :	{ required: true},
          },
		messages:{
			tournamentId      :	{ required:'Tournament is required'},
		}
	});
//-------------- tournament rules manage ----------------End

});
/**

										depends: function(element){
										console.log('1111111111'+$("#Distance").val())
											//var a	=	parseInt($("#Distance").val());
											if(parseInt($("#Distance").val()) > 0) { 
											console.log('2222222222'+$("#Distance").val())
												//return true;	
												false;
											}
											else {false;}
										}
									}

*/
//--------------Website Home---------------start
/*$("#website_home_form").validate({
		rules:{
			promo_video		       :	{ fileTypeVideo:true, fileSizeVideo:true},
			developer_content      :	{ required:true},
			media_content	       :	{ required:true},
			bg_image		       :	{ fileTypeImage:true, fileSize:true}
		},
		messages:{
			promo_video			   	:	{ fileTypeVideo:'Please upload mp4 video only', fileSizeVideo:'Video size should be less than 10 MB'},
			developer_content   	:	{ required:'Developers Area Content	is required'},
			media_content     		:   { required:'Media Area Content is required'},
			bg_image     			:   { fileTypeImage:'Please upload JPEG, JPG and PNG images only', fileSize:'Image size should be less than 10 MB'}
		}
	});
	
	$.validator.addMethod("fileSizeVideo", function(){
		if($("#promo_video")[0].files[0].size <= 10485760) { 
				return true;	
		}else {
			return false;
		}
	});
	$.validator.addMethod("fileSize", function(){
		if($("#bg_image")[0].files[0].size <= 10485760) { 
				return true;	
		}else {
			return false;
		}
	});
	
	$.validator.addMethod("fileTypeVideo", function(){
		var uploadedFile = document.getElementById('promo_video').value;
		alert("----"+uploadedFile+"-----");
		if($.trim(uploadedFile) !=  '' || $.trim(uploadedFile) !=  null|| $.trim(uploadedFile) !=  null){
			var extension = uploadedFile.substring(uploadedFile.lastIndexOf('.') + 1).toLowerCase();
			if (extension == 'mp4') {
					return true;			
			}else {
				return false;
			}
		}
		return true;
	});
	$.validator.addMethod("fileTypeImage", function(){
		var uploadedFile = document.getElementById('bg_image').value;
		if($.trim(uploadedFile) !=  '' || $.trim(uploadedFile) !=  null){
			var extension = uploadedFile.substring(uploadedFile.lastIndexOf('.') + 1).toLowerCase();
			if (extension == 'jpeg' || extension == 'pjpeg' || extension == 'jpg' || extension == 'png') {
					return true;	
			}else {
				return false;
			}
		}
		return true;
	})*/
//--------------Website Home----------------End


//---------------------Website Home page validate-----------------------------
$("#website_home_form").validate({	
	rules:{			
		promo_video_validate :	{ required:true},
		video_image_validate :	{ required:true},
		developer_content    :	{ required:true},		
		media_content        :	{ required:true},
		bg_image_validate    :	{ required:true},
		download_content	 :	{ required:true}
	},
	messages:{
		promo_video_validate 	: { required:'Promo Video is required'},
		video_image_validate  	: { required:'Video Image is required'},
		developer_content  		: { required:'Developers Area Content is required'},
		media_content      		: { required:'Media Area Content is required'},
		bg_image_validate  		: { required:'Background Image is required'},
		download_content  		: { required:'Download Now Content is required'}
	}
});

//---------------------Website About us validate-----------------------------
$("#about_us_form").validate({
	
		rules:{			
			about_usContent :	{ required:true},		
			tilt_gamers     :	{ required:true},		
			tilt_developers :	{ required:true},
			tilt_media      :   { required:true},
			explain         :	{ required:true},
			about_us_validate : 	{ required:true}
		},
		messages:{
			about_usContent  :	{ required:'About Us is required'},
			tilt_gamers      :	{ required:'Tilt For Gamers is required'},
			tilt_developers  :	{ required:'Tilt For Developers is required'},
			tilt_media		 :  { required:'Tilt For Media is required'},
			explain          :	{ required:'Explain is required'},
			about_us_validate : { required:'About Us Image is required'}
		}
	});
//---------------------Website Developer page validate-----------------------------
$("#website_developer_form").validate({	
	rules:{			
		promo_video_validate :	{ required:true},
		video_image_validate :	{ required:true},
		info_image_validate  :	{ required:true},		
		info_content       	 :	{ required:true},
		inte_content		 :	{ required:true},
		bg_image_validate    :	{ required:true},
		current_promo		 :	{ required:true}
		
	},
	messages:{
		promo_video_validate 	: { required:'Promo Video is required'},
		video_image_validate  	: { required:'Video Image is required'},
		info_image_validate  	: { required:'Infographics Image is required'},
		info_content      		: { required:'Infographics Content is required'},
		inte_content      		: { required:'Integration Content is required'},
		bg_image_validate  		: { required:'Background Image is required'},
		current_promo	  		: { required:'Current Promotion is required'}
		
	}
});
//---------------------Website Developer page validate-----------------------------
// --- #Brand Image -----------
$("#brand_manage_form").validate({	
	rules:{			
		username 	:	{ required:true,noWhiteSpace:true},
		firstname 	:	{ required:true},
		brandname  	:	{ required:true},		
		password    :	{ required:true,minlength:6,maxlength:20},
		email       :	{ required:true,email:true }
	},
	messages:{
		username 	: 	{ required:'User Name is required',noWhiteSpace:'User Name should not contain white space'},
		firstname  	: 	{ required:'Name is required'},
		brandname  	: 	{ required:'Brand Name is required'},
		password    : 	{ required:'Password is required',minlength:'Password should have atleast 6 characters',maxlength:'Password can have maximum 20 characters'},
		email       :	{ required:'Email address is required',email:'Please enter a valid Email address.'}
		
	}
});

// --- @Brand Image -----------
//---------------------Website Developer page validate-----------------------------
$("#website_media_form").validate({	
	rules:{			
		promo_video_validate :	{ required:true},
		video_image_validate :	{ required:true},
		info_image_validate  :	{ required:true},		
		info_content       	 :	{ required:true},
		inte_content		 :	{ required:true},
		bg_image_validate    :	{ required:true},
		current_promo		 :	{ required:true}
		
	},
	messages:{
		promo_video_validate 	: { required:'Promo Video is required'},
		video_image_validate  	: { required:'Video Image is required'},
		info_image_validate  	: { required:'Infographics Image is required'},
		info_content      		: { required:'Infographics Content is required'},
		inte_content      		: { required:'Integration Content is required'},
		bg_image_validate  		: { required:'Background Image is required'},
		current_promo	  		: { required:'Current Promotion is required'}
		
	}
});
//---------------------Website Developer page validate-----------------------------

// --- #Game Developer page -----------
$("#game_developer_manage_form").validate({	
	rules:{			
		//companyname 	:	{ required:true},
		//username 		:	{ required:true,noWhiteSpace: true},
		//contactname  	:	{ required:true},		
		email       	:	{ required:true,email:true },
		//password    	:	{ required:true,minlength:6,maxlength:20}
		
	},
	messages:{
		//companyname 	: 	{ required:'Company Name is required'},
		//username  		: 	{ required:'User Name is required',noWhiteSpace:'User Name should not contain white space'},
		//contactname  	: 	{ required:'Contact Name is required'},
		email       	:	{ required:'Email address is required',email:'Please enter a valid Email address.'},
		//password    	: 	{ required:'Password is required',minlength:'Password should have atleast 6 characters',maxlength:'Password can have maximum 20 characters'}
		
		
	}
});
// --- @Game Developer page -----------
// --- #InApp Products page -----------
$( "#add_inapp_form1" ).validate({
	rules: {
		'productId[]': {required: true},
		'product_name[]': {required: true},
		'product_desc[]': {required: true},
		'product_price[]': {required: true}
    },
	messages:{
		'productId[]': {required: "Product Id is required"},
		'product_name[]': {required: "Product Name is required"},
		'product_desc[]': {required: "Product Description is required"},
		'product_price[]': {required: "Prize is required"}
	}
});

// --- @InApp Products page -----------

// ---------------------- BEGIN : SDK -----------------------
$("#sdk_form").validate({
		rules:{
			isdk	:	{ isdkvalidate:true},
			idoc	:	{ idocvalidate:true},
			iver	:	{ required:true},
			ilog	:   { required:true},			
			asdk	:	{ asdkvalidate:true},
			adoc	:	{ adocvalidate:true},
			aver	:	{ required:true},
			alog	:   { required:true}	
			
		},
		messages:{
			isdk 		:	{ isdkvalidate:'Upload valid SDK file'},
			idoc		:	{ idocvalidate:'Upload valid Document file'},
			iver		:	{ required:'Version is required'},
			ilog  		:   { required:'Change Log is required'},
			asdk        :	{ asdkvalidate:'Upload valid SDK file'},
			adoc		:	{ adocvalidate:'Upload valid Document file' },
			aver		:	{ required:'Version is required'},
			alog  		:   { required:'Change Log is required'}		
		}		
	});
	
	$.validator.addMethod("isdkvalidate", function(){
		if(parseInt($("#status_isdk").val()) == 1) { 
				return true;	
			}
			else {
			return false;
			}
	});
	$.validator.addMethod("idocvalidate", function(){
		if(parseInt($("#status_idoc").val()) == 1) { 
				return true;	
			}
			else {
			return false;
			}
	});
	$.validator.addMethod("asdkvalidate", function(){
		if(parseInt($("#status_asdk").val()) == 1) { 
				return true;	
			}
			else {
			return false;
			}
	});
	$.validator.addMethod("adocvalidate", function(){
		if(parseInt($("#status_adoc").val()) == 1) { 
				return true;	
			}
			else {
			return false;
			}
	});
// -----------------------END: SDK -----------------------
//User name white space validation
$.validator.addMethod("noWhiteSpace", function(element){if (element.match(/\s/)) return false;else return true;});
