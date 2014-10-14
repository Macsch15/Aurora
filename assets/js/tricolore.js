$(function() {
  $('[data-toggle="tooltip"]').tooltip({container: 'body'});

  var filter = $('.nav a').filter(function () {
    return this.href == location.href;
  });

  filter.parent().addClass('active').siblings().removeClass('active');

  $('.nav a').click(function () {
    $(this).parent().addClass('active').siblings().removeClass('active');
  });
});
