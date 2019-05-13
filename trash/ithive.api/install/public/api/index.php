<?
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck", true);
define("EXTRANET_NO_REDIRECT", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<?

CModule::IncludeModule("rusvipavia.takeheli");
CModule::IncludeModule("rusvipavia.takejet");
CModule::IncludeModule("ithive.api");
$api = new \ITHive\API\Api();
$api->request();
$api->respond();

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php"); ?>