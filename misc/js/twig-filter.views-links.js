(function($){

Drupal.behaviors.atbaseTwigFilterViews = {
  attach: function(context, settings) {
    $('.ctools-dropdown').once('atbaseTwigFilterViews')
    .parent()
      .addClass('atbase-views-wrapper')
      .css({ display: 'block', position: 'relative' })
      .find('.ctools-dropdown')
        .css({ display: 'block', position: 'absolute', top: 10, right: 10 })
        .hide().end()
    .hover(
      function() { $(this).find('.ctools-dropdown').show(); },
      function() { $(this).find('.ctools-dropdown').hide(); }
    );
  }
};

})(jQuery);
