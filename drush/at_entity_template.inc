<?php

function drush_at_entity_template() {
  $args = func_get_args();
  if (empty($args)) {
    foreach (entity_get_info() as $entity_type => $entity_info) {
      drush_at_entity_template_report($entity_type);
    }
  }
  else {
    $entity_type = array_shift($args);
    drush_at_entity_template_report($entity_type);
  }
}

function drush_at_entity_template_report($entity_type) {
  $entity_info = entity_get_info($entity_type);

  if (!empty($entity_info['bundles']) && !empty($entity_info['view modes'])) {
    foreach ($entity_info['bundles'] as $bundle_type => $bundle_info) {
      drush_at_entity_template_report__details($entity_type, $entity_info, $bundle_type, $bundle_info);
    }
  }
}

function drush_at_entity_template_report__details($entity_type, $entity_info, $bundle_type, $bundle_info) {
  $rows = array();
  foreach ($entity_info['view modes'] as $view_mode_type => $view_mode_info) {
    $build = array(
      '#bundle' => $bundle_type,
      '#entity' => entity_create($entity_type, array()),
      '#view_mode' => $view_mode_type,
    );

    if ($config = at_id( new Drupal\at_base\Hook\Entity\View_Alter($build, $entity_type))->fetchConfig()) {
      $template = $blocks = $extra = 'N/A';

      // Template report
      if (isset($config['template'])) {
          $template = '';

          $template = is_string($config['template'])
            ? $config['template']
            : '- ' . implode("\n- ", $config['template']);
          unset($config['template']);
      }

      // Block report: theme.region.blocks
      if (isset($config['blocks'])) {
        $blocks = '';
        foreach ($config['blocks'] as $theme => $regions) {
          foreach ($regions as $region => $boxes) {
            $blocks .= "{$theme} -> {$region}:\n-------\n";
            $blocks .= print_r(reset($boxes), TRUE);
            $blocks .= "\n\n";
          }
        }
        unset($config['blocks']);
      }

      if (!empty($config)) {
        $extra = '';
        foreach ($config as $k => $v) {
          $extra .= "{$k}:\n---\n" . str_replace("\n", ' ', print_r($v, TRUE)) . "\n";
        }
      }

      $rows[] = array($view_mode_type, $template, $blocks, $extra);

      drush_at_entity_template_report__details__print($entity_info, $bundle_type, $rows);
    }
  }
}

function drush_at_entity_template_report__details__print($entity_info, $bundle_type, $rows) {
  if (!empty($rows)) {
    drush_print_r('----------------------------');
    drush_print_r(' Entity Type: ' . $entity_info['label'] . ' > ' . $bundle_type);
    drush_print_r('----------------------------' . "\n");

    drush_print_table(array(
      'header'   => array('View mode',     'Template'     , 'Blocks', 'Extra'),
      'seprator' => array('-------------', '-------------', '------', '-----'),
    ) + $rows, TRUE);
    drush_print_r("\n\n");
  }
}
