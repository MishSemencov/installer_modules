<?
/*
 * IdeasFiles - класс для работы с таблицей файлов идей
*/
namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    ITHive\IBoard\Models\IdeasFilesTable;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class IdeasFiles {
    /*
     * метод для добавления файла к идее
     *  @$fileId - id файла
     *  @$ideaId - id идеи
    */
    public static function add($fileId, $ideaId)
    {
        $arFields = array(
            'file_id' => $fileId,
            'idea_id' => $ideaId
        );

        $result = IdeasFilesTable::add($arFields);

        if ($result->isSuccess())
            $arResult["ID"] = $result->getId();
        else
            $arResult["ERRORS"] = $result->getErrorMessages();

        return $arResult;
    }

    /*
    * метод для удаления файла из идеи
     *  @$id - id записи
    */
    public static function delete($id)
    {
        $result = IdeasFilesTable::delete($id);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else
            $arResult = true;

        return $arResult;
    }

    /*
     * метод для получения списка файлов по id идей
     *  @$ideaIds - id идей для получения списка файлов.
     * можно передавать, как целое число, так и массив
    */
    public static function getList($ideaIds)
    {
        global $DB;
        $arFiles = array();
        $filesTableName = IdeasFilesTable::getTableName();

        $sql = "
            select files.id as IDEA_FILE_ID, files.file_id as ID, files.idea_id as IDEA_ID 
            from " . $filesTableName . " files ";
        if (!is_array($ideaIds))
            $sql .= "where files.idea_id = " . $ideaIds;
        else
            $sql .= "where files.idea_id in(" . implode(",", $ideaIds) . ")";
        $dbResults = $DB->Query($sql);
        while ($arFile = $dbResults->Fetch()) {
            $arFileInfo = \CFile::GetFileArray($arFile["ID"]);
            $arFile["SRC"] = $arFileInfo["SRC"];
            $arFile["WIDTH"] = $arFileInfo["WIDTH"];
            $arFile["HEIGHT"] = $arFileInfo["HEIGHT"];
            $arFile["FILE_NAME"] = $arFileInfo["FILE_NAME"];
            $arFile["CONTENT_TYPE"] = $arFileInfo["CONTENT_TYPE"];
            $type = (substr_count($arFile["CONTENT_TYPE"], "image") > 0) ? "IMAGES" : "OTHERS";
            $arFiles[$arFile["IDEA_ID"]][$type][] = $arFile;
            $arFiles[$arFile["IDEA_ID"]]["ID"][$arFile["ID"]] = $arFile["ID"];
            $arFiles[$arFile["IDEA_ID"]]["IDEA_FILES_ID"][$arFile["ID"]] = $arFile["IDEA_FILE_ID"];
        }

        return $arFiles;
    }
}