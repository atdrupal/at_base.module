<?php

namespace Drupal\at_base\Helper\Content_Render;

class String_Engine {
  protected $content_render;
  protected $data;
  protected $attached;

  public function __construct($content_render) {
    $this->content_render = $content_render;
    $this->data = $this->content_render->getData();
  }

  public function render() {
    $return = $this->process();

    if (empty($this->data['attached'])) {
      return $return;
    }

    // Attach assets
    $return['#attached'] = isset($return['#attached'])
      ? array_merge_recursive($return['#attached'], $this->processAttachedAsset())
      : $this->processAttachedAsset();

    return $return;
  }

  protected function process() {
    return array('#markup' => $this->data);
  }

  protected function processAttachedAsset() {
    foreach (array_keys($this->data['attached']) as $type) {
      foreach ($this->data['attached'][$type] as $k => $item) {
        if (is_string($item)) {
          $this->data['attached'][$type][$k] = at_container('helper.real_path')->get($item);
        }
      }
    }
    return $this->data['attached'];
  }
}
