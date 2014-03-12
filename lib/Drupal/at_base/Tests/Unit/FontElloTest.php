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
    $css_code = 'duckduckgo';

    $html = $this->service->generate($css_code);

    // Font path is cached.
    $cache = at_container('wrapper.cache')->get("atfont:font_path:{$css_code}", 'cache');
    $font_path = $cache->data;
    $this->assertNotNull($font_path, 'font path is cached.');

    // Font name is cached.
    $cache = at_container('wrapper.cache')->get("atfont:font_name:{$css_code}", 'cache');
    $font_name = $cache->data;
    $this->assertNotNull($font_name, 'font name is cached.');

    $expected_html = '<i class="icon-' . $font_name . '-' . $css_code . '"></i>';

    $this->assertEqual($expected_html, $html, 'Service font.fontello generate the right html for icon.');

    // Library is added.
    $css = drupal_static('drupal_add_css', array());
    $fontello_library_path = at_library('fontello', NULL, FALSE);
    $this->assertTrue(isset($css[$fontello_library_path . 'assets/icons/src/css/animation.css']), "animation.css is added to page.");
    $this->assertTrue(isset($css[$fontello_library_path . 'assets/icons/src/css/icons-ie7.css']), "icons-ie7.css is added to page.");
    $this->assertEqual('IE 7', $css[$fontello_library_path . 'assets/icons/src/css/icons-ie7.css']['browsers']['IE'], "icons-ie7.css is only added if the browser is IE 7.");

    // hexadecimal code is cached.
    $cache = at_container('wrapper.cache')->get("atfont:code_map:{$css_code}", 'cache');
    $code = $cache->data;
    $this->assertTrue(strpos($code, '\\') === 0, 'hexadecimal code of css code is cached.');

    // Inline css is added.
    $inline_css_content = FALSE;
    $inline_css_loading_font = FALSE;
    foreach ($css as $key => $value) {
      if (!is_numeric($key) || $value['type'] != 'inline') {
        continue;
      }

      if (".icon-$font_name-$css_code:before { content: '$code'; }" == $value['data']) {
        $inline_css_content = TRUE;
      }

      if (strpos($value['data'], $font_path . '/' . $font_name) !== FALSE) {
        $inline_css_loading_font = TRUE;
      }
    }
    $this->assertEqual(TRUE, $inline_css_content && $inline_css_loading_font, 'inline css is added to page.');

    // Test at_icon.
    $this->assertEqual($expected_html, at_icon($css_code, 'fontello'), 'at_icon return the same markup.');
  }
}
