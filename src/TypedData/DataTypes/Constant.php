<?php

namespace Drupal\at_base\TypedData\DataTypes;

/**
 * @todo Support more type of constant
 *
 *  - CLASS_NAME::CONSTANT_2
 *  - NAMESPACE\CLASS_NAME::CONSTANT_3
 *
 * @param  string $v
 */
class Constant extends Base {

  public function getValue() {
    if ($this->validate()) {
      return constant($this->value);
    }
  }

  public function validateInput(&$error = NULL) {
    if (!is_string($this->value)) {
      $error = 'Input must be a string.';
      return FALSE;
    }

    if (!preg_match('/^[A-Z0-9_]+$/', $this->value)) {
      $error = 'Constant must only contains A-Z/0-9/_';
      return FALSE;
    }

    if (!defined($this->value)) {
      $error = 'Constant is not defined.';
      return FALSE;
    }

    return parent::validateInput($error);
  }

}
