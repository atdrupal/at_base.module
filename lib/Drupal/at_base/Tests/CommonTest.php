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

  public function testCacheTagging() {
    $cache_options = array('bin' => 'cache', 'reset' => FALSE, 'ttl' => '+ 15 minutes');

    // ---------------------
    // Cache tagging
    // ---------------------
    $options = array('id' => 'atest_base:cache:tag:1', 'tags' => array('at_base', 'atest')) + $cache_options;

    $data_1 = at_cache($options, function(){ return 'Data #1'; });
    $data_2 = at_cache($options, function(){ return 'This is not called'; });

    // Delete items tagged with 'atest'
    at_cache_flush_by_tags($options['tags']);

    $data_3 = at_cache($options, function(){ return 'Data #3 — must be called.'; });

    $this->assertNotEqual($data_1, $data_3);
  }
}
