<?php

namespace Drupal\at_base\Helper;

/**
 * Helper class for rendering data:
 *
 *  $data = array(
 *    'template_string' => $template_string,
 *    'variables' => $variables,
 *    'attached' => $attached,
 *  );
 *  return at_id(new \Drupal\at_base\Helper\RenderContent($data))->render();
 *
 *  $data = array(
 *    'controller' => array('\Drupal\atest_base\Controller\Sample', 'renderAction'),
 *  );
 *  return at_id(new \Drupal\at_base\Helper\RenderContent($data))->render();
 *
 * @see  \Drupal\at_base\Controller\DefaultController
 * @see  \Drupal\at_base\Hook\BlockView
 * @see  \At_Base_Helper_RenderContent_TestCase::testRenderContent()
 */
class RenderContent {
  private $data;

  public function __construct($data) {
    if (!is_string($data)) {
      if (isset($data['template'])) {
        $data['template_file'] = $data['template'];
        unset($data['template']);
      }
    }
    $this->data = $data;
  }

  public function render() {
    // Fetch content
    if (is_string($this->data))                    return $this->data;
    elseif (isset($this->data['template_string'])) $return = $this->renderTemplateString();
    elseif (isset($this->data['template_file']))   $return = $this->renderTemplateFile();
    elseif (isset($this->data['controller']))      $return = $this->renderController();
    elseif (isset($this->data['form']))            $return = $this->renderForm();

    // Invalid structure
    if (empty($return)) throw new \Exception('Invalid data structure.');

    // Attach assets
    if (empty($this->data['attached'])) return $return;

    if (is_string($return)) {
      $return = array('#markup' => $return);
    }

    $return['#attached'] = isset($return['#attached'])
                            ? array_merge_recursive($return['#attached'], $this->processAttachedAsset())
                            : $this->processAttachedAsset();

    return $return;
  }

  private function renderTemplateString() {
    $variables = !empty($this->data['variables']) ? $this->data['variables'] : array();
    return \AT::twig_string()->render($this->data['template_string'], $variables);
  }

  private function renderTemplateFile() {
    $this->data['variables'] = is_array($this->data['variables']) ? $this->data['variables'] : array();
    return \AT::twig()->render(at_id(new \Drupal\at_base\Helper\RealPath($this->data['template_file']))->get(), $this->data['variables']);
  }

  private function renderController() {
    @list($class, $action, $arguments) = $this->data['controller'];
    return call_user_func_array(array(new $class(), $action), !empty($arguments) ? $arguments : array());
  }

  private function renderForm() {
    $args[] = 'at_form';
    $args[] = $this->data['form'];
    $args[] = isset($this->data['form arguments']) ? $this->data['form arguments'] : array();
    return call_user_func_array('drupal_get_form', $args);
  }

  private function processAttachedAsset() {
    foreach (array_keys($this->data['attached']) as $type) {
      foreach ($this->data['attached'][$type] as $k => $item) {
        if (is_string($item)) {
          $this->data['attached'][$type][$k] = str_replace('%theme', path_to_theme(), $item);
        }
      }
    }
    return $this->data['attached'];
  }
}
