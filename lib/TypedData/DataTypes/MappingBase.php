<?php

namespace Drupal\at_base\TypedData\DataTypes;

abstract class MappingBase extends Base {

  protected $allow_extra_properties = TRUE;

  public function setDef($def) {
    $this->def = $def;

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

}
