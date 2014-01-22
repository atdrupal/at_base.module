<?php
namespace Drupal\at_base;

use Drupal\at_base\Container\Service_Resolver;
use Drupal\at_base\Helper\Config_Fetcher;
use Drupal\at_base\Helper\Wrapper\Database as DB_Wrapper;
use Drupal\at_base\Helper\Wrapper\Cache as Cache_Wrapper;
use Drupal\at_base\Config\Resolver as Config_Resolver;
use Drupal\at_base\Config\Config;

require_once at_library('pimple') . '/lib/Pimple.php';

/**
 * Service Container/Locator.
 *
 * @see  https://github.com/andytruong/at_base/wiki/7.x-2.x-service-container
 */
class Container extends \Pimple {
    public function __construct()
    {
        parent::__construct(array(
            'container' => $this,
            // Dependencies for Container itself
            'wrapper.db' => function() { return new DB_Wrapper(); },
            'wrapper.cache' => function() { return new Cache_Wrapper(); },
            'config' => function() { return new Config(new Config_Resolver()); },
            'service.resolver' => function() { return new Service_Resolver(); },
            'helper.config_fetcher' => function() { return new Config_Fetcher(); },
        ));
    }

    public function offsetGet($id)
    {
        if (!$this->offsetExists($id)) {
            if ($value = $this['service.resolver']->getClosure($id)) {
                $this->offsetSet($id, $value);
            }
        }

        return parent::offsetGet($id);
    }

    /**
     * Find services by tag
     *
     * @param  string  $tag
     * @return array
     */
    public function find($tag, $return = 'service_name')
    {
        $defs = at_cache("atc:tag:{$tag}, + 1 year", array($this['service.resolver'], 'fetchDefinitions'), array($tag));

        if ($return === 'service_name') {
            return $defs;
        }

        if ($return === 'service') {
            foreach ($defs as $k => $name) {
                unset($defs[$k]);
                $defs[$name] = $this[$name];
            }
        }

        return $defs;
    }

}
