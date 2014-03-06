<?php

/**
 * Add custom assets to /devel/php form to support php syntax.
 */
function at_ui_form_devel_execute_form_alter(&$form, $form_state) {
  $form['execute']['code']['#resizable'] = FALSE;
  $form['execute']['code']['#default_value']
    = !empty($form['execute']['code']['#default_value'])
        ?: (!empty($_SESSION['php_execute_code']) ? $_SESSION['php_execute_code'] : "return at_container('twig_string')->render('{{ \"Hello \"  ~ \"Drupal!\" }}');");
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
  $_SESSION['php_execute_code'] = $code = $form_state['values']['code'];

  try {
    ob_start();
    print eval($code);
    $output = ob_get_clean();
  }
  catch (Exception $e) {
    $output = $e->getMessage();
  }

  // Render
  $form['result']['#markup'] = kprint_r($output, TRUE);
  return $form['result'];
}
