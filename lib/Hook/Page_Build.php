<?php
namespace Drupal\at_base\Hook;

class Page_Build {
  private $page;
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

    usort($blocks, function($a, $b) { return ($a->weight < $b->weight) ? -1 : 1; });

    $output = _block_render_blocks($blocks);

    $this->page[$region][] = _block_get_renderable_array($output);
  }

  private function loadBlock($config) {
    list($module, $delta) = explode(':', is_string($config) ? $config : $config[0]);
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
