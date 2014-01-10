<?php
namespace Drupal\at_base\Container;

class Service_Resolver {
  public function getCallback($service_name) {
    return $this->convertDefinitionToCallback(
      $this->getDefinition($service_name)
    );
  }

  /**
   * Get service definition in configuration files.
   */
  private function getDefinition($service_name) {
    $def = at_container('helper.config_fetcher')->getItem('at_base', 'services', 'services', $service_name, TRUE);
    if (is_null($def)) {
      throw new \Exception("Missing service: {$service_name}");
    }
    return $this->resolve($def);
  }

  private function resolve($def) {
    // A service depends on others, this method to resolve them.
    foreach (array('arguments', 'calls') as $k) {
      if (!empty($def[$k])) {
        $this->resolveDependencies($def[$k]);
      }
    }

    // Service has factory
    if (!empty($def['factory_service'])) {
      at_container('container')->set($def['factory_service']);
    }

    return $def;
  }

  /**
   * Resolve array of dependencies.
   *
   * @see resolveDefinition()
   */
  private function resolveDependencies($array) {
    foreach ($array as $item) {
      if (is_array($item))             $this->resolveDependencies($item);
      if (!is_string($item))           continue;
      if ('@' !== substr($item, 0, 1)) continue;

      $service_name = substr($item, 1);
      at_container('container')->set($service_name);
    }
  }

  private function convertDefinitionToCallback($def) {
    return function($c) use ($def) {
      $def['arguments'] = !empty($def['arguments']) ? $def['arguments'] : array();

      // Make arguments are objects.
      foreach (array_keys($def['arguments']) as $k) {
        if ('@' === substr($def['arguments'][$k], 0, 1)) {
          $a_service_name = substr($def['arguments'][$k], 1);
          $def['arguments'][$k] = $c[$a_service_name];
        }
      }

      if (!empty($def['factory_service'])) {
        $f = $c[$def['factory_service']];
        return call_user_func_array(
          array($f, $def['factory_method']),
          $def['arguments']
        );
      }

      if (!empty($def['factory_class'])) {
        $f = new $def['factory_class'];
        return call_user_func_array(
          array($f, $def['factory_method']),
          $def['arguments']
        );
      }

      $class = new \ReflectionClass($def['class']);
      return $class->newInstanceArgs($def['arguments']);
    };
  }
}
