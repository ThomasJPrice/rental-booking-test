<?php
if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjReservationModel extends pjAppModel
{
    protected $primaryKey = 'id';
    
    protected $table = 'reservations';
    
    protected $schema = array(
        array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'calendar_id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'uuid', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
        array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
        array('name' => 'price_based_on', 'type' => 'enum', 'default' => ':NULL'),
        array('name' => 'c_name', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'c_email', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'c_phone', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'c_adults', 'type' => 'smallint', 'default' => ':NULL'),
        array('name' => 'c_children', 'type' => 'smallint', 'default' => ':NULL'),
        array('name' => 'c_notes', 'type' => 'text', 'default' => ':NULL'),
        array('name' => 'c_address', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'c_city', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'c_country', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'c_state', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'c_zip', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'modified', 'type' => 'datetime', 'default' => ':NULL'),
        array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
        array('name' => 'ip', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'payment_method', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'amount', 'type' => 'decimal', 'default' => ':NULL'),
        array('name' => 'extra_price', 'type' => 'decimal', 'default' => ':NULL'),
        array('name' => 'deposit', 'type' => 'decimal', 'default' => ':NULL'),
        array('name' => 'tax', 'type' => 'decimal', 'default' => ':NULL'),
        array('name' => 'tourist_tax', 'type' => 'decimal', 'default' => ':NULL'),
        array('name' => 'security', 'type' => 'decimal', 'default' => ':NULL'),
        array('name' => 'promo_amount', 'type' => 'decimal', 'default' => ':NULL'),
        array('name' => 'promo_code', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'promo_type', 'type' => 'enum', 'default' => ':NULL'),
        array('name' => 'discount_amount', 'type' => 'decimal', 'default' => ':NULL'),
        array('name' => 'cc_type', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'cc_num', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
        array('name' => 'cc_exp_month', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
        array('name' => 'cc_exp_year', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
        array('name' => 'cc_code', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
        array('name' => 'txn_id', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'processed_on', 'type' => 'datetime', 'default' => ':NULL'),
        array('name' => 'status', 'type' => 'enum', 'default' => ':NULL'),
        array('name' => 'locale_id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'provider_id', 'type' => 'tinyint', 'default' => '0')
    );
    
    protected $validate = array(
        'rules' => array(
            'calendar_id' => array(
                'pjActionNumeric' => true,
                'pjActionRequired' => true
            ),
            'uuid' => array(
                'pjActionAlphaNumeric' => true,
                'pjActionNotEmpty' => true,
                'pjActionRequired' => true
            ),
            'date_from' => array(
                'rule' => array('pjActionDate', 'ymd', '/\d{4}-\d{2}-\d{2}/'),
                'pjActionRequired' => true,
                'pjActionNotEmpty' => true
            ),
            'date_to' => array(
                'rule' => array('pjActionDate', 'ymd', '/\d{4}-\d{2}-\d{2}/'),
                'pjActionRequired' => true,
                'pjActionNotEmpty' => true
            ),
            'ip' => array(
                'pjActionRequired' => true,
                'pjActionNotEmpty' => true
            ),
            /*'payment_method' => array(
             'pjActionRequired' => true,
             'pjActionNotEmpty' => true
             ),*/
            'status' => array(
                'pjActionRequired' => true,
                'pjActionNotEmpty' => true
            )
        )
    );
    
    public static function factory($attr=array())
    {
        return new pjReservationModel($attr);
    }
    
	public function getInfo($calendar_id, $date_from, $date_to, $option_arr=array(), $id=NULL, $show_calendar=NULL)
    {
        $arr = array();
        $this->reset();
        if (!is_null($id))
        {
            $this->where('id !=', $id);
        }
        
        $r_arr = $this
        ->where('calendar_id', $calendar_id)
        ->where('status !=', 'Cancelled')
        ->where("( (`date_from` BETWEEN '$date_from' AND '$date_to') OR (`date_to` BETWEEN '$date_from' AND '$date_to') OR ('$date_from' BETWEEN `date_from` AND `date_to`) OR ('$date_to' BETWEEN `date_from` AND `date_to`))")
        ->findAll()
        ->getData();
        
        $l_arr = pjLimitModel::factory()
        ->where('calendar_id', $calendar_id)
        ->where('blocked', 'T')
        ->where("( (`date_from` BETWEEN '$date_from' AND '$date_to') OR (`date_to` BETWEEN '$date_from' AND '$date_to') OR ('$date_from' BETWEEN `date_from` AND `date_to`) OR ('$date_to' BETWEEN `date_from` AND `date_to`))")
        ->findAll()
        ->getData();
        
        foreach ($l_arr as $limit)
        {
            $limit_date_from = $limit['date_from'];
            $limit_date_to = ($option_arr['o_price_based_on'] == 'nights' && $limit['date_from'] == $limit['date_to']) ? date('Y-m-d', strtotime('+1 day',  strtotime($limit['date_to']))) : $limit['date_to'];
            
            if($limit['blocked_days'] != "" && $limit['blocked_days'] != "1|2|3|4|5|6|0")
            {
                $blocked_days = explode("|", $limit['blocked_days']);
                
                $dt_from = strtotime($date_from);
                $dt_to = strtotime($date_to);
                for($i = $dt_from; $i <= $dt_to; $i = strtotime('+1 day', $i))
                {
                    $week_day = date('w', $i);
                    
                    if(in_array($week_day, $blocked_days) && ($i >= strtotime($limit['date_from']) && $i <= strtotime($limit['date_to'])) )
                    {
                        $limit_date_from = date('Y-m-d', $i);
                        $limit_date_to = date('Y-m-d', $i);
                        if ($option_arr['o_price_based_on'] == 'nights')
                        {
                            $temp_ts = strtotime('+1 day', $i);
                            $week_day_to = date('w', $temp_ts);
                            while(in_array($week_day_to, $blocked_days))
                            {
                                $temp_ts = strtotime('+1 day', $temp_ts);
                                $week_day_to = date('w', $temp_ts);
                            }
                            $limit_date_to = date('Y-m-d', $temp_ts);
                            $i = $temp_ts;
                        }
                        $temp = array();
                        $temp['price_based_on'] = $option_arr['o_price_based_on'];
                        $temp['date_from'] = $limit_date_from;
                        $temp['date_to'] = $limit_date_to;
                        $temp['status'] = 'Confirmed';
                        $temp['from_limit'] = 1;
                        $temp['id'] = $limit['id']+$i;
                        $r_arr[] = $temp;
                    }
                }
            }else{
                $temp = array();
                $temp['price_based_on'] = $option_arr['o_price_based_on'];
                $temp['date_from'] = $limit_date_from;
                $temp['date_to'] = $limit_date_to;
                $temp['status'] = 'Confirmed';
                $temp['from_limit'] = 1;
                $temp['id'] = $limit['id'];
                $r_arr[] = $temp;
            }
        }
        
        if (count($r_arr) === 0)
        {
            return array();
        }
        
        $nights_mode = false;
        if ($option_arr['o_price_based_on'] == 'nights')
        {
            $nights_mode = true;
        }
        foreach ($r_arr as $res)
        {
            if(!empty($res['price_based_on']) && in_array($res['price_based_on'], array('nights', 'days')))
            {
                if($res['price_based_on'] == 'nights')
                {
                    $nights_mode = true;
                }else{
                    $nights_mode = false;
                }
            }
            $dt_from = strtotime($res['date_from']);
            $dt_to = strtotime($res['date_to']);
            for($i = $dt_from; $i <= $dt_to; $i = strtotime('+1 day', $i))
            {
                $arr[$i]['is_change_over'] = 0;
                $arr[$i]['reservation_id'] = $res['id'];
                $arr[$i]['count'] = isset($arr[$i]['count']) ? $arr[$i]['count'] + 1 : 1;
                if(($i == $dt_from || $i == $dt_to) && $nights_mode == true)
                {
                    $arr[$i]['is_change_over'] = 1;
                }
                if($i == $dt_from)
                {
                    $arr[$i]['start'] = array('id' => $res['id'], 'status' => $res['status']);
                    if($nights_mode == true)
                    {
                        $arr[$i]['start']['count'] = isset($arr[$i]['start']['count']) ? $arr[$i]['start']['count'] + 1 : 1;
                    }
                    if($nights_mode == true)
                    {
                    	if(isset($res['from_limit'])) {
                    		$arr[$i]['afternoon']['confirmed'] = (int) $option_arr['o_bookings_per_day'];
                    	} else {
	                        switch ($res['status'])
	                        {
	                            case 'Confirmed':
	                            	$arr[$i]['afternoon']['confirmed'] = isset($arr[$i]['afternoon']['confirmed']) ? $arr[$i]['afternoon']['confirmed'] + 1 : 1;
	                                break;
	                            case 'Pending':
	                                $arr[$i]['afternoon']['pending'] = isset($arr[$i]['afternoon']['pending']) ? $arr[$i]['afternoon']['pending'] + 1 : 1;
	                                break;
	                        }
                    	}
                    }else{
                    	if(isset($res['from_limit'])) {
                    		$arr[$i]['morning']['confirmed'] = (int) $option_arr['o_bookings_per_day'];
                            $arr[$i]['afternoon']['confirmed'] = (int) $option_arr['o_bookings_per_day'];
                    	} else {
	                        switch ($res['status'])
	                        {
	                            case 'Confirmed':
	                            	$arr[$i]['morning']['confirmed'] = isset($arr[$i]['morning']['confirmed']) ? $arr[$i]['morning']['confirmed'] + 1 : 1;
		                            $arr[$i]['afternoon']['confirmed'] = isset($arr[$i]['afternoon']['confirmed']) ? $arr[$i]['afternoon']['confirmed'] + 1 : 1;
	                                break;
	                            case 'Pending':
	                                $arr[$i]['morning']['pending'] = isset($arr[$i]['morning']['pending']) ? $arr[$i]['morning']['pending'] + 1 : 1;
	                                $arr[$i]['afternoon']['pending'] = isset($arr[$i]['afternoon']['pending']) ? $arr[$i]['afternoon']['pending'] + 1 : 1;
	                                break;
	                        }
                    	}
                    }
                }
                if($i == $dt_to)
                {
                    $arr[$i]['end'] = array('id' => $res['id'], 'status' => $res['status']);
                    if($nights_mode == true)
                    {
                    	if(isset($res['from_limit'])) {
                    		$arr[$i]['morning']['confirmed'] = (int) $option_arr['o_bookings_per_day'];
                    	} else {
	                        switch ($res['status'])
	                        {
	                            case 'Confirmed':
	                            	$arr[$i]['morning']['confirmed'] = isset($arr[$i]['morning']['confirmed']) ? $arr[$i]['morning']['confirmed'] + 1 : 1;
	                                break;
	                            case 'Pending':
	                                $arr[$i]['morning']['pending'] = isset($arr[$i]['morning']['pending']) ? $arr[$i]['morning']['pending'] + 1 : 1;
	                                break;
	                        }
                    	}
                    }else{
                    	if(isset($res['from_limit'])) {
                    		$arr[$i]['morning']['confirmed'] = (int) $option_arr['o_bookings_per_day'];
                            $arr[$i]['afternoon']['confirmed'] = (int) $option_arr['o_bookings_per_day'];
                    	} else {
	                        switch ($res['status'])
	                        {
	                            case 'Confirmed':
	                            	$arr[$i]['morning']['confirmed'] = isset($arr[$i]['morning']['confirmed']) ? $arr[$i]['morning']['confirmed'] + 1 : 1;
		                            $arr[$i]['afternoon']['confirmed'] = isset($arr[$i]['afternoon']['confirmed']) ? $arr[$i]['afternoon']['confirmed'] + 1 : 1;
	                                break;
	                            case 'Pending':
	                                $arr[$i]['morning']['pending'] = isset($arr[$i]['morning']['pending']) ? $arr[$i]['morning']['pending'] + 1 : 1;
	                                $arr[$i]['afternoon']['pending'] = isset($arr[$i]['afternoon']['pending']) ? $arr[$i]['afternoon']['pending'] + 1 : 1;
	                                break;
	                        }
                    	}
                    }
                }
                if($i > $dt_from && $i < $dt_to)
                {
                	if(isset($res['from_limit'])) {
                		$arr[$i]['morning']['confirmed'] = (int) $option_arr['o_bookings_per_day'];
		                $arr[$i]['afternoon']['confirmed'] = (int) $option_arr['o_bookings_per_day'];
                	} else { 
	                    switch ($res['status'])
	                    {
	                        case 'Confirmed':
	                        	$arr[$i]['morning']['confirmed'] = isset($arr[$i]['morning']['confirmed']) ? $arr[$i]['morning']['confirmed'] + 1 : 1;
		                        $arr[$i]['afternoon']['confirmed'] = isset($arr[$i]['afternoon']['confirmed']) ? $arr[$i]['afternoon']['confirmed'] + 1 : 1;
	                            break;
	                        case 'Pending':
	                            $arr[$i]['morning']['pending'] = isset($arr[$i]['morning']['pending']) ? $arr[$i]['morning']['pending'] + 1 : 1;
	                            $arr[$i]['afternoon']['pending'] = isset($arr[$i]['afternoon']['pending']) ? $arr[$i]['afternoon']['pending'] + 1 : 1;
	                            break;
	                    }
                	}
                    $arr[$i]['in'] = array('id' => $res['id'], 'status' => $res['status']);
                    if(isset($arr[$i]['start']))
                    {
                        unset($arr[$i]['start']);
                    }
                    if(isset($arr[$i]['end']))
                    {
                        unset($arr[$i]['end']);
                    }
                }
                switch ($res['status'])
                {
                    case 'Confirmed':
                        if(isset($res['from_limit']))
                        {
                            $arr[$i]['confirmed'] = (int) $option_arr['o_bookings_per_day'];
                        }else{
                            $arr[$i]['confirmed'] = isset($arr[$i]['confirmed']) ?  $arr[$i]['confirmed'] + 1 : 1;
                        }
                        break;
                    case 'Pending':
                        $arr[$i]['pending'] = isset($arr[$i]['pending']) ?  $arr[$i]['pending'] + 1 : 1;
                        break;
                }
            }
        }
        
        ksort($arr);
        foreach($arr as $timestamp => $v)
        {
            $v['status_morning'] = 1;
            $v['status_afternoon'] = 1;
            if (isset($v['morning']['confirmed']))
            {
            	if($v['morning']['confirmed'] >= (int) $option_arr['o_bookings_per_day'])
                {
                	$v['status_morning'] = 2;
				}else{
                	$v['status_morning'] = 3;
                }
            }else if(isset($v['morning']['pending'])){
                $v['status_morning'] = 3;
            }
            if (isset($v['afternoon']['confirmed']))
            {
            	if($v['afternoon']['confirmed'] >= (int) $option_arr['o_bookings_per_day'])
                {
                	$v['status_afternoon'] = 2;
                }else{
                	$v['status_afternoon'] = 3;
                }
            }else if(isset($v['afternoon']['pending'])){
                $v['status_afternoon'] = 3;
            }
            $arr[$timestamp] = $v;
        }
        
        foreach($arr as $timestamp => $v)
        {
            $count = 0;
            $multiplier = 1;
            if (isset($v['confirmed']))
            {
                $count += $v['confirmed'];
            }
            if ($v['is_change_over'] == 1)
            {
                $multiplier = 2;
            }
            $arr[$timestamp]['is_limit_reached'] = (int) ($count == (int) $option_arr['o_bookings_per_day'] * $multiplier);
            if ($v['count'] == 0)
            {
                $arr[$timestamp]['status'] = 1;
            } elseif ($count < (int) $option_arr['o_bookings_per_day'] * $multiplier) {
                $arr[$timestamp]['status'] = 3;
            } else {
                $arr[$timestamp]['status'] = 2;
                unset($arr[$timestamp]['reservation_id']);
            }
            $arr[$timestamp]['dt'] = date("d.m.Y", $timestamp);
        }
        return $arr;
    }
}
?>