<?php
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

defined('_JEXEC') or die;

JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'k2plugin.php');


class plgK2WeeverMapsK2 extends K2Plugin {

	public 	$pluginName = 'weevermapsk2';
	public 	$pluginNameHumanReadable = 'Weever Maps Geotagger for K2';
	public  $pluginVersion = "0.1";

	public function __construct(&$subject, $params) 
	{
		
		JPlugin::loadLanguage('plg_k2_'.$this->pluginName, JPATH_ADMINISTRATOR);
		$document = &JFactory::getDocument();
		$document->addScript( 'http://maps.googleapis.com/maps/api/js?sensor=false' );
		$document->addScript( '/media'.DS.'plg_weevermapsk2'.DS.'js'.DS.'markerwithlabel.js' );
		$document->addScript( '/media'.DS.'plg_weevermapsk2'.DS.'js'.DS.'wmx.js' );
		$document->addScript( '/media'.DS.'plg_weevermapsk2'.DS.'js'.DS.'jq.ready.js' );

		$document->addStyleSheet(DS.'media'.DS.'plg_weevermapsk2'.DS.'css'.DS.'wmx.css', 'text/css', null, array());
		$document->addStyleSheet(DS.'media'.DS.'plg_weevermapsk2'.DS.'css'.DS.'jquery.ui.css', 'text/css', null, array());
		
		echo "<div id='wmx-dialog' title='&lt;img id=&quot;wmx-logo&quot; src=&quot;/media/plg_weevermapsk2/images/weever.png&quot;&gt; ".$this->pluginNameHumanReadable." v".$this->pluginVersion."'>
				<div id='wmx-address'>
					<input type='text' id='wmx-address-input' placeholder='Type your address here' value='Type in an address or click a location on the map' />
					<button id='wmx-address-geocode'>Find</button> <button id='wmx-address-add-marker'>+ Add Marker</button>
				</div>
				<div id='wmx-map'>This will be a map.</div>
				<div id='wmx-map-console'>
					<div id='wmx-instructions'><span><b>Instructions:</b> Click a location to add a marker; double click a marker to edit its properties.</span></div>
					<div id='wmx-console-options'>
						<div id='wmx-latlong-container' class='wmx-console-container-widget'>
							<div id='wmx-latlong-title' class='wmx-title'>GPS Position</div>
							<div id='wmx-latitude'>
								<div class='wmx-hover-label'><label for='wmx-lat-hover'>Latitude:</label></div>
								<input type='text' id='wmx-lat-hover' />
							</div>
							<div id='wmx-longitude'>
								<div class='wmx-hover-label'><label for='wmx-long-hover'>Longitude:</label></div>
								<input type='text' id='wmx-long-hover' />
							</div>
							<div id='wmx-latlong-button-container'>
								
								<button class='wmx-latlong-button' id='wmx-latlong-location'>Show Location</button>
								<button class='wmx-latlong-button' id='wmx-latlong-add-marker'>+ Add Marker</button>
							</div>
						</div>
						<div id='wmx-marker-container' class='wmx-console-container-widget'>
							<div id='wmx-right-marker'>				
								<img src='http://weeverapp.com/media/sprites/default-marker.png' id='wmx-marker-image' /><br />
							</div>
							<div id='wmx-right-marker-buttons'>
								<input type='hidden' name='wmx-marker-url' id='wmx-marker-url' value='' />
								<button id='wmx-select-marker'>Choose Marker Icon...</button><br />
							</div>
						</div>
						<div id='wmx-options-container' class='wmx-console-container-widget'>
							<div id='wmx-options-title' class='wmx-title'>Other Options</div>
							<button id='wmx-gps-location'>Show Your Location</button> 
							<button id='wmx-add-kml'>Add KML File</button> 
						</div>
					</div>
				</div>
				
			</div>
			
			<div id='wmx-marker-dialog' title='Edit Marker'>
				
				<div id='wmx-marker-dialog-options-container'>
					<div class='wmx-title'>Marker Options</div>
					<label class='wmx-marker-button' for='wmx-marker-label-input'>Label: </label>
					<input id='wmx-marker-label-input' type='text' class='wmx-marker-button'  placeholder='Label' /><br />
					<input type='button' id='wmx-marker-change-icon' class='wmx-marker-button' value='Change image...' /><br />
					<input type='hidden' id='wmx-marker-icon' name='wmx-marker-icon' value='' />
					<input type='button' id='wmx-marker-delete' class='wmx-marker-button' value='Delete Marker' />
				</div>
				
			</div>
			
			<div id='wmx-kml-dialog' title='Add KML File'>
				
				<div>
					<input type='text' id='wmx-kml-url' value='' placeholder='http://' /><br />
					<button id='wmx-kml-media'>File From Server...</button>
				</div>
				
			</div>
			
			<div id='wmx-close-dialog' title='Are you sure?'>
				
				<div>
					Are you sure you want to close this window? Your changes will <b>not</b> be saved.
				</div>
				
			</div>
			";
		
	
		parent::__construct($subject, $params);
		
	}

	
	public function onAfterK2Save(&$item, $isNew) {
	
		$mainframe = &JFactory::getApplication();
		
		$geoData = json_decode($item->plugins);
		

		//, longitude, altitude, address, kml, marker
			$query = " 	INSERT  ".
					"	INTO	#__weever_maps ".
					"	(component_id, component, location) ".
					"	VALUES ('".$item->id."', 'com_k2', GeomFromText(' POINT(".$geoData->weevermapsk2latitude_item." ".$geoData->weevermapsk2longitude_item.") '))";

		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		
		$result = $db->loadObject();
		
		
		/*
		
		// search code for distance...
		
		SELECT *,  glength( linestringfromwkb( linestring( GeomFromText('POINT(45.123 54.262)'), location ) ) ) as 'distance'
		FROM
		jos_weever_maps
		ORDER BY
		distance
		
		*/
	
	}

	
} 

