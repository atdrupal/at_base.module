<?php
namespace Drupal\at_base;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class CompilerPass implements CompilerPassInterface {
  public function process(ContainerBuilder $container) {
    foreach (\at_fn::at_modules('at_base', 'services') as $module) {
      $loader = new YamlFileLoader($container, new FileLocator(DRUPAL_ROOT));
      $loader->load(drupal_get_path('module', $module) . '/config/services.yml');
    }

    $container->register('at_context', 'Drupal\at_base\Context');
  }
}
