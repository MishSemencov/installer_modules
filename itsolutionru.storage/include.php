<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE) die();

class AgentClassStorage
{
    public static function checkStorage()
    {
        $overloadedDisks = [];
        $settings = static::getSettings();
        $diskStatus = static::getStorageData();
        foreach($diskStatus as $name => $disk)
        {
            if($disk['availableP'] < $settings[$name]['availableP'] && $settings[$name]['isChecking'])
                $overloadedDisks[] = $name;
        }
        if(sizeof($overloadedDisks) > 0) static::sendWarningMessage($overloadedDisks,$settings['notificationType']);
        return "AgentClassStorage::checkStorage();";
    }

    public static function getStorageData()
    {
        $result = [];
        $output = shell_exec("df -B M");
        while (strpos($output, "  ") !== FALSE) $output = str_replace("  ", " ", $output);
        $lines = explode("\n", $output);
        unset($lines[0]);
        foreach($lines as $disk)
        {
            $disk = explode(" ",$disk);
            if($disk[0] == "") continue;
            $result[$disk[0]] = [
                "overall" => (int) preg_replace("/[^0-9]/","",$disk[1]),
                "availableM" => (int) preg_replace("/[^0-9]/","",$disk[3]),
                "availableP" => 100-intval(preg_replace("/[^0-9]/","",$disk[4]))
            ];
        }
        return $result;
    }

    public static function sendWarningMessage($diskNames,$notificationType)
    {
        $user = CUser::GetList($by,$order,["GROUPS_ID" => [1]]);
        while($userData = $user -> fetch()) {
            if($notificationType == 1 || $notificationType == 3) {
                CModule::IncludeModule("im");
                $arMessageFields = array(
                    "TO_USER_ID" => $userData['ID'],
                    "FROM_USER_ID" => 0,
                    "NOTIFY_MESSAGE" => "На следующих дисках осталось места меньше, чем Вы указали:\n" . implode("\n", $diskNames)
                );
                CIMNotify::Add($arMessageFields);
            }
            if($notificationType == 2 || $notificationType == 3)
            {
                $to      = $userData['EMAIL'];
                $subject = 'Недостаточно места на дисках';
                $message = 'На портале '.$_SERVER['SERVER_NAME'].' следующих дисках осталось места меньше, чем Вы указали: '.implode(" ", $diskNames);
                $headers = 'From: team@it-solution.ru' . "\r\n" .
                    'Reply-To: team@it-solution.ru' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                mail($to, $subject, $message, $headers);
            }
        }
    }

    public static function saveSettings($settings)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT']."/local/its.storage/settings.json",json_encode($settings));
    }

    public static function getSettings()
    {
        $settings = file_get_contents($_SERVER['DOCUMENT_ROOT']."/local/its.storage/settings.json");
        return json_decode($settings,true);
    }

    public static function createDefaultSettings()
    {
        $output = [];
        $storageData = static::getStorageData();
        foreach($storageData as $name => $value)
        {
            $output[$name]['availableP'] = 10;
            $output[$name]['isChecking'] = 0;
        }
        $output['notificationType'] = 3;
        static::saveSettings($output);
    }
}
//<div class="bx-session-message" style="top: 0px; background-color: rgb(255, 235, 65); border: 1px solid rgb(237, 218, 60); width: 630px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; text-align: center; color: black; position: fixed; z-index: 10000; padding: 10px; left: 213px;"><a class="bx-session-message-close" style="display:block; width:12px; height:12px; background:url(/bitrix/js/main/core/images/close.gif) center no-repeat; float:right;" href="javascript:bxSession.Close()"></a>Ваш сеанс работы с сайтом завершен из-за отсутствия активности в течение 15 мин. Введенные на странице данные не будут сохранены. Скопируйте их перед тем, как закроете или обновите страницу.</div>