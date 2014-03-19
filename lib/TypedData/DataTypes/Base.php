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
    if (!empty($this->def['validate'])) {
      foreach ($this->def['validate'] as $callback) {
        if (is_callable($callback)) {
          if (!$callback($this->value, $error)) {
            return FALSE;
          }
        }
      }
    }

    return TRUE;
  }
}
