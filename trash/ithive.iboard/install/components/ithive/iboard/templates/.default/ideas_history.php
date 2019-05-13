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
	"ithive:iboard.ideas.history",
	"",
	Array(
        "USER_ID" => $GLOBALS["USER"]->GetID(),
		"ELEMENT_PAGE_COUNT" => "20",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A"
	)
);?>