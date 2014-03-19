<?php
namespace Drupal\at_base\TypedData\DataTypes;

class Boolean extends Base {
  public function isEmpty() {
    if (!is_null($this->value)) {
      return $this->value === FALSE;
    }
  }

  public function validateInput(&$error = NULL) {
    if (!is_bool($this->value)) {
      $error = 'Input is not a boolean value.';
      return FALSE;
    }
    return parent::validateInput($error);
  }
}
