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
