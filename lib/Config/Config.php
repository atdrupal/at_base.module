<?php
namespace Drupal\at_base\Config;

class Config {
  /**
   * Module name.
   *
   * @var string
   */
  private $module;

  /**
   * Config ID.
   *
   * @var string
   */
  private $id;

  /**
   * Resolver.
   *
   * @var Resolver
   */
  private $resolver;

  /**
   * Fetched data.
   * @var mixed
   */
  private $config_data;

  public function __construct(ResolverInterface $resolver) {
    $this->id = 'config';
    $resolver->setConfig($this);
    $this->resolver = $resolver;
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    if (!empty($this->config_data)) {
      $this->config_data = NULL;
    }

    $this->id = $id;
  }

  public function getModule() {
    return $this->module;
  }

  public function setModule($module) {
    if (!empty($this->config_data)) {
      $this->config_data = NULL;
    }

    if (!module_exists($module) && !drupal_get_path('module', $module)) {
      throw new \Exception("Invalid module: {$module}");
    }

    $this->module = $module;
  }

  public function getPath() {
    return $this->resolver->getPath();
  }

  /**
   * Fetch configuration data.
   */
  private function fetchData() {
    $resolver = $this->resolver;
    $options['cache_id'] = "ATConfig:{$this->module}:{$this->id}";
    $options['ttl'] = '+ 1 year';
    $options['tags'] = array('at-config');

    $this->config_data = at_cache($options, function() use ($resolver) {
      return $resolver->fetchData();
    });
  }

  /**
   * Get configured value by key.
   *
   * @param  string $key Config key.
   * @return mixed
   */
  public function get($key) {
    if (!$this->config_data) {
      $this->fetchData();
    }

    if (!isset($this->config_data[$key])) {
      throw new NotFoundException("{$this->module}.{$this->id}#{$key}");
    }

    return $this->config_data[$key];
  }

  public function set($key, $data) {
    if (!$this->config_data) {
      $this->fetchData();
    }

    $this->config_data[$key] = $data;
  }

  public function getAll() {
    if (!$this->config_data) {
      $this->fetchData();
    }
    return $this->config_data;
  }

  public function setAll($data) {
    $this->config_data = $data;
  }

  public function write() {
    $this->resolver->writeData($this->config_data);
  }
}
