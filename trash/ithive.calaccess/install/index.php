<?
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\IO\FileNotFoundException,
    \Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);


Class ithive_calaccess extends CModule
{
    var $MODULE_ID = "ithive.calaccess";
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
            $this->MODULE_VERSION = '0.0.2';
            $this->MODULE_VERSION_DATE = "2017-12-20 00:00:00";
        }

        $this->MODULE_NAME = Loc::getMessage("ITHIVE_CALLENDAR_ACCESS_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("ITHIVE_CALLENDAR_ACCESS_MODULE_DESC");
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
        $this->InstallFiles();
        return true;
    }

    public function DoUninstall()
    {

        UnRegisterModule($this->MODULE_ID);
        $this->UnInstallFiles();

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
        return true;
    }

    public function UnInstallEvents()
    {
        return true;
    }

    public function InstallFiles($arParams = array())
    {

        CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/local/modules/ithive.calaccess/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true, true);

        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/local/modules/ithive.calaccess/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true, true);

        return true;
    }

}