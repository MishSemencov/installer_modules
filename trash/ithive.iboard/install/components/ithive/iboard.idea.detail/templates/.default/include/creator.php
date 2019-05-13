<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<div class="layout-block">
    <div class="cell">
        <?=Loc::Getmessage("IDEA_CREATOR");?>
    </div>
    <div class="cell">
        <div class="user-layout">
            <?if ($arResult["IDEA"]["CREATOR"]["PERSONAL_PHOTO"]) {?>
                <div class="image">
                    <a href="/company/personal/user/<?=$arResult["IDEA"]["CREATOR"]["ID"]?>/">
                        <figure class="image-cover round">
                            <img src="<?=$arResult["IDEA"]["CREATOR"]["PERSONAL_PHOTO"]?>" alt="<?=$arResult["IDEA"]["CREATOR"]["NAME"] . " " . $arResult["IDEA"]["CREATOR"]["LAST_NAME"]?>">
                        </figure>
                    </a>
                </div>
            <?}?>
            <div class="info idea-user-name<?=(!$arResult["IDEA"]["CREATOR"]["PERSONAL_PHOTO"]) ? " idea-user-no-photo" : ""?>">
                <a href="/company/personal/user/<?=$arResult["IDEA"]["CREATOR"]["ID"]?>/"><?=$arResult["IDEA"]["CREATOR"]["NAME"] . " " . $arResult["IDEA"]["CREATOR"]["LAST_NAME"]?></a>
            </div>
        </div>
    </div>
</div>

<hr>