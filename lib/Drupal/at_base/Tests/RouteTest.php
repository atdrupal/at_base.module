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
    parent::setUp('atest_route');
  }

  public function testControllerRevoler() {
    $resolver = at_container('controller.resolver');

    // Case 1: array
    $obj = new \At_Base_Test_Class();
    $definition = array($obj, 'foo');
    $expected = array($obj, 'foo');
    $actual = $resolver->get($definition);
    $this->assertEqual($expected, $actual);

    // Case 2: $foo::__invoke()
    $definition = $obj;
    $expected = $obj;
    $actual = $resolver->get($definition);
    $this->assertEqual($expected, $actual);

    // Case 3: class::method
    $definition = 'At_Base_Test_Class::foo';
    $expected = array('At_Base_Test_Class', 'foo');
    $actual = $resolver->get($definition);
    $this->assertEqual($expected, $actual);

    // Case 4: Twig template
    $definition = "{{ 'Hello ' ~ 'Andy Truong' }}";
    $expected = 'Hello Andy Truong';
    $actual = $resolver->get($definition);
    $actual = trim(call_user_func($actual));
    $this->assertEqual($expected, $actual);

    // Case 5: Simple function
    $definition = 'time';
    $expected = 'time';
    $actual = $resolver->get($definition);
    $this->assertEqual($expected, $actual);

    // Case 6: Simple class with __invoke magic method
    $definition = 'At_Base_Test_Class';
    $expected = 'At_Base_Test_Class';
    $actual = $resolver->get($definition);
    $this->assertEqual($expected, get_class($actual));
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
    $this->assertTrue(strpos($response, 'Foo: bar'));
    $this->assertTrue(strpos($response, 'User ID: 1'));

    # ---------------------
    # Test /atest_route/with_assets
    # ---------------------
    $response = at_id(new \Drupal\at_base\Helper\SubRequest('atest_route/with_assets'))->request();
    $this->assertTrue(in_array('misc/vertical-tabs.css', $response['#attached']['css']));
    $this->assertTrue(in_array('misc/vertical-tabs.js', $response['#attached']['js']));
    $this->assertTrue(in_array(array('system', 'jquery.bbq'), $response['#attached']['library']));

    # ---------------------
    # Support caching
    # ---------------------
    // bit of hack, more sure the route is cachable
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $response_0 = trim(at_id(new \Drupal\at_base\Helper\SubRequest('atest_route/cache/1'))->request());
    sleep(1);
    $response_1 = trim(at_id(new \Drupal\at_base\Helper\SubRequest('atest_route/cache/1'))->request());
    $this->assertEqual($response_0, $response_1);
  }
}
