<?php
namespace Drupal\at_base\Config;

class Resolver implements ResolverInterface
{
  /**
   * @var Config
   */
  private $config;

  public function setConfig($config)
  {
    $this->config = $config;
  }

  /**
   * [getPath description]
   * @return [type] [description]
   */
  public function getPath()
  {
    if ($path = $this->getOverridePath()) {
      return $path;
    }
    return $this->getOriginalPath();
  }

  /**
   *
   *
   * @return string|false
   */
  public function getOriginalPath()
  {
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
   *
   * @return string|false
   */
  public function getOverridePath($check_exists = TRUE)
  {
    $return = variable_get('file_private_path');
    $return = dirname($return);
    $return .= '/config';
    $return .= '/' . $this->config->getModule();
    $return .= '/' . $this->config->getId();
    $return .= '.yml';
    $return = DRUPAL_ROOT . '/' . $return;
    if (!$check_exists) {
      return $return;
    }
    return is_file($return) ? $return : FALSE;
  }

  /**
   * Fetch config data.
   *
   * @return mixed
   */
  public function fetchData()
  {
    if ($path = $this->getPath()) {
      return $this->fetchFile($path);
    }
  }

  /**
   * @param string $path
   */
  private function fetchFile($path)
  {
    $return = yaml_parse_file($path);

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

  public function writeData($data)
  {
    $path = $this->getOverridePath(FALSE);
    $data = yaml_emit($data);

    @mkdir(dirname($path), 0777, TRUE);
    return file_put_contents($path, $data);
  }
}

if (!function_exists('yaml_parse')) {
  /**
   * Read YAML file.
   *
   * @param  string $path Path to yaml file.
   * @return mixed
   */
  public function yaml_parse_file($path)
  {
    if (!is_file(DRUPAL_ROOT . '/sites/all/libraries/spyc/Spyc.php')) {
      throw new \RuntimeException('Missing library: spyc');
    }

    if (!function_exists('spyc_load_file')) {
      require_once DRUPAL_ROOT . '/sites/all/libraries/spyc/Spyc.php';
    }

    return spyc_load_file($path);
  }
}

if (!function_exists('yaml_emit')) {
  public function yaml_emit($data)
  {
    return spyc_dump($data);
  }
}
