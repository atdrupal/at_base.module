<?php
namespace Drupal\at_base\TypedData\DataTypes;

class Function extends String {
  public function validate() {
    return is_string($this->value) && function_exists($this->value);
  }
}
