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
}
