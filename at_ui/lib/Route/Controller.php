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
    // @todo Check permission
    if (!empty($_GET['edit'])) {
      return drupal_get_form('at_ui_route_form', $this->route);
    }

    $this->prepareCache();
    $this->prepareFunctionCallback();
    $this->prepareContextBlocks();
    return $this->render->render($this->route);
  }
}
