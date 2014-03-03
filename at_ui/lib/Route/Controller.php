<?php

namespace Drupal\at_ui\Route;

use Drupal\at_base\Helper\Content_Render;

class Controller extends \Drupal\at_base\Route\Controller {
  /**
   * Dispatch the controller.
   *
   * @return array
   */
  public function execute() {
    $this->prepareCache();
    $this->prepareFunctionCallback();
    $this->prepareContextBlocks();
    return $this->render->render($this->route);
  }
}
