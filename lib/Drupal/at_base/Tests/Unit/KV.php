<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;
use Drupal\at_base\Helper\Test\Cache;

class KV extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Key-Value storage') + parent::getInfo();
  }

  public function testServices() {
    $defs = at_config('at_base', 'services')->get('services');
    foreach ($defs as $name => $def) {
      at_container($name);
      $this->assertTrue(TRUE, "Service {$name} created successfull.");
    }
  }

  public function testArrayStorage() {
    $kv = at_kv('foo', array(), 'array');
    $kv->save('name', 'at_base');

    $this->assertEqual('at_base', $kv->fetch('name'));
  }
}
