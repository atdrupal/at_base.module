<?php
namespace Drupal\at_base\Cache\Warming;

/**
 * Warm cached data.
 *
 * Usage
 *   at_id(new \Drupal\at_base\Cache\Warming($node, 'node', 'entity_insert'))
 *     ->warm();
 */
class Entity {
  private $entity;
  private $entity_type;
  private $event_name;

  public function __construct($entity, $entity_type, $event_name) {
    if (!function_exists('at_config')) {
      throw new \Exception('Missing module: at_config');
    }

    if (!$info = entity_get_info($entity_type)) {
      throw new \Exception('Invalid entity type: ' . $entity_type);
    }

    $this->entity        = $entity;
    $this->entity_type   = $entity_type;
    $this->event_name    = $event_name;
    $this->entity_bundle = !empty($info['entity keys']['bundle']) ? $entity->{$info['entity keys']['bundle']} : '';
    $this->entity_id     = $entity->{$info['entity keys']['id']};
  }

  public function warm() {
    $tags = array();

    $find[] = '%entity_type';
    $find[] = '%type';
    $find[] = '%entity_bundle';
    $find[] = '%bundle';
    $find[] = '%entity_id';
    $find[] = '%id';

    $replace[] = $this->entity_type;
    $replace[] = $this->entity_type;
    $replace[] = $this->entity_bundle;
    $replace[] = $this->entity_bundle;
    $replace[] = $this->entity_id;
    $replace[] = $this->entity_id;
    $replace[] = $this->entity_id;

    foreach ($this->getConfigTags() as $tag) {
      $tags[] = str_replace($find, $replace,  $tag);
    }

    at_cache_flush_by_tags($tags);
  }

  private function getModules() {
    foreach (at_modules('at_base') as $module) {
      $file = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . '/config/cache_warming.yml';
      if (is_file($file)) {
        $modules[] = $module;
      }
    }

    return !empty($modules) ? $modules : array();
  }

  private function getConfigTags() {
    foreach ($this->getModules() as $module) {
      if ($data = at_config($module, 'cache_warming')->get('tags')) {
        if (isset($data[$this->event_name])) {
          return $data[$this->event_name];
        }
      }
    }

    return array();
  }
}
