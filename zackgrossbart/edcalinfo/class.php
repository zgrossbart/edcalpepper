<?php
/******************************************************************************
 Pepper
 
 Developer		: Zack Grossbart
 Plug-in Name	: Edcal Info
 
 http://www.zackgrossbart.com

 ******************************************************************************/
 
$installPepper = "SI_EdcalInfo";
	
class SI_EdcalInfo extends Pepper
{
	var $version	= 1; // Displays as 0.01
	var $info		= array
	(
		'pepperName'	=> 'Editorial Calendar Statistics Info',
		'pepperUrl'		=> 'http://www.zackgrossbart.com',
		'pepperDesc'	=> 'This is a specialized pepper for showing information about the statistics for the WordPress Editorial Calendar plugin',
		'developerName'	=> 'Zack Grossbart',
		'developerUrl'	=> 'http://www.zackgrossbart.com'
	);
    
    var $panes = array
	(
        'Editorial Calendar Statistics Information' => array
		(
			' '
		)
	);
    
    /**************************************************************************
	 isCompatible()
	 **************************************************************************/
	function isCompatible()
	{
		if ($this->Mint->version >= 120)
		{
			return array
			(
				'isCompatible'	=> true
			);
		}
		else
		{
			return array
			(
				'isCompatible'	=> false,
				'explanation'	=> '<p>This Pepper is only compatible with Mint 1.2 and higher.</p>'
		);
		}
	}
	
    /**************************************************************************
	 onDisplay()
	 **************************************************************************/
	function onDisplay($pane, $tab, $column = '', $sort = '')
	{
        $html = '';
		switch($pane) 
		{
			case 'Editorial Calendar Statistics Information':
                $html = $this->getHTML_EdcalInfo();
                break;
		}
		
		return $html;
	}
	
	/**************************************************************************
	 onDisplayPreferences()
	 **************************************************************************/
	function onDisplayPreferences() 
	{
		$defaultGroups = get_class_vars('SI_EdcalInfo');
		
		/* Global *************************************************************/
		$preferences['Edcal Info']	= '';
		
		return $preferences;
	}
	
	/**************************************************************************
	 onSavePreferences()
	 **************************************************************************/
	function onSavePreferences() 
	{
		
	}
	
	/**************************************************************************
	 getHTML_EdcalUsers()
     
     This function gets the list of editorial calendar users.
	 **************************************************************************/
	function getHTML_EdcalInfo()
	{
        $html = '<div style="font: 16px/20px Verdana,sans-serif;"><p style="color: black;">This page shows statistics information for the <a href="http://wordpress.org/extend/plugins/editorial-calendar/">WordPress Editorial Calendar</a>.  We use this information to understand how people are using the calendar and make the plugin better.</p><p style="color: black; margin-top: 1em;">We never sell any of this information or use it for any other purpose.</p><p style="color: black;  margin-top: 1em;">Check out <a href="http://www.zackgrossbart.com/hackito/edcal_stats/">Collecting Statistics for The WordPress Editorial Calendar Plugin</a> to find out more about how we collect this information and what we use it for.</p></div>';
        
        return $html;
	}
}
?>