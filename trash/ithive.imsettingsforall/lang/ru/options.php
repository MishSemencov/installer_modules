<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$moduleLang = strtoupper(basename(dirname(dirname(__DIR__))));

$MESS[$moduleLang . '_TAB_SETTINGS'] = 'Настройки';
$MESS[$moduleLang . '_TAB_TITLE_SETTINGS'] = 'Настройки модуля';
$MESS[$moduleLang . '_TAB_DESCRIPTION_SETTINGS'] = 'Модуль заработает, если установлена галка "Модуль включен" и выбраны "Группы пользователей" для которых он включен.';
$MESS[$moduleLang . '_OPTION_SETTING_HEAD'] = 'Получать стандартные уведомления на:';
$MESS[$moduleLang . '_OPTION_USER_SITE'] = 'Сайт, мобильное и десктопное приложения';
$MESS[$moduleLang . '_OPTION_USER_MAIL'] = 'Электронную почту';
$MESS[$moduleLang . '_OPTION_USER_PUSH'] = 'Push-уведомления';
$MESS[$moduleLang . '_OPTION_START'] = 'Обработать';
$MESS[$moduleLang . '_OPTION_MODULE_ON_DESCRIPTION'] = '<b>Перед началом обработки данных, Сохранить настройки</b>';

