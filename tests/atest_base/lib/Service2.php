<?php

namespace Drupal\atest_base;

class Service2 {

  private $service_1;

  public function __construct($service_1) {
    $this->service_1 = $service_1;
  }

  public function getService1() {
    return $this->service_1;
  }

}
