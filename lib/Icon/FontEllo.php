<?php
namespace Drupal\at_base\Icon;

class FontEllo extends FontElloBase {
  /**
   * Get Icon instance with information to generate icon tag.
   *
   * @param type $name
   *   The name of icon in fontello.
   *   Browse available icons at http://fontello.com/
   * @return \Drupal\at_base\Icon\Icon
   *   Contain enough information to generate icon tag.
   */
  public function get($id) {
    static $included = array();

    list($set_name, $name) = explode('/', $id);

    $font_path = dirname($this->getIconSetPath($set_name)) . '/font';
    if ($set_config = $this->getIconSetConfig($set_name)) {
      $font_name = $set_config['font']['fontname'];
    }

    if (empty($font_path) || empty($font_name)) {
      return new Icon();
    }

    // Include needed css files
    if (!isset($included[$name])) {
      $css = array_merge(
        $this->getInlineDataCSS($name, $this->getUnicodeChar($set_name, $name), $font_name),
        $this->getLibrariesCSS(),
        $this->getInlineLoadingFontCSS($font_path, $font_name)
      );

      foreach ($css as $included_css) {
        $this->includeCSS($included_css);
      }
    }

    return new Icon(
      $class = 'icon-' . $font_name . '-' . $name,
      $tag   = 'i',
      $text  = '');
  }

  private function includeCSS($included_css) {
    if (is_string($included_css)) {
      drupal_add_css($included_css);
    }
    elseif (is_array($included_css)) {
      drupal_add_css($included_css['data'], $included_css['options'] ? $included_css['options'] : array());
    }
  }

  /**
   * Translate icon name to unicode character.
   *
   * @param  string $name Icon name.
   * @return string       Unicode character.
   */
  private function getUnicodeChar($set_name, $name) {
    if ($icon_config = $this->getIconConfig($set_name, $name)) {
      return '\\' . dechex($icon_config['code']);
    }
  }
}
