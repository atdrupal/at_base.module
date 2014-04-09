<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

class ContentRenderConditionsTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Test content render conditions') + parent::getInfo();
  }

  public function setUp() {
    parent::setUp();
    $this->render = at_container('helper.content_render');
  }

  public function testNoConditions() {
    // String without conditions.
    $string = 'Hello Andy Truong';
    $output = $this->render->render($string);
    $this->assertEqual($string, $output);

    // Function without conditions.
    $output = $this->render->render(array('function' => 'atest_base_hello'));
    $this->assertEqual('Hello Andy Truong', $output);

    // Function with empty conditions.
    $output = $this->render->render(array(
      'conditions' => array(),
      'function' => 'atest_base_hello'
    ));
    $this->assertEqual('Hello Andy Truong', $output);

    // Function with empty condition callbacks.
    $output = $this->render->render(array(
      'conditions' => array(
        'type' => 'and',
        'callbacks' => array()
      ),
      'function' => 'atest_base_hello'
    ));
    $this->assertEqual('Hello Andy Truong', $output);

    // Function with empty condition callbacks and type not.
    $output = $this->render->render(array(
      'conditions' => array(
        'type' => 'not',
        'callbacks' => array()
      ),
      'function' => 'atest_base_hello'
    ));
    $this->assertEqual(NULL, $output);
  }
  
  private function _getOutput($type, $value1, $value2) {
    $output = $this->render->render(array(
      'conditions' => array(
        'type' => $type,
        'callbacks' => array(
          array(
            'atest_base_content_render_condition_callback',
            $value1 ? array('not empty') : array()
          ),
          array(
            'atest_base_content_render_condition_callback',
            $value2 ? array('not empty') : array()
          ),
        )
      ),
      'function' => 'atest_base_hello'
    ));
    return $output;
  }

  public function testInvalidConditionType() {
    $output = $this->_getOutput('any_type', TRUE, TRUE);
    $this->assertEqual('Hello Andy Truong', $output);
  }

  public function testAnd() {
    $output = $this->_getOutput('and', TRUE, TRUE);
    $this->assertEqual('Hello Andy Truong', $output);

    $output = $this->_getOutput('and', TRUE, FALSE);
    $this->assertEqual(NULL, $output);

    $output = $this->_getOutput('and', FALSE, TRUE);
    $this->assertEqual(NULL, $output);

    $output = $this->_getOutput('and', FALSE, FALSE);
    $this->assertEqual(NULL, $output);
  }

  public function testOr() {
    $output = $this->_getOutput('or', TRUE, TRUE);
    $this->assertEqual('Hello Andy Truong', $output);

    $output = $this->_getOutput('or', TRUE, FALSE);
    $this->assertEqual('Hello Andy Truong', $output);

    $output = $this->_getOutput('or', FALSE, TRUE);
    $this->assertEqual('Hello Andy Truong', $output);

    $output = $this->_getOutput('or', FALSE, FALSE);
    $this->assertEqual(NULL, $output);
  }

  public function testXor() {
    $output = $this->_getOutput('xor', TRUE, TRUE);
    $this->assertEqual(NULL, $output);

    $output = $this->_getOutput('xor', TRUE, FALSE);
    $this->assertEqual('Hello Andy Truong', $output);

    $output = $this->_getOutput('xor', FALSE, TRUE);
    $this->assertEqual('Hello Andy Truong', $output);

    $output = $this->_getOutput('xor', FALSE, FALSE);
    $this->assertEqual(NULL, $output);
  }

  // Not-and actually.
  public function testNot() {
    $output = $this->_getOutput('not', TRUE, TRUE);
    $this->assertEqual(NULL, $output);

    $output = $this->_getOutput('not', TRUE, FALSE);
    $this->assertEqual('Hello Andy Truong', $output);

    $output = $this->_getOutput('not', FALSE, TRUE);
    $this->assertEqual('Hello Andy Truong', $output);

    $output = $this->_getOutput('not', FALSE, FALSE);
    $this->assertEqual('Hello Andy Truong', $output);
  }
}
