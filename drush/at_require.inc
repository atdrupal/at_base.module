<?php
/**
 * @file ./drush/at_reqruire.inc
 */

use \Drupal\at_base\Drush\Command\AtRequire as AtRequireCommand;
use \Drupal\at_base\Drush\Command\AtRequireSpyc as AtRequireSpycCommand;
use \Drupal\at_base\Drush\Command\AtRequire\DependencyFetcher;

class DrushMakeProject_AtRequire_Library extends \DrushMakeProject_Library {
  /**
   * Override default value of parent.
   */
  protected function generatePath($base = TRUE) {
    return parent::generatePath($base = FALSE);
  }
}

/**
 * Callback for at_require command.
 */
function drush_at_require($module = 'all') {
  at_id(new AtRequireCommand($module))->execute();
}

/**
 * Implements drush_hook_COMMAND_pre_validate()
 */
function drush_at_require_pm_enable_pre_validate($module) {
  _drush_at_check_yml_reader();

  // Module was already processed, no need redo
  if (!module_exists($module)) {
    drush_at_require($module);
  }
}

# ---------------------
# Hack to download required depedencies
# ---------------------

// Required projects must be available
if (function_exists('drupal_get_path')) {
  $projects = drupal_get_path('module', 'at_base') . '/config/require.yml';
  $projects = yaml_parse($projects);
  unset($projects['spyc']);

  // Backup global working copy option.
  $working_copy = drush_get_option('working-copy');
  foreach ($projects['projects'] as $name => $info) {
    try {
      at_library($name);
    }
    catch (\Exception $e) {
      if (empty($working_copy) && !empty($info['options']['working-copy'])) {
        // Apply working copy option for each library.
        drush_set_option('working-copy', TRUE);
      }

      at_id(new DependencyFetcher($name, $info))->fetch();

      // Restore global working copy option.
      if (empty($working_copy) && !empty($info['options']['working-copy'])) {
        drush_set_option('working-copy', $working_copy);
      }
    }
  }
}
