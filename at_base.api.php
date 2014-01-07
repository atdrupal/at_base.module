<?php

/**
 * Implements hook_at_base_services_alter()
 *
 * @see Drupal\at_base\Container\Definition::getAll()
 */
function hook_at_base_services_alter(&$serivces) {
  $serivces['helper.timer']['class'] = 'My_Class_Custom_Class';
}
