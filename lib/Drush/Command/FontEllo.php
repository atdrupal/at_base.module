<?php

namespace Drupal\at_base\Drush\Command;

class FontEllo {

  public function execute() {
    $fontello_library_path = at_library('fontello', NULL, FALSE);
    $drupal_root = drush_get_context('DRUSH_DRUPAL_ROOT');

    $output = drush_shell_cd_and_exec($drupal_root . '/', 'git status');
    if (empty($output)) {
      throw new \Exception(printf("%s is not a git repo\n", $drupal_root . '/' . $fontello_library_path));
    }

    drush_shell_exec_interactive('cd ' . $drupal_root . '/' . $fontello_library_path . '&& git submodule init');
    drush_shell_exec_interactive('cd ' . $drupal_root . '/' . $fontello_library_path . '&& git submodule update');
  }
}
