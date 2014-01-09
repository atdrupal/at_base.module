<?php

namespace Drupal\at_base\Tests;

/**
 * cache_get()/cache_set() does not work on unit test cases.
 */
class CacheTest extends \DrupalWebTestCase {
  public function getInfo() {
    return array(
      'name' => 'AT Base: Cache',
      'description' => 'Make sure the at_cache() is working correctly.',
      'group' => 'AT Base'
    );
  }

  public function setUp() {
    $this->profile = 'testing';
    parent::setUp('at_base');
  }

  /**
   * Helper method for testObjectCallback().
   * @return int
   */
  public static function time() {
    return time();
  }

  /**
   * Test cache function.
   */
  public function testCacheUsages() {
    $cache_options = array('bin' => 'cache', 'reset' => FALSE, 'ttl' => '+ 15 minutes');

    $callbacks['closure']    = array(function () { return time(); }, array());
    $callbacks['string']     = array('time', array());
    $callbacks['object']     = array(array($this, 'time'), array());
    $callbacks['static']     = array('\Drupal\at_base\Tests\CacheTest::time', array());
    $callbacks['arguments']  = array('sprintf', array('Timestamp: %d', time()));
    foreach ($callbacks as $type => $callback) {
      list($callback, $arguments) = $callback;
      $options = array('id' => "at_test:time:{$type}", 'reset' => TRUE) + $cache_options;

      // Init the value
      $output_1 = at_cache($options, $callback, $arguments);
      sleep(2);

      // Call at_cache() again
      $output_2 = at_cache(array('reset' => FALSE) + $options, $callback, $arguments);

      // The value should be same — it's cached.
      $this->assertEqual($output_1, $output_2);
    }

    // ---------------------
    // Allow empty
    // ---------------------
    $options = array('id' => 'at_test:time:allowEmpty', 'reset' => TRUE, 'allow_empty' => FALSE) + $cache_options;

    // Init the value
    $time_1 = at_cache($options, '\Drupal\at_base\Tests\CacheTest::time');
    sleep(2);

    // Change cached-data to empty string
    cache_set($options['id'], '', $options['bin'], strtotime($options['ttl']));

    // Call at_cache() again
    $time_2 = at_cache(array('reset' => FALSE) + $options, '\Drupal\at_base\Tests\CacheTest::time');

    // The value should not be same
    $this->assertNotEqual($time_1, $time_2);

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

  /**
   * @todo Test when we can fake the service.
   */
  public function testCacheWarming() {
    // Fake the cache.tag_flusher service

    // Warmer > Simple

    // Warmer > Entity

    // Warmer > Views

    // Warmer service
  }
}
