(function($){

setTimeout(function(){
  var ids = [
    'edit-route'
    , 'edit-blocks'
    , 'edit-attached'
    , 'edit-cache'
  ];

  for (var i in ids) {
    var e = document.getElementById(ids[i]);
    if (e) {
      var editor = CodeMirror.fromTextArea(e, {
        lineNumbers: true
        , matchBrackets: true
        , viewportMargin: Infinity
        , theme: 'monokai'
        , mode: "yaml"
      });
    }
  }

}, 500);

})(jQuery);
