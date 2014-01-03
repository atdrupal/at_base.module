<?php

namespace Drupal\at_base\Helper\Content_Render;

class Function_Engine extends String_Engine {
  public function process() {
    return call_user_func_array($this->data['function'], $this->data['arguments'] ? $this->data['arguments'] : array());
  }
}
