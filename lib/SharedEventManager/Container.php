<?php
namespace Drupal\at_base\SharedEventManager;

use Drupal\at_base\Container\ServiceResolver;
use Drupal\at_base\Container\ArgumentResolver;
use Drupal\at_base\Helper\ConfigFetcher;
use Drupal\at_base\Helper\Wrapper\Database as DB_Wrapper;
use Drupal\at_base\Helper\Test\Database as DB_Wrapper_Test;
use Drupal\at_base\Helper\Wrapper\Cache as Cache_Wrapper;
use Drupal\at_base\Helper\Test\Cache as Cache_Wrapper_Test;
use Drupal\at_base\Config\Resolver as Config_Resolver;
use Drupal\at_base\Config\Config;
use Zend\EventManager\SharedEventManager;

class Container extends SharedEventManager {
  /**
   * Flag to make sure the boot script only run once.
   *
   * @var boolean
   */
  protected $booted;

  public function __construct() {
    $this->attach('AndyTruong\Common\Container', 'at.container.not_found', array($this, 'onNotFound'));
    $this->attach('AndyTruong\Common\Container', 'at.container.find_by_tag', array($this, 'onFindByTag'));
  }

  /**
   * The callback to be stored directly in container.
   *
   * @param string $id
   * @param array $def
   * @return callable
   */
  protected function getServiceCallback($id, $def) {
    return function($c) use ($id, $def) {
      if (isset($c["{$id}:arguments"])) {
        $def['arguments'] = $c["{$id}:arguments"];
      }

      list($args, $calls) = $c['argument.resolver']->resolve($def);

      return $c['service.resolver']->convertDefinitionToService($def, $args, $calls);
    };
  }

  /**
   * Find service, save to container.
   *
   * @param Zend\EventManager\Event $e
   */
  public function onNotFound($e) {
    // Initialize base services
    if (is_null($this->booted)) {
      $container = atc();

      $container->offsetSet('wrapper.db', function() {
        # return defined('AT_BASE_TESTING_UNIT') ? new DB_Wrapper_Test() : new DB_Wrapper();
        return new DB_Wrapper();
      });

      $container->offsetSet('wrapper.cache', function() {
        # return defined('AT_BASE_TESTING_UNIT') ? new Cache_Wrapper_Test() : new Cache_Wrapper();
        return new Cache_Wrapper();
      });

      $container->offsetSet('config', function() { return new Config(new Config_Resolver()); });
      $container->offsetSet('service.resolver', function() { return new ServiceResolver(); });
      $container->offsetSet('argument.resolver', function() { return new ArgumentResolver(); });
      $container->offsetSet('helper.config_fetcher', function() { return new ConfigFetcher(); });

      // Make sure this code-block run once
      $this->booted = TRUE;
    }

    // Get service ID
    list($id, $args) = $e->getParams();

    // Get service definition array from ID
    if ($def = atcg('service.resolver')->getDefinition($id)) {
      if (!is_null($args)) {
        $def['arguments'] = $args;
      }

      (isset($def['reuse']) && !$def['reuse'])
        ? atcf($id, $this->getServiceCallback($id, $def))
        : atcs($id, $this->getServiceCallback($id, $def))
      ;
    }
  }

  /**
   * …
   *
   * @param \Zend\EventManager\Event $e
   * @return \Drupal\at_base\SharedEventManager\Container
   */
  public function onFindByTag($e) {
    $container = $e->getTarget();
    list($tag, $return) = $e->getParams();

    $defs = at_cache("atc:tag:{$tag}, + 1 year",
              array($container->offsetGet('service.resolver'), 'fetchDefinitions'),
              array($tag));

    if ($return === 'service') {
      foreach ($defs as $k => $name) {
        unset($defs[$k]);
        $defs[$name] = $container->offsetGet($name);
      }
    }

    return $defs;
  }
}
