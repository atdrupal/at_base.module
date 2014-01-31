<?php

namespace Drupal\at_base\Helper\Content_Render;

class Form_Engine extends Base_Engine {
  public function process() {
    $args = array(
      'at_form',
      $this->data['form'],
      isset($this->data['form arguments']) ? $this->data['form arguments'] : array(),
    );
    return call_user_func_array('drupal_get_form', $args);
  }
}
