<?php
namespace Drupal\at_base\Config;

interface ResolverInterface {

  /**
   * @param Config $config
   *
   * @return void
   */
  public function setConfig($config);

  /**
   * @return string|false
   */
  public function getPath();
  public function fetchData();
}
