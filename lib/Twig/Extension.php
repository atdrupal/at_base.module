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
    return at_cache('at:twig:fts', function() {
      return at_id(new Filter_Fetcher())->fetch();
    });
  }

  function getFunctions() {
    return at_cache('at:twig:fns', function() {
      return at_id(new Function_Fetcher())->fetch();
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
