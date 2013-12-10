<?php
namespace Drupal\at_base\Hook;

/**
 * Details for at_base_flush_caches().
 */
class Flush_Cache {
  public function execute() {
    $this->flushAPCData();
    $this->flushTaggedCacheData();
    $this->refreshCachedModules();
    $this->refreshAutoloaderMapping();
    $this->fixModuleWeight();
  }

  private function flushTaggedCacheData() {
    db_delete('at_base_cache_tag')->execute();
  }

  private function flushAPCData() {
    if (function_exists('apc_clear_cache')) {
      apc_clear_cache('user');
    }
  }

  private function refreshCachedModules() {
    at_modules('at_base', TRUE);
  }

  private function refreshAutoloaderMapping() {
    \Drupal\at_base\Autoloader::rebuildMapping();
  }

  private function fixModuleWeight() {
    foreach (system_list('module_enabled') as $module_name => $project) {
      if (!empty($project->info['weight'])) {
        $weight = $project->info['weight'];
        if (is_numeric($weight)) {
          $sql = "UPDATE {system} SET weight = :weight WHERE name = :name";
          db_query($sql, array(':weight' => $weight, ':name' => $module_name));
        }
      }
    }
  }
}
