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
    $return = at_cache('at:twig:fts', function() {
      return at_id(new FilterFetcher())->fetch();
    });

    return array_merge($return, $this->getMagicItems('Twig_SimpleFilter'));
  }

  function getFunctions() {
    $return = at_cache('at:twig:fns', function() {
      return at_id(new FunctionFetcher())->fetch();
    });

    return array_merge($return, $this->getMagicItems('Twig_SimpleFunction'));
  }

  function getGlobals() {
    global $user;

    return array(
      'user'         => $user,
      'request_path' => request_path(),
    );
  }

  /**
   * @param string $base_class
   */
  private function getMagicItems($base_class) {
    $items = array();

    // fn__*
    $items[] = at_newv($base_class, array(
      'fn__*', function () {
        $args = func_get_args();
        $name = array_shift($args);
        return call_user_func_array($name, $args);
      }
    ));

    // *__class__*
    $items[] = at_newv($base_class, array(
      '*__class__*', function ($class, $method, $args = array()) {
        if ('ns_' === substr($class, 0, 3)) {
          $class = str_replace('__', '\\', substr($class, 3));
        }
        return call_user_func("{$class}::{$method}", $args);
      }
    ));

    // *__obj__*
    $items[] = at_newv($base_class, array(
      '*__obj__*', function ($class, $method, $args = array()) {
        if ('ns_' === substr($class, 0, 3)) {
          $class = str_replace('__', '\\', substr($class, 3));
        }
        return at_newv($class, is_array($args) ? $args : array($args))->{$method}();
      }
    ));

    return $items;
  }
}
