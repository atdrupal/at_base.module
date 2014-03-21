(function($){

Drupal.behaviors.atbaseTwigFilterViews = {
  attach: function(context, settings) {
    $('.ctools-dropdown').once('atbaseTwigFilterViews')
    .parent()
      .find('.ctools-dropdown').hide().end()
    .hover(
      function() { $(this).find('.ctools-dropdown').show(); },
      function() { $(this).find('.ctools-dropdown').hide(); }
    );
  }
};

})(jQuery);
