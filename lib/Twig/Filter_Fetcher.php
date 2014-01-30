<?php
namespace Drupal\at_base\Twig;

class Filter_Fetcher {
  protected $config_id  = 'twig_filters';
  protected $config_key = 'twig_filters';
  protected $twig_base  = '\Twig_SimpleFilter';
  protected $wrapper    = '\Drupal\at_base\Twig\Filters\Wrapper';

  protected function fetchDefinitions() {
    return at_container('helper.config_fetcher')
      ->getItems('at_base', $this->config_id, $this->config_key, TRUE);
  }

  public function fetch() {
    $filters = array();

    foreach ($this->fetchDefinitions() as $name => $def) {
      $filters[] = $this->makeFilter($name, $def);
    }

    return $filters;
  }

  protected function makeFilter($name, $def) {
    // Backward compactible
    //    old style: - [url, url]
    //    new style: - url: url
    if (is_numeric($name)) {
      return $this->makeFilter($def[0], $def[1]);
    }

    if (is_array($def)) {
      return $this->makeClassBasedFilter($name, $def);
    }

    return at_newv($this->twig_base, array($name, $def));
  }

  protected function makeClassBasedFilter($name, $def) {
    if ('__' === substr($name, 0, 2)) {
      return $this->makeContructiveClassBasedFilter($name, $def);
    }

    list($class, $method) = $def;
    return at_newv($this->twig_base, array($name, "{$class}::{$method}"));
  }

  protected function makeContructiveClassBasedFilter($name, $def) {
    $name = substr($name, 2);
    return at_newv($this->twig_base, array($name, "{$this->wrapper}::{$name}"));
  }
}
