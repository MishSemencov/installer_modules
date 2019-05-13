<?php
use Bitrix\Main\Loader,
    ITHive\Calaccess\Access,
    Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->SetTitle("Изменение прав на просмотр календарей пользователей");

IncludeModuleLangFile(__FILE__);
Loc::loadMessages(__FILE__);

//проверка доступа
$STAT_RIGHT = $APPLICATION->GetGroupRight("statistic");
if($STAT_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

//подключение модулей

CModule::IncludeModule("calendar");
CModule::IncludeModule('ithive.calaccess');

//получаем группы пользователей
$res = CGroup::GetList();

//получаем права
$arTasks = CCalendar::GetAccessTasks("calendar_section");

if($_REQUEST["group"] && $_REQUEST["access"])
    Access::ChangePermission($_REQUEST);


?>
<form name="form1" method="POST" action="<?=$APPLICATION->GetCurPage()?>?lang=ru">
    <div style="margin-bottom: 15px">
        <strong>Выберите группу:</strong>
        <select id="bxec-group" name="group">
            <?while ($arGroup = $res->Fetch()){?>
                <option value="G<?=$arGroup["ID"]?>" <?if($_REQUEST["group"] == "G".$arGroup["ID"]){?>selected="selected"<?}?>><?= htmlspecialcharsex($arGroup['NAME']);?></option>
            <?}?>
        </select>
    </div>
    <div style="margin-bottom: 15px"><strong>Установите права:</strong>

        <select id="bxec-calendar_section" name="access">

            <?foreach ($arTasks as $taskId => $task):?>
                <option value="<?=intval($taskId)?>" <?if($_REQUEST["access"] == $taskId){?>selected="selected"<?}?>><?= htmlspecialcharsex($task['title']);?></option>
            <?endforeach;?>
        </select>
    </div>
    <div style="margin-bottom: 15px"> <strong>Дополнительные настройки:</strong><br><br>
        <input id="deny_busy_invitation" type="checkbox" name="deny_busy_invitation" <?if($_REQUEST["deny_busy_invitation"] == "on"){?>checked="checked"<?}?>>
        <label for="deny_busy_invitation">Запрещать приглашать в события, если время занято</label>
    </div>
    <input type="submit" name="submit" value="Применить" />
</form>