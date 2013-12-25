<?php

namespace Drupal\at_base\Route;

class RouteToMenu {
  /**
   * @var string
   */
  private $module;

  /**
   * @var string
   */
  private $route_name;

  /**
   * @var array
   */
  private $route_data;

  /**
   * @var array
   */
  private $menu_item = array();

  public function __construct($module, $route_name, $route_data) {
    $this->module     = $module;
    $this->route_name = $route_name;
    $this->route_data = $route_data;
  }

  /**
   * We can not define constants in yaml, this method is to convert them.
   */
  public function convert() {
    $this->menu_item = $this->route_data + array(
      'pattern' => $this->route_name,
      'file path' => drupal_get_path('module', $this->module)
    );

    // Parse constants
    if (!empty($this->menu_item['context'])) $this->menu_item['context'] = at_container('expression_language')->evaluate($this->menu_item['context']);
    if (!empty($this->menu_item['type']))    $this->menu_item['type']    = at_container('expression_language')->evaluate($this->menu_item['type']);

    // Prepare magic properties
    $need_wrapper = FALSE;
    $need_wrapper = $need_wrapper || !empty($this->menu_item['controller']);
    $need_wrapper = $need_wrapper || !empty($this->menu_item['template']);
    $need_wrapper = $need_wrapper || !empty($this->menu_item['template_string']);
    $need_wrapper = $need_wrapper || !empty($this->menu_item['form']);
    if ($need_wrapper) {
      $this->menu_item['page arguments'][] = $this->menu_item;
      $this->menu_item['page callback'] = 'at_route';
    }

    return $this->menu_item;
  }
}
