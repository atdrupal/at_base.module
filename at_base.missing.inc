<?php
/**
 * @file at_base.missing.inc
 *
 * Provide missing API
 */

/**
 * Implements hook_node_insert()
 */
function at_base_node_insert($node) {
  module_invoke_all('node_save', $node);
}

/**
 * Implements hook_node_update()
 */
function at_base_node_update($node) {
  module_invoke_all('node_save', $node);
}

if (!function_exists('entity_bundle')) {
  function entity_bundle($entity_type, $entity) {
    $info = entity_get_info($entity_type);
    if (!empty($info['entity keys']['bundle'])) {
      $key = $info['entity keys']['bundle'];
      return isset($entity->$key) ? $entity->$key : NULL;
    }
  }
}
