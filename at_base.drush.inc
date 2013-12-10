<?php
use \Drupal\at_base\Helper\ConstantParser;

require_once dirname(__FILE__) . '/at_base.module';
require_once dirname(__FILE__) . '/drush/at_services.inc';

/**
 * Implements hook_drush_command()
 */
function at_base_drush_command() {
  foreach (array('at_base' => 'at_base') + at_modules('at_base', 'drush') as $module) {
    foreach ($config = at_config($module, 'drush')->get('commands') as $command => $definition) {
      if (!empty($definition['bootstrap'])) {
        $definition['bootstrap'] = at_id(new ConstantParser($definition['bootstrap']))->parse();
      }
      $commands[$command] = $definition;
    }
  }

  return $commands;
}

if (class_exists('DrushMakeProject_Library')) {
  require_once dirname(__FILE__) . '/drush/at_require.inc';
}
else {
  function drush_at_require() {
    drush_print_r('at_require depends on make feature of Drush, which introduced in 5.x');
  }
}
