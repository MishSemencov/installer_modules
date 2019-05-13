<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?ob_start();?>
<?$APPLICATION->IncludeComponent(
    "ithive:iboard.menu",
    "white",
    Array(
        "URL_TEMPLATES" => [
            "BASE_FOLDER" => $arResult['FOLDER'],
            "LINKS" => $arResult['URL_TEMPLATES']
        ],
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A"
    )
);?>
<?
$menuContent = ob_get_contents();
ob_end_clean();
$APPLICATION->AddViewContent('iboard_menu', $menuContent);
?>
<?
$APPLICATION->IncludeComponent(
	"ithive:iboard.ideas.create",
	"",
	Array(
		"FORM_ID" => "idea_add_form",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A"
	)
);?>

