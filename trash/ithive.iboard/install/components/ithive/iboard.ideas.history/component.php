<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    ITHive\IBoard\History;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("ithive.iboard")) return;


$userId = ($arParams["USER_ID"]) ? $arParams["USER_ID"] : $GLOBALS["USER"]->GetID();
$historyOrder = (isset($_REQUEST["sort"]) && $_REQUEST["sort"] == "asc") ? "asc" : "desc";
/*get page navigation*/
$pageSize = ($arParams["ELEMENT_PAGE_COUNT"]) ? $arParams["ELEMENT_PAGE_COUNT"] : 20;
$nav = new \Bitrix\Main\UI\PageNavigation("PAGEN");
$nav->allowAllRecords(false)
    ->setPageSize($pageSize)
    ->initFromUri();
$elementsCnt = History::getHistoryPageNav(array("user_id" => $userId));
$nav->setRecordCount($elementsCnt);
$currentPage = $nav->getCurrentPage();

$obCache = new CPHPCache();
$cacheLifetime = $arParams["CACHE_TIME"];
$cacheID = 'idea_history/' . $userId . $currentPage . $pageSize . $historyOrder;
$cachePath = "/idea_history";

if ($obCache->InitCache($cacheLifetime, $cacheID, '/' . $cachePath)) {
    $arResult = $obCache->GetVars();
} else {
    $arUser = \CUser::GetById($userId)->fetch();

    $historyFilter = array("user_id" => $userId);
    $historySelect = array("user_id", "idea_id", "date", "data", "type");
    $arResult["HISTORY"] = History::getList($historyFilter, $historySelect, array("date" => $historyOrder, "id" => $historyOrder), false, "id", $nav);

    foreach ($arResult["HISTORY"] as &$arHistory) {
        switch ($arHistory["TYPE"]) {
            case "task":
                $arHistory["DATA"] = Loc::Getmessage("IDEA_TASK_CREATED", array("#IDEA_ID#" => $arHistory["IDEA_ID"], "#TASK_ID#" => $arHistory["DATA"]));
                break;
            case "event":
                $arHistory["DATA"] = Loc::Getmessage("IDEA_EVENT_CREATED", array("#IDEA_ID#" => $arHistory["IDEA_ID"], "#EVENT_ID#" => $arHistory["DATA"]));
                break;
            default:
                break;
        }
    }

    $arResult["USER"] = array(
        "NAME" => $arUser["NAME"],
        "LAST_NAME" => $arUser["LAST_NAME"]
    );

    $obCache->StartDataCache();
    $GLOBALS['CACHE_MANAGER']->StartTagCache($cachePath);
    $GLOBALS['CACHE_MANAGER']->RegisterTag('idea_history');
    $GLOBALS['CACHE_MANAGER']->RegisterTag('idea_history_'.$userId);
    $GLOBALS['CACHE_MANAGER']->EndTagCache();
    $obCache->EndDataCache(
        array(
            "HISTORY" => $arResult["HISTORY"],
            "USER" => $arResult["USER"]
        )
    );
}

$arResult["NAVIGATION"] = $nav;

$APPLICATION->SetTitle(Loc::Getmessage("TITLE"));
$this->IncludeComponentTemplate();

