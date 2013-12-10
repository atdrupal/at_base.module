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
    @unset($this->config_data);
    $this->id = $id;
  }

  public function getModule() {
    return $this->module;
  }

  public function setModule($module) {
    @unset($this->config_data);

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
    $this->config_data = $resolver->fetchData();
    return;

    $options['ttl'] = '+ 1 year';
    $options['cache_id'] = "at_config:data:{$id}";
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

  public function getAll() {
    if (!$this->config_data) {
      $this->fetchData();
    }
    return $this->config_data;
  }
}
