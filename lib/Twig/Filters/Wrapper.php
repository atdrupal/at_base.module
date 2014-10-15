<?php

namespace Drupal\at_base\Twig\Filters;

/**
 * Handler for drupalEntity Twig filter.
 *
 * @see Drupal\at_base\Twig\FilterFetcher::makeContructiveClassBasedFilter()
 */
class Wrapper {
  public static function __callStatic($name, $arguments) {
    $def = at_container('helper.config_fetcher')->getItem('at_base', 'twig_filters', 'twig_filters' , "__{$name}", TRUE);

    if (!$def) {
      throw new \Exception("Can not find definition for Twig filter: {$name}");
    }

    list($class, $method) = $def;
    return at_newv($class, $arguments)->{$method}();
  }
}
