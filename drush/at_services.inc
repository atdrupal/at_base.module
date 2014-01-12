<?php

/**
 * Callback for 'at_service' drush command.
 *
 * @param  string $service_name
 */
function drush_at_services($service_name = 'all') {
  $config_fetcher = at_container('helper.config_fetcher');

  if ($service_name === 'all') {
    $defs = $config_fetcher->getItems('at_base', 'services', 'services', TRUE);
    drush_print_r($defs);
  }
  elseif (!empty($service_name)) {
    $def = $config_fetcher->getItem('at_base', 'services', 'services', $service_name, TRUE);
    drush_print_r($def);
  }
  else {
    drush_set_error(DRUSH_APPLICATION_ERROR, 'Service not found: ' . $service_name);
  }
}
