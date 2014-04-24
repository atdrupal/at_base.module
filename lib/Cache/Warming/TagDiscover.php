<?php
namespace Drupal\at_base\Cache\Warming;

class TagDiscover {
  private $event_name;

  public function setEventName($event_name) {
    $this->event_name = $event_name;
  }

  public function tags() {
    foreach (at_modules('at_base', 'cache_warming') as $module) {
      if ($data = at_config($module, 'cache_warming')->get('tags')) {
        if (isset($data[$this->event_name])) {
          return $data[$this->event_name];
        }
      }
    }

    return array();
  }
}
