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

/**
 * Define form structure for /at/twig/views
 */
function at_ui_twig_views_form($form = array(), $form_state) {
  $view_name = arg(3);

  $names = array('' => '-- Select a view --');
  foreach (views_get_all_views() as $name => $def) {
    $names[$name] = $def->human_name;
  }

  $form['name'] = array(
    '#type' => 'select',
    '#title' => 'View name',
    '#options' => $names,
    '#default_value' => $view_name,
  );

  if (!empty($view_name)) {
    $display_id = arg(4) ? arg(4) : 'default';
    $display_ids = array();
    $fields_markup = '';

    if ($view = views_get_view($view_name)) {
      foreach ($view->display as $name => $def) {
        $display_ids[$name] = $def->display_title;
      }

      $form['display_id'] = array(
        '#prefix' => '<div id="at-ui-displays">',
        '#suffix' => '</div>',
        '#type' => 'select',
        '#title' => 'Display ID',
        '#default_value' => $display_id,
        '#options' => $display_ids,
      );

      $fields = array(
        'row.item' => array(),
        'title', 'exposed', 'attachment_before', 'attachment_after', 'rows'
      );

      if (!empty($display_id)) {
        $view->preview($display_id, array('all', 'all', 'all', 'all'));
        foreach ($view->result[0] as $k => $v) {
          $fields['row.item'][] = $k;
        }
        $fields_markup = '<pre><code>' .print_r($fields, TRUE) . '</code></pre>';

        $form['fields'] = array(
          '#type' => 'item',
          '#title' => 'Available variables',
          '#markup' => $fields_markup,
        );
      }
    }
  }

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Update',
    '#submit' => array(function($form, &$form_state){
      drupal_goto("at/twig/views/{$form_state['values']['name']}/{$form_state['values']['display_id']}");
    })
  );

  return $form;
}
