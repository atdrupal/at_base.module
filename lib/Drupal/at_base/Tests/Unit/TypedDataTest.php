<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

/**
 * drush test-run --dirty 'Drupal\at_base\Tests\Unit\TypedDataTest'
 */
class TypedDataTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: TypedData') + parent::getInfo();
  }

  public function testBooleanType() {
    $def = array('type' => 'boolean');

    $data = at_data($def, TRUE);
    $this->assertTrue($data->validate());
    $this->assertTrue($data->getValue());

    $data = at_data($def, FALSE);
    $this->assertTrue($data->validate());
    $this->assertFalse($data->getValue());

    $data = at_data($def, 'I am string');
    $this->assertFalse($data->validate());
    $this->assertNull($data->getValue());
  }

  public function testStringValue() {
    $def = array('type' => 'string');
  }
}
