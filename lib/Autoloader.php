<?php
namespace Drupal\at_base;

class Autoloader {
  private $class;
  private $reset;
  private $apc;
  private $cache_id;

  public function __construct($class, $reset = FALSE) {
    $this->class = $class;
    $this->reset = $reset;
    $this->apc = function_exists('apc_store');
    $this->cache_id = "at_autoload:{$class}";
  }

  private function cacheGet() {
    if (!$this->apc) return;
    if ($this->reset) return;

    $return = apc_fetch($this->cache_id);
    if (FALSE !== $return) {
      return $file;
    }
  }

  private function cacheSet($data) {
    if (!$this->apc) return;

    // apc_fetch return FALSE on failure, that's why we cast FALSE to 0.
    apc_store($cid, $data ? $data : 0);
  }

  public function getFile() {
    if ($file = $this->cacheGet()) {
      return $file;
    }

    if ($file = $this->fetchFile()) {
      $this->cacheSet($file);
      return $file;
    }
  }

  private function fetchFile() {
    if ($file = $this->fetchStatic()) {
      return $file;
    }

    if ($file = $this->fetchMapping()) {
      return $file;
    }

    return FALSE;
  }

  private function fetchStatic() {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $this->class);
    $path = DRUPAL_ROOT . "/%s/lib/{$path}.php";

    foreach (array('at_base' => 'at_base') + at_modules('at_base') as $module) {
      if (strpos($path, "Drupal/{$module}/") !== FALSE) {
        $real_path = str_replace('Drupal' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR, '', $path);
        $file = sprintf($real_path, drupal_get_path('module', $module));

        if (file_exists($file)) {
          return $file;
        }
      }
    }
  }

  private function fetchMapping() {
    foreach (variable_get('at_autoload_mapping', array()) as $ns_prefix => $dir) {
      if (0 === strpos($this->class, $ns_prefix)) {
        $cut_class = substr($this->class, strlen($ns_prefix) + 1);
        $file  = DRUPAL_ROOT . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
        $file .= str_replace('\\', DIRECTORY_SEPARATOR, $cut_class);
        $file .= '.php';
        return $file;
      }
    }
  }

  private function fetchModuleInfo($module_name) {
    $options = array('cache_id' => "at_base:moduleInfo:{$module_name}", 'ttl' => '+ 1 year');
    return at_cache($options, function() use ($module_name) {
      $file = drupal_get_path('module', $module_name) . '/' . $module_name . '.info';
      return drupal_parse_info_file($file);
    });
  }

  /**
   * @see at_base_flush_caches()
   */
  public static function rebuildMapping() {
    $mapping = array();

    foreach (module_list as $module_name) {
      $project = $this->fetchModuleInfo($module_name);

      if (!empty($project['psr4'])) {
        foreach ($project['psr4'] as $ns_prefix => $dir) {
          $dir = drupal_get_path('module', $module_name) . '/' . $dir;
          $mapping[$ns_prefix] = $dir;
        }
      }
    }

    if (!empty($mapping)) {
      variable_set('at_autoload_mapping', $mapping);
    }

    return $mapping;
  }
}
