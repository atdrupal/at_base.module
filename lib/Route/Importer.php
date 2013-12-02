<?php
namespace Drupal\at_base\Route;

class Importer {
  /**
   * @var string
   */
  private $module;

  /**
   * @var string
   */
  private $path;

  public function setModule($module) {
    $this->module = $module;
    return $this;
  }

  public function import() {
    $data = at_config($this->module, 'routes')->get('routes');

    foreach ($data as $route_name => $route_data) {
      if ($item = at_id(new RouteToMenu($this->module, $route_name, $route_data))->convert()) {
        $items[$route_name] = $item;
      }
    }

    return !empty($items) ? $items : array();
  }
}
