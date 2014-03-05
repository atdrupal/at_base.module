<?php

/**
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
 * Add custom assets to /devel/php form to support php syntax.
 */
function at_ui_form_devel_execute_form_alter(&$form, $form_state) {
  $form['execute']['code']['#resizable'] = FALSE;
  $form['execute']['code']['#default_value']
    = !empty($form['execute']['code']['#default_value'])
        ?: (!empty($_SESSION['sql_execute_code']) ? $_SESSION['sql_execute_code'] : '');
  $form['execute']['code']['#description'] .= ' ' . at_ui_codemirror_submit_shortcut_hint();
  $form['execute']['code']['#ajax'] = array(
    'event' => 'change',
    'callback' => 'at_ui_form_devel_execute_form_alter_ajax_callback',
    'wrapper' => 'at-ui-results',
  );

  $form['result'] = array(
    '#prefix' => '<div id="at-ui-results">',
    '#suffix' => '</div>',
    '#markup' => '',
  );

  at_ui_include_codemirror(array('mode/clike/clike.js', 'mode/php/php.js'));
  drupal_add_js(drupal_get_path('module', 'at_ui') . '/misc/js/devel.php.js');
  drupal_add_css(drupal_get_path('module', 'at_ui') . '/misc/css/cm.css');
}

function at_ui_form_devel_execute_form_alter_ajax_callback($form, &$form_state) {
  $_SESSION['sql_execute_code'] = $code = $form_state['values']['code'];

  // Execute the code
  ob_start();
  print eval($code);
  $output = ob_get_clean();

  // Render
  $form['result']['#markup'] = kprint_r($output, TRUE);
  return $form['result'];
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

    at_ui_include_codemirror(array(
      'mode/htmlmixed/htmlmixed.js',
      'mode/xml/xml.js',
      'addon/mode/overlay.js',
    ));

    drupal_add_js(drupal_get_path('module', 'at_ui') . '/misc/js/at.twig.js');
    drupal_add_css(drupal_get_path('module', 'at_ui') . '/misc/css/cm.css');
  }
}
