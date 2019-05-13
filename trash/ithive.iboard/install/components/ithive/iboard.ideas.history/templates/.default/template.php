<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);?>
<div class="row middle-xs">
    <div class="col-xs-6">
        <div class="typical-title"><?=Loc::Getmessage("HISTORY_TITLE")?></div>
    </div>
    <div class="col-xs-6 align-right">
    </div>
</div>

<table class="typical-table">
    <thead>
        <th>
            <a href="<?=(isset($_REQUEST["sort"]) && $_REQUEST["sort"] == "asc") ? "?sort=desc" : "?sort=asc"?>">
                <?=Loc::Getmessage("DATE")?>
                <i class="icon icon-chevron-<?=(isset($_REQUEST["sort"]) && $_REQUEST["sort"] == "asc") ? "down" : "up"?>"></i>
            </a>
        </th>
        <th><?=Loc::Getmessage("AUTHOR")?></th>
        <th><?=Loc::Getmessage("IDEA")?></th>
<!--        <th>--><?//=Loc::Getmessage("WHAT_CHANGED")?><!--</th>-->
        <th><?=Loc::Getmessage("DATA")?></th>
    </thead>
    <tbody>
        <?
        if (is_array($arResult["HISTORY"]) && count($arResult["HISTORY"]) >0) {
            foreach ($arResult["HISTORY"] as $arHistory) { ?>
                <tr>
                    <td><?= $arHistory["DATE"] ?></td>
                    <td><?= $arResult["USER"]["NAME"] . " " . $arResult["USER"]["LAST_NAME"]?></td>
                    <td><?=($arHistory["IDEA_ID"] > 0 ) ? Loc::Getmessage("IDEA_NUMBER", array("#IDEA_ID#" => $arHistory["IDEA_ID"])) : ""?></td>
<!--                    <td>--><?//= $arHistory["AUTHOR"] ?><!--</td>-->
                    <td><?=$arHistory["DATA"]?></td>
                </tr>
            <?
            }
        }?>
    </tbody>
</table>