<?
/*
 * History - класс для работы с таблицей истории
*/
namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Loader,
    Bitrix\Main\Type,
    ITHive\IBoard\Models\HistoryTable;

class History {
    /*
     * метод для добавления записи истории
     *  @$data - текст записи
     *  @$userId - id пользователя
     *  @$ideaId - id идеи
     *  @$boardId - id доски
     *  @$type - тип, необязательный, используется для хранения информации о поставленных задачах, событиях, сообщениях в чат, письмах и постов в живой ленте
     * Значения:
            task - задача,
            event - событие,
            mail - письмо,
            chat - сообщение в чат,
            lfeed - пост в живой лент,
            другое - рассматривается как обычная запись
    */
    public static function add($data, $userId = false, $ideaId = false, $boardId = false, $type = false)
    {
        $userId = (!$userId && $ideaId)
            ? Ideas::getList(array("id" => $ideaId), array("user_id"))[$ideaId]["USER_ID"]
            : ((!$userId)
                ? $GLOBALS["USER"]->GetID()
                : $userId
            );

        $arFields = array(
            'date' => \Bitrix\Main\Type\DateTime::createFromTimestamp(time()),
            'user_id' => $userId,
            'data' => $data
        );

        if ($ideaId)
            $arFields["idea_id"] = $ideaId;

        if ($boardId)
            $arFields["board_id"] = $boardId;

        if ($type)
            $arFields["type"] = $type;

        $result = HistoryTable::add($arFields);

        if ($result->isSuccess()) {
            $arResult["ID"] = $result->getId();
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_history_".$userId);
        } else
            $arResult["ERRORS"] = $result->getErrorMessages();

        return $arResult;
    }

    /*
    * метод для удаления записей истории из системы
     *  @$id - id записи
    */
    public static function delete($id)
    {
        $userId = self::getList(array("id" => $id), array("user_id"))[$id]["USER_ID"];
        $result = HistoryTable::delete($id);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else {
            $arResult = true;
            $GLOBALS['CACHE_MANAGER']->ClearByTag("idea_history_".$userId);
        }

        return $arResult;
    }

    /*
     * метод для получения списка записей истории
     *  @$filter - фильтр, необязательный
     *  @$select - поля для выборки
     *  @$order - сортировка
     *  @$limit - ограничение выбираемых тегов
     *  @$by - по какому полю формировать массив
     *  @$nav - постраничная навигация
    */
    public static function getList($filter = false, $select, $order = false, $limit = false, $by = "id", $nav = false)
    {
        $arHistory = array();

        if (!in_array("id", $select))
            $select[] = "id";
        if (!in_array($by, $select))
            $select[] = $by;

        $query = new \Bitrix\Main\Entity\Query(HistoryTable::getEntity());
        $query->setSelect($select);
        if ($nav) {
            $query->setLimit($nav->getLimit());
            $query->setOffset($nav->getOffset());
        } elseif ($limit)
            $query->setLimit($limit);
        if ($order)
            $query->setOrder($order);
        if (is_array($filter) && count($filter) > 0)
            $query->setFilter($filter);
        $result = $query->exec();

        while ($dbHistory = $result->fetch()) {
            foreach ($select as $fieldName) {
                $arHistory[$dbHistory[$by]][strtoupper($fieldName)] = $dbHistory[$fieldName];
            };
        }

        return $arHistory;
    }

    /*
     * метод для количество записей по фильтру для формирования постраничной навигации
     *  @$filter - фильтр
    */
    public static function getHistoryPageNav($filter)
    {
        $cnt = HistoryTable::getCount($filter);
        return $cnt;
    }
}