<?php

namespace Drupal\at_base\Cache;

/**
 * Warm cached data.
 *
 * Usage
 *
 *   $warmer = new CacheWarmer('user_login');
 *   $warmer->setEntity('user', $account);
 *   $warmer->warm();
 *
 */
class Warmer {
  private $entity;
  private $entity_type;
  private $event_name;
  private $config;

  public function __construct($event_name, $config = NULL) {
    if (!function_exists('at_config')) {
      throw new \Exception('Missing module: at_config');
    }

    $this->event_name = $event_name;

    if (!$config) {
      $config = new WarmerConfig($this->event_name);
    }

    $this->config = $config;
  }

  public function setEntity($entity_type, $entity) {
    if (!$info = entity_get_info($entity_type)) {
      throw new \Exception('Invalid entity type: ' . $entity_type);
    }

    $this->entity_type = $entity_type;
    $this->entity = $entity;
    $this->entity_bundle = !empty($info['entity keys']['bundle']) ? $entity->{$info['entity keys']['bundle']} : '';
    $this->entity_id     = $entity->{$info['entity keys']['id']};
  }

  /**
   * Wrapper function to warm cached-tags & views.
   */
  public function warm() {
    $cache_tags = $views_tags = array();

    $find = $this->getTagFind();
    $replace = $this->getTagReplace();

    foreach ($this->config->getConfigTags() as $tag) {
      if (strpos($tag, 'views:') === 0) {
        $views_tags[] = $tag;
      }
      else {
        $cache_tags[] = str_replace($find, $replace,  $tag);
      }
    }

    $this->warmTags($cache_tags);
    $this->warmViews($views_tags);
  }

  private function warmTags($tags) {
    at_cache_flush_by_tags($tags);
  }

  private function warmViews($tags) {
    views_include_handlers();
    module_load_include('inc', 'views', 'plugins/views_plugin_cache');
    foreach ($tags as $tag) {
      @list($module, $view_name, $display_id) = explode($tag);
      if ($view = views_get_view($name)) {
        $display_id = $display_id ? $display_id : 'default';
        $display = isset($view->display[$display_id]) ? $view->display[$display_id] : $view->display['default'];
        $cache = new views_plugin_cache($view, $display);
        $cache->cache_flush();
      }
    }
  }

  private function getTagFind() {
    $find = array();
    if ($this->entity) {
      $find[] = '%entity_type';
      $find[] = '%type';
      $find[] = '%entity_bundle';
      $find[] = '%bundle';
      $find[] = '%entity_id';
      $find[] = '%id';
    }
    return $find;
  }

  private function getTagReplace() {
    $replace = array();
    if ($this->entity) {
      $replace[] = $this->entity_type;
      $replace[] = $this->entity_type;
      $replace[] = $this->entity_bundle;
      $replace[] = $this->entity_bundle;
      $replace[] = $this->entity_id;
      $replace[] = $this->entity_id;
      $replace[] = $this->entity_id;
    }
    return $replace;
  }
}
