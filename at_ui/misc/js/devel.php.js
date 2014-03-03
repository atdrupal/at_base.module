(function($){

setTimeout(function(){

var editor = CodeMirror.fromTextArea(document.getElementById("edit-code"), {
  lineNumbers: true
  , matchBrackets: true
  , viewportMargin: Infinity
  , theme: 'monokai'
  , mode: "text/x-php"
});

}, 500);

})(jQuery);
