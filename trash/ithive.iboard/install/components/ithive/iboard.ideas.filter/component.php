<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    ITHive\IBoard\Ideas;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("ithive.iboard")) return;

$userId = ($arParams["USER_ID"]) ? $arParams["USER_ID"] : $GLOBALS["USER"]->GetID();

$obCache = new CPHPCache();
$cacheLifetime = $arParams["CACHE_TIME"];
$cacheID = 'idea_filter/' . $userId;
$cachePath = "/idea_filter";

if ($obCache->InitCache($cacheLifetime, $cacheID, '/' . $cachePath)) {
    $arResult = $obCache->GetVars();
} else {
    $arResult["FILTER"] = array(
        "PROPERTIES" => array(
            "CATEGORY" => array(
                "NAME" => Loc::Getmessage("CATEGORY_TITLE"),
                "TYPE" => "TEXT",
                "PLACEHOLDER" => Loc::Getmessage("CATEGORY_PLACEHOLDER")
            ),
            "IMPORTANT" => array(
                "NAME" => Loc::Getmessage("IMPORTANT_TITLE"),
                "TYPE" => "SELECT",
                "VALUES" => array(
                    Loc::Getmessage("IMPORTANT_IDEA") => "1",
                    Loc::Getmessage("NOT_IMPORTANT_IDEA") => "0"
                ),
                "PLACEHOLDER" => Loc::Getmessage("IMPORTANT_PLACEHOLDER")
            ),
            "TAG" => array(
                "NAME" => Loc::Getmessage("TAG_TITLE"),
                "TYPE" => "TEXT",
                "PLACEHOLDER" => Loc::Getmessage("TAG_PLACEHOLDER")
            ),
            "DATE_CREATE" => array(
                "NAME" => Loc::Getmessage("DATE_CREATE_TITLE"),
                "TYPE" => "DATE",
                "PLACEHOLDER_FROM" => Loc::Getmessage("DATE_CREATE_FROM_PLACEHOLDER"),
                "PLACEHOLDER_TO" => Loc::Getmessage("DATE_CREATE_TO_PLACEHOLDER")
            ),
            "CREATOR" => array(
                "NAME" => Loc::Getmessage("CREATOR_TITLE"),
                "TYPE" => "USER_SELECT"
            ),
            "WATCHER" => array(
                "NAME" => Loc::Getmessage("WATCHER_TITLE"),
                "TYPE" => "USER_SELECT"
            )
        )
    );

    $obCache->StartDataCache();
    $GLOBALS['CACHE_MANAGER']->StartTagCache($cachePath);
    $GLOBALS['CACHE_MANAGER']->RegisterTag('idea_filter');
    $GLOBALS['CACHE_MANAGER']->EndTagCache();
    $obCache->EndDataCache(
        array(
            "FILTER" => $arResult["FILTER"]
        )
    );
}

global $arrIdeasFilter;
if ($_REQUEST["clear_filter"] == "Y") {
    unset($_SESSION["IDEAS_FILTER"]);
} elseif (!empty($_POST["FILTER"])) {
    $arrIdeasFilter = array(
        "USER_ID" => $userId
    );

    foreach ($_POST["FILTER"]["CREATOR"] as $arUser) {
        $arUsersIds[] = $arUser["ID"];
    }
    foreach ($_POST["FILTER"]["WATCHER"] as $arUser) {
        $arUsersIds[] = $arUser["ID"];
    }

    $dbUser = CUser::GetList(($by="id"), ($order="asc"), array("ID" => implode(" | ", $arUsersIds)), array("SELECT" => array("ID", "LAST_NAME", "NAME")));
    while ($arUser = $dbUser->Fetch()) {
        $arIdeaUsers[$arUser["ID"]] = array(
            "ID" => $arUser["ID"],
            "NAME" => $arUser["NAME"],
            "LAST_NAME" => $arUser["LAST_NAME"]
        );
    }

    foreach ($_POST["FILTER"] as $filterCode => $arFilter) {
        if ($filterCode == "IMPORTANT" && $arFilter == -1) continue;
        switch ($filterCode) {
            case "CREATOR":
            case "WATCHER":
                reset($arFilter);
                $arCurrentFilter = current($arFilter);
                $arCurrentFilter["NAME"] = $arIdeaUsers[$arCurrentFilter["ID"]]["NAME"];
                $arCurrentFilter["LAST_NAME"] = $arIdeaUsers[$arCurrentFilter["ID"]]["LAST_NAME"];
                $arCurrentFilter["DISPLAY_VALUE"] = $arCurrentFilter["NAME"] . " " . $arCurrentFilter["LAST_NAME"];
                $arFilter = $arCurrentFilter;
                break;
        }
        if (!empty($arFilter) && !is_array($arFilter) || is_array($arFilter) && !$arFilter["DISPLAY_VALUE"])
            $arrIdeasFilter[$filterCode] = $arFilter;
    }
    
    $_SESSION["IDEAS_FILTER"] = $arrIdeasFilter;
} elseif (isset($_SESSION["IDEAS_FILTER"]) && count($_SESSION["IDEAS_FILTER"]) > 0) {
    $arrIdeasFilter = $_SESSION["IDEAS_FILTER"];
}

$this->IncludeComponentTemplate();

