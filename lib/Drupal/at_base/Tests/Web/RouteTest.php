<?php

namespace Drupal\at_base\Tests\Web;

/**
 * Test cases for routing feature.
 *
 * drush test-run --dirty 'Drupal\at_base\Tests\Web\RouteTest'
 */
class RouteTest extends \DrupalWebTestCase {
  public function getInfo() {
    return array(
      'name' => 'AT Base: Route',
      'description' => 'Make sure the routing feature is working correctly.',
      'group' => 'AT Web',
    );
  }

  public function setUp() {
    $this->profile = 'testing';
    parent::setUp('atest_base', 'atest_route');
  }

  public function testRoutes() {
    $request = new \Drupal\at_base\Helper\SubRequest();

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
    # Test /atest_route/multiple-template
    # ---------------------
    $output = menu_execute_active_handler('atest_route/multiple-template', FALSE);
    $output = trim(render($output));
    $this->assertEqual('Hello Andy Truong', $output);

    # ---------------------
    # Test /atest_route/fancy_template/%user
    # ---------------------
    $response = $request->request('atest_route/fancy_template/1');
    $this->assertTrue(strpos($response, 'Foo: bar'));
    $this->assertTrue(strpos($response, 'User ID: 1'));

    # ---------------------
    # Test /atest_route/with_assets
    # ---------------------
    $response = $request->request('atest_route/with_assets');
    $this->assertTrue(in_array('misc/vertical-tabs.css', $response['#attached']['css']));
    $this->assertTrue(in_array('misc/vertical-tabs.js', $response['#attached']['js']));
    $this->assertTrue(in_array(array('system', 'jquery.bbq'), $response['#attached']['library']));

    # ---------------------
    # Support caching
    # ---------------------
    // bit of hack, more sure the route is cachable
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $response_0 = trim($request->request('atest_route/cache/1'));
    sleep(1);
    $response_1 = trim($request->request('atest_route/cache/1'));
    $this->assertEqual($response_0, $response_1);
  }
}
