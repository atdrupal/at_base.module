<?php
namespace Drupal\at_base\Hook;

/**
 * Details for hook_page_build().
 *
 * Parse block configuration, build attached-blocks to page structure.
 */
class Page_Build {
  /**
   * Structure of page.
   *
   * @var array
   */
  private $page;

  /**
   * Configuration of blocks for context page.
   *
   * @var array
   */
  private $blocks;

  public function __construct(&$page, $blocks) {
    $this->page = &$page;
    $this->blocks = $blocks;
  }

  public function execute() {
    foreach ($this->blocks as $region => $blocks) {
      $this->renderRegion($region, $blocks);
    }
  }

  private function renderRegion($region, $blocks) {
    foreach ($blocks as $i => &$block) {
      $block = $this->loadBlock($block);
    }

    $output = _block_render_blocks($blocks);

    $this->page[$region][] = _block_get_renderable_array($output);
    $this->page[$region]['#sorted'] = FALSE;
  }

  private function loadBlock($config) {
    list($module, $delta) = explode(':', is_string($config) ? $config : $config[0]);

    // Case of modules which use at_base to define the blocks
    if (!function_exists("{$module}_block_info")) {
      $delta = "{$module}|{$delta}";
      $module = 'at_base';
    }

    $block = block_load($module, $delta);

    if (is_array($config)) {
      $options = array_pop($config);
      foreach ($options as $k => $v) {
        if (isset($block->{$k})) {
          $block->{$k} = $v;
        }
      }
    }

    return $block;
  }
}
