<?
/*
 * IdeasShared - класс для работы с таблицей информации о расшаренных идеях
*/
namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    ITHive\IBoard\Boards,
    ITHive\IBoard\Categories,
    ITHive\IBoard\Ideas,
    ITHive\IBoard\IdeasTags,
    ITHive\IBoard\Models\IdeasSharedTable;
use ITHive\IBoard\Models\IdeasTable;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class IdeasShared {
    /*
     * метод для добавления информации о расшаренной идее
     *  @$userId - id пользователя
     *  @$ideaId - id идеи
     *  @$watcherIdIdea - id расшаренной идеи
    */
    public static function add($ideaId, $userId, $watcherIdIdea)
    {
        $arFields = array(
            'idea_id' => $ideaId,
            'user_id' => $userId
        );

        $result = IdeasSharedTable::add($arFields);

        if ($result->isSuccess()) {
            $arResult["ID"] = $result->getId();
//            $sharedMessage = Loc::GetMessage("IDEA_SHARED", array("#IDEA_ID#" => $ideaId, "#USER_ID#" => $userId));
            $sharedMessage = Loc::GetMessage("IDEA_SHARED", array("#IDEA_URL#" => "/iboard/idea/" . $watcherIdIdea . "/"));
            History::add($sharedMessage, $userId, $watcherIdIdea);
            $notifyRes = IdeasCommonFunctions::sendReminder($userId, 0, false, $sharedMessage, $watcherIdIdea);
        } else
            $arResult["ERRORS"] = $result->getErrorMessages();

        return $arResult;
    }

    /*
     * метод для удаления информации о расшаренной идее
     *  @$id - id записи
     *  @$userId - id владельца идеи
     *  @$ideaId - id идеи
     *  @$watcherId - id пользователя, отписавшегося от идеи
    */
    public static function delete($id, $userId, $ideaId, $watcherId)
    {
        $result = IdeasSharedTable::delete($id);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else {
            $arResult = true;
            $arUser = \CUser::GetById($watcherId)->fetch();
//            $shortName = self::getShortName($ideaId);
            $shortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
            $unShareMessage = Loc::GetMessage("IDEA_UNSHARED", array("#IDEA_ID#" => $ideaId, "#IDEA_NAME#" => $shortName, "#USER_ID#" => $arUser["NAME"] . " " . $arUser["LAST_NAME"]));
            History::add($unShareMessage, $userId, $ideaId);
            $notifyRes = IdeasCommonFunctions::sendReminder($watcherId, 0, false, $unShareMessage, $ideaId);
        }

        return $arResult;
    }

    /*
     * метод для получения списка записей истории
     *  @$ideaId - id идеи
     *  @$needUserInfo - нужна ли информация (имя, фамилия, фото) по пользователю
     *  @$watcherId - id наблюдателя
    */
    public static function getList($ideaId, $needUserInfo = false, $watcherId = false)
    {
        global $DB;
        $arUsers = array();
        $sharedTableName = IdeasSharedTable::getTableName();
        $ideasTableName = IdeasTable::getTableName();

        $sql = "
            select shared.id as SHARED_ID, shared.idea_id as IDEA_ID, shared.user_id as ID, ideas.ID as SHARED_IDEA_ID ";
        if ($needUserInfo)
            $sql .= ", users.NAME as NAME, users.LAST_NAME as LAST_NAME, users.PERSONAL_PHOTO as PERSONAL_PHOTO ";
        $sql .= "from " . $sharedTableName . " shared ";
        if ($needUserInfo)
            $sql .= "left join b_user users on (users.ID = shared.user_id) ";

        $sql .= "left join " . $ideasTableName . " ideas on (ideas.origin_id = shared.idea_id and shared.user_id = ideas.user_id) ";
        $sql .= "where shared.idea_id = " . $ideaId . " ";
        if ($watcherId)
            $sql .= "and shared.user_id = " . $watcherId;
        $dbResults = $DB->Query($sql);
        while ($arUser = $dbResults->Fetch()) {
            if ($needUserInfo) {
                $arFileInfo = \CFile::GetFileArray($arUser["PERSONAL_PHOTO"]);
                $arUser["SRC"] = $arFileInfo["SRC"];
            }
            $arUsers[$arUser["IDEA_ID"]][$arUser["ID"]] = $arUser;
        }

        return $arUsers;
    }

    /*
     * метод для расшаривания идеи
     *  @$ideaId - id идеи
     *  @$arTagsId - список id тегов
     *  @$watcherId - id наблюдателя
    */
    public static function shareIdea($ideaId, $watcherId, $arTagsId)
    {
        $arResult = true;

        $arWatcherBoard = Boards::getUserSystemBoard($watcherId, true);
        if (empty($arWatcherBoard["ERROR"])) {
            $watcherBoardId = $arWatcherBoard["id"];
            $arWatcherDefaultCategory = Categories::getUserIncomingCategory($watcherId, $watcherBoardId, true);
            if (empty($arWatcherDefaultCategory["ERROR"])) {
                $watcherDefaultCategoryId = $arWatcherDefaultCategory["id"];
                $ideaShareResult = Ideas::add($ideaId, "", $watcherId, 0, 0, $watcherDefaultCategoryId, $watcherBoardId);
                $ideaSharedId = intval($ideaShareResult["ID"]);
                if ($ideaSharedId > 0) {
                    $ideasSharedResult = IdeasShared::add($ideaId, $watcherId, $ideaSharedId);
                    if (!empty($ideasSharedResult["ERROR"]))
                        $arResult["ERRORS"] = $ideasSharedResult["ERROR"];
                    if (is_array($arTagsId) && count($arTagsId) > 0) {
                        foreach ($arTagsId as $tagId) {
                            $ideasTagAddResult = IdeasTags::add($ideaSharedId, $tagId["ID"], $watcherBoardId);
                            if (!empty($ideasTagAddResult["ERROR"]))
                                $arResult["ERRORS"] = $ideasTagAddResult["ERROR"];
                        }
                    }
                } else
                    $arResult["ERRORS"] = $ideaShareResult["ERROR"];
            } else
                $arResult["ERRORS"] = $arWatcherDefaultCategory["ERROR"];
        } else
            $arResult["ERRORS"] = $arWatcherBoard["ERROR"];

        return $arResult;
    }

    /*
     * метод для дешаринга идеи
     *  @$ideaOriginId - id родительскойи идеи
     *  @$watcherId - id удаляемого наблюдателя
    */
    public static function unshareIdea($ideaOriginId, $watcherId)
    {
        $arResult = 1;
        $arIdea = Ideas::getList(array("user_id" => $watcherId, "origin_id" => $ideaOriginId), array(), false, "origin_id");
        $ideaId = intval($arIdea[$ideaOriginId]["ID"]);
        if ($ideaId > 0) {
            $resIdeaDelete = Ideas::delete($ideaId, $watcherId);
            if (!empty($resIdeaDelete["ERROR"]))
                $arResult["ERRORS"] = $resIdeaDelete["ERROR"];
        } else
            $arResult["ERRORS"] = Loc::Getmessage("SHARED_IDEA_NOT_FOUND", array("#IDEA_ID#" => $ideaOriginId, "#USER_ID#" => $watcherId));

        return $arResult;
    }
}