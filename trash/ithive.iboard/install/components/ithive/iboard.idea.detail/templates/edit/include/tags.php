<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;

?>
<div class="layout-block">
    <div class="cell">
        <?=Loc::Getmessage("IDEAS_TAGS");?>
    </div>
    <div class="cell">
        <div class="idea-entity-wrap">
            <ul class="idea-current-tag tags-list">
                <?if (is_array($arResult["IDEA"]["TAGS"]) && count($arResult["IDEA"]["TAGS"]) > 0) {?>
                    <?foreach ($arResult["IDEA"]["TAGS"] as $index => $arTag) {?>
                        <li class="primary" style="background-color: #<?=$arTag["COLOR"]?>" data-name="<?=strtolower($arTag["NAME"])?>">
                            <input type="hidden" class="idea-entity-id-input" name="IDEA[TAGS][<?=$index?>][IDEA_TAG_ID]" value="<?=$arTag["IDEA_TAG_ID"]?>">
                            <input type="hidden" class="idea-entity-id-input" name="IDEA[TAGS][<?=$index?>][ID]" value="<?=$arTag["TAG_ID"]?>">
                            <input type="hidden" class="idea-entity-id-input" name="IDEA[TAGS][<?=$index?>][NAME]" value="<?=$arTag["NAME"]?>">
                            <input type="hidden" class="idea-entity-delete-input" name="IDEA[TAGS][<?=$index?>][NEED_DELETE]" value="">
                            <span><?=$arTag["NAME"]?></span>
                            <span onclick="window.ITHIdea.deleteEntity({obj: this});" class="idea-entity-delete"></span>
                        </li>
                    <?}?>
                <?}?>
            </ul>
            <a id="idea-change-tag" class="idea-change-btn idea-popup-btn" data-popup="idea-tags-popup">
                <?=Loc::Getmessage("IDEA_ADD");?>
                <div class="ideas-entity-popup idea-tags-popup">
                    <input type="text" class="idea-search-entity idea-input" value="" onkeyup="window.ITHIdea.searchEntity({obj: this, type: 'tag', user: <?=$GLOBALS["USER"]->GetID()?>});">
                    <span class="idea-input-add idea-tag-add" onclick="window.ITHIdea.ideaEntitySet({obj: this, type: 'tag', mode: 'multiple'});"><?=Loc::getmessage("IDEA_ADD")?></span>
                    <ul class="idea-found-entity">

                    </ul>
                </div>
            </a>
        </div>
    </div>
</div>

<hr>