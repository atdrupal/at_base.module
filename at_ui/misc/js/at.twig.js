(function($){

$(function(){
  // Hide the form submit button
  $('#at-ui-twig-form').find('.form-submit').hide();

  var save = function(cm) {
    $('#edit-string').val(cm.getValue()).trigger('change');
  };

  CodeMirror.defineMode('mustache', function(config, parserConfig) {
    var mustacheOverlay = {
      token: function(stream, state) {
        var ch;
        if (stream.match('{{')) {
          while ((ch = stream.next()) != null)
            if (ch == '}' && stream.next() == '}') break;
          stream.eat('}');
          return 'mustache';
        }
        while (stream.next() != null && !stream.match('{{', false)) {}
        return null;
      }
    };

    return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || 'text/html'), mustacheOverlay);
  });

  var editor = CodeMirror.fromTextArea(document.getElementById('edit-string'), {
    lineNumbers: true
    , viewportMargin: Infinity
    , theme: 'monokai'
    , extraKeys: {'Cmd-S': save , 'Ctrl-S': save}
    , mode: 'mustache'
  });

});

})(jQuery);
