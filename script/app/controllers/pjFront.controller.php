<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{
	public $defaultCaptcha = 'PHPJabbersCaptcha';
	
	public $defaultLocale = 'front_locale_id';
	
	public $defaultCalendar = 'ABCalendar';
	
	public function __construct()
	{
		$this->setLayout('pjActionFront');
		
		self::allowCORS();
	}
	
	public function afterFilter()
	{
		if ($this->_get->check('locale') && $this->_get->toInt('locale') > 0)
	    {
	        $this->pjActionSetLocale($this->_get->toInt('locale'));
	    }
	    
	    if ($this->pjActionGetLocale() === FALSE)
	    {
	        $locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
	        if (count($locale_arr) === 1)
	        {
	            $this->pjActionSetLocale($locale_arr[0]['id']);
	        }
	    }
	    if ($this->_get->check('action') && !in_array($this->_get->toArray('action'), array('pjActionLoadCss')))
	    {
	        $this->loadSetFields(true);
	    }
	    
		$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')->findAll()->getData();
		
		$this->set('locale_arr', $locale_arr);
	}
	
	public function beforeFilter()
	{
		parent::beforeFilter();
		 
		if($this->_get->toInt('cid') > 0)
		{
		    $this->setCalendarId($this->_get->toInt('cid'));
		}else{
			$this->setCalendarId(1);
		}
		
		$base_option_arr = pjBaseOptionModel::factory()->getPairs(1);
		
		$script_option_arr = pjOptionModel::factory()->getPairs($this->getCalendarId());
		$this->option_arr = array_merge($base_option_arr, $script_option_arr);
		$this->set('option_arr', $this->option_arr);
		
		pjRegistry::getInstance()->set('options', $this->option_arr);
		return true;
	}
	
	public function beforeRender()
	{
		if ($this->_get->check('iframe'))
		{
			$this->setLayout('pjActionIframe');
		}
	}
	
	public function pjActionLoad()
	{
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
		if ($this->_get->check('locale') && $this->_get->toInt('locale') > 0)
		{
			$this->pjActionSetLocale($this->_get->toInt('locale'));
			$this->loadSetFields(true);
		}
		$limit_arr = pjLimitModel::factory()
		->select('t1.min_nights, t1.max_nights, t1.blocked, UNIX_TIMESTAMP(t1.date_from) AS ts_from, UNIX_TIMESTAMP(t1.date_to) AS ts_to')
		->where('t1.calendar_id', $this->_get->toInt('cid'))
		->findAll()
		->getData();
			
		foreach ($limit_arr as $k => $limit)
		{
			$limit_arr[$k] = array_map("intval", $limit);
		}
	
		$this->set('limit_arr', $limit_arr);
	}
	
	public function pjActionLoadCalendar()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$limit_arr = pjLimitModel::factory()
			->select('t1.min_nights, t1.max_nights, UNIX_TIMESTAMP(t1.date_from) AS ts_from, UNIX_TIMESTAMP(t1.date_to) AS ts_to')
			->where('t1.calendar_id', $this->_get->toInt('cid'))
			->findAll()
			->getData();
	
			foreach ($limit_arr as $k => $limit)
			{
				$limit_arr[$k] = array_map("intval", $limit);
			}
				
			$this->set('limit_arr', $limit_arr);
		}
	}
	
	public function pjActionLoadAvail()
	{
		$this->setAjax(true);
	}
	
	public function pjActionLoadAvailability()
	{
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
		if ($this->_get->check('locale') && $this->_get->toInt('locale') > 0)
		{
			$this->pjActionSetLocale($this->_get->toInt('locale'));
			$this->loadSetFields(true);
		}
	}
	
	public function pjActionCancel()
	{
		$this->setLayout('pjActionCancel');
		$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
		if($is_ip_blocked == true)
		{
			$this->set('status', 'IP_BLOCKED');
		} else {
			if ($this->_get->check('id') && $this->_get->toInt('id') > 0 && $this->_get->check('cid') && $this->_get->toInt('cid') > 0 &&
				$this->_get->check('hash') && $this->_get->toString('hash') != '' && $this->_get->toString('hash') == sha1($this->_get->toInt('id') . PJ_SALT))
			{
				$arr = pjReservationModel::factory()
					->select("t1.*, t2.content AS country, t3.user_id")
					->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
					->join('pjCalendar', 't3.id=t1.calendar_id', 'left outer')
					->where('t1.id', $this->_get->toInt('id'))
					->where('t1.calendar_id', $this->_get->toInt('cid'))
					->limit(1)
					->findAll()
					->getData();
					
				if (!empty($arr))
				{
					$arr = $arr[0];
				}
				if ($this->_post->check('cancel_booking') && $this->_post->check('id') && $this->_post->toInt('id') > 0)
				{
					$err = NULL;
					if (pjReservationModel::factory()->set('id', $this->_post->toInt('id'))->modify(array('status' => 'Cancelled'))->getAffectedRows() == 1)
					{
						$err = '&err=AR13';
						pjFront::pjActionConfirmSend($this->_post->toInt('id'), $this->option_arr, $arr['locale_id'], 'cancel');
						if (!empty($this->option_arr['o_cancel_url']) && preg_match('/http(s)?:\/\//', $this->option_arr['o_cancel_url']))
						{
							pjUtil::redirect($this->option_arr['o_cancel_url']);
						}
					}
					pjUtil::redirect(sprintf("%sindex.php?controller=pjFront&action=pjActionCancel&cid=%u&id=%u&hash=%s%s", PJ_INSTALL_URL, $this->_get->toInt('cid'), $this->_get->toInt('id'), $this->_get->toString('hash'), $err));
				}
					
				if (empty($arr))
				{
					$this->set('status', 'AR16');
				} else {
					$this->set('arr', $arr);
				}
			} else {
				$this->set('status', 'AR15');
			}
		}
		$this
				->appendCss('cancel.css')
				->appendCss('pj-button.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
	}
	
	public function pjActionCaptcha()
	{
	    $this->setAjax(true);

		header("Cache-Control: max-age=3600, private");
		$rand = $this->_get->toInt('rand') ?: rand(1, 9999);
		$patterns = 'app/web/img/button.png';
		if(!empty($this->option_arr['o_captcha_background_front']) && $this->option_arr['o_captcha_background_front'] != 'plain')
		{
			$patterns = PJ_INSTALL_PATH . $this->getConstant('pjBase', 'PLUGIN_IMG_PATH') . 'captcha_patterns/' . $this->option_arr['o_captcha_background_front'];
		}
		$Captcha = new pjCaptcha(PJ_INSTALL_PATH . $this->getConstant('pjBase', 'PLUGIN_WEB_PATH') . 'obj/arialbd.ttf', $this->defaultCaptcha, (int) $this->option_arr['o_captcha_length_front']);
		$Captcha->setImage($patterns)->setMode($this->option_arr['o_captcha_mode_front'])->init($rand);
		exit;
	}
	
	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
		if (!$this->_get->check('captcha') || !$this->_get->toString('captcha') || strtoupper($this->_get->toString('captcha')) != $_SESSION[$this->defaultCaptcha]){
			echo 'false';
		}else{
			echo 'true';
		}
		exit;
	}
	
	public function pjActionCheckReCaptcha()
	{
		$this->setAjax(true);
		$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$this->option_arr['o_captcha_secret_key_front'].'&response='.$this->_get->toString('recaptcha'));
		$responseData = json_decode($verifyResponse);
		echo $responseData->success ? 'true': 'false';
		exit;
	}
	
	public function pjActionCheckDates()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$date_from = date("Y-m-d", $this->_get->toString('start_dt'));
			$date_to = date("Y-m-d",$this->_get->toString('end_dt'));
			if ($date_from > $date_to)
			{
				$tmp = $date_from;
				$date_from = $date_to;
				$date_to = $tmp;
			}
			$resp = $this->pjActionCheckDt($date_from, $date_to, $this->_get->toInt('cid'), NULL, TRUE);
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
	
	private static function pjActionConfirmSend($reservation_id, $option_arr, $locale_id, $variant)
	{
		$Email = self::getMailer($option_arr);
        
        $pjNotificationModel = pjNotificationModel::factory();
        
        $reservation = pjReservationModel::factory()
						->select("t1.*, t2.content AS country")
						->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
						->find($reservation_id)->getData();        
        $tokens = pjAppController::getTokens($reservation, $option_arr, $locale_id);
        
		/*****Client Email*******/
        if (!empty($reservation['c_email'])) {
	        $notification = $pjNotificationModel->reset()->where('foreign_id', $reservation['calendar_id'])->where('is_general', 0)->where('recipient', 'client')->where('transport', 'email')->where('variant', $variant)->findAll()->getDataIndex(0);
	        if((int) $notification['id'] > 0 && $notification['is_active'] == 1)
	        {
	            $resp = pjAppController::getSubjectMessage($notification, $locale_id);
	            
	            $lang_message = $resp['lang_message'];
	            $lang_subject = $resp['lang_subject'];
	            
	            if(!empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
	            {
	                $subject = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
	                $subject = stripslashes($subject);
	                $message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	                $message = pjUtil::textToHtml($message);	                
	                $Email
	                ->setTo($reservation['c_email'])
	                ->setSubject($subject)
	                ->send($message);
	            }
	        }
        }
        
		/*****Owner Email*******/
        $owner_email = pjAppController::getOwnerEmail($reservation['calendar_id']);
        if ($owner_email) {
	        $notification = $pjNotificationModel->reset()->where('foreign_id', $reservation['calendar_id'])->where('is_general', 0)->where('recipient', 'owner')->where('transport', 'email')->where('variant', $variant)->findAll()->getDataIndex(0);
	        if((int) $notification['id'] > 0 && $notification['is_active'] == 1)
	        {
	            $resp = pjAppController::getSubjectMessage($notification, $locale_id);
	            
	            $lang_message = $resp['lang_message'];
	            $lang_subject = $resp['lang_subject'];
	            
	            if(!empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
	            {
	                $subject = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
	                $subject = stripslashes($subject);
	                $message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	                $message = pjUtil::textToHtml($message);	                
	                $Email
	                ->setTo($owner_email)
	                ->setSubject($subject)
	                ->send($message);
	            }
	        }
        }
        
        $admin_arr = pjAuthUserModel::factory()->find(1)->getData();
		/*****Admin Email*******/
        $notification = $pjNotificationModel->reset()->where('foreign_id', 0)->where('is_general', 1)->where('recipient', 'admin')->where('transport', 'email')->where('variant', $variant)->findAll()->getDataIndex(0);
        if((int) $notification['id'] > 0 && $notification['is_active'] == 1)
        {
            $resp = pjAppController::getSubjectMessage($notification, $locale_id);
            
            $lang_message = $resp['lang_message'];
            $lang_subject = $resp['lang_subject'];
            
            if(!empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
            {
                $subject = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
                $subject = stripslashes($subject);
                $message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
                $message = pjUtil::textToHtml($message);
                $Email
                        ->setTo($admin_arr['email'])
                        ->setSubject($subject)
                        ->send($message);
            }
        }
        
		/*****Admin SMS*******/
        if (!empty($admin_arr['phone'])) {
	        $notification = $pjNotificationModel->reset()->where('foreign_id', 0)->where('is_general', 1)->where('recipient', 'admin')->where('transport', 'sms')->where('variant', $variant)->findAll()->getDataIndex(0);
	        if((int) $notification['id'] > 0 && $notification['is_active'] == 1)
	        {
	            $resp = pjAppController::getSmsMessage($notification, $locale_id);
	            $lang_message = $resp['lang_message'];
	            
	            if(!empty($lang_message[0]['content']))
	            {
	                $message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	                $message = stripslashes($message);
	                $params = array(
	                    'text' => $message,
	                    'type' => 'unicode',
	                    'key' => md5($option_arr['private_key'] . PJ_SALT)
	                );
	            	$params['number'] = $admin_arr['phone'];
					pjBaseSms::init($params)->pjActionSend();
	            }
	        }
        }
	}
	
	public function pjActionGetBookingForm()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if($is_ip_blocked == true)
			{
				$this->set('status', 'IP_BLOCKED');
			} else {
				if (!isset($_SESSION[$this->defaultCalendar]))
				{
					$_SESSION[$this->defaultCalendar] = array();
				}
				if ($this->_get->check('start_dt') && $this->_get->check('end_dt'))
				{
					$start_dt = $this->_get->toString('start_dt');
					$end_dt = $this->_get->toString('end_dt');
					if ($this->_get->toString('start_dt') > $this->_get->toString('end_dt'))
					{
						$start_dt = $this->_get->toString('end_dt');
						$end_dt = $this->_get->toString('start_dt');
					}
					if($start_dt == $end_dt && $this->option_arr['o_booking_behavior'] == 2 && $this->option_arr['o_price_based_on'] == 'nights')
					{
						$end_dt = strtotime('+1 day', $end_dt);
					}
					$start_date = new DateTime(date('Y-m-d', $start_dt));
					$end_date = new DateTime(date('Y-m-d', $end_dt));
					$nights = $end_date->diff($start_date)->format("%a");
					if ($this->option_arr['o_price_based_on'] == 'days')
					{
						$nights += 1;
					}
					$_SESSION[$this->defaultCalendar] = array_merge($_SESSION[$this->defaultCalendar], compact('start_dt', 'end_dt', 'nights'));
				}
				$extra_arr = pjExtraModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
					->where('t1.status', 'T')
					->where("(t1.id IN (SELECT TCE.extra_id FROM `".pjCalendarExtraModel::factory()->getTable()."` AS `TCE` WHERE `TCE`.calendar_id='".$this->_get->toInt('cid')."') )")
					->orderBy('`name` ASC')
					->findAll()->getData();
					
				$default_extra_arr = array();
				$default_qty_arr = array();
				foreach($extra_arr as $k => $v)
				{
					if($v['required'] == 'T')
					{
						$default_qty_arr[$v['id']] = 1;
						$default_extra_arr[$v['id']] = 'on';
					}
				}
				
				$this->set('price_arr', $this->pjActionCalcPrices(
						$this->_get->toInt('cid'), 
						$_SESSION[$this->defaultCalendar]['start_dt'], 
						$_SESSION[$this->defaultCalendar]['end_dt'], 
						@$_SESSION[$this->defaultCalendar]['c_adults'], 
						@$_SESSION[$this->defaultCalendar]['c_children'], 
						(isset($_SESSION[$this->defaultCalendar]['extra']) ? @$_SESSION[$this->defaultCalendar]['extra'] : $default_extra_arr), 
						(isset($_SESSION[$this->defaultCalendar]['qty']) ? @$_SESSION[$this->defaultCalendar]['qty'] : $default_qty_arr),
						(isset($_SESSION[$this->defaultCalendar]['promo_code']) ? @$_SESSION[$this->defaultCalendar]['promo_code'] : ''),
						$this->option_arr,
						$this->getLocaleId()));
				
				if ((int) $this->option_arr['o_bf_terms'] !== 1)
				{
					$this->set('cal_arr', pjCalendarModel::factory()
						->select('t1.*, t2.content AS terms_url, t3.content AS terms_body')
						->join('pjMultiLang', sprintf("t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='terms_url' AND t2.locale='%u'", $this->pjActionGetLocale()), 'left outer')
						->join('pjMultiLang', sprintf("t3.model='pjCalendar' AND t3.foreign_id=t1.id AND t3.field='terms_body' AND t3.locale='%u'", $this->pjActionGetLocale()), 'left outer')
						->find($this->_get->toInt('cid'))
						->getData()
					);
				}
				
				if ((int) $this->option_arr['o_bf_country'] !== 1)
				{
					$this->set('country_arr', pjBaseCountryModel::factory()
						->select('t1.*, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
						->where('t1.status', 'T')
						->orderBy('`name` ASC')
						->findAll()->getData());
				}
				$this->set('extra_arr', $extra_arr);
				
				$bank_account = pjMultiLangModel::factory()
			    ->select('t1.content')
			    ->where('t1.model','pjOption')
			    ->where('t1.locale', $this->getLocaleId())
			    ->where('t1.foreign_id', $this->_get->toInt('cid'))
			    ->where('t1.field', 'o_bank_account')
			    ->limit(1)
			    ->findAll()
			    ->getDataIndex(0);
			    $this->set('bank_account', $bank_account ? $bank_account['content'] : '');
				
				if(pjObject::getPlugin('pjPayments') !== NULL)
				{
				    $this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions($this->_get->toInt('cid')));
				    $this->set('payment_titles', pjPayments::getPaymentTitles($this->_get->toInt('cid'), $this->getLocaleId()));
				}else{
				    $this->set('payment_titles', __('payment_methods', true));
				}
			}
		}
	}
	
	public function pjActionGetCalendar()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if($is_ip_blocked == true)
			{
				$this->set('status', 'IP_BLOCKED');
			} else {
				if (!$this->_get->check('month') && !$this->_get->check('year'))
				{
					list($m, $y) = explode("-", date("n-Y"));
				} else {
					$m = $this->_get->toInt('month');
					$y = $this->_get->toInt('year');
				}
				
				$ABCalendar = new pjABCalendar();
				$ABCalendar
					->setWeekTitle(__('lblWeekTitle', true, false))
					->setShowNextLink($this->_get->toInt('view') > 1 ? false : true)
					->setShowPrevLink($this->_get->toInt('view') > 1 ? false : true)
					->setPrevLink("")
					->setNextLink("")
					->set('calendarId', $this->_get->toInt('cid'))
					->set('reservationsInfo', pjReservationModel::factory()
						->getInfo(
							$this->_get->toInt('cid'),
							date("Y-m-d", mktime(0, 0, 0, $m, 1, $y)),
							date("Y-m-d", mktime(23, 59, 59, $m + $this->_get->toInt('view'), 0, $y)),
							$this->option_arr,
							NULL,
							1
						)
					)
					->set('options', $this->option_arr)
					->set('weekNumbers', (int) $this->option_arr['o_show_week_numbers'] === 1 ? true : false)
					->setStartDay($this->option_arr['o_week_start'])
					->setDayNames(__('day_names', true))
					->setWeekDays(__('days', true))
					->setNA(mb_strtoupper(__('lblNA', true), 'UTF-8'))
					->setMonthNames(__('months', true))
				;
				if ($this->option_arr['o_price_plugin'] == 'period')
				{
					$periods = pjPeriodModel::factory()->getPeriodsPerDay($this->_get->toInt('cid'), $m, $y, $this->_get->toInt('view'), $this->option_arr['o_price_based_on'] == 'days');
					
					$ABCalendar->set('periods', $periods);
				}
				if ((int) $this->option_arr['o_show_prices'] === 1)
				{
					if ($this->option_arr['o_price_plugin'] == 'price')
					{
						$price_arr = pjPriceModel::factory()->getPricePerDay(
							$this->_get->toInt('cid'),
							date("Y-m-d", mktime(0, 0, 0, $m, 1, $y)),
							date("Y-m-d", mktime(0, 0, 0, $m + $this->_get->toInt('view'), 1, $y)),
							$this->option_arr
						);
						$ABCalendar
							->set('prices', $price_arr['priceData'])
							->set('showPrices', true);
					}
				}
				
				$this->set('ABCalendar', $ABCalendar);
			}
		}
	}
	
	public function pjActionGetPeriods()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!$this->_get->check('month') && !$this->_get->check('year'))
			{
				list($m, $y) = explode("-", date("n-Y"));
			} else {
				$m = $this->_get->toInt('month');
				$y = $this->_get->toInt('year');
			}
			
			$date_from = date("Y-m-d", mktime(0, 0, 0, $m, 1, $y));
			$date_to = date("Y-m-d", mktime(0, 0, 0, $m + (int) $this->_get->toInt('view'), 0, $y));

			# http://en.wikipedia.org/wiki/De_Morgan's_laws
			# http://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap
			# (StartA <= EndB) and (EndA >= StartB)
			$periods = pjPeriodModel::factory()
				->where('t1.foreign_id', $this->_get->toInt('cid'))
				->where('t1.start_date <=', $date_to)
				->where('t1.end_date >=', $date_from)
				->findAll()
				->getData();
			foreach ($periods as $k => $period)
			{
				$periods[$k]['start_ts'] = strtotime($period['start_date']);
    			$periods[$k]['end_ts'] = strtotime($period['end_date']);
			}
			
			pjAppController::jsonResponse($periods);
		}
		exit;
	}
	
	public function pjActionGetPrice()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$this->set('price_arr', $this->pjActionCalcPrices(
					$this->_get->toInt('cid'), 
					$_SESSION[$this->defaultCalendar]['start_dt'], 
					$_SESSION[$this->defaultCalendar]['end_dt'], 
					$this->_post->toInt('c_adults'), 
					$this->_post->toInt('c_children'), 
					$this->_post->check('extra') ? $this->_post->toArray('extra') : array(), 
					$this->_post->check('qty') ? $this->_post->toArray('qty') : array(),
					$this->_post->check('promo_code') ? $this->_post->toString('promo_code') : '',
					$this->option_arr,
					$this->getLocaleId())
				);
		}
	}
	
	public function pjActionGetPaymentForm()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if($is_ip_blocked == true)
			{
				$this->set('status', 'IP_BLOCKED');
			} else {
				$booking_arr = pjReservationModel::factory()
					->join('pjMultiLang', "t2.foreign_id = t1.calendar_id AND t2.model = 'pjCalendar' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->select('t1.*, t2.content as calendar_name')
					->find($this->_get->toInt('reservation_id'))
					->getData();
					
				if(pjObject::getPlugin('pjPayments') !== NULL)
			    {
			        $pjPlugin = pjPayments::getPluginName($booking_arr['payment_method']);
			        if(pjObject::getPlugin($pjPlugin) !== NULL)
			        {
			            $this->set('params', $pjPlugin::getFormParams(array('payment_method' => $booking_arr['payment_method']), array(
			                'locale_id'	 => $this->getLocaleId(),
			                'return_url'	=> $this->option_arr['o_thankyou_page'],
			                'id'			=> $booking_arr['id'],
			                'foreign_id'	=> $booking_arr['calendar_id'],
			                'uuid'		  => $booking_arr['uuid'],
			                'name'		  => $booking_arr['c_name'],
			                'email'		 => $booking_arr['c_email'],
			                'phone'		 => $booking_arr['c_phone'],
			                'amount'		=> $booking_arr['deposit'],
			                'cancel_hash'   => sha1($booking_arr['uuid'].strtotime($booking_arr['created']).PJ_SALT),
			                'currency_code' => $this->option_arr['o_currency'],
			            )));
			        }
			        if ($booking_arr['payment_method'] == 'bank')
			        {
			            $bank_account = pjMultiLangModel::factory()
			            ->select('t1.content')
			            ->where('t1.model','pjOption')
			            ->where('t1.locale', $this->getLocaleId())
			            ->where('t1.foreign_id', $booking_arr['calendar_id'])
			            ->where('t1.field', 'o_bank_account')
			            ->limit(1)
			            ->findAll()
			            ->getDataIndex(0);
			            $this->set('bank_account', $bank_account ? $bank_account['content'] : '');
			        }
			    }
				
				$this->set('booking_arr', $booking_arr);
				$this->set('get', $this->_get->raw());
			}
		}
	}
	
	public function pjActionGetSummaryForm()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if($is_ip_blocked == true)
			{
				$this->set('status', 'IP_BLOCKED');
			} else {
				$_SESSION[$this->defaultCalendar] = array_merge($_SESSION[$this->defaultCalendar], $this->_post->raw());
				
				$this->set('price_arr', $this->pjActionCalcPrices($this->_get->toInt('cid'), 
						$_SESSION[$this->defaultCalendar]['start_dt'], 
						$_SESSION[$this->defaultCalendar]['end_dt'], 
						@$_SESSION[$this->defaultCalendar]['c_adults'], 
						((int) $this->option_arr['o_bf_children'] !== 1 ? @$_SESSION[$this->defaultCalendar]['c_children'] : 0), 
						isset($_SESSION[$this->defaultCalendar]['extra']) ? $_SESSION[$this->defaultCalendar]['extra'] : array(),
						isset($_SESSION[$this->defaultCalendar]['qty']) ? $_SESSION[$this->defaultCalendar]['qty'] : array(),
						isset($_SESSION[$this->defaultCalendar]['promo_code']) ? $_SESSION[$this->defaultCalendar]['promo_code'] : '',
						$this->option_arr,
						$this->getLocaleId()));
	
				if ((int) $this->option_arr['o_bf_country'] !== 1 && isset($_SESSION[$this->defaultCalendar]['c_country']))
				{
					$this->set('country_arr', pjBaseCountryModel::factory()
						->select('t1.*, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
						->where('t1.status', 'T')
						->find($_SESSION[$this->defaultCalendar]['c_country'])->getData());
				}
				
				$bank_account = pjMultiLangModel::factory()
			    ->select('t1.content')
			    ->where('t1.model','pjOption')
			    ->where('t1.locale', $this->getLocaleId())
			    ->where('t1.foreign_id', $this->_get->toInt('cid'))
			    ->where('t1.field', 'o_bank_account')
			    ->limit(1)
			    ->findAll()
			    ->getDataIndex(0);
			    $this->set('bank_account', $bank_account ? $bank_account['content'] : '');
				
				if(pjObject::getPlugin('pjPayments') !== NULL)
				{
				    $this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions($this->_get->toInt('cid')));
				    $this->set('payment_titles', pjPayments::getPaymentTitles($this->_get->toInt('cid'), $this->getLocaleId()));
				}else{
				    $this->set('payment_titles', __('payment_methods', true));
				}
			}
		}
	}
	
	public function pjActionImage()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
		$w = $this->_get->check('width') && $this->_get->toInt('width') > 0 ? $this->_get->toInt('width') : 100;
		$h = $this->_get->check('height') && $this->_get->toInt('height') > 0 ? $this->_get->toInt('height') : 100;
		
		# Spatial_anti-aliasing. Make an image larger then it's intended
		$width = $w * 10;
		$height = $h * 10;
		
		$image = imagecreatetruecolor($width, $height);
		if (function_exists('imageantialias'))
		{
			imageantialias($image, true);
		}
		$backgroundColor = pjUtil::html2rgb($this->_get->toString('color1'));
		$color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
		imagefill($image, 0, 0, $color);
		
		if ($this->_get->check('color2') && $this->_get->toString('color2') != '')
		{
			if ($this->_get->toString('color1') == $this->_get->toString('color2'))
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
				imagefilledpolygon($image, $values, 7, $color);
			} else {
				$backgroundColor = pjUtil::html2rgb($this->_get->toString('color2'));
				$color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
				$values = array(
						$width,  0,  // Point 1 (x, y)
						$width,  $height, // Point 2 (x, y)
						0, $height,
						$width,  0
				);
				imagefilledpolygon($image, $values, 4, $color);
			}
		}
		# Shrink it down to remove the aliasing and make it it's intended size
		$new_image = imagecreatetruecolor($w, $h);
		imagecopyresampled($new_image, $image, 0, 0, 0, 0, $w, $h, $width, $height);

		header('Content-Type: image/jpeg');
		imagejpeg($new_image, null, 100);
		imagedestroy($image);
		imagedestroy($new_image);
		exit;
	}
	
	public function pjActionGetAvailability()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
			if($is_ip_blocked == true)
			{
				$this->set('status', 'IP_BLOCKED');
			} else {
				$locale = $this->_get->check('locale') && $this->_get->toInt('locale') > 0 ? $this->_get->toInt('locale') : $this->pjActionGetLocale();
				
				$pjCalendarModel = pjCalendarModel::factory()
					->select("t1.*, t2.content AS `title`, t3.value AS `o_bookings_per_day`")
					->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='$locale'", 'left outer')
					->join('pjOption', "t3.foreign_id=t1.id AND t3.key='o_bookings_per_day'", 'left outer');
				$pjReservationModel = pjReservationModel::factory();
				$pjOptionModel = pjOptionModel::factory();
				$arr = $pjCalendarModel->orderBy('t1.id ASC')->findAll()->getData();
				
				$last_timezone = NULL;
				foreach ($arr as $k => $calendar)
				{
					list($Y, $n) = explode("-", date("Y-n"));
					$year = $this->_get->check('year') && $this->_get->toInt('year') > 0 ? $this->_get->toInt('year') : $Y;
					$month = $this->_get->check('month') && $this->_get->toInt('month') > 0 ? $this->_get->toInt('month') : $n;
					
					$arr[$k]['date_arr'] = $pjReservationModel->getInfo(
						$calendar['id'],
						date("Y-m-d", mktime(0, 0, 0, $month, 1, $year)),
						date("Y-m-d", mktime(0, 0, 0, $month + 1, 0, $year)),
						$pjOptionModel->reset()->getPairs($calendar['id']),
						NULL,
						1
					);
				}
				
				$this->set('arr', $arr);
			}
		}
	}
	
	public function pjActionLoadAvailabilityCss()
	{
		header("Content-Type: text/css; charset=utf-8");
		$index = rand(1,99999);
		$arr = array(
			array('file' => 'ABCalendar.Availability.css', 'path' => PJ_CSS_PATH),
			array('file' => 'ABCalendar.Availability.txt', 'path' => PJ_CSS_PATH)
		);
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();
			
			if ($string !== FALSE)
			{
				echo str_replace(
					array('../img/', '[background_nav]', "pjWrapper"),
					array(PJ_IMG_PATH, '#187c9a', "pjWrapperRPBCAvailability_" . $index),
					$string) . "\n";
			}
		}
		
		$pjOptionModel = pjOptionModel::factory();
		$arr = pjCalendarModel::factory()->findAll()->getData();
		
		ob_start();
		@readfile(PJ_CSS_PATH . 'availability.txt');
		$string = ob_get_contents();
		ob_end_clean();
		
		foreach ($arr as $calendar)
		{
			$option_arr = $pjOptionModel->reset()->getPairs($calendar['id']);
			if ($string !== FALSE && isset($option_arr['o_background_available']))
			{
				echo str_replace(
					array(
						'[calendarContainer]',
						'[URL]',
						'[cell_width]',
						'[cell_height]',
						'[background_available]',
						'[c_background_available]',
						'[background_booked]',
						'[c_background_booked]',
						'[background_empty]',
						'[background_month]',
						'[background_past]',
						'[background_pending]',
						'[c_background_pending]',
						'[background_select]',
						'[background_weekday]',
						'[border_inner]',
						'[border_inner_size]',
						'[border_outer]',
						'[border_outer_size]',
						'[color_available]',
						'[color_booked]',
						'[color_legend]',
						'[color_month]',
						'[color_past]',
						'[color_pending]',
						'[color_weekday]',
						'[font_family]',
						'[font_family_legend]',
						'[font_size_available]',
						'[font_size_booked]',
						'[font_size_legend]',
						'[font_size_month]',
						'[font_size_past]',
						'[font_size_pending]',
						'[font_size_weekday]',
						'[font_style_available]',
						'[font_style_booked]',
						'[font_style_legend]',
						'[font_style_month]',
						'[font_style_past]',
						'[font_style_pending]',
						'[font_style_weekday]'
					),
					array(
						'.abCal-id-' . $calendar['id'],
						PJ_INSTALL_URL,
						43,
						31,
						$option_arr['o_background_available'],
						str_replace('#', '', $option_arr['o_background_available']),
						$option_arr['o_background_booked'],
						str_replace('#', '', $option_arr['o_background_booked']),
						$option_arr['o_background_empty'],
						$option_arr['o_background_month'],
						$option_arr['o_background_past'],
						$option_arr['o_background_pending'],
						str_replace('#', '', $option_arr['o_background_pending']),
						$option_arr['o_background_select'],
						$option_arr['o_background_weekday'],
						$option_arr['o_border_inner'],
						$option_arr['o_border_inner_size'],
						$option_arr['o_border_outer'],
						$option_arr['o_border_outer_size'],
						$option_arr['o_color_available'],
						$option_arr['o_color_booked'],
						$option_arr['o_color_legend'],
						$option_arr['o_color_month'],
						$option_arr['o_color_past'],
						$option_arr['o_color_pending'],
						$option_arr['o_color_weekday'],
						$option_arr['o_font_family'],
						$option_arr['o_font_family_legend'],
						$option_arr['o_font_size_available'],
						$option_arr['o_font_size_booked'],
						$option_arr['o_font_size_legend'],
						$option_arr['o_font_size_month'],
						$option_arr['o_font_size_past'],
						$option_arr['o_font_size_pending'],
						$option_arr['o_font_size_weekday'],
						$option_arr['o_font_style_available'],
						$option_arr['o_font_style_booked'],
						$option_arr['o_font_style_legend'],
						$option_arr['o_font_style_month'],
						$option_arr['o_font_style_past'],
						$option_arr['o_font_style_pending'],
						$option_arr['o_font_style_weekday']
					),
					$string
				);
			}
		}
		
		exit;
	}
	
	public function pjActionLoadCss()
	{
		$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		
		$arr = array(
			array('file' => 'ABCalendar.css', 'path' => PJ_CSS_PATH),
			array('file' => 'ABFonts.min.css', 'path' => PJ_CSS_PATH)
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();
			
			if ($string !== FALSE)
			{
				echo str_replace(
					array('../img/', '../fonts/', 'images/', "pjWrapper"),
					array(PJ_IMG_PATH, PJ_FONT_PATH, "pjWrapperRPBC_" . $this->_get->toInt('cid')),
					$string) . "\n";
			}
		}
		
		ob_start();
		@readfile(PJ_CSS_PATH . 'ABCalendar.txt');
		$string = ob_get_contents();
		ob_end_clean();

		if ($string !== FALSE && isset($this->option_arr['o_show_week_numbers']))
		{
			echo str_replace(
				array(
					'[calendarContainer]',
					'[URL]',
					'[cell_width]',
					'[cell_height]',
					'[background_available]',
					'[c_background_available]',
					'[background_booked]',
					'[c_background_booked]',
					'[background_empty]',
					'[background_month]',
					'[background_nav]',
					'[background_nav_hover]',
					'[background_past]',
					'[background_pending]',
					'[c_background_pending]',
					'[background_select]',
					'[background_weekday]',
					'[border_inner]',
					'[border_inner_size]',
					'[border_outer]',
					'[border_outer_size]',
					'[color_available]',
					'[color_booked]',
					'[color_legend]',
					'[color_month]',
					'[color_past]',
					'[color_pending]',
					'[color_weekday]',
					'[font_family]',
					'[font_family_legend]',
					'[font_size_available]',
					'[font_size_booked]',
					'[font_size_legend]',
					'[font_size_month]',
					'[font_size_past]',
					'[font_size_pending]',
					'[font_size_weekday]',
					'[font_style_available]',
					'[font_style_booked]',
					'[font_style_legend]',
					'[font_style_month]',
					'[font_style_past]',
					'[font_style_pending]',
					'[font_style_weekday]'
				),
				array(
					'#pjWrapperRPBC_' . $this->_get->toInt('cid'),
					PJ_INSTALL_URL,
					number_format((100 / ((int) $this->option_arr['o_show_week_numbers'] === 1 ? 8 : 7)), 2, '.', ''),
					number_format(100 / 8, 2, '.', ''),
					$this->option_arr['o_background_available'],
					str_replace('#', '', $this->option_arr['o_background_available']),
					$this->option_arr['o_background_booked'],
					str_replace('#', '', $this->option_arr['o_background_booked']),
					$this->option_arr['o_background_empty'],
					$this->option_arr['o_background_month'],
					$this->option_arr['o_background_nav'],
					$this->option_arr['o_background_nav_hover'],
					$this->option_arr['o_background_past'],
					$this->option_arr['o_background_pending'],
					str_replace('#', '', $this->option_arr['o_background_pending']),
					$this->option_arr['o_background_select'],
					$this->option_arr['o_background_weekday'],
					$this->option_arr['o_border_inner'],
					$this->option_arr['o_border_inner_size'],
					$this->option_arr['o_border_outer'],
					$this->option_arr['o_border_outer_size'],
					$this->option_arr['o_color_available'],
					$this->option_arr['o_color_booked'],
					$this->option_arr['o_color_legend'],
					$this->option_arr['o_color_month'],
					$this->option_arr['o_color_past'],
					$this->option_arr['o_color_pending'],
					$this->option_arr['o_color_weekday'],
					$this->option_arr['o_font_family'],
					$this->option_arr['o_font_family_legend'],
					$this->option_arr['o_font_size_available'],
					$this->option_arr['o_font_size_booked'],
					$this->option_arr['o_font_size_legend'],
					$this->option_arr['o_font_size_month'],
					$this->option_arr['o_font_size_past'],
					$this->option_arr['o_font_size_pending'],
					$this->option_arr['o_font_size_weekday'],
					$this->option_arr['o_font_style_available'],
					$this->option_arr['o_font_style_booked'],
					$this->option_arr['o_font_style_legend'],
					$this->option_arr['o_font_style_month'],
					$this->option_arr['o_font_style_past'],
					$this->option_arr['o_font_style_pending'],
					$this->option_arr['o_font_style_weekday']
				),
				$string
			);
		}
		exit;
	}
	
	public function pjActionBookingSave()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$data = array();
			if (isset($_SESSION[$this->defaultCalendar]) && is_array($_SESSION[$this->defaultCalendar]) && count($_SESSION[$this->defaultCalendar]) > 0)
			{
				$data['ip'] = $_SERVER['REMOTE_ADDR'];
				$data['calendar_id'] = $this->_get->toInt('cid');
				$data['uuid'] = pjUtil::uuid();
				$data['status'] = ucfirst($this->option_arr['o_status_if_not_paid']);
				$data['locale_id'] = $this->pjActionGetLocale();
				
				$data['date_from'] = date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']);
				$data['date_to'] = date("Y-m-d", $_SESSION[$this->defaultCalendar]['end_dt']);
				$data['price_based_on'] = $this->option_arr['o_price_based_on'];
				
				$resp = $this->pjActionCheckDt($data['date_from'], $data['date_to'], $data['calendar_id'], NULL, TRUE);
				if ($resp['status'] == 'ERR')
				{
					pjAppController::jsonResponse($resp);
				}
				
				$data = array_merge($_SESSION[$this->defaultCalendar], $data);
	
				$price = $this->pjActionCalcPrices(
						$data['calendar_id'], 
						$_SESSION[$this->defaultCalendar]['start_dt'], 
						$_SESSION[$this->defaultCalendar]['end_dt'], 
						$data['c_adults'], $data['c_children'], 
						isset($data['extra']) ? $data['extra'] : array(), 
						isset($data['qty']) ? $data['qty'] : array(), 
						isset($_SESSION[$this->defaultCalendar]['promo_code']) ? $_SESSION[$this->defaultCalendar]['promo_code'] : '',
						$this->option_arr,
						$this->getLocaleId());
				
				$data['amount'] = @$price['amount'];
				$data['extra_price'] = @$price['extra_price'];
				$data['deposit'] = @$price['deposit'];
				$data['tax'] = @$price['tax'];
				$data['tourist_tax'] = @$price['tourist_tax'];
				$data['security'] = @$price['security'];
				if($price['promo_valid'] == 1)
				{
					$data['promo_amount'] = @$price['promo_amount'];
					$data['promo_code'] = @$price['promo_code'];
					$data['promo_type'] = @$price['promo_type'];
				}else{
					$data['promo_amount'] = ':NULL';
					$data['promo_code'] = ':NULL';
					$data['promo_type'] = ':NULL';
				}
				if($price['discount_valid'] == 1)
				{
					$data['discount_amount'] = @$price['discount_amount'];
				}else{
					$data['discount_amount'] = ':NULL';
				}
	
				if (isset($data['payment_method']) && $data['payment_method'] != 'creditcard')
				{
					unset($data['cc_type']);
					unset($data['cc_num']);
					unset($data['cc_exp_month']);
					unset($data['cc_exp_year']);
					unset($data['cc_code']);
				}
	
				$pjReservationModel = new pjReservationModel();
				if (!$pjReservationModel->validates($data))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Reservations data does not validate.'));
				}
				
				$reservation_id = $pjReservationModel->setAttributes($data)->insert()->getInsertId();
				if ($reservation_id === false || (int) $reservation_id === 0)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Reservation was not saved.'));
				}
				
				if(isset($data['extra']) && !empty($data['extra']))
				{
					$pjReservationExtraModel = pjReservationExtraModel::factory();
					$pjReservationExtraModel->begin();
					foreach($data['extra'] as $extra_id => $on)
					{
						$pjReservationExtraModel
							->reset()
							->set('reservation_id', $reservation_id)
							->set('extra_id', $extra_id)
							->set('qty', $data['qty'][$extra_id])
							->insert();
					}
					$pjReservationExtraModel->commit();
				}
	
				$_SESSION[$this->defaultCalendar] = NULL;
				unset($_SESSION[$this->defaultCalendar]);
				
				$_SESSION[$this->defaultCaptcha] = NULL;
				unset($_SESSION[$this->defaultCaptcha]);
				
				pjFront::pjActionConfirmSend($reservation_id, $this->option_arr, $this->getLocaleId(), 'confirmation');
			
				pjAppController::jsonResponse(array(
					'status' => 'OK', 'code' => 200, 'text' => 'Reservation was saved.',
					'reservation_id' => $reservation_id,
					'payment_method' => @$data['payment_method']
				));
			} else {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing or empty params.'));
			}
		}
		exit;
	}

	public function pjActionLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if ($this->_get->check('locale_id') && $this->_get->toInt('locale_id') > 0)
			{
				$this->pjActionSetLocale($this->_get->toInt('locale_id'));
				$this->loadSetFields(true);
			}
		}
		exit;
	}
	
	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}
	
	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}
	
	public function isXHR()
	{
		// CORS
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}
	
	static protected function allowCORS()
	{
		$install_url = parse_url(PJ_INSTALL_URL);
	    if($install_url['scheme'] == 'https'){
	        header('Set-Cookie: '.session_name().'='.session_id().'; SameSite=None; Secure');
	    }
	    
	    if (!isset($_SERVER['HTTP_ORIGIN']))
	    {
	        return;
	    }
	    
	    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
	    header("Access-Control-Allow-Credentials: true");
	    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
	    header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	    header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
	    
	    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
	    {
	        exit;
	    }
	}
	
	public function pjActionExportFeed()
    {
    	$is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
		if($is_ip_blocked == true)
		{
			 __('front_ip_address_blocked');
			 exit;
		} else {
	        $this->setLayout('pjActionEmpty');
	        $access = true;
	        if($this->_get->check('p') && !$this->_get->isEmpty('p'))
	        {
	            $pjPasswordModel = pjPasswordModel::factory();
	            $arr = $pjPasswordModel
	            	->where('t1.password', $this->_get->toString('p'))
					->where("t1.calendar_id", $this->_get->toInt('calendar_id'))
					->where("t1.format", $this->_get->toString('format'))
					->where("t1.type", $this->_get->toString('type'))
					->where("t1.period", $this->_get->toString('period'))
		            ->limit(1)
		            ->findAll()
		            ->getData();
	            if (count($arr) != 1)
	            {
	                $access = false;
	            }
	        }else{
	            $access = false;
	        }
	        if($access == true)
	        {
	            $arr = $this->pjGetFeedData($this->_get->raw(), $arr[0]['user_id']);
	            if(!empty($arr))
	            {
	                if($this->_get->toString('format') == 'xml')
	                {
	                    $xml = new pjXML();
	                    echo $xml
	                    ->setEncoding('UTF-8')
	                    ->process($arr)
	                    ->getData();
	                    
	                }
	                if($this->_get->toString('format') == 'csv')
	                {
	                    $csv = new pjCSV();
	                    echo $csv->setHeader(true)->process($arr)->getData();
	                }
	                if($this->_get->toString('format') == 'ical')
	                {
	                    foreach($arr as $k => $v)
	                    {
	                        $v['uuid'] = $v['uuid'] . '-' . $k;
	                        $_arr = array();
	                        if(!empty($v['c_name']))
	                        {
	                            $_arr[] = pjSanitize::html($v['c_name']);
	                        }
	                        if(!empty($v['property']))
	                        {
	                            $_arr[] = 'Property: ' . pjSanitize::html($v['property']);
	                        }
	                        if(!empty($v['c_email']))
	                        {
	                            $_arr[] = 'Email: ' . pjSanitize::html($v['c_email']);
	                        }
	                        if(!empty($v['c_phone']))
	                        {
	                            $_arr[] = 'Phone: ' . pjSanitize::html($v['c_phone']);
	                        }
	                        if(!empty($v['amount']))
	                        {
	                            $_arr[] = 'Amount: ' . pjSanitize::html($v['amount']);
	                        }
	                        if(!empty($v['c_notes']))
	                        {
	                            $_arr[] = 'Notes: ' . pjSanitize::html(preg_replace('/\n|\r|\r\n/', ' ', $v['c_notes']));
	                        }
	                        $_arr[] = 'Status: ' . pjSanitize::html($v['status']);
	                        
	                        $v['desc'] = join("\; ", $_arr);
	                        $v['location'] = pjSanitize::html($v['property']);
	                        $v['summary'] = 'Reservation';
	                        $arr[$k] = $v;
	                    }
	                    
	                    $ical = new pjICal();
	                    echo $ical
	                    ->setProdID('Rental Property Booking Calendar')
	                    ->setSummary('summary')
	                    ->setCName('desc')
	                    ->setLocation('location')
	                    ->setVersion("VERSION:2.0")
	                    ->setTimezone("UTC/GMT")
	                    ->process($arr)
	                    ->getData();
	                    
	                }
	            }
	        }else{
	            __('lblNoAccessToFeed');
	        }
		}
        exit;
    }
    
	public function pjGetFeedData($get, $user_id)
	{
		$arr = array();
		$status = true;
		$type = '';
		$period = '';
		if(isset($get['period']))
		{
			if(!ctype_digit($get['period']))
			{
				$status = false;
			}else{
				$period = $get['period'];
			}
		}else{
			$status = false;
		}
		if(isset($get['type']))
		{
			if(!in_array($get['type'], array('next', 'last','all')))
			{
				$status = false;
			}else{
				$type = $get['type'];
			}
		}else{
			$status = false;
		}
		if($status == true && $type != '' && $period != '')
		{
			$pjReservationModel = pjReservationModel::factory();
			$pjCalendarModel = pjCalendarModel::factory();
			
			$user =pjAuthUserModel::factory()->find($user_id)->getData();
			if($user['role_id'] != 1)
			{
				$pjCalendarModel->where('t1.user_id', $user_id);
			}			
			if(isset($get['calendar_id']) && !empty($get['calendar_id']))
			{
				$pjCalendarModel->where('t1.id', $get['calendar_id']);
			}
			$calendar_arr = $pjCalendarModel
				->findAll()
				->getData();		
			foreach($calendar_arr as $k => $v)
			{
				$option_arr = pjOptionModel::factory()->reset()->getPairs($v['id']);
				$week_start = $this->option_arr['o_week_start'];
				$column = 'created';
				$direction = 'DESC';
				$pjReservationModel->reset();
				$pjReservationModel->where('t1.calendar_id', $v['id']);
				if($type == 'next')
				{
					$column = 'date_from';
					$direction = 'ASC';
					
					$where_str = pjUtil::getComingWhere($period, $week_start);
					if($where_str != '' )
					{
						$pjReservationModel->where($where_str);
					}
				}else if($type == 'last'){
					$where_str = pjUtil::getMadeWhere($period, $week_start);
					if($where_str != '')
					{
						$pjReservationModel->where($where_str);
					}
				}
				$_arr = $pjReservationModel
					->select('t1.id, t2.content AS property, t1.uuid, t1.date_from, t1.date_to, t1.status,
							  t1.amount, t1.extra_price, t1.deposit, t1.tax, t1.tourist_tax, t1.security, t1.promo_amount, discount_amount, 
							  t1.c_name, t1.c_email, t1.c_phone, t1.c_phone, t1.c_adults, t1. c_children,
							  t1.c_notes, t1.c_address, t1.c_city, t1.c_country, t1.c_state, t1.c_zip, t1.ip, t1.payment_method, t1.created, t1.modified')
					->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.calendar_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy("$column $direction")
					->findAll()
					->getData();
				foreach($_arr as $v)
				{
					if($option_arr['o_price_based_on'] == 'nights')
					{
						$v['date_from'] = $v['date_from'] . ' ' . "12:00:00";
						$v['date_to'] = $v['date_to'] . ' ' . "12:00:00";
					}else{
						$v['date_from'] = $v['date_from'] . ' ' . "00:00:00";
						$v['date_to'] = $v['date_to'] . ' ' . "23:59:59";
					}
					$arr[] = $v;
				}
			}
		}
		return $arr;
	}
	
	public function pjActionConfirm()
	{
	    $this->setAjax(true);
	    
	    if (pjObject::getPlugin('pjPayments') === NULL)
	    {
	        $this->log('pjPayments plugin not installed');
	        exit;
	    }
	    
	    $pjPayments = new pjPayments();
	    if($pjPlugin = $pjPayments->getPaymentPlugin($_REQUEST))
	    {
	        if($uuid = $this->requestAction(array('controller' => $pjPlugin, 'action' => 'pjActionGetCustom', 'params' => $_REQUEST), array('return')))
	        {
	            $pjReservationModel = pjReservationModel::factory();
	            
	            $booking_arr = $pjReservationModel
	            ->reset()
	            ->select('t1.*, t2.content AS country')
                ->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.c_country AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
                ->where('t1.uuid', $uuid)
                ->limit(1)
                ->findAll()
                ->getDataIndex(0);
    				
				if (!empty($booking_arr))
				{
				    $booking_id = $booking_arr['id'];
				    $option_arr = pjOptionModel::factory()->getPairs($booking_arr['calendar_id']);
				    $option_arr = array_merge($this->option_arr, $option_arr);
				    $params = array(
				        'request'		=> $_REQUEST,
				        'payment_method' => $_REQUEST['payment_method'],
				        'foreign_id'	 => $booking_arr['calendar_id'],
				        'amount'		 => $booking_arr['deposit'],
				        'txn_id'		 => $booking_arr['txn_id'],
				        'order_id'	   => $booking_arr['id'],
				        'cancel_hash'	=> sha1($booking_arr['uuid'].strtotime($booking_arr['created']).PJ_SALT),
				        'key'			=> md5($option_arr['private_key'] . PJ_SALT)
				    );
				    $response = $this->requestAction(array('controller' => $pjPlugin, 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
				    
				    if($response['status'] == 'OK')
				    {
				        $this->log("Payments | {$pjPlugin} plugin<br>Reservation was confirmed. UUID: {$uuid}");
				        if($booking_arr['status'] != $option_arr['o_status_if_paid'])
				        {
    				        $pjReservationModel
    				        ->reset()
    				        ->set('id', $booking_arr['id'])
    				        ->modify(array('txn_id' => @$response['txn_id'], 'status' => $option_arr['o_status_if_paid']));
    				        
    				        pjFront::pjActionConfirmSend($booking_arr['id'], $option_arr, $this->getLocaleId(), 'payment');
				        }
            			echo $option_arr['o_thankyou_page'];
            			exit;
				    }elseif($response['status'] == 'CANCEL'){
				        $this->log("Payments | {$pjPlugin} plugin<br>Payment was cancelled. UUID: {$uuid}");
				        $pjReservationModel
				        ->reset()
				        ->set('id', $booking_arr['id'])
				        ->modify(array('status' => 'Cancelled', 'processed_on' => ':NOW()'));
				        
				         pjFront::pjActionConfirmSend($booking_arr['id'], $option_arr, $this->getLocaleId(), 'cancel');
				        
				        echo $option_arr['o_cancel_url'];
				        exit;
				    }else{
				        $this->log("Payments | {$pjPlugin} plugin<br>Reservation confirmation was failed. UUID: {$uuid}");
				    }
				    
				    if(isset($response['redirect']) && $response['redirect'] == true)
				    {
				        echo $option_arr['o_thankyou_page'];
				        exit;
				    }
				}else{
				    $this->log("Payments | {$pjPlugin} plugin<br>Reservation with UUID {$uuid} not found.");
				}
				echo $this->option_arr['o_thankyou_page'];
				exit;
	        }
	    }
	    
	    echo $this->option_arr['o_thank_you_page'];
	    exit;
	}
}
?>