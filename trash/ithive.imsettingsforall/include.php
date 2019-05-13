<?php
use \Bitrix\Main\Context;
use \Bitrix\Main\Loader;

$moduleId = strtolower(basename(__DIR__));
//$server = Context::getCurrent()->getServer()->getDocumentRoot();

Loader::registerAutoLoadClasses($moduleId, [
    'ITHive\Im\SettingsForAll' => 'lib/settingsforall.php',
]);
