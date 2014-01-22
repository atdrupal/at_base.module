<?php
namespace Drupal\at_base\Hook;

class BlockView
{
  private $module;
  private $key;

  public function __construct($delta)
  {
    list($module, $key) = explode('|', $delta);
    $this->module = $module;
    $this->key = $key;
  }

  public function view()
  {
    $info = $this->getInfo();
    $render = at_container('helper.content_render');

    return array(
      'subject' => $render->setData($info['subject'])->render(),
      'content' => $render->setData($info['content'])->render(),
    );
  }

  private function getInfo()
  {
    $info = at_config($this->module, 'blocks')->get('blocks');
    if (!isset($info[$this->key])) {
      throw new \Exception("Invalid block: {$this->module}:{$this->key}");
    }
    return $info[$this->key];
  }
}
