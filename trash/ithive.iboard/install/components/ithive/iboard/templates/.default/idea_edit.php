<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
    "ithive:iboard.menu",
    "",
    Array(
        "URL_TEMPLATES" => [
            "BASE_FOLDER" => $arResult['FOLDER'],
            "LINKS" => $arResult['URL_TEMPLATES']
        ],
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A"
    )
);?>
<?$APPLICATION->IncludeComponent(
	"ithive:iboard.idea.detail",
	"edit",
	Array(
        "IDEA_ID" => $arResult["VARIABLES"]["IDEA_ID"],
        "FORM_ID" => "idea_edit",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A"
	)
);?>