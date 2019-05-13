<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<?if (is_array($arResult["IDEA"]["TAGS"]) && count($arResult["IDEA"]["TAGS"]) > 0) {?>
    <ul class="tags-list">
        <?foreach ($arResult["IDEA"]["TAGS"] as $arTag) {?>
            <li class="primary" style="background-color: #<?=$arTag["COLOR"]?>"><a><?=$arTag["NAME"]?></a></li>
        <?}?>
    </ul>
<?}?>