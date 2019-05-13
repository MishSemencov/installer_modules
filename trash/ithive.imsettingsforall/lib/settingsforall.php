<?php

namespace ITHive\Im;


use Bitrix\Main\Config\Option;

/**
 * Class SettingsForAll
 * @package ITHive\Im
 */
class SettingsForAll
{
    /**
     * возвращает ID пользователя для из настроек модуля или текущего пользователя
     * @return int
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public static function getNotifySettings()
    {
        $moduleId = strtolower(basename(dirname(__DIR__)));
        $unotifyScheme = array(
            "notifySchemeSendSite" => (Option::get($moduleId, "notifySchemeSendSite") == "N")?false:true,
            "notifySchemeSendEmail" => (Option::get($moduleId, "notifySchemeSendEmail") == "N")?false:true,
            "notifySchemeSendPush" => (Option::get($moduleId, "notifySchemeSendPush") == "N")?false:true,
        );
        return $unotifyScheme;
    }

    /**
     * функция устанавливает настройки для пользователя по его ID
     * @param $userId
     * @param $arSettings
     * @return bool
     */
    public static function actionImSettingsSaveForUser($userId)
    {
        $resSetNotify = false;
        $arSettings = self::getNotifySettings();
        if ($arSettings && $userId && \Bitrix\Main\Loader::IncludeModule('im'))
        {
            $resSetNotify = \CIMSettings::Set(\CIMSettings::SETTINGS, $arSettings, $userId);
            return $resSetNotify;
        }
        return $resSetNotify;
    }

    /**
     * Изменение настроек для всех пользователей
     * @param $arSettings
     * @return int
     */
    public static function actionImSettingsSaveForAll($page, $limit)
    {
        $rsEl = \Bitrix\Main\UserTable::getList(array(
            'select' => array('ID'),
            'limit' => $limit,
            'count_total' => true,
            'offset' => $page*$limit
        ));
        while ($arEl = $rsEl->Fetch())
        {
            self::actionImSettingsSaveForUser($arEl["ID"]);
        }

        $rsAll = \Bitrix\Main\UserTable::getList(array(
            'select' => array('CNT'),
            'runtime' => array(
                new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')
            )
        ))->fetch()['CNT'];

        $totalPage = $rsAll/$limit;
        $percent = round(100*$totalPage/$page, 2);

        return 'CurrentStatus = Array('.$percent.',"'.($percent >= 100 ? $page+1 : '').'","Обрабатываю страница №'.$page.' из '.ceil($totalPage).'");';
    }


    /**
     * устанавливает новым пользователям дефолтные настройки
     * @param $arFields
     */
    public static function SetDefaulNotifySettings($arFields)
    {
        if($arFields["ID"]){
            self::actionImSettingsSaveForUser($arFields["ID"]);
        }
    }
}