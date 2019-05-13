var ps;

window.ITHIdea = {
    ajaxUrl: "/bitrix/tools/ithive.iboard/ajax/action.php",
    lastSortState: {},

    init: function ($params) {
        $ideaId = $params.ideaId;
        
        BX.bindDelegate(document.body, 'click', {className: 'ideas-filter-wrap'}, BX.delegate(this.showIdeasFilter, this));
        BX.bindDelegate(document.body, 'mousedown', {className: 'ideas-event-create'}, BX.delegate(this.eventCreate, this));
        BX.bindDelegate(document.body, 'mousedown', {className: 'ideas-lfeed-post'}, BX.delegate(this.lfeedCreate, this));
        BX.bindDelegate(document.body, 'click', {className: 'idea-active-filter-del'}, BX.delegate(this.deleteIdeaActiveFilter, this));
        BX.bindDelegate(document.body, 'click', {className: 'idea-set-minimized'}, BX.delegate(this.setMinimized, this));
        BX.bindDelegate(document.body, 'click', {className: 'ideas-list-left'}, BX.delegate(this.scrollLeft, this));
        BX.bindDelegate(document.body, 'click', {className: 'ideas-list-right'}, BX.delegate(this.scrollRight, this));

        if (parseInt($ideaId) > 0) {
            BX.bindDelegate(document.body, 'click', {className: 'idea-task-create'}, BX.delegate(function () {
                ITHFastObjects.showTaskForm({ideaId: $ideaId, pageUrl: location.href});
            }, this));
            BX.bindDelegate(document.body, 'click', {className: 'idea-chat-create'}, BX.delegate(function () {
                ITHFastObjects.showChatForm({ideaId: $ideaId, pageUrl: location.href});
            }, this));
            BX.bindDelegate(document.body, 'click', {className: 'idea-event-create'}, BX.delegate(function () {
                ITHFastObjects.showEventForm({ideaId: $ideaId, pageUrl: location.href});
            }, this));
            BX.bindDelegate(document.body, 'click', {className: 'idea-live-feed-create'}, BX.delegate(function () {
                ITHFastObjects.showLiveFeedForm({ideaId: $ideaId, pageUrl: location.href});
            }, this));
            BX.bindDelegate(document.body, 'click', {className: 'idea-mail-create'}, BX.delegate(function () {
                ITHFastObjects.showMailForm({ideaId: $ideaId, pageUrl: location.href});
            }, this));
        }

        if ($("#ideas-scroller").length > 0)
            ps = new PerfectScrollbar(document.getElementById('ideas-scroller'), {
                wheelSpeed: 2,
                wheelPropagation: true,
                minScrollbarLength: 20,
                suppressScrollY: true
            });
    },

    scrollLeft: function () {
        var curScroll = $("#ideas-scroller").scrollLeft();
        newScroll = curScroll - 100;
        $("#ideas-scroller").scrollLeft(newScroll);
    },

    scrollRight: function () {
        var curScroll = $("#ideas-scroller").scrollLeft();
        newScroll = curScroll + 100;
        $("#ideas-scroller").scrollLeft(newScroll);
    },

    showAjaxShadow: function(element, idArea, localeShadow)
    {

        if (localeShadow == true){
            $(element).addClass('ajax-shadow');
            $(element).addClass('ajax-shadow-r');
        }
        else{
            if ($('div').is('#'+idArea)){

            }
            else
            {
                $('<div id="'+idArea+'" class="ajax-shadow"></div>').appendTo('body');
            }

            $('#'+idArea).show();
            $('#'+idArea).width($(element).width());
            $('#'+idArea).height($(element).outerHeight());
            if ($(element).length && $(element).offset()) {
                $('#' + idArea).css('top', $(element).offset().top + 'px');
                $('#' + idArea).css('left', $(element).offset().left + 'px');
            }
        }

    },

    closeAjaxShadow: function(idArea, localShadow)
    {
        if (localShadow == true){
            $(idArea).removeClass('ajax-shadow-r');
            $(idArea).removeClass('ajax-shadow');
        }
        else{
            $('#'+idArea).hide();
        }
    },

    makeRandomColor: function () {
        color = "";
        possible = "abcdef0123456789";

        for (var i = 0; i < 6; i++)
            color += possible.charAt(Math.floor(Math.random() * possible.length));

        return color;
    },

    getDefaultColors: function () {
        return [
            "#1bbc9b", "#16a086", "#2dcc70", "#3598db",
            "#297fb8", "#9a59b5", "#8d44ad", "#34495e",
            "#2d3e50", "#f1c40f", "#e67f22", "#d25400",
            "#e84c3d", "#c1392b", "#95a5a5", "#7e8c8d",
        ]
    },

    createColorPopup: function ($params) {
        $obj = $($params.obj);
        $titleContainer = $obj.closest(".idea-title");
        $categoryId = $titleContainer.find(".idea-category-id-input").val();

        if ($(".color-picker-wrap").length == 0) {
            colors = this.getDefaultColors();
            $colorHtml = "<div class='color-picker-wrap visible'><ul class='color-picker-list' data-entity='" + $categoryId + "'>";
            $.each(colors, function () {
                $clearColor = this.replace(/#/g,"");
                $colorHtml += "<li class='color-picker-list-item' data-color='" + $clearColor + "' style='background-color: " + this + "'>" + this + "</li>";
            });
            $colorHtml += "</ul></div>";
            $("body").append($colorHtml);
            BX.bindDelegate(document.body, 'click', {className: 'color-picker-list-item'}, BX.delegate(this.setColor, this));
        } else {
            $(".color-picker-list").data("entity", $categoryId);
            $(".color-picker-wrap").addClass("visible");
        }

        $top = $obj.offset().top;
        $left = $obj.offset().left;

        $(".color-picker-wrap").css("top", $top + "px");
        $(".color-picker-wrap").css("left", $left + "px");

        return $colorHtml;
    },

    setColor: function () {
        context = BX.proxy_context;
        $colorWrap = $(context).closest(".color-picker-wrap");
        $color = $(context).data("color");
        $entityId = $(context).closest(".color-picker-list").data("entity");
        $('[name="IDEA_CATEGORY_COLOR_' + $entityId + '"]').val($color);
        $($('[name="IDEA_CATEGORY_COLOR_' + $entityId + '"]').closest(".idea-title")).css("background-color", "#" + $color);
        $($('[name="IDEA_CATEGORY_COLOR_' + $entityId + '"]').closest(".idea-category-wrap")).find(".idea").css("border-color", "#" + $color);

        $colorWrap.removeClass("visible");
    },

    setReminderDate: function() {
        $("#idea-reminder-period-select").val("false");
        $("#idea-reminder-period-time").hide();
    },

    setReminderPeriod: function() {
        $this = $("#idea-reminder-period-select");
        $("#idea-reminder-date").val("");
        $("#idea-reminder-period-time").show();
        switch($($this).val()) {
            case "false":
                $("#idea-reminder-period-time").hide();
                break;
            case "day":
                $(".idea-reminder-period-daily").show();
                $("#idea-reminder-period-week-days").hide();
                $(".idea-reminder-period-days").hide();
                break;
            case "week":
                $(".idea-reminder-period-daily").show();
                $("#idea-reminder-period-week-days").show();
                $(".idea-reminder-period-days").hide();
                break;
            case "month":
                $(".idea-reminder-period-daily").show();
                $("#idea-reminder-period-week-days").hide();
                $(".idea-reminder-period-days").show();
                break;
        }
    },

    changeImportant: function ($params) {
        $obj = $($params.obj);
        $importantInput = $obj.find(".idea-important-input");
        $importantUpdate = $obj.find(".idea-important-update");
        $importantText = $obj.find(".idea-important-text");
        $importantVal = ($($obj).hasClass("active")) ? 0 : 1;
        $importantUpdateVal = ($importantUpdate.val() == "N") ? "Y" : "N";
        $importantUpdateText = ($($obj).hasClass("active")) ? BX.message("MAKE_IDEA_IMPORTANT") : BX.message("IMPORTANT_IDEA");

        $obj.toggleClass("active");
        $importantInput.val($importantVal);
        $importantUpdate.val($importantUpdateVal);
        $importantText.html($importantUpdateText);
    },

    setImportant: function($obj, $ideaId) {
        $importantVal = ($($obj).hasClass("active")) ? 0 : 1;
        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "update",
                ideaId: $ideaId,
                updateFields: {important: $importantVal}
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                if ($.parseJSON(data) == null) {
                    $($obj).toggleClass("active");
                    if ($($obj).hasClass("active"))
                        $($obj).find("span").html(BX.message("IMPORTANT_IDEA"));
                    else
                        $($obj).find("span").html(BX.message("MAKE_IDEA_IMPORTANT"));
                }
            },
            onfailure: function(){
                // console.log("fail");
            }
        });
    },

    onDeleteIdea: function ($ideaId) {
        btn = [
            new BX.PopupWindowButton({
                text: BX.message("DELETE"),
                className: "popup-window-button-accept",
                events: {
                    click: function (e) {
                        window.ITHIdea.deleteIdea($ideaId);
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: BX.message("CANCEL"),
                className: "popup-window-button-link popup-window-button-link-cancel",
                events: {
                    click: function (e) {
                        window.ITHIdea.ideaPopup.close();
                    }
                }
            })
        ];

        content = BX.message("IDEA_ON_DELETE_WARNING");
        title = BX.message("IDEA_WARNING_TITLE");
        this.createPopup(title, content, btn, "popup-auto-width");
    },

    deleteIdea: function ($ideaId) {
        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "delete",
                ideaId: $ideaId
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                // console.log("sucDel");
                location.reload();
            },
            onfailure: function(){
                // console.log("failDel");
            }
        });
    },

    editIdea: function ($ideaId) {
        self = this;
        BX.ajax({
            url: "/iboard/edit/" + $ideaId + "/",
            data: {
                AJAX_MODE: "Y"
            },
            method: 'POST',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                title = BX.message("IDEA_EDIT_TITLE");
                self.createPopup(title, data, [], "", function() {
                    if ($("#idea-popup").find(".idea-text-wrap").length == 0)
                        LHEPostForm.unsetHandler("idea_edit");
                });
            },
            onfailure: function(){
                // console.log("failUp");
            }
        });
    },

    createPopup: function (title, content, btn, className, onCloseCallback) {
        this.ideaPopup = new BX.PopupWindow(
            'idea-popup',
            null,
            {
                content: content,
                titleBar: title,
                className: className,
                closeIcon: {},
                offsetLeft: 0,
                offsetTop: 0,
                draggable: {restrict: true},
                autoHide: false,
                closeByEsc: true,
                overlay: {backgroundColor: 'black', opacity: '80'},
                buttons: btn,
                events: {
                    onPopupClose: function (popupWindow) {
                        if (onCloseCallback)
                            onCloseCallback();
                        needReload = ($("#idea-popup").find(".idea-message-wrap").length == 1) ? true : false;
                        popupWindow.destroy();
                        if (needReload)
                            location.reload();
                    }
                }
            }
        );
        this.ideaPopup.show();
    },

    searchEntity: function ($params) {
        $obj = $($params.obj);
        $container = $obj.closest(".ideas-entity-popup");
        $searchContainer = $container.find(".idea-found-entity")
        $searchVal = $obj.val();

        $type = $params.type;
        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "searchEntity",
                params: $params,
                value: $searchVal,
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(found){
                $searchContainer.html("");
                $.each(found, function () {
                    $searchContainer.append("<li onclick='window.ITHIdea.ideaEntityChoose({obj: this, type: $type})' data-id='" + this.ID + "' data-name='" + this.NAME.toLowerCase() + "' data-color='" + this.COLOR + "'>" + this.NAME + "</li>");
                });
            },
            onfailure: function(){
                // console.log("failUp");
            }
        });
    },

    ideaEntityChoose: function ($params) {
        $obj = $($params.obj);
        $type = $params.type;

        $val = $obj.data("name");

        $entityContainer = $obj.closest(".idea-entity-wrap");
        $entityPopup = $entityContainer.find(".ideas-entity-popup");
        $entityInput = $entityPopup.find(".idea-search-entity");
        $entityAddBtn = $entityPopup.find(".idea-" + $type + "-add");

        $entityInput.val($val);
        $entityAddBtn.trigger("click");
    },

    ideaEntitySet: function ($params) {
        $obj = $($params.obj);
        $mode = $params.mode;
        $type = $params.type;
        $upperType = $type.toUpperCase();

        $entityContainer = $obj.closest(".idea-entity-wrap");
        $entityPopup = $obj.closest(".ideas-entity-popup");
        $entityFound = $entityPopup.find(".idea-found-entity");
        $entityInput = $entityPopup.find(".idea-search-entity");
        $val = $entityInput.val();
        $lowerVal = $val.toLowerCase();

        if ($mode == "single") {
            $entityContainer.find(".idea-current-" + $type + " input[name='IDEA[" + $upperType + "][NEED_UPDATE]']").val("Y");
            if ($entityPopup.find("li[data-name='" + $lowerVal + "']").length == 1) {
                $entityId = $entityPopup.find("li[data-name='" + $lowerVal + "']").data("id");
                $entityName = $entityPopup.find("li[data-name='" + $lowerVal + "']").html();
                $isNew = "";
            } else {
                $entityId = "";
                $entityName = $val;
                $isNew = "Y";
            }
            $entityContainer.find(".idea-current-" + $type + " input[name='IDEA[" + $upperType + "][ID]']").val($entityId);
            $entityContainer.find(".idea-current-" + $type + " input[name='IDEA[" + $upperType + "][NEW]']").val($isNew);
            $entityContainer.find(".idea-current-" + $type + " input[name='IDEA[" + $upperType + "][VALUE]']").val($entityName);
            $entityContainer.find(".idea-current-" + $type + " b").html($entityName);
        } else if ($mode == "multiple") {
            $currentEntity = $entityContainer.children("ul").find("li[data-name='" + $lowerVal + "']");
            if ($currentEntity.length == 1) {
                if ($currentEntity.find(".idea-entity-delete-input").val() == "Y") {
                    $currentEntity.find(".idea-entity-delete-input").val("");
                    $currentEntity.toggleClass("deleted");
                }
            } else {
                $entityListWrap = $entityContainer.find(".idea-current-" + $type);
                $index = $entityListWrap.find("li").length;

                if ($entityPopup.find("li[data-name='" + $lowerVal + "']").length == 1) {
                    $entityId = $entityPopup.find("li[data-name='" + $lowerVal + "']").data("id");
                    $entityName = $entityPopup.find("li[data-name='" + $lowerVal + "']").html();
                    $entityColor = $entityPopup.find("li[data-name='" + $lowerVal + "']").data("color");
                    $needCreate = "";
                } else {
                    $entityId = "";
                    $entityName = $val;
                    $entityColor = window.ITHIdea.makeRandomColor();
                    $needCreate = "Y";
                }

                $entityListWrap.append("<li class=\"primary\" style=\"background-color: #" + $entityColor + "\" data-name=\"" + $entityName + "\">" +
                    "<input type=\"hidden\" class=\"idea-entity-id-input\" name=\"IDEA[TAGS][" + $index + "][ID]\" value=\"" + $entityId + "\">" +
                    "<input type=\"hidden\" class=\"idea-entity-id-input-value\" name=\"IDEA[TAGS][" + $index + "][VALUE]\" value=\"" + $entityName + "\">" +
                    "<input type=\"hidden\" class=\"idea-entity-color-input\" name=\"IDEA[TAGS][" + $index + "][COLOR]\" value=\"" + $entityColor + "\">" +
                    "<input type=\"hidden\" class=\"idea-entity-create-input\" name=\"IDEA[TAGS][" + $index + "][NEED_CREATE]\" value=\"" + $needCreate + "\">" +
                    "<input type=\"hidden\"  class=\"idea-entity-add-input\"name=\"IDEA[TAGS][" + $index + "][NEED_ADD]\" value=\"Y\">" +
                    "<span>" + $entityName + "</span>" +
                    "<span onclick=\"window.ITHIdea.deleteEntity({obj: this});\" class=\"idea-entity-delete\"></span>" +
                    "</li>"
                );
            };

            $entityInput.val("");
            $entityFound.html("");
        }
    },

    deleteEntity: function ($params) {
        $obj = $($params.obj);

        $entityContainer = $obj.parent();
        if ($entityContainer.find(".idea-entity-add-input").length == 1)
            $entityContainer.remove();
        else {
            $entityContainer.find(".idea-entity-delete-input").val("Y");
            $entityContainer.addClass("deleted");
        }
    },

    addReminderDate: function ($params) {
        $obj = $($params.obj);
        $entityListWrap = $(".idea-current-reminder");
        $index = $entityListWrap.find("li").length;

        $reminderDate = $("#idea-reminder-date").val();
        $reminderPeriod = $("#idea-reminder-period-select").val();
        $reminderPeriodText = $("#idea-reminder-period-select option:selected").text();
        $reminderPeriodHour = $("#idea-reminder-period-hour").val() ? $("#idea-reminder-period-hour").val() : "12";
        $reminderPeriodMinutes = $("#idea-reminder-period-minutes").val() ? $("#idea-reminder-period-minutes").val() : "00";
        $reminderPeriodWeekDayVal = $("#idea-reminder-period-week-days").val() ? $("#idea-reminder-period-week-days").val() : "0";
        $reminderPeriodWeekDayDisplay = $("#idea-reminder-period-week-days option:selected").html() ? $("#idea-reminder-period-week-days option:selected").html() : "0";
        $reminderPeriodDayVal = $("#idea-reminder-period-days").val() ? $("#idea-reminder-period-days").val() : "1";

        $reminderPeriodDay = ($reminderPeriod == "week") ? $reminderPeriodWeekDayVal : $reminderPeriodDayVal;
        $reminderPeriodDayDisplay = ($reminderPeriod == "week") ? $reminderPeriodWeekDayDisplay : (($reminderPeriod == "month") ? $reminderPeriodDayVal + " " + BX.message("NUMBER") : "");

        $entityVal = ($reminderPeriod != "false") ? $reminderPeriod : ( ($reminderDate.length) ? $reminderDate : false );
        $entityDisplayVal = ($reminderPeriod != "false") ? $reminderPeriodText + " " + $reminderPeriodHour + ":" + $reminderPeriodMinutes + " " + $reminderPeriodDayDisplay : $reminderDate;
        $periodInput = ($reminderPeriod != "false") ? "<input type=\"hidden\" class=\"idea-entity-period-input\" name=\"IDEA[REMINDER][" + $index + "][PERIOD]\" value=\"" + $reminderPeriod + "\">" : "";

        if ($reminderPeriod  != "false") {
            $entityListWrap.find(".idea-entity-period-input").each(function () {
                $(this).parent().find(".idea-entity-delete").trigger("click");
            });
        }

        if ($entityVal)
            $(".idea-current-reminder").append(
                "<li class=\"card\">" +
                "<input type=\"hidden\" class=\"idea-entity-date-input\" name=\"IDEA[REMINDER][" + $index + "][DATE]\" value=\"" + $reminderDate + "\">" +
                $periodInput +
                "<input type=\"hidden\" class=\"idea-entity-display-val-input\" name=\"IDEA[REMINDER][" + $index + "][DISPALY_VALUE]\" value=\"" + $entityDisplayVal + "\">" +
                "<input type=\"hidden\" class=\"idea-entity-add-input\" name=\"IDEA[REMINDER][" + $index + "][NEED_ADD]\" value=\"Y\">" +
                "<input type=\"hidden\" class=\"idea-entity-hour-input\" name=\"IDEA[REMINDER][" + $index + "][HOUR]\" value=\"" + $reminderPeriodHour + "\">" +
                "<input type=\"hidden\" class=\"idea-entity-min-input\" name=\"IDEA[REMINDER][" + $index + "][MINUTES]\" value=\"" + $reminderPeriodMinutes + "\">" +
                "<input type=\"hidden\" class=\"idea-entity-min-input\" name=\"IDEA[REMINDER][" + $index + "][DAY]\" value=\"" + $reminderPeriodDay + "\">" +
                "<span>" + $entityDisplayVal + "</span>" +
                "<span onclick=\"window.ITHIdea.deleteEntity({obj: this});\" class=\"idea-entity-delete\"></span>" +
                "</li>"
            );
    },

    editSaveIdea: function ($params) {
        $obj = $($params.obj);

        $form = $obj.closest("form");
        $form.submit();
    },
    
    ideaSubmitForm: function ($params) {
        $form = $($params.form);
        $formId = $form.attr("id");
        if ($form.find(".idea-text-wrap").length == 0) {
            $editorObjName = "PlEditor" + $formId;
            $actualText = window[$editorObjName].oEditor.textareaView.element.value;
            $('[name="IDEA_TEXT"]').val($actualText);
        }
        $ajaxUrl = $('[name="IDEA_EDIT_FORM"]').attr("action");

        BX.ajax({
            url: $ajaxUrl,
            data: $form.serialize(),
            method: "POST",
            timeout: 30,
            async: true,
            onsuccess: function(formResultHtml) {
                $form.closest(".idea-article").html(formResultHtml);
                $("#idea-popup").css("width", "auto");
                $("#idea-popup").css("left", "50%");
                $("#idea-popup").css("margin-left", "-155px");
                $("#idea-popup .idea-article").css("min-width", "250px");
                $('html,body').animate({
                    scrollTop: $(".idea-message-wrap").offset().top - 200
                }, 500);
            },
        });
    },

    editCategory: function ($params) {
        $obj = $($params.obj);
        $titleContainer = $obj.closest(".idea-title");
        $title = $titleContainer.find(".idea-title-input");
        $edit = $titleContainer.find(".idea-category-edit");
        $colorEdit = $titleContainer.find(".idea-color-edit");
        $sortEdit = $titleContainer.find(".idea-sort-edit");
        $confirmEdit = $titleContainer.find(".idea-category-confirm");
        $deleteCategory = $titleContainer.find(".idea-category-delete");

        if (!$params.hide) {
            $title.removeAttr("disabled");
            $title.removeAttr("size");

            $colorEdit.addClass("visible");
            $sortEdit.addClass("visible");
            $confirmEdit.addClass("visible");

            $deleteCategory.hide();
            $edit.hide();
        } else {
            $title.attr("disabled", "disabled");
            $size = $title.val().length - 2;
            $size = ($size < 1) ? 1 : $size;
            $title.attr("size", $size);

            $colorEdit.removeClass("visible");
            $sortEdit.removeClass("visible");
            $confirmEdit.removeClass("visible");

            $deleteCategory.show();
            $edit.show();
        }
    },

    setCategoriesSort: function () {
        var $arSort = {};
        $(".idea-category-wrap").each(function(i, elem) {
            $catId = $(this).find(".idea-category-id-input").val();
            $arSort[i] = $catId;
        });

        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "setCategoriesSort",
                arSort: $arSort
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                window.ITHIdea.closeAjaxShadow("categories-shadow");
            },
            onfailure: function(){
                window.ITHIdea.closeAjaxShadow("categories-shadow");
            }
        });
    },

    sortCategory: function ($params) {
        $obj = $($params.obj);
        $mode = $params.mode;
        $categoryContainer = $obj.closest(".idea-category-wrap");

        switch ($mode) {
            case "up":
                $prevCategoryContainer = $categoryContainer.prev(".idea-category-wrap");
                if ($prevCategoryContainer.length > 0) {
                    window.ITHIdea.showAjaxShadow(".ideas-scroller", "categories-shadow");
                    $prevCategoryContainer.before($categoryContainer);
                    window.ITHIdea.setCategoriesSort();
                }
                break;
            case "down":
                $nextCategoryContainer = $categoryContainer.next(".idea-category-wrap");
                if ($nextCategoryContainer.length > 0) {
                    window.ITHIdea.showAjaxShadow(".ideas-scroller", "categories-shadow");
                    $nextCategoryContainer.after($categoryContainer);
                    window.ITHIdea.setCategoriesSort();
                }
                break;
        }
    },

    confirmInputEditCategory: function ($params) {
        $event = $($params.event);
        $keyCode = $event[0].keyCode;

        if ($keyCode == 13)
            this.confirmEditCategory($params);
    },

    confirmEditCategory: function ($params) {
        $obj = $($params.obj);
        $titleContainer = $obj.closest(".idea-title");
        $categoryId = $titleContainer.find(".idea-category-id-input").val();
        $categoryColor = $titleContainer.find(".idea-category-color-input").val();
        $categoryName = $titleContainer.find(".idea-title-input").val();
        $params.hide = true;

        $updateParams = {
            categoryId: $categoryId,
            updateFields: {name: $categoryName, color: $categoryColor},
            callback: window.ITHIdea.editCategory($params)
        };

        window.ITHIdea.updateCategory($updateParams)
    },

    updateCategory: function ($params) {
        $categoryId = $params.categoryId;
        $updateFields = $params.updateFields;

        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "updateCategory",
                categoryId: $categoryId,
                updateFields: $updateFields
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function($updateResult){
                if ($params.callback)
                    $params.callback();
            },
            onfailure: function(){
                // console.log("fail update");
            }
        });
    },

    onDeleteCategory: function ($params) {
        $obj = $($params.obj);
        $titleContainer = $obj.closest(".idea-title");
        $categoryId = $titleContainer.find(".idea-category-id-input").val();

        btn = [
            new BX.PopupWindowButton({
                text: BX.message("DELETE"),
                className: "popup-window-button-accept",
                events: {
                    click: function (e) {
                        window.ITHIdea.deleteCategory($categoryId);
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: BX.message("CANCEL"),
                className: "popup-window-button-link popup-window-button-link-cancel",
                events: {
                    click: function (e) {
                        window.ITHIdea.ideaPopup.close();
                    }
                }
            })
        ];

        content = BX.message("CATEGORY_DELETE_WARNING");
        title = BX.message("IDEA_WARNING_TITLE");
        this.createPopup(title, content, btn, "popup-auto-width");
    },

    deleteCategory: function ($categoryId) {
        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "deleteCategory",
                categoryId: $categoryId
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                $("#idea-popup .popup-window-buttons").remove();
                $("#idea-popup .popup-window-content").html("<div class='idea-message-wrap'>" + BX.message("CATEGORY_DELETED") + "</div>");
            },
            onfailure: function(){
                // console.log("fail delete");
            }
        });
    },

    followIdea: function ($params) {
        $obj = $($params.obj);
        $ideaContainer = $obj.closest(".idea");
        $ideaId = $ideaContainer.find(".idea-id-input").val();
        $action = ($obj.hasClass("unfollow")) ? "unfollowIdea" : "followIdea";

        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: $action,
                ideaId: $ideaId
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                $obj.toggleClass("unfollow");
                if ($obj.hasClass("unfollow"))
                    $obj.attr("title", BX.message("UNFOLLOW"));
                else
                    $obj.attr("title", BX.message("FOLLOW"));
            },
            onfailure: function(){
                // console.log("fail follow");
            }
        });
    },

    updateIdeaCategoriesSort: function ($sortType, $arSort, $needCategoryUpdate, $ideaIdUpdate, $categoryIdUpdate) {
        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "setSortByCategories",
                sortType: $sortType,
                arSort: $arSort
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                if ($needCategoryUpdate)
                    window.ITHIdea.updateIdeaCategories($ideaIdUpdate, $categoryIdUpdate)
                else
                    window.ITHIdea.closeAjaxShadow("ideas-shadow");
            },
            onfailure: function(){
                window.ITHIdea.closeAjaxShadow("ideas-shadow");
                // console.log("fail");
            }
        });

    },

    updateIdeaCategories: function ($ideaId, $categoryId) {
        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "update",
                ideaId: $ideaId,
                updateFields: {category_id: $categoryId}
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                if ($.parseJSON(data) == null) {
                    $ideaIdInput = $(".idea-id-input[data-value=" + $ideaId + "]");
                    $ideaContainer = $ideaIdInput.closest(".idea");
                    $categoryIdInput = $(".idea-category-id-input[data-value=" + $categoryId + "]");
                    $categoryColor = $(".idea-category-color-input[data-value=" + $categoryId + "]").val();
                    $ideaContainer.css("border-color", "#" + $categoryColor);

                    $(".idea-category-wrap").each(function () {
                        $categoryContainer = $(this);
                        $categoryCount = $categoryContainer.find('.idea-count');
                        $ideasCount = $categoryContainer.find(".idea").length;
                        $categoryCount.text($ideasCount);
                        $ideasCount > 0 ? $categoryContainer.find('.empty-text').addClass('hidden') : $categoryContainer.find('.empty-text').removeClass('hidden');
                    });


                    window.ITHIdea.closeAjaxShadow("ideas-shadow");
                }
            },
            onfailure: function(){
                window.ITHIdea.closeAjaxShadow("ideas-shadow");
                // console.log("fail");
            }
        });
    },

    onRestoreIdea: function ($params) {

        btn = [
            new BX.PopupWindowButton({
                text: BX.message("RESTORE"),
                className: "popup-window-button-accept",
                events: {
                    click: function (e) {
                        window.ITHIdea.restoreIdea($params);
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: BX.message("CANCEL"),
                className: "popup-window-button-link popup-window-button-link-cancel",
                events: {
                    click: function (e) {
                        window.ITHIdea.ideaPopup.close();
                    }
                }
            })
        ];

        content = BX.message("IDEA_ON_RESTORE_WARNING");
        title = BX.message("IDEA_WARNING_TITLE");
        this.createPopup(title, content, btn, "popup-auto-width");
    },

    restoreIdea: function ($params) {
        $obj = $($params.obj);
        $ideaContainer = $obj.closest(".idea");
        $ideaId = $ideaContainer.find(".idea-id-input").val();

        window.ITHIdea.showAjaxShadow(".ideas-list", "ideas-shadow");

        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "restoreIdea",
                ideaId: $ideaId,
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                if ($.parseJSON(data) == null)
                    location.reload();
            },
            onfailure: function(){
                window.ITHIdea.closeAjaxShadow("ideas-shadow");
                // console.log("fail");
            }
        });
    },

    removeIdea: function ($ideaId) {
        window.ITHIdea.showAjaxShadow("#idea-popup", "ideas-shadow");
        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "removeIdea",
                ideaId: $ideaId,
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                $("#idea-popup .popup-window-buttons").remove();
                window.ITHIdea.closeAjaxShadow("ideas-shadow");
                $("#idea-popup .popup-window-content").html("<div class='idea-message-wrap'>" + BX.message("IDEA_DELETED") + "</div>");
                if ($.parseJSON(data) == null) {
                }
            },
            onfailure: function(){
                // console.log("fail");
                window.ITHIdea.closeAjaxShadow("ideas-shadow");
            }
        });
    },

    onRemoveIdea: function ($params) {
        $obj = $($params.obj);
        $ideaContainer = $obj.closest(".idea");
        $ideaId = $ideaContainer.find(".idea-id-input").val();

        btn = [
            new BX.PopupWindowButton({
                text: BX.message("DELETE"),
                className: "popup-window-button-accept",
                events: {
                    click: function (e) {
                        window.ITHIdea.removeIdea($ideaId);
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: BX.message("CANCEL"),
                className: "popup-window-button-link popup-window-button-link-cancel",
                events: {
                    click: function (e) {
                        window.ITHIdea.ideaPopup.close();
                    }
                }
            })
        ];

        content = BX.message("IDEA_DELETE_WARNING");
        title = BX.message("IDEA_WARNING_TITLE");
        this.createPopup(title, content, btn, "popup-auto-width");
    },

    showIdeasFilter: function(event)
    {
        context = BX.proxy_context;
        if ($(event.target).closest(".ideas-filter-form").length)
            return;
        $(".task-form-field-link").html(BX.message("CHOOSE"));
        $(".ideas-filter-form").toggleClass("visible");
    },

    deleteIdeaActiveFilter: function () {
        context = BX.proxy_context;
        $propCode = $(context).closest(".idea-active-filter-val-wrap").data("prop");

        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "removeIdeaActiveFilter",
                propCode: $propCode,
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                console.log(data);
                window.location = window.location.href;
            },
            onfailure: function(){
                // console.log("fail");
            }
        });
    },

    getSelectedIdeasId: function () {
        $arIDs = [];
        $(".idea.selected").each(function () {
            $arIDs.push($(this).find(".idea-id-input").val());

            $(this).find("input[type='checkbox']").trigger("click");
            $(this).removeClass("selected");
        });
        return $arIDs;
    },

    eventCreate: function () {
        $arIds = this.getSelectedIdeasId();
        if ($arIds.length > 0) {
            ITHFastObjects.showEventForm({ideaId: $arIds, pageUrl: location.href});
        } else {
            title = BX.message("IDEA_WARNING_TITLE");
            this.createPopup(title, BX.message("NO_IDEAS_SELECTED"), [], "popup-auto-width");
        }
    },

    lfeedCreate: function () {
        $arIds = this.getSelectedIdeasId();
        if ($arIds.length > 0) {
            ITHFastObjects.showLiveFeedForm({ideaId: $arIds, pageUrl: location.href});
        } else {
            title = BX.message("IDEA_WARNING_TITLE");
            this.createPopup(title, BX.message("NO_IDEAS_SELECTED"), [], "popup-auto-width");
        }
    },

    setMinimized: function () {
        context = BX.proxy_context;
        $type = $(context).data("type");
        $ideaWrap = $(context).closest(".idea");
        $ideaId = $($ideaWrap).find(".idea-id-input").val();
        $minimized = ($ideaWrap.hasClass("active")) ? 0 : 1;

        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "minimizeIdea",
                ideaId: $ideaId,
                type: $type,
                minVal: $minimized,
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
            },
            onfailure: function(){
                // console.log("fail");
            }
        });
    }
},

window.startCallback = function(event, ui, data) {
    // console.log('start...', data.getData());
    window.ITHIdea.lastSortState = data.getData();
}

window.dragCallback = function(event, ui, data) {
    // console.log('drag...')
}

window.dropCallback = function(event, ui, data){
    // console.log('drop...', data.getData());
    window.ITHIdea.showAjaxShadow(".ideas-scroller", "ideas-shadow");

    $ideasList = data.getData();

    $needCategoryUpdate = false;
    $ideaIdUpdate = 0;
    $categoryIdUpdate = 0;

    $arCategories = $ideasList["categories"];
    $arSort = $ideasList["sort"];
    $sortType = "sort_table";

    $size = Object.keys($arCategories).length;
    if ($size > 0) {
        $sortType = "sort_list";
        $.each($arCategories, function ($categoryId, $category) {
            $.each($category, function ($ideaId) {
                if (!window.ITHIdea.lastSortState["categories"][$categoryId][$ideaId]) {
                    $ideaIdUpdate = $ideaId;
                    $categoryIdUpdate = $categoryId;
                    $needCategoryUpdate = true;
                }
            });
        });
    }
    window.ITHIdea.updateIdeaCategoriesSort($sortType, $arSort, $needCategoryUpdate, $ideaIdUpdate, $categoryIdUpdate)
}

window.dropCategoriesCallback = function(event, ui, data){
    // console.log('drop cats...', data.getData());
}

$(document).click(function(event) {
    if (
        $(event.target).closest(".ideas-filter-wrap").length
        || $(event.target).closest(".idea-active-filter-val-wrap").length
    )
        return;
    $(".ideas-filter-form").removeClass("visible");
});

$(document).on("click", ".idea-archive-list .idea .title", function(event) {
   $(this).closest(".idea").toggleClass("active");
});


