<?php
use Drupal\at_base\Container\Definition;

function drush_at_services($service_name = 'all') {
  $definitions = Definition::getAll();

  if ($service_name === 'all') {
    $service_names = array_keys($definitions);
    drush_print_r($service_names);
  }
  elseif (isset($definitions[$service_name])) {
    drush_print_r($definitions[$service_name]);
  }
  else {
    drush_set_error(DRUSH_APPLICATION_ERROR, 'Service not found: ' . $service_name);
  }
}
