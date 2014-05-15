<?php
namespace Drupal\at_base;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class CompilerPass implements CompilerPassInterface {
  public function process(ContainerBuilder $container) {
    $loader = new YamlFileLoader($container, new FileLocator(DRUPAL_ROOT));

    // First, load all default services
    $loader->load(drupal_get_path('module', 'at_base') . '/config/services.yml');

    foreach (\at_fn::at_modules('at_base', 'services') as $module) {
      if ($module !== 'at_base') {
        $loader->load(drupal_get_path('module', $module) . '/config/services.yml');
      }
    }

    // Fix including file feature
    // SFDI does not know real path — @module_name should be replaced to /path/to/module_name
    // on including.
    foreach ($container->getDefinitions() as $definition) {
        if ($file = $definition->getFile()) {
            $file = at_id(new Helper\RealPath())->get($file);
            $definition->setFile($file);
        }
    }

    $container->register('at_context', 'Drupal\at_base\Context');
  }
}
