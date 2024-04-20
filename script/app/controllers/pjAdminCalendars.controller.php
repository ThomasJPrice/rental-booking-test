<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminCalendars extends pjAdmin
{
	public $sessionPrice = 'pjPrice_session';
	
	public function pjActionCheckRefId()
    {
        $this->setAjax(true);
        
        if ($this->isXHR() && $this->_get->check('uuid'))
        {
            $pjCalendarModel = pjCalendarModel::factory();
            if ($this->_get->toInt('id') > 0)
            {
                $pjCalendarModel->where('t1.id !=', $this->_get->toInt('id'));
            }
            echo $pjCalendarModel->where('t1.uuid', $this->_get->toString('uuid'))->findCount()->getData() == 0 ? 'true' : 'false';
        }
        exit;
    }
    
	public function pjActionCreate()
	{
		$this->checkLogin();
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
		
		if ($this->_post->check('calendar_create'))
		{
			$id = pjCalendarModel::factory($this->_post->raw())->insert()->getInsertId();
			if ($id !== false && (int) $id > 0)
			{
				$locale_arr = pjLocaleModel::factory()
				    ->select('t1.*, t2.file')
				    ->join('pjBaseLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				    ->where('t2.file IS NOT NULL')
				    ->orderBy('t1.sort ASC')->findAll()->getData();
				$pjOptionModel = pjOptionModel::factory();
				$pjOptionModel->init($id);
				$pjOptionModel->initConfirmation($id, $locale_arr);
				$err = 'ACR03';
				$i18n_arr = $this->_post->toI18n('i18n');
				if ($i18n_arr)
				{
					pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $id, 'pjCalendar');
				}
				
				$data = $pjOptionModel->reset()->getAllPairs($id);
				pjUtil::pjActionGenerateImages($id, $data);
			} else {
				$err = 'ACR04';
			}
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCalendars&action=pjActionIndex&err=$err");
		} else {
			$this->setLocalesData();
			
			$this->set('user_arr', pjAuthUserModel::factory()->orderBy('t1.name ASC')->findAll()->getData());
	
			$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
	        $this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminCalendars.js');
		}
	}
	
	public function pjActionDeleteCalendar()
	{
		$this->setAjax(true);
	    
	    if (!$this->isXHR())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
	    }
	    
	    if (!self::isPost())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'HTTP method not allowed.'));
	    }
	    
		if (!pjAuth::factory()->hasAccess())
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}
		
	    if ($this->_get->check('id') && $this->_get->toInt('id') > 0)
	    {
	        $id = $this->_get->toInt('id');
	     	if ($id == 1)
	        {
	            pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Default calendar can not be deleted.'));
	        } elseif ($id == $this->getCalendarId()) {
	            pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Current calendar can not be deleted.'));
	        }
	        if (pjCalendarModel::factory()->set('id', $id)->erase()->getAffectedRows() == 1)
	        {
	            pjFeedModel::factory()->where('calendar_id', $id)->eraseAll();
	        	pjCalendarExtraModel::factory()->where('calendar_id', $id)->eraseAll();
	        	pjCalendarDiscountModel::factory()->where('calendar_id', $id)->eraseAll();
	        	pjCalendarVoucherModel::factory()->where('calendar_id', $id)->eraseAll();
	        	pjOptionModel::factory()->where('foreign_id', $id)->eraseAll();
	            pjMultiLangModel::factory()->where('model', 'pjCalendar')->where('foreign_id', $id)->eraseAll();
	            pjMultiLangModel::factory()->reset()->where('model', 'pjOption')->where('foreign_id', $id)->eraseAll();				
				$reservation_ids = pjReservationModel::factory()->where('calendar_id', $id)->findAll()->getDataPair(NULL, 'id');
				if ($reservation_ids) {
					pjReservationModel::factory()->reset()->whereIn('id', $reservation_ids)->eraseAll();
					pjReservationExtraModel::factory()->whereIn('reservation_id', $reservation_ids)->eraseAll();
				}				
				pjPaymentOptionModel::factory()->where('foreign_id', $id)->eraseAll();
				pjMultiLangModel::factory()->reset()->where('model', 'pjPayment')->where('foreign_id', $id)->eraseAll();				
				pjPriceModel::factory()->where('foreign_id', $id)->eraseAll();
				$period_ids = pjPeriodModel::factory()->where('foreign_id', $id)->findAll()->getDataPair(NULL, 'id');
				if ($period_ids) {
					pjPeriodModel::factory()->reset()->whereIn('id', $period_ids)->eraseAll();
					pjPeriodPriceModel::factory()->whereIn('period_id', $period_ids)->eraseAll();
				}				
				$notification_ids = pjNotificationModel::factory()->where('foreign_id', $id)->findAll()->getDataPair(NULL, 'id');
	        	if ($notification_ids) {
					pjNotificationModel::factory()->reset()->whereIn('foreign_id', $notification_ids)->eraseAll();
					pjMultiLangModel::factory()->reset()->where('model', 'pjNotification')->whereIn('foreign_id', $notification_ids)->eraseAll();
				}
				$this->pjActionDeleteImages($id);
	            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Calendar has been deleted.'));
	        }
	        self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Calendar has not been deleted.'));
	    }
	    
	    self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing, empty or invalid parameters.'));
	}
	
	private function pjActionDeleteImages($cid)
	{
		$arr = array(
			PJ_UPLOAD_PATH . '%u_reserved_start.jpg',
			PJ_UPLOAD_PATH . '%u_reserved_end.jpg',
			PJ_UPLOAD_PATH . '%u_pending_pending.jpg',
			PJ_UPLOAD_PATH . '%u_reserved_pending.jpg',
			PJ_UPLOAD_PATH . '%u_pending_reserved.jpg',
			PJ_UPLOAD_PATH . '%u_reserved_reserved.jpg',
			PJ_UPLOAD_PATH . '%u_pending_start.jpg',
			PJ_UPLOAD_PATH . '%u_pending_end.jpg',
		);
		
		if (is_array($cid))
		{
			foreach ($cid as $id)
			{
				if ($id == 1 || $id == $this->getCalendarId()) {
					continue;
				}
				foreach ($arr as $img)
				{
					@unlink(sprintf($img, $id));
				}
			}
		} else {
			if ($cid == 1 || $cid == $this->getCalendarId()) {
				return ;
			}
			foreach ($arr as $img)
			{
				@unlink(sprintf($img, $cid));
			}
		}
	}
	
	public function pjActionDeleteCalendarBulk()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isPost())
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'HTTP method not allowed.'));
		}
		if (!pjAuth::factory()->hasAccess())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
	    }
		$record = $this->_post->toArray('record');
		if (count($record) > 0)
		{
		    pjCalendarModel::factory()->where('id !=', 1)->whereIn('id', $record)->where('id !=', $this->getCalendarId())->eraseAll();
		    pjFeedModel::factory()->where('calendar_id !=', 1)->whereIn('calendar_id', $record)->where('calendar_id !=', $this->getCalendarId())->eraseAll();
			pjCalendarExtraModel::factory()->where('calendar_id !=', 1)->whereIn('calendar_id', $record)->where('calendar_id !=', $this->getCalendarId())->eraseAll();
        	pjCalendarDiscountModel::factory()->where('calendar_id !=', 1)->whereIn('calendar_id', $record)->where('calendar_id !=', $this->getCalendarId())->eraseAll();
        	pjCalendarVoucherModel::factory()->where('calendar_id !=', 1)->whereIn('calendar_id', $record)->where('calendar_id !=', $this->getCalendarId())->eraseAll();
			pjOptionModel::factory()->where('foreign_id !=', 1)->whereIn('foreign_id', $record)->where('foreign_id !=', $this->getCalendarId())->eraseAll();
            pjMultiLangModel::factory()->where('foreign_id !=', 1)->where('model', 'pjCalendar')->whereIn('foreign_id', $record)->where('foreign_id !=', $this->getCalendarId())->eraseAll();
            pjMultiLangModel::factory()->where('foreign_id !=', 1)->reset()->where('model', 'pjOption')->whereIn('foreign_id', $record)->where('foreign_id !=', $this->getCalendarId())->eraseAll();				
			$reservation_ids = pjReservationModel::factory()->where('calendar_id !=', 1)->whereIn('calendar_id', $record)->where('calendar_id !=', $this->getCalendarId())->findAll()->getDataPair(NULL, 'id');
			if ($reservation_ids) {
				pjReservationModel::factory()->reset()->whereIn('id', $reservation_ids)->eraseAll();
				pjReservationExtraModel::factory()->whereIn('reservation_id', $reservation_ids)->eraseAll();
			}				
			pjPaymentOptionModel::factory()->where('foreign_id !=', 1)->whereIn('foreign_id', $record)->where('foreign_id !=', $this->getCalendarId())->eraseAll();
			pjMultiLangModel::factory()->reset()->where('foreign_id !=', 1)->where('model', 'pjPayment')->whereIn('foreign_id', $record)->where('foreign_id !=', $this->getCalendarId())->eraseAll();				
			pjPriceModel::factory()->where('foreign_id !=', 1)->whereIn('foreign_id', $record)->where('foreign_id !=', $this->getCalendarId())->eraseAll();
			$period_ids = pjPeriodModel::factory()->where('foreign_id !=', 1)->whereIn('foreign_id', $record)->where('foreign_id !=', $this->getCalendarId())->findAll()->getDataPair(NULL, 'id');
			if ($period_ids) {
				pjPeriodModel::factory()->reset()->whereIn('id', $period_ids)->eraseAll();
				pjPeriodPriceModel::factory()->whereIn('period_id', $period_ids)->eraseAll();
			}				
			$notification_ids = pjNotificationModel::factory()->where('foreign_id !=', 1)->whereIn('foreign_id', $record)->where('foreign_id !=', $this->getCalendarId())->findAll()->getDataPair(NULL, 'id');
        	if ($notification_ids) {
				pjNotificationModel::factory()->reset()->whereIn('foreign_id', $notification_ids)->eraseAll();
				pjMultiLangModel::factory()->reset()->where('model', 'pjNotification')->whereIn('foreign_id', $notification_ids)->eraseAll();
			}		    
			$this->pjActionDeleteImages($record);
		    pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Calendar(s) has been deleted.'));
		}
		pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing or empty params.'));
	}
	
	public function pjActionGetCalendar()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCalendarModel = pjCalendarModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCalendar' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left');
			
			if($this->isOwner())
			{
				$pjCalendarModel->where('t1.user_id', $this->getUserId());
			}
			
			if ($q = $this->_get->toString('q'))
		    {
		        $q = $pjCalendarModel->escapeStr(trim($q));
		        $q = str_replace(array('%', '_'), array('\%', '\_'), $q);
		        $pjCalendarModel->where('(t1.uuid LIKE "%'.$q.'%" OR t2.content LIKE "%'.$q.'%")');
		        
		    }
		    
			$column = 'name';
			$direction = 'ASC';
			if ($this->_get->check('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
			{
				$column = $this->_get->toString('column');
	        	$direction = strtoupper($this->_get->toString('direction'));
			}

			$total = $pjCalendarModel->findCount()->getData();
			$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjCalendarModel
				->select("t1.id, t1.uuid, t1.status, t2.content AS name,
					(SELECT CONCAT(`TR`.`id`,'~:~',IFNULL(`TR`.`c_name`, ''),'~:~',IFNULL(`TR`.`c_email`, ''),'~:~',`TR`.`created`) FROM `".pjReservationModel::factory()->getTable()."` AS `TR` WHERE `TR`.`calendar_id`=t1.id ORDER BY `TR`.`created` DESC LIMIT 1) AS `latest_booking`, 
					(SELECT `TR`.`created` FROM `".pjReservationModel::factory()->getTable()."` AS `TR` WHERE `TR`.`calendar_id`=t1.id ORDER BY `TR`.`created` DESC LIMIT 1) AS `latest_booking_created`")
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach ($data as $k => $v) {
				if (!empty($v['latest_booking'])) {
					$data[$k]['name'] = pjSanitize::clean($v['name']);
					$latest_booking = explode('~:~', pjSanitize::clean($v['latest_booking']));
					$latest_booking[3] = __('label_on', true).' '.date($this->option_arr['o_date_format'].', '.$this->option_arr['o_time_format'], strtotime($latest_booking[3]));
					$data[$k]['latest_booking'] = implode('~:~', $latest_booking);
				}
			}		
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
	    
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
	    $this->set('has_update', pjAuth::factory('pjAdminCalendars', 'pjActionUpdate')->hasAccess());
	    $this->set('has_create', pjAuth::factory('pjAdminCalendars', 'pjActionCreate')->hasAccess());
	    $this->set('has_delete', pjAuth::factory('pjAdminCalendars', 'pjActionDeleteCalendar')->hasAccess());
	    $this->set('has_delete_bulk', pjAuth::factory('pjAdminCalendars', 'pjActionDeleteCalendarBulk')->hasAccess());
	    $this->set('has_booking_options', pjAuth::factory('pjAdminOptions', 'pjActionIndex')->hasAccess());
	    $this->set('has_update_booking', pjAuth::factory('pjAdminReservations', 'pjActionUpdate')->hasAccess());
	    
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminCalendars.js');
	}
	
	public function pjActionSaveCalendar()
	{
		$this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        
        if (!pjAuth::factory($this->_get->toString('controller'), 'pjActionUpdate')->hasAccess())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
        }
        $pjCalendarModel = pjCalendarModel::factory();
        $arr = $pjCalendarModel->find($this->_get->toInt('id'))->getData();
        if (!$arr)
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Calendar is not found.'));
        }
        if (!in_array($this->_post->toString('column'), $pjCalendarModel->getI18n()))
        {
            $pjCalendarModel->reset()->where('id', $this->_get->toInt('id'))->limit(1)->modifyAll(array($this->_post->toString('column') => $this->_post->toString('value')));
        } else {
            pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($this->_post->toString('column') => $this->_post->toString('value'))), $this->_get->toInt('id'), 'pjCalendar', 'data');
        }
        
        self::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => 'Calendar has been updated.'));
        
        exit;
	}

	private function __getCalendar($cid, $year, $month, $view=1)
	{
		$ABCalendar = new pjABCalendar();
		$ABCalendar
			->setShowNextLink((int) $view > 1 ? false : true)
			->setShowPrevLink((int) $view > 1 ? false : true)
			->setPrevLink("")
			->setNextLink("")
			->set('calendarId', $cid)
			->set('reservationsInfo', pjReservationModel::factory()
				->getInfo(
					$cid,
					date("Y-m-d", mktime(0, 0, 0, $month, 1, $year)),
					date("Y-m-d", mktime(23, 59, 59, $month + $view, 0, $year)),
					$this->option_arr, NULL,
					1
				)
			)
			->set('options', $this->option_arr)
			->set('weekNumbers', (int) $this->option_arr['o_show_week_numbers'] === 1 ? true : false)
			->setStartDay($this->option_arr['o_week_start'])
			->setDayNames(__('day_names', true))
			->setMonthNames(__('months', true))
		;
		
		$this->set('ABCalendar', $ABCalendar);
	}
	
	public function pjActionGetCal()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$this->__getCalendar($this->_get->toInt('cid'), $this->_get->toInt('year'), $this->_get->toInt('month'));
		}
	}
	
	public function pjActionUpdate()
    {
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        $pjOptionModel = new pjOptionModel();
        $pjLimitModel = pjLimitModel::factory();
        
        $calendar_id = $this->getCalendarId(); 
        if ($this->_post->check('property_update'))
        {
	        $pjOptionModel
		        ->where('foreign_id', $calendar_id)
		        ->where('type', 'bool')
		        ->where('tab_id', $this->_post->toInt('tab_id'))
		        ->modifyAll(array('value' => '1|0::0'));   
		        
        	foreach ($this->_post->raw() as $key => $value)
	        {
	            if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
	            {
	                list(, $type, $k) = explode("-", $key);
	                if (!empty($k))
	                {
	                    $_value = ':NULL';
	                    if ($value)
	                    {
	                        switch ($type)
	                        {
	                            case 'string':
	                            case 'text':
	                            case 'enum':
	                            case 'color':
	                                $_value = $this->_post->toString($key);
	                                break;
	                            case 'int':
	                            case 'bool':
	                                $_value = $this->_post->toInt($key);
	                                break;
	                            case 'float':
	                                $_value = $this->_post->toFloat($key);
	                                break;
	                        }
	                    }
	                    $pjOptionModel
	                    ->reset()
	                    ->where('foreign_id', $calendar_id)
	                    ->where('`key`', $k)
	                    ->limit(1)
	                    ->modifyAll(array('value' => $_value));
	                }
	            }
	        }
	        
	        $i18n_arr = $this->_post->toI18n('i18n');	        
	        if (!empty($i18n_arr))
	        {
	            pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $calendar_id, 'pjCalendar', 'data');
	        }
	        
        	switch ($this->_post->toInt('tab_id'))
			{
				case '1':
					pjCalendarModel::factory()->set('id', $this->getCalendarId())->modify(array('user_id' => $this->_post->toInt('user_id')));
					$err = 'AO01';
					break;
				case '2':
					set_time_limit(300);
					$calendar_option = $pjOptionModel->reset()->getAllPairs($this->getCalendarId());
					pjUtil::pjActionGenerateImages($this->getCalendarId(), $calendar_option);
					$err = 'AO02';
					break;
				case '3':
					$err = 'AO03';
					break;
				case '4':
					$err = 'AO04';
					break;
				case '5':
					$err = 'AO05';
					break;
				case '6':
					$err = 'AO06';
					break;
				case '7':
					$err = 'AO07';
					break;
				case '8':
					$err = 'AO12';
					break;
				case '10':
					$pjLimitModel->where('calendar_id', $this->getCalendarId())->eraseAll();
					$pjLimitModel->begin();
					$haystack = array();
					$dates = array();
					$overlaping = false;
					
					if ($this->_post->check('date_from') && $this->_post->check('date_to') && count($this->_post->toArray('date_from')) > 0)
					{
						$date_from_arr = $this->_post->toArray('date_from');
						$date_to_arr = $this->_post->toArray('date_to');
						$block_dates_arr = $this->_post->toArray('block_dates');
						$min_nights_arr = $this->_post->toArray('min_nights');
						$max_nights_arr = $this->_post->toArray('max_nights');
						$blocked_days_arr = $this->_post->toArray('blocked_days');						
						foreach ($date_from_arr as $k => $v)
						{
							if (empty($date_from_arr[$k]) || empty($date_to_arr[$k]) || (!isset($block_dates_arr[$k]) && empty($min_nights_arr[$k]) && empty($max_nights_arr[$k])) )
							{
								continue;
							}							
							$overlap = false;							
							$date_from = strtotime(pjDateTime::formatDate($date_from_arr[$k], $this->option_arr['o_date_format']));
							$date_to = strtotime(pjDateTime::formatDate($date_to_arr[$k], $this->option_arr['o_date_format']));							
							foreach ($dates as $item)
							{
								if ($item['date_from'] <= $date_to && $item['date_to'] >= $date_from)
								{
									$overlap = true;
									$overlaping = true;
									break;
								}
							}
							if ($overlap)
							{
								continue;
							}							
							$needle = $date_from . "_" . $date_to;
							if (in_array($needle, $haystack))
							{
								continue;
							}
							$blocked_days = ':NULL';
							$blocked = 'F';
							$min_nights = empty($min_nights_arr[$k]) ? 1 : $min_nights_arr[$k];
							$max_nights = $max_nights_arr[$k];
							if(isset($block_dates_arr[$k]))
							{
								$blocked = 'T';
								if(!empty($blocked_days_arr[$k]))
								{
									$blocked_days = join("|", $blocked_days_arr[$k]);
								}
							}else{
								array_push($haystack, $needle);
								array_push($dates, array('date_from' => $date_from, 'date_to' => $date_to));
							}
							
							$pjLimitModel
								->reset()
								->set('calendar_id', $this->getCalendarId())
								->set('date_from', pjDateTime::formatDate($date_from_arr[$k], $this->option_arr['o_date_format']))
								->set('date_to', pjDateTime::formatDate($date_to_arr[$k], $this->option_arr['o_date_format']))
								->set('blocked', $blocked)
								->set('min_nights', $min_nights)
								->set('max_nights', $max_nights)
								->set('blocked_days', $blocked_days)
								->insert()
							;
						}
					}
					$pjLimitModel->commit();
					$err = 'AO10';
					break;
				case '12':
					$pjReservationModel = pjReservationModel::factory();
					$pjCalendarModel = pjCalendarModel::factory();
					
					$week_start = $this->option_arr['o_week_start'];
					$pjReservationModel->reset();
					$pjReservationModel->where('t1.calendar_id', $this->getCalendarId());
					if($this->_post->toString('period') == 'next')
					{
						$column = 'date_from';
						$direction = 'ASC';
						
						$where_str = pjUtil::getComingWhere($this->_post->toString('coming_period'), $week_start);
						if($where_str != '')
						{
							$pjReservationModel->where($where_str);
						}
					}else if($this->_post->toString('period') == 'all'){
						$column = 'created';
						$direction = 'ASC';
					}else if($this->_post->toString('period') == 'range'){
						$column = 'created';
						$direction = 'ASC';
						$date_from = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
						$date_to = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
						$where_str = "((t1.date_from BETWEEN '$date_from' AND '$date_to') OR 
							   (t1.date_to BETWEEN '$date_from' AND '$date_to') OR 
							   (t1.date_from <= '$date_from' AND t1.date_to >= '$date_to'))";
						$pjReservationModel->where($where_str);
					}else{
						$column = 'created';
						$direction = 'ASC';
						$where_str = pjUtil::getMadeWhere($this->_post->toString('made_period'), $week_start);
						if($where_str != '')
						{
							$pjReservationModel->where($where_str);
						}
					}	
					
					$_arr= $pjReservationModel
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
						if($this->option_arr['o_price_based_on'] == 'nights')
						{
							$v['date_from'] = $v['date_from'] . ' ' . "12:00:00";
							$v['date_to'] = $v['date_to'] . ' ' . "12:00:00";
						}else{
							$v['date_from'] = $v['date_from'] . ' ' . "00:00:00";
							$v['date_to'] = $v['date_to'] . ' ' . "23:59:59";
						}
						$arr[] = $v;
					}
					
					if($this->_post->toString('type') == 'file')
					{
						$this->setLayout('pjActionEmpty');						
						if($this->_post->toString('format') == 'csv')
						{
							$csv = new pjCSV();
							$csv
								->setHeader(true)
								->setName("Export-".time().".csv")
								->process($arr)
								->download();
						}
						if($this->_post->toString('format') == 'xml')
						{
							$xml = new pjXML();
							$xml
								->setEncoding('UTF-8')
								->setName("Export-".time().".xml")
								->process($arr)
								->download();
						}
						if($this->_post->toString('format') == 'ical')
						{						
							foreach($arr as $k => $v)
			                {
			                    $v['uuid'] = $v['uuid'] . '-' . $k;			                    
			                    $_arr = array();
			                    if(!empty($v['c_name']))
			                    {
			                        $_arr[] = pjSanitize::html($v['c_name']);
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
			                    
			                    $v['desc'] = join("\r\n\t", $_arr);
			                    $v['location'] = pjSanitize::html($v['property']);
			                    $v['summary'] = 'Reservation';
			                    $arr[$k] = $v;
			                }
			                
							$ical = new pjICal();
			                $ical
			                ->setName("Export-".time().".ics")
			                ->setProdID('Rental Property Booking Calendar')
			                ->setSummary('summary')
			                ->setCName('desc')
			                ->setLocation('location')
			                ->setVersion("VERSION:2.0")
			                ->setTimezone("UTC/GMT")
			                ->process($arr)
			                ->download();
						}
						exit;
					}else{
						$pjPasswordModel = pjPasswordModel::factory();
						$password = md5($this->_post->toString('password').$this->getUserId().PJ_SALT);
						$period = $this->_post->toString('period') == 'next' ? $this->_post->toString('coming_period') : $this->_post->toString('made_period');
						$arr = $pjPasswordModel
							->where("t1.calendar_id", $this->getCalendarId())
							->where("t1.user_id", $this->getUserId())
							->where("t1.password", $password)
							->where("t1.format", $this->_post->toString('format'))
							->where("t1.type", $this->_post->toString('period'))
							->where("t1.period", $period)
							->limit(1)
							->findAll()
							->getData();
						if (count($arr) != 1)
						{
							$pjPasswordModel->setAttributes(array('calendar_id' => $this->getCalendarId(), 'user_id' => $this->getUserId(), 'password' => $password, 'format' => $this->_post->toString('format'), 'type' => $this->_post->toString('period'), 'period' => $period))->insert();
						}
						$this->set('password', $password);
						
					}
				break;
			}
			if ($this->_post->toInt('tab_id') != 12) {
	        	pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCalendars&action=pjActionUpdate&id=" . $calendar_id . "&err=" . $err . "&tab=" . $this->_post->toString('tab'));
			}
        }
        
       	$arr = pjCalendarModel::factory()
			->find($this->getCalendarId())
			->getData();
		if (count($arr) === 0)
		{
			pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCalendars&action=pjActionIndex&err=ACR08");
		}
		if($this->isOwner())
		{
			if($arr['user_id'] != $this->getUserId())
			{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCalendars&action=pjActionIndex&err=ACR08");
			}
		}
		$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjCalendar');            
		$this->set('arr', $arr);
		
		$this->setLocalesData();
		
		$this->__getCalendar($this->getCalendarId(), date("Y"), date("n"));
		$this->set('user_arr', pjAuthUserModel::factory()->orderBy('t1.name ASC')->findAll()->getData());
		
		$calendar_option_arr = $pjOptionModel
			->where('t1.foreign_id', $calendar_id)
			->orderBy('t1.tab_id ASC, t1.order ASC')
			->findAll()
			->getDataPair('key', null);
		$this->set('calendar_option_arr', $calendar_option_arr);
		$group_1_arr = $group_2_arr = $group_3_arr = array();
		foreach ($calendar_option_arr as $val) {
			if (in_array($val['key'], array('o_bf_name', 'o_bf_phone', 'o_bf_adults', 'o_bf_children'))) {
				$group_1_arr[] = $val;
			} elseif (in_array($val['key'], array('o_bf_address', 'o_bf_country', 'o_bf_state', 'o_bf_city', 'o_bf_zip'))) {
				$group_2_arr[] = $val;
			} elseif (in_array($val['key'], array('o_bf_notes', 'o_bf_captcha', 'o_bf_terms'))) {
				$group_3_arr[] = $val;
			}
		}
		$this->set('group_1_arr', $group_1_arr)
			->set('group_2_arr', $group_2_arr)
			->set('group_3_arr', $group_3_arr);
			
		$this->set('limit_arr', pjLimitModel::factory()->where('t1.calendar_id', $this->getCalendarId())->orderBy('t1.date_from ASC')->findAll()->getData());
		
		if ($this->option_arr['o_price_plugin'] == 'price') {
			$this->getPrices();
		} else {
			$this->getPeriods();
		}
		
		$this->appendCss('index.php?controller=pjFront&action=pjActionLoadCss&cid=' . $this->getCalendarId() . '&' . rand(1,99999), PJ_INSTALL_URL, true);
		
		$this->appendCss('awesome-bootstrap-checkbox.css', PJ_THIRD_PARTY_PATH . 'awesome_bootstrap_checkbox/');
		$this->appendJs('bootstrap-colorpicker.min.js', PJ_THIRD_PARTY_PATH . 'colorpicker/');
		$this->appendCss('bootstrap-colorpicker.min.css', PJ_THIRD_PARTY_PATH . 'colorpicker/');
		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendCss('datepicker3.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		if ($this->option_arr['o_price_plugin'] == 'price') {
			$this->appendJs('pjAdminPrices.js');
		} else {
			$this->appendJs('pjAdminPeriods.js');
		}
		$this->appendJs('pjAdminCalendars.js');
    }
	
    private function getPrices() {
    	$pjPriceModel = pjPriceModel::factory();
    	$_price_arr = $pjPriceModel
			->where('t1.foreign_id', $this->getCalendarId())
			->orderBy('t1.tab_id ASC, t1.id ASC, t1.date_from DESC, t1.date_to DESC')
			->findAll()
			->getData();
		
		$price_arr = array();
		foreach ($_price_arr as $k => $v)
		{
			if (!isset($price_arr[$v['season']]))
			{
				$price_arr[$v['season']] = array();
			}
			$price_arr[$v['season']][] = $v;
		}
		$query = sprintf("SELECT p1.id, p1.foreign_id, p1.tab_id, p1.season, p1.date_from, p1.date_to,
			p2.id AS `p2_id`, p2.foreign_id AS `p2_foreign_id`, p2.tab_id AS `p2_tab_id`, p2.season AS `p2_season`, p2.date_from AS `p2_date_from`, p2.date_to AS `p2_date_to`
			FROM (
				SELECT p1.id AS `pid1`, p2.id AS `pid2`
				FROM `%1\$s` `p1`, `%1\$s` `p2`
				WHERE p2.date_from BETWEEN p1.date_from AND p1.date_to
                AND p2.id != p1.id
					UNION
				SELECT p1.id, p2.id
				FROM `%1\$s` `p1`, `%1\$s` `p2`
				WHERE p2.date_to BETWEEN p1.date_from AND p1.date_to
				AND p2.id != p1.id
			) `p`, `%1\$s` `p1`, `%1\$s` `p2`
			WHERE p1.id = `pid1` AND p2.id = `pid2`
			AND p2.id > p1.id
			AND p1.tab_id != p2.tab_id
			GROUP BY p1.id, p1.foreign_id, p1.tab_id, p1.season, p1.date_from, p1.date_to, p2.id, p2.foreign_id, p2.tab_id, p2.season, p2.date_from, p2.date_to", $pjPriceModel->getTable());
		$price_overlap_arr = $pjPriceModel->reset()->prepare($query)->exec()->getData();
		
		$this
			->set('price_arr', $price_arr)
			->set('price_overlap_arr', $price_overlap_arr)
			->set('date_format', pjUtil::toBootstrapDate($this->option_arr['o_date_format']));
    }
    
    private function getPeriods() {
     	$period_arr = pjPeriodModel::factory()
			->where('foreign_id', $this->getCalendarId())
			->orderBy('t1.start_date ASC, t1.end_date ASC')
			->findAll()
			->getData();
			
		$pjPeriodPriceModel = pjPeriodPriceModel::factory();
		foreach ($period_arr as $k => $period)
		{
			$period_arr[$k]['price_arr'] = $pjPeriodPriceModel->reset()->where('t1.period_id', $period['id'])->orderBy('t1.adults ASC, t1.children ASC')->findAll()->getData();
		}
		$this->set('period_arr', $period_arr);
    }
	
	public function pjActionCheckAssignCalendar()
	{
	    $this->setAjax(true);
	    if($this->getCalendarId() == false)
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('lblNoCalendarAssignedMsg', true), 'btnOK' => __('gridBtnOk', true)));
	    }else{
	        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	    }
	    exit;
	}
	
	public function pjActionPaymentOptions()
    {
        $this->checkLogin();
        
        $this->setAjax(true);
        
        $foreign_id = NULL;
        if($this->_get->check('calendar_id') && $this->_get->toInt('calendar_id') > 0)
        {
            $foreign_id = $this->_get->toInt('calendar_id');
        }
        if($this->_post->check('options_update'))
        {
            if (pjObject::getPlugin('pjPayments') !== NULL && $this->_post->check('plugin_payment_options'))
            {
                $this->requestAction(array(
                    'controller' => 'pjPayments',
                    'action' => 'pjActionSaveOptions',
                    'params' => array(
                        'foreign_id' => $foreign_id,
                        'data' => $this->_post->toArray('plugin_payment_options'),
                    )
                ), array('return'));
            }
            if(in_array($this->_post->toString('payment_method'), array('cash', 'bank')))
            {
                $pjPaymentOptionModel = new pjPaymentOptionModel();
                
                if($pjPaymentOptionModel->reset()->where('foreign_id', $foreign_id)->where('`payment_method`', $this->_post->toString('payment_method'))->findCount()->getData() == 0)
                {
                    $pjPaymentOptionModel->reset()->setAttributes(array('foreign_id' => $foreign_id, 'payment_method' => $this->_post->toString('payment_method'), 'is_active' => $this->_post->toInt('is_active')))->insert();
                }else{
                    $pjPaymentOptionModel
                    ->reset()
                    ->where('foreign_id', $foreign_id)
                    ->where('`payment_method`', $this->_post->toString('payment_method'))
                    ->limit(1)
                    ->modifyAll(array('is_active' => $this->_post->toInt('is_active')));
                }
            }
            if ($this->_post->check('i18n'))
            {
                pjMultiLangModel::factory()->updateMultiLang($this->_post->toI18n('i18n'), $foreign_id, 'pjPayment', 'data');
            }
            if ($this->_post->check('i18n_options'))
            {
                pjMultiLangModel::factory()->updateMultiLang($this->_post->toI18n('i18n_options'), $foreign_id, 'pjOption', 'data');
            }
        }else{
            $this->set('i18n', pjMultiLangModel::factory()->getMultiLang($foreign_id, 'pjPayment'));
            $this->set('i18n_options', pjMultiLangModel::factory()->getMultiLang($foreign_id, 'pjOption'));
            
            $this->setLocalesData();
            
            $o_arr = pjOptionModel::factory()->getPairs($foreign_id);
            $this->set('o_arr', $o_arr);
        }
    }
    
	public function pjActionNotificationsGetMetaData()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if (!self::isGet())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
        }
        
        if (!(isset($this->query['recipient']) && pjValidation::pjActionNotEmpty($this->query['recipient'])))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
        }
        $arr = pjNotificationModel::factory()
        ->where('t1.recipient', $this->query['recipient'])
        ->where('t1.is_general', 0)
        ->where('t1.foreign_id', $this->_get->toInt('calendar_id'))
        ->orderBy('t1.id ASC')
        ->findAll()
        ->getData();
        $this->set('arr', $arr);
    }
    
    public function pjActionNotificationsGetContent()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if (!self::isGet())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
        }
        
        if (!($this->_get->check('recipient') && $this->_get->check('variant') && $this->_get->check('transport'))
            && pjValidation::pjActionNotEmpty($this->_get->toString('recipient'))
            && pjValidation::pjActionNotEmpty($this->_get->toString('variant'))
            && pjValidation::pjActionNotEmpty($this->_get->toString('transport'))
            && in_array($this->_get->toString('transport'), array('email', 'sms'))
            )
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
        }
        
        $arr = pjNotificationModel::factory()
        ->where('t1.recipient', $this->_get->toString('recipient'))
        ->where('t1.variant', $this->_get->toString('variant'))
        ->where('t1.transport', $this->_get->toString('transport'))
        ->where('t1.foreign_id', $this->_get->toInt('calendar_id'))
        ->where('t1.is_general', 0)
        ->limit(1)
        ->findAll()
        ->getDataIndex(0);
        
        if (!$arr)
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Message not found.'));
        }
        
        $arr['i18n'] = pjBaseMultiLangModel::factory()->getMultiLang($arr['id'], 'pjNotification');
        $this->set('arr', $arr);
        
        # Check SMS
        $this->set('is_sms_ready', (isset($this->option_arr['plugin_sms_api_key']) && !empty($this->option_arr['plugin_sms_api_key']) ? 1 : 0));
        
        # Get locales
        $locale_arr = pjBaseLocaleModel::factory()
        ->select('t1.*, t2.file, t2.title')
        ->join('pjBaseLocaleLanguage', 't2.iso=t1.language_iso', 'left')
        ->where('t2.file IS NOT NULL')
        ->orderBy('t1.sort ASC')
        ->findAll()
        ->getData();
        
        $lp_arr = array();
        foreach ($locale_arr as $item)
        {
            $lp_arr[$item['id']."_"] = array($item['file'], $item['title']);
        }
        $this->set('lp_arr', $locale_arr);
        $this->set('locale_str', self::jsonEncode($lp_arr));
        $this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjBaseLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
    }
    
    public function pjActionNotificationsSetContent()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
        }
        
        if (!(isset($this->body['notify_id']) && pjValidation::pjActionNumeric($this->body['notify_id'])))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
        }
        
        $isToggle = $this->_post->check('is_active') && in_array($this->_post->toInt('is_active'), array(1,0));
        $isFormSubmit = $this->_post->check('i18n') && !$this->_post->isEmpty('i18n');
        
        if (!($isToggle xor $isFormSubmit))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Data mismatch.'));
        }
        
        if ($isToggle)
        {
            pjNotificationModel::factory()
            ->set('id', $this->_post->toInt('notify_id'))
            ->modify(array('is_active' => $this->_post->toInt('is_active')));
        } elseif ($isFormSubmit) {
            pjBaseMultiLangModel::factory()->updateMultiLang($this->_post->toArray('i18n'), $this->_post->toInt('notify_id'), 'pjNotification');
        }
        
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Notification has been updated.'));
    }
    
	public function pjActionDeleteAllPrices()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Prices has been deleted.'));
	}
	
	public function pjActionBeforeSavePrices()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!isset($_SESSION[$this->sessionPrice]) || !is_array($_SESSION[$this->sessionPrice]))
		{
			$_SESSION[$this->sessionPrice] = array();
		}
		
		if ($this->_post->check('tabs'))
		{
			$post = $this->_post->raw();
			if (isset($_SESSION[$this->sessionPrice]['tabs']))
			{
				// If you want to append array elements from the second array to the first array
				// while not overwriting the elements from the first array and not re-indexing,
				// use the + array union operator:
				$_SESSION[$this->sessionPrice]['tabs'] = $_SESSION[$this->sessionPrice]['tabs'] + $post['tabs'];
				$this->_post->remove('tabs');
			}
			
			$_SESSION[$this->sessionPrice] = array_merge($_SESSION[$this->sessionPrice], $this->_post->raw());
			self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
		}
		self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
	}
	
	public function pjActionSavePrices()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!isset($_SESSION[$this->sessionPrice]) || empty($_SESSION[$this->sessionPrice]))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		if (!pjAuth::factory('pjAdminCalendars', 'pjActionUpdate')->hasAccess())
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Access denied.'));
		}
		$STORE = $_SESSION[$this->sessionPrice];
		
		$tmp = array();
		$tab_ids = array();
		foreach ($STORE['tabs'] as $tab_id => $tab_name)
		{
			$tab_ids[] = $tab_id;
			$i = $tab_id;
			if (!is_int($i) || (is_int($i) && $i > 1))
			{
				$tmp_arr = $STORE[$i . '_date_from'];
				reset($tmp_arr);
				$first_key = key($tmp_arr);
				if (is_array($STORE[$i . '_date_from'][$first_key])) {
					$date_from = pjDateTime::formatDate($STORE[$i . '_date_from'][$first_key][0], $this->option_arr['o_date_format']);
					$date_to = pjDateTime::formatDate($STORE[$i . '_date_to'][$first_key[0]], $this->option_arr['o_date_format']);
				} else {
					$date_from = pjDateTime::formatDate($STORE[$i . '_date_from'][$first_key], $this->option_arr['o_date_format']);
					$date_to = pjDateTime::formatDate($STORE[$i . '_date_to'][$first_key], $this->option_arr['o_date_format']);
				}
			}
			foreach ($STORE[$i . '_adults'] as $k => $adults)
			{
				$arr = array($tab_id, $adults, $STORE[$i . '_children'][$k]);
				if ($i > 1)
				{
					$arr[] = $date_from;
					$arr[] = $date_to;
				} else {
					$arr[] = $tab_name;
				}
				$string = join("|", $arr);
				if (in_array($string, $tmp))
				{
					self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				}
				$tmp[] = $string;
				foreach(range(0, 6) as $wday)
			    {
			        if((float) $STORE[$i . '_day_' . $wday][$k] > 99999999999999.99)
			        {
			            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => ''));
			        }
			    }
			}
		}
		$j = 1;
		$insert_ids = array();
		$pjPriceModel = pjPriceModel::factory();
		$pjPriceModel->where('foreign_id', $this->getCalendarId())->eraseAll();
		
		foreach ($STORE['tabs'] as $tab_id => $tab_name)
		{
			$i = $tab_id;
			
			$data = array();
			
			$data['season'] = $tab_name;
			$data['foreign_id'] = $this->getCalendarId();
			if (!is_int($i) || (is_int($i) && $i > 1))
			{
				$tmp_arr = $STORE[$i . '_date_from'];
				reset($tmp_arr);
				$first_key = key($tmp_arr);
				
				if (is_array($STORE[$i . '_date_from'][$first_key])) {
					$data['date_from'] = pjDateTime::formatDate($STORE[$i . '_date_from'][$first_key][0], $this->option_arr['o_date_format']);
					$data['date_to'] = pjDateTime::formatDate($STORE[$i . '_date_to'][$first_key][0], $this->option_arr['o_date_format']);
				} else {
					$data['date_from'] = pjDateTime::formatDate($STORE[$i . '_date_from'][$first_key], $this->option_arr['o_date_format']);
					$data['date_to'] = pjDateTime::formatDate($STORE[$i . '_date_to'][$first_key], $this->option_arr['o_date_format']);
				}
				$j++;
			}
			$data['tab_id'] = $j;
			
			$rand = null;
			foreach ($STORE[$i . '_adults'] as $k => $adults)
			{
				$data['adults'] = $STORE[$i . '_adults'][$k];
				$data['children'] = $STORE[$i . '_children'][$k];
				$data['mon'] = $STORE[$i . '_day_1'][$k];
			    $data['tue'] = $STORE[$i . '_day_2'][$k];
			    $data['wed'] = $STORE[$i . '_day_3'][$k];
			    $data['thu'] = $STORE[$i . '_day_4'][$k];
			    $data['fri'] = $STORE[$i . '_day_5'][$k];
			    $data['sat'] = $STORE[$i . '_day_6'][$k];
			    $data['sun'] = $STORE[$i . '_day_0'][$k];
				if(strpos($k, "~:~") != false)
				{
				    list($idx1, $idx2) = explode("~:~", $k);
				    $insert_ids[] = $pjPriceModel->reset()->setAttributes($data)->insert()->getInsertId();
				}else{
				    if((int) $data['adults'] == 0 && (int) $data['children'] == 0)
				    {
				        $insert_ids[] = $pjPriceModel->reset()->setAttributes($data)->insert()->getInsertId();
				    }
				}
			}
		}
		$_SESSION[$this->sessionPrice] = NULL;
		unset($_SESSION[$this->sessionPrice]);
		
		if (in_array(false, $insert_ids) || in_array(0, $insert_ids))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	}
	
	public function pjActionDeleteSeasonPrices()
	{
	    $this->setAjax(true);
	    
	    if (!$this->isXHR())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
	    }
	    if (!($this->getCalendarId()))
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
	    }
	    if (!($this->_get->toInt('tab_id')))
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
	    }
	    pjPriceModel::factory()->where('foreign_id', $this->getCalendarId())->where('tab_id', $this->_get->toInt('tab_id'))->eraseAll();
	    self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Season prices has been deleted.'));
	}
	
	public function pjActionDeletePeriods()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		$pjPeriodModel = pjPeriodModel::factory();
		$period_ids = $pjPeriodModel->where('foreign_id', $this->getCalendarId())->findAll()->getDataPair(NULL, 'id');
		if (!empty($period_ids))
		{
			$pjPeriodModel->eraseAll();
			pjPeriodPriceModel::factory()->whereIn('period_id', $period_ids)->eraseAll();
			
		}
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Prices has been deleted.'));
	}
	
	public function pjActionSavePeriods()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!pjAuth::factory('pjAdminCalendars', 'pjActionUpdate')->hasAccess())
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Access denied.'));
		}
		if ($this->_post->check('default_price') && count($this->_post->toArray('default_price')) > 0 && $this->_post->check('start_date') && count($this->_post->toArray('start_date')) > 0)
		{
			$pjPeriodModel = pjPeriodModel::factory();
			$pjPeriodPriceModel = pjPeriodPriceModel::factory();
			
			$default_price_arr = $this->_post->toArray('default_price');
			$start_date_arr = $this->_post->toArray('start_date');
			$end_date_arr = $this->_post->toArray('end_date');
			$from_day_arr = $this->_post->toArray('from_day');
			$to_day_arr = $this->_post->toArray('to_day');
			foreach ($default_price_arr as $k => $v)
			{
				if (empty($start_date_arr[$k]) || empty($end_date_arr[$k]))
				{
					continue;
				}
				
				$start_date = pjDateTime::formatDate($start_date_arr[$k], $this->option_arr['o_date_format']);
				$end_date = pjDateTime::formatDate($end_date_arr[$k], $this->option_arr['o_date_format']);
				if (!pjValidation::pjActionDate($start_date) || !pjValidation::pjActionDate($end_date))
				{
					continue;
				}
				
				$period_id = $pjPeriodModel->reset()->setAttributes(array(
					'foreign_id' => $this->getCalendarId(),
					'start_date' => $start_date,
					'end_date' => $end_date,
					'from_day' => $from_day_arr[$k],
					'to_day' => $to_day_arr[$k],
					'default_price' => $default_price_arr[$k]
				))->insert()->getInsertId();
				
				if ($period_id !== false && (int) $period_id > 0)
				{
					$adults_arr = $this->_post->check('adults') ? $this->_post->toArray('adults') : array();
					if ($adults_arr && isset($adults_arr[$k]))
					{
						$children_arr = $this->_post->toArray('children');
						$price_arr = $this->_post->toArray('price');
						foreach ($adults_arr[$k] as $index => $smth)
						{
							if (empty($price_arr[$k][$index]))
							{
								continue;
							}
							$pjPeriodPriceModel
								->reset()
								->set('period_id', $period_id)
								->set('adults', $adults_arr[$k][$index])
								->set('children', $children_arr[$k][$index])
								->set('price', $price_arr[$k][$index])
								->insert();
						}
					}
				}
			}
		}
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	}
	
	public function pjActionDeletePeriod()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
	
		if ($this->_post->check('id') && $this->_post->toInt('id') > 0) {
			if (pjPeriodModel::factory()->set('id', $this->_post->toInt('id'))->erase()->getAffectedRows() == 1)
			{
				pjPeriodPriceModel::factory()->where('period_id', $this->_post->toInt('id'))->eraseAll();
				self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
		}
		self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing parameters.'));
	}
	
	public function pjActionGetFeed()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if (!self::isGet())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        
        $pjFeedModel = pjFeedModel::factory()
	        ->join('pjCalendar', 't2.id=t1.calendar_id', 'left outer')
	        ->join('pjBaseMultiLang', sprintf("t3.foreign_id = t2.id AND t3.model = 'pjCalendar' AND t3.locale = '%u' AND t3.field = 'name'", $this->getLocaleId()), 'left outer');
        $pjFeedModel->where('calendar_id', $this->_get->toInt('calendar_id'));
        
        $column = 'property';
        $direction = 'ASC';
        if (isset($this->query['direction']) && isset($this->query['column']) && in_array(strtoupper($this->query['direction']), array('ASC', 'DESC')))
        {
            $column = $this->query['column'];
            $direction = strtoupper($this->query['direction']);
        }
        
        $total = $pjFeedModel->findCount()->getData();
        $rowCount = $this->_get->toInt('rowCount') ?: 10;
        $pages = ceil($total / $rowCount);
        $page = $this->_get->toInt('page') ?: 1;
        $offset = ((int) $page - 1) * $rowCount;
        if ($page > $pages)
        {
            $page = $pages;
        }
        
        $data = array();
        if ($total)
        {
            $data = $pjFeedModel
            ->select(sprintf('t1.id, t1.provider_id, t1.calendar_id, t3.content AS property,
    					(SELECT COUNT(*) FROM `%s` AS t3 WHERE t3.calendar_id = t1.calendar_id AND t3.provider_id = t1.provider_id AND t3.date_to >= "%s") AS `cnt`
    					', pjReservationModel::factory()->getTable(), date('Y-m-d')))
			->orderBy("$column $direction")
			->limit($rowCount, $offset)
			->findAll()
			->getData();
			
			$data = pjSanitize::clean($data);			
			$providers = __('feed_providers', true);
			foreach($data as $k => $v)
			{
			    $data[$k]['provider'] = $providers[$v['provider_id']];
			}
        }
        self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction', 'is_support', 'has_update', 'has_delete'));
    }
    
    public function pjActionSaveFeed()
    {
        $this->setAjax(true);
        
        if ($this->isXHR())
        {
            if($this->_post->isEmpty('url'))
            {
                self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('rpbc_feed_url_empty', true)));
            }
            include ( PJ_COMPONENTS_PATH . 'iCalEasyReader.php' );
            $valid = false;
            $feed_url = $this->_post->toString('url');
            $ical = new iCalEasyReader();
            $fead_contents = file_get_contents($feed_url);
            $lines = $ical->load( $fead_contents );
            
        	if (!empty($lines)) {
                if(isset($lines['VEVENT']) && !empty($lines['VEVENT']))
                {
                    $valid = true;
                } else {
                	self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => __('pj_feed_data_empty', true)));
                }
            }
            if (!$valid) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => __('pj_feed_error_msg', true)));
            }
            $provider_id = 1;
            if(strpos($feed_url, 'airbnb') !== false)
            {
                $provider_id = 2;
            }else if(strpos($feed_url, 'vrbo.com') !== false){
                $provider_id = 3;
            }else if(strpos($feed_url, 'homeaway') !== false){
                $provider_id = 4;
            }else if(strpos($feed_url, 'tripadvisor.com') !== false){
                $provider_id = 5;
            }else if(strpos($feed_url, 'booking.com') !== false){
                $provider_id = 6;
            }
            if($this->_post->toInt('feed_id') > 0)
            {
                $feed = pjFeedModel::factory()->find($this->_post->toInt('feed_id'))->getData();
                if (!$feed)
                {
                    self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Feed not found.'));
                }
                
                $data = array();
                $data['id'] = $this->_post->toInt('feed_id');
                $data['provider_id'] = $this->_post->toInt('provider_id');
                $data['url'] = $feed_url;
                pjFeedModel::factory()->set('id', $this->_post->toInt('feed_id'))->modify($data);
                pjAppController::syncFeeds($this->_post->toInt('feed_id'));
                self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Feed has been updated.'));
            }else{
                $id = pjFeedModel::factory(array_merge($this->_post->raw(), array('provider_id' => $provider_id)))->insert()->getInsertId();
                if (!$id)
                {
                    self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Feed has not been added.'));
                }
                $resp = pjAppController::syncFeeds($id);
            }
            
            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Feed has been added', 'provider_id' => $this->_post->toInt('provider_id')));
        }
        exit;
    }
    
    public function pjActionViewFeed()
    {
        if (!self::isGet())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        
        if (!(isset($this->query['id']) && pjValidation::pjActionNumeric($this->query['id'])))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }
        
        $feed = pjFeedModel::factory()->find($this->query['id'])->getData();
        if (!$feed)
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Feed not found.'));
        }
        
        pjUtil::redirect($feed['url']);
    }
    
    public function pjActionRefreshFeed()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if (!self::isGet())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        
        if (!(isset($this->query['id']) && pjValidation::pjActionNumeric($this->query['id'])))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }
        
        $response = pjAppController::syncFeeds($this->query['id']);
        self::jsonResponse($response);
    }
    
    public function pjActionDeleteFeed()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        if (!pjAuth::factory()->hasAccess())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
        }
        if (!($this->_get->toInt('id')))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }
        if (!pjFeedModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Feed has not been deleted.'));
        }
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Feed has been deleted'));
        exit;
    }
    
    
    public function pjActionUpdateFeed()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if (!self::isGet())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        if (!($this->_get->toInt('id')))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }
        $feed = pjFeedModel::factory()->find($this->_get->toInt('id'))->getData();
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'feed' => $feed));
    }
    
	public function pjActionGetPassword()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        $pjPasswordModel = pjPasswordModel::factory();
	        $pjPasswordModel->where('t1.calendar_id', $this->getCalendarId());
	        $column = 'id';
	        $direction = 'ASC';
	        if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
	        {
	            $column = $this->_get->toString('column');
	            $direction = strtoupper($this->_get->toString('direction'));
	        }
	        
	        $total = $pjPasswordModel->findCount()->getData();
	        $rowCount = $this->_get->toInt('rowCount') ?: 10;
	        $pages = ceil($total / $rowCount);
	        $page = $this->_get->toInt('page') ?: 1;
	        $offset = ((int) $page - 1) * $rowCount;
	        if ($page > $pages)
	        {
	            $page = $pages;
	        }
	        
	        $data = $pjPasswordModel
	        ->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
	        
	        $export_formats = __('export_formats', true, false);
			$coming_arr = __('coming_arr', true, false);
			$made_arr = __('made_arr', true, false);
			$export_periods = __('export_periods', true, false);
	        foreach($data as $k => $v)
	        {
	            $v['params'] = '&format=' . $v['format'] . '&calendar_id=' . $v['calendar_id'] . '&type=' . $v['type'] . '&period=' . $v['period'] . '&p=' . $v['password'];
				if($v['type'] == 'all')
				{
					$v['period'] = '';
				}else{
					if($v['type'] == 'next')
					{
						$v['period'] = $coming_arr[$v['period']];
					}else{
						$v['period'] = $made_arr[$v['period']];
					}
				}
				$v['type'] = $export_periods[$v['type']];
				$v['format'] = $export_formats[$v['format']];
				$data[$k] = $v;
	        }
	        
	        pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
	    }
	    exit;
	}
	public function pjActionDeletePassword()
	{
	    $this->setAjax(true);
	    
	    if (!$this->isXHR())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
	    }
	    if (!self::isPost())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
	    }
	    if (!pjAuth::factory()->hasAccess())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
	    }
	    if (!($this->_get->toInt('id')))
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
	    }
	    $id = $this->_get->toInt('id');
	    if (pjPasswordModel::factory()->setAttributes(array('id' => $id))->erase()->getAffectedRows() == 1)
	    {
	        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'The password has been deleted.'));
	    } else {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'The password has not been deleted.'));
	    }
	    exit;
	}
	
	public function pjActionDeletePasswordBulk()
	{
	    $this->setAjax(true);
	    
	    if (!$this->isXHR())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
	    }
	    if (!self::isPost())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
	    }
	    if (!pjAuth::factory()->hasAccess())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
	    }
	    if (!$this->_post->has('record'))
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
	    }
	    $record = $this->_post->toArray('record');
	    if (empty($record))
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Missing, empty or invalid parameters.'));
	    }
	    pjPasswordModel::factory()->whereIn('id', $record)->eraseAll();
	    self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Passwords has been deleted.'));
	    exit;
	}
	
	public function pjActionGetReservation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjReservationModel = pjReservationModel::factory()->join('pjCalendar', 't2.id=t1.calendar_id', 'inner');
			if ($this->_get->check('uuid') && $this->_get->toString('uuid') != '')
			{
				$q = $this->_get->toString('uuid');
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjReservationModel->where("t1.uuid LIKE '%$q%'");
			}
			
			if ($this->_get->check('calendar_id') && $this->_get->toInt('calendar_id') > 0)
			{
				$pjReservationModel->where('t1.calendar_id', $this->_get->toInt('calendar_id'));
			}
			
			if ($this->_get->check('date') && $this->_get->toString('date') != '')
			{
				$pjReservationModel->where(sprintf("('%s' BETWEEN t1.date_from AND t1.date_to)", $this->_get->toString('date')));
			}
			
			if ($this->_get->check('status') && $this->_get->toString('status') != '')
			{
				$pjReservationModel->where('t1.status', $this->_get->toString('status'));
			}
			
			if ($q = $this->_get->toString('q'))
			{
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjReservationModel->where("(t1.uuid LIKE '%$q%' OR t1.c_name LIKE '%$q%' OR t1.c_email LIKE '%$q%' OR t1.c_phone LIKE '%$q%')");
			}
			
			if ($this->_get->check('time') && $this->_get->toString('time') != '')
			{
				$pjReservationModel->where(sprintf("'%s' BETWEEN `date_from` AND `date_to`", date("Y-m-d", $this->_get->toString('time'))));
			}
			
			if ($this->_get->check('c_name') && $this->_get->toString('c_name') != '')
			{
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($this->_get->toString('c_name')));
				$pjReservationModel->where("t1.c_name LIKE '%$q%'");
			}
			
			if ($this->_get->check('c_name') && $this->_get->toString('c_email') != '')
			{
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($this->_get->toString('c_email')));
				$pjReservationModel->where("t1.c_email LIKE '%$q%'");
			}
			
			if ($this->_get->check('amount_from') && $this->_get->toFloat('amount_from') > 0)
			{
				$pjReservationModel->where('t1.amount >=', $this->_get->toFloat('amount_from'));
			}
			
			if ($this->_get->check('amount_to') && $this->_get->toFloat('amount_to') > 0)
			{
				$pjReservationModel->where('t1.amount <=', $this->_get->toFloat('amount_to'));
			}
			
			if ($this->_get->check('last_7days') && $this->_get->toInt('last_7days') === 1)
			{
				$pjReservationModel->where('(DATE(t1.created) BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE())');
			}
			
			if ($this->_get->check('current_week') && $this->_get->toInt('current_week') === 1)
			{
				$monday = strtotime('last monday', strtotime('tomorrow'));
				$sunday = strtotime('next sunday', strtotime('yesterday'));
				
				$pjReservationModel
					->where('t1.date_from <=', date("Y-m-d", $sunday))
					->where('t1.date_to >=', date("Y-m-d", $monday));
			}
			
			if ($this->_get->check('date_from') && $this->_get->toString('date_from') != '' && $this->_get->check('date_to') && $this->_get->toString('date_to') != '')
			{
				$pjReservationModel->where(sprintf("(`date_from` <= '%2\$s' AND `date_to` >= '%1\$s')",
					pjDateTime::formatDate($this->_get->toString('date_from'), $this->option_arr['o_date_format']),
					pjDateTime::formatDate($this->_get->toString('date_to'), $this->option_arr['o_date_format'])
				));
			} else {
				if ($this->_get->check('date_from') && $this->_get->toString('date_from') != '')
				{
					$pjReservationModel->where('t1.date_from >=', pjDateTime::formatDate($this->_get->toString('date_from'), $this->option_arr['o_date_format']));
				}
				if ($this->_get->check('date_to') && $this->_get->toString('date_to') != '')
				{
					$pjReservationModel->where('t1.date_to <=', pjDateTime::formatDate($this->_get->toString('date_to'), $this->option_arr['o_date_format']));
				}
			}
			
			if ($this->isOwner())
			{
				$pjReservationModel->where('t2.user_id', $this->getUserId());
			}
			
			$column = 'date_from';
			$direction = 'DESC';
			if ($this->_get->check('direction') && $this->_get->check('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
			{
				$column = $this->_get->toString('column');
				$direction = strtoupper($this->_get->toString('direction'));
			}

			$total = $pjReservationModel->findCount()->getData();
			$rowCount = $this->_get->check('rowCount') && $this->_get->toInt('rowCount') > 0 ? $this->_get->toInt('rowCount') : 10;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->check('page') && $this->_get->toInt('page') > 0 ? $this->_get->toInt('page') : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjReservationModel->select('t1.id, t1.calendar_id, t1.uuid, t1.date_from, t1.date_to, t1.status, t1.price_based_on, t1.amount, t1.deposit, t1.c_name, t1.c_email,
				IF(t1.price_based_on="nights", DATEDIFF(t1.date_to, t1.date_from), DATEDIFF(t1.date_to, t1.date_from) + 1) AS nights, t3.content AS calendar')
				->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach($data as $k => $v)
			{
				$v['c_name'] = pjSanitize::clean($v['c_name']);
				$v['c_email'] = pjSanitize::clean($v['c_email']);
				$v['date_from'] = date($this->option_arr['o_date_format'], strtotime($v['date_from']));
			    $v['date_to'] = date($this->option_arr['o_date_format'], strtotime($v['date_to']));
			    $v['amount_formated'] = pjCurrency::formatPrice($v['amount']);
				$data[$k] = $v;
			}			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionCopy()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$copy_options_msg = __('copy_options_msg', true);
			$copy_tab = $this->_post->toString('copy_tab');
			if (!$this->_post->check('calendar_id') || $this->_post->toInt('calendar_id') <= 0 || !$this->_post->check('tab_id') || $this->_post->toInt('tab_id') <= 0) {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'copy_tab' => $copy_tab, 'text' => $copy_options_msg[1]));
			}
			if ($this->_post->toInt('tab_id') == 11 && !$this->_post->check('confirmed')) {
				$option_arr = pjOptionModel::factory()->getPairs($this->_post->toInt('calendar_id'));
				if ($option_arr['o_price_plugin'] != $this->option_arr['o_price_plugin']) {
					$property_price_plugin = __('property_price_plugin', true);
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'copy_tab' => $copy_tab, 'text' => sprintf($copy_options_msg[2], $property_price_plugin[$option_arr['o_price_plugin']])));
				}
			}
			if ($this->_post->toInt('tab_id') == 11){
				$pjPriceModel = pjPriceModel::factory();
				$pjPeriodModel = pjPeriodModel::factory();
				$pjPeriodPriceModel = pjPeriodPriceModel::factory();
				
				$option_arr = pjOptionModel::factory()->getPairs($this->_post->toInt('calendar_id'));
				if ($option_arr['o_price_plugin'] != $this->option_arr['o_price_plugin']) {
					pjOptionModel::factory()->reset()
						->where('foreign_id', $this->getCalendarId())
	                    ->where('`key`', 'o_price_plugin')
	                    ->limit(1)
	                    ->modifyAll(array('value' => 'price|period::'.$option_arr['o_price_plugin']));
				}
				$pjPriceModel->where('foreign_id', $this->getCalendarId())->eraseAll();
				$period_ids = $pjPeriodModel->where('foreign_id', $this->getCalendarId())->findAll()->getDataPair(NULL, 'id');
				if ($period_ids) {
					$pjPeriodModel->reset()->whereIn('id', $period_ids)->eraseAll();
					$pjPeriodPriceModel->whereIn('period_id', $period_ids)->eraseAll();
				}
				$copy_tab = $option_arr['o_price_plugin'] == 'price' ? 'prices' : 'periods';
				if ($option_arr['o_price_plugin'] == 'price') {
					$price_arr = $pjPriceModel->reset()->where('foreign_id', $this->_post->toInt('calendar_id'))->findAll()->getData();
					if ($price_arr) {
						$pjPriceModel->reset()->begin();
						foreach($price_arr as $price)
						{
							$pjPriceModel
							 ->reset()
							 ->set('foreign_id', $this->getCalendarId())
							 ->set('tab_id', $price['tab_id'])
							 ->set('season', $price['season'])
							 ->set('date_from', $price['date_from'])
							 ->set('date_to', $price['date_to'])
							 ->set('adults', $price['adults'])
							 ->set('children', $price['children'])
							 ->set('mon', $price['mon'])
							 ->set('tue', $price['tue'])
							 ->set('wed', $price['wed'])
							 ->set('thu', $price['thu'])
							 ->set('fri', $price['fri'])
							 ->set('sat', $price['sat'])
							 ->set('sun', $price['sun'])
							 ->insert();
						}
						$pjPriceModel->commit();
					}
				} else {
					$period_arr = $pjPeriodModel->reset()->where('foreign_id', $this->_post->toInt('calendar_id'))->findAll()->getData();
					if ($period_arr) {
						foreach ($period_arr as $period) {
							$p_data = $period;
							unset($p_data['id']);
							$p_data['foreign_id'] = $this->getCalendarId();
							$pid = $pjPeriodModel->reset()->setAttributes($p_data)->insert()->getInsertId();
							if ($pid !== false && (int)$pid > 0) {
								$period_price_arr = $pjPeriodPriceModel->reset()->where('period_id', $period['id'])->findAll()->getData();
								if ($period_price_arr) {
									$pjPeriodPriceModel->reset()->begin();
									foreach($period_price_arr as $price)
									{
										$pjPeriodPriceModel
										 ->reset()
										 ->set('period_id', $pid)
										 ->set('adults', $price['adults'])
										 ->set('children', $price['children'])
										 ->set('price', $price['price'])
										 ->insert();
									}
									$pjPeriodPriceModel->commit();
								}
							}
						}
					}
				}
			} elseif ($this->_post->toInt('tab_id') == 10){
				 $pjLimitModel = pjLimitModel::factory();
				 $pjLimitModel->where('calendar_id', $this->getCalendarId())->eraseAll();
				 $limit_arr = $pjLimitModel->reset()->where('t1.calendar_id', $this->_post->toInt('calendar_id'))->findAll()->getData();	
				 if ($limit_arr) {			
					 $pjLimitModel->reset()->begin();
					 foreach($limit_arr as $limit)
					 {
					 	$pjLimitModel
							 ->reset()
							 ->set('calendar_id', $this->getCalendarId())
							 ->set('date_from', $limit['date_from'])
							 ->set('date_to', $limit['date_to'])
							 ->set('blocked', $limit['blocked'])
							 ->set('min_nights', $limit['min_nights'])
							 ->set('max_nights', $limit['max_nights'])
							 ->set('blocked_days', $limit['blocked_days'])
							 ->insert();
					 }
					 $pjLimitModel->commit();
				 }				
			} elseif ($this->_post->toInt('tab_id') == 7){
				$pjMultiLangModel = pjMultiLangModel::factory();
				$pjPaymentOptionModel = pjPaymentOptionModel::factory();
				
				$pjPaymentOptionModel->where('foreign_id', $this->getCalendarId())->eraseAll();
				$pjMultiLangModel->where('model', 'pjPayment')->where('foreign_id', $this->getCalendarId())->eraseAll();
				
				$pm_arr = $pjPaymentOptionModel->reset()->where('t1.foreign_id', $this->_post->toInt('calendar_id'))->findAll()->getData();
				foreach ($pm_arr as $val) {
					$payment_data = $val;
					unset($payment_data['id']);
                    $payment_data['foreign_id'] = $this->getCalendarId();
                   	$pjPaymentOptionModel->reset()->setAttributes($payment_data)->insert();
				}
				$i18n_arr = $pjMultiLangModel->reset()->getMultiLang($this->_post->toInt('calendar_id'), 'pjPayment');
				if ($i18n_arr) {
                	$pjMultiLangModel->reset()->saveMultiLang($i18n_arr, $this->getCalendarId(), 'pjPayment');	
				}
				
				$pjMollieOptionModel = pjMollieOptionModel::factory();
				$mo_arr = $pjMollieOptionModel->where('t1.foreign_id', $this->_post->toInt('calendar_id'))->findAll()->getData();
				foreach ($mo_arr as $val) {
					$mo_data = $val;
					unset($mo_data['id']);
                    $mo_data['foreign_id'] = $this->getCalendarId();
                   	$pjMollieOptionModel->reset()->setAttributes($mo_data)->insert();
				}
					
				$src = $pjMultiLangModel->reset()
					->where('t1.model', 'pjOption')
					->where('t1.foreign_id', $this->_post->toInt('calendar_id'))
					->whereIn('t1.field', 'o_bank_account')
					->findAll()->getData();
				$pjMultiLangModel->reset()->begin();
				foreach ($src as $item)
				{
					$item['id'] = NULL;
					unset($item['id']);
					$item['foreign_id'] = $this->getCalendarId();
						
					$pjMultiLangModel->prepare(sprintf(
						"INSERT INTO `%s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`)
						VALUES (NULL, :foreign_id, :model, :locale, :field, :content)
						ON DUPLICATE KEY UPDATE `content` = :content", $pjMultiLangModel->getTable())
					)->exec($item);
				}
				$pjMultiLangModel->commit();
			} elseif ($this->_post->toInt('tab_id') == 5){
				$pjNotificationModel = pjNotificationModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$notification_ids = pjNotificationModel::factory()->where('foreign_id', $this->getCalendarId())->findAll()->getDataPair(NULL, 'id');
	        	if ($notification_ids) {
					$pjMultiLangModel->where('model', 'pjNotification')->whereIn('foreign_id', $notification_ids)->eraseAll();
				}
				
				$notification_arr = $pjNotificationModel->reset()->where('foreign_id', $this->_post->toInt('calendar_id'))->findAll()->getData();
				if ($notification_arr) {
					foreach ($notification_arr as $val) {
						$ml_arr = $pjMultiLangModel->reset()->where('foreign_id', $val['id'])->where('model', 'pjNotification')->findAll()->getData();
						if ($ml_arr) {
							$notification = $pjNotificationModel->reset()->where('foreign_id', $this->getCalendarId())
								->where('recipient', $val['recipient'])
								->where('transport', $val['transport'])
								->where('variant', $val['variant'])
								->limit(1)
								->findAll()->getDataIndex(0);
							if ($notification) {
								$pjMultiLangModel->reset()->begin();
								foreach($ml_arr as $v)
								{
									$pjMultiLangModel
										->reset()
										->set('foreign_id', $notification['id'])
										->set('model', $v['model'])
										->set('locale', $v['locale'])
										->set('field', $v['field'])
										->set('content', $v['content'])
										->set('source', $v['source'])
										->insert();
								}
								$pjMultiLangModel->commit();
							}
						}
					}
				}
			} else {
				$pjOptionModel = pjOptionModel::factory();
				
				$src = $pjOptionModel->where('t1.foreign_id', $this->_post->toInt('calendar_id'))->where('t1.tab_id', $this->_post->toInt('tab_id'))->findAll()->getData();
				$src_pair = $pjOptionModel->getDataPair('key', 'value');
				$pjOptionModel->begin();
				foreach ($src as $option)
				{
					$pjOptionModel
						->reset()
						->where('foreign_id', $this->getCalendarId())
						->where('`key`', $option['key'])
						->limit(1)
						->modifyAll(array('value' => $option['value']));
				}
				$pjOptionModel->commit();
				
				$fields = array();
				if ($this->_post->toInt('tab_id') === 6) {
					$fields = array('terms_url', 'terms_body');
				} elseif ($this->_post->toInt('tab_id') === 8) {
				    $fields = array('y_company', 'y_name', 'y_street_address', 'y_city', 'y_state');
				    $calendar_arr = pjCalendarModel::factory()->find($this->_post->toInt('calendar_id'))->getData();
				    if(!empty($calendar_arr['y_logo']) && is_file(PJ_INSTALL_PATH . $calendar_arr['y_logo']))
				    {
				        $pjImage = new pjImage();
				        $hash = md5(uniqid(rand(), true));
				        $thumb = 'app/web/calendars/' . $this->getCalendarId() . '_' . $hash . '_thumb.png';
				        $pjImage->loadImage(PJ_INSTALL_PATH . $calendar_arr['y_logo'])->saveImage($thumb);
				        $calendar_arr['y_logo'] = $thumb;
				    }
				    pjCalendarModel::factory()->set('id', $this->getCalendarId())->modify($calendar_arr);
				}elseif ($this->_post->toInt('tab_id') === 2) {
					set_time_limit(300);
					pjUtil::pjActionGenerateImages($this->getCalendarId(), $src_pair);
				}

				if (!empty($fields))
				{
					$pjMultiLangModel = pjMultiLangModel::factory();
					
					$src = $pjMultiLangModel
						->where('t1.model', 'pjCalendar')
						->where('t1.foreign_id', $this->_post->toInt('calendar_id'))
						->whereIn('t1.field', $fields)
						->findAll()->getData();

					$pjMultiLangModel->begin();
					foreach ($src as $item)
					{
						$item['id'] = NULL;
						unset($item['id']);
						$item['foreign_id'] = $this->getCalendarId();
							
						$pjMultiLangModel->prepare(sprintf(
							"INSERT INTO `%s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`)
							VALUES (NULL, :foreign_id, :model, :locale, :field, :content)
							ON DUPLICATE KEY UPDATE `content` = :content", $pjMultiLangModel->getTable())
						)->exec($item);
					}
					$pjMultiLangModel->commit();
				}
			}
		}
		pjAppController::jsonResponse(array('status' => 'OK', 'copy_tab' => $copy_tab, 'calendar_id' => $this->getCalendarId()));
		exit;
	}
}
?>