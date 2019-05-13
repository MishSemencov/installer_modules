$('[data-toggle-active]').click(function(e) {
  if ($(this).data('toggle-active') === true) {
    e.preventDefault();
  }
  $(this).toggleClass('active');
});
