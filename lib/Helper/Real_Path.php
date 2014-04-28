<?php
namespace Drupal\at_base\Helper;

/**
 * Tool to replace tokens with real values:
 *
 * @code
 *  %theme    => /path/to/active_theme/
 *  @at_base  => /path/to/at_base/
 *  %%name    => /path/to/libraries/name
 * @code
 *
 * @see \At_Base_TestCase::testRealPath()
 */
class Real_Path {

  /**
   * @param  string $path
   * @return string
   */
  public function get($path, $include_drupal_root = TRUE) {
    foreach (array('Module', 'Theme', 'Library') as $k) {
      $method = "replace{$k}Token";
      if ($real_path = $this->{$method}($path, $include_drupal_root)) {
        return $real_path;
      }
    }

    return $path;
  }

  /**
   * Replace @module_name to /path/to/module_name.
   *
   * @param  string $path
   * @return string|null
   */
  private function replaceModuleToken($path) {
    if ('@' === substr($path, 0, 1)) {
      preg_match('/@([a-z_]+)/i', $path, $matches);
      if (!empty($matches)) {
        $module = $matches[1];
        if ($module_path = drupal_get_path('module', $module)) {
          return str_replace("@{$module}/", $module_path . '/', $path);
        }
      }
    }
  }

  /**
   * Replace %theme to path to active theme.
   *
   * @param  string $path
   * @return string|null
   */
  private function replaceThemeToken($path) {
    if ('%theme/' === substr($path, 0, 7)) {
      return str_replace('%theme/', path_to_theme() . '/', $path);
    }
  }

  /**
   * Replace %library_name to /path/to/libraries/library_name/.
   *
   * @return string|null
   */
  private function replaceLibraryToken($path, $include_drupal_root) {
    if ('%' === substr($path, 0, 1)) {
      preg_match('/%([a-z0-9_\.]+)/i', $path, $matches);
      
      if (!empty($matches)) {
        $library = $matches[1];
        if ($library_path = at_library($library, NULL, $include_drupal_root)) {
          return str_replace("%{$library}/", $library_path . '/', $path);
        }
      }
    }
  }
}
