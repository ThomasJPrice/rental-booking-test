<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjOptionModel extends pjAppModel
{
	protected $primaryKey = NULL;
	
	protected $table = 'options';
	
	protected $schema = array(
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'key', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'tab_id', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'value', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'label', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'varchar', 'default' => 'string'),
		array('name' => 'order', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'is_visible', 'type' => 'tinyint', 'default' => 1),
		array('name' => 'style', 'type' => 'varchar', 'default' => 'string')
	);
	
	public static function factory($attr=array())
	{
		return new pjOptionModel($attr);
	}
	
	public function getAllPairs($foreign_id)
	{
		return $this
			->where("((t1.foreign_id='".$foreign_id."' AND t1.`key` NOT IN('".implode("','", pjUtil::getCommonOptions())."')) OR (t1.foreign_id='0' AND t1.`key` IN('".implode("','", pjUtil::getCommonOptions())."')))")
			->findAll()
			->getDataPair('key', 'value');
	}
	
	public function getPairs($foreign_id)
	{
		$_arr = $this
			->where("((t1.foreign_id='".$foreign_id."' AND t1.`key` NOT IN('".implode("','", pjUtil::getCommonOptions())."')) OR (t1.foreign_id='0' AND t1.`key` IN('".implode("','", pjUtil::getCommonOptions())."')))")
			->findAll()
			->getData();
		
		$arr = array();
		foreach ($_arr as $row)
		{
			switch ($row['type'])
			{
				case 'enum':
				case 'bool':
					list(, $arr[$row['key']]) = explode("::", $row['value']);
					break;
				default:
					$arr[$row['key']] = $row['value'];
					break;
			}
		}
		return $arr;
	}

	public function init($calendar_id)
	{
	    $data = array(
	        array($calendar_id, 'o_accept_bookings', 3, '1|0::1', '', 'bool', 1, 1, ''),
	        array($calendar_id, 'o_background_available', 2, '#80b369', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_background_booked', 2, '#da5350', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_background_empty', 2, '#f8f6f0', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_background_month', 2, '#248faf', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_background_nav', 2, '#187c9a', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_background_nav_hover', 2, '#116b86', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_background_past', 2, '#f2f0ea', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_background_pending', 2, '#f9ce67', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_background_select', 2, '#99CCCC', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_background_weekday', 2, '#ffffff', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_bf_address', 4, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 6, 1, ''),
	        array($calendar_id, 'o_bf_adults', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 4, 1, ''),
	        array($calendar_id, 'o_bf_captcha', 4, '1|3::3', 'No|Yes (Required)', 'enum', 12, 1, ''),
	        array($calendar_id, 'o_bf_children', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 5, 1, ''),
	        array($calendar_id, 'o_bf_city', 4, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 9, 1, ''),
	        array($calendar_id, 'o_bf_country', 4, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 7, 1, ''),
	        array($calendar_id, 'o_bf_email', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 2, 0, ''),
	        array($calendar_id, 'o_bf_name', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 1, 1, ''),
	        array($calendar_id, 'o_bf_notes', 4, '1|2|3::1', 'No|Yes|Yes (Required)', 'enum', 11, 1, ''),
	        array($calendar_id, 'o_bf_phone', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 3, 1, ''),
	        array($calendar_id, 'o_bf_state', 4, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 8, 1, ''),
	        array($calendar_id, 'o_bf_terms', 4, '1|3::3', 'No|Yes (Required)', 'enum', 13, 1, ''),
	        array($calendar_id, 'o_bf_zip', 4, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 10, 1, ''),
	        array($calendar_id, 'o_bookings_per_day', 3, '1', '', 'int', 5, 1, ''),
	        array($calendar_id, 'o_booking_behavior', 3, '1|2::1', 'Start & End date required|Single date', 'enum', 2, 1, ''),
	        array($calendar_id, 'o_border_inner', 2, '#e0dfde', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_border_inner_size', 2, '1', '', 'int', 1, 1, ''),
	        array($calendar_id, 'o_border_outer', 2, '#000000', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_border_outer_size', 2, '0', '', 'int', 1, 1, ''),
	        array($calendar_id, 'o_color_available', 2, '#ffffff', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_color_booked', 2, '#ffffff', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_color_legend', 2, '#676F71', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_color_month', 2, '#ffffff', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_color_past', 2, '#c5c6c7', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_color_pending', 2, '#ffffff', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_color_weekday', 2, '#737576', '', 'color', 1, 1, ''),
	        array($calendar_id, 'o_email_new_reservation', 8, "ID: {ReservationID}<br/><br/>Start date: {StartDate}<br/>End date: {EndDate}<br/><br/>Personal details<br/>Name: {Name}<br/>Phone: {Phone}<br/>Email: {Email}", '', 'text', 2, 1, ''),
	        array($calendar_id, 'o_email_new_reservation_subject', 8, 'New reservation received', '', 'string', 1, 1, ''),
	        array($calendar_id, 'o_email_password_reminder', 8, "Dear {Name},<br/><br/>Your password is: {Password}", '', 'text', 6, 1, ''),
	        array($calendar_id, 'o_email_password_reminder_subject', 8, 'Password reminder.', '', 'string', 5, 1, ''),
	        array($calendar_id, 'o_email_reservation_cancelled', 8, "Reservation has been cancelled.<br/><br/>ID: {ReservationID}<br/><br/>Start date: {StartDate}<br/>End date: {EndDate}<br/><br/>Personal details<br/>Name: {Name}<br/>Phone: {Phone}<br/>Email: {Email}", '', 'text', 4, 1, ''),
	        array($calendar_id, 'o_email_reservation_cancelled_subject', 8, 'Reservation cancelled', '', 'string', 3, 1, ''),
	        array($calendar_id, 'o_font_family', 2, 'Arial|Arial Black|Book Antiqua|Century|Century Gothic|Comic Sans MS|Courier|Courier New|Impact|Lucida Console|Lucida Sans Unicode|Monotype Corsiva|Modern|Sans Serif|Serif|Small fonts|Symbol|Tahoma|Times New Roman|Verdana::Arial', '', 'enum', 1, 1, ''),
	        array($calendar_id, 'o_font_family_legend', 2, 'Arial|Arial Black|Book Antiqua|Century|Century Gothic|Comic Sans MS|Courier|Courier New|Impact|Lucida Console|Lucida Sans Unicode|Monotype Corsiva|Modern|Sans Serif|Serif|Small fonts|Symbol|Tahoma|Times New Roman|Verdana::Arial', '', 'enum', 1, 1, ''),
	        array($calendar_id, 'o_font_size_available', 2, '14', '', 'int', 1, 1, ''),
	        array($calendar_id, 'o_font_size_booked', 2, '14', '', 'int', 1, 1, ''),
	        array($calendar_id, 'o_font_size_legend', 2, '12', '', 'int', 1, 1, ''),
	        array($calendar_id, 'o_font_size_month', 2, '20', '', 'int', 1, 1, ''),
	        array($calendar_id, 'o_font_size_past', 2, '14', '', 'int', 1, 1, ''),
	        array($calendar_id, 'o_font_size_pending', 2, '14', '', 'int', 1, 1, ''),
	        array($calendar_id, 'o_font_size_weekday', 2, '12', '', 'int', 1, 1, ''),
	        array($calendar_id, 'o_font_style_available', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: bold', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, ''),
	        array($calendar_id, 'o_font_style_booked', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: bold', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, ''),
	        array($calendar_id, 'o_font_style_legend', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: normal', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, ''),
	        array($calendar_id, 'o_font_style_month', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: normal', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, ''),
	        array($calendar_id, 'o_font_style_past', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: bold', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, ''),
	        array($calendar_id, 'o_font_style_pending', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: bold', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, ''),
	        array($calendar_id, 'o_font_style_weekday', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: normal', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, ''),
	        array($calendar_id, 'o_multi_lang', 99, '1|0::1', '', 'bool', '', 0, ''),
	        array($calendar_id, 'o_price_based_on', 3, 'days|nights::days', 'Days|Nights', 'enum', 11, 1, ''),
	        array($calendar_id, 'o_price_plugin', 3, 'price|period::price', 'Day/Night|Periods', 'enum', 12, 1, ''),
	        array($calendar_id, 'o_bf_adults_max', 3, '5', '', 'int', 13, 1, ''),
	        array($calendar_id, 'o_bf_children_max', 3, '5', '', 'int', '', 0, ''),
	        array($calendar_id, 'o_max_people', 3, '10', '', 'int', 14, 1, ''),
	        array($calendar_id, 'o_min_people', 3, '1', '', 'int', 14, 1, ''),
	        array($calendar_id, 'o_show_legend', 1, '1|0::1', '', 'bool', 10, 1, ''),
	        array($calendar_id, 'o_show_prices', 1, '1|0::1', '', 'bool', 8, 1, ''),
	        array($calendar_id, 'o_show_week_numbers', 1, '1|0::1', '', 'bool', 9, 1, ''),
	        array($calendar_id, 'o_sms_new_reservation', 9, 'New reservation has been received.', '', 'text', 1, 1, ''),
	        array($calendar_id, 'o_sms_reservation_cancelled', 9, 'A reservation has been cancelled.', '', 'text', 2, 1, ''),
	        array($calendar_id, 'o_status_if_not_paid', 3, 'confirmed|pending|cancelled::pending', 'Confirmed|Pending|Cancelled', 'enum', 10, 1, ''),
	        array($calendar_id, 'o_status_if_paid', 3, 'confirmed|pending|cancelled::confirmed', 'Confirmed|Pending|Cancelled', 'enum', 9, 1, ''),
	        
	        array($calendar_id, 'o_disable_payments', 3, '1|0::0', '', 'bool', 15, 1, ''),
	        array($calendar_id, 'o_tourist_tax', 3, '10', '', 'float', 16, 1, ''),
	        array($calendar_id, 'o_max_tax', 3, '', '', 'float', 16, 0, ''),
	        array($calendar_id, 'o_deposit', 3, '10', '', 'float', 17, 1, ''),
	        array($calendar_id, 'o_deposit_type', 3, 'amount|percent::percent', 'Amount|Percent', 'enum', 17, 0, ''),
	        array($calendar_id, 'o_security', 3, '20', '', 'float', 18, 1, ''),
	        array($calendar_id, 'o_to_be_paid', 3, 'when_booking|on_arrival::when_booking', 'when booking|on arrival', 'enum', 18, 0, ''),
	        array($calendar_id, 'o_tax', 3, '10', '', 'float', 19, 1, ''),
	        array($calendar_id, 'o_require_all_within', 3, '10', '', 'int', 20, 1, ''),
	        array($calendar_id, 'o_allow_paypal', 3, '1|0::0', '', 'bool', 21, 1, ''),
	        array($calendar_id, 'o_paypal_address', 3, '', '', 'string', 22, 1, ''),
	        array($calendar_id, 'o_allow_authorize', 3, '1|0::0', '', 'bool', 23, 1, ''),
	        array($calendar_id, 'o_authorize_key', 3, '', '', 'string', 24, 1, ''),
	        array($calendar_id, 'o_authorize_mid', 3, '', '', 'string', 25, 1, ''),
	        array($calendar_id, 'o_authorize_hash', 3, '', '', 'string', 26, 1, ''),
	        array($calendar_id, 'o_authorize_tz', 3, '-43200|-39600|-36000|-32400|-28800|-25200|-21600|-18000|-14400|-10800|-7200|-3600|0|3600|7200|10800|14400|18000|21600|25200|28800|32400|36000|39600|43200|46800::0', 'GMT-12:00|GMT-11:00|GMT-10:00|GMT-09:00|GMT-08:00|GMT-07:00|GMT-06:00|GMT-05:00|GMT-04:00|GMT-03:00|GMT-02:00|GMT-01:00|GMT|GMT+01:00|GMT+02:00|GMT+03:00|GMT+04:00|GMT+05:00|GMT+06:00|GMT+07:00|GMT+08:00|GMT+09:00|GMT+10:00|GMT+11:00|GMT+12:00|GMT+13:00', 'enum', 27, 1, ''),
	        array($calendar_id, 'o_allow_creditcard', 3, '1|0::1', '', 'bool', 28, 1, ''),
	        array($calendar_id, 'o_allow_bank', 3, '1|0::1', '', 'bool', 29, 1, ''),
	        array($calendar_id, 'o_bank_account', 3, "Please, send your payment to HSBC\r\naccount number: ABCDEF1234567890", '', 'text', 30, 1, ''),
	        array($calendar_id, 'o_allow_cash', 3, '1|0::1', '', 'bool', 31, 1, ''),
	        array($calendar_id, 'o_thankyou_page', 3, 'http://www.phpjabbers.com/', '', 'string', 31, 1, ''),
	        array($calendar_id, 'o_cancel_url', 3, 'http://www.phpjabbers.com/', '', 'string', 32, 1, '')
	    );
	    
	    $this->setBatchFields(array('foreign_id', 'key', 'tab_id', 'value', 'label', 'type', 'order', 'is_visible', 'style'));
	    $this->setBatchRows($data);
	    $this->insertBatch();
	}
	
	public function initConfirmation($calendar_id, $locale_arr)
	{
		$pjNotificationModel = pjNotificationModel::factory();
	    $init_notify_arr = $pjNotificationModel->where('foreign_id', 0)->where('is_general', 0)->orderBy("id ASC")->findAll()->getData();
	    foreach ($init_notify_arr as $record)
	    {
	        $record['foreign_id'] = $calendar_id;
	        unset($record['id']);
	        $pjNotificationModel->reset()->setAttributes($record)->insert();
	    }
	    
	    $pjPaymentOptionModel = pjPaymentOptionModel::factory();
	    $payment_arr = $pjPaymentOptionModel->where("foreign_id IS NULL")->findAll()->getData();
	    foreach($payment_arr as $payment_data)
	    {
	        unset($payment_data['id']);
	        $payment_data['foreign_id'] = $calendar_id;
	        $pjPaymentOptionModel->reset()->setAttributes($payment_data)->insert();
	    }
	    $pjPaymentOptionModel->reset()->setAttributes(array('foreign_id' => $calendar_id, 'payment_method' => 'bank', 'is_active' => 0))->insert();
	    $po_id = $pjPaymentOptionModel->reset()->setAttributes(array('foreign_id' => $calendar_id, 'payment_method' => 'cash', 'is_active' => 1))->insert()->getInsertId();
	    if ($po_id !== false && (int) $po_id > 0)
	    {
	        $locale_arr = pjLocaleModel::factory()->findAll()->getData();
	        $i18n_arr = array();
	        foreach($locale_arr as $locale)
	        {
	            $i18n_arr[$locale['id']]['cash'] = 'Cash';
	        }
	        pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $calendar_id, 'pjPayment');
	    }
	}
}
?>