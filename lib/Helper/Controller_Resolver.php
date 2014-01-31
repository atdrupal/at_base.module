<?php
namespace Drupal\at_base\Helper;

/**
 * helper.controller.resolver service.
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
  private $def;

  public function get($def) {
    $this->def = $def;

    foreach (get_class_methods(get_class($this)) as $method) {
      if ('detect' === substr($method, 0, 6)) {
        if ($callable = $this->{$method}()) {
          return $callable;
        }
      }
    }
  }

  /**
   * definition: [Foo, bar]
   *
   * @return array
   */
  private function detectPair() {
    if (is_array($this->def) && 2 === count($this->def)) {
      return $this->def;
    }
  }

  private function detectMagic() {
    // $foo with magic method __invoke
    if (is_object($this->def) && method_exists($this->def, '__invoke')) {
      return $this->def;
    }
  }

  /**
   * definition is class::method
   */
  private function detectStatic() {
    if (strpos($this->def, '::') !== FALSE) {
      list($class, $method) = explode('::', $this->def, 2);
      return array($class, $method);
    }
  }

  private function detectTwig() {
    $is_twig_1 = FALSE !== strpos($this->def, '{{');
    $is_twig_2 = FALSE !== strpos($this->def, '{%');
    if ($is_twig_1 || $is_twig_2) {
      $obj = at_container('twig_controller');
      $obj->setTemplate($this->def);
      return array($obj, 'render');
    }
  }

  private function detectService() {
    // definition is service_name:service_method
    if (strpos($this->def, ':') !== FALSE) {
      list($service, $method) = explode(':', $this->def, 2);
      return array(at_container($service), $method);
    }
  }

  /**
   * Simple function
   */
  private function detectFunction() {
    if (method_exists($this->def, '__invoke')) {
      return new $this->def;
    }

    if (function_exists($this->def)) {
      return $this->def;
    }
  }
}
