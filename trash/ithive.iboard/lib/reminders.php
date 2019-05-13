<?
/*
 * Reminders - класс для работы с таблицей напоминаний
*/
namespace ITHive\IBoard;

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    ITHive\IBoard\Models\RemindersTable,
    ITHive\IBoard\Models\IdeasTable,
    ITHive\IBoard\IdeasCommonFunctions;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Reminders {
    /**
     * метод для добавления напоминаний
     *
     * @$ideaId - id идеи
     * @$type - date/period - тип напоминания - дата, период
     * @$date - дата ближайшего напоминания
     */
    public static function add($ideaId, $type, $date, $hour = false, $minutes = false, $day = false)
    {
        $arFields = array(
            'date_create' => \Bitrix\Main\Type\DateTime::createFromTimestamp(time()),
            'idea_id' => $ideaId,
        );

        if ($type == "period") {
            $arFields["period"] = $date;
            $hour = ($hour) ? $hour : date("H");
            $minutes = ($minutes) ? $minutes : date("i");
            $seconds = "00";
            switch ($date) {
                case "day":
                    $date = \Bitrix\Main\Type\DateTime::createFromTimestamp(mktime($hour, $minutes, $seconds, date("m"), date("d") + 1, date("Y")));
                    break;
                case "week":
                    $nextDayTime = strtotime('next ' . $day);
                    $nextDay = date( "d", $nextDayTime);
                    $nextMonth = date( "m", $nextDayTime);
                    $nextYear = date( "Y", $nextDayTime);
                    $date = \Bitrix\Main\Type\DateTime::createFromTimestamp(mktime($hour, $minutes, $seconds, $nextMonth, $nextDay, $nextYear));
                    break;
                case "month":
                    $curDay = date("d");
                    $month = ($curDay < $day) ? date("m") : date("m") + 1;
                    $date = \Bitrix\Main\Type\DateTime::createFromTimestamp(mktime($hour, $minutes, $seconds, $month, $day, date("Y")));
                    break;
            }
        }

        $arFields["date"] = \Bitrix\Main\Type\DateTime::createFromUserTime($date);

        $result = RemindersTable::add($arFields);

        if ($result->isSuccess()) {
            $arResult["ID"] = $result->getId();
        } else
            $arResult["ERRORS"] = $result->getErrorMessages();

        return $arResult;
    }

    /**
     * метод для обновления напоминания
     *
     * @$id - id напоминания
     * @$fields - массив полей для обноления
     */
    public static function update($id, $fields)
    {
        $result = RemindersTable::update($id, $fields);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else
            $arResult = true;

        return $arResult;
    }

    /**
     * метод для удаления напоминания
     *
     * @$id - id напоминания
     */
    public static function delete($id)
    {
        $result = RemindersTable::delete($id);

        if (!$result->isSuccess())
            $arResult["ERRORS"] = $result->getErrorMessages();
        else
            $arResult = true;

        return $arResult;
    }

    /* получение списка напоминаний
     *
     * @$ideaId int - id идеи
    */
    public static function getList($ideaId)
    {
        global $DB;
        $arReminders = array();
        $remindersTableName = RemindersTable::getTableName();

        $sql = "
            select reminders.id as REMINDER_ID, " . $DB->DateToCharFunction("reminders.date") . " as DATE, reminders.period as PERIOD, reminders.idea_id as IDEA_ID 
            from " . $remindersTableName . " reminders 
            where reminders.idea_id = " . $ideaId;
        $dbResults = $DB->Query($sql);
        while ($arReminder = $dbResults->Fetch()) {
            $arDate = explode(" ", $arReminder["DATE"]);
            switch ($arReminder["PERIOD"]) {
                case "d":
                    $arReminder["DISPALY_VALUE"] = Loc::getMessage("REMINDER_DAILY") . " " . $arDate[1];
                    break;
                case "w":
                    $arReminder["DISPALY_VALUE"] = Loc::getMessage("REMINDER_WEEKLY") . " " . $arDate[1];
                    break;
                case "m":
                    $arReminder["DISPALY_VALUE"] = Loc::getMessage("REMINDER_MONTHLY") . " " . $arDate[1];
                    break;
                default:
                    $arReminder["DISPALY_VALUE"] = $arReminder["DATE"];
                    break;
            }
            $arReminders[$arReminder["IDEA_ID"]][] = $arReminder;
        }

        return $arReminders;
    }

    /**
     * агент рассылки уведомлений
    **/
    function sendAgent()
    {
        global $DB;
        $tableName = RemindersTable::getTableName();
        $ideasTableName = IdeasTable::getTableName();
        $currentDBDate = $DB->CharToDateFunction(\Bitrix\Main\Type\DateTime::createFromTimestamp(time()));

        $sql = "
            select reminders.id as reminder_id, reminders.date as date, reminders.period as period, reminders.idea_id as idea_id, ideas.user_id as user_id, ideas.description as description 
            from " . $tableName . " reminders
            left join " . $ideasTableName . " ideas on (reminders.idea_id = ideas.id)
            where date <= " . $currentDBDate;
        $dbResults = $DB->Query($sql);
        while ($arReminder = $dbResults->Fetch())
        {
            $notifyMessage = Loc::GetMessage("REMINDER_MESSAGE", array("#IDEA_HREF#" => "/iboard/idea/" . $arReminder["idea_id"] . "/", "#IDEA_TEXT#" => substr($arReminder["description"], 0, 3)));
            $notifyRes = IdeasCommonFunctions::sendReminder($arReminder["user_id"], 0, IM_NOTIFY_SYSTEM, $notifyMessage, $arReminder["idea_id"]);

            if (intval($notifyRes) > 0) {
                if ($arReminder["period"] == "")
                    \ITHive\IBoard\Reminders::delete($arReminder["reminder_id"]);
                else {
                    $date = strtotime($arReminder["date"]);
                    switch ($arReminder["period"]) {
                        case "d":
                            $date = strtotime("1 day", $date);
                            break;
                        case "w":
                            $date = strtotime("1 week", $date);
                            break;
                        case "m":
                            $date = strtotime("1 month", $date);;
                            break;
                    }
                    $arReminderFields["date"] = \Bitrix\Main\Type\DateTime::createFromTimestamp($date);
                    \ITHive\IBoard\Reminders::update($arReminder["reminder_id"], $arReminderFields);
                }
            }
        }

        return "ITHive\IBoard\Reminders::sendAgent();";
    }
}