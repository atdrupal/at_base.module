<?php
namespace Drupal\at_base\TypedData;

class DataDefinition {
  public function create($type) {
    $def['type'] = $type;
    return new static($def);
  }

  public function getDataType() {}

  public function getLabel() {}

  public function getDescription() {}

  public function isList() {}

  public function isRequired() {}
}
