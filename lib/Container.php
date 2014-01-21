<?php
namespace Drupal\at_base;

use Drupal\at_base\Container\Service_Resolver;
use Drupal\at_base\Helper\Config_Fetcher;
use Drupal\at_base\Helper\Wrapper\Database as DB_Wrapper;
use Drupal\at_base\Helper\Wrapper\Cache as Cache_Wrapper;
use Drupal\at_base\Config\Resolver as ConfigResolver;
use Drupal\at_base\Config\Config;

/**
 * Service Container/Locator.
 *
 * @see  https://github.com/andytruong/at_base/wiki/7.x-2.x-service-container
 */
class Container {
  protected static $c;

  public function __construct() {
    if (!self::$c) {
      require_once at_library('pimple') . '/lib/Pimple.php';

      self::$c = new \Pimple(array(
        'container' => $this,
        'service.resolver' => function() { return new Service_Resolver(); },
        'helper.config_fetcher' => function() { return new Config_Fetcher(); },
        'wrapper.db' => function() { return new DB_Wrapper(); },
        'wrapper.cache' => function() { return new Cache_Wrapper(); },
        'config' => function() { return new Config(new ConfigResolver()); },
      ));
    }
  }

  /**
   * Get a service by name.
   *
   * @param string $name
   */
  public function get($name) {
    if (empty(self::$c[$name])) {
      $this->set($name);
    }

    return self::$c[$name];
  }

  /**
   * Find services by tag
   *
   * @param  string  $tag
   * @return array
   */
  public function find($tag, $return = 'service_name') {
    $defs = self::$c['service.resolver']->findDefinitions($tag);

    if ($return === 'service_name') {
      return $defs;
    }
    elseif ($return === 'service') {
      foreach ($defs as $k => $name) {
        unset($defs[$k]);
        $defs[$name] = $this->get($name);
      }
    }

    return $defs;
  }

  /**
   * Main method for configure service in Pimple.
   *
   * @param string $name
   */
  public function set($name) {
    if (!isset(self::$c[$name])) {
      $this->register($name, self::$c['service.resolver']->getCallback($name));
    }
  }

  /**
   * Unregister the service.
   *
   * @param string $name
   * @param callable $callback
   */
  public function register($name, $callback) {
    if (!isset(self::$c[$name])) {
      self::$c[$name] = $callback;
    }
    else {
      $this->unregister($name);
      self::$c[$name] = $callback;
    }
  }

  /**
   * Unregister the service.
   *
   * @param string $name
   */
  public function unregister($name) {
    unset(self::$c[$name]);
  }
}
