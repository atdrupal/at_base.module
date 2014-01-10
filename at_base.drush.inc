<?php

// At install/Update processes, we do not have cache_* funtions
// which break at-base functions.
if (function_exists('cache_get')) {

  require_once dirname(__FILE__) . '/at_base.module';
  require_once dirname(__FILE__) . '/drush/at_services.inc';
  require_once dirname(__FILE__) . '/drush/at_cache.inc';

  function _drush_at_check_yml_reader() {
    if (!function_exists('yaml_parse')) {
      if (!is_file(DRUPAL_ROOT . '/sites/all/libraries/spyc/Spyc.php')) {
        drush_at_require_spyc();
      }
    }
  }

  /**
   * Implements hook_drush_command()
   */
  function at_base_drush_command() {
    _drush_at_check_yml_reader();

    return at_cache(array('cache_id' => 'atDrushCommands'), function() {
      $modules = array('at_base' => 'at_base') + at_modules('at_base', 'drush');

      foreach ($modules as $module) {
        $config = at_config($module, 'drush')->get('commands');
        foreach ($config as $command => $definition) {
          if (!empty($definition['bootstrap'])) {
            $definition['bootstrap'] = at_container('expression_language')->evaluate($definition['bootstrap']);
          }
          $commands[$command] = $definition;
        }
      }

      return $commands;
    });
  }

  if (class_exists('DrushMakeProject_Library')) {
    require_once dirname(__FILE__) . '/drush/at_require.inc';
  }
  else {
    function drush_at_require() {
      drush_print_r('at_require depends on make feature of Drush, which introduced in 5.x');
    }

    function drush_at_require_spyc() {
      return drush_at_require();
    }
  }

  /**
   * Implements drush_hook_pre_COMMAND()
   *
   * Make sure cached-config is flushed before other config data rebuilt.
   */
  function drush_at_base_pre_cache_clear() {
    cache_clear_all('*', 'cache');
  }
}
