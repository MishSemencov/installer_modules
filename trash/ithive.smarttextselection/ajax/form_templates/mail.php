<?
global $APPLICATION;
$APPLICATION->IncludeComponent(
    "ithive:mail.form",
    ".default",
    Array(
        "DATA" => $data,
        "ACTION" => "mail",
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "COMPONENT_TEMPLATE" => ".default"
    )
);?>