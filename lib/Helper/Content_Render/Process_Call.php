<?php
namespace Drupal\at_base\Helper\Content_Render;

class Process_Call {
  private $before;
  private $after;

  public function __construct($before, $after) {
    $this->before = $before;
    $this->after = $after;
  }

  public function callBefore($key = 'before') {
    if (!empty($this->$key)) {
      $this->runCallbacks($this->$key);
    }
  }

  public function callAfter() {
    $this->callBefore('after');
  }

  private function runCallbacks($calls) {
    $cr = at_container('helper.controller.resolver');
    foreach ($calls as $call) {
      $call = is_string($call) ? array($call, array()) : $call;
      if ($controller = $cr->get($call[0])) {
        $args = isset($call[1]) ? $call[1] : array();
        $this->prepareArguments($args);
        call_user_func_array($controller, $args);
      }
    }
  }

  private function prepareArguments(&$args) {
    foreach ($args as &$arg) {
      if (is_array($arg)) {
        $this->prepareArguments($arg);
      }
      elseif (is_string($arg) && preg_match('/^[@%].+/', $arg)) {
        $arg = at_container('helper.real_path')->get($arg);
      }
    }
  }
}
