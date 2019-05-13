<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    ITHive\IBoard\Boards,
    ITHive\IBoard\Tags,
    ITHive\IBoard\IdeasTags,
    ITHive\IBoard\History,
    ITHive\IBoard\IdeasFiles,
    ITHive\IBoard\Categories,
    ITHive\IBoard\Ideas,
    ITHive\IBoard\IdeasSubscription;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("ithive.iboard")) return;

$cacheId = $USER->GetId();

//to do get from module options
$ideaForumId = COption::GetOptionInt("ithive.iboard", 'iboard_forum');

$userId = ($arParams["USER_ID"]) ? $arParams["USER_ID"] : $GLOBALS["USER"]->GetID();
$arUserBoard = Boards::getUserSystemBoard($userId, true);
$userBoardId = $arUserBoard["id"];
$ideasSort = ($_SESSION["IDEA_LIST_VIEW"] != "table") ? array("sort_list" => "asc", "id" => "desc") : array("sort_table" => "asc", "id" => "desc");

global $arrIdeasFilter;
$strIdeasFilter = $arrIdeasFilter;
$strIdeasFilter["CREATOR"] = implode("|", $arrIdeasFilter["CREATOR"]);
$strIdeasFilter["WATCHER"] = implode("|", $arrIdeasFilter["WATCHER"]);
$strIdeasFilter = implode("|", $strIdeasFilter);
//echo "<pre>STR_FILTER: ";
//print_r($strIdeasFilter);
//echo "</pre>";

$obCache = new CPHPCache();
$cacheLifetime = $arParams["CACHE_TIME"];
$cacheID = 'idea_list/' . $USER->GetId() . $strIdeasFilter;
$cachePath = "/idea_list";

if ($obCache->InitCache($cacheLifetime, $cacheID, '/' . $cachePath)) {
    $arResult = $obCache->GetVars();
} else {
    $arCategoriesIds = array();
    $arOriginalIds = array();
    $arUsersIds = array($userId);

    $arUserIdeasFilter = array("user_id" => $userId, "active" => 1, "board_id" => $userBoardId);
    /*get ideas id by filter*/
    if (isset($arrIdeasFilter) and count($arrIdeasFilter) > 0) {
        $arIdeasIds = Ideas::getIdeasIdsByFilter($arrIdeasFilter);
        $arUserIdeasFilter["id"] = (count($arIdeasIds) > 0) ? $arIdeasIds : -1;
    }
    $arUserIdeasSelect = array("id", "origin_id", "sort_list", "sort_table", "min_list", "min_table", "name", "description", "category_id", "important", "date_create", "date_update", "date_visit", "user_id");
    $arIdeas = Ideas::getList($arUserIdeasFilter, $arUserIdeasSelect, false, "id", $ideasSort);
    $arUserIdeasIds = array_keys($arIdeas);

    /*get ideas categories info*/
    $arIdeasCategoriesFilter = array("user_id" => $userId);
    $arIdeasCategoriesSelect = array("id", "name", "color", "system");
    $arIdeasCategories = Categories::getList($arIdeasCategoriesFilter, $arIdeasCategoriesSelect, false, "id", array("sort" => "asc", "id" => "desc"));

    if (count($arUserIdeasIds) > 0) {
        foreach ($arIdeas as &$arIdea) {
            if ($arIdea["DATE_CREATE"])
                $arIdea["DATE_CREATE"] = $arIdea["DATE_CREATE"]->toString();
            if ($arIdea["DATE_UPDATE"])
                $arIdea["DATE_UPDATE"] = $arIdea["DATE_UPDATE"]->toString();
            if ($arIdea["DATE_VISIT"])
                $arIdea["DATE_VISIT"] = $arIdea["DATE_VISIT"]->toString();
            $arResult["LAST_VISITES"][$arIdea["ID"]] = $arIdea["DATE_VISIT"];
            $arIdea["DETAIL_PAGE_URL"] = $arParams["SEF_FOLDER"] . str_replace("#IDEA_ID#", $arIdea["ID"], $arParams["DETAIL_PAGE_MASK"]);
            $arCategoriesIds[] = $arIdea["CATEGORY_ID"];
            if ($arIdea["ORIGIN_ID"] && !in_array($arIdea["ORIGIN_ID"], $arOriginalIds))
                $arOriginalIds[] = $arIdea["ORIGIN_ID"];
            if ($arIdea["ORIGIN_ID"])
                $arIdeasIdByOriginId[$arIdea["ORIGIN_ID"]] = $arIdea["ID"];
        }

        /*get origin ideas info*/
        if (count($arOriginalIds) > 0) {
            $arOriginIdeasFilter = array("=id" => $arOriginalIds, "active" => 1);
            $arOriginIdeasSelect = array("id", "origin_id", "name", "description", "user_id");
            $arOriginIdeas = Ideas::getList($arOriginIdeasFilter, $arOriginIdeasSelect);
            foreach ($arOriginIdeas as $arOriginIdea) {
                if (!in_array($arOriginIdea["USER_ID"], $arUsersIds))
                    $arUsersIds[] = $arOriginIdea["USER_ID"];
            }
        }

        /*get users info*/
        $dbUser = CUser::GetList(($by="id"), ($order="asc"), array("ID" => implode(" | ", $arUsersIds)), array("SELECT" => array("ID", "LAST_NAME", "NAME", "PERSONAL_PHOTO")));
        while ($arUser = $dbUser->Fetch()) {
            $arIdeaUsers[$arUser["ID"]] = array(
                "ID" => $arUser["ID"],
                "NAME" => $arUser["NAME"],
                "LAST_NAME" => $arUser["LAST_NAME"],
                "PHOTO" => \CFile::GetFileArray($arUser["PERSONAL_PHOTO"])["SRC"]
            );
        }
        unset($arUsersIds);

        /*get is user follow ideas*/
        $arIdeasFollow = IdeasSubscription::getList(array("user_id" => $userId, "idea_id" => $arUserIdeasIds), array(), false, "idea_id");

        /*get files*/
        $arAllIdeas = array_merge($arUserIdeasIds, $arOriginalIds);
        if (count($arAllIdeas) > 0)
            $arFiles = IdeasFiles::getList($arAllIdeas);

        /*get ideas tags info*/
        $arIdeasTags = IdeasTags::getListInfo($arUserIdeasIds);

        /*get tasks*/
        $arTasks = History::getList(array("idea_id" => $arUserIdeasIds, "type" => "task"), array("data", "type", "idea_id"));
        if (is_array($arTasks) && count($arTasks) > 0) {
            foreach ($arTasks as $arTask) {
                $arIdeasTasks[$arTask["IDEA_ID"]][$arTask["ID"]] = $arTask;
            }
        }

        /*get ideas comments*/
        $arForumMessages = Ideas::getIdeaComments($ideaForumId, $arAllIdeas);
        foreach ($arForumMessages as $ideaId => $arMsg) {
            $myIdeaId = (!$arIdeas[$ideaId]) ? $arIdeasIdByOriginId[$ideaId] : $ideaId;
            $lastVisit = $arIdeas[$myIdeaId]["DATE_VISIT"];
            $arIdeas[$myIdeaId]["FORUM_MSG"]["TOTAL"] = 0;
            $arIdeas[$myIdeaId]["FORUM_MSG"]["NEW"] = 0;
            foreach ($arMsg as $msgDate) {
                $arIdeas[$myIdeaId]["FORUM_MSG"]["TOTAL"]++;
                if (strtotime($msgDate) > strtotime($lastVisit))
                    $arIdeas[$myIdeaId]["FORUM_MSG"]["NEW"]++;
            }
        }

        /*compose ideas array*/
        foreach ($arIdeas as $ideaId => &$arIdea) {
            if ($arIdea["ORIGIN_ID"] > 0 && !$arOriginIdeas[$arIdea["ORIGIN_ID"]]) {
                unset($arIdeas[$ideaId]);
                continue;
            }
            /*group by categories*/
            $arIdeasCategories[$arIdea["CATEGORY_ID"]]["ELEMENTS"][] = $arIdea["ID"];
            $arIdea["FILES"] = ($arIdea["ORIGIN_ID"] > 0) ? $arFiles[$arIdea["ORIGIN_ID"]] : $arFiles[$arIdea["ID"]];
            $arIdea["TAGS"] = $arIdeasTags[$arIdea["ID"]];
            $arIdea["TASKS"] = $arIdeasTasks[$arIdea["ID"]];
            $arIdea["CREATOR"] = ($arIdea["ORIGIN_ID"] > 0) ? $arIdeaUsers[$arOriginIdeas[$arIdea["ORIGIN_ID"]]["USER_ID"]] : $arIdeaUsers[$arIdea["USER_ID"]];
            if ($arIdea["ORIGIN_ID"] > 0) {
                $arIdea["DESCRIPTION"] = $arOriginIdeas[$arIdea["ORIGIN_ID"]]["DESCRIPTION"];
                $arIdea["NAME"] = $arOriginIdeas[$arIdea["ORIGIN_ID"]]["NAME"];
                $arIdea["FORUM_MSG"] = (isset($arIdeas[$arIdea["ORIGIN_ID"]]["FORUM_MSG"])) ? $arIdeas[$arIdea["ORIGIN_ID"]]["FORUM_MSG"] : $arIdea["FORUM_MSG"];
                unset($arIdeas[$arIdea["ORIGIN_ID"]]);
            }
            $arIdea["NAME"] = $arIdea["NAME"];
            $arIdea["MIN_LIST"] = $arIdea["MIN_LIST"];
            $arIdea["MIN_TABLE"] = $arIdea["MIN_TABLE"];
            $arIdea["~DESCRIPTION"] = $arIdea["DESCRIPTION"];
            $parser = new \CTextParser();
            $arIdea["DESCRIPTION"] = $parser->convertText($arIdea["DESCRIPTION"]);
            $arIdea["IS_FOLLOW"] = ($arIdeasFollow[$arIdea["ID"]]) ? true : false;
        }

        $arResult["IDEAS"] = $arIdeas;

        unset($arOriginalIds);
        unset($arFiles);
        unset($arIdeasTags);
        unset($arOriginIdeas);
        unset($arIdeaUsers);
    }

    $arResult["CATEGORIES"] = $arIdeasCategories;
    unset($arIdeasCategories);

    $arResult["ALL_IDEAS"] = $arAllIdeas;
    $arResult["ID_BY_ORGIN"] = $arIdeasIdByOriginId;

    $arResult["FORUM"]["ID"] = $ideaForumId;
    $arResult["FORUM"]["IDEA_MESSAGES"] = $arForumMessages;

    $arResult["CREATE_LINK"] = $arParams["SEF_FOLDER"] . $arParams["CREATE_PAGE_MASK"];

    $obCache->StartDataCache();
    $GLOBALS['CACHE_MANAGER']->StartTagCache($cachePath);
    $GLOBALS['CACHE_MANAGER']->RegisterTag('idea_list');
    $GLOBALS['CACHE_MANAGER']->RegisterTag('idea_list_' . $userId);
    $GLOBALS['CACHE_MANAGER']->EndTagCache();
    $obCache->EndDataCache(
        array(
            "IDEAS" => $arResult["IDEAS"],
            "ALL_IDEAS" => $arResult["ALL_IDEAS"],
            "ID_BY_ORGIN" => $arResult["ID_BY_ORGIN"],
            "CATEGORIES" => $arResult["CATEGORIES"],
            "CREATE_LINK" => $arResult["CREATE_LINK"],
            "FORUM" => $arResult["FORUM"],
            "LAST_VISITES" => $arResult["LAST_VISITES"],
        )
    );
}
$APPLICATION->SetTitle(Loc::Getmessage("TITLE"));

$this->IncludeComponentTemplate();
