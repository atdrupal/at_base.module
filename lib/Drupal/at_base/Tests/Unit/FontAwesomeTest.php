<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

class FontAwesomeTest extends UnitTestCase {

  static public function getInfo() {
    return array('name' => 'AT Unit: Icon Fontawesome') + parent::getInfo();
  }

  public function setUp() {
    parent::setUp();

    drupal_static_reset('fontawesome_library_added');
  }

  public function testHtmlGeneration() {
    $css_code = 'fa-camera-retro';
    $expected_html = '<i class="fa '. $css_code .'"></i>';

    $this->assertEqual($expected_html, at_icon($css_code, 'icon.fontawesome'), 'at_icon return the the right html for icon.');
  }

  public function testCssAdded() {
    $library_added = FALSE;
    $fontawesome_library_path = at_library('fontawesome', NULL, FALSE);

    \at_fake::drupal_add_css(function($data = NULL, $options = NULL) use ($fontawesome_library_path, &$library_added) {
      if ($data == $fontawesome_library_path . 'css/font-awesome.css') {
        $library_added = TRUE;
      }
    });

    at_icon('fa-book', 'icon.fontawesome');
    $this->assertTrue($library_added, "fontawesome's css is added to page.");
  }
}
