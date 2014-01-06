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

    $this->callback = at_container('controller.resolver')->get($callback);
    $this->options = $options;
  }

  public static function render($callback, $options) {
    return at_id(new self($callback, $options))->execute();
  }

  public function execute() {
    return at_cache($this->options, $this->callback, $this->arguments);
  }
}
