<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "SEF_MODE" => Array(
            "idea_add" => array(
                "NAME" => GetMessage("ITHIVE_IBOARD_SEF_PAGE_IDEA_ADD"),
                "DEFAULT" => "",
                "VARIABLES" => array(),
            ),
            "idea_detail" => array(
                "NAME" => GetMessage("ITHIVE_IBOARD_SEF_PAGE_IDEA_DETAIL"),
                "DEFAULT" => "idea/#IDEA_ID#/",
                "VARIABLES" => array("IDEA_ID", "SECTION_ID"),
            ),
            "ideas_list" => array(
                "NAME" => GetMessage("ITHIVE_IBOARD_SEF_PAGE_IDEA_LIST"),
                "DEFAULT" => "ideas/",
                "VARIABLES" => array("SECTION_ID"),
            ),
            "ideas_history" => array(
                "NAME" => GetMessage("ITHIVE_IBOARD_SEF_PAGE_IDEA_HISTORY"),
                "DEFAULT" => "history/",
            ),
            "ideas_archive" => array(
                "NAME" => GetMessage("ITHIVE_IBOARD_SEF_PAGE_IDEA_ARCHIVE"),
                "DEFAULT" => "archive/",
            ),
        ),
        "CACHE_TIME" => array("DEFAULT" => 3600),
    )
);