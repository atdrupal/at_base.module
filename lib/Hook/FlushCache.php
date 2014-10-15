<?php

namespace Drupal\at_base\Hook;

/**
 * Details for at_base_flush_caches().
 */
class FlushCache {

  public function execute() {
    $this->flushAPCData();
    $this->flushTaggedCacheData();
    $this->refreshCachedModules();
    $this->fixModuleWeight();
  }

  private function flushTaggedCacheData() {
    at_container('wrapper.db')->delete('at_base_cache_tag')->execute();
  }

  private function flushAPCData() {
    if (function_exists('apc_clear_cache')) {
      apc_clear_cache('user');
    }
  }

  private function refreshCachedModules() {
    at_modules('at_base', TRUE);
  }

  /**
   * Update module's weight value in system table.
   */
  public function fixModuleWeight() {
    foreach (system_list('module_enabled') as $module_name => $module_info) {
      if (isset($module_info->info['weight'])) {
        $this->resolveModuleWeight($module_name, $module_info->info['weight']);
      }
    }
  }

  public function resolveModuleWeight($module_name, $weight) {
    if (is_numeric($weight)) {
      at_container('wrapper.db')
        ->update('system')
        ->condition('name', $module_name)
        ->fields(array('weight' => $weight))
        ->execute()
      ;
    }
  }

}
