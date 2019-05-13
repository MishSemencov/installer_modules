<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    ITHive\IBoard\Boards,
    ITHive\IBoard\Tags,
    ITHive\IBoard\IdeasTags,
    ITHive\IBoard\Reminders,
    ITHive\IBoard\IdeasFiles,
    ITHive\IBoard\Categories,
    ITHive\IBoard\Ideas,
    ITHive\IBoard\IdeasShared;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("ithive.iboard")) return;

$cacheId = $USER->GetId();

$userId = ($arParams["USER_ID"]) ? $arParams["USER_ID"] : $GLOBALS["USER"]->GetID();
$arUserBoard = Boards::getUserSystemBoard($userId, true);
$userBoardId = $arUserBoard["id"];
$ideasSort = array("sort_table" => "asc");

$obCache = new CPHPCache();
$cacheLifetime = $arParams["CACHE_TIME"];
$cacheID = 'idea_archive/' . $USER->GetId();
$cachePath = "/idea_archive";

if ($obCache->InitCache($cacheLifetime, $cacheID, '/' . $cachePath)) {
    $arResult = $obCache->GetVars();
} else {
    $arUsersIds = array($userId);

    $arUserIdeasFilter = array("user_id" => $userId, "active" => 0, "board_id" => $userBoardId);
    $arUserIdeasSelect = array("id", "sort_table", "name", "description", "date_create", "user_id");
    $arIdeas = Ideas::getList($arUserIdeasFilter, $arUserIdeasSelect, false, "id", $ideasSort);
    $arUserIdeasIds = array_keys($arIdeas);

    if (count($arUserIdeasIds) > 0) {
        foreach ($arIdeas as &$arIdea) {
            if ($arIdea["DATE_CREATE"])
                $arIdea["DATE_CREATE"] = $arIdea["DATE_CREATE"]->toString();
        }

        /*get files*/
        $arFiles = IdeasFiles::getList($arUserIdeasIds);

        /*get ideas tags info*/
        $arIdeasTags = IdeasTags::getListInfo($arUserIdeasIds);

        /*compose ideas array*/
        foreach ($arIdeas as $ideaId => &$arIdea) {
            $arIdea["TAGS"] = $arIdeasTags[$arIdea["ID"]];
            $arIdea["FILES"] = $arFiles[$arIdea["ID"]];
            $arIdea["~DESCRIPTION"] = $arIdea["DESCRIPTION"];
            $parser = new \CTextParser();
            $arIdea["DESCRIPTION"] = $parser->convertText($arIdea["DESCRIPTION"]);
            $arIdea["DETAIL_PAGE_URL"] = $arParams["SEF_FOLDER"] . str_replace("#IDEA_ID#", $arIdea["ID"], $arParams["DETAIL_PAGE_MASK"]);
        }

        $arResult["IDEAS"] = $arIdeas;

        unset($arFiles);
        unset($arIdeasTags);
        unset($arUserIdeasIds);

        $obCache->StartDataCache();
        $GLOBALS['CACHE_MANAGER']->StartTagCache($cachePath);
        $GLOBALS['CACHE_MANAGER']->RegisterTag('idea_archive');
        $GLOBALS['CACHE_MANAGER']->RegisterTag('idea_archive_' . $userId);
        $GLOBALS['CACHE_MANAGER']->EndTagCache();
        $obCache->EndDataCache(
            array(
                "IDEAS" => $arResult["IDEAS"],
            )
        );
    }
}
$APPLICATION->SetTitle(Loc::Getmessage("TITLE"));
$this->IncludeComponentTemplate();
