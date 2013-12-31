<?php

namespace Drupal\at_base\Helper\Content_Render;

class TemplateFile_Engine extends String_Engine {
  public function process() {
    return \AT::twig()->render(
      at_container('helper.real_path')->get($this->data['template']),
      $this->data['variables'] ? $this->data['variables'] : array()
    );
  }
}
