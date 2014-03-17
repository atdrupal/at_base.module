<?php
namespace Drupal\at_base\TypedData\DataTypes;

class String extends Any {
  public function validate() {
    return is_string($this->value);
  }
}
