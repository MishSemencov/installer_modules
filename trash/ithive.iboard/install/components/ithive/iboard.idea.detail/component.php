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

$cacheId = $USER->GetId().$arParams["IDEA_ID"];

//to do get from module options
$ideaForumId = COption::GetOptionInt("ithive.iboard", 'iboard_forum');

$ideaId = (intval($_POST["IDEA"]["ID"])) ? intval($_POST["IDEA"]["ID"]) : intval($arParams["IDEA_ID"]);
$userId = $GLOBALS["USER"]->GetID();

if ($ideaId) {
    $obCache = new CPHPCache();
    $cacheLifetime = $arParams["CACHE_TIME"];
    $cacheID = 'idea_detail/' . $USER->GetId().$arParams["IDEA_ID"];
    $cachePath = "/idea_detail";

    if ($obCache->InitCache($cacheLifetime, $cacheID, '/' . $cachePath)) {
        $arResult = $obCache->GetVars();
    } else {
        $arResult["IDEA"] = Ideas::getIdeaById($ideaId, $userId);
        $arResult["IDEA"]["FORUM"]["ID"] = $ideaForumId;

        $obCache->StartDataCache();
        $GLOBALS['CACHE_MANAGER']->StartTagCache($cachePath);
        $GLOBALS['CACHE_MANAGER']->RegisterTag('idea_detail');
        $GLOBALS['CACHE_MANAGER']->RegisterTag('idea_detail_'.$ideaId);
        $GLOBALS['CACHE_MANAGER']->EndTagCache();
        $obCache->EndDataCache(
            array(
                "IDEA" => $arResult["IDEA"]
            )
        );
    }

    if ($_POST["IDEA"]["UPDATE"] == "Y") {
        $isCreator = ($arResult["IDEA"]["CREATOR"]["ID"] == $GLOBALS["USER"]->GetID()) ? true : false;
        $arUserBoard = Boards::getUserSystemBoard($userId, true);
        $userBoardId = $arUserBoard["id"];

        $arIdeaUpdate = array();

        /*is need update important*/
        $importantUpdate = $_POST["IDEA"]["IMPORTANT"];
        if ($importantUpdate["NEED_UPDATE"] == "Y")
            $arIdeaUpdate["important"] = $importantUpdate["VALUE"];

        if ($isCreator) {
            /*is need name update*/
            if ($arResult["IDEA"]["NAME"] != $_POST["IDEA"]["NAME"] && $_POST["IDEA"]["NAME"] != "") {
                $arIdeaUpdate["name"] = $_POST["IDEA"]["NAME"];
            }

            /*is need text update*/
            if ($arResult["IDEA"]["~TEXT"] != $_POST["IDEA_TEXT"]) {
                $arIdeaUpdate["description"] = $_POST["IDEA_TEXT"];
            }

            /*is need files update*/
            $arFilesIdToDelete = $arResult["IDEA"]["FILES"]["ID"];
            if (is_array($_POST["IDEA_FILES"]) && count($_POST["IDEA_FILES"])) {
                foreach ($_POST["IDEA_FILES"] as $postFileId) {
                    if (in_array($postFileId, $arFilesIdToDelete))
                        unset($arFilesIdToDelete[$postFileId]);
                    else
                        $arFilesIdToAdd[$postFileId] = $postFileId;
                };
            }
            if (is_array($arFilesIdToDelete) && count($arFilesIdToDelete)) {
                foreach ($arFilesIdToDelete as $fileId => $fileVal) {
                    $arFilesIdToDelete[$fileId] = $arResult["IDEA"]["FILES"]["IDEA_FILES_ID"][$fileId];
                }
            }
        }

        $DB->StartTransaction();
        try
        {
            if ($isCreator) {
                /*delete idea files*/
                foreach ($arFilesIdToDelete as $fileId) {
                    $fileRemoveResult = IdeasFiles::delete($fileId);
                    if (!empty($fileRemoveResult["ERRORS"]))
                        throw new Exception($fileRemoveResult["ERRORS"]);
                }

                /*add idea files*/
                foreach ($arFilesIdToAdd as $fileId) {
                    $fileAddResult = IdeasFiles::add($fileId, $ideaId);
                    if (!empty($fileAddResult["ERRORS"]))
                        throw new Exception($fileAddResult["ERRORS"]);
                }
            }

            /*is need update category*/
            $arCategoryUpdate = $_POST["IDEA"]["CATEGORY"];
            if ($arCategoryUpdate["NEED_UPDATE"] == "Y") {
                $categoryId = $arCategoryUpdate["ID"];
                if ($arCategoryUpdate["NEW"] == "Y") {
                    $categoryAddResult = Categories::add($arCategoryUpdate["VALUE"], false, $userBoardId, $userId);
                    if(!empty($categoryAddResult["ERRORS"]))
                        throw new Exception($categoryAddResult["ERRORS"]);
                    if (intval($categoryAddResult["id"]) > 0)
                        $categoryId = $categoryAddResult["id"];
                }
                $arIdeaUpdate["category_id"] = $categoryId;
            }

            /*is need update tags*/
            $arTagsUpdate = $_POST["IDEA"]["TAGS"];
            if (is_array($arTagsUpdate) && count($arTagsUpdate) > 0) {
                foreach ($arTagsUpdate as $arTag) {
                    $tagId = $arTag["ID"];
                    $ideaTagId = $arTag["IDEA_TAG_ID"];
                    if ($arTag["NEED_DELETE"] == "Y") {
                        $tagDeleteResult = IdeasTags::delete($ideaTagId);
                        if(!empty($tagDeleteResult["ERRORS"]))
                            throw new Exception($tagDeleteResult["ERRORS"]);
                    }
                    if ($arTag["NEED_CREATE"] == "Y") {
                        $tagAddResult = Tags::add($arTag["VALUE"], $arTag["COLOR"]);
                        if(!empty($tagAddResult["ERRORS"]))
                            throw new Exception($tagAddResult["ERRORS"]);
                        if (intval($tagAddResult["ID"]) > 0)
                            $tagId = $tagAddResult["ID"];
                    }
                    if ($arTag["NEED_DELETE"] != "Y" && !in_array($tagId, $arTagsIds))
                        $arTagsIds[] = $tagId;
                    if ($arTag["NEED_ADD"] == "Y") {
                        $tagAddResult = IdeasTags::add($ideaId, $tagId, $userBoardId);
                        if (!empty($tagAddResult["ERRORS"]))
                            throw new Exception($tagAddResult["ERRORS"]);
                    }
                }
            }

            /*is need update reminders*/
            $arRemindersUpdate = $_POST["IDEA"]["REMINDER"];
            if (is_array($arRemindersUpdate) && count($arRemindersUpdate) > 0) {
                foreach ($arRemindersUpdate as $arReminder) {
                    $reminderId = $arReminder["ID"];
                    $type = ($arReminder["PERIOD"]) ? "period" : "date";
                    $date = ($arReminder["PERIOD"]) ? $arReminder["PERIOD"] : $arReminder["DATE"];
                    if ($arReminder["NEED_DELETE"] == "Y") {
                        $reminderDeleteResult = Reminders::delete($reminderId);
                        if(!empty($reminderDeleteResult["ERRORS"]))
                            throw new Exception($reminderDeleteResult["ERRORS"]);
                    }
                    if ($arReminder["NEED_ADD"] == "Y") {
                        $reminderAddResult = Reminders::add($ideaId, $type, $date, $arReminder["HOUR"], $arReminder["MINUTES"], $arReminder["DAY"]);
                        if(!empty($reminderAddResult["ERRORS"]))
                            throw new Exception($reminderAddResult["ERRORS"]);
                        if (intval($reminderAddResult["ID"]) > 0)
                            $reminderId = $reminderAddResult["ID"];
                    }
                }
            }

            if ($isCreator) {
                /*is need watchers update*/
                $arCurrentWatchers = $arResult["IDEA"]["WATCHERS"];
                $arWatchersUpdate = $_POST["IDEA"]["WATCHERS"];

                if (is_array($arWatchersUpdate) && count($arWatchersUpdate) > 0) {
                    foreach ($arWatchersUpdate as $arWatcher) {
                        if (intval($arWatcher["ID"]) <= 0)
                            continue;
                        if (!array_key_exists($arWatcher["ID"], $arCurrentWatchers)) {
                            $watcherAddResult = IdeasShared::shareIdea($ideaId, $arWatcher["ID"], $arTagsIds);
                            if (!empty($watcherAddResult["ERRORS"]))
                                throw new Exception($watcherAddResult["ERRORS"]);
                        } else {
                            unset($arCurrentWatchers[$arWatcher["ID"]]);
                        }
                    };

                    if (is_array($arCurrentWatchers) && count($arCurrentWatchers) > 0) {
                        foreach ($arCurrentWatchers as $arWatcher) {
                            $watcherDeleteResult = IdeasShared::unshareIdea($ideaId, $arWatcher["ID"]);
                            if (!empty($watcherDeleteResult["ERRORS"]))
                                throw new Exception($watcherDeleteResult["ERRORS"]);
                        }
                    }
                }
            }

            /*idea update*/
            $ideaUpdateResult = Ideas::update($ideaId, $arIdeaUpdate);
            if (!empty($ideaUpdateResult["ERRORS"]))
                throw new Exception($ideaUpdateResult["ERRORS"]);

            $DB->Commit();
            $arResult["MESSAGES"][] = Loc::Getmessage("SUCCESS_UPDATED");
//            LocalRedirect();
        }

        catch( Exception $ex )
        {
            $DB->Rollback();
            $bSuccess = false;
            $arResult["ERRORS"][] = Loc::Getmessage("ERROR") . $ex->getMessage();
        }
    }
    $APPLICATION->SetTitle($arResult["IDEA"]["NAME"]);
    $this->IncludeComponentTemplate();
}
