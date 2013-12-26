<?php

namespace Drupal\at_base\Helper;

/**
 * Helper class for rendering data:
 *
 *  $data = array('template_string' => $template_string, 'variables' => $variables, 'attached' => $attached);
 *  return at_container('helper.content_render')
 *    ->setData($data)
 *    ->render()
 *  ;
 *
 *
 *  $data = array('controller' => array('\Drupal\atest_base\Controller\Sample', 'renderAction'));
 *  return at_container('helper.content_render')
 *    ->setData($data)
 *    ->render()
 *  ;
 *
 * @see  \Drupal\at_base\Controller\DefaultController
 * @see  \Drupal\at_base\Hook\BlockView
 * @see  \At_Base_Helper_ContentRender_TestCase::testContentRender()
 */
class Content_Render {
  private $data;
  private $engine;

  /**
   * Get render engine.
   *
   * @todo  Remove hardcode -- Let other module can define custom engine.
   */
  private function getEngine() {
    if     (is_string($this->data))                $engine = 'Drupal\at_base\Helper\Content_Render\String_Engine';
    elseif (isset($this->data['template_string'])) $engine = 'Drupal\at_base\Helper\Content_Render\TemplateString_Engine';
    elseif (isset($this->data['template']))        $engine = 'Drupal\at_base\Helper\Content_Render\TemplateFile_Engine';
    elseif (isset($this->data['controller']))      $engine = 'Drupal\at_base\Helper\Content_Render\Controller_Engine';
    elseif (isset($this->data['form']))            $engine = 'Drupal\at_base\Helper\Content_Render\Form_Engine';

    if (!empty($engine)) {
      return $this->engine = new $engine($this);
    }

    // Invalid structure
    throw new \Exception('Invalid data structure.');
  }

  public function setData($data) {
    $this->data = $data;
    return $this;
  }

  public function getData() {
    return $this->data;
  }

  public function render() {
    return $this->getEngine()->render();
  }
}
