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
	public 	$pluginNameHumanReadable;
	public  $pluginVersion = "0.1";
	public	$pluginLongVersion = "Version 0.1 \"Anaximander\" (beta)";
	public  $pluginReleaseDate = "January 23, 2012";
	public  $joomlaVersion;

	public function __construct(&$subject, $params) 
	{
		
		$app =& JFactory::getApplication();
		
		if( !$app->isAdmin() )
			return false;
		
		JPlugin::loadLanguage('plg_k2_'.$this->pluginName, JPATH_ADMINISTRATOR);
		
		$this->pluginNameHumanReadable = JText::_('WEEVERMAPSK2_PLG_NAME');
		
		$version = new JVersion;
		$this->joomlaVersion = substr($version->getShortVersion(), 0, 3);
		
		if(JRequest::getVar("view") != "item")
			return false;
		
		// Javascript localization assignment. All localized Javascript strings must register here.
		
		if($this->joomlaVersion == '1.5')
		{

			// Joomla 1.5 does not support Javascript localization. This adds support.

			include_once JPATH_PLUGINS.DS.'k2'.DS.'weevermapsk2'.DS.'jsjtext15.php';
			
			jsJText::script('WEEVERMAPSK2_CONFIRM_CLOSE');
			jsJText::script('WEEVERMAPSK2_ERROR_NO_RESULTS');
			jsJText::load();
		
		}
		else
		{
		
			JText::script('WEEVERMAPSK2_CONFIRM_CLOSE');
			JText::script('WEEVERMAPSK2_ERROR_NO_RESULTS');
			
		}
		
		
		include JPATH_PLUGINS.DS.'k2'.DS.'weevermapsk2'.DS.'view.html.php';
		
		parent::__construct($subject, $params);
		
	}

	
	public function onAfterK2Save(&$item, $isNew) {
		
		if($this->joomlaVersion == '1.5')
		{
		
			// K2 for Joomla 1.5 stores $item->plugin as INI string rather than JSON
			// ... and Joomla 1.5 has its own INI parsing class, JRegistry.
		
			$registry	= new JRegistry();
			$registry->loadINI($item->plugins);
			$geoData	= $registry->toObject( );
			
		}
		else 
		{
		
			// K2 for Joomla 1.6+ is normal.
		
			$geoData = json_decode($item->plugins);
			
		}
		
		if($geoData->weevermapsk2altitude_item == "wxdebug") 
		{
		
			print_r($item->plugins);
			echo "\n\n\n";
			print_r($geoData);

		}
			
		
		$geoLatArray = 		explode( 	";", rtrim( $geoData->weevermapsk2latitude_item, 	";") 	);
		$geoLongArray = 	explode( 	";", rtrim( $geoData->weevermapsk2longitude_item, 	";") 	);
		$geoAddressArray = 	explode( 	";", rtrim( $geoData->weevermapsk2address_item, 	";") 	);
		$geoLabelArray = 	explode( 	";", rtrim( $geoData->weevermapsk2label_item, 		";") 	);
		$geoMarkerArray = 	explode( 	";", rtrim( $geoData->weevermapsk2marker_item, 		";") 	);
		
		$db = &JFactory::getDBO();
		
		$query = " 	DELETE FROM #__weever_maps 
					WHERE
						component_id = ".$db->Quote($item->id)."
						AND
						component = ".$db->Quote('com_k2');
						
	
		$db->setQuery($query);
		$db->query();
		
		
		if($geoData->weevermapsk2altitude_item == "wxdebug") 
			echo $query;
		
		foreach( (array) $geoLatArray as $k=>$v )
		{
		
			$query = " 	INSERT  ".
					"	INTO	#__weever_maps ".
					"	(component_id, component, location, address, label, marker) ".
					"	VALUES ('".$item->id."', ".$db->Quote('com_k2').", 
							GeomFromText(' POINT(".$geoLatArray[$k]." ".$geoLongArray[$k].") '),
							".$db->Quote($geoAddressArray[$k]).", 
							".$db->Quote($geoLabelArray[$k]).", 
							".$db->Quote($geoMarkerArray[$k]).")";
						
		
			$db->setQuery($query);
			$db->query();
			
			
			if($geoData->weevermapsk2altitude_item == "wxdebug") 
				echo $query;
		
		}
		
		if($geoData->weevermapsk2kml_item)
		{
			
			$query = " 	INSERT  ".
					"	INTO	#__weever_maps ".
					"	(component_id, component, kml) ".
					"	VALUES ('".$item->id."', 'com_k2', ".$db->Quote($geoData->weevermapsk2kml_item).")";
			
			$db->setQuery($query);
			$db->query();
			
			
			if($geoData->weevermapsk2altitude_item == "wxdebug") 
				echo $query;

		}
		
		
		if($geoData->weevermapsk2altitude_item == "wxdebug") 
			jexit();
		
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



