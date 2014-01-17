<?php
namespace Drupal\at_base\Helper;

/**
 * controller.resolver service.
 *
 * Get controller from definition.
 *
 * Definition can be:
 *  - name of function.
 *  - name of class
 *  - pair of object, method
 *  - Twig string
 *
 * @see Drupal\at_base\Tests\CommonTest::testControllerRevoler()
 */
class Controller_Resolver {
  public function get($definition) {
    // definition: [Foo, bar]
    if (is_array($definition) && 2 === count($definition)) {
      return $definition;
    }

    // $foo with magic method __invoke
    if (is_object($definition) && method_exists($definition, '__invoke')) {
      return $definition;
    }

    // definition is class::method
    if (strpos($definition, '::') !== FALSE) {
      list($class, $method) = explode('::', $definition, 2);
      return array($class, $method);
    }

    // definition is service_name:service_method
    if (strpos($definition, ':') !== FALSE) {
      list($service, $method) = explode(':', $definition, 2);
      return array(at_container($service), $method);
    }

    // Twig
    $is_twig_1 = FALSE !== strpos($definition, '{{') && FALSE !== strpos($definition, '}}');
    $is_twig_2 = FALSE !== strpos($definition, '{%') && FALSE !== strpos($definition, '%}');

    if ($is_twig_1 || $is_twig_2) {
      $obj = at_container('twig_controller');
      $obj->setTemplate($definition);
      return array($obj, 'render');
    }

    // Simple function
    if (method_exists($definition, '__invoke')) {
      return new $definition;
    }
    elseif (function_exists($definition)) {
      return $definition;
    }
  }
}
