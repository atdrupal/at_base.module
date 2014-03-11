<?php

/**
 * Implements hook_init()
 */
function at_ui_init() {
  if (at_debug() && !defined('AT_DISABLE_DEVELOPMENT_INDICATOR') && empty($_GET['no-indicator'])) {
    drupal_add_css(drupal_get_path('module', 'at_ui') . '/misc/css/dev.indicator.css');
    drupal_add_js(drupal_get_path('module', 'at_ui') . '/misc/js/dev.indicator.js');
  }
}

/**
 * Implements hook_menu()
 */
function at_ui_menu_alter(&$menu) {
  foreach ($menu as &$item) {
    if ($item['page callback'] === 'Drupal\at_base\Route\Controller::pageCallback') {
      $item['page callback'] = 'Drupal\at_ui\Route\Controller::pageCallback';
    }
  }
}
