<?php
namespace Drupal\at_base\Helper;

/**
 * Usage:
 *
 *  atcg('helper.config_fetcher')
 *    ->getItems('at_base', 'services', 'services', TRUE)
 *  ;
 *
 * @todo  Test me
 * @todo  Remove duplication code — at_modules('at_base', …)
 * @todo  Support expression_language:evaluate() — check \Drupal\at_base\Hook\BlockInfo
 */
class ConfigFetcher {
  public function getItems($module, $id, $key, $include_base = FALSE, $reset = FALSE) {
    $o = array(
      'ttl' => '+ 1 year',
      'id' => "ATConfig:{$module}:{$id}:{$key}:" . ($include_base ? 1 : 0),
      'reset' => $reset,
    );
    return at_cache($o, array($this, 'fetchItems'), array($module, $id, $key, $include_base));
  }

  public function fetchItems($module, $id, $key, $include_base) {
    $modules = at_modules($module, $id);

    if ($include_base) {
      $modules = array_merge(array($module), $modules);
    }

    $items = array();
    foreach ($modules as $module_name) {
      $items = array_merge($items, at_config($module_name, $id)->get($key));
    }

    return $items;
  }

  public function getItem($module, $id, $key, $item_key, $include_base = FALSE, $reset = FALSE) {
    $o = array(
      'ttl' => '+ 1 year',
      'id' => "ATConfig:{$module}:{$id}:{$key}:{$item_key}:" . ($include_base ? 1 : 0),
      'reset' => $reset,
    );

    return at_cache($o, array($this, 'fetchItem'), array($module, $id, $key, $item_key, $include_base));
  }

  public function fetchItem($module, $id, $key, $item_key, $include_base) {
    if ($items = $this->getItems($module, $id, $key, $include_base)) {
      if (!empty($items[$item_key])) {
        return $items[$item_key];
      }
    }
  }
}
