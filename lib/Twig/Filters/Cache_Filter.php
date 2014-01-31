<?php
namespace Drupal\at_base\Twig\Filters;

class Cache_Filter {
  private $callback;
  private $arguments = array();
  private $options;

  public function __construct($callback, $options) {
    if (is_array($callback) && isset($callback['callback'])) {
      if (isset($callback['arguments'])) {
        $this->arguments = $callback['arguments'];
      }
      $callback = $callback['callback'];
    }

    $this->callback = at_container('helper.controller.resolver')->get($callback);
    $this->options = $options;
  }

  public function render() {
    return at_cache($this->options, $this->callback, $this->arguments);
  }
}
