<?php

namespace Drupal\at_base;

use Drupal\at_base\Config\Config;
use Drupal\at_base\Config\Resolver as Config_Resolver;
use Drupal\at_base\Helper\ConfigFetcher;

class Container extends \Pimple
{

    public function __construct()
    {
        parent::__construct(array(
            // Dependencies for Container itself
            'wrapper.db' => function() {
                return at()->getApi()->getDrupalDatabaseAPI();
            },
            'wrapper.cache' => function() {
                return at()->getApi()->getDrupalCacheAPI();
            },
            'config' => function() {
                return new Config(new Config_Resolver());
            },
            'helper.config_fetcher' => function() {
                return new ConfigFetcher();
            },
        ));
    }

}
