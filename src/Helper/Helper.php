<?php

namespace Drupal\at_base\Helper;

class Helper
{

    /** @var ContainerCreator */
    private $containerCreator;

    /**
     * @param string $baseModule
     * @param string $configFile
     * @return \Drupal\at_base\Helper\ModuleFetcher
     */
    public function getModuleFetcher($baseModule, $configFile)
    {
        return new ModuleFetcher($baseModule, $configFile);
    }

    public function getContainerCreator($fileName, $namespace = '', $className = 'AT_Container')
    {
        if (NULL === $this->containerCreator) {
            $this->containerCreator = new ContainerCreator($fileName, $namespace, $className);
        }
        return $this->containerCreator;
    }

    public function setContainerCreator(ContainerCreator $containerCreator)
    {
        $this->containerCreator = $containerCreator;
        return $this;
    }

}
