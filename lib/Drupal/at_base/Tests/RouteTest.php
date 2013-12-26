<?php

namespace Drupal\at_base\Tests;

/**
 * …
 */
class RouteTest extends \DrupalWebTestCase {
  public function getInfo() {
    return array(
      'name' => 'AT Base: Route',
      'description' => 'Make sure the routing feature is working correctly.',
      'group' => 'AT Base',
    );
  }

  public function setUp() {
    $this->profile = 'testing';
    parent::setUp('atest_route');
  }

  public function testRoutes() {
    # Test /atest_route/drupal
    $output = menu_execute_active_handler('atest_route/drupal', FALSE);
    $this->assertEqual('Hello Andy Truong', $output);

    # Test /atest_route/controller
    $output = menu_execute_active_handler('atest_route/controller', FALSE);
    $this->assertEqual('Hi Andy Truong!', $output);

    # Test /atest_route/template
    $output = menu_execute_active_handler('atest_route/template', FALSE);
    $output = trim(render($output));
    $this->assertEqual('Hello Andy Truong', $output);

    # Test /atest_route/fancy_template/%user
    $response = at_id(new \Drupal\at_base\Helper\SubRequest('atest_route/fancy_template/1'))->request();
    $this->assertTrue(strpos($response, 'Foo: bar'));
    $this->assertTrue(strpos($response, 'User ID: 1'));

    # Test /atest_route/with_assets
    $response = at_id(new \Drupal\at_base\Helper\SubRequest('atest_route/with_assets'))->request();
    $this->assertTrue(in_array('misc/vertical-tabs.css', $response['#attached']['css']));
    $this->assertTrue(in_array('misc/vertical-tabs.js', $response['#attached']['js']));
    $this->assertTrue(in_array(array('system', 'jquery.bbq'), $response['#attached']['library']));
  }
}
