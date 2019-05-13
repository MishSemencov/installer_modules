<?
/*
 * IdeasCommonFunctions - класс общих функций
*/
namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    ITHive\IBoard\Ideas,
    ITHive\IBoard\IdeasShared,
    ITHive\IBoard\IdeasSubscription;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class IdeasCommonFunctions
{
    /*
    * метод для генерации случайного цвета
    */
    public static function getRandomColor()
    {
        $color = "";
        $possible = "abcdef0123456789";

        for ($i = 0; $i < 6; $i++)
            $color .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);

        return $color;
    }

    /*
    * метод для отправки уведомлений
     *  @$userTo - id пользователя для отправки уведомления
     *  @$userFrom - id пользователя - отправителя, по умолчанию отправляется от системы
     *  @$notifyType - тип уведомления, по умолчанию - системное
     *  @$notifyMessage - сообщение
     *  @$ideaId - id идеи, используется для проверки, что пользователь подписан на уведомления по идеи
    */
    public static function sendReminder($userTo, $userFrom = false, $notifyType = false, $notifyMessage, $ideaId)
    {
        $arSubscribeRes = IdeasSubscription::getList(array("user_id" => $userTo, "idea_id" => $ideaId));
        if (count($arSubscribeRes) <= 0) return;

        if (!\CModule::IncludeModule("im")) return;

        $arMessageFields = array(
            "TO_USER_ID" => $userTo,
            "FROM_USER_ID" => $userFrom ? $userFrom : 0,
            "NOTIFY_TYPE" => ($notifyType) ? $notifyType : IM_NOTIFY_SYSTEM,
            "NOTIFY_MODULE" => "ithive.iboard",
            "NOTIFY_MESSAGE" => $notifyMessage,
        );

        $notifyRes = \CIMNotify::Add($arMessageFields);

        return $notifyRes;
    }

    /*
     * метод проверяет нужно ли отправлять уведомления пользователям
     * $arFields - массив полей сообщения
     * $messageType - тип сообщения, используется для подтягивания языковой фразы уведомления
    */
    function ideaMessageCheck($arFields, $messageType)
    {
        if (substr_count($arFields["XML_ID"], "IDEA_")) {
            $commentAuthor = $arFields["AUTHOR_ID"];
            $commentAuthorInfo = \CUser::GetById($commentAuthor)->fetch();

            $ideaId = str_replace("IDEA_", "", $arFields["XML_ID"]);
            $arIdea = Ideas::getList(array("id" => $ideaId), array("user_id", "description"));
            $ideaAuthor = $arIdea[$ideaId]["USER_ID"];
            $ideaText = $arIdea[$ideaId]["DESCRIPTION"];
//        $shortName = Ideas::getShortName(false, $ideaText);
            $shortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];

            $ideaCommentedMsg = Loc::GetMessage($messageType, array("#IDEA_ID#" => $ideaId, "#IDEA_NAME#" => $shortName, "#USER#" => $commentAuthorInfo["NAME"] . " " . $commentAuthorInfo["LAST_NAME"]));
            if ($commentAuthor != $ideaAuthor) {
                if ($commentAuthorInfo["NAME"] != "" || $commentAuthorInfo["LAST_NAME"] != "")
                    $notifyRes = self::sendReminder($ideaAuthor, 0, false, $ideaCommentedMsg, $ideaId);
            } else
                History::add($ideaCommentedMsg, $ideaAuthor, $ideaId);
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_" . $ideaId);
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_" . $ideaAuthor);

            $arWatchers = IdeasShared::getList($ideaId);
            if (is_array($arWatchers) && count($arWatchers) > 0) {
                foreach ($arWatchers[$ideaId] as $arWatcher) {
                    $ideaCommentedMsg = Loc::GetMessage($messageType, array("#IDEA_ID#" => $arWatcher["SHARED_IDEA_ID"], "#IDEA_NAME#" => $shortName, "#USER#" => $commentAuthorInfo["NAME"] . " " . $commentAuthorInfo["LAST_NAME"]));
                    if ($arWatcher["ID"] != $commentAuthor) {
                        if ($commentAuthorInfo["NAME"] != "" || $commentAuthorInfo["LAST_NAME"] != "")
                            $notifyRes = self::sendReminder($arWatcher["ID"], 0, false, $ideaCommentedMsg, $arWatcher["SHARED_IDEA_ID"]);
                    } else
                        History::add($ideaCommentedMsg, $commentAuthor, $arWatcher["SHARED_IDEA_ID"]);

                    $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_detail_" . $arWatcher["SHARED_IDEA_ID"]);
                    $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_" . $arWatcher["ID"]);
                }
            }
        }

        return $arFields;
    }

    /*
     * метод вызывается после добаления комментария к идее и, если необходимо, отправляет уведомления пользователям
     * $arFields - массив полей сообщения
    */
    function ideaMessageAdd(&$arFields)
    {
        self::ideaMessageCheck($arFields, "IDEA_COMMENTED");
    }

    /*
     * метод вызывается после удаления комментария к идее и, если необходимо, отправляет уведомления пользователям
     * $id - id сообщения
    */
    function ideaMessageDelete($id)
    {
        \Bitrix\Main\Loader::includeModule("forum");
        $arFields = \CForumMessage::GetByID($id);
        self::ideaMessageCheck($arFields, "IDEA_COMMENT_DELETED");
    }


    function getFiles($arFiles)
    {
        $arImg = array();
        if (is_array($arFiles) && count($arFiles) > 0) {
            foreach ($arFiles as $fid) {
                $arFile = \CFile::GetFileArray($fid);
                if ($arFile["WIDTH"] > 0 && $arFile["HEIGHT"] > 0) {
                    $arImg[] = array(
                        "width" => $arFile["WIDTH"],
                        "height" => $arFile["HEIGHT"],
                        "src" => $arFile["SRC"],
                    );
                }
            }
        }

        return $arImg;
    }
}