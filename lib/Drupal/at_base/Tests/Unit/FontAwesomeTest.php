<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

class FontAwesomeTest extends UnitTestCase {
  private $service;

  static public function getInfo() {
    return array('name' => 'AT Unit: Icon Fontawesome') + parent::getInfo();
  }

  public function setUp() {
    parent::setUp();
    $this->service = at_container('icon.fontawesome');
  }

  protected function setUpModules() {
    parent::setUpModules();
  }

  public function testGenerate() {
    $css_code = 'fa-camera-retro';
    $expected_html = '<i class="fa '. $css_code .'"></i>';
    $library_added = FALSE;
    \at_fake::drupal_add_css(function($data = NULL, $options = NULL) use (&$library_added) {
      $fontawesome_library_path = at_library('fontawesome', NULL, FALSE);
      if ($data == $fontawesome_library_path . 'css/font-awesome.css') {
        $library_added = TRUE;
      }
    });

    $icon = $this->service->get($css_code);
    $this->assertEqual($expected_html, $icon->render(), 'Service icon.fontawesome generate the right html for icon.');

    $this->assertEqual($expected_html, at_icon($css_code, 'icon.fontawesome'), 'at_icon return the same markup.');

    $this->assertTrue($library_added, "fontawesome's css is added to page.");
  }
}
