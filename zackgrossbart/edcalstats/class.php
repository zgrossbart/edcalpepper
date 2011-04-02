<?php
/*******************************************************************************
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 ******************************************************************************/
 
 
/******************************************************************************
 Pepper
 
 Developer		: Zack Grossbart
 Plug-in Name	: Edcal Stats
 
 http://www.zackgrossbart.com

 ******************************************************************************/
 
$installPepper = "SI_EdcalStats";
	
class SI_EdcalStats extends Pepper
{
	var $version	= 1; // Displays as 0.01
	var $info		= array
	(
		'pepperName'	=> 'Editorial Calendar Stats',
		'pepperUrl'		=> 'http://www.zackgrossbart.com',
		'pepperDesc'	=> 'This is a specialized pepper for tracking information about the WordPress Editorial Calendar plugin',
		'developerName'	=> 'Zack Grossbart',
		'developerUrl'	=> 'http://www.zackgrossbart.com'
	);
    
    var $panes = array
	(
        'Editorial Calendar Stats' => array
		(
			'Stats'
		)
	);
    
    var $prefs = array
	(
		// Common resolutions widths minus worst-case browser chrome width (56)
		'edcalWeeksGroup' => '584, 744, 968, 1096, 1344, 1384, 1544, 1624, 1824'
	);
	
	var $manifest = array
	(
		'visit'	=> array
		(
			'edcal_weeks' => "SMALLINT(5) NOT NULL DEFAULT '-1'",
            'edcal_posts' => "SMALLINT(5) NOT NULL DEFAULT '-1'",
            'edcal_author' => "TINYINT",
            'edcal_author_count' => "TINYINT"
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
	 onJavaScript()
	 **************************************************************************/
	function onJavaScript() 
	{
		$js = "pepper/zackgrossbart/edcalstats/script.js";
		if (file_exists($js))
		{
			include_once($js);
		}
	}
	
	/**************************************************************************
	 onRecord()
	 **************************************************************************/
	function onRecord() 
	{
        $edcalWeeks =  $this->Mint->escapeSQL($_GET['edcal_weeks']);
        $edcalPosts =  $this->Mint->escapeSQL($_GET['edcal_posts']);
        $edcalAuthor =  $this->Mint->escapeSQL($_GET['edcal_author']);
        $edcalAuthorCount =  $this->Mint->escapeSQL($_GET['edcal_author_count']);
		
		return array
		(
			'edcal_weeks' => (float) $edcalWeeks,
            'edcal_posts' => (float) $edcalPosts,
            'edcal_author' => (boolean) $edcalAuthor,
            'edcal_author_count' => (float) $edcalAuthorCount
		);
	}
	
	/**************************************************************************
	 onDisplay()
	 **************************************************************************/
	function onDisplay($pane, $tab, $column = '', $sort = '')
	{
        $html = '';
		switch($pane) 
		{
			case 'Editorial Calendar Stats':
                $html  = '<table cellspacing="0" class="two-edcal-columns">';
        		$html .= "\r\t<tr>\r";
        		$html .= "\t\t<td style=\"padding-right: 4px;\" class=\"left\">\r";
        		$html .= $this->getHTML_EdcalWeeks();
                $html .= "<br />";
                $html .= $this->getHTML_EdcalAuthorCount();
        		$html .= "\t\t</td>";
        		$html .= "\t\t<td class=\"right\">\r";
        		$html .= $this->getHTML_EdcalPosts();
                $html .= "<br />";
                $html .= $this->getHTML_EdcalAuthor();
        		$html .= "\t\t</td>";
        		$html .= "\r\t</tr>\r";
        		$html .= "</table>\r";
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
		$preferences['Edcal Weeks']	= '';
		
		return $preferences;
	}
	
	/**************************************************************************
	 onSavePreferences()
	 **************************************************************************/
	function onSavePreferences() 
	{
		
	}
	
	/**************************************************************************
	 getHTML_EdcalWeeks()
     
     This function gets the number of visible weeks in the blog and displays them
     as a set of averages.
	 **************************************************************************/
	function getHTML_EdcalWeeks()
	{
	
		$html = '';
		
        /*
         * The number of visible weeks
         */
        $tableData['table'] = array('id'=>'','class'=>'inline striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Visible Weeks','class'=>''),
			array('value'=>'% of Total','class'=>'')
		);
		
		$query = "SELECT COUNT(`edcal_weeks`) AS `total` FROM `{$this->Mint->db['tblPrefix']}visit`
                                WHERE `edcal_weeks` > -1";
		
		if ($result = $this->query($query))	{
			if ($r = mysql_fetch_array($result)) {
				$total = ($r['total'])?$r['total']:0;
				
				for ($i = 1; $i < 6; $i++) {
					$weekquery = "SELECT COUNT(`edcal_weeks`) AS `total` FROM `{$this->Mint->db['tblPrefix']}visit` 
									WHERE `edcal_weeks` = " . $i;
									
					if ($weekresult = $this->query($weekquery))	{
						if ($weekr = mysql_fetch_array($weekresult)) {
							$weeks = ($weekr['total'])?$weekr['total']:0;
							$weekTitle = 'Weeks';
							
							if ($i == 1) {
								$weekTitle = 'Week';
							}
							
							$tableData['tbody'][] = array
							(
								$i . " " . $weekTitle,
								$this->Mint->formatPercents($weeks/$total*100)
							);
						}
					}
				}
			}
		}
		
		
		$html = $this->Mint->generateTable($tableData);
		return $html;
	}
    
    /**************************************************************************
	 getHTML_EdcalAuthorCount()
     
     This function gets the number of authors visible in the calendar
	 **************************************************************************/
	function getHTML_EdcalAuthorCount()
	{
	
		$html = '';
		
        /*
         * Number of authors
         */
        $tableData['table'] = array('id'=>'','class'=>'inline striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Number of Authors','class'=>''),
			array('value'=>'% of Total','class'=>'')
		);
        
        /*
         * First we need to get the total count of rows that have data about the 
         * number of posts in a day.  We need it since we are doing averages.
         */
        $total = 0;
        $query = "SELECT COUNT(`edcal_author_count`) AS `total` FROM `{$this->Mint->db['tblPrefix']}visit` WHERE `edcal_author_count` > 0";
        if ($result = $this->query($query))	{
			if ($r = mysql_fetch_array($result)) {
                $total = $r['total'];
            }
        }
        
        unset($query);
        unset ($result);
        unset($r);
		
		$query = "SELECT DISTINCT(`edcal_author_count`) AS `posts` FROM `{$this->Mint->db['tblPrefix']}visit` WHERE `edcal_author_count` > 0";
        
		
		if ($result = $this->query($query))	{
			while ($r = mysql_fetch_assoc($result)) {
                $posts = $r['posts'];
				
                $weekquery = "SELECT COUNT(`edcal_author_count`) AS `total` FROM `{$this->Mint->db['tblPrefix']}visit` 
                                WHERE `edcal_author_count` = " . $posts;
                                
                if ($weekresult = $this->query($weekquery))	{
                    if ($weekr = mysql_fetch_array($weekresult)) {
                        $weeks = ($weekr['total'])?$weekr['total']:0;
                        
                        $weekTitle = 'Authors';
                        
                        if ($posts == 1) {
                            $weekTitle = 'Author';
                        }
                        
                        $tableData['tbody'][] = array
                        (
                            $posts . " " . $weekTitle,
                            $this->Mint->formatPercents($weeks/$total*100)
                        );
                    }
                }
			}
		}
		
		
		$html = $this->Mint->generateTable($tableData);
		return $html;
	}
    
    /**************************************************************************
	 getHTML_EdcalAuthor()
     
     This functions get a simple boolean value to indicate what percentage of 
     blogs are showing the author.
	 **************************************************************************/
	function getHTML_EdcalAuthor()
	{
	
		$html = '';
		
        /*
         * If the Author is visible
         */
        $tableData['table'] = array('id'=>'','class'=>'inline striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Showing Author','class'=>'')
		);
		
		$query = "SELECT COUNT(`edcal_author`) AS `total` FROM `{$this->Mint->db['tblPrefix']}visit`";
		
		if ($result = $this->query($query))	{
			if ($r = mysql_fetch_array($result)) {
				$total = ($r['total'])?$r['total']:0;
				
				for ($i = 0; $i < 2; $i++) {
					$weekquery = "SELECT COUNT(`edcal_author`) AS `total` FROM `{$this->Mint->db['tblPrefix']}visit` 
									WHERE `edcal_author` = " . $i;
									
					if ($weekresult = $this->query($weekquery))	{
						if ($weekr = mysql_fetch_array($weekresult)) {
							$weeks = ($weekr['total'])?$weekr['total']:0;
                            
							if ($i == 1) {
                                $tableData['tbody'][] = array
    							(
    								$this->Mint->formatPercents($weeks/$total*100) . " are showing authors"
    							);
							} else {
                                $tableData['tbody'][] = array
    							(
    								$this->Mint->formatPercents($weeks/$total*100) . " are not showing authors"
    							);
							}
						}
					}
				}
			}
		}
		
		
		$html = $this->Mint->generateTable($tableData);
		return $html;
	}
    
    /**************************************************************************
	 getHTML_EdcalPosts()
     
     This function gets the average number of posts per day and displays them as
     a list of averages.
	 **************************************************************************/
	function getHTML_EdcalPosts()
	{
	
		$html = '';
		
        /*
         * Average number of posts
         */
        $tableData['table'] = array('id'=>'','class'=>'inline striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Average posts per day','class'=>''),
			array('value'=>'% of Total','class'=>'')
		);
        
        /*
         * First we need to get the total count of rows that have data about the 
         * number of posts in a day.  We need it since we are doing averages.
         */
        $total = 0;
        $query = "SELECT COUNT(`edcal_posts`) AS `total` FROM `{$this->Mint->db['tblPrefix']}visit` WHERE `edcal_posts` > -1";
        if ($result = $this->query($query))	{
			if ($r = mysql_fetch_array($result)) {
                $total = $r['total'];
            }
        }
        
        unset($query);
        unset ($result);
        unset($r);
		
		$query = "SELECT DISTINCT(`edcal_posts`) AS `posts` FROM `{$this->Mint->db['tblPrefix']}visit` WHERE `edcal_posts` > -1";
        
		
		if ($result = $this->query($query))	{
			while ($r = mysql_fetch_assoc($result)) {
                $posts = $r['posts'];
				
                $weekquery = "SELECT COUNT(`edcal_posts`) AS `total` FROM `{$this->Mint->db['tblPrefix']}visit` 
                                WHERE `edcal_posts` = " . $posts;
                                
                if ($weekresult = $this->query($weekquery))	{
                    if ($weekr = mysql_fetch_array($weekresult)) {
                        $weeks = ($weekr['total'])?$weekr['total']:0;
                        
                        $weekTitle = 'Posts per day';
                        
                        if ($posts == 1) {
                            $weekTitle = 'Post per day';
                        }
                        
                        $tableData['tbody'][] = array
                        (
                            $posts . " " . $weekTitle,
                            $this->Mint->formatPercents($weeks/$total*100)
                        );
                    }
                }
			}
		}
		
		
		$html = $this->Mint->generateTable($tableData);
		return $html;
	}
}
?>