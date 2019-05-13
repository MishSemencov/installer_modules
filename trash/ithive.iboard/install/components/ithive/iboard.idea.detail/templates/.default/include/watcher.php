<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<?if (is_array($arResult["IDEA"]["WATCHERS"]) && count($arResult["IDEA"]["WATCHERS"]) > 0) {?>
    <div class="layout-block">
        <div class="cell">
            <?=Loc::Getmessage("IDEAS_WATCHERS");?>
        </div>
        <div class="cell">
            <ul class="users-list">
                <?foreach ($arResult["IDEA"]["WATCHERS"] as $arWatcher) {?>
                    <li>
                        <div class="user-layout">
                            <?if ($arWatcher["SRC"]) {?>
                                <div class="image">
                                    <a href="/company/personal/user/<?=$arWatcher["ID"]?>/">
                                        <figure class="image-cover round">
                                            <img src="<?=$arWatcher["SRC"]?>" alt="<?=$arWatcher["NAME"] . " " . $arWatcher["LAST_NAME"]?>">
                                        </figure>
                                    </a>
                                </div>
                            <?}?>
                            <div class="info idea-user-name">
                                <a href="/company/personal/user/<?=$arWatcher["USER_ID"]?>/"><?=$arWatcher["NAME"] . " " . $arWatcher["LAST_NAME"]?></a>
                            </div>
                        </div>
                    </li>
                <?}?>
            </ul>
        </div>
    </div>

    <hr>
<?}?>