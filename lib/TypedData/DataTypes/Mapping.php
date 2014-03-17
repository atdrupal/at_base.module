<?php
namespace Drupal\at_base\TypedData\DataTypes;

class Mapping extends Base {
  public function validate() {
    if (!is_array($v) || !isset($v['mapping']) || !is_array($v['mapping'])) {
      return FALSE;
    }

    foreach ($v['mapping'] as $k => $def) {
      $callback = at_data_get_validate_callback($def['type']);
      // if (TRUE === $callback($def)) {}
    }
  }
}
