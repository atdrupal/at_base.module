<?php

namespace Drupal\at_base\Helper\Content_Render;

class TemplateString_Engine extends String_Engine {
  public function process() {
    $variables = !empty($this->data['variables']) ? $this->data['variables'] : array();
    return array(
      '#markup' => \AT::twig_string()->render($this->data['template_string'], $variables)
    );
  }
}
