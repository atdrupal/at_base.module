<?php

namespace Drupal\at_base;

use Drupal\at_base\API\API;
use Drupal\at_base\Helper\ModuleFetcher;
use Drupal\at_base\HookImplementation\Implementation;

class AT
{

    /** @var Implementation */
    private $hookImplementation;

    /** @var API */
    private $api;

    /**
     * @return Implementation
     */
    public function getHookImplementation()
    {
        if (NULL === $this->hookImplementation) {
            $this->hookImplementation = new Implementation();
        }
        return $this->hookImplementation;
    }

    public function getApi()
    {
        if (NULL === $this->api) {
            $this->api = new API();
        }
        return $this->api;
    }

    public function getModuleFetcher($baseModule, $configFile = '')
    {
        return new ModuleFetcher($baseModule, $configFile);
    }

    public function setHookImplementation($hookImplementation)
    {
        $this->hookImplementation = $hookImplementation;
        return $this;
    }

    public function setApi($api)
    {
        $this->api = $api;
        return $this;
    }

}
