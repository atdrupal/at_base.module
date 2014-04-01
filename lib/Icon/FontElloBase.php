<?php

namespace Drupal\at_base\Icon;

abstract class FontElloBase implements IconSourceInterface {
  public function getName() {
    return 'Fontello';
  }

  public function getIconConfig($set_name, $icon_name) {
    if ($v = at_container('kv', 'aticon.fontello')->get("{$set_name}/{$icon_name}")) {
      # return $v;
    }

    // Fetch and save config
    at_container('kv', 'aticon.fontello')
      ->set("{$set_name}/{$icon_name}", $config = $this->fetchIconConfig($set_name, $icon_name));

    return $config;
  }

  public function fetchIconConfig($set_name, $icon_name) {
    $return = array();

    $set_config = $this->getIconSetConfig($set_name);
    if (!empty($set_config['glyphs'])) {
      foreach ($set_config['glyphs'] as $icon_config) {
        if ($icon_name === $icon_config['css']) {
          $return = $icon_config + array('set_name' => $set_name);
        }
      }
    }

    return $return;
  }

  public function getIconSets() {
    $sets = array();
    foreach (file_scan_directory(at_library('fontello', NULL, FALSE) . 'src', '/config.yml$/') as $file => $info) {
      $sets[] = basename(dirname($file));
    }
    return $sets;
  }

  public function getIconSetPath($set_name) {
    return at_library('fontello', NULL, FALSE) . "src/{$set_name}/config.yml";
  }

  public function getIconSetConfig($set_name) {
    if ($v = at_container('kv', 'aticon.fontello.set')->get($set_name)) {
      return $v;
    }

    // fetch and save set config
    at_container('kv', 'aticon.fontello.set')->set($set_name, $config = $this->fetchIconSetConfig($set_name));

    return $config;
  }

  public function fetchIconSetConfig($set_name) {
    if ($config = \yaml_parse_file($this->getIconSetPath($set_name))) {
      return $config;
    }
    return array();
  }

  public function getIconList($set_name) {
    $icons = array();

    $config = $this->getIconSetConfig($set_name);
    if (!empty($config['glyphs'])) {
      foreach ($config['glyphs'] as $icon_config) {
        $icons[] = $icon_config['css'];
      }
    }

    return $icons;
  }

  /**
   * Cache config path of icon name.
   *
   * @param type $config_path
   * @param type $name
   * @return type
   */
  public function cacheFontPath($config_path, $name) {
    \at_fn::at_cache("atfont:font_path:{$name}", function() use ($config_path) {
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
    \at_fn::at_cache("atfont:font_name:{$name}", function() use ($config) {
      return $config['font']['fontname'];
    });
  }

  /**
   * Get inline data css.
   *
   * @param string $name
   * @param string $unicode_char
   * @param string $font_name
   */
  public function getInlineDataCSS($name, $unicode_char, $font_name) {
    $included = &drupal_static('fontello_inline_data_css_added');
    $css = array();

    if (empty($included[$name]) && !empty($unicode_char)) {
      $included[$name] = TRUE;

      $css[] = array(
        'data' => ".icon-$font_name-$name:before { content: '$unicode_char'; }",
        'options' => array('type' => 'inline')
      );
    }

    return $css;
  }

  /**
   * Get fontello library css.
   *
   * @return array
   */
  public function getLibrariesCSS() {
    $included = &drupal_static('fontello_library_added');
    $css = array();

    // Add library.
    if (!$included) {
      $fontello_library_path = at_library('fontello', NULL, FALSE);
      $css[] = $fontello_library_path . 'assets/icons/src/css/animation.css';
      $css[] = array(
        'data' => $fontello_library_path . 'assets/icons/src/css/icons-ie7.css',
        'options' => array('browsers' => array('IE' => 'IE 7', '!IE' => FALSE))
      );
      $included = TRUE;
    }

    return $css;
  }

  /**
   * Get inline loading font css.
   *
   * @param string $font_path
   * @param string $font_name
   * @return array
   */
  public function getInlineLoadingFontCSS($font_path, $font_name) {
    global $base_path;

    $included = &drupal_static('fontello_inline_loading_font_css_added');
    $css = array();

    // Add inline css code that load the right font base on font name.
    if (empty($included[$font_name])) {
      $included[$font_name] = TRUE;

      $css[] = array(
        'data' => at_container('twig')->render('@at_base/templates/fontello.css.twig', array(
          'name' => $font_name,
          'path' => $base_path . $font_path,
        )),
        'options' => array('type' => 'inline')
      );
    }

    return $css;
  }
}
