$(document).ready(function () {

    var smartSelectionMenu = $('.smart-text-selection-menu');
    //menu click event handler
    $('.smart-text-selection-menu-item').click(function (e) {
        if (ITHSmartSelection.selectedData && (ITHSmartSelection.selectedData.text != '' || ITHSmartSelection.selectedData.images.length > 0)) {
            var action = $(this).data('action');
            //call form to action
            switch (action) {
                case ITHFastObjects.actions.task:
                    ITHFastObjects.showTaskForm(ITHSmartSelection.selectedData);
                    break;
                case ITHFastObjects.actions.mail:
                    ITHFastObjects.showMailForm(ITHSmartSelection.selectedData);
                    break;
                case ITHFastObjects.actions.event:
                    ITHFastObjects.showEventForm(ITHSmartSelection.selectedData);
                    break;
                case ITHFastObjects.actions.chat:
                    ITHFastObjects.showChatForm(ITHSmartSelection.selectedData);
                    break;
                case ITHFastObjects.actions.livefeed:
                    ITHFastObjects.showLiveFeedForm(ITHSmartSelection.selectedData);
                    break;
                case ITHFastObjects.actions.idea:
                    ITHFastObjects.ideaCreate(ITHSmartSelection.selectedData);
                    break;
            }
        }
    });

    //select content event handler
    // $(window).on('click', document, function (e) {
    $(document).click(function(e) {
        ITHSmartSelection.removeSelected();
        $(smartSelectionMenu).hide();

        var pos = {x: 0, y: 0};
        pos.x = e.pageX + 10;
        pos.y = e.pageY - 50;

        ITHSmartSelection.getSelected();

        try {
            if ((ITHSmartSelection.selectedData.text != '' && ITHSmartSelection.selectedData.text.length > 1) || ITHSmartSelection.selectedData.images.length > 0) {
                $(smartSelectionMenu).show();
                $(smartSelectionMenu).css('top', pos.y).css('left', pos.x);
            }
        } catch (e) {

        }
    });
});


var ITHSmartSelection = {

    selectedData: {},

    removeSelected: function () {
        this.selectedData = {};
    },

    /**
     * Метод получает выделенный текст, изображения и текущий url, записывает результат в параметр this.selectedData
     */
    getSelected: function () {

        var images = [];

        //get selected document fragment
        try {
            var domContent = window.getSelection().getRangeAt(0).cloneContents();


            //delete <script> elements form selected document fragment
            domContent.querySelectorAll('script').forEach(function (elem) {
                elem.parentNode.removeChild(elem);
            });

            var selectedText = '', range;

            if (window.getSelection) {
                range = window.getSelection();
                selectedText = range.toString();
            } else if (document.selection) {
                range = document.selection;
                selectedText = range.createRange().text;
            }
            //get text from selected document fragment
            //var selectedText = domContent.textContent;

            //get images from selected document fragment
            var imagesNodes = domContent.querySelectorAll('img');
            $.each(imagesNodes, function (i, value) {
                var image = [];
                image['width'] = $(value).attr('width');
                image['height'] = $(value).attr('height');
                if($(value).attr('data-bx-full') != undefined)
                    image['src'] = $(value).attr('data-bx-full');
                else
                    image['src'] = $(value).attr('src');
                images.push(image);
            });

            this.selectedData = {
                text: selectedText.replace(/\s{2,}/g, ' '),
                images: images,
                pageUrl: window.location.href
            };
        }
        catch (e) {
            this.selectedData = {};
        }

    }
};

var ITHFastObjects = {

    actions: {
        task: 'task',
        mail: 'mail',
        event: 'event',
        chat: 'chat',
        livefeed: 'livefeed',
        idea: 'idea'
    },

    stsPopup: {},

    actionFormClassName: 'fast-object-action-form',
    ajaxUrls: {
        htmlTemplate: '/bitrix/tools/ithive.smarttextselection/ajax/form_templates.php',
        action: '/bitrix/tools/ithive.smarttextselection/ajax/action.php'
    },

    /**
     * Метод создаёт модальное окно
     *
     * @param e - event
     * @param okText - тектс об успешной обработке формы
     * @param popupMode - флаг вызова submit из всплывающего окна
     */
    formSubmit: function (e, okText, popupMode) {
        var _this = this;
        console.log(e);
        var form = (popupMode)
            ? $(e.target).closest('.popup-window').find('form.' + _this.actionFormClassName)
            : $(e.target).closest('.fast-object-create-wrap').find('form.' + _this.actionFormClassName);
        $(form).find('.field-error').removeClass('field-error');

        var thisBtn = $(e.target);
        $(thisBtn).addClass('btn-loader');

        form.submit(function() {
            var options = {
                url: _this.ajaxUrls.action,
                dataType: 'json',
                type: 'POST',
                success: function (data) {
                    // console.log(data);
                    var success = data['status']==='success'?true:false;
                    data = data['data'];
                    if (success)
                    {
                        _this.setFormResult(form, data, true, okText);

                        $(thisBtn).hide();

                        if(data['type'] && popupMode){
                            _this.afterObjectCreate(data['type'], data['data']);
                        }

                        if (popupMode) {
                            setTimeout(function () {
                                _this.stsPopup.destroy()
                            }, 3000, _this.stsPopup);
                        }
                        else {
                            // setTimeout (function() {
                            if (data['data'] && data['data']['url'])
                                var newWindow = window.open(data['data']['url'], "_blank");
                            var myWindow = window.open("", "_self");
                            myWindow.document.write("");
                            myWindow.close();
                            // }, 500);

                            // location.href = data['data']['url'];
                            //     window.open('','_self').close();
                        }
                    }
                    else if (!success)
                    {
                        _this.setFormResult(form, data, false);
                    }
                    $(thisBtn).removeClass('btn-loader');
                }
            };
            $(this).ajaxSubmit(options);
            form.unbind('submit');
            return false;
        });
        form.submit();

        // if (!popupMode) {
        //     setTimeout (function() {var myWindow = window.open("", "_self");myWindow.document.write("");myWindow.close();},1000);
        // }
        //     window.open(data['data']['url'],'_self')
    },

    /**
     * Метод создаёт модальное окно
     *
     * @param content - html контент
     * @param title - заголовок всплывающего окна
     * @param btnText - тектс кнопки сабмита
     * @returns {BX.PopupWindow}
     */
    createPopupWindow: function (content, btnText, okText, title, popupClass) {
        var _this = this;
        this.stsPopup = new BX.PopupWindow(
            'smart-selected-text-popup',
            null,
            {
                content: content,
                titleBar: title,
                closeIcon: {right: "20px", top: "10px"},
                offsetLeft: 0,
                offsetTop: 0,
                draggable: {restrict: true},
                autoHide: false,
                overlay: {backgroundColor: 'black', opacity: '80'},
                buttons: [
                    new BX.PopupWindowButton({
                        text: btnText,
                        className: "popup-window-button-accept",
                        events: {
                            click: function (e) {

                                var form = $(e.target).parent().parent().find('form.' + _this.actionFormClassName);
                                $(form).find('.field-error').removeClass('field-error');

                                var thisBtn = $(e.target);
                                $(thisBtn).addClass('btn-loader');

                                form.submit(function() {
                                    var options = {
                                        url: _this.ajaxUrls.action,
                                        dataType: 'json',
                                        type: 'POST',
                                        success: function (data) {

                                            var success = data['status']==='success'?true:false;
                                            data = data['data'];
                                            if (success)
                                            {
                                                _this.setFormResult(form, data, true, okText);

                                                $(thisBtn).hide();

                                                if(data['type']){
                                                    _this.afterObjectCreate(data['type'], data['data']);
                                                }

                                                setTimeout(function () {
                                                    _this.stsPopup.destroy()
                                                }, 3000, _this.stsPopup);
                                            }
                                            else if (!success)
                                            {
                                                _this.setFormResult(form, data, false);
                                            }
                                            $(thisBtn).removeClass('btn-loader');
                                        }
                                    };
                                    $(this).ajaxSubmit(options);
                                    form.unbind('submit');
                                    return false;
                                });
                                form.submit();
                            }
                        }
                    }),
                    new BX.PopupWindowButton({
                        text: BX.message('FO_POPUP_CLOSE'),
                        className: "popup-window-button-link popup-window-button-link-cancel",
                        events: {
                            click: function (e) {
                                _this.stsPopup.destroy();
                            }
                        }
                    })
                ],
                events: {
                    onPopupClose: function (popupWindow) {
                        popupWindow.destroy();
                    }
                }
            }
        );
        this.stsPopup.show();


    },

    afterObjectCreate: function(type, data){
        switch (type){
            case ITHFastObjects.actions.event:
                if(data['url']){
                    var win = window.open(data['url']);
                    if (!win) {
                        alert(BX.message('FO_POPUP_ALLOW'));
                    }
                }
                break;
            case ITHFastObjects.actions.task:
                if(data['url']){
                    var win = window.open(data['url']);
                    if (!win) {
                        alert(BX.message('FO_POPUP_ALLOW'));
                    }
                }
                break;
        }
    },

    setFormResult: function(form, data, success, okText) {

        okText = (okText == undefined) ? '' : okText;

        var successClass = 'fast-object-action-form-result-success';
        var errorClass = 'fast-object-action-form-result-error';

        var resultWrap = $(form).find('.fast-object-action-form-result');

        $(form).find('label').removeClass('field-error');

        if(success === true)
        {
            $(resultWrap).removeClass(errorClass);
            $(resultWrap).addClass(successClass);
            $(resultWrap).html(okText)
        }
        else
        {
            var resultText = '';
            $(resultWrap).removeClass(successClass);
            $(resultWrap).addClass(errorClass);
            if (data instanceof Array && data.length > 0)
            {
                data.forEach(function (field) {
                    $(form).find('label[data-field-code=' + field+']').addClass('field-error');
                });
                resultText = BX.message('FO_FORM_RESULT_REQUIRED');
            }
            else
            {
                resultText = BX.message('FO_FORM_RESULT_ERROR');
            }
            $(resultWrap).html(resultText);
        }

    },

    setPopupWindowContent: function (html) {
        this.stsPopup.setContent(html);
    },

    /**
     * Метод делает ajax запрос и получает html шаблон для различных действий
     *
     * @param action - action name
     * @param data - данные выделенного контента
     * @param callback - функция callback
     */
    getHtmlTemplate: function (action, data, callback) {

        var params = {
            action: action,
            text: data.text,
            images: data.images,
            pageUrl: data.pageUrl,
            ideaId: data.ideaId,
            name: data.name,
            important: data.important,
            //pageUrl: data.pageUrl
        };

        var requestUrl = this.ajaxUrls.htmlTemplate;

        BX.ajax({
            url: requestUrl,
            start: true,
            emulateOnload: true,
            cache: false,
            data: params,
            method: 'POST',
            dataType: 'html',
            onsuccess: function (data) {
                callback(data);
            },
            onfailure: function () {
                console.log("ITHFastObjects.getHtmlTemplate.ajax: bad response");
            }
        });
    },

    getLoader: function () {
        return '<div class="fast-object-form-loader"></div>';
    },

    showTaskForm: function (data) {

        var btnText = BX.message('FO_TASK_SUBMIT_BTN');
        var okText = BX.message('FO_TASK_SUBMIT_SUCCESS');
        var title = BX.message('FO_TASK_FORM_TITLE');
        var popupClass = 'task-popup';

        this.createPopupWindow(this.getLoader(), btnText, okText, title, popupClass);
        this.getHtmlTemplate(this.actions.task, data, function (html) {
            ITHFastObjects.setPopupWindowContent(html);
        });
    },

    showMailForm: function (data) {

        var btnText = BX.message('FO_MAIL_SUBMIT_BTN');
        var okText = BX.message('FO_MAIL_SUBMIT_SUCCESS');
        var title = BX.message('FO_MAIL_FORM_TITLE');
        var popupClass = 'mail-popup';

        this.createPopupWindow(this.getLoader(), btnText, okText, title, popupClass);
        this.getHtmlTemplate(this.actions.mail, data, function (html) {
            ITHFastObjects.setPopupWindowContent(html);
        });
    },

    showEventForm: function (data) {

        var btnText = BX.message('FO_EVENT_SUBMIT_BTN');
        var okText = BX.message('FO_EVENT_SUBMIT_SUCCESS');
        var title = BX.message('FO_EVENT_FORM_TITLE');
        var popupClass = 'event-popup';

        this.createPopupWindow(this.getLoader(), btnText, okText, title, popupClass);
        this.getHtmlTemplate(this.actions.event, data, function (html) {
            ITHFastObjects.setPopupWindowContent(html);
        });
    },

    showChatForm: function (data) {

        var btnText = BX.message('FO_CHAT_SUBMIT_BTN');
        var okText = BX.message('FO_CHAT_SUBMIT_SUCCESS');
        var title = BX.message('FO_CHAT_FORM_TITLE');
        var popupClass = 'chat-popup';

        this.createPopupWindow(this.getLoader(), btnText, okText, title, popupClass);
        this.getHtmlTemplate(this.actions.chat, data, function (html) {
            ITHFastObjects.setPopupWindowContent(html);
        });
    },

    showLiveFeedForm: function (data) {

        var btnText = BX.message('FO_LIVEFEED_SUBMIT_BTN');
        var okText = BX.message('FO_LIVEFEED_SUBMIT_SUCCESS');
        var title = BX.message('FO_LIVEFEED_FORM_TITLE');
        var popupClass = 'lfeed-popup';

        this.createPopupWindow(this.getLoader(), btnText, okText, title, popupClass);
        this.getHtmlTemplate(this.actions.livefeed, data, function (html) {
            ITHFastObjects.setPopupWindowContent(html);
        });
    },

    ideaCreate: function (data) {
        $text = data.text;
        $images = "";
        $.each(data.images, function (i) {
            $images += this.src + "|";
        });
        var $form = '<form action="/iboard/add/" method="POST" id="tmp-idea-form" target="_blank"><input type="text" name="TEXT" value="' + $text + '"><input type="text" name="IMAGES" value="' + $images + '"></form>';
        $('body').append($form);
        $("#tmp-idea-form").submit();

        setTimeout(function () {
            $("#tmp-idea-form").remove();
        }, 3000);
    }
};
