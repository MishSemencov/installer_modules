<?
namespace ITHive\Calaccess;

IncludeModuleLangFile(__FILE__);

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\IO\File;
use Bitrix\Main\Text\Encoding;

class Access
{
    public function ChangePermission($data)
    {
        if (CModule::IncludeModule("calendar")){
            if($data["group"] && $data["access"]) {
                $arCalendars = CCalendarSect::GetList(/*array('arFilter' => array("OWNER_ID" => $atUserIds))*/);

                if ($arCalendars) {
                    foreach ($arCalendars as $calendar) {

                        if($data["deny_busy_invitation"] == "on")
                            CCalendarUserSettings::Set(array('denyBusyInvitation' => true), $calendar["OWNER_ID"]);

                        //$getTypes = CCalendarType::GetList(array('arFilter' => array("XML_ID" => 'user')));

                        $calendar["ACCESS"][$data["group"]] = $data["access"];

                        $id = intVal(CCalendar::SaveSection(array('arFields' => $calendar)));

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
}