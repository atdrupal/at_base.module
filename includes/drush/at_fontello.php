<?php

/**
 * @file ./drush/at_reqruire.php
 */
use Drupal\at_base\Drush\Command\FontEllo as FontElloCommand;

/**
 * Callback for at_fontello command.
 */
function drush_fontello()
{
    (new FontElloCommand())->execute();
}
