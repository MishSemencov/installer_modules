<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<div class="layout-block">
    <div class="cell">
        <?=Loc::Getmessage("IDEAS_CATEGORY");?>
    </div>
    <div class="cell">
        <a><b><?=$arResult["IDEA"]["CATEGORY"]["NAME"]?></b></a>
    </div>
</div>

<hr>