<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCalendarVoucherModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'calendars_vouchers';
	
	protected $schema = array(
		array('name' => 'calendar_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'voucher_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjCalendarVoucherModel($attr);
	}
}
?>