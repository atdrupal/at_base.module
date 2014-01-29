<?php
namespace Drupal\at_base\Twig;

use \Drupal\at_base\Twig\Filters as Twig_Filters;
use \Drupal\at_base\Twig\Functions as Twig_Functions;

/**
 * Twig extensions, collection of filters, functions, … for Drupal site.
 *
 * @todo  Convert this to tagged-service.
 */
class Extension extends \Twig_Extension {
  public function getName() {
    return 'AT Base';
  }

  function getFilters() {
    return at_cache(array('id' => 'at:twig:fts'), function() {
      $filters = array();

      $fs = at_container('helper.config_fetcher')->getItems('at_base', 'twig_filters', 'twig_filters', TRUE);
      foreach ($fs as $f) {
        $valid = is_string($f[1]) && function_exists($f[1]);
        $valid = $valid || is_string($f[1][0]) && class_exists($f[1][0]);
        if ($valid) {
          $filters[] = new \Twig_SimpleFilter($f[0], $f[1]);
        }
      }

      return $filters;
    });
  }

  function getFunctions() {
    return at_cache(array('id' => 'at:twig:fns'), function() {
      $functions = array();

      $fns = at_container('helper.config_fetcher')->getItems('at_base', 'twig_functions', 'twig_functions', TRUE);
      foreach ($fns as $fn) {
        $functions[] = new \Twig_SimpleFunction($fn, $fn);
      }

      return $functions;
    });
  }

  function getGlobals() {
    global $user;

    return array(
      'user' => $user,
      'request_path' => request_path(),
    );
  }
}
