<?php

namespace Drupal\at_base\API;

use Drupal\at_base\Helper\Wrapper\DrupalCacheAPI;

class API
{

    /** @var BreadcrumbAPI */
    private $breadcrumb;

    /** @var DrupalCacheAPI */
    private $drupalCacheAPI;

    /** @var DrupalDatabaseAPI */
    private $drupalDatabaseAPI;

    /** @var \Drupal\at_base\API\CacheAPI */
    private $cacheAPI;

    public function getBreadcrumbAPI()
    {
        if (NULL === $this->breadcrumb) {
            $this->breadcrumb = new BreadcrumbAPI();
        }
        return $this->breadcrumb;
    }

    public function getDrupalDatabaseAPI()
    {
        if (NULL === $this->drupalDatabaseAPI) {
            $this->drupalDatabaseAPI = new DrupalDatabaseAPI();
        }
        return $this->drupalDatabaseAPI;
    }

    public function getDrupalCacheAPI()
    {
        if (NULL == $this->drupalCacheAPI) {
            $this->drupalCacheAPI = new DrupalCacheAPI();
        }
        return $this->drupalCacheAPI;
    }

    public function getCacheAPI()
    {
        if (NULL === $this->cacheAPI) {
            $this->cacheAPI = new \Drupal\at_base\API\CacheAPI();
        }
        return $this->cacheAPI;
    }

    public function setBreadcrumbAPI($api)
    {
        $this->breadcrumb = $api;
        return $this;
    }

    public function setDrupalDatabaseAPI($api)
    {
        $this->drupalDatabaseAPI = $$api;
        return $this;
    }

    public function setDrupalCacheAPI($api)
    {
        $this->drupalCacheAPI = $api;
        return $this;
    }

    public function setCacheAPI($api)
    {
        $this->cacheAPI = $api;
        return $this;
    }

}
