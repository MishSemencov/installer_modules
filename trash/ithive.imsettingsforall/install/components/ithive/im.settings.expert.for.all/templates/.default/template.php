<?php
//$MESS["MAIN_SAVE"] = "Сохранить";
//$MESS["MAIN_RESET"] = "Сбросить";
//$MESS["IM_SETTINGS_SAVE"] = "Сохранить";
//$MESS["IM_SETTINGS_CLOSE"] = "Закрыть";
//$MESS["IM_SETTINGS_WAIT"] = "Сохранение...";
//$MESS["UI_BUTTONS_CANCEL_BTN_TEXT"] = "Отменить";
//$MESS["UI_BUTTONS_CLOSE_BTN_TEXT"] = "Закрыть";


use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
CJSCore::Init(['popup']);
//Extension::load('popup');
Extension::load("ui.buttons");
Extension::load("ui.buttons.icons");
Extension::load("ui.alerts");
?>
<form method="post" id="im_settings_expert_for_all_form" >
    <input type="hidden" name="<?=$arParams["ACTION_VARIABLE"]?>" value="SAVE_FOR_ALL" />
    <?=bitrix_sessid_post();?>
    <table cellpadding="0" cellspacing="0" border="0"
           id="im_settings_expert_for_all_table"
           class="bx-messenger-settings-table-extra bx-messenger-settings-table-extra-notify">
        <tr>
            <th></th>
            <th><?=Loc::getMessage("IM_SETTINGS_NOTIFY_SITE")?></th>
            <th><?=Loc::getMessage("IM_SETTINGS_NOTIFY_EMAIL")?></th>
            <th><?=Loc::getMessage("IM_SETTINGS_NOTIFY_PUSH")?></th>
        </tr>

        <?
        $moduleId = 'im';
        if(!empty($arResult["NOTIFY_NAMES"][$moduleId])) {
            ?>
            <tr>
                <td colspan="5" class="bx-messenger-settings-table-sep"><span><?= $arResult["NOTIFY_NAMES"][$moduleId]["NAME"] ?></span>
                </td>
            </tr>
            <?
            foreach ($arResult["NOTIFY_NAMES"][$moduleId]["NOTIFY"] as $notifyId => $notifyName) {
                //$notifyName = $notify["NAME"];
                ?>
                <tr>
                    <td><span><?= $notifyName ?></span></td>
                    <?
                    $item = CIMSettings::CLIENT_SITE;
                    // site|im|message
                    $itemValue = $arResult["SETTINGS"]["notify"]["$item|$moduleId|$notifyId"];
                    // disabled|site|im|message
                    $itemValueDisabled = $arResult["SETTINGS"]["notify"]["disabled|$item|$moduleId|$notifyId"];
                    ?>
                    <td>
                        <div style="white-space: nowrap;">
                            <input type="checkbox"
                                   name="notify|<?= $item ?>|<?= $moduleId ?>|<?= $notifyId ?>"
                                   id="notifyId|<?= $item ?>|<?= $moduleId ?>|<?= $notifyId ?>"
                                <?= $itemValue ? 'checked="checked"' : '' ?>
                                <?= $itemValueDisabled ? 'disabled="true"' : '' ?>
                                   data-save="1"
                                   data-module-id="<?=$moduleId?>"
                                   data-item="<?=$item?>"
                            />
                        </div>
                    </td>
                    <?
                    $item = CIMSettings::CLIENT_MAIL;
                    // site|im|message
                    $itemValue = $arResult["SETTINGS"]["notify"]["$item|$moduleId|$notifyId"];
                    // disabled|site|im|message
                    $itemValueDisabled = $arResult["SETTINGS"]["notify"]["disabled|$item|$moduleId|$notifyId"];
                    ?>
                    <td>
                        <div style="white-space: nowrap;">
                            <input type="checkbox"
                                   name="notify|<?= $item ?>|<?= $moduleId ?>|<?= $notifyId ?>"
                                   id="notifyId|<?= $item ?>|<?= $moduleId ?>|<?= $notifyId ?>"
                                <?= $itemValue ? 'checked="checked"' : '' ?>
                                <?= $itemValueDisabled ? 'disabled="true"' : '' ?>
                                   data-save="1"
                                   data-module-id="<?=$moduleId?>"
                                   data-item="<?=$item?>"
                            />
                        </div>
                    </td>
                    <?
                    $item = CIMSettings::CLIENT_PUSH;
                    // site|im|message
                    $itemValue = $arResult["SETTINGS"]["notify"]["$item|$moduleId|$notifyId"];
                    // disabled|site|im|message
                    $itemValueDisabled = $arResult["SETTINGS"]["notify"]["disabled|$item|$moduleId|$notifyId"];
                    ?>
                    <td>
                        <div style="white-space: nowrap;">
                            <input type="checkbox"
                                   name="notify|<?= $item ?>|<?= $moduleId ?>|<?= $notifyId ?>"
                                   id="notifyId|<?= $item ?>|<?= $moduleId ?>|<?= $notifyId ?>"
                                <?= $itemValue ? 'checked="checked"' : '' ?>
                                <?= $itemValueDisabled ? 'disabled="true"' : '' ?>
                                   data-save="1"
                                   data-module-id="<?=$moduleId?>"
                                   data-item="<?=$item?>"
                            />
                        </div>
                    </td>
                </tr>
                <?

            }
        }
        foreach ($arResult["NOTIFY_NAMES"] as $moduleId => $arNotify){
            if ($moduleId == 'im'){
                continue;
            }
            ?>
            <tr>
                <td colspan="5" class="bx-messenger-settings-table-sep"><span><?=$arResult["NOTIFY_NAMES"][$moduleId]["NAME"]?></span>
                </td>
            </tr>
            <?
            foreach($arResult["NOTIFY_NAMES"][$moduleId]["NOTIFY"] as $notifyId=>$notifyName) {
                ?>
                <tr>
                    <td><span><?=$notifyName?></span></td>
                    <?
                    $item = CIMSettings::CLIENT_SITE;
                    // site|im|message
                    $itemValue = $arResult["SETTINGS"]["notify"]["$item|$moduleId|$notifyId"];
                    // disabled|site|im|message
                    $itemValueDisabled = $arResult["SETTINGS"]["notify"]["disabled|$item|$moduleId|$notifyId"];
                    ?>
                    <td>
                        <div style="white-space: nowrap;">
                            <input type="checkbox"
                                   name="notify|<?=$item?>|<?=$moduleId?>|<?=$notifyId?>"
                                   id="notifyId|<?=$item?>|<?=$moduleId?>|<?=$notifyId?>"
                                <?=$itemValue?'checked="checked"':''?>
                                <?=$itemValueDisabled?'disabled="true"':''?>
                                   data-save="1"
                                   data-module-id="<?=$moduleId?>"
                                   data-item="<?=$item?>"
                            />
                        </div>
                    </td>
                    <?
                    $item = CIMSettings::CLIENT_MAIL;
                    // site|im|message
                    $itemValue = $arResult["SETTINGS"]["notify"]["$item|$moduleId|$notifyId"];
                    // disabled|site|im|message
                    $itemValueDisabled = $arResult["SETTINGS"]["notify"]["disabled|$item|$moduleId|$notifyId"];
                    ?>
                    <td>
                        <div style="white-space: nowrap;">
                            <input type="checkbox"
                                   name="notify|<?=$item?>|<?=$moduleId?>|<?=$notifyId?>"
                                   id="notifyId|<?=$item?>|<?=$moduleId?>|<?=$notifyId?>"
                                <?=$itemValue?'checked="checked"':''?>
                                <?=$itemValueDisabled?'disabled="true"':''?>
                                   data-save="1"
                                   data-module-id="<?=$moduleId?>"
                                   data-item="<?=$item?>"
                            />
                        </div>
                    </td>
                    <?
                    $item = CIMSettings::CLIENT_PUSH;
                    // site|im|message
                    $itemValue = $arResult["SETTINGS"]["notify"]["$item|$moduleId|$notifyId"];
                    // disabled|site|im|message
                    $itemValueDisabled = $arResult["SETTINGS"]["notify"]["disabled|$item|$moduleId|$notifyId"];
                    ?>
                    <td>
                        <div style="white-space: nowrap;">
                            <input type="checkbox"
                                   name="notify|<?=$item?>|<?=$moduleId?>|<?=$notifyId?>"
                                   id="notifyId|<?=$item?>|<?=$moduleId?>|<?=$notifyId?>"
                                <?=$itemValue?'checked="checked"':''?>
                                <?=$itemValueDisabled?'disabled="true"':''?>
                                   data-save="1"
                                   data-module-id="<?=$moduleId?>"
                                   data-item="<?=$item?>"
                            />
                        </div>
                    </td>
                </tr>
                <?
            }
        }
        ?>
        <tr>
            <td colspan="5">
                <input class="ui-btn ui-btn-lg ui-btn-primary" type="button" value="<?=Loc::getMessage("ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL")?>" id="im_settings_expert_for_all_submit_button">
                <??>
                <input class="ui-btn ui-btn-lg" type="reset" value="<?=Loc::getMessage("MAIN_RESET")?>">
                <??>
            </td>

        </tr>
    </table>
</form>
<script>
    BX.message({
        ITHIVE_IM_SETTINGS_FOR_ALL_CONFIRM_HEADER: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_FOR_ALL_CONFIRM_HEADER')?>',
        ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL')?>',
        ITHIVE_IM_SETTINGS_FOR_ALL_RESET: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_FOR_ALL_RESET')?>',
        ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_CONFIRM: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_CONFIRM')?>',
        ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_WAIT: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_WAIT')?>',
        ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_SUCCESS_HEADER: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_SUCCESS_HEADER')?>',
        ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_SUCCESS_CONTENT: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_SUCCESS_CONTENT')?>',
        ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_SUCCESS_COUNT: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_SUCCESS_COUNT')?>',
        ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_ERROR_HEADER: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_ERROR_HEADER')?>',
        ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_ERROR_USER: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_ERROR_USER')?>',
        ITHIVE_IM_SETTINGS_EXPERT_FOR_ALL_COMPONENT_MODULE_NOT_MODULE_ON: '<?=Loc::getMessage('ITHIVE_IM_SETTINGS_EXPERT_FOR_ALL_COMPONENT_MODULE_NOT_MODULE_ON')?>',
        IM_SETTINGS_CLOSE: '<?=Loc::getMessage('IM_SETTINGS_CLOSE')?>',
        IM_SETTINGS_SAVE: '<?=Loc::getMessage('IM_SETTINGS_SAVE')?>',
        IM_SETTINGS_WAIT: '<?=Loc::getMessage('IM_SETTINGS_WAIT')?>',
        MAIN_RESET: '<?=Loc::getMessage('MAIN_RESET')?>',
        MAIN_SAVE: '<?=Loc::getMessage('MAIN_SAVE')?>',
        UI_BUTTONS_CANCEL_BTN_TEXT: '<?=Loc::getMessage('UI_BUTTONS_CANCEL_BTN_TEXT')?>',
        UI_BUTTONS_CLOSE_BTN_TEXT: '<?=Loc::getMessage('UI_BUTTONS_CLOSE_BTN_TEXT')?>',
    });
    BX.ready(function(){
        let settingsCallback = [];
        let settingsDisabled = [];

        function callbackSaveForAll(e)
        {
            let t;
            if(e.target == undefined){
                t = e;
            }
            else{
                t = this;
            }

            let moduleId = t.getAttribute("data-module-id");
            let item = t.getAttribute("data-item");
            if(item == 'site')
            {
                if (BX(t.id.replace('|site|', '|email|')).disabled) { return true; }
                if (!t.checked) {BX(t.id.replace('|site|', '|email|')).checked = false;}
                else {BX(t.id.replace('|site|', '|email|')).checked = true;}
            }
            if(item == 'email')
            {
                if (BX(t.id.replace('|email|', '|site|')).disabled) { return true; }
                if (t.checked) {BX(t.id.replace('|email|', '|site|')).checked = true;}
            }

            //// same version
            // if(moduleId == 'im'){
            //     if(item == 'site')
            //     {
            //         if (BX(t.id.replace('|site|', '|email|')).disabled) { return true; }
            //         if (!t.checked) {BX(t.id.replace('|site|', '|email|')).checked = false;}
            //         else {BX(t.id.replace('|site|', '|email|')).checked = true;}
            //     }
            //     if(item == 'email')
            //     {
            //         if (BX(t.id.replace('|email|', '|site|')).disabled) { return true; }
            //         if (t.checked) {BX(t.id.replace('|email|', '|site|')).checked = true;}
            //     }
            //
            // }
            // else{
            //     if(item == 'site')
            //     {
            //         if (BX(t.id.replace('|site|', '|email|')).disabled) { return true; }
            //         if (!t.checked) {BX(t.id.replace('|site|', '|email|')).checked = false;}
            //         else {BX(t.id.replace('|site|', '|email|')).checked = true;}
            //     }
            //     if(item == 'email')
            //     {
            //         if (BX(t.id.replace('|email|', '|site|')).disabled) { return true; }
            //         if (t.checked) {BX(t.id.replace('|email|', '|site|')).checked = true;}
            //     }
            // }
            // // old version
            // if (BX(t.id.replace('|site|', '|email|')).disabled) {
            //     return true;
            // }
            // if (!t.checked) {
            //     BX(t.id.replace('|site|', '|email|')).checked = false;
            // }
            // else {
            //     BX(t.id.replace('|site|', '|email|')).checked = true;
            // }


        }

        function initCallbacks()
        {
            var inputs = BX.findChildren(BX('im_settings_expert_for_all_table'), {attribute : "data-save"}, true);
            for (var i = 0; i < inputs.length; i++){
                BX.bind(inputs[i], 'change', callbackSaveForAll);
                //settingsCallback[inputs[i].name] = callbackSaveForAll;
                if(inputs[i].disabled ){
                    settingsDisabled[inputs[i].name] = inputs[i].checked;
                }
            }
        }

        initCallbacks();

        function prepareSettings()
        {
            var settings = [];
            var values = {notify: {}};

            var inputs = BX.findChildren(BX('im_settings_expert_for_all_table'), {attribute : "data-save"}, true);
            for (var i = 0; i < inputs.length; i++)
            {
                if (inputs[i].tagName == 'INPUT' && inputs[i].type == 'checkbox')
                {
                    // if (typeof(settingsCallback[inputs[i].name]) == 'function')
                    //     settings[inputs[i].name] = settingsCallback[inputs[i].name](inputs[i]);
                    // else
                        settings[inputs[i].name] = inputs[i].checked;
                }
                else if (inputs[i].tagName == 'INPUT' && inputs[i].type == 'radio' && inputs[i].checked)
                {
                    // if (typeof(settingsCallback[inputs[i].name]) == 'function')
                    //     settings[inputs[i].name] = settingsCallback[inputs[i].name](inputs[i]);
                    // else
                        settings[inputs[i].name] = inputs[i].value;
                }
                else if (inputs[i].tagName == 'SELECT')
                {
                    // if (typeof(settingsCallback[inputs[i].name]) == 'function')
                    //     settings[inputs[i].name] = settingsCallback[inputs[i].name](inputs[i]);
                    // else
                        settings[inputs[i].name] = inputs[i][inputs[i].selectedIndex].value;
                }
            }

            //var values = settings['notifyScheme'] == 'simple'? {}: {notify: {}};
            for (var config in settings)
            {
                if (config.substr(0,7) == 'notify|')
                {
                    if (settingsDisabled[config])
                        continue;
                    if (values['notify'])
                        values['notify'][config.substr(7)] = settings[config];
                }
                // else
                // {
                //     values[config] = settings[config];
                // }
            }
            return values;
        }

        function saveForAllButtonClickHandler(event)
        {
            let bStart=false;
            let popup = BX.PopupWindowManager.create("popup-save-im-settings-for-all", false, {
                content: BX.message('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_CONFIRM'),
                width: 400, // ширина окна
                height: 100, // высота окна
                zIndex: 100, // z-index
                // closeIcon: {
                //     // объект со стилями для иконки закрытия, при null - иконки не будет
                //     opacity: 1
                // },
                closeIcon: false,
                titleBar: BX.message('ITHIVE_IM_SETTINGS_FOR_ALL_CONFIRM_HEADER'),
                closeByEsc: false, // закрытие окна по esc
                darkMode: false, // окно будет светлым или темным
                autoHide: false, // закрытие при клике вне окна
                draggable: true, // можно двигать или нет
                resizable: true, // можно ресайзить
                min_height: 100, // минимальная высота окна
                min_width: 100, // минимальная ширина окна
                lightShadow: true, // использовать светлую тень у окна
                angle: false, // появится уголок
                overlay: {
                    // объект со стилями фона
                    backgroundColor: 'black',
                    opacity: 500
                },
                buttons: [
                    new BX.PopupWindowButton({
                        text: BX.message('MAIN_SAVE'), // текст кнопки
                        id: 'popup-save-im-settings-for-all-save-btn', // идентификатор
                        className: 'ui-btn ui-btn-success', // доп. классы
                        events: {
                            click: function(e) {
                                if(bStart)
                                    return;
                                bStart = true;
                                // Событие при клике на кнопку
                                // TODO: соберем форму и отправим AJAX
                                this.addClassName('ui-btn-clock');
                                popup.setTitleBar(BX.message('IM_SETTINGS_WAIT'));
                                popup.setContent(BX.message('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_WAIT'));
                                BX.hide(BX('popup-save-im-settings-for-all-cancel-btn'));

                                let values = prepareSettings();
                                let settingsValues = JSON.stringify(values);
                                let dataAjax = {'<?=$arParams["ACTION_VARIABLE"]?>' : 'SAVE_FOR_ALL', SETTINGS: settingsValues, 'sessid': BX.bitrix_sessid()};
                                BX.ajax.post('/local/components/ithive/im.settings.expert.for.all/ajax.php',
                                    dataAjax,
                                    function(dataJson){
                                        let data = JSON.parse(dataJson);
                                        let header = '';
                                        let content = '';
                                        if(data.SUCCESS == 'Y'){
                                            header = BX.message('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_SUCCESS_HEADER');
                                            content += BX.message(data.SUCCESS_MESSAGE);
                                            content += '<br>';
                                            content += BX.message('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_SUCCESS_COUNT');
                                            content += data.COUNT;
                                        }
                                        else{
                                            header = BX.message('ITHIVE_IM_SETTINGS_FOR_ALL_SAVE_FOR_ALL_ERROR_HEADER');
                                            content = BX.message(data.ERROR);
                                        }
                                        popup.setTitleBar(header);
                                        popup.setContent(content);

                                        var btnClose = new BX.PopupWindowButton({
                                            text: BX.message('IM_SETTINGS_CLOSE'),
                                            id: 'popup-save-im-settings-for-all-close-btn',
                                            className: 'ui-btn', // доп. классы
                                            events: {
                                                click: function(e) {
                                                    popup.close();
                                                    //popup.destroy();
                                                }
                                            }
                                        });
                                        popup.setButtons([btnClose]);
                                    });
                            }
                        }
                    }),
                    new BX.PopupWindowButton({
                        text: BX.message('UI_BUTTONS_CANCEL_BTN_TEXT'),
                        id: 'popup-save-im-settings-for-all-cancel-btn',
                        className: 'ui-btn',
                        events: {
                            click: function(e) {
                                popup.close();
                            }
                        }
                    })
                ],
                events: {
                    onPopupShow: function() {
                        // Событие при показе окна
                    },
                    onPopupClose: function() {
                        // Событие при закрытии окна
                        popup.destroy();
                        bStart = false;
                    }
                }
            });

            popup.show();
            event.preventDefault() ;
            event.stopPropagation();
            return false;
        }

        BX.bind(BX('im_settings_expert_for_all_submit_button'), 'click', BX.delegate(saveForAllButtonClickHandler, this));

    });
</script>
