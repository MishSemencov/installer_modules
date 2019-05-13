<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


$defaultTaskComponentfolder = '/bitrix/components/bitrix/tasks.task/templates/.default';
$module_id = GetModuleID(__FILE__);

\CJSCore::RegisterExt(
    'fastobject_task',
    array(
        'rel' =>  array(
            'tasks_util_datepicker',
            'fx',
            'tasks_util_widget',
            'tasks_util_template',
            'tasks_itemsetpicker',
            'tasks_util_query',
            'tasks'
        ),
        'skip_core' => true,
        'lang' => $defaultTaskComponentfolder.'/lang/'.LANGUAGE_ID.'/template.php'
    )
);

\CJSCore::RegisterExt(
    'fastobject_core',
    array(
        'js' => [
            '/bitrix/js/'.$module_id.'/jquery.form.min.js',
            '/bitrix/js/'.$module_id.'/select.js',
        ],
        'css' => [
            '/bitrix/css/'.$module_id.'/tools.css',
            '/bitrix/css/'.$module_id.'/select.css',
        ],
        'rel' => ['ls'],
        'skip_core' => true,
        'lang' => '/bitrix/js/'.$module_id.'/lang/'.LANGUAGE_ID.'/select.php'
    )
);