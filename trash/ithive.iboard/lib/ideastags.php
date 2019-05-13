<?
/*
 * IdeasTags - класс для работы с таблицей тегов идей
*/

namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    ITHive\IBoard\Models\IdeasTagsTable,
    ITHive\IBoard\Models\TagsTable,
    ITHive\IBoard\Tags,
    ITHive\IBoard\Ideas,
    ITHive\IBoard\History;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class IdeasTags {
    /*
    * метод для добавления тега к идеи
     *  @$ideaId - id идеи
     *  @$tagId - id тега
     *  @$userBoardId - id доски пользователя
    */
    public static function add($ideaId, $tagId, $userBoardId)
    {
        $arFields = array(
            'idea_id' => $ideaId,
            'tag_id' => $tagId
        );

        $result = IdeasTagsTable::add($arFields);

        if ($result->isSuccess()) {
            $arResult["ID"] = $result->getId();
            $tagName = Tags::getList(array("id" => $tagId), array("name"))[$tagId]["NAME"];
//            $ideaShortName = Ideas::getShortName($ideaId);
            $ideaShortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
            History::add(Loc::GetMessage("IDEA_TAG_ADDED", array("#IDEA_ID#" => $ideaId, "#IDEA_NAME#" => $ideaShortName, "#TAG_NAME#" => $tagName)), false, $ideaId, $userBoardId);
        } else
            $arResult["ERRORS"] = $result->getErrorMessages();

        return $arResult;
    }

    /*
     * метод для удаления тега из идеи
     *  @$id - id записи тега идеи
    */
    public static function delete($id)
    {

        $ideaTag = self::getList(array("id" => $id), array("tag_id", "idea_id"));

        $result = IdeasTagsTable::delete($id);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else {
            $arResult = true;

            $tagId = $ideaTag[$id]["TAG_ID"];
            $tagName = Tags::getList(array("id" => $tagId), array("name"))[$tagId]["NAME"];
            $ideaId = $ideaTag[$id]["IDEA_ID"];
            $ideaShortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
//            $ideaShortName = Ideas::getShortName($ideaId);
            History::add(Loc::GetMessage("IDEA_TAG_DELETED", array("#IDEA_ID#" => $ideaId, "#IDEA_NAME#" => $ideaShortName, "#TAG_NAME#" => $tagName)), false, $ideaId);
        }

        return $arResult;
    }

    /*
     * метод для получения данных о тегах по id идеи
     *  @$ideaId - id идеи
    */
    public static function getListInfo($ideaIds)
    {
        global $DB;
        $arTags = array();
        $ideasTagsTableName = IdeasTagsTable::getTableName();
        $tagsTableName = TagsTable::getTableName();

        $sql = "
            select ideas_tags.id as IDEA_TAG_ID, ideas_tags.idea_id as IDEA_ID, ideas_tags.tag_id as TAG_ID, tags.name as NAME, tags.color as COLOR 
            from " . $ideasTagsTableName . " ideas_tags 
            left join  " . $tagsTableName . " tags on (ideas_tags.tag_id = tags.id) ";
        if (!is_array($ideaIds))
            $sql .= "where ideas_tags.idea_id = " . $ideaIds;
        else
            $sql .= "where ideas_tags.idea_id in(" . implode(",", $ideaIds) . ")";
        $dbResults = $DB->Query($sql);
        while ($arTag = $dbResults->Fetch()) {
            $arTags[$arTag["IDEA_ID"]][] = $arTag;
        }

        return $arTags;
    }

    /*
     * метод для получения списка тегов, привязанных к идеям
     *  @$filter - фильтр, необязательный
     *  @$select - поля для выборки
     *  @$limit - ограничение выбираемых тегов
    */
    public static function getList($filter = false, $select, $limit = false)
    {
        $arTags = array();

        if (!in_array("id", $select))
            $select[] = "id";

        $query = new \Bitrix\Main\Entity\Query(IdeasTagsTable::getEntity());
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