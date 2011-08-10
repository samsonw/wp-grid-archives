jQuery(document).ready(function($) {
  $("ul.ga_year_list").tabs("div.ga_pane", {
      effect: 'fade',
      fadeOutSpeed: 50,
      fadeInSpeed: 1200
  });

  var scrollElement = 'html, body';
  $('html, body').each(function () {
    var initScrollTop = $(this).attr('scrollTop');
    $(this).attr('scrollTop', initScrollTop + 1);
    if ($(this).attr('scrollTop') == initScrollTop + 1) {
      scrollElement = this.nodeName.toLowerCase();
      $(this).attr('scrollTop', initScrollTop);
      return false;
    }
  });

  $("a[href^='#']").click(function(event) {
    event.preventDefault();

    var $this = $(this),
        target = this.hash,
        $target = $(target);

    $(scrollElement).stop().animate({
      'scrollTop': $target.offset().top
    }, 2000, 'swing', function() {
      // window.location.hash = target;
    });
  });
});
