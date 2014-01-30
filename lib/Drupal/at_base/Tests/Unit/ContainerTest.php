<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

/**
 * Test service container
 *
 *  drush test-run --dirty 'Drupal\at_base\Tests\Unit\ContainerTest'
 */
class ContainerTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Container') + parent::getInfo();
  }

  /**
   * Test for at_container().
   */
  public function testServiceContainer() {
    // Simple service
    $service_1 = at_container('helper.content_render');
    $this->assertEqual('Drupal\at_base\Helper\Content_Render', get_class($service_1));

    // Service with factory
    $service_2 = at_container('twig.core');
    $this->assertEqual('Twig_Environment', get_class($service_2));

    // Service depends on others
    $service_3 = at_container('twig_string');
    $this->assertEqual('Twig_Environment', get_class($service_3));

    $service_4 = at_container('twig');
    $this->assertEqual('Twig_Environment', get_class($service_4));
  }

  public function testIncludingFile() {
    $service = at_container('atest_base.include_me');
    $this->assertEqual('ATest_Base_Include_Me', get_class($service));
  }

  public function testDynamicArguments() {
    $service = at_container('atest_base.dynamic_arguments');
    $this->assertEqual('Drupal\atest_base\Dynamic_Arguments', get_class($service));
    $this->assertEqual('atest_base', $service->getDynParam());
    $this->assertEqual('Drupal\atest_base\Service_1', get_class($service->getDynService()));
  }

  public function testAutoloadPSR0() {
    $service = at_container('atest_base.psr0_me');
    $this->assertEqual('Drupal\atest_base\PSR0Me', get_class($service));
  }

  public function testAutoloadPSR4() {
    $service = at_container('atest_base.psr04_me');
    $this->assertEqual('Drupal\atest_base\PSR4Me', get_class($service));
  }

  public function testTaggedServices() {
    // With weight
    $expected = array('cache.warmer.view', 'cache.warmer.entity', 'cache.warmer.simple');
    $actual = at_container('container')->find('cache.warmer');
    $this->assertEqual(implode(', ', $expected), implode(', ', $actual));

    // Return services instead of services name
    foreach (at_container('container')->find('cache.warmer', $return = 'service') as $name => $service) {
      $expected = get_class(at_container($name));
      $actual = get_class($service);
      $this->assertEqual($expected, $actual);
    }
  }
}
