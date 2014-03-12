<?php
namespace Drupal\at_base\Icon;

require_once DRUPAL_ROOT . '/sites/all/libraries/spyc/Spyc.php';

class FontEllo implements IconInterface {

  public function generate($css_code) {
    static $libraries_added = FALSE;
    static $css_added = FALSE;

    // Cache css code map.
    $code = at_cache("atfont:code_map:{$css_code}, + 1 year", function() use ($css_code) {
      $sub_libs = file_scan_directory(at_library('fontello', NULL, FALSE) . '/src', '/config.yml$/');

      foreach ($sub_libs as $config_file => $sub_lib) {
        // Cache parsing config file.
        $config = at_cache("atfont:config_file:{$config_file}, + 1 year", function() use ($config_file) {
          return \spyc_load_file($config_file);
        });

        if (!isset($config['glyphs'])) {
          continue;
        }

        foreach ($config['glyphs'] as $glyph) {
          if ($glyph['css'] == $css_code) {
            $code = $glyph['code'];

            // Update code map.
            return $code;
          }
        }
      }

      return NULL;
    });

    // Add css.
    if (empty($css_added[$css_code]) && !empty($code)) {

      drupal_add_css(
        ".icon-$css_code:before { content: '$code'; }",
        array(
          'type' => 'inline',
        )
      );
      $css_added[$css_code] = TRUE;
    }

    // Add library.
    if (!$libraries_added) {
      drupal_add_css(at_library('fontello', NULL, FALSE) . 'assets/icons/src/css/icons.css');
      drupal_add_css(at_library('fontello', NULL, FALSE) . 'assets/icons/src/css/animation.css');
      drupal_add_css(at_library('fontello', NULL, FALSE) . 'assets/icons/src/css/icons-ie7.css', array('browsers' => array('IE' => 'IE 7', '!IE' => FALSE)));
      $libraries_added = TRUE;
    }

    return '<i class="icon-' . $css_code . '"></i>';
  }
}
