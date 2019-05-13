<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$module_id = GetModuleID(__FILE__);

\CJSCore::RegisterExt(
    'iboard_core',
    array(
        'js' => [
            '/bitrix/js/'.$module_id.'/iboard.script.js',
            '/bitrix/js/'.$module_id.'/idea.create.js'
        ],
        'css' => '/bitrix/css/'.$module_id.'/iboard.script.css',
        'rel' =>  array(
            'date',
            'tags',
            'socnetlogdest',
            'tasks_util_query',
            'tasks'
        ),
        'skip_core' => true,
        'lang' => '/bitrix/js/'.$module_id.'/lang/'.LANGUAGE_ID.'/iboard.php'
    )
);