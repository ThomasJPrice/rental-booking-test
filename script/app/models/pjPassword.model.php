<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPasswordModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'password';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'calendar_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'format', 'type' => 'enum', 'default' => 'ical'),
		array('name' => 'delimiter', 'type' => 'enum', 'default' => 'comma'),
		array('name' => 'password', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => 'next'),
		array('name' => 'period', 'type' => 'enum', 'default' => '1')
	);
	
	public static function factory($attr=array())
	{
		return new pjPasswordModel($attr);
	}
}
?>