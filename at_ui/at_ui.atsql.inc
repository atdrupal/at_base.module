<?php

function at_ui_sql_form($form, &$form_state) {
  $form = array('#redirect' => FALSE);

  $form['string'] = array(
    '#title' => 'SQL Query',
    '#type' => 'textarea',
    '#resizable' => FALSE,
    '#default_value' => (isset($_SESSION['sql_execute_code'])
                ? $_SESSION['sql_execute_code']
                : "SELECT u.uid, u.name, u.mail, u.access \nFROM {users} u \nWHERE u.uid \nORDER BY u.access DESC \nLIMIT 10"),
    '#description' => at_ui_codemirror_submit_shortcut_hint(),
    '#ajax' => array(
      'event' => 'change',
      'callback' => 'at_ui_sql_form_ajax_callback',
      'wrapper' => 'at-sql-results',
    ),
  );

  $form['result'] = array(
    '#prefix' => '<div id="at-sql-results">',
    '#markup' => '',
    '#suffix' => '</div>',
  );

  return $form;
}

function at_ui_sql_form_ajax_callback($form, &$form_state) {
  $_SESSION['sql_execute_code'] = $sql = $form_state['values']['string'];

  $query = db_query($sql);

  $header = $rows = array();
  foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
    if (empty($header)) {
      $header = array_keys($row);
    }
    $rows[] = $row;
  }

  $form['result']['#markup'] = array(
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
    '#caption' => t('Results'),
    '#sticky' => FALSE,
  );
  $form['result']['#markup'] = drupal_render($form['result']['#markup']);

  return $form['result'];
}
