<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjExtraModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'extras';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price_type', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'required', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'multi', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'max_count', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjExtraModel($attr);
	}
}
?>