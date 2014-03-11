<?php

function at_ui_twig_form($form, $form_state) {
  $form = array('#redirect' => FALSE);

  $form['code'] = array(
    '#type' => 'textarea',
    '#resizable' => FALSE,
    '#default_value' => (isset($_SESSION['twig_execute_code'])
                          ? $_SESSION['twig_execute_code']
                          : "{{ user.access|fn__format_date('long') }}"),
    '#description' => at_ui_codemirror_submit_shortcut_hint(),
    '#ajax' => array(
      'event' => 'change',
      'callback' => 'at_ui_twig_form_ajax_callback',
      'wrapper' => 'at-ui-results',
    ),
  );

  $form['result'] = array(
    '#prefix' => '<div id="at-ui-results">',
    '#suffix' => '</div>',
    '#markup' => '',
  );

  $form['submit'] = array('#type' => 'submit', '#value' => 'Execute');

  return $form;
}

/**
 * Ajax callback for at_ui_twig_form().
 */
function at_ui_twig_form_ajax_callback($form, &$form_state) {
  $_SESSION['twig_execute_code'] = $code = $form_state['values']['code'];

  ob_start();
  try {
    print at_container('twig_string')->render($code);
  }
  catch (Exception $e) {
    drupal_set_message($e->getMessage());
    at_trace($e->getTrace(), 'message');
  }

  $output = ob_get_clean();

  // Render
  $form['result']['#markup'] = kprint_r($output, TRUE);
  return $form['result'];
}
