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

  public function testHtmlGeneration() {
    $css_code = 'duckduckgo';
    $font_name = 'zocial';

    $icon = $this->service->get($css_code);

    $expected_html = '<i class="icon-' . $font_name . '-' . $css_code . '"></i>';

    $this->assertEqual($expected_html, $icon->render(), 'Service font.fontello generate the right html for icon.');

    // Test at_icon.
    $this->assertEqual($expected_html, at_icon($css_code, 'icon.fontello'), 'at_icon return the same markup.');
  }

  public function testCaching() {
    $css_code = 'duckduckgo';

    // Font path is cached.
    $cache = at_container('wrapper.cache')->get("atfont:font_path:{$css_code}", 'cache');
    $font_path = $cache->data;
    $this->assertNotNull($font_path, 'font path is cached.');

    // Font name is cached.
    $cache = at_container('wrapper.cache')->get("atfont:font_name:{$css_code}", 'cache');
    $font_name = $cache->data;
    $this->assertNotNull($font_name, 'font name is cached.');

    // Unicode character is cached.
    $cache = at_container('wrapper.cache')->get("atfont:uni_char:{$css_code}", 'cache');
    $char = $cache->data;
    $this->assertTrue(strpos($char, '\\') === 0, 'Unicode character is cached.');
  }

  public function testCssAdded() {

    drupal_static_reset('fontello_library_added');

    $css_code = 'github';
    $fontello_library_path = at_library('fontello', NULL, FALSE);

    $css_count = 0;
    \at_fake::drupal_add_css(function($data = NULL, $options = NULL) use ($fontello_library_path, &$css_count) {
      if ($data == $fontello_library_path . 'assets/icons/src/css/animation.css' ||
        ($data == $fontello_library_path . 'assets/icons/src/css/icons-ie7.css' && $options['browsers']['IE'] == 'IE 7')) {
        $css_count++;
      }
    });

    at_icon($css_code, 'icon.fontello');

    // Library is added.
    $this->assertEqual(2, $css_count, "fontello's css is added to page.");

    $cache = at_container('wrapper.cache')->get("atfont:font_path:{$css_code}", 'cache');
    $font_path = $cache->data;

    $cache = at_container('wrapper.cache')->get("atfont:font_name:{$css_code}", 'cache');
    $font_name = $cache->data;

    $cache = at_container('wrapper.cache')->get("atfont:uni_char:{$css_code}", 'cache');
    $char = $cache->data;

    drupal_static_reset('fontello_inline_css_data_added');
    drupal_static_reset('fontello_inline_css_loading_font_added');

    $inline_css_content_added = FALSE;
    $inline_css_loading_font_added = FALSE;
    \at_fake::drupal_add_css(function($data = NULL, $options = NULL) use ($font_path, $font_name, $css_code, $char, &$inline_css_content_added, &$inline_css_loading_font_added) {
      if ($options['type'] != 'inline') {
        return;
      }

      if ($data == ".icon-$font_name-$css_code:before { content: '$char'; }") {
        $inline_css_content_added = TRUE;
      }

      if (strpos($data, $font_path . '/' . $font_name) !== FALSE) {
        $inline_css_loading_font_added = TRUE;
      }
    });

    at_icon($css_code, 'icon.fontello');

    // Inline css is added.
    $this->assertEqual(TRUE, $inline_css_content_added && $inline_css_loading_font_added, 'inline css is added to page.');
  }
}
