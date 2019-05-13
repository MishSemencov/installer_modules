$(document).ready(function() {
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
            return window[$('.custom-drag').data('ondrag')](event, null, serializeDraggable($('.custom-drag')));
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
});
