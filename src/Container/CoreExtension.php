<?php

namespace Drupal\at_base\Container;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CoreExtension extends Extension
{

    public function load(array $config, ContainerBuilder $container)
    {
        foreach (['at_base' => 'at_base'] + at_modules('at_base', 'services') as $module) {
            $locator = new FileLocator(DRUPAL_ROOT);
            $loader = new YamlFileLoader($container, $locator);
            $loader->load(drupal_get_path('module', $module) . '/config/services.yml');
        }
    }

}
