<?php

require_once dirname(__FILE__) . '/at_base.module';
require_once dirname(__FILE__) . '/drush/at_fontello.inc';

/**
 * Implements hook_drush_command()
 */
function at_base_drush_command() {
  $cmds = array();

  $cmds['at_require'] = array(
    'callback' => 'drush_at_require',
    'description' => 'Help download all missing dependencies.',
    'arguments' => array(
      'module' => 'Download dependencies for specific module',
    ),
    'examples' => array(
      'drush at_require => Download dependencies for all modules',
      'drush at_require module_name => Download dependencies for specific module'
    ),
    'aliases' => array('at-require', 'atr'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
    'options' => array(
      'working-copy' => 'Preserves VCS directories, like .git, for projects downloaded using such methods.',
    )
  );

  $cmds['at_cache_clear'] = array(
    'callback' => 'drush_at_cache_clear',
    'description' => 'Flush cache by tags',
    'examples' => array(
      'drush atcc at-config' => 'Flush cached content tagged with at-config',
      'drush atcc node user' => 'Flush cached content tagged with node or user',
    ),
    'aliases' => array('cache:clear', 'atcc', 'at_cache:clear'),
  );

  $cmds['at_fontello'] = array(
    'callback' => 'drush_fontello',
    'description' => 'Build Fontello library.',
    'examples' => array(
      'drush fontello => Build Fontello library',
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
    'aliases' => array('atft'),
  );

  return $cmds;
}

/**
 * Implements drush_hook_pre_COMMAND()
 *
 * Make sure cached-config is flushed before other config data rebuilt.
 */
function drush_at_base_pre_cache_clear() {
  cache_clear_all('*', 'cache');
}

function drush_at_cache_clear() {
  $tags = func_get_args();
  at_cache_flush_by_tags($tags);
}

/**
 * Drush make is not inluded in version 4, which leading to fatal errors.
 */
if (class_exists('DrushMakeProject_Library') && class_exists('Drupal\at_base\Drush\Command\AtRequire\DependencyFetcher')) {
  require_once dirname(__FILE__) . '/drush/at_require.inc';
}
else {
  function drush_at_require() {
    $msg = 'at_require depends on make feature of Drush, which introduced in 5.x';
    drush_print_r($msg);
  }
}
