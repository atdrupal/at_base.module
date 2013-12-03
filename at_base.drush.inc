<?php

/**
 * @file ./drush/at_reqruire.inc
 */

use \Drupal\at_base\Drush\Command\AtRequire as AtRequireCommand;
use \Drupal\at_base\Drush\Command\AtRequireSpyc as AtRequireSpycCommand;
use \Drupal\at_base\Drush\Command\AtRequire\DependencyFetcher;

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
  $command = new AtRequireCommand($module);
  $command->execute();
}

/**
 * Implements drush_hook_COMMAND_pre_validate()
 */
function drush_at_require_pm_enable_pre_validate($module) {
  if (!is_file(DRUPAL_ROOT . '/sites/all/libraries/spyc/Spyc.php')) {
    if (!function_exists('yaml_parse')) {
      drush_at_require_spyc();
    }
  }

  if (module_exists($module)) {
    return;
  }

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
}
