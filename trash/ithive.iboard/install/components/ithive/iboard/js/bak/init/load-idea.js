$('[data-load-idea]').click(function(e) {
  var btn;
  btn = $(this);
  $.ajax({
    method: 'GET',
    url: "data/idea" + (Math.floor(Math.random() * 3) + 1) + ".html",
    success: function(res) {
      var classname, data, idea;
      idea = $("<li>" + res + "</li>").insertAfter(btn.parent());
      classname = btn.closest('.ideas-list').data('classname') || 'primary';
      idea.find('.idea').removeClass('info danger primary').addClass(classname);
      window.bindDraggable();
      data = serializeDraggable().getData();
      $('#count1').text(data[0].length > 0 ? "(" + data[0].length + ")" : "");
      $('#count2').text(data[1].length > 0 ? "(" + data[1].length + ")" : "");
      $('#count3').text(data[2].length > 0 ? "(" + data[2].length + ")" : "");
      if (data[0].length > 0) {
        $('.empty-text').eq(0).addClass('hidden');
      } else {
        $('.empty-text').eq(0).removeClass('hidden');
      }
      if (data[1].length > 0) {
        $('.empty-text').eq(1).addClass('hidden');
      } else {
        $('.empty-text').eq(1).removeClass('hidden');
      }
      if (data[2].length > 0) {
        return $('.empty-text').eq(2).addClass('hidden');
      } else {
        return $('.empty-text').eq(2).removeClass('hidden');
      }
    }
  });
});
