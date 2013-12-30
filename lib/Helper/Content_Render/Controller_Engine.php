<?php

namespace Drupal\at_base\Helper\Content_Render;

class Controller_Engine extends String_Engine {
  public function process() {
    @list($class, $action, $arguments) = $this->data['controller'];
    return call_user_func_array(array(new $class(), $action), !empty($arguments) ? $arguments : array());
  }
}
