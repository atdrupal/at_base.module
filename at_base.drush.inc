<?php
use \Drupal\at_base\Helper\ConstantParser;

/**
 * Implements hook_drush_command()
 */
function at_base_drush_command() {
  foreach (array('at_base' => 'at_base') + at_modules('at_base', 'drush') as $module) {
    foreach ($config = at_config($module, 'drush')->get('commands') as $command) {
      if (!empty($command['bootstrap'])) {
        $command['bootstrap'] = at_id(new ConstantParser($command['bootstrap']))->parse();
      }
      $commands[] = $command;
    }
  }

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
