<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arExtensions = array("jquery", "fx", "popup", "iboard_core");
CJSCore::Init($arExtensions);
?>
<article class="idea-article">
    <?
    if ($arResult["IDEA"]["ID"] <= 0)
        echo Loc::Getmessage("IDEA_NOT_FOUND");
    else{
    ?>
        <hr>
        <div class="row">
            <div class="col-xs-8">
                <?include_once "include/tags.php";?>
            </div>
            <div class="col-xs-4 align-right">
                <a class="link right-origin<?=$arResult["IDEA"]["IMPORTANT"] ? " active" : ""?> idea-important-switch" onclick="window.ITHIdea.setImportant(this, <?=$arResult["IDEA"]["ID"]?>)">
                    <i class="icon icon-flame"></i>
                    <span><?=$arResult["IDEA"]["IMPORTANT"] ? Loc::Getmessage("IDEA_IMPORTANT") : Loc::Getmessage("IDEA_DO_IMPORTANT");?></span>
                </a>
            </div>
        </div>

        <?include_once "include/name.php";?>
        <?include_once "include/text.php";?>
        <?include_once "include/watcher.php";?>
        <?include_once "include/images.php";?>
        <?include_once "include/files.php";?>
        <?include_once "include/category.php";?>
        <?include_once "include/reminder.php";?>
        <?include_once "include/date_update.php";?>
        <?include_once "include/creator.php";?>
        <?include_once "include/task.php";?>
        <?include_once "include/event.php";?>
        <?include_once "include/date_create.php";?>
        <?if (\Bitrix\Main\Loader::includeModule('ithive.smarttextselection'))
            include_once "include/fast_obj_btn.php";?>

        <div>
            <a class="idea-btn idea-btn-simple idea-btn-danger" onclick="window.ITHIdea.onDeleteIdea(<?=$arResult["IDEA"]["ID"]?>)"><?=Loc::Getmessage("IDEA_DELETE");?></a>
            <a class="idea-btn idea-btn-simple idea-btn-link" onclick="window.ITHIdea.editIdea(<?=$arResult["IDEA"]["ID"]?>)"><?=Loc::Getmessage("IDEA_EDIT");?></a>
        </div>
        <?include_once "include/comments.php";?>
    <?}?>
</article>

<script>
    $(document).ready(function() {
        window.ITHIdea.init({
            ideaId: "<?=$arResult["IDEA"]["ID"]?>"
        });
    });
</script>