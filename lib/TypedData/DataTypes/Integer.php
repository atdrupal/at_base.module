<?php
namespace Drupal\at_base\TypedData\DataTypes;

class Integer extends Base {
  public function validate() {
    return is_int($this->value);
  }
}
