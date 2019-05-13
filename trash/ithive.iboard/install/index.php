<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE) die();

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\IO\FileNotFoundException;

Loc::loadMessages(__FILE__);


if (class_exists("ithive_iboard")) return;


class ithive_iboard extends CModule
{
    var $MODULE_ID = "ithive.iboard";
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

        $this->PARTNER_NAME = Loc::getMessage("ITHIVE_IBOARD_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("ITHIVE_IBOARD_PARTNER_URI");
        $this->MODULE_NAME = Loc::getMessage("ITHIVE_IBOARD_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("ITHIVE_IBOARD_MODULE_DESC");
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
        $this->InstallSefRules();
        $this->InstallDB();
        $this->InstallForum();
        $this->InstallAgent();
        $this->InstallMenuPt();
//        $this->InstallMailEvent();

        return true;
    }

    public function DoUninstall()
    {
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UnInstallSefRules();
        $this->UnInstallDB();
        $this->UnInstallForum();
        $this->UnInstallAgent();
        $this->UnInstallMenuPt();
//        $this->UnInstallMailEvent();
        UnRegisterModule($this->MODULE_ID);

        return true;
    }

    public function InstallDB()
    {
        global $DB;
        $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/db/".strtolower($DB->type)."/install.sql");
        return true;
    }

    public function UnInstallDB($arParams = array())
    {
        global $DB;
        $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/db/".strtolower($DB->type)."/uninstall.sql");
        return true;
    }

    public function InstallMenuPt()
    {
        CModule::IncludeModule('fileman');
        $menuPath =  '/.top.menu.php';
        $menuRes = CFileMan::GetMenuArray($_SERVER['DOCUMENT_ROOT'] . $menuPath);
        $menuRes['aMenuLinks'][] = array(Loc::getMessage("ITHIVE_IBOARD_MENU_PT"), '/iboard/', array(), array(), '');

        $dbSitesRes = \CSite::GetList($lby="sort", $lorder="asc");
        while ($sitesRes = $dbSitesRes->Fetch()):
            CFileMan::SaveMenu(array($sitesRes["LID"], '/.top.menu.php'), $menuRes['aMenuLinks'], $menuRes['sMenuTemplate']);
        endwhile;
    }

    public function UnInstallMenuPt()
    {
        CModule::IncludeModule('fileman');
        $menuPath =  '/.top.menu.php';
        $menuRes = CFileMan::GetMenuArray($_SERVER['DOCUMENT_ROOT'] . $menuPath);
        foreach ($menuRes["aMenuLinks"] as $key => $menuPt) {
            if ($menuPt[1] == "/iboard/")
                unset($menuRes["aMenuLinks"][$key]);
        }

        $dbSitesRes = \CSite::GetList($lby="sort", $lorder="asc");
        while ($sitesRes = $dbSitesRes->Fetch()):
            CFileMan::SaveMenu(array($sitesRes["LID"], '/.top.menu.php'), $menuRes['aMenuLinks'], $menuRes['sMenuTemplate']);
        endwhile;
    }

    public function InstallEvents()
    {
        RegisterModuleDependences("forum", "onBeforeMessageAdd", $this->MODULE_ID, '\ITHive\IBoard\IdeasCommonFunctions', "ideaMessageAdd");
        RegisterModuleDependences("forum", "onBeforeMessageDelete", $this->MODULE_ID, '\ITHive\IBoard\IdeasCommonFunctions', "ideaMessageDelete");
        return true;
    }

    public function UnInstallEvents()
    {
        UnRegisterModuleDependences("forum", "onBeforeMessageAdd", $this->MODULE_ID, '\ITHive\IBoard\IdeasCommonFunctions', "ideaMessageAdd");
        UnRegisterModuleDependences("forum", "onBeforeMessageDelete", $this->MODULE_ID, '\ITHive\IBoard\IdeasCommonFunctions', "ideaMessageDelete");
        return true;
    }

    public function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID, true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/components/", $_SERVER["DOCUMENT_ROOT"]."/local/components/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/public/iboard/", $_SERVER["DOCUMENT_ROOT"]."/iboard/", true, true);
        return true;
    }

    public function InstallSefRules()
    {
        $arUrlRewrite = array();
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/urlrewrite.php"))
        {
            include($_SERVER["DOCUMENT_ROOT"] . "/urlrewrite.php");
        }

        $arNewUrlRewrite = array(
            array(
                "CONDITION" => "#^/iboard/#",
                "RULE" => "",
                "ID" => "ithive:iboard",
                "PATH" => "/iboard/index.php",
            )
        );

        foreach ($arNewUrlRewrite as $arUrl)
        {
            if (!in_array($arUrl, $arUrlRewrite))
            {
                CUrlRewriter::Add($arUrl);
            }
        }
    }

    public function UnInstallSefRules()
    {
        $arUrlRewrite = array();
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/urlrewrite.php"))
        {
            include($_SERVER["DOCUMENT_ROOT"] . "/urlrewrite.php");
        }

        $arNewUrlRewrite = array(
            array(
                "CONDITION" => "#^/iboard/#",
                "RULE" => "",
                "ID" => "ithive:iboard",
                "PATH" => "/iboard/index.php",
            )
        );

        foreach ($arNewUrlRewrite as $arUrl)
        {
            if (in_array($arUrl, $arUrlRewrite))
            {
                CUrlRewriter::Delete($arUrl);
            }
        }
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID."/");
        DeleteDirFilesEx("/local/components/ithive/");
        DeleteDirFilesEx("/bitrix/tools/".$this->MODULE_ID."/");
        DeleteDirFilesEx("/iboard/");
        return true;
    }

    public function InstallForum()
    {
        if (\Bitrix\Main\Loader::includeModule('forum')) {
            $dbForum = \CForumNew::GetListEx(array("ID"=>"ASC")  ,array("TEXT"=>"IBOARD_IDEA"));
            if ($arForum = $dbForum->Fetch()) {
                $forumId = $arForum["ID"];
                \COption::SetOptionInt($this->MODULE_ID, "iboard_forum", $forumId);
            } else {
                $arFields = Array(
                    "NAME" => Loc::getMessage("ITHIVE_IBOARD_FORUM_NAME"),
                    "DESCRIPTION" => Loc::getMessage("ITHIVE_IBOARD_FORUM_DESC"),
                    "FORUM_GROUP_ID" => 0,
                    "GROUP_ID" => array(1 => "Y", 2 => "I"),
                    "SITES" => array(),
                    "ACTIVE" => "Y",
                    "MODERATION" => "N",
                    "INDEXATION" => "Y",
                    "SORT" => 150,
                    "ASK_GUEST_EMAIL" => "N",
                    "USE_CAPTCHA" => "N",
                    "ALLOW_HTML" => "N",
                    "ALLOW_ANCHOR" => "Y",
                    "ALLOW_BIU" => "Y",
                    "ALLOW_IMG" => "Y",
                    "ALLOW_VIDEO" => "Y",
                    "ALLOW_LIST" => "Y",
                    "ALLOW_QUOTE" => "Y",
                    "ALLOW_CODE" => "Y",
                    "ALLOW_FONT" => "Y",
                    "ALLOW_SMILES" => "Y",
                    "ALLOW_UPLOAD" => "Y",
                    "ALLOW_UPLOAD_EXT" => "",
                    "ALLOW_TOPIC_TITLED" => "N",
                    "EVENT1" => "forum"
                );

                $dbRes = \CSite::GetList($lby="sort", $lorder="asc");
                while ($res = $dbRes->Fetch()):
                    $arFields["SITES"][$res["LID"]] = "/".$res["LID"]."/forum/#FORUM_ID#/#TOPIC_ID#/";
                endwhile;

                $res = \CForumNew::Add($arFields);
                if (intVal($res) > 0):
                    \COption::SetOptionInt($this->MODULE_ID, "iboard_forum", $res);
                else:
                    $e = $GLOBALS['APPLICATION']->GetException();
                    if ($e && $str = $e->GetString()):
                        $forumError = Loc::getMessage("ITHIVE_IBOARD_ERROR") . ": " . $str;
                    else:
                        $forumError = Loc::getMessage("ITHIVE_IBOARD_UNKNOWN_ERROR");
                    endif;
                    throw new Exception($forumError, 1);
                endif;
            }

        }
    }

    public function UnInstallForum()
    {
        if (\Bitrix\Main\Loader::includeModule('forum')) {
            $forumId = COption::GetOptionInt($this->MODULE_ID, 'iboard_forum');
            \CForumNew::Delete($forumId);
        }
        COption::SetOptionInt($this->MODULE_ID, 'iboard_forum');
    }

    public function InstallAgent()
    {
        CAgent::AddAgent("ITHive\IBoard\Reminders::sendAgent();", $this->MODULE_ID, "N", 60, "", "Y");
    }

    public function UnInstallAgent()
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
    }

    /**
     * Метод создаёт почтовое событие и шаблон почтового события для отправки письма из формы быстрых объектов
     *
     * @throws \Bitrix\Main\LoaderException
     */
    public function InstallMailEvent()
    {
        \Bitrix\Main\Loader::includeModule('main');

        $eventName = 'ITHIVE_IBOARD_NEW_LETTER';

        $arLangs = ['ru', 'en'];

        foreach($arLangs as $lang){
            $et = new CEventType;
            $etId = $et->Add([
                "LID"           => $lang,
                "EVENT_NAME"    => $eventName,
                "NAME"          => Loc::getMessage("ITHIVE_IBOARD_EVENT_TYPE_NAME"),
                "DESCRIPTION"   => Loc::getMessage("ITHIVE_IBOARD_EVENT_TYPE_DESCRIPTION")
            ]);
        }

        $defSite = CSite::GetList($by = 'id', $order = 'asc', ['DEFAULT'=>'Y'])->Fetch();

        $arrEmess["ACTIVE"] = "Y";
        $arrEmess["EVENT_NAME"] = $eventName;
        $arrEmess["LID"] = $defSite['ID'];
        $arrEmess["EMAIL_FROM"] = "#EMAIL_FROM#";
        $arrEmess["BCC"] = "#EMAIL_FROM#";
        $arrEmess["EMAIL_TO"] = "#EMAIL_TO#";
        $arrEmess["SUBJECT"] = "#SUBJECT#";
        $arrEmess["BODY_TYPE"] = "html";
        $arrEmess["MESSAGE"] = "#MESSAGE#";

        $emess = new CEventMessage;
        $emessId = $emess->Add($arrEmess);

        COption::SetOptionInt($this->MODULE_ID, 'fo_mail_mess_id', $emessId);
        COption::SetOptionString($this->MODULE_ID, 'fo_mail_type_name', $eventName);

    }

    /**
     * Метод удаляет почтовое событие и шаблон почтового события для отправки письма из формы быстрых объектов
     *
     * @throws \Bitrix\Main\LoaderException
     */
    public function UnInstallMailEvent()
    {
        \Bitrix\Main\Loader::includeModule('main');
        $eventName = COption::GetOptionString($this->MODULE_ID, 'fo_mail_type_name');
        if(!empty($eventName)){
            $et = new CEventType;
            $et->Delete($eventName);
        }
        $emessId = COption::GetOptionInt($this->MODULE_ID, 'fo_mail_mess_id');
        if(!empty($emessId)){
            $emess = new CEventMessage;
            $emess->Delete($emessId);
        }


    }

}