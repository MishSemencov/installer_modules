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

if (is_array($_POST) && isset($_POST["CREATE_IDEA"]) && $_POST["CREATE_IDEA"] == "Y") {
    $arResult["ERRORS_MESSAGES"] = array();

    $userId = $GLOBALS["USER"]->GetID();
    $arUserBoard = Boards::getUserSystemBoard($userId, true);
    $userBoardId = $arUserBoard["id"];
    $arUserDefaultCategory = Categories::getUserSystemCategory($userId, $userBoardId, true);
    $userDefaultCategoryId = $arUserDefaultCategory["id"];

    $ideaText = trim($_POST["IDEA_TEXT"]);
    if (!isset($_POST["IDEA_TEXT"]) || strlen($ideaText) == 0)
        $arResult["ERRORS_MESSAGES"][] = Loc::GetMessage("IDEA_MESSAGE_ERROR");

    if (count($arResult["ERRORS_MESSAGES"]) == 0) {
        /*parse tags array and create new tags, if it needed*/
        if (is_array($_POST["IDEA_TAG_ITEM"]) && count($_POST["IDEA_TAG_ITEM"])) {
            foreach ($_POST["IDEA_TAG_ITEM"] as $strTag) {
                $arStrTag = explode("#", $strTag);
                $arTag[$arStrTag[1]] = array(
                    "ID" => $arStrTag[0],
                    "NAME" => $arStrTag[1],
                    "COLOR" => $arStrTag[2],
                    "IS_NEW" => (!$arStrTag[0] && $arStrTag[3] == "new") ? "Y" : "N",
                );

                if ($arTag[$arStrTag[1]]["IS_NEW"] == "Y") {
                    $tagAddResult = Tags::add($arTag[$arStrTag[1]]["NAME"], $arTag[$arStrTag[1]]["COLOR"]);
                    if (intval($tagAddResult["ID"]) > 0)
                        $arTag[$arStrTag[1]]["ID"] = $tagAddResult["ID"];
                    elseif ($tagAddResult["ERRORS"])
                        $arResult["ERRORS_MESSAGES"][] = $tagAddResult["ERRORS"];
                    else
                        $arResult["ERRORS_MESSAGES"][] = Loc::GetMessage("ADD_TAG_ERROR");
                }
            }
        }

        /*parse categories array and create new categories, if it needed*/
        if (is_array($_POST["IDEA_CATEGORY_ITEM"]) && count($_POST["IDEA_CATEGORY_ITEM"])) {
            foreach ($_POST["IDEA_CATEGORY_ITEM"] as $strCategory) {
                $arStrCategory = explode("#", $strCategory);
                $arCategory = array(
                    "ID" => $arStrCategory[0],
                    "NAME" => $arStrCategory[1],
                    "COLOR" => $arStrCategory[2],
                    "IS_NEW" => (!$arStrCategory[0] && $arStrCategory[3] == "new") ? "Y" : "N",
                );

                if ($arCategory["IS_NEW"] == "Y") {
                    $categoryAddResult = Categories::add($arCategory["NAME"], $arCategory["COLOR"], $userBoardId, $userId);
                    if (intval($categoryAddResult["id"]) > 0)
                        $ideaCategory = $categoryAddResult["id"];
                    elseif ($categoryAddResult["ERRORS"])
                        $arResult["ERRORS_MESSAGES"][] = $categoryAddResult["ERRORS"];
                    else
                        $arResult["ERRORS_MESSAGES"][] = Loc::GetMessage("ADD_CATEGORY_ERROR");
                } else
                    $ideaCategory = $arStrCategory[0];
            }
        }

        /*parse reminders array and create new reminders, if it needed*/
        if (is_array($_POST["IDEA_REMINDER_ITEM"]) && count($_POST["IDEA_REMINDER_ITEM"])) {
            foreach ($_POST["IDEA_REMINDER_ITEM"] as $strReminder) {
                $arStrReminder = explode("#", $strReminder);
                $arTime = explode(":", $arStrReminder[2]);
                $arReminder[$arStrReminder[1]][] = array(
                    $arStrReminder[0],
                    $arTime[0],
                    $arTime[1],
                    $arTime[2],
                );
            }
        }

        /*parse watchers*/
        if (is_array($_POST["IDEA_WATCHERS"]) && count($_POST["IDEA_WATCHERS"])) {
            foreach ($_POST["IDEA_WATCHERS"] as $arWatcher) {
                if (!in_array($arWatcher["ID"], $arWatchers) && intval($arWatcher["ID"]) > 0)
                    $arWatchers[] = $arWatcher["ID"];
            }
        }
    }

    /*create idea*/
    if (count($arResult["ERRORS_MESSAGES"]) == 0) {
        $isImportant = ($_POST["IDEA_IMPORTANT"] == "on") ? 1 : 0;
        $name = (trim($_POST["IDEA_NAME"]) != "") ? trim($_POST["IDEA_NAME"]) : false;
        $ideaCategory = ($ideaCategory) ? $ideaCategory : $userDefaultCategoryId;
        $ideaAddResult = Ideas::add(false, $ideaText, $userId, 0, 0, $ideaCategory, $userBoardId, $isImportant, $name);
        $ideaId = intval($ideaAddResult["ID"]);
        if ($ideaId > 0) {
            $arResult["IDEA_ID"] = $ideaId;
            /*add idea files*/
            if (is_array($_POST["IDEA_FILES"]) && count($_POST["IDEA_FILES"]) > 0) {
                foreach ($_POST["IDEA_FILES"] as $fileId) {
                    IdeasFiles::add($fileId, $ideaId);
                }
            }

            /*add idea tags*/
            if (is_array($arTag) && count($arTag) > 0) {
                foreach ($arTag as $tagId) {
                    IdeasTags::add($ideaId, $tagId["ID"], $userBoardId);
                }
            }

            /*add idea reminders*/
            if (is_array($arReminder) && count($arReminder) > 0) {
                foreach ($arReminder as $type => $arDate) {
                    foreach ($arDate as $date) {
                        Reminders::add($ideaId, $type, $date[0], $date[1], $date[2], $date[3]);
                    }
                }
            }

            /*add idea watchers*/
            if (is_array($arWatchers) && count($arWatchers) > 0) {
                foreach ($arWatchers as $watcherId) {
                    IdeasShared::shareIdea($ideaId, $watcherId, $arTag);
                }
            }
        }
    }
}

$arParams["FORM_ID"] = ($arParams["FORM_ID"]) ? $arParams["FORM_ID"] : "idea_add_form";

/*get category if it needed*/
if (intval($_REQUEST["CID"]) > 0) {
    $arCategory = Categories::getList(array("id" => $_REQUEST["CID"]), array("name", "color"));
    if ($arCategory[$_REQUEST["CID"]])
        $arResult["CATEGORY"] = $arCategory[$_REQUEST["CID"]];
}

/*start btns array organisation*/
if ($arResult["EDIT_MODE"] != "Y") {
    $arResult["ADDITIONAL_BTN"] = array(
        "PANEL_EDITOR" => array(
            "ID" => "lhe_button_editor_" . $arParams["FORM_ID"],
            "CLASS" => "feed-add-post-form-editor-btn feed-add-post-form-btn-active",
            "ON_CLICK" => "LHEPostForm.getHandler('" . $arParams["FORM_ID"] . "').showPanelEditor();",
            "TITLE" => Loc::getMessage("PANEL_EDITOR"),
        ),
        "REMINDER" => array(
            "ID" => "lhe_button_reminder_" . $arParams["FORM_ID"],
            "CLASS" => "feed-add-post-form-but feed-add-reminder",
            "DATA_POPUP" => "idea-reminder-popup",
            "ON_CLICK" => "",
            "TITLE" => Loc::getMessage("ADD_REMINDER"),
        ),
        "TAGS" => array(
            "ID" => "lhe_button_tags_" . $arParams["FORM_ID"],
            "CLASS" => "feed-add-post-form-but feed-add-tags",
            "DATA_POPUP" => "idea-tags-popup",
            "ON_CLICK" => "",
            "TITLE" => Loc::getMessage("ADD_TAGS"),
        ),
        "CATEGORY" => array(
            "ID" => "lhe_button_category_" . $arParams["FORM_ID"],
            "CLASS" => "feed-add-post-form-but feed-add-category",
            "DATA_POPUP" => "idea-categories-popup",
            "ON_CLICK" => "",
            "TITLE" => Loc::getMessage("ADD_CATEGORY"),
        )
    );

    foreach ($arResult["ADDITIONAL_BTN"] as $arBtn) {
        $onclick = ($arBtn["ON_CLICK"] != "") ? " onclick=\"" . $arBtn["ON_CLICK"] . "\"" : "";
        $arResult["ADDITIONAL_BTN_HTML"][] = '<span' . $onclick . ' class="' . $arBtn["CLASS"] . '" id="' . $arBtn["ID"] . '" data-popup="' . $arBtn["DATA_POPUP"] . '" title="' . $arBtn["TITLE"] . '"></span>';
    }
}
/*end btns array organisation*/

/*start reminder period array*/
$arResult["REMINDER_PERIOD"] = array(
  "day" => Loc::getMessage("DAYLY"),
  "week" => Loc::getMessage("WEEKLY"),
  "month" => Loc::getMessage("MONTHLY")
);
/*end reminder period array*/

/*start check post files*/
if ($_POST["IMAGES"]) {
    $arFiles = explode("|", $_POST["IMAGES"]);
    if (is_array($arFiles) && count($arFiles) > 0) {
        foreach ($arFiles as $arFile) {
            if (strlen($arFile) == 0) continue;
            $arFile = str_replace("/upload/", "", $arFile);
            $arFilePath = explode("/", $arFile);
            $fName = $arFilePath[count($arFilePath) - 1];
            unset($arFilePath[count($arFilePath) - 1]);
            $dbFile = \CFile::GetList(array("ID" => "desc"), array("SUBDIR" => $arFilePath,"FILE_NAME" => $fName));
            if ($resFile = $dbFile->GetNext()) {
                $arResult["FILES"][] = $resFile["ID"];
            }
        }
    }
}
/*end check post files*/
$APPLICATION->SetTitle(Loc::Getmessage("TITLE"));

$this->IncludeComponentTemplate();
