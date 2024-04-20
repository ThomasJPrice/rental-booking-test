<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCalendarModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'calendars';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'uuid', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'y_logo', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'y_country', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'y_zip', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'y_phone', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'y_fax', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'y_email', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'y_url', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	public $i18n = array('name', 'confirm_subject', 'confirm_tokens', 'payment_subject', 'payment_tokens', 'terms_url', 'terms_body', 'y_company', 'y_name', 'y_street_address', 'y_city', 'y_state');
	
	public static function factory($attr=array())
	{
		return new pjCalendarModel($attr);
	}
	
	public function getConfigData($locale_id, $pk=1)
	{
	    $arr = $this
	    ->select("t1.*, t2.content as y_country_title")
	    ->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.y_country AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
	    ->find($pk)->getData();
	    
	    $i18n = pjMultiLangModel::factory()
	    ->where('t1.model', 'pjCalendar')
	    ->where('t1.foreign_id', $pk)
	    ->where('t1.locale', $locale_id)
	    ->whereIn('t1.field', $this->getI18n())
	    ->findAll()
	    ->getDataPair('field', 'content');
	    
	    return array_merge($arr, $i18n);
	}
}
?>