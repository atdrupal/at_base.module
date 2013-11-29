<?php
namespace Drupal\at_base\Helper;

/**
 * Tool to replace tokens with real values:
 *
 *  %theme => /path/to/active_theme/
 *  @at_base => /path/to/at_base/
 */
class RealPath {
  private $path;

  public function __construct($path) {
    $this->path = $path;
  }

  public function get() {
    $real_path = $this->path;

    if (strpos($real_path, '%theme/') !== FALSE) {
      $real_path = str_replace('%theme/', path_to_theme() . '/', $real_path);
    }

    preg_match('/@([a-z_]+)/', $real_path, $matches);
    if (!empty($matches)) {
      $module = $matches[1];
      if ($module_path = drupal_get_path('module', $module)) {
        $real_path = str_replace("@{$module}/", $module_path . '/', $real_path);
      }
    }

    return $real_path;
  }
}
