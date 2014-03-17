<?php
namespace Drupal\at_base\TypedData\DataTypes;

class Boolean extends Base {
  public function validate(&$error = NULL) {
    if (!is_bool($this->value)) {
      $error = 'Input is not a boolean value.';
      return FALSE;
    }
    return TRUE;
  }
}
