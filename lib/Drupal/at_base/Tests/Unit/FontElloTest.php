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

    at_container('wrapper.cache')->clearAll('atfont', 'cache', TRUE);
    drupal_static_reset('fontello_library_added');
    drupal_static_reset('fontello_inline_data_css_added');
    drupal_static_reset('fontello_inline_loading_font_css_added');
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
    $css_code = 'thumbs-up';

    $font_path = '';
    $font_name = '';
    $char = '';
    \at_fake::at_cache(function($options, $callback = NULL, $arguments = array()) use ($css_code, &$font_path, &$font_name, &$char) {
      if (strpos($options, "atfont:font_path:{$css_code}") !== FALSE) {
        $font_path = at_cache($options, $callback, $arguments);
        return $font_path;
      }
      if (strpos($options, "atfont:font_name:{$css_code}") !== FALSE) {
        $font_name = at_cache($options, $callback, $arguments);
        return $font_name;
      }
      if (strpos($options, "atfont:uni_char:{$css_code}") !== FALSE) {
        $char = at_cache($options, $callback, $arguments);
        return $char;
      }
    });

    at_icon($css_code, 'icon.fontello');

    // Font path is cached.
    $this->assertTrue(!empty($font_path), 'font path is cached.');

    // Font name is cached.
    $this->assertTrue(!empty($font_name), 'font name is cached.');

    // Unicode character is cached.
    $this->assertTrue(strpos($char, '\\') === 0, 'Unicode character is cached.');
  }

  public function testCssAdded() {

    $css_code = 'github';

    // Fake some values.
    $font_path = 'sites/all/libraries/fontello/src/cool.font/font';
    $font_name = 'cool';
    $char = '\\123456';
    \at_fake::at_cache(function($options, $callback = NULL, $arguments = array()) use ($css_code, $font_path, $font_name, $char) {
      if (strpos($options, "atfont:font_path:{$css_code}") !== FALSE) {
        return $font_path;
      }
      if (strpos($options, "atfont:font_name:{$css_code}") !== FALSE) {
        return $font_name;
      }
      if (strpos($options, "atfont:uni_char:{$css_code}") !== FALSE) {
        return $char;
      }
    });

    $inline_css_content_added = FALSE;
    $inline_css_loading_font_added = FALSE;
    $fontello_library_path = at_library('fontello', NULL, FALSE);
    $css_count = 0;
    \at_fake::drupal_add_css(function($data = NULL, $options = NULL) use ($css_code, $font_path, $font_name, $char, $fontello_library_path, &$inline_css_content_added, &$inline_css_loading_font_added, &$css_count) {

      if ($data == ".icon-$font_name-$css_code:before { content: '$char'; }") {
        $inline_css_content_added = TRUE;
      }

      if (strpos($data, $font_path . '/' . $font_name) !== FALSE) {
        $inline_css_loading_font_added = TRUE;
      }

      if ($data == $fontello_library_path . 'assets/icons/src/css/animation.css' ||
        ($data == $fontello_library_path . 'assets/icons/src/css/icons-ie7.css' && $options['browsers']['IE'] == 'IE 7')) {
        $css_count++;
      }
    });

    at_icon($css_code, 'icon.fontello');

    // Library is added.
    $this->assertEqual(2, $css_count, "fontello's css is added to page.");

    // Inline css is added.
    $this->assertEqual(TRUE, $inline_css_content_added && $inline_css_loading_font_added, 'inline css is added to page.');
  }
}
