<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<div class="layout-block">
    <div class="cell">
        <?=Loc::Getmessage("IDEAS_CATEGORY");?>
    </div>
    <div class="cell">
        <div class="idea-entity-wrap">
            <span class="idea-current-category">
                <input type="hidden" name="IDEA[CATEGORY][ID]" value="<?=$arResult["IDEA"]["CATEGORY"]["ID"]?>">
                <input type="hidden" name="IDEA[CATEGORY][VALUE]" value="<?=$arResult["IDEA"]["CATEGORY"]["NAME"]?>">
                <input type="hidden" name="IDEA[CATEGORY][NEW]" value="">
                <input type="hidden" name="IDEA[CATEGORY][NEED_UPDATE]" value="">
                <b><?=$arResult["IDEA"]["CATEGORY"]["NAME"]?></b>
            </span>
            <a id="idea-change-category" class="idea-change-btn idea-popup-btn" data-popup="idea-categories-popup">
                <?=Loc::Getmessage("IDEA_CHANGE");?>
                <div class="ideas-entity-popup idea-categories-popup">
                    <input type="text" class="idea-search-entity idea-input" value="" onkeyup="window.ITHIdea.searchEntity({obj: this, type: 'category', user: <?=$GLOBALS["USER"]->GetID()?>})">
                    <span class="idea-input-add idea-category-add" onclick="window.ITHIdea.ideaEntitySet({obj: this, type: 'category', mode: 'single'})"><?=Loc::getmessage("IDEA_CHANGE")?></span>
                    <ul class="idea-found-entity">

                    </ul>
                </div>
            </a>
        </div>
    </div>
</div>

<hr>