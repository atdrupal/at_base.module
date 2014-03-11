(function($){

$(function(){
  // Hide the form submit button
  $('#devel-execute-form').find('.form-submit').hide();

  var save = function(cm) {
    $('#edit-code').val(cm.getValue()).trigger('change');
  };

  var editor = CodeMirror.fromTextArea(document.getElementById('edit-code'), {
    lineNumbers: true
    , matchBrackets: true
    , viewportMargin: Infinity
    , readOnly: 0 === document.getElementsByClassName('form-submit').length
    , theme: 'monokai'
    , extraKeys: {'Cmd-S': save , 'Ctrl-S': save}
    , mode: 'text/x-php'
  });
});

})(jQuery);
