<?php

namespace Drupal\at_base\Twig\Functions;

/**
 * Handler for drupalEntity Twig filter.
 *
 * @see Drupal\at_base\Twig\FilterFetcher::makeContructiveClassBasedFilter()
 */
class Wrapper {
  public static function __callStatic($name, $arguments) {
    $def = at_container('helper.config_fetcher')->getItem('at_base', 'twig_functions', 'twig_functions' , "__{$name}", TRUE);

    if (!$def) {
      throw new \Exception("Can not find definition for Twig function: {$name}");
    }

    list($class, $method) = $def;
    return at_newv($class, $arguments)->{$method}();
  }
}
