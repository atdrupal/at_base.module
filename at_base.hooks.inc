<?php

use Drupal\at_base\Cache\Warming\Entity as EntityWarming;

/**
 * Implements hook_flush_caches()
 */
function at_base_flush_caches() {
  db_delete('at_base_cache_tag')->execute();
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_update($entity, $type) {
  at_id(new EntityWarming($entity, $type, 'entity_update'))->warm();
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_insert($entity, $type) {
  at_id(new EntityWarming($entity, $type, 'entity_insert'))->warm();
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_delete($entity, $type) {
  at_id(new EntityWarming($entity, $type, 'entity_delete'))->warm();
}

/**
 * Implements hook_user_login()
 */
function at_base_user_login(&$edit, $account) {
  at_id(new EntityWarming($account, 'user', 'user_login'))->warm();
}

/**
 * Implements hook_user_login()
 */
function at_base_user_logout($account) {
  at_id(new EntityWarming($account, 'user', 'user_logout'))->warm();
}
