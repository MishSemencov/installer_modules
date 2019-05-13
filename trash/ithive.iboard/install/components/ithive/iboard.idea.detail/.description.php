<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage('ITHIVE_IBOARD_IDEA_DETAIL_COMPONENT_NAME'),
	"DESCRIPTION" => GetMessage('ITHIVE_IBOARD_COMPONENT_IDEA_DETAIL_DESCRIPTION'),
	"ICON" => "images/icon.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "HIVE",
		"NAME" => GetMessage('ITHIVE_IBOARD_COMPONENT_DEVELOPER_TITLE'),
		"CHILD" => array(
			"ID" => "iboard",
			"NAME" => GetMessage('ITHIVE_IBOARD_COMPONENT_CATEGORY_TITLE'),
			"CHILD" => array(
				"ID" => "iboard_components",
			),
		),
	),
);

?>