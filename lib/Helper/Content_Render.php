<?php

namespace Drupal\at_base\Helper;

use Drupal\at_base\Helper\Content_Render\CacheHandler_Interface;

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
 * @see  \At_Twig_TestCase::testContentRender()
 */
class Content_Render {
  /**
   * Data to be rendered.
   *
   * @var array
   */
  private $data;

  /**
   * @var CacheHandler_Interface
   */
  private $cache_handler;

  public function setData($data) {
    $this->data = $data;
    return $this;
  }

  public function getData() {
    return $this->data;
  }

  public function setCacheHandler(CacheHandler_Interface $cache_handler) {
    $this->cache_handler = $cache_handler;
    return $this;
  }

  public function getCacheHandler() {
    return $this->cache_handler;
  }

  public function render() {
    $no_cache = !empty($this->data['cache']) && is_null($this->cache_handler);
    $no_cache = $no_cache || empty($this->data['cache']);
    if ($no_cache) {
      return $this->build();
    }

    return $this
      ->getCacheHandler()
      ->setOptions($this->data['cache'])
      ->setCallback(array($this, 'build'))
      ->render()
    ;
  }

  public function build() {
    if (is_string($this->data)) {
      return $this->data;
    }

    $return = $this->process();

    // Attach assets
    if (is_array($this->data) && !empty($this->data['attached'])) {
      $return = is_array($return) ?: array('#markup' => $return);

      if (isset($return['#attached'])) {
        $return['#attached'] = array_merge_recursive($return['#attached'], $this->buildAttached());
      }
      else {
        $return['#attached'] = $this->buildAttached();
      }
    }

    return $return;
  }

  public function process() {
    if (isset($this->data['function'])) {
      return $this->processFunction();
    }

    if (isset($this->data['controller'])) {
      return $this->processController();
    }

    if (isset($this->data['template']) || isset($this->data['template_file'])) {
      return $this->processTemplate();
    }

    if (isset($this->data['template_string'])) {
      return $this->processTemplateString();
    }
  }

  public function processFunction() {
    $func = $this->data['function'];
    $args = $this->getVariables();
    return call_user_func_array($func, $args);
  }

  public function processController() {
    @list($class, $method, $args) = $this->data['controller'];
    return call_user_func_array(array(new $class(), $method), !empty($args) ? $args : array());
  }

  /**
   * @todo  Test case for template as array.
   */
  public function processTemplate() {
    $template = $this->data['template'];
    if (is_string($template)) {
      $template = at_container('helper.real_path')->get($template);
      return at_container('twig')->render($template, $this->getVariables());
    }

    if (is_array($template)) {
      foreach ($template as $tpl) {
        $file = at_container('helper.real_path')->get($tpl);
        if (is_file($file)) {
          return at_container('twig')->render($file, $this->getVariables());
        }
      }
    }
  }

  public function processTemplateString() {
    return at_container('twig_string')->render($this->data['template_string'], $this->getVariables());
  }

  public function getVariables() {
    return isset($this->data['arguments']) ? $this->data['arguments'] : (
      isset($this->data['variables']) ? $this->data['variables'] : array()
    );
  }

  protected function buildAttached() {
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
