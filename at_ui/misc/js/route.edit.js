(function($){

setTimeout(function(){

  var save = function() {
    $('#edit-code').parents('form').trigger('submit');
  };

  var ids = ['edit-route', 'edit-blocks', 'edit-attached', 'edit-cache'];

  for (var i in ids) {
    var e = document.getElementById(ids[i]);
    if (e) {
      var editor = CodeMirror.fromTextArea(e, {
        lineNumbers: true
        , matchBrackets: true
        , viewportMargin: Infinity
        , theme: 'monokai'
        , extraKeys: {"Cmd-S": save , "Ctrl-S": save}
        , mode: "yaml"
      });
    }
  }

}, 500);

})(jQuery);
