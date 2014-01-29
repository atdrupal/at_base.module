<?php
namespace Drupal\at_base\Twig\Filters;

/**
 * Callback for at_config filter.
 */
class ATConfig {
  private $module;
  private $id;
  private $key;

  public function __construct($string) {
    list($this->module, $this->id, $this->key) = explode(':', $string, 3);
  }

  public function render($string) {
    return at_config($this->module, $this->id)->get($this->key);
  }
}
