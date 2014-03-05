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
    if (!empty($this->data[$key])) {
      $this->runCallbacks($this->data[$key]);
    }
  }

  public function callAfter() {
    $this->runBefore('after');
  }

  private function runCallbacks($calls) {
    $cr = at_container('helper.controller.resolver');
    foreach ($calls as $call) {
      $call = is_string($call) ? array($call, array()) : $call;
      if ($controller = $cr->get($call[0])) {
        call_user_func_array($controller, isset($call[1]) ? $call[1] : array());
      }
    }
  }
}
