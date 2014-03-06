<?php

namespace Drupal\at_base\Twig\Filters;

/**
 * Callback for drupalBlock filter.
 */
class Block {
  /**
   * Block object.
   *
   * @var \stdClass
   */
  private $block;

  /**
   * If FALSE, render block wrapper.
   *
   * @var boolean
   */
  private $content_only;

  /**
   * @param  string  $string       %module:%delta
   * @param  boolean $content_only TRUE to do not use block template.
   */
  public function __construct($string, $content_only = FALSE) {
    $this->block = $this->load($string);
    $this->content_only = $content_only;
  }

  public function render() {
    $output = _block_render_blocks(array($this->block));
    $output = _block_get_renderable_array($output);

    if ($this->content_only) {
      $output = reset($output);
      return isset($output['#markup']) ? $output['#markup'] : render(reset($output));
    }

    return drupal_render($output);
  }

  /**
   * Load the block.
   *
   * @param string $string Format %module:%delta
   */
  private function load($string) {
    list($module, $delta) = $this->findLoadKeys($string);
    if (!$block = block_load($module, $delta)) {
      throw new \Exception('Block not found');
    }

    // Make sure properties are set
    $block->region = isset($block->region) ? $block->region : -1;
    $block->title = isset($block->title) ? $block->title : '';

    return $block;
  }

  private function findLoadKeys($string) {
    $string = explode(':', $string);
    if (2 !== count($string)) {
      throw new \Exception('Wrong param');
    }

    list($module, $delta) = $string;
    if (!module_exists($module)) {
      throw new \Exception('Invalid module');
    }

    // Case of modules which use at_base to define the blocks
    if (!function_exists("{$module}_block_info")) {
      $delta = "{$module}|{$delta}";
      $module = 'at_base';
    }

    return array($module, $delta);
  }
}
