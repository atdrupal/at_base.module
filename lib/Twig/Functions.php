<?php

namespace Drupal\at_base\Twig;

class Functions extends Filters {
  public function get() {
    $functions = array();

    foreach (array('at_base' => 'at_base') + at_modules('at_base', 'twig_functions') as $module) {
      $functions = array_merge($functions, $this->getByModule($module));
    }

    return $functions;
  }

  public function getByModule($module) {
    $functions = array();

    try {
      $_functions = at_config($module, 'twig_functions')->get('twig_functions');
      if (!is_array($_functions)) continue;

      foreach ($_functions as $_function) {
        $functions[] = new \Twig_SimpleFunction($_function, $_function);
      }

      return $functions;
    }
    catch (\Exception $e) {
      return array();
    }
  }
}
