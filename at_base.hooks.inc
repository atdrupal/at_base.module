<?php
/**
 * @file at_base.hooks.inc
 *
 * Drupal hook implementations.
 */

use \Drupal\at_base\Route\Importer as RouteImporter;
use \Drupal\at_base\Hook\Menu as HookMenu;
use \Drupal\at_base\Hook\Page_Build as Hook_Page_Build;
use \Drupal\at_base\Hook\Entity\View_Alter as Hook_Entity_View_Alter;

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

###############################################################
# Cache warming
###############################################################

/**
 * Implements hook_entity_view()
 */
function at_base_entity_view($entity, $type, $view_mode, $langcode) {
  at_container('breadcrumb_api')->checkEntityConfig($entity, $type, $view_mode, $langcode);
}

if (defined('AT_BASE_ENTITY_TEMPLATE') && constant('AT_BASE_ENTITY_TEMPLATE')) {
  function at_base_entity_view_alter(&$build, $entity_type) {
    at_id(new Hook_Entity_View_Alter($build, $entity_type))->execute();
  }
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_update($entity, $type) {
  at_container('cache.warmer')
    ->setEventName('entity_update')
    ->setContext(array('entity_type' => $type, 'entity' => $entity))
    ->warm()
  ;
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_insert($entity, $type) {
  at_container('cache.warmer')
    ->setEventName('entity_insert')
    ->setContext(array('entity_type' => $type, 'entity' => $entity))
    ->warm()
  ;
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_delete($entity, $type) {
  at_container('cache.warmer')
    ->setEventName('entity_delete')
    ->setContext(array('entity_type' => $type, 'entity' => $entity))
    ->warm()
  ;
}

/**
 * Implements hook_user_login()
 */
function at_base_user_login(&$edit, $account) {
  at_container('cache.warmer')
    ->setEventName('user_login')
    ->setContext(array('entity_type' => 'user', 'entity' => $account))
    ->warm()
  ;
}

/**
 * Implements hook_user_login()
 */
function at_base_user_logout($account) {
  at_container('cache.warmer')
    ->setEventName('user_logout')
    ->setContext(array('entity_type' => 'user', 'entity' => $account))
    ->warm()
  ;
}

/**
 * Implements hook_page_build().
 *
 * Renders blocks into their regions.
 *
 * @see Drupal\at_base\Route\Controller::prepareContextBlocks()
 */
function at_base_page_build(&$page) {
  if (at_container('container')->offsetExists('page.blocks')) {
    at_id(new Hook_Page_Build($page, at_container('page.blocks')))->execute();
  }

  at_container('breadcrumb_api')->pageBuild();
}
