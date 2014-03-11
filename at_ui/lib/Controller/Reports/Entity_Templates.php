<?php
namespace Drupal\at_ui\Controller\Reports;

class Entity_Templates {
  public function render() {
    $rows = array();

    foreach (at_modules('at_base', 'entity_template') as $module) {
      foreach (at_config($module, 'entity_template')->get('entity_templates') as $entity_type => $entity_config) {
        foreach ($entity_config as $bundle => $bundle_config) {
          foreach ($bundle_config as $view_mode => $config) {
            $blocks = isset($config['blocks']) ? $config['blocks'] : NULL;
            $attached = isset($config['attached']) ? $config['attached'] : NULL;

            $rows[] = array($entity_type, $bundle, $view_mode, atdr($config), atdr($attached), atdr($blocks));
          }
        }
      }
    }

    return array('#theme' => 'table',
      '#header' => array(
        array('data' => 'Entity', 'width' => '100px'),
        array('data' => 'Bundle', 'width' => '100px'),
        array('data' => 'View Mode', 'width' => '100px'),
        'Config', 'Attached', 'Blocks'),
      '#rows' => $rows,
      '#empty' => 'Empty',
      '#prefix' => '<style>table td { vertical-align: top !important; }</style>',
    );
  }
}
