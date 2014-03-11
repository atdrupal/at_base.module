<?php

/**
 * Editing form for editable-route.
 *
 * @todo  Vertical tab-group for route, blocks, …
 * @todo  Case developer edit a template route…
 * @todo  Warning if route configuration file is not editable.
 * @todo  Backup configuration file before saving…
 */
function at_ui_route_form($form, $form_state, $route) {
  $parts = array('content' => 'Content', 'blocks' => 'Blocks', 'attached' => 'Attached', 'cache' => 'Cache');

  foreach ($parts as $k => $v) {
    if (isset($route[$k])) {
      $$k = $route[$k];
      unset($route[$k]);
    }
  }

  $form['route'] = array(
    '#type' => 'textarea',
    '#title' => 'Route',
    '#required' => TRUE,
    '#resizable' => FALSE,
    '#default_value' => yaml_emit($route),
  );

  if (isset($content)) {
    $form['#prefix']  = '<div id="editable-route-preview">';
    $form['#prefix'] .= ' ' . $content;
    $form['#prefix'] .= '</div>';

    $form['#suffix']  = '<div id="editable-route-icons">';
    $form['#suffix'] .= theme('item_list', array('items' => array(
        at_icon('fa-edit'),
        at_icon('fa-angle-up'),
        at_icon('fa-angle-down'),
        at_icon('fa-minus'),
        at_icon('fa-plus'),
    )));
    $form['#suffix'] .= '</div>';

    $form['content'] = array(
      '#type' => 'textarea',
      '#title' => 'Content',
      '#required' => TRUE,
      '#resizable' => FALSE,
      '#default_value' => at_container('twig_string')->render($content),
    );
    unset($content);
  }

  foreach ($parts as $k => $label) {
    if (isset($$k)) {
      $form[$k] = array(
        '#type' => 'textarea',
        '#title' => $label,
        '#required' => TRUE,
        '#resizable' => FALSE,
        '#default_value' => yaml_emit($$k),
      );
    }
  }

  $form['submit'] = array('#type' => 'submit', '#value' => t('Save'));

  return $form;
}
