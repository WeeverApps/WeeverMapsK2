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

	jQuery("<button class='wmx-latlong'>GeoTag</button>").insertAfter("#featured1-lbl");
	
	mapImages = {
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
		              )
		              
	}
	
	jQuery('.wmx-latlong').click(function(e) {
	
		e.preventDefault();
	
		jQuery("#wmx-dialog").dialog({
			modal: true, 
			width: 'auto',
			height: 'auto',
			open: function(e, ui) {
			
			//	 = jQuery('#wmx-map').data('gmap');
			
				
				var myOptions = {
				          center: new google.maps.LatLng(-34.397, 150.644),
				          zoom: 8,
				          mapTypeId: google.maps.MapTypeId.ROADMAP
				        };
				
				var map = new google.maps.Map(document.getElementById("wmx-map"),
				            myOptions);
				
				google.maps.event.addListener(map, 'mousemove', function(event) {
				
					document.getElementById('wmx-lat-hover').value = event.latLng.lat();
					document.getElementById('wmx-long-hover').value = event.latLng.lng();
				
				}); 
				
				google.maps.event.addListener(map, 'click', function(event) {

					//document.getElementById('latlongclicked').value = event.latLng;

					var marker = new google.maps.Marker({
					    position: event.latLng, 
					    map: map,
					    icon: mapImages.icon,
					    draggable:true
					});
					
					
					google.maps.event.addListener(
					    marker,
					    'drag',
					    function() {
					    	marker.setIcon(mapImages.selected);
					    }
					);
					
					google.maps.event.addListener(
					    marker,
					    'dragend',
					    function() {
					    	marker.setIcon(mapImages.icon);
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
					
					
					
					
				
				}); 
				
				
				
		
				
				
			
			}
			}); 
	
	
	});
	
	
});

