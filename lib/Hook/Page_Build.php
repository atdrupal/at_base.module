<?php
namespace Drupal\at_base\Hook;

use Drupal\at_base\Hook\BlockView;

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
    foreach ($blocks as &$block) {
      $block = $this->loadBlock($block);
    }

    $output = _block_render_blocks($blocks);

    if ($blocks = _block_get_renderable_array($output)) {
      $this->page[$region] = isset($this->page[$region]) ? $this->page[$region] : array();
      $this->page[$region] = array_merge($this->page[$region], $blocks);
      element_children($this->page[$region], TRUE);
    }
  }

  /**
   * @param  array $config
   * @return stdClass
   */
  private function loadBlock($config) {
    if ($this->detectTraditionBlock($config)) {
      if ($block = $this->loadTraditionBlock($config)) {
        return $block;
      }
    }
    elseif ($block = $this->loadFancyBlock($config)) {
      return $block;
    }
  }

  private function detectTraditionBlock($config) {
    if (is_string($config)) {
      return TRUE;
    }

    $keys = array_keys($config);
    $first_key = reset($keys);
    return is_numeric($first_key);
  }

  /**
   * Fancy blocks:
   *
   *  - { content: " 2 = {{ 1 + 1 }} " }
   *  - [ {template: '@my_module/templates/fancy_block.html.twig'}, { title: 'Block title', weight: 1000} ]
   *
   * @param  [type] $config [description]
   * @return [type]         [description]
   */
  private function loadFancyBlock($config) {
    BlockView::setDynamicData(
      $key = isset($config['delta']) ? $config['delta'] : md5(serialize($config)),
      $config
    );
    return (object)array(
      'module' => 'at_base',
      'delta' => "dyn_{$key}",
      'region' => '',
      'title' => '',
    );
  }

  /**
   * Tradition blocks:
   *
   *  - system:powered-by
   *  - ['user:online', {title: "Online users", weight: -100}]
   *
   * @param  array $config
   * @return \stdClass
   */
  private function loadTraditionBlock($config) {
    list($module, $delta) = explode(':', is_string($config) ? $config : $config[0]);

    // Case of modules which use at_base to define the blocks
    if (!function_exists("{$module}_block_info")) {
      $delta = "{$module}|{$delta}";
      $module = 'at_base';
    }

    if ($block = block_load($module, $delta)) {
      if (is_array($config) && isset($config[1])) {
        $block = $this->overrideBlock($block, $config[1]);
      }

      return $block;
    }
  }

  private function overrideBlock($block, $options) {
    foreach ($options as $k => $v) {
      if (isset($block->{$k})) {
        $block->{$k} = $v;
      }
    }

    return $block;
  }
}
