(function($){

setTimeout(function(){

var save = function() {
  $('#edit-code').parents('form').trigger('submit');
};

var editor = CodeMirror.fromTextArea(document.getElementById("edit-code"), {
  lineNumbers: true
  , matchBrackets: true
  , viewportMargin: Infinity
  , theme: 'monokai'
  , extraKeys: {"Cmd-S": save , "Ctrl-S": save}
  , mode: "text/x-php"
});

}, 500);

})(jQuery);
