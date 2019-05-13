<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    ITHive\IBoard\Boards,
    ITHive\IBoard\Categories;


$assets = \Bitrix\Main\Page\Asset::getInstance();

$assets->addCss($componentPath.'/css/bundle.css');
$assets->addCss($componentPath.'/css/flexboxgrid.min.css');
$assets->addCss($componentPath.'/css/perfect-scrollbar.css');
$assets->addJs($componentPath.'/js/plugins/jquery-ui.min.js');
$assets->addJs($componentPath.'/js/plugins/jquery.ui.touch-punch.js');
$assets->addJs($componentPath.'/js/plugins/lightbox.min.js');
$assets->addJs($componentPath.'/js/plugins/perfect-scrollbar.min.js');
$assets->addJs($componentPath.'/js/init/drag-serialize.js');
//$assets->addJs($componentPath.'/js/bundle.js');
$assets->addJs($componentPath.'/js/main.js');
$assets->addJs($componentPath.'/js/init/idea.js');
$assets->addJs($componentPath.'/js/init/dropdown.js');
$assets->addJs($componentPath.'/js/init/custom-drag.js');
$assets->addJs($componentPath.'/js/init/sortable.js');;

if (!Loader::includeModule("ithive.iboard")) return;
/*create user board and system category at first visit*/
$userId = $GLOBALS["USER"]->GetID();
$arUserBoard = Boards::getUserSystemBoard($userId, true);
$userBoardId = $arUserBoard["id"];
$arUserDefaultCategory = Categories::getUserSystemCategory($userId, $userBoardId, true);

$arDefaultUrlTemplates404 = array(
    "idea_add" => "add/",
    "idea_edit" => "edit/#IDEA_ID#/",
    "idea_detail" => "idea/#IDEA_ID#/",
    "ideas_list" => "ideas/",
    "ideas_history" => "history/",
    "ideas_archive" => "archive/",
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array(
    "IDEA_ID"
);

if($arParams["SEF_MODE"] == "Y")
{
    $arVariables = array();

    $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
    $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

    $engine = new CComponentEngine($this);

    $componentPage = $engine->guessComponentPath(
        $arParams["SEF_FOLDER"],
        $arUrlTemplates,
        $arVariables
    );

    if(!$componentPage)
    {
        $componentPage = "ideas_list";
    }

    CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

    $arResult = array(
        "FOLDER" => $arParams["SEF_FOLDER"],
        "URL_TEMPLATES" => $arUrlTemplates,
        "VARIABLES" => $arVariables,
        "ALIASES" => $arVariableAliases,
    );
}
$this->includeComponentTemplate($componentPage);