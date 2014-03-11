<?php
namespace Drupal\at_ui\Controller\Reports;

class Routes {
  public function render() {
    $rows = array();
    foreach (at_modules('at_base', 'routes') as $module) {
      foreach (at_config($module, 'routes')->get('routes') as $path => $route) {
        $attached = isset($route['attached']) ? $route['attached'] : NULL;
        $blocks = isset($route['blocks']) ? $route['blocks'] : NULL;
        unset($route['attached'], $route['blocks']);

        $rows[] = array($module, l($path, $path), atdr($route), atdr($attached), atdr($blocks));
      }
    }

    return array('#theme' => 'table',
      '#header' => array(
        array('data' => 'Module', 'width' => '75px'),
        array('data' => 'Path', 'width' => '350px'),
        'Route', 'Attached', 'Blocks'),
      '#rows' => $rows,
      '#prefix' => '<style>table td { vertical-align: top !important; }</style>',
    );
  }
}
