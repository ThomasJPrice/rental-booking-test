<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCron extends pjAppController
{
	public function __construct()
	{
		$this->setLayout('pjActionEmpty');
	}
	
	public function pjActionIndex()
	{
	    $feed_arr = pjFeedModel::factory()->findAll()->getData();
	    if(!empty($feed_arr))
	    {
	        include ( PJ_COMPONENTS_PATH . 'iCalEasyReader.php' );
	        foreach($feed_arr as $feed)
	        {
	            pjAppController::syncFeeds($feed['id'], false);
	        }
	    }
		return "Import reservations from feeds for properties";
	}
}
?>