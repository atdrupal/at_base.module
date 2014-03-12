<?php

namespace Drupal\at_base\Drush\Command;

class FontEllo {

  public function execute() {
    $fontello_library_path = at_library('fontello', NULL, FALSE);
    $drupal_root = drush_get_context('DRUSH_DRUPAL_ROOT');
//    $site_root = drush_get_context('DRUSH_DRUPAL_SITE_ROOT');

    drush_shell_exec_interactive('cd ' . $drupal_root . '/' . $fontello_library_path. '&& cd .. && rm -rf fontello && git clone https://github.com/fontello/fontello.git');
    drush_shell_exec_interactive('cd ' . $drupal_root . '/' . $fontello_library_path . '&& git submodule init');
    drush_shell_exec_interactive('cd ' . $drupal_root . '/' . $fontello_library_path . '&& git submodule update');
  }
}
