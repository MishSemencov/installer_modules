$(".ideas-list").sortable({
    connectWith: ".ideas-list",
    start: function(event, ui) {
        // console.log("start");
        $item = ui.item;
       if (window[$($item).closest('.ideas-list').data('onstart')]) {
           window[$($item).closest('.ideas-list').data('onstart')](event, ui, serializeDraggable());
       }
    },
    sort: function(event, ui) {
        // console.log("sort");
        $item = ui.item;
        if (window[$($item).closest('.ideas-list').data('ondrag')]) {
            window[$($item).closest('.ideas-list').data('ondrag')](event, ui, serializeDraggable());
        }
    },
    stop: function(event, ui) {
        // console.log("stop");
        $item = ui.item;
        if (window[$($item).closest('.ideas-list').data('ondrop')]) {
            window[$($item).closest('.ideas-list').data('ondrop')](event, ui, serializeDraggable());
        }
    }
}).disableSelection();