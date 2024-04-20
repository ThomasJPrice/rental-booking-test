<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOptions extends pjAdmin
{
	public function pjActionInstall()
	{
		$this->checkLogin();
	    
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
		
		$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.title')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->orderBy('t1.sort ASC')->findAll()->getData();
		$this->set('locale_arr', $locale_arr);
				
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionNotifications()
	{
		if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
	    $this->setLocalesData();
	    
	    $this->appendCss('awesome-bootstrap-checkbox.css', PJ_THIRD_PARTY_PATH . 'awesome_bootstrap_checkbox/');
	    $this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
	    $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
	    $this->appendJs('pjAdminOptions.js');
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
		    ->where('t1.is_general', 1)
		    ->where('t1.foreign_id', 0)
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
		    ->where('t1.is_general', 1)
		    ->where('t1.foreign_id', 0)
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
	    
	    if (!(isset($this->body['id']) && pjValidation::pjActionNumeric($this->body['id'])))
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
	        ->set('id', $this->_post->toInt('id'))
	        ->modify(array('is_active' => $this->_post->toInt('is_active')));
	    } elseif ($isFormSubmit) {
	        pjBaseMultiLangModel::factory()->updateMultiLang($this->_post->toArray('i18n'), $this->_post->toInt('id'), 'pjNotification');
	    }
	    
	    self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Notification has been updated.'));
	}
	
	public function pjActionPreview()
	{
		$this->checkLogin();
	    
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
			
	    $this->appendJs('pjAdminOptions.js');
	}
}
?>