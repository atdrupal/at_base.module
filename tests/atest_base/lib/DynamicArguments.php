<?php
namespace Drupal\atest_base;

class DynamicArguments {
  private $dyn_param;
  private $dyn_service;

  public function __construct($dyn_param, $dyn_service) {
    $this->dyn_param = $dyn_param;
    $this->dyn_service = $dyn_service;
  }

  public function getDynParam() {
    return $this->dyn_param;
  }

  public function getDynService() {
    return $this->dyn_service;
  }
}
