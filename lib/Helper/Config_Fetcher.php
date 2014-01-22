<?php
namespace Drupal\at_base\Helper;

/**
 * Usage:
 *
 *  at_container('helper.config_fetcher')
 *    ->getItems('at_base', 'services', 'services', TRUE)
 *  ;
 *
 * @todo  Test me
 * @todo  Remove duplication code — at_modules('at_base', …)
 * @todo  Support expression_language:evaluate() — check \Drupal\at_base\Hook\BlockInfo
 */
class Config_Fetcher {
  public function getItems($module, $id, $key, $include_at_base = FALSE, $reset = FALSE) {
    $o = array(
      'ttl' => '+ 1 year',
      'id' => "ATConfig:{$module}:{$id}:{$key}:" . ($include_at_base ? 1 : 0),
      'reset' => $reset,
    );

    return at_cache($o, function() use ($module, $id, $key, $include_at_base) {
      $modules = at_modules($module, $id);

      if ($include_at_base) {
        $modules = array_merge(array('at_base'), $modules);
      }

      $items = array();
      foreach ($modules as $module_name) {
        $items = array_merge($items, at_config($module_name, $id)->get($key));
      }

      return $items;
    });
  }

  public function getItem($module, $id, $key, $item_key, $include_at_base = FALSE, $reset = FALSE) {
    $o = array(
      'ttl' => '+ 1 year',
      'id' => "ATConfig:{$module}:{$id}:{$key}:{$item_key}:" . ($include_at_base ? 1 : 0),
      'reset' => $reset,
    );

    return at_cache($o, function() use ($module, $id, $key, $item_key, $include_at_base) {
      $items = at_container('helper.config_fetcher')->getItems($module, $id, $key, $include_at_base);
      if (isset($items[$item_key])) {
        return $items[$item_key];
      }
    });
  }
}
