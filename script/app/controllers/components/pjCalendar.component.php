<?php
class pjCalendar
{
    private $startDay = 0;

    private $startMonth = 1;

    private $dayNames = array("S", "M", "T", "W", "T", "F", "S");
    
    private $weekDays = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

    private $monthNames = array(
    	1 => "January",
    	2 => "February",
    	3 => "March",
    	4 => "April",
    	5 => "May",
    	6 => "June",
    	7 => "July",
    	8 => "August",
    	9 => "September",
    	10 => "October",
    	11 => "November",
    	12 => "December"
    );

    private $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    
    private $showNextLink = true;
    
    private $showPrevLink = true;
    
    private $weekTitle = "#";
    
    private $prevLink = "&lt;";
    
    private $nextLink = "&gt;";
    
    private $na = 'N/A';
    
    private $classTable = "abCalendarTable";
    private $classTablePrice = "abCalendarTablePrice";
    private $classWeekDay = "abCalendarWeekDay";
    private $classWeekDayInner = "abCalendarWeekDayInner";
    private $classMonth = "abCalendarMonth";
    private $classMonthInner = "abCalendarMonthInner";
    private $classMonthPrev = "abCalendarMonthPrev";
    private $classMonthNext = "abCalendarMonthNext";
    private $classPending = "abCalendarPending";
    private $classReserved = "abCalendarReserved";
    private $classCalendar = "abCalendarDate";
    private $classEmpty = "abCalendarEmpty";
    private $classWeekNum = "abCalendarWeekNum";
    private $classPast = "abCalendarPast";
    private $classPendingNightsStart = "abCalendarPendingNightsStart";
    private $classReservedNightsStart = "abCalendarReservedNightsStart";
    private $classPendingNightsEnd = "abCalendarPendingNightsEnd";
    private $classReservedNightsEnd = "abCalendarReservedNightsEnd";
	private $classNightsPendingPending = "abCalendarNightsPendingPending";
	private $classNightsReservedPending = "abCalendarNightsReservedPending";
	private $classNightsPendingReserved = "abCalendarNightsPendingReserved";
	private $classNightsReservedReserved = "abCalendarNightsReservedReserved";
	private $classPrice = "abCalendarPrice";
	private $classPriceStatic = "abCalendarPriceStatic";
	private $classLinkDate = "abCalendarLinkDate";
	private $classPartial = "abCalendarPartial";
    
    public function __construct()
    {
    	
    }
    
    public function setNA($value)
    {
    	$this->na = $value;
    	return $this;
    }
    
    public function getNA()
    {
    	return $this->na;
    }
    
    public function setPrevLink($value)
    {
    	$this->prevLink = $value;
    	return $this;
    }
    
	public function setNextLink($value)
    {
    	$this->nextLink = $value;
    	return $this;
    }
    
	public function getPrevLink()
    {
    	return $this->prevLink;
    }
    
	public function getNextLink()
    {
    	return $this->nextLink;
    }
    
    public function setShowNextLink($value)
    {
    	if (is_bool($value))
    	{
    		$this->showNextLink = $value;
    	}
    	return $this;
    }
    
    public function getShowNextLink()
    {
    	return $this->showNextLink;
    }
    
	public function setShowPrevLink($value)
    {
    	if (is_bool($value))
    	{
    		$this->showPrevLink = $value;
    	}
    	return $this;
    }
    
    public function getShowPrevLink()
    {
    	return $this->showPrevLink;
    }

    public function getDayNames()
    {
        return $this->dayNames;
    }

    public function setDayNames($names)
    {
        $this->dayNames = $names;
        return $this;
    }

    public function getWeekDays()
    {
    	return $this->weekDays;
    }
    
    public function setWeekDays($days)
    {
    	$this->weekDays = $days;
    	return $this;
    }
    
    public function getMonthNames()
    {
        return $this->monthNames;
    }

    public function setMonthNames($names)
    {
        $this->monthNames = $names;
        return $this;
    }

    public function getStartDay()
    {
        return $this->startDay;
    }

    public function setStartDay($day)
    {
        $this->startDay = $day;
        return $this;
    }

    public function getStartMonth()
    {
        return $this->startMonth;
    }

    public function setStartMonth($month)
    {
        $this->startMonth = $month;
        return $this;
    }
    
	public function setWeekTitle($title)
    {
        $this->weekTitle = $title;
        return $this;
    }

    public function getCalendarLink($month, $year)
    {
        return "";
    }

    public function getDateLink($day, $month, $year)
    {
        return "";
    }

    public function getCurrentMonthView()
    {
        $d = getdate(time());
        return $this->getMonthView($d["mon"], $d["year"]);
    }

    public function getMonthView($month, $year)
    {
        return $this->getMonthHTML($month, $year);
    }

    public function getDaysInMonth($month, $year)
    {
        if ($month < 1 || $month > 12)
        {
            return 0;
        }
   
        $d = $this->daysInMonth[$month - 1];
   
        if ($month == 2)
        {
            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                    {
                        $d = 29;
                    }
                } else {
                    $d = 29;
                }
            }
        }
    
        return $d;
    }

    public function getMonthHTML($m, $y, $showYear = 1)
    {
    	$reservationsInfo = $this->reservationsInfo;
    	if ((int) $this->options['o_bookings_per_day'] === 1)
    	{
    		$reservationsInfo = pjUtil::fixSingleDay($reservationsInfo);
    	}
    	
    	$end_arr = array();
    	foreach ($this->periods as $k => $timestamp_arr)
    	{
    		if(is_array($timestamp_arr))
    		{
	    		foreach($timestamp_arr as $range)
	    		{
	    			$timestamp = $range['end_ts'] + 24*60*60;    			
	    			if(!in_array($timestamp, $end_arr))
	    			{
	    				$end_arr[] = $timestamp;
	    			}
	    		}
    		}
    	}
    	
    	$max_index = count($this->periods) > 0 ? max(array_keys($this->periods)) : NULL;
    	$min_index = count($this->periods) > 0 ? min(array_keys($this->periods)) : NULL;
    	
    	$haystack = array(
	    	'calendarStatus1' => $this->classCalendar,
			'calendarStatus2' => $this->classReserved,
			'calendarStatus3' => $this->classPending,//
			'calendarStatus_1_2' => $this->classReservedNightsStart,
			'calendarStatus_1_3' => $this->classPendingNightsStart,
			'calendarStatus_2_1' => $this->classReservedNightsEnd,
			'calendarStatus_2_3' => $this->classNightsReservedPending,
			'calendarStatus_3_1' => $this->classPendingNightsEnd,//
			'calendarStatus_3_2' => $this->classNightsPendingReserved,
    		'calendarStatusPartial' => $this->classPartial
		);
		
		$imageMap = array(
		/*'abCalendarReservedNightsStart' => sprintf("%sindex.php?controller=pjFront&action=pjActionImage&color1=%s&color2=%s&width=120&height=120", PJ_INSTALL_URL, str_replace('#', '', $this->options['o_background_available']), str_replace('#', '', $this->options['o_background_booked'])),
			'abCalendarReservedNightsEnd' => sprintf("%sindex.php?controller=pjFront&action=pjActionImage&color1=%s&color2=%s&width=120&height=120", PJ_INSTALL_URL, str_replace('#', '', $this->options['o_background_booked']), str_replace('#', '', $this->options['o_background_available'])),
			'abCalendarNightsPendingPending' => sprintf("%sindex.php?controller=pjFront&action=pjActionImage&color1=%s&color2=%s&width=120&height=120", PJ_INSTALL_URL, str_replace('#', '', $this->options['o_background_pending']), str_replace('#', '', $this->options['o_background_pending'])),
			'abCalendarNightsReservedPending' => sprintf("%sindex.php?controller=pjFront&action=pjActionImage&color1=%s&color2=%s&width=120&height=120", PJ_INSTALL_URL, str_replace('#', '', $this->options['o_background_booked']), str_replace('#', '', $this->options['o_background_pending'])),
			'abCalendarNightsPendingReserved' => sprintf("%sindex.php?controller=pjFront&action=pjActionImage&color1=%s&color2=%s&width=120&height=120", PJ_INSTALL_URL, str_replace('#', '', $this->options['o_background_pending']), str_replace('#', '', $this->options['o_background_booked'])),
			'abCalendarNightsReservedReserved' => sprintf("%sindex.php?controller=pjFront&action=pjActionImage&color1=%s&color2=%s&width=120&height=120", PJ_INSTALL_URL, str_replace('#', '', $this->options['o_background_booked']), str_replace('#', '', $this->options['o_background_booked'])),
			'abCalendarPendingNightsStart' => sprintf("%sindex.php?controller=pjFront&action=pjActionImage&color1=%s&color2=%s&width=120&height=120", PJ_INSTALL_URL, str_replace('#', '', $this->options['o_background_available']), str_replace('#', '', $this->options['o_background_pending'])),
			'abCalendarPendingNightsEnd' => sprintf("%sindex.php?controller=pjFront&action=pjActionImage&color1=%s&color2=%s&width=120&height=120", PJ_INSTALL_URL, str_replace('#', '', $this->options['o_background_pending']), str_replace('#', '', $this->options['o_background_available']))
		*/
			'abCalendarReservedNightsStart' => sprintf("%s%s%u_reserved_start.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $this->calendarId),
			'abCalendarReservedNightsEnd' => sprintf("%s%s%u_reserved_end.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $this->calendarId),
			'abCalendarNightsPendingPending' => sprintf("%s%s%u_pending_pending.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $this->calendarId),
			'abCalendarNightsReservedPending' => sprintf("%s%s%u_reserved_pending.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $this->calendarId),
			'abCalendarNightsPendingReserved' => sprintf("%s%s%u_pending_reserved.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $this->calendarId),
			'abCalendarNightsReservedReserved' => sprintf("%s%s%u_reserved_reserved.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $this->calendarId),
			'abCalendarPendingNightsStart' => sprintf("%s%s%u_pending_start.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $this->calendarId),
			'abCalendarPendingNightsEnd' => sprintf("%s%s%u_pending_end.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $this->calendarId)
		);
		
		$rand = rand(1,9999);
        $s = "";

        $a = $this->adjustDate($m, $y);
        $month = $a[0];
        $year = $a[1];
        
    	$daysInMonth = $this->getDaysInMonth($month, $year);
    	$date = getdate(mktime(12, 0, 0, $month, 1, $year));
    	
    	$first = $date["wday"];
    	$monthName = $this->monthNames[$month];
    	
    	$prev = $this->adjustDate($month - 1, $year);
    	$next = $this->adjustDate($month + 1, $year);
    	
    	if ($showYear == 1)
    	{
    	    $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
    	    $nextMonth = $this->getCalendarLink($next[0], $next[1]);
    	} else {
    	    $prevMonth = "";
    	    $nextMonth = "";
    	}
    	
    	$search = array('Month', 'Year');
    	$replace = array($monthName, $showYear > 0 ? $year : "");
    	$header = str_replace($search, $replace, $this->options['o_month_year_format']);
		    	
    	$prevM = ((int) $month - 1) < 1 ? 12 : (int) $month - 1;
    	$prevY = ((int) $month - 1) < 1 ? (int) $year - 1 : (int) $year;
    	
    	$nextM = ((int) $month + 1) > 12 ? 1 : (int) $month + 1;
    	$nextY = ((int) $month + 1) > 12 ? (int) $year + 1 : (int) $year;
    	
    	$cols = $this->weekNumbers ? 8 : 7;
    	
    	$s .= "<table class=\"".($this->isPrice ? $this->classTablePrice : $this->classTable)."\" cellspacing=\"0\" cellpadding=\"0\">\n";
    	$s .= "<tbody><tr>\n";
    	$s .= "<td class=\"".$this->classMonth." ".$this->classMonthPrev."\">" . (!$this->getShowPrevLink() ? '<div class="abCalendarMonthInner">&nbsp;</div>' : '<div class="abCalendarMonthInner"><a data-cid="'.$this->calendarId.'" data-direction="prev" data-month="'.$prevM.'" data-year="'.$prevY.'" href="'.$prevMonth['href'].'" class="'.$prevMonth['class'].'">'.$this->getPrevLink().'</a></div>')  . "</td>\n";
    	$s .= "<td class=\"".$this->classMonth."\" colspan=\"".($cols == 7 ? 5 : 6)."\">$header</td>\n";
    	$s .= "<td class=\"".$this->classMonth." ".$this->classMonthNext."\">" . (!$this->getShowNextLink() ? '<div class="abCalendarMonthInner">&nbsp;</div>' : '<div class="abCalendarMonthInner"><a data-cid="'.$this->calendarId.'" data-direction="next" data-month="'.$nextM.'" data-year="'.$nextY.'" href="'.$nextMonth['href'].'" class="'.$nextMonth['class'].'">'.$this->getNextLink().'</a></div>')  . "</td>\n";
    	$s .= "</tr>\n";
    	
    	$s .= "<tr>\n";
    	if ($this->weekNumbers)
    	{
    		$s .= sprintf('<td class="%s"><span class="'.$this->classWeekDayInner.'">%s</span></td>%s', $this->classWeekDay, $this->weekTitle, "\n");
    		$weekNumPattern = "<td class=\"".$this->classWeekNum."\">{WEEK_NUM}</td>";
    	}
    	$s .= sprintf('<td class="%s"><span class="'.$this->classWeekDayInner.'">%s</span></td>%s', $this->classWeekDay, $this->dayNames[($this->startDay)%7], "\n");
    	$s .= sprintf('<td class="%s"><span class="'.$this->classWeekDayInner.'">%s</span></td>%s', $this->classWeekDay, $this->dayNames[($this->startDay+1)%7], "\n");
    	$s .= sprintf('<td class="%s"><span class="'.$this->classWeekDayInner.'">%s</span></td>%s', $this->classWeekDay, $this->dayNames[($this->startDay+2)%7], "\n");
    	$s .= sprintf('<td class="%s"><span class="'.$this->classWeekDayInner.'">%s</span></td>%s', $this->classWeekDay, $this->dayNames[($this->startDay+3)%7], "\n");
    	$s .= sprintf('<td class="%s"><span class="'.$this->classWeekDayInner.'">%s</span></td>%s', $this->classWeekDay, $this->dayNames[($this->startDay+4)%7], "\n");
    	$s .= sprintf('<td class="%s"><span class="'.$this->classWeekDayInner.'">%s</span></td>%s', $this->classWeekDay, $this->dayNames[($this->startDay+5)%7], "\n");
    	$s .= sprintf('<td class="%s"><span class="'.$this->classWeekDayInner.'">%s</span></td>%s', $this->classWeekDay, $this->dayNames[($this->startDay+6)%7], "\n");
    	$s .= "</tr>\n";

    	$d = $this->startDay + 1 - $first;
    	while ($d > 1)
    	{
    	    $d -= 7;
    	}

        $today = getdate(time());
    	
        $rows = 0;
    	while ($d <= $daysInMonth)
    	{
    	    $s .= "<tr>\n";
    	    
    	    if ($this->weekNumbers)
    	    {
    	    	$s .= $weekNumPattern;
    	    }
    	    for ($i = 0; $i < 7; $i++)
    	    {
    	    	$scope = 0;
    	    	$timestamp = mktime(0, 0, 0, $month, $d, $year);
    	    	$isPast = false;
    	    	$class = "";
    	    	
    	    	if ($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"])
    	    	{
    	    		$class = $this->classCalendar; //calendarToday
    	    		$scope = 1;
    	    	} elseif ($d < 1 || $d > $daysInMonth) {
    	    		$class = $this->classEmpty;
    	    	} elseif ($timestamp < $today[0]) {
    	    		$isPast = true;
    	    		$class = $this->classPast;
    	    		$scope = -1;
    	    	} else {
    	    		$class = $this->classCalendar;
    	    		$scope = 1;
    	    	}
    	    	
    	    	$_class = NULL;
        	    if ($d > 0 && $d <= $daysInMonth && !$isPast)
        	    {
        	    	if (array_key_exists($timestamp, $reservationsInfo))
        	    	{
        	    	    
        	    	    $class = pjUtil::getClass($reservationsInfo, $timestamp, strtotime('+1 day', $timestamp), strtotime('-1 day', $timestamp), $this->options['o_bookings_per_day'], $haystack);
        	    		//$class = $this->classCalendar;
        	    		$_class = $class;
        	    	}
        	    }
        	    
        	    if ($d < 1 || $d > $daysInMonth) {
        	    	$s .= '<td class="'.$class.'">';
        	    } else {
        	    	$dataRange = array('start' => NULL, 'middle' => NULL, 'end' => NULL, 'weekly' => NULL, 'from' => array(), 'to' => array(), 'weekday'=>array(), 'in_out' => array());
        	    	if (array_key_exists($timestamp, $this->periods) && is_array($this->periods[$timestamp]))
        	    	{
        	    		foreach ($this->periods[$timestamp] as $range)
        	    		{
        	    			switch ($timestamp)
        	    			{
        	    				case $range['from']:
        	    				    $in_out = sprintf('%u-%u', $range['from_day'], $range['to_day']);
        	    					if(!empty($dataRange['in_out']))
        	    					{
        	    					    if(!in_array($in_out, $dataRange['in_out']))
        	    					    {
        	    					        $dataRange['in_out'][] = $in_out;
        	    					    }
        	    					}else{
        	    					    $dataRange['in_out'][] = $in_out;
        	    					}
        	    					$dataRange['start'] = true;
        	    					$dataRange['from'][] = intval($range['from']);
        	    					if(in_array($timestamp, $end_arr))
        	    					{
        	    						$dataRange['end'] = true;
        	    						$dataRange['to'][] = intval($range['to']);
        	    					}
		        	    			if ($range['from_day'] == $range['to_day'])
		        	    			{
		        	    				$dataRange['weekly'] = true;
		        	    			}
		        	    			if(!empty($dataRange['toWeekDays']))
		        	    			{
		        	    			    if(!in_array($this->weekDays[date('w', $range['to'])], $dataRange['toWeekDays']))
		        	    			    {
		        	    			        $dataRange['toWeekDays'][] = $this->weekDays[date('w', $range['to'])];
		        	    			    }
		        	    			}else{
		        	    			    $dataRange['toWeekDays'][] = $this->weekDays[date('w', $range['to'])];
		        	    			}
		        	    			if(isset($this->periods[$range['to']]))
		        	    			{
		        	    				$run = $range['to'];
		        	    				while($run <= $max_index)
		        	    				{
		        	    					$run = $run + 24*60*60;
		        	    					if(isset($this->periods[$run]) && is_array($this->periods[$run]))
		        	    					{
		        	    						foreach ($this->periods[$run] as $_range)
			        	    					{
			        	    						if($_range['from'] == $range['to'])
			        	    						{
			        	    							/* $dataRange['toWeekDays'][] = $this->weekDays[date('w', $_range['to'])]; */
			        	    						    if(!empty($dataRange['toWeekDays']))
			        	    						    {
			        	    						        if(!in_array($this->weekDays[date('w', $range['to'])], $dataRange['toWeekDays']))
			        	    						        {
			        	    						            $dataRange['toWeekDays'][] = $this->weekDays[date('w', $range['to'])];
			        	    						        }
			        	    						    }else{
			        	    						        $dataRange['toWeekDays'][] = $this->weekDays[date('w', $range['to'])];
			        	    						    }
			        	    							$run = $_range['to'];
			        	    						}
			        	    					}
		        	    					}
		        	    				}
		        	    			}
        	    					break;
        	    				case $range['to']:
        	    					/* $dataRange['in_out'][] = sprintf('%u-%u', $range['from_day'], $range['to_day']); */
        	    				    $in_out = sprintf('%u-%u', $range['from_day'], $range['to_day']);
        	    				    if(!empty($dataRange['in_out']))
        	    				    {
        	    				        if(!in_array($in_out, $dataRange['in_out']))
        	    				        {
        	    				            $dataRange['in_out'][] = $in_out;
        	    				        }
        	    				    }else{
        	    				        $dataRange['in_out'][] = $in_out;
        	    				    }
        	    					$dataRange['start'] = true;
        	    					$dataRange['end'] = true;
        	    					if(date('w', $timestamp) == date('w', intval($range['to'])))
        	    					{
        	    						$dataRange['to'][] = intval($range['to']);
        	    						$dataRange['toW'][] = $this->weekDays[date('w', $range['to'])];
        	    					}
		        	    			if ($range['from_day'] == $range['to_day'])
		        	    			{
		        	    				$dataRange['weekly'] = true;
		        	    			}
		        	    			/* $dataRange['fromWeekDays'][] = $this->weekDays[date('w', $range['from'])]; */
		        	    			if(!empty($dataRange['fromWeekDays']))
		        	    			{
		        	    			    if(!in_array($this->weekDays[date('w', $range['from'])], $dataRange['fromWeekDays']))
		        	    			    {
		        	    			        $dataRange['fromWeekDays'][] = $this->weekDays[date('w', $range['from'])];
		        	    			    }
		        	    			}else{
		        	    			    $dataRange['fromWeekDays'][] = $this->weekDays[date('w', $range['from'])];
		        	    			}
		        	    			
		        	    			if(isset($this->periods[$range['from']]))
		        	    			{
		        	    				$run = $range['from'];
		        	    				while($run >= $min_index)
		        	    				{
		        	    					$run = $run - 24*60*60;
		        	    					if(isset($this->periods[$run]) && is_array($this->periods[$run]))
		        	    					{
			        	    					foreach ($this->periods[$run] as $_range)
			        	    					{
			        	    						if($_range['to'] == $range['from'])
			        	    						{
			        	    							/* $dataRange['fromWeekDays'][] = $this->weekDays[date('w', $_range['from'])]; */
			        	    						    if(!empty($dataRange['fromWeekDays']))
			        	    						    {
			        	    						        if(!in_array($this->weekDays[date('w', $range['from'])], $dataRange['fromWeekDays']))
			        	    						        {
			        	    						            $dataRange['fromWeekDays'][] = $this->weekDays[date('w', $range['from'])];
			        	    						        }
			        	    						    }else{
			        	    						        $dataRange['fromWeekDays'][] = $this->weekDays[date('w', $range['from'])];
			        	    						    }
			        	    							$run = $_range['from'];
			        	    						}
			        	    					}
		        	    					}
		        	    				}
		        	    			}
        	    					break;
        	    				default:
        	    					$dataRange['middle'] = true;
        	    			}
        	    		}
        	    	}
        	    	foreach (array('toWeekDays', 'fromWeekDays', 'toW', 'from', 'to', 'in_out') as $index)
        	    	{
        	    		if (isset($dataRange[$index]) && !empty($dataRange[$index]))
        	    		{
        	    			$dataRange[$index] = array_unique($dataRange[$index]);
        	    		}
        	    	}
        	    	
        	    	$s .= '<td
        	    		class="abCalendarCell '.$class.'"
        	    		data-range="'. htmlentities($this->JSON->encode($dataRange)) .'"
        	    		data-cid="'.$this->calendarId.'" data-time="'.$timestamp.'" data-dayofweek="'.date('N', $timestamp).'">';
        	    }
    	               
    	        if ($d > 0 && $d <= $daysInMonth)
    	        {
    	        	$price = NULL;
    	        	$price_only = NULL;
    	        	$data_price = NULL;

    	        	//if (!is_null($_class) && array_key_exists($_class, $imageMap))
    	        	$_class = !empty($_class) ? preg_replace('/(\s*'.$this->classPartial.'\s*)/', '', $_class) : '';
    	        	if (!is_null($_class) && array_key_exists($_class, $imageMap))
    	        	{
    	        		$s .= '<div class="abCalendarCellInner">';
    	        		$s .= sprintf('<div class="abImageWrap"><img src="%s?rand=%u" class="abImage" alt="" /></div>', $imageMap[$_class], $rand);
    	        	}
    	        	$priceMin = pjUtil::converToCurrencyFormat(@$this->prices[$timestamp]['priceMin'], $this->options);
    	        	if ($this->isPrice) {
    	        		 $price = '<p class="'.$this->classPriceStatic.'">'.(isset($this->prices[$timestamp]) ? pjCurrency::formatPrice($priceMin) : $this->getNA()).'</p>';
    	        		 $s .= '<div class="'.$this->classLinkDate.'"><span class="abCalendarLinkDateInner">1'.$d.'</span></div>'.$price;
    	        	} else {
    	        		if ($this->showPrices)
    	        		{
    	        			$price_only = (
    	        				isset($this->prices[$timestamp]) ?
    	        				($this->prices[$timestamp]['priceNum'] > 1 ? __('lblPriceFrom', true) ." ". pjCurrency::formatPrice($priceMin) : pjCurrency::formatPrice($priceMin)) :
    	        				$this->getNA()
    	        			);
    	        			//$price = '<span class="'.$this->classPrice.'">'.$price_only.'</span>';
    	        			$data_price = sprintf(' data-price="%s"', pjSanitize::html($price_only));
    	        		}
    	        		
    	        		/*$res_left_class = '';
    	        		$res_right_class = '';
    	        		if($scope == 1)
    	        		{
	    	        		if($reservationsInfo[$timestamp]['is_change_over'] == 0)
	    	        		{
	    	        			if(isset($reservationsInfo[$timestamp]['in']))
	    	        			{
	    	        				if($reservationsInfo[$timestamp]['in']['status'] == 'Pending')
	    	        				{
	    	        					$res_left_class = ' abLeftPending';
	    	        					$res_right_class = ' abRightPending';
	    	        				}else{
	    	        					$res_left_class = ' abLeftConfirmed';
	    	        					$res_right_class = ' abLeftConfirmed';
	    	        				}
	    	        			}
	    	        			if(isset($reservationsInfo[$timestamp]['start']))
	    	        			{
	    	        				if($reservationsInfo[$timestamp]['start']['status'] == 'Pending')
	    	        				{
	    	        					$res_left_class = ' abLeftPending';
	    	        					$res_right_class = ' abRightPending';
	    	        				}else{
	    	        					$res_left_class = ' abLeftConfirmed';
	    	        					$res_right_class = ' abLeftConfirmed';
	    	        				}
	    	        			}
	    	        			if(isset($reservationsInfo[$timestamp]['end']))
	    	        			{
	    	        				if($reservationsInfo[$timestamp]['end']['status'] == 'Pending')
	    	        				{
	    	        					$res_left_class = ' abLeftPending';
	    	        					$res_right_class = ' abRightPending';
	    	        				}else{
	    	        					$res_left_class = ' abLeftConfirmed';
	    	        					$res_right_class = ' abLeftConfirmed';
	    	        				}
	    	        			}
	    	        		}else{
	    	        			if(isset($reservationsInfo[$timestamp]['start']))
	    	        			{
	    	        				if($reservationsInfo[$timestamp]['start']['status'] == 'Pending')
	    	        				{
	    	        					$res_right_class = ' abRightPending';
	    	        				}else{
	    	        					$res_right_class = ' abRightConfirmed';
	    	        				}
	    	        			}
	    	        			if(isset($reservationsInfo[$timestamp]['end']))
	    	        			{
	    	        				if($reservationsInfo[$timestamp]['end']['status'] == 'Pending')
	    	        				{
	    	        					$res_left_class = ' abLeftPending';
	    	        				}else{
	    	        					$res_left_class = ' abLeftConfirmed';
	    	        				}
	    	        			}
	    	        		}
    	        		}	
    	        		$div_left = '<div class="abLeft'.$res_left_class.'"></div>';
    	        		$div_right = '<div class="abRight'.$res_right_class.'"></div>';
    	        		
    	        		$s .= '<div class="'.$this->classLinkDate.'"'.$data_price.'><span class="abCalendarLinkDateInner">'.$d.'</span>'.$price.$div_left.$div_right.'</div>';*/
    	        		$s .= '<div class="'.$this->classLinkDate.'"'.$data_price.'><span class="abCalendarLinkDateInner">'.$d.'</span>'.$price.'</div>';
    	        	}
    	        	if (!is_null($_class) && array_key_exists($_class, $imageMap))
    	        	{
    	        		$s .= '</div>';
    	        	}
    	        	
    	            //$link = $this->getDateLink($d, $month, $year);
    	            //$s .= $link == "" ? $d : '<a rel="'.$timestamp.'" href="'.$link['href'].'"'.(!empty($link['onclick']) ? ' onclick="'.$link['onclick'].'"' : NULL).' class="'.$link['class'].'">'.$d.'</a>';

    	        	//$s .= '<p class="'.$this->classLinkDate.'">'.$d.$price.'</p>';
    	        } else {
    	            $s .= "&nbsp;";
    	        }
      	        $s .= "</td>\n";
        	    $d++;
    	    }
    	    if ($this->weekNumbers)
    	    {
    	    	$s = str_replace('{WEEK_NUM}', date("W", $timestamp), $s);
    	    }
    	    $s .= "</tr>\n";
    	    $rows++;
    	}
    	
    	if ($rows == 5)
    	{
    		if ($cols == 7)
    		{
    			$s .= "<tr>" . str_repeat('<td class="'.$this->classEmpty.'">&nbsp;</td>', $cols) . "</tr>";
    		} else {
    			$s .= '<tr><td class="abCalendarWeekNum">&nbsp;</td>' . str_repeat('<td class="'.$this->classEmpty.'">&nbsp;</td>', 7) . "</tr>";
    		}
    	}
    	
    	$s .= "</tbody></table>\n";

    	return $s;
    }

    static public function adjustDate($month, $year)
    {
        $a = array();
        $a[0] = $month;
        $a[1] = $year;
        
        while ($a[0] > 12)
        {
            $a[0] -= 12;
            $a[1]++;
        }
        
        while ($a[0] <= 0)
        {
            $a[0] += 12;
            $a[1]--;
        }
        
        return $a;
    }    
}
?>