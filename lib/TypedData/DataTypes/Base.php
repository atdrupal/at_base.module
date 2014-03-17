<?php
namespace Drupal\at_base\TypedData\DataTypes;

abstract class Base {
  protected $def;
  protected $value;

  public function __construct($def, $value = NULL) {
    $this->setDef($def);
    $this->setValue($value);
  }

  public function setDef($def) {
    $this->def = $def;
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
    return TRUE;
  }
}
