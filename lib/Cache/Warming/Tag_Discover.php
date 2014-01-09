<?php
namespace Drupal\at_base\Cache\Warmer;

class WarmerConfig {
  private $event_name;

  public function __construct($event_name) {
    $this->event_name = $event_name;
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

  public function getConfigTags() {
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
