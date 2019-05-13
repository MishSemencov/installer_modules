<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<div class="layout-block">
    <div class="cell">
        <?=Loc::Getmessage("IDEAS_DATE_UPDATE");?>
    </div>
    <div class="cell">
        <b><?=($arResult["IDEA"]["DATE_UPDATE"]) ? $arResult["IDEA"]["DATE_UPDATE"] : $arResult["IDEA"]["DATE_CREATE"]?></b>
    </div>
</div>

<hr>
