<?php

namespace Drupal\at_base\Twig\Filters;

class Block {
  /**
   * Callback for drupalBlock filter.
   *
   * @param  string  $string       %module:%delta
   * @param  boolean $content_only TRUE to do not use block template.
   */
  public static function render($string, $content_only = FALSE) {
    try {
      $block = self::load($string);

      $output = _block_render_blocks(array($block));
      $output = _block_get_renderable_array($output);

      if ($content_only) {
        $output = reset($output);
        return isset($output['#markup']) ? $output['#markup'] : render(reset($output));
      }

      return drupal_render($output);
    }
    catch (\Exception $e) {
      return '<!-- '. $e->getMessage() .' -->';
    }
  }

  /**
   * Load the block.
   *
   * @param string $string Format %module:%delta
   */
  private static function load($string) {
    $string = explode(':', $string);
    if (2 !== count($string)) {
      throw new \Exception('Wrong param');
    }

    list($module, $delta) = $string;
    if (!module_exists($module)) {
      throw new \Exception('Invalid module');
    }

    if (!$block = block_load($module, $delta)) {
      throw new \Exception('Block not found');
    }

    // Make sure properties are set
    $block->region = -1;
    if (!isset($block->title)) {
      $block->title = '';
    }

    return $block;
  }
}
