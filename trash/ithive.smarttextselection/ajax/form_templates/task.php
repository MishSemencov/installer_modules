<?
global $APPLICATION;
$APPLICATION->IncludeComponent(
    "ithive:task.create",
    ".default",
    Array(
        "DATA" => $data,
        "ACTION" => "task",
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "COMPONENT_TEMPLATE" => ".default"
    )
);?>