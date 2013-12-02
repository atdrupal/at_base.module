<?php

use Drupal\at_base\Cache\Warmer as CacheWarmer;

/**
 * Implements hook_flush_caches()
 *
 * @tag cache
 * @tag autoload
 */
function at_base_flush_caches() {
  // Flush tagged cache
  db_delete('at_base_cache_tag')->execute();

  // Refresh the cached-modules
  at_modules('at_base', TRUE);

  // Rebuild the class mapping
  \Drupal\at_base\Autoloader::rebuildMapping();

  // Flush file-path caching
  if (function_exists('apc_clear_cache')) {
    apc_clear_cache('user');
  }
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_update($entity, $type) {
  $warmer = new CacheWarmer('entity_update');
  $warmer->setEntity($type, $entity);
  $warmer->warm();
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_insert($entity, $type) {
  $warmer = new CacheWarmer('entity_insert');
  $warmer->setEntity($type, $entity);
  $warmer->warm();
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_delete($entity, $type) {
  $warmer = new CacheWarmer('entity_delete');
  $warmer->setEntity($type, $entity);
  $warmer->warm();
}

/**
 * Implements hook_user_login()
 */
function at_base_user_login(&$edit, $account) {
  $warmer = new CacheWarmer('user_login');
  $warmer->setEntity('user', $account);
  $warmer->warm();
}

/**
 * Implements hook_user_login()
 */
function at_base_user_logout($account) {
  $warmer = new CacheWarmer('user_logout');
  $warmer->setEntity('user', $account);
  $warmer->warm();
}

/**
 * Implements hook_modules_enabled()
 *
 * @tag autoload
 */
function at_base_modules_enabled($modules) {
  // Refresh the cached-modules
  at_modules('at_base', TRUE);
  \Drupal\at_base\Autoloader::rebuildMapping();
}
