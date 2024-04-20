<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjDiscountModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'discounts';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'discount', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'condition', 'type' => 'enum', 'default' => 'period'),
		array('name' => 'options', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'early_days', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'min_persons', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'max_persons', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'min_duration', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'max_duration', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'apply_on', 'type' => 'enum', 'default' => ':NULL')
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjDiscountModel($attr);
	}
}
?>