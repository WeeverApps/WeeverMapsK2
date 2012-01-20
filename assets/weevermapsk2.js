/*	
*	Weever Maps for K2
*	(c) 2012 Weever Apps Inc. <http://www.weeverapps.com/>
*
*	Author: 	Robert Gerald Porter (rob@weeverapps.com)
*	Version: 	0.1
*   License: 	GPL v3.0
*
*   This extension is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This extension is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details <http://www.gnu.org/licenses/>.
*
*/


var wmx = wmx || {};
wmx.pin = wmx.pin || null;

jQuery(document).ready(function(){ 
 
	jQuery('#wmx-select-marker').click(function(event){
					event.preventDefault();
					SqueezeBox.initialize();
					SqueezeBox.fromElement(this, {
						handler: 'iframe',
						url: K2BasePath+'index.php?option=com_k2&view=media&type=image&tmpl=component&fieldID=wmx-marker-url',
						size: {x: 800, y: 434}
					});
				});
				
	jQuery('#wmx-address-geocode').click(function(event){
	
		wmx.address = jQuery('input#wmx-address-input').val();
		wmx.getGeocode(wmx.address, function() {
		
			wmx.mapCenter();
		
		});
	
	});
	
	jQuery('#wmx-latlong-add-marker').click(function(event){
	
		wmx.position = new google.maps.LatLng( 
			jQuery('input#wmx-lat-hover').val(), 
			jQuery('input#wmx-long-hover').val() 
		);
	
		wmx.map.setCenter(wmx.position);
		wmx.addMarker(wmx.position);
		wmx.pin.setMap(null);
	
	});
	
	jQuery('#wmx-latlong-location').click(function(event){
	
		wmx.position = new google.maps.LatLng( 
			jQuery('input#wmx-lat-hover').val(), 
			jQuery('input#wmx-long-hover').val() 
		);

		wmx.mapCenter();
	
	});

	jQuery('#wmx-address-add-marker').click(function(event){
	
		var address = jQuery('input#wmx-address-input').val();
		
		if(address == wmx.address)
		{
		
			wmx.map.setCenter(wmx.position);
			wmx.addMarker(wmx.position);
			wmx.pin.setMap(null);
		
		}
		else 
		{
			
			wmx.getGeocode(wmx.address, function() {
			
				wmx.map.setCenter(wmx.position);
				wmx.addMarker(wmx.position);
				wmx.pin.setMap(null);
			
			});
			
		}
		
	});


	jQuery("<button class='wmx-latlong'>GeoTag</button>").insertAfter("#featured1-lbl");
	
	wmx.mapImages = {
		icon: new google.maps.MarkerImage(
		                'http://weeverapp.com/media/sprites/default-marker.png',
		                new google.maps.Size(32, 37),
		                new google.maps.Point(0,0),
		                new google.maps.Point(16, 37),
		                new google.maps.Size(64, 37)
		              ),              
		selected: new google.maps.MarkerImage(
		                'http://weeverapp.com/media/sprites/default-marker.png',
		                new google.maps.Size(32, 37),
		                new google.maps.Point(32,0),
		                new google.maps.Point(16, 37),
		                new google.maps.Size(64, 37)
		              ),
		pin: new google.maps.MarkerImage(
		                '/media/plg_weevermapsk2/images/point.png',
		                new google.maps.Size(32, 31),
		                new google.maps.Point(0,0),
		                new google.maps.Point(16, 31)
		              )
		              
	}
	
	jQuery('.wmx-latlong').click(function(e) {
	
		e.preventDefault();
	
		jQuery("#wmx-dialog").dialog({
			modal: true, 
			resizeable: false,
			width: 'auto',
			height: 'auto',
			open: function(e, ui) {
			
			//	 = jQuery('#wmx-map').data('gmap');
			
				
				var myOptions = {
				          center: new google.maps.LatLng(-34.397, 150.644),
				          zoom: 8,
				          mapTypeId: google.maps.MapTypeId.ROADMAP
				        };
				
				wmx.map = new google.maps.Map(document.getElementById("wmx-map"),
				            myOptions);
				
				google.maps.event.addListener(wmx.map, 'mousemove', function(event) {
				
					document.getElementById('wmx-lat-hover').value = event.latLng.lat();
					document.getElementById('wmx-long-hover').value = event.latLng.lng();
				
				}); 
				
				google.maps.event.addListener(wmx.map, 'click', function(event) {
				
					wmx.addMarker(event.latLng);
					
				}); 
				
			
			}
			}); 
	
	
	});
	
	
});


wmx.addMarker = function(position) {

	var marker = new google.maps.Marker({
	    position: position, 
	    map: wmx.map,
	    icon: wmx.mapImages.icon,
	    draggable:true
	});
	
	
	google.maps.event.addListener(
	    marker,
	    'drag',
	    function() {
	    	marker.setIcon(wmx.mapImages.selected);
	    }
	);
	
	google.maps.event.addListener(
	    marker,
	    'dragend',
	    function() {
	    	marker.setIcon(wmx.mapImages.icon);
	        document.getElementById('latlongclicked').value = marker.position;
	    }
	);
	
	google.maps.event.addListener(
	    marker,
	    'dblclick',
	    function() {
	        marker.setMap(null);
	    }
	);
	
}


wmx.getGeocode = function(address, callback) {
	
   	var geocoder = new google.maps.Geocoder();
   	var callback = callback;
   	
   	geocoder.geocode({'address': address}, function(results, status) {
   		
   		if(status == google.maps.GeocoderStatus.OK) {
   		
   			wmx.position = results[0].geometry.location;

   			callback();
   			
   		} else {
   		
   			console.log("Geocoding was not successful for the following reasons: " + status);
   			
   			if(status == "OVER_QUERY_LIMIT")
   			{
   				alert("Error: You have somehow reached the limit for geocoding addresses into coordinates. Wait a few seconds and try again.");
   				
   			}
   				
   			if(status == "ZERO_RESULTS")
   			{
   				alert("Error: No results for the address: "+address);
   			}
   		
   		}
   			
   	});
}
 
 
wmx.mapCenter = function() {

	wmx.map.setCenter(wmx.position);
	
	wmx.pin = new google.maps.Marker({
	    position: wmx.position, 
	    map: wmx.map,
	    icon: wmx.mapImages.pin,
	    draggable:false
	});

}

