<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__); 

try
{
	if (!Loader::includeModule('im'))
		throw new LoaderException('module im is not loaded');
	if (!Loader::includeModule('ithive.imsettingsforall'))
		throw new LoaderException('module ithive.imsettingsforall is not loaded');


	$arComponentParameters = array(
		'GROUPS' => array(
		),
		'PARAMETERS' => array(
			'ACTION_VARIABLE' =>  array(
				'PARENT' => 'BASE',
				'NAME' => Loc::getMessage('IM_SETTINGS_EXPERT_FOR_ALL_PARAMETERS_ACTION_VARIABLE'),
				'TYPE' => 'STRING',
				'DEFAULT' => 'ACTION'
			),
			'AJAX' =>  array(
//				'PARENT' => 'BASE',
//				'NAME' => Loc::getMessage('IM_SETTINGS_EXPERT_FOR_ALL_PARAMETERS_AJAX'),
//				'TYPE' => 'STRING',
//				'DEFAULT' => 'ACTION'
			),
		),
	);
}
catch (LoaderException $e)
{
	ShowError($e->getMessage());
}
