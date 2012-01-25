<?php
/*	
*	Weever Geotagger for K2
*	(c) 2012 Weever Apps Inc. <http://www.weeverapps.com/>
*
*	Author: 	Robert Gerald Porter <rob@weeverapps.com>
*	Version: 	0.2
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
	
	

	jQuery('#wmx-marker-change-icon').click(function(event){
		event.preventDefault();
		SqueezeBox.initialize();
		SqueezeBox.fromElement(this, {
			handler: 'iframe',
			url: K2BasePath+'index.php?option=com_k2&view=media&type=image&tmpl=component&fieldID=wmx-marker-icon',
			size: {x: 800, y: 434}
		});
	});
	
	/*
	jQuery('#wmx-kml-media').click(function(event){
		event.preventDefault();
		SqueezeBox.initialize();
		SqueezeBox.fromElement(this, {
			handler: 'iframe',
			url: K2BasePath+'index.php?option=com_k2&view=media&type=image&tmpl=component&fieldID=wmx-kml-url',
			size: {x: 800, y: 434}
		});
	});*/

});


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