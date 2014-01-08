<?php
/**
 * @file at_base.hooks.inc
 *
 * Drupal hook implementations.
 */

use \Drupal\at_base\Cache\Warmer as CacheWarmer;
use \Drupal\at_base\Route\Importer as RouteImporter;
use \Drupal\at_base\Hook\Menu as HookMenu;

/**
 * Implements hook_menu()
 */
function at_base_menu() {
  require_once dirname(__FILE__) . '/lib/Hook/Menu.php';
  require_once dirname(__FILE__) . '/lib/Route/Importer.php';

  return at_id(new HookMenu($importer = new RouteImporter()))
          ->getMenuItems();
}

/**
 * Implements hook_flush_caches()
 *
 * @tag cache
 * @tag autoload
 */
function at_base_flush_caches() {
  at_id(new \Drupal\at_base\Hook\Flush_Cache())->execute();
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

  // Rebuild module weight
  at_id(new Drupal\at_base\Hook\Flush_Cache())->fixModuleWeight();
}

/**
 * Implements hook_block_info()
 */
function at_base_block_info() {
  return at_id(new \Drupal\at_base\Hook\BlockInfo())->import();
}

/**
 * Implements hook_block_view
 */
function at_base_block_view($delta) {
  return at_id(new \Drupal\at_base\Hook\BlockView($delta))->view();
}

/**
 * Implements hook_admin_paths()
 */
function at_base_admin_paths() {
  return array('at/twig' => TRUE);
}
