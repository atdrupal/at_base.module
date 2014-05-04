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
   * Test for atc().
   */
  public function testServiceContainer() {
    // Simple service
    $service_1 = atcg('helper.content_render');
    $this->assertEqual('Drupal\at_base\Helper\ContentRender', get_class($service_1));

    // Service with factory
    $service_2 = atcg('twig.core');
    $this->assertEqual('Twig_Environment', get_class($service_2));

    // Service depends on others
    $service_3 = atcg('twig_string');
    $this->assertEqual('Twig_Environment', get_class($service_3));

    $service_4 = atcg('twig');
    $this->assertEqual('Twig_Environment', get_class($service_4));
  }

  public function testIncludingFile() {
    $service = atcg('atest_base.include_me');
    $this->assertEqual('ATest_Base_Include_Me', get_class($service));
  }

  public function testDynamicArguments() {
    $service = atcg('atest_base.dynamic_arguments');
    $this->assertEqual('Drupal\atest_base\DynamicArguments', get_class($service));
    $this->assertEqual('atest_base', $service->getDynParam());
    $this->assertEqual('Drupal\atest_base\Service1', get_class($service->getDynService()));
  }

  public function testTaggedServices() {
    // With weight
    $expected = array('cache.warmer.view', 'cache.warmer.entity', 'cache.warmer.simple');
    $actual = atc()->find('cache.warmer');
    $this->assertEqual(implode(', ', $expected), implode(', ', $actual));

    // Return services instead of services name
    foreach (atc()->find('cache.warmer', $return = 'service') as $name => $service) {
      $expected = get_class(atcg($name));
      $actual = get_class($service);
      $this->assertEqual($expected, $actual);
    }
  }
}
