<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$reminderExtensions = array("jquery", "date", "socnetlogdest", "iboard_core");
CJSCore::Init($reminderExtensions);
global $arrIdeasFilter;
?>
<div class="ideas-filter-wrap">
    <?if (is_array($arrIdeasFilter) && count($arrIdeasFilter) > 0) {?>
        <?foreach ($arrIdeasFilter as $key => $arValue) {
            if ($key == "USER_ID") continue;
            if ($key == "IMPORTANT")
                $arValue = ($arValue == 1) ? Loc::GetMessage("IMPORTANT_IDEA") : Loc::GetMessage("NOT_IMPORTANT_IDEA")?>
            <span class="idea-active-filter-val-wrap" data-prop="<?=$key?>">
                <span class="idea-active-filter-val"><?=Loc::GetMessage($key)?> <?=(!is_array($arValue)) ? $arValue : $arValue["DISPLAY_VALUE"]?></span>
                <span class="idea-active-filter-del"></span>
            </span>
        <?}?>
    <?}?>
    <form action="<?=$APPLICATION->GetCurPage()?>" method="POST" name="IDEAS_FILTER">
        <input class="idea-input idea-input-name ideas-filter-input-btn" placeholder="<?=Loc::Getmessage("SEARCH_PLACEHOLDER")?>" name="FILTER[TEXT]" value="<?=$arrIdeasFilter["TEXT"]?>">
        <span class="ideas-search-ico"></span>
        <div class="ideas-filter-form">
            <?foreach ($arResult["FILTER"]["PROPERTIES"] as $propCode => $arProperty) {?>
                <div class="idea-property-wrap">
                    <div class="idea-property-name">
                        <?=$arProperty["NAME"]?>
                    </div>
                    <?switch ($arProperty["TYPE"]) {
                        case "TEXT":
                        default:?>
                            <input class="idea-input idea-filter-input" type="text" name="FILTER[<?=$propCode?>]" value="<?=$arrIdeasFilter[$propCode] ? $arrIdeasFilter[$propCode] : ""?>"
                                placeholder="<?=$arProperty["PLACEHOLDER"]?>">
                            <?break;
                        case "SELECT":?>
                            <select class="idea-input idea-filter-select" type="text" name="FILTER[<?=$propCode?>]" value="<?=$arrIdeasFilter[$propCode] ? $arrIdeasFilter[$propCode] : ""?>">
                                <option value="-1" style="color: #a9adb2;"><?//=$arProperty["PLACEHOLDER"]?></option>
                                <?foreach ($arProperty["VALUES"] as $vName => $vCode) {?>
                                    <option value="<?=$vCode?>"<?=($vCode == $arrIdeasFilter[$propCode]) ? " selected" : ""?>><?=$vName?></option>
                                <?}?>
                            </select>
                            <?break;
                        case "DATE":?>
                            <input class="idea-input idea-input-small idea-filter-input" type="text" name="FILTER[<?=$propCode?>_FROM]" onclick="BX.calendar({node: this, field: this, bTime: false});" value="<?=$arrIdeasFilter[$propCode . "_FROM"] ? $arrIdeasFilter[$propCode . "_FROM"] : ""?>"
                                   placeholder="<?=$arProperty["PLACEHOLDER_FROM"]?>">
                            <input class="idea-input idea-input-small idea-filter-input" type="text" name="FILTER[<?=$propCode?>_TO]" onclick="BX.calendar({node: this, field: this, bTime: false});" value="<?=$arrIdeasFilter[$propCode . "_TO"] ? $arrIdeasFilter[$propCode . "_TO"] : ""?>"
                                   placeholder="<?=$arProperty["PLACEHOLDER_TO"]?>">
                            <?break;
                        case "USER_SELECT":?>
                            <?
                            $APPLICATION->IncludeComponent(
                                'bitrix:tasks.widget.member.selector',
                                '',
                                array(
                                    'TEMPLATE_CONTROLLER_ID' => 'idea_filter_'.$propCode,
                                    'DISPLAY' => 'inline',
                                    'MIN' => 1,
                                    'MAX' => 1,
                                    'TYPES' => array('USER', 'USER.EXTRANET', 'USER.MAIL'),
                                    'INPUT_PREFIX' => 'FILTER['.$propCode.']',
                                    'ATTRIBUTE_PASS' => array(
                                        'ID',
                                        'NAME',
                                        'LAST_NAME'
                                    ),
        //                            'DATA' => array($arrIdeasFilter[$propCode]),
                                    'DATA' => ($arrIdeasFilter[$propCode]["ID"]) ? array($arrIdeasFilter[$propCode]) : "",
                                    'READ_ONLY' => 'N',
                                ),
                                false,
                                array("HIDE_ICONS" => "Y", "ACTIVE_COMPONENT" => "Y")
                            )
                            ?>
                            <?break;
                    }?>
                </div>
            <?}?>
            <input class="idea-btn" type="submit" value="<?=Loc::Getmessage("FIND_BTN")?>">
            <a href="?clear_filter=Y" class="idea-btn idea-btn-simple idea-btn-link"><?=Loc::Getmessage("CLEAR_BTN")?></a>
        </div>
    </form>
</div>