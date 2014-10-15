<?php

namespace Drupal\at_base\Helper\Content_Render;

class ProcessCall {

  private $before;

  public function __construct($before) {
    $this->before = $before;
  }

  public function callBefore($key = 'before') {
    if (!empty($this->$key)) {
      $this->runCallbacks($this->$key);
    }
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
