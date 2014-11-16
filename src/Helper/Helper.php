<?php

namespace Drupal\at_base\Helper;

class Helper
{

    public function getModuleFetcher($baseModule, $configFile)
    {
        if (NULL === $this->moduleFetcher) {
            $this->moduleFetcher = new ModuleFetcher($baseModule, $configFile);
        }
        return $this->moduleFetcher;
    }

}
