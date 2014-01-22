<?php
namespace Drupal\atest_route\Controller;

class HelloController
{
  public function helloAction($name)
  {
    return "Hi {$name}!";
  }
}
