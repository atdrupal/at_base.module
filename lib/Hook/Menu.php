<?php
namespace Drupal\at_base\Hook;

use Drupal\at_base\Route\RouteToMenu;

class Menu {
  private $items;

  /**
   * Get all menu items.
   */
  public function getMenuItems() {
    $items = array();
    foreach (at_modules('at_base', 'routes') as $module) {
      $items += $this->import($module);
    }
    return $items;
  }

  private function import($module) {
    $items = array();

    $data = at_config($module, 'routes', $refresh = TRUE)->get('routes');
    foreach ($data as $route_name => $route_data) {
      if ($item = at_id(new RouteToMenu($module, $route_name, $route_data))->convert()) {
        $items[$route_name] = $item;
      }
    }

    return $items;
  }
}
