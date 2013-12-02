<?php
namespace Drupal\at_base\At_Container;

class Definition {
  public function getDefinition($service_name) {
    $options = array('ttl' => '+ 1 year', 'cache_id' => "at_base:services:{$service_name}");
    return at_cache($options, function() use ($service_name) {
      $services = Drupal\at_base\At_Container::getDefinitions();
      return $services[$service_name];
    });
  }

  public function getDefinitions() {
    $options = array('ttl' => '+ 1 year', 'cache_id' => 'at_base:services');
    return at_cache($options, function() {
      $services = array();
      foreach (at_modules('at_base', 'services') as $module_name) {
        $services += at_config($module_name, 'services')->get('services');
      }
      return $services;
    });
  }
}
