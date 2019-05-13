//# sourceMappingURL=lightbox.min.map;$(document).ready(function() {
  var activeTile, dropper, lastIndex, lastTime, max, moveHandler, rects, render, setRects, tiles;
  tiles = $('.custom-drag > .item');
  rects = [];
  max = 0;
  activeTile = {
    index: -1,
    pos: {
      top: 0,
      left: 0
    },
    offset: {
      top: 0,
      left: 0
    },
    lastPos: {
      top: 0,
      left: 0
    }
  };
  lastIndex = -1;
  dropper = $('.custom-drag .dropper');
  var _max;
  setRects = function() {
    var counter;
    max = 0;
    _max = 0;
    counter = 0;
    tiles.each(function(i, node) {
      rects[i] = {
        top: max,
        left: $('.custom-drag').outerWidth() * counter * .33,
        width: $(node).outerWidth(),
        height: $(node).outerHeight()
      };
      if (_max < max + rects[i].height) {
        _max = max + rects[i].height;
      }
      counter++;
      if (counter > 2) {
        counter = 0;
        max = _max;
      }
    });
  };
  render = function() {
    var i, j, len, tileRect;
    $('.custom-drag').css('height', _max);
    for (i = j = 0, len = rects.length; j < len; i = ++j) {
      tileRect = rects[i];
      tiles.eq(i).css('top', tileRect.top).css('left', tileRect.left);
    }
  };
  $(window).resize(function() {
    waitForFinalEvent(function() {
      setRects();
      return render();
    }, 300, 'custom-drag');
  });
  setTimeout(function() {
    setRects();
    render();
    return tiles.each(function(i, tile) {
      return setTimeout(function() {
        return $(tile).addClass('loaded');
      }, 50 * i);
    });
  }, 100);
  lastTime = new Date().getTime();
  moveHandler = function(e) {
    var currentTime;

    activeTile.pos.top = e.pageY - activeTile.lastPos.top;
    activeTile.pos.left = e.pageX - activeTile.lastPos.left;
    dropper.css('transform', "translate(" + activeTile.pos.left + "px," + activeTile.pos.top + "px)");
    currentTime = new Date().getTime();
    if (currentTime - lastTime < 300) {
      return;
    }
    lastTime = currentTime;
    tiles.each(function(i, tile) {
      var active, actived;
      if (($(tile).offset().left < e.pageX && e.pageX < $(tile).offset().left + rects[i].width) && ($(tile).offset().top < e.pageY && e.pageY < $(tile).offset().top + rects[i].height)) {
        if (i === activeTile.index) {
          return;
        }
        active = tiles.eq(activeTile.index);
        actived = tiles.eq(i);
        if (i < activeTile.index) {
          active.insertBefore(actived);
        } else {
          active.insertAfter(actived);
        }
        tiles = $('.custom-drag > .item');
        setTimeout(function() {
          setRects();
          render();
          if (window[$('.custom-drag').data('ondrag')]) {
            return window[$('.custom-drag').data('ondrag')](e, null, serializeDraggable($('.custom-drag')));
        }
        }, 200);
        lastIndex = i;
        activeTile.index = active.index();
      }
    });
  };

mouseDownTouchStartHandler = function(e, self, mouseEvent) {
    var html;
    if ($(e.target).closest('.toggler, a, input').length > 0) {
        return;
    }
    e.preventDefault();
    activeTile.index = $(self).index();
    activeTile.lastPos = {
        top: e.pageY,
        left: e.pageX
    };
    tiles.eq(activeTile).addClass('drag-anchor');
    html = tiles.eq(activeTile.index).html();
    tiles.eq(activeTile.index).addClass('drag-anchor').html(dropper.html()).css({
        // lineHeight: rects[activeTile.index].height + "px",
        height: rects[activeTile.index].height
    });
    dropper.html(html).addClass('active').css({
        top: rects[activeTile.index].top,
        left: rects[activeTile.index].left,
        width: rects[activeTile.index].width,
        height: rects[activeTile.index].height
    });
    if (window[$('.custom-drag').data('onstart')]) {
        window[$('.custom-drag').data('onstart')](e, null, serializeDraggable($('.custom-drag')));
    }
    $(document).on(mouseEvent, moveHandler);
};

mouseUpTouchEndHandler = function(e, self, mouseEvent) {
    var html;
    if ($(e.target).closest('.toggler, a, input').length > 0) {
        return;
    }
    if (activeTile.index < 0) {
        return;
    }
    html = tiles.eq(activeTile.index).html();
    tiles.eq(activeTile.index).removeClass('drag-anchor').css({
        lineHeight: '',
        height: ''
    }).html(dropper.html());
    dropper.html(html).removeClass('active').css('transform', "translate(0,0)");
    activeTile.index = -1;
    if (window[$('.custom-drag').data('ondrop')]) {
        window[$('.custom-drag').data('ondrop')](e, null, serializeDraggable($('.custom-drag')));
    }
    $(document).off(mouseEvent, moveHandler);
};

// $('.custom-drag').bind("touchstart", '.item', function(e){
//     mouseDownTouchStartHandler(e, this, "ontouchmove");
// });
  $('.custom-drag').on('mousedown', '.item', function(e) {
      mouseDownTouchStartHandler(e, this, 'mousemove');
  });

// $(document).bind("touchend", function(e){
//     mouseDownTouchStartHandler(e, this, "ontouchmove");
// });
  $(document).on('mouseup', function(e) {
      mouseUpTouchEndHandler(e, this, 'mousemove');
  });
  $('.custom-drag').on('click', '.idea .toggler', function(e) {
    setRects();
    render();
  });
// });
$('[data-toggle-active]').click(function(e) {
  if ($(this).data('toggle-active') === true) {
    e.preventDefault();
  }
  $(this).toggleClass('active');
});
;window.serializeDraggable = function(selected) {
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
;$(document).ready(function() {
  var cloned;
  cloned = null;
  window.bindDraggable = function() {
    $('.ideas-list:not(.ideas-list-not-draggable) > li').draggable({
      revert: 'invalid',
      start: function(e, ui) {
        event = e || window.event;
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
      stop: function(e, ui) {
        $('[class*="col-"]').closest('[class*="col-"]').removeClass('hight-index');
        $('.ideas-list li.faded').remove();
      },
      drag: function(e, ui) {
        var list;
        list = null;
        event = e || window.event;
        $('.ideas-list').each(function(i, _list) {
          // console.log($(_list));
          // console.log($(_list).offset());
          // console.log($(_list).outerWidth());
          // console.log($(_list).outerHeight());
          // console.log(event.pageX);
          // console.log(event.pageY);
          if (($(_list).offset().left < event.pageX)
              && ($(_list).offset().left + $(_list).outerWidth() > event.pageX)
              && ($(_list).offset().top < event.pageY)
              && ($(_list).offset().top + $(_list).outerHeight() > event.pageY)) {
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
            if (($(li).offset().top < event.pageY
                    && $(li).offset().top + $(li).outerHeight() > event.pageY)
                && (!$(li).hasClass('faded'))
                && (li !== ui.target)) {
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
      if ($(ui.draggable.context).hasClass("idea-category-wrap")) {
          if (window[$(this).data('ondrop')]) {
              window[$(this).data('ondrop')](event, ui, serializeDraggable());
          }
          return;
      }
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
;$('[data-dropdown]').each(function(i, dropdown) {
  $('.anchor', dropdown).mousedown(function(e) {
    e.preventDefault();
    return $(dropdown).toggleClass('active');
  });
  $('body').click(function(e) {
    if ($(e.target).closest(dropdown).length === 0) {
      $(dropdown).removeClass('active');
    }
  });
  $('a, li', dropdown).mousedown(function(e) {
    $(dropdown).removeClass('active');
  });
});
