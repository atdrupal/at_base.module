<?php
namespace Drupal\at_base;

use Drupal\at_base\At_Container\Definition;

/**
 * @todo support tags
 */
class At_Container {
  private static $container;

  public function __construct() {
    if (!$container) {
      require_once DRUPAL_ROOT . '/sites/all/libraries/pimple/lib/Pimple.php';
      $this->container = new \Pimple();
    }
  }

  public function get($service_name) {
    if (empty($this->container[$service_name])) {
      $this->set($service_name);
    }

    return $this->container[$service_name];
  }

  private function set($service_name) {
    // Get definition
    $definition = at_id(new Definition())->get('$service_name');

    // Resolve dependencies
    $this->resolveDefinition();

    // Config Pimple
    $this->container[$service_name] = function($container) use ($definition) {
      if (!empty($definition['factory_service'])) {
        $f = $container[$definition['factory_service']];
        return call_user_func_array(
          array($f, $definition['factory_method'])
          $definition['arguments'] ? $definition['arguments'] : array()
        );
      }

      $class = new ReflectionClass($definition['class']);
      return $class->newInstanceArgs($definition['arguments']);
    };
  }

  private function resolveDefinition($definition) {
    if (!empty($definition['arguments'])) {
      $this->resolveDependencies($definition['arguments']);
    }

    if (!empty($definition['calls'])) {
      $this->resolveDependencies($definition['calls']);
    }

    if (!empty($definition['factory_service'])) {
      $this->set($definition['factory_service']);
    }
  }

  private function resolveDependencies($array) {
    foreach ($array as $item) {
      if (is_array($item)) $this->resolveDependencies($item);
      if (!is_string($item)) continue;
      if ('@' !== substr($item, 0, 1)) continue;

      $service_name = substr($item, 1);
      $this->set($service_name);
    }
  }
}
