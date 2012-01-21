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
		
		if(wmx.pin instanceof google.maps.Marker)
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
			
			if(wmx.pin instanceof google.maps.Marker)
				wmx.pin.setMap(null);
		
		}
		else 
		{
			
			wmx.getGeocode(wmx.address, function() {
			
				wmx.map.setCenter(wmx.position);
				wmx.addMarker(wmx.position);
				
				if(wmx.pin instanceof google.maps.Marker)
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
	
	jQuery('#wmx-marker-label-input').keyup(function(e) {
	
		if( jQuery('#wmx-marker-label-input').val() == "" ) {
		
			wmx.selectedMarker.set('labelContent', "");
			wmx.selectedMarker.set('labelStyle', {opacity: 0});
		
		} else {
		
			var hash = Math.floor((wmx.selectedMarker.position.lat() + wmx.selectedMarker.position.lng()) * 10000);
			
			wmx.selectedMarker.set('labelContent', jQuery('#wmx-marker-label-input').val());
			wmx.selectedMarker.set('labelId', 'wmx-label-'+hash);
			
			var point = ((jQuery('#wmx-label-'+hash).width() + 10) / 2) - 1;
			
			wmx.selectedMarker.set('labelAnchor', new google.maps.Point(point, 0));
			wmx.selectedMarker.set('labelClass', 'wmx-label');
			wmx.selectedMarker.set('labelStyle', {opacity: 0.75});
		
		}
	
	});
	
	jQuery('#wmx-marker-done').click(function(e) {
	
		jQuery('#wmx-marker-dialog').dialog('close');
	
	});
	
	jQuery('#wmx-marker-delete').click(function(e) {
	
		wmx.selectedMarker.setMap(null);
		jQuery('#wmx-marker-dialog').dialog('close');
	
	});
	
	jQuery('.wmx-latlong').click(function(e) {
	
		e.preventDefault();
	
		jQuery("#wmx-dialog").dialog({
			modal: true, 
			resizable: false,
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



