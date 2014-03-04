(function($){

var cm_mixed_mode = {
    name: 'htmlmixed'
  , scriptTypes: [
      {matches: /\/x-handlebars-template|\/x-mustache/i,  mode: null}
    , {matches: /(text|application)\/(x-)?vb(a|script)/i, mode: "vbscript"}
  ]
};

var ui = {
  pane: {
    editor_id: 0,
    tpl: {
      pane_wrapper: '<div class="atui-pane-wrapper"></div>'
      , pane_placeholder: '<em class="atui-pane-placeholder">%put-your-content-here</em>'
      , links: '<div class="atui-pane-links-wrapper">'
        + '  <ul class="atui-pane-links">'
        + '    <li><a href="#" class="button remove" data-action="remove">Remove</a></li>'
        + '    <li><a href="#" class="button edit"   data-action="edit">Edit</a></li>'
        + '    <li><a href="#" class="button before" data-action="before">Before</a></li>'
        + '    <li><a href="#" class="button after"  data-action="after">After</a></li>'
        + '  </ul>'
        + '</div>'
    },
    factory: function() {
      return $(ui.pane.tpl.pane_wrapper)
        .append(ui.pane.tpl.links)
        .append(ui.pane.tpl.pane_placeholder)
      ;
    },
    link: {
      click: {
        edit: function($w) {
            var id = 'atuid-pane-text-' + (ui.pane.editor_id++);
            var html = $w.clone().find('.atui-pane-links-wrapper').remove().end().html();
            var save = function(cm) {
              cm.toTextArea();

              var $e = $('#' + id);
              var html = $e.val();
              var $w = $e.closest('.atui-pane-wrapper');

              $e.closest('form').remove().end();
              $w.append(html);
            };

            $w
              .children().not('.atui-pane-links-wrapper').remove().end().end()
              .append('<form></form>')
              .find('form')
                .append('<textarea id="'+ id +'" class="form-textarea"></textarea>')
                .append('<div class="form-actions form-wrapper">'
                    + ' <div class="description"><code>Ctrl-S</code>/<code>Cmd-S</code> to save</div>'
                    + '</div>'
                  )
                .find('.form-submit').click(ui.pane.save)
            ;
            $('#' + id).val(html);

            var editor = CodeMirror.fromTextArea(document.getElementById(id), {
              lineNumbers: true
              , viewportMargin: Infinity
              , theme: 'monokai'
              , extraKeys: {"Cmd-S": save , "Ctrl-S": save}
              , blur: function(CodeMirror) {
                console.log(CodeMirror.getValue);
              }
              , mode: cm_mixed_mode
            });
        },
        add: function($w, action) {
            var $pane = ui.pane.factory();
            $w[action]($pane);
            Drupal.attachBehaviors($pane.parent(), Drupal.settings);
        },
        remove: function($w) {
            if (confirm('Remove this pane?')) $w.remove();
        },
        callback: function() {
          var action = $(this).data('action');
          var $w = $(this).closest('.atui-pane-wrapper');

          switch (action) {
            case 'edit':   ui.pane.link.click.edit($w);        break;
            case 'remove': ui.pane.link.remove($w);            break;
            case 'before':
            case 'after':  ui.pane.link.click.add($w, action); break;
          }

          return false;
        }
      }
    }
  }
};

Drupal.behaviors.atUiLiveEditableRouteInit = {
  attach: function(context, settings) {
    $('#editable-route-preview', context).once('atUiLiveEditableRoute', function() {
      // Make sure the region wrapper able to container contextual links
      $(this).closest('.region').addClass('atui-pane-links-region');

      // Add links to panes
      $(this).children().each(function(){
            $(this).wrap(ui.pane.tpl.pane_wrapper)
                   .parent().append(ui.pane.tpl.links);
            Drupal.attachBehaviors($(this).closest('.atui-pane-wrapper'), settings);
      });

      // ---------------------
      // Just for test
      // ---------------------
      setTimeout(function(){
          $('.atui-pane-wrapper .atui-pane-links')
            // add content before
            .find('a:eq(2)').trigger('click').end()
            // add content after
            .find('a:eq(3)').trigger('click').end()
            // Edit content
            .find('a:eq(1)').trigger('click').end()
          ;
      }, 500);
    });
  }
};

Drupal.behaviors.atUiLiveEditableRouteLinks = {
  attach: function(context, settings) {
    $('.atui-pane-wrapper', context).once('atUiLiveEditableRouteLinks', function() {
        $('.atui-pane-links-wrapper .atui-pane-links li a', $(this))
          .click(ui.pane.link.click.callback);
    });
  }
};

})(jQuery);