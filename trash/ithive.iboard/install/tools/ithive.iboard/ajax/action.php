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
$ideaId = isset($_REQUEST['ideaId']) && intval($_REQUEST["ideaId"]) ? intval($_REQUEST["ideaId"]) : false;
$categoryId = isset($_REQUEST['categoryId']) && intval($_REQUEST["categoryId"]) ? intval($_REQUEST["categoryId"]) : false;
$userId = isset($_REQUEST['userId']) && intval($_REQUEST["userId"]) ? intval($_REQUEST["userId"]) : $GLOBALS["USER"]->GetID();
$popupHtml = isset($_REQUEST['popupHtml']) && $_REQUEST["popupHtml"] ? $_REQUEST["popupHtml"] : false;

switch ($action) {
    case "update":
        $updateFields = $_REQUEST["updateFields"];
        $updateRes = \ITHive\IBoard\Ideas::update($ideaId, $updateFields);
        echo json_encode($updateRes);
        break;
    case "delete":
        $arIdea = \ITHive\IBoard\Ideas::getIdea($ideaId, false, array("user_id"));
        if ($arIdea["IDEA_ID"] == $ideaId)
            \ITHive\IBoard\Ideas::delete($ideaId, $arIdea["USER_ID"]);
    case "searchEntity":
        $type = $_REQUEST["params"]["type"];
        $search = $_REQUEST["value"];
        $userId = $_REQUEST["params"]["user"];
        switch ($type) {
            case "category":
                $arCategories = \ITHive\IBoard\Categories::getList(array("user_id" => $userId, "%name" => $search), array("id", "name", "color"));
                echo json_encode($arCategories);
                break;
            case "tag":
                $arTags = \ITHive\IBoard\Tags::getList(array("%name" => $search), array("id", "name", "color"));
                echo json_encode($arTags);
                break;
            default:
                break;
        }
        break;
        break;
    case "updateCategory":
        $updateFields = $_REQUEST["updateFields"];
        $categoryResUpdate = \ITHive\IBoard\Categories::update($categoryId, $updateFields);
        if (!$categoryResUpdate["ERRORS"])
            echo json_encode($categoryId);
        else
            echo json_encode(array("ERROR" => $categoryResUpdate["ERRORS"]));
        break;
    case "deleteCategory":
        $categoryResUpdate = \ITHive\IBoard\Categories::deleteWithIdeas($categoryId);
        if (!$categoryResUpdate["ERRORS"])
            echo json_encode($categoryId);
        else
            echo json_encode(array("ERROR" => $categoryResUpdate["ERRORS"]));
        break;
    case "followIdea":
        $arIdea = \ITHive\IBoard\Ideas::getIdea($ideaId, false, array("user_id"));
        if ($arIdea["IDEA_ID"]) {
            $followIdeaResult = \ITHive\IBoard\IdeasSubscription::add($arIdea["IDEA_ID"], $userId);
            if (intval($arIdea["ORIGIN_ID"]) > 0)
                $followIdeaResult = \ITHive\IBoard\IdeasSubscription::add($arIdea["ORIGIN_ID"], $userId);
            echo json_encode($followIdeaResult);
        }

        break;
    case "unfollowIdea":
        $unfollowIdeaResult = \ITHive\IBoard\IdeasSubscription::delete($ideaId);
        echo json_encode($unfollowIdeaResult);
        break;
    case "setSortByCategories":
        $arSort = $_REQUEST["arSort"];
        $sortType = $_REQUEST["sortType"];
        $setSortIdeaResult = \ITHive\IBoard\Ideas::setSortByCategories($arSort, $sortType);
        echo json_encode($setSortIdeaResult);
        break;
    case "restoreIdea":
        $restoreIdeaResult = \ITHive\IBoard\Ideas::restoreIdea($ideaId);
        echo json_encode($restoreIdeaResult);
        break;
    case "removeIdea":
        $removeIdeaResult = \ITHive\IBoard\Ideas::removeFromArchive($ideaId, $userId);
        echo json_encode($removeIdeaResult);
        break;
    case "removeIdeaActiveFilter":
        $code = $_REQUEST["propCode"];
        global $arrIdeasFilter;
        unset($_SESSION["IDEAS_FILTER"][$code]);
        unset($arrIdeasFilter[$code]);
        unset($_POST["FILTER"]);
        echo json_encode(array(true));
        break;
    case "minimizeIdea":
        $arUpdate = array("min_" . $_REQUEST["type"] => $_REQUEST["minVal"]);
        $minimizeIdeaResult = \ITHive\IBoard\Ideas::update($ideaId, $arUpdate);
        echo json_encode($minimizeIdeaResult);
        break;
    case "setCategoriesSort":
        $arSort = $_REQUEST["arSort"];
        $setCategoriesSortResult = \ITHive\IBoard\Categories::setSort($arSort);
        echo json_encode($setCategoriesSortResult);
        break;
    default:
        break;
}
