<?php
namespace Drupal\at_base\Icon;

class FontEllo implements IconInterface {

  /**
   * Get Icon instance with information to generate icon tag.
   *
   * @param type $name
   *   The name of icon in fontello.
   *   Browse available icons at http://fontello.com/
   * @return \Drupal\at_base\Icon\Icon
   *   Contain enough information to generate icon tag.
   */
  public function get($name) {

    $css = array();
    $tag = 'i';
    $class = '';
    $text = '';

    $unicode_char = $this->getUnicodeChar($name);

    // Load font path and font name cache base on icon name.
    $font_path = \at_fn::at_cache("atfont:font_path:{$name}", function () {
      return '';
    });
    $font_name = \at_fn::at_cache("atfont:font_name:{$name}", function() {
      return '';
    });
    if (empty($font_path) || empty($font_name)) {
      return new EmptyIcon($css, $tag, $class, $text);
    }

    $css = $this->getCss($name, $unicode_char, $font_path, $font_name);
    $class = 'icon-' . $font_name . '-' . $name;

    return new Icon($css, $tag, $class, $text);
  }

  /**
   * Translate icon name to unicode character.
   *
   * @param type $name
   *   Icon name.
   * @return string
   *   Unicode character.
   */
  public function getUnicodeChar($name) {
    $callback = array($this, 'scanDirectory');
    $directory = at_library('fontello', NULL, FALSE) . 'src';
    $arguments = array($directory, $name);

    $char = \at_fn::at_cache("atfont:uni_char:{$name}, + 1 year", $callback, $arguments);

    return $char;
  }

  /**
   * Scan directory for unicode character of icon name.
   *
   * @param type $directory
   * @param type $name
   * @return string
   *   Unicode character of icon name.
   */
  public function scanDirectory($directory, $name) {
    $sub_libs = file_scan_directory($directory, '/config.yml$/');

    // Loop through each font.
    foreach ($sub_libs as $config_file => $sub_lib) {
      // Cache parsing config file.
      $config = $this->parseConfigFile($config_file);

      if (!isset($config['glyphs'])) {
        continue;
      }

      foreach ($config['glyphs'] as $glyph) {
        if ($glyph['css'] == $name) {

          // We font the font, cache font path and name, no need to look up again.
          $this->cacheFontPath($config_file, $name);
          $this->cacheFontName($config, $name);

          $code = $glyph['code'];
          return '\\' . dechex($code);
        }
      }
    }

    return NULL;
  }

  /**
   * Cache config path of icon name.
   *
   * @param type $config_path
   * @param type $name
   * @return type
   */
  public function cacheFontPath($config_path, $name) {

    // Cache font path.
    \at_fn::at_cache("atfont:font_path:{$name}, + 1 year", function() use ($config_path) {
      global $base_root;
      return $base_root . '/' . str_replace('config.yml', '', $config_path) . 'font';
    });
  }

  /**
   * Cache font name of icon name.
   *
   * @param type $config
   * @param type $name
   * @return type
   */
  public function cacheFontName($config, $name) {

    // Cache font name.
    \at_fn::at_cache("atfont:font_name:{$name}, + 1 year", function() use ($config) {
      return $config['font']['fontname'];
    });
  }

  /**
   * Parse yml config file. Cache the result.
   *
   * @param string $file_path
   * @return array
   *   Parsed array.
   */
  public function parseConfigFile($file_path) {
    return at_cache("atfont:config_file:{$file_path}, + 1 year", function() use ($file_path) {
      return \yaml_parse_file($file_path);
    });
  }

  /**
   * Get all css of fontello.
   *
   * @param string $name
   * @param string $unicode_char
   * @param string $font_path
   * @param string $font_name
   */
  public function getCss($name, $unicode_char, $font_path, $font_name) {
    $css = array();

    $css = array_merge($css, $this->getInlineDataCss($name, $unicode_char, $font_name));
    $css = array_merge($css, $this->getLibrariesCss());
    $css = array_merge($css, $this->getInlineLoadingFontCss($font_path, $font_name));

    return $css;
  }

  /**
   * Get inline data css.
   *
   * @param type $name
   * @param type $unicode_char
   * @param type $font_name
   */
  public function getInlineDataCss($name, $unicode_char, $font_name) {
    $inline_data_css_added = &drupal_static('fontello_inline_data_css_added');
    $css = array();

    if (empty($inline_data_css_added[$name]) && !empty($unicode_char)) {
      $inline_data_css_added[$name] = TRUE;

      $css[] = array(
        'data' => ".icon-$font_name-$name:before { content: '$unicode_char'; }",
        'options' => array(
          'type' => 'inline',
        )
      );
    }

    return $css;
  }

  /**
   * Get fontello library css.
   *
   * @return array
   */
  public function getLibrariesCss() {
    $libraries_added = &drupal_static('fontello_library_added');
    $css = array();

    // Add library.
    if (!$libraries_added) {
      $fontello_library_path = at_library('fontello', NULL, FALSE);
      $css[] = array(
        'data' => $fontello_library_path . 'assets/icons/src/css/animation.css',
        'options' => NULL
      );
      $css[] = array(
        'data' => $fontello_library_path . 'assets/icons/src/css/icons-ie7.css',
        'options' => array('browsers' => array('IE' => 'IE 7', '!IE' => FALSE))
      );

      $libraries_added = TRUE;
    }

    return $css;
  }

  /**
   * Get inline loading font css.
   *
   * @param type $font_path
   * @param type $font_name
   * @return type
   */
  public function getInlineLoadingFontCss($font_path, $font_name) {
    $inline_loading_font_css_added = &drupal_static('fontello_inline_loading_font_css_added');
    $css = array();

    // Add inline css code that load the right font base on font name.
    if (empty($inline_loading_font_css_added[$font_name])) {
      $inline_loading_font_css_added[$font_name] = TRUE;

      $css[] = array(
        'data' => "@font-face {
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
        'options' => array('type' => 'inline')
      );
    }

    return $css;
  }
}
