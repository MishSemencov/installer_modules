<?
global $APPLICATION;
$APPLICATION->IncludeComponent(
    "ithive:event.create",
    ".default",
    Array(
        "DATA" => $data,
        "ACTION" => "event",
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "COMPONENT_TEMPLATE" => ".default"
    )
);?>