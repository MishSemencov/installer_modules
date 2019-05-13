<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE) die();

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\IO\FileNotFoundException;

Loc::loadMessages(__FILE__);


if (class_exists("ithive_smarttextselection")) return;


class ithive_smarttextselection extends CModule
{
    var $MODULE_ID = "ithive.smarttextselection";
    public $MODULE_SORT = -1;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME = 'Hive';
    public $PARTNER_URI = 'https://wehive.digital';

    public function __construct()
    {
        try {
            $this->setVersion();
        } catch (\Exception $ex) {
            $this->MODULE_VERSION = '0.0.1';
            $this->MODULE_VERSION_DATE = "1970-01-01 00:00:00";
        }

        $this->PARTNER_NAME = Loc::getMessage("ITHIVE_SMARTTEXTSELECTION_PARTNER_NAME");
        $this->MODULE_NAME = Loc::getMessage("ITHIVE_SMARTTEXTSELECTION_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("ITHIVE_SMARTTEXTSELECTION_MODULE_DESC");
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
        RegisterModule($this->MODULE_ID);
        $this->InstallEvents();
        $this->InstallFiles();
        $this->InstallMailEvent();

        return true;
    }

    public function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->UnInstallMailEvent();
        UnRegisterModule($this->MODULE_ID);

        return true;
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
        RegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, '\ITHive\SmartTextSelection\Handler', "onEpilog");
        return true;
    }

    public function UnInstallEvents()
    {
        UnRegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, '\ITHive\SmartTextSelection\Handler', "onEpilog");
        return true;
    }

    public function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID, true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/css/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/css/".$this->MODULE_ID, true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/ajax/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/ajax/', true, true);
        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID);
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/css/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/css/".$this->MODULE_ID);
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/ajax/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/ajax/');
        return true;
    }

    public function InstallMailEvent()
    {
        \Bitrix\Main\Loader::includeModule('main');

        $eventName = 'ITHIVE_STS_NEW_LETTER';

        $arLangs = ['ru', 'en'];

        foreach($arLangs as $lang){
            $et = new CEventType;
            $etId = $et->Add([
                "LID"           => $lang,
                "EVENT_NAME"    => $eventName,
                "NAME"          => Loc::getMessage("ITHIVE_SMARTTEXTSELECTION_EVENT_TYPE_NAME"),
                "DESCRIPTION"   => Loc::getMessage("ITHIVE_SMARTTEXTSELECTION_EVENT_TYPE_DESCRIPTION")
            ]);
        }

        $defSite = CSite::GetList($by = 'id', $order = 'asc', ['DEFAULT'=>'Y'])->Fetch();

        $arrEmess["ACTIVE"] = "Y";
        $arrEmess["EVENT_NAME"] = "ITHIVE_STS_NEW_LETTER";
        $arrEmess["LID"] = $defSite['ID'];
        $arrEmess["EMAIL_FROM"] = "#EMAIL_FROM#";
        $arrEmess["BCC"] = "#EMAIL_FROM#";
        $arrEmess["EMAIL_TO"] = "#EMAIL_TO#";
        $arrEmess["SUBJECT"] = "#SUBJECT#";
        $arrEmess["BODY_TYPE"] = "html";
        $arrEmess["MESSAGE"] = "#MESSAGE#";

        $emess = new CEventMessage;
        $emessId = $emess->Add($arrEmess);

        COption::SetOptionInt($this->MODULE_ID, 'sts_mail_mess_id', $emessId);
        COption::SetOptionString($this->MODULE_ID, 'sts_mail_type_name', $eventName);

    }

    public function UnInstallMailEvent()
    {
        \Bitrix\Main\Loader::includeModule('main');
        $eventName = COption::GetOptionString($this->MODULE_ID, 'sts_mail_type_name');
        if(!empty($eventName)){
            $et = new CEventType;
            $et->Delete($eventName);
        }
        $emessId = COption::GetOptionInt($this->MODULE_ID, 'sts_mail_mess_id');
        if(!empty($emessId)){
            $emess = new CEventMessage;
            $emess->Delete($emessId);
        }


    }

}