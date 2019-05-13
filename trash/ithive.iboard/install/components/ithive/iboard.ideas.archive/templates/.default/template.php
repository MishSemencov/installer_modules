<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
CJSCore::Init(array("jquery", "iboard_core"));
?>
<?if (!empty($arResult["IDEAS"])) {?>
    <ul class="idea-clear ideas-list ideas-list-not-draggable simple grayscaled idea-archive-list">
        <?foreach ($arResult["IDEAS"] as $ideaId => $arIdea) {?>
            <li>
                <div class="idea active">
                    <input type="hidden" class="idea-id-input" name="IDEA_ID" value="<?=$ideaId?>" data-value="<?=$ideaId?>">
                    <div class="row collapsed-row head middle-xs">
                        <div class="col-xs-8">
                            <div class="title"><?=($arIdea["NAME"]) ? $arIdea["NAME"] : Loc::Getmessage("IDEA_NUMBER", array("#IDEA_ID#" => $ideaId))?></div>
                        </div>
                        <div class="col-xs-4 align-right">
                            <button type="button" class="toggler" title="<?=Loc::Getmessage("MORE_INFOа")?>">
                                <i class="icon icon-menu"></i>
                                <i class="icon icon-menu-opened"></i>
                            </button>
                            <span onclick="window.ITHIdea.onRestoreIdea({obj: this})" class="idea-ico idea-restore-btn" title="<?=Loc::Getmessage("RESTORE")?>"></span>
                            <span onclick="window.ITHIdea.onRemoveIdea({obj: this})" class="idea-ico idea-remove-btn" title="<?=Loc::Getmessage("DELETE")?>"></span>
                        </div>
                    </div>
                    <div class="text">
                        <?=$arIdea["DESCRIPTION"]?>
                    </div>
                    <?if (is_array($arIdea["FILES"]["IMAGES"])) {?>
                        <ul class="idea-clear images-list idea-clearfix">
                            <?foreach ($arIdea["FILES"]["IMAGES"] as $arImg) {?>
                                <li>
                                    <a href="<?=$arImg["SRC"]?>" data-lightbox="gallery-idea-<?=$ideaId?>">
                                        <figure class="image-cover"><img src="<?=$arImg["SRC"]?>" alt="<?=$arImg["FILE_NAME"]?>"></figure>
                                    </a>
                                </li>
                            <?}?>
                        </ul>
                    <?}?>
                    <div class="date"><?=$arIdea["DATE_CREATE"]?></div>
                    <?if (is_array($arIdea["TAGS"])) {?>
                        <ul class="idea-clear tags-list">
                            <?foreach ($arIdea["TAGS"] as $tag) {?>
                                <li style="background-color: #<?=$tag["COLOR"]?>"><?=$tag["NAME"]?></li>
                            <?}?>
                        </ul>
                    <?}?>
                </div>
            </li>
        <?}?>
    </ul>
<?}?>