<?php
namespace Drupal\at_base\Container;

class Definition {
  private $service_name;

  public function __construct($service_name) {
    $this->service_name = $service_name;
  }

  public function get() {
    $service_name = $this->service_name;
    $options = array('ttl' => '+ 1 year', 'cache_id' => "at_base:services:{$service_name}");

    return at_cache($options, function() use ($service_name) {
      $services = Definition::getAll();
      return isset($services[$service_name]) ? $services[$service_name] : FALSE;
    });
  }

  public static function getAll() {
    $options = array('ttl' => '+ 1 year', 'cache_id' => 'at_base:services');
    return at_cache($options, function() {
      $services = array();
      foreach (array('at_base' => 'at_base') + at_modules('at_base', 'services') as $module_name) {
        $services += at_config($module_name, 'services')->get('services');
      }
      return $services;
    });
  }
}
