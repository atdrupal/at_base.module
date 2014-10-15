<?php

namespace Drupal\at_base\TypedData\DataTypes;

abstract class Base {

  protected $def;
  protected $value;

  public function __construct($def = NULL, $val = NULL) {
    !is_null($def) && $this->setDef($def);
    !is_null($val) && $this->setValue($val);
  }

  public function setDef($def) {
    $this->def = $def;
    return $this;
  }

  public function setValue($value) {
    $this->value = $value;
    return $this;
  }

  public function isEmpty() {
    return FALSE;
  }

  /**
   * Alias of self::validate()
   * @return boolean
   */
  public function isValid(&$error = NULL) {
    return $this->validate($error);
  }

  public function getValue() {
    if ($this->validate()) {
      return $this->value;
    }
  }

  public function validate(&$error = NULL) {
    return $this->validateDefinition($error) && $this->validateInput($error);
  }

  protected function validateDefinition(&$error) {
    if (!is_array($this->def)) {
      $error = 'Data definition must be an array.';
      return FALSE;
    }
    return TRUE;
  }

  protected function validateInput(&$error) {
    if (!empty($this->def['validate'])) {
      return $this->validateUserCallacks($error);
    }
    return TRUE;
  }

  protected function validateUserCallacks(&$error) {
    foreach ($this->def['validate'] as $callback) {
      if (is_callable($callback)) {
        if (!$callback($this->value, $error)) {
          return FALSE;
        }
      }
    }

    return TRUE;
  }

}
