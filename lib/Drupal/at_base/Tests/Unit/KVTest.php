<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;
use Drupal\at_base\Helper\Test\Cache;

/**
 * Test case for Key-Value storage
 *
 *    drush test-run --dirty 'Drupal\at_base\Tests\Unit\KVTest'
 */
class KVTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Key-Value storage') + parent::getInfo();
  }

  private function getKV($collection = 'atest') {
    return at_container('kv', $collection);
  }

  private function getKVExpirable($collection = 'atest') {
    return at_container('kv.expirable', $collection);
  }

  public function testInfo() {
    $kv = $this->getKV('atest');
    $this->assertEqual('atest', $kv->getCollectionName());
    unset($kv);

    $kv = $this->getKVExpirable('atest');
    $this->assertEqual('atest', $kv->getCollectionName());
    unset($kv);

    $kv = $this->getKV('atest_other');
    $this->assertNotEqual('atest', $kv->getCollectionName());
    unset($kv);

    $kv = $this->getKVExpirable('atest_other');
    $this->assertNotEqual('atest', $kv->getCollectionName());
    unset($kv);
  }

  public function testSetGetDelete() {
    $kv = $this->getKV();

    // String
    $kv->set('first_name', 'Andy');
    $kv->setIfNotExists('first_name', 'Hong');
    $kv->setIfNotExists('last_name', 'Truong');
    $this->assertEqual('Andy', $kv->get('first_name'));
    $this->assertEqual('Truong', $kv->get('last_name'));
    $kv->delete('first_name');
    $kv->delete('last_name');
    $this->assertNull($kv->get('first_name'));
    $this->assertNull($kv->get('last_name'));

    // Array
    $name = array('Andy', 'Truong');
    $kv->set('name', $name);
    $this->assertEqual($name, $kv->get('name'));
    $kv->delete('name');
    $this->assertNull($kv->get('name'));
  }

  public function testSetGetDeleteMultiple() {
    $kv = $this->getKV();

    $kv->setMultiple(array('first_name' => 'Andy', 'last_name' => 'Truong'));
    list($first_name, $last_name) = $kv->getMultiple(array('first_name', 'last_name'));
    $this->assertEqual('Andy', $first_name);
    $this->assertEqual('Truong', $last_name);
  }

  public function testGetDeleteAll() {
    $kv = $this->getKV();

    // Clean everything
    $kv->deleteAll();

    // Check getAll()
    $kv->setMultiple(array('first_name' => 'Andy', 'last_name' => 'Truong'));
    list($first_name, $last_name) = $kv->getAll();
    $this->assertEqual('Andy', $first_name);
    $this->assertEqual('Truong', $last_name);

    // Check deleteAll()
    $kv->deleteAll();
    $this->assertNull($kv->get('first_name'));
    $this->assertNull($kv->get('last_name'));
  }

  public function testExpirable() {
    $kv = $this->getKVExpirable();

    // Change
    $time = time();

    // Setup value now $time
    at_fn_fake('time', function() use ($time) { return $time; });
    $kv->setWithExpire('minute', date('m'), 60);
    $this->assertEqual(60, $kv->get('minute'));

    // $time after 61 seconds
    at_fn_fake('time', function() use ($time) { return $time; });
    $this->assertNull($kv->get('minute'));

    // Back to $time
    at_fn_fake('time', function() use ($time) { return $time; });
    $kv->setMultipleWithExpire(array('hour' => 3600, 'day' => 86400), 60);
    $this->assertEqual(3600,  $kv->get('hour'));
    $this->assertEqual(86400, $kv->get('day'));

    // $time after 61 seconds
    $this->assertNull($kv->get('hour'));
    $this->assertNull($kv->get('day'));
  }
}
