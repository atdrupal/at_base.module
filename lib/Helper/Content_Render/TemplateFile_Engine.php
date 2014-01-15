<?php

namespace Drupal\at_base\Helper\Content_Render;

/**
 * @todo  Test case for template as array.
 */
class TemplateFile_Engine extends String_Engine {
  public function process() {
    $template = $this->data['template'];
    if (is_string($template)) {
      return $this->processTemplate(at_container('helper.real_path')->get($template));
    }
    elseif (is_array($template)) {
      foreach ($template as $tpl) {
        $file = at_container('helper.real_path')->get($tpl);
        if (is_file($file)) {
          return $this->processTemplate($file);
        }
      }
    }
  }

  private function processTemplate($template) {
    $variables = $this->data['variables'] ? $this->data['variables'] : array();
    return at_container('twig')->render($template, $variables);
  }
}
