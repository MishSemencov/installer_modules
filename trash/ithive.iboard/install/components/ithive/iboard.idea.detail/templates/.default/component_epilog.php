<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$ideaId = $arResult["IDEA"]["ID"];
$dateVisit = \Bitrix\Main\Type\DateTime::createFromTimestamp(time());
if ($ideaId > 0)
    \ITHive\IBoard\Ideas::update($ideaId, array("date_visit" => $dateVisit), false);
?>
