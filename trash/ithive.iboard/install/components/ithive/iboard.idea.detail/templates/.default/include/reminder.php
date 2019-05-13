<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<?if (is_array($arResult["IDEA"]["REMINDERS"]) && count($arResult["IDEA"]["REMINDERS"]) > 0) {?>
    <div class="layout-block">
        <div class="cell">
            <?=Loc::Getmessage("IDEAS_REMINDER");?>
        </div>
        <div class="cell">
            <ul class="reminder-list">
                <?foreach ($arResult["IDEA"]["REMINDERS"] as $arReminder) {?>
                    <li class="card">
                        <?=$arReminder["DISPALY_VALUE"]?>
                    </li>
                <?}?>
            </ul>
        </div>
    </div>

    <hr>
<?}?>