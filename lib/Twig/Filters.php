<?php

namespace Drupal\at_base\Twig;

class Filters {
  public function get() {
    $filters = array();

    foreach (array('at_base' => 'at_base') + at_modules('at_base', 'twig_filters') as $module) {
      $filters = array_merge($filters, $this->getByModule($module));
    }

    return $filters;
  }

  public function getByModule($module) {
    $filters = array();

    try {
      $_filters = at_config($module, 'twig_filters')->get('twig_filters');
      if (!is_array($_filters)) continue;

      foreach ($_filters as $_filter) {
        $valid = is_string($_filter[1]) && function_exists($_filter[1]);
        $valid = $valid || is_string($_filter[1][0]) && class_exists($_filter[1][0]);
        if ($valid) {
          $filters[] = new \Twig_SimpleFilter($_filter[0], $_filter[1]);
        }
      }

      return $filters;
    }
    catch (\Exception $e) {
      return array();
    }
  }
}
