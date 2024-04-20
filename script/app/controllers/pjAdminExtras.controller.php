<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminExtras extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->setAjax(true);
		if ($this->isXHR()) {
			if ($this->_post->check('extra_create'))
			{
				$pjExtraModel = pjExtraModel::factory();
				
				$data = array();
				$data['status'] = $this->_post->check('status') ? 'T' : 'F';
				$data['required'] = $this->_post->check('required') ? 'T' : 'F';
				$data['multi'] = $this->_post->check('multi') ? 'T' : 'F';
				$data['max_count'] = $this->_post->toInt('apply_max_count') == 1 ? $this->_post->toInt('max_count') : ':NULL';				
				$id = $pjExtraModel->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$i18n_arr = $this->_post->toI18n('i18n');
					if (!empty($i18n_arr))
					{
						pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $id, 'pjExtra');
					}
					$calendar_id_arr = $this->_post->toArray('calendar_id');
					if(count($calendar_id_arr) > 0)
	            	{
	            		$pjCalendarExtraModel = pjCalendarExtraModel::factory();
		                foreach ($calendar_id_arr as $calendar_id)
	    	            {
	    	                $pjCalendarExtraModel->addBatchRow(array($id, $calendar_id));
	    	            }
	    	            $pjCalendarExtraModel->setBatchFields(array('extra_id', 'calendar_id'))->insertBatch();
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
		
	public function pjActionDeleteExtra()
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
		if (pjExtraModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
		{
			pjMultiLangModel::factory()->where('model', 'pjExtra')->where('foreign_id', $this->_get->toInt('id'))->eraseAll();
			pjCalendarExtraModel::factory()->where('extra_id', $this->_get->toInt('id'))->eraseAll();
			$response = array('status' => 'OK');
		} else {
			$response = array('status' => 'ERR');
		}
		
		self::jsonResponse($response);
	}
	
	public function pjActionDeleteExtraBulk()
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
		
		if (pjExtraModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
			pjMultiLangModel::factory()->where('model', 'pjExtra')->whereIn('foreign_id', $record)->eraseAll();
			pjCalendarExtraModel::factory()->whereIn('extra_id', $record)->eraseAll();
			self::jsonResponse(array('status' => 'OK'));
		}
		
		self::jsonResponse(array('status' => 'ERR'));
	}
	
	public function pjActionGetExtra()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjExtraModel = pjExtraModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left');
			
			if ($q = $this->_get->toString('q'))
			{
				$q = str_replace(array('_', '%'), array('\_', '\%'), $pjExtraModel->escapeStr($q));
				$pjExtraModel->where('t2.content LIKE', "%$q%");
			}
			
			if ($this->_get->check('status') && in_array($this->_get->toString('status'), array('T', 'F')))
			{
				$pjExtraModel->where('t1.status', $this->_get->toString('status'));
			}
			if ($this->_get->check('calendar_id') && $this->_get->toInt('calendar_id') > 0)
			{
				$pjExtraModel->where("(t1.id IN(SELECT `TCE`.extra_id FROM `".pjCalendarExtraModel::factory()->getTable()."` AS `TCE` WHERE `TCE`.calendar_id='".$this->_get->toInt('calendar_id')."') )");
			}

			$column = 'name';
			$direction = 'ASC';
			if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
			{
				$column = $this->_get->toString('column');
				$direction = strtoupper($this->_get->toString('direction'));
			}
	
			$total = $pjExtraModel->findCount()->getData();
			$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
			$pages = ceil($total / $rowCount);
			$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjExtraModel
				->select('t1.*, t2.content AS name')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			
			$price_types = $this->option_arr['o_price_based_on'] == 'days' ? __('day_price_types', true) : __('price_types', true);
			foreach($data as $k => $v)
			{
				$v['name'] = pjSanitize::clean($v['name']);
				$v['price'] = pjCurrency::formatPrice($v['price']) . ' ' . @$price_types[$v['price_type']];
				$v['max_count'] = !empty($v['max_count']) ? $v['max_count'] : __('lblNA', true);
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
	    $this->set('has_update', pjAuth::factory('pjAdminExtras', 'pjActionUpdate')->hasAccess());
	    $this->set('has_create', pjAuth::factory('pjAdminExtras', 'pjActionCreate')->hasAccess());
	    $this->set('has_delete', pjAuth::factory('pjAdminExtras', 'pjActionDeleteExtra')->hasAccess());
	    $this->set('has_delete_bulk', pjAuth::factory('pjAdminExtras', 'pjActionDeleteExtraBulk')->hasAccess());
	    
	    $this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
        $this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminExtras.js');
	}
	
	public function pjActionSaveExtra()
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
		
		$pjExtraModel = pjExtraModel::factory();
		if (!in_array($params['column'], $pjExtraModel->getI18n()))
		{
			$pjExtraModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjExtra');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
	
	public function pjActionUpdate()
	{
		$this->setAjax(true);
		if ($this->isXHR()) {
			if ($this->_post->check('extra_update'))
			{
				$pjExtraModel = pjExtraModel::factory();
				
				$data['status'] = $this->_post->check('status') ? 'T' : 'F';
				$data['required'] = $this->_post->check('required') ? 'T' : 'F';
				$data['multi'] = $this->_post->check('multi') ? 'T' : 'F';
				$data['max_count'] = $this->_post->toInt('apply_max_count') == 1 ? $this->_post->toInt('max_count') : ':NULL';				
				pjExtraModel::factory()->set('id', $this->_post->toInt('id'))->modify(array_merge($this->_post->raw(), $data));
				
				$i18n_arr = $this->_post->toI18n('i18n');
				if (!empty($i18n_arr))
				{
					pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $this->_post->toInt('id'), 'pjExtra');
				}
				
				$pjCalendarExtraModel = pjCalendarExtraModel::factory();
				$pjCalendarExtraModel->where('extra_id', $this->_post->toInt('id'))->eraseAll();
				
				$calendar_id_arr = $this->_post->toArray('calendar_id');
				if(count($calendar_id_arr) > 0)
            	{
            		$pjCalendarExtraModel->reset();
	                foreach ($calendar_id_arr as $calendar_id)
    	            {
    	                $pjCalendarExtraModel->addBatchRow(array($this->_post->toInt('id'), $calendar_id));
    	            }
    	            $pjCalendarExtraModel->setBatchFields(array('extra_id', 'calendar_id'))->insertBatch();
				}
				pjAppController::jsonResponse(array('status' => 'OK'));
			} else {
				$arr = pjExtraModel::factory()->find($this->_get->toInt('id'))->getData();
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjExtra');
				$this->set('arr', $arr);
				
				$this->setLocalesData();
				
				$this->set('calendar_arr', pjCalendarModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('name ASC')
					->findAll()
					->getData());
				
				$this->set('calendar_id_arr', pjCalendarExtraModel::factory()
					->where("extra_id", $arr['id'])
					->findAll()
					->getDataPair("calendar_id", "calendar_id"));
			}
		}
	}
}
?>