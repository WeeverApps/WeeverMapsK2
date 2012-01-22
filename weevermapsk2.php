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

	public function __construct(&$subject, $params) 
	{
		
		JPlugin::loadLanguage('plg_k2_'.$this->pluginName, JPATH_ADMINISTRATOR);
		
		$this->pluginNameHumanReadable = JText::_('WEEVERMAPSK2_PLG_NAME');
		
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		
		if(substr($joomla,0,3) == '1.5')
		{

			require_once JPATH_PLUGINS.DS.'k2'.DS.'weevermapsk2'.DS.'jsjtext15.php';
			
			jsJText::script('WEEVERMAPSK2_CONFIRM_CLOSE');
			jsJText::script('WEEVERMAPSK2_ERROR_NO_RESULTS');
			jsJText::load();
		
		}
		else
		{
		
			JText::script('WEEVERMAPSK2_CONFIRM_CLOSE');
			JText::script('WEEVERMAPSK2_ERROR_NO_RESULTS');
		}
		
		$document = &JFactory::getDocument();
		$document->addScript( 'http://maps.googleapis.com/maps/api/js?sensor=false' );
		$document->addScript( '/media'.DS.'plg_weevermapsk2'.DS.'js'.DS.'markerwithlabel.js' );
		$document->addScript( '/media'.DS.'plg_weevermapsk2'.DS.'js'.DS.'wmx.js' );
		$document->addScript( '/media'.DS.'plg_weevermapsk2'.DS.'js'.DS.'jq.ready.js' );

		$document->addStyleSheet(DS.'media'.DS.'plg_weevermapsk2'.DS.'css'.DS.'wmx.css', 'text/css', null, array());
		$document->addStyleSheet(DS.'media'.DS.'plg_weevermapsk2'.DS.'css'.DS.'jquery.ui.css', 'text/css', null, array());
		
		echo "<div id='wmx-dialog' title='&lt;img id=&quot;wmx-logo&quot; src=&quot;/media/plg_weevermapsk2/images/weever.png&quot;&gt; ".$this->pluginNameHumanReadable." v".$this->pluginVersion."'>
				<div id='wmx-address'>
					<input type='text' id='wmx-address-input' placeholder='".JText::_('WEEVERMAPSK2_ADDRESS_PLACEHOLDER')."' value='".JText::_('WEEVERMAPSK2_ADDRESS_VALUE')."' />
					<button id='wmx-address-geocode'>".JText::_('WEEVERMAPSK2_ADDRESS_GO')."</button> 
					<button id='wmx-address-add-marker'>".JText::_('WEEVERMAPSK2_ADDRESS_ADD_MARKER')."</button>
				</div>
				<div id='wmx-map'>This will be a map.</div>
				<div id='wmx-map-console'>
				
					<div id='wmx-instructions'><span>".JText::_('WEEVERMAPSK2_INSTRUCTIONS')."</span></div>
					
					<div id='wmx-console-options'>
					
						<div id='wmx-latlong-container' class='wmx-console-container-widget'>
						
							<div id='wmx-latlong-title' class='wmx-title'>".JText::_('WEEVERMAPSK2_GPS_POSITION')."</div>
							
							<div id='wmx-latlong-stats-container'>
								<div id='wmx-latitude'>
									<div class='wmx-hover-label'><label for='wmx-lat-hover'>".JText::_('WEEVERMAPSK2_LAT_COLON')."</label></div>
									<input type='text' id='wmx-lat-hover' />
								</div>
								
								<div id='wmx-longitude'>
									<div class='wmx-hover-label'><label for='wmx-long-hover'>".JText::_('WEEVERMAPSK2_LONG_COLON')."</label></div>
									<input type='text' id='wmx-long-hover' />
								</div>
							</div>
							
							<div id='wmx-gps-center'>
								<button class='wmx-latlong-button' id='wmx-latlong-location'>".JText::_('WEEVERMAPSK2_LAT_LONG_GO')."</button>
							</div>
							
							<div id='wmx-latlong-button-container'>
								<button class='wmx-latlong-button' id='wmx-gps-location'>".JText::_('WEEVERMAPSK2_MY_LOCATION')."</button> 
								<button class='wmx-latlong-button' id='wmx-latlong-add-marker'>".JText::_('WEEVERMAPSK2_LAT_LONG_ADD_MARKER')."</button>
							</div>
							
						</div>
						
						<div id='wmx-marker-container' class='wmx-console-container-widget'>
						
							<div id='wmx-right-marker'>				
								<img src='http://weeverapp.com/media/sprites/default-marker.png' id='wmx-marker-image' /><br />
							</div>
							
							<div id='wmx-right-marker-buttons'>
								<input type='hidden' name='wmx-marker-url' id='wmx-marker-url' value='' />
								<button id='wmx-select-marker'>".JText::_('WEEVERMAPSK2_CHOOSE_MARKER_ICON')."</button><br />
							</div>
							
						</div>
						
						<div id='wmx-options-container' class='wmx-console-container-widget'>
							
							<div id='wmx-options-title' class='wmx-title'>".JText::_('WEEVERMAPSK2_OTHER_OPTIONS')."</div>
							<button id='wmx-add-kml'>".JText::_('WEEVERMAPSK2_ADD_KML_FILE')."</button> 
							
						</div>
						
					</div>
					
				</div>
				
			</div>
			
			<div id='wmx-marker-dialog' title='".JText::_('WEEVERMAPSK2_EDIT_MARKER')."'>
				
				<div id='wmx-marker-dialog-options-container'>
					<div class='wmx-title'>".JText::_('WEEVERMAPSK2_MARKER_OPTIONS')."</div>
					<label class='wmx-marker-button' for='wmx-marker-label-input'>".JText::_('WEEVERMAPSK2_MARKER_LABEL_COLON')."</label>
					<input id='wmx-marker-label-input' type='text' class='wmx-marker-button'  placeholder='".JText::_('WEEVERMAPSK2_MARKER_LABEL')."' /><br />
					<input type='button' id='wmx-marker-change-icon' class='wmx-marker-button' value='".JText::_('WEEVERMAPSK2_CHANGE_ICON_ELLIPSIS')."' /><br />
					<input type='hidden' id='wmx-marker-icon' name='wmx-marker-icon' value='' />
					<input type='button' id='wmx-marker-delete' class='wmx-marker-button' value='".JText::_('WEEVERMAPSK2_DELETE_MARKER')."' />
				</div>
				
			</div>
			
			<div id='wmx-kml-dialog' title='".JText::_('WEEVERMAPSK2_ADD_KML_FILE')."'>
				
				<div id='wmx-kml-dialog-container'>
					<input type='text' id='wmx-kml-url' value='' placeholder='http://' /><br />
					<!--button id='wmx-kml-media'>".JText::_('WEEVERMAPSK2_FILE_FROM_SERVER_ELLIPSIS')."</button-->
					<span>".JText::_('WEEVERMAPSK2_KML_INSTRUCTIONS')."</span>
				</div>
				
			</div>
			
			<div id='wmx-close-dialog' title='".JText::_('WEEVERMAPSK2_ARE_YOU_SURE')."'>
				
				<div>
					".JText::_('WEEVERMAPSK2_CONFIRM_CLOSE')."
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



