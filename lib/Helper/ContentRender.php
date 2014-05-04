<?php

namespace Drupal\at_base\Helper;

use Drupal\at_base\Helper\ContentRender\CacheHandlerInterface;
use Drupal\at_base\Helper\ContentRender\Process;

/**
 * Helper class for rendering data:
 *
 *  $data = array('template_string' => $template_string, 'variables' => $variables, 'attached' => $attached);
 *  return atcg('helper.content_render')
 *    ->setData($data)
 *    ->render()
 *  ;
 *
 *
 *  $data = array('controller' => array('\Drupal\atest_base\Controller\Sample', 'renderAction'));
 *  return atcg('helper.content_render')
 *    ->setData($data)
 *    ->render()
 *  ;
 *
 * @see  \Drupal\at_base\Controller\DefaultController
 * @see  \Drupal\at_base\Hook\BlockView
 * @see  \At_Twig_TestCase::testContentRender()
 */
class ContentRender {
  /**
   * Data to be rendered.
   *
   * @var array
   */
  private $data;

  /**
   * @var CacheHandlerInterface
   */
  private $cache_handler;

  public function setData($data) {
    $this->data = $data;

    if (is_array($this->data) && empty($this->data['variables'])) {
      $this->data['variables'] = array();
    }

    return $this;
  }

  public function getData() {
    return $this->data;
  }

  public function setCacheHandler(CacheHandlerInterface $cache_handler) {
    $this->cache_handler = $cache_handler;
    return $this;
  }

  public function getCacheHandler() {
    return $this->cache_handler;
  }

  public function render($data = NULL) {
    if (!is_null($data)) {
      $this->setdata($data);
    }

    return (empty($this->data['cache']) || is_null($this->cache_handler))
      ? $this->build()
      : $this
          ->getCacheHandler()
          ->setOptions($this->data['cache'])
          ->setCallback(array($this, 'build'))
          ->render();
  }

  public function build() {
    if (is_string($this->data)) {
      return $this->data;
    }

    $args = $this->getVariables();
    $return = at_id(new Process($this->data, $args))->execute();

    // Attach assets
    if (is_array($this->data) && !empty($this->data['attached'])) {
      $return = is_array($return) ? $return : array('#markup' => $return);

      if (isset($return['#attached'])) {
        $return['#attached'] = array_merge_recursive($return['#attached'], $this->buildAttached());
      }
      else {
        $return['#attached'] = $this->buildAttached();
      }
    }

    return $return;
  }

  /**
   * @return array
   */
  private function getVariables() {
    if (isset($this->data['arguments'])) {
      return $this->data['arguments'];
    }

    if (TRUE === $this->getDynamicVariables()) {
      return $this->data['variables'];
    }

    if (TRUE === $this->getStaticVariables()) {
      return $this->data['variables'];
    }
  }

  private function getStaticVariables() {
    if (!empty($this->data['variables'])) {
      $v = &$this->data['variables'];

      $k = array_keys($v);
      if (is_numeric($k[0])) {
        $msg  = 'Expected keyed-array for $variables.';
        throw new \Exception($msg);
      }

      return TRUE;
    }
  }

  private function getDynamicVariables() {
    if (!empty($this->data['variables'])) {
      $v = &$this->data['variables'];

      $dyn = is_string($v);
      $dyn = $dyn || (($k = array_keys($v)) && is_numeric($k[0]));
      if ($dyn && $callback = atcg('helper.controller.resolver')->get($v)) {
        $this->data['variables'] = call_user_func($callback);
        return $this->getStaticVariables();
      }
    }
  }

  protected function buildAttached() {
    foreach (array_keys($this->data['attached']) as $type) {
      foreach ($this->data['attached'][$type] as $k => $item) {
        if (is_string($item)) {
          $this->data['attached'][$type][$k] = atcg('helper.real_path')->get($item, FALSE);
        }
      }
    }
    return $this->data['attached'];
  }
}
