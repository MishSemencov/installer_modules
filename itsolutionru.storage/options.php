<?php
CModule::IncludeModule("itsolutionru.storage");
$settings = AgentClassStorage::getSettings();
if(isset($_POST['update']))
{
    foreach($settings as $key => $setting) if(isset($settings[$key]['isChecking'])) $settings[$key]['isChecking'] = 0;
    foreach($_POST as $key => $post)
    {
        if(strpos($key,"checkbox") !== FALSE) $settings[substr($key,9)]['isChecking'] = 1;
        if(strpos($key,"text") !== FALSE) $settings[substr($key,5)]['availableP'] = intval($post);
        if(strpos($key,"notification") !== FALSE) $settings['notificationType'] = intval($_POST['notification']);
    }
    AgentClassStorage::saveSettings($settings);
    CAdminMessage::ShowNote("Успех.");
}

$aTabs = array(
    array("DIV" => "edit1", "TAB" => "Настройки", "ICON" => "ib_settings", "TITLE" => "Настройки оповещений при недостатке места на диске"),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$storages = AgentClassStorage::getStorageData();
$tabControl->Begin();
?>

<form method="POST" name="mainform" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
    <?$tabControl->BeginNextTab();?>
    <tr><th style="text-align:center;">Название ФС</th><th>Объем ФС</th><th>Доступно</th><th>Оповещать при недостатке места</th><th>Остаток места для оповещения</th></tr>
    <?foreach ($storages as $name => $storage):?>
        <tr>
            <td style="text-align:center;"><?=$name?></td>
            <td><?=$storage['overall']?> Мб</td>
            <td><?=$storage['availableM']?> Мб (<?=$storage['availableP']?>%)</td>
            <td style="text-align:center;"><input type="checkbox" name="checkbox-<?=$name?>" <? if($settings[$name]['isChecking']) echo " checked";?>></td>
            <td style="text-align:center;"><input type="number" name="text-<?=$name?>" style="width:15%" value="<?=$settings[$name]['availableP']?>">%</td>
        </tr>
    <?endforeach;?>
    <tr>
        <td colspan="3" class="adm-detail-content-cell-l">Способ оповещения: </td>
        <td colspan="2" class="adm-detail-content-cell-r">
            <select name="notification">
                <option value="1" <? if($settings['notificationType'] == 1) echo "selected";?>>Уведомление в Битрикс</option>
                <option value="2" <? if($settings['notificationType'] == 2) echo "selected";?>>Оповещения на E-mail</option>
                <option value="3" <? if($settings['notificationType'] == 3) echo "selected";?>>На E-mail и уведомлением</option>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="5" align="center">
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <div>Уведомления отправляются всем администраторам портала уведомлениями на самом портале и/или на привязанную к аккаунту почту.</div>
                </div>
            </div>
        </td>
    </tr>
    <?$tabControl->Buttons();?>
    <input type="submit" name="update" value="Сохранить" title="Сохранить" class="adm-btn-save">
    <?$tabControl->End();?>
</form>


