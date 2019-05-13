<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$moduleId = GetModuleID(__FILE__);

\Bitrix\Main\Loader::includeModule('main');
\Bitrix\Main\Loader::includeModule($moduleId);

$SJOptions = \ITHive\SJTickets\SJOptions::getInstance();

$aTabs = array(
    array("DIV" => "sjtickets_options", "TAB" => 'Параметры модуля', "ICON" => "", "TITLE" => 'Настройки параметров для работы модуля'),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_REQUEST["save"] != "" || $_REQUEST["apply"] != "") && check_bitrix_sessid()) {

    if ($_POST['tabControl_active_tab'] === 'sjtickets_options') {

        COption::SetOptionString($moduleId, 'api_token', $_POST['api_token']);
        COption::SetOptionString($moduleId, 'sj_support_user', $_POST['sj_support_user']);
        COption::SetOptionString($moduleId, 'mess_source', $_POST['mess_source']);
    }

    if ($_REQUEST["save"] != "" && $_GET["return_url"] != "") {
        LocalRedirect($_GET["return_url"]);
    }
    LocalRedirect("/bitrix/admin/settings.php?mid=" . $moduleId . "&mid_menu=1&lang=" . LANGUAGE_ID . ($_GET["return_url"] ? "&return_url=" . urlencode($_GET["return_url"]) : "") . "&" . $tabControl->ActiveTabParam());

}

$api_token = $SJOptions->getApiToken();
$sj_support_user = $SJOptions->getSupportSJUser();
$mess_source = $SJOptions->getTicketMessSourceId();

$moduleReady = $SJOptions->isReady();

?>
<form method="POST" action="/bitrix/admin/settings.php?mid=<?= $moduleId ?>&mid_menu=1&lang=<? echo LANGUAGE_ID ?><? echo $_GET["return_url"] ? "&amp;return_url=" . urlencode($_GET["return_url"]) : "" ?>">
    <?
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td>
            <?
            if(!$moduleReady){
                $arReadyStatus = [
                    'MESSAGE' => 'Модуль не готов к работе',
                    'DETAILS' => 'Выполните все настройки',
                    'TYPE' => 'ERROR'
                ];
            }elseif($moduleReady){
                $arReadyStatus = [
                    'MESSAGE' => 'Модуль готов к работе',
                    'TYPE' => 'OK'
                ];
            }
            CAdminMessage::ShowMessage($arReadyStatus);
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <table>
                <tr>
                    <td>
                        <b>Токен API: </b>
                    </td>
                    <td>
                        <input type="text" name="api_token" value="<?=$api_token?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Пользователь тех. поддержки SoftJoys (id): </b>
                    </td>
                    <td>
                        <input type="text" name="sj_support_user" value="<?=$sj_support_user?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Источник тех. поддержки (id): </b>
                    </td>
                    <td>
                        <input type="text" name="mess_source" value="<?=$mess_source?>">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <?
    $tabControl->EndTab();
    $tabControl->Buttons(array(
        "btnApply" => false,
        "back_url" => $_GET["return_url"] ? $_GET["return_url"] : "/bitrix/admin/settings.php?mid=" . $moduleId . "&mid_menu=1&lang=" . LANGUAGE_ID,
    ));
    ?>
    <? echo bitrix_sessid_post(); ?>
    <input type="hidden" name="lang" value="<? echo LANGUAGE_ID ?>">
    <?
    $tabControl->End();
    ?>
</form>