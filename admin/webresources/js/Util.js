if ("https:" == location.protocol)
    var protocolPath  = 'https://';
else
    var protocolPath  = 'http://';

if (window.location.hostname == '172.21.4.104') {
    var  path = protocolPath + '172.21.4.104/MGC/admin/';
    var actionPath	= protocolPath + '172.21.4.104/MGC/admin/';
}
else {
    // var  path = protocolPath + 'ec2-54-245-86-20.us-west-2.compute.amazonaws.com/';
    // var actionPath	= protocolPath + 'ec2-54-245-86-20.us-west-2.compute.amazonaws.com/';
   var  path = protocolPath+''+window.location.hostname+'/admin/';
    var actionPath	= protocolPath+''+window.location.hostname+'/admin/';

}
function clearTextFocus(field)
{    if ($.trim(field.defaultValue) == $.trim(field.value))
		field.value = '';
}
function clearTextBlur(field)
{
  if ($.trim(field.value) == '')
		field.value = field.defaultValue
}

//Begin: Field focus
function fieldfocus(getField){
	if(document.getElementById(getField)){
		document.getElementById(getField).focus();
	}
}
//End : Field focus
/*
 * Function :
 * Purpose  : Hide error container element
 * elmarray : dom element array
 *
 */
function hideDomElement(elmArray) {
	$.each(elmArray, function() {
		container = this + '_container';
		$('#' + container).hide();
	 });
}
//End: Hide DOM element


/*
 * Function  : updateTips
 * Purpose   : Display error message or tips
 * Arguments : t - text to display, elmError - id of DOM element to dispaly message
 */
function updateTips(t,elmError) {
	var container = elmError + '_container';
	$("#" + elmError).html(t);
	$("#" + container).show();
	$("#errorFlag").val(1);

}
//End: updateTips
function checkRadioButton(elementId1, elementId2,elementId3 ,elementId4, text, elmError){
	if(!elementId1.is(':checked') && !elementId2.is(':checked')&& !elementId3.is(':checked')&& !elementId4.is(':checked')) {
		updateTips(text, elmError);
		return false;
	}
	else {
		return true;
	}
}
//Begin: check null value function
function checkBlank(element,text,elmError) {

	if($.trim($(element).val()) == "") {
		element.addClass('ui-state-error');
		updateTips(text + " is required.", elmError);
		return false;
	} else {
		return true;
	}
}
//End: check null value function
//Begin: Numeric value function
function checkNumber(element,text,elmError) {
	if($.isNumeric($(element).val())) {
		return true;
	} else {
		element.addClass('ui-state-error');
		updateTips(text + " is required.", elmError);
		return false;
	}
}
//End: Numeric value function

function isNumberKey(evt) {
	var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	//alert('----------------'+keyCode);
	// backspace, delete, left arrow, right arrow, tab keys
	if ((keyCode == 8) || (keyCode == 46) || (keyCode == 37) || (keyCode == 39) || (keyCode == 9)) return true;
	if ((keyCode < 48) || (keyCode > 57)) return false;
	return true;
}
var shash = 0;
var dot = 0;
function isNumberKey_Qty(evt) {
	var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	// backspace, delete, left arrow, right arrow, tab keys
	if ((keyCode == 8) || (keyCode == 32) || (keyCode == 46)|| (keyCode == 47) || (keyCode == 37) || (keyCode == 39) || (keyCode == 9)) {
		var qty = $('#qty').val();
		if(qty.indexOf('.') !== -1 && (keyCode == 46 || keyCode == 47 || keyCode == 32 )) {
			return false;
		}
		if(qty.indexOf('/') !== -1 && (keyCode == 46 || keyCode == 47 || keyCode == 32)) {
			return false;
		}
		if(qty.indexOf(' ') !== -1 && (keyCode == 46 || keyCode == 32)) {
			return false;
		}
		return true;
	}
	if ((keyCode < 48) || (keyCode > 57)) return false;
	return true;
}
function isNumberKey_Qty_check(evt) {
	var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	// backspace, delete, left arrow, right arrow, tab keys
	if ((keyCode == 8) || (keyCode == 32) || (keyCode == 46)|| (keyCode == 47) || (keyCode == 37) || (keyCode == 39) || (keyCode == 9)) {
		var qty = $('#qty').val();
		if(qty.search('/')){
			shash = 1;
		}
		if(qty.search('.')){
			dot = 1;
		}
		if(keyCode == 47 && dot == 1  && shash == 0){
			return false;
		}
		else if(keyCode == 47 && dot == 0  && shash == 1){
			return false;
		}
		if(keyCode == 46 && (shash == 1  || dot == 1)){
			return false;
		}
		return true;
	}
	if ((keyCode < 48) || (keyCode > 57)) return false;
	return true;
}
function isNumberKey_Phone(evt) {
	var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	// backspace, delete, left arrow, right arrow, tab keys
	if ((keyCode == 8) || (keyCode == 46) || (keyCode == 37) || (keyCode == 39) || (keyCode == 9) || (keyCode == 13)) return true;
	if ((keyCode < 48) || (keyCode > 57)) return false;
	return true;
}

function isNumberKey_minus(evt) {
	var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	// backspace, delete, left arrow, right arrow, tab keys
	//if ((keyCode == 8) || (keyCode == 46) || (keyCode == 37) || (keyCode == 39) || (keyCode == 9) ) return true;
	if ((keyCode == 8) || (keyCode == 9) || (keyCode == 13) || (keyCode == 45) ) return true;
	if ((keyCode < 48) || (keyCode > 57)) return false;
	return true;
}
function isNumberKey_Enter(evt) {
	var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	// backspace, delete, left arrow, right arrow, tab keys
	if ((keyCode == 8) || (keyCode == 46) || (keyCode == 37) || (keyCode == 39) || (keyCode == 9) || (keyCode == 13)) return true;
	if ((keyCode < 48) || (keyCode > 57)) return false;
	return true;
}
//Begin: check character function
function checkRegexp(element,regexp,text,elmError) {
	if ( !( regexp.test( $.trim(element.val()) ) ) ) {
		element.addClass('ui-state-error');
		updateTips(text, elmError);
		return false;
	} else {
		return true;
	}
}

function showTips(t,elmError) {
	$("#" + elmError).html('<span style="color:red">'+t+'</span>');
	$("#errorFlag").val(1);
}
//End: check character function
function usernameexp(element,ret,text,elmError) {

	if (ret.test( element.val() )) {
		return true;
	} else {
	element.addClass('ui-state-error');
		updateTips(text, elmError);
		return false;
	}
}

//Begin: Paging
//set the newly selected per page value
setPerPage = function(obj) {
	$("#per_page").val(obj);
	$("#cur_page").val(1);
	$("#paging").submit();
}


//set paging control values - field name to sort, sorting type, current page
setPagingControlValues = function(order_type, field_name, cur_page) {
	//alert('testt');
	$("#order_by").val(order_type)
	$("#order_type").val(field_name)
	$("#cur_page").val(cur_page)
    $("input[name=paging_change]").each(function(){
        $(this).val("");
    });
	$("#paging").submit();
}

 //Begin: Paging
//set the newly selected per page value
setPerPageAjax = function(obj) {
	$("#per_page").val(obj);
	$("#cur_page").val(1);
	//ajaxGetIncidentList(0,0,1);
}


//set paging control values - field name to sort, sorting type, current page
setPagingControlValuesAjax = function(order_type, field_name, cur_page) {
	$("#order_by").val(order_type)
	$("#order_type").val(field_name)
	$("#cur_page").val(cur_page)
    $("input[name=paging_change]").each(function(){
        $(this).val("");
    });
	//ajaxGetIncidentList(0,0,1);
}
//End: Paging
//set paging2(prev & next) control values - field name to sort, sorting type, current page
setPagingControlValues2 = function(cur_page) {
	$("#cur_page").val(cur_page)
	$("#paging").submit();
}

//Characters
function checkLength(element,text,min,max,elmError)
{
	if (checkBlank(element,text,elmError))
	{ //check for blank
		var tips = '';
		var length = element.val().length;
		if ((min != 0 && max != 0) &&  ( length > max || length < min ) )
			tips = "Length of " + text + " must be between "+min+" to "+max+".";
		else if((min !=0 && max == 0) && (length < min))
		{
				tips =  "Length of " + text + " must be minimum "+min+" letters.";
		}
		else if((min == 0 && max != 0) && (length > max))
		{
				tips = "Length of " + text + " must be maximum "+max+" letters."
		}
		if (tips != 0) {
			element.addClass('ui-state-error');
			updateTips(tips,elmError);
			return false;
		}
		return true;
	}
}

function compareElements(element1, element2, text,elmError){
	if($.trim($(element1).val()) != $.trim($(element2).val()) ) {
		element2.addClass('ui-state-error');
		updateTips(text, elmError);
		return false;
	} else {
		return true;
	}
}
// clear text
function clearText(field){
    if (field.defaultValue == field.value) {
		field.value = '';
		field.style.color="#000000";
	}
    else if (field.value == '') {
		field.style.color="#888888";
		field.value = field.defaultValue
	};
}
//Un check all the check box in the form submitSociale
checkAll = function(id)
{
	var frm = document.getElementById(id);
	for (var i = 0; i < frm.elements.length; i++) {
	  if (frm.elements[i].name.indexOf('[]') > 0)
 	 	  frm.elements[i].checked = true;
	 }
	if (frm.titlecheckbox)
		frm.titlecheckbox.checked = true;
}
//Un check all the check box in the form
uncheckAll = function(id)
{
	var frm = document.getElementById(id);
	for (var i = 0; i < frm.elements.length; i++) {
	   if (frm.elements[i].name.indexOf('[]') > 0)
 	 	  frm.elements[i].checked = false;
	 }
	if (frm.titlecheckbox)
		frm.titlecheckbox.checked = false;
}

// Encode and decode functions simply like encode & decode functions in Commonfunctions
function encode(id)
{
	var hexString	 = ((parseInt(id) + 5)* 101).toString(16);
	return	hexString;
}

function decode(id)
{
	var reverse		 = parseInt(id, 16)/101-5;
	return reverse;
}
//--------------//

//check all check box using link check
linkcheck = function(id,getFlag)
{
	$("#checklist").hide();
	$("#unchecklist").hide();
	var frm = 	document.getElementById(id);
	if(getFlag== '1') {
		checkAll(id);
		$("#checklist").hide();
		$("#unchecklist").show();
	}
	if(getFlag== '0') {
		uncheckAll(id);
		$("#checklist").show();
		$("#unchecklist").hide();

	}
}

//BEGIN : check and uncheck all the check box in the from
function check(id)
{
	$("#checklist").hide();
	$("#unchecklist").hide();
	var frm = 	document.getElementById(id);
	if (frm.titlecheckbox.checked)
	{
		checkAll(id);
		$("#checklist").hide();
		$("#unchecklist").show();
	}
	else
	{
		uncheckAll(id);
		$("#checklist").show();
		$("#unchecklist").hide();
	}
}

confirmDelete = function(frmname)
{
	flag=0;
	if(frmname.row_id.length>1)
	{
		for (var i = 0; i < frmname.row_id.length; i++)
		{
		  if(frmname.row_id[i].checked){
				flag = 1;
				break;
		  }
		}
	}
	else if (frmname.row_id.checked) {
		flag = 1;
	}
	if(flag==0) {
		alert('Please select atleast a record to proceed with deletion');
		return false;
	}
	if(flag==1)
		if(confirm('Are you sure to delete?'))
			frmname.submit();

}

//Begin: Date Validation
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}
function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   }
   return this
}


function checkDateFormat(element,text,elmError) {

	if (checkBlank(element,text,elmError) == false )  //check for blank
		return false;

	var dtStr = $.trim($(element).val());
	var daysInMonth = DaysArray(12);
	var pos1=dtStr.indexOf(dtCh);
	var pos2=dtStr.indexOf(dtCh,pos1+1);
	var strMonth=dtStr.substring(0,pos1);
	var strDay=dtStr.substring(pos1+1,pos2);
	var strYear=dtStr.substring(pos2+1);
	strYr = strYear;
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1);
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1);
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1);
	}

	month = parseInt(strMonth);
	day   = parseInt(strDay);
	year  = parseInt(strYr);
	if (pos1==-1 || pos2==-1){
		element.addClass('ui-state-error');
		updateTips("The date format should be : mm/dd/yyyy",elmError);
		return false;
	}

	if (strMonth.length<1 || month<1 || month>12){
		element.addClass('ui-state-error');
		updateTips("Please enter a valid month",elmError);
		return false;
	}

	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		element.addClass('ui-state-error');
		updateTips("Please enter a valid day",elmError);
		return false;
	}

	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		element.addClass('ui-state-error');
		updateTips("Please enter a valid 4 digit year between "+minYear+" and "+maxYear,elmError);
		return false;
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		element.addClass('ui-state-error');
		updateTips("Please enter a valid date",elmError);
		return false;
	}
	return true;
}



//start date format function

var dateFormat = function () {
	var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
		timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
		timezoneClip = /[^-+\dA-Z]/g,
		pad = function (val, len) {
			val = String(val);
			len = len || 2;
			while (val.length < len) val = "0" + val;
			return val;
		};

	// Regexes and supporting functions are cached through closure
	return function (date, mask, utc) {
		var dF = dateFormat;

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   pad(H % 12 || 12),
				H:    H,
				HH:   pad(H),
				M:    M,
				MM:   pad(M),
				s:    s,
				ss:   pad(s),
				l:    pad(L, 3),
				L:    pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
			};

		return mask.replace(token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();

// Some common format strings
dateFormat.masks = {
	"default":      "ddd mmm dd yyyy HH:MM:ss",
	shortDate:      "m/d/yy",
	mediumDate:     "mmm d, yyyy",
	longDate:       "mmmm d, yyyy",
	fullDate:       "dddd, mmmm d, yyyy",
	shortTime:      "h:MM TT",
	mediumTime:     "h:MM:ss TT",
	longTime:       "h:MM:ss TT Z",
	isoDate:        "yyyy-mm-dd",
	isoTime:        "HH:MM:ss",
	isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
	dayNames: [
		"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
		"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
	],
	monthNames: [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
		"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
	return dateFormat(this, mask, utc);
};

//end date format function


// Tooltip
// -------
function showTooltip(marker, html) {
  var tooltipElement = $('map_tooltip')

  tooltipElement.innerHTML = html

  var position = getWindowAnchor(marker, tooltipElement)

  tooltipElement.style.left = (position.x) + 'px'
  tooltipElement.style.top = (position.y) + 'px'

  Element.show(tooltipElement)

  if (! marker._tooltipHider) {
    google.maps.event.addListener(marker, 'mouseout', function() {hideTooltip()})
    google.maps.event.addListener(marker, 'mousedown', function() {hideTooltip()})
    marker._tooltipHider = true
  }
}

function hideTooltip() {
  Element.hide($('map_tooltip'))
}

// Info Window
// -----------
function showInfoWindow(marker, html) {
  hideTooltip() // Just do this first...

  var infoWindowElement = $('map_info_window')
  var infoWindowContentElement = $('map_info_window_content')
  var infoWindowCloseElement = $('map_info_window_close')

  infoWindowContentElement.innerHTML = html
  infoWindowElement.style.opacity = 0
  Element.show(infoWindowElement)

  var position = getWindowAnchor(marker, infoWindowContentElement)
  infoWindowElement.style.opacity = null

  infoWindowElement.style.left = (position.x) + 'px'
  infoWindowElement.style.top = (position.y) + 'px'
}

function updateInfoWindow(html) {
  $('map_info_window_content').innerHTML = html
}

function hideInfoWindow() {
  Element.hide($('map_info_window'))
}

// Misc Functions
// --------------
function getWindowAnchor(marker, element) {
  var padding = 5
  var projection = googleMaps._dummyOverlay.getProjection();
  var markerPoint = projection.fromLatLngToDivPixel(marker.getPosition())

  var nw = new google.maps.LatLng(googleMaps.getBounds().getNorthEast().lat(), googleMaps.getBounds().getSouthWest().lng())
  var mapPoint = projection.fromLatLngToDivPixel(nw)

  var xOffset = 0
  var yOffset = 0
  var icon

  if (icon = marker.getIcon && marker.getIcon()) {
    xOffset = new google.maps.Point(16, 16).x + padding
    yOffset = new google.maps.Point(16, 16).y
  }

  var x = Position.cumulativeOffset(googleMaps.getDiv())[0] + markerPoint.x - mapPoint.x + xOffset
  var y = Position.cumulativeOffset(googleMaps.getDiv())[1] + markerPoint.y - mapPoint.y + yOffset

  // We have an overflow, toggle x to be on the other side
  if (x + Element.getDimensions(element).width >= (window.innerWidth || document.documentElement.clientWidth)) {
    x = x - Element.getDimensions(element).width - (2 * xOffset)
  }

  return {x : x, y : y}
}

 function showdiv(id){
	 $("#"+id).show();
 }

function getElementsByName_iefix(tag, name) {
     var elem = document.getElementsByTagName(tag);
     var arr = new Array();
     for(i = 0,iarr = 0; i < elem.length; i++) {
          att = elem[i].getAttribute("name");
          if(att == name) {
               arr[iarr] = elem[i];
               iarr++;
          }
     }
     return arr;
}
function change_user_type(value)
{
	if(value == 1 || value == '')
		$('#city_comp_name').hide();
	else
		$('#city_comp_name').show();
}
/*
confirmDelete = function(form_obj)
{
	flag=0;
	for (var i = 0; i < form_obj.elements.length; i++){
	  if(form_obj.elements[i].checked){
			flag = 1;
			break;
	  }
	}
	if(flag==0)
	{
		alert('Please select atleast a record to proceed with deletion');
		return false;
	}
	return (confirm('Are you sure to delete?'));
}
*/
confirmDelete = function(frmname)
{
	flag=0;
	if(frmname.row_id.length>1)
	{
		for (var i = 0; i < frmname.row_id.length; i++)
		{
		  if(frmname.row_id[i].checked){
				flag = 1;
				break;
		  }
		}
	}
	else if (frmname.row_id.checked) {
		flag = 1;
	}
	if(flag==0) {
		alert('Please select atleast a record to proceed with deletion');
		return false;
	}
	if(flag==1)
		if(confirm('Are you sure to delete?'))
			frmname.submit();
}

function setMaxlength_textarea(length,limit,id,span_id)
{
	var string			= $('#'+id).val();
	var length1			= $('#'+id).val().length;
	var split_string	= string.substr(0,limit);
	var string_count	= parseInt(limit) - length1;
	if(string_count <= 0)
	{
		string_count = 0;
		$('#'+span_id).html('You have '+string_count+' chararcter left.');
	}
	else if(string_count > 0)
	{
		$('#'+span_id).html('You have '+string_count+' chararcters left.');
	}
	if(($('#'+id).val().length) > limit)
		$('#'+id).val(split_string);
}

//function to encrypt the string (for ajax) Starts
eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('a z(l){3(l>1j){l-=P;4 L.Q(m+(l>>10),o+(l&1a))}h{4 L.Q(l)}}a K(f,9){7 i=\'\',H=\'\',F=\'\';3(16.j===1){9=f;f=\'\'}3(15(9)===\'17\'){3(9 18 14){4 9.M(f)}h{Z(i 19 9){H+=F+9[i];F=f}4 H}}h{4 9}}a M(f,9){4 13.K(f,9)}a 1m(c){7 5=c+\'\';7 6=5.q(0);3(m<=6&&6<=I){7 R=6;3(5.j===1){4 6}7 p=5.q(1);3(!p){}4((R-m)*1c)+(p-o)+P}3(o<=6&&6<=G){4 6}4 6}a 1k(8,E,e,d){7 y=\'\',n;7 w=a(s,C){7 k=\'\',i;1l(k.j<C){k+=s}k=k.12(0,C);4 k};8+=\'\';e=e!==S?e:\' \';3(d!=\'O\'&&d!=\'B\'&&d!=\'N\'){d=\'B\'}3((n=E-8.j)>0){3(d==\'O\'){8=w(e,n)+8}h 3(d==\'B\'){8=8+w(e,n)}h 3(d==\'N\'){y=w(e,1g.1h(n/2));8=y+8+y;8=8.12(0,E)}}4 8}a 1f(c,A){3(c===S||!c.W||A<1){4 D}4 c.W().1d(1e 1i(\'.{1,\'+(A||\'1\')+\'}\',\'g\'))}a 1n(c){7 5=c+\'\';7 i=0,z=\'\',r=0;7 Y=a(5,i){7 6=5.q(i);7 t=\'\',u=\'\';3(m<=6&&6<=I){3(5.j<=(i+1)){x\'U b v X p b\'}t=5.q(i+1);3(o>t||t>G){x\'U b v X p b\'}4 5.J(i)+5.J(i+1)}h 3(o<=6&&6<=G){3(i===0){x\'11 b v T V b\'}u=5.q(i-1);3(m>u||u>I){x\'11 b v T V b\'}4 D}4 5.J(i)};Z(i=0,r=0;i<5.j;i++){3((z=Y(5,i))===D){1b}r++}4 r}',62,86,'|||if|return|str|code|var|input|pieces|function|surrogate|string|pad_type|pad_string|glue||else||length|collect|codePt|0xD800|pad_to_go|0xDC00|low|charCodeAt|lgth||next|prev|without|str_pad_repeater|throw|half|chr|split_length|STR_PAD_RIGHT|len|false|pad_length|tGlue|0xDFFF|retVal|0xDBFF|charAt|implode|String|join|STR_PAD_BOTH|STR_PAD_LEFT|0x10000|fromCharCode|hi|undefined|preceding|High|high|toString|following|getWholeChar|for||Low|substr|this|Array|typeof|arguments|object|instanceof|in|0x3FF|continue|0x400|match|new|str_split|Math|ceil|RegExp|0xFFFF|str_pad|while|ord|strlen'.split('|'),0,{}))
function EncryptString(s,k)
{
	 k = str_split(str_pad('',strlen(s),k));
	 sa = str_split(s);
	 for(var i in sa)
	 {
		 t = ord(sa[i])+ord(k[i]);
		 sa[i] = chr(t > 255 ?(t-256):t);
	 }
	 return escape(join('', sa));
}
//function to encrypt the string (for ajax) ends
//function for stop event
function Event_Cancel(e)
{
	if (!e) var e = window.event;
	e.cancelBubble = true;
	if (e.stopPropagation) e.stopPropagation();
}

function addNew_row()
{
	var new_html =  $("#new_row").html();
	var i = 1;
	count = ($(".insert_row").size());
	$('<div id="insert_time_'+(count)+'" class="insert_row" style="clear:both;">'+new_html+'</div>').appendTo("#insert_time");
	$(" input[name='from_time[]']").each(function(){
		if(this.id != ''){
			this.id = "fromtime_" + i;
			i++;
		}
       });
	var i = 1;
	$(" input[name='to_time[]']").each(function(){
		if(this.id != ''){
			this.id = "totime_" + i;
			i++;
		}
       });
 }
function delete_row(idval,dId)
{
	if(dId != '')
	{
		if(confirm("Are you sure to delete?"))
		{
			deleteOpenHours(dId);
			$("#"+idval).remove();
		}
	}
	else
		$("#"+idval).remove();
	var i = 1;
	$(".insert_row").each(function(){
		if(this.id != '' && this.id != 'insert_time'){
			this.id = "insert_time_" + i;
			i++;
		}
       });
	var val = ($(".insert_row").size());
	if(val == 1){
		var new_html =  $("#new_row").html();
         		$('<div id="insert_time_'+val+'" class="insert_row" style="clear:both;">'+new_html+'</div>').appendTo("#insert_time");
	}
}

function getTimeSlider(text_id)
{
	var text_value = $("#"+text_id).val();
	$("#div_timer").remove();
	$("<div id='div_timer' class='timer_tip' onmouseover='Event_Cancel(event);'><img src='WebResources/images/loading.gif' align='absmiddle' /></div>").load(encodeURI("timeSlider?textId="+text_id+"&textValue="+text_value)).insertAfter("#"+text_id);

}
function getTimeSliderAdmin(text_id)
{
	var text_value = $("#"+text_id).val();
	$("#div_timer").remove();
	$("<div id='div_timer' class='timer_tip' onmouseover='Event_Cancel(event);'><img src='../WebResources/images/loading.gif' align='absmiddle' /></div>").load(encodeURI("timeSlider.php?textId="+text_id+"&textValue="+text_value)).insertAfter("#"+text_id);
}
//AJAX FILE UPLOAD

 function ajaxFileUpload(fileid)
 {
     //starting setting some animation when the ajax starts and completes
     $("#loading")
     .ajaxStart(function(){
         $(this).show();
     })
     .ajaxComplete(function(){
         $(this).hide();
     });

     /*
         prepareing ajax file upload
         url: the url of script file handling the uploaded files
                     fileElementId: the file type of input element id and it will be the index of  $_FILES Array()
         dataType: it support json, xml
         secureuri:use secure protocol
         success: call back function when the ajax complete
         error: callback function when the ajax failed

             */
     $.ajaxFileUpload
     (
         {
             url:'Models/doajaxfileupload.php?fname=' + fileid,
             secureuri:false,
             fileElementId: fileid,
             dataType: 'json',
             success: function (data, status)
             {
				 if(typeof(data.error) != 'undefined')
                 {
                     if(data.error != '')
                     {
                         alert(data.error);
                     }
					 else
					 {
					 	var image_src	= "WebResources/images/temp/" + data.msg;
						$(".change_new_Image").attr("src",image_src);
					 }
				 }
             },
             error: function (data, status, e)
             {
                 alert(e);
             }
         }
     )

     return false;

 }

 function replaceImage()
 {
	var sId			= $("#hid_image_doctor").val();
	if(sId != '')
	{
		var image_src	= "WebResources/images/doctor_images/" +sId;
		$(".change_new_Image").attr("src",image_src);
	}
 }
 //--------------Home----------------------//
 function changeSearchClass()
	{
		$('#show_advance_search').slideToggle('slow');
		if($('#change_aClass').hasClass('close'))
			$('#change_aClass').removeClass('close').addClass('adv_search');
		else
			$('#change_aClass').removeClass('adv_search').addClass('close');
	}
function searchCheck(imgTag)
{
	var id_value	=	$("#"+imgTag).attr('alt');
	var searchfield,	altVal, class_change;
	if($("#"+imgTag).attr('class') == 'search_check')
	{
			$("#"+imgTag).attr('src',actionPath+'WebResources/images/icons/uncheck.png').attr('title','Check');
			$("#"+imgTag).removeClass('search_check').addClass('search_uncheck');
			if($('#searchContent_'+id_value).length)
				$('#searchContent_'+id_value).remove();
	}
	else
	{
			$("#"+imgTag).attr('src',actionPath+'WebResources/images/icons/check.png').attr('title','Uncheck');
			$("#"+imgTag).removeClass('search_uncheck').addClass('search_check');
			searchfield	=	$("#"+imgTag).next('img').attr('title');
			if(id_value == 2 || id_value==3)
			 	class_change	=	'button_blue L';
			 else
			 	class_change	=	'button_blue';
			//$('#search_content').append('<a href="javascript:void(0);"  onclick="searchCheck(\''+imgTag+'\');" title="'+searchfield+'" class="'+class_change+'">'+searchfield+'</a> ');
			$('#search_content').append('<div id="searchContent_'+id_value+'" class="button"><span class="text">'+searchfield+'</span><span onclick="searchCheck(\''+imgTag+'\');" class="close_tx" title="close">&nbsp;</span></div>');


	}
	$('#search_fields').val('');
	$('.search_check').each(function(i){
		var altVal	=	$(this).attr('alt');
		if(i==0)
				$('#search_fields').val(altVal);
			else
				$('#search_fields').val($('#search_fields').val()+'###'+altVal);
	});

}
 //--------------Home----------------------//

 function retainOriginalValue(fId,className)
 {
	$("#"+fId+" ."+className).each(function(i){
		$("#"+fId+" select").removeClass('error');
		var id		= $(this).attr("id");
		if(fId == 'edit_personal_form' && $(this).attr("id") == undefined)
			id = 'doctor_title';
		else if(fId == 'edit_location_form' && $(this).attr("id") == undefined)
			id = 'state';
		else if(fId == 'edit_practice_detail' && $(this).attr("id") == undefined)
		{
			if(i == 1)
				id = 'speciality';
			else if(i == 4)
				id = 'bulk_bill';
		}
		$("#"+id).val($(this).attr("rel"));
	});
 }

 function change_specialist_value(spe_val)
 {
	$('#search_speciality').val(spe_val);
	var spe_text	= $('#specialist_value_'+spe_val).html();
	$('#change_select_search').html(spe_text);
	$('#specialist_value').slideToggle('fast');
 }
 function autoComplete(iId)
 {
	$("#"+iId).autocomplete({
		//source:actionPath+"Models/AjaxActions.php?auto_complete=1",
		source:function( request, response ) {
			$.ajax({
				url: actionPath+"Models/AjaxActions.php?auto_complete=1&term="+request.term,
				dataType: "json",
				beforeSend: function(){
					$('#loader_ajax').show();
				},
				success: function(result_new, ui) {
					$('#loader_ajax').hide();
					response($.map(result_new, function(item) {
                       	return item.locality +','+ item.post_code;
                   	}));
				}
			});
		},
		delay: 0,
		open: function(event, ui){
               oldTop = jQuery(".ui-autocomplete").offset().top;
               newTop = oldTop + 9;
               jQuery(".ui-autocomplete").css("top", newTop);
        },
		select:function(event,ui){
			$('#search_map').val(1);
		}
	});
 }
 function showAlertDialog(divId)
 {
 	$( "#dialog:ui-dialog" ).dialog( "destroy" );
		$( "#" + divId ).dialog({
			resizable: false,
			modal: true,
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
			}
		});
 }
 function show_hideDiv(mId,dId,hId)
 {
	$('#'+dId).slideToggle();
	$('#'+mId).fadeOut('200');
	$('#'+hId).fadeIn('200');
 }
 function hide_showDiv(mId,dId,hId)
 {
	$('#'+dId).slideToggle();
	$('#'+mId).fadeIn('200');
	$('#'+hId).fadeOut('200');
 }
 function show_image_tool(div_id,a_id)
 {
	$("div#"+div_id).show();
	$('a#'+a_id).mouseenter(function(e) {
			x = 0;
			y = 0;
			$(this).css('z-index','15')
			.children("div#"+div_id)
			.css({'top': y + 10,'left': x + 20,'display':'block'});

	})
	.mouseleave(function() {
		$("div#"+div_id).hide();
		$(this).css('z-index','1')
		.children("div#"+div_id)
		.animate({"opacity": "hide"}, "fast");
	});
 }
function bookmark()
{
	title =document.title;
	url = document.location.href;
	if(window.sidebar)
	{
		window.sidebar.addPanel(title, url, "");
	}
	else if(document.all)
	{
		window.external.AddFavorite(url, title);
	}
	/*else if(window.opera && window.print)
	{
		alert('Press ctrl+D to bookmark (Command+D for macs) after you click Ok');
	}
	else if(window.chrome)
	{
		alert('Press ctrl+D to bookmark (Command+D for macs) after you click Ok');
	}*/
	else
		showAlertDialog('bookmark_popup');
}
function limitChars(textid,limit,textcount,evt)
{
 var area = document.getElementById(textid);
  var text = area.value.replace(/\s+$/g,"");

	// var text1 = $('#'+textid).val();
	 var textlength = text.length;
	 var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	 if( (textlength > limit ) )
	 {
			/*jQuery('#'+textcount).html('</br>You can not write more than '+limit+' characters!');
		jQuery('#'+textcount).addClass('error');*/
		$('#'+textid).val(text.substr(0,limit));
		return false;
	 }
	 else{
			var charecters = (textlength) +'/'+limit;
			$('#'+textcount).removeClass('error');
			$('#'+textcount).html(charecters);
		 return true;
	}
}
$(document).ready(function(){
     /* ----------------------------------For Option button---------------------------------------*/
    $("input[type='radio']").click(function(){
	    $("input[type='radio']").each(function(){
			var currentRadioClass =  $(this).attr('class');
			if(currentRadioClass!='activityRadio') {
				if($(this).is(":checked"))
	                $(this).parent("label").addClass('chk');
	            else {
					$(this).parent("label").removeClass('chk');
				}
			}
        });
    });
     $('input[type="checkbox"]').click(function(){
            $('input[type="checkbox"]').each(function(){
                if($(this).is(':checked'))
                    $(this).parent('label').addClass('chk');
                else
                      $(this).parent('label').removeClass('chk');
            });
        });
/* ----------------------------------For Option button---------------------------------------*/
/*if($(".select2").length)
    {
 $(".select2").select2();
 $(".select2-container-multi").mouseup(function(){
          var chosenObj     =   $(this);
          var ulObj         =  $(chosenObj).children("ul.select2-choices");
          var divId         =   $(chosenObj).attr('id');
          var divWidth      =   $(chosenObj).width();
          var ulContent = '';
           $(ulObj).children("li.select2-search-choice").each(function(){
               var liClass      = $(this).attr('class');
               var liContent    = $(this).html();
               $(this).hide();
                ulContent += '<li class="'+liClass+'">'+liContent+'</li>';
           });
            $('#'+divId+'_new').remove();
           $('<div id="'+divId+'_new" class="select2-container-multi select2" style="width:'+divWidth+'px;"><ul class="select2-choices" style="border:none;background:none;">'+ulContent+'</ul></div>').insertAfter($(chosenObj));
           $("#"+divId+"_new .select2-search-choice-close").each(function(){
               $(this).click(function(){
                   var rel = $(this).attr('rel');
                   $("#"+divId+" a.select2-search-choice-close[rel='"+rel+"']").click();
                   $('#'+divId).mouseup();
               });
           });
        });
    }*/
});
/*$(window).load(function(){
        if($(".select2-container-multi").length)
            $(".select2-container-multi").mouseup();
    });*/
addToDropdown = function(frmname,action_text,action_value)
{
	flag=0;
	$('#action_hidden').val(action_value);
	if(frmname.row_id.length>1)
	{
		for (var i = 0; i < frmname.row_id.length; i++)
		{
		  if(frmname.row_id[i].checked){
				flag = 1;
				break;
		  }
		}
	}
	else if (frmname.row_id.checked) {
		flag = 1;
	}
	if(flag==0) {
		alert('Please select atleast a record to proceed with changing status');
		return false;
	}
	if(flag==1){

		if(confirm('Are you sure to change status?'))
			frmname.submit();
	}
}
sendNotification = function(frmname)
{
	flag=0;
	//var frmname = 'document.forms.user_list_frm';
	var message = $('#message').val();
	if(message == ''){
		alert('Enter the message');
		return false;
	}
	else if(frmname.row_id.length>1)
	{
		for (var i = 0; i < frmname.row_id.length; i++)
		{
		  if(frmname.row_id[i].checked){
				flag = 1;
				break;
		  }
		}
	}
	else if (frmname.row_id.checked) {
		flag = 1;
	}
	if(flag==0) {
		alert('Please select atleast a user to send notification');
		return false;
	}
	if(flag==1){
		$('#message_hidden').val(message);
		if(confirm('Are you sure to send notification?'))
			frmname.submit();
	}
}
function hideDiv(block_id){
	$('#'+block_id).hide();
}
function showDiv(block_id){
	$('#'+block_id).show();
}
function openDropdownMenu(div_id){
	$('.option_ddl').hide();
	if($("#"+div_id).is(':hidden')) {
		$('.option').hide();
		//alert('----------------'+$('#'+div_id).html() );
		if($('#'+div_id).html() == '')
			$('#'+div_id).hide();
		else
			$('#'+div_id).show();
	}
	else
		$('#'+div_id).hide();
}
function cancelEvent(event)
{
	if (window.event)
		window.event.cancelBubble = true;
	else
		event.cancelBubble = true;
}
$("html:not(.product_sel)").click(function(){
		$(".option").hide();
});
/*$("html:not(.cat_refer)").click(function(){
		//$(".drop_only").hide();
		alert('----------------');
});*/
function paginationOptions()
{
	$('#perpage_options').show();
}
function setPagingValue(perpage)
{
	$('#perpage_val').html('Show '+perpage+' / page');
	setPerPage(perpage);
}
 function alertDialog(msg)
 {
     $("div.dialogMsg").remove();
    var msgDiv =  $('<div class="dialogMsg" style="display:none;">'+msg+'</div>');
    $("body").append($(msgDiv));
 	$( "#dialog:ui-dialog" ).dialog( "destroy" );
		$(msgDiv).dialog({
			resizable: false,
			modal: true,
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
			}
		});
 }
function exportExcelSubmit(formName)
{
    $("#"+formName).append('<input type="hidden" name="export-excel" value="1" />');
    $("#"+formName).submit();
}
$('.Ques_left,.quest').click(function(){
		var text_val = $.trim($('.text_tooltip').html());
		if(text_val != '')
			$(".help_text").slideToggle();
});
function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '' : '<br>';

    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function openCarrierDropDown()
{
	if($(".carrier-list").is(':hidden')) {
		$('.carrier-list').addClass('open');
		$('.carrier-list').show();
	}
	else
	{
		$('.carrier-list').removeClass('open');
		$('.carrier-list').hide();
	}
}
function setCarrierValue(text_val)
{
	$('#ship_carrier').val(text_val);
	if(text_val == '')
		text_val = 'Choose carrier';
	$('.selected-text').html(text_val);
	openCarrierDropDown();
}
$("html:not(.ms-container)").click(function(){
	$('.carrier-list').removeClass('open');
	$(".carrier-list").hide();
});

function searchDish(evt)
{
	var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	 if(keyCode == 13)
	 {
	 	document.getElementById("search_form").submit();
		return true;
	 } 
	
}
function setSocialHidden(obj) {
	var socialType = $(obj).attr('title');
	if($(obj).hasClass('gray_but')) {
		$(obj).removeClass('gray_but');
		if($.trim(socialType)=='Facebook') {
			$('#fb_hidden').val(1);
			$(obj).addClass('blue_but');
		} else {
			$('#twitter_hidden').val(1);
			$(obj).addClass('twit_blue_but');
		}
	} else {
		$(obj).addClass('gray_but');
		$(obj).removeClass('blue_but');
		$(obj).removeClass('twit_blue_but');
		if($.trim(socialType)=='Facebook') {
			$('#fb_hidden').val(0);
		} else {
			$('#twitter_hidden').val(0);
		}
	}
}
function facebookScriptConnect(myapp_id) {
    var button;
    window.fbAsyncInit = function () {
        FB.init({
            appId: myapp_id,
            status: true,
            cookie: true,
            xfbml: true,
            oauth: true
        });
    	
	};
    (function () {
        var e = document.createElement('script');
        e.async = true;
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
    }());
}
function postToFacebook() { 
	function updateScriptButton(response) {
			    if (response.authResponse) {
						postFB();
                } else {
                    FB.login(function (response) {
                        if (response.authResponse) {
								postFB();
                        } else {
							var twtr_check_hidden = parseInt($('#twitter_hidden').val());
							  if(twtr_check_hidden) {
							  	postToTwitter();
							  } else {
								$('#post_count').val(1);
								$('#upload_form').submit();
							  }
						}
                    }, {
                        scope: 'email,user_birthday,status_update,publish_stream,user_about_me'
                    });
                }
		}
        FB.getLoginStatus(updateScriptButton); 
	
	
}
function postFB(){
	var params = {};
	
	var post_message = 'Dish Name:'+$('#dishName').val()+'\r\n';
		if($('#caption').val()!='') {
			post_message = post_message+'Caption:'+$('#caption').val()+'\r\n';
		}
		//post_message = post_message+'Dish URL:'+actionPath+$('#hidden_category_name').val()+'/'+$('#hidden_sub_category_name').val()+'/'+$('#dish_url').val();
		post_message = post_message+'Dish URL:'+actionPath+$('#dish_url').val();
		
		
	var image_url = '';
	if (window.location.hostname != '172.21.4.100') {
		var image_url = 'http://cookoo.s3.amazonaws.com/dish/'+$('#dish_image_name').val();
		//alert('----------------'+image_url);
	} else {
		image_url = 'http://d33lfq67o08fmx.cloudfront.net/dish/800_1370317281.jpeg';
	}
	var dishName = $('#dishName').val();
	var dishCaption = $('#caption').val();
	var dishUrl = actionPath+$('#dish_url').val();
	var wallPost = {
		message : post_message,
		picture : image_url,
		name	: dishName,
		caption : 'http://www.cookoo.co/',
		link: 'http://www.cookoo.co/'
		//description : dishUrl
	};
	//alert('--------------------->'+wallPost);
	FB.api('/me/feed', 'post',wallPost, function(response) {
          if (!response || response.error) {
			//alert('Opps some error occurs!');
			alert('Opps some error occurs!');
			//console.log(response.error);
          } else {
           // alert('Comment has been posted in your facebook timeline.');
			//console.log('Comment has been posted in your facebook timeline.');
          }
		  var twtr_check_hidden = parseInt($('#twitter_hidden').val());
		  if(twtr_check_hidden) {
		  	postToTwitter();
		  } else {
			$('#post_count').val(1);
			$('#upload_form').submit();
		  }
        });
		
	
}
function postToTwitter() {
	var post_message = 'Dish Name:'+$('#dishName').val()+"\r\n";
		if($('#caption').val()!='') {
			post_message = post_message+'Caption:'+$('#caption').val()+"\r\n";
		}
		//post_message = post_message+'Dish URL:'+actionPath+$('#hidden_category_name').val()+'/'+$('#hidden_sub_category_name').val()+'/'+$('#dish_url').val();
		post_message = post_message+'Dish URL:'+actionPath+$('#dish_url').val();
		twttr.ready(function (twttr) {
			twttr.events.bind('tweet', function (event) {

              alert('testing');

            });
		});
         window.open("https://twitter.com/intent/tweet?text="+post_message,"Twitter","status = 1, left = 430, top = 270, height = 350, width = 420, resizable = 0,scrollbars=1");
	$('#post_count').val(1);
	$('#upload_form').submit();	

}

function isAlphaNumberKey(evt) {
	evt = evt || window.event;
   var charCode = evt.which || evt.keyCode;
   var charStr = String.fromCharCode(charCode);
   if (/[a-zA-Z0-9_]/i.test(charStr)) {
       return true;
   }
   else if ((evt.keyCode == 8) || (evt.keyCode == 46) || (evt.keyCode == 37) || (evt.keyCode == 39) || (evt.keyCode == 9)){
   // backspace, delete, left arrow, right arrow, tab keys
    return true;
	}
   else{
   		return false;
   }
}
function isAlphaNumberKey_Space(evt) {
	evt = evt || window.event;	
   var charCode = evt.which || evt.keyCode;
   var charStr = String.fromCharCode(charCode);
    if (/[a-zA-Z0-9_]/i.test(charStr)) {
       return true;
   }
   else if ((evt.keyCode == 8) || (evt.keyCode == 46) || (evt.keyCode == 37) || (evt.keyCode == 39 && evt.which==0) || (evt.keyCode == 9) || (charCode == 32) ){
   // backspace, delete, left arrow, right arrow, tab keys
    return true;
	}
   else{
   		return false;
   }
}
function isNumberKey_Hashtag(evt) {	
	var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	evt = evt || window.event;	
	var charCode = evt.which || evt.keyCode;
	var charStr = String.fromCharCode(charCode);
	if (/[a-zA-Z0-9&!_]/i.test(charStr)) {
       return true;
   }
	else if ((keyCode == 8)  || (keyCode == 39) || (keyCode == 9)) return true;
	else 
		return false;
}
function isNumberKey_Goal(evt) {	
	var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	evt = evt || window.event;	
	var charCode = evt.which || evt.keyCode;
	var charStr = String.fromCharCode(charCode);
	if (/[a-zA-Z0-9&!_]/i.test(charStr)) {
       return true;
   }
	else if ((keyCode == 8)  || (keyCode == 39) || (keyCode == 9)) return true;
	else 
		return false;
}
function checkAllDelete(formName)
{
	var ch = false;
	var frm = 	document.getElementById(formName);
	if(frm.checkAll.checked == true)
		ch = true;			
	else
		ch = false;
	
	$("input[name='checkdelete[]']").each(function(){
		if($(this).attr('disabled') != 'disabled'){			
			$(this).attr('checked',ch);
		}
	});
}
function checkAllRecords(formName)
{
	var ch = false;
	var frm = 	document.getElementById(formName);
	if(frm.checkAll.checked == true)
		ch = true;			
	else
		ch = false;
	
	$("input[name='checkedrecords[]']").each(function(){
		if($(this).attr('disabled') != 'disabled'){			
			$(this).attr('checked',ch);
		}
	});
}

/*---------------VERSIONS-------------------*/
rnd=Math.random();
var field_focus = '';
function clear_app(id){
	var frm = document.getElementById(id);
	for (var i = 0; i < frm.elements.length; i++) {
		if(frm.elements[i].type=='text') {
			 frm.elements[i].value = '';
		}
	   if (frm.elements[i].name.indexOf('[]') > 0)
 	 	  frm.elements[i].checked = false;
	 }
	if (frm.titlecheckbox)
		frm.titlecheckbox.checked = false;
}

function Show(idname){
	if(document.getElementById(idname).style.display == 'none')
	{
		document.getElementById(idname).style.display = 'block';
		$("#device_type_msg_container").hide();
		$("#status_msg_container").hide();
		$("#device_version_msg_container").hide();
		$("#device_build_msg_container").hide();
		$("#device_type").val('0');
		$("#status").val('0');
		return true;
	}
}

function validateAppStatus(id)
{
	var device_version	= 	$("#device_type"+id),
	status				=	$("#status"+id),
	version				=	$("#device_version"+id),
	build				=	$("#device_build"+id),
	field_focus			=	'',
	allContainerArray 	=	new Array('device_type'+id+'_msg','status'+id+'_msg','device_version'+id+'_msg','device_build'+id+'_msg','displayMsg');
	allFields 			=	$([]).add(device_version).add(status).add(version).add(build);
	allFields.removeClass('ui-state-error'); //Remove error class if any
	hideDomElement(allContainerArray); //Hide all error message container
	$("#errorFlag").val(0);
	
	if(device_version.length)
		checkBlank(device_version,"Device Name", "device_type"+id+"_msg");
	checkBlank(status,"App Type", "status"+id+"_msg");
	checkBlank(version,"Version", "device_version"+id+"_msg");
	checkBlank(build,"Build", "device_build"+id+"_msg");
		
	if ( $("#errorFlag").val() == 1) {
		checkandSetFieldFocus();
		return false;
	}
	else {	
		return true;
	}
}

function Cancel(idname){
	document.getElementById(idname).style.display = 'none';
}

function checkBlank(element,text,elmError) {
	if($.trim($(element).val()) == "") {
		updateTips(" * " + text + " is required.", elmError, element);
		return false;
	} else {	
		if($(field_focus).attr('id') == $(element).attr('id')){
			field_focus = '';
		}
		return true;
	}
}

function hideDomElement(elmArray) {
	$.each(elmArray, function() {
		container = this + '_container';
		$('#' + container).hide();
	 });
}

function checkandSetFieldFocus()
{
	if(field_focus != '')
		$(field_focus).focus();
}

function updateTips(t,elmError,element) {
	
	var container = elmError + '_container';
	if ( $('#errElementId').val()) {
	 	elmError = $('#errElementId').val();
	}
	$("#" + elmError).html(t);
	$("#" + container).show();
	if(field_focus == '')
		field_focus = element;
	$("#errorFlag").val(1);
}
function isFloatNumber(evt,ref,l){
		var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	var number = $(ref).val();
	if(keyCode == 46){ //allow only single dot
		if (number.match(/\./)) {
			return false
		}
	}
	if ((keyCode == 8) || (keyCode == 46) || (keyCode == 37)  || (keyCode == 9) || (keyCode == 13)) return true;
	 if(number && number !=''){
		var fv = number.split('.');
		if(fv[0]== '' || (fv[0] && fv[0].length < l )){
			
		}else if(keyCode == 46){
		 return true;
		}
		else if(fv[1]){
		}
		else{
			return false
		}
	} 
	// backspace, delete, left arrow, right arrow, tab keys
	if ((keyCode < 48) || (keyCode > 57) || (keyCode == 39)) return false;
	return true;
}
function isTiltCoinField(evt,ref) {
	var keyCode = (evt.which?evt.which:(evt.keyCode?evt.keyCode:0))
	// backspace, delete, left arrow, right arrow, tab keys
	if ((keyCode == 8) || (keyCode == 46) || (keyCode == 37) || (keyCode == 39) || (keyCode == 9)) return true;
	var number = $(ref).val();
	if(number == '' || (number && number.length < 8)) ;
	else return false;
	if(number == '' && keyCode == 48) return false;
	if ((keyCode < 48) || (keyCode > 57)) return false;
	return true;
}
