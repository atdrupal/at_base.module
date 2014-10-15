<?php

namespace Drupal\at_base\Helper;

/**
 * @see at_modules()
 */
class ModulesFetcher {

  private $base_module;
  private $config_file;

  /**
   * @param string $base_module
   * @param string $config_file
   */
  public function __construct($base_module, $config_file) {
    $this->base_module = $base_module;
    $this->config_file = $config_file;
  }

  public function fetch($enabled_modules) {
    $modules = array();

    foreach ($enabled_modules as $name => $info) {
      if ($this->validateModule($name, $info->info)) {
        $modules[] = $name;
      }
    }

    return $modules;
  }

  private function validateModule($name, $info) {
    if (empty($info['dependencies'])) {
      return FALSE;
    }

    if (!in_array($this->base_module, $info['dependencies'])) {
      return FALSE;
    }

    // Do no need checking config file
    if (empty($this->config_file)) {
      return TRUE;
    }

    // Config file is available
    $file = DRUPAL_ROOT . '/' . drupal_get_path('module', $name) . '/config/' . $this->config_file . '.yml';

    return is_file($file);
  }

}
