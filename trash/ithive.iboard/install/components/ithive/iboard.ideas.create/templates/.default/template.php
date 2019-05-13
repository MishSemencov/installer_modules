<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("fileman") || !Loader::includeModule("socialnetwork")) return;

$reminderExtensions = array("jquery", "iboard_core");
CJSCore::Init($reminderExtensions);

$text = htmlspecialchars($_POST["TEXT"]);
if ($arResult["IDEA_ID"]) {?>
   <div class="idea-success-created"><?=Loc::getmessage("IDEA_CREATED", array("#IDEA_HREF#" => "/iboard/idea/" . $arResult["IDEA_ID"] . "/"))?></div>
<?} else if(count($arResult["ERRORS_MESSAGES"]) > 0) {?>
    <div class="idea-error-created"><?=implode("</br>", $arResult["ERRORS_MESSAGES"])?></div>
<?}?>
<div class="idea-add-form-wrapper innactive initializing">
    <form action="<?=$APPLICATION->GetCurPage()?>" method="POST" id="<?=$arParams["FORM_ID"]?>" name="idea-add-form">
        <input class="create-idea-name-input" name="IDEA_NAME" placeholder="<?=Loc::GetMessage("IDEA_NAME")?>">
        <div class="idea-info-panel-important">
            <input id="idea-important" type="checkbox" name="IDEA_IMPORTANT">
            <label for="idea-important"><?=Loc::GetMessage("MAKE_IMPORTANT")?></label>
        </div>
        <?$APPLICATION->IncludeComponent(
            "bitrix:main.post.form",
            "",
            Array(
                "FORM_ID" => "idea-form-". $arParams["FORM_ID"],
                "SHOW_MORE" => "N",
                "PARSER" => array(
                    "Bold",
                    "Italic",
                    "Underline",
                    "Strike",
                    "ForeColor",
                    "FontList",
                    "FontSizeList",
                    "RemoveFormat",
                    "Quote",
                    "Code",
                    "CreateLink",
                    "Image",
                    "Table",
                    "Justify",
                    "InsertOrderedList",
                    "InsertUnorderedList",
                    "SmileList",
                    "Source",
                    "InsertVideo",
                    "More"
                ),
                "BUTTONS" => array(
                    "UploadImage",
                    "UploadFile",
                    "InputVideo",
                ),
                "LHE" => array(
                    'id' => $arParams["FORM_ID"],
                    'bResizable' => true,
                    'bAutoResize' => true,
                    "height" => 600,
                    "bbCode" => 1
                ),
                "NAME_TEMPLATE" => "#NAME# #LAST_NAME#",
                "TEXT" => Array(
                    "ID" => "IDEA_TEXT",
                    "NAME" => "IDEA_TEXT",
                    "VALUE" => (strlen($text)) ? $text : "",
                    "SHOW" => "Y",
                    "HEIGHT" => "600px"
                ),
                "ADDITIONAL" => $arResult["ADDITIONAL_BTN_HTML"],
                "UPLOAD_FILE" => array(
                    "INPUT_NAME" => 'IDEA_FILES',
                    "INPUT_VALUE" => ($arResult["FILES"]) ? $arResult["FILES"] : array(),
//                    "MAX_FILE_SIZE" => 5000000,
                    "MULTIPLE" => "Y",
                    "MODULE_ID" => "ithive.iboard",
                    "ALLOW_UPLOAD" => "A",
                    "ALLOW_UPLOAD_EXT" => "Y"
                ),
                "PIN_EDITOR_PANEL" => "Y"
            )
        );
        ?>
        <?include_once "popup/watcher.php";?>
        <?if ($arResult["ADDITIONAL_BTN"]["REMINDER"])
            include_once "popup/reminder.php";?>
        <?if ($arResult["ADDITIONAL_BTN"]["TAGS"])
            include_once "popup/tags.php";?>
        <?if ($arResult["ADDITIONAL_BTN"]["CATEGORY"])
            include_once "popup/category.php";?>
        <?if ($arResult["CATEGORY"]) {?>
            <div class="idea-category-items" data-last="0">
                <div class="idea-category-title"><?=Loc::Getmessage("CATEGORY_TITLE")?></div>
                <span class="idea-category-item idea-category-to-add" data-id="<?=$arResult["CATEGORY"]["ID"]?>">
                    <input type="hidden" name="IDEA_CATEGORY_ITEM[]" value="<?=$arResult["CATEGORY"]["ID"]?>#<?=$arResult["CATEGORY"]["NAME"]?>#<?=$arResult["CATEGORY"]["COLOR"]?>#">
                    <span class="idea-category-color-label" style="background-color: #<?=$arResult["CATEGORY"]["COLOR"]?>"></span>
                    <span class="idea-category-exist-name" data-name="<?=$arResult["CATEGORY"]["NAME"]?>"><?=$arResult["CATEGORY"]["NAME"]?></span>
                    <span class="idea-select-item-delete idea-category-delete" id="idea-category-delete-0"></span>
                </span>
            </div>
        <?}?>
        <div class="idea-add-form-footer">
            <div class="row">
                <div class="col-xs-8">
                    <?if (\Bitrix\Main\Loader::includeModule('ithive.smarttextselection')) {?>
                        <div class="links-container">
                            <a href="#" data-tooltip="<?=Loc::GetMessage("CREATE_TASK")?>"><i class="test-icon icon1 idea-tmp-task-create"></i></a>
                            <a href="#" data-tooltip="<?=Loc::GetMessage("CREATE_CHAT_MESSAGE")?>"><i class="test-icon icon2 idea-tmp-chat-create"></i></a>
                            <a href="#" data-tooltip="<?=Loc::GetMessage("CREATE_MEETING")?>"><i class="test-icon icon3 idea-tmp-event-create"></i></a>
                            <a href="#" data-tooltip="<?=Loc::GetMessage("CREATE_LIVE_FEED")?>"><i class="test-icon icon4 idea-tmp-lfeed-create"></i></a>
                            <a href="#" data-tooltip="<?=Loc::GetMessage("CREATE_LETTER")?>"><i class="test-icon icon5 idea-tmp-mail-create"></i></a>
                        </div>
                    <?}?>
                </div>
                <div class="col-xs-4 align-right">
                    <div class="idea-add-form-btns">
                        <input class="idea-btn" type="submit" value="<?=Loc::GetMessage("SAVE")?>">
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="CREATE_IDEA" value="Y">
    </form>
</div>

<script>
    BX.message({
        ADD: '<?=GetMessageJS('ADD')?>',
        REMINDER_TITLE: '<?=GetMessageJS('REMINDER_TITLE')?>',
        TAG_TITLE: '<?=GetMessageJS('TAG_TITLE')?>',
        CATEGORY_TITLE: '<?=GetMessageJS('CATEGORY_TITLE')?>',
        IDEA_IMPORTANT: '<?=GetMessageJS('IDEA_IMPORTANT')?>',
        MAKE_IMPORTANT: '<?=GetMessageJS('MAKE_IMPORTANT')?>',
    });

    var ob_<?=$arParams["FORM_ID"]?> = new ITHIdeaCreate({
        siteId: '<?=CUtil::JSEscape(SITE_ID)?>',
        userId: '<?=CUtil::JSEscape($GLOBALS["USER"]->GetID())?>',
        formId: '<?=$arParams["FORM_ID"]?>',
        componentPath: '<?=CUtil::JSEscape($componentPath)?>',
        ajaxUrl: '<?=CUtil::JSEscape($componentPath)?>/ajax.php',
    });
</script>