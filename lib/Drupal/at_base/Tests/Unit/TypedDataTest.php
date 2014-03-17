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

  public function testAnyType() {
    $def = array('type' => 'any');

    $input = array();
    $input[] = NULL;
    $input[] = 'String';
    $input[] = array('Array Input');
    foreach ($input as $in) {
      $data = at_data($def, $in);
      $this->assertTrue($data->validate());
      $this->assertEqual($in, $data->getValue());
    }
  }

  public function testBooleanType() {
    $def = array('type' => 'boolean');

    $data = at_data($def, TRUE);
    $this->assertTrue($data->validate());
    $this->assertTrue($data->getValue());
    $this->assertFalse($data->isEmpty());

    $data = at_data($def, FALSE);
    $this->assertTrue($data->validate());
    $this->assertFalse($data->getValue());
    $this->assertTrue($data->isEmpty());

    $data = at_data($def, 'I am string');
    $this->assertFalse($data->validate());
    $this->assertNull($data->getValue());
  }

  public function testStringType() {
    $def = array('type' => 'string');

    $data = at_data($def, 'I am string');
    $this->assertTrue($data->validate());
    $this->assertTrue($data->getValue());
    $this->assertFalse($data->isEmpty());

    $data = at_data($def, '');
    $this->assertTrue($data->validate());
    $this->assertEqual('', $data->getValue());
    $this->assertTrue($data->isEmpty());

    $data = at_data($def, array('I am array'));
    $this->assertFalse($data->validate());
    $this->assertNull($data->getValue());
  }

  public function testIntegerType() {
    $def = array('type' => 'integer');

    $data = at_data($def, 1);
    $this->assertTrue($data->validate());
    $this->assertEqual(1, $data->getValue());
    $this->assertFalse($data->isEmpty());

    $data = at_data($def, -1);
    $this->assertTrue($data->validate());
    $this->assertEqual(-1, $data->getValue());
    $this->assertFalse($data->isEmpty());

    $data = at_data($def, 0);
    $this->assertTrue($data->validate());
    $this->assertEqual(0, $data->getValue());
    $this->assertTrue($data->isEmpty());

    $data = at_data($def, 'I am string');
    $this->assertFalse($data->validate());
    $this->assertNull($data->getValue());
  }

  public function testFunctionType() {
    $def = array('type' => 'function');

    $data = at_data($def, 't');
    $this->assertTrue($data->validate());
    $this->assertEqual('t', $data->getValue());

    $data = at_data($def, 'this_is_invalid_function');
    $this->assertFalse($data->validate($error));
    $this->assertEqual('Function does not exist.', $error);

    $data = at_data($def, array('I am array'));
    $this->assertFalse($data->validate($error));
    $this->assertEqual('Function name must be a string.', $error);
  }

  public function testConstantType() {
    $def = array('type' => 'constant');

    $data = at_data($def, 'MENU_DEFAULT_LOCAL_TASK');
    $this->assertTrue($data->validate($error));
    $this->assertEqual(constant('MENU_DEFAULT_LOCAL_TASK'), $data->getValue());

    $data = at_data($def, 'NON_VALID_CONSTANT_THIS_IS');
    $this->assertFalse($data->validate($error));
    $this->assertEqual('Constant is not defined.', $error);
    $this->assertNull($data->getValue());

    $data = at_data($def, 'in valid ^^');
    $this->assertFalse($data->validate($error));

    $data = at_data($def, array('also', 'invalidate'));
    $this->assertFalse($data->validate($error));
  }

  public function testListType() {
    $def = array('type' => 'list');

    $input = array();
    $input[] = array(NULL, TRUE, 1, 'one', array('one'), at_id(new \stdClass()));
    $input[] = array('One', 'Two', 'Three');
    foreach ($input as $in) {
      $data = at_data($def, $in);
      $this->assertTrue($data->validate($error));
      $this->assertEqual($in, $data->getValue());
    }
  }

  public function testMapping() {
  }
}
