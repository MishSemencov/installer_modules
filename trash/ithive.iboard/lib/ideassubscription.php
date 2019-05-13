<?
/*
 * IdeasSubscription - класс для работы с таблицей подписок на события идей
*/
namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Application,
    ITHive\IBoard\Ideas,
    ITHive\IBoard\Models\IdeasSubscriptionTable;

class IdeasSubscription {
    /*
     * метод для добавления подписки на идею
     *  @$userId - id пользователя
     *  @$ideaId - id идеи
    */
    public static function add($ideaId, $userId)
    {
        $arFields = array(
            'idea_id' => $ideaId,
            'user_id' => $userId
        );

        $result = IdeasSubscriptionTable::add($arFields);

        if ($result->isSuccess()) {
            $arResult["ID"] = $result->getId();
        } else
            $arResult["ERRORS"] = $result->getErrorMessages();

        return $arResult;
    }

    /*
    * метод для удаления подписки на идею
     *  @$ideaId - id идеи
    */
    public static function delete($ideaId)
    {
        global $DB;
        $tableName = IdeasSubscriptionTable::getTableName();

        $arIdea = Ideas::getIdea($ideaId, false, array("user_id"), false);
        if (intval($arIdea["ORIGIN_ID"]) > 0)
            $ideaIds = array(
                $arIdea["IDEA_ID"],
                $arIdea["ORIGIN_ID"],
            );
        else
            $ideaIds = $arIdea["IDEA_ID"];

        if ($arIdea["USER_ID"] && $ideaIds) {

            $sql = "delete from " . $tableName;
            if (is_array($ideaIds))
                $sql .= " where idea_id in (" . implode(",", $ideaIds) . ")";
            else
                $sql .= " where idea_id = " . $ideaIds;
            $sql .= " and user_id = " . $arIdea["USER_ID"];
            $dbResults = $DB->Query($sql);
            return $dbResults;
        }

        return false;
    }

    /*
     * метод для получения списка записей истории
     *  @$filter - фильтр, необязательный
     *  @$select - поля для выборки
     *  @$limit - ограничение выбираемых тегов
     *  @$by - по какому полю формировать массив
    */
    public static function getList($filter = false, $select, $limit = false, $by = "id")
    {
        $arSubscribe = array();

        if (!in_array("id", $select))
            $select[] = "id";
        if (!in_array($by, $select))
            $select[] = $by;

        $query = new \Bitrix\Main\Entity\Query(IdeasSubscriptionTable::getEntity());
        $query->setSelect($select);
        if ($limit)
            $query->setLimit($limit);
        if (is_array($filter) && count($filter) > 0)
            $query->setFilter($filter);
        $result = $query->exec();

        while ($dbSubscribe = $result->fetch()) {
            foreach ($select as $fieldName) {
                $arSubscribe[$dbSubscribe[$by]][strtoupper($fieldName)] = $dbSubscribe[$fieldName];
            };
        }

        return $arSubscribe;
    }
}