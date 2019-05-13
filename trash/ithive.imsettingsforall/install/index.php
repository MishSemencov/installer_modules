<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;
use Bitrix\Sale\Internals\OrderPropsTable;

Loc::loadMessages(__FILE__);

class ithive_imsettingsforall extends CModule
{
    /** @var string */
    public $MODULE_ID = 'ithive.imsettingsforall';

    /** @var string */
    public $MODULE_VERSION;

    /** @var string */
    public $MODULE_VERSION_DATE;

    /** @var string */
    public $MODULE_NAME;

    /** @var string */
    public $MODULE_DESCRIPTION;

    /** @var string */
    public $MODULE_GROUP_RIGHTS;

    /** @var string */
    public $PARTNER_NAME;

    /** @var string */
    public $PARTNER_URI;

    /** @var string */
    public $SHOW_SUPER_ADMIN_GROUP_RIGHTS;

    /** @var string */
    public $MODULE_NAMESPACE;

    protected $moduleId;
    protected $moduleLang;

    protected $PARTNER_CODE;
    protected $MODULE_CODE;
    

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__.'/version.php';
        $this->moduleId = strtolower(basename(dirname(__DIR__)));
        $this->moduleLang = strtoupper(basename(dirname(__DIR__)));
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID = $this->moduleId;
        $this->MODULE_NAME = Loc::getMessage($this->moduleLang.'_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage($this->moduleLang.'_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage($this->moduleLang.'_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage($this->moduleLang.'_PARTNER_URI');
        $this->MODULE_NAMESPACE = 'ITHive\Im';

        $this->MODULE_GROUP_RIGHTS = 'Y';
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';

        $this->PARTNER_CODE = $this->getPartnerCodeByModuleID();
        $this->MODULE_CODE = $this->getModuleCodeByModuleID();
        
        $rsSites = CSite::GetList($by="sort", $order="desc", ['ACTIVE' => 'Y']);
        $arSite = $rsSites->Fetch();
        $this->siteId = $arSite['ID'];
    }

    /**
     * функция возвращает текущий PATH для инсталлятора
     * @param bool $notDocumentRoot
     * @return mixed|string
     */
    protected function GetPath($notDocumentRoot=false)
    {
        if($notDocumentRoot)
            return str_ireplace(\Bitrix\Main\Application::getDocumentRoot(),'',dirname(__DIR__));
        else
            return dirname(__DIR__);
    }


    /**
     * Получение кода партнера из ID модуля
     * @return string
     */
    protected function getPartnerCodeByModuleID()
    {
        $delimeterPos = strpos($this->MODULE_ID, '.');
        $pCode = substr($this->MODULE_ID, 0, $delimeterPos);

        if (!$pCode) {
            $pCode = $this->MODULE_ID;
        }

        return $pCode;
    }

    /**
     * Получение кода модуля из ID модуля
     * @return string
     */
    protected function getModuleCodeByModuleID()
    {
        $delimeterPos = strpos($this->MODULE_ID, '.') + 1;
        $mCode = substr($this->MODULE_ID, $delimeterPos);

        if (!$mCode) {
            $mCode = $this->MODULE_ID;
        }

        return $mCode;
    }

    /**
     * Проверка версии ядра системы
     *
     * @return bool
     */
    protected function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }

    public function InstallFiles()
    {
        return true;
    }

    public function UnInstallFiles()
    {
        return true;
    }

    /**
     * Установка модуля
     */
    public function DoInstall()
    {
        global $APPLICATION;
        if ($this->isVersionD7()) {
            ModuleManager::registerModule($this->MODULE_ID);
            try {
//                $this->InstallDB();
//                $this->InstallIblocks();
//                $this->InstallProps();
//                $this->InstallSalePersonTypes();
//                $this->InstallSaleOrderPropsGroups();
//                $this->InstallSaleOrderProps();
//                $this->InstallEmails();
                $this->InstallEvents();
                $this->InstallFiles();
//                $this->InstallTasks();

                $APPLICATION->IncludeAdminFile(Loc::getMessage($this->MODULE_ID.'_INSTALL_TITLE'), $this->getPath() . "/install/step.php");

            }
            catch (Exception $e) {
                ModuleManager::unRegisterModule($this->MODULE_ID);
                $APPLICATION->ThrowException(Loc::getMessage($this->MODULE_ID.'_INSTALL_ERROR'));
            }
        }
        else {
            $APPLICATION->ThrowException(Loc::getMessage($this->moduleLang."_INSTALL_ERROR_WRONG_VERSION"));
        }

    }

    /**
     * Удаление модуля
     */
    public function DoUnInstall()
    {
        global $APPLICATION;
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        if ($request->get('step') < 2) {
            $APPLICATION->IncludeAdminFile(Loc::getMessage($this->moduleLang."_UNINSTALL_TITLE"), $this->getPath()."/install/unstep1.php");
        }
        elseif($request->get('step') == 2) {
            $this->UnInstallEvents();
            $this->UnInstallFiles();
//            $this->UnInstallTasks();

//            if($request->get('savedata') != 'Y') {
//                $this->UnInstallDB();
//            }
//
//            if($request->get('saveprops') != 'Y') {
//                $this->UnInstallProps();
//            }
//
//            if($request->get('savesaleprops') != 'Y') {
//                $this->UnInstallSalePersonTypes();
//                $this->UnInstallSaleOrderPropsGroups();
//                $this->UnInstallSaleOrderProps();
//            }
            
//            if($request->get('saveiblocks') != 'Y') {
//                $this->UnInstallIblocks();
//                $this->UnInstallEmails();
//            }
            ModuleManager::unRegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(Loc::getMessage($this->moduleLang."_UNINSTALL_TITLE"), $this->getPath()."/install/unstep2.php");
        }
    }
    
    /**
     * Работа с событиями при установке модуля
     * @return bool
     */
    public function InstallEvents()
    {
        RegisterModuleDependences('main', 'OnAfterUserAdd', $this->MODULE_ID, 'ITHive\Im\SettingsForAll', 'SetDefaulNotifySettings');

        return true;
    }

    /**
     * Работа с событиями при удалении модуля
     * @return bool
     */
    public function UnInstallEvents()
    {
        UnRegisterModuleDependences('main', 'OnAfterUserAdd', $this->MODULE_ID, 'ITHive\Im\SettingsForAll', 'SetDefaulNotifySettings');
        return true;
    }


}
