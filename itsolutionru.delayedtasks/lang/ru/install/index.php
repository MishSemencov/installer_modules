<?php defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

$moduleLang = strtoupper(basename(dirname(dirname(dirname(__DIR__)))));

$MESS[$moduleLang . '_MODULE_NAME'] = 'Тестовый модуль';
$MESS[$moduleLang . '_MODULE_DESCRIPTION'] = 'Тестовый модуль для итс';
$MESS[$moduleLang . '_PARTNER_NAME'] = 'Итс';
$MESS[$moduleLang . '_PARTNER_URI'] = 'site';
$MESS[$moduleLang . '_INSTALL_TITLE'] = 'Установка модуля "'.$MESS[$moduleLang . '_MODULE_NAME'].'"';
$MESS[$moduleLang . '_INSTALL_ERROR'] = 'Произошла ошибка при установке "'.$MESS[$moduleLang . '_MODULE_NAME'].'"';
$MESS[$moduleLang . '_UNINSTALL_TITLE'] = 'Удаление модуля "'.$MESS[$moduleLang . '_MODULE_NAME'].'"';
$MESS[$moduleLang . '_INSTALL_ERROR_WRONG_VERSION'] = 'Версия ядра системы не соответствует требованиям модуля, обновите систему и попробуйте установить модуль еще раз';