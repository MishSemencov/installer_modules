<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?use Bitrix\Main\Localization\Loc;
$reminderExtensions = array("jquery", "socnetlogdest");
CJSCore::Init($reminderExtensions);
?>
<?if ($isCreator) {?>
    <div class="layout-block">
        <div class="cell">
            <?=Loc::Getmessage("IDEAS_WATCHERS");?>
        </div>
        <div class="cell">
            <?
            $APPLICATION->IncludeComponent(
                'bitrix:tasks.widget.member.selector',
                '',
                array(
                    'TEMPLATE_CONTROLLER_ID' => 'idea-watchers'.time(),
                    'DISPLAY' => 'inline',
                    'MIN' => 1,
                    'TYPES' => array('USER', 'USER.EXTRANET', 'USER.MAIL'),
                    'INPUT_PREFIX' => 'IDEA[WATCHERS]',
                    'ATTRIBUTE_PASS' => array(
                        'ID',
                        'NAME',
                        'LAST_NAME',
                        'EMAIL',
                    ),
                    'DATA' => $arResult["IDEA"]["WATCHERS"],
                    'READ_ONLY' => 'N',
                ),
                false,
                array("HIDE_ICONS" => "Y", "ACTIVE_COMPONENT" => "Y")
            )
            ?>
        </div>
    </div>

    <hr>
<?} else {?>
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
                                <div class="info">
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
<?}?>
