<?php
namespace ITHive\Tools;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader,
    ITHive\Calaccess\Access;

class AuthHandler
{
    public function OnBeforeUserLogin(&$arFields){
        if($arFields['LOGIN']){
            $rsUser = \CUser::GetByLogin($arFields['LOGIN']);
            if($arUser = $rsUser->Fetch()){
                if(empty($arUser['LAST_LOGIN']))
                    $arFields['FIRST_LOGIN'] = true;
            }
        }

    }

    public function OnAfterUserLogin(&$arFields){

        //постановка задачи пользователю при первой авторизации
        if($arFields['FIRST_LOGIN'] && $arFields['USER_ID']>0){

            Loader::includeModule('tasks');

            if(Loader::includeModule('ithive.calaccess')){
                $arSetCalendar = array("group"=>"G12","access"=>17,"deny_busy_invitation"=>"on");
                Access::ChangePermission($arSetCalendar);
            }

            $directorGroupCode = 'director';
            $linePersonalGroupCode = 'line_personal';
            $tradePointAdministratorGroupCode = 'trade_point_administrator';

            $arUserGroups = [];
            $resGroups = \CUser::GetUserGroupEx($arFields['USER_ID']);
            while($arGroup = $resGroups->fetch()){
                $arUserGroups[] = $arGroup['STRING_ID'];
            }

            if(in_array('director', $arUserGroups))
                $taskTplId = 7;
            elseif(in_array('trade_point_administrator', $arUserGroups))
                $taskTplId = 8;
            elseif(in_array('line_personal', $arUserGroups))
                $taskTplId = 9;
            else
                $taskTplId = 9;

            if($taskTplId)
                $res = \CTaskItem::addByTemplate($taskTplId, $arFields['USER_ID'], ['RESPONSIBLE_ID' => $arFields['USER_ID']]);

        }
    }
}