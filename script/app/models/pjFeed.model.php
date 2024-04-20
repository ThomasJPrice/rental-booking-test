<?php
if (!defined("ROOT_PATH")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

class pjFeedModel extends pjAppModel
{
    protected $primaryKey = 'id';

    protected $table = 'feeds';

    protected $schema = array(
        array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'calendar_id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'provider_id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'url', 'type' => 'varchar', 'default' => ':NULL')
    );

    public static function factory($attr = array())
    {
        return new self($attr);
    }
}
?>