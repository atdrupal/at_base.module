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

/**
 * Improved version of user_access() function, accept multiple permissions
 * checking.
 *
 * at_user_access_or('administer content', 'edit any page node', $account);
 * 
 * @return bool
 */
function at_user_access_or() {
  global $user;

  $perms = func_get_args();

  // Last argument maybe account object
  $last_arg = array_pop($perms);
  if (is_object($last_arg) && isset($last_arg->uid)) {
    $account = $last_arg;
  }
  else {
    $account = $user;
    $perms[] = $last_arg;
  }

  foreach ($perms as $perm) {
    if (user_access($perm, $account)) {
      return TRUE;
    }
  }

  return FALSE;
}

/**
 * Improved version of user_access() function, accept multiple permissions
 * checking.
 *
 * at_user_access_and('view page node', 'edit any page node', $account);
 * 
 * @return bool
 */
function at_user_access_and() {
  global $user;

  $perms = func_get_args();

  // Last argument maybe account object
  $last_arg = array_pop($perms);
  if (is_object($last_arg) && isset($last_arg->uid)) {
    $account = $last_arg;
  }
  else {
    $account = $user;
    $perms[] = $last_arg;
  }

  foreach ($perms as $perm) {
    if (!user_access($perm, $account)) {
      return FALSE;
    }
  }

  return TRUE;
}

if (!function_exists('entity_bundle')) {
  /**
   * Get bundle value of an entity.
   *
   * @param  string   $entity_type
   * @param  stdClass $entity
   * @return string
   */
  function entity_bundle($entity_type, $entity) {
    $info = entity_get_info($entity_type);
    if (!empty($info['entity keys']['bundle'])) {
      $key = $info['entity keys']['bundle'];
      return isset($entity->$key) ? $entity->$key : NULL;
    }
  }
}
