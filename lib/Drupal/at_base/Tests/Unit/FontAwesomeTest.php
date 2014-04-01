<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

/**
 * drush test-run --dirty 'Drupal\at_base\Tests\Unit\FontAwesomeTest'
 */
class FontAwesomeTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Icon Fontawesome') + parent::getInfo();
  }

  public function testIconRendering() {
    $css_code = 'camera-retro';

    \at_fake::drupal_add_css(function($data = NULL, $options = NULL) {
      static $included = array();

      if (!is_null($data)) {
        $included[$data] = TRUE;
      }

      return $included;
    });

    $expected = '<i class="fa fa-'. $css_code .'"></i>';
    $actual = at_icon($css_code, 'icon.fontawesome');
    $included_css = \at_fn::drupal_add_css();

    $this->assertEqual($expected, $actual, 'at_icon() returns the the correct html for icon.');
    $this->assertTrue(isset($included_css[at_library('fontawesome', NULL, FALSE) . 'css/font-awesome.css']), "fontawesome's css is included to page.");
  }
}
