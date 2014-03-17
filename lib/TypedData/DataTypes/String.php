<?php
namespace Drupal\at_base\TypedData\DataTypes;

class String extends Base {
  public function isEmpty() {
    if (!is_null($this->value)) {
      return $this->value === '';
    }
  }

  public function validate() {
    return is_string($this->value);
  }
}
