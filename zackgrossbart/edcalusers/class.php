<?php
/******************************************************************************
 Pepper
 
 Developer		: Zack Grossbart
 Plug-in Name	: Edcal Users
 
 http://www.zackgrossbart.com

 ******************************************************************************/
 
$installPepper = "SI_EdcalUsers";
	
class SI_EdcalUsers extends Pepper
{
	var $version	= 1; // Displays as 0.01
	var $info		= array
	(
		'pepperName'	=> 'Editorial Calendar Users',
		'pepperUrl'		=> 'http://www.zackgrossbart.com',
		'pepperDesc'	=> 'This is a specialized pepper for tracking the users of the WordPress Editorial Calendar plugin',
		'developerName'	=> 'Zack Grossbart',
		'developerUrl'	=> 'http://www.zackgrossbart.com'
	);
    
    var $panes = array
	(
        'Editorial Calendar Users' => array
		(
			'Blogs'
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
			case 'Editorial Calendar Users':
                $html = $this->getHTML_EdcalUsers();
                break;
		}
		
		return $html;
	}
	
	/**************************************************************************
	 onDisplayPreferences()
	 **************************************************************************/
	function onDisplayPreferences() 
	{
		$defaultGroups = get_class_vars('SI_EdcalStats');
		
		/* Global *************************************************************/
		$preferences['Edcal Users']	= '';
		
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
	function getHTML_EdcalUsers()
	{
        $html = '';
        
        $tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
            array('value'=>'Title','class'=>''),
            array('value'=>'When','class'=>'')
		);
		
		$query = "SELECT DISTINCT(`referer`) AS `url`, (`resource_title`) AS `title`, (`dt`) AS `date` FROM `{$this->Mint->db['tblPrefix']}visit` 
                    WHERE `referer_checksum` != 0
                    ORDER BY `dt` DESC";
        
        if ($result = $this->query($query)) 
        {
            while ($r = mysql_fetch_assoc($result))
            {
                if ($r['url'])
                {
                    $title = $r['title'];
                    
                    if (strpos($title, 'Calendar &#x2039; ') === 0) {
                        $title = substr($title, 18);
                    }
                    
                    if (strpos($title, ' &#x2014; WordPress') > 0) {
                        $title = substr($title, 0, -19);
                    }
                    
                    $url = $r['url'];
                     
                    if (strpos($url, 'wp-admin/') > 0) {
                        $url = substr($url, 0, strpos($url, 'wp-admin/'));
                    }
                    
                    $link = '<a href="' . $url . '">' . $title . '</a>';
                    $tableData['tbody'][] = array
                    (
                        $link,
                        $this->Mint->formatDateTimeRelative($r['date'])
                    );
                }
            }
        }
			
		$html = $this->Mint->generateTable($tableData);
		return $html;
	}
}
?>