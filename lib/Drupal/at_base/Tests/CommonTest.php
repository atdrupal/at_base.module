<?php

namespace Drupal\at_base\Tests;

/**
 * …
 */
class CommonTest extends \DrupalWebTestCase {
  public static function getInfo() {
    return array(
      'name' => 'AT Base: Basic features',
      'description' => 'Make sure basic features are working correctly.',
      'group' => 'AT Base',
    );
  }

  public function setUp() {
    $this->profile = 'testing';
    parent::setUp('atest_base', 'atest2_base');
  }

  /**
   * Test for at_id() function.
   */
  public function testAtId() {
    $expected = 'Hello Andy Truong';
    $actual = at_id(new \At_Base_Test_Class())->hello('Andy Truong');
    $this->assertEqual($expected, $actual);
  }

  /**
   * Make sure at_modules() function is working correctly.
   */
  public function testAtModules() {
    $this->assertTrue(in_array('atest_base', at_modules()));
    $this->assertTrue(in_array('atest2_base', at_modules('atest_base')));
  }

  /**
   * Module weight can be updated correctly
   */
  public function testWeight() {
    at_base_flush_caches();

    $query = "SELECT weight FROM {system} WHERE name = :name";
    $weight = db_query($query, array(':name' => 'atest_base'))->fetchColumn();

    $this->assertEqual(10, $weight);
  }

  /**
   * Autoload feature
   */
  public function testAutoloader() {
    $this->assertTrue(class_exists('Drupal\at_base\Drush\Command\AtRequire'));
    $this->assertTrue(class_exists('Drupal\at_base\Container'));
  }

  /**
   * Test for \Drupal\at_base\Helper\RealPath class
   */
  public function testRealPath() {
    $expected = path_to_theme() . '/templates/page.home.html.twig';
    $actual = at_container('helper.real_path')->get('%theme/templates/page.home.html.twig');
    $this->assertEqual($expected, $actual);
  }

  /**
   * Test for at_container().
   */
  public function testServiceContainer() {
    // Simple service
    $service_1 = at_container('atest_base.service_1');
    $this->assertEqual('Drupal\atest_base\Service_1', get_class($service_1));

    // Service depends on others
    $service_2 = at_container('atest_base.service_2');
    $this->assertEqual('Drupal\atest_base\Service_2', get_class($service_2));
    $this->assertEqual('Drupal\atest_base\Service_1', get_class($service_2->getService1()));

    // Service use factory
    $service_3 = at_container('atest_base.service_3');
    $this->assertEqual('Drupal\atest_base\Service_3', get_class($service_3));
  }

  /**
   * Test easy block definition.
   */
  public function testEasyBlocks() {
    $block_1 = \AT::twig_string()->render("{{ 'at_base:atest_base|hi_s'  | drupalBlock(TRUE) }}");
    $block_2 = \AT::twig_string()->render("{{ 'at_base:atest_base|hi_t'  | drupalBlock(TRUE) }}");
    $block_3 = \AT::twig_string()->render("{{ 'at_base:atest_base|hi_ts' | drupalBlock(TRUE) }}");

    $expected = 'Hello Andy Truong';
    $this->assertEqual($expected, trim($block_1));
    $this->assertEqual($expected, trim($block_2));
    $this->assertEqual($expected, trim($block_3));
  }

  /**
   * Test ExpressionLanguage.
   */
  public function testExpressionLanguage() {
    $expected = 'Symfony\Component\ExpressionLanguage\ExpressionLanguage';
    $actual = get_class(at_container('expression_language'));
    $this->assertEqual($expected, $actual);

    $expected = 3;
    $actual = at_container('expression_language')->evaluate("constant('MENU_CONTEXT_PAGE') | constant('MENU_CONTEXT_INLINE')");
    $this->assertEqual($expected, $actual);
  }

  /**
   * Test Tagged Service.
   */
  public function testTaggedService() {
    // Test find services has an existent tag 'foo'.
    $expected = array('Drupal\atest_base\Tagged_Service_1', 'Drupal\atest_base\Tagged_Service_2');
    $actual = array();
    $services = at_id(new \Drupal\at_base\Container())->findTaggedServices(array('foo'));
    foreach ($services as $service_name => $service) {
      $actual[] = get_class($service);
    }
    $this->assertEqual($expected, $actual);

    // Test find services has an existent tag 'bar' and an un-existent tag 'baz'.
    $expected = array();
    $actual = array();
    $services = at_id(new \Drupal\at_base\Container())->findTaggedServices(array('bar', 'baz'));
    foreach ($services as $service_name => $service) {
      $actual[] = get_class($service);
    }
    $this->assertEqual($expected, $actual);

    // Test find services has an un-existent tag 'baz'.
    $expected = array();
    $services = at_id(new \Drupal\at_base\Container())->findTaggedServices(array('baz'));
    $this->assertEqual($expected, $services);

    // Find services has two existent tags 'foo' and 'bar'.
    $expected = array('Drupal\atest_base\Tagged_Service_1');
    $actual = array();
    $services = at_id(new \Drupal\at_base\Container())->findTaggedServices(array('foo', 'bar'));
    foreach ($services as $service_name => $service) {
      $actual[] = get_class($service);
    }
    $this->assertEqual($expected, $actual);

    // Find services has two existent tags 'bar' and 'foo'.
    $expected = array('Drupal\atest_base\Tagged_Service_1');
    $actual = array();
    $services = at_id(new \Drupal\at_base\Container())->findTaggedServices(array('bar', 'foo'));
    foreach ($services as $service_name => $service) {
      $actual[] = get_class($service);
    }
    $this->assertEqual($expected, $actual);

    // Find services has two existent tags 'foo' or 'bar'.
    $expected = array('Drupal\atest_base\Tagged_Service_1', 'Drupal\atest_base\Tagged_Service_2', 'Drupal\atest_base\Tagged_Service_3');
    $actual = array();
    $services = at_id(new \Drupal\at_base\Container())->findTaggedServices(array('foo', 'bar'), 'or');
    foreach ($services as $service_name => $service) {
      $actual[] = get_class($service);
    }
    $this->assertEqual($expected, $actual);

    // Find services has an existent tag 'foo' or an un-existent tag 'baz'.
    $expected = array('Drupal\atest_base\Tagged_Service_1', 'Drupal\atest_base\Tagged_Service_2');
    $actual = array();
    $services = at_id(new \Drupal\at_base\Container())->findTaggedServices(array('foo', 'baz'), 'or');
    foreach ($services as $service_name => $service) {
      $actual[] = get_class($service);
    }
    $this->assertEqual($expected, $actual);

    // @todo -Need to work out the behaviour when we pass empty array to tags,
    // with custom operator ('and' and 'or').
//    $expected = array();
//    $actual = at_id(new \Drupal\at_base\Container())->findTaggedServices();
//    $this->assertEqual($expected, $actual);
  }
}
