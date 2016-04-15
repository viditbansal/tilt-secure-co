(function(e){function t(e,t){var n=new google.maps.Map(e,t);var r=new google.maps.Marker({position:new google.maps.LatLng(54.19335,-3.92695),map:n,title:"Drag Me",draggable:t.draggable});return{map:n,marker:r,circle:null,location:r.position,radius:t.radius,locationName:t.locationName,addressComponents:{formatted_address:null,addressLine1:null,addressLine2:null,streetName:null,streetNumber:null,city:null,state:null,stateOrProvince:null},settings:t.settings,domContainer:e,geodecoder:new google.maps.Geocoder}}function r(e){return i(e)!=undefined}function i(t){return e(t).data("locationpicker")}function s(e,t){if(!e)return;var r=n.locationFromLatLng(t.location);if(e.latitudeInput){e.latitudeInput.val(r.latitude)}if(e.longitudeInput){e.longitudeInput.val(r.longitude)}if(e.radiusInput){e.radiusInput.val(t.radius)}if(e.locationNameInput){e.locationNameInput.val(t.locationName)}}function o(t,r){if(t){if(t.radiusInput){t.radiusInput.on("change",function(){r.radius=e(this).val();n.setPosition(r,r.location,function(e){e.settings.onchanged.apply(r.domContainer,[n.locationFromLatLng(e.location),e.radius,false])})})}if(t.locationNameInput&&r.settings.enableAutocomplete){r.autocomplete=new google.maps.places.Autocomplete(t.locationNameInput.get(0));google.maps.event.addListener(r.autocomplete,"place_changed",function(){var e=r.autocomplete.getPlace();if(!e.geometry){r.settings.onlocationnotfound(e.name);return}n.setPosition(r,e.geometry.location,function(e){s(t,e);e.settings.onchanged.apply(r.domContainer,[n.locationFromLatLng(e.location),e.radius,false])})})}if(t.latitudeInput){t.latitudeInput.on("change",function(){n.setPosition(r,new google.maps.LatLng(e(this).val(),r.location.lng()),function(e){e.settings.onchanged.apply(r.domContainer,[n.locationFromLatLng(e.location),e.radius,false])})})}if(t.longitudeInput){t.longitudeInput.on("change",function(){n.setPosition(r,new google.maps.LatLng(r.location.lat(),e(this).val()),function(e){e.settings.onchanged.apply(r.domContainer,[n.locationFromLatLng(e.location),e.radius,false])})})}}}var n={drawCircle:function(t,n,r,i){if(t.circle!=null){t.circle.setMap(null)}if(r>0){r*=1;i=e.extend({strokeColor:"#0000FF",strokeOpacity:.35,strokeWeight:2,fillColor:"#0000FF",fillOpacity:.2},i);i.map=t.map;i.radius=r;i.center=n;t.circle=new google.maps.Circle(i);return t.circle}return null},setPosition:function(e,t,r){e.location=t;e.marker.setPosition(t);e.map.panTo(t);this.drawCircle(e,t,e.radius,{});if(e.settings.enableReverseGeocode){e.geodecoder.geocode({latLng:e.location},function(t,i){if(i==google.maps.GeocoderStatus.OK&&t.length>0){e.locationName=t[0].formatted_address;e.addressComponents=n.address_component_from_google_geocode(t[0].address_components)}if(r){r.call(this,e)}})}else{if(r){r.call(this,e)}}},locationFromLatLng:function(e){return{latitude:e.lat(),longitude:e.lng()}},address_component_from_google_geocode:function(e){var t={};for(var n=e.length-1;n>=0;n--){var r=e[n];if(r.types.indexOf("postal_code")>=0){t.postalCode=r.short_name}else if(r.types.indexOf("street_number")>=0){t.streetNumber=r.short_name}else if(r.types.indexOf("route")>=0){t.streetName=r.short_name}else if(r.types.indexOf("sublocality")>=0){t.city=r.short_name}else if(r.types.indexOf("administrative_area_level_1")>=0){t.stateOrProvince=r.short_name}else if(r.types.indexOf("country")>=0){t.country=r.short_name}}t.addressLine1=[t.streetNumber,t.streetName].join(" ").trim();t.addressLine2="";return t}};e.fn.locationpicker=function(u,a){if(typeof u=="string"){var f=this.get(0);if(!r(f))return;var l=i(f);switch(u){case"location":if(a==undefined){var c=n.locationFromLatLng(l.location);c.radius=l.radius;c.name=l.locationName;return c}else{if(a.radius){l.radius=a.radius}n.setPosition(l,new google.maps.LatLng(a.latitude,a.longitude),function(e){s(e.settings.inputBinding,e)})}break;case"subscribe":if(a==undefined){return null}else{var h=a.event;var p=a.callback;if(!h||!p){console.error('LocationPicker: Invalid arguments for method "subscribe"');return null}google.maps.event.addListener(l.map,h,p)}break;case"map":if(a==undefined){var d=n.locationFromLatLng(l.location);d.formattedAddress=l.locationName;d.addressComponents=l.addressComponents;return{map:l.map,marker:l.marker,location:d}}else{return null}}return null}return this.each(function(){var i=e(this);if(r(this))return;var a=e.extend({},e.fn.locationpicker.defaults,u);var f=new t(this,{zoom:a.zoom,center:new google.maps.LatLng(a.location.latitude,a.location.longitude),mapTypeId:google.maps.MapTypeId.ROADMAP,mapTypeControl:false,disableDoubleClickZoom:false,scrollwheel:a.scrollwheel,streetViewControl:false,radius:a.radius,locationName:a.locationName,settings:a,draggable:a.draggable});i.data("locationpicker",f);google.maps.event.addListener(f.marker,"dragend",function(e){n.setPosition(f,f.marker.position,function(e){var t=n.locationFromLatLng(f.location);e.settings.onchanged.apply(f.domContainer,[t,e.radius,true]);s(f.settings.inputBinding,f)})});n.setPosition(f,new google.maps.LatLng(a.location.latitude,a.location.longitude),function(e){s(a.inputBinding,f);e.settings.oninitialized(i);var t=n.locationFromLatLng(f.location);a.onchanged.apply(f.domContainer,[t,e.radius,false])});o(a.inputBinding,f)})};e.fn.locationpicker.defaults={location:{latitude:40.7324319,longitude:-73.82480799999996},locationName:"",radius:500,zoom:15,scrollwheel:true,inputBinding:{latitudeInput:null,longitudeInput:null,radiusInput:null,locationNameInput:null},enableAutocomplete:false,enableReverseGeocode:true,draggable:true,onchanged:function(e,t,n){},onlocationnotfound:function(e){},oninitialized:function(e){}}})(jQuery)