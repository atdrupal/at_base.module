(function($){

$(function(){
  $('#at-ui-sql-form').find('.form-submit').hide();

  var save = function(cm) {
    $('#edit-string').val(cm.getValue()).trigger('change');
  };

  var editor = CodeMirror.fromTextArea(document.getElementById("edit-string"), {
    lineNumbers: true
    , matchBrackets: true
    , viewportMargin: Infinity
    , theme: 'monokai'
    , extraKeys: {"Cmd-S": save , "Ctrl-S": save}
    , mode: "text/x-mariadb"
  });
});

})(jQuery);
