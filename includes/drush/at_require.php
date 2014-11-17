<?php

/**
 * @file ./drush/at_reqruire.php
 */
use Drupal\at_base\Drush\Command\AtRequire as AtRequireCommand;

class DrushMakeProject_AtRequire_Library extends \DrushMakeProject_Library
{

    /**
     * Override default value of parent.
     */
    protected function generatePath($base = TRUE)
    {
        return parent::generatePath($base = FALSE);
    }

}

/**
 * Implements drush_hook_COMMAND_pre_validate()
 */
function drush_at_require_pm_enable_pre_validate($module)
{
    // Module was already processed, no need redo
    if (!module_exists($module)) {
        drush_at_require($module);
    }
}

/**
 * Callback for at_require command.
 */
function drush_at_require($module = 'all')
{
    (new AtRequireCommand($module))->execute();
}
