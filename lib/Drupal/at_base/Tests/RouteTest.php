<?php

namespace Drupal\at_base\Tests;

/**
 * Test cases for routing feature.
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
    parent::setUp('atest_base', 'atest_route');
  }

  public function testRoutes() {
    # ---------------------
    # Test /atest_route/drupal
    # ---------------------
    $output = menu_execute_active_handler('atest_route/drupal', FALSE);
    $this->assertEqual('Hello Andy Truong', $output);

    # ---------------------
    # Test /atest_route/controller
    # ---------------------
    $output = menu_execute_active_handler('atest_route/controller', FALSE);
    $this->assertEqual('Hi Andy Truong!', $output);

    # ---------------------
    # Test /atest_route/template
    # ---------------------
    $output = menu_execute_active_handler('atest_route/template', FALSE);
    $output = trim(render($output));
    $this->assertEqual('Hello Andy Truong', $output);

    # ---------------------
    # Test /atest_route/fancy_template/%user
    # ---------------------
    $response = at_id(new \Drupal\at_base\Helper\SubRequest('atest_route/fancy_template/1'))->request();
    $response = render($response);
    $this->assertTrue(strpos($response, 'Foo: bar'));
    $this->assertTrue(strpos($response, 'User ID: 1'));

    # ---------------------
    # Test /atest_route/with_assets
    # ---------------------
    $response = at_id(new \Drupal\at_base\Helper\SubRequest('atest_route/with_assets'))->build();
    $this->assertTrue(in_array('misc/vertical-tabs.css', $response['#attached']['css']));
    $this->assertTrue(in_array('misc/vertical-tabs.js', $response['#attached']['js']));
    $this->assertTrue(in_array(array('system', 'jquery.bbq'), $response['#attached']['library']));

    # ---------------------
    # Support caching
    # ---------------------
    // bit of hack, more sure the route is cachable
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $response_0 = at_id(new \Drupal\at_base\Helper\SubRequest('atest_route/cache/1'))->request();
    $response_0 = trim(render($response_0));
    sleep(1);
    $response_1 = at_id(new \Drupal\at_base\Helper\SubRequest('atest_route/cache/1'))->request();
    $response_1 = trim(render($response_1));
    $this->assertEqual($response_0, $response_1);
  }
}
