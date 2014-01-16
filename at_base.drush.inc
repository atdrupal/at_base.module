<?php

require_once dirname(__FILE__) . '/at_base.module';
require_once dirname(__FILE__) . '/drush/at_services.inc';
require_once dirname(__FILE__) . '/drush/at_cache.inc';

if (class_exists('DrushMakeProject_Library')) {
  require_once dirname(__FILE__) . '/drush/at_require.inc';
}
else {
  function drush_at_require() {
    $msg = 'at_require depends on make feature of Drush, which introduced in 5.x';
    drush_print_r($msg);
  }
}

/**
 * Implements hook_drush_command()
 */
function at_base_drush_command() {
  if (function_exists('cache_get')) {
    return at_cache(array('id' => 'at:drush:commands'), 'at_base_fetch_commands');
  }
  return at_base_fetch_commands();
}

/**
 * Fetch drush commands from config file.
 */
function at_base_fetch_commands() {
  $el = at_container('expression_language');
  foreach (array('at_base' => 'at_base') + at_modules('at_base', 'drush') as $module) {
    foreach (at_config($module, 'drush')->get('commands') as $name => $def) {
      if (!empty($def['bootstrap'])) {
        $def['bootstrap'] = $el->evaluate($def['bootstrap']);
      }
      $cmd[$name] = $def;
    }
  }
  return $cmd;
}

/**
 * Implements drush_hook_pre_COMMAND()
 *
 * Make sure cached-config is flushed before other config data rebuilt.
 */
function drush_at_base_pre_cache_clear() {
  cache_clear_all('*', 'cache');
}
