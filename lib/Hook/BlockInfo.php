<?php
namespace Drupal\at_base\Hook;

class BlockInfo
{
  public function import()
  {
    $info = array();
    foreach (at_modules('at_base', 'blocks') as $module) {
      $info += $this->importResource($module);
    }
    return $info;
  }

  private function importResource($module)
  {
    $info = array();
    foreach (at_config($module, 'blocks')->get('blocks') as $k => $block) {
      $cache = DRUPAL_CACHE_PER_ROLE;
      if (!empty($block['cache'])) {
        $cache = at_container('expression_language')->evaluate($block['cache']);
      }

      $info["{$module}|{$k}"] = array(
        'info' => empty($block['info']) ? $k : $block['info'],
        'cache' => $cache,
      );
    }
    return $info;
  }
}
