window.serializeDraggable = function(selected) {
  var parseData;
  parseData = function() {
    var data;
    data = [];
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
        col.push({
          id: $('.idea-id-input', idea).val().trim() || '',
          title: $('.title', idea).text().trim() || '',
          date: $('.date', idea).text().trim() || '',
          text: $('.text', idea).text().trim() || '',
          follow: $('.follow', idea).text().trim() || '',
          tags: tags,
          checked: $('input[type="checkbox"]', idea).prop('checked'),
          comments: $('.comments-count', idea).text().trim() || 0
        });
      });
      data.push(col);
    });
    return data;
  };
  return {
    timestamp: new Date().getTime(),
    getData: parseData
  };
};
