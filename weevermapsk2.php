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
	public 	$pluginNameHumanReadable = 'Weever Maps for K2';

	function __construct(&$subject, $params) 
	{
		parent::__construct($subject, $params);
	}
	
	function onAfterK2Save(&$item, $isNew) {
	
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

	
} // END CLASS

