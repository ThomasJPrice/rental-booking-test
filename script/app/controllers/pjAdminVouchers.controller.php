<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminVouchers extends pjAdmin
{
	public function pjActionCheckCode()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (!$this->_get->check('code'))
	        {
	            echo 'false';
	            exit;
	        }
	        $code = $this->_get->toString('code');
	        if(empty($code))
	        {
	            echo 'false';
	            exit;
	        }
	        $pjVoucherModel = pjVoucherModel::factory()->where('t1.code', $code);
	    	if ($this->_get->check('id') && $this->_get->toInt('id') > 0)
	        {
	        	$pjVoucherModel->where('t1.id !=', $this->_get->toInt('id'));
	        }
	       echo $pjVoucherModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->setAjax(true);
		if ($this->isXHR()) {
			if ($this->_post->check('voucher_create'))
			{
				$data = array();			
				$data['code'] = $this->_post->toString('code');
				$data['used_count'] = $this->_post->toInt('used_count');
				$data['discount'] = $this->_post->toString('discount');
				$data['type'] = $this->_post->toString('type');
				$data['condition'] = $this->_post->toString('condition');
				$data['apply_on'] = $this->_post->toString('apply_on');
				$data['date_from'] = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
				$data['date_to'] = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
				$id = pjVoucherModel::factory($data)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$calendar_id_arr = $this->_post->toArray('calendar_id');
					if(count($calendar_id_arr) > 0)
	            	{
	            		$pjCalendarVoucherModel = pjCalendarVoucherModel::factory();
		                foreach ($calendar_id_arr as $calendar_id)
	    	            {
	    	                $pjCalendarVoucherModel->addBatchRow(array($id, $calendar_id));
	    	            }
	    	            $pjCalendarVoucherModel->setBatchFields(array('voucher_id', 'calendar_id'))->insertBatch();
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
			}
		}
	}
	
	public function pjActionDeleteVoucher()
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
		if (pjVoucherModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
		{
			pjCalendarVoucherModel::factory()->where('voucher_id', $this->_get->toInt('id'))->eraseAll();
			$response = array('status' => 'OK');
		} else {
			$response = array('status' => 'ERR');
		}
		
		self::jsonResponse($response);
	}
	
	public function pjActionDeleteVoucherBulk()
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
		
		if (pjVoucherModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
			pjCalendarVoucherModel::factory()->whereIn('voucher_id', $record)->eraseAll();
			self::jsonResponse(array('status' => 'OK'));
		}
		
		self::jsonResponse(array('status' => 'ERR'));
	}
	
	public function pjActionExportVoucher()
	{
		$this->checkLogin();
		
		if ($record = $this->_post->toArray('record'))
		{
			$arr = pjVoucherModel::factory()->whereIn('id', $record)->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Vouchers-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetVoucher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjVoucherModel = pjVoucherModel::factory();
			
			if ($q = $this->_get->toString('q'))
			{
				$q = str_replace(array('_', '%'), array('\_', '\%'), $pjVoucherModel->escapeStr($q));
				$pjVoucherModel->where('t1.code LIKE', "%$q%");
			}
	
			if ($this->_get->check('calendar_id') && $this->_get->toInt('calendar_id') > 0)
			{
				$pjVoucherModel->where("(t1.id IN(SELECT `TCV`.voucher_id FROM `".pjCalendarVoucherModel::factory()->getTable()."` AS `TCV` WHERE `TCV`.calendar_id='".$this->_get->toInt('calendar_id')."') )");
			}
	
			$column = 'code';
			$direction = 'ASC';
			if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
			{
				$column = $this->_get->toString('column');
				$direction = strtoupper($this->_get->toString('direction'));
			}
	
			$total = $pjVoucherModel->findCount()->getData();
			$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjVoucherModel->select('t1.*')->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			$apply_on = pjUtil::sortArrayByArray(__('apply_on'), array('room','extra','both'));
			foreach($data as $k => $v)
			{
				$v['code'] = pjSanitize::clean($v['code']);
				if($v['type'] == 'percent')
				{
					$v['discount'] = $v['discount'] . '%';
				}else{
					$v['discount'] = pjCurrency::formatPrice($v['discount']);
				}
				$v['date_from'] = date($this->option_arr['o_date_format'], strtotime($v['date_from']));
				$v['date_to'] = date($this->option_arr['o_date_format'], strtotime($v['date_to']));
				$v['apply_on'] = @$apply_on[$v['apply_on']];
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
	    
	    $this->set('has_update', pjAuth::factory('pjAdminVouchers', 'pjActionUpdate')->hasAccess());
	    $this->set('has_create', pjAuth::factory('pjAdminVouchers', 'pjActionCreate')->hasAccess());
	    $this->set('has_export', pjAuth::factory('pjAdminVouchers', 'pjActionExportVoucher')->hasAccess());
	    $this->set('has_delete', pjAuth::factory('pjAdminVouchers', 'pjActionDeleteVoucher')->hasAccess());
	    $this->set('has_delete_bulk', pjAuth::factory('pjAdminVouchers', 'pjActionDeleteVoucherBulk')->hasAccess());
	    
	    $this->appendCss('awesome-bootstrap-checkbox.css', PJ_THIRD_PARTY_PATH . 'awesome_bootstrap_checkbox/');
		$this->appendCss('datepicker3.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
	    $this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
        $this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminVouchers.js');
	}
	
	public function pjActionSaveVoucher()
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
		
		$pjVoucherModel = pjVoucherModel::factory();
		if (!in_array($params['column'], $pjVoucherModel->getI18n()))
		{
			$pjVoucherModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjVoucher');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
	
	public function pjActionUpdate()
	{
		$this->setAjax(true);
		if ($this->isXHR()) {
			if ($this->_post->check('voucher_update'))
			{
				$data = array();			
				$data['code'] = $this->_post->toString('code');
				$data['used_count'] = $this->_post->toInt('used_count');
				$data['discount'] = $this->_post->toString('discount');
				$data['type'] = $this->_post->toString('type');
				$data['condition'] = $this->_post->toString('condition');
				$data['apply_on'] = $this->_post->toString('apply_on');
				$data['date_from'] = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
				$data['date_to'] = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
				pjVoucherModel::factory()->where('id', $this->_post->toInt('id'))->limit(1)->modifyAll($data);
				
				$pjCalendarVoucherModel = pjCalendarVoucherModel::factory();
				$pjCalendarVoucherModel->where('voucher_id', $this->_post->toInt('id'))->eraseAll();
				$calendar_id_arr = $this->_post->toArray('calendar_id');
				if (count($calendar_id_arr) > 0)
				{
					$pjCalendarVoucherModel->reset();
	                foreach ($calendar_id_arr as $calendar_id)
    	            {
    	                $pjCalendarVoucherModel->addBatchRow(array($this->_post->toInt('id'), $calendar_id));
    	            }
    	            $pjCalendarVoucherModel->setBatchFields(array('voucher_id', 'calendar_id'))->insertBatch();
				}
				pjAppController::jsonResponse(array('status' => 'OK'));	
			} else {
				$arr = pjVoucherModel::factory()->find($this->_get->toInt('id'))->getData();
				$this->set('arr', $arr);
				
				$this->set('calendar_arr', pjCalendarModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('name ASC')
					->findAll()
					->getData());
			
				$this->set('calendar_id_arr', pjCalendarVoucherModel::factory()
					->where("voucher_id", $this->_get->toInt('id'))
					->findAll()
					->getDataPair("calendar_id", "calendar_id"));
			}
		}
	}
}
?>