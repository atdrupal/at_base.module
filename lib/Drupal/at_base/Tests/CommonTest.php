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
