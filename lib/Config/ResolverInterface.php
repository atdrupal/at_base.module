<?php
namespace Drupal\at_base\Config;

interface ResolverInterface {
  public function setConfig($config);
  public function getPath();
  public function fetchData();
}
