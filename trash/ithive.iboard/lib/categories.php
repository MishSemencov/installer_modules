<?
/*
 * Categories - класс для работы с таблицей категорий
*/
namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    ITHive\IBoard\Models\CategoriesTable,
    ITHive\IBoard\Ideas,
    ITHive\IBoard\History,
    ITHive\IBoard\IdeasCommonFunctions;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Categories {
    /*
     * метод для добавления категории
     *  @$name - название категории
     *  @$color - цвет категории
     *  @$boardId - id доски
     *  @$userId - id пользователя
     *  @$isSystem - системная категория
     *  @$isIncoming - категория Входящие
     *  @$sort - сортировка
    */
    public static function add($name, $color = false, $boardId, $userId = false, $isSystem = false, $isIncoming = false, $sort = 0)
    {
        $userId = (!$userId) ? $GLOBALS["USER"]->GetID() : $userId;
        $color = (!$color) ? IdeasCommonFunctions::getRandomColor() : $color;

        $arFields = array(
            'name' => trim($name),
            'color' => $color,
            'sort' => $sort,
            'user_id' => $userId,
            'board_id' => ($boardId) ? $boardId : 0,
            'system' => ($isSystem) ? 1 : 0,
            'incoming' => ($isIncoming) ? 1 : 0,
        );

        $result = CategoriesTable::add($arFields);

        if ($result->isSuccess()) {
            $arResult["id"] = $result->getId();
            History::add(Loc::GetMessage("CATEGORY_ADDED", array("#CATEGORY_ID#" => $arResult["id"], "#CATEGORY_NAME#" => $name)), $userId, false, $boardId);
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$userId);
        } else
            $arResult["ERRORS"] = $result->getErrorMessages();

        return $arResult;
    }

    /*
    * метод для обновления категории
     *  @$categoryId - id категории
     *  @$update - массив обновляемых полей
    */
    public static function update($categoryId, $update)
    {
        $userId = $GLOBALS["USER"]->GetID();
        $result = CategoriesTable::update($categoryId, $update);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else {
            $arResult = true;
            $categoryName = self::getList(array("id" => $categoryId), array("name"))[$categoryId]["NAME"];
            $categoryUpMsg = Loc::GetMessage("CATEGORY_UPDATED", array("#CATEGORY_NAME#" => $categoryName, "#CATEGORY_ID#" => $categoryId));
            History::add($categoryUpMsg, $userId);
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$userId);
        }

        return $arResult;
    }

    /*
    * метод для удаления категории
     *  @$id - id категории
    */
    public static function delete($id)
    {
        $userId = $GLOBALS["USER"]->GetID();
        $result = CategoriesTable::delete($id);

        if (!$result->isSuccess()) {
            $arResult["ERRORS"] = $result->getErrorMessages();
        } else {
            $arResult = true;
            $categoryName = self::getList(array("id" => $id), array("name"))[$id]["NAME"];
            $categoryDelMsg = Loc::GetMessage("CATEGORY_DELETED", array("#CATEGORY_NAME#" => $categoryName, "#CATEGORY_ID#" => $id));
            History::add($categoryDelMsg, $userId);
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$userId);
        }

        return $arResult;
    }

    /*
    * метод для удаления категории со всеми идеями
     *  @$id - id категории
     *  @$userId - id пользователя
    */
    public static function deleteWithIdeas($id, $userId = false)
    {
        $categoryDeleteRes = array();

        $userId = ($userId) ? $userId : $GLOBALS["USER"]->GetID();
        $ideasToDel = Ideas::getList(array("category_id" => $id));
        if (is_array($ideasToDel) && count($ideasToDel) > 0) {
            foreach ($ideasToDel as $ideaId => $arIdea) {
                $ideaDeleteRes = \ITHive\IBoard\Ideas::delete($ideaId, $userId);
                if ($ideaDeleteRes["ERRORS"])
                    return $ideaDeleteRes;
            }
        }
        $categoryDeleteRes = self::delete($id);

        return $categoryDeleteRes;
    }

    /*
     * метод для получения списка категорий
     *  @$filter - фильтр, необязательный
     *  @$select - поля для выборки
     *  @$limit - ограничение выбираемых категорий
     *  @$by - по какому полю формировать массив
    */
    public static function getList($filter = false, $select, $limit = false, $by = "id", $order = false)
    {
        $arCategories = array();

        if (!in_array("id", $select))
            $select[] = "id";

        $query = new \Bitrix\Main\Entity\Query(CategoriesTable::getEntity());
        $query->setSelect($select);
        if ($limit)
            $query->setLimit($limit);
        if (is_array($filter) && count($filter) > 0)
            $query->setFilter($filter);
        if ($order)
            $query->setOrder($order);
        $result = $query->exec();

        while ($dbCategory = $result->fetch()) {
            foreach ($select as $fieldName) {
                $arCategories[$dbCategory[$by]][strtoupper($fieldName)] = $dbCategory[$fieldName];
            };
        }

        return $arCategories;
    }

    /*
     * метод для получения системной категории пользователя
     *  @$userId - id пользователя
     *  @$boardId - id доски
     *  @$createIfNotExist - создать системную категорию пользователю, если не существует
    */
    public static function getUserSystemCategory($userId, $boardId, $createIfNotExist = false)
    {
        if (intval($userId) <= 0)
            $arCategory["ERROR"] = Loc::GetMessage("USER_NOT_FOUND", array("#USER_ID#" => $userId));

        if (intval($boardId) <= 0)
            $arCategory["ERROR"] = Loc::GetMessage("BOARD_NOT_FOUND", array("#BOARD_ID#" => $boardId));

        $q = new \Bitrix\Main\Entity\Query(CategoriesTable::getEntity());
        $q->setSelect(array('id', 'name'));
        $q->setFilter(array('=user_id' => $userId, 'board_id' => $boardId, 'system' => 1));
        $result = $q->exec();

        if ($arCategory = $result->fetch())
            return $arCategory;
        elseif ($createIfNotExist)
            return $arCategory = self::add(Loc::GetMessage("SYSTEM_TITLE"), false, $boardId, $userId, true);
        else
            return $arCategory["ERROR"] = Loc::GetMessage("CATEGORY_NOT_FOUND");
    }

    /*
     * метод для получения категории "Входящие" пользователя
     *  @$userId - id пользователя
     *  @$boardId - id доски
     *  @$createIfNotExist - создать категорию "Входящие" пользователю, если не существует
    */
    public static function getUserIncomingCategory($userId, $boardId, $createIfNotExist = false)
    {
        if (intval($userId) <= 0)
            $arCategory["ERROR"] = Loc::GetMessage("USER_NOT_FOUND", array("#USER_ID#" => $userId));

        if (intval($boardId) <= 0)
            $arCategory["ERROR"] = Loc::GetMessage("BOARD_NOT_FOUND", array("#BOARD_ID#" => $boardId));

        $q = new \Bitrix\Main\Entity\Query(CategoriesTable::getEntity());
        $q->setSelect(array('id', 'name'));
        $q->setFilter(array('=user_id' => $userId, 'board_id' => $boardId, 'incoming' => 1));
        $result = $q->exec();

        if ($arCategory = $result->fetch())
            return $arCategory;
        elseif ($createIfNotExist)
            return $arCategory = self::add(Loc::GetMessage("INCOMING_TITLE"), false, $boardId, $userId, false, true);
        else
            return $arCategory["ERROR"] = Loc::GetMessage("CATEGORY_NOT_FOUND");
    }

    /*
     * массовая сортировка категорий
     * $arSort - массив, где ключ - сортировка, значение - id категории
    */
    public static function setSort($arSort)
    {
        global $DB;
        $categoriesTableName = CategoriesTable::getTableName();
        $strWhen = "";

        foreach ($arSort as $sort => $categoryId) {
            $strWhen .= "WHEN id = " . $categoryId . " THEN " . $sort . " ";
        }

        if ($strWhen != "") {
            $sql = "UPDATE " . $categoriesTableName . "  SET sort = CASE "
                . $strWhen .
                "ELSE sort END;";
            $updateResult = $DB->Query($sql);

            $userId = $GLOBALS["USER"]->GetID();
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_list_".$userId);
        }
    }
}