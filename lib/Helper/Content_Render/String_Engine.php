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

    // Attach assets
    if (is_array($this->data) && !empty($this->data['attached'])) {
      $return = is_array($return) ?: array('#markup' => $return);

      if (isset($return['#attached'])) {
        $return['#attached'] = array_merge_recursive($return['#attached'], $this->processAttachedAsset());
      }
      else {
        $return['#attached'] = $this->processAttachedAsset();
      }
    }

    return $return;
  }

  protected function process() {
    return $this->data;
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
