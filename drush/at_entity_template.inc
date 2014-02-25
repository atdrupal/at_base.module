<?php

function drush_at_entity_template() {
  $args = func_get_args();
  if (empty($args)) {
    return drush_at_entity_template_all();
  }
}

function drush_at_entity_template_all() {
  $rows = array();
  foreach (entity_get_info() as $entity_type => $entity_info) {
    foreach ($entity_info['bundles'] as $bundle_type => $bundle_info) {
      foreach ($entity_info['view modes'] as $view_mode_type => $view_mode_info) {
        $build = array(
          '#bundle' => $bundle_type,
          '#entity' => entity_create($entity_type, array()),
          '#view_mode' => $view_mode_type,
        );

        if ($config = at_id( new Drupal\at_base\Hook\Entity\View_Alter($build, $entity_type))->fetchConfig()) {
          $template = $blocks = $extra = 'N/A';
          foreach (array('template', 'blocks') as $k) {
            if (isset($config[$k])) {
              $$k = is_string($config[$k]) ? $config[$k] : '- ' . implode("\n- ", $config[$k]);
              unset($config[$k]);
            }
          }

          if (!empty($config)) {
            $extra = '';
            foreach ($config as $k => $v) {
              $extra .= "{$k}:\n---\n" . str_replace("\n", ' ', print_r($v, TRUE)) . "\n";
            }
          }

          $rows[] = array($entity_type, $bundle_type, $view_mode_type, $template, $blocks, $extra);
          $rows[] = array('-----------', '------', '-------------', '-------------', '------', '-----');
        }
      }
    }
  }

  drush_print_table(array(
    'header'   => array('Entity Type', 'Bundle', 'View mode',     'Template'     , 'Blocks', 'Extra'),
    'seprator' => array('===========', '======', '=============', '=============', '======', '====='),
  ) + $rows, TRUE);
}
