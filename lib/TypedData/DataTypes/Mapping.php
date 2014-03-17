<?php
namespace Drupal\at_base\TypedData\DataTypes;

class Mapping extends Base {
  protected $required_properties = array();
  protected $allow_extra_properties = TRUE;

  public function setDef($def) {
    $this->def = $def;

    if (isset($def['required_properties'])) {
      $this->required_properties = $def['required_properties'];
    }

    if (isset($def['allow_extra_properties'])) {
      $this->allow_extra_properties = $def['allow_extra_properties'];
    }

    return $this;
  }

  public function getValue() {
    if ($this->validate()) {
      $value = array();

      foreach ($this->value as $k => $v) {
        $value[$k] = $this->getItemValue($k, $v);
      }

      return $value;
    }
  }

  private function getItemValue($k, $v) {
    $return = $v;

    if (isset($this->def['mapping'][$k]['type'])) {
      $def = array('type' => $this->def['mapping'][$k]['type']);
      $data = at_data($def, $v);
      $return = $data->getValue();
    }

    return $return;
  }

  public function validate(&$error = NULL) {
    return $this->validateDefinition($error) && $this->validateInput($error);
  }

  private function validateDefinition(&$error) {
    if (!is_array($this->def)) {
      $error = 'Data definition must be an array';
      return FALSE;
    }

    if (!isset($this->def['mapping'])) {
      $error = 'Missing mappaping property for data definition.';
      return FALSE;
    }

    if (!is_array($this->def['mapping'])) {
      $error = 'Mapping property of data definition must be an array.';
      return FALSE;
    }

    return TRUE;
  }

  private function validateInput(&$error) {
    return $this->validateRequiredProperties($error)
      && $this->validateAllowingExtraProperties($error)
    ;
  }

  private function validateRequiredProperties(&$error) {
    if (!empty($this->required_properties)) {
      foreach ($this->required_properties as $k) {
        if (!isset($this->value[$k])) {
          $error = "Property {$k} is required.";
          return FALSE;
        }
      }
    }
    return TRUE;
  }

  private function validateAllowingExtraProperties(&$error) {
    if (!$this->allow_extra_properties) {
      foreach (array_keys($this->value) as $k) {
        if (!isset($this->def['mapping'][$k])) {
          $error = 'Unexpected key found: '. $k .'.';
          return FALSE;
        }
      }
    }

    return TRUE;
  }
}
