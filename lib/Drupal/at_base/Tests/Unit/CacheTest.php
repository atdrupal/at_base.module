<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

/**
 * cache_get()/cache_set() does not work on unit test cases.
 */
class CacheTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Cache') + parent::getInfo();
  }

  /**
   * Helper method for testObjectCallback().
   * @return int
   */
  public static function time() {
    return time();
  }

  public function testFakeCacheWrapper() {
    $wrapper = at_container('wrapper.cache');

    // Make sure the cache wrapper is faked correctly
    $this->assertEqual('Drupal\at_base\Helper\Test\Cache', get_class($wrapper));

    // Save 1 then get 1
    $wrapper->set(__FUNCTION__, 1);
    $this->assertEqual(1, $wrapper->get(__FUNCTION__));
  }

  public function testAtCache() {
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
      sleep(1);

      // Call at_cache() again
      $output_2 = at_cache(array('reset' => FALSE) + $options, $callback, $arguments);

      // The value should be same — it's cached.
      $this->assertEqual($output_1, $output_2);
    }
  }

  public function testAtCacheAllowEmpty() {
    $options = array('bin' => 'cache', 'reset' => FALSE, 'ttl' => '+ 15 minutes');
    $options = array('id' => 'at_test:time:allowEmpty', 'reset' => TRUE, 'allow_empty' => FALSE) + $options;

    // Init the value
    $time_1 = at_cache($options, '\Drupal\at_base\Tests\CacheTest::time');
    sleep(1);

    // Change cached-data to empty string
    at_container('wrapper.cache')->set($options['id'], '', $options['bin'], strtotime($options['ttl']));

    // Call at_cache() again
    $time_2 = at_cache(array('reset' => FALSE) + $options, __CLASS__ . '::time');

    // The value should not be same
    $this->assertNotEqual($time_1, $time_2);
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
