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
    $service_1 = at_container('helper.content_render');
    $this->assertEqual('Drupal\at_base\Helper\ContentRender', get_class($service_1));

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

  public function testTaggedServices() {
    // With weight
    $expected = array('cache.warmer.view', 'cache.warmer.entity', 'cache.warmer.simple');
    $actual = at_container()->findTaggedServiceIds('cache.warmer');

    foreach ($expected as $expected_service_id) {
        $this->assertTrue(isset($actual[$expected_service_id]));
    }
  }
}
