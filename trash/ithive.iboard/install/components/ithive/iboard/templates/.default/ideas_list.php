<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<div class="row">
    <div class="col-xs-12 col-lg-8 ideas-menu-col">
        <?$APPLICATION->IncludeComponent(
            "ithive:iboard.menu",
            "",
            Array(
                "URL_TEMPLATES" => [
                    "BASE_FOLDER" => $arResult['FOLDER'],
                    "LINKS" => $arResult['URL_TEMPLATES']
                ],
                "CACHE_TIME" => "3600",
                "CACHE_TYPE" => "A"
            )
        );?>
        <?if (\Bitrix\Main\Loader::includeModule('ithive.smarttextselection')) {?>
            <div class="dropdown" data-dropdown="">
                <button class="anchor idea-btn idea-btn-default"><?=Loc::Getmessage("CHOOSE_ACTION")?></button>
                <div class="dropdown-menu">
                    <ul class="idea-clear">
                        <li><a class="ideas-event-create"><?=Loc::Getmessage("CREATE_EVENT")?></a></li>
                        <li><a class="ideas-lfeed-post"><?=Loc::Getmessage("CREATE_LIFE_FEED")?></a></li>
                    </ul>
                </div>
            </div>
        <?}?>
    </div>
    <div class="col-xs-12 col-lg-4">
        <?$APPLICATION->IncludeComponent(
            "ithive:iboard.ideas.filter",
            $template,
            Array(
                "USER_ID" => $GLOBALS["USER"]->GetID(),
                "CACHE_TIME" => "3600",
                "CACHE_TYPE" => "A"
            )
        );?>
        <?
        if (isset($_REQUEST["VIEW"]))
            $_SESSION["IDEA_LIST_VIEW"] = $_REQUEST["VIEW"];
        ?>
        <a href="?VIEW=<?=($_SESSION["IDEA_LIST_VIEW"] == "table") ? "list" : "table"?>" class="ideas-list-toggle<?=($_SESSION["IDEA_LIST_VIEW"] == "table") ? " ideas-list-toggle-list" : " ideas-list-toggle-table";?>"
           title="<?=($_SESSION["IDEA_LIST_VIEW"] == "table") ? Loc::Getmessage("COL_VIEW") : Loc::Getmessage("LIVE_VIEW")?>"
        ></a>
    </div>
</div>
<?$template = ($_SESSION["IDEA_LIST_VIEW"] != "table") ? "" : "table"?>

<?$APPLICATION->IncludeComponent(
	"ithive:iboard.ideas.list",
	$template,
	Array(
	    "USER_ID" => $GLOBALS["USER"]->GetID(),
	    "SEF_FOLDER" => $arParams["SEF_FOLDER"],
	    "CREATE_PAGE_MASK" => $arResult["URL_TEMPLATES"]["idea_add"],
	    "DETAIL_PAGE_MASK" => $arResult["URL_TEMPLATES"]["idea_detail"],
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A"
	)
);?>

<script>
    $(document).ready(function() {
        window.ITHIdea.init({
            ideaId: ""
        });
    });
</script>
