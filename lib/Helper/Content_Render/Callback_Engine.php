<?php

namespace Drupal\at_base\Helper\Content_Render;

class Callback_Engine extends String_Engine {
  public function process() {
    return call_user_func_array($this->data['callback'], $this->data['variables'] ? $this->data['variables'] : array());
  }
}
