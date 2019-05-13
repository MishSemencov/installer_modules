<?
/*
 * Tags - класс для работы с таблицей тегов
*/
namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    ITHive\IBoard\Models\TagsTable,
    ITHive\IBoard\History,
    ITHive\IBoard\IdeasCommonFunctions;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Tags {
    /*
    * метод для добавления тегов в систему
     *  @$name - название
     *  @$color - цвет, при отсутствии генерируется случайным образом
    */
    public static function add($name, $color = false)
    {
        $color = (!$color) ? IdeasCommonFunctions::getRandomColor() : $color;

        $arFields = array(
            'name' => trim($name),
            'color' => $color
        );

        $result = TagsTable::add($arFields);

        if ($result->isSuccess()) {
            $arResult["ID"] = $result->getId();
//            History::add(Loc::GetMessage("TAG_ADDED", array("#TAG_ID#" => $arResult["ID"])));
        } else
            $arResult["ERRORS"] = $result->getErrorMessages();

        return $arResult;
    }

    /*
    * метод для удаления тегов из системы
     *  @$id - id тега
    */
    public static function delete($id)
    {
        $result = TagsTable::delete($id);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else
            $arResult = true;

        return $arResult;
    }

    /*
     * метод для получения списка тегов
     *  @$filter - фильтр, необязательный
     *  @$select - поля для выборки
     *  @$limit - ограничение выбираемых тегов
    */
    public static function getList($filter = false, $select, $limit = false)
    {
        $arTags = array();

        if (!in_array("id", $select))
            $select[] = "id";

        $query = new \Bitrix\Main\Entity\Query(TagsTable::getEntity());
        $query->setSelect($select);
        if ($limit)
            $query->setLimit($limit);
        if (is_array($filter) && count($filter) > 0)
            $query->setFilter($filter);
        $result = $query->exec();

        while ($dbTag = $result->fetch()) {
            foreach ($select as $fieldName) {
                $arTags[$dbTag["id"]][strtoupper($fieldName)] = $dbTag[$fieldName];
            };
        }

        return $arTags;
    }

}