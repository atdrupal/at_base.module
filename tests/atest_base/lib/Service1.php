<?php

namespace Drupal\atest_base;

class Service1 {

  public function hello($name = 'Andy Truong') {
    return "Hello {$name}";
  }

  public static function helloStatic($name = 'Andy Truong') {
    return "Hello {$name}";
  }

}
