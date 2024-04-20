<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPaypal extends pjPaypalAppController
{
    protected static $logPrefix = "Payments | pjPaypal plugin<br>";
    
    protected static $paymentMethod = 'paypal';
    
    public function pjActionOptions()
    {
        $this->checkLogin();

        $this->setLayout('pjActionEmpty');

        $params = $this->getParams();

        $this->set('arr', pjPaymentOptionModel::factory()->getOptions($params['foreign_id'], self::$paymentMethod));

        $i18n = pjMultiLangModel::factory()->getMultiLang($params['fid'], 'pjPayment');
        $this->set('i18n', $i18n);
        $locale_arr = pjLocaleModel::factory()
        	->select('t1.*, t2.file')
	        ->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
	        ->where('t2.file IS NOT NULL')
	        ->orderBy('t1.sort ASC')
	        ->findAll()
        	->getData();

        $lp_arr = array();
        $default_locale_id = NULL;
        foreach ($locale_arr as $item)
        {
        	$lp_arr[$item['id']."_"] = $item['file'];
        	if ($item['is_default'])
        	{
        		$default_locale_id = $item['id'];
        	}
        }
        $this->set('lp_arr', $locale_arr);
        $this->set('locale_str', pjAppController::jsonEncode($lp_arr));
        $this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
        
        $this->set('locale_id', isset($params['locale_id']) ? $params['locale_id'] : $default_locale_id);
    }

    public function pjActionSaveOptions()
    {
        $this->checkLogin();

        return true;
    }

    public function pjActionCopyOptions()
    {
        $this->checkLogin();

        return true;
    }

    public function pjActionDeleteOptions()
    {
        $this->checkLogin();

        return true;
    }

    public static function getFormParams($post, $order_arr)
    {
        $params = parent::getFormParams($post, $order_arr);

        $params['locale'] = self::getPaymentLocale($params['locale_id']);
        if (isset($order_arr['cancel_url']) && !empty($order_arr['cancel_url'])) {
            $cancel_url = $order_arr['cancel_url'];
            if (strpos($cancel_url, '?') !== false) {
                $cancel_url .= "&";
            } else {
                $cancel_url .= "?";
            }
        } else {
            $cancel_url = $params['notify_url']."&";
        }
        $params['cancel_url'] = "{$cancel_url}custom={$params['custom']}&cancel_hash={$params['cancel_hash']}";
        $params['charset'] = 'utf-8';
        $params['submit'] = __('plugin_paypal_btn_submit', true, true);
        $params['submit_class'] = 'btn-paypal-submit';

        return $params;
    }

    public static function getPaymentLocale($localeId = null)
    {
        $locale = 'en_US'; // English (default)

        if($localeId && $locale_arr = pjLocaleModel::factory()->select('language_iso')->find($localeId)->getData())
        {
            $lang = strtok($locale_arr['language_iso'], '-');
            if(in_array($locale_arr['language_iso'], array('zh-TW')))
            {
                $lang = 'tw';
            }
            elseif(in_array($locale_arr['language_iso'], array('es', 'es-ES')))
            {
                $lang = 'es_es';
            }
            elseif(strpos($locale_arr['language_iso'], '-NO') || in_array($locale_arr['language_iso'], array('nb', 'nn')))
            {
                $lang = 'no';
            }
            elseif(strpos($locale_arr['language_iso'], '-RU'))
            {
                $lang = 'ru';
            }
            elseif(strpos($locale_arr['language_iso'], '-SE'))
            {
                $lang = 'sv';
            }
            elseif(in_array($locale_arr['language_iso'], array('pt-BR')))
            {
                $lang = 'pt_br';
            }
            elseif(in_array($locale_arr['language_iso'], array('en-AU')))
            {
                $lang = 'en_au';
            }
            elseif(in_array($locale_arr['language_iso'], array('fr-BE', 'fr-FR')))
            {
                $lang = 'fr_fr';
            }
            elseif(in_array($locale_arr['language_iso'], array('fr-CA')))
            {
                $lang = 'fr_ca';
            }
            elseif(strpos($locale_arr['language_iso'], '-GB') || strpos($locale_arr['language_iso'], '-IN') || in_array($locale_arr['language_iso'], array('en-SG')))
            {
                $lang = 'en_gb';
            }
            elseif(in_array($locale_arr['language_iso'], array('zh-HK')))
            {
                $lang = 'hk';
            }

            $locales = array(
                'ar' => 'ar_EG',
                'es' => 'es_XC',
                'es_es' => 'es_ES',
                'de' => 'de_DE',
                'sv' => 'sv_SE',
                'tw' => 'zh_TW',
                'th' => 'th_TH',
                'ru' => 'ru_RU',
                'uk' => 'ru_RU',
                'et' => 'ru_RU',
                'lv' => 'ru_RU',
                'nl' => 'nl_NL',
                'he' => 'he_IL',
                'it' => 'it_IT',
                'ja' => 'ja_JP',
                'id' => 'id_ID',
                'pl' => 'pl_PL',
                'no' => 'no_NO',
                'pt' => 'pt_PT',
                'pt_br' => 'pt_BR',
                'da' => 'da_DK',
                'fo' => 'da_DK',
                'kl' => 'da_DK',
                'en_au' => 'en_AU',
                'ko' => 'ko_KR',
                'fr' => 'fr_XC',
                'fr_fr' => 'fr_FR',
                'fr_ca' => 'fr_CA',
                'en_gb' => 'en_GB',
                'zh' => 'zh_CN',
                'hk' => 'zh_HK',
            );

            if(array_key_exists($lang, $locales))
            {
                $locale = $locales[$lang];
            }
        }

        return $locale;
    }

    public function pjActionGetCustom()
    {
        $request = $this->getParams();
        $custom = isset($request['custom']) ? $request['custom']: null;

        if (!empty($custom))
        {
            $this->log(self::$logPrefix . "Start confirmation process for: {$custom}<br>Request Data:<br>" . print_r($request, true));
        } else {
            $this->log(self::$logPrefix . "Missing parameters. Cannot start confirmation process.<br>Request Data:<br>" . print_r($request, true));
        }

        return $custom;
    }

	public function pjActionConfirm()
	{
        $params = $this->getParams();
        $request = $params['request'];

        if (!isset($params['key']) || $params['key'] != md5($this->option_arr['private_key'] . PJ_SALT))
        {
            $this->log(self::$logPrefix . "Missing or invalid 'key' parameter.");
            $response = array('status' => 'FAIL', 'redirect' => false);
            return $response;
        }
        if (!isset($request['paypal_order_id']) || (isset($request['paypal_order_id']) && empty($request['paypal_order_id']))) {
        	$this->log(self::$logPrefix . "Missing or invalid 'Paypal Order ID' parameter.");
        	$response = array('status' => 'FAIL', 'redirect' => false);
            return $response;
        }

        if (isset($request['cancel_hash']) && $request['cancel_hash'] == $params['cancel_hash'])
        {
            $this->log(self::$logPrefix . "Payment was cancelled.");
            $response = array('status' => 'CANCEL', 'redirect' => false);
            return $response;
        }
        
        $response = array('status' => 'FAIL', 'redirect' => false);

        $options = pjPaymentOptionModel::factory()->getOptions($params['foreign_id'], self::$paymentMethod);
        $access_token = $this->getAccessToken($options);

        $is_test_mode = (int) $options['is_test_mode'] === 1;
        if ($is_test_mode)
        {
        	$url  = 'https://api-m.sandbox.paypal.com/v2/checkout/orders/'.$request['paypal_order_id'].'/capture';
        } else {
        	$url  = 'https://api-m.paypal.com/v2/checkout/orders/'.$request['paypal_order_id'].'/capture';
        }		
		$http = new pjHttp();	
		$headers = array(
			"Content-Type: application/json",
		   	"Authorization: Bearer ".$access_token,
			"PayPal-Request-Id: ".time()
		);
		
		$http->setHeaders($headers);
		$http->setMethod('POST');
		$resp = $http->request($url);
		$result = json_decode($resp->getResponse(), true);
		if (isset($result['status'])) {
			if (in_array($result['status'], array('COMPLETED','APPROVED'))) {
				if (isset($result['purchase_units'][0]['payments']['captures'][0]['id']) && !empty($result['purchase_units'][0]['payments']['captures'][0]['id'])) {
					$txn_id = $result['purchase_units'][0]['payments']['captures'][0]['id'];
					if (isset($result['purchase_units'][0]['payments']['captures'][0]['status']) && $result['purchase_units'][0]['payments']['captures'][0]['status'] == 'COMPLETED') {				
						if (isset($result['purchase_units'][0]['payments']['captures'][0]['amount']['value']) && $result['purchase_units'][0]['payments']['captures'][0]['amount']['value'] == $params['amount']) {
							$this->log(self::$logPrefix . "TXN ID: {$txn_id}<br>AMOUNT is OK");
							if (isset($result['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code']) && $result['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'] == $this->option_arr['o_currency'])
							{
								$response['status'] = 'OK';
								$response['txn_id'] = $txn_id;
								$this->log(self::$logPrefix . "Payment was successful. TXN ID: {$txn_id}.");
								return $response;
							} else {
								$this->log(self::$logPrefix . "TXN ID: {$txn_id}<br>CURRENCY didn't match");
							}
						} else {
							$this->log(self::$logPrefix . "TXN ID: {$txn_id}<br>AMOUNT didn't match");
						}
					} else {
						$this->log(self::$logPrefix . "TXN ID: {$txn_id}<br>Not Completed");
					}
				} else {
					$this->log(self::$logPrefix . "TXN ID not found");
				}
			} else {
				$this->log(self::$logPrefix . "Order status: ".$result['status']);
				$response['status'] = $result['status'];
			}
		} else {
			$this->log(self::$logPrefix . "Unknow payment status.");
            $response['status'] = 'UNKNOW';
		}
		return $response;
	}

	public function pjActionSave($foreign_id, $data=array())
	{
		$this->setLayout('pjActionEmpty');

		$params = $this->getParams();
		if (!isset($params['key']) || $params['key'] != md5($this->option_arr['private_key'] . PJ_SALT))
		{
			return FALSE;
		}

		return $this->pjActionSaveIpn($params['foreign_id'], $params['data']);
	}
	
	private function pjActionSaveIpn($foreign_id, $data)
	{
		return pjPaypalModel::factory()
			->setAttributes(array(
				'foreign_id' => $foreign_id? $foreign_id: ':NULL',
				'subscr_id' => @$data['subscr_id'],
				'txn_id' => @$data['txn_id'],
				'txn_type' => @$data['txn_type'],
				'mc_gross' => @$data['mc_gross'],
				'mc_currency' => @$data['mc_currency'],
				'payer_email' => @$data['payer_email'],
				'dt' => date("Y-m-d H:i:s", strtotime(@$data['payment_date']))
			))
			->insert()
			->getInsertId();
	}
	
	public function pjActionForm()
	{
		$this->setLayout('pjActionEmpty');

		if (defined('PJ_INSTALL_UUID'))
        {
            $this->setTemplate(get_class(), get_class().':pjActionForm2');
        }

		$this->set('arr', $this->getParams());
	}
/**
 * @link https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/#id08A6HI00JQU
 */
	public function pjActionSubscribe()
	{
		$this->setAjax(true);
		// KEYS:
		//-------------
		//name
		//id
		//class
		//target
		//business
		//item_name => 127 chars
		//currency_code => 3 chars
		//custom => 255 chars
		//a1_price
		//p1_duration => 1-90 or 1-52 or 1-24 or 1-5 (depend of duration_unit)
		//t1_duration_unit => D,W,M,Y
		//a2_price
		//p2_duration => 1-90 or 1-52 or 1-24 or 1-5 (depend of duration_unit)
		//t2_duration_unit => D,W,M,Y
		//a3_price
		//p3_duration => 1-90 or 1-52 or 1-24 or 1-5 (depend of duration_unit)
		//t3_duration_unit => D,W,M,Y
		//recurring_payments => 0,1
		//recurring_times => 2-52
		//reattempt_on_failure => 0,1
		//return
		//cancel_return
		//notify_url
		//submit
		//submit_class
		$this->set('arr', $this->getParams());
	}

	public function pjActionGetDetails()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
		    $id = null;
		    if (class_exists('pjInput'))
            {
                $id = $this->_get->toInt('id');
            } elseif (isset($_GET['id']) && (int) $_GET['id'] > 0) {
                $id = (int) $_GET['id'];
            }

			if ($id)
			{
				$this->set('arr', pjPaypalModel::factory()->find($id)->getData());
			}
		}
	}
	
	public function pjActionGetPaypal()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjPaypalModel = pjPaypalModel::factory();

			$q = null;
			if (class_exists('pjInput'))
            {
                $q = $this->_get->toString('q');
            }
            elseif (isset($_GET['q']) && !empty($_GET['q']))
            {
                $q = $pjPaypalModel->escapeStr($_GET['q']);
            }
			if ($q)
			{
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjPaypalModel->where('t1.txn_id LIKE', "%$q%");
				$pjPaypalModel->orWhere('t1.txn_type LIKE', "%$q%");
				$pjPaypalModel->orWhere('t1.mc_gross LIKE', "%$q%");
				$pjPaypalModel->orWhere('t1.mc_currency LIKE', "%$q%");
				$pjPaypalModel->orWhere('t1.payer_email LIKE', "%$q%");
				$pjPaypalModel->orWhere('t1.dt LIKE', "%$q%");
			}
				
			$column = 'dt';
			$direction = 'DESC';
			if (class_exists('pjInput'))
            {
                if (in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
                {
                    $column = $this->_get->toString('column');
                    $direction = strtoupper($this->_get->toString('direction'));
                }
            }
            elseif (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
            {
                $column = $_GET['column'];
                $direction = strtoupper($_GET['direction']);
            }

			$total = $pjPaypalModel->findCount()->getData();
			if (class_exists('pjInput'))
            {
                $rowCount = $this->_get->toInt('rowCount') ?: 10;
    			$page = $this->_get->toInt('page') ?: 1;
            }
            else
            {
                $rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
                $page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
            }
			$pages = ceil($total / $rowCount);
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjPaypalModel->select('t1.*')
				->orderBy("`$column` $direction")->limit($rowCount, $offset)->findAll()->getData();
						
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
	    if (!$this->isLoged())
        {
            $this->sendForbidden();
            return;
        }

        $this->appendJs('pjPaypal.js', $this->getConst('PLUGIN_JS_PATH'));
        if (pjObject::getPlugin('pjCms') !== null)
        {
            $this->appendJs('jquery.datagrid.js', $this->getConstant('pjCms', 'PLUGIN_JS_PATH'), false, false);
            $this->appendJs('index.php?controller=pjCms&action=pjActionMessages', PJ_INSTALL_URL, true);
        }
        else
        {
            $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
            $this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
        }
	}
	
	public function pjActionTest()
	{
		$this->setLayout('pjActionEmpty');
		
		$data = self::generateTestData();
		
		$post = array(
			'payment_method' => self::$paymentMethod,
		);
		
		$order = array(
			'locale_id'     => $this->getLocaleId(),
			'return_url'    => PJ_INSTALL_URL . (class_exists('pjUtil') && method_exists('pjUtil', 'getWebsiteUrl') ? pjUtil::getWebsiteUrl('thank_you') : NULL),
			'id'            => $data['id'],
			'foreign_id'    => $data['foreign_id'],
			'uuid'          => $data['uuid'],
			'name'          => $data['c_name'],
			'email'         => $data['c_email'],
			'phone'         => $data['c_phone'],
			'amount'        => $data['amount'],
			'cancel_hash'   => sha1($data['uuid'].strtotime($data['created']).PJ_SALT),
			'currency_code' => isset($this->option_arr['o_currency']) ? $this->option_arr['o_currency'] : 'USD',
		);
		
		# Override parameters from query string, e.g. &foreign_id=2
		$qs = array();
		foreach (array_keys($order) as $key)
		{
			if (class_exists('pjInput'))
			{
				if ($this->_get->has($key))
				{
					$order[$key] = $this->_get->raw($key);
					$qs[$key] = $order[$key];
				}
			} else {
				if (array_key_exists($key, $_GET))
				{
					$order[$key] = $_GET[$key];
					$qs[$key] = $order[$key];
				}
			}
		}
		$this->set('qs', $qs);
		
		$params = self::getFormParams($post, $order);
		
		$params['a3_price'] = $data['a3_price'];
		$params['p3_duration'] = $data['p3_duration'];
		$params['t3_duration_unit'] = $data['t3_duration_unit'];
		
		$this->set('params', $params);
	}
	
	private function getAccessToken($params) {
		if ((int)$params['is_test_mode'] == 1) {
			$url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
			$client_id = $params['test_merchant_id'];
			$client_secret = $params['test_private_key'];
		} else {
			$url = 'https://api-m.paypal.com/v1/oauth2/token';
			$client_id = $params['merchant_id'];
			$client_secret = $params['private_key'];
		}
		
		$http = new pjHttp();		
		$http->setUsername($client_id);
		$http->setPassword($client_secret);
		$data = array(
			'grant_type' => 'client_credentials'
		);
		$http->setMethod('POST');
		$http->setData($data);
		$resp = $http->request($url);
		$result = json_decode($resp->getResponse(), true);
		$access_token = $result['access_token'];
		
		return $access_token;
	}
	
	public function pjActionCreateOrder() {
		$this->setAjax(true);
		
		$params = pjPaymentOptionModel::factory()->getOptions($_REQUEST['foreign_id'], self::$paymentMethod);
					
		$access_token = $this->getAccessToken($params);
		$input = file_get_contents('php://input');
		$post = json_decode($input, true);
		
		$http = new pjHttp();	
		$headers = array(
			"Content-Type: application/json",
		   	"Authorization: Bearer ".$access_token,
			"PayPal-Request-Id: ".time()
		);
		
		$data = array(
			'intent' => 'CAPTURE',
			'purchase_units' => array(
				array(
					'reference_id' => $post['cart']['reference_id'],
					'amount' => array(
						'currency_code' => $post['cart']['currency'],
						'value' => $post['cart']['amount']
					),
					'description' => $post['cart']['description']
				)
			)
		);
		
		if ((int)$params['is_test_mode'] == 1) {
			$url = 'https://api-m.sandbox.paypal.com/v2/checkout/orders';
		} else {
			$url = 'https://api-m.paypal.com/v2/checkout/orders';
		}
		
		$http->setHeaders($headers);
		$http->setMethod('POST');
		$http->setData(json_encode($data), false);
		$resp = $http->request($url);
		$result = json_decode($resp->getResponse(), true);
		pjAppController::jsonResponse($result);
	}
}
?>