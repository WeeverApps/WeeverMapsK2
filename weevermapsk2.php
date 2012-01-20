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
		$document->addScript( '/media'.DS.'plg_weevermapsk2'.DS.'js'.DS.'wmx.js' );
		$document->addScript( '/media'.DS.'plg_weevermapsk2'.DS.'js'.DS.'jq.ready.js' );

		$document->addStyleSheet(DS.'media'.DS.'plg_weevermapsk2'.DS.'css'.DS.'weevermapsk2.css', 'text/css', null, array());
		$document->addStyleSheet(DS.'media'.DS.'plg_weevermapsk2'.DS.'css'.DS.'jquery.ui.css', 'text/css', null, array());
		
		echo "<div id='wmx-dialog' title='&lt;img id=&quot;wmx-logo&quot; src=&quot;/media/plg_weevermapsk2/images/weever.png&quot;&gt; ".$this->pluginNameHumanReadable." v".$this->pluginVersion."'>
				<div id='wmx-address'>
					<input type='text' id='wmx-address-input' placeholder='Type your address here' />
					<button id='wmx-address-geocode'>Find</button> <button id='wmx-address-add-marker'>+ Add Marker</button>
				</div>
				<div id='wmx-map'>This will be a map.</div>
				<div id='wmx-map-console'>
					<div id='wmx-latlong-container'>
						<div id='wmx-latlong-title'>GPS Position</div>
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
					<div id='wmx-marker-container'>
						<div id='wmx-right-marker'>				
							<img src='http://weeverapp.com/media/sprites/default-marker.png' id='wmx-marker-image' /><br />
						</div>
						<div id='wmx-right-marker-buttons'>
							<input type='hidden' id='wmx-marker-url' />
							<button id='wmx-select-marker'>Choose Marker Image...</button><br />
						</div>
					</div>
					<div id='wmx-instructions'>
						<p><b>To add a marker:</b><br />
						click on a location in the map, or<br />
						type in an address (above), or<br />
						type in the latitude and longitude and click &quot;+ Add Marker&quot;.<br />
						<b>Double-click a marker to delete it.</b></p>
						<button>Add KML File</button> 
					</div>
				</div>
				
			</div>";
		
	
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

