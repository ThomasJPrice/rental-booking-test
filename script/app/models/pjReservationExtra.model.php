<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjReservationExtraModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'reservations_extras';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'reservation_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'qty', 'type' => 'int', 'default' => ':NULL'),
	);
	
	public static function factory($attr=array())
	{
		return new pjReservationExtraModel($attr);
	}
}
?>