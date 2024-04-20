<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminDiscounts extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->setAjax(true);
		if ($this->isXHR()) {
			if ($this->_post->check('discount_create'))
			{
				$data = array();			
				$data['discount'] = $this->_post->toString('discount');
				$data['type'] = $this->_post->toString('type');
				$data['condition'] = $this->_post->toString('condition');
				$data['date_from'] = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
				$data['date_to'] = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
				$data['options'] = $this->_post->toString('options');				
				$data['early_days'] = ':NULL';
				$data['min_persons'] = ':NULL';
				$data['max_persons'] = ':NULL';
				$data['min_duration'] = ':NULL';
				$data['max_duration'] = ':NULL';				
				if($this->_post->toString('options') == 'early')
				{
					$data['early_days'] = $this->_post->toInt('early_days');
				}
				if($this->_post->toString('options') == 'persons')
				{
					$data['min_persons'] = $this->_post->toInt('min_persons');
					$data['max_persons'] = $this->_post->toInt('max_persons');
				}
				if($this->_post->toString('options') == 'duration')
				{
					$data['min_duration'] = $this->_post->toInt('min_duration');
					$data['max_duration'] = $this->_post->toInt('max_duration');
				}
				$id = pjDiscountModel::factory($data)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$i18n_arr = $this->_post->toI18n('i18n');
					if (!empty($i18n_arr))
					{
						pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $id, 'pjDiscount');
					}
					
					$calendar_id_arr = $this->_post->toArray('calendar_id');
					if(count($calendar_id_arr) > 0)
	            	{
	            		$pjCalendarDiscountModel = pjCalendarDiscountModel::factory();
		                foreach ($calendar_id_arr as $calendar_id)
	    	            {
	    	                $pjCalendarDiscountModel->addBatchRow(array($id, $calendar_id));
	    	            }
	    	            $pjCalendarDiscountModel->setBatchFields(array('discount_id', 'calendar_id'))->insertBatch();
					}
				}
				pjAppController::jsonResponse(array('status' => 'OK'));
			} else {
				$this->set('calendar_arr', pjCalendarModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('name ASC')
					->findAll()
					->getData());
				$this->setLocalesData();
			}
		}
	}
	
	public function pjActionDeleteDiscount()
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
		if (pjDiscountModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
		{
			pjMultiLangModel::factory()->where('model', 'pjDiscount')->where('foreign_id', $this->_get->toInt('id'))->eraseAll();
			pjCalendarDiscountModel::factory()->where('discount_id', $this->_get->toInt('id'))->eraseAll();
			$response = array('status' => 'OK');
		} else {
			$response = array('status' => 'ERR');
		}
		
		self::jsonResponse($response);
	}
	
	public function pjActionDeleteDiscountBulk()
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
		
		if (pjDiscountModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
			pjMultiLangModel::factory()->where('model', 'pjDiscount')->whereIn('foreign_id', $record)->eraseAll();
			pjCalendarDiscountModel::factory()->whereIn('discount_id', $record)->eraseAll();
			self::jsonResponse(array('status' => 'OK'));
		}
		
		self::jsonResponse(array('status' => 'ERR'));
	}
	
	public function pjActionExportDiscount()
	{
		$this->checkLogin();
		
		if ($record = $this->_post->toArray('record'))
		{
			$arr = pjDiscountModel::factory()->whereIn('id', $record)->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Discounts-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetDiscount()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjDiscountModel = pjDiscountModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjDiscount' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left');
			
			if ($q = $this->_get->toString('q'))
			{
				$q = str_replace(array('_', '%'), array('\_', '\%'), $pjDiscountModel->escapeStr($q));
				$pjDiscountModel->where('t2.content LIKE', "%$q%");
			}
	
			if ($this->_get->check('calendar_id') && $this->_get->toInt('calendar_id') > 0)
			{
				$pjDiscountModel->where("(t1.id IN(SELECT `TCD`.discount_id FROM `".pjCalendarDiscountModel::factory()->getTable()."` AS `TCD` WHERE `TCD`.calendar_id='".$this->_get->toInt('calendar_id')."') )");
			}
			
			$column = 'name';
			$direction = 'ASC';
			if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
			{
				$column = $this->_get->toString('column');
				$direction = strtoupper($this->_get->toString('direction'));
			}
	
			$total = $pjDiscountModel->findCount()->getData();
			$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			$discount_options = pjUtil::sortArrayByArray(__('discount_options'), array('early','persons','family', 'duration'));
			$data = $pjDiscountModel->select('t1.*, t2.content as name')->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach($data as $k => $v)
			{
				$v['name'] = pjSanitize::clean($v['name']);
				if($v['type'] == 'percent')
				{
					$v['discount'] = $v['discount'] . '%';
				}else{
					$v['discount'] = pjCurrency::formatPrice($v['discount']);
				}
				$v['date_from'] = date($this->option_arr['o_date_format'], strtotime($v['date_from']));
				$v['date_to'] = date($this->option_arr['o_date_format'], strtotime($v['date_to']));
				$v['options_formated'] = @$discount_options[$v['options']];
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
	    $this->setLocalesData();
	    $this->set('has_update', pjAuth::factory('pjAdminDiscounts', 'pjActionUpdate')->hasAccess());
	    $this->set('has_create', pjAuth::factory('pjAdminDiscounts', 'pjActionCreate')->hasAccess());
	    $this->set('has_export', pjAuth::factory('pjAdminDiscounts', 'pjActionExportDiscount')->hasAccess());
	    $this->set('has_delete', pjAuth::factory('pjAdminDiscounts', 'pjActionDeleteDiscount')->hasAccess());
	    $this->set('has_delete_bulk', pjAuth::factory('pjAdminDiscounts', 'pjActionDeleteDiscountBulk')->hasAccess());
	    
	    $this->appendCss('awesome-bootstrap-checkbox.css', PJ_THIRD_PARTY_PATH . 'awesome_bootstrap_checkbox/');
		$this->appendCss('datepicker3.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
	    $this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
        $this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminDiscounts.js');
	}
	
	public function pjActionSaveDiscount()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isPost())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'HTTP method not allowed.'));
		}
		
		$params = array(
				'id' => $this->_get->toInt('id'),
				'column' => $this->_post->toString('column'),
				'value' => $this->_post->toString('value'),
		);
		if (!(isset($params['id'], $params['column'], $params['value'])
				&& pjValidation::pjActionNumeric($params['id'])
				&& pjValidation::pjActionNotEmpty($params['column'])))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		
		$pjDiscountModel = pjDiscountModel::factory();
		if (!in_array($params['column'], $pjDiscountModel->getI18n()))
		{
			$pjDiscountModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjDiscount');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
	
	public function pjActionUpdate()
	{
		$this->setAjax(true);
		if ($this->isXHR()) {
			if ($this->_post->check('discount_update'))
			{
				$data = array();			
				$data['discount'] = $this->_post->toString('discount');
				$data['type'] = $this->_post->toString('type');
				$data['condition'] = $this->_post->toString('condition');
				$data['date_from'] = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
				$data['date_to'] = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
				$data['options'] = $this->_post->toString('options');				
				$data['early_days'] = ':NULL';
				$data['min_persons'] = ':NULL';
				$data['max_persons'] = ':NULL';
				$data['min_duration'] = ':NULL';
				$data['max_duration'] = ':NULL';				
				if($this->_post->toString('options') == 'early')
				{
					$data['early_days'] = $this->_post->toInt('early_days');
				}
				if($this->_post->toString('options') == 'persons')
				{
					$data['min_persons'] = $this->_post->toInt('min_persons');
					$data['max_persons'] = $this->_post->toInt('max_persons');
				}
				if($this->_post->toString('options') == 'duration')
				{
					$data['min_duration'] = $this->_post->toInt('min_duration');
					$data['max_duration'] = $this->_post->toInt('max_duration');
				}
				pjDiscountModel::factory()->set('id', $this->_post->toInt('id'))->modify($data);
				$i18n_arr = $this->_post->toI18n('i18n');
				if (!empty($i18n_arr))
				{
					pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $this->_post->toInt('id'), 'pjDiscount');
				}
				
				$pjCalendarDiscountModel = pjCalendarDiscountModel::factory();
				$pjCalendarDiscountModel->where('discount_id', $this->_post->toInt('id'))->eraseAll();
				
				$calendar_id_arr = $this->_post->toArray('calendar_id');
				if(count($calendar_id_arr) > 0)
            	{
            		$pjCalendarDiscountModel->reset();
	                foreach ($calendar_id_arr as $calendar_id)
    	            {
    	                $pjCalendarDiscountModel->addBatchRow(array($this->_post->toInt('id'), $calendar_id));
    	            }
    	            $pjCalendarDiscountModel->setBatchFields(array('discount_id', 'calendar_id'))->insertBatch();
				}
				pjAppController::jsonResponse(array('status' => 'OK'));				
			} else {
				$arr = pjDiscountModel::factory()->find($this->_get->toInt('id'))->getData();
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjDiscount');
				$this->set('arr', $arr);
				
				$this->setLocalesData();
				
				$this->set('calendar_arr', pjCalendarModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('name ASC')
					->findAll()
					->getData());
			
				$this->set('calendar_id_arr', pjCalendarDiscountModel::factory()
					->where("discount_id", $this->_get->toInt('id'))
					->findAll()
					->getDataPair("calendar_id", "calendar_id"));
			}
		}
	}
}
?>