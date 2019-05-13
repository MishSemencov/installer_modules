<?
/*
 * Ideas - класс для работы с таблицей идей
*/
namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    ITHive\IBoard\Models\IdeasTable,
    ITHive\IBoard\Models\CategoriesTable,
    ITHive\IBoard\IdeasTags,
    ITHive\IBoard\IdeasSubscription,
    ITHive\IBoard\Models\IdeasTagsTable,
    ITHive\IBoard\Models\RemindersTable,
    ITHive\IBoard\IdeasShared,
    ITHive\IBoard\Reminders,
    ITHive\IBoard\IdeasCommonFunctions,
    ITHive\IBoard\History;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Ideas {
    /*длина сокращенного названия идеи*/
    private static $shortNameLength = 15;

    /*
     * метод для добавления идеи
     *  @$ideaId - id идеи
     *  @$ideaText - текст идеи
     *  @$userId - id пользователя
     *  @$sortList - сортировка в виде по категориям
     *  @$sortTable - сортировка в табличном виде
     *  @$categoryId - id категории
     *  @$boardId - id доски
     *  @$important - флаг важности идеи
    */
    public static function add($ideaId = false, $ideaText = "", $userId = false, $sortList = 0, $sortTable = 0, $categoryId, $boardId, $important = 0, $name = false)
    {
        $userId = (!$userId) ? $GLOBALS["USER"]->GetID() : $userId;

        $arFields = array(
            'active' => '1',
            'user_id' => $userId,
            'sort_list' => $sortList,
            'sort_table' => $sortTable,
            'description' => trim($ideaText),
            'category_id' => $categoryId,
            'board_id' => $boardId,
            'important' => $important,
            'date_create' => \Bitrix\Main\Type\DateTime::createFromTimestamp(time()),
            'date_visit' => \Bitrix\Main\Type\DateTime::createFromTimestamp(time())
        );

        if ($ideaId) {
            $arFields["origin_id"] = $ideaId;
            $name = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
        }

        if ($name)
            $arFields["name"] = trim($name);

        $result = IdeasTable::add($arFields);

        if ($result->isSuccess()) {
            $arResult["ID"] = $result->getId();
            if (!$name) {
                $name = Loc::Getmessage("IDEA_DEFAULT_NAME", array("#IDEA_ID#" => $arResult["ID"]));
                self::update($arResult["ID"], array("name" => $name));
            }

            if (!$ideaId) {
//                $shortName = self::getShortName($arResult["ID"], $ideaText);
                $shortName = Ideas::getList(array("id" => $arResult["ID"]), array("name"))[$arResult["ID"]]["NAME"];
                $ideaAddedMsg = Loc::GetMessage("IDEA_ADDED", array("#IDEA_ID#" => $arResult["ID"], "#IDEA_NAME#" => $shortName));
                History::add($ideaAddedMsg, $userId, $arResult["ID"], $boardId);
            }
            IdeasSubscription::add($arResult["ID"], $userId);
            if ($ideaId)
                IdeasSubscription::add($ideaId, $userId);

            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_".$arResult["ID"]);
            if ($ideaId)
                $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_".$ideaId);
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$userId);
        } else
            $arResult["ERRORS"] = $result->getErrorMessages();

        return $arResult;
    }

    /*
     * метод для обновления идеи
     *  @$ideaId - id идеи
     *  @$update - массив полей для обновления
     *  @$needDateUpdate - нужна ли запись в истории и фиксации даты обновления
    */
    public static function update($ideaId, $update, $needDateUpdate = true)
    {
        $needWatcherReminders = (count($update) == 1 && $update["category_id"]) ? false : true;

        if ($needDateUpdate)
            $update["date_update"] = \Bitrix\Main\Type\DateTime::createFromTimestamp(time());
        $result = IdeasTable::update($ideaId, $update);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else {
            $arResult = true;
            if ($needDateUpdate) {
//                $shortName = self::getShortName($ideaId);
                $shortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
                $ideaUpdatedMsg = Loc::GetMessage("IDEA_UPDATED", array("#IDEA_ID#" => $ideaId, "#IDEA_NAME#" => $shortName));
                History::add($ideaUpdatedMsg, false, $ideaId);

                if ($needWatcherReminders) {
                    $arWatchers = IdeasShared::getList($ideaId);
                    if (is_array($arWatchers) && count($arWatchers) > 0) {
                        foreach ($arWatchers[$ideaId] as $arWatcher) {
                            $ideaUpdatedMsg = Loc::GetMessage("IDEA_UPDATED", array("#IDEA_ID#" => $arWatcher["SHARED_IDEA_ID"], "#IDEA_NAME#" => $shortName));
                            $notifyRes = IdeasCommonFunctions::sendReminder($arWatcher["ID"], 0, false, $ideaUpdatedMsg, $arWatcher["SHARED_IDEA_ID"]);

                            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_" . $arWatcher["SHARED_IDEA_ID"]);
                            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_" . $arWatcher["ID"]);
                        }
                    }
                }
            }
        }

        $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_".$ideaId);
        $userId = $GLOBALS["USER"]->GetID();
        $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$userId);
        $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_archive_".$userId);

        return $arResult;
    }

    /*
     * метод для установки сортировки
     *  @$arUpdate - массив вида array(sort => ideaId)
     *  @$sortType - тип сортировки - в таблице, в списке по категориям
    */
    public static function setSortByCategories($arUpdate, $sortType = "sort_list")
    {
        global $DB;
        $ideasTableName = IdeasTable::getTableName();
        $strWhen = "";

        foreach ($arUpdate as $sort => $ideaId) {
            $strWhen .= "WHEN id = " . $ideaId . " THEN " . $sort . " ";
        }

        if ($strWhen != "") {
            $sql = "UPDATE " . $ideasTableName . "  SET " . $sortType . " = CASE "
                . $strWhen .
                "ELSE " . $sortType . " END;";
            $updateResult = $DB->Query($sql);

            $userId = $GLOBALS["USER"]->GetID();
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$userId);
        }
    }

    /*
     * метод для получения списка идей
     *  @$filter - фильтр, необязательный
     *  @$select - поля для выборки
     *  @$limit - ограничение выбираемых тегов
     *  @$by - по какому полю формировать массив
     *  @$order - сортировка
    */
    public static function getList($filter = false, $select, $limit = false, $by = "id", $order = false)
    {
        $arIdeas = array();

        if (!in_array("id", $select))
            $select[] = "id";
        if (!in_array($by, $select))
            $select[] = $by;

        $query = new \Bitrix\Main\Entity\Query(IdeasTable::getEntity());
        $query->setSelect($select);
        if ($limit)
            $query->setLimit($limit);
        if (is_array($filter) && count($filter) > 0)
            $query->setFilter($filter);
        if ($order)
            $query->setOrder($order);
        $result = $query->exec();

        while ($dbIdea = $result->fetch()) {
            foreach ($select as $fieldName) {
                $arIdeas[$dbIdea[$by]][strtoupper($fieldName)] = $dbIdea[$fieldName];
            };
        }

        return $arIdeas;
    }

    /*
     * метод для получения идеи с проверкой активности, пользователя
     *  @$ideaId - id идеи
     *  @$userId - id пользователя
     *  @$select - поля для выборки
     *  @$checkActive - проверять активность
    */
    public static function getIdea($ideaId, $userId = false, $select = false, $checkActive = true)
    {
        global $DB;
        $ideasTableName = IdeasTable::getTableName();

        $sql = "
            select ideas.id as IDEA_ID, ideas.origin_id as ORIGIN_ID, ideas.category_id as CATEGORY_ID";
        if (is_array($select) && count($select) > 0) {
            foreach ($select as $selectItem) {
                $sql .= ", ideas." . $selectItem ." as " . strtoupper($selectItem);
            }
        }
        $sql .= " from " . $ideasTableName . " ideas 
            where ideas.id = " . $ideaId;
        if ($checkActive)
            $sql .= " and ideas.active = 1";
        if ($userId)
            $sql .= " and ideas.user_id = " . $userId;
        $dbResults = $DB->Query($sql);
        if ($arIdea = $dbResults->Fetch())
            return $arIdea;
        else
            return array("ERROR" => Loc::Getmessage("IDEA_NOT_FOUND"));
    }

    /*
     * метод для получения идеи с информацией по категории и автору
     *  @$ideaId - id идеи
     *  @$userId - id пользователя
    */
    public static function getIdeaInfo($ideaId, $userId = false, $checkActive = true)
    {
        global $DB;
        $ideasTableName = IdeasTable::getTableName();
        $categoriesTableName = CategoriesTable::getTableName();

        $sql = "
            select ideas.id as IDEA_ID, ideas.description as IDEA_TEXT, ideas.important as IDEA_IMPORTANT, ideas.name as IDEA_NAME,  
            " . $DB->DateToCharFunction("ideas.date_create") . " as IDEA_DATE_CREATE, " . $DB->DateToCharFunction("ideas.date_update") . " as IDEA_DATE_UPDATE, 
            ideas.category_id as IDEA_CATEGORY_ID, categories.name as CATEGORY_NAME, categories.color as CATEGORY_COLOR, 
            ideas.user_id as USER_ID, users.NAME as USER_NAME, users.LAST_NAME as USER_LAST_NAME, users.PERSONAL_PHOTO as USER_PERSONAL_PHOTO 
            from " . $ideasTableName . " ideas
            left join " . $categoriesTableName . " categories on (categories.id = ideas.category_id)
            left join b_user users on (users.ID = ideas.user_id)
            where ideas.id = " . $ideaId;
        if ($checkActive)
            $sql .= " and ideas.active = 1";
        if ($userId)
            $sql .= " and ideas.user_id = " . $userId;
        $dbResults = $DB->Query($sql);
        if ($arIdea = $dbResults->Fetch()) {
            $arIdea["~IDEA_TEXT"] = $arIdea["IDEA_TEXT"];
            $parser = new \CTextParser();
            $arIdea["IDEA_TEXT"] = $parser->convertText($arIdea["IDEA_TEXT"]);
            $arIdea["USER_PERSONAL_PHOTO"] = \CFile::GetFileArray($arIdea["USER_PERSONAL_PHOTO"])["SRC"];
            return $arIdea;
        } else
            return array("ERROR" => Loc::Getmessage("IDEA_NOT_FOUND"));
    }

    /*
     * метод для удаления идеи
     * удалеят идею наблюдателя, переносит идею создателя в архив или полностью удаляет при переданном параметре $fullDelete = true
     *  @$ideaId - id идеи
     *  @$userId - id пользователя
     *  @$fullDelete - флаг полного удаления
    */
    public static function delete($ideaId, $userId, $fullDelete = false)
    {
        $arResult = true;
        $arIdea = Ideas::getIdea($ideaId, $userId);
        $ideaOrigin = (intval($arIdea["ORIGIN_ID"]) > 0) ? false : true;
        $arUser = \CUser::GetById($userId)->fetch();

        $isUpdated = self::update($ideaId, array("active" => 0));
        if ($isUpdated && !$isUpdated["ERRORS"]) {
//            $shortName = self::getShortName($ideaId);
            $shortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
            $ideaDeletedMessage = Loc::GetMessage("IDEA_DELETED", array("#IDEA_ID#" => $ideaId, "#IDEA_NAME#" => $shortName));
            if ($ideaOrigin && !$fullDelete) {
                $arHistoryAddResult = History::add($ideaDeletedMessage, $userId, $ideaId);
                if (!empty($arHistoryAddResult["ERRORS"]))
                    $arResult["ERRORS"] = $arHistoryAddResult["ERRORS"];
            }

            $arIdeaReminders = Reminders::getList($ideaId);

            if (is_array($arIdeaReminders) && count($arIdeaReminders) > 0) {
                foreach ($arIdeaReminders[$ideaId] as $arIdeaReminder) {
                    $arRemindersDeleteResult = Reminders::delete($arIdeaReminder["REMINDER_ID"]);
                    if (!empty($arRemindersDeleteResult["ERRORS"]))
                        $arResult["ERRORS"] = $arRemindersDeleteResult["ERRORS"];
                }
            }

            $notifyRes = IdeasCommonFunctions::sendReminder($userId, 0, false, $ideaDeletedMessage, $ideaId);
            /*if creator delete idea to archive*/
            if ($ideaOrigin && !$fullDelete) {
                $arWatchers = IdeasShared::getList($ideaId);
                if (is_array($arWatchers) && count($arWatchers) > 0) {
                    foreach ($arWatchers[$ideaId] as $arWatcher) {
                        $ideaDeletedByCreatorMessage = Loc::GetMessage("IDEA_DELETED_BY_CREATOR", array("#IDEA_ID#" => $arWatcher["SHARED_IDEA_ID"], "#USER_ID#" => $arUser["NAME"] . " " . $arUser["LAST_NAME"]));
                        $notifyRes = IdeasCommonFunctions::sendReminder($arWatcher["ID"], 0, false, $ideaDeletedByCreatorMessage, $ideaId);

                        $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_".$arWatcher["SHARED_IDEA_ID"]);
                        $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$arWatcher["ID"]);
                    }
                }
            } else {
                /*if creator delete idea from archive*/
                if ($ideaOrigin) {
                    $arWatchers = IdeasShared::getList($ideaId);
                    if (is_array($arWatchers) && count($arWatchers) > 0) {
                        foreach ($arWatchers[$ideaId] as $arWatcher) {
                            Ideas::delete($arWatcher["SHARED_IDEA_ID"], $arWatcher["ID"]);
                        }
                    }

                    /*delete ideas files*/
                    $arFiles = IdeasFiles::getList($ideaId);
                    if (is_array($arFiles) && count($arFiles) > 0) {
                        foreach ($arFiles[$ideaId]["IDEA_FILES_ID"] as $fileID) {
                            IdeasFiles::delete($fileID);
                        }
                    }

                    /*delete ideas comments*/
                    $commentsDeleteResult = Ideas::deleteIdeaComments($ideaId);
                    /*if watcher delete idea*/
                } else {
                    $originIdeaSelect = array("user_id");
                    $arOriginIdea = Ideas::getIdea($arIdea["ORIGIN_ID"], false, $originIdeaSelect);

                    $arUnsharedUser = \CUser::GetById($userId)->fetch();
                    $ideaUnshareMessage = Loc::GetMessage("IDEA_UNSHARED", array("#IDEA_ID#" => $arOriginIdea["IDEA_ID"], "#USER_ID#" => $arUnsharedUser["NAME"] . " " . $arUnsharedUser["LAST_NAME"]));
                    $notifyRes = IdeasCommonFunctions::sendReminder($arOriginIdea["USER_ID"], 0, false, $ideaUnshareMessage, $arOriginIdea["IDEA_ID"]);
                    $arShared = IdeasShared::getList($arIdea["ORIGIN_ID"], false, $userId);
                    foreach ($arShared[$arIdea["ORIGIN_ID"]] as $arUser) {
                        $resSharedDelete = IdeasShared::delete($arUser["SHARED_ID"], $arOriginIdea["USER_ID"], $arUser["IDEA_ID"], $arUnsharedUser["NAME"] . " " . $arUnsharedUser["LAST_NAME"]);
                        if (!empty($resSharedDelete["ERRORS"]))
                            $arResult["ERRORS"] = $resSharedDelete["ERRORS"];
                    };
                }
                /*delete ideas history*/
                $arHistory = History::getList(array("idea_id" => $ideaId));
                foreach ($arHistory as $arRecord) {
                    $resHistoryDelete = History::delete($arRecord["ID"]);
                    if (!empty($resHistoryDelete["ERRORS"]))
                        $arResult["ERRORS"] = $resHistoryDelete["ERRORS"];
                };


                /*delete ideas tags*/
                $arTags = IdeasTags::getList(array("idea_id" => $ideaId), array("tag_id"));
                foreach ($arTags as $arTag) {
                    $resIdeasTagsDelete = IdeasTags::delete($arTag["ID"]);
                    if (!empty($resIdeasTagsDelete["ERRORS"]))
                        $arResult["ERRORS"] = $resIdeasTagsDelete["ERRORS"];
                };
                $unsubsribeResult = IdeasSubscription::delete($ideaId);
                if (empty($arResult["ERRORS"]))
                    $resIdeaDelete = Ideas::remove($ideaId);
                if (!empty($resIdeaDelete["ERRORS"]))
                    $arResult["ERRORS"] = $resIdeaDelete["ERRORS"];
                $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_".$arIdea["ORIGIN_ID"]);
                $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$userId);
                $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_archive_".$userId);
            };

            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_".$ideaId);
        } else
            $arResult["ERRORS"] = $isUpdated["ERRORS"];

        return $arResult;
    }

    /*
     * метод для удаления идеи
     *  @$ideaId - id идеи
    */
    public static function remove($id)
    {
        $result = IdeasTable::delete($id);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else {
            $arResult = true;
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_".$id);
            $userId = $GLOBALS["USER"]->GetID();
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$userId);
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_archive_".$userId);
        }

        return $arResult;
    }

    /*
     * метод для получения полной информации по id идеи
     *  @$ideaId - id идеи
     *  @$userId - id пользователя
    */
    public static function getIdeaById($ideaId, $userId)
    {
        $ideaSelect = array("important", "category_id", "active");
        $arIdea = Ideas::getIdea($ideaId, $userId, $ideaSelect, false);
        $ideaId = intval($arIdea["IDEA_ID"]);
        $ideaOriginId = (intval($arIdea["ORIGIN_ID"]) > 0) ? intval($arIdea["ORIGIN_ID"]) : $ideaId;
        $arIdeaInfo = Ideas::getIdeaInfo($ideaId,false, false);
        if (intval($arIdea["ORIGIN_ID"]) > 0) {
            $arIdeaOriginalInfo = Ideas::getIdeaInfo($ideaOriginId);
            if (count($arIdeaOriginalInfo["ERROR"]) > 0)
                return false;
        }

        $arIdeaTags = IdeasTags::getListInfo($ideaId);
        $arIdeaReminders = Reminders::getList($ideaId);
        $arIdeaFiles = IdeasFiles::getList($ideaOriginId);
        $arWatchers = IdeasShared::getList($ideaOriginId, true);
        $arFastObjects = History::getList(array("idea_id" => $ideaId, "type" => array("task", "event")), array("data", "type"));
        if (is_array($arFastObjects) && count($arFastObjects) > 0) {
            foreach ($arFastObjects as $arObject) {
                switch ($arObject["TYPE"]) {
                    case "task":
                        $arTasks[] = $arObject;
                        break;
                    case "event":
                        $arEvents[] = $arObject;
                        break;
                    default:
                        break;
                }
            }
        }

        return array(
            "ID" => $ideaId,
            "ACTIVE" => $arIdea["ACTIVE"],
            "ORIGIN_ID" => (intval($arIdea["ORIGIN_ID"]) > 0) ? $arIdea["ORIGIN_ID"] : $ideaId,
            "NAME" => (intval($arIdea["ORIGIN_ID"]) > 0) ? $arIdeaOriginalInfo["IDEA_NAME"] : $arIdeaInfo["IDEA_NAME"],
            "TEXT" => (intval($arIdea["ORIGIN_ID"]) > 0) ? $arIdeaOriginalInfo["IDEA_TEXT"] : $arIdeaInfo["IDEA_TEXT"],
            "~TEXT" => (intval($arIdea["ORIGIN_ID"]) > 0) ? $arIdeaOriginalInfo["~IDEA_TEXT"] : $arIdeaInfo["~IDEA_TEXT"],
            "DATE_CREATE" => (intval($arIdea["ORIGIN_ID"]) > 0) ? $arIdeaOriginalInfo["IDEA_DATE_CREATE"] : $arIdeaInfo["IDEA_DATE_CREATE"],
            "DATE_UPDATE" => $arIdeaInfo["IDEA_DATE_UPDATE"],
            "IMPORTANT" => ($arIdeaInfo["IDEA_IMPORTANT"] == 1) ? true : false,
            "CATEGORY" => array(
                "ID" => $arIdeaInfo["IDEA_CATEGORY_ID"],
                "NAME" => $arIdeaInfo["CATEGORY_NAME"],
                "COLOR" => $arIdeaInfo["CATEGORY_COLOR"],
            ),
            "TAGS" => $arIdeaTags[$ideaId],
            "REMINDERS" => $arIdeaReminders[$ideaId],
            "FILES" => $arIdeaFiles[$ideaOriginId],
            "WATCHERS" => $arWatchers[$ideaOriginId],
            "CREATOR" => array(
                "ID" => (intval($arIdea["ORIGIN_ID"]) > 0) ? $arIdeaOriginalInfo["USER_ID"] : $arIdeaInfo["USER_ID"],
                "NAME" => (intval($arIdea["ORIGIN_ID"]) > 0) ? $arIdeaOriginalInfo["USER_NAME"] : $arIdeaInfo["USER_NAME"],
                "LAST_NAME" => (intval($arIdea["ORIGIN_ID"]) > 0) ? $arIdeaOriginalInfo["USER_LAST_NAME"] : $arIdeaInfo["USER_LAST_NAME"],
                "PERSONAL_PHOTO" => (intval($arIdea["ORIGIN_ID"]) > 0) ? $arIdeaOriginalInfo["USER_PERSONAL_PHOTO"] : $arIdeaInfo["USER_PERSONAL_PHOTO"],
            ),
            "TASKS" => $arTasks,
            "EVENTS" => $arEvents,
        );
    }

    /*
     * метод для получения комментариев идеи
     *  @$forumId - id форума
     *  @$ideaId - массив id идей
    */
    public static function getIdeaComments($forumId, $ideaIds)
    {
        global $DB;
        $arIdeaMessages = array();

        $sql = "select ID, XML_ID, " . $DB->DateToCharFunction("POST_DATE") . " as POST_DATE 
            from b_forum_message
            where FORUM_ID = " . $forumId . " AND XML_ID in ('IDEA_" . implode("','IDEA_", $ideaIds) . "') AND AUTHOR_ID != ''";
//        AND NEW_TOPIC != 'Y'";
//            group by XML_ID";
        $dbResults = $DB->Query($sql);
        while ($arMessages = $dbResults->Fetch())
        {
            $arXml = explode("IDEA_", $arMessages["XML_ID"]);
            $ideaId = $arXml[1];
            $arIdeaMessages[$ideaId][] = $arMessages["POST_DATE"];
        }

        return $arIdeaMessages;
    }

    /*
     * метод для удаления комментариев идеи
     *  @$ideaId - id идеи
    */
    public static function deleteIdeaComments($ideaId)
    {
        global $DB;
        $sql = "delete from b_forum_message
          where XML_ID = 'IDEA_" . $ideaId . "'";
        $resDelete = $DB->Query($sql);

        return $resDelete;
    }

    /*
     * метод для проверки существования категории идеи
     *  @$categoryId - id категории
     *  @$userID - id пользователя
    */
    public static function checkIdeaCategory($categoryId, $userID)
    {
        if ($categoryId > 0)
            $arCategory = Categories::getList(array("id" => $categoryId));

        if (count($arCategory) == 0 || intval($categoryId) <= 0) {
            $arUserBoard = Boards::getUserSystemBoard($userID, true);
            $userBoardId = $arUserBoard["id"];
            $arUserDefaultCategory = Categories::getUserSystemCategory($userID, $userBoardId, true);
            $userDefaultCategoryId = $arUserDefaultCategory["id"];

            return $userDefaultCategoryId;
        } else
            return $categoryId;

    }

    /*
     * метод для восстановления идеи из архива
     *  @$ideaId - id идеи
    */
    public static function restoreIdea($ideaId)
    {
        $arUpdate = array("active" => 1);

        $arIdea = Ideas::getList(array("id" => $ideaId), array("user_id", "category_id"));
        $userID = $arIdea[$ideaId]["USER_ID"];
        $categoryID = Ideas::checkIdeaCategory($arIdea[$ideaId]["CATEGORY_ID"], $userID);

        if ($categoryID != $arIdea[$ideaId]["CATEGORY_ID"])
            $arUpdate["category_id"] = $categoryID;

        $result = Ideas::update($ideaId, $arUpdate);

        if (!$result)
            $arResult["ERRORS"] = $result->getErrorMessages();
        else {
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_".$ideaId);
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$userID);
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_archive_".$userID);
            $arResult = true;
//            $shortName = self::getShortName($ideaId);
            $shortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
            $ideaRestoredMsg = Loc::GetMessage("IDEA_RESTORED_HISTORY", array("#IDEA_ID#" => $ideaId, "#IDEA_NAME#" => $shortName));
            History::add($ideaRestoredMsg, $userID, $ideaId);
            $arWatchers = IdeasShared::getList($ideaId);
            if (is_array($arWatchers) && count($arWatchers) > 0) {
                foreach ($arWatchers[$ideaId] as $arWatcher) {
                    $ideaRestoredMsg = Loc::GetMessage("IDEA_RESTORED", array("#IDEA_URL#" => "/iboard/idea/" . $arWatcher["SHARED_IDEA_ID"] . "/"));
                    $notifyRes = IdeasCommonFunctions::sendReminder($arWatcher["ID"], 0, false, $ideaRestoredMsg, $arWatcher["SHARED_IDEA_ID"]);

                    $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_".$arWatcher["SHARED_IDEA_ID"]);
                    $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$arWatcher["ID"]);
                }
            }
        }

        return $arResult;
    }

    /*
     * метод для удаления идеи из архива
     *  @$ideaId - id идеи
     *  @$userId - id пользователя
    */
    public static function removeFromArchive($ideaId, $userId)
    {
//        $shortName = self::getShortName($ideaId);
        $shortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
        $ideaDeletedMsg = Loc::GetMessage("IDEA_DELETED", array("#IDEA_ID#" => $ideaId, "#IDEA_NAME#" => $shortName));
        $resIdeaDelete = Ideas::delete($ideaId, $userId, true);
        if (!empty($resIdeaDelete["ERRORS"]))
            $arResult["ERRORS"] = $resIdeaDelete["ERRORS"];
        else {
            $arResult = true;
            History::add($ideaDeletedMsg, $userId, $ideaId);
        }

        return $arResult;
    }

    /*
     * метод для получения короткого названия идеи
     *  @$ideaId - id идеи
    */
    public static function getShortName($ideaId = false, $text = false)
    {
        $parser = new \CTextParser();
        if (!$text && $ideaId) {
            $arIdea = self::getList(array("id" => $ideaId), array("description", "origin_id"));
            if ($arIdea[$ideaId]["ORIGIN_ID"] > 0)
                $arOriginIdea = self::getList(array("id" => $arIdea[$ideaId]["ORIGIN_ID"]), array("description"));
            $description = ($arIdea[$ideaId]["ORIGIN_ID"] > 0) ? $arOriginIdea[$arIdea[$ideaId]["ORIGIN_ID"]]["DESCRIPTION"] : $arIdea[$ideaId]["DESCRIPTION"];
            $text = $parser->convertText($description);
        } else
            $text = $parser->convertText($text);
        $shortName = htmlspecialchars_decode(strip_tags(TruncateText($text, self::$shortNameLength)));

        return $shortName;
    }

    /*
     * метод для получения id идей по фильтру
     *  @$arFilter - фильтр
    */
    public static function getIdeasIdsByFilter($arFilter)
    {
        global $DB;
        $arIdeasId = array();

        $sql = "select ideas.id 
          from ithive_iboard_ideas ideas ";
        if (!empty($arFilter["CATEGORY"]))
            $sql .= "left join ithive_iboard_categories category on category.id = ideas.category_id ";
        if (!empty($arFilter["TAG"]))
            $sql .= "left join ithive_iboard_ideas_tags itags on itags.idea_id = ideas.id  
                    left join ithive_iboard_tags tags on tags.id = itags.tag_id ";
        if (!empty($arFilter["WATCHER"]["ID"]) && $arFilter["WATCHER"]["ID"] != $arFilter["USER_ID"])
            $sql .= "left join ithive_iboard_ideas_shared shared on (shared.user_id = " . $arFilter["WATCHER"]["ID"] . " and (shared.idea_id = ideas.origin_id or shared.idea_id = ideas.id)) ";
        $sql .= "where ideas.id != '' ";
        if (!empty($arFilter["CREATOR"]["ID"]) && $arFilter["CREATOR"]["ID"] != $arFilter["USER_ID"])
            $sql .= "and ideas.origin_id in (
		        select id
		        from ithive_iboard_ideas sub_ideas
		        where sub_ideas.user_id = " . $arFilter["CREATOR"]["ID"] . "
	        ) ";
        if (!empty($arFilter["WATCHER"]["ID"]) && $arFilter["WATCHER"]["ID"] != $arFilter["USER_ID"])
            $sql .= "and shared.id is not null ";
        if (!empty($arFilter["CATEGORY"]))
            $sql .= "and upper(category.name) like upper(\"%" . $arFilter["CATEGORY"] . "%\") ";
        if (!empty($arFilter["TAG"]))
            $sql .= "and upper(tags.name) like upper('%" . $arFilter["TAG"] . "%') ";
        if (!empty($arFilter["TEXT"]))
            $sql .= "and (upper(ideas.name) like upper('%" . $arFilter["TEXT"] . "%') or upper(ideas.description) like upper('%" . $arFilter["TEXT"] . "%')) ";
        if (!empty($arFilter["IMPORTANT"]) && $arFilter["IMPORTANT"] != -1)
            $sql .= "and ideas.important = " . $arFilter["IMPORTANT"] . " ";
        if (!empty($arFilter["DATE_CREATE_FROM"]))
            $sql .= "and ideas.date_create >= " . $DB->CharToDateFunction(\Bitrix\Main\Type\DateTime::createFromUserTime($arFilter["DATE_CREATE_FROM"] . " 00:00:00")) . " ";
        if (!empty($arFilter["DATE_CREATE_TO"]))
            $sql .= "and ideas.date_create <= " . $DB->CharToDateFunction(\Bitrix\Main\Type\DateTime::createFromUserTime($arFilter["DATE_CREATE_TO"] . " 23:59:59")) . " ";
        $sql .= "group by ideas.id";

        $results = $DB->Query($sql);
        while ($arIdea = $results->Fetch()) {
            $arIdeasId[] = $arIdea["id"];
        }
        if (count($arIdeasId) > 0) {

            $ownSql = "select ideas.id 
                from ithive_iboard_ideas ideas 
                where 
                (
                    ideas.id in (" . implode(",", $arIdeasId) . ")
                    or
                    ideas.origin_id in (" . implode(",", $arIdeasId) . ")
                )
                and 
                ideas.user_id = " . $arFilter["USER_ID"] .
                " group by ideas.id";

            $arIdeasId = array();
            $ownResults = $DB->Query($ownSql);
            while ($arIdea = $ownResults->Fetch()) {
                $arIdeasId[] = $arIdea["id"];
            }
        }

        return $arIdeasId;
    }
}