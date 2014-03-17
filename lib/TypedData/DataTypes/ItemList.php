<?php
namespace Drupal\at_base\TypedData\DataTypes;

class ItemList extends Base {
  /**
   * @var string
   */
  protected $elemen_type = NULL;

  protected $element = array();

  public function getElementValue(string $key, &$error = NULL) {
    if (isset($this->element[$key])) {
      return $this->element[$key];
    }

    $error = 'Not found property {$key}';
  }

  public function setElementValue(string $key, $value) {
    $this->element[$key] = $value;
  }

  public function isEmpty() {
    return is_empty($this->element);
  }

  public function validate(&$error = NULL) {
    if (!is_array($v)) {
      $error = 'Input must be an array';
      return FALSE;
    }

    if (!is_null($this->element_type)) {
      $data = at_data(array('type' => $this->element_type));

      foreach ($this->element as $k => $v) {
        $data->setValue($v)
        if (!$data->validate()) {
          $error = "{$k} is not type of {$this->element_type}";
          return FALSE;
        }
      }
    }

    return TRUE;
  }
}
