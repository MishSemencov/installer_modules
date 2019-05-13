<?
/** @global \CMain $APPLICATION */
define('STOP_STATISTICS', true);

$siteId = isset($_REQUEST['siteId']) && is_string($_REQUEST['siteId']) ? $_REQUEST['siteId'] : '';
$siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $siteId), 0, 2);
if (!empty($siteId) && is_string($siteId))
{
    define('SITE_ID', $siteId);
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
if (!\Bitrix\Main\Loader::includeModule("ithive.iboard")) return;

$action = isset($_REQUEST['action']) ? htmlspecialchars($_REQUEST["action"]) : '';
$popupHtml = isset($_REQUEST['popupHtml']) && $_REQUEST["popupHtml"] ? $_REQUEST["popupHtml"] : false;

switch ($action) {
    case "getTags":
        $existTags = ITHive\IBoard\Tags::getList(false, array("id", "name", "color"), 2);
        if ($popupHtml) {
            $popupHtml = "";
            if (is_array($existTags)) {
                $popupHtml = "<div class='idea-tags-exist'>";
                foreach ($existTags as $arTag) {
                    $popupHtml .= "<div class='idea-tag-item idea-tags-add-exist-tag' style='background-color: #" . $arTag["COLOR"] . "' data-id='" . $arTag["ID"] . "' data-name='" . $arTag["NAME"] . "' data-color='" . $arTag["COLOR"] . "'>" . $arTag["NAME"] . "</div>";
                }
                $popupHtml .= "</div>";
            }
            echo json_encode($popupHtml);
        } else
            echo json_encode($existTags);
        break;
    case "getCategories":
        $userId = $GLOBALS["USER"]->GetID();
        $userBoardId = ITHive\IBoard\Boards::getUserSystemBoard($userId);
        $existCategories = ITHive\IBoard\Categories::getList(array("user_id" => $userId, "board_id" => $userBoardId), array("id", "name", "color"), 2);
        if ($popupHtml) {
            $popupHtml = "";
            if (is_array($existCategories)) {
                $popupHtml = "<div class='idea-categories-exist'>";
                foreach ($existCategories as $arCategory) {
                    $popupHtml .= "<div class='idea-categories-item idea-categories-add-exist-category' data-id='" . $arCategory["ID"] . "' data-name='" . $arCategory["NAME"] . "' data-color='" . $arCategory["COLOR"] . "' title='" . $arCategory["NAME"] . "'><span class='idea-category-color-label' style='background-color: #" . $arCategory["COLOR"] . "'></span><span class='idea-category-exist-name'>" . $arCategory["NAME"] . "</span></div>";
                }
                $popupHtml .= "</div>";
            }
            echo json_encode($popupHtml);
        } else
            echo json_encode($existCategories);
        break;
    case "getExistItem":
        $name = htmlspecialchars($_REQUEST["name"]);
        if ($_REQUEST["type"] == "tag" && strlen($name)) {
            $existTags = ITHive\IBoard\Tags::getList(array("name" => $name), array("id", "name", "color"));
            echo json_encode(current($existTags));
        } elseif ($_REQUEST["type"] == "category" && strlen($name)) {
            $userId = $GLOBALS["USER"]->GetID();
            $userBoardId = ITHive\IBoard\Boards::getUserSystemBoard($userId);
            $existCategories = ITHive\IBoard\Categories::getList(array("user_id" => $userId, "board_id" => $userBoardId, "name" => $name), array("id", "name", "color"));
            echo json_encode(current($existCategories));
        }
        break;
    case "getFiles":
        $arFiles = $_REQUEST["fileIds"];
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
        echo json_encode($arImg);
        break;
    default:
        break;
}