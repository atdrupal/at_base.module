<?php
namespace Drupal\at_base\TypedData\DataTypes;

class Callable extends String {
  public function isEmpty() {
    return is_null($this->value) || empty($this->value);
  }

  protected function validateInput(&$error = NULL) {
    if (!is_callable($this->value)) {
      $error = 'Function does not exist.';
      return FALSE;
    }

    return parent::validateInput($error);
  }
}
