<?php
namespace Drupal\at_base;

use Drupal\at_base\Container\Definition;
use Drupal\at_base\Container\Service_Resolver;
use Drupal\at_base\Helper\Config_Fetcher;

/**
 * Service Container/Locator.
 *
 * @todo support tags
 * @todo support calls
 */
class Container {
  private static $c;

  public function __construct() {
    if (!self::$c) {
      require_once at_library('pimple') . '/lib/Pimple.php';

      self::$c = new \Pimple(array(
        'container' => $this,
        'service.resolver' => function() { return new Service_Resolver(); },
        'helper.config_fetcher' => function() { return new Config_Fetcher(); },
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
   * @todo   Document me.
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
    if (empty(self::$c[$name])) {
      self::$c[$name] = self::$c['service.resolver']->getCallback($name);
    }
  }
}
