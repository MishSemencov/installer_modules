<?

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE) die();

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\IO\FileNotFoundException;

Loc::loadMessages(__FILE__);


if(class_exists("ithive_api")) return;


class ithive_api extends CModule
{

	var $MODULE_ID = "ithive.api";
	public $MODULE_SORT = -1;
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME = 'Hive';
	public $PARTNER_URI = 'https://wehive.digital/';

	public function __construct()
	{

        try
		{
			$this->setVersion();
		}
		catch(\Exception $ex)
		{
			$this->MODULE_VERSION = '0.0.1';
			$this->MODULE_VERSION_DATE = "1970-01-01 00:00:00";
		}

		$this->MODULE_NAME = Loc::getMessage("ITHIVE_API_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("ITHIVE_API_MODULE_DESC");
	}

	private function setVersion(){
		if(!file_exists(__DIR__ . "/version.php")){
			throw new FileNotFoundException(__DIR__ . "/version.php");
		}

		$arModuleVersion = array();
		include(__DIR__ . "/version.php");

		if (!isset($arModuleVersion["VERSION"]) || !isset($arModuleVersion["VERSION_DATE"]) ) {
			throw new Exception(Loc::getMessage('MOD_ERROR_NOT_CORRECT_VERSION', array('#FILE_PATH' =>  __DIR__ . '/version.php')), 1);
		}

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
	}

	public function DoInstall() {

		RegisterModule($this->MODULE_ID);

 		return true;
	}

	public function DoUninstall() {

		UnRegisterModule($this->MODULE_ID);
		return true;
	}

	public function InstallDB() {
		return true;
	}

	public function UnInstallDB($arParams = array()) {
		return true;
	}

	public function InstallEvents()
	{

	}

	public function UnInstallEvents()
	{

	}

    public function InstallFiles()
    {
        return true;
    }

    public function UnInstallFiles()
    {
        return true;
    }

}