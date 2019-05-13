$('body').on('mousedown', '.idea .toggler', function(e) {
  e.preventDefault();
  $(this).closest('.idea').toggleClass('active');
});

$('body').on('mouseup', '.idea input[type="checkbox"]', function(e) {
  e.preventDefault();
  setTimeout((function(_this) {
    return function() {
      if (_this.checked) {
        return $(_this).closest('.idea').addClass('selected');
      } else {
        return $(_this).closest('.idea').removeClass('selected');
      }
    };
  })(this));
});
