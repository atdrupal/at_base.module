<?php

namespace Drupal\at_base\Route;

use \Drupal\at_base\Helper\ConstantParser;

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
    if (!empty($this->menu_item['context'])) $this->menu_item['context'] = at_id(new ConstantParser($this->menu_item['context']))->parse();
    if (!empty($this->menu_item['type']))    $this->menu_item['type']    = at_id(new ConstantParser($this->menu_item['type']))->parse();

    // Prepare magic properties
    if (!empty($this->menu_item['controller']))      $this->_prepareController();
    if (!empty($this->menu_item['template']))        $this->_prepareTemplateFile();
    if (!empty($this->menu_item['template_string'])) $this->_prepareTemplateString();

    return $this->menu_item;
  }

  private function _prepareController() {
    $this->menu_item['page callback'] = '\Drupal\at_base\Controller\DefaultController::controllerAction';
    $this->menu_item['page arguments'] = $this->menu_item['controller'];
  }

  private function _prepareTemplateFile() {
    $this->menu_item['page callback'] = '\Drupal\at_base\Controller\DefaultController::templateFileAction';
    $this->menu_item['page arguments'] = array(
      'template'  => $this->menu_item['template'],
      'variables' => !empty($this->menu_item['variables']) ? $this->menu_item['variables'] : array(),
      'attached'  => !empty($this->menu_item['attached']) ? $this->menu_item['attached'] : array(),
    );
  }

  private function _prepareTemplateString() {
    $this->menu_item['page callback'] = '\Drupal\at_base\Controller\DefaultController::templateStringAction';
    $this->menu_item['page arguments'] = array(
      $this->menu_item['pattern'],
      $this->menu_item['template_string'],
      !empty($this->menu_item['variables']) ? $this->menu_item['variables'] : array(),
      !empty($this->menu_item['attached']) ? $this->menu_item['attached'] : array(),
    );
  }
}
