<?php
/**
 * @file ./drush/at_reqruire.inc
 */

use \Drupal\at_base\Drush\Command\FontEllo as FontElloCommand;

/**
 * Callback for at_require command.
 */
function drush_fontello() {
  at_id(new FontElloCommand())->execute();
}
