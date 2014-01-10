<?php
namespace Drupal\at_base;

use Drupal\at_base\Container\Definition;
use Drupal\at_base\Helper\Config_Fetcher;

/**
 * @todo support tags
 * @todo support calls
 */
class Container {
  private static $container;

  public function __construct() {
    if (!self::$container) {
      require_once at_library('pimple') . '/lib/Pimple.php';

      self::$container = new \Pimple(array(
        'helper.config_fetcher' => function() { return new Config_Fetcher(); },
      ));
    }
  }

  /**
   * Get a service by name.
   *
   * @param string $service_name
   */
  public function get($service_name) {
    if (empty(self::$container[$service_name])) {
      $this->set($service_name);
    }

    return self::$container[$service_name];
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

    // The service maybe defined in self::resolveDefinition()
    if (isset(self::$container[$service_name])) {
      return;
    }

    // Config Pimple
    self::$container[$service_name] = function($container) use ($definition) {
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

      if (!empty($definition['factory_class'])) {
        $f = new $definition['factory_class'];
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
