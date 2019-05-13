window.serializeDraggable = function(selected) {
    var parseData;
    parseData = function() {
        var data;
        // data = {};
        data = [];
        data["categories"] = {};
        $(".idea-category-wrap").each(function () {
            $categoryId = $(this).find(".idea-category-id-input").val().trim();
            data["categories"][$categoryId] = {};
        });
        data["sort"] = [];
        (selected || $('.ideas-list')).each(function(i, list) {
            var col;
            col = [];
            $(list).find('> li, > .item').not('.faded').find('.idea').each(function(i, idea) {
                var tags;
                tags = [];
                $(idea).find('.tags-list > li').each(function(i, tag) {
                    if ($(tag).text().trim()) {
                        return tags.push($(tag).text().trim());
                    }
                });
                $ideaId = $('.idea-id-input', idea).val().trim();
                if ($(".idea-category-wrap").length) {
                    $categoryId = $(idea).closest(".idea-category-wrap").find(".idea-category-id-input").val().trim();
                    if (data["categories"][$categoryId] == undefined)
                        data["categories"][$categoryId] = {};

                    data["categories"][$categoryId][$ideaId] = {
                        id: $ideaId || '',
                    };
                }
                data["sort"].push($ideaId);
                //   data["sort"].push({
                //   categoryId: $categoryId || '',
                //   id: $ideaId || '',
                //   title: $('.title', idea).text().trim() || '',
                //   date: $('.date', idea).text().trim() || '',
                //   text: $('.text', idea).text().trim() || '',
                //   follow: $('.follow', idea).text().trim() || '',
                //   tags: tags,
                //   checked: $('input[type="checkbox"]', idea).prop('checked'),
                //   comments: $('.comments-count', idea).text().trim() || 0
                // }
                // );
            });
            // data.push(col);
            // if (data[$categoryId] == undefined)
            //     data[$categoryId] = [];
            // data[$categoryId] = col;
        });
        return data;
    };
    return {
        timestamp: new Date().getTime(),
        getData: parseData
    };
};