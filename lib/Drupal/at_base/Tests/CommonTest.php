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
