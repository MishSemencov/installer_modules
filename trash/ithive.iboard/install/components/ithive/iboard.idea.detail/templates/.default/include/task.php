<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<?if (is_array($arResult["IDEA"]["TASKS"]) && count($arResult["IDEA"]["TASKS"]) > 0) {?>
<div>
    <?=Loc::Getmessage("TASKS_TITLE")?>
    <ul class="list">
        <?foreach ($arResult["IDEA"]["TASKS"] as $arTask) {?>
            <li><a href="<?=$arTask["DATA"]?>"><?=$arTask["DATA"]?></a></li>
        <?}?>
    </ul>
</div>

<hr>
<?}?>