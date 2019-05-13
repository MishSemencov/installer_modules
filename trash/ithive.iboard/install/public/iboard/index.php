<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
?><?$APPLICATION->IncludeComponent(
	"ithive:iboard",
	".default",
	array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"SEF_FOLDER" => "/iboard/",
		"SEF_MODE" => "Y",
		"COMPONENT_TEMPLATE" => ".default",
		"SEF_URL_TEMPLATES" => array(
			"idea_add" => "add/",
            "idea_edit" => "edit/#IDEA_ID#/",
			"idea_detail" => "idea/#IDEA_ID#/",
			"ideas_list" => "ideas/",
			"ideas_history" => "history/",
			"ideas_archive" => "archive/",
		)
	),
	false
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>