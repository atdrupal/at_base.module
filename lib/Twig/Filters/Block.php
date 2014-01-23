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
    $string = explode(':', $string);
    if (2 !== count($string)) {
      return '<!-- Wrong param -->';
    }

    list($module, $delta) = $string;
    if (!module_exists($module)) {
      return '<!-- Invalid module -->';
    }

    if (!$block = block_load($module, $delta)) {
      return '<!-- Block not found -->';
    }

    // Make sure properties are set
    $block->region = -1;
    if (!isset($block->title)) {
      $block->title = '';
    }

    $output = _block_render_blocks(array($block));
    $output = _block_get_renderable_array($output);

    if ($content_only) {
      $output = reset($output);
      return isset($output['#markup']) ? $output['#markup'] : render(reset($output));
    }

    return drupal_render($output);
  }
}
