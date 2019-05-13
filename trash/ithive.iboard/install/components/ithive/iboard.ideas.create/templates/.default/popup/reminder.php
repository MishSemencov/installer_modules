<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?use Bitrix\Main\Localization\Loc;
$reminderExtensions = array("jquery", "date");
CJSCore::Init($reminderExtensions);

$arDays = array(
    "MONDAY" => Loc::getMessage("MONDAY"),
    "TUESDAY" => Loc::getMessage("TUESDAY"),
    "WEDNESDAY" => Loc::getMessage("WEDNESDAY"),
    "THURSDAY" => Loc::getMessage("THURSDAY"),
    "FRIDAY" => Loc::getMessage("FRIDAY"),
    "SATURDAY" => Loc::getMessage("SATURDAY"),
    "SUNDAY" => Loc::getMessage("SUNDAY"),
)
?>
<div class="idea-reminder-popup idea-popup">
    <input type="text" id="idea-reminder-date" name="IDEA_REMINDER_DATE" onclick="BX.calendar({node: this, field: this, bTime: true, bHideTime: false, currentTime: <?=time()?>});">
    <select id="idea-reminder-period-select">
        <option value="false" selected><?=Loc::getMessage("REMINDER_PERIOD")?></option>
        <?foreach ($arResult["REMINDER_PERIOD"] as $val => $name) {?>
            <option value="<?=$val?>"><?=$name?></option>
        <?}?>
    </select>
    <span id="idea-reminder-period-time">
        <span class="idea-reminder-period-daily">
            <input id="idea-reminder-period-hour" class="idea-reminder-input-time idea-reminder-input-time-small" name="IDEA_REMINDER_PERIOD_HOUR" value="12">
            <span>:</span>
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
        <div id="idea-add-reminder" class="idea-btn"><?=Loc::getMessage("ADD")?></div>
    </div>
    <div class="idea-popup-angly-bottom"></div>
    <i class="idea-popup-close popup-window-close-icon"></i>
</div>