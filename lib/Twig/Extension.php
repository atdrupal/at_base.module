<?php
namespace Drupal\at_base\Twig;

class Extension extends \Twig_Extension {
  public function getName() {
    return 'AT Base';
  }

  function getFilters() {
    return at_cache(array('id' => 'at:twig:fts'), function() {
      return at_id(new \Drupal\at_base\Twig\Filters())->get();
    });
  }

  function getFunctions() {
    return at_cache(array('id' => 'at:twig:fns'), function() {
      return at_id(new \Drupal\at_base\Twig\Functions())->get();
    });
  }

  function getGlobals() {
    global $user;

    return array(
      'user' => $user
    );
  }
}
