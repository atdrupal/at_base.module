<?php

namespace Drupal\at_base\Tests;

class ConfigTest extends \DrupalWebTestCase {
  public function getInfo() {
    return array(
      'name' => 'AT Base: Config',
      'description' => 'Make sure the at_config() is working correctly.',
      'group' => 'AT Base',
    );
  }

  public function setUp() {
    $this->profile = 'testing';
    parent::setUp('atest_config', 'atest_base', 'atest2_base');
  }

  /**
   * Make sure at_modules() function is working correctly.
   */
  public function testAtModules() {
    $this->assertTrue(in_array('atest_base', at_modules()));
    $this->assertTrue(in_array('atest2_base', at_modules('atest_base')));
  }
}
