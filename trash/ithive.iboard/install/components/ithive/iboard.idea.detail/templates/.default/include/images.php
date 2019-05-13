<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<?if (is_array($arResult["IDEA"]["FILES"]["IMAGES"]) && count($arResult["IDEA"]["FILES"]["IMAGES"]) > 0) {?>
    <div class="layout-block">
        <div class="cell">
            <?=Loc::Getmessage("IDEAS_PHOTO");?>
        </div>
        <div class="cell">
            <ul class="photos-list">
                <?foreach ($arResult["IDEA"]["FILES"]["IMAGES"] as $arImg) {?>
                    <li>
                        <a href="<?=$arImg["SRC"]?>" data-lightbox="gallery-idea-<?=$arResult["IDEA"]["ID"]?>">
                            <figure class="image-cover">
                                <img src="<?=$arImg["SRC"]?>" alt="<?=$arImg["FILE_NAME"]?>">
                            </figure>
                        </a>
                    </li>
                <?}?>
            </ul>
        </div>
    </div>

    <hr>
<?}?>