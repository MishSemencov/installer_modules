<?
use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);
Loc::loadMessages(__FILE__);

$iModuleID = "ithive.calaccess";
if ($APPLICATION->GetGroupRight($iModuleID) != "D") {

    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "sort" => -2,
        "text" => GetMessage("MENU_CALLENDAR_ACCESS_TEXT"),
        "title" => GetMessage("MENU_CALLENDAR_ACCESS_TITLE"),
        "url" => "ithive_calaccess.php?lang=" . LANGUAGE_ID,
        "items_id" => "menu_ithive.calaccess"
    );
    return $aMenu;
}
?>