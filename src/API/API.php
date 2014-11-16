<?php

namespace Drupal\at_base\API;

class API
{

    private $breadcrumb;

    public function getBreadcrumbAPI()
    {
        if (NULL === $this->breadcrumb) {
            $this->breadcrumb = new BreadcrumbAPI();
        }
        return $this->breadcrumb;
    }

    public function setBreadcrumbAPI($breadcrumbAPI)
    {
        $this->breadcrumb = $breadcrumbAPI;
        return $this;
    }

}
