<?
/**
 * Uninstall module step. Request
 *
 * @package ITHive\API
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 *
 * @global Bitrix\Main\Application|CMain $APPLICATION
 * @var ithive_api $Module Setup by bitrix modules installer
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE) die();

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\NotSupportedException;

Loc::loadMessages(__FILE__);

/* there is no need, this was checked by Bitrix */
//if (!check_bitrix_sessid()) return;

// check the availability of Bitrix created variable $Module for working with module
if (!isset($Module->MODULE_ID)) {
    throw new NotSupportedException(Loc::getMessage("MOD_NOT_SUPPORTED"));
}

// id = <MODULE_ID>
// lang = <LANGUAGE_ID>
// install = <anything>, "Y" by default
// step = <step_number> - go to <step_number> REQUIRED! last step might have nonexistent step number
?>
<form action="<?= $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="id" value="<?= $Module->MODULE_ID ?>">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    <?= bitrix_sessid_post(); ?>

    <?
    /** @noinspection PhpDynamicAsStaticMethodCallInspection */
    CAdminMessage::ShowMessage(Loc::getMessage("MOD_UNINST_WARN"));
    ?>

    <p><?= Loc::getMessage("MOD_UNINST_SAVE") ?></p>
    <p>
        <label for="savedata">
            <input type="checkbox" name="savedata" id="savedata" value="Y" checked="checked" />
            <?= Loc::getMessage("MOD_UNINST_SAVE_TABLES") ?>
        </label>
    </p>
    <p>
        <label for="save_option">
            <input type="checkbox" name="save_option" id="save_option" value="Y" checked="checked" />
            <?= Loc::getMessage("ITHIVE_API_MOD_UNINST_SAVE_OPTIONS") ?>
        </label>
    </p>

    <input type="submit" name="uninstall" value="<?= Loc::getMessage("MOD_UNINST_DEL") ?>">
<form>