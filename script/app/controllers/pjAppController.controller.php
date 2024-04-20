<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjBaseAppController
{
	public $models = array();

	public $defaultCalendarId = 'admin_calendar_id';
		
	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array('app/web/upload');
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}
		
		return $result;
	}
	
	public function beforeFilter()
    {
    	parent::beforeFilter();
    	if(!in_array($this->_get->toString('controller'), array('pjFront', 'pjInstaller')))
    	{
    		$pjCalendarModel = pjCalendarModel::factory();
    		if ($this->isOwner())
    		{
    			$pjCalendarModel->where('t1.user_id', $this->getUserId());
    		}
	    	$calendars = $pjCalendarModel
				->select('t1.*, t2.content AS `name`')
				->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->orderBy('t1.id ASC')
				->findAll()->getDataPair('id');
			$this->set('calendars', $calendars);
			
			if ($this->getCalendarId() === false && count($calendars) > 0)
			{
				$keys = array_keys($calendars);
				$this->setCalendarId($keys[0]);
				
			}
			
			$base_option_arr = pjBaseOptionModel::factory()->getPairs(1);
	        $script_option_arr = pjOptionModel::factory()->getPairs($this->getCalendarId());
	        $this->option_arr = array_merge($base_option_arr, $script_option_arr);
	        $this->set('option_arr', $this->option_arr);
	        
	        pjRegistry::getInstance()->set('options', $this->option_arr);
	        
	        $this->appendJs('pjAdminCore.js');
    	}
    	return true;
    }
	
	public function afterFilter()
	{
		parent::afterFilter();
	    if(!in_array($this->_get->toString('controller'), array('pjFront', 'pjInstaller')))
	    {
	        $this->appendCss('admin.css');
	    }
	}
	
	public function pjActionAfterInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
	    $pjAuthRolePermissionModel = pjAuthRolePermissionModel::factory();
	    $pjAuthUserPermissionModel = pjAuthUserPermissionModel::factory();
	    
	    $permissions = pjAuthPermissionModel::factory()->findAll()->getDataPair('key', 'id');
	    
	    $roles = array(1 => 'admin', 2 => 'editor', 3 => 'owner');
	    foreach ($roles as $role_id => $role)
	    {
	        if (isset($GLOBALS['CONFIG'], $GLOBALS['CONFIG']["role_permissions_{$role}"])
	        && is_array($GLOBALS['CONFIG']["role_permissions_{$role}"])
	        && !empty($GLOBALS['CONFIG']["role_permissions_{$role}"]))
	        {
	            $pjAuthRolePermissionModel->reset()->where('role_id', $role_id)->eraseAll();
	            
	            foreach ($GLOBALS['CONFIG']["role_permissions_{$role}"] as $role_permission)
	            {
	                if($role_permission == '*')
	                {
	                    // Grant full permissions for the role
	                    foreach($permissions as $key => $permission_id)
	                    {
	                        $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
	                    }
	                    break;
	                }
	                else
	                {
	                    $hasAsterix = strpos($role_permission, '*') !== false;
	                    if($hasAsterix)
	                    {
	                        $role_permission = str_replace('*', '', $role_permission);
	                    }
	                    
	                    foreach($permissions as $key => $permission_id)
	                    {
	                        if($role_permission == $key || ($hasAsterix && strpos($key, $role_permission) !== false))
	                        {
	                            $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
	                        }
	                    }
	                }
	            }
	        }
	    }
	    pjAuthRoleModel::factory()->setAttributes(array('id' => 3, 'role' => 'Owner', 'is_backend' => 'T', 'is_admin' => 'T'))->insert();
	    
	    // Grant full permissions to Admin
	    $user_id = 1; // Admin ID
	    $pjAuthUserPermissionModel->reset()->where('user_id', $user_id)->eraseAll();
	    foreach($permissions as $key => $permission_id)
	    {
	        $pjAuthUserPermissionModel->setAttributes(compact('user_id', 'permission_id'))->insert();
	    }
	    
		$id = pjCalendarModel::factory()->setAttributes(array('user_id' => 1, 'uuid' => pjUtil::uuid()))->insert()->getInsertId();
		if ($id !== false && (int) $id > 0)
		{
			pjMultiLangModel::factory()->saveMultiLang(array(
				1 => array('name' => 'Calendar 1')
			), $id, 'pjCalendar');
			
			$pjOptionModel = pjOptionModel::factory();
			$pjOptionModel->init($id);
			$pjOptionModel->initConfirmation($id, null);

			$data = $data = $pjOptionModel->reset()->getAllPairs($id);
			pjUtil::pjActionGenerateImages($id, $data);
		}
		
		return array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded');
	}
    
	public function getCalendarId()
	{
	    if (isset($_SESSION[$this->defaultCalendarId]))
	    {
	        return $_SESSION[$this->defaultCalendarId];
	    }
	    return false;
	}
	
	public function setCalendarId($calendar_id)
	{
	    $_SESSION[$this->defaultCalendarId] = (int) $calendar_id;
	    return $this;
	}
	
    public static function getTokens($booking_arr, $option_arr, $locale_id)
    {
        $payment_methods = __('payment_methods', true);
    	$na = __('lblNA', true, false);
    	$c_name = !empty($booking_arr['c_name']) ? @$booking_arr['c_name'] : $na;
    	$c_email = !empty($booking_arr['c_email']) ? @$booking_arr['c_email'] : $na;
    	$c_phone = !empty($booking_arr['c_phone']) ? @$booking_arr['c_phone'] : $na;
    	$c_adults = $booking_arr['c_adults'] != '' ? @$booking_arr['c_adults'] : $na;
    	$c_children = $booking_arr['c_children'] != '' ? @$booking_arr['c_children'] : $na;
    	$c_notes = !empty($booking_arr['c_notes']) ? @$booking_arr['c_notes'] : $na;
    	$c_address = !empty($booking_arr['c_address']) ? @$booking_arr['c_address'] : $na;
    	$c_city = !empty($booking_arr['c_city']) ? @$booking_arr['c_city'] : $na;
    	$country = !empty($booking_arr['country']) ? @$booking_arr['country'] : $na;
    	$c_state = !empty($booking_arr['c_state']) ? @$booking_arr['c_state'] : $na;
    	$c_zip = !empty($booking_arr['c_zip']) ? @$booking_arr['c_zip'] : $na;
    	$cc_type = !empty($booking_arr['cc_type']) ? @$booking_arr['cc_type'] : $na;
    	$cc_num = !empty($booking_arr['cc_num']) ? @$booking_arr['cc_num'] : $na;
    	$cc_exp_month = @$booking_arr['payment_method'] == 'creditcard' ? (!empty($booking_arr['cc_exp_month']) ? @$booking_arr['cc_exp_month'] : $na) : $na;
    	$cc_exp_year = @$booking_arr['payment_method'] == 'creditcard' ? (!empty($booking_arr['cc_exp_year']) ? @$booking_arr['cc_exp_year'] : $na) : $na;
    	$cc_code = !empty($booking_arr['cc_code']) ? @$booking_arr['cc_code'] : $na;
    	$payment_method = !empty($booking_arr['payment_method']) ? $payment_methods[$booking_arr['payment_method']] : $na;
    	
    	$security = NULL;
    	$tax = pjCurrency::formatPrice($booking_arr['tax']);
    	$tourist_tax = pjCurrency::formatPrice($booking_arr['tourist_tax']);
    	$amount = pjCurrency::formatPrice($booking_arr['amount']);

		if($option_arr['o_to_be_paid'] == 'when_booking') {
			$security = pjCurrency::formatPrice($booking_arr['security']);
		}

		if (!isset($booking_arr['total'])) {
			$total =  $booking_arr['amount'] + $booking_arr['extra_price'] - $booking_arr['discount_amount'] - $booking_arr['promo_amount'] + $booking_arr['tax'] + $booking_arr['tourist_tax'];
		}
		else {
			$total = $booking_arr['total'];
		}
    	$total_price =  pjCurrency::formatPrice($total);
    	$extra_price = pjCurrency::formatPrice($booking_arr['extra_price']);
    	$discount_amount = pjCurrency::formatPrice($booking_arr['discount_amount']);
    	$promo_amount = pjCurrency::formatPrice($booking_arr['promo_amount']);
    	$deposit = pjCurrency::formatPrice($booking_arr['deposit']);
    	
    	$cancelURL = sprintf("%sindex.php?controller=pjFront&action=pjActionCancel&cid=%u&locale=%u&id=%u&hash=%s", PJ_INSTALL_URL, @$booking_arr['calendar_id'], $locale_id, @$booking_arr['id'], sha1(@$booking_arr['id'] . PJ_SALT));
    	$cancelURL = '<a href="'.$cancelURL.'">'.$cancelURL.'</a>';
    	
    	$calendar_name = '';
		if(isset($booking_arr['calendar_id']) && (int) $booking_arr['calendar_id'] > 0)
		{
			$calendar = pjCalendarModel::factory()
			->select("t1.*, t2.content as name")
			->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCalendar' AND t2.locale = '".$locale_id."' AND t2.field = 'name'", 'left')
			->find($booking_arr['calendar_id'])->getData();
			$calendar_name = $calendar['name'];
		}
		
		$extra_arr = pjExtraModel::factory()
			->select('t1.*, t2.content AS name, t3.qty')
			->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
			->join('pjReservationExtra', 't1.id=t3.extra_id', 'left outer')
			->where('t1.status', 'T')
			->where("(t1.id IN (SELECT TCE.extra_id FROM `".pjCalendarExtraModel::factory()->getTable()."` AS `TCE` WHERE `TCE`.calendar_id='".$booking_arr['calendar_id']."') )")
			->where("(t3.reservation_id='".$booking_arr['id']."')")
			->orderBy('`name` ASC')
			->findAll()->getData();
		$extras = '';
		$temp_extra = array();
		$price_types = $option_arr['o_price_based_on'] == 'days' ? __('day_price_types', true) : __('price_types', true);
		foreach($extra_arr as $extra)
		{
			$temp_extra[] = $extra['qty'] . ' x ' . pjSanitize::html($extra['name']) . ' (' . pjCurrency::formatPrice($extra['price']) . ' ' . $price_types[$extra['price_type']] . ')';
		}
		$extras = join("<br/>", $temp_extra);
		
    	$search = array(
    		'{Name}', '{Email}', '{Phone}', '{Adults}', '{Children}',
    		'{Notes}', '{Address}', '{City}', '{Country}', '{State}',
    		'{Zip}', '{CCType}', '{CCNum}', '{CCExpMonth}', '{CCExpYear}',
    		'{CCSec}', '{PaymentMethod}', '{StartDate}', '{EndDate}', '{Deposit}',
    		'{Security}', '{Tax}', '{TouristTax}', '{Price}', '{TotalPrice}', '{ExtraPrice}', '{Discount}', '{Promo}', '{PromoCode}','{CalendarID}', '{ReservationID}',
    		'{ReservationUUID}', '{CancelURL}', '{CalendarName}', '{Extras}');
		$replace = array(
			$c_name, $c_email, $c_phone, $c_adults, $c_children,
			$c_notes, $c_address, $c_city, $country, $c_state,
			$c_zip, $cc_type, $cc_num, $cc_exp_month, $cc_exp_year,
			$cc_code, $payment_method, date(@$option_arr['o_date_format'], strtotime(@$booking_arr['date_from'])), date(@$option_arr['o_date_format'], strtotime(@$booking_arr['date_to'])), @$deposit,
			$security, $tax, $tourist_tax, $amount, @$total_price, @$extra_price, @$discount_amount, @$promo_amount, @$booking_arr['promo_code'], @$booking_arr['calendar_id'], @$booking_arr['id'],
			@$booking_arr['uuid'], $cancelURL, $calendar_name, $extras
		);
		return compact('search', 'replace');
    }
    
	public static function getSubjectMessage($notification, $locale_id)
    {
    	$variant = $notification['variant'] == 'confirmation' ? 'confirm' : $notification['variant'];
        $field = $variant . '_tokens_' . $notification['recipient'];
        $pjMultiLangModel = pjMultiLangModel::factory();
        $lang_message = $pjMultiLangModel
        ->reset()
        ->select('t1.*')
        ->where('t1.foreign_id', $notification['id'])
        ->where('t1.model','pjNotification')
        ->where('t1.locale', $locale_id)
        ->where('t1.field', $field)
        ->limit(0, 1)
        ->findAll()
        ->getData();
        $field = $variant . '_subject_' . $notification['recipient'];
        $lang_subject = $pjMultiLangModel
        ->reset()
        ->select('t1.*')
        ->where('t1.foreign_id',  $notification['id'])
        ->where('t1.model','pjNotification')
        ->where('t1.locale', $locale_id)
        ->where('t1.field', $field)
        ->limit(0, 1)
        ->findAll()
        ->getData();
        return compact('lang_message', 'lang_subject');
    }
    
	public static function getSmsMessage($notification, $locale_id)
    {
    	$variant = $notification['variant'] == 'confirmation' ? 'confirm' : $notification['variant'];
        $field = $variant . '_sms_' . $notification['recipient'];
        $pjMultiLangModel = pjMultiLangModel::factory();
        $lang_message = $pjMultiLangModel
        ->reset()
        ->select('t1.*')
        ->where('t1.foreign_id', $notification['id'])
        ->where('t1.model','pjNotification')
        ->where('t1.locale', $locale_id)
        ->where('t1.field', $field)
        ->limit(0, 1)
        ->findAll()
        ->getData();
        return compact('lang_message');
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function isEditor()
	{
		return $this->getRoleId() == 2;
	}

	public function isOwner()
	{
		return $this->getRoleId() == 3;
	}
	
	public function isPriceReady()
	{
		return $this->isAdmin() || $this->isEditor() || $this->isOwner();
	}
	
	public function isPeriodReady()
	{
		return $this->isAdmin() || $this->isEditor() || $this->isOwner();
	}
	
	public function isInvoiceReady()
	{
		return $this->isAdmin() || $this->isEditor() || $this->isOwner();
	}
	
	public function isCountryReady()
	{
		return $this->isAdmin();
	}
	
	public function isOneAdminReady()
	{
		return $this->isAdmin();
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	protected function pjActionCheckDt($date_from, $date_to, $calendar_id=NULL, $id=NULL, $backend=false)
	{
		$calendar_id = !empty($calendar_id) ? (int) $calendar_id : $this->getCalendarId();
		
		if ($backend && $calendar_id != $this->getCalendarId())
		{
			$option_arr = pjOptionModel::factory()->getPairs($calendar_id);
		} else {
			$option_arr = $this->option_arr;
		}
		$validate_booking_msg = __('validate_booking_msg', true);
		if ($option_arr['o_price_based_on'] == 'nights' && $date_from == $date_to && $option_arr['o_booking_behavior'] == 1)
		{
			return array('status' => 'ERR', 'code' => 100, 'text' => $validate_booking_msg[1]);
		}
		
		$pjReservationModel = pjReservationModel::factory();
		$pjLimitModel = pjLimitModel::factory();
		
		$info = $pjReservationModel
			->prepare(sprintf("SELECT `date_from`, `date_to` 
				FROM `%1\$s`
				WHERE `calendar_id` = :calendar_id
				%2\$s
				AND `status` != :status
				AND ((`date_from` BETWEEN :date_from AND :date_to)
				OR ( `date_to` BETWEEN :date_from AND :date_to)
				OR ( `date_from` <= :date_from AND `date_to` >= :date_to))",
				$pjReservationModel->getTable(), (!empty($id) ? " AND `id` != :id" : NULL),
				($option_arr['o_price_based_on'] == 'nights' ? '<' : '<='),
				($option_arr['o_price_based_on'] == 'nights' ? '>' : '>=')
			))
			->exec(array(
				'calendar_id' => $calendar_id,
				'status' => 'Cancelled',
				'date_from' => $date_from,
				'date_to' => $date_to,
				'id' => $id
			))
			->getData();

		$limit_info = $pjLimitModel
			->prepare(sprintf("SELECT `date_from`, `date_to`,`blocked_days`
				FROM `%1\$s`
				WHERE `calendar_id` = :calendar_id
				%2\$s
				AND `blocked` = :blocked
				AND ((`date_from` BETWEEN :date_from AND :date_to)
				OR ( `date_to` BETWEEN :date_from AND :date_to)
				OR ( `date_from` <= :date_from AND `date_to` >= :date_to))",
					$pjLimitModel->getTable(), NULL,
					($option_arr['o_price_based_on'] == 'nights' ? '<' : '<='),
					($option_arr['o_price_based_on'] == 'nights' ? '>' : '>=')
			))
			->exec(array(
					'calendar_id' => $calendar_id,
					'blocked' => 'T',
					'date_from' => $date_from,
					'date_to' => $date_to,
			))
			->getData();
		
		foreach ($limit_info as $limit)
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
						    $temp_ts =  strtotime('+1 day', $i);
							$week_day_to = date('w', $temp_ts);
							while(in_array($week_day_to, $blocked_days))
							{
							    $temp_ts =  strtotime('+1 day', $temp_ts);
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
						$info[] = $temp;
					}
				}
			}else{
				$temp = array();
				$temp['price_based_on'] = $option_arr['o_price_based_on'];
				$temp['date_from'] = $limit_date_from;
				$temp['date_to'] = $limit_date_to;
				$temp['status'] = 'Confirmed';
				$temp['from_limit'] = 1;
				$info[] = $temp;
			}
		}
			
		$morning = array();
		$afternoon = array();
		$av_arr = array();
		$booked_arr = array();
		$nights_mode = false;
		if ($option_arr['o_price_based_on'] == 'nights')
		{ 
			$nights_mode = true;
		}
		if(isset($info) && count($info)  >0)
		{
			foreach ($info as $res)
			{
				$dt_from = strtotime($res['date_from']);
				$dt_to = strtotime($res['date_to']);
				for($i = $dt_from; $i <= $dt_to; $i = strtotime('+1 day', $i))
				{
					if(!empty($res['price_based_on']) && in_array($res['price_based_on'], array('nights', 'days')))
					{
						if($res['price_based_on'] == 'nights')
						{
							$nights_mode = true;
						}
					}
					if (!isset($afternoon[$i])) {
						$afternoon[$i] = 0;
					}
					if (!isset($morning[$i])) {
						$morning[$i] = 0;
					}
					if (!isset($booked_arr[$i])) {
						$booked_arr[$i] = 0;
					}
					if ($i == $dt_from && $nights_mode){
						$afternoon[$i] += 1;
					}elseif ($i == $dt_to && $nights_mode) {
						$morning[$i] += 1;
					}else {
						$booked_arr[$i] += 1;
					}
				}
			}
		}
		$s_from = strtotime($date_from);
		$s_to = strtotime($date_to);	
  		for($z = $s_from; $z <= $s_to; $z = strtotime('+1 day', $z))
		{
			if(isset($booked_arr[$z]) || isset($morning[$z]) || isset($afternoon[$z])) 
			{
				$booked_value = isset($booked_arr[$z]) ? $booked_arr[$z] : 0;
				$monring_value = isset($morning[$z]) ? $morning[$z] : 0;
				$afternoon_value = isset($afternoon[$z]) ? $afternoon[$z] : 0;
				
				$booked_value += min($monring_value,$afternoon_value);
				$morning[$z] -= min($monring_value, $afternoon_value);
				$afternoon[$z] -= min($monring_value, $afternoon_value);
				
				$av_arr[$z] = $booked_value;
				if($morning[$z] >= $afternoon[$z])
				{
					if($z > $s_from && $z <= $s_to)
					{
						$av_arr[$z] = $booked_value + ($morning[$z] );
					}
				}else{
					if($z >= $s_from && $z < $s_to)
					{
						$av_arr[$z] = $booked_value + ($afternoon[$z]);
					}
				}
			}else{
				$av_arr[$z] = 0;
			}
		}
		$cnt = max($av_arr);
		
		if(empty($id))
		{
			if ($cnt < (int) $option_arr['o_bookings_per_day'])
			{
				$result = array('status' => 'OK', 'code' => 200, 'text' => '');
			} else {
				$result = array('status' => 'ERR', 'code' => 100, 'text' => $validate_booking_msg[2]);
			}
		}else{
			if ($cnt < (int) $option_arr['o_bookings_per_day'])
			{
				$result = array('status' => 'OK', 'code' => 200, 'text' => '');
			} else {
				$result = array('status' => 'ERR', 'code' => 100, 'text' => $validate_booking_msg[2]);
			}
		}
		return $result;
	}
	
	protected function pjActionCalcPrices($calendar_id, $start_dt, $end_dt, $adults, $children, $extras, $extras_qty, $promo_code, $option_arr, $locale_id)
	{
		$tourist_tax = 0;
		$amount = 0;
		$extra_price = 0;
		$sub_total = 0;
		$tax = 0;
		$total = 0;
		$deposit = 0;
		$net = 0;
		$security = 0;
		$promo_valid = 0;
		$promo_amount = 0;
		$promo_type = '';
		$promo_percentage = 0;
		$discount_valid = 0;
		$discount_check = 0;
		$discount_amount = 0;
		$discount_type = '';
		$discount_percentage = 0;
		$discount_name = '';
		$temp_discount_amount;
		
		$nights = 0;
		$early_nights = 0;
		if ($end_dt > $start_dt) {
			$new_start_date = new DateTime(date('Y-m-d', $start_dt));
			$new_end_date = new DateTime(date('Y-m-d', $end_dt));
			$nights = $new_end_date->diff($new_start_date)->format("%a");
		}
		
		$new_start_early_nights = new DateTime(date('Y-m-d', $start_dt));
		$new_end_early_nights = new DateTime(date('Y-m-d', time()));
		$early_nights = $new_end_early_nights->diff($new_start_early_nights)->format("%a");
		
		if ($option_arr['o_price_based_on'] == 'days')
		{
			$nights += 1;
			$early_nights += 1;
		}
		
		$price_arr = array();
		if ($option_arr['o_price_plugin'] == 'price')
		{
			$price_arr = pjPriceModel::factory()->getPrice(
					$calendar_id,
					date("Y-m-d", $start_dt),
					date("Y-m-d", $end_dt),
					$this->option_arr,
					@$adults,
					(int) $option_arr['o_bf_children'] !== 1 ? @$children : 0
			);
		
		} elseif ($option_arr['o_price_plugin'] == 'period') {
			$price_arr = pjPeriodModel::factory()->getPrice(
					$calendar_id,
					date("Y-m-d", $start_dt),
					date("Y-m-d", $end_dt),
					$this->option_arr,
					@$adults,
					(int) $option_arr['o_bf_children'] !== 1 ? @$children : 0
			);
		}
		$people = @$adults + ((int) $option_arr['o_bf_children'] !== 1 ? @$children : 0);
		$tourist_taxt_per_nights = 0;
		if((float)$option_arr['o_tourist_tax'] > 0)
		{
			$tourist_taxt_per_nights = (float)$option_arr['o_tourist_tax'] * $nights;
		}
		if(isset($option_arr['o_max_tax']) && (float)$option_arr['o_max_tax'] > 0 && $tourist_taxt_per_nights > (float)$option_arr['o_max_tax'])
		{
			$tourist_taxt_per_nights = (float)$option_arr['o_max_tax'];
		}
		$tourist_tax = $people * $tourist_taxt_per_nights;
		
		$net = $price_arr['net'];
		$amount = $price_arr['amount'];
		//$tax = $price_arr['tax'];
		
		if(isset($extras) && !empty($extras))
		{
			$extra_arr = pjExtraModel::factory()->whereIn('t1.id', array_keys($extras))->findAll()->getData();
			foreach($extra_arr as $k => $v)
			{
				$qty = $extras_qty[$v['id']];
				switch ($v['price_type']) {
					case 'person':
				 		$extra_price += $v['price'] * $qty * $people;
					break;
					
					case 'night':
						$extra_price += $v['price'] * $qty * $nights;
					break;
					
					case 'person_night':
						$extra_price += $v['price'] * $qty * $people * $nights;
					break;
					case 'count':
						$extra_price += $v['price'] * $qty;
					break;
					case 'count_night':
						$extra_price += $v['price'] * $qty * $nights;
					break;
					case 'one_time':
						$extra_price += $v['price'] * $qty;
					break;
				}
			}
		}
		
		$sub_total = $amount + $extra_price;
		
		if($promo_code != '')
		{
			$voucher_arr = pjVoucherModel::factory()
				->where('code', $promo_code)
				->where("t1.id IN (SELECT TCV.voucher_id FROM `".pjCalendarVoucherModel::factory()->getTable()."` AS `TCV` WHERE `TCV`.calendar_id='".$calendar_id."') ")
				->findAll()
				->getData();
			if(!empty($voucher_arr))
			{
				$voucher_arr = $voucher_arr[0];
				switch ($voucher_arr['condition']) {
					case 'period':
						$date_from_ts = strtotime($voucher_arr['date_from']);
						$date_to_ts = strtotime($voucher_arr['date_to']);
						//if( ($start_dt >= $date_from_ts && $start_dt <= $date_to_ts) || ($end_dt >= $date_from_ts && $end_dt <= $date_to_ts) || ($date_from_ts >= $start_dt && $date_from_ts <= $end_dt ) || ($date_to_ts >= $start_dt && $date_to_ts <= $end_dt ))
						if ($start_dt >= $date_from_ts && $end_dt <= $date_to_ts)
						{
							$promo_valid = 1;
						}
						break;
		
					case 'made':
						$date_from_ts = strtotime($voucher_arr['date_from']);
						$date_to_ts = strtotime($voucher_arr['date_to']);
						if (time() >= $date_from_ts && time() <= $date_to_ts)
						{
							$promo_valid = 1;
						}
						break;
							
					case 'both':
						$date_from_ts = strtotime($voucher_arr['date_from']);
						$date_to_ts = strtotime($voucher_arr['date_to']);
						//if( (($start_dt >= $date_from_ts && $start_dt <= $date_to_ts) || ($end_dt >= $date_from_ts && $end_dt <= $date_to_ts) || ($date_from_ts >= $start_dt && $date_from_ts <= $end_dt ) || ($date_to_ts >= $start_dt && $date_to_ts <= $end_dt )) && (time() >= $date_from_ts && time() <= $date_to_ts))
						if (($start_dt >= $date_from_ts && $end_dt <= $date_to_ts) && (time() >= $date_from_ts && time() <= $date_to_ts))
						{
							$promo_valid = 1;
						}
						break;
				}
				if($promo_valid == 1)
				{
					$cnt_reservations = pjReservationModel::factory()->where('status', 'confirmed')->where('promo_code', $promo_code)->findCount()->getData();
					if($cnt_reservations >= (int) $voucher_arr['used_count'])
					{
						$promo_valid = 0;
					}else{
						$promo_amount = (float) $voucher_arr['discount'];
						$promo_type = $voucher_arr['type'];
						if($voucher_arr['type'] == 'percent')
						{
							$promo_percentage = (float) $voucher_arr['discount'];
							if($voucher_arr['apply_on'] == 'both')
							{
								$promo_amount = ($amount + $extra_price) * (float) $voucher_arr['discount'] / 100;
							}else if($voucher_arr['apply_on'] == 'room'){
								$promo_amount = ($amount) * (float) $voucher_arr['discount'] / 100;
							}else if($voucher_arr['apply_on'] == 'extra'){
								$promo_amount = ($extra_price) * (float) $voucher_arr['discount'] / 100;
							}
						}
					}
				}
			}
		}
		
		
		$discount_arr = pjDiscountModel::factory()
			->select('t1.*, t2.content as name')
			->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjDiscount' AND t2.locale = '".$locale_id."' AND t2.field = 'name'", 'left')
			->where("(t1.id IN (SELECT TCV.discount_id FROM `".pjCalendarDiscountModel::factory()->getTable()."` AS `TCV` WHERE `TCV`.calendar_id='".$calendar_id."'))")
			->findAll()
			->getData();
		
		foreach($discount_arr as $k => $v)
		{
			$condition = false;
			switch ($v['condition']) {
				case 'period':
					$date_from_ts = strtotime($v['date_from']);
					$date_to_ts = strtotime($v['date_to']);
					//if( ($start_dt >= $date_from_ts && $start_dt <= $date_to_ts) || ($end_dt >= $date_from_ts && $end_dt <= $date_to_ts) || ($date_from_ts >= $start_dt && $date_from_ts <= $end_dt ) || ($date_to_ts >= $start_dt && $date_to_ts <= $end_dt ))
					if ($start_dt >= $date_from_ts && $end_dt <= $date_to_ts)
					{
						$condition = true;
					}
					break;
			
				case 'made':
					$date_from_ts = strtotime($v['date_from']);
					$date_to_ts = strtotime($v['date_to']);
					if (time() >= $date_from_ts && time() <= $date_to_ts)
					{
						$condition = true;
					}
					break;
						
				case 'both':
					$date_from_ts = strtotime($v['date_from']);
					$date_to_ts = strtotime($v['date_to']);
					//if( (($start_dt >= $date_from_ts && $start_dt <= $date_to_ts) || ($end_dt >= $date_from_ts && $end_dt <= $date_to_ts) || ($date_from_ts >= $start_dt && $date_from_ts <= $end_dt ) || ($date_to_ts >= $start_dt && $date_to_ts <= $end_dt )) && (time() >= $date_from_ts && time() <= $date_to_ts))
					if (($start_dt >= $date_from_ts && $end_dt <= $date_to_ts) && (time() >= $date_from_ts && time() <= $date_to_ts))
					{
						$condition = true;
					}
					break;
			}
			if($condition == true)
			{
				switch ($v['options']) {
					case 'early':
						if($early_nights >= (int) $v['early_days'])
						{
							$discount_check = 1;
						}
						break;
				
					case 'persons':
						if($people >= (int) $v['min_persons'] && $people <= (int) $v['max_persons'])
						{
							$discount_check = 1;
						}
						break;
							
					case 'family':
						if((int) $option_arr['o_bf_children'] !== 1 && (int) @$children > 0)
						{
							$discount_check = 1;
						}
						break;
					case 'duration':
						if($nights >= (int) $v['min_duration'] && $nights <= (int) $v['max_duration'])
						{
							$discount_check = 1;
						}	
						break;
				}
				if($discount_check == 1)
				{
					$temp_discount_amount = (float) $v['discount'];
					if($v['type'] == 'percent')
					{
						$temp_discount_amount = ($amount + $extra_price) * (float) $v['discount'] / 100;
					}
					if($temp_discount_amount >= $discount_amount)
					{
						$discount_amount = $temp_discount_amount;
						$discount_name = pjSanitize::html($v['name']);
						$discount_type = $v['type'];
						if($v['type'] == 'percent')
						{
							$discount_percentage = (float) $v['discount'];
						}
					}
					$discount_valid = 1;
					$discount_check = 0;
				}
			}
		}

		$amount_after_discount = $sub_total - $promo_amount - $discount_amount;
		if ($amount_after_discount < 0) {
			$amount_after_discount = 0;
		}
		if ($amount_after_discount > 0 && isset($option_arr['o_tax']) && (float) $option_arr['o_tax'] > 0)
		{
			$tax = ($amount_after_discount * (float) $option_arr['o_tax']) / 100;
		}
		
		if (isset($option_arr['o_security']) && (float) $option_arr['o_security'] > 0)
		{
			$security = (float) $option_arr['o_security'];
		}
		$date = new DateTime();
		$date->modify('+'.$option_arr['o_require_all_within'].' day');
		if (isset($option_arr['o_require_all_within']) && (int) $option_arr['o_require_all_within'] > 0 && $date->getTimestamp() >= $start_dt)
		{
		    $deposit = $amount_after_discount + $tax + $tourist_tax + $security;
		} elseif (isset($option_arr['o_deposit']) && (float) $option_arr['o_deposit'] > 0) {
				
			switch ($option_arr['o_deposit_type'])
			{
				case 'percent':
					$deposit = (($amount_after_discount + $tax + $tourist_tax) * (float) $option_arr['o_deposit']) / 100 + $security;
					break;
				case 'amount':
					$deposit = (float) $option_arr['o_deposit'] + $security;
					break;
			}
		}
		$total = $amount_after_discount + $tax + $tourist_tax;
		if($option_arr['o_to_be_paid'] == 'on_arrival' && $deposit > 0)
		{
			$deposit = $deposit - $security;
		}
		$unformat_deposit = $deposit;
		$unformat_total = $total;
		
		$result = compact('nights', 'amount', 'extra_price', 'amount_after_discount', 'deposit', 'sub_total', 'tax', 'tourist_tax', 'total', 'security', 'net', 'extras', 'promo_valid', 'promo_code', 'promo_amount', 'promo_type', 'promo_percentage', 'discount_valid', 'discount_amount', 'discount_type', 'discount_percentage', 'discount_name', 'unformat_deposit', 'unformat_total');
		
		return $result;
	}
	static public function getFromEmail($option_arr)
	{
		$email = $option_arr['o_email_address'];
		if($email == '')
		{
			$arr = pjAuthUserModel::factory()
				->findAll()
				->orderBy("t1.id ASC")
				->limit(1)
				->getData();
			$email = !empty($arr) ? $arr[0]['email'] : null;
		}
		return $email;
	}
	static public function getOwnerEmail($calendar_id)
	{
		$arr = pjCalendarModel::factory()->select("t1.*, t2.email")->join("pjAuthUser", "t1.user_id=t2.id", 'left')->find($calendar_id)->getData();
		if(!empty($arr))
		{
			if(!empty($arr['email']))
			{
				return $arr['email'];
			}else{
				return null;
			}
		}else{
			return null;
		}
	}
	
	public static function syncFeeds($feed_id)
    {
        $feed = pjFeedModel::factory()->find($feed_id)->getData();       
        if ($feed)
        {
        	$option_arr = pjOptionModel::factory()->getPairs($feed['calendar_id']);
            $pjReservationModel = pjReservationModel::factory();
            if (!empty($feed['url']))
            {
                if(!class_exists ('iCalEasyReader'))
                {
                    include ( PJ_COMPONENTS_PATH . 'iCalEasyReader.php' );
                }
                $ical = new iCalEasyReader();
                $fead_contents = file_get_contents($feed['url']);
                $lines = $ical->load( $fead_contents );
                
                if (!empty($lines) && isset($lines['VEVENT']) && !empty($lines['VEVENT'])) 
                {
                    $pjReservationModel
	                    ->reset()
	                    ->where('calendar_id', $feed['calendar_id'])
	                    ->where('provider_id', $feed['provider_id'])
	                    ->eraseAll();
                    $pjReservationModel->reset()->begin();
                    
                    foreach($lines['VEVENT'] as $item)
                    {
                        $booking = array();
                        $booking['calendar_id'] = $feed['calendar_id'];
                        $booking['provider_id'] = $feed['provider_id'];
                        $booking['price_based_on'] = $option_arr['o_price_based_on'];
                        $booking['status'] = 'Confirmed';
                        
                        if(isset($item['UID']) && !empty($item['UID']))
                        {
                            $booking['uuid'] = $item['UID'];
                        }else{
                            $booking['uuid'] = pjUtil::uuid();
                        }
                        if(isset($item['DTSTART']))
                        {
                            if(is_array($item['DTSTART']))
                            {
                                if(isset($item['DTSTART']['value']) && !empty($item['DTSTART']['value']))
                                {
                                    $booking['date_from'] = substr($item['DTSTART']['value'],0,4) . '-' . substr($item['DTSTART']['value'],4,2) . '-' . substr($item['DTSTART']['value'],6,2);
                                }
                            }else if(!empty($item['DTSTART'])){
                                $booking['date_from'] = substr($item['DTSTART'],0,4) . '-' . substr($item['DTSTART'],4,2) . '-' . substr($item['DTSTART'],6,2);
                            }
                        }
                        if(isset($item['DTEND']))
                        {
                            if(is_array($item['DTEND']))
                            {
                                if(isset($item['DTEND']['value']) && !empty($item['DTEND']['value']))
                                {
                                    $booking['date_to'] = substr($item['DTEND']['value'],0,4) . '-' . substr($item['DTEND']['value'],4,2) . '-' . substr($item['DTEND']['value'],6,2);
                                }
                            }else if(!empty($item['DTEND'])){
                                $booking['date_to'] = substr($item['DTEND'],0,4) . '-' . substr($item['DTEND'],4,2) . '-' . substr($item['DTEND'],6,2);
                            }
                        }
                        if(isset($item['SUMMARY']) && !empty($item['SUMMARY']))
                        {
                            if (in_array($feed['provider_id'], array(2)))
                            {
                                $booking['status'] = (preg_match('/Cancelled|cancelled/i', $item['SUMMARY'])) ? 'Cancelled' : 'Pending';
                            }
                            if (in_array($feed['provider_id'], array(3,4)))
                            {
                                $booking['status'] = (preg_match('/Reserved|Blocked/i', $item['SUMMARY'])) ? 'Confirmed' : 'Pending';
                            }
                            if (strpos($item['SUMMARY'], '-') == true) {
                                $name = explode('-', $item['SUMMARY']);
                                $booking['c_name'] = (!empty($name) && $name[1]) ? trim($name[1]) : trim($item['SUMMARY']);
                            } else {
                                $booking['c_name'] = $item['SUMMARY'];
                            }
                        }
                        if (!empty($booking['date_to']) && $booking['date_to'] >= date('Y-m-d') && $booking['status'] != 'Cancelled')
                        {
                            $pjReservationModel->reset()->setAttributes($booking)->insert();
                        }
                    }
                    $pjReservationModel->commit();
                }
            }
        }
        
        return array('status' => 'OK', 'code' => 200);
    }
}
?>