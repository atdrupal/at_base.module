<?php
namespace Drupal\at_base\Twig\Filters;

class ATConfig {
  public static function render($string) {
    list($module, $id, $key) = explode(':', $string);
    return at_config($module, $id)->get($key);
  }
}
