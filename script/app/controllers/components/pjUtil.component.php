<?php
if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjUtil extends pjToolkit
{
    static public function html2rgb($color)
    {
        $color = str_replace('#', '', $color);
        if (strlen($color) == 6)
        {
            list($r, $g, $b) = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
        } elseif (strlen($color) == 3) {
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        } else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        
        return array($r, $g, $b);
    }
    
    static public function uuid()
    {
        return chr(rand(65,90)) . chr(rand(65,90)) . time();
    }
    
	static public function getClass($arr, $date, $tomorrow, $yesterday, $bookings_per_day, $haystack=array())
    {
        /*
         * 1=available
         * 2=booked
         * 3=pending
         */
        
        $default = array(
            'calendarStatus1' => 'calendarStatus1',
            'calendarStatus2' => 'calendarStatus2',
            'calendarStatus3' => 'calendarStatus3',
            'calendarStatus_1_2' => 'calendarStatus_1_2',
            'calendarStatus_1_3' => 'calendarStatus_1_3',
            'calendarStatus_2_1' => 'calendarStatus_2_1',
            'calendarStatus_2_3' => 'calendarStatus_2_3',
            'calendarStatus_3_1' => 'calendarStatus_3_1',
            'calendarStatus_3_2' => 'calendarStatus_3_2',
            'calendarStatusPartial' => 'calendarStatusPartial'
        );
        
        $stack = array_merge($default, $haystack);
        
        $class = NULL;
        if (isset($arr[$date]))
        {
            if($arr[$date]['status_morning'] == 1 && $arr[$date]['status_afternoon'] == 1)
            {
                $class = $stack['calendarStatus1'];
            }else if($arr[$date]['status_morning'] == 2 && $arr[$date]['status_afternoon'] == 2){
                $class = $stack['calendarStatus2'];
            }else if($arr[$date]['status_morning'] == 3 && $arr[$date]['status_afternoon'] == 3){
                $class = $stack['calendarStatus3'];
            }else if($arr[$date]['status_morning'] == 1 && $arr[$date]['status_afternoon'] == 2){
                $class = $stack['calendarStatus_1_2'];
            }else if($arr[$date]['status_morning'] == 1 && $arr[$date]['status_afternoon'] == 3){
                $class = $stack['calendarStatus_1_3'];
            }else if($arr[$date]['status_morning'] == 2 && $arr[$date]['status_afternoon'] == 1){
                $class = $stack['calendarStatus_2_1'];
            }else if($arr[$date]['status_morning'] == 2 && $arr[$date]['status_afternoon'] == 3){
                $class = $stack['calendarStatus_2_3'];
            }else if($arr[$date]['status_morning'] == 3 && $arr[$date]['status_afternoon'] == 1){
                $class = $stack['calendarStatus_3_1'];
            }else if($arr[$date]['status_morning'] == 3 && $arr[$date]['status_afternoon'] == 2){
                $class = $stack['calendarStatus_3_2'];
            }
        	if($arr[$date]['is_limit_reached'] == 0)
            {
                $class .= ' ' . $stack['calendarStatusPartial'];
            }
        }
        return $class;
    }
    
	static public function fixSingleDay($arr)
    {
        //1 Free
        //2 Reserved
        //3 Pending
        foreach ($arr as $key => $item)
        {
            if (!isset($item['confirmed']) && !isset( $item['pending']))
            {
                $arr[$key]['status'] = 1;
            } elseif (isset($item['confirmed']) && $item['confirmed'] == 1 && !isset( $item['pending'])) {
                $arr[$key]['status'] = 2;
            } elseif (!isset($item['confirmed']) && isset( $item['pending']) && $item['pending'] == 1) {
                $arr[$key]['status'] = 3;
            } elseif (isset($item['confirmed']) && $item['confirmed'] == 1 && isset( $item['pending']) && $item['pending'] == 1) {
                // Nights
                if (isset($item['start']['status']))
                {
                    switch ($item['start']['status'])
                    {
                        case 'Pending':
                            $arr[$key]['status'] = 3;
                            break;
                        case 'Confirmed':
                            $arr[$key]['status'] = 2;
                            break;
                    }
                }
            } elseif (!isset($item['confirmed']) && isset($item['pending']) && $item['pending'] == 2) {
                // Nights
                $arr[$key]['status'] = 3;
            } elseif (isset($item['confirmed']) && $item['confirmed'] == 2 && !isset($item['pending']) ) {
                // Nights
                $arr[$key]['status'] = 2;
            }else{
                $arr[$key]['status'] = 1;
            }
        }
        
        return $arr;
    }
    
    static public function pjActionGenerateImages($cid, $data)
    {
        pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_reserved_start.jpg', str_replace('#', '', $data['o_background_available']), str_replace('#', '', $data['o_background_booked']));
        pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_reserved_end.jpg', str_replace('#', '', $data['o_background_booked']), str_replace('#', '', $data['o_background_available']));
        pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_pending_pending.jpg', str_replace('#', '', $data['o_background_pending']), str_replace('#', '', $data['o_background_pending']));
        pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_reserved_pending.jpg', str_replace('#', '', $data['o_background_booked']), str_replace('#', '', $data['o_background_pending']));
        pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_pending_reserved.jpg', str_replace('#', '', $data['o_background_pending']), str_replace('#', '', $data['o_background_booked']));
        pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_reserved_reserved.jpg', str_replace('#', '', $data['o_background_booked']), str_replace('#', '', $data['o_background_booked']));
        pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_pending_start.jpg', str_replace('#', '', $data['o_background_available']), str_replace('#', '', $data['o_background_pending']));
        pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_pending_end.jpg', str_replace('#', '', $data['o_background_pending']), str_replace('#', '', $data['o_background_available']));
    }
    
    static private function pjActionBackgroundImage($dst, $color_1, $color_2, $w=200, $h=200)
    {
        if (!extension_loaded('gd') || !function_exists('gd_info'))
        {
            return false;
        }
        # Spatial_anti-aliasing. Make an image larger then it's intended
        $width = $w * 10;
        $height = $h * 10;
        
        $image = imagecreatetruecolor($width, $height);
        if ($image === false)
        {
            return false;
        }
        if (function_exists('imageantialias'))
        {
            imageantialias($image, true);
        }
        $backgroundColor = pjUtil::html2rgb($color_1);
        $color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
        if ($color !== false && $color !== -1)
        {
            if (function_exists('imagefilledrectangle'))
            {
                //imagefill($image, 0, 0, $color); // this one lead to an unexpected problem, works on localhost, but not at the server
                imagefilledrectangle($image, 0, 0, $width, $height, $color);
            }
        }
        if (isset($color_2) && !empty($color_2))
        {
            if ($color_1 == $color_2)
            {
                $backgroundColor = pjUtil::html2rgb('ffffff');
                $color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
                
                $values = array(
                    0, $height-2,
                    $width-2, 0,
                    $width, 0,
                    $width, 1,
                    1, $height,
                    0, $height,
                    0, $height-1
                );
                if (PHP_VERSION_ID <= 80000) {
                    imagefilledpolygon($image, $values, 7, $color);
                } else {
                    imagefilledpolygon($image, $values, $color);
                }
            } else {
                $backgroundColor = pjUtil::html2rgb($color_2);
                $color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
                $values = array(
                    $width,  0,  // Point 1 (x, y)
                    $width,  $height, // Point 2 (x, y)
                    0, $height,
                    $width,  0
                );
                if (PHP_VERSION_ID <= 80000) {
                    imagefilledpolygon($image, $values, 4, $color);
                } else {
                    imagefilledpolygon($image, $values, $color);
                }
            }
        }
        # Shrink it down to remove the aliasing and make it it's intended size
        $new_image = imagecreatetruecolor($w, $h);
        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $w, $h, $width, $height);
        # save image
        imagejpeg($new_image, $dst, 100);
        imagedestroy($image);
        imagedestroy($new_image);
    }
    
    static public function getWeekRange($date, $week_start)
    {
        $week_arr = array(0=>'sunday',
            1=>'monday',
            2=>'tuesday',
            3=>'wednesday',
            4=>'thursday',
            5=>'friday',
            6=>'saturday');
        
        $ts = strtotime($date);
        $start = (date('w', $ts) == 0) ? $ts : strtotime('last ' . $week_arr[$week_start], $ts);
        $week_start = ($week_start == 0 ? 6 : $week_start -1);
        return array(date('Y-m-d', $start), date('Y-m-d', strtotime('next ' . $week_arr[$week_start], $start)));
    }
    
    static public function getComingWhere($period, $week_start)
    {
        $where_str = '';
        switch ($period) {
            case 1:
                $where_str = "(CURDATE() BETWEEN t1.date_from AND t1.date_to)";
                break;
                ;
            case 2:
                $where_str = "(DATE(DATE_ADD(NOW(), INTERVAL 1 DAY)) BETWEEN t1.date_from AND t1.date_to)";
                break;
                ;
            case 3:
                list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d'), $week_start);
                $where_str = "((t1.date_from BETWEEN CURDATE() AND '$end_week') OR
							   (t1.date_to BETWEEN CURDATE() AND '$end_week') OR
							   (t1.date_from <= CURDATE() AND t1.date_to >= '$end_week'))";
                break;
                ;
            case 4:
                list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d', strtotime("+7 days")), $week_start);
                $where_str = "((t1.date_from BETWEEN '$start_week' AND '$end_week') OR
							   (t1.date_to BETWEEN '$start_week' AND '$end_week') OR
							   (t1.date_from <= '$start_week' AND t1.date_to >= '$end_week'))";
                break;
                ;
            case 5:
                $end_month = date('Y-m-t',strtotime('this month'));
                $where_str = "((t1.date_from BETWEEN CURDATE() AND '$end_month') OR
							   (t1.date_to BETWEEN CURDATE() AND '$end_month') OR
							   (t1.date_from <= CURDATE() AND t1.date_to >= '$end_month'))";
                break;
                ;
            case 6:
                $start_month = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, 1, date("Y")));
                $end_month = date("Y-m-d", mktime(0, 0, 0, date("m") + 2, 0, date("Y")));
                $where_str = "((t1.date_from BETWEEN '$start_month' AND '$end_month') OR
							   (t1.date_to BETWEEN '$start_month' AND '$end_month') OR
							   (t1.date_from <= '$start_month' AND t1.date_to >= '$end_month'))";
                break;
                ;
        }
        return $where_str;
    }
    
    static public function getMadeWhere($period, $week_start)
    {
        $where_str = '';
        switch ($period) {
            case 1:
                $where_str = "(DATE(t1.created) = CURDATE() OR DATE(t1.modified) = CURDATE())";
                break;
                ;
            case 2:
                $where_str = "(DATE(t1.created) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) OR DATE(t1.modified) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)))";
                break;
                ;
            case 3:
                list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d'), $week_start);
                $where_str = "((DATE(t1.created) BETWEEN '$start_week' AND '$end_week') OR (DATE(t1.modified) BETWEEN '$start_week' AND '$end_week'))";
                break;
                ;
            case 4:
                list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d', strtotime("-7 days")), $week_start);
                $where_str = "((DATE(t1.created) BETWEEN '$start_week' AND '$end_week') OR (DATE(t1.modified) BETWEEN '$start_week' AND '$end_week'))";
                break;
                ;
            case 5:
                $start_month = date('Y-m-01',strtotime('this month'));
                $end_month = date('Y-m-t',strtotime('this month'));
                $where_str = "((DATE(t1.created) BETWEEN '$start_month' AND '$end_month') OR (DATE(t1.modified) BETWEEN '$start_month' AND '$end_month'))";
                break;
                ;
            case 6:
                $start_month = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1, date("Y")));
                $end_month = date("Y-m-d", mktime(0, 0, 0, date("m"), 0, date("Y")));
                $where_str = "((DATE(t1.created) BETWEEN '$start_month' AND '$end_month') OR (DATE(t1.modified) BETWEEN '$start_month' AND '$end_month'))";
                break;
                ;
        }
        return $where_str;
    }
    
    static public function getTimezoneName($timezone)
    {
        $offset = $timezone / 3600;
        $timezone_name = timezone_name_from_abbr(null, $offset * 3600, true);
        if($timezone_name === false)
        {
            $timezone_name = timezone_name_from_abbr(null, $offset * 3600, false);
        }
        if($offset == -12)
        {
            $timezone_name = 'Pacific/Wake';
        }
        return $timezone_name;
    }
    
    static public function sortArrayByArray(Array $array, Array $orderArray)
    {
        $ordered = array();
        foreach($orderArray as $key)
        {
            if(array_key_exists($key,$array))
            {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }
    
    static public function convertToMySQLNumber($number, $option_arr)
    {
        $currency_format = isset($option_arr['o_currency_format']) ? (int) $option_arr['o_currency_format'] : 0;
        
        if($currency_format == 1 || $currency_format == 2)
        {
            if(strpos($number, ".") === false)
            {
                $number = preg_replace('/\s+/', '', $number);
                $number = preg_replace('/[,]/', '', $number);
                $number = (float) $number;
            }else{
                $number = preg_replace('/\s+/', '', $number);
                $number = preg_replace('/[,]/', '', $number);
                $number = (float) $number;
            }
        }else if($currency_format == 3 || $currency_format == 4){
            if(strpos($number, ",") === false)
            {
                if(strpos($number, ".") !== false)
                {
                    $number = (float) $number;
                }else{
                    $number = preg_replace('/\s+/', '', $number);
                    $number = preg_replace('/[.]/', '', $number);
                    $number = (float) $number;
                }
            }else{
                $number = preg_replace('/\s+/', '', $number);
                $number = preg_replace('/[.]/', '', $number);
                $number = preg_replace('/[,]/', '.', $number);
                $number = (float) $number;
            }
        }
        return $number;
    }
    
    static public function converToCurrencyFormat($number, $option_arr)
    {
        $currency_format = isset($option_arr['o_currency_format']) ? (int) $option_arr['o_currency_format'] : 0;
        
        switch ($currency_format) {
            case 1:
                $number = number_format($number, 2, '.', ',');
                break;
            case 2:
                $number = number_format($number, 2, '.', ' ');
                break;
            case 3:
                $number = number_format($number, 2, ',', '.');
                break;
            case 4:
                $number = number_format($number, 2, ',', ' ');
                break;
        }
        return $number;
    }
    
	static public function getCommonOptions()
	{
		return array('private_key', 'o_month_year_format');
	}
	
	public static function toMomemtJS($format)
	{
	    return str_replace(
	        array('Y', 'm', 'n', 'd', 'j'),
	        array('YYYY', 'MM', 'M', 'DD', 'D'),
	        $format);
	}
	
	public static function toBootstrapDate($format)
	{
	    return str_replace(
	        array('Y', 'm', 'n', 'd', 'j'),
	        array('yyyy', 'mm', 'm', 'dd', 'd'),
	        $format);
	}
	
	public static function getNights($date_from, $date_to, $price_based_on = 'days')
	{
	    $seconds = abs(strtotime($date_to) - strtotime($date_from));
	    $rental_days = floor($seconds / 86400);
	    if($price_based_on == 'days')
	    {
	        $rental_days += 1;
	    }
	    return $rental_days;
	}
	
	static public function textToHtml($content)
	{
		return '<html><head><title></title></head><body>'.stripslashes($content).'</body></html>';
	}
}
?>