<?php

namespace Drupal\at_base\Tests;

/**
 * Test cases for basic/simple features.
 */
class CommonTest extends \DrupalWebTestCase {
  public function getInfo() {
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

  public function testControllerRevoler() {
    $resolver = at_container('controller.resolver');

    // Case 1: array
    $obj = new \At_Base_Test_Class();
    $definition = array($obj, 'foo');
    $exptected = array($obj, 'foo');
    $actual = $resolver->get($definition);
    $this->assertEqual($exptected, $actual);

    // Case 2: $foo::__invoke()
    $definition = $obj;
    $exptected = $obj;
    $actual = $resolver->get($definition);
    $this->assertEqual($exptected, $actual);

    // Case 3: class::method
    $definition = 'At_Base_Test_Class::foo';
    $exptected = array('At_Base_Test_Class', 'foo');
    $actual = $resolver->get($definition);
    $this->assertEqual($exptected, $actual);

    // Case 4: Twig template
    $definition = "{{ 'Hello ' ~ 'Andy Truong' }}";
    $exptected = 'Hello Andy Truong';
    $actual = $resolver->get($definition);
    $actual = trim(call_user_func($actual));
    $this->assertEqual($exptected, $actual);

    // Case 5: Simple function
    $definition = 'time';
    $exptected = 'time';
    $actual = $resolver->get($definition);
    $this->assertEqual($exptected, $actual);

    // Case 6: Simple class with __invoke magic method
    $definition = 'At_Base_Test_Class';
    $exptected = 'At_Base_Test_Class';
    $actual = $resolver->get($definition);
    $this->assertEqual($exptected, get_class($actual));
  }
}
