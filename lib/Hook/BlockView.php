<?php
namespace Drupal\at_base\Hook;

class BlockView {
  private $module;
  private $key;

  public function __construct($delta) {
    list($module, $key) = explode('___', $delta);
    $this->module = $module;
    $this->key = $key;
  }

  public function view () {
    $info = $this->getInfo();
    return array(
      'subject' => at_id(new \Drupal\at_base\Helper\RenderContent($info['subject']))->render(),
      'content' => at_id(new \Drupal\at_base\Helper\RenderContent($info['content']))->render(),
    );
  }

  private function getInfo() {
    $info = at_config($this->module, 'blocks')->get('blocks');
    if (!isset($info[$this->key])) {
      throw new \Exception("Invalid block: {$this->module}:{$this->key}");
    }
    return $info[$this->key];
  }
}
