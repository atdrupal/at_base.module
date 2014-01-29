<?php
namespace Drupal\at_base\Twig;

class Function_Fetcher {
  private function fetchDefinitions() {
    return at_container('helper.config_fetcher')->getItems('at_base', 'twig_functions', 'twig_functions', TRUE);
  }

  public function fetch() {
    $functions = array();

    foreach ($this->fetchDefinitions() as $fn) {
      $functions[] = new \Twig_SimpleFunction($fn, $fn);
    }

    return $functions;
  }
}
