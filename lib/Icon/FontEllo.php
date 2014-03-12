<?php
namespace Drupal\at_base\Icon;

class FontEllo implements IconInterface {

  public function generate($css_code) {
    static $libraries_added = FALSE;
    static $css_added = FALSE;

    // Cache css code map.
    $code = at_cache("atfont:code_map:{$css_code}, + 1 year", function() use ($css_code) {
      $sub_libs = file_scan_directory(at_library('fontello', NULL, FALSE) . 'src', '/config.yml$/');

      foreach ($sub_libs as $config_file => $sub_lib) {
        // Cache parsing config file.
        $config = at_cache("atfont:config_file:{$config_file}, + 1 year", function() use ($config_file) {
          return \yaml_parse_file($config_file);
        });

        if (!isset($config['glyphs'])) {
          continue;
        }

        foreach ($config['glyphs'] as $glyph) {
          if ($glyph['css'] == $css_code) {

            // Cache font path.
            at_cache("atfont:font_path:{$css_code}, + 1 year", function() use ($config_file) {
              global $base_root;
              return $base_root . '/' . str_replace('config.yml', '', $config_file) . 'font';
            });

            // Cache font name.
            at_cache("atfont:font_name:{$css_code}, + 1 year", function() use ($config) {
              return $config['font']['fontname'];
            });

            $code = $glyph['code'];

            return '\\' . dechex($code);
          }
        }
      }

      return NULL;
    });

    // Load font path and font name cache base on css code.
    $cache = at_container('wrapper.cache')->get("atfont:font_path:{$css_code}", 'cache');
    $font_path = $cache->data;
    $cache = at_container('wrapper.cache')->get("atfont:font_name:{$css_code}", 'cache');
    $font_name = $cache->data;
    if (empty($font_path) || empty($font_name)) {
      return '';
    }

    $this->addFontCss($font_path, $font_name);

    // Add css.
    if (empty($css_added[$css_code]) && !empty($code)) {

      drupal_add_css(
        ".icon-$font_name-$css_code:before { content: '$code'; }",
        array(
          'type' => 'inline',
        )
      );
      $css_added[$css_code] = TRUE;
    }

    // Add library.
    if (!$libraries_added) {
      drupal_add_css(at_library('fontello', NULL, FALSE) . 'assets/icons/src/css/animation.css');
      drupal_add_css(at_library('fontello', NULL, FALSE) . 'assets/icons/src/css/icons-ie7.css', array('browsers' => array('IE' => 'IE 7', '!IE' => FALSE)));
      $libraries_added = TRUE;
    }

    return '<i class="icon-' . $font_name . '-' . $css_code . '"></i>';
  }

  /**
   * Add inline css code that load the right font base on font name.
   *
   * @staticvar boolean $font_added
   * @param type $font_path
   * @param type $font_name
   */
  public function addFontCss($font_path, $font_name) {
    static $font_added = FALSE;

    if (empty($font_added[$font_name])) {

      drupal_add_css("@font-face {
          font-family: '{$font_name}';
          src: url('{$font_path}/{$font_name}.eot');
          src: url('{$font_path}/{$font_name}.eot') format('embedded-opentype'),
               url('{$font_path}/{$font_name}.woff') format('woff'),
               url('{$font_path}/{$font_name}.ttf') format('truetype'),
               url('{$font_path}/{$font_name}.svg') format('svg');
          font-weight: normal;
          font-style: normal;
        }
        /* Chrome hack: SVG is rendered more smooth in Windozze. 100% magic, uncomment if you need it. */
        /* Note, that will break hinting! In other OS-es font will be not as sharp as it could be */
        /*
        @media screen and (-webkit-min-device-pixel-ratio:0) {
          @font-face {
            font-family: 'zocial';
            src: url('{$font_path}/{$font_name}.svg') format('svg');
          }
        }
        */

         [class^=\"icon-{$font_name}-\"]:before, [class*=\" icon-{$font_name}-\"]:before {
          font-family: \"{$font_name}\";
          font-style: normal;
          font-weight: normal;
          speak: none;

          display: inline-block;
          text-decoration: inherit;
          width: 1em;
          margin-right: .2em;
          text-align: center;
          /* opacity: .8; */

          /* For safety - reset parent styles, that can break glyph codes*/
          font-variant: normal;
          text-transform: none;

          /* fix buttons height, for twitter bootstrap */
          line-height: 1em;

          /* Animation center compensation - magrins should be symmetric */
          /* remove if not needed */
          margin-left: .2em;

          /* you can be more comfortable with increased icons size */
          /* font-size: 120%; */

          /* Uncomment for 3D effect */
          /* text-shadow: 1px 1px 1px rgba(127, 127, 127, 0.3); */
        }",
        array('type' => 'inline')
      );

      $font_added[$font_name] = TRUE;
    }
  }
}