<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<?if (is_array($arResult["IDEA"]["EVENTS"]) && count($arResult["IDEA"]["EVENTS"]) > 0) {?>
    <div>
        <?=Loc::Getmessage("EVENTS_TITLE")?>
        <ul class="list">
            <?foreach ($arResult["IDEA"]["EVENTS"] as $arEvent) {?>
                <li><a href="<?=$arEvent["DATA"]?>"><?=$arEvent["DATA"]?></a></li>
            <?}?>
        </ul>
    </div>

    <hr>
<?}?>
