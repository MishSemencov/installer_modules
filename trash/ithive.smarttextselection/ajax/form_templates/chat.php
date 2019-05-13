<?
global $APPLICATION;
$APPLICATION->IncludeComponent(
    "ithive:chat.mess.create",
    ".default",
    Array(
        "DATA" => $data,
        "ACTION" => "chat",
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "COMPONENT_TEMPLATE" => ".default"
    )
);?>