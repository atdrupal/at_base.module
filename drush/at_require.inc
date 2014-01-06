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
  protected function generatePath() {
   return parent::generatePath($base = FALSE);
  }
}

/**
 * Callback for at_require command.
 */
function drush_at_require($module = 'all') {
  _drush_at_check_yml_reader();

  at_id(new AtRequireCommand($module))->execute();
}

/**
 * Implements drush_hook_COMMAND_pre_validate()
 */
function drush_at_require_pm_enable_pre_validate($module) {
  _drush_at_check_yml_reader();

  // Module was already processed, no need redo
  if (module_exists($module)) return;

  drush_at_require($module);
}

function drush_at_require_spyc() {
  $name = 'spyc';
  $info = array(
    'type' => 'library',
    'download' => array(
      'type' => 'git',
      'url' => 'https://github.com/mustangostang/spyc.git',
      'branch' => '0.5.1',
    ),
  );

  at_id(new DependencyFetcher($name, $info))->fetch();


  foreach (at_config('at_base', 'require')->get('projects') as $name => $info) {
    try {
      at_library($name);
    }
    catch (\Exception $e) {
      at_id(new DependencyFetcher($name, $info))->fetch();
    }
  }
}
