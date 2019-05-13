<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE) die();

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\IO\FileNotFoundException;

Loc::loadMessages(__FILE__);


if (class_exists("itsolutionru_storage")) return;


class itsolutionru_storage extends CModule
{
    var $MODULE_ID = "itsolutionru.storage";
    public $MODULE_SORT = -1;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    var $PARTNER_NAME="IT-Solution";
    var $PARTNER_URI="https://it-solution.ru";

    public function __construct()
    {
        try {
            $this->setVersion();
        } catch (\Exception $ex) {
            $this->MODULE_VERSION = "0.0.1";
            $this->MODULE_VERSION_DATE = "1970-01-01 00:00:00";
        }
        $this->PARTNER_NAME="IT-Solution";
        $this->PARTNER_URI="https://it-solution.ru";
        $this->MODULE_NAME = "IT-Solution: дисковый менеджер";
        $this->MODULE_DESCRIPTION = "Дисковый менеджер";
    }

    private function setVersion()
    {
        if (!file_exists(__DIR__ . "/version.php")) {
            throw new FileNotFoundException(__DIR__ . "/version.php");
        }
        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        if (!isset($arModuleVersion["VERSION"]) || !isset($arModuleVersion["VERSION_DATE"])) {
            throw new Exception(Loc::getMessage('MOD_ERROR_NOT_CORRECT_VERSION', array('#FILE_PATH' => __DIR__ . '/version.php')), 1);
        }

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }

    public function DoInstall()
    {
        require_once __DIR__."/../include.php";
        RegisterModule($this->MODULE_ID);
        $this -> InstallFiles();
        $this -> InstallAgent();
        AgentClassStorage::createDefaultSettings();
        return true;
    }

    public function DoUninstall()
    {
        $this -> UnInstallFiles();
        $this -> UnInstallAgent();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    public function InstallAgent()
    {
        CAgent::AddAgent("AgentClassStorage::checkStorage();", $this->MODULE_ID, "Y", 900, "", "Y"/*, strtotime("d.m.Y H:i:s",time()+300)*/);
    }

    public function UnInstallAgent()
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
    }

    public function InstallDB()
    {

        return true;
    }

    public function UnInstallDB($arParams = array())
    {

        return true;
    }

    public function InstallEvents()
    {

        return true;
    }

    public function UnInstallEvents()
    {

        return true;
    }

    public function InstallFiles()
    {
        if(!file_exists($_SERVER['DOCUMENT_ROOT']."/local/")) mkdir($_SERVER['DOCUMENT_ROOT']."/local/",0777);
        if(!file_exists($_SERVER['DOCUMENT_ROOT']."/local/its.storage/")) mkdir($_SERVER['DOCUMENT_ROOT']."/local/its.storage/",0777);
        file_put_contents($_SERVER['DOCUMENT_ROOT']."/local/its.storage/settings.json","");
        chmod($_SERVER['DOCUMENT_ROOT']."/local/its.storage/settings.json",0777);
        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx("/local/its.storage/");
        return true;
    }

}
?>