<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

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

    // Save __FUNCTION__ then get __FUNCTION__
    $wrapper->set(__FUNCTION__, __CLASS__);
    $this->assertEqual(__CLASS__, $wrapper->get(__FUNCTION__)->data);
  }

  public function testAtCache() {
    $cache_options = array('bin' => 'cache', 'reset' => FALSE, 'ttl' => '+ 15 minutes');

    $callbacks['closure']    = array(function () { return time(); }, array());
    $callbacks['string']     = array('time', array());
    $callbacks['object']     = array(array($this, 'time'), array());
    $callbacks['static']     = array(__CLASS__ . '::time', array());
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
    $time_1 = at_cache($options, __CLASS__ . '::time');
    sleep(1);

    // Change cached-data to empty string
    at_container('wrapper.cache')->set($options['id'], '', $options['bin'], strtotime($options['ttl']));

    // Call at_cache() again
    $time_2 = at_cache(array('reset' => FALSE) + $options, __CLASS__ . '::time');

    // The value should not be same
    $this->assertNotEqual($time_1, $time_2);
  }

  public function testCacheTagging() {
    $o = array('bin' => 'cache', 'reset' => FALSE, 'ttl' => '+ 15 minutes');
    $o = array('id' => 'atest_base:cache:tag:1', 'tags' => array('at_base', 'atest')) + $o;

    // ---------------------------------------------------------------
    // Tag must be written when cache with tag(s)
    // ---------------------------------------------------------------
    at_cache($o, function(){ return 'Data #1'; });

    $db_log = at_container('wrapper.db')->getLog();
    $tag1_row = array('bin' => 'cache', 'cid' => $o['id'], 'tag' => $o['tags'][0]);
    $tag2_row = array('bin' => 'cache', 'cid' => $o['id'], 'tag' => $o['tags'][1]);

    $this->assertEqual($tag1_row, $db_log['insert']['at_base_cache_tag']['fields'][0][0]);
    $this->assertEqual($tag2_row, $db_log['insert']['at_base_cache_tag']['fields'][1][0]);

    at_container('wrapper.db')->resetLog();

    // ---------------------
    // Tag must be deleted
    // ---------------------
    // Delete items tagged with 'atest'
    at_container('cache.tag_flusher')->setTags($o['tags'])->flush();

    $db_log = at_container('wrapper.db')->getLog();
    $con = array('tag', $o['tags']);
    foreach ($db_log['delete']['at_base_cache_tag']['condition'] as $_con) {
      if ('tag' === $_con[0]) {
        $this->assertEqual($con, $_con);
        return;
      }
    }
    $this->assertTrue(FALSE, 'No delete query on tags found');
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
