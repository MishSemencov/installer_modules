<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<div class="idea-comments-block" id="idea-comments-block"><?
    $APPLICATION->IncludeComponent(
        "bitrix:forum.comments",
        "bitrix24",
        array(
            "FORUM_ID" => $arResult["IDEA"]["FORUM"]["ID"],
            "ENTITY_TYPE" => "II",
            "ENTITY_ID" => $arResult["IDEA"]["ORIGIN_ID"],
            "ENTITY_XML_ID" => "IDEA_".$arResult["IDEA"]["ORIGIN_ID"],
            "URL_TEMPLATES_PROFILE_VIEW" => "/company/personal/user/#USER_ID#/",
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "IMAGE_HTML_SIZE" => 400,
            "MESSAGES_PER_PAGE" => 10,
            "PAGE_NAVIGATION_TEMPLATE" => "arrows",
            "EDITOR_CODE_DEFAULT" => "N",
            "SHOW_MODERATION" => "N",
            "SHOW_AVATAR" => "Y",
            "SHOW_RATING" => "Y",
            "SHOW_MINIMIZED" => "Y",
            "USE_CAPTCHA" => "N",
            "PREORDER" => "N",
            "SHOW_LINK_TO_FORUM" => "N",
            "SHOW_SUBSCRIBE" => "N",
            "FILES_COUNT" => 10,
            "SHOW_WYSIWYG_EDITOR" => "Y",
            "BIND_VIEWER" => "N",
            "AUTOSAVE" => true,
            "PERMISSION" => "U",
            "NAME_TEMPLATE" => "#NAME# #LAST_NAME#",
            "MESSAGE_COUNT" => 3,
            "PUBLIC_MODE" => "",
            "ALLOW_MENTION" => "Y",
            "USER_FIELDS_SETTINGS" => array()
        ),
        false,
        array("HIDE_ICONS" => "Y")
    );
    ?>
</div>