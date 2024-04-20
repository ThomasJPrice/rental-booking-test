<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVoucherModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'vouchers';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'code', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'used_count', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'discount', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'condition', 'type' => 'enum', 'default' => 'period'),
		array('name' => 'apply_on', 'type' => 'enum', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjVoucherModel($attr);
	}
}
?>