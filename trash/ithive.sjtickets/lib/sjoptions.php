<?php
namespace ITHive\SJTickets;

use Bitrix\Main\Loader;

class SJOptions
{
    private static $_instance = null;
    private $moduleId;

    private function __construct()
    {
        Loader::includeModule('main');
        $this->moduleId = GetModuleID(__FILE__);
    }

    protected function __clone()
    {
        // TODO: Implement __clone() method.
    }

    static public function getInstance()
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function isReady()
    {
        $api = $this->getApiToken();
        $user = $this->getSupportSJUser();
        $sourceId = $this->getTicketMessSourceId();

        if(empty($api) || empty($user) || empty($sourceId))
            return false;
        else
            return true;
    }

    public function getApiToken()
    {
        return \COption::GetOptionString($this->moduleId, 'api_token');
    }

    public function getSupportSJUser()
    {
        return (int)\COption::GetOptionString($this->moduleId, 'sj_support_user');
    }

    public function getTicketMessSourceId()
    {
        return (int)\COption::GetOptionString($this->moduleId, 'mess_source');
    }
}