window.ITHIdeaCreate = function(params) {
    self = this;

    this.siteId = params.siteId;
    this.userId = params.userId;
    this.formId = params.formId;
    this.componentPath = params.componentPath;
    this.ajaxUrl = params.ajaxUrl;
    this.alreadyHidden = false;

    BX.addCustomEvent("OnEditorIsLoaded", BX.delegate(function() {self.hideEditor()}, this));

    BX.bindDelegate(document.body, 'click', {className: 'idea-popup-close'}, BX.delegate(function () {
        this.closePopup();
    }, this));

    BX.bind(BX("idea-add-watcher"), 'click', BX.delegate(this.showWatchersPopup, this));
    BX.bind(BX("idea-add-reminder"), 'click', BX.delegate(this.addIdeaReminder, this));
    BX.bind(BX("lhe_button_reminder_" +  this.formId), 'click', BX.delegate(this.showIdeaPopup, this));
    BX.bind(BX("idea-reminder-date"), 'change', BX.delegate(this.setReminderDate, this));
    BX.bind(BX("idea-reminder-period-select"), 'change', BX.delegate(this.setReminderPeriod, this));

    BX.bind(BX("lhe_button_tags_" +  this.formId), 'click', BX.delegate(this.getTags, this));

    BX.bind(BX("lhe_button_category_" +  this.formId), 'click', BX.delegate(this.getCategory, this));
    BX.bind(BX("idea-important"), 'change', BX.delegate(this.changeImportant, this));

    BX.bindDelegate(document.body, 'click', {className: 'idea-tmp-task-create'}, BX.delegate(function () {
        this.getCurrentIdeaObj("task");
    }, this));
    BX.bindDelegate(document.body, 'click', {className: 'idea-tmp-chat-create'}, BX.delegate(function () {
        this.getCurrentIdeaObj("chat");
    }, this));
    BX.bindDelegate(document.body, 'click', {className: 'idea-tmp-event-create'}, BX.delegate(function () {
        this.getCurrentIdeaObj("event");
    }, this));
    BX.bindDelegate(document.body, 'click', {className: 'idea-tmp-lfeed-create'}, BX.delegate(function () {
        this.getCurrentIdeaObj("lfeed");
    }, this));
    BX.bindDelegate(document.body, 'click', {className: 'idea-tmp-mail-create'}, BX.delegate(function () {
        this.getCurrentIdeaObj("mail");
    }, this));

    this.setWatchersPosition();
};

window.ITHIdeaCreate.prototype = {
    getCurrentIdeaObj: function($mode)
    {
        $text = $('.idea-add-form-wrapper').find("textarea").val();
        $name = $('.create-idea-name-input').val();
        $important = ($("#idea-important").is(":checked")) ? 1 : 0;
        $arFileId = [];
        $('[name="IDEA_FILES[]"]').each(function() {
            $arFileId.push($(this).val());
        });

        BX.ajax({
            url: this.ajaxUrl,
            data: {
                action: "getFiles",
                fileIds: $arFileId,
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                $location = window.location.href;
                switch ($mode) {
                    case "task":
                        ITHFastObjects.showTaskForm({images: data, text: $text, pageUrl: $location, name: $name, important: $important});
                        break;
                    case "chat":
                        ITHFastObjects.showChatForm({images: data, text: $text, pageUrl: $location});
                        break;
                    case "event":
                        ITHFastObjects.showEventForm({images: data, text: $text, pageUrl: $location, name: $name});
                        break;
                    case "lfeed":
                        ITHFastObjects.showLiveFeedForm({images: data, text: $text, pageUrl: $location});
                        break;
                    case "mail":
                        ITHFastObjects.showMailForm({images: data, text: $text, pageUrl: $location, name: $name});
                        break;
                    default:
                        break;
                }
            },
            onfailure: function(){
                // console.log("fail");
            }
        });
    },

    hideEditor: function () {
        if (this.formId != undefined && !this.alreadyHidden) {
            this.alreadyHidden = true;
            LHEPostForm.getHandler(this.formId).showPanelEditor();
            $(".idea-add-form-wrapper").removeClass("initializing");
        }
    },

    changeImportant: function () {
        element = BX.proxy_context;
        id = $(element).attr("id");
        $("#" + id + " + label").toggleClass("active")
        if ($(element).is(":checked"))
            $("#" + id + " + label").html(BX.message("IDEA_IMPORTANT"));
        else
            $("#" + id + " + label").html(BX.message("MAKE_IMPORTANT"));
    },

    setWatchersPosition: function () {
        $top = $(".feed-add-post-form-but-more-open").offset().top + 2;
        $left = $(".feed-add-post-form-but-more-open").offset().left + 110;

        $("#idea-watchers-wrapper").css("top", $top + "px");
        $("#idea-watchers-wrapper").css("left", $left + "px");

        $("#idea-watchers-wrapper").removeClass("innactive");
    },

    closePopup: function () {
        element = BX.proxy_context;
        $popup = $(element).closest(".idea-popup").removeClass("active");
    },

    showIdeaPopup: function() {
        element = BX.proxy_context;

        $topOffset = $(element).offset().top;
        $leftOffset = $(element).offset().left;
        $elementHeight = $(element).height();
        $elementWidth = $(element).width();

        $popupId = "." + $(element).data("popup");
        $popupHeight = $($popupId).height();
        $popupTop = $topOffset - $elementHeight - $popupHeight - 5;
        $popupLeft = $leftOffset - $elementWidth;

        $($popupId).css("left", $popupLeft + "px");
        $($popupId).css("top", $popupTop + "px");

        if ($($popupId).hasClass("active"))
            $($popupId).removeClass("active");
        else {
            $(".idea-popup").removeClass("active");
            $($popupId).addClass("active");
        }
    },

    showWatchersPopup: function () {
        $("#idea-watchers-popup").toggleClass("active");
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

    addIdeaReminder: function() {
        self = this;

        $reminderDate = $("#idea-reminder-date").val();
        $reminderPeriod = $("#idea-reminder-period-select").val();
        $reminderPeriodText = $("#idea-reminder-period-select option:selected").text();
        $reminderPeriodHour = $("#idea-reminder-period-hour").val() ? $("#idea-reminder-period-hour").val() : "12";
        $reminderPeriodMinutes = $("#idea-reminder-period-minutes").val() ? $("#idea-reminder-period-minutes").val() : "00";
        $reminderPeriodWeekDay = $("#idea-reminder-period-week-days").val() ? $("#idea-reminder-period-week-days").val() : "0";
        $reminderPeriodWeekDayDisplay = $("#idea-reminder-period-week-days option:selected").html() ? $("#idea-reminder-period-week-days option:selected").html() : "0";
        $reminderPeriodDay = $("#idea-reminder-period-days").val() ? $("#idea-reminder-period-days").val() : "1";
        $val = ($reminderPeriod != "false") ? $reminderPeriod : ( ($reminderDate.length) ? $reminderDate : false );

        if ($val) {
            $reminderContainer = $(".idea-reminder-items");
            if (!$reminderContainer.length) {
                $reminderContainerHtml = "<div class='idea-reminder-items' data-last='0'><div class='idea-reminder-title'>" + BX.message("REMINDER_TITLE") + "</div></div>";
                $("#" + self.formId).find(".idea-add-form-footer").before($reminderContainerHtml);
                $reminderContainer = $(".idea-reminder-items");
            }
            $periodStr = "";
            $periodStrDisplay = "";
            switch ($val) {
                case "day":
                    $periodStr = $reminderPeriodHour + ":" + $reminderPeriodMinutes;
                    $periodStrDisplay = $reminderPeriodHour + ":" + $reminderPeriodMinutes;
                    break;
                case "week":
                    $periodStr = $reminderPeriodHour + ":" + $reminderPeriodMinutes + ":" + $reminderPeriodWeekDay;
                    $periodStrDisplay = $reminderPeriodHour + ":" + $reminderPeriodMinutes + " " + $reminderPeriodWeekDayDisplay;
                    break;
                case "month":
                    $periodStr = $reminderPeriodHour + ":" + $reminderPeriodMinutes + ":" + $reminderPeriodDay;
                    $periodStrDisplay = $reminderPeriodHour + ":" + $reminderPeriodMinutes + " " + $reminderPeriodDay + " " + BX.message("NUMBER");
                    break;
            }
            $displayVal = ($reminderPeriod != "false") ? $reminderPeriodText + " " + $periodStrDisplay : $reminderDate;
            $type = ($reminderPeriod != "false") ? "period" : "date";
            if ($type == "period")
                $reminderContainer.find('.idea-reminder-item[data-type="period"]').remove();
            $reminderLastTotalItemsCnt = $(".idea-reminder-items").data("last");
            $reminderHtml = "<span class='idea-reminder-item' data-type='" + $type + "'><input type='hidden' name='IDEA_REMINDER_ITEM[]' value='" + $val + "#" + $type + "#" + $periodStr + "'>" + $displayVal + "<span class='idea-select-item-delete idea-reminder-delete' id='idea-reminder-delete-" + $reminderLastTotalItemsCnt + "'></span></span>";
            $reminderContainer.data("last", $reminderLastTotalItemsCnt + 1);
            $reminderContainer.append($reminderHtml);
            BX.bind(BX("idea-reminder-delete-" + $reminderLastTotalItemsCnt), 'click', BX.delegate(this.deleteIdeaReminder, this));
        }
    },

    deleteIdeaReminder: function () {
        obj = BX.proxy_context;
        $reminderContainer = $(obj).closest(".idea-reminder-items");
        $(obj).closest(".idea-reminder-item").remove();
        if ($(".idea-reminder-item").length == 0)
            $reminderContainer.remove();
    },

    makeRandomColor: function () {
        colors = window.ITHIdea.getDefaultColors();
        color = colors[Math.floor(Math.random() * colors.length)];

        $clearColor = color.replace(/#/g,"");

        return $clearColor;
    },

    addIdeaItem: function ($type, $pType, $itemId, $itemName, $itemColor, $itemNew) {
        self = this;
        $obj = $(BX.proxy_context);
        $upType = $type.toUpperCase();

        if ($itemName && $itemColor) {
            $itemsContainer = $(".idea-" + $type + "-items");
            if (!$itemsContainer.length) {
                $itemsContainerHtml = "<div class='idea-" + $type + "-items' data-last='0'><div class='idea-" + $type + "-title'>" + BX.message($upType + "_TITLE") + "</div></div>";
                $("#" + self.formId).find(".idea-add-form-footer").before($itemsContainerHtml);
                $itemsContainer = $(".idea-" + $type + "-items");
            }

            $itemsLastTotalItemsCnt = $itemsContainer.data("last");
            if ($type == "category") {
                $itemsHtml = "<span class='idea-" + $type + "-item idea-" + $type + "-to-add' data-id='" + $itemId + "'><input type='hidden' name='IDEA_" + $upType + "_ITEM[]' value='" + $itemId + "#" + $itemName + "#" + $itemColor + "#" + $itemNew + "'><span class='idea-" + $type + "-color-label' style='background-color: #" + $itemColor + "'></span><span class='idea-" + $type + "-exist-name' data-name='" + $itemName + "'>" + $itemName + "</span><span class='idea-select-item-delete idea-" + $type + "-delete' id='idea-" + $type + "-delete-" + $itemsLastTotalItemsCnt + "'></span></span>";
                $itemsContainer.find(".idea-" + $type + "-item").remove();
                $(".idea-" + $pType + "-add-exist-" + $type).removeClass("disabled");
            } else {
                $itemsHtml = "<span class='idea-" + $type + "-item idea-" + $type + "-to-add' data-id='" + $itemId + "' style='background-color: #" + $itemColor + "'><input type='hidden' name='IDEA_" + $upType + "_ITEM[]' value='" + $itemId + "#" + $itemName + "#" + $itemColor + "#" + $itemNew + "'><span class='idea-" + $type + "-exist-name' data-name='" + $itemName + "'>" + $itemName + "</span><span class='idea-select-item-delete idea-" + $type + "-delete' id='idea-" + $type + "-delete-" + $itemsLastTotalItemsCnt + "'></span></span>";
                $itemsContainer.data("last", $itemsLastTotalItemsCnt + 1);
            }
            $itemsContainer.append($itemsHtml);
            BX.bind(BX("idea-" +  $type + "-delete-" + $itemsLastTotalItemsCnt), 'click', BX.delegate(function() {self.deleteIdeaItem($type, $pType)}, this));
            $(".idea-" + $pType + "-add-exist-" + $type + "[data-name='" + $itemName + "']").addClass("disabled");
            $(".idea-new-" + $type + "-name").val("");
        }
    },

    addExistIdeaItem: function ($type, $pType) {
        $obj = $(BX.proxy_context);

        if ($obj.hasClass("disabled"))
            return;

        $itemId = $obj.data("id");
        $itemName = $obj.data("name");
        $itemColor = $obj.data("color");
        $itemNew = "";

        this.addIdeaItem($type, $pType, $itemId, $itemName, $itemColor, $itemNew);
    },

    addNewIdeaItem: function ($type, $pType, $itemName) {
        self = this;
        BX.ajax({
            url: this.ajaxUrl,
            data: {
                siteId: this.siteId,
                userId: this.userId,
                action: "getExistItem",
                type: $type,
                name: $itemName,
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(data){
                $existItem = $(".idea-" + $pType + "-add-exist-" + $type + "[data-name='" + $itemName + "']");
                $$existItemDisabled = $existItem.hasClass("disabled");
                $itemId = (data.ID) ? data.ID : "";
                $itemColor = (data.COLOR) ? data.COLOR : self.makeRandomColor();
                $itemNew = (data.ID) ? "" : "new";

                $existAddedTag = $(".idea-" + $type + "-exist-name[data-name='" + $itemName + "']");

                if ($existItem.length == 1 && $$existItemDisabled || $existAddedTag.length == 1)
                    $(".idea-new-" + $type + "-name").val("");
                else if ($itemName.length)
                    self.addIdeaItem($type, $pType, $itemId, $itemName, $itemColor, $itemNew);
            },
            onfailure: function(){
                // console.log("fail");
            }
        });


    },

    deleteIdeaItem: function ($type, $pType) {
        obj = BX.proxy_context;
        $itemContainer = $(obj).closest(".idea-" + $type + "-items");
        $id = $(obj).closest(".idea-" + $type + "-item").data("id");
        $(obj).closest(".idea-" + $type + "-item").remove();
        $(".idea-" + $pType + "-add-exist-" + $type + ".disabled[data-id='" + $id + "']").removeClass("disabled");
        if ($(".idea-" + $type + "-to-add").length == 0)
            $itemContainer.remove();
    },

    getTags: function () {
        context = BX.proxy_context;
        self = this;
        BX.ajax({
            url: this.ajaxUrl,
            data: {
                siteId: this.siteId,
                userId: this.userId,
                action: "getTags",
                popupHtml: true
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(existTagsHtml){
                BX.proxy_context = context;
                $tagsHtml = "<div class='idea-popup-title idea-tags-title'>" + BX.message("TAG_TITLE") + "</div><input class='idea-input idea-new-tag-name' type='text' value=''><span id='idea-tag-add' class='idea-input-add idea-tag-add'>" + BX.message("ADD") + "</span>" + existTagsHtml;
                $(".idea-tags-popup-body").html($tagsHtml);
                BX.bind(BX("idea-tag-add"), 'click', BX.delegate(self.addNewIdeaTag, self));
                BX.bindDelegate(document.body, 'click', {className: 'idea-tags-add-exist-tag'}, BX.delegate(self.addExistIdeaTag, self));
                self.showIdeaPopup();
            },
            onfailure: function(){
                // console.log("fail");
            }
        });
    },

    addExistIdeaTag: function () {
        self.addExistIdeaItem("tag", "tags");
    },

    addNewIdeaTag: function () {
        $tagName = $(".idea-new-tag-name").val();
        self.addNewIdeaItem("tag", "tags", $tagName);
    },

    getCategory: function () {
        context = BX.proxy_context;
        self = this;
        BX.ajax({
            url: this.ajaxUrl,
            data: {
                siteId: this.siteId,
                userId: this.userId,
                action: "getCategories",
                popupHtml: true
            },
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            onsuccess: function(existCategoriesHtml){
                BX.proxy_context = context;
                $categoriesHtml = "<div class='idea-popup-title idea-categories-title'>" + BX.message("CATEGORY_TITLE") + "</div><input class='idea-input idea-new-category-name' type='text' value=''><span id='idea-category-add' class='idea-input-add idea-category-add'>" + BX.message("ADD") + "</span>" + existCategoriesHtml;
                $(".idea-categories-popup-body").html($categoriesHtml);
                BX.bind(BX("idea-category-add"), 'click', BX.delegate(self.addNewIdeaCategory, self));
                BX.bindDelegate(document.body, 'click', {className: 'idea-categories-add-exist-category'}, BX.delegate(self.addExistIdeaCategory, self));
                self.showIdeaPopup();
            },
            onfailure: function(){
                // console.log("fail");
            }
        });
    },

    addExistIdeaCategory: function () {
        self.addExistIdeaItem("category", "categories");
    },

    addNewIdeaCategory: function () {
        $categoryName = $(".idea-new-category-name").val();
        self.addNewIdeaItem("category", "categories", $categoryName);
    },
}

$(document).click(function(event) {
    if ($(event.target).closest(".idea-popup").length || $(event.target).closest(".feed-add-post-form-but").length)
        return;
    $(".idea-popup").removeClass("active");
});

$(document).on("keyup", "#idea-reminder-period-hour", function (e) {
    $(this).val($(this).val().replace (/\D/, ''));
    if ($(this).val() > 24)
        $(this).val("00");
});

$(document).on("keyup", "#idea-reminder-period-minutes", function (e) {
    $(this).val($(this).val().replace (/\D/, ''));
    if ($(this).val() > 60)
        $(this).val("00");
});