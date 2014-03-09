(function($){

Drupal.behaviors.atuiDevIndicator = {
  attach: function(context, settings) {
    $('body').once('atuiDevIndicator', function(){
      $('body')
        .addClass('environment-indicator-adjust environment-indicator-left')
        .append(
            '<div id="environment-indicator">'
              + 'DEVELOPMENT ENVIRONMENT'.replace(/(.{1})/g, '$1<br />')
            + '</div>'
        )
      ;
    });
  }
};

})(jQuery);
