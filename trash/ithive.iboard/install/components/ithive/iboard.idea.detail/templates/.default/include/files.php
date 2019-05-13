<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<?
if (is_array($arResult["IDEA"]["FILES"]["OTHERS"]) && count($arResult["IDEA"]["FILES"]["OTHERS"]) > 0) {?>
    <div class="layout-block">
        <div class="cell">
            <?=Loc::Getmessage("IDEAS_FILES");?>
        </div>
        <div class="cell">
            <ul class="list">
                <?foreach ($arResult["IDEA"]["FILES"]["OTHERS"] as $arFile) {?>
                    <li>
                        <a href="<?=$arFile["SRC"]?>" download="">
                            <?=$arFile["FILE_NAME"]?>
                        </a>
                    </li>
                <?}?>
            </ul>
        </div>
    </div>

    <hr>
<?}?>