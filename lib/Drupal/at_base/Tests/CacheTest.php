<?php
namespace Drupal\at_base\Tests;

/**
 * cache_get()/cache_set() does not work on unit test cases.
 *
 * Test me:
 *  drush test-run 'Drupal\at_base\Tests\CacheTest'
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
    parent::setUp('at_base');
  }

  /**
   * Helper method for testObjectCallback().
   * @return int
   */
  public static function time() {
    return time();
  }

  public function testCacheUsages() {
    $cache_options = array('bin' => 'cache', 'reset' => FALSE, 'ttl' => '+ 15 minutes');

    // ---------------------
    // Closure
    // ---------------------
    $options = array('id' => 'at_test:time:closure', 'reset' => TRUE) + $cache_options;

    // Init the value
    $time_1 = at_cache($options, function () { return time(); });
    sleep(2);

    // Call at_cache() again
    $time_2 = at_cache(array('reset' => FALSE) + $options, function () { return time(); });

    // The value should be same — it's cached.
    $this->assertEqual($time_1, $time_2);

    // ---------------------
    // String
    // ---------------------
    $options = array('id' => 'at_test:time:string', 'reset' => TRUE) + $cache_options;

    // Init the value
    $time_1 = at_cache($options, 'time');
    sleep(2);

    // Call at_cache() again
    $time_2 = at_cache(array('reset' => FALSE) + $options, 'time');

    // The value should be same — it's cached.
    $this->assertEqual($time_1, $time_2);

    // ---------------------
    // Callback with arguments
    // ---------------------
    $options = array('id' => 'at_test:string:arguments', 'reset' => TRUE) + $cache_options;

    // Init the value
    $string_1 = at_cache($options, 'sprintf', array('Timestamp: %d', time()));
    sleep(2);

    // Call at_cache() again
    $string_2 = at_cache(array('reset' => FALSE) + $options, 'sprintf', array('Timestamp: %d', time()));

    // The value should be same — it's cached.
    $this->assertEqual($string_1, $string_2);

    // ---------------------
    // Static method
    // ---------------------
    $options = array('id' => 'at_test:time:', 'reset' => TRUE) + $cache_options;

    // Init the value
    $time_1 = at_cache($options, '\Drupal\at_base\Tests\CacheTest::time');
    sleep(2);

    // Call at_cache() again
    $time_2 = at_cache(array('reset' => FALSE) + $options, '\Drupal\at_base\Tests\CacheTest::time');

    // The value should be same — it's cached.
    $this->assertEqual($time_1, $time_2);

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
}
