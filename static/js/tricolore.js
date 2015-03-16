+function registerBootstrapTooltips()
{
  "use strict";

  $('[data-toggle="tooltip"]').tooltip({container: 'body'});
}();

+function searchInLoadedClasses()
{
  "use strict";

  $("#search-loaded-classes").keyup(function () {
    var filter = $(this).val();

    $("ul li#loaded-class-node").each(function () {
      var search = $(this).text().search(new RegExp(escapeRegExp(filter), "i"));

      if (search < 0) {
        $(this).hide();
      } else {
        $(this).show();
      }
    });

    var founded_classes = $('li#loaded-class-node').filter(':visible').length;

    if (founded_classes == 0) {
      $('#class-not-found').show();
    } else {
      $('#class-not-found').hide();
    }

    $('#loaded-classes-count').text(founded_classes);
  });
}();

+function destroyAllFlashMessages()
{
  "use strict";

  setTimeout(function () {
    $(".flash-message").hide('fast', function () {
      $(this).remove();
    });
  }, 2000);
}();

function escapeRegExp(string) {
  return string.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}
