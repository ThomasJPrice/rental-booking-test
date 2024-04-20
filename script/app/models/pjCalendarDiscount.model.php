<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCalendarDiscountModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'calendars_discounts';
	
	protected $schema = array(
		array('name' => 'calendar_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'discount_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjCalendarDiscountModel($attr);
	}
}
?>