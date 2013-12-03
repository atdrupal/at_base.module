<?php
namespace Drupal\at_base\Hook;

class BlockInfo {
  public function import() {
    $info = array();
    foreach (at_modules('at_base', 'blocks') as $module) {
      $info += $this->importResource($module);
    }
    return $info;
  }

  private function importResource($module) {
    $info = array();
    foreach (at_config($module, 'blocks')->get('blocks') as $k => $block) {
      $info["{$module}___{$k}"] = array(
        'info' => !empty($block['info']) ? $block['info'] : $k,
        'cache' => !empty($block['cache']) ? constant($block['cache']) : DRUPAL_CACHE_PER_ROLE,
      );
    }
    return $info;
  }
}
