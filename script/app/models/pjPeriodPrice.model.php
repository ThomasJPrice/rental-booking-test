<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPeriodPriceModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'period_price';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'period_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'adults', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'children', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL')
	);

	public static function factory($attr=array())
	{
		return new pjPeriodPriceModel($attr);
	}
}
?>