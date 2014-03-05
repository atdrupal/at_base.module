<?php

/**
 * Include CodeMirror assets.
 *
 * @param  array  $extras
 * @param  string $theme
 */
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
 */
function at_ui_form_devel_execute_form_alter(&$form, $form_state) {
  $form['execute']['code']['#resizable'] = FALSE;
  $form['execute']['code']['#description']  = isset($form['string']['#description']) ? ($form['string']['#description'] . '<br />') : '';
  $form['execute']['code']['#description'] .= 'Use <code>Ctrl+s</code> or <code>Cmd+s</code> to submit the form.';

  at_ui_include_code_mirror(array('mode/clike/clike.js', 'mode/php/php.js'));

  drupal_add_js(drupal_get_path('module', 'at_ui') . '/misc/js/devel.php.js');
  drupal_add_css(drupal_get_path('module', 'at_ui') . '/misc/css/cm.css');
}

/**
 * Add custom assets to /devel/php form to support php syntax.
 *
 * @link https://github.com/marijnh/CodeMirror/issues/103
 * @link http://codemirror.net/demo/mustache.html
 */
function at_ui_form_at_form_alter(&$form, $form_state) {
  if ($form['#at_form'][0] === 'Drupal\at_ui\Controller\TwigFormController') {
    $form['string']['#resizable'] = FALSE;
    $form['string']['#description']  = isset($form['string']['#description']) ? ($form['string']['#description'] . '<br />') : '';
    $form['string']['#description'] .= 'Use <code>Ctrl+s</code> or <code>Cmd+s</code> to submit the form.';

    at_ui_include_code_mirror(array(
      'mode/htmlmixed/htmlmixed.js',
      'mode/xml/xml.js',
      'addon/mode/overlay.js',
    ));

    drupal_add_js(drupal_get_path('module', 'at_ui') . '/misc/js/at.twig.js');
    drupal_add_css(drupal_get_path('module', 'at_ui') . '/misc/css/cm.css');
  }
}
