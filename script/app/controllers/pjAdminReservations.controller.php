<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminReservations extends pjAdmin
{
    public $__option_arr = array();
    
	public function pjActionCheckUnique()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
		    if (!$this->_get->check('uuid') || $this->_get->isEmpty('uuid'))
			{
				echo 'false';
				exit;
			}
			$pjReservationModel = pjReservationModel::factory()->where('t1.uuid', $this->_get->toString('uuid'));
			if ($this->_get->check('id')&& $this->_get->toInt('id') > 0)
			{
			    $pjReservationModel->where('t1.id !=', $this->_get->toInt('id'));
			}
			echo $pjReservationModel->findCount()->getData() == 0 ? 'true' : 'false';
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

		if ($this->isOwner())
		{
			$calendars = $this->get('calendars');
			if (empty($calendars))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR19");
			}
		}
		
		if(self::isPost() && $this->_post->check('reservation_create'))
		{
			$pjReservationModel = pjReservationModel::factory();
			if (0 != $pjReservationModel->where('t1.uuid', $this->_post->toString('uuid'))->findCount()->getData())
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR04");
			}
			$option_arr = pjOptionModel::factory()->getPairs($this->_post->toInt('calendar_id'));
			$data = array();
			$data['date_from'] = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
			$data['date_to'] = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
			$data['price_based_on'] = $option_arr['o_price_based_on'];
			$data['ip'] = $_SERVER['REMOTE_ADDR'];
			$data['locale_id'] = $this->getLocaleId();			
			$insert_id = $pjReservationModel->reset()->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
			if ($insert_id !== false && (int) $insert_id > 0)
			{
				if($this->_post->check('extra') && count($this->_post->toArray('extra')) > 0)
				{
					$ex_arr = $this->_post->toArray('extra');
					$ex_qty_arr = $this->_post->toArray('qty');
					$pjReservationExtraModel = pjReservationExtraModel::factory();
					$pjReservationExtraModel->begin();
					foreach($ex_arr as $extra_id => $on)
					{
						$pjReservationExtraModel
						->reset()
						->set('reservation_id', $insert_id)
						->set('extra_id', $extra_id)
						->set('qty', $ex_qty_arr[$extra_id])
						->insert();
					}
					$pjReservationExtraModel->commit();
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR03");
			} else {
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR04");
			}
		}
		
		$this->set('country_arr', pjBaseCountryModel::factory()
			->select('t1.*, t2.content AS name')
			->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->where('t1.status', 'T')
			->orderBy('`name` ASC')->findAll()->getData()
		);

		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendCss('datepicker3.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('pjAdminReservations.js');
	}
	
	public function pjActionDeleteReservation()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isGet() && !$this->_get->check('id') && $this->_get->toInt('id') < 0)
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		
		if (pjReservationModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
		{
			pjReservationExtraModel::factory()->where('reservation_id', $this->_get->toInt('id'))->eraseAll();
			pjMultiLangModel::factory()->where('model', 'pjReservation')->where('foreign_id', $this->_get->toInt('id'))->eraseAll();
			$response = array('status' => 'OK');
		} else {
			$response = array('status' => 'ERR');
		}
		
		self::jsonResponse($response);
	}
	
	public function pjActionDeleteReservationBulk()
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

		if (!$this->_post->has('record') || !($record = $this->_post->toArray('record')))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid data.'));
		}
		
		if (pjReservationModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
			pjMultiLangModel::factory()->where('model', 'pjReservation')->whereIn('foreign_id', $record)->eraseAll();
			pjReservationExtraModel::factory()->whereIn('reservation_id', $record)->eraseAll();
			self::jsonResponse(array('status' => 'OK'));
		}
		
		self::jsonResponse(array('status' => 'ERR'));
	}
	
	public function pjActionExportReservation()
	{
		if ($record = $this->_post->toArray('record'))
		{
			$arr = pjReservationModel::factory()->whereIn('id', $record)->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Reservations-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionCalcPrice()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$response = array();
			$date_from = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
			$date_to = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
			if ($date_from === FALSE || $date_to === FALSE)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Given date(s) are empty.'));
			}
			if (!$this->_post->check('calendar_id') || $this->_post->toInt('calendar_id') <= 0)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Calendar is empty or invalid.'));
			}
			if (strtotime($date_to) < strtotime($date_from)) {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Date to must be greater than date from.'));
			}
			$option_arr = pjOptionModel::factory()->getPairs($this->_post->toInt('calendar_id'));
			$response = $this->pjActionCalcPrices(
					$this->_post->toInt('calendar_id'), 
					strtotime($date_from), 
					strtotime($date_to), 
					$this->_post->check('c_adults') ? $this->_post->toInt('c_adults') : 0, 
					$this->_post->check('c_children') ? $this->_post->toInt('c_children') : 0, 
					$this->_post->check('extra') ? $this->_post->toArray('extra') : array(), 
					$this->_post->check('qty') ? $this->_post->toArray('qty') : array(), 
					$this->_post->check('promo_code') ? $this->_post->toString('promo_code') : '',
					$option_arr,
					$this->getLocaleId());
			$response['status'] = 'OK';
			pjAppController::jsonResponse($response);
		}
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
				$q = $this->_get->toString('c_name');
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjReservationModel->where("t1.c_name LIKE '%$q%'");
			}
			
			if ($this->_get->check('c_email') && $this->_get->toString('c_email') != '')
			{
				$q = $this->_get->toString('c_email');
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
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
			
			if ($this->_get->check('last_7days') && $this->_get->toInt('last_7days') === 1)
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
			
			$column = 'id';
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
	
	public function pjActionIndex()
	{
		$this->checkLogin();
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
		
		$pjCalendarModel = pjCalendarModel::factory();
		if ($this->isOwner())
		{
			$pjCalendarModel->where('t1.user_id', $this->getUserId());
		}
		$this->set('calendar_arr', $pjCalendarModel
			->select('t1.id, t2.content AS name')
			->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->orderBy('`name` ASC')
			->findAll()
			->getData()
		);
		
		$this->appendCss('datepicker3.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
	    $this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
	    $this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
	    $this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
	    $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
	    $this->appendJs('pjAdminReservations.js');
	}
	
	public function pjActionSaveReservation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjReservationModel = pjReservationModel::factory();
			if (!in_array($this->_post->toString('column'), $pjReservationModel->getI18n()))
			{
				$reservation = $pjReservationModel
					->select("t1.*, t2.content AS country, t3.user_id")
					->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
					->join('pjCalendar', 't3.id=t1.calendar_id', 'left outer')
					->find($this->_get->toInt('id'))->getData();
				if ((in_array($this->_post->toString('column'), array('status')) && $this->_post->toString('value') != 'Cancelled' && $reservation['status'] == 'Cancelled' ) )
				{
					$date_from = $reservation['date_from'];
					$date_to = $reservation['date_to'];					
					$response = $this->pjActionCheckDt($date_from, $date_to, $reservation['calendar_id'], $reservation['id'], true);
					if ($response['status'] != 'OK')
					{
						pjAppController::jsonResponse($response);
					}
				}
				$pjReservationModel->set('id', $this->_get->toInt('id'))->modify(array($this->_post->toString('column') => $this->_post->toString('value')));
			} else {
			    pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($this->_post->toString('column') => $this->_post->toString('value'))), $this->_get->toInt('id'), 'pjReservation');
			}
		}
		exit;
	}
		
	public function pjActionUpdate()
	{
		$this->checkLogin();
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
		
		$pjReservationModel = pjReservationModel::factory();

		if ($this->_get->check('id') && $this->_get->toInt('id') > 0)
		{
			$pjReservationModel->where('t1.id', $this->_get->toInt('id'));
		} elseif ($this->_get->check('uuid') && $this->_get->isEmpty('uuid')) {
			$pjReservationModel->where('t1.uuid', $this->_get->toString('uuid'));
		} else {
			$pjReservationModel->where('t1.id', '0');
		}
		
		$reservation = $pjReservationModel
			->select("t1.*, t2.content AS country, t3.user_id, t4.content AS calendar_name")
			->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
			->join('pjCalendar', 't3.id=t1.calendar_id', 'left outer')
			->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.field='name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
			->limit(1)
			->findAll()->getData();
		
		if (empty($reservation) || count($reservation) == 0)
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR08");
		}
		$reservation = $reservation[0];
		
		$calendar = pjCalendarModel::factory()->find($reservation['calendar_id'])->getData();
		
		if (empty($calendar) || count($calendar) == 0)
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR09");
		}
		
		if ($this->isOwner())
		{
			if ($calendar['user_id'] != $this->getUserId())
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR10");
			}
		}
		
		if (self::isPost() && $this->_post->check('reservation_update'))
		{
			if (0 != $pjReservationModel->reset()->where('t1.uuid', $this->_post->toString('uuid'))->where('t1.id !=', $this->_post->toInt('id'))->findCount()->getData())
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR02");
			}
			
			$data = array();
			$data['date_from'] = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
			$data['date_to'] = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
			$data['modified'] = date('Y-m-d H:i:s');			
			$option_arr = $this->option_arr;
			if ($this->_post->toInt('calendar_id') != $this->getCalendarId())
			{
				$option_arr = pjOptionModel::factory()->getPairs($this->_post->toInt('calendar_id'));
			}
			if ($this->_post->toString('status') != 'Cancelled') {
				$check = $this->pjActionCheckDt($data['date_from'], $data['date_to'], $this->_post->toInt('calendar_id'), $this->_post->toInt('id'), true);
				if ($check['status'] == 'ERR')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionUpdate&id=".$this->_post->toInt('id')."&err=AR11");
				}
			}
			$pjReservationModel->reset()->where('id', $this->_post->toInt('id'))->limit(1)->modifyAll(array_merge($this->_post->raw(), $data));
			$pjReservationExtraModel = pjReservationExtraModel::factory();
			$pjReservationExtraModel->where('reservation_id', $this->_post->toInt('id'))->eraseAll();
			if($this->_post->check('extra') && count($this->_post->toArray('extra')) > 0)
			{
				$ex_arr = $this->_post->toArray('extra');
				$ex_qty_arr = $this->_post->toArray('qty');
				$pjReservationExtraModel->reset()->begin();
				foreach($ex_arr as $extra_id => $on)
				{
					$pjReservationExtraModel
					->reset()
					->set('reservation_id', $this->_post->toInt('id'))
					->set('extra_id', $extra_id)
					->set('qty', $ex_qty_arr[$extra_id])
					->insert();
				}
				$pjReservationExtraModel->commit();
			}
			
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR01");
		} else {
			$extra_arr = pjExtraModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.status', 'T')
				->where("(t1.id IN (SELECT TCE.extra_id FROM `".pjCalendarExtraModel::factory()->getTable()."` AS `TCE` WHERE `TCE`.calendar_id='".$reservation['calendar_id']."') )")
				->orderBy('`name` ASC')
				->findAll()->getData();
			
			$extra_id_arr = pjReservationExtraModel::factory()->where('t1.reservation_id', $reservation['id'])->findAll()->getDataPair('extra_id', 'qty');
			
			$this->set('arr', $reservation);
			$this->set('extra_arr', $extra_arr);
			$this->set('extra_id_arr', $extra_id_arr);
		}
		
		$this->set('country_arr', pjBaseCountryModel::factory()
			->select('t1.*, t2.content AS name')
			->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->where(sprintf("t1.status != IF(t1.id != '%u', 'F', 'WHATEVER')", $reservation['c_country']))
			->orderBy('`name` ASC')->findAll()->getData()
		);
		
		if(pjObject::getPlugin('pjPayments') !== NULL)
        {
            $this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions($reservation['calendar_id']));
            $this->set('payment_titles', pjPayments::getPaymentTitles($reservation['calendar_id'], $this->getLocaleId()));
        }else{
            $this->set('payment_titles', __('payment_methods', true));
        }
        
		$OptionModel = pjOptionModel::factory();
		$this->__option_arr = $OptionModel->getPairs($reservation['calendar_id']);
		$this->set('__option_arr', $this->__option_arr);
		
		$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendCss('datepicker3.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('pjAdminReservations.js');
	}

	public function pjActionCheckDates()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$date_from = pjDateTime::formatDate($this->_get->toString('date_from'), $this->option_arr['o_date_format']);
			$date_to = pjDateTime::formatDate($this->_get->toString('date_to'), $this->option_arr['o_date_format']);
			$resp = $this->pjActionCheckDt($date_from, $date_to, $this->_get->toInt('calendar_id'), $this->_get->toInt('id'), true);
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
	
	public function pjActionGetAdults(){
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$this->set('option_arr', pjOptionModel::factory()->getPairs($this->_get->toInt('id')));
		}
	}
	
	public function pjActionGetChildren(){
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$this->set('option_arr', pjOptionModel::factory()->getPairs($this->_get->toInt('id')));
		}
	}
	
	public function pjActionGetExtras()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if($this->_get->check('id') && $this->_get->toInt('id') > 0)
			{
				$extra_arr = pjExtraModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('t1.status', 'T')
					->where("(t1.id IN (SELECT TCE.extra_id FROM `".pjCalendarExtraModel::factory()->getTable()."` AS `TCE` WHERE `TCE`.calendar_id='".$this->_get->toInt('id')."') )")
					->orderBy('`name` ASC')
					->findAll()->getData();
				
				if(!empty($extra_arr))
				{
					$this->set('extra_arr', $extra_arr);
				}else{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 109));
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 108));
			}
		}
	}
	
	public function pjActionGetPMs()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        $calendar_id = $this->_get->toInt('id');
	        $this->set('_option_arr', pjOptionModel::factory()->getPairs($calendar_id));	        
	        if(pjObject::getPlugin('pjPayments') !== NULL)
	        {
	            $this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions($calendar_id));
	            $this->set('payment_titles', pjPayments::getPaymentTitles($calendar_id, $this->getLocaleId()));
	        }else{
	            $this->set('payment_titles', __('payment_methods', true));
	        }
	    }
	}
	
	public function pjActionSchedule()
	{
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
	    	    
	    $this->appendJs('pjAdminReservations.js');
	}
	
	public function pjActionGetSchedule()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	    	$pjOptionModel = pjOptionModel::factory();
	    	$pjReservationModel = pjReservationModel::factory();
	        $date = date('Y-m-01');
	        if($this->_get->check('date') && !$this->_get->isEmpty('date'))
	        {
	            $date = $this->_get->toString('date');
	        }
	        $ts = strtotime($date);
	        $first_day_of_month = date('Y-m-01', $ts);
	        $last_day_of_month  = date('Y-m-t', $ts);
	        
	        $pjCalendarModel = pjCalendarModel::factory()
		        ->select('t1.id, t1.uuid, t2.content AS name, t3.value AS o_bookings_per_day')
		        ->join('pjMultiLang', sprintf("t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale=%u", $this->getLocaleId()), 'left outer')
		        ->join('pjOption', "t3.foreign_id=t1.id AND t3.key='o_bookings_per_day'", 'left outer')
		        ->orderBy('name ASC, t1.uuid ASC');
	        if ($this->isOwner())
	        {
	            $pjCalendarModel->where('t1.user_id', $this->getUserId());
	        }
	        $total = $pjCalendarModel->findCount()->getData();
	        $rowCount = $this->_get->toInt('rowCount') ?: 10;
	        $pages = ceil($total / $rowCount);
	        $page = $this->_get->toInt('page') ?: 1;
	        $offset = ((int) $page - 1) * $rowCount;
	        if ($page > $pages)
	        {
	            $page = $pages;
	        }
	        $listing_arr = $pjCalendarModel->limit($rowCount, $offset)->findAll()->getData();
	        foreach ($listing_arr as $k => $v) {
	        	$listing_arr[$k]['date_arr'] = $pjReservationModel->getInfo(
					$v['id'],
					$first_day_of_month,
					$last_day_of_month,
					$pjOptionModel->reset()->getPairs($v['id']),
					NULL,
					1
				);
	        }
	        $this->set('listing_arr', $listing_arr);
	        $this->set('paginator', compact('total', 'pages', 'page', 'rowCount'));
	        
	        $this->set('date', $date);
	        $this->set('first_day_of_month', $first_day_of_month);
	        $this->set('last_day_of_month', $last_day_of_month);
	    }
	}
	
	public function pjActionGetBookingFields()
    {
        $this->setAjax(true);
        
        if ($this->isXHR())
        {
        	$option_arr = pjOptionModel::factory()->getPairs($this->_get->toInt('calendar_id'));
            self::jsonResponse($option_arr);
        }
    }
    
	public function pjActionEmailConfirmation()
	{
	    $this->checkLogin();
	    
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        if (self::isPost())
	        {
	            if($this->_post->toInt('send_email') && $this->_post->toString('to') && $this->_post->toString('subject') && $this->_post->toString('message') && $this->_post->toInt('id'))
	            {
	                $Email = self::getMailer($this->option_arr);
	                $message = pjUtil::textToHtml($this->_post->toString('message'));
	                $r = $Email
	                ->setTo($this->_post->toString('to'))
	                ->setSubject($this->_post->toString('subject'))
	                ->send($message);
	                if (isset($r) && $r)
	                {
	                    pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	                }
	                pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
	            }
	        }
	        if (self::isGet())
	        {
	            if($reservation_id = $this->_get->toInt('reservation_id'))
	            {
	                $pjNotificationModel = pjNotificationModel::factory();
	                $pjReservationModel = pjReservationModel::factory();
	                
	                $reservation = $pjReservationModel->reset()
						->select("t1.*, t2.content AS country")
						->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
						->find($reservation_id)->getData();
					$option_arr = pjOptionModel::factory()->getPairs($reservation['calendar_id']);	
	                $notification = $pjNotificationModel->reset()->where('foreign_id', $reservation['calendar_id'])->where('recipient', 'client')->where('transport', 'email')->where('variant', 'confirmation')->findAll()->getDataIndex(0);
	                if((int) $notification['id'] > 0 && $notification['is_active'] == 1)
	                {
	                	$option_arr = array_merge($this->option_arr, $option_arr);
	                    $tokens = pjAppController::getTokens($reservation, $option_arr, $reservation['locale_id']);
	                    $resp = pjAppController::getSubjectMessage($notification, $reservation['locale_id']);
	                    
	                    $lang_message = $resp['lang_message'];
	                    $lang_subject = $resp['lang_subject'];
	                    if (isset($lang_subject[0]['content']) && !empty($lang_subject[0]['content']) && isset($lang_message[0]['content']) && !empty($lang_message[0]['content'])) {
    	                    $subject_client = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
    	                    $message_client = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
    	                    
    	                    $this->set('arr', array(
    	                        'id' => $reservation_id,
    	                        'to' => $reservation['c_email'],
    	                        'message' => $message_client,
    	                        'subject' => $subject_client
    	                    ));
	                    }
	                }
	            }
	        }
	    }
	}
	
	public function pjActionEmailCancellation()
	{
		$this->checkLogin();
	    
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        if (self::isPost())
	        {
	            if($this->_post->toInt('send_email') && $this->_post->toString('to') && $this->_post->toString('subject') && $this->_post->toString('message') && $this->_post->toInt('id'))
	            {
	                $Email = self::getMailer($this->option_arr);
	                $message = pjUtil::textToHtml($this->_post->toString('message'));
	                $r = $Email
	                ->setTo($this->_post->toString('to'))
	                ->setSubject($this->_post->toString('subject'))
	                ->send($message);
	                if (isset($r) && $r)
	                {
	                    pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	                }
	                pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
	            }
	        }
	        if (self::isGet())
	        {
	            if($reservation_id = $this->_get->toInt('reservation_id'))
	            {
	                $pjNotificationModel = pjNotificationModel::factory();
	                $pjReservationModel = pjReservationModel::factory();
	                
	                $reservation = $pjReservationModel->reset()
						->select("t1.*, t2.content AS country")
						->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
						->find($reservation_id)->getData();
					$option_arr = pjOptionModel::factory()->getPairs($reservation['calendar_id']);	
	                $notification = $pjNotificationModel->reset()->where('foreign_id', $reservation['calendar_id'])->where('recipient', 'client')->where('transport', 'email')->where('variant', 'cancel')->findAll()->getDataIndex(0);
	                if((int) $notification['id'] > 0 && $notification['is_active'] == 1)
	                {
	                	$option_arr = array_merge($this->option_arr, $option_arr);
	                    $tokens = pjAppController::getTokens($reservation, $option_arr, $reservation['locale_id']);
	                    $resp = pjAppController::getSubjectMessage($notification, $reservation['locale_id']);
	                    
	                    $lang_message = $resp['lang_message'];
	                    $lang_subject = $resp['lang_subject'];
	                    if (isset($lang_subject[0]['content']) && !empty($lang_subject[0]['content']) && isset($lang_message[0]['content']) && !empty($lang_message[0]['content'])) {
    	                    $subject_client = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
    	                    $message_client = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
    	                    
    	                    $this->set('arr', array(
    	                        'id' => $reservation_id,
    	                        'to' => $reservation['c_email'],
    	                        'message' => $message_client,
    	                        'subject' => $subject_client
    	                    ));
	                    }
	                }
	            }
	        }
	    }
	}
}
?>