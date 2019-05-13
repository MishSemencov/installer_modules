$(document).ready(function() {
  var cloned;
  cloned = null;
  window.bindDraggable = function() {
    $('.ideas-list:not(.ideas-list-not-draggable) > li').draggable({
      revert: 'invalid',
      start: function(ui) {
        $(this).closest('[class*="col-"]').addClass('hight-index');
        cloned = $(this).clone().removeClass('ui-draggable-dragging').addClass('faded');
        cloned.insertAfter($(this));
        $(this).css({
          width: cloned.outerWidth() + 'px',
          height: cloned.outerHeight() + 'px'
        });
        if (window[$(this).closest('.ideas-list').data('onstart')]) {
          window[$(this).closest('.ideas-list').data('onstart')](event, ui, serializeDraggable());
        }
      },
      stop: function(ui) {
        $('[class*="col-"]').closest('[class*="col-"]').removeClass('hight-index');
        $('.ideas-list li.faded').remove();
      },
      drag: function(ui) {
        var list;
        list = null;
        $('.ideas-list').each(function(i, _list) {
          if (($(_list).offset().left < event.pageX) && ($(_list).offset().left + $(_list).outerWidth() > event.pageX) && ($(_list).offset().top < event.pageY) && ($(_list).offset().top + $(_list).outerHeight() > event.pageY)) {
            list = $(_list);
            return;
          }
        });
        if (!list) {
          return;
        }
        if (list.find('> li:gt(0)').length === 0) {
          list.append(cloned);
          return;
        }
        list.find('> li:gt(0)').each((function(_this) {
          return function(i, li) {
            if (($(li).offset().top < event.pageY && $(li).offset().top + $(li).outerHeight() > event.pageY) && (!$(li).hasClass('faded')) && (li !== ui.target)) {
              if ($(li).offset().top + $(li).outerHeight() / 2 < event.pageY) {
                cloned.insertAfter(list.find('> li').eq(i));
              } else {
                cloned.insertAfter(list.find('> li').eq(i + 1));
              }
              return;
            }
          };
        })(this));
        if (window[$(this).closest('.ideas-list').data('ondrag')]) {
          window[$(this).closest('.ideas-list').data('ondrag')](event, ui, serializeDraggable());
        }
      }
    });
  };
  bindDraggable();
  $('.ideas-list:not(.ideas-list-not-draggable)').droppable({
    drop: function(event, ui) {
      var classname;
      classname = cloned.closest('.ideas-list').data('classname') || 'primary';
      $(ui.draggable).find('.idea').removeClass('info danger primary').addClass(classname);
      $(ui.draggable).removeAttr('style').insertBefore(cloned);
      if (window[$(this).data('ondrop')]) {
        window[$(this).data('ondrop')](event, ui, serializeDraggable());
      }
    },
    over: function(event, ui) {},
    out: function(event, ui) {}
  });
});
