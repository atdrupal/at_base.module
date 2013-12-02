<?php
namespace Drupal\at_base\Config;

class Resolver implements ResolverInterface {
  /**
   * @var Config
   */
  private $config;

  public function setConfig($config) {
    $this->config = $config;
  }

  /**
   * [getPath description]
   * @return [type] [description]
   */
  public function getPath() {
    if ($path = $this->getOverridePath($this->config->getId(), $this->config->getModule())) {
      return $path;
    }
    return $this->getOriginalPath($this->config->getId(), $this->config->getModule());
  }

  /**
   * [getOriginalPath description]
   * @return [type] [description]
   */
  public function getOriginalPath() {
    $config_id = $this->config->getId();

    $path = DRUPAL_ROOT . '/' . conf_path();
    if ($_path = drupal_get_path('module', $this->config->getModule())) {
      $config_id = trim($config_id, '/');
      $config_id = empty($config_id) ? $this->config->getModule() : $config_id;
      $path = DRUPAL_ROOT . '/' . $_path;
    }
    $config_id = trim(str_replace('.', '/', $config_id), '/');
    $path .= '/config/' . $config_id . '.yml';
    return is_file($path) ? $path : FALSE;
  }

  /**
   * [getOverridePath description]
   * @return [type] [description]
   */
  public function getOverridePath() {
  }

  /**
   * Fetch config data.
   *
   * @return mixed
   */
  public function fetchData() {
    if ($path = $this->getPath()) {
      return $this->fetchFile($path);
    }
  }

  private function fetchFile($path) {
    $return = at_config_read_yml($path);

    if (empty($return['imports'])) {
      return $return;
    }

    $_return = array();
    foreach ($return['imports'] as $i => $import) {
      $resource = $import['resource'];
      $resource = DRUPAL_ROOT . '/' . drupal_get_path('module', $this->config->getModule()) . '/config/' . $resource;
      if ($data = $this->fetchFile($resource)) {
        foreach ($data as $k => $v) {
          $_return[$k] = !isset($_return[$k]) ? $v : array_merge($_return[$k], $v);
        }
      }
    }

    unset($return['imports']);

    foreach ($return as $k => $v) {
      $_return[$k] = !isset($_return[$k]) ? $v : array_merge($_return[$k], $v);
    }

    return $_return;
  }
}
