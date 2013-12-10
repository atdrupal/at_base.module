<?php

/**
 * Implements hook_drush_command()
 */
function at_base_drush_command() {
  $commands['at_require'] = array(
    'callback' => 'drush_at_require',
    'description' => 'Help download all missing dependencies.',
    'arguments' => array(
      'module' => 'Download dependencies for specific module',
    ),
    'examples' => array(
      'drush at_require' => 'Download dependencies for all modules',
      'drush at_require module_name' => 'Download dependencies for specific module',
    ),
    'aliases' => array('at-require', 'atr'),
    'bootstrap' => DRUSH_BOOTSTRAP_FULL,
  );
  return $commands;
}

if (class_exists('DrushMakeProject_Library') {
  require_once dirname(__FILE__) . '/drush/at_require.inc';
}
else {
  function drush_at_require() {
    drush_print_r('at_require depends on make feature of Drush, which introduced in 5.x');
  }
}
