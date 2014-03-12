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

    $html = $this->service->generate($css_code);
    $this->assertEqual('<i class="fa '. $css_code .'"></i>', $html, 'Service font.fontawesome generate the right html for icon.');

    $css = drupal_static('drupal_add_css', array());
    $this->assertTrue(isset($css[at_library('fontawesome', NULL, FALSE) . '/css/font-awesome.css']), "fontawesome's css is added to page.");

    $this->assertEqual('<i class="fa '. $css_code .'"></i>', at_icon($css_code, 'fontawesome'), 'at_icon return the same markup.');
  }
}
