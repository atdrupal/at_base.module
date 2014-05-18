<?php

namespace Drupal\at_base\Tests\Web;

/**
 * Test cases for routing feature.
 *
 * drush test-run --dirty 'Drupal\at_base\Tests\Web\RouteTest'
 */
class ContentRenderConditionsSysArgsTest extends \DrupalWebTestCase {
  public function getInfo() {
    return array(
      'name' => 'AT Unit: Test content render conditions system arguments',
      'description' => 'Make sure the render conditions feature with system arguments is working correctly.',
      'group' => 'AT Web',
    );
  }

  public function setUp() {
    $this->profile = 'testing';
    parent::setUp('atest_base');
  }

  public function testSystemArguments() {
    // Create new node type.
    $test_node_type = array(
      'type' => 'test',
      'name' => t('Test'),
    );
    $content_type = node_type_set_defaults($test_node_type);
    node_type_save($content_type);

    // Create node.
    $node = new \stdClass();
    $node->title = "Render me";
    $node->type = "test";
    $node->language = LANGUAGE_NONE;
    $node->status = 1;
    node_save($node);

    $build = node_view($node, 'full', LANGUAGE_NONE);
    $this->assertEqual('Hello Render me', $build['at_base']['#markup']);

    $node->title = 'Dont render me';
    node_save($node);

    $build = node_view($node, 'full', LANGUAGE_NONE);
    $this->assertEqual(FALSE, isset($build['at_base']));
  }
}
