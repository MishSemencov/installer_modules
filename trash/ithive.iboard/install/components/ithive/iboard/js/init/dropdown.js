$('[data-dropdown]').each(function(i, dropdown) {
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
