<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?use Bitrix\Main\Localization\Loc;
$reminderExtensions = array("jquery", "date");
CJSCore::Init($reminderExtensions);


/*start reminder period array*/
$arResult["REMINDER_PERIOD"] = array(
    "day" => Loc::getMessage("DAYLY"),
    "week" => Loc::getMessage("WEEKLY"),
    "month" => Loc::getMessage("MONTHLY")
);

$arDays = array(
    "MONDAY" => Loc::getMessage("MONDAY"),
    "TUESDAY" => Loc::getMessage("TUESDAY"),
    "WEDNESDAY" => Loc::getMessage("WEDNESDAY"),
    "THURSDAY" => Loc::getMessage("THURSDAY"),
    "FRIDAY" => Loc::getMessage("FRIDAY"),
    "SATURDAY" => Loc::getMessage("SATURDAY"),
    "SUNDAY" => Loc::getMessage("SUNDAY"),
)
/*end reminder period array*/
?>
<div class="idea-reminder-popup idea-popup">
    <div class="layout-block">
        <div class="cell">
            <?=Loc::Getmessage("IDEAS_REMINDER");?>
        </div>
        <div class="cell">
            <div class="idea-entity-wrap">
                <ul class="idea-current-reminder reminder-list">
                    <?if (is_array($arResult["IDEA"]["REMINDERS"]) && count($arResult["IDEA"]["REMINDERS"]) > 0) {?>
                        <?foreach ($arResult["IDEA"]["REMINDERS"] as $index => $arReminder) {?>
                            <li class="card">
                                <input type="hidden" class="idea-entity-id-input" name="IDEA[REMINDER][<?=$index?>][ID]" value="<?=$arReminder["REMINDER_ID"]?>">
                                <input type="hidden" class="idea-entity-date-input" name="IDEA[REMINDER][<?=$index?>][DATE]" value="<?=$arReminder["DATE"]?>">
                                <?if ($arReminder["PERIOD"] != "") {?>
                                    <input type="hidden" class="idea-entity-period-input" name="IDEA[REMINDER][<?=$index?>][PERIOD]" value="<?=$arReminder["PERIOD"]?>">
                                <?}?>
                                <input type="hidden" class="idea-entity-display-val-input" name="IDEA[REMINDER][<?=$index?>][DISPALY_VALUE]" value="<?=$arReminder["DISPALY_VALUE"]?>">
                                <input type="hidden" class="idea-entity-delete-input" name="IDEA[REMINDER][<?=$index?>][NEED_DELETE]" value="">
                                <span><?=$arReminder["DISPALY_VALUE"]?></span>
                                <span onclick="window.ITHIdea.deleteEntity({obj: this});" class="idea-entity-delete"></span>
                            </li>
                        <?}?>
                    <?}?>
                </ul>
                <a id="idea-change-reminder" class="idea-change-btn idea-popup-btn" data-popup="idea-reminder-popup">
                    <?=Loc::Getmessage("IDEA_ADD");?>
                    <div class="ideas-entity-popup idea-reminder-popup">
                        <input type="text" id="idea-reminder-date" onchange="window.ITHIdea.setReminderDate()" name="IDEA_REMINDER_DATE" onclick="BX.calendar({node: this, field: this, bTime: true, bHideTime: false, currentTime: <?=time()?>});">
                        <select id="idea-reminder-period-select" onchange="window.ITHIdea.setReminderPeriod()">
                            <option value="false" selected><?=Loc::getMessage("REMINDER_PERIOD")?></option>
                            <?foreach ($arResult["REMINDER_PERIOD"] as $val => $name) {?>
                                <option value="<?=$val?>"><?=$name?></option>
                            <?}?>
                        </select>
                        <span id="idea-reminder-period-time">
                            <span class="idea-reminder-period-daily">
                                <input id="idea-reminder-period-hour" class="idea-reminder-input-time idea-reminder-input-time-small" name="IDEA_REMINDER_PERIOD_HOUR" value="12">
                                <span class="idea-reminder-period-delimeter">:</span>
                                <input id="idea-reminder-period-minutes" class="idea-reminder-input-time idea-reminder-input-time-small" name="IDEA_REMINDER_PERIOD_MINUTES" value="00">
                            </span>
                            <select id="idea-reminder-period-week-days" class="idea-reminder-input-time" name="IDEA_REMINDER_PERIOD_WEEK_DAYS">
                                <?foreach ($arDays as $val => $dVal) {?>
                                    <option value="<?=$val?>"<?=($val == "MONDAY") ? " selected" : ""?>><?=$dVal?></option>
                                <?}?>
                            </select>
                            <span class="idea-reminder-period-days">
                                <select id="idea-reminder-period-days" class="idea-reminder-input-time" name="IDEA_REMINDER_PERIOD_DAYS">
                                    <?for ($i = 1; $i < 32; $i++) {?>
                                        <option value="<?=$i?>"<?=($i == 1) ? " selected" : ""?>><?=$i?></option>
                                    <?}?>
                                </select>
                                <span><?=Loc::GetMessage("NUMBER")?></span>
                            </span>
                        </span>
                        <div class="idea-reminder-btn-wrapper">
                            <div id="idea-add-reminder" class="idea-btn" onclick="window.ITHIdea.addReminderDate({obj: this})"><?=Loc::getMessage("IDEA_ADD")?></div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>
<hr>
