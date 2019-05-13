<?
/*
 * Boards - класс для работы с таблицей досок идей
*/
namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc,
    ITHive\IBoard\Models\BoardsTable;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Boards{
    /*
     * метод для добавления записи истории
     *  @$name - название доски
     *  @$userId - id пользователя
     *  @$isSystem - флаг системности доски
    */
    public static function add($name, $userId = false, $isSystem = false)
    {
        $userId = (!$userId) ? $GLOBALS["USER"]->GetID() : $userId;

        $arFields = array(
            'name' => $name,
            'user_id' => $userId,
            'system' => ($isSystem) ? 1 : 0,
        );

        $result = BoardsTable::add($arFields);

        if ($result->isSuccess()) {
            $arResult["id"] = $result->getId();
            History::add(Loc::GetMessage("BOARD_ADDED", array("#BOARD_ID#" => $arResult["id"])), $userId, false, $arResult["id"]);
        } else
            $arResult["ERRORS"] = $result->getErrorMessages();

        return $arResult;
    }

    /*
    * метод для удаления доски
     *  @$id - id доски
    */
    public static function delete($id)
    {
        $result = BoardsTable::delete($id);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else
            $arResult = true;

        return $arResult;
    }

    public static function getUserSystemBoard($userId, $createIfNotExist = false)
    {
        if (intval($userId) <= 0)
            $arBoard["ERROR"] = Loc::GetMessage("USER_NOT_FOUND", array("#USER_ID#" => $userId));

        $q = new \Bitrix\Main\Entity\Query(BoardsTable::getEntity());
        $q->setSelect(array('id', 'name'));
        $q->setFilter(array('=user_id' => $userId, 'system' => 1));
        $result = $q->exec();

        if ($arBoard = $result->fetch())
            return $arBoard;
        elseif ($createIfNotExist)
            return $arBoard = self::add(Loc::GetMessage("SYSTEM_TITLE"), $userId, true);
        else
            return $arBoard["ERROR"] = Loc::GetMessage("BOARD_NOT_FOUND", array("#USER_ID#" => $userId));
    }
}