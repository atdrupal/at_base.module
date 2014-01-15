<?php
namespace Drupal\at_base\Twig;

use \Drupal\at_base\Twig\Filters as Twig_Filters;
use \Drupal\at_base\Twig\Functions as Twig_Functions;

class Extension extends \Twig_Extension {
  public function getName() {
    return 'AT Base';
  }

  function getFilters() {
    return at_cache(array('id' => 'at:twig:fts'), function() {
      return at_id(new Twig_Filters())->get();
    });
  }

  function getFunctions() {
    return at_cache(array('id' => 'at:twig:fns'), function() {
      return at_id(new Twig_Functions())->get();
    });
  }

  function getGlobals() {
    global $user;

    return array(
      'user' => $user
    );
  }
}
