<?php

namespace Drupal\at_base\Drush\Command;

use \Drupal\at_base\Drush\Command\AtRequire\DependenciesFetcher;

class AtRequire {
  private $module;

  public function __construct($module = 'all') {
    $this->module = $module;
  }

  /**
   * Get supported modules.
   */
  private function getModules() {
    if ($this->module === 'all') {
      return at_modules('at_base', 'require');
    }

    return array($this->module);
  }

  public function execute() {
    foreach ($this->getModules() as $module) {
      at_id(new DependenciesFetcher($module))->fetch();
    }
  }
}
