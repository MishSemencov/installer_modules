<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
if ($_POST["AJAX_MODE"] == "Y") {
    $APPLICATION->RestartBuffer();
    $APPLICATION->ShowAjaxHead(true, true, true, true);
}

Loc::loadMessages(__FILE__);

$arExtensions = array("jquery", "fx", "popup", "iboard_core");
CJSCore::Init($arExtensions);

if ($arResult["IDEA"]["ID"] <= 0)
    echo Loc::Getmessage("IDEA_NOT_FOUND");
elseif ($arResult["MESSAGES"]) {?>
    <div class="idea-message-wrap idea-success-wrap">
        <?foreach ($arResult["MESSAGES"] as $message) {
          echo $message;
        }?>
    </div>
<?} elseif ($arResult["ERRORS"]) {?>
    <div class="idea-message-wrap idea-error-wrap">
        <?foreach ($arResult["ERRORS"] as $error) {
            echo $error;
        }?>
    </div>
<?} else {
    $isCreator = ($arResult["IDEA"]["CREATOR"]["ID"] == $GLOBALS["USER"]->GetID()) ? true : false;
?>
    <article class="idea-article">
        <div class="idea-info-wrap"></div>
        <form id="idea_form_<?=$arParams["FORM_ID"]?>" action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="IDEA_EDIT_FORM">
            <div class="row">
                <div class="col-xs-8">
                    <?include_once "include/name.php";?>
                </div>
                <div class="col-xs-4 align-right">
                    <a class="link right-origin<?=$arResult["IDEA"]["IMPORTANT"] ? " active" : ""?> idea-important-switch" onclick="window.ITHIdea.changeImportant({obj: this})">
                        <input type="hidden" class="idea-important-input" name="IDEA[IMPORTANT][VALUE]" value="<?=$arResult["IDEA"]["IMPORTANT"] ? "1" : "0"?>">
                        <input type="hidden" class="idea-important-update" name="IDEA[IMPORTANT][NEED_UPDATE]" value="N">
                        <i class="icon icon-flame"></i>
                        <span class="idea-important-text"><?=$arResult["IDEA"]["IMPORTANT"] ? Loc::Getmessage("IDEA_IMPORTANT") : Loc::Getmessage("IDEA_DO_IMPORTANT");?></span>
                    </a>
                </div>
            </div>


            <?include_once "include/text.php";?>
            <?include_once "include/files.php";?>
            <?include_once "include/watcher.php";?>
            <?include_once "include/category.php";?>
            <?include_once "include/tags.php";?>
            <?include_once "include/reminder.php";?>
            <?include_once "include/date_create.php";?>
            <?include_once "include/date_update.php";?>
            <?include_once "include/creator.php";?>
            <?include_once "include/task.php";?>
            <?include_once "include/event.php";?>

            <div>
                <input type="button" class="idea-btn idea-btn-simple"  onclick="window.ITHIdea.ideaSubmitForm({form: $(this).closest('form'), event: event});" value="<?=Loc::Getmessage("IDEA_SAVE");?>">
                <a class="idea-btn idea-btn-simple idea-btn-link" onclick="window.ITHIdea.ideaPopup.close()"><?=Loc::Getmessage("IDEA_CANCEL");?></a>
            </div>
            <input type="hidden" name="AJAX_MODE" value="Y">
            <input type="hidden" name="IDEA[UPDATE]" value="Y">
            <input type="hidden" name="IDEA[ID]" value="<?=$arResult["IDEA"]["ID"]?>">
        </form>
    </article>
<?}

if ($_POST["AJAX_MODE"] == "Y")
    die();
?>