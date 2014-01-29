<?php
namespace Drupal\at_base\Twig;

class Filter_Fetcher {

  private function fetchDefinitions() {
    return at_container('helper.config_fetcher')->getItems('at_base', 'twig_filters', 'twig_filters', TRUE);
  }

  public function fetch() {
    $filters = array();

    foreach ($this->fetchDefinitions() as $name => $def) {
      $filters[] = $this->makeFilter($name, $def);
    }

    return $filters;
  }

  private function makeFilter($name, $def) {
    // Backward compactible
    //    old style: - [url, url]
    //    new style: - url: url
    if (is_numeric($name)) {
      return $this->makeFilter($def[0], $def[1]);
    }

    if (is_array($def)) {
      return $this->makeClassBasedFilter($name, $def);
    }

    return new \Twig_SimpleFilter($name, $def);
  }

  private function makeClassBasedFilter($name, $def) {
    if ('__' === substr($name, 0, 2)) {
      return $this->makeContructiveClassBasedFilter($name, $def);
    }

    list($class, $method) = $def;
    return new \Twig_SimpleFilter($name, "{$class}::{$method}");
  }

  private function makeContructiveClassBasedFilter($name, $def) {
    $name = substr($name, 2);
    return new \Twig_SimpleFilter($name, "\Drupal\at_base\Twig\Filters\Wrapper::{$name}");
  }
}
