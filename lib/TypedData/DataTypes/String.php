<?php
namespace Drupal\at_base\TypedData\DataTypes;

class String extends Base {
  public function isEmpty() {
    if (!is_null($this->value)) {
      return $this->value === '';
    }
  }

  public function validate(&$error = NULL) {
    if (!is_string($this->value)) {
      $error = 'Input is not a string value.';
      return FALSE;
    }
    return parent::validate($error);
  }
}
