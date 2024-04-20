<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPaypalExpress extends pjPaypalExpressAppController
{
    protected static $logPrefix = "Payments | pjPaypalExpress plugin<br>";
    
    protected static $paymentMethod = 'paypal_express';
    
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
        $params['notify_url'] .= '&custom=' . $params['custom'];
        $params['cancel_url'] = "{$params['notify_url']}&cancel_hash={$params['cancel_hash']}";

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
        $custom = isset($request['custom']) ? $request['custom'] : null;

        if (!empty($custom))
        {
            $this->log(self::$logPrefix . "Start confirmation process for: {$custom}<br>Request Data:<br>" . print_r($request, true));
        } else {
            $this->log(self::$logPrefix . "Missing parameters. Cannot start confirmation process.<br>Request Data:<br>" . print_r($request, true));
        }

        return $custom;
    }

    public function pjActionForm()
    {
        $this->setLayout('pjActionEmpty');

        $params = $this->getParams();

        $sdk = new pjPaypalExpressSDK($params['merchant_id'], $params['public_key'], $params['private_key'], $params['is_test_mode']);

        $order = array(
            'intent' => 'CAPTURE', // CAPTURE, AUTHORIZE
            'purchase_units' => array(
                array(
                    'amount' => array(
                        'value' => $params['amount'],
                        'currency_code' => $params['currency_code'],
                    ),
                    'custom_id' => $params['custom'],
                    'description' => $params['description'],
                    'invoice_id' => $params['custom'],
                )
            ),
            'payment_source' => array(
                'paypal' => array(
                    'experience_context' => array(
                        'payment_method_selected' => 'PAYPAL',
                        'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED', // UNRESTRICTED, IMMEDIATE_PAYMENT_REQUIRED
                        'landing_page' => 'NO_PREFERENCE', // LOGIN, GUEST_CHECKOUT, NO_PREFERENCE
                        'user_action' => 'PAY_NOW', // CONTINUE, PAY_NOW
                        'return_url' => $params['notify_url'],
                        'cancel_url' => $params['cancel_url'],
                    ),
                ),
            ),
        );

        try {
            $result = $sdk->createOrder($order);

            switch ($result['status'])
            {
                case 'PAYER_ACTION_REQUIRED':
                    foreach ($result['links'] as $link)
                    {
                        if ($link['rel'] == 'payer-action')
                        {
                            $this->set('form', array(
                                'action' => $link['href'],
                                'method' => $link['method'],
                            ));
                            break;
                        }
                    }
                    break;
                case 'APPROVED':
                case 'COMPLETED':
                case 'CREATED':
                case 'SAVED':
                default:
                    foreach ($result['links'] as $link)
                    {
                        if ($link['rel'] == 'approve')
                        {
                            $this->set('form', array(
                                'action' => $link['href'],
                                'method' => $link['method'],
                            ));
                            break;
                        }
                    }
            }
        } catch (Exception $e) {
            $this->log(self::$logPrefix . "Error: " . $e->getMessage());
            //echo $e->getMessage();
        }

        $this->set('arr', $params);
    }

	public function pjActionConfirm()
	{
        $params = $this->getParams();
        $request = $params['request'];

        if (!isset($params['key']) || $params['key'] != md5($this->option_arr['private_key'] . PJ_SALT))
        {
            $this->log(self::$logPrefix . "Missing or invalid 'key' parameter.");
            return FALSE;
        }

        $response = array('status' => 'FAIL', 'redirect' => true);
        if (isset($request['cancel_hash']) && $request['cancel_hash'] == $params['cancel_hash'])
        {
            $this->log(self::$logPrefix . "Payment was cancelled.");
            $response['status'] = 'CANCEL';
            return $response;
        }

        $options = pjPaymentOptionModel::factory()->getOptions($params['foreign_id'], self::$paymentMethod);

        if ((int) $options['is_test_mode'] === 1)
        {
        	$merchantId = $options['test_merchant_id'];
        	$publicKey  = $options['test_public_key'];
        	$privateKey = $options['test_private_key'];
        	$sandbox    = true;
        } else {
        	$merchantId = $options['merchant_id'];
        	$publicKey  = $options['public_key'];
        	$privateKey = $options['private_key'];
        	$sandbox    = false;
        }

        $sdk = new pjPaypalExpressSDK($merchantId, $publicKey, $privateKey, $sandbox);
        try {

            if (isset($request['subscription_id']))
            {
                $subscription = $sdk->getSubscription($request['subscription_id']);
                $this->log(self::$logPrefix . 'Subscription: ' . print_r($subscription, true));
                if (isset($subscription['status']))
                {
                    if ($subscription['status'] == 'APPROVED')
                    {
                        $capture = $sdk->captureSubscription($request['subscription_id'], array(
                            'amount' => array(
                                'value' => $subscription['billing_info']['outstanding_balance']['value'],
                                'currency_code' => $subscription['billing_info']['outstanding_balance']['currency_code'],
                            ),
                            'capture_type' => 'OUTSTANDING_BALANCE',
                            'note' => 'Subscription confirmation',
                        ));

                        if (isset($capture['status']) && $capture['status'] == 'COMPLETED')
                        {
                            $response['status'] = 'OK';
                            $response['txn_id'] = $capture['id'];
                        }
                    } elseif ($subscription['status'] == 'ACTIVE') {
                        $response['status'] = 'OK';
                        $response['txn_id'] = null;
                    }
                }
            } elseif (isset($request['token'])) {
                $order = $sdk->getOrder($request['token']);
                $this->log(self::$logPrefix . 'Order: ' . print_r($order, true));
                if (isset($order['status']))
                {
                    if ($order['status'] == 'APPROVED')
                    {
                        $capture = $sdk->captureOrder($order['id']);

                        if (isset($capture['status']) && $capture['status'] == 'COMPLETED')
                        {
                            $response['status'] = 'OK';
                            $response['txn_id'] = $capture['id'];
                        }
                    } elseif ($order['status'] == 'COMPLETED') {
                        $response['status'] = 'OK';
                        $response['txn_id'] = @$order['purchase_units'][0]['payments']['captures'][0]['id'];
                    }
                }
            }
        } catch (Exception $e) {
        	$this->log(self::$logPrefix . "Error: " . $e->getMessage());
        }

        return $response;
	}

    public function pjActionSubscribe()
    {
        $this->setLayout('pjActionEmpty');

        $params = $this->getParams();

        $sdk = new pjPaypalExpressSDK($params['merchant_id'], $params['public_key'], $params['private_key'], $params['is_test_mode']);

        $product = array(
            'name' => $params['item_name'],
            'description' => $params['description'],
            'type' => 'SERVICE', // PHYSICAL, DIGITAL, SERVICE
        );

        $plan = array(
            'billing_cycles' => array(
                array(
                    'frequency' => array(
                        'interval_unit' => isset($params['interval_unit']) ? $params['interval_unit'] : 'MONTH', // DAY, WEEK, MONTH, YEAR
                        'interval_count' => isset($params['interval_count']) ? $params['interval_count'] : 1,
                    ),
                    'pricing_scheme' => array(
                        'fixed_price' => array(
                            'value' => $params['amount'],
                            'currency_code' => $params['currency_code'],
                        ),
                    ),
                    'sequence' => 1,
                    'tenure_type' => 'REGULAR', // TRIAL, REGULAR
                    'total_cycles' => 0
                )
            ),
            'name' => $params['item_name'],
            'description' => $params['description'],
            'payment_preferences' => array(
                'auto_bill_outstanding' => true,
                'payment_failure_threshold' => 3,
            ),
            'status' => 'ACTIVE', // ACTIVE, INACTIVE, CREATED
        );

        $subscription = array(
            'custom_id' => $params['custom'],
            'subscriber' => array(
                'email_address' => $params['email'],
                'name' => array(
                    'given_name' => $params['first_name'],
                    'surname' => $params['last_name'],
                )
            ),
            'application_context' => array(
                'user_action' => 'SUBSCRIBE_NOW',
                'payment_method' => array(
                    'payer_selected' => 'PAYPAL',
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                ),
                'return_url'=> $params['notify_url'],
                'cancel_url'=> $params['cancel_url'],
            ),
        );

        try {
            $cp_result = $sdk->createProduct($product);

            $plan['product_id'] = $cp_result['id'];
            $bp_result = $sdk->createSubscriptionPlan($plan);

            $params['plan_id'] = $bp_result['id'];

            $subscription['plan_id'] = $bp_result['id'];
            $bs_result = $sdk->createSubscription($subscription);

            if ($bs_result['status'] == 'APPROVAL_PENDING')
            {
                foreach ($bs_result['links'] as $link)
                {
                    if ($link['rel'] == 'approve')
                    {
                        $this->set('form', array(
                            'action' => $link['href'],
                            'method' => $link['method'],
                        ));
                        break;
                    }
                }
            }

        } catch (Exception $e) {
            $this->log(self::$logPrefix . "Error: " . $e->getMessage());
            //echo $e->getMessage();
        }

        $this->set('arr', $params);
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

		$this->set('params', $params);
	}
}
