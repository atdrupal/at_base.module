(function($){

setTimeout(function(){

var editor = CodeMirror.fromTextArea(document.getElementById("edit-code"), {
  lineNumbers: true
  , matchBrackets: true
  , mode: "text/x-php"
});

// mode: "application/x-httpd-php",

}, 500);

})(jQuery);
