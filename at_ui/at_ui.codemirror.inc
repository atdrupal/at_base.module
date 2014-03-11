<?php

function at_ui_include_code_mirror($extras = array(), $theme = 'monokai') {
  $path = at_library('code.mirror', NULL, FALSE);

  drupal_add_js($path . '/lib/codemirror.js');
  drupal_add_css($path . '/lib/codemirror.css');
  drupal_add_css($path . '/theme/'. $theme .'.css');
  foreach ($extras as $extra) {
    drupal_add_js($path . '/' . $extra);
  }
}

/**
 * Add custom assets to /devel/php form to support php syntax.
 * Include CodeMirror assets.
 *
 * @param  array  $extras
 * @param  string $theme
 */
function at_ui_include_codemirror($extras = array(), $theme = 'monokai') {
  $path = at_library('code.mirror', NULL, FALSE);

  drupal_add_js($path . '/lib/codemirror.js');
  drupal_add_css($path . '/lib/codemirror.css');
  drupal_add_css($path . '/theme/'. $theme .'.css');
  foreach ($extras as $extra) {
    drupal_add_js($path . '/' . $extra);
  }
}

/**
 * @return string.
 */
function at_ui_codemirror_submit_shortcut_hint() {
  return 'Use <code>Ctrl+s</code> or <code>Cmd+s</code> to submit the form.';
}

/**
 *
 */
function at_ui_display_file($form, $form_state, $file, $type = 'unknow') {
  drupal_add_css(drupal_get_path('module', 'at_ui') . '/misc/css/cm.css');

  switch ($type) {
    case 'twig':
      at_ui_include_codemirror(array('mode/htmlmixed/htmlmixed.js', 'mode/xml/xml.js', 'addon/mode/overlay.js'));
      drupal_add_js('@at_ui/misc/js/at.twig.js');
      break;

    case 'yaml':
      at_ui_include_codemirror(array('mode/yaml/yaml.js'));
      drupal_add_css(drupal_get_path('module', 'at_ui') . '/misc/css/cm.css');
      drupal_add_js('(function(){
        Drupal.behaviors.atuiDisplayFile = {
          attach: function(context, settings) {
            var editor = CodeMirror.fromTextArea(document.getElementById("edit-code"), {
              lineNumbers: true
              , viewportMargin: Infinity
              , readOnly: true
              , theme: "monokai"
              , mode: "text/x-yaml"
            });
          }
        };
      })(jQuery);', 'inline');
      break;

    case 'js':
    case 'php':
      at_ui_include_codemirror(array('mode/clike/clike.js', 'mode/php/php.js'));
      drupal_add_js(drupal_get_path('module', 'at_ui') . '/misc/js/devel.php.js');
      break;
  }

  $form['code'] = array(
    '#type' => 'textarea',
    '#default_value' => file_get_contents($file),
    '#disabled' => TRUE,
    '#resizable' => FALSE,
  );

  return $form;
}

function at_ui_menu_alter__devel(&$menu) {
  $menu['user/%user/devel']['page callback']
    = $menu['user/%user/devel/render']['page callback']
    = $menu['node/%node/devel']['page callback']
    = $menu['node/%node/devel/render']['page callback']
    = $menu['comment/%comment/devel']['page callback']
    = $menu['comment/%comment/devel/render']['page callback']
    = $menu['taxonomy/term/%taxonomy_term/devel']['page callback']
    = $menu['taxonomy/term/%taxonomy_term/devel/render']['page callback']
    = 'atdr';

  $menu['user/%user/devel']['page arguments']
    = $menu['user/%user/devel/render']['page arguments']
    = $menu['node/%node/devel']['page arguments']
    = $menu['node/%node/devel/render']['page arguments']
    = $menu['comment/%comment/devel']['page arguments']
    = $menu['comment/%comment/devel/render']['page arguments']
    = $menu['taxonomy/term/%taxonomy_term/devel']['page arguments']
    = $menu['taxonomy/term/%taxonomy_term/devel/render']['page arguments']
    = array(1);

  $menu['taxonomy/term/%taxonomy_term/devel']['page arguments']
    = $menu['taxonomy/term/%taxonomy_term/devel/render']['page arguments']
    = array(2);

  if (isset($menu['block/%bean_delta/devel'])) {
    $menu['block/%bean_delta/devel']['page callback'] = 'atdr';
    $menu['block/%bean_delta/devel/render']['page callback'] = 'atdr';
    $menu['block/%bean_delta/devel']['page arguments'] = array(1);
    $menu['block/%bean_delta/devel/render']['page arguments'] = array(1);
  }
}
