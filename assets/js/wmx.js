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

wmx.addMarker = function(position) {

	var marker = new MarkerWithLabel({
	       position: position,
	       draggable: true,
	       map: wmx.map,
	       icon: wmx.mapImages.icon
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
	        jQuery('#wmx-long-hover').val( position.lng() );
	        jQuery('#wmx-lat-hover').val( position.lat() );
	    }
	);
	
	google.maps.event.addListener(
	    marker,
	    'dblclick',
	    function() {
	        //marker.setMap(null);
	        jQuery('#wmx-long-hover').val( position.lng() );
	        jQuery('#wmx-lat-hover').val( position.lat() );
	        
	        wmx.selectedMarker = marker;
	        
	        jQuery('#wmx-marker-dialog').dialog({
	        	modal: true, 
	        	resizable: false,
	        	width: 'auto',
	        	height: 'auto',
	        	open: function(e, ui) {
	        	
	        		jQuery('#wmx-marker-label-input').val( marker.get('labelContent') );
	        	
	        	}
	        		        	
	        });
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
	
	if(wmx.pin instanceof google.maps.Marker)
		wmx.pin.setMap(null);
	
	wmx.pin = new google.maps.Marker({
	    position: wmx.position, 
	    map: wmx.map,
	    icon: wmx.mapImages.pin,
	    draggable:false
	});

}


wmx.setMarkerIconDefault = function(el) {

	var image = el.value;
	
	wmx.mapImages.icon = new google.maps.MarkerImage(
	                image,
	                new google.maps.Size(32, 37),
	                new google.maps.Point(0,0),
	                new google.maps.Point(16, 37),
	                new google.maps.Size(64, 37)
	              );
	              
	jQuery('#wmx-marker-image').attr('src', image); 

}


wmx.setMarkerIcon = function(el) {

	wmx.selectedMarker.setIcon(	
		new google.maps.MarkerImage(
	        el.value,
	        new google.maps.Size(32, 37),
	        new google.maps.Point(0,0),
	        new google.maps.Point(16, 37),
	        new google.maps.Size(64, 37)
	   )
	);

};


// override the K2 media update function

var _elFinderUpdate = elFinderUpdate;

var elFinderUpdate = function(fieldID, value) {
	
	if(fieldID == 'wmx-marker-url') {
	
		var image = "/"+value;
	
		wmx.mapImages.icon = new google.maps.MarkerImage(
		                image,
		                new google.maps.Size(32, 37),
		                new google.maps.Point(0,0),
		                new google.maps.Point(16, 37),
		                new google.maps.Size(64, 37)
		              );
		              
		jQuery('#wmx-marker-image').attr('src', image); 
		
		_elFinderUpdate(fieldID, value);
	
	} else if (fieldID == 'wmx-marker-icon') {
	
		wmx.selectedMarker.setIcon(	
			new google.maps.MarkerImage(
		        "/"+value,
		        new google.maps.Size(32, 37),
		        new google.maps.Point(0,0),
		        new google.maps.Point(16, 37),
		        new google.maps.Size(64, 37)
		   )
		);
		
		_elFinderUpdate(fieldID, value);
	
	} else {
	
		_elFinderUpdate(fieldID, value);
	
	}

};
