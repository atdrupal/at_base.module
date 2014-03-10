<?php
namespace Drupal\at_ui\Controller\Reports;

class Routes {
  public function render() {
    $rows = array();
    foreach (at_modules('at_base', 'routes') as $module) {
      foreach (at_config($module, 'routes')->get('routes') as $path => $route) {
        $rows[] = array($module, $path, atdr($route));
      }
    }

    return array('#theme' => 'table',
      '#header' => array(
        array('data' => 'Module', 'width' => '100px'),
        array('data' => 'Path', 'width' => '100px'),
        'Route'),
      '#rows' => $rows);
  }
}
