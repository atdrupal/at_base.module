<?php

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
  }
}
