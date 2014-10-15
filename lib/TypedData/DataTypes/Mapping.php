<?php
namespace Drupal\at_base\TypedData\DataTypes;

/**
 * at_data($schema, $input)->validate($error);
 *
 * Example schema, check `at_base/config/schema/route.yml`
 */
class Mapping extends MappingBase {
  protected function validateDefinition(&$error) {
    if (!parent::validateDefinition($error)) {
      return FALSE;
    }

    if (empty($this->def['skip validate mapping'])) {
      if (!$this->validateDefMapping($error)) {
       return FALSE;
      }
    }

    return TRUE;
  }

  protected function validateDefMapping(&$error) {
    if (!isset($this->def['mapping'])) {
      $error = 'Wrong schema: Missing mapping property';
      return FALSE;
    }

    if (!is_array($this->def['mapping'])) {
      $error = 'Mapping property of data definition must be an array.';
      return FALSE;
    }

    $element_schema = array(
      'type' => 'mapping',
      'skip validate mapping' => TRUE,
      'mapping' => array(
        'type'        => array('type' => 'string', 'required' => TRUE),
        'required'    => array('type' => 'boolean'),
        'label'       => array('type' => 'string'),
        'description' => array('type' => 'string'),
      )
    );

    foreach ($this->def['mapping'] as $k => $e) {
      if (!at_data($element_schema, $e)->validate($error)) {
        $error = "Wrong schema: {$error}";
        return FALSE;
      }
    }

    return TRUE;
  }

  protected function validateInput(&$error) {
    if (!is_array($this->value)) {
      $error = 'Input must be an array.';
      return FALSE;
    }

    return $this->validateRequiredProperties($error)
      && $this->validateAllowingExtraProperties($error)
      && $this->validateElementType($error)
      && $this->validateRequireOne($error)
      && parent::validateInput($error)
    ;
  }

  protected function validateRequiredProperties(&$error) {
    foreach ($this->def['mapping'] as $k => $item_def) {
      if (!empty($item_def['required']) && !isset($this->value[$k])) {
        $error = "Property {$k} is required.";
        return FALSE;
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

  protected function validateElementType(&$error) {
    foreach ($this->value as $k => $v) {
      if (!empty($this->def['mapping'][$k])) {
        if (!at_data($this->def['mapping'][$k], $v)->validate($error)) {
          $error = "Invalid property `{$k}`: {$error}";
          return FALSE;
        }
      }
    }
    return TRUE;
  }

  /**
   * Supported require_one_of:
   *
   *  Example 1: ['key 1', 'key 2']
   *  Example 1: [['key 1.1', 'key 1.2'], 'key 2']
   */
  protected function validateRequireOne(&$error) {
    if (empty($this->def['require_one_of'])) {
      return TRUE;
    }

    foreach ($this->def['require_one_of'] as $keys) {
      if ($this->validateRequireOneKeys($keys)) {
        return TRUE;
      }
    }

    $error = 'Missing one of  required keys: ' . print_r($this->def['require_one_of'], TRUE);

    return FALSE;
  }

  private function validateRequireOneKeys($keys) {
    is_string($keys) && $keys = array($keys);

    $provided = TRUE;

    foreach ($keys as $k) {
      if (!isset($this->value[$k])) {
        $provided = FALSE;
        break;
      }
    }

    return $provided;
  }
}
