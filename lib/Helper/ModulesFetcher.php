<?php
namespace Drupal\at_base\Helper;

/**
 * @see at_modules()
 */
class ModulesFetcher {
  private $base_module;
  private $config_file;

  public function __construct($base_module, $config_file) {
    $this->base_module = $base_module;
    $this->config_file = $config_file;
  }

  public function fetch() {
    $modules = array();

    foreach (module_list() as $module_name) {
      if ($this->validateModule($module_name)) {
        $modules[] = $module_name;
      }
    }

    return $modules;
  }

  private function fetchModuleInfo($module_name) {
    $options = array('cache_id' => "at_base:moduleInfo:{$module_name}", 'ttl' => '+ 1 year');
    return at_cache($options, function() use ($module_name) {
      $file = drupal_get_path('module', $module_name) . '/' . $module_name . '.info';
      return drupal_parse_info_file($file);
    });
  }

  private function validateModule($module_name) {
    if (!$module_info = $this->fetchModuleInfo($module_name)) return FALSE;
    if (empty($module_info['dependencies'])) return FALSE;
    if (!in_array($this->base_module, $module_info['dependencies'])) return FALSE;

    // Do no need checking config file
    if (empty($this->config_file)) return TRUE;

    // Config file is available
    $file = DRUPAL_ROOT . '/' . drupal_get_path('module', $module_name) . '/config/'. $this->config_file .'.yml';
    if (is_file($file)) return TRUE;

    return FALSE;
  }
}
