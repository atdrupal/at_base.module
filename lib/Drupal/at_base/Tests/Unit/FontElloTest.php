<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

class FontElloTest extends UnitTestCase {
  private $service;

  static public function getInfo() {
    return array('name' => 'AT Unit: Icon Fontello') + parent::getInfo();
  }

  public function setUp() {
    parent::setUp();
    $this->service = at_container('icon.fontello');
  }

  protected function setUpModules() {
    parent::setUpModules();
  }

  public function testGenerate() {
    $css_code = 'chrome';

    $html = $this->service->generate($css_code);
    $this->assertEqual('<i class="icon-' . $css_code . '"></i>', $html, 'Service font.fontello generate the right html for icon.');

    $css = drupal_static('drupal_add_css', array());
    $fontello_library_path = at_library('fontello', NULL, FALSE);
    $this->assertTrue(isset($css[$fontello_library_path . 'assets/icons/src/css/icons.css']), "icon.css is added to page.");
    $this->assertTrue(isset($css[$fontello_library_path . 'assets/icons/src/css/animation.css']), "animation.css is added to page.");
    $this->assertTrue(isset($css[$fontello_library_path . 'assets/icons/src/css/icons-ie7.css']), "icons-ie7.css is added to page.");
    $this->assertEqual('IE 7', $css[$fontello_library_path . 'assets/icons/src/css/icons-ie7.css']['browsers']['IE'], "icons-ie7.css is only added if the browser is IE 7.");

    $cache = at_container('wrapper.cache')->get("atfont:code_map:{$css_code}", 'cache');
    $code = $cache->data;
    $this->assertNotNull($code, 'hexadecimal code of css code is cached.');

    $inline_css = FALSE;
    foreach ($css as $key => $value) {
      if (is_numeric($key) && $value['type'] == 'inline' && ".icon-$css_code:before { content: '$code'; }" == $value['data']) {
        $inline_css = TRUE;
        break;
      }
    }
    $this->assertEqual(TRUE, $inline_css, 'css inline code is added to page.');

    $this->assertEqual('<i class="icon-' . $css_code . '"></i>', at_icon($css_code, 'fontello'), 'at_icon return the same markup.');
  }
}
