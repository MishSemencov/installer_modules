<?
namespace ITHive\Calaccess;

IncludeModuleLangFile(__FILE__);

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity\FieldError;
use Bitrix\Main\Entity\Result;
use Bitrix\Main\Entity\ScalarField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main,
    Bitrix\Main\Loader;



class Access
{
    public static function ChangePermission($data)
    {
             Loader::includeModule('calendar');

            if($data["group"] && $data["access"]) {
                $arCalendars = \CCalendarSect::GetList(/*array('arFilter' => array("OWNER_ID" => $atUserIds))*/);

                if ($arCalendars) {
                    foreach ($arCalendars as $calendar) {

                        if($data["deny_busy_invitation"] == "on")
                            \CCalendarUserSettings::Set(array('denyBusyInvitation' => true), $calendar["OWNER_ID"]);

                        //$getTypes = CCalendarType::GetList(array('arFilter' => array("XML_ID" => 'user')));

                        $calendar["ACCESS"][$data["group"]] = $data["access"];

                        $id = intVal(\CCalendar::SaveSection(array('arFields' => $calendar)));

                        /*CCalendarType::Edit(array(
                            'arFields' => array(
                                'XML_ID' => 'user',
                                'ACCESS' => array($_REQUEST["group"] => $_REQUEST["access"])
                            )
                        ));*/
                    }
                }
            }

    }
}