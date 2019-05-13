<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage('IM_SETTINGS_EXPERT_FOR_ALL_DESCRIPTION_NAME'),
	"DESCRIPTION" => Loc::getMessage('IM_SETTINGS_EXPERT_FOR_ALL_DESCRIPTION_DESCRIPTION'),
	"ICON" => '/images/icon.gif',
	"SORT" => 20,
	"PATH" => array(
		"ID" => 'ithive',
		"NAME" => Loc::getMessage('IM_SETTINGS_EXPERT_FOR_ALL_DESCRIPTION_GROUP'),
		"SORT" => 10,
		"CHILD" => array(
			"ID" => 'standard',
			"NAME" => Loc::getMessage('IM_SETTINGS_EXPERT_FOR_ALL_DESCRIPTION_DIR'),
			"SORT" => 10
		)
	),
);