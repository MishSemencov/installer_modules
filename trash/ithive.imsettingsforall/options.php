<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;
use \Bitrix\Main\Context;
use ITHive\Im\SettingsForAll;
//$arModConf = include __DIR__ . '/mod_conf.php';

// нужна для управления правами модуля
$moduleId = strtolower(basename(__DIR__));
$moduleLang = strtoupper(basename(__DIR__));

Loc::loadMessages(Context::getCurrent()->getServer()->getDocumentRoot().BX_ROOT."/modules/main/options.php");
Loc::loadMessages(Context::getCurrent()->getServer()->getDocumentRoot().BX_ROOT."/modules/main/public/component_props.php");
Loc::loadMessages(__FILE__);

$MOD_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MOD_RIGHT < "R") {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

CModule::IncludeModule("iblock");

Loader::includeModule($moduleId);

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$aTabs = [
    [// вкладка "Настройки"
        'DIV' => 'edit1', // Код вкладки
        'TAB' => Loc::getMessage($moduleLang . '_TAB_SETTINGS'), // то что написано на табе
        'TITLE' => Loc::getMessage($moduleLang . '_TAB_TITLE_SETTINGS'), // То что написано в области таба
        'OPTIONS' => [
            Loc::getMessage($moduleLang.'_OPTION_SETTING_HEAD'),
            [
                'notifySchemeSendSite', // Имя поля
                Loc::getMessage($moduleLang.'_OPTION_USER_SITE'), // Подпись поля
                '', // Значение по умолчанию
                [
                    'checkbox',
                    'Y',
                ], // тип с настройками
                '',
            ],
            ['notifySchemeSendEmail', // Имя поля
                Loc::getMessage($moduleLang.'_OPTION_USER_MAIL'), // Подпись поля
                '', // Значение по умолчанию
                [
                    'checkbox',
                    'Y',
                ], // тип с настройками
                '',
               ],
            ['notifySchemeSendPush', // Имя поля
                Loc::getMessage($moduleLang.'_OPTION_USER_PUSH'), // Подпись поля
                '', // Значение по умолчанию
                [
                    'checkbox',
                    'Y',
                ], // тип с настройками
                '',],
            Loc::getMessage($moduleLang.'_OPTION_MODULE_ON_DESCRIPTION'),
        ]
    ],
];


// сохранение
if ($request->get('RestoreDefaults') && check_bitrix_sessid()){
    // обнуляем настройки
    Option::delete($moduleId);

    // Что бы повторно не отправилась форма при обновлении страницы
    LocalRedirect($APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID . '&mid_menu=1&mid=' . urlencode($moduleId) .
        '&tabControl_active_tab=' . urlencode($request['tabControl_active_tab']) . '&sid=' . SITE_ID);

}
elseif ($request->isPost() && $request->getPost('update') && check_bitrix_sessid()){
    // Сохраняем настройки
    foreach ($aTabs as $aTab)
    {
        \__AdmSettingsSaveOptions($moduleId, $aTab['OPTIONS']);
    }
    // Что бы повторно не отправилась форма при обновлении страницы

    LocalRedirect($APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID . '&mid_menu=1&mid=' . urlencode($moduleId) .
       '&tabControl_active_tab=' . urlencode($request['tabControl_active_tab']) . '&sid=' . SITE_ID);

}


$limit = 100;

if($_REQUEST['work_start'] && $_REQUEST["lastpage"])
{
    $page = (int)$_REQUEST["lastpage"];
    echo SettingsForAll::actionImSettingsSaveForAll($page, $limit);
    die();
}

$clean_test_table = '<table id="result_table" cellpadding="0" cellspacing="0" border="0" width="100%" class="internal">'.
    '<tr class="heading">'.
    '<td>Текущее действие</td>'.
    '<td width="1%">&nbsp;</td>'.
    '</tr>'.
    '</table>';

// рисуем форму
$tabControl = new CAdminTabControl('tabControl', $aTabs);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<script type="text/javascript">

    var bWorkFinished = false;
    var bSubmit;

    function set_start(val)
    {
        document.getElementById('work_start').disabled = val ? 'disabled' : '';
        document.getElementById('work_stop').disabled = val ? '' : 'disabled';
        document.getElementById('progress').style.display = val ? 'block' : 'none';

        if (val)
        {
            ShowWaitWindow();
            document.getElementById('result').innerHTML = '<?=$clean_test_table?>';
            document.getElementById('status').innerHTML = 'Работаю...';

            document.getElementById('percent').innerHTML = '0%';
            document.getElementById('indicator').style.width = '0%';

            CHttpRequest.Action = work_onload;
            CHttpRequest.Send('<?= $_SERVER["PHP_SELF"]?>?lang=ru&mid=ithive.imsettingsforall&work_start=Y&lastpage=1');
        }
        else
            CloseWaitWindow();
    }

    function work_onload(result)
    {

        try
        {
            eval(result);

            iPercent = CurrentStatus[0];
            strNextRequest = parseInt(CurrentStatus[1]);
            strCurrentAction = CurrentStatus[2];
           // document.getElementById('percent').innerHTML = iPercent + '%';
           // document.getElementById('indicator').style.width = iPercent + '%';

          //  document.getElementById('status').innerHTML = 'Работаю...';

            if (strCurrentAction != 'null')
            {
                oTable = document.getElementById('result_table');
                oRow = oTable.insertRow(-1);
                oCell = oRow.insertCell(-1);
                oCell.innerHTML = strCurrentAction;
                oCell = oRow.insertCell(-1);
                oCell.innerHTML = '';
            }

            if (strNextRequest && document.getElementById('work_start').disabled)
                CHttpRequest.Send('<?= $_SERVER["PHP_SELF"]?>?lang=ru&mid=ithive.imsettingsforall&work_start=Y&lastpage=' + strNextRequest);
            else
            {
                set_start(0);
                bWorkFinished = true;
            }

        }
        catch(e)
        {
            CloseWaitWindow();
            document.getElementById('work_start').disabled = '';
            alert('Сбой в получении данных');
        }
    }

</script>
<?php $tabControl->Begin();?>
    <form method="POST"
          action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request['mid'])?>&amp;lang=<?=$request['lang']?>"
          name="<?=$moduleId?>_settings"
          enctype="multipart/form-data">

        <? foreach($aTabs as $aTab): ?>
            <? if($aTab['OPTIONS']): ?>
                <? $tabControl->BeginNextTab(); ?>
                <? __AdmSettingsDrawList($moduleId, $aTab['OPTIONS']); ?>
            <? endif; ?>
        <? endforeach; ?>

        <? /*$tabControl->BeginNextTab(); ?>

        <? // функционал настройки прав доступа к модулю
        require_once (Context::getCurrent()->getServer()->getDocumentRoot().BX_ROOT.'/modules/main/admin/group_rights.php');
        */?>
        <?$systemTabControl = new CAdminTabControl('saleSysTabControl', $systemTabs, true, true);

        $systemTabControl->Begin();
        $systemTabControl->BeginNextTab();
        ?>
        <tr>
            <td colspan="2" align="center">

                <input type=button value="Старт" id="work_start" onclick="set_start(1)" />
                <input type=button value="Стоп" disabled id="work_stop" onclick="bSubmit=false;set_start(0)" />
                <div id="progress" style="display:none;" width="100%">
                    <br />
                    <div id="status"></div>
                    <table border="0" cellspacing="0" cellpadding="2" width="100%">
                        <tr>
                            <td height="10">
                                <div style="border:1px solid #B9CBDF">
                                    <div id="indicator" style="height:10px; width:0%; background-color:#B9CBDF"></div>
                                </div>
                            </td>
                            <td width=30>&nbsp;<span id="percent">0%</span></td>
                        </tr>
                    </table>
                </div>
                <div id="result" style="padding-top:10px"></div>

            </td>
        </tr>

        <? $tabControl->Buttons(); ?>

        <script language="JavaScript">
            function RestoreDefaults()
            {
                if(confirm('<?echo AddSlashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>'))
                    window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)."&".bitrix_sessid_get();?>";
            }
        </script>
        <input type="submit" name="update" <?if ($MOD_RIGHT<'W') echo "disabled" ?> value="<?echo GetMessage('MAIN_SAVE')?>">
        <input type="reset" name="reset" value="<?echo GetMessage('MAIN_RESET')?>">
        <?=bitrix_sessid_post();?>
        <input type="button" <?if ($MOD_RIGHT<'W') echo "disabled" ?> title="<?echo GetMessage('MAIN_HINT_RESTORE_DEFAULTS')?>" OnClick="RestoreDefaults();" value="<?echo GetMessage('MAIN_RESTORE_DEFAULTS')?>">

    </form>
<?
$systemTabControl->End();?>
<?php $tabControl->End();?>
