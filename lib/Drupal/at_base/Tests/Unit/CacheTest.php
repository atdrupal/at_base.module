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

  public function testFakeCacheWrapper() {
    $wrapper = at_container('wrapper.cache');

    // Make sure the cache wrapper is faked correctly
    $this->assertEqual('Drupal\at_base\Helper\Test\Cache', get_class($wrapper));

    // Save 1 then get 1
    $wrapper->set(__FUNCTION__, 1);
    $this->assertEqual(1, $wrapper->get(__FUNCTION__));
  }

  public function testAtCache() {
    $o = array('id' => 'atest:cache:set:1');
    $expected = at_cache($o, function() { return 1; });
    $actual = at_cache($o, function() { return 2; });
    $this->assertEqual($expected, $actual);
  }
}
