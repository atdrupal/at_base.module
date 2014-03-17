<?php
namespace Drupal\at_base\TypedData\DataTypes;

class ItemList extends Base {
  /**
   * @var string
   */
  protected $element_type = NULL;

  public function isEmpty() {
    if (!is_null($this->value)) {
      return is_empty($this->value);
    }
  }

  public function validate(&$error = NULL) {
    if (!is_array($this->value)) {
      $error = 'Input must be an array';
      return FALSE;
    }

    if (!is_null($this->element_type)) {
      $data = at_data(array('type' => $this->element_type));

      foreach ($this->element as $k => $v) {
        $data->setValue($v);
        if (!$data->validate()) {
          $error = "{$k} is not type of {$this->element_type}";
          return FALSE;
        }
      }
    }

    return TRUE;
  }
}
