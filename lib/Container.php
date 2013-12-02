<?php
namespace Drupal\at_base;

use Drupal\at_base\Container\Definition;

/**
 * @todo support tags
 * @todo support calls
 *
 * @see at_container()
 */
class Container {
  private static $container;

  public function __construct() {
    if (!$container) {
      require_once DRUPAL_ROOT . '/sites/all/libraries/pimple/lib/Pimple.php';
      $this->container = new \Pimple();
    }
  }

  /**
   * Get a service by name.
   *
   * @param string $service_name
   */
  public function get($service_name) {
    if (empty($this->container[$service_name])) {
      $this->set($service_name);
    }

    return $this->container[$service_name];
  }

  /**
   * Main method for configure service in Pimple.
   *
   * @param string $service_name
   */
  private function set($service_name) {
    // Get definition
    if (!$definition = at_id(new Definition($service_name))->get()) {
      throw new \Exception("Missing service: {$service_name}");
    }

    // Resolve dependencies
    $this->resolveDefinition($definition);

    // Config Pimple
    $this->container[$service_name] = function($container) use ($definition) {
      $definition['arguments'] = !empty($definition['arguments']) ? $definition['arguments'] : array();

      // Make arguments are objects.
      foreach (array_keys($definition['arguments']) as $k) {
        if ('@' === substr($definition['arguments'][$k], 0, 1)) {
          $a_service_name = substr($definition['arguments'][$k], 1);
          $definition['arguments'][$k] = $container[$a_service_name];
        }
      }

      if (!empty($definition['factory_service'])) {
        $f = $container[$definition['factory_service']];
        return call_user_func_array(
          array($f, $definition['factory_method']),
          $definition['arguments']
        );
      }

      $class = new \ReflectionClass($definition['class']);
      return $class->newInstanceArgs($definition['arguments']);
    };
  }

  /**
   * A service depends on others, this method to resolve them.
   */
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

  /**
   * Resolve array of dependencies.
   *
   * @see resolveDefinition()
   */
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
