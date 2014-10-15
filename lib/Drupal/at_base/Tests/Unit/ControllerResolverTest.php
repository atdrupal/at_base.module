<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

/**
 * drush test-run --dirty 'Drupal\at_base\Tests\Unit\ControllerResolverTest'
 */
class ControllerResolverTest extends UnitTestCase {

  private $resolver;

  public function getInfo() {
    return array('name' => 'AT Unit: Controller Resolver') + parent::getInfo();
  }

  public function setUp() {
    $this->resolver = at_container('helper.controller.resolver');
    parent::setUp();
  }

  public function testObjectMethodPair() {
    $obj = new \At_Base_Test_Class();
    $definition = array($obj, 'foo');
    $expected = array($obj, 'foo');
    $actual = $this->resolver->get($definition);
    $this->assertEqual($expected, $actual);
  }

  public function testStaticMethod() {
    $definition = 'At_Base_Test_Class::foo';
    $expected = array('At_Base_Test_Class', 'foo');
    $actual = $this->resolver->get($definition);
    $this->assertEqual($expected, $actual);
  }

  public function testTwigTemplate() {
    $definition = "{{ 'Hello ' ~ 'Andy Truong' }}";
    $expected = 'Hello Andy Truong';
    $actual = $this->resolver->get($definition);
    $actual = trim(call_user_func($actual));
    $this->assertEqual($expected, $actual);
  }

  public function testFunctionString() {
    $definition = 'time';
    $expected = 'time';
    $actual = $this->resolver->get($definition);
    $this->assertEqual($expected, $actual);
  }

  public function testObjectInvoke() {
    $obj = new \At_Base_Test_Class();
    $definition = $obj;
    $expected = $obj;
    $actual = $this->resolver->get($definition);
    $this->assertEqual($expected, $actual);
  }

  public function testClassStringInvoke() {
    $definition = 'At_Base_Test_Class';
    $expected = 'At_Base_Test_Class';
    $actual = $this->resolver->get($definition);
    $this->assertEqual($expected, get_class($actual));
  }

}
