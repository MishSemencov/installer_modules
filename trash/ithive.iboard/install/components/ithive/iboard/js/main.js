$(document).ready(function() {
  var setFooterHeight, teleport;
  window.waitForFinalEvent = (function() {
    var timers;
    timers = {};
    return function(callback, ms, uniqueId) {
      if (!uniqueId) {
        uniqueId = 'Don\'t call this twice without a uniqueId';
      }
      if (timers[uniqueId]) {
        clearTimeout(timers[uniqueId]);
      }
      timers[uniqueId] = setTimeout(callback, ms);
    };
  })();

  /*teleport */
  (teleport = function() {
    $('[data-tablet]').each(function(i, elem) {
      var parent;
      if ($(document).width() <= 992) {
        $(elem).appendTo($($(elem).data('tablet')));
      } else {
        parent = $($(elem).data('desktop'));
        $(elem).appendTo(parent);
      }
    });
    $('[data-mobile]').each(function(i, elem) {
      var parent;
      if ($(document).width() <= 768) {
        $(elem).appendTo($($(elem).data('mobile')));
      } else {
        parent = $($(elem).data('desktop'));
        $(elem).appendTo(parent);
      }
    });
  })();

  /*scrollto */
  $('[data-scrollto]').click(function(e) {
    e.preventDefault();
    $('html,body').animate({
      scrollTop: $($(this).data('scrollto')).offset().top
    }, 500);
  });
  setFooterHeight = function() {
    var footerHeight;
    footerHeight = $('.main-footer').outerHeight();
    $('main').css({
      paddingBottom: footerHeight + 'px'
    });
    $('.main-footer').css({
      marginTop: -footerHeight + 'px'
    });
  };
  setFooterHeight();
  $(window).resize(function() {
    waitForFinalEvent((function() {
      setFooterHeight();
      teleport();
    }), 200, '');
  });

  $(document).click(function(event) {
      if ($(event.target).closest(".idea-popup-btn").length)
          return;
      $(".ideas-entity-popup").removeClass("visible");
      event.stopPropagation();
  });

  $(document).on("click", ".idea-popup-btn", function (event) {
      if(!event) event = window.event;
      if ($(event.target).closest(".ideas-entity-popup").length)
          return;
      $thisPopup = $(this).find(".ideas-entity-popup");
      $thisPopupVisible = $thisPopup.hasClass("visible");
      $(".ideas-entity-popup").removeClass("visible");
      if (!$thisPopupVisible)
          $thisPopup.addClass("visible");
  });
});
